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

class WeeklyStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		$timezone_converted_date = $this->metric->getTimezoneDateSQL('', 'l.date');
		return 'DATE_FORMAT(STR_TO_DATE(CONCAT(yearweek(' . $timezone_converted_date . '), " ' . firebox()->_('FB_MONDAY') . '"), \'%X%V %W\'), \'%d %b %y\') as label, count(*) as total';
	}
	
	public function getGroupBy(): string
	{
		$timezone_converted_date = $this->metric->getTimezoneDateSQL('', 'l.date');
		return 'GROUP BY yearweek(' . $timezone_converted_date . ')';
	}
}
