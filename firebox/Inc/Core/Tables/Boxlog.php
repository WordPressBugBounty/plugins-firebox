<?php
/**
 * @package         FireBox
 * @version         3.1.14 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Tables;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Includes\DB;

class Boxlog extends DB
{
	/**
	 * The column list below covers this table in full, so inserts can safely be
	 * restricted to it.
	 *
	 * @var  bool
	 */
	protected $enforce_insert_columns = true;

	public function __construct()
	{
		$this->table_name  = 'firebox_logs';
	}

	/**
	 * Returns whether the given impression log belongs to the given campaign.
	 *
	 * Conversion events are logged against a log_id supplied by the client, so without
	 * this any visitor could attribute conversions to impressions that never happened or
	 * that belong to another campaign.
	 *
	 * @param   int  $log_id
	 * @param   int  $box_id
	 *
	 * @return  bool
	 */
	public function belongsToCampaign($log_id, $box_id)
	{
		$log_id = (int) $log_id;
		$box_id = (int) $box_id;

		if ($log_id <= 0 || $box_id <= 0)
		{
			return false;
		}

		global $wpdb;

		return (bool) $wpdb->get_var($wpdb->prepare(
			"SELECT 1 FROM `{$this->getFullTableName()}` WHERE id = %d AND box = %d LIMIT 1",// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$log_id,
			$box_id
		));
	}

	/**
	 * Get columns and formats 
	 *
	 * @return  array
	*/
	public function get_columns()
	{
		return [
			'id'		 => '%d',
			'sessionid'	 => '%s',
			'visitorid'	 => '%s',
			'user'		 => '%d',
			'box'		 => '%d',
			'page'		 => '%s',
			'country'    => '%s',
			'device'	 => '%s',
			'referrer'	 => '%s',
			'date'		 => '%s',
		];
	}
}