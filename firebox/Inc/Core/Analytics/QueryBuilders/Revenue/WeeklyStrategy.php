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

class WeeklyStrategy extends BaseRevenueQueryStrategy
{

	public function getSelect(): string
	{
		return 'DATE_FORMAT(STR_TO_DATE(CONCAT(yearweek(' . $this->metric->getTimezoneDateSQL('', 'bld.date') . '), " ' . firebox()->_('FB_MONDAY') . '"), \'%X%V %W\'), \'%d %b %y\') as label, JSON_EXTRACT(bld.event_label, \'$.order_type\') as order_type, JSON_EXTRACT(bld.event_label, \'$.order_id\') as order_id, bld.event_source, ' . $this->getGlobalCountColumns();
	}

	public function getWhere(): string
	{
		return 'AND bld.event = \'revenue\'';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY yearweek(' . $this->metric->getTimezoneDateSQL('', 'bld.date') . '), JSON_EXTRACT(bld.event_label, \'$.order_id\'), JSON_EXTRACT(bld.event_label, \'$.order_type\')';
	}

	public function getOrderBy(): string
	{
		$orderby = 'bld.date DESC';
		$options = $this->metric->getOptions();
		
		if (isset($options['orderby']))
		{
			$orderby = $options['orderby'];
		}

		return 'ORDER BY ' . $orderby;
	}

	public function getHaving(): string
	{
		return '';
	}

	/**
	 * Process results for weekly revenue
	 */
	public function processResults($results)
	{
		return $this->metric->calculateRevenueGrouped($results);
	}
}
