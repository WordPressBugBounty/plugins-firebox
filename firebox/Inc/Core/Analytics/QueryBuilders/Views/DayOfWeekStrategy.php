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

class DayOfWeekStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		return $this->metric->getTimezoneDateSQL('DAYNAME', 'l.date') . ' as label, COUNT(*) as total';
	}
	
	public function getWhere(): string
	{
		return '';
	}
	
	public function getGroupBy(): string
	{
		return 'GROUP BY label';
	}
	
	public function getOrderBy(): string
	{
		return $this->getTotalOrderBy();
	}
	
	public function getHaving(): string
	{
		return '';
	}
}
