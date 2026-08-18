<?php
/**
 * @package         FireBox
 * @version         3.1.10 Free
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

/**
 * Installs FireBox on the current site.
 *
 * This is the single entry point for "make this site ready to run FireBox". It
 * is called from the activation hook, for every site of a network activation,
 * when a new site is added to a network, and as a self-heal whenever the stored
 * version drifts from the running one. It must therefore be idempotent.
 */
class SiteInstaller
{
	/**
	 * Installs, or repairs, FireBox on the current site.
	 *
	 * @return  void
	 */
	public static function install($run_migrations = true)
	{
		$activation = new PluginActivation(firebox()->hook_data);

		// Read before anything is created: a site with neither a stored version
		// nor tables is new and has nothing to migrate, while one with tables
		// but no version predates the version option and needs every migration.
		$installed_version = get_option('firebox_version', '');

		if (!$installed_version && $activation->tablesInstalled())
		{
			$installed_version = '1.0.0';
		}

		/**
		 * Migrations first, install second.
		 *
		 * Stamping the version is what stops this running again, and a
		 * migration that fatals half way through must not leave the site
		 * looking up to date — it would never be retried. Running them first
		 * means a failure leaves the old version in place and the next request
		 * tries again.
		 */
		if ($installed_version && $run_migrations)
		{
			(new \FireBox\Core\Migrator($installed_version))->run();
		}

		$activation->start();

		// Migrations still owed: leave the version alone so the next request
		// picks the site up and finishes the job.
		if ($installed_version && !$run_migrations)
		{
			return;
		}

		update_option('firebox_version', FBOX_VERSION);
	}

	/**
	 * Installs FireBox on the current site during plugin activation.
	 *
	 * Activation is a hostile place to run code. WordPress includes the plugin
	 * file for the first time inside activate_plugin(), long after `init` has
	 * fired — and `init` is where the framework registers its autoloader, so
	 * none of the FPFramework classes the migrations rely on can be loaded.
	 * (This is why the install steps below require the few framework files they
	 * need by hand.)
	 *
	 * So activation installs, and never migrates. A site that owes migrations
	 * keeps its stored version, and the self-heal on its next request — with
	 * the framework loaded — runs them.
	 *
	 * @return  void
	 */
	public static function installOnActivation()
	{
		self::install(false);
	}

	/**
	 * Installs FireBox on every site of the network.
	 *
	 * @param   int  $limit  Sites to install per run. The rest are picked up by
	 *                       the next run, or lazily by self-heal.
	 *
	 * @return  int  Number of sites installed.
	 */
	public static function installNetwork($limit = 50)
	{
		if (!is_multisite())
		{
			self::installOnActivation();

			return 1;
		}

		$offset = (int) get_site_option('firebox_network_install_offset', 0);

		$sites = get_sites([
			'fields'  => 'ids',
			'number'  => $limit,
			'offset'  => $offset,
			'orderby' => 'id'
		]);

		foreach ($sites as $site_id)
		{
			switch_to_blog($site_id);
			self::installOnActivation();
			restore_current_blog();
		}

		$installed = count($sites);

		if ($installed < $limit)
		{
			// Reached the end of the network.
			delete_site_option('firebox_network_install_offset');
		}
		else
		{
			update_site_option('firebox_network_install_offset', $offset + $installed);

			if (!wp_next_scheduled('firebox_network_install'))
			{
				wp_schedule_single_event(time() + 60, 'firebox_network_install');
			}
		}

		return $installed;
	}
}
