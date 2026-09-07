<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.5
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

if (!function_exists('add_action'))
{
	// We are running outside of the context of WordPress.
	return;
}

if (!function_exists('firepluginsframework_getFrameworkVersion'))
{
	/**
	 * Return the framework version
	 * 
	 * @param   string  $dir
	 * 
	 * @return  void
	 */
	function firepluginsframework_getFrameworkVersion($dir)
	{
		// Memoize the parsed framework.xml per directory so it is only parsed once per request.
		static $versions = [];

		if (isset($versions[$dir]))
		{
			return $versions[$dir];
		}

		$framework_file = 'framework.xml';

		$xmlFile = $dir . '/Framework/' . $framework_file;

		/**
		 * On development the xml file is located in /framework/source/framework.xml.
		 * On final zip, the file will rest on /Framework/framework.xml.
		 *
		 * Thus the check below.
		 */
		$xmlFile = file_exists($xmlFile) ? $xmlFile : $dir . '/source/' . $framework_file;

		$xml = [];

		// load XML file
		if (function_exists('simplexml_load_file'))
		{
			$xml = simplexml_load_file($xmlFile);
		}

		return ($versions[$dir] = isset($xml->version) ? (string) $xml->version : '1.0.0');
	}
}

if (!function_exists('firepluginsframework_getFrameworkPriority'))
{
	/**
	 * Return the framework priority
	 * 
	 * @param   string  $dir
	 * 
	 * @return  void
	 */
	function firepluginsframework_getFrameworkPriority($dir)
	{
		static $priorities = [];

		if (isset($priorities[$dir]))
		{
			return $priorities[$dir];
		}

		$version = firepluginsframework_getFrameworkVersion($dir);
		$version = array_map('intval', array_pad(explode('.', $version), 3, 0));

		/**
		 * Order the bundled frameworks so the newest one always registers first.
		 *
		 * Each component is weighted so it can never be outranked by the components below it.
		 * The previous weights (105 / 55 / 1) let the patch number outgrow a minor bump, so
		 * 1.1.150 loaded ahead of 1.2.0 and the older framework won.
		 *
		 * Bundles still shipping those weights produce offsets in the low hundreds, so any
		 * bundle using the weights below outranks them. That is the correct outcome, as every
		 * such bundle is by definition newer than the ones using the old weights.
		 */
		$id = PHP_INT_MAX - (($version[0] * 1000000) + ($version[1] * 1000) + $version[2]);

		return ($priorities[$dir] = $id);
	}
}

/**
 * Initialize the Framework
 * 
 * @return  void
 */
add_action('init', function()
{
	if (class_exists('\FPFramework\Framework'))
	{
		return;
	}

	$version = firepluginsframework_getFrameworkVersion(dirname(__DIR__));
	$priority = firepluginsframework_getFrameworkPriority(dirname(__DIR__));
	
	// Framework priority
	if (!defined('FPF_LOADED'))
	{
		define('FPF_LOADED', $priority);
	}

	// Framework version
	if (!defined('FPF_VERSION'))
	{
		define('FPF_VERSION', $version);
	}

	// Site URL
	if (!defined('FPF_SITE_URL'))
	{
		define('FPF_SITE_URL', 'https://www.fireplugins.com/');
	}

	// Framework Folder Path
	if (!defined('FPF_BASE_DIR'))
	{
		define('FPF_BASE_DIR', dirname(__DIR__));
	}

	// Framework Folder Path
	if (!defined('FPF_DIR'))
	{
		define('FPF_DIR', plugin_dir_path(__FILE__) . 'Inc');
	}

	// Framework Layouts Folder Path
	if (!defined('FPF_LAYOUTS_DIR'))
	{
		define('FPF_LAYOUTS_DIR', FPF_DIR . '/Layouts/');
	}

	// Support URL
	if (!defined('FPF_SUPPORT_URL'))
	{
		define('FPF_SUPPORT_URL', FPF_SITE_URL . 'contact/');
	}
	
	// Site Tower Endpoint
	if (!defined('FPF_TOWER_ENDPOINT'))
	{
		define('FPF_TOWER_ENDPOINT', FPF_SITE_URL . 'wp-json/tower/v1/');
	}

	// The templates.fireplugins.com Site URL
	if (!defined('FPF_TEMPLATES_SITE_URL'))
	{
		define('FPF_TEMPLATES_SITE_URL', 'https://templates.fireplugins.com/');
	}

	// Site Tower Endpoint
	if (!defined('FPF_TEMPLATES_TOWER_ENDPOINT'))
	{
		define('FPF_TEMPLATES_TOWER_ENDPOINT', FPF_TEMPLATES_SITE_URL . 'wp-json/tower/v1/');
	}

	// URL to retrieve templates
	if (!defined('FPF_TEMPLATES_GET_URL'))
	{
		define('FPF_TEMPLATES_GET_URL', FPF_TEMPLATES_TOWER_ENDPOINT . 'templates/{{PLUGIN}}/{{LICENSE_KEY}}/{{PLUGIN_VERSION}}/{{SITE_URL}}/get');
	}

	// URL to retrieve a template
	if (!defined('FPF_TEMPLATE_GET_URL'))
	{
		define('FPF_TEMPLATE_GET_URL', FPF_TEMPLATES_TOWER_ENDPOINT . 'template/{{PLUGIN}}/{{TEMPLATE}}/{{LICENSE_KEY}}/{{SITE_URL}}/get');
	}

	/**
	 * Get License Version URL
	 */
	if (!defined('FPF_GET_LICENSE_VERSION_URL'))
	{
		define('FPF_GET_LICENSE_VERSION_URL', FPF_TOWER_ENDPOINT . 'plugins/get_version/');
	}

	/**
	 * Plugin Changelog URL
	 */
	if (!defined('FPF_PLUGIN_CHANGELOG_URL'))
	{
		define('FPF_PLUGIN_CHANGELOG_URL', FPF_SITE_URL . '%s/changelog/');
	}

	// Now kick off the class autoloader.
	spl_autoload_register(
		/**
		 * Autoload classes
		 * 
		 * @param   string  $class
		 * 
		 * @return  void
		 */
		function ($class)
		{
			static $namespaces = null;

			if ($namespaces === null)
			{
				$base = dirname(__FILE__) . '/Inc/';

				$namespaces = [
					'FPFramework\\GeoIp2\\'     => $base . 'Libs/Vendors/geoip2/geoip2/src/',
					'FPFramework\\'              => $base,
					'MaxMind\\Db\\'              => $base . 'Libs/Vendors/maxmind-db/reader/src/MaxMind/Db/',
					'MaxMind\\'                  => $base . 'Libs/Vendors/maxmind/web-service-common/src/',
					'splitbrain\\PHPArchive\\'   => $base . 'Libs/Vendors/splitbrain/php-archive/src/',
				];
			}

			foreach ($namespaces as $prefix => $dir)
			{
				if (strpos($class, $prefix) === 0)
				{
					$file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';

					if (file_exists($file))
					{
						require_once $file;
					}

					return;
				}
			}
		}
	);

	// load once
	if (FPF_LOADED == $priority)
	{
		/**
		 * Fires when FPFramework is included/loaded
		 */
		do_action('fpf_init');

		if (is_admin())
		{
			/**
			 * Fires on the admin side when FPFramework is included/loaded.
			 */
			do_action('fpf_admin_init');
		}
	}
}, firepluginsframework_getFrameworkPriority(dirname(__DIR__)));
