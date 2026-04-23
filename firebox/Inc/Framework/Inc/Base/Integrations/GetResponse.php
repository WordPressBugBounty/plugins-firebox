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

class GetResponse extends Integration
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
		$this->setEndpoint('https://api.getresponse.com/v3');

		$this->headers = array_merge($this->headers, [
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'X-Auth-Token' => 'api-key ' . $this->key
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
			$this->throwError(new \WP_Error('getresponse_invalid_payload', 'GetResponse requires an email address and list.'));
			return false;
		}

		$existing_contact = $this->getContactByEmail($email);
		if ($existing_contact && !$update_existing)
		{
			$this->throwError(new \WP_Error('getresponse_contact_exists', 'Contact already exists.'));
			return false;
		}

		$payload = [
			'email' => $email,
			'campaign' => [
				'campaignId' => $list_id
			]
		];

		$name = $this->getName($params);
		if ($name !== '')
		{
			$payload['name'] = $name;
		}

		// GetResponse handles DOI primarily from account/list settings. Keep parameter for parity.
		if ($double_optin)
		{
			$payload['dayOfCycle'] = 0;
		}

		$custom_field_values = $this->mapCustomFields($params);
		if (!empty($custom_field_values))
		{
			$payload['customFieldValues'] = $custom_field_values;
		}

		if ($existing_contact)
		{
			$contact_id = isset($existing_contact['contactId']) ? trim((string) $existing_contact['contactId']) : '';
			if ($contact_id === '')
			{
				$this->throwError(new \WP_Error('getresponse_contact_missing_id', 'Unable to resolve existing contact ID.'));
				return false;
			}

			$this->post('contacts/' . rawurlencode($contact_id), $payload);
			if (!$this->success())
			{
				$this->put('contacts/' . rawurlencode($contact_id), $payload);
			}

			return $this->success();
		}

		$this->post('contacts', $payload);

		return $this->success();
	}

	/**
	 * Return available lists.
	 *
	 * @param   int  $limit
	 *
	 * @return  array
	 */
	public function getLists($limit = 100)
	{
		$response = $this->get('campaigns', [
			'perPage' => (int) $limit
		]);

		if (!$this->success() || !is_array($response))
		{
			return [];
		}

		$lists = [];

		foreach ($response as $item)
		{
			if (!is_array($item))
			{
				continue;
			}

			$id = isset($item['campaignId']) ? trim((string) $item['campaignId']) : '';
			$name = isset($item['name']) ? trim((string) $item['name']) : '';

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
	 * Verify API credentials.
	 *
	 * @return  bool
	 */
	public function verifyConnection()
	{
		$this->get('accounts');

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

		if (!empty($body['code']) && is_string($body['code']))
		{
			return (string) $body['code'];
		}

		if (!empty($body['context']) && is_array($body['context']))
		{
			$chunks = [];
			foreach ($body['context'] as $key => $value)
			{
				if (is_array($value))
				{
					$value = implode(', ', array_map('strval', $value));
				}
				$chunks[] = trim((string) $key) . ': ' . trim((string) $value);
			}

			if (!empty($chunks))
			{
				return implode(' ', $chunks);
			}
		}

		return '';
	}

	/**
	 * Get contact by email.
	 *
	 * @param   string  $email
	 *
	 * @return  array|false
	 */
	private function getContactByEmail($email)
	{
		$response = $this->get('contacts', [
			'query[email]' => (string) $email,
			'perPage' => 1
		]);

		if (!$this->success() || !is_array($response) || empty($response[0]) || !is_array($response[0]))
		{
			return false;
		}

		return $response[0];
	}

	/**
	 * Extract a fallback name from form fields.
	 *
	 * @param   array  $params
	 *
	 * @return  string
	 */
	private function getName($params = [])
	{
		if (!is_array($params))
		{
			return '';
		}

		$name = trim((string) $this->getCustomFieldValue(['name', 'full_name', 'fullname'], $params));
		if ($name !== '')
		{
			return $name;
		}

		$first = trim((string) $this->getCustomFieldValue(['first_name', 'firstname', 'fname'], $params));
		$last = trim((string) $this->getCustomFieldValue(['last_name', 'lastname', 'lname'], $params));

		return trim($first . ' ' . $last);
	}

	/**
	 * Map form values to GetResponse custom fields by field name.
	 *
	 * @param   array  $params
	 *
	 * @return  array
	 */
	private function mapCustomFields($params = [])
	{
		if (!is_array($params) || empty($params))
		{
			return [];
		}

		$response = $this->get('custom-fields', ['perPage' => 500]);
		if (!$this->success() || !is_array($response))
		{
			return [];
		}

		$field_map = [];
		foreach ($response as $field)
		{
			if (!is_array($field))
			{
				continue;
			}

			$name = isset($field['name']) ? strtolower(trim((string) $field['name'])) : '';
			$id = isset($field['customFieldId']) ? trim((string) $field['customFieldId']) : '';
			if ($name === '' || $id === '')
			{
				continue;
			}

			$field_map[$name] = $id;
		}

		if (empty($field_map))
		{
			return [];
		}

		$excluded = ['email', 'name', 'full_name', 'fullname', 'first_name', 'firstname', 'fname', 'last_name', 'lastname', 'lname'];
		$mapped = [];

		foreach ($params as $key => $value)
		{
			$key = strtolower(trim((string) $key));
			if ($key === '' || in_array($key, $excluded, true) || !isset($field_map[$key]))
			{
				continue;
			}

			$values = $this->normalizeValues($value);
			if (empty($values))
			{
				continue;
			}

			$mapped[] = [
				'customFieldId' => $field_map[$key],
				'value' => $values
			];
		}

		return $mapped;
	}

	/**
	 * Normalize scalar/array values to non-empty strings.
	 *
	 * @param   mixed  $value
	 *
	 * @return  array
	 */
	private function normalizeValues($value)
	{
		if (is_array($value))
		{
			$out = [];
			foreach ($value as $item)
			{
				$item = trim((string) $item);
				if ($item !== '')
				{
					$out[] = $item;
				}
			}

			return array_values(array_unique($out));
		}

		if (is_bool($value))
		{
			return [$value ? '1' : '0'];
		}

		if (!is_scalar($value) && $value !== null)
		{
			return [];
		}

		$value = trim((string) $value);

		return $value === '' ? [] : [$value];
	}
}
