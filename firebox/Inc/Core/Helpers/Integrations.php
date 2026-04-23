<?php
/**
 * @package         FireBox
 * @version         3.1.6
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace FireBox\Core\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Integrations
{
	/**
	 * Global integrations metadata.
	 *
	 * @var  array
	 */
	private static $integrations = [
		'mailchimp' => [
			'label' => 'MailChimp',
			'settings_key' => 'mailchimp_api_key',
			'class_name' => 'MailChimp',
			'docs_slug' => 'mailchimp',
			'logo_file' => 'mailchimp.svg',
			'connection_type' => 'api_key',
			'pro_only' => false,
			'form' => [
				'legacyApiKeyField' => 'mailchimpAPIKey',
				'defaults' => [
					'listId' => '',
					'doubleOptin' => false,
					'updateExisting' => true
				],
				'legacyMap' => [
					'listId' => 'mailchimpListID',
					'doubleOptin' => 'mailchimpDoubleOptin',
					'updateExisting' => 'mailchimpUpdateExisting'
				],
				'fields' => [
					[
						'type' => 'list',
						'key' => 'listId',
						'label' => 'List'
					],
					[
						'type' => 'toggle',
						'key' => 'doubleOptin',
						'label' => 'Double Optin'
					],
					[
						'type' => 'toggle',
						'key' => 'updateExisting',
						'label' => 'Update existing user'
					]
				]
			]
		],
		'brevo' => [
			'label' => 'Brevo',
			'settings_key' => 'brevo_api_key',
			'class_name' => 'Brevo',
			'docs_slug' => 'brevo',
			'logo_file' => 'brevo.svg',
			'connection_type' => 'api_key',
			'pro_only' => false,
			'form' => [
				'legacyApiKeyField' => 'brevoAPIKey',
				'defaults' => [
					'listId' => '',
					'updateExisting' => true
				],
				'legacyMap' => [
					'listId' => 'brevoListID',
					'updateExisting' => 'brevoUpdateExisting'
				],
				'fields' => [
					[
						'type' => 'list',
						'key' => 'listId',
						'label' => 'List'
					],
					[
						'type' => 'toggle',
						'key' => 'updateExisting',
						'label' => 'Update existing user'
					]
				]
			]
		],
		'mailerlite' => [
			'label' => 'MailerLite',
			'settings_key' => 'mailerlite_api_key',
			'class_name' => 'MailerLite',
			'docs_slug' => 'mailerlite',
			'logo_file' => 'mailerlite.svg',
			'connection_type' => 'api_key',
			'pro_only' => true,
			'required_plan' => 'pro',
			'form' => [
				'defaults' => [
					'listId' => '',
					'status' => 'active'
				],
				'fields' => [
					[
						'type' => 'list',
						'key' => 'listId',
						'label' => 'Group'
					],
					[
						'type' => 'select',
						'key' => 'status',
						'label' => 'Subscriber Status',
						'options' => [
							[
								'value' => 'active',
								'label' => 'Active'
							],
							[
								'value' => 'unconfirmed',
								'label' => 'Unconfirmed'
							],
							[
								'value' => 'unsubscribed',
								'label' => 'Unsubscribed'
							],
							[
								'value' => 'bounced',
								'label' => 'Bounced'
							],
							[
								'value' => 'junk',
								'label' => 'Junk'
							]
						]
					]
				]
			]
		],
		'getresponse' => [
			'label' => 'GetResponse',
			'settings_key' => 'getresponse_api_key',
			'class_name' => 'GetResponse',
			'docs_slug' => 'getresponse',
			'logo_file' => 'getresponse.png',
			'connection_type' => 'api_key',
			'pro_only' => true,
			'required_plan' => 'pro',
			'form' => [
				'defaults' => [
					'listId' => '',
					'updateExisting' => true,
					'doubleOptin' => false
				],
				'fields' => [
					[
						'type' => 'list',
						'key' => 'listId',
						'label' => 'List'
					],
					[
						'type' => 'toggle',
						'key' => 'updateExisting',
						'label' => 'Update existing user'
					],
					[
						'type' => 'toggle',
						'key' => 'doubleOptin',
						'label' => 'Double Optin'
					]
				]
			]
		],
		'activecampaign' => [
			'label' => 'ActiveCampaign',
			'class_name' => 'ActiveCampaign',
			'docs_slug' => 'activecampaign',
			'logo_file' => 'activecampaign.jpeg',
			'connection_type' => 'api_key',
			'credential_fields' => [
				[
					'key' => 'api_url',
					'settings_key' => 'activecampaign_api_url',
					'label' => 'API URL',
					'placeholder' => 'https://your-account.api-us1.com'
				],
				[
					'key' => 'api_key',
					'settings_key' => 'activecampaign_api_key',
					'label' => 'API Key',
					'placeholder' => 'API Key',
					'type' => 'password'
				]
			],
			'connection_value_template' => '{api_url}|{api_key}',
			'pro_only' => true,
			'required_plan' => 'pro',
			'form' => [
				'defaults' => [
					'listId' => '',
					'updateExisting' => true,
					'doubleOptin' => false
				],
				'fields' => [
					[
						'type' => 'list',
						'key' => 'listId',
						'label' => 'List'
					],
					[
						'type' => 'toggle',
						'key' => 'updateExisting',
						'label' => 'Update existing user'
					],
					[
						'type' => 'toggle',
						'key' => 'doubleOptin',
						'label' => 'Double Optin'
					]
				]
			]
		],
		'klaviyo' => [
			'label' => 'Klaviyo',
			'settings_key' => 'klaviyo_api_key',
			'class_name' => 'Klaviyo',
			'docs_slug' => 'klaviyo',
			'logo_file' => 'klaviyo.svg',
			'connection_type' => 'api_key',
			'pro_only' => true,
			'required_plan' => 'growth',
			'form' => [
				'defaults' => [
					'listId' => '',
					'updateExisting' => true,
					'doubleOptin' => false
				],
				'fields' => [
					[
						'type' => 'list',
						'key' => 'listId',
						'label' => 'List'
					],
					[
						'type' => 'toggle',
						'key' => 'updateExisting',
						'label' => 'Update existing user'
					],
					[
						'type' => 'toggle',
						'key' => 'doubleOptin',
						'label' => 'Double Optin'
					]
				]
			]
		],
		'salesforcewebtolead' => [
			'label' => 'Salesforce Web-to-Lead',
			'settings_key' => 'salesforcewebtolead_api_key',
			'class_name' => 'SalesforceWebToLead',
			'docs_slug' => 'salesforce',
			'logo_file' => 'salesforcewebtolead.svg',
			'connection_type' => 'api_key',
			'api_key_placeholder' => 'Organization ID (OID), e.g. 00DXXXXXXXXXXXX',
			'pro_only' => true,
			'required_plan' => 'growth',
			'form' => [
				'defaults' => [
					'testMode' => false
				],
				'fields' => [
					[
						'type' => 'toggle',
						'key' => 'testMode',
						'label' => 'Test Mode'
					]
				]
			]
		],
		'acymailing' => [
			'label' => 'AcyMailing',
			'settings_key' => '',
			'class_name' => 'AcyMailing',
			'docs_slug' => 'acymailing',
			'logo_file' => 'acymailing.png',
			'connection_type' => 'plugin',
			'plugin_file' => 'acymailing/index.php',
			'pro_only' => true,
			'required_plan' => 'basic',
			'form' => [
				'defaults' => [
					'listId' => '',
					'doubleOptin' => false
				],
				'legacyMap' => [
					'listId' => 'acymailingListID',
					'doubleOptin' => 'acymailingDoubleOptin'
				],
				'fields' => [
					[
						'type' => 'list',
						'key' => 'listId',
						'label' => 'List'
					],
					[
						'type' => 'toggle',
						'key' => 'doubleOptin',
						'label' => 'Double Optin'
					]
				]
			]
		]
	];

	/**
	 * Returns all integrations with slug in payload.
	 *
	 * @return  array
	 */
	public static function getIntegrations()
	{
		$integrations = [];

		foreach (self::$integrations as $slug => $integration)
		{
			if (!self::isIntegrationAvailable($integration))
			{
				continue;
			}

			$integrations[$slug] = self::normalizeIntegrationEntry($slug, $integration);
		}

		return $integrations;
	}

	/**
	 * Returns locked integrations for unavailable plans.
	 *
	 * @return  array
	 */
	public static function getLockedIntegrations()
	{
		$integrations = [];

		foreach (self::$integrations as $slug => $integration)
		{
			if (!self::isIntegrationLocked($integration))
			{
				continue;
			}

			$item = self::normalizeIntegrationEntry($slug, $integration);
			$item['locked'] = true;
			$item['connection_type'] = 'locked';
			$item['connected'] = false;

			$integrations[$slug] = $item;
		}

		return $integrations;
	}

	/**
	 * Returns integrations visible in UI (available + locked).
	 *
	 * @return  array
	 */
	private static function getVisibleIntegrations()
	{
		return self::getIntegrations() + self::getLockedIntegrations();
	}

	/**
	 * Returns whether integration should be available.
	 *
	 * @param   array  $integration
	 *
	 * @return  bool
	 */
	private static function isIntegrationAvailable($integration = [])
	{
		$integration = is_array($integration) ? $integration : [];

		if (!empty($integration['pro_only']) && !self::isProVersion())
		{
			return false;
		}

		$required_plan = isset($integration['required_plan']) ? trim((string) $integration['required_plan']) : '';

		return $required_plan ? self::isRequiredPlanMet($required_plan) : true;
	}

	/**
	 * Returns whether integration should be displayed as locked.
	 *
	 * @param   array  $integration
	 *
	 * @return  bool
	 */
	private static function isIntegrationLocked($integration = [])
	{
		$integration = is_array($integration) ? $integration : [];

		if (self::isIntegrationAvailable($integration))
		{
			return false;
		}

		return !empty($integration['required_plan']) || !empty($integration['pro_only']);
	}

	/**
	 * Returns integration metadata by slug or class name.
	 *
	 * @param   string  $integration
	 *
	 * @return  array|null
	 */
	public static function getIntegration($integration = '')
	{
		if (!is_string($integration))
		{
			return null;
		}

		$integration = trim($integration);
		if (!$integration)
		{
			return null;
		}

		$integrations = self::getVisibleIntegrations();
		$integration_lc = strtolower($integration);

		if (isset($integrations[$integration_lc]))
		{
			return $integrations[$integration_lc];
		}

		foreach ($integrations as $item)
		{
			if (!isset($item['class_name']))
			{
				continue;
			}

			if (strtolower((string) $item['class_name']) === $integration_lc)
			{
				return $item;
			}
		}

		return null;
	}

	/**
	 * Resolves a supported integration slug.
	 *
	 * @param   string  $integration
	 *
	 * @return  string|null
	 */
	public static function getIntegrationSlug($integration = '')
	{
		if (!$integration = self::getIntegration($integration))
		{
			return null;
		}

		return $integration['slug'];
	}

	/**
	 * Returns framework class name for a supported integration.
	 *
	 * @param   string  $integration
	 *
	 * @return  string|null
	 */
	public static function getFrameworkIntegrationClassName($integration = '')
	{
		if (!$integration = self::getIntegration($integration))
		{
			return null;
		}

		return $integration['class_name'];
	}

	/**
	 * Returns the stored setting key for a given integration.
	 *
	 * @param   string  $integration
	 *
	 * @return  string|null
	 */
	public static function getIntegrationSettingKey($integration = '')
	{
		$credential_fields = self::getCredentialFields($integration);

		if (empty($credential_fields))
		{
			return null;
		}

		$field = reset($credential_fields);
		$key = isset($field['settings_key']) ? trim((string) $field['settings_key']) : '';

		return !empty($key) ? $key : null;
	}

	/**
	 * Returns normalized credential fields for an integration.
	 *
	 * @param   string|array  $integration
	 *
	 * @return  array
	 */
	public static function getCredentialFields($integration = '')
	{
		if (!is_array($integration))
		{
			$integration = self::getIntegration($integration);
		}

		$integration = is_array($integration) ? $integration : [];
		$fields = [];

		if (!empty($integration['credential_fields']) && is_array($integration['credential_fields']))
		{
			$fields = $integration['credential_fields'];
		}
		else
		{
			$settings_key = isset($integration['settings_key']) ? trim((string) $integration['settings_key']) : '';
			if ($settings_key !== '')
			{
				$fields[] = [
					'key' => 'api_key',
					'settings_key' => $settings_key,
					'label' => 'API Key',
					'placeholder' => isset($integration['api_key_placeholder']) ? (string) $integration['api_key_placeholder'] : '',
					'type' => 'password'
				];
			}
		}

		$normalized = [];

		foreach ($fields as $field)
		{
			if (!is_array($field))
			{
				continue;
			}

			$key = isset($field['key']) ? trim((string) $field['key']) : '';
			$settings_key = isset($field['settings_key']) ? trim((string) $field['settings_key']) : '';

			if ($key === '' || $settings_key === '')
			{
				continue;
			}

			$normalized[] = [
				'key' => $key,
				'settings_key' => $settings_key,
				'label' => !empty($field['label']) ? self::translateRegistryText($field['label']) : '',
				'placeholder' => !empty($field['placeholder']) ? self::translateRegistryText($field['placeholder']) : '',
				'type' => isset($field['type']) && trim((string) $field['type']) === 'text' ? 'text' : 'password',
				'required' => !array_key_exists('required', $field) || (bool) $field['required']
			];
		}

		return $normalized;
	}

	/**
	 * Returns integration human label.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getIntegrationLabel($integration = '')
	{
		$integration_input = $integration;

		if (!$integration = self::getIntegration($integration_input))
		{
			return trim((string) $integration_input);
		}

		return $integration['label'];
	}

	/**
	 * Returns integration docs URL.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getFindAPIKeyURL($integration = '')
	{
		if (!$integration = self::getIntegration($integration))
		{
			return '';
		}

		$docs_slug = isset($integration['docs_slug']) ? trim((string) $integration['docs_slug']) : '';
		if (!$docs_slug)
		{
			return '';
		}

		return \FPFramework\Base\Functions::getUTMURL(
			'https://www.fireplugins.com/docs/integrations/' . $docs_slug . '/#find_my_api_key',
			'',
			'misc',
			$docs_slug
		);
	}

	/**
	 * Returns settings keys used by integration global API keys.
	 *
	 * @return  array
	 */
	public static function getSettingsKeys()
	{
		$keys = [];

		foreach (self::getVisibleIntegrations() as $integration)
		{
			foreach (self::getCredentialFields($integration) as $field)
			{
				if (empty($field['settings_key']))
				{
					continue;
				}

				$keys[] = $field['settings_key'];
			}
		}

		return array_values(array_unique($keys));
	}

	/**
	 * Returns data used by Settings > Integrations UI.
	 *
	 * @return  array
	 */
	public static function getSettingsManagedIntegrations()
	{
		$integrations = [];

		foreach (self::getVisibleIntegrations() as $integration)
		{
			$slug = $integration['slug'];
			$connection_type = self::getConnectionType($slug);
			$connected = self::isConnected($slug);
			$is_locked = self::isLocked($slug);

			$item = [
				'label' => $integration['label'],
				'slug' => $slug,
				'connected' => $connected,
				'locked' => $is_locked,
				'connection_type' => $connection_type,
				'status_label' => $is_locked ? __('Locked', 'firebox') : firebox()->_($connected ? 'FB_INTEGRATION_CONNECTED' : 'FB_INTEGRATION_DISCONNECTED'),
				'logo_url' => !empty($integration['logo_file']) ? FBOX_MEDIA_ADMIN_URL . 'images/integrations/' . $integration['logo_file'] : ''
			];

			if ($is_locked)
			{
				$required_plan_label = self::getRequiredPlanLabel($slug);
				/* translators: %s: Required plan label. */
				$item['locked_message'] = sprintf(__('Upgrade to %s to connect this integration.', 'firebox'), $required_plan_label);
				/* translators: %s: Required plan label. */
				$item['upgrade_badge_label'] = sprintf(__('Upgrade to %s', 'firebox'), $required_plan_label);
				$item['upgrade_label'] = sprintf(fpframework()->_('FPF_UNLOCK_X_FEATURE'), $required_plan_label);
				$item['upgrade_plan'] = $required_plan_label;
				$item['current_plan'] = self::getCurrentPlan();
				$item['upgrade_url'] = self::getUpgradeURL($slug);
			}
			else if ($connection_type === 'plugin')
			{
				$item['docs_url'] = self::getFindAPIKeyURL($slug);
				$plugin_state = self::getPluginState($slug);

				if ($plugin_state === 'inactive')
				{
					$item['status_label'] = __('Inactive', 'firebox');
					/* translators: %s: Integration label. */
					$item['plugin_message'] = sprintf(__('Installed but inactive. Activate %s to use this integration.', 'firebox'), $integration['label']);
					$item['plugin_action_label'] = __('Activate', 'firebox');
					$item['plugin_action_url'] = self::getActivatePluginURL($slug);
				}
				else if ($plugin_state === 'missing')
				{
					$item['status_label'] = __('Not Installed', 'firebox');
					/* translators: %s: Integration label. */
					$item['plugin_message'] = sprintf(__('%s is not installed. Install it to use this integration.', 'firebox'), $integration['label']);
					$item['plugin_action_label'] = __('Install', 'firebox');
					$item['plugin_action_url'] = self::getInstallPluginURL($slug);
				}
				else
				{
					/* translators: %s: Integration label. */
					$item['plugin_message'] = sprintf(__('%s is installed and active.', 'firebox'), $integration['label']);
					$item['plugin_action_label'] = '';
					$item['plugin_action_url'] = '';
				}
			}
			else
			{
				$item['credentials'] = self::getSettingsCredentialFields($slug);
				$item['docs_url'] = self::getFindAPIKeyURL($slug);
			}

			$integrations[] = $item;
		}

		return $integrations;
	}

	/**
	 * Returns whether integration is connected.
	 *
	 * @param   string  $integration
	 *
	 * @return  bool
	 */
	public static function isConnected($integration = '')
	{
		$connection_type = self::getConnectionType($integration);

		if ($connection_type === 'locked')
		{
			return false;
		}

		if ($connection_type === 'plugin')
		{
			return self::getPluginState($integration) === 'active';
		}

		return self::hasGlobalConnection($integration);
	}

	/**
	 * Returns integration connection type.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getConnectionType($integration = '')
	{
		if (!$integration = self::getIntegration($integration))
		{
			return 'api_key';
		}

		$type = isset($integration['connection_type']) ? trim((string) $integration['connection_type']) : '';

		return $type ?: 'api_key';
	}

	/**
	 * Returns API key input placeholder text.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getAPIKeyPlaceholder($integration = '')
	{
		$credential_fields = self::getCredentialFields($integration);
		if (empty($credential_fields))
		{
			return firebox()->_('FB_INTEGRATION_API_KEY');
		}

		$field = reset($credential_fields);
		$placeholder = isset($field['placeholder']) ? trim((string) $field['placeholder']) : '';

		return $placeholder !== '' ? $placeholder : firebox()->_('FB_INTEGRATION_API_KEY');
	}

	/**
	 * Returns plugin state for plugin-based integrations.
	 *
	 * @param   string  $integration
	 *
	 * @return  string  active|inactive|missing
	 */
	public static function getPluginState($integration = '')
	{
		if (self::getConnectionType($integration) !== 'plugin')
		{
			return 'missing';
		}

		$plugin_file = self::getPluginFile($integration);
		if (!$plugin_file)
		{
			return 'missing';
		}

		if (!function_exists('is_plugin_active'))
		{
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		if (is_plugin_active($plugin_file))
		{
			return 'active';
		}

		$plugins = get_plugins();
		if (isset($plugins[$plugin_file]))
		{
			return 'inactive';
		}

		return 'missing';
	}

	/**
	 * Returns plugin install URL for plugin-based integrations.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getInstallPluginURL($integration = '')
	{
		$label = self::getIntegrationLabel($integration);

		return admin_url('plugin-install.php?tab=search&type=term&s=' . rawurlencode($label));
	}

	/**
	 * Returns plugin activation URL for plugin-based integrations.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getActivatePluginURL($integration = '')
	{
		$plugin_file = self::getPluginFile($integration);
		if (!$plugin_file)
		{
			return '';
		}

		$url = admin_url('plugins.php?action=activate&plugin=' . rawurlencode($plugin_file));

		return wp_nonce_url($url, 'activate-plugin_' . $plugin_file);
	}

	/**
	 * Returns integration plugin file.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getPluginFile($integration = '')
	{
		if (!$integration = self::getIntegration($integration))
		{
			return '';
		}

		return isset($integration['plugin_file']) ? trim((string) $integration['plugin_file']) : '';
	}

	/**
	 * Returns required plan for an integration.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getRequiredPlan($integration = '')
	{
		if (!$integration = self::getIntegration($integration))
		{
			return '';
		}

		return isset($integration['required_plan']) ? strtolower(trim((string) $integration['required_plan'])) : '';
	}

	/**
	 * Returns required plan label for an integration.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getRequiredPlanLabel($integration = '')
	{
		$required_plan = self::getRequiredPlan($integration);

		return !empty($required_plan) ? ucfirst($required_plan) : __('higher', 'firebox');
	}

	/**
	 * Returns whether the integration is locked by plan.
	 *
	 * @param   string  $integration
	 *
	 * @return  bool
	 */
	public static function isLocked($integration = '')
	{
		if (!$integration = self::getIntegration($integration))
		{
			return false;
		}

		return !empty($integration['locked']);
	}

	/**
	 * Returns whether current runtime is Pro.
	 *
	 * @return  bool
	 */
	private static function isProVersion()
	{
		$current_plan = self::getCurrentPlan();

		// Any paid plan should expose paid integrations; required_plan handles exact tier access.
		if ($current_plan && $current_plan !== 'free')
		{
			return true;
		}

		// Backward-compat fallback for runtimes that still key off license type only.
		if (!defined('FBOX_LICENSE_TYPE'))
		{
			return false;
		}

		$license_type = strtolower(trim((string) FBOX_LICENSE_TYPE));

		return in_array($license_type, ['pro', 'basic', 'growth'], true);
	}

	/**
	 * Normalizes integration metadata payload.
	 *
	 * @param   string  $slug
	 * @param   array   $integration
	 *
	 * @return  array
	 */
	private static function normalizeIntegrationEntry($slug = '', $integration = [])
	{
		$integration = is_array($integration) ? $integration : [];

		return array_merge(
			[
				'slug' => $slug,
				'locked' => false
			],
			$integration
		);
	}

	/**
	 * Returns current license plan.
	 *
	 * @return  string
	 */
	private static function getCurrentPlan()
	{
		if (!defined('FBOX_LICENSE_PLAN'))
		{
			return 'free';
		}

		return strtolower(trim((string) FBOX_LICENSE_PLAN));
	}

	/**
	 * Returns whether the current plan meets the required plan.
	 *
	 * @param   string  $required_plan
	 *
	 * @return  bool
	 */
	private static function isRequiredPlanMet($required_plan = '')
	{
		$required_plan = strtolower(trim((string) $required_plan));
		$current_plan = self::getCurrentPlan();

		switch ($required_plan)
		{
			case 'growth':
				return $current_plan === 'growth';

			case 'pro':
				return in_array($current_plan, ['pro', 'growth'], true);

			case 'basic':
				return in_array($current_plan, ['basic', 'pro', 'growth'], true);

			default:
				return true;
		}
	}

	/**
	 * Returns upgrade URL for locked integrations.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	private static function getUpgradeURL($integration = '')
	{
		if (defined('FBOX_GO_PRO_URL'))
		{
			return sprintf(FBOX_GO_PRO_URL, strtolower(trim((string) $integration)) . '-integration');
		}

		return \FPFramework\Base\Functions::getUTMURL(
			'https://www.fireplugins.com/upgrade/',
			'',
			'feature',
			strtolower(trim((string) $integration)) . '-integration'
		);
	}

	/**
	 * Returns credential fields decorated for Settings > Integrations UI.
	 *
	 * @param   string  $integration
	 *
	 * @return  array
	 */
	public static function getSettingsCredentialFields($integration = '')
	{
		$values = self::getGlobalCredentials($integration);
		$fields = [];

		foreach (self::getCredentialFields($integration) as $field)
		{
			$key = $field['key'];
			$fields[] = [
				'key' => $key,
				'label' => $field['label'],
				'placeholder' => $field['placeholder'],
				'type' => $field['type'],
				'required' => !empty($field['required']),
				'value' => isset($values[$key]) ? (string) $values[$key] : ''
			];
		}

		return $fields;
	}

	/**
	 * Returns the stored global credentials for an integration.
	 *
	 * @param   string  $integration
	 *
	 * @return  array
	 */
	public static function getGlobalCredentials($integration = '')
	{
		$integration_slug = self::getIntegrationSlug($integration);
		$fields = self::getCredentialFields($integration);
		if (empty($fields))
		{
			return [];
		}

		$settings = get_option('firebox_settings', []);
		if (!is_array($settings))
		{
			$settings = [];
		}

		$credentials = [];
		foreach ($fields as $field)
		{
			$key = $field['key'];
			$settings_key = $field['settings_key'];

			if (!isset($settings[$settings_key]))
			{
				$credentials[$key] = '';
				continue;
			}

			$credentials[$key] = self::getGlobalAPIKeyEncryption()->decrypt($settings[$settings_key]);
		}

		return self::normalizeCredentialValues($integration_slug, $credentials);
	}

	/**
	 * Returns the global API key.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getGlobalAPIKey($integration = '')
	{
		$credentials = self::getGlobalCredentials($integration);

		return isset($credentials['api_key']) ? (string) $credentials['api_key'] : '';
	}

	/**
	 * Updates integration global credentials.
	 *
	 * @param   string  $integration
	 * @param   array   $credentials
	 *
	 * @return  bool
	 */
	public static function setGlobalCredentials($integration = '', $credentials = [])
	{
		$integration_slug = self::getIntegrationSlug($integration);
		$fields = self::getCredentialFields($integration);
		$credentials = is_array($credentials) ? $credentials : [];

		if (empty($fields))
		{
			return false;
		}

		$credentials = self::normalizeCredentialValues($integration_slug, $credentials);

		$settings = get_option('firebox_settings', []);
		$settings = is_array($settings) ? $settings : [];

		foreach ($fields as $field)
		{
			$key = $field['key'];
			$settings_key = $field['settings_key'];
			$value = isset($credentials[$key]) && is_scalar($credentials[$key]) ? trim((string) $credentials[$key]) : '';
			$settings[$settings_key] = self::getGlobalAPIKeyEncryption()->encrypt($value);
		}

		return update_option('firebox_settings', $settings);
	}

	/**
	 * Updates an integration global API key.
	 *
	 * @param   string  $integration
	 * @param   string  $api_key
	 *
	 * @return  bool
	 */
	public static function setGlobalAPIKey($integration = '', $api_key = '')
	{
		$integration_slug = self::getIntegrationSlug($integration);
		$credentials = [
			'api_key' => $api_key
		];

		if ($integration_slug)
		{
			$credentials = self::normalizeCredentialValues($integration_slug, $credentials);
		}

		return self::setGlobalCredentials($integration, $credentials);
	}

	/**
	 * Returns composed connection value consumed by the framework integration.
	 *
	 * @param   string  $integration
	 * @param   array   $credentials
	 *
	 * @return  string
	 */
	public static function getConnectionValue($integration = '', $credentials = [])
	{
		$integration_entry = self::getIntegration($integration);
		$credentials = is_array($credentials) ? $credentials : [];

		if (!$integration_entry)
		{
			return '';
		}

		$template = isset($integration_entry['connection_value_template']) ? trim((string) $integration_entry['connection_value_template']) : '';
		if ($template === '')
		{
			return isset($credentials['api_key']) ? trim((string) $credentials['api_key']) : '';
		}

		$value = preg_replace_callback('/\{([a-z0-9_]+)\}/i', function($matches) use ($credentials) {
			$key = isset($matches[1]) ? $matches[1] : '';
			return isset($credentials[$key]) ? trim((string) $credentials[$key]) : '';
		}, $template);

		return trim((string) $value);
	}

	/**
	 * Returns composed connection value from stored credentials.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getGlobalConnectionValue($integration = '')
	{
		return self::getConnectionValue($integration, self::getGlobalCredentials($integration));
	}

	/**
	 * Normalizes legacy combined credential formats to individual fields.
	 *
	 * @param   string  $integration
	 * @param   array   $credentials
	 *
	 * @return  array
	 */
	private static function normalizeCredentialValues($integration = '', $credentials = [])
	{
		$integration = self::getIntegrationSlug($integration);
		$credentials = is_array($credentials) ? $credentials : [];

		if ($integration !== 'activecampaign')
		{
			return $credentials;
		}

		$api_url = isset($credentials['api_url']) ? trim((string) $credentials['api_url']) : '';
		$api_key = isset($credentials['api_key']) ? trim((string) $credentials['api_key']) : '';
		if ($api_url !== '' || $api_key === '')
		{
			return $credentials;
		}

		$parts = [];
		if (strpos($api_key, '|') !== false)
		{
			$parts = array_map('trim', explode('|', $api_key, 2));
		}
		else if (preg_match('/\r\n|\r|\n/', $api_key))
		{
			$parts = array_map('trim', preg_split('/\r\n|\r|\n/', $api_key));
			$parts = array_values(array_filter($parts));
		}

		if (count($parts) < 2)
		{
			return $credentials;
		}

		$credentials['api_url'] = (string) $parts[0];
		$credentials['api_key'] = (string) $parts[1];

		return $credentials;
	}

	/**
	 * Returns API key encryption helper.
	 *
	 * @return  \FireBox\Core\Helpers\Encryption
	 */
	private static function getGlobalAPIKeyEncryption()
	{
		static $encryption = null;
		if ($encryption === null)
		{
			$encryption = new Encryption();
		}

		return $encryption;
	}

	/**
	 * Determines whether there is an active global connection.
	 *
	 * @param   string  $integration
	 *
	 * @return  bool
	 */
	public static function hasGlobalConnection($integration = '')
	{
		$credentials = self::getGlobalCredentials($integration);
		$fields = self::getCredentialFields($integration);

		if (empty($fields))
		{
			return false;
		}

		foreach ($fields as $field)
		{
			if (empty($field['required']))
			{
				continue;
			}

			$key = $field['key'];
			$value = isset($credentials[$key]) ? trim((string) $credentials[$key]) : '';
			if ($value === '')
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns legacy action API key from block attributes.
	 *
	 * @param   string  $integration
	 * @param   array   $attrs
	 *
	 * @return  string
	 */
	public static function getLegacyActionAPIKey($integration = '', $attrs = [])
	{
		$integration = self::getIntegrationSlug($integration);
		$attrs = is_array($attrs) ? $attrs : [];

		if (!$integration)
		{
			return '';
		}

		switch ($integration)
		{
			case 'mailchimp':
				return isset($attrs['mailchimpAPIKey']) ? trim((string) $attrs['mailchimpAPIKey']) : '';

			case 'brevo':
				return isset($attrs['brevoAPIKey']) ? trim((string) $attrs['brevoAPIKey']) : '';

			default:
				return '';
		}
	}

	/**
	 * Determines whether the action has a legacy API key.
	 *
	 * @param   string  $integration
	 * @param   array   $attrs
	 *
	 * @return  bool
	 */
	public static function hasLegacyActionAPIKey($integration = '', $attrs = [])
	{
		return !empty(self::getLegacyActionAPIKey($integration, $attrs));
	}

	/**
	 * Resolve effective API key used by actions.
	 * Priority: legacy action key -> global key.
	 *
	 * @param   string       $integration
	 * @param   array        $attrs
	 * @param   string|null  $legacy_api_key
	 *
	 * @return  string
	 */
	public static function resolveActionAPIKey($integration = '', $attrs = [], $legacy_api_key = null)
	{
		$integration = self::getIntegrationSlug($integration);
		$attrs = is_array($attrs) ? $attrs : [];

		if (!$integration)
		{
			return '';
		}

		if ($legacy_api_key === null)
		{
			$legacy_api_key = self::getLegacyActionAPIKey($integration, $attrs);
		}

		$legacy_api_key = trim((string) $legacy_api_key);
		if (!empty($legacy_api_key))
		{
			return $legacy_api_key;
		}

		return self::getGlobalConnectionValue($integration);
	}

	/**
	 * Returns integrations registry used by the Form block editor.
	 *
	 * @return  array
	 */
	public static function getFormEditorIntegrationsRegistry()
	{
		$registry = [];

		foreach (self::getVisibleIntegrations() as $integration)
		{
			$slug = $integration['slug'];
			$action = isset($integration['class_name']) ? trim((string) $integration['class_name']) : '';
			if (!$action)
			{
				continue;
			}

			$registry[$slug] = [
				'slug' => $slug,
				'label' => $integration['label'],
				'action' => $action,
				'connected' => self::isConnected($slug),
				'locked' => self::isLocked($slug),
				'requiredPlan' => self::getRequiredPlan($slug),
				'requiredPlanLabel' => self::getRequiredPlan($slug) ? self::getRequiredPlanLabel($slug) : '',
				'connectionType' => self::getConnectionType($slug),
				'form' => self::getFormSchema($slug)
			];
		}

		$registry = apply_filters('firebox/form/integrations_registry', $registry);

		return is_array($registry) ? $registry : [];
	}

	/**
	 * Returns normalized Form integration schema for a given integration slug.
	 *
	 * @param   string  $slug
	 *
	 * @return  array
	 */
	private static function getFormSchema($slug = '')
	{
		$integration = self::getIntegration($slug);
		$integration = is_array($integration) ? $integration : [];
		$form = isset($integration['form']) && is_array($integration['form']) ? $integration['form'] : [];
		$form = array_replace_recursive([
			'legacyApiKeyField' => '',
			'defaults' => [],
			'legacyMap' => [],
			'fields' => []
		], $form);

		$legacy_map = [];
		if (!empty($form['legacyMap']) && is_array($form['legacyMap']))
		{
			foreach ($form['legacyMap'] as $setting_key => $legacy_field)
			{
				$setting_key = trim((string) $setting_key);
				$legacy_field = trim((string) $legacy_field);
				if ($setting_key === '' || $legacy_field === '')
				{
					continue;
				}

				$legacy_map[$setting_key] = $legacy_field;
			}
		}

		$fields = [];
		if (!empty($form['fields']) && is_array($form['fields']))
		{
			foreach ($form['fields'] as $field)
			{
				if (!is_array($field))
				{
					continue;
				}

				$type = strtolower(trim((string) (isset($field['type']) ? $field['type'] : '')));
				$key = trim((string) (isset($field['key']) ? $field['key'] : ''));

				if (!in_array($type, ['list', 'select', 'toggle'], true) || $key === '')
				{
					continue;
				}

				$normalized_field = [
					'type' => $type,
					'key' => $key,
					'label' => !empty($field['label']) ? self::translateRegistryText($field['label']) : ''
				];

				if ($type === 'select')
				{
					$options = [];
					if (!empty($field['options']) && is_array($field['options']))
					{
						foreach ($field['options'] as $option)
						{
							if (!is_array($option) || !array_key_exists('value', $option))
							{
								continue;
							}

							$options[] = [
								'value' => $option['value'],
								'label' => !empty($option['label']) ? self::translateRegistryText($option['label']) : ''
							];
						}
					}

					if (empty($options))
					{
						continue;
					}

					$normalized_field['options'] = $options;
				}

				$fields[] = $normalized_field;
			}
		}

		return [
			'legacyApiKeyField' => trim((string) $form['legacyApiKeyField']),
			'defaults' => is_array($form['defaults']) ? $form['defaults'] : [],
			'legacyMap' => $legacy_map,
			'fields' => $fields
		];
	}

	/**
	 * Translates known integration registry labels/placeholders.
	 *
	 * @param   string  $text
	 *
	 * @return  string
	 */
	private static function translateRegistryText($text = '')
	{
		$text = trim((string) $text);

		switch ($text)
		{
			case 'API Key':
				return __('API Key', 'firebox');

			case 'API URL':
				return __('API URL', 'firebox');

			case 'List':
				return __('List', 'firebox');

			case 'Double Optin':
				return __('Double Optin', 'firebox');

			case 'Update existing user':
				return __('Update existing user', 'firebox');

			case 'Group':
				return __('Group', 'firebox');

			case 'Subscriber Status':
				return __('Subscriber Status', 'firebox');

			case 'Active':
				return __('Active', 'firebox');

			case 'Unconfirmed':
				return __('Unconfirmed', 'firebox');

			case 'Unsubscribed':
				return __('Unsubscribed', 'firebox');

			case 'Bounced':
				return __('Bounced', 'firebox');

			case 'Junk':
				return __('Junk', 'firebox');

			case 'Test Mode':
				return __('Test Mode', 'firebox');

			case 'https://your-account.api-us1.com':
				return __('https://your-account.api-us1.com', 'firebox');

			case 'Organization ID (OID), e.g. 00DXXXXXXXXXXXX':
				return __('Organization ID (OID), e.g. 00DXXXXXXXXXXXX', 'firebox');
		}

		return $text;
	}
}
