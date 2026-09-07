<?php
/**
 * @package         FireBox
 * @version         3.1.13 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Admin;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Maintenance
{
	/**
	 * Hook data
	 * 
	 * @var  array
	 */
	private static $hook_data;

	public function __construct($hook_data)
	{
		self::$hook_data = $hook_data;
	}
	
	/**
	 * Runs maintenance.
	 * 
	 * @return  void
	 */
	public function init()
	{
		register_activation_hook(FBOX_PLUGIN_BASE_FILE, [__CLASS__, 'activation']);
		register_uninstall_hook(FBOX_PLUGIN_BASE_FILE, [__CLASS__, 'uninstall']);

		if (!is_multisite())
		{
			return;
		}

		// A site added to the network needs the same install every other site got.
		add_action('wp_initialize_site', [__CLASS__, 'onSiteInitialized'], 100, 1);

		// A site removed from the network takes its FireBox tables with it.
		add_filter('wpmu_drop_tables', [__CLASS__, 'dropTables'], 10, 2);

		// Drains the remaining sites of a large network activation.
		add_action('firebox_network_install', [__CLASS__, 'continueNetworkInstall']);
	}

	/**
	 * Runs on plugin activation
	 *
	 * @param   bool  $network_wide  Whether the plugin was network activated.
	 *
	 * @return  void
	 */
	public static function activation($network_wide = false)
	{
		if (is_multisite() && $network_wide)
		{
			SiteInstaller::installNetwork();
		}
		else
		{
			SiteInstaller::installOnActivation();
		}

		// Redirect to the dashboard on first activation. Read by
		// Admin::redirectAfterActivation(), which skips the network admin.
		update_option('firebox_activation_redirect', true);
	}

	/**
	 * Installs FireBox on a site that was just added to the network.
	 *
	 * @param   \WP_Site  $site
	 *
	 * @return  void
	 */
	public static function onSiteInitialized($site)
	{
		if (!is_multisite() || !$site instanceof \WP_Site)
		{
			return;
		}

		if (!self::isNetworkActive())
		{
			return;
		}

		switch_to_blog($site->blog_id);
		SiteInstaller::install();
		restore_current_blog();
	}

	/**
	 * Adds the FireBox tables to the ones WordPress drops with a deleted site.
	 *
	 * WordPress calls this filter while switched to the site being deleted, so
	 * $wpdb->prefix is that site's prefix.
	 *
	 * @param   array  $tables
	 * @param   int    $blog_id
	 *
	 * @return  array
	 */
	public static function dropTables($tables, $blog_id)
	{
		global $wpdb;

		foreach (self::$hook_data['tables'] as $table)
		{
			$tables[] = $wpdb->prefix . $table;
		}

		return $tables;
	}

	/**
	 * Installs the next batch of sites of a network activation.
	 *
	 * @return  void
	 */
	public static function continueNetworkInstall()
	{
		SiteInstaller::installNetwork();
	}

	/**
	 * Whether FireBox is activated for the whole network.
	 *
	 * @return  bool
	 */
	private static function isNetworkActive()
	{
		if (!function_exists('is_plugin_active_for_network'))
		{
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active_for_network(FBOX_PLUGIN_BASENAME);
	}
	
	/**
	 * Runs on plugin deactivation
	 * 
	 * @return  void
	 */
	public static function uninstall()
	{
		$pluginUninstall = new PluginUninstall(self::$hook_data);
		$pluginUninstall->start();
	}
}