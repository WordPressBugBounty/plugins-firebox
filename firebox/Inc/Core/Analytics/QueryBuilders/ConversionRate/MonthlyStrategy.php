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

class MonthlyStrategy extends BaseConversionRateQueryStrategy
{
	public function getSelect(): string
	{
		return $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'l.date', '\'%b %Y\'') . ' as label, CASE WHEN COUNT(DISTINCT l.id) = 0 THEN 0 ELSE (COUNT(DISTINCT bld.id) / COUNT(DISTINCT l.id)) * 100 END AS total';
	}

	public function getWhere(): string
	{
		return '';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY ' . $this->metric->getTimezoneDateSQL('YEAR', 'l.date') . ', ' . $this->metric->getTimezoneDateSQL('MONTH', 'l.date');
	}

	public function getOrderBy(): string
	{
		$options = $this->metric->getOptions();
		
		if (isset($options['orderby']))
		{
			return 'ORDER BY ' . $options['orderby'];
		}

		return 'ORDER BY ' . $this->metric->getTimezoneDateSQL('DATE', 'l.date') . ' DESC';
	}

	public function getHaving(): string
	{
		return '';
	}
}
