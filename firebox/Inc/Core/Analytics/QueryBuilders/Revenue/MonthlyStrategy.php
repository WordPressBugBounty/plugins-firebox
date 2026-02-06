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

class MonthlyStrategy extends BaseRevenueQueryStrategy
{
	public function getSelect(): string
	{
		return $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'bld.date', '\'%b %Y\'') . ' as label, JSON_EXTRACT(bld.event_label, \'$.order_type\') as order_type, JSON_EXTRACT(bld.event_label, \'$.order_id\') as order_id, bld.event_source, ' . $this->getGlobalCountColumns();
	}

	public function getWhere(): string
	{
		return 'AND bld.event = \'revenue\'';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY YEAR(' . $this->metric->getTimezoneDateSQL('', 'bld.date') . '), MONTH(' . $this->metric->getTimezoneDateSQL('', 'bld.date') . '), JSON_EXTRACT(bld.event_label, \'$.order_id\'), JSON_EXTRACT(bld.event_label, \'$.order_type\')';
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
	 * Process results for monthly revenue
	 */
	public function processResults($results)
	{
		return $this->metric->calculateRevenueGrouped($results);
	}
}
