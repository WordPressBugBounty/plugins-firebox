<?php
/**
 * @package         FireBox
 * @version         3.1.14 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Form;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use \FireBox\Core\Helpers\Form\Form;
use \FireBox\Core\Helpers\BoxHelper;

class Ajax
{
	/**
	 * Accepted submissions one client may make per minute.
	 *
	 * Set well above anything a person produces — the target is a script replaying a
	 * valid submission, not a fast typist. Only submissions that pass validation count,
	 * so the headroom also absorbs the cases where several visitors look like one
	 * client: an office or campus behind a single address, or a proxy that does not
	 * pass the visitor's address on.
	 *
	 * Sites can tune this through the firebox/rate_limit/limit filter, which switches
	 * the throttle off entirely when it returns 0.
	 *
	 * @var  int
	 */
	const SUBMISSIONS_PER_MINUTE = 60;

	public function __construct()
	{
		$this->setupAjax();

		new Actions\Ajax();
    }
    
	/**
	 * Setup ajax requests
	 * 
	 * @return  void
	 */
	public function setupAjax()
	{
		add_action('wp_ajax_fb_form_submission_status_change', [$this, 'fb_form_submission_status_change']);

		add_action('wp_ajax_fb_form_submit', [$this, 'fb_form_submit']);
        add_action('wp_ajax_nopriv_fb_form_submit', [$this, 'fb_form_submit']);
    }

	/**
	 * Update submission status.
	 * 
	 * @return  void
	 */
	public function fb_form_submission_status_change()
	{
		if (!current_user_can('edit_fireboxes'))
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'You are not allowed to do this.'
			]);
			wp_die();
		}

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fb_form_submission_action'))
        {
			echo wp_json_encode([
				'error' => true,
				'message' => 'Cannot verify request.'
			]);
			wp_die();
        }

		$submission_id = isset($_POST['submission_id']) ? absint($_POST['submission_id']) : 0;
		$new_state = isset($_POST['new_state']) ? sanitize_key(wp_unslash($_POST['new_state'])) : '';

		$new_state = $new_state === 'publish' ? 1 : 0;

		// Confirm the submission exists before reporting anything about it.
		if (!$submission_id || !\FireBox\Core\Helpers\Form\Submission::exists($submission_id))
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'Submission state couldn\'t be updated.'
			]);
			wp_die();
		}

		if (!\FireBox\Core\Helpers\Form\Submission::updateState($submission_id, $new_state))
		{
			echo wp_json_encode([
				// This branch means the write failed, so report it as an error.
				'error' => true,
				'message' => 'Submission state couldn\'t be updated.'
			]);
			wp_die();
		}

		echo wp_json_encode([
			'error' => false,
			'message' => 'Submission state updated successfully.'
		]);
		wp_die();
	}
    
    /**
     * Form submit.
     * 
     * @return void
     */
    public function fb_form_submit()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fbox_js_nonce'))
        {
			/**
			 * A full-page cache routinely outlives the 12-24h nonce baked into the cached
			 * HTML. Name the cause so the client can fetch a fresh nonce and replay the
			 * submission once, instead of losing the lead.
			 */
			echo wp_json_encode([
				'error' => 'invalid_nonce',
				'message' => firebox()->_('FB_FORM_CANNOT_VERIFY_REQUEST')
			]);
			wp_die();
        }

		/**
		 * The nonce above is printed into every page a campaign renders on and is the
		 * same for every logged-out visitor, so it does not bound how often this endpoint
		 * can be called. An accepted submission writes several rows and may send mail and
		 * hit third-party APIs, so throttle per client.
		 *
		 * Only the check happens here. The hit is recorded further down, once the
		 * submission has passed the honeypot, the captcha and validation — see
		 * RATE LIMIT below. Counting rejected attempts would let a spam bot exhaust an
		 * allowance that real visitors may be sharing: wherever a proxy leaves many
		 * visitors looking like one client, junk traffic would lock all of them out of a
		 * form they are entitled to use. It would also spend a visitor's own allowance on
		 * their typos.
		 */
		if (\FireBox\Core\Helpers\RateLimit::isLimited('form_submit', self::SUBMISSIONS_PER_MINUTE))
		{
			echo wp_json_encode([
				'error' => true,
				'message' => firebox()->_('FB_FORM_TOO_MANY_SUBMISSIONS')
			]);
			wp_die();
		}

		$form_data = isset($_POST['form_data']) ? sanitize_text_field(wp_unslash($_POST['form_data'])) : '';
		$form_data = $form_data ? json_decode(stripslashes($form_data), true) : '';
		
		if (!$form_data)
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'Cannot submit form.'
			]);
			wp_die();
		}

		$form_id = isset($form_data['form_id']) ? $form_data['form_id'] : false;
		if (!$form_id)
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'Missing Form ID.'
			]);
			wp_die();
		}

		$values = isset($form_data['fields']) ? $form_data['fields'] : false;
		if (!$values || !is_array($values))
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'Missing submission data.'
			]);
			wp_die();
		}

		$form_id = str_replace('form-', '', $form_id);
		if (!$form = Form::getFormByID($form_id))
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'This form does not exist.'
			]);
			wp_die();
		}

		// Forms live inside campaigns; only a published campaign may accept submissions from the frontend.
		if (empty($form['state']) || $form['state'] !== '1')
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'This form does not exist.'
			]);
			wp_die();
		}

		$form_block = $form['block'];
		$form_fields = $form['fields'];

		// Get the Campaign ID
		$box_id = isset($_POST['box_id']) ? sanitize_key(wp_unslash($_POST['box_id'])) : false;

		// Get box
		$box = firebox()->box->get($box_id);

		// Allow to hook into the form submission process and validate the submission data
		try {
			$values = apply_filters('firebox/form/process', $values, $box, $form_id);
		}
		catch (\Exception $e)
		{
			echo wp_json_encode([
				'error' => true,
				'message' => wp_kses_post($e->getMessage())
			]);
			wp_die();
		}
		
		try {
			$validated_fields = Form::validate($form_fields, $values);
		}
		catch (\Exception $e)
		{
			echo wp_json_encode([
				'error' => true,
				'message' => wp_kses_post($e->getMessage())
			]);
			wp_die();
		}

		if (isset($validated_fields['error']))
		{
			$payload = [
				'error' => true,
				'message' => isset($validated_fields['message']) ? $validated_fields['message'] : 'Form is invalid.'
			];

			if (is_array($validated_fields['error']))
			{
				$payload['validation'] = $validated_fields['error'];
			}
			echo wp_json_encode($payload);
			wp_die();
		}

		/**
		 * RATE LIMIT: the submission is accepted from here on, and everything below it
		 * costs something — rows written, mail sent, integrations called. This is the
		 * point worth rationing, so the hit is recorded here rather than on arrival.
		 */
		\FireBox\Core\Helpers\RateLimit::hit('form_submit', MINUTE_IN_SECONDS);

		$submission = [];
		$submission_meta_data = [];

		/**
		 * Also set the popup log id in field values.
		 * 
		 * This is useful for analytics purposes, i.e. to track form conversions.
		 */
		$box_log_id = isset($_POST['box_log_id']) && !empty($_POST['box_log_id']) ? sanitize_key(wp_unslash($_POST['box_log_id'])) : false;
		if ($box_log_id)
		{
			$submission_meta_data['box_log_id'] = $box_log_id;
		}

		// $submission_meta_data is the raw submitted data that are saved in the database
		foreach ($validated_fields as $field)
		{
			$field_id = $field->getOptionValue('id');
			$field_name = $field->getOptionValue('name');

			if (!isset($values[$field_name]))
			{
				continue;
			}
			
			$submission_meta_data[$field_id] = $values[$field_name];
		}

		// Determine whether to store the submission and store it
		$storeSubmissions = isset($form_block['attrs']['storeSubmissions']) ? $form_block['attrs']['storeSubmissions'] : true;
		if (!$submission = Form::storeSubmission($form_id, $form_block, $validated_fields, $submission_meta_data, $storeSubmissions))
		{
			echo wp_json_encode([
				'error' => true,
				'message' => 'Could not save submission. Please try again.'
			]);
			wp_die();
		}

		/**
		 * Track conversion after storing the submission.
		 *
		 * box_log_id arrives from the client, so it is only trusted once we have
		 * confirmed it refers to a real impression of this campaign. Otherwise any
		 * submitter could attribute their conversion to any campaign.
		 */
		if ($box_log_id && firebox()->tables->boxlog->belongsToCampaign($box_log_id, $box_id))
		{
			$factory = new \FPFramework\Base\Factory();
			$data = [
				'log_id' => (int) $box_log_id,
				'event' => 'conversion',
				'event_source' => 'form',
				'event_label' => 'FireBox #' . (int) $box_id . ' Form',
				'date' => $factory->getDate()->format('Y-m-d H:i:s')
			];

			firebox()->tables->boxlogdetails->insert($data);
		}

		// Replace Smart Tags in form attributes
		Form::replaceSmartTags($form_block['attrs'], $values, $submission);

		// Determine whether to run actions and run them
		if (isset($form_block['attrs']['actions']) && is_array($form_block['attrs']['actions']) && count($form_block['attrs']['actions']))
		{
			if ($box_id)
			{
				$submission['box_id'] = (int) $box_id;
			}

			$actions = new \FireBox\Core\Form\Actions\Actions($form_block, $submission);
			if (!$actions->run())
			{
				echo wp_json_encode([
					'error' => true,
					'message' => wp_kses_post($actions->getErrorMessage())
				]);
				wp_die();
			}
		}

		$action = Form::getSubmissionAction($form_block['attrs']);

		/**
		 * Fires after a successful form submission.
		 * 
		 * @param  array  $box  	   The campaign settings
		 * @param  array  $values      The form values
		 * @param  array  $submission  The submission
		 */
		do_action('firebox/form/success', $box, $values, $submission);

		/**
		 * Allow to hook into the success action and customize it.
		 * 
		 * @param   array  $action
		 */
		$action = apply_filters('firebox/form/submit_action', $action);

		echo wp_json_encode(array_merge([
			'error' => false
		], $action));
		wp_die();
    }
}