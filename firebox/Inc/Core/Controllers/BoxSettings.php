<?php
/**
 * @package         FireBox
 * @version         3.1.5 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Controllers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Form;
use FPFramework\Base\FieldsParser;
use FPFramework\Base\Ui\Tabs;

class BoxSettings extends BaseController
{
    protected $action = '';
    
	/**
	 * The form settings name
	 * 
	 * @var  string
	 */
	const settings_name = 'firebox_settings';
	
    public function __construct()
    {
        add_action('update_option_firebox_settings', [$this, 'after_update_settings'], 10, 3);
    }

	/**
	 * Render the page content
	 * 
	 * @return  void
	 */
	public function render()
	{
		// page content
		add_action('firebox/settings_page', [$this, 'settingsPageContent']);
		
		// render layout
		firebox()->renderer->admin->render('pages/settings');
	}

    /**
     * Stop the usage tracking if the user disables the tracking behavior.
     * 
     * @param   array  $old_value
     * @param   array  $new_value
     * 
     * @return  void
     */
    public function after_update_settings($old_value, $new_value)
    {
        if (isset($new_value['usage_tracking']) && !$new_value['usage_tracking'])
        {
            $tracking = new \FireBox\Core\UsageTracking\SendUsage();
            $tracking->stop();
        }
    }

	/**
	 * Load required media files
	 * 
	 * @return void
	 */
	public function addMedia()
	{
		// load geoip js
		wp_register_script(
			'fpf-geoip',
			FPF_MEDIA_URL . 'admin/js/fpf_geoip.js',
			[],
			FPF_VERSION,
			false
		);
		wp_enqueue_script('fpf-geoip');

		wp_register_script(
			'firebox-settings-integrations',
			FBOX_MEDIA_ADMIN_URL . 'js/integrations_settings.js',
			[],
			FBOX_VERSION,
			true
		);
		wp_enqueue_script('firebox-settings-integrations');

		wp_register_style(
			'firebox-settings-integrations',
			FBOX_MEDIA_ADMIN_URL . 'css/integrations_settings.css',
			[],
			FBOX_VERSION
		);
		wp_enqueue_style('firebox-settings-integrations');

		$integrations = [];
		foreach (\FireBox\Core\Helpers\Integrations::getSettingsManagedIntegrations() as $integration)
		{
			$integrations[$integration['slug']] = [
				'label' => $integration['label'],
				'connected' => !empty($integration['connected']),
				'locked' => !empty($integration['locked']),
				'connection_type' => isset($integration['connection_type']) ? $integration['connection_type'] : 'api_key',
				'docs_url' => isset($integration['docs_url']) ? $integration['docs_url'] : ''
			];
		}

		$data = [
			'ajax_url' => admin_url('admin-ajax.php'),
			'settings_url' => admin_url('admin.php?page=firebox-settings#integrations'),
			'nonce' => wp_create_nonce('fpf_js_nonce'),
			'i18n' => [
				'connected' => firebox()->_('FB_INTEGRATION_CONNECTED'),
				'disconnected' => firebox()->_('FB_INTEGRATION_DISCONNECTED'),
				'could_not_connect' => firebox()->_('FB_INTEGRATION_ERROR_CONNECT'),
				'could_not_disconnect' => firebox()->_('FB_INTEGRATION_ERROR_DISCONNECT'),
				'please_enter_api_key' => fpframework()->_('FPF_PLEASE_ENTER_AN_API_KEY')
			],
			'integrations' => $integrations
		];

		wp_localize_script('firebox-settings-integrations', 'firebox_integrations_settings', $data);
	}

	/**
	 * Callback used to handle the processing of settings.
	 * Useful when using a Repeater field to remove the template from the list of submitted items.
	 * 
	 * @param   array  $input
	 * 
	 * @return  void
	 */
	public function processBoxSettings($input)
	{
        if (isset($_REQUEST['action']) && in_array($_REQUEST['action'], ['firebox_download_key_notice_activate', 'firebox_enable_usage_tracking']))
        {
            return $input;
        }

		$input = is_array($input) ? $input : [];
		$is_settings_form_submit = isset($_POST['option_page']) && sanitize_text_field(wp_unslash($_POST['option_page'])) === self::settings_name;

		// Allow programmatic updates (e.g. AJAX integration connect/disconnect) without settings form nonce.
		if (!$is_settings_form_submit)
		{
			return $input;
		}
        
		// run a quick security check
        if (!check_admin_referer('fpf_form_nonce_firebox_settings', 'fpf_form_nonce_firebox_settings'))
        {
			return; // get out if we didn't click the Activate button
        }

        // Disable usage tracking
        if (!isset($input['usage_tracking']))
        {
            $tracking = new \FireBox\Core\UsageTracking\SendUsage();
            $tracking->stop();
        }

		
		
		// Preserve integration keys managed through AJAX connect/disconnect.
		$stored_settings = get_option('firebox_settings', []);
		$stored_settings = is_array($stored_settings) ? $stored_settings : [];
		foreach (\FireBox\Core\Helpers\Integrations::getSettingsKeys() as $key)
		{
			if (!array_key_exists($key, $input) && isset($stored_settings[$key]))
			{
				$input[$key] = $stored_settings[$key];
			}
		}

		// Filters the fields value
		\FPFramework\Helpers\FormHelper::filterFields($input, \FireBox\Core\Admin\Forms\Settings::getSettings());

		\FPFramework\Libs\AdminNotice::displaySuccess(fpframework()->_('FPF_SETTINGS_SAVED'));
		
		return $input;
	}

    

	/**
	 * What the settings page will contain
	 * 
	 * @return  void
	 */
	public function settingsPageContent()
	{
		$fieldsParser = new FieldsParser([
			'fields_name_prefix' => 'firebox_settings',
			'fields_path' => ['\\FireBox\\Core\\Fields\\']
		]);

		$settings = \FireBox\Core\Admin\Forms\Settings::getSettings();
		foreach ($settings['data'] as $key => $value)
		{
			ob_start();
			$fieldsParser->renderContentFields($value);
			$html = ob_get_contents();
			ob_end_clean();

			$settings['data'][$key]['title'] = $value['title'];
			$settings['data'][$key]['content'] = $html;
		}

		// render settings as tabs
		$tabs = new Tabs($settings);

		// render form
		$form = new Form($tabs->render(), [
			'section_name' => self::settings_name
		]);
        
		echo $form->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

}
