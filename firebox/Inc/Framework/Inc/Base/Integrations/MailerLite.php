<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.144
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

class MailerLite extends Integration
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
		$this->setKey($options['api']);
		$this->setEndpoint('https://connect.mailerlite.com/api');

		$this->headers = array_merge($this->headers, [
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
			'Authorization' => 'Bearer ' . $this->key
		]);
	}

	/**
	 * Upserts a subscriber.
	 *
	 * API reference:
	 * https://developers.mailerlite.com/docs/subscribers.html#create-upsert-subscriber
	 *
	 * @param   string       $email
	 * @param   array        $params
	 * @param   int|string   $group_id
	 *
	 * @return  bool
	 */
	public function subscribe($email, $params = [], $group_id = '', $status = 'active')
	{
		$data = [
			'email' => (string) $email,
			'status' => $this->normalizeStatus($status)
		];

		$group_id = is_scalar($group_id) ? trim((string) $group_id) : '';
		if (!empty($group_id))
		{
			$data['groups'] = [$group_id];
		}

		$fields = $this->normalizeFields($params);
		if (!empty($fields))
		{
			$data['fields'] = (object) $fields;
		}

		$this->curlRequest('post', 'subscribers', $data);

		return true;
	}

	/**
	 * Returns all groups.
	 *
	 * API reference:
	 * https://developers.mailerlite.com/docs/groups.html#list-all-groups
	 *
	 * @return  array
	 */
	public function getLists($limit = 100)
	{
		$response = $this->curlRequest('get', 'groups', ['limit' => (int) $limit]);

		if (!$this->success() || !is_array($response))
		{
			return [];
		}

		$groups = isset($response['data']) && is_array($response['data']) ? $response['data'] : [];
		$lists = [];

		foreach ($groups as $group)
		{
			if (!is_array($group) || !isset($group['id']) || !isset($group['name']))
			{
				continue;
			}

			$lists[] = [
				'id' => (string) $group['id'],
				'name' => (string) $group['name']
			];
		}

		return $lists;
	}

	/**
	 * Verifies API credentials against account endpoint.
	 *
	 * @return  bool
	 */
	public function verifyConnection()
	{
		$this->curlRequest('get', 'timezones');

		return $this->success();
	}

	/**
	 * Returns normalized error message from API response.
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

		if (isset($body['errors']) && is_array($body['errors']))
		{
			$errors = [];

			foreach ($body['errors'] as $field => $messages)
			{
				if (is_array($messages))
				{
					foreach ($messages as $message)
					{
						$message = trim((string) $message);
						if (!$message)
						{
							continue;
						}

						$errors[] = is_string($field) ? $field . ': ' . $message : $message;
					}
				}
				else
				{
					$message = trim((string) $messages);
					if (!$message)
					{
						continue;
					}

					$errors[] = is_string($field) ? $field . ': ' . $message : $message;
				}
			}

			if (!empty($errors))
			{
				return implode(' ', $errors);
			}
		}

		if (isset($body['error']))
		{
			if (is_array($body['error']) && !empty($body['error']['message']))
			{
				return (string) $body['error']['message'];
			}

			if (is_string($body['error']) && trim($body['error']) !== '')
			{
				return trim($body['error']);
			}
		}

		if (!empty($body['detail']))
		{
			return (string) $body['detail'];
		}

		if (!empty($body['title']))
		{
			return (string) $body['title'];
		}

		if (is_string($body) && trim($body) !== '')
		{
			return trim($body);
		}

		if (is_array($this->last_response) && isset($this->last_response['response']['code']))
		{
			$status_code = (int) $this->last_response['response']['code'];

			if ($status_code === 401)
			{
				return 'MailerLite rejected this API token (401). Please verify the token and reconnect.';
			}

			if ($status_code === 403)
			{
				return 'MailerLite denied access to groups (403). The action is denied for this account or API token.';
			}

			return sprintf('Request failed with HTTP %d.', $status_code);
		}

		return '';
	}

	/**
	 * Maps FireBox form fields into MailerLite custom fields.
	 *
	 * @param   array   $params
	 * @return  array
	 */
	private function normalizeFields($params = [])
	{
		if (!is_array($params))
		{
			return [];
		}

		$fields = [];

		foreach ($params as $key => $value)
		{
			$key = is_scalar($key) ? trim((string) $key) : '';
			if (!$key)
			{
				continue;
			}

			if (strtolower($key) === 'email')
			{
				continue;
			}

			if (is_array($value))
			{
				$flat_values = array_filter(array_map(function($item) {
					return is_scalar($item) ? trim((string) $item) : '';
				}, $value));

				if (empty($flat_values))
				{
					continue;
				}

				$fields[$key] = implode(', ', $flat_values);
				continue;
			}

			if (!is_scalar($value) && $value !== null)
			{
				continue;
			}

			if (is_bool($value))
			{
				$fields[$key] = $value ? '1' : '0';
				continue;
			}

			$value = trim((string) $value);
			if ($value === '')
			{
				continue;
			}

			$fields[$key] = $value;
		}

		return $fields;
	}

	/**
	 * Ensures status is one of the supported values.
	 *
	 * @param   string  $status
	 *
	 * @return  string
	 */
	private function normalizeStatus($status = 'active')
	{
		$status = strtolower(trim((string) $status));
		$allowed = [
			'active',
			'unsubscribed',
			'unconfirmed',
			'bounced',
			'junk'
		];

		return in_array($status, $allowed, true) ? $status : 'active';
	}
}
