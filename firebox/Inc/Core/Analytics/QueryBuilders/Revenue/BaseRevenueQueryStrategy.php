<?php
/**
 * @package         FireBox
 * @version         3.1.4 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Analytics\QueryBuilders\Revenue;

use FireBox\Core\Analytics\QueryBuilders\BaseQueryStrategy;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

abstract class BaseRevenueQueryStrategy extends BaseQueryStrategy
{
	/**
	 * Revenue uses logs_details table as primary date column
	 */
	protected function getDateColumn(): string
	{
		return 'bld.date';
	}
	
	/**
	 * Revenue-specific FROM/JOIN logic
	 * Uses logs_details as primary table with JOIN to logs
	 * Includes subquery for multi-touch attribution global counts (MySQL 5.5+ compatible)
	 */
	protected function getDefaultFromAndJoins(): string
	{
		$table_details = $this->metric->getTableDetails();
		$table_logs = $this->metric->getTableLogs();
		
		return "{$table_details} as bld
			LEFT JOIN {$table_logs} as bl ON bl.id = bld.log_id
			LEFT JOIN (
				SELECT 
					JSON_EXTRACT(event_label, '\$.order_id') as order_id,
					JSON_EXTRACT(event_label, '\$.order_type') as order_type,
					SUM(CASE WHEN event_source = 'conversion' THEN 1 ELSE 0 END) as global_conversion_count,
					SUM(CASE WHEN event_source = 'impression' THEN 1 ELSE 0 END) as global_impression_count,
					COUNT(*) as global_contribution_count
				FROM {$table_details}
				WHERE event = 'revenue'
				GROUP BY JSON_EXTRACT(event_label, '\$.order_id'), JSON_EXTRACT(event_label, '\$.order_type')
			) counts ON counts.order_id = JSON_EXTRACT(bld.event_label, '\$.order_id') 
			           AND counts.order_type = JSON_EXTRACT(bld.event_label, '\$.order_type')";
	}
	
	/**
	 * Revenue metrics use 'bl' alias for logs table
	 */
	protected function getTableAliases(): array
	{
		return [
			'logs' => 'bl',
			'details' => 'bld'
		];
	}
	
	/**
	 * Override getLimitOffset for Revenue-specific logic
	 * For breakdown types that need revenue calculation and sorting, don't apply SQL LIMIT
	 */
	public function getLimitOffset(): string
	{
		$type = $this->metric->getType();
		
		// For breakdown types that need revenue calculation and sorting, don't apply SQL LIMIT
		// The limit will be applied after PHP sorting in processResults
		if (in_array($type, ['top_campaign', 'countries', 'referrers', 'devices', 'pages', 'day_of_week']))
		{
			return '';
		}
		
		// For other types, use the parent method
		return parent::getLimitOffset();
	}
	
	/**
	 * Default processing for revenue strategies
	 * Strategies can override this method for custom processing
	 */
	public function processResults($results)
	{
		return $this->metric->calculateRevenueGrouped($results);
	}
	
	/**
	 * Get the column selection for global multi-touch attribution counts
	 * These come from the subquery JOIN in getDefaultFromAndJoins()
	 * Strategies should append this to their SELECT clause
	 * 
	 * @return string SQL columns from counts subquery
	 */
	protected function getGlobalCountColumns(): string
	{
		return 'counts.global_contribution_count,
			counts.global_conversion_count,
			counts.global_impression_count';
	}
}
