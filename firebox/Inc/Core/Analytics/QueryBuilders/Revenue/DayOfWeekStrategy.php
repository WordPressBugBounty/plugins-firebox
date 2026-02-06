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

class DayOfWeekStrategy extends BaseRevenueQueryStrategy
{
	public function getSelect(): string
	{
		return $this->metric->getTimezoneDateSQL('DAYNAME', 'bld.date') . ' as label, JSON_EXTRACT(bld.event_label, \'$.order_type\') as order_type, JSON_EXTRACT(bld.event_label, \'$.order_id\') as order_id, bld.event_source, ' . $this->getGlobalCountColumns();
	}

	public function getWhere(): string
	{
		return 'AND bld.event = \'revenue\'';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY ' . $this->metric->getTimezoneDateSQL('DAYNAME', 'bld.date') . ', JSON_EXTRACT(bld.event_label, \'$.order_id\'), JSON_EXTRACT(bld.event_label, \'$.order_type\')';
	}

	public function getOrderBy(): string
	{
		$orderby = $this->metric->getTimezoneDateSQL('DAYNAME', 'bld.date');
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
}
