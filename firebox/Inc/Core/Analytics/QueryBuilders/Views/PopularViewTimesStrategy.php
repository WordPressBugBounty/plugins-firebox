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

namespace FireBox\Core\Analytics\QueryBuilders\Views;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class PopularViewTimesStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		return 'CONCAT(' . $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'l.date', '\'%H\'') . ', \':00\') as label, COUNT(*) as total';
	}
	
	public function getGroupBy(): string
	{
		return 'GROUP BY CONCAT(' . $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'l.date', '\'%H\'') . ', \':00\')';
	}
	
	public function getWhere(): string
	{
		$options = $this->metric->getOptions();
		if (isset($options['weekday'])) {
			$timezone_converted_date = $this->metric->getTimezoneDateSQL('', 'l.date');
			return 'AND WEEKDAY(' . $timezone_converted_date . ') = \'' . $options['weekday'] . '\'';
		}
		return '';
	}
}
