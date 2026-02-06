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

class WeeklyStrategy extends BaseConversionRateQueryStrategy
{
	public function getSelect(): string
	{
		$timezoneDate = $this->getTimezoneConvertedDate();

		return 'DATE_FORMAT(STR_TO_DATE(CONCAT(yearweek(' . $timezoneDate . '), " ' . firebox()->_('FB_MONDAY') . '"), \'%%X%%V %%W\'), \'%%d %%b %%y\') as label, CASE WHEN COUNT(DISTINCT l.id) = 0 THEN 0 ELSE (COUNT(DISTINCT bld.id) / COUNT(DISTINCT l.id)) * 100 END AS total';
	}

	public function getWhere(): string
	{
		return '';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY yearweek(' . $this->getTimezoneConvertedDate() . ')';
	}

	public function getOrderBy(): string
	{
		$options = $this->metric->getOptions();
		
		if (isset($options['orderby']))
		{
			return 'ORDER BY ' . $options['orderby'];
		}

		return 'ORDER BY yearweek(' . $this->getTimezoneConvertedDate() . ') DESC';
	}

	public function getHaving(): string
	{
		return '';
	}

	private function getTimezoneConvertedDate(): string
	{
		return $this->metric->getTimezoneDateSQL('', 'l.date');
	}
}
