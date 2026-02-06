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

class MonthlyStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		return $this->metric->getTimezoneDateSQL('DATE_FORMAT', 'l.date', '\'%b %Y\'') . ' as label, COUNT(*) as total';
	}
	
	public function getGroupBy(): string
	{
		return 'GROUP BY ' . $this->metric->getTimezoneDateSQL('YEAR', 'l.date') . ', ' . $this->metric->getTimezoneDateSQL('MONTH', 'l.date');
	}
}
