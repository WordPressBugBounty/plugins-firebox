<?php
/**
 * @package         FireBox
 * @version         3.1.6 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Admin\Includes;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Onboarding
{
	/**
	 * Onboarding status: completed.
	 *
	 * @var  string
	 */
	const ONBOARDING_STATUS_COMPLETED = 'completed';

	/**
	 * Onboarding status: skipped.
	 *
	 * @var  string
	 */
	const ONBOARDING_STATUS_SKIPPED = 'skipped';

	/**
	 * Onboarding version for future updates
	 * 
	 * @var  string
	 */
	const ONBOARDING_VERSION = '1.0';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Only register pointers if site hasn't completed onboarding
		if (!$this->isOnboardingCompleted())
		{
			add_action('admin_enqueue_scripts', [$this, 'registerPointers']);
		}
	}

	/**
	 * Check if site has completed onboarding
	 * 
	 * @return  bool
	 */
	private function isOnboardingCompleted()
	{
		return !empty(trim((string) get_option('firebox_onboarding_completed', '')));
	}

	/**
	 * Mark onboarding as completed for site
	 *
	 * @param   string  $status  completed|skipped
	 * 
	 * @return  void
	 */
	public function markOnboardingCompleted($status = self::ONBOARDING_STATUS_COMPLETED)
	{
		$status = strtolower(trim((string) $status));
		if (!in_array($status, [self::ONBOARDING_STATUS_COMPLETED, self::ONBOARDING_STATUS_SKIPPED], true))
		{
			$status = self::ONBOARDING_STATUS_COMPLETED;
		}

		update_option('firebox_onboarding_completed', $status);
	}

	/**
	 * Register WordPress internal pointers
	 * 
	 * @return  void
	 */
	public function registerPointers()
	{
		// Enqueue WordPress pointer script and styles
		wp_enqueue_script('wp-pointer');
		wp_enqueue_style('wp-pointer');

		// Register pointers
		$pointers = $this->getPointers();

		if (!empty($pointers))
		{
			wp_localize_script('wp-pointer', 'fireboxPointers', [
				'pointers' => $pointers,
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'completeNonce' => wp_create_nonce('firebox_onboarding_complete'),
				'i18n' => [
					'btnBack' => firebox()->_('FB_ONBOARDING_BTN_BACK'),
					'btnContinue' => firebox()->_('FB_ONBOARDING_BTN_CONTINUE'),
					'btnComplete' => firebox()->_('FB_ONBOARDING_BTN_COMPLETE'),
					'btnSkip' => firebox()->_('FB_ONBOARDING_BTN_SKIP'),
					'skipConfirm' => firebox()->_('FB_ONBOARDING_SKIP_CONFIRM'),
				],
			]);

			// Enqueue our pointer initialization script
			wp_enqueue_script(
				'firebox-onboarding',
				FBOX_PLUGIN_URL . 'media/admin/js/onboarding.js',
				['wp-pointer', 'jquery'],
				FBOX_VERSION,
				true
			);

			// Enqueue pointer styles
			wp_enqueue_style(
				'firebox-onboarding-style',
				FBOX_PLUGIN_URL . 'media/admin/css/onboarding.css',
				['wp-pointer'],
				FBOX_VERSION
			);
		}
	}

	/**
	 * Get all pointer definitions
	 * Each pointer targets a specific UI element in the FireBox workflow
	 * 
	 * @return  array
	 */
	private function getPointers()
	{
		$pointers = [];

		// Step 1: New Campaign Button
		$pointers['firebox_pointer_new_campaign'] = [
			'target' => '.fpframework-admin-container--sidebar--outer--inner--item .fpf-open-library-modal',
			'edge' => 'left',
			'align' => 'left',
			'title' => firebox()->_('FB_ONBOARDING_STEP_1_TITLE'),
			'content' => firebox()->_('FB_ONBOARDING_STEP_1_DESC'),
			'step' => 1,
			'cssClass' => 'firebox-pointer-new-campaign',
		];

		// Step 2: Template Library
		$pointers['firebox_pointer_template_library'] = [
			'target' => '.fpf-library-body',
			'edge' => 'right',
			'align' => 'middle',
			'title' => firebox()->_('FB_ONBOARDING_STEP_2_TITLE'),
			'content' => firebox()->_('FB_ONBOARDING_STEP_2_DESC'),
			'step' => 2,
			'cssClass' => 'firebox-pointer-template-library',
		];

		// Step 3: Blank Campaign Option
		$pointers['firebox_pointer_blank_campaign'] = [
			'target' => '.fpf-library-item.blank_popup',
			'edge' => 'left',
			'align' => 'middle',
			'title' => firebox()->_('FB_ONBOARDING_STEP_3_TITLE'),
			'content' => firebox()->_('FB_ONBOARDING_STEP_3_DESC'),
			'step' => 3,
			'cssClass' => 'firebox-pointer-blank-campaign',
			'redirect' => admin_url('post-new.php?post_type=firebox'),
		];

		// Step 4: Campaign Editor
		$pointers['firebox_pointer_editor'] = [
			'target' => '.firebox-editor-wrapper',
			'edge' => 'left',
			'align' => 'right',
			'title' => firebox()->_('FB_ONBOARDING_STEP_4_TITLE'),
			'content' => firebox()->_('FB_ONBOARDING_STEP_4_DESC'),
			'step' => 4,
			'cssClass' => 'firebox-pointer-editor',
		];

		// Step 5: Format and Trigger
		$pointers['firebox_pointer_settings'] = [
			'target' => '.firebox-format-panel',
			'edge' => 'right',
			'align' => 'top',
			'title' => firebox()->_('FB_ONBOARDING_STEP_5_TITLE'),
			'content' => firebox()->_('FB_ONBOARDING_STEP_5_DESC'),
			'step' => 5,
			'cssClass' => 'firebox-pointer-settings',
		];

		// Step 6: Display Conditions
		$pointers['firebox_pointer_display_conditions'] = [
			'target' => '.firebox-display-conditions-panel',
			'edge' => 'right',
			'align' => 'top',
			'title' => firebox()->_('FB_ONBOARDING_STEP_6_TITLE'),
			'content' => firebox()->_('FB_ONBOARDING_STEP_6_DESC'),
			'step' => 6,
			'cssClass' => 'firebox-pointer-display-conditions',
		];

		// Step 7: Publish Campaign
		$pointers['firebox_pointer_publish'] = [
			'target' => '.editor-post-publish-button__button',
			'edge' => 'top',
			'align' => 'right',
			'title' => firebox()->_('FB_ONBOARDING_STEP_7_TITLE'),
			'content' => firebox()->_('FB_ONBOARDING_STEP_7_DESC'),
			'step' => 7,
			'cssClass' => 'firebox-pointer-publish',
		];

		return $pointers;
	}

	/**
	 * Handle completion of onboarding via AJAX
	 * 
	 * @return  void
	 */
	public static function handleOnboardingComplete()
	{
		$nonce_raw = isset($_POST['nonce']) ? wp_unslash($_POST['nonce']) : '';
		$nonce = is_scalar($nonce_raw) ? sanitize_text_field((string) $nonce_raw) : '';

		if (!$nonce || !wp_verify_nonce($nonce, 'firebox_onboarding_complete'))
		{
			wp_send_json_error('Invalid nonce');
		}

		if (!current_user_can('manage_options'))
		{
			wp_send_json_error('Insufficient permissions');
		}

		$status_raw = isset($_POST['status']) ? wp_unslash($_POST['status']) : self::ONBOARDING_STATUS_COMPLETED;
		$status = is_scalar($status_raw) ? sanitize_text_field((string) $status_raw) : self::ONBOARDING_STATUS_COMPLETED;
		if (!in_array($status, [self::ONBOARDING_STATUS_COMPLETED, self::ONBOARDING_STATUS_SKIPPED], true))
		{
			wp_send_json_error('Invalid onboarding status');
		}

		$onboarding = new self();
		$onboarding->markOnboardingCompleted($status);

		wp_send_json_success([
			'message' => __('Onboarding completed successfully', 'firebox'),
		]);
	}
}
