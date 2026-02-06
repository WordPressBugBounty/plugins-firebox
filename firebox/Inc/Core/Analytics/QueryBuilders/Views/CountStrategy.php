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

class CountStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		return 'COUNT(*) as total';
	}
	
	public function getOrderBy(): string
	{
		return ''; // Count strategies don't need ordering
	}
}
