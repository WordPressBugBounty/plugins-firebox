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

class ActiveCampaign extends Integration
{
	/**
	 * Create a new instance.
	 *
	 * API credential format: {base_api_url}|{api_key}
	 * Example: https://accountname.api-us1.com|abcd1234
	 *
	 * @param   array  $options
	 *
	 * @throws  \Exception
	 */
	public function __construct($options)
	{
		$raw = isset($options['api']) ? (string) $options['api'] : (string) $options;
		list($endpoint, $api_key) = $this->parseCredentials($raw);

		$this->setKey($api_key);
		$this->setEndpoint($endpoint);

		$this->headers = array_merge($this->headers, [
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
			'Api-Token' => $this->key
		]);
	}

	/**
	 * Subscribe (or update) a contact in a list.
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
			$this->throwError(new \WP_Error('activecampaign_invalid_payload', 'ActiveCampaign requires an email address and list.'));
			return false;
		}

		$existing = $this->getContactByEmail($email);
		if ($existing && !$update_existing)
		{
			$this->throwError(new \WP_Error('activecampaign_contact_exists', 'Contact already exists.'));
			return false;
		}

		$contact_payload = [
			'contact' => [
				'email' => $email,
				'firstName' => trim((string) $this->getCustomFieldValue(['first_name', 'firstname', 'fname'], (array) $params)),
				'lastName' => trim((string) $this->getCustomFieldValue(['last_name', 'lastname', 'lname'], (array) $params)),
				'phone' => trim((string) $this->getCustomFieldValue(['phone', 'mobile', 'telephone', 'tel'], (array) $params)),
			]
		];

		$sync = $this->post('contact/sync', $contact_payload);
		if (!$this->success())
		{
			return false;
		}

		$contact_id = '';
		if (is_array($sync) && isset($sync['contact']) && is_array($sync['contact']) && isset($sync['contact']['id']))
		{
			$contact_id = trim((string) $sync['contact']['id']);
		}
		if ($contact_id === '' && $existing && isset($existing['id']))
		{
			$contact_id = trim((string) $existing['id']);
		}

		if ($contact_id === '')
		{
			return false;
		}

		$this->syncContactFields($contact_id, (array) $params);
		if (!$this->subscribeContactToList($contact_id, $list_id, $double_optin))
		{
			return false;
		}

		return true;
	}

	/**
	 * Return available lists.
	 *
	 * @return  array
	 */
	public function getLists($limit = 100)
	{
		$response = $this->get('lists', [
			'limit' => (int) $limit,
			'offset' => 0
		]);

		if (!$this->success() || !is_array($response) || !isset($response['lists']) || !is_array($response['lists']))
		{
			return [];
		}

		$lists = [];
		foreach ($response['lists'] as $list)
		{
			if (!is_array($list))
			{
				continue;
			}

			$id = isset($list['id']) ? trim((string) $list['id']) : '';
			$name = isset($list['name']) ? trim((string) $list['name']) : '';

			if ($id === '' || $name === '')
			{
				continue;
			}

			$lists[] = [
				'id' => $id,
				'name' => $name
			];
		}

		return $lists;
	}

	/**
	 * Verify credentials.
	 *
	 * @return  bool
	 */
	public function verifyConnection()
	{
		$this->get('users/me');

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

		if (!empty($body['message']))
		{
			return (string) $body['message'];
		}

		if (!empty($body['errors']))
		{
			if (is_array($body['errors']))
			{
				$flattened = [];
				foreach ($body['errors'] as $error_item)
				{
					if (is_array($error_item) && !empty($error_item['title']))
					{
						$flattened[] = (string) $error_item['title'];
						continue;
					}
					$flattened[] = trim((string) $error_item);
				}

				$flattened = array_filter($flattened);
				if (!empty($flattened))
				{
					return implode(' ', $flattened);
				}
			}
			else
			{
				return trim((string) $body['errors']);
			}
		}

		return '';
	}

