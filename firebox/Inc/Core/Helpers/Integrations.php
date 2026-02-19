<?php
/**
 * @package         FireBox
 * @version         3.1.5
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
		]
		
		
	];

	/**
	 * Locked integrations metadata for unavailable plans.
	 *
	 * @var  array
	 */
	private static $locked_integrations = [
		
		'acymailing' => [
			'label' => 'AcyMailing',
			'class_name' => 'AcyMailing',
			'settings_key' => '',
			'docs_slug' => '',
			'logo_file' => 'acymailing.png',
			'connection_type' => 'locked',
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
		],
		
		
		'mailerlite' => [
			'label' => 'MailerLite',
			'class_name' => 'MailerLite',
			'settings_key' => 'mailerlite_api_key',
			'docs_slug' => 'mailerlite',
			'logo_file' => 'mailerlite.svg',
			'connection_type' => 'locked',
			'required_plan' => 'pro',
			'form' => [
				'defaults' => [
					'listId' => '',
					'status' => 'active'
				],
				'legacyMap' => [],
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
			if (!empty($integration['pro_only']) && !self::isProVersion())
			{
				continue;
			}

			$required_plan = isset($integration['required_plan']) ? trim((string) $integration['required_plan']) : '';
			if ($required_plan && !self::isRequiredPlanMet($required_plan))
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

		foreach (self::$locked_integrations as $slug => $integration)
		{
			$required_plan = isset($integration['required_plan']) ? trim((string) $integration['required_plan']) : '';
			if ($required_plan && self::isRequiredPlanMet($required_plan))
			{
				continue;
			}

			$item = self::normalizeIntegrationEntry($slug, $integration);
			$item['locked'] = true;
			$item['connected'] = false;

			$integrations[$slug] = $item;
		}

		return $integrations;
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

		$integrations = array_merge(
			self::getIntegrations(),
			self::getLockedIntegrations()
		);
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
		if (!$integration = self::getIntegration($integration))
		{
			return null;
		}

		$key = isset($integration['settings_key']) ? trim((string) $integration['settings_key']) : '';

		return !empty($key) ? $key : null;
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
		$keys = array_column(
			array_merge(self::getIntegrations(), self::getLockedIntegrations()),
			'settings_key'
		);
		$keys = array_filter($keys, function($key) {
			return is_string($key) && trim($key) !== '';
		});

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

		foreach (array_merge(self::getIntegrations(), self::getLockedIntegrations()) as $integration)
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
				$item['upgrade_badge_label'] = sprintf(__('Upgrade to %s', 'firebox'), $required_plan_label);
				$item['upgrade_label'] = sprintf(fpframework()->_('FPF_UNLOCK_X_FEATURE'), $required_plan_label);
				$item['upgrade_plan'] = $required_plan_label;
				$item['current_plan'] = self::getCurrentPlan();
				$item['upgrade_url'] = self::getUpgradeURL($slug);
			}
			else if ($connection_type === 'plugin')
			{
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
				$item['api_key'] = self::getGlobalAPIKey($slug);
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
		return defined('FBOX_LICENSE_TYPE') && FBOX_LICENSE_TYPE === 'pro';
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
	 * Returns the global API key.
	 *
	 * @param   string  $integration
	 *
	 * @return  string
	 */
	public static function getGlobalAPIKey($integration = '')
	{
		if (!$key = self::getIntegrationSettingKey($integration))
		{
			return '';
		}

		$settings = get_option('firebox_settings', []);
		if (!is_array($settings))
		{
			return '';
		}

		if (!isset($settings[$key]))
		{
			return '';
		}

		return self::getGlobalAPIKeyEncryption()->decrypt($settings[$key]);
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
		if (!$key = self::getIntegrationSettingKey($integration))
		{
			return false;
		}

		$settings = get_option('firebox_settings', []);
		$settings = is_array($settings) ? $settings : [];

		$settings[$key] = self::getGlobalAPIKeyEncryption()->encrypt($api_key);

		return update_option('firebox_settings', $settings);
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
		return !empty(self::getGlobalAPIKey($integration));
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

		return self::getGlobalAPIKey($integration);
	}

	/**
	 * Returns integrations registry used by the Form block editor.
	 *
	 * @return  array
	 */
	public static function getFormEditorIntegrationsRegistry()
	{
		$registry = [];

		foreach (array_merge(self::getIntegrations(), self::getLockedIntegrations()) as $integration)
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
					'label' => !empty($field['label']) ? __((string) $field['label'], 'firebox') : ''
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
								'label' => !empty($option['label']) ? __((string) $option['label'], 'firebox') : ''
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
}
