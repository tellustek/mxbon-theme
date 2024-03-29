<?php
/**
 * @package   awf
 * @copyright Copyright (c)2014-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or later
 */

namespace Awf\Download\Adapter;
use Awf\Download\DownloadInterface;
use Awf\Text\Text;

/**
 * A download adapter using URL fopen() wrappers
 */
class Fopen extends AbstractAdapter implements DownloadInterface
{
	public function __construct()
	{
		$this->priority = 100;
		$this->supportsFileSize = false;
		$this->supportsChunkDownload = true;
		$this->name = 'fopen';

		// If we are not allowed to use ini_get, we assume that URL fopen is
		// disabled.
		if (!function_exists('ini_get'))
		{
			$this->isSupported = false;
		}
		else
		{
			$this->isSupported = ini_get('allow_url_fopen');
		}
	}

	/**
	 * Download a part (or the whole) of a remote URL and return the downloaded
	 * data. You are supposed to check the size of the returned data. If it's
	 * smaller than what you expected you've reached end of file. If it's empty
	 * you have tried reading past EOF. If it's larger than what you expected
	 * the server doesn't support chunk downloads.
	 *
	 * If this class' supportsChunkDownload returns false you should assume
	 * that the $from and $to parameters will be ignored.
	 *
	 * @param   string   $url   The remote file's URL
	 * @param   integer  $from  Byte range to start downloading from. Use null for start of file.
	 * @param   integer  $to    Byte range to stop downloading. Use null to download the entire file ($from is ignored)
     * @param   array    $params  Additional params that will be added before performing the download
	 *
	 * @return  string  The raw file data retrieved from the remote URL.
	 *
	 * @throws  \Exception  A generic exception is thrown on error
	 */
	public function downloadAndReturn($url, $from = null, $to = null, array $params = array())
	{
		if (empty($from))
		{
			$from = 0;
		}

		if (empty($to))
		{
			$to = 0;
		}

		if ($to < $from)
		{
			$temp = $to;
			$to = $from;
			$from = $temp;
			unset($temp);
		}

		if (!(empty($from) && empty($to)))
		{
            $options = array(
                'http'	=> array(
                    'method'	=> 'GET',
                    'header'	=> ["Range: bytes=$from-$to\r\n"]
                ),
                'ssl' => array(
                    'verify_peer'   => true,
                    'cafile'        => __DIR__ . '/cacert.pem',
                    'verify_depth'  => 5,
                )
            );
		}
		else
		{
            $options = array(
                'http'	=> array(
                    'method'	=> 'GET',
                ),
                'ssl' => array(
                    'verify_peer'   => true,
                    'cafile'        => __DIR__ . '/cacert.pem',
                    'verify_depth'  => 5,
                )
            );
		}

		if (isset($params['proxy']))
		{
			$proxyParams = $params['proxy'];
			unset($params['proxy']);
			$host = isset($proxyParams['host']) ? trim($proxyParams['host']) : '';
			$port = isset($proxyParams['port']) ? (int) $proxyParams['port'] : 0;
			$user = isset($proxyParams['user']) ? trim($proxyParams['user']) : '';
			$pass = isset($proxyParams['pass']) ? trim($proxyParams['pass']) : '';
			$enabled = !empty($host) && !empty($port) && ($port > 0) && ($port < 65536);

			if ($enabled)
			{
				if (!isset($params['http']))
				{
					$params['http'] = [];
				}

				$params['http']['proxy'] = $host . ':' . $port;
				$params['http']['request_fulluri'] = true;

				if (!empty($user))
				{
					$params['http']['header'] = [
						'Proxy-Authorization: Basic ' . base64_encode($user . ':' . $pass)
					];
				}
			}
		}

		$options = array_merge_recursive($options, $params);
		$context = stream_context_create($options);

		if (!(empty($from) && empty($to)))
		{
			$result  = @file_get_contents($url, false, $context, $from - $to + 1);
		}
		else
		{
			$result  = @file_get_contents($url, false, $context);
		}

        global $http_response_header_test;

        if (!isset($http_response_header) && empty($http_response_header_test))
		{
			$error = Text::sprintf('AWF_DOWNLOAD_ERR_LIB_FOPEN_ERROR');
			throw new \Exception($error, 404);
		}
		else
		{
            // Used for testing
            if (!isset($http_response_header) && !empty($http_response_header_test))
            {
                $http_response_header = $http_response_header_test;
            }

			$http_code = 200;
			$nLines = count($http_response_header);

			for ($i = $nLines - 1; $i >= 0; $i--)
			{
				$line = $http_response_header[$i];
				if (strncasecmp("HTTP", $line, 4) == 0)
				{
					$response = explode(' ', $line);
					$http_code = $response[1];
					break;
				}
			}

			if ($http_code >= 299)
			{
				$error = Text::sprintf('AWF_DOWNLOAD_ERR_LIB_FOPEN_ERROR', $http_code);
				throw new \Exception($error, $http_code);
			}
		}

		if ($result === false)
		{
			$error = Text::sprintf('AWF_DOWNLOAD_ERR_LIB_FOPEN_ERROR');
			throw new \Exception($error, 1);
		}
		else
		{
			return $result;
		}
	}
}
