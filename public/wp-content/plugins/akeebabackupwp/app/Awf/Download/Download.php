<?php
/**
 * @package   awf
 * @copyright Copyright (c)2014-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or later
 */

namespace Awf\Download;

use Awf\Application\Application;
use Awf\Container\Container;
use Awf\Text\Text;
use Awf\Timer\Timer;

class Download
{
    /**
     * The component container object
     *
     * @var  Container
     */
    protected $container = null;

	/**
	 * Parameters passed from the GUI when importing from URL
	 *
	 * @var  array
	 */
	private $params = array();

	/**
	 * The download adapter which will be used by this class
	 *
	 * @var  DownloadInterface
	 */
	private $adapter = null;

    /**
     * Additional params that will be passed to the adapter while performing the download
     *
     * @var  array
     */
    private $adapterOptions = array();

	public function __construct(Container $c = null)
	{
        if(!is_object($c))
        {
            $c = Application::getInstance()->getContainer();
        }

        $this->container = $c;

		// Find the best fitting adapter
        $allAdapters = self::getFiles(__DIR__ . '/Adapter', array(), array('AbstractAdapter.php', 'cacert.pem'));
		$priority = 0;

		foreach ($allAdapters as $adapterInfo)
		{
			if (!class_exists($adapterInfo['classname'], true))
			{
				continue;
			}

			/** @var Adapter\AbstractAdapter $adapter */
			$adapter = new $adapterInfo['classname'];

			if (!$adapter->isSupported())
			{
				continue;
			}

			if ($adapter->priority > $priority)
			{
				$this->adapter = $adapter;
				$priority = $adapter->priority;
			}
		}
	}

	/**
	 * Forces the use of a specific adapter
	 *
	 * @param   string  $className  The name of the class or the name of the adapter
	 */
	public function setAdapter($className)
	{
		$adapter = null;

		if (class_exists($className, true))
		{
			$adapter = new $className;
		}
		elseif (class_exists('\\Awf\\Download\\Adapter\\' . ucfirst($className)))
		{
			$className = '\\Awf\\Download\\Adapter\\' . ucfirst($className);
			$adapter = new $className;
		}

		if (is_object($adapter) && ($adapter instanceof DownloadInterface))
		{
			$this->adapter = $adapter;
		}
	}

    /**
     * Returns the name of the current adapter
     *
     * @return string
     */
    public function getAdapterName()
    {
        if(is_object($this->adapter))
        {
            $class = get_class($this->adapter);

            return strtolower(str_ireplace('Awf\\Download\\Adapter\\', '', $class));
        }

        return '';
    }

    /**
     * Sets the additional options for the adapter
     *
     * @param array $options
     *
     * @codeCoverageIgnore
     */
    public function setAdapterOptions(array $options)
    {
        $this->adapterOptions = $options;
    }

