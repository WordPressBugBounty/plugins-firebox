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

class PagesStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		return 'l.page as label, COUNT(*) as total';
	}
	
	public function getGroupBy(): string
	{
		return 'GROUP BY l.page';
	}
	
	public function getOrderBy(): string
	{
		return $this->getTotalOrderBy();
	}
}
