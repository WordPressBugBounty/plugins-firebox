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

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class AttributionBreakdownByPeriodStrategy extends BaseRevenueQueryStrategy
{
	public function getSelect(): string
	{
		$periodType = $this->determinePeriodType();
		$globalCounts = ', ' . $this->getGlobalCountColumns();
		
		switch ($periodType) {
			case 'hourly':
				return 'CONCAT(' . $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'bld.date', '\'%H\'') . ', \':00\') as label, bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_type\') as order_type, JSON_EXTRACT(bld.event_label, \'$.order_id\') as order_id' . $globalCounts;
				
			case 'weekly':
				return 'DATE_FORMAT(STR_TO_DATE(CONCAT(yearweek(' . $this->metric->getTimezoneDateSQL('', 'bld.date') . '), " ' . firebox()->_('FB_MONDAY') . '"), \'%X%V %W\'), \'%d %b %y\') as label, bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_type\') as order_type, JSON_EXTRACT(bld.event_label, \'$.order_id\') as order_id' . $globalCounts;
				
			case 'monthly':
				return $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'bld.date', '\'%b %Y\'') . ' as label, bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_type\') as order_type, JSON_EXTRACT(bld.event_label, \'$.order_id\') as order_id' . $globalCounts;
				
			case 'daily':
			default:
				return $this->metric->getTimezoneDateSQL('date', 'bld.date') . ' as label, bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_type\') as order_type, JSON_EXTRACT(bld.event_label, \'$.order_id\') as order_id' . $globalCounts;
		}
	}

	public function getWhere(): string
	{
		return 'AND bld.event = \'revenue\'';
	}

	public function getGroupBy(): string
	{
		$periodType = $this->determinePeriodType();
		
		switch ($periodType) {
			case 'hourly':
				return 'GROUP BY CONCAT(' . $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'bld.date', '\'%H\'') . ', \':00\'), bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_id\'), JSON_EXTRACT(bld.event_label, \'$.order_type\')';
				
			case 'weekly':
				return 'GROUP BY yearweek(' . $this->metric->getTimezoneDateSQL('', 'bld.date') . '), bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_id\'), JSON_EXTRACT(bld.event_label, \'$.order_type\')';
				
			case 'monthly':
				return 'GROUP BY YEAR(' . $this->metric->getTimezoneDateSQL('', 'bld.date') . '), MONTH(' . $this->metric->getTimezoneDateSQL('', 'bld.date') . '), bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_id\'), JSON_EXTRACT(bld.event_label, \'$.order_type\')';
				
			case 'daily':
			default:
				return 'GROUP BY ' . $this->metric->getTimezoneDateSQL('date', 'bld.date') . ', bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_id\'), JSON_EXTRACT(bld.event_label, \'$.order_type\')';
		}
	}

	public function getOrderBy(): string
	{
		return '';
	}

	public function getHaving(): string
	{
		return '';
	}

	/**
	 * Process results for attribution breakdown by period
	 */
	public function processResults($results)
	{
		return $this->metric->calculateAttributionBreakdownByPeriod($results);
	}

	/**
	 * Determine the period type based on chart filter and days between
	 */
	protected function determinePeriodType()
	{
		$options = $this->metric->getOptions();
		$days_between = $options['days_between'] ?? 1;
		$chart_filter = $options['chart_filter'] ?? 'list';
		
		// Same logic as chart endpoint
		if ($days_between == 1) {
			return 'hourly';
		}
		
		if ($days_between > 1) {
			switch ($chart_filter) {
				case 'weekly':
					return 'weekly';
				case 'monthly':
					return 'monthly';
				case 'list':
				default:
					return 'daily';
			}
		}
		
		return 'daily';
	}
}
