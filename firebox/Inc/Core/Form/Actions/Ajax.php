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

namespace FireBox\Core\Form\Actions;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Ajax
{
	public function __construct()
	{
		$this->setupAjax();
    }
    
	/**
	 * Setup ajax requests
	 * 
	 * @return  void
	 */
	public function setupAjax()
	{
		add_action('wp_ajax_fb_get_integration_lists', [$this, 'fb_get_integration_lists']);
		add_action('wp_ajax_fb_connect_integration', [$this, 'fb_connect_integration']);
		add_action('wp_ajax_fb_disconnect_integration', [$this, 'fb_disconnect_integration']);
    }

	/**
	 * Retrieve the Integration list given the API Key.
	 * 
	 * @return  void
	 */
	public function fb_get_integration_lists()
	{
		$this->canRun();

		if (!$this->verifyNonce())
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_CANNOT_VERIFY_REQUEST')
			]);
		}

		$integrationClassName = $this->getPostedIntegrationClassName();
		$managedIntegration = \FireBox\Core\Helpers\Integrations::getIntegration($integrationClassName);
		if ($managedIntegration && \FireBox\Core\Helpers\Integrations::isLocked($integrationClassName))
		{
			$this->sendResponse([
				'error' => true,
				'code' => 'locked',
				'message' => $this->getLockedIntegrationMessage($integrationClassName)
			]);
		}

		$connectionType = \FireBox\Core\Helpers\Integrations::getConnectionType($integrationClassName);
		$isApiKeyConnection = $connectionType === 'api_key';
		$apiKeyParamRaw = isset($_POST['api_key']) ? wp_unslash($_POST['api_key']) : null;
		$apiKeyParam = is_scalar($apiKeyParamRaw) ? trim((string) $apiKeyParamRaw) : null;
		$postedCredentials = $this->getPostedCredentials($integrationClassName);
		$connectionValue = '';
		$connectedVia = '';

		// Resolve API key
		if ($isApiKeyConnection)
		{
			if ($this->hasProvidedCredentialValues($postedCredentials))
			{
				$connectionValue = \FireBox\Core\Helpers\Integrations::getConnectionValue($integrationClassName, $postedCredentials);
				$connectedVia = 'legacy';
			}
			else if ($apiKeyParam !== null && $apiKeyParam !== '' && $apiKeyParam !== 'skip')
			{
				$connectionValue = $apiKeyParam;
				$connectedVia = 'legacy';
			}
			else if ($globalConnectionValue = \FireBox\Core\Helpers\Integrations::getGlobalConnectionValue($integrationClassName))
			{
				$connectionValue = $globalConnectionValue;
				$connectedVia = 'global';
			}
			else
			{
				$this->sendResponse([
					'error' => true,
					'code' => 'not_connected',
					'message' => sprintf(firebox()->_('FB_INTEGRATION_CONNECT_FIRST_IN_SETTINGS'), $this->getIntegrationLabel($integrationClassName)),
					'help' => '<a href="' . esc_url(admin_url('admin.php?page=firebox-settings#integrations')) . '" target="_blank" rel="noreferrer noopener">' . esc_html(fpframework()->_('FPF_INTEGRATIONS')) . '</a>'
				]);
			}
		}
		// Plugin/non-auth integrations
		else
		{
			// Keep legacy "skip" sentinel so framework integrations can bypass API-key validation paths.
			$connectionValue = ($apiKeyParam === null || $apiKeyParam === '') ? 'skip' : (string) $apiKeyParam;
		}

		// Build integration client
		$class = '\FPFramework\Base\Integrations\\' . $integrationClassName;
		if (!class_exists($class))
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_NO_SUCH_INTEGRATION_EXISTS')
			]);
		}

		try {
			$integrationClass = new $class([
				'api' => $connectionValue
			]);
		}
		catch (\Throwable $e)
		{
			$this->sendResponse([
				'error' => true,
				'message' => $this->formatIntegrationErrorMessage($e->getMessage(), $integrationClassName)
			]);
		}

		// Ensure getLists method exists
		if (!method_exists($integrationClass, 'getLists'))
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_INTEGRATION_INVALID')
			]);
		}

		$lists = [];
		try {
			$lists = $integrationClass->getLists();
		}
		catch (\Throwable $e)
		{
			$this->sendResponse([
				'error' => true,
				'message' => $this->formatIntegrationErrorMessage($e->getMessage(), $integrationClassName)
			]);
		}

		$requestSuccessful = method_exists($integrationClass, 'success') ? $integrationClass->success() : true;
		if (!$requestSuccessful)
		{
			$errorMessage = method_exists($integrationClass, 'getLastError') ? $integrationClass->getLastError() : '';
			$errorMessage = trim(wp_strip_all_tags((string) $errorMessage));

			// Some plugin-based integrations don't explicitly set success() for list retrieval.
			// Only treat unsuccessful state as fatal when an actual error message exists.
			if ($errorMessage !== '')
			{
				$this->sendResponse([
					'error' => true,
					'message' => $this->formatIntegrationErrorMessage($errorMessage, $integrationClassName)
				]);
			}
		}

		if (!is_array($lists) || !count($lists))
		{
			$this->sendResponse([
				'error' => true,
				'message' => sprintf(fpframework()->_('FPF_INTEGRATION_ACCOUNT_HAS_NO_LISTS'), $this->getIntegrationLabel($integrationClassName))
			]);
		}

		$responseLists = [
			[
				'label' => fpframework()->_('FPF_SELECT_A_LIST'),
				'value' => null
			]
		];

		foreach ($lists as $list)
		{
			$listName = '';
			$listID = '';

			if (is_array($list))
			{
				$listName = isset($list['name']) ? trim(wp_strip_all_tags((string) $list['name'])) : '';
				$listID = isset($list['id']) && is_scalar($list['id']) ? (string) $list['id'] : '';
			}
			else if (is_object($list))
			{
				$listName = isset($list->name) ? trim(wp_strip_all_tags((string) $list->name)) : '';
				$listID = isset($list->id) && is_scalar($list->id) ? (string) $list->id : '';
			}

			if ($listName === '' || $listID === '')
			{
				continue;
			}

			$responseLists[] = [
				'label' => $listName,
				'value' => $listID
			];
		}

		if (count($responseLists) === 1)
		{
			$this->sendResponse([
				'error' => true,
				'message' => sprintf(fpframework()->_('FPF_INTEGRATION_ACCOUNT_HAS_NO_LISTS'), $this->getIntegrationLabel($integrationClassName))
			]);
		}

		$payload = [
			'error' => false,
			'message' => [
				'lists' => $responseLists
			]
		];

		if (!empty($connectedVia))
		{
			$payload['connected_via'] = $connectedVia;
		}

		$this->sendResponse($payload);
	}

	/**
	 * Connect an integration and persist API key in global settings.
	 *
	 * @return  void
	 */
	public function fb_connect_integration()
	{
		$this->canRun();

		if (!$this->verifyNonce())
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_CANNOT_VERIFY_REQUEST')
			]);
		}

		$integrationClassName = $this->getPostedIntegrationClassName();
		$managedIntegration = \FireBox\Core\Helpers\Integrations::getIntegration($integrationClassName);

		if (!$managedIntegration)
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_NO_SUCH_INTEGRATION_EXISTS')
			]);
		}

		if (\FireBox\Core\Helpers\Integrations::isLocked($integrationClassName))
		{
			$this->sendResponse([
				'error' => true,
				'message' => $this->getLockedIntegrationMessage($integrationClassName)
			]);
		}

		if (\FireBox\Core\Helpers\Integrations::getConnectionType($integrationClassName) !== 'api_key')
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_INTEGRATION_INVALID')
			]);
		}

		$credentials = $this->getPostedCredentials($integrationClassName);
		if ($missingField = $this->getMissingRequiredCredentialField($integrationClassName, $credentials))
		{
			$this->sendResponse([
				'error' => true,
				// translators: %s: Missing credential field label.
				'message' => sprintf(__('Please enter %s.', 'firebox'), $missingField)
			]);
		}

		$connectionValue = \FireBox\Core\Helpers\Integrations::getConnectionValue($integrationClassName, $credentials);

		$class = '\FPFramework\Base\Integrations\\' . $integrationClassName;
		if (!class_exists($class))
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_NO_SUCH_INTEGRATION_EXISTS')
			]);
		}

		try {
			$integrationInstance = new $class([
				'api' => $connectionValue
			]);
		}
		catch (\Throwable $e)
		{
			$this->sendResponse([
				'error' => true,
				'message' => $this->formatIntegrationErrorMessage($e->getMessage(), $integrationClassName)
			]);
		}

		if (!method_exists($integrationInstance, 'verifyConnection'))
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_INTEGRATION_INVALID')
			]);
		}

		try {
			$integrationInstance->verifyConnection();
		}
		catch (\Throwable $e)
		{
			$this->sendResponse([
				'error' => true,
				'message' => $this->formatIntegrationErrorMessage($e->getMessage(), $integrationClassName)
			]);
		}

		if (method_exists($integrationInstance, 'success') && !$integrationInstance->success())
		{
			$errorMessage = method_exists($integrationInstance, 'getLastError') ? $integrationInstance->getLastError() : '';
			$errorMessage = $this->formatIntegrationErrorMessage($errorMessage, $integrationClassName);

			$this->sendResponse([
				'error' => true,
				'message' => $errorMessage
			]);
		}

		\FireBox\Core\Helpers\Integrations::setGlobalCredentials($integrationClassName, $credentials);

		$payload = [
			'error' => false,
			'message' => sprintf(firebox()->_('FB_INTEGRATION_CONNECTED_SUCCESS'), $this->getIntegrationLabel($integrationClassName)),
			'connected' => true
		];

		$this->sendResponse($payload);
	}

	/**
	 * Disconnect integration from global settings.
	 *
	 * @return  void
	 */
	public function fb_disconnect_integration()
	{
		$this->canRun();

		if (!$this->verifyNonce())
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_CANNOT_VERIFY_REQUEST')
			]);
		}

		$integrationClassName = $this->getPostedIntegrationClassName();
		$managedIntegration = \FireBox\Core\Helpers\Integrations::getIntegration($integrationClassName);

		if (!$managedIntegration)
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_NO_SUCH_INTEGRATION_EXISTS')
			]);
		}

		if (\FireBox\Core\Helpers\Integrations::isLocked($integrationClassName))
		{
			$this->sendResponse([
				'error' => true,
				'message' => $this->getLockedIntegrationMessage($integrationClassName)
			]);
		}

		if (\FireBox\Core\Helpers\Integrations::getConnectionType($integrationClassName) !== 'api_key')
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_INTEGRATION_INVALID')
			]);
		}

		\FireBox\Core\Helpers\Integrations::setGlobalCredentials($integrationClassName, []);

		$this->sendResponse([
			'error' => false,
			'message' => sprintf(firebox()->_('FB_INTEGRATION_DISCONNECTED_SUCCESS'), $this->getIntegrationLabel($integrationClassName)),
			'connected' => false
		]);
	}

	/**
	 * Verify AJAX nonce.
	 *
	 * @return  bool
	 */
	private function verifyNonce()
	{
		$nonceRaw = isset($_POST['nonce']) ? wp_unslash($_POST['nonce']) : '';
		$nonce = is_scalar($nonceRaw) ? sanitize_text_field((string) $nonceRaw) : '';
		return wp_verify_nonce($nonce, 'fpf_js_nonce');
	}

	/**
	 * Ensure user can run integration requests.
	 *
	 * @return  void
	 */
	private function canRun()
	{
		if (!current_user_can('read_fireboxes'))
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_CANNOT_VERIFY_REQUEST')
			]);
		}
	}

	/**
	 * Sends JSON response and terminates request.
	 *
	 * @param   array  $payload
	 *
	 * @return  void
	 */
	private function sendResponse($payload = [])
	{
		wp_send_json($payload);
	}

	/**
	 * Returns posted integration class name or sends an error response.
	 *
	 * @return  string
	 */
	private function getPostedIntegrationClassName()
	{
		$integrationRaw = isset($_POST['integration']) ? wp_unslash($_POST['integration']) : '';
		$integration = is_scalar($integrationRaw) ? sanitize_text_field((string) $integrationRaw) : '';
		if (!$integration)
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_NO_INTEGRATION_SUPPLIED')
			]);
		}

		$integrationClassName = $this->getIntegrationClassName($integration);
		if ($integrationClassName === '')
		{
			$this->sendResponse([
				'error' => true,
				'message' => fpframework()->_('FPF_NO_SUCH_INTEGRATION_EXISTS')
			]);
		}

		return $integrationClassName;
	}

	/**
	 * Normalizes integration to framework class name.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	private function getIntegrationClassName($integration = '')
	{
		if ($className = \FireBox\Core\Helpers\Integrations::getFrameworkIntegrationClassName($integration))
		{
			return trim((string) $className);
		}

		return '';
	}

	/**
	 * Returns sanitized posted credentials for an integration.
	 *
	 * @param   string  $integrationClassName
	 *
	 * @return  array
	 */
	private function getPostedCredentials($integrationClassName = '')
	{
		$credentials = [];
		$rawCredentials = isset($_POST['credentials']) ? wp_unslash($_POST['credentials']) : '';

		if (is_string($rawCredentials) && trim($rawCredentials) !== '')
		{
			$decoded = json_decode($rawCredentials, true);
			if (is_array($decoded))
			{
				$credentials = $decoded;
			}
		}

		$apiKeyRaw = isset($_POST['api_key']) ? wp_unslash($_POST['api_key']) : null;
		if (!array_key_exists('api_key', $credentials) && is_scalar($apiKeyRaw))
		{
			$credentials['api_key'] = (string) $apiKeyRaw;
		}

		$sanitized = [];
		foreach (\FireBox\Core\Helpers\Integrations::getCredentialFields($integrationClassName) as $field)
		{
			$key = $field['key'];
			$sanitized[$key] = isset($credentials[$key]) && is_scalar($credentials[$key])
				? trim((string) $credentials[$key])
				: '';
		}

		return $sanitized;
	}

	/**
	 * Returns whether any credential value has been supplied.
	 *
	 * @param   array  $credentials
	 *
	 * @return  bool
	 */
	private function hasProvidedCredentialValues($credentials = [])
	{
		foreach ((array) $credentials as $value)
		{
			if (trim((string) $value) !== '')
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns the first missing required credential label.
	 *
	 * @param   string  $integrationClassName
	 * @param   array   $credentials
	 *
	 * @return  string
	 */
	private function getMissingRequiredCredentialField($integrationClassName = '', $credentials = [])
	{
		foreach (\FireBox\Core\Helpers\Integrations::getCredentialFields($integrationClassName) as $field)
		{
			if (empty($field['required']))
			{
				continue;
			}

			$key = $field['key'];
			$value = isset($credentials[$key]) ? trim((string) $credentials[$key]) : '';
			if ($value === '')
			{
				return !empty($field['label']) ? $field['label'] : $key;
			}
		}

		return '';
	}

	/**
	 * Returns human readable integration label.
	 *
	 * @param   string  $integrationClassName
	 *
	 * @return  string
	 */
	private function getIntegrationLabel($integrationClassName = '')
	{
		$label = \FireBox\Core\Helpers\Integrations::getIntegrationLabel($integrationClassName);
		return !empty($label) ? $label : $integrationClassName;
	}

	/**
	 * Returns locked integration message with dynamic plan requirement.
	 *
	 * @param   string  $integrationClassName
	 *
	 * @return  string
	 */
	private function getLockedIntegrationMessage($integrationClassName = '')
	{
		$label = $this->getIntegrationLabel($integrationClassName);
		$plan_label = \FireBox\Core\Helpers\Integrations::getRequiredPlanLabel($integrationClassName);

		/* translators: 1: Integration label, 2: Required plan label. */
		return sprintf(__('%1$s is available in the %2$s plan.', 'firebox'), $label, $plan_label);
	}

	/**
	 * Formats integration error messages consistently.
	 *
	 * @param   string  $message
	 * @param   string  $integrationClassName
	 *
	 * @return  string
	 */
	private function formatIntegrationErrorMessage($message = '', $integrationClassName = '')
	{
		$message = trim(wp_strip_all_tags((string) $message));
		if ($message !== '')
		{
			return $message;
		}

		$integrationLabel = $this->getIntegrationLabel($integrationClassName);
		$connectionType = \FireBox\Core\Helpers\Integrations::getConnectionType($integrationClassName);
		if ($connectionType === 'plugin')
		{
			return sprintf(
				/* translators: %s: Integration label. */
				__('%s request failed. Please make sure the related plugin is installed and active.', 'firebox'),
				$integrationLabel
			);
		}

		return sprintf(
			/* translators: %s: Integration label. */
			__('%s request failed. Please verify the API key and permissions.', 'firebox'),
			$integrationLabel
		);
	}
}
