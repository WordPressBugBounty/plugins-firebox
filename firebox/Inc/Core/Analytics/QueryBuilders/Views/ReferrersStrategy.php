<?php
/**
 * @package         FireBox
 * @version         3.1.5 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Analytics\QueryBuilders\Views;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class ReferrersStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		return 'l.referrer as label, COUNT(*) as total';
	}
	
	public function getGroupBy(): string
	{
		return 'GROUP BY l.referrer';
	}
	
	public function getOrderBy(): string
	{
		return $this->getTotalOrderBy();
	}
}
