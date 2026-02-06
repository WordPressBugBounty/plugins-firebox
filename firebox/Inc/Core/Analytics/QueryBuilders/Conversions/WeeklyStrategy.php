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

namespace FireBox\Core\Analytics\QueryBuilders\Conversions;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class WeeklyStrategy extends BaseConversionsQueryStrategy
{
	public function getSelect(): string
	{
		$timezone_converted_date = $this->metric->getTimezoneDateSQL('', 'bld.date');
		return 'DATE_FORMAT(STR_TO_DATE(CONCAT(yearweek(' . $timezone_converted_date . '), " ' . firebox()->_('FB_MONDAY') . '"), \'%X%V %W\'), \'%d %b %y\') as label, COUNT(bld.id) as total';
	}

	public function getWhere(): string
	{
		return 'AND bld.event = \'conversion\'';
	}

	public function getGroupBy(): string
	{
		$timezone_converted_date = $this->metric->getTimezoneDateSQL('', 'bld.date');
		return 'GROUP BY yearweek(' . $timezone_converted_date . ')';
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
}
