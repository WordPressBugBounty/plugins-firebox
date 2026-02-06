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

class ListStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		if ($this->metric->isSingleDay())
		{
			return 'CONCAT(' . $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'l.date', '\'%H\'') . ', \':00\') as label, COUNT(*) as total';
		}
		
		return $this->metric->getTimezoneDateSQL('DATE', 'l.date') . ' as label, COUNT(*) as total';
	}
	
	public function getGroupBy(): string
	{
		if ($this->metric->isSingleDay())
		{
			return 'GROUP BY CONCAT(' . $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'l.date', '\'%H\'') . ', \':00\')';
		}
		
		return 'GROUP BY ' . $this->metric->getTimezoneDateSQL('DATE', 'l.date');
	}
	
	public function getOrderBy(): string
	{
		return 'ORDER BY l.date DESC';
	}
}
