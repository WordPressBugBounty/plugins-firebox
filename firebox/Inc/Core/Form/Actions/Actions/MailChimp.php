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

namespace FireBox\Core\Form\Actions\Actions;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class MailChimp extends \FireBox\Core\Form\Actions\Action
{
	protected function prepare()
	{
		$attrs = isset($this->form_settings['attrs']) && is_array($this->form_settings['attrs']) ? $this->form_settings['attrs'] : [];
		$integration_settings = isset($attrs['integrationSettings']['mailchimp']) && is_array($attrs['integrationSettings']['mailchimp']) ? $attrs['integrationSettings']['mailchimp'] : [];

		$this->action_settings = [
			'api_key' => \FireBox\Core\Helpers\Integrations::resolveActionAPIKey('mailchimp', $attrs),
			'list_id' => isset($integration_settings['listId']) ? trim((string) $integration_settings['listId']) : (isset($attrs['mailchimpListID']) ? trim((string) $attrs['mailchimpListID']) : ''),
			'doubleoptin' => isset($integration_settings['doubleOptin']) ? (bool) $integration_settings['doubleOptin'] : (isset($attrs['mailchimpDoubleOptin']) ? $attrs['mailchimpDoubleOptin'] : false),
			'updateexisting' => isset($integration_settings['updateExisting']) ? (bool) $integration_settings['updateExisting'] : (isset($attrs['mailchimpUpdateExisting']) ? $attrs['mailchimpUpdateExisting'] : true),
			
		];
	}

	/**
	 * Runs the action.
	 * 
	 * @throws  Exception
	 * 
	 * @return  void
	 */
	public function run()
	{
		$api = new \FPFramework\Base\Integrations\MailChimp([
			'api' => $this->action_settings['api_key']
		]);

		

		$api->subscribe(
			$this->action_settings['list_id'],
			$this->submission['prepared_fields']['email']['value'],
			$this->field_values,
			$this->action_settings['doubleoptin'],
			$this->action_settings['updateexisting'],
			
		);
		
		if (!$api->success())
		{
			$error = $api->getLastError();
			$error_parts = explode(' ', $error);

			if (function_exists('mb_strpos'))
			{
				// Make MalChimp errors translatable
				if (mb_strpos($error, 'is already a list member') !== false)
				{
					$error = sprintf(fpframework()->_('FPF_ERROR_USER_ALREADY_EXIST'), $error_parts[0]);
				}
	
				if (mb_strpos($error, 'fake or invalid') !== false)
				{
					$error = sprintf(fpframework()->_('FPF_ERROR_INVALID_EMAIL_ADDRESS'), $error_parts[0]);
				}
			}

			throw new \Exception(esc_html($error));
		}

		return true;
	}

	/**
	 * Validates the action prior to running it.
	 * 
	 * @return  void
	 */
	public function validate()
	{
		if (empty($this->action_settings['api_key']))
		{
			throw new \Exception(esc_html(sprintf(firebox()->_('FB_INTEGRATION_ERROR_NO_API_KEY_SET'), 'MailChimp')));
		}

		if (empty($this->action_settings['list_id']))
		{
			throw new \Exception(esc_html(sprintf(firebox()->_('FB_INTEGRATION_ERROR_NO_LIST_SELECTED'), 'MailChimp')));
		}

		return true;
	}

	
}
