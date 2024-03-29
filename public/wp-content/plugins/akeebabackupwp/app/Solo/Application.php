<?php
/**
 * @package   solo
 * @copyright Copyright (c)2014-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Solo;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Awf\Application\TransparentAuthentication;
use Awf\Text\Text;
use Awf\Uri\Uri;
use Solo\Helper\SecretWord;

class Application extends \Awf\Application\Application
{
	const secretKeyRelativePath = '/engine/secretkey.php';

	public function initialise()
	{
		// Let AWF know that the prefix for our system JavaScript is 'akeeba.System.'
		\Awf\Html\Grid::$javascriptPrefix = 'akeeba.System.';

		// This line must appear before the user manager initializes, or it won't find the users table!
		$this->container->appConfig->set('user_table', '#__ak_users');

		// If the PHP session save path is not writeable we will use the 'session' subdirectory inside our tmp directory
		$this->discoverSessionSavePath();

		// Set up the template (theme) to use
		$this->setTemplate('default');

		// Load the language files
		$this->loadLanguages();

		// Redirect to the setup page if the configuration does not exist yet
		$configPath = $this->container->appConfig->getDefaultPath();

		$this->redirectToSetup($configPath);

		// Load the configuration if it's present
		if (@file_exists($configPath))
		{
			// Load the application's configuration
			$this->container->appConfig->loadConfiguration();

			// Apply the timezone
			$this->applyTimezonePreference();

			// Load Akeeba Engine's settings encryption preferences
			$this->loadEngineEncryptionKey();

			// Enforce encryption of the front-end Secret Word
			SecretWord::enforceEncryption('frontend_secret_word');

			// Load Akeeba Engine's configuration
			$this->loadBackupProfile();

			// Session timeout check
			$this->applySessionTimeout();
		}

		// Load the application routes
		$this->loadRoutes();

		// Attach the user privileges to the user manager
		$manager = $this->container->userManager;

		$this->attachPrivileges($manager);

		// Only apply TFA when debug mode has not been enabled
		$this->applyTwoFactorAuthentication($manager);

		// Show the login page when necessary
		$this->redirectToLogin();

		// Set up the media query key
		$this->setupMediaVersioning();
	}

	/**
	 * Language file processing callback. It converts _QQ_ to " and replaces the product name in the legacy INI files
	 * imported from Akeeba Backup for Joomla!.
	 *
	 * @param   string  $filename  The full path to the file being loaded
	 * @param   array   $strings   The key/value array of the translations
	 *
	 * @return  boolean|array  False to prevent loading the file, or array of processed language string, or true to
	 *                         ignore this processing callback.
	 */
	public function processLanguageIniFile($filename, $strings)
	{
		foreach ($strings as $k => $v)
		{
			$v = str_replace('_QQ_', '"', $v);
			$v = str_replace('Akeeba Backup', 'Akeeba Solo', $v);
			$strings[$k] = $v;
		}

		return $strings;
	}

	/**
	 * Apply the session timeout setting.
	 */
	public function applySessionTimeout()
	{
		// Get the session timeout
		$sessionTimeout = (int)$this->container->appConfig->get('session_timeout', 1440);

		// Get the base URL and set the cookie path
		$uri = new Uri(Uri::base(false, $this->container), $this);

		// Force the cookie timeout to coincide with the session timeout
		if ($sessionTimeout > 0)
		{
			$this->container->session->setCookieParams(array(
				'lifetime'	=> $sessionTimeout * 60,
				'path'		=> $uri->getPath(),
				'domain'	=> $uri->getHost(),
				'secure'	=> $uri->getScheme() == 'https',
				'httponly'	=> true,
			));
		}

		// Calculate a hash for the current user agent and IP address
		$ip = \Awf\Utils\Ip::getUserIP();
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$uniqueData = $ip . $user_agent . $this->container->basePath . Application::secretKeyRelativePath;
		$hash_algos = function_exists('hash_algos') ? hash_algos() : array();

		// Prefer SHA-512...
		if (in_array('sha512', $hash_algos))
		{
			$sessionKey = hash('sha512', $uniqueData, false);
		}
		// ...then SHA-256...
		elseif (in_array('sha256', $hash_algos))
		{
			$sessionKey = hash('sha256', $uniqueData, false);
		}
		// ...then SHA-1...
		elseif (function_exists('sha1'))
		{
			$sessionKey = sha1($uniqueData);
		}
		// ...then MD5...
		elseif (function_exists('md5'))
		{
			$sessionKey = md5($uniqueData);
		}
		// ... CRC32?! ...
		elseif (function_exists('crc32'))
		{
			$sessionKey = crc32($uniqueData);
		}
		// ... base64_encode????! ...
		elseif (function_exists('base64_encode'))
		{
			$sessionKey = base64_encode($uniqueData);
		}
		// ... paint your server a deep blue and toss it in the middle of the ocean where it will never be found!
		else
		{
			throw new \Exception('Your server does not provide any kind of hashing method. Please use a decent host.', 500);
		}

		// Get the current session's key
		$currentSessionKey = $this->container->segment->get('session_key', '');

		// If there is no key, set it
		if (empty($currentSessionKey))
		{
			$this->container->segment->set('session_key', $sessionKey);
		}
		// If there is a key and it doesn't match, trash the session and restart.
		elseif ($currentSessionKey != $sessionKey)
		{
			$this->container->session->destroy();
			$this->redirect($this->container->router->route('index.php'));
		}

		// If the session timeout is 0 or less than 0 there is no limit. Nothing to check.
		if ($sessionTimeout <= 0)
		{
			return;
		}

		// What is the last session timestamp?
		$lastCheck = $this->container->segment->get('session_timestamp', 0);
		$now = time();

		// If there is a session timestamp make sure it's valid, otherwise trash the session and restart
		if (($lastCheck != 0) && (($now - $lastCheck) > ($sessionTimeout * 60)))
		{
			$this->container->session->destroy();
			$this->redirect($this->container->router->route('index.php'));
		}
		// In any other case, refresh the session timestamp
		else
		{
			$this->container->segment->set('session_timestamp', $now);
		}
	}

	/**
	 * Creates or updates the custom session save path
	 *
	 * @param   string   $path    The custom session save path
	 * @param   boolean  $silent  Should I suppress all errors?
	 *
	 * @return  void
	 *
	 * @throws \Exception  If $silent is set to false
	 */
	public function createOrUpdateSessionPath($path, $silent = true)
	{
		try
		{
			$fs = $this->container->fileSystem;
			$protectFolder = false;

			if (!@is_dir($path))
			{
				$fs->mkdir($path, 0777);
			}
			elseif (!is_writeable($path))
			{
				$fs->chmod($path, 0777);
				$protectFolder = true;
			}
			else
			{
				if (!@file_exists($path . '/.htaccess'))
				{
					$protectFolder = true;
				}

				if (!@file_exists($path . '/web.config'))
				{
					$protectFolder = true;
				}
			}

			if ($protectFolder)
			{
				$fs->copy($this->container->basePath . '/.htaccess', $path . '/.htaccess');
				$fs->copy($this->container->basePath . '/web.config', $path . '/web.config');

				$fs->chmod($path . '/.htaccess', 0644);
				$fs->chmod($path . '/web.config', 0644);
			}
		}
		catch (\Exception $e)
		{
			if (!$silent)
			{
				throw $e;
			}
		}
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	private function discoverSessionSavePath()
	{
		$sessionPath = $this->container->session->getSavePath();

		if (!@is_dir($sessionPath) || !@is_writable($sessionPath))
		{
			$sessionPath = APATH_BASE . '/tmp/session';
			$this->createOrUpdateSessionPath($sessionPath);
			$this->container->session->setSavePath($sessionPath);
		}
	}

	/**
	 * @return void
	 */
	private function loadLanguages()
	{
		// Load the language files
		Text::loadLanguage(null, 'akeebabackup', '.com_akeebabackup.ini', false, $this->container->languagePath);
		Text::loadLanguage('en-GB', 'akeebabackup', '.com_akeebabackup.ini', false, $this->container->languagePath);

		// Load the extra language files
		Text::loadLanguage(null, 'akeeba', '.com_akeeba.ini', false, $this->container->languagePath);
		Text::loadLanguage('en-GB', 'akeeba', '.com_akeeba.ini', false, $this->container->languagePath);
	}

	/**
	 * @param $configPath
	 *
	 * @return void
	 */
	private function redirectToSetup($configPath)
	{
		if (!@file_exists($configPath) && !in_array($this->getContainer()->input->getCmd('view', ''), array(
				'setup', 'ftpbrowser', 'sftpbrowser'
			)))
		{
			$this->getContainer()->input->setData(array(
				'view' => 'setup'
			));
		}
	}

	/**
	 * @return void
	 */
	private function applyTimezonePreference()
	{
		if (function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set'))
		{
			if (function_exists('error_reporting'))
			{
				$oldLevel = error_reporting(0);
			}

			$serverTimezone = @date_default_timezone_get();

			if (empty($serverTimezone) || !is_string($serverTimezone))
			{
				$serverTimezone = $this->container->appConfig->get('timezone', 'UTC');
			}

			if (function_exists('error_reporting'))
			{
				error_reporting($oldLevel);
			}

			@date_default_timezone_set($serverTimezone);
		}
	}

	/**
	 * @return void
	 */
	private function loadEngineEncryptionKey()
	{
		$secretKeyFile = $this->container->basePath . static::secretKeyRelativePath;

		if (@file_exists($secretKeyFile))
		{
			require_once $secretKeyFile;
		}

		Factory::getSecureSettings()->setKeyFilename('secretkey.php');
	}

	/**
	 * @return void
	 */
	private function loadBackupProfile()
	{
		try
		{
			Platform::getInstance()->load_configuration();
		}
		catch (\Exception $e)
		{
			// Ignore database exceptions, they simply mean we need to install or update the database
		}
	}

	/**
	 * @return void
	 */
	private function loadRoutes()
	{
// Load the routes from JSON, if they are present
		$routesJSONPath = $this->container->basePath . '/assets/private/routes.json';
		$router         = $this->container->router;
		$importedRoutes = false;

		if (@file_exists($routesJSONPath))
		{
			$json = @file_get_contents($routesJSONPath);

			if (!empty($json))
			{
				$router->importRoutes($json);
				$importedRoutes = true;
			}
		}

		// If we could not import routes from routes.json, try loading routes.php
		$routesPHPPath = $this->container->basePath . '/assets/private/routes.php';

		if (!$importedRoutes && @file_exists($routesPHPPath))
		{
			require_once $routesPHPPath;
		}
	}

	/**
	 * @param $manager
	 *
	 * @return void
	 */
	private function attachPrivileges($manager)
	{
		$manager->registerPrivilegePlugin('akeeba', '\\Solo\\Application\\UserPrivileges');
		$manager->registerAuthenticationPlugin('password', '\\Solo\\Application\\UserAuthenticationPassword');
	}

	/**
	 * @param $manager
	 *
	 * @return void
	 */
	private function applyTwoFactorAuthentication($manager)
	{
		if (!defined('AKEEBADEBUG'))
		{
			$manager->registerAuthenticationPlugin('yubikey', '\\Solo\\Application\\UserAuthenticationYubikey');
			$manager->registerAuthenticationPlugin('google', '\\Solo\\Application\\UserAuthenticationGoogle');
		}
	}

	/**
	 * @return void
	 */
	private function redirectToLogin()
	{
		// Get the view. Necessary to go through $this->getContainer()->input as it may have already changed
		$view = $this->getContainer()->input->getCmd('view', '');

		// Get the user manager
		$manager = $this->container->userManager;

		// Show the login page if there is no logged in user and we're not in the setup or login page already
		// and we're not using the remote (front-end backup), json (remote JSON API) views of the (S)FTP
		// browser views (required by the session task of the setup view).
		if (!in_array($view, array(
				'check', 'login', 'setup', 'json', 'api', 'Api', 'remote', 'ftpbrowser', 'sftpbrowser',
			)) && !$manager->getUser()->getId())
		{
			// Try to perform transparent authentication
			$transparentAuth = new TransparentAuthentication($this->container);
			$credentials     = $transparentAuth->getTransparentAuthenticationCredentials();

			if (!is_null($credentials))
			{
				$this->container->segment->setFlash('auth_username', $credentials['username']);
				$this->container->segment->setFlash('auth_password', $credentials['password']);
				$this->container->segment->setFlash('auto_login', 1);
			}

			$return_url = $this->container->segment->getFlash('return_url');

			if (empty($return_url))
			{
				$return_url = Uri::getInstance()->toString();
			}

			$this->container->segment->setFlash('return_url', $return_url);

			$this->getContainer()->input->setData(array(
				'view' => 'login',
			));
		}
	}

	/**
	 * @return void
	 */
	private function setupMediaVersioning()
	{
		$this->getContainer()->mediaQueryKey = md5(microtime(false));
		$isDebug                             = !defined('AKEEBADEBUG');
		$hasVersion                          = defined('AKEEBABACKUP_VERSION') && defined('AKEEBABACKUP_DATE');
		$isDevelopment                       = $hasVersion ? ((strpos(AKEEBABACKUP_VERSION, 'svn') !== false) || (strpos(AKEEBABACKUP_VERSION, 'dev') !== false) || (strpos(AKEEBABACKUP_VERSION, 'rev') !== false)) : true;

		if (!$isDebug && !$isDevelopment && $hasVersion)
		{
			$this->getContainer()->mediaQueryKey = md5(AKEEBABACKUP_VERSION . AKEEBABACKUP_DATE);
		}
	}
}
