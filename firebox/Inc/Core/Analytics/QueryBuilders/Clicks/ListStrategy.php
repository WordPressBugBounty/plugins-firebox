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

namespace FireBox\Core\Analytics\QueryBuilders\Clicks;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class ListStrategy extends BaseClicksQueryStrategy
{
	public function getSelect(): string
	{
		if ($this->metric->isSingleDay())
		{
			return 'CONCAT(' . $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'bld.date', '\'%H\'') . ', \':00\') as label, COUNT(bld.id) as total';
		}
		
		return $this->metric->getTimezoneDateSQL('DATE', 'bld.date') . ' as label, COUNT(bld.id) as total';
	}
	
	public function getWhere(): string
	{
		return 'AND bld.event = \'click\'';
	}
	
	public function getGroupBy(): string
	{
		if ($this->metric->isSingleDay())
		{
			return 'GROUP BY CONCAT(' . $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'bld.date', '\'%H\'') . ', \':00\')';
		}
		
		return 'GROUP BY ' . $this->metric->getTimezoneDateSQL('DATE', 'bld.date');
	}
	
	public function getOrderBy(): string
	{
		return 'ORDER BY bld.date DESC';
	}
}
