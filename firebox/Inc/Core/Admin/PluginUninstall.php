<?php
/**
 * @package         FireBox
 * @version         3.1.15 Free
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

require_once FBOX_PLUGIN_DIR . 'Inc/Framework/Inc/Admin/Includes/Uninstall.php';

class PluginUninstall extends \Uninstall
{
	/**
	 * Runs once we uninstall the plugin.
	 *
	 * The uninstall hook fires once for the whole network, so on multisite this
	 * walks every site — each holds its own tables, campaigns, options and
	 * uploads — and applies the network's decision to all of them.
	 *
	 * @return  void
	 */
	public function start()
	{
		if (!is_multisite())
		{
			$this->uninstallCurrentSite();

			return;
		}

		/**
		 * One decision for the network, taken from its main site.
		 *
		 * Only a network administrator can delete a network plugin, and there
		 * is no screen where they can answer this for each site. Reading it per
		 * site sounds fairer but does not work: the setting defaults to keeping
		 * data, so every site that never changed it would hold on to its tables
		 * and the delete would leave the network littered with them.
		 */
		$keep_data = $this->networkKeepsData();

		wp_raise_memory_limit('admin');

		foreach (get_sites(['fields' => 'ids', 'number' => 0]) as $site_id)
		{
			/**
			 * Give each site its own slice of the clock rather than letting the
			 * whole network share one. Without this a large network walks into
			 * max_execution_time part way through; with it, only a single
			 * pathological site can.
			 *
			 * The loop is resumable either way — a site that finished no longer
			 * has firebox_settings, so uninstallCurrentSite() returns straight
			 * away and a retry carries on where this left off.
			 */
			if (function_exists('set_time_limit'))
			{
				@set_time_limit(30); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			}

			switch_to_blog($site_id);
			$this->uninstallCurrentSite($keep_data);
			restore_current_blog();
		}

		$this->deleteNetworkOptions();
	}

	/**
	 * Removes FireBox from the current site.
	 *
	 * Safe to call while switched: the framework resolves the table prefix from
	 * the current site, and every option read below is a site option.
	 *
	 * @param   bool|null  $keep_data  Whether to keep this site's data. Null
	 *                                 reads the site's own setting, which is
	 *                                 what a single site does.
	 *
	 * @return  void
	 */
	public function uninstallCurrentSite($keep_data = null)
	{
		// No settings means FireBox was never set up here — nothing of ours to
		// remove, and nothing to read a decision from.
		if (!$settings = get_option('firebox_settings'))
		{
			return;
		}

		// Disable usage tracking
		$tracking = new \FireBox\Core\UsageTracking\SendUsage();
		$tracking->stop();

		// Unschedule the log retention cleanup cron
		\FireBox\Core\FB\Log::unscheduleCleanup();

		if ($keep_data === null)
		{
			$keep_data = isset($settings['keep_data_on_uninstall']) && $settings['keep_data_on_uninstall'] == '1';
		}

		if ($keep_data)
		{
			return;
		}

		require_once FBOX_PLUGIN_DIR . 'Inc/Framework/Inc/Helpers/Directory.php';
		require_once FBOX_PLUGIN_DIR . 'Inc/Framework/Inc/Helpers/WPHelper.php';

		/**
		 * Campaigns go first, while the tables are still there.
		 *
		 * Deleting one fires delete_post, and our handler clears that campaign's
		 * logs and submissions. Dropping the tables first leaves that handler
		 * querying tables that no longer exist, which fills debug.log with
		 * database errors on every uninstall.
		 */
		$items = get_posts(['post_type' => 'firebox', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids']);

		if ($items)
		{
			foreach ($items as $item)
			{
				wp_delete_post($item, true);
			}
		}

		// remove all db tables
		$this->pluginUninstall();

		$this->removeCapabilities();

		// de-register post type
		unregister_post_type('firebox');

		// remove all options
		delete_option('firebox_version');
		delete_option('firebox_settings');
		delete_option('firebox_import');
		delete_option('firebox_license_status');
		delete_option('firebox_license_key');
		

		delete_option('firebox_session_conditions_in_use');
		delete_option('firebox_boxes_cache');
		delete_option('firebox_onboarding_completed');
		delete_option('firebox_activation_redirect');

		// Remove first campaign timing data
		delete_option('firebox_first_campaign_tracking_started_at');
		delete_option('firebox_time_to_first_campaign_draft_seconds');
		delete_option('firebox_time_to_first_campaign_publish_seconds');

		// Delete /wp-content/uploads/firebox directory
		\FPFramework\Helpers\Directory::delete(\FPFramework\Helpers\WPHelper::getPluginUploadsDirectory('firebox'));
	}

	/**
	 * Whether the network is keeping its data.
	 *
	 * Read from the main site, which is the network's own copy of the setting.
	 * Defaults to keeping it when there is nothing to read.
	 *
	 * @return  bool
	 */
	private function networkKeepsData()
	{
		switch_to_blog(get_main_site_id());
		$settings = get_option('firebox_settings');
		restore_current_blog();

		if (!is_array($settings) || !isset($settings['keep_data_on_uninstall']))
		{
			return true;
		}

		return $settings['keep_data_on_uninstall'] == '1';
	}

	/**
	 * Removes the options FireBox keeps at network level.
	 *
	 * @return  void
	 */
	private function deleteNetworkOptions()
	{
		delete_site_option('firebox_license_key');
		delete_site_option('firebox_license_status');
		delete_site_option('firebox_network_install_offset');
		delete_site_option('firebox_usage_tracking_site');
	}

	private function removeCapabilities()
	{
		$capabilities = \FireBox\Core\Admin\Capabilities::getCapabilities();

		// Remove capabilities from all roles
		foreach ($capabilities as $cap)
		{
            foreach (wp_roles()->roles as $role_name => $role_info)
			{
                $role = get_role($role_name);

                if ($role && $role->has_cap($cap))
				{
                    $role->remove_cap($cap);
                }
            }
        }
	}
}
