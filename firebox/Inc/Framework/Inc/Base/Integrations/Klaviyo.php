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

class Klaviyo extends Integration
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
		$this->setEndpoint('https://a.klaviyo.com/api');

		$this->headers = array_merge($this->headers, [
			'Content-Type' => 'application/vnd.api+json',
			'Accept' => 'application/vnd.api+json',
			'Revision' => '2026-01-15',
			'Authorization' => 'Klaviyo-API-Key ' . $this->key
		]);
	}

	/**
	 * Subscribe (or update) a profile in a list.
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
			$this->throwError(new \WP_Error('klaviyo_invalid_payload', 'Klaviyo requires an email address and list.'));
			return false;
		}

		$existing_profile_id = $this->findProfileIdByEmail($email);
		if ($existing_profile_id && !$update_existing)
		{
			$this->throwError(new \WP_Error('klaviyo_profile_exists', 'Profile already exists.'));
			return false;
		}

		$properties = $this->extractProperties($params);
		$profile_id = $existing_profile_id;

		if (!$profile_id)
		{
			$profile_id = $this->createProfile($email, $properties);
			if (!$profile_id)
			{
				if ($this->success())
				{
					$this->throwError(new \WP_Error('klaviyo_profile_missing_id', 'Unable to resolve profile ID.'));
				}
				return false;
			}
		}
		else if ($update_existing)
		{
			$this->updateProfile($profile_id, $email, $properties);
			if (!$this->success())
			{
				return false;
			}
		}

		if ($double_optin)
		{
			$this->subscribeProfileDoubleOptin($email, $list_id);
		}
		else
		{
			$this->subscribeProfileToList($profile_id, $list_id);
		}

		return $this->success();
	}

	/**
	 * Return available lists.
	 *
	 * @return  array
	 */
	public function getLists()
	{
		$lists = [];
		$cursor = '';
		$seenCursors = [];
		$maxPages = 100;

		for ($page = 0; $page < $maxPages; $page++)
		{
			$args = [];
			if ($cursor !== '')
			{
				$args['page[cursor]'] = $cursor;
			}

			$response = $this->get('lists', $args);
			if (!$this->success() || !is_array($response) || !isset($response['data']) || !is_array($response['data']))
			{
				return [];
			}

			foreach ($response['data'] as $item)
			{
				if (!is_array($item))
				{
					continue;
				}

				$id = isset($item['id']) ? trim((string) $item['id']) : '';
				$name = isset($item['attributes']['name']) ? trim((string) $item['attributes']['name']) : '';

				if ($id === '' || $name === '')
				{
					continue;
				}

				$lists[] = [
					'id' => $id,
					'name' => $name
				];
			}

			$nextCursor = $this->extractNextCursor($response);
			if ($nextCursor === '' || isset($seenCursors[$nextCursor]))
			{
				break;
			}

			$seenCursors[$nextCursor] = true;
			$cursor = $nextCursor;
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
		$this->get('lists');

		return $this->success();
	}

	/**
	 * Extract next page cursor from Klaviyo pagination links.
	 *
	 * @param   array  $response
	 *
	 * @return  string
	 */
	private function extractNextCursor($response = [])
	{
		if (!is_array($response))
		{
			return '';
		}

		$next = isset($response['links']['next']) ? trim((string) $response['links']['next']) : '';
		if ($next === '')
		{
			return '';
		}

		$query = parse_url($next, PHP_URL_QUERY);
		if (!is_string($query) || $query === '')
		{
			return '';
		}

		$params = [];
		parse_str($query, $params);

		if (isset($params['page']) && is_array($params['page']) && isset($params['page']['cursor']))
		{
			return trim((string) $params['page']['cursor']);
		}

		if (isset($params['page[cursor]']))
		{
			return trim((string) $params['page[cursor]']);
		}

		return '';
	}

	/**
	 * Return normalized error.
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

		if (!empty($body['errors']) && is_array($body['errors']))
		{
			$messages = [];
			foreach ($body['errors'] as $error)
			{
				if (!is_array($error))
				{
					$messages[] = trim((string) $error);
					continue;
				}

				if (!empty($error['detail']))
				{
					$messages[] = (string) $error['detail'];
				}
				else if (!empty($error['title']))
				{
					$messages[] = (string) $error['title'];
				}
			}

			$messages = array_filter($messages);
			if (!empty($messages))
			{
				return implode(' ', $messages);
			}
		}

		return '';
	}

	/**
	 * Find existing profile id by email.
	 *
	 * @param   string  $email
	 *
	 * @return  string
	 */
	private function findProfileIdByEmail($email)
	{
		$filter = sprintf('equals(email,"%s")', addslashes((string) $email));

		$response = $this->get('profiles', [
			'filter' => $filter,
			'page[size]' => 1
		]);

		if (!$this->success() || !is_array($response) || empty($response['data'][0]['id']))
		{
			return '';
		}

		return trim((string) $response['data'][0]['id']);
	}

	/**
	 * Create profile.
	 *
	 * @param   string  $email
	 * @param   array   $properties
	 *
	 * @return  string
	 */
	private function createProfile($email, $properties = [])
	{
		$payload = [
			'data' => [
				'type' => 'profile',
				'attributes' => [
					'email' => (string) $email,
					'properties' => (object) $properties
				]
			]
		];

		$response = $this->post('profiles', $payload);
		if ($this->success() && is_array($response) && !empty($response['data']['id']))
		{
			return trim((string) $response['data']['id']);
		}

		// If profile already exists, resolve its id.
		$status = $this->getLastResponseCode();
		if ($status === 409)
		{
			return $this->findProfileIdByEmail($email);
		}

		return '';
	}

	/**
	 * Return the last HTTP response code.
	 *
	 * @return  int
	 */
	private function getLastResponseCode()
	{
		if (!is_array($this->last_response))
		{
			return 0;
		}

		return isset($this->last_response['response']['code']) ? (int) $this->last_response['response']['code'] : 0;
	}

	/**
	 * Update profile.
	 *
	 * @param   string  $profile_id
	 * @param   string  $email
	 * @param   array   $properties
	 *
	 * @return  void
	 */
	private function updateProfile($profile_id, $email, $properties = [])
	{
		$payload = [
			'data' => [
				'type' => 'profile',
				'id' => (string) $profile_id,
				'attributes' => [
					'email' => (string) $email,
					'properties' => (object) $properties
				]
			]
		];

		$this->patch('profiles/' . rawurlencode((string) $profile_id), $payload);
	}

	/**
	 * Subscribe profile to list (single opt-in style).
	 *
	 * @param   string  $profile_id
	 * @param   string  $list_id
	 *
	 * @return  void
	 */
	private function subscribeProfileToList($profile_id, $list_id)
	{
		$payload = [
			'data' => [
				[
					'type' => 'profile',
					'id' => (string) $profile_id
				]
			]
		];

		$this->post('lists/' . rawurlencode((string) $list_id) . '/relationships/profiles', $payload);
	}

	/**
	 * Subscribe profile with double opt-in flow.
	 *
	 * @param   string  $email
	 * @param   string  $list_id
	 *
	 * @return  void
	 */
	private function subscribeProfileDoubleOptin($email, $list_id)
	{
		$payload = [
			'data' => [
				'type' => 'profile-subscription-bulk-create-job',
				'attributes' => [
					'profiles' => [
						'data' => [
							[
								'type' => 'profile',
								'attributes' => [
									'email' => (string) $email,
									'subscriptions' => [
										'email' => [
											'marketing' => [
												'consent' => 'SUBSCRIBED'
											]
										]
									]
								]
							]
						]
					],
					'custom_source' => 'FireBox'
				],
				'relationships' => [
					'list' => [
						'data' => [
							'type' => 'list',
							'id' => (string) $list_id
						]
					]
				]
			]
		];

		$this->post('profile-subscription-bulk-create-jobs', $payload);
	}

	/**
	 * Extract profile properties from form fields.
	 *
	 * @param   array  $params
	 *
	 * @return  array
	 */
	private function extractProperties($params = [])
	{
		if (!is_array($params))
		{
			return [];
		}

		$excluded = ['email'];
		$properties = [];

		foreach ($params as $key => $value)
		{
			$key = trim((string) $key);
			if ($key === '' || in_array(strtolower($key), $excluded, true))
			{
				continue;
			}

			if (is_array($value))
			{
				$clean = array_filter(array_map(function($item) {
					return trim((string) $item);
				}, $value));
				if (!empty($clean))
				{
					$properties[$key] = implode(', ', $clean);
				}
				continue;
			}

			if (is_bool($value))
			{
				$properties[$key] = $value;
				continue;
			}

			if (!is_scalar($value) && $value !== null)
			{
				continue;
			}

			$value = trim((string) $value);
			if ($value === '')
			{
				continue;
			}

			$properties[$key] = $value;
		}

		return $properties;
	}
}
