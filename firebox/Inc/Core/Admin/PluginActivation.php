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

if (!class_exists('Activation'))
{
	require_once FBOX_PLUGIN_DIR . 'Inc/Framework/Inc/Admin/Includes/Activation.php';
}

class PluginActivation extends \Activation
{
	/**
	 * Installs FireBox on the current site.
	 *
	 * Idempotent: it runs on activation, for every site of a network, when a
	 * site is added to a network, and as a self-heal on version drift. Callers
	 * go through SiteInstaller, which decides which sites to run it on and
	 * handles the migrations an existing site needs.
	 *
	 * @return  void
	 */
	public function start()
	{
		$this->pluginActivation();

		\FireBox\Core\Helpers\Activation::createLibraryDirectories();

		// The notices count days from this date. Keeps an existing date.
		\FireBox\Core\Helpers\Plugin::setExtensionData('install_date', gmdate('Y-m-d H:i:s'));

		

		$this->installDefaultSettings();

		// initialize capabilities
		new Capabilities();

		// Each site runs its own log retention cleanup; cron is per site.
		\FireBox\Core\FB\Log::maybeScheduleCleanup();
	}

	/**
	 * Whether this site already has the FireBox tables.
	 *
	 * @return  bool
	 */
	public function tablesInstalled()
	{
		return (bool) $this->tablesInitialized();
	}

	/**
	 * Writes any missing default settings.
	 *
	 * Missing keys are filled in rather than the whole option being written
	 * only when absent: a site can end up with a partial firebox_settings from
	 * a migration long before it ever sees this method.
	 *
	 * @return  void
	 */
	private function installDefaultSettings()
	{
		$settings = get_option('firebox_settings');
		$settings = is_array($settings) ? $settings : [];

		$defaults = [
			'showcopyright' => '1',
			'show_admin_bar_menu_item' => '1',
			'debug' => '0',
			'statsdays' => '730',
			'geo_license_key' => '',
			'keep_data_on_uninstall' => '1',
			'usage_tracking' => '0',
			'enable_phpscripts' => '0',
			'license_key' => '',
		];

		foreach (\FireBox\Core\Helpers\Integrations::getSettingsKeys() as $integration_key)
		{
			$defaults[$integration_key] = '';
		}

		$missing = array_diff_key($defaults, $settings);

		if (!$missing)
		{
			return;
		}

		update_option('firebox_settings', array_merge($settings, $missing));
	}

}
