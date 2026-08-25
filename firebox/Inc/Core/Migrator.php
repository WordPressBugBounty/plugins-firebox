<?php
/**
 * @package         FireBox
 * @version         3.1.11 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FireBox\Core\Helpers\Form\Form;

class Migrator
{
	/**
	 * The currently installed version.
	 * 
	 * @var string
	 */
	private $installedVersion;

	/**
	 * Whether we run a migration.
	 * 
	 * @var bool
	 */
	private $run = false;
	
	public function __construct($installedVersion = '')
	{
		$this->installedVersion = $installedVersion;
	}

	public function run()
	{
		if (!$this->installedVersion)
		{
			return;
		}

		$this->moveFireBoxDataToUploadsDirectory();
		$this->installFormsTables();
		$this->improveIndexes();
		$this->updatePublishingRulesToConditionBuilder();
		$this->updateEcommerceProductsInCartConditions();
		$this->updateAnalyticsStoragePeriodSetting();
		$this->update213version();
		$this->addMissingCapabilities();
		
		
		$this->addNewIndexes();
		$this->addSubmissionsIndexes();
		$this->preserveCampaignRoleAccess();
		$this->grantExecutePHPCapability();
		$this->grantExecuteJSCapability();

		// Update firebox version
		if ($this->run)
		{
			// Update version
			update_option('firebox_version', FBOX_VERSION);
		}
	}

	/**
	 * Moves plugin data to its own /wp-content/uploads/firebox folder.
	 * 
	 * @since	1.1.10
	 * 
	 * @return  void
	 */
	private function moveFireBoxDataToUploadsDirectory()
	{
        if (version_compare($this->installedVersion, '1.1.10', '>=')) 
        {
            return;
        }

		// Create dirs
		\FireBox\Core\Helpers\Activation::createLibraryDirectories();
		
		$this->run = true;
	}

	/**
	 * Creates the forms tables.
	 * 
	 * @since   1.2.0
	 * 
	 * @return  void
	 */
	private function installFormsTables()
	{
        if (version_compare($this->installedVersion, '1.2.0', '>=')) 
        {
            return;
        }

		if (!class_exists('Activation'))
		{
			require_once FBOX_PLUGIN_DIR . '/Inc/Framework/Inc/Admin/Includes/Activation.php';
		}
		$activation = new \Activation(firebox()->hook_data);
		$activation->initTables();
		
		$this->run = true;
	}

	/**
	 * Improves indexes on form and box logs tables.
	 * 
	 * @since   1.2.4
	 * 
	 * @return  void
	 */
	private function improveIndexes()
	{
        if (version_compare($this->installedVersion, '1.2.4', '>=')) 
        {
            return;
        }

		if (!$this->hasIndex('idx_box_id', 'firebox_logs'))
		{
			global $wpdb;
			$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_logs ADD INDEX `idx_box_id` (`box`, `id`)");
		}

		if (!$this->hasIndex('idx_meta_key', 'firebox_submission_meta'))
		{
			global $wpdb;
			$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_submission_meta ADD INDEX `idx_meta_key` (`meta_key`)");
		}
		
		$this->run = true;
	}

	/**
	 * Update old Publishing Rules to Condition Builder.
	 * 
	 * @since   2.0.0
	 * 
	 * @return  void
	 */
	private function updatePublishingRulesToConditionBuilder()
	{
		if (version_compare($this->installedVersion, '2.0.0', '>=')) 
		{
			return;
		}

		// Get all boxes, regardless of post status, thus we pass [] to ensure all popups are returned.
		$boxes = \FireBox\Core\Helpers\BoxHelper::getAllBoxes([]);

		if (!$boxes->posts)
		{
			$this->run = true;
			return;
		}

		foreach ($boxes->posts as $box)
		{
			// Get post meta and add it to its object
			$meta = \FireBox\Core\Helpers\BoxHelper::getMeta($box->ID);
			
			// Skip popup that already has rules
			if (isset($meta['rules']))
			{
				continue;
			}

			$box->params = new \FPFramework\Libs\Registry($meta);

			// Pass only params
			\FPFramework\Base\Conditions\Migrator::run($box->params);

			// Update popup settings
			update_post_meta($box->ID, 'fpframework_meta_settings', wp_slash(json_decode(wp_json_encode($box->params), true)));
		}

		$this->run = true;
	}

	/**
	 * Updates the following conditions to match the new condition settings.
	 * 
	 * EDD\CartContainsProducts
	 * WooCommerce\CartContainsProducts
	 * 
	 * @since   2.0.10
	 * 
	 * @return  void
	 */
	private function updateEcommerceProductsInCartConditions()
	{
		if (version_compare($this->installedVersion, '2.0.10', '>=')) 
		{
			return;
		}

		// Get all boxes, regardless of post status, thus we pass [] to ensure all popups are returned.
		$boxes = \FireBox\Core\Helpers\BoxHelper::getAllBoxes([]);

		if (!$boxes->posts)
		{
			$this->run = true;
			return;
		}

		foreach ($boxes->posts as $box)
		{
			$meta = \FireBox\Core\Helpers\BoxHelper::getMeta($box->ID);

			if (!isset($meta['rules']) || !is_array($meta['rules']))
			{
				continue;
			}

			$allowed_rules = [
				'EDD\CartContainsProducts',
				'WooCommerce\CartContainsProducts'
			];

			$changed = false;

			foreach ($meta['rules'] as &$ruleset)
			{
				if (!isset($ruleset['rules']))
				{
					continue;
				}
				
				foreach ($ruleset['rules'] as &$rule)
				{
					if (!isset($rule['name']))
					{
						continue;
					}
					
					if (!in_array($rule['name'], $allowed_rules))
					{
						continue;
					}

					if (!isset($rule['value']))
					{
						continue;
					}

					if (!is_array($rule['value']))
					{
						continue;
					}

					if (!count($rule['value']))
					{
						continue;
					}

					$changed = true;

					$rule['value'] = array_map(function($id) {
						// Old value wasn't an array, skip if new value was found
						if (is_array($id))
						{
							return $id;
						}
						
						return [
							'value' => $id,
							'quantity_operator' => 'any',
							'quantity_value1' => '1',
							'quantity_value2' => '1'
						];
					}, $rule['value']);
				}
			}

			if (!$changed)
			{
				continue;
			}
			
			update_post_meta($box->ID, 'fpframework_meta_settings', wp_slash(json_decode(wp_json_encode($meta), true)));
		}

		$this->run = true;
	}

	/**
	 * Updates the Analytics Storage Period setting.
	 * 
	 * @since   2.1.1
	 * 
	 * @return  void
	 */
	public function updateAnalyticsStoragePeriodSetting()
	{
		if (version_compare($this->installedVersion, '2.1.1', '>=')) 
		{
			return;
		}

		$params = get_option('firebox_settings', []);

		$statsdays = isset($params['statsdays']) ? $params['statsdays'] : 730;
		$statsdays_custom = isset($params['statsdays_custom']) ? $params['statsdays_custom'] : false;

		if ($statsdays === 'custom')
		{
			$statsdays = $statsdays_custom;
		}

		$statsdays = (int) $statsdays;

		if ($statsdays <= 365)
		{
			$statsdays = 365;
		}
		else if ($statsdays <= 365 * 2)
		{
			$statsdays = 365 * 2;
		}
		else
		{
			$statsdays = 365 * 5;
		}

		$params['statsdays'] = $statsdays;

		update_option('firebox_settings', $params);

		$this->run = true;
	}

	public function update213version()
	{
		$this->updateLogsDetailsParamsColumn();
		$this->moveFormConversionsToLogsDetailsTable();
	}

	/**
	 * Adds a new column called event_source after event column.
	 * Renames the params column in the logs details table to event_label.
	 * 
	 * @since   2.1.4
	 * 
	 * @return  void
	 */
	public function updateLogsDetailsParamsColumn()
	{
		if (version_compare($this->installedVersion, '2.1.4', '>=')) 
		{
			return;
		}

		global $wpdb;
		
		// If has column params
		$row = $wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = '{$wpdb->dbname}' AND table_name = '{$wpdb->prefix}firebox_logs_details' AND column_name = 'params'");
		if (!$row)
		{
			$this->run = true;
			return;
		}

		// Add column "event_source" after "event"
		$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_logs_details ADD COLUMN `event_source` varchar(200) NOT NULL AFTER `event`");

		// Rename params to event_label
		$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_logs_details CHANGE COLUMN `params` `event_label` text NOT NULL");
		
		$this->run = true;
	}

	/**
	 * Moves form conversions from submission meta table to logs details table.
	 * 
	 * @since   2.1.4
	 * 
	 * @return  void
	 */
	private function moveFormConversionsToLogsDetailsTable()
	{
		if (version_compare($this->installedVersion, '2.1.4', '>=')) 
		{
			return;
		}

		global $wpdb;

		// Get all submission meta data
		$boxlogids = firebox()->tables->submissionmeta->getResults([
			'select' => [
				"{$wpdb->prefix}firebox_submission_meta.id",
				'l.box',
				's.form_id',
				"{$wpdb->prefix}firebox_submission_meta.meta_value",
				"{$wpdb->prefix}firebox_submission_meta.created_at"
			],
			'where' => [
				'meta_key' => " = '" . absint('box_log_id') . "'"
			],
			'join' => [
				"LEFT JOIN" => " {$wpdb->prefix}firebox_submissions as s ON s.id = submission_id LEFT JOIN {$wpdb->prefix}firebox_logs as l ON l.id = meta_value",
			]
		]);

		if (!$boxlogids)
		{
			$this->run = true;
			return;
		}

		global $wpdb;

		// Migrate to the box logs details table
		foreach ($boxlogids as $row)
		{
			if (!$row->box)
			{
				continue;
			}

			$form = Form::getFormByID($row->form_id);

            $data = [
                'log_id' => $row->meta_value,
                'event' => 'conversion',
				'event_source' => 'form',
                'event_label' => isset($form['block']['attrs']['formName']) ? $form['block']['attrs']['formName'] : 'FireBox #' . $row->box . ' Form',
                'date' => $row->created_at
            ];

            firebox()->tables->boxlogdetails->insert($data);
		}

		// Delete all submission_meta rows
		$ids = implode(',', array_map('intval', array_column($boxlogids, 'id')));
		$sm_table_name = firebox()->tables->submissionmeta->getFullTableName();
		$wpdb->query("DELETE FROM $sm_table_name WHERE id IN($ids)");// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$this->run = true;
	}

	public function addMissingCapabilities()
	{
		if (version_compare($this->installedVersion, '2.1.15', '>=')) 
		{
			return;
		}

		$cap_name = 'read_fireboxes';

		$admin = get_role('administrator');

		if ($admin)
		{
			if ($admin->has_cap($cap_name))
			{
				return true;
			}

			$admin->add_cap($cap_name);
		}
		else
		{
			$roles = get_editable_roles();

			foreach ($roles as $role_name => $data)
			{
				if (isset($data['capabilities']['manage_options']) && $data['capabilities']['manage_options'])
				{
					$role = get_role($role_name);

					if (!$role)
					{
						continue;
					}

					if ($role->has_cap($cap_name))
					{
						continue;
					}
		
					$role->add_cap($cap_name);
				}
			}
		}

        wp_get_current_user()->get_role_caps();

		return true;
	}

	

	

	/**
	 * Adds new indexes to the logs details table.
	 * 
	 * @since   3.1.0
	 * 
	 * @return  void
	 */
	private function addNewIndexes()
	{
		if (version_compare($this->installedVersion, '3.1.0', '>=')) 
		{
			return;
		}

		if (!$this->hasIndex('idx_firebox_logs_details_event_log', 'firebox_logs_details'))
		{
			global $wpdb;
			$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_logs_details ADD INDEX `idx_firebox_logs_details_event_log` (`event`, `log_id`)");
		}
		if (!$this->hasIndex('idx_firebox_logs_details_event_source_log', 'firebox_logs_details'))
		{
			global $wpdb;
			$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_logs_details ADD INDEX `idx_firebox_logs_details_event_source_log` (`event_source`, `log_id`)");
		}
		if (!$this->hasIndex('idx_firebox_logs_details_event_date', 'firebox_logs_details'))
		{
			global $wpdb;
			$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_logs_details ADD INDEX `idx_firebox_logs_details_event_date` (`event`, `date`)");
		}
		if (!$this->hasIndex('idx_firebox_logs_details_event_source_event_log', 'firebox_logs_details'))
		{
			global $wpdb;
			$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_logs_details ADD INDEX `idx_firebox_logs_details_event_source_event_log` (`event_source`, `event`, `log_id`)");
		}
		if (!$this->hasIndex('idx_firebox_logs_details_event_source_event_date', 'firebox_logs_details'))
		{
			global $wpdb;
			$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_logs_details ADD INDEX `idx_firebox_logs_details_event_source_event_date` (`event_source`, `event`, `date`)");
		}
		
		$this->run = true;
	}

	/**
	 * Checks whether an index exists in a table.
	 * 
	 * @param   string  $index
	 * @param   string  $table
	 * 
	 * @return  bool
	 */
	/**
	 * Adds missing indexes on the submissions tables to speed up
	 * form_id/state lookups and submission_id meta lookups.
	 *
	 * @since   3.1.9
	 *
	 * @return  void
	 */
	private function addSubmissionsIndexes()
	{
		if (version_compare($this->installedVersion, '3.1.9', '>='))
		{
			return;
		}

		if (!$this->hasIndex('idx_form_id_state', 'firebox_submissions'))
		{
			global $wpdb;
			$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_submissions ADD INDEX `idx_form_id_state` (`form_id`, `state`)");
		}

		if (!$this->hasIndex('idx_submission_id', 'firebox_submission_meta'))
		{
			global $wpdb;
			$wpdb->query("ALTER TABLE {$wpdb->prefix}firebox_submission_meta ADD INDEX `idx_submission_id` (`submission_id`)");
		}

		$this->run = true;
	}

	/**
	 * Keeps existing roles' campaign access working after the campaign post type moved
	 * from core's "post" capabilities to FireBox's own.
	 *
	 * Before this change anyone with edit_posts could edit campaigns. Granting each role
	 * the FireBox capability matching the core capability it already holds preserves
	 * exactly who could do what, so upgrading changes nobody's access.
	 *
	 * Note this deliberately does not grant read_fireboxes: that gates the FireBox admin
	 * screens, was never derived from the post type, and granting it here would hand
	 * roles access they did not previously have.
	 *
	 * Site owners who want to tighten campaign access can now remove these capabilities
	 * and have it actually take effect, which was not previously the case.
	 *
	 * @since   3.1.10
	 *
	 * @return  void
	 */
	private function preserveCampaignRoleAccess()
	{
		if (version_compare($this->installedVersion, '3.1.10', '>='))
		{
			return;
		}

		$map = \FireBox\Core\Admin\Includes\Cpts\Firebox::getPostTypeCapabilities();

		// Only the primitive capabilities are assigned to roles.
		unset($map['edit_post'], $map['read_post'], $map['delete_post'], $map['create_posts']);

		// wp_roles() rather than get_editable_roles(): the latter is admin-only and
		// undefined on the frontend requests this migration also runs on.
		foreach (wp_roles()->role_objects as $role)
		{
			foreach ($map as $core_cap => $firebox_cap)
			{
				if (!empty($role->capabilities[$core_cap]))
				{
					$role->add_cap($firebox_cap);
				}
			}
		}

		$this->run = true;
	}

	/**
	 * Grants the firebox_execute_php capability to administrators.
	 *
	 * This capability gates the PHP display condition and PHP Scripts. It is separate
	 * from the campaign-editing capabilities on purpose, so editing a campaign never
	 * grants the ability to run PHP. Existing installs get it on administrators here;
	 * a site owner can grant it to another role deliberately.
	 *
	 * @since   3.1.11
	 *
	 * @return  void
	 */
	private function grantExecutePHPCapability()
	{
		if (version_compare($this->installedVersion, '3.1.11', '>='))
		{
			return;
		}

		$cap = 'firebox_execute_php';

		$admin = get_role('administrator');

		if ($admin)
		{
			if (!$admin->has_cap($cap))
			{
				$admin->add_cap($cap);
				$this->run = true;
			}

			return;
		}

		// No administrator role on this install: fall back to roles that manage options.
		foreach (wp_roles()->role_objects as $role)
		{
			if (!empty($role->capabilities['manage_options']) && !$role->has_cap($cap))
			{
				$role->add_cap($cap);
				$this->run = true;
			}
		}
	}

	/**
	 * Grants the firebox_execute_js capability to administrators.
	 *
	 * This capability gates campaign-authored JavaScript (the Custom JavaScript
	 * setting and the "Run Javascript" action) alongside unfiltered_html. It exists
	 * so administrators keep custom JS where WordPress withholds unfiltered_html
	 * (multisite site admins, DISALLOW_UNFILTERED_HTML hosts). Like
	 * firebox_execute_php it is separate from the campaign-editing capabilities on
	 * purpose, so editing a campaign never grants it; a site owner can grant it to
	 * another role deliberately.
	 *
	 * @since   3.1.11
	 *
	 * @return  void
	 */
	private function grantExecuteJSCapability()
	{
		if (version_compare($this->installedVersion, '3.1.11', '>='))
		{
			return;
		}

		$cap = 'firebox_execute_js';

		$admin = get_role('administrator');

		if ($admin)
		{
			if (!$admin->has_cap($cap))
			{
				$admin->add_cap($cap);
				$this->run = true;
			}

			return;
		}

		// No administrator role on this install: fall back to roles that manage options.
		foreach (wp_roles()->role_objects as $role)
		{
			if (!empty($role->capabilities['manage_options']) && !$role->has_cap($cap))
			{
				$role->add_cap($cap);
				$this->run = true;
			}
		}
	}

	private function hasIndex($index = '', $table = '')
	{
		if (empty($index) || empty($table))
		{
			return;
		}

		global $wpdb;
		
		$existing = $wpdb->get_row("SHOW CREATE TABLE `{$wpdb->prefix}{$table}`", ARRAY_N);// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if (isset($existing[1]) && strpos(strtolower($existing[1]), 'key `' . $index . '` (') !== false)
		{
			return true;
		}

		return false;
	}
}
