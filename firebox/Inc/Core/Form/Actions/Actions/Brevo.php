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

namespace FireBox\Core\Form\Actions\Actions;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Brevo extends \FireBox\Core\Form\Actions\Action
{
	protected function prepare()
	{
		$attrs = isset($this->form_settings['attrs']) && is_array($this->form_settings['attrs']) ? $this->form_settings['attrs'] : [];
		$integration_settings = isset($attrs['integrationSettings']['brevo']) && is_array($attrs['integrationSettings']['brevo']) ? $attrs['integrationSettings']['brevo'] : [];
		
		$this->action_settings = [
			'api_key' => \FireBox\Core\Helpers\Integrations::resolveActionAPIKey('brevo', $attrs),
			'list_id' => isset($integration_settings['listId']) ? trim((string) $integration_settings['listId']) : (isset($attrs['brevoListID']) ? trim((string) $attrs['brevoListID']) : ''),
			'updateexisting' => isset($integration_settings['updateExisting']) ? (bool) $integration_settings['updateExisting'] : (isset($attrs['brevoUpdateExisting']) ? $attrs['brevoUpdateExisting'] : true),
			'doubleoptin' => isset($attrs['brevoDoubleOptin']) ? $attrs['brevoDoubleOptin'] : false,
			'doubleoptin_redirect_url' => isset($attrs['brevoDOIRedirectURL']) ? $attrs['brevoDOIRedirectURL'] : '',
			'doubleoptin_template_id' => isset($attrs['brevoDOITemplateID']) ? $attrs['brevoDOITemplateID'] : ''
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
		$api = new \FPFramework\Base\Integrations\Brevo([
			'api' => $this->action_settings['api_key']
		]);

		$api->subscribe(
			$this->submission['prepared_fields']['email']['value'],
			$this->field_values,
			$this->action_settings['list_id'],
			$this->action_settings['updateexisting'],
			$this->action_settings['doubleoptin'],
			$this->action_settings['doubleoptin_redirect_url'],
			$this->action_settings['doubleoptin_template_id']
		);
		
		if (!$api->success())
		{
			throw new \Exception(esc_html($api->getLastError()));
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
			throw new \Exception(esc_html(sprintf(firebox()->_('FB_INTEGRATION_ERROR_NO_API_KEY_SET'), 'Brevo')));
		}

		if (empty($this->action_settings['list_id']))
		{
			throw new \Exception(esc_html(sprintf(firebox()->_('FB_INTEGRATION_ERROR_NO_LIST_SELECTED'), 'Brevo')));
		}

		return true;
	}
}
