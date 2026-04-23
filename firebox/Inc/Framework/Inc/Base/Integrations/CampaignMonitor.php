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

class CampaignMonitor extends Integration
{
	/**
	 * Create a new instance.
	 *
	 * @param   array  $options
	 *
	 * @throws  \Exception
	 */
	public function __construct($options)
	{
		$this->setKey(isset($options['api']) ? $options['api'] : $options);
		$this->setEndpoint('https://api.createsend.com/api/v3.3');

		$this->headers = array_merge($this->headers, [
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'Authorization' => 'Basic ' . base64_encode($this->key . ':x')
		]);
	}

	/**
	 * Subscribe a contact to a list.
	 *
	 * @param   string   $email
	 * @param   array    $params
	 * @param   string   $list_id
	 * @param   bool     $update_existing
	 * @param   bool     $double_optin
	 *
	 * @return  bool
	 */
	public function subscribe($email, $params = [], $list_id = '', $update_existing = true, $double_optin = false)
	{
		$email = trim((string) $email);
		$list_id = trim((string) $list_id);

		if ($email === '' || $list_id === '')
		{
			$this->throwError(new \WP_Error('campaignmonitor_invalid_payload', 'Campaign Monitor requires an email address and list.'));
			return false;
		}

		$name = trim((string) $this->getCustomFieldValue(['name', 'full_name', 'fullname'], (array) $params));
		if ($name === '')
		{
			$first = trim((string) $this->getCustomFieldValue(['first_name', 'firstname', 'fname'], (array) $params));
			$last = trim((string) $this->getCustomFieldValue(['last_name', 'lastname', 'lname'], (array) $params));
			$name = trim($first . ' ' . $last);
		}

		$payload = [
			'EmailAddress' => $email,
			'Name' => $name,
			'Resubscribe' => (bool) $update_existing,
			'RestartSubscriptionBasedAutoresponders' => !$double_optin,
			'ConsentToTrack' => 'Unchanged'
		];

		$custom_fields = $this->mapCustomFields($params);
		if (!empty($custom_fields))
		{
			$payload['CustomFields'] = $custom_fields;
		}

		$this->post('subscribers/' . rawurlencode($list_id) . '.json', $payload);

		return $this->success();
	}

	/**
	 * Return all available lists across clients.
	 *
	 * @return  array
	 */
	public function getLists()
	{
		$clients = $this->get('clients.json');
		if (!$this->success() || !is_array($clients))
		{
			return [];
		}

		$lists = [];
		foreach ($clients as $client)
		{
			if (!is_array($client) || empty($client['ClientID']))
			{
				continue;
			}

			$client_id = trim((string) $client['ClientID']);
			if ($client_id === '')
			{
				continue;
			}

			$client_lists = $this->get('clients/' . rawurlencode($client_id) . '/lists.json');
			if (!$this->success() || !is_array($client_lists))
			{
				continue;
			}

			foreach ($client_lists as $list)
			{
				if (!is_array($list))
				{
					continue;
				}

				$id = isset($list['ListID']) ? trim((string) $list['ListID']) : '';
				$name = isset($list['Name']) ? trim((string) $list['Name']) : '';

				if ($id === '' || $name === '')
				{
					continue;
				}

				$lists[] = [
					'id' => $id,
					'name' => $name
				];
			}
		}

		return $lists;
	}

	/**
	 * Verify API credentials.
	 *
	 * @return  bool
	 */
	public function verifyConnection()
	{
		$this->get('clients.json');

		return $this->success();
	}

	/**
	 * Return normalized error message.
	 *
	 * @return  string
	 */
	public function getLastError()
	{
		if ($error = parent::getLastError())
		{
			return $error;
		}

		$body = isset($this->last_response['body']) ? $this->last_response['body'] : [];
		if (!is_array($body))
		{
			return '';
		}

		if (!empty($body['Message']))
		{
			return (string) $body['Message'];
		}

		if (!empty($body['Code']))
		{
			return (string) $body['Code'];
		}

		if (!empty($body['Result']) && is_array($body['Result']) && !empty($body['Result']['Message']))
		{
			return (string) $body['Result']['Message'];
		}

		return '';
	}

	/**
	 * Map form params to Campaign Monitor custom fields.
	 *
	 * @param   array  $params
	 *
	 * @return  array
	 */
	private function mapCustomFields($params = [])
	{
		if (!is_array($params))
		{
			return [];
		}

		$excluded = ['email', 'name', 'full_name', 'fullname', 'first_name', 'firstname', 'fname', 'last_name', 'lastname', 'lname'];
		$fields = [];

		foreach ($params as $key => $value)
		{
			$key = trim((string) $key);
			if ($key === '' || in_array(strtolower($key), $excluded, true))
			{
				continue;
			}

			$value = $this->normalizeValue($value);
			if ($value === '')
			{
				continue;
			}

			$fields[] = [
				'Key' => $key,
				'Value' => $value
			];
		}

		return $fields;
	}

	/**
	 * Normalize value.
	 *
	 * @param   mixed  $value
	 *
	 * @return  string
	 */
	private function normalizeValue($value)
	{
		if (is_array($value))
		{
			$clean = array_filter(array_map(function($item) {
				return trim((string) $item);
			}, $value));

			return implode(', ', $clean);
		}

		if (is_bool($value))
		{
			return $value ? '1' : '0';
		}

		if (!is_scalar($value) && $value !== null)
		{
			return '';
		}

		return trim((string) $value);
	}
}
