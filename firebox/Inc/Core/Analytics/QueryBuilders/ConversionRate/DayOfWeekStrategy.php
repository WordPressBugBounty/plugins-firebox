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

namespace FireBox\Core\Analytics\QueryBuilders\ConversionRate;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class DayOfWeekStrategy extends BaseConversionRateQueryStrategy
{
	public function getSelect(): string
	{
		return $this->metric->getTimezoneDateSQL('DAYNAME', 'l.date') . ' as label, CASE WHEN COUNT(DISTINCT l.id) = 0 THEN 0 ELSE (COUNT(DISTINCT bld.id) / COUNT(DISTINCT l.id)) * 100 END AS total';
	}

	public function getWhere(): string
	{
		return '';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY ' . $this->metric->getTimezoneDateSQL('DAYOFWEEK', 'l.date');
	}

	public function getOrderBy(): string
	{
		$orderby = 'total desc';
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
