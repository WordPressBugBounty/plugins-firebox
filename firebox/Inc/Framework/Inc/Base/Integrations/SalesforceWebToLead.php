<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.145
 *
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\Integrations;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class SalesforceWebToLead extends Integration
{
	/**
	 * Service API Endpoint.
	 *
	 * @var  string
	 */
	protected $endpoint = 'https://{{ENV}}.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8';

	/**
	 * Encode data before sending the request.
	 *
	 * @var  bool
	 */
	protected $encode = false;

	/**
	 * Create a new instance.
	 *
	 * @param   array|string  $options
	 */
	public function __construct($options = [])
	{
		$options = is_array($options) ? $options : ['api' => $options];

		$this->setKey(isset($options['api']) ? $options['api'] : $options);
		$this->prepareEndpoint($options);
		$this->response_type = 'text';

		$this->headers = array_merge($this->headers, [
			'Content-Type' => 'application/x-www-form-urlencoded'
		]);
	}

	/**
	 * Prepare endpoint based on mode.
	 *
	 * @param   array  $options
	 *
	 * @return  void
	 */
	private function prepareEndpoint($options = [])
	{
		$options = is_array($options) ? $options : [];
		$env = !empty($options['test_mode']) ? 'test' : 'webto';

		$this->setEndpoint(str_replace('{{ENV}}', $env, $this->endpoint));
	}

	/**
	 * Subscribe user to Salesforce.
	 *
	 * @param   string  $email
	 * @param   array   $params
	 *
	 * @return  bool
	 */
	public function subscribe($email, $params = [])
	{
		$data = [
			'email' => trim((string) $email),
			'oid' => $this->key
		];

		if (is_array($params) && count($params))
		{
			$data = array_merge($data, $params);
		}

		$this->post('', $data);

		return true;
	}

	/**
	 * Determine if the lead has been stored successfully in Salesforce.
	 *
	 * @return  bool
	 */
	protected function determineSuccess()
	{
		if (is_wp_error($this->last_response))
		{
			return false;
		}

		$status = 0;
		if (is_array($this->last_response) && isset($this->last_response['response']['code']))
		{
			$status = (int) $this->last_response['response']['code'];
		}

		if ($status < 200 || $status > 299)
		{
			return false;
		}

		$is_processed = (string) wp_remote_retrieve_header($this->last_response, 'Is-Processed');
		if ($is_processed !== '' && strpos($is_processed, 'Exception') !== false)
		{
			$this->last_error = 'Salesforce Web-to-Lead request failed.';
			return false;
		}

		return ($this->request_successful = true);
	}

	/**
	 * Return a compatibility list selector.
	 *
	 * @return  array
	 */
	public function getLists()
	{
		return [
			[
				'id' => 'default',
				'name' => 'Default Lead Route'
			]
		];
	}

	/**
	 * Verify the stored OID format.
	 *
	 * @return  bool
	 */
	public function verifyConnection()
	{
		if (!$this->isValidOid($this->key))
		{
			$this->throwError(new \WP_Error('salesforce_invalid_oid', 'Salesforce OID is invalid.'));
			return false;
		}

		$this->setSuccessful(true);

		return true;
	}

	/**
	 * Validate Salesforce OID format.
	 *
	 * @param   string  $oid
	 *
	 * @return  bool
	 */
	private function isValidOid($oid = '')
	{
		$oid = trim((string) $oid);

		return (bool) preg_match('/^[a-zA-Z0-9]{15,18}$/', $oid);
	}
}