	/**
	 * Parse raw credential value into endpoint + key.
	 *
	 * @param   string  $raw
	 *
	 * @return  array
	 */
	private function parseCredentials($raw = '')
	{
		$raw = trim((string) $raw);
		if ($raw === '')
		{
			throw new \Exception('Invalid API Key supplied.');
		}

		$parts = [];
		if (strpos($raw, '|') !== false)
		{
			$parts = array_map('trim', explode('|', $raw, 2));
		}
		else if (strpos($raw, PHP_EOL) !== false)
		{
			$parts = array_map('trim', preg_split('/\r\n|\r|\n/', $raw));
			$parts = array_values(array_filter($parts));
		}

		if (count($parts) < 2)
		{
			throw new \Exception('ActiveCampaign requires API URL and API Key separated by "|".');
		}

		$base_url = rtrim((string) $parts[0], '/');
		$api_key = trim((string) $parts[1]);

		if ($base_url === '' || $api_key === '')
		{
			throw new \Exception('ActiveCampaign requires API URL and API Key separated by "|".');
		}

		if (strpos($base_url, 'http://') !== 0 && strpos($base_url, 'https://') !== 0)
		{
			$base_url = 'https://' . $base_url;
		}

		return [$base_url . '/api/3', $api_key];
	}

	/**
	 * Return contact by email.
	 *
	 * @param   string  $email
	 *
	 * @return  array|false
	 */
	private function getContactByEmail($email)
	{
		$response = $this->get('contacts', [
			'email' => (string) $email,
			'limit' => 1
		]);

		if (!$this->success() || !is_array($response) || empty($response['contacts']) || !is_array($response['contacts']))
		{
			return false;
		}

		$contact = reset($response['contacts']);

		return is_array($contact) ? $contact : false;
	}

	/**
	 * Subscribe contact to a list.
	 *
	 * @param   string  $contact_id
	 * @param   string  $list_id
	 * @param   bool    $double_optin
	 *
	 * @return  bool
	 */
	private function subscribeContactToList($contact_id, $list_id, $double_optin = false)
	{
		$payload = [
			'contactList' => [
				'list' => (string) $list_id,
				'contact' => (string) $contact_id,
				'status' => 1
			]
		];

		// ActiveCampaign handles DOI from list/forms configuration. Keep parity input for action signature.
		if ($double_optin)
		{
			$payload['contactList']['status'] = 1;
		}

		$this->post('contactLists', $payload);

		if ($this->success())
		{
			return true;
		}

		// If it already exists, treat as success.
		$err = strtolower(trim((string) $this->getLastError()));
		if ($err !== '' && strpos($err, 'exists') !== false)
		{
			$this->setSuccessful(true);
			return true;
		}

		return false;
	}

	/**
	 * Sync custom fields by field title/perstag.
	 *
	 * @param   string  $contact_id
	 * @param   array   $params
	 *
	 * @return  void
	 */
	private function syncContactFields($contact_id, $params = [])
	{
		if (!$contact_id || !is_array($params) || empty($params))
		{
			return;
		}

		$fields_response = $this->get('fields', ['limit' => 200, 'offset' => 0]);
		if (!$this->success() || !is_array($fields_response) || empty($fields_response['fields']) || !is_array($fields_response['fields']))
		{
			return;
		}

		$field_map = [];
		foreach ($fields_response['fields'] as $field)
		{
			if (!is_array($field))
			{
				continue;
			}

			$id = isset($field['id']) ? trim((string) $field['id']) : '';
			if ($id === '')
			{
				continue;
			}

			$title = isset($field['title']) ? strtolower(trim((string) $field['title'])) : '';
			$perstag = isset($field['perstag']) ? strtolower(trim((string) $field['perstag'])) : '';

			if ($title !== '')
			{
				$field_map[$title] = $id;
			}
			if ($perstag !== '')
			{
				$field_map[$perstag] = $id;
			}
		}

		if (empty($field_map))
		{
			return;
		}

		$excluded = ['email', 'first_name', 'firstname', 'fname', 'last_name', 'lastname', 'lname', 'phone', 'mobile', 'telephone', 'tel'];
		foreach ($params as $key => $value)
		{
			$key = strtolower(trim((string) $key));
			if ($key === '' || in_array($key, $excluded, true) || !isset($field_map[$key]))
			{
				continue;
			}

			$value = $this->normalizeValue($value);
			if ($value === '')
			{
				continue;
			}

			$this->post('fieldValues', [
				'fieldValue' => [
					'contact' => (string) $contact_id,
					'field' => (string) $field_map[$key],
					'value' => $value
				]
			]);
		}
	}

	/**
	 * Normalize scalar/array values.
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