    /**
     * Returns the additional options for the adapter
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function getAdapterOptions()
    {
        return $this->adapterOptions;
    }

	/**
	 * Used to decode the $params array
	 *
	 * @param   string $key     The parameter key you want to retrieve the value for
	 * @param   mixed  $default The default value, if none is specified
	 *
	 * @return  mixed  The value for this parameter key
	 */
	private function getParam($key, $default = null)
	{
		if (array_key_exists($key, $this->params))
		{
			return $this->params[$key];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Download data from a URL and return it
	 *
	 * @param   string  $url  The URL to download from
	 *
	 * @return  bool|string  The downloaded data or false on failure
	 */
	public function getFromURL($url)
	{
		if (is_null($this->adapter))
		{
			throw new \RuntimeException("No downloader adapter available. Please check your server configuration");
		}

		try
		{
            return $this->adapter->downloadAndReturn($url, null, null, $this->adapterOptions);
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	/**
	 * Performs the staggered download of file.
	 *
	 * @param   array $params A parameters array, as sent by the user interface
	 *
	 * @return  array  A return status array
	 */
	public function importFromURL($params)
	{
		$this->params = $params;

		// Fetch data
        $url         	= $this->getParam('url');
        $localFilename	= $this->getParam('localFilename');
        $frag        	= $this->getParam('frag', -1);
        $totalSize   	= $this->getParam('totalSize', -1);
        $doneSize    	= $this->getParam('doneSize', -1);
        $maxExecTime 	= $this->getParam('maxExecTime', 5);
        $runTimeBias 	= $this->getParam('runTimeBias', 75);
        $length      	= $this->getParam('length', 1048576);

        if (empty($localFilename))
        {
            $localFilename = basename($url);

            if (strpos($localFilename, '?') !== false)
            {
                $paramsPos = strpos($localFilename, '?');
                $localFilename = substr($localFilename, 0, $paramsPos - 1);

                $tmpDir = $this->container['temporaryPath'];
                $tmpDir = rtrim($tmpDir, '/\\');

                $localFilename = $tmpDir . '/' . $localFilename;
            }
        }

        // Init retArray
        $retArray = array(
            "status"    => true,
            "error"     => '',
            "frag"      => $frag,
            "totalSize" => $totalSize,
            "doneSize"  => $doneSize,
            "percent"   => 0,
            "localfile"	=> $localFilename
        );

        try
        {
            $timer = new Timer($maxExecTime, $runTimeBias);
            $start = $timer->getRunningTime(); // Mark the start of this download
            $break = false; // Don't break the step

            do
            {
                // Do we have to initialize the file?
                if ($frag == -1)
                {
                    // Currently downloaded size
                    $doneSize = 0;

                    if (@file_exists($localFilename))
                    {
                        @unlink($localFilename);
                    }

                    // Delete and touch the output file
                    $fp = @fopen($localFilename, 'w');

                    if ($fp !== false)
                    {
                        @fclose($fp);
                    }

                    // Init
                    $frag = 0;

                    $retArray['totalSize'] = $this->adapter->getFileSize($url, $this->adapterOptions);

                    if ($retArray['totalSize'] <= 0)
                    {
                        $retArray['totalSize'] = 0;
                    }

                    $totalSize = $retArray['totalSize'];
                }

                // Calculate from and length
                $from = $frag * $length;
                $to   = $length + $from - 1;

                // Try to download the first frag
                $required_time = 1.0;

                $error = '';

                try
                {
                    $result = $this->adapter->downloadAndReturn($url, $from, $to, $this->adapterOptions);
                }
                catch (\Exception $e)
                {
                    $result = false;
                    $error = $e->getMessage();
                }

                if ($result === false)
                {
                    // Failed download
                    if ($frag == 0)
                    {
                        // Failure to download first frag = failure to download. Period.
                        $retArray['status'] = false;
                        $retArray['error'] = $error;

                        return $retArray;
                    }
                    else
                    {
                        // Since this is a staggered download, consider this normal and finish
                        $frag = -1;
                        $totalSize = $doneSize;
                        $break = true;
                    }
                }

                // Add the currently downloaded frag to the total size of downloaded files
                if ($result !== false)
                {
                    $fileSize = strlen($result);
                    $doneSize += $fileSize;

                    // Append the file
                    $fp = @fopen($localFilename, 'a');

                    if ($fp === false)
                    {
                        // Can't open the file for writing
                        $retArray['status'] = false;
                        $retArray['error']  = Text::sprintf('AWF_DOWNLOAD_ERR_LIB_COULDNOTWRITELOCALFILE', $localFilename);

                        return $retArray;
                    }

                    fwrite($fp, $result);
                    fclose($fp);

                    $frag++;

                    if (($fileSize < $length) || ($fileSize > $length)
                        || (($totalSize == $doneSize) && ($totalSize > 0))
                    )
                    {
                        // A partial download or a download larger than the frag size means we are done
                        $frag = -1;
                        //debugMsg("-- Import complete (partial download of last frag)");
                        $totalSize = $doneSize;
                        $break = true;
                    }
                }

                // Advance the frag pointer and mark the end
                $end = $timer->getRunningTime();

                // Do we predict that we have enough time?
                $required_time = max(1.1 * ($end - $start), $required_time);

                if ($required_time > (10 - $end + $start))
                {
                    $break = true;
                }

                $start = $end;

            } while (($timer->getTimeLeft() > 0) && !$break);

            if ($frag == -1)
            {
                $percent = 100;
            }
            elseif ($doneSize <= 0)
            {
                $percent = 0;
            }
            else
            {
                if ($totalSize > 0)
                {
                    $percent = 100 * ($doneSize / $totalSize);
                }
                else
                {
                    $percent = 0;
                }
            }

            // Update $retArray
            $retArray = array(
                "status"    => true,
                "error"     => '',
                "frag"      => $frag,
                "totalSize" => $totalSize,
                "doneSize"  => $doneSize,
                "percent"   => $percent,
            );
        }
        catch (\Exception $e)
        {
            $retArray['status'] = false;
            $retArray['error'] = $e->getMessage();
        }

        return $retArray;
	}

	/**
	 * This method will crawl a starting directory and get all the valid files
	 * that will be analyzed by __construct. Then it organizes them into an
	 * associative array.
	 *
	 * @param   string $path          Folder where we should start looking
	 * @param   array  $ignoreFolders Folder ignore list
	 * @param   array  $ignoreFiles   File ignore list
	 *
	 * @return  array   Associative array, where the `fullpath` key contains the path to the file,
	 *                  and the `classname` key contains the name of the class
	 */
	protected static function getFiles($path, array $ignoreFolders = array(), array $ignoreFiles = array())
	{
		$return = array();

		$files = self::scanDirectory($path, $ignoreFolders, $ignoreFiles);

		// Ok, I got the files, now I have to organize them
		foreach ($files as $file)
		{
			$clean = str_replace($path, '', $file);
			$clean = trim(str_replace('\\', '/', $clean), '/');

			$parts = explode('/', $clean);

			$return[] = array(
				'fullpath'  => $file,
				'classname' => '\\Awf\\Download\\Adapter\\' . ucfirst(basename($parts[0], '.php'))
			);
		}

		return $return;
	}

	/**
	 * Recursive function that will scan every directory unless it's in the
	 * ignore list. Files that aren't in the ignore list are returned.
	 *
	 * @param   string  $path           Folder where we should start looking
	 * @param   array   $ignoreFolders  Folder ignore list
	 * @param   array   $ignoreFiles    File ignore list
	 *
	 * @return  array   List of all the files
	 */
	protected static function scanDirectory($path, array $ignoreFolders = array(), array $ignoreFiles = array())
	{
		$return = array();

		$handle = @opendir($path);

		if (!$handle)
		{
			return $return;
		}

		while (($file = readdir($handle)) !== false)
		{
			if ($file == '.' || $file == '..')
			{
				continue;
			}

			$fullpath = $path . '/' . $file;

			if ((is_dir($fullpath) && in_array($file, $ignoreFolders)) || (is_file($fullpath) && in_array($file, $ignoreFiles)))
			{
				continue;
			}

			if (is_dir($fullpath))
			{
				$return = array_merge(self::scanDirectory($fullpath, $ignoreFolders, $ignoreFiles), $return);
			}
			else
			{
				$return[] = $path . '/' . $file;
			}
		}

		return $return;
	}
}
