<?php
/**
 * @package         FireBox
 * @version         3.1.14
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 * 
 * Plugin Name:       FireBox 3.1.14 Free
 * Plugin URI:        https://www.fireplugins.com/
 * Description:       WordPress Popup and leads generation plugin that converts visitors into subscribers & customers.
 * Version:           3.1.14
 * Author:            FireBox Popup Plugin Team
 * Author URI:        https://www.fireplugins.com/
 * Text Domain:       firebox
 * Domain Path:       /languages
 * Requires at least: 6.3
 * Requires PHP: 	  7.0
 * License: 		  GPLv3 or later
 * License URI: 	  http://www.gnu.org/licenses/gpl-2.0.html
 */

// If this file is called directly, abort.
if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

// Load Framework
if (!file_exists(dirname(__FILE__) . '/Inc/Framework/init.php'))
{
	return;
}
require_once dirname(__FILE__) . '/Inc/Framework/init.php';

// Plugin Autoloader
if (!file_exists(dirname(__FILE__) . '/autoload.php'))
{
	return;
}
require_once dirname(__FILE__) . '/autoload.php';



// Plugin version
if (!defined('FBOX_VERSION'))
{
	define('FBOX_VERSION', '3.1.14');
}

// Plugin License type (free/pro)
if (!defined('FBOX_LICENSE_TYPE'))
{
	
	$fbox_license_type = 'lite';
	
	
	define('FBOX_LICENSE_TYPE', $fbox_license_type);
	unset($fbox_license_type);
}

// Plugin License Plan (basic/pro/growth)
if (!defined('FBOX_LICENSE_PLAN'))
{
	
	$fbox_license_plan = 'free';
	
	
	
	

	define('FBOX_LICENSE_PLAN', $fbox_license_plan);
	unset($fbox_license_plan);
}

// PHP Minimm Version
if (!defined('FBOX_MINIMUM_PHP_VERSION'))
{
	define('FBOX_MINIMUM_PHP_VERSION', '7.0');
}

// Go Pro URL
if (!defined('FBOX_GO_PRO_URL'))
{
	define('FBOX_GO_PRO_URL', 'https://www.fireplugins.com/upgrade/?utm_source=product&utm_campaign=firebox-' . (FBOX_LICENSE_TYPE === 'lite' ? 'free' : 'pro') . '&utm_medium=%s-feature');
}



// Plugin Documentation URL
if (!defined('FBOX_DOC_URL'))
{
	define('FBOX_DOC_URL', 'https://www.fireplugins.com/docs/?utm_source=product&utm_campaign=firebox-' . (FBOX_LICENSE_TYPE === 'lite' ? 'free' : 'pro') . '&utm_medium=misc&utm_content=documentation');
}

// Plugin Folder Path
if (!defined('FBOX_PLUGIN_DIR'))
{
	define('FBOX_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Plugin Release Date
if (!defined('FBOX_RELEASE_DATE'))
{
	define('FBOX_RELEASE_DATE', '2026-10-02');
}

// Layouts Folder Path
if (!defined('FBOX_LAYOUTS_DIR'))
{
	define('FBOX_LAYOUTS_DIR', FBOX_PLUGIN_DIR . 'Inc/Core/Layouts/');
}

// Admin Layouts Folder Path
if (!defined('FBOX_ADMIN_LAYOUTS_DIR'))
{
	define('FBOX_ADMIN_LAYOUTS_DIR', FBOX_PLUGIN_DIR . 'Inc/Core/Layouts/admin/');
}

// Public Layouts Folder Path
if (!defined('FBOX_PUBLIC_LAYOUTS_DIR'))
{
	define('FBOX_PUBLIC_LAYOUTS_DIR', FBOX_PLUGIN_DIR . 'Inc/Core/Layouts/public/');
}

// Plugin Folder URL
if (!defined('FBOX_PLUGIN_URL'))
{
	define('FBOX_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Media Plugin Folder URL
if (!defined('FBOX_MEDIA_URL'))
{
	define('FBOX_MEDIA_URL', plugin_dir_url(__FILE__) . 'media/');
}

// Admin Media Plugin Folder URL
if (!defined('FBOX_MEDIA_ADMIN_URL'))
{
	define('FBOX_MEDIA_ADMIN_URL', plugin_dir_url(__FILE__) . 'media/admin/');
}

// Public Media Plugin Folder URL
if (!defined('FBOX_MEDIA_PUBLIC_URL'))
{
	define('FBOX_MEDIA_PUBLIC_URL', plugin_dir_url(__FILE__) . 'media/public/');
}

// Plugin Root File
if (!defined('FBOX_PLUGIN_BASE_FILE'))
{
	define('FBOX_PLUGIN_BASE_FILE', __FILE__);
}

// Plugin Root Folder
if (!defined('FBOX_BASE_FOLDER'))
{
	define('FBOX_BASE_FOLDER', dirname(__FILE__));
}

if (!defined('FBOX_PLUGIN_BASENAME'))
{
	define('FBOX_PLUGIN_BASENAME', plugin_basename(__FILE__));
}

// Minimum PHP version check
if (!version_compare(PHP_VERSION, FBOX_MINIMUM_PHP_VERSION, '>='))
{
	add_action('admin_notices', 'firebox_fail_php_version');
}
else
{
	/**
	 * Install and upgrade entry point.
	 *
	 * The activation hook fires on one site only, and never for a site added to
	 * a network later. It is also missed when a plugin is activated by WP-CLI on
	 * part of a network, or when a site is restored from a backup. Comparing the
	 * stored version to the running one catches all of those, and it covers the
	 * plugin update too — upgrader_process_complete runs while the old code is
	 * still loaded, so the first request on the new code is what has to do the
	 * work anyway. The installer stamps the version, so this runs once.
	 */
	function firebox_init_installer_start()
	{
		add_action('fpf_init', function() {
			if (get_option('firebox_version') === FBOX_VERSION)
			{
				return;
			}

			\FireBox\Core\Admin\SiteInstaller::install();
		});
	}
	add_action('plugins_loaded', 'firebox_init_installer_start', -1);
	
	
	
	// Start Plugin
	require_once dirname(__FILE__) . '/Inc/Core/Plugin.php';
	firebox();

	// Run Activation/Deactivation procedures.
	$maintenance = new \FireBox\Core\Admin\Maintenance(firebox()->hook_data);
	$maintenance->init();
}

/**
 * FireBox admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @return void
 */
if (!function_exists('firebox_fail_php_version'))
{
	function firebox_fail_php_version()
	{
		$message = sprintf(fpframework()->_('FPF_PHP_VERSION_FAIL'), 'FireBox', FBOX_MINIMUM_PHP_VERSION);
		$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
		echo wp_kses_post( $html_message );
	}
}
