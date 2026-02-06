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

class CountStrategy extends BaseConversionRateQueryStrategy
{
	public function getSelect(): string
	{
		return 'COUNT(DISTINCT l.id) AS total_views, COUNT(DISTINCT bld.id) AS total_conversions, CASE WHEN COUNT(DISTINCT l.id) = 0 THEN 0 ELSE (COUNT(DISTINCT bld.id) / COUNT(DISTINCT l.id)) * 100 END AS total';
	}

	public function getWhere(): string
	{
		return '';
	}
	
	public function getHaving(): string
	{
		return '';
	}
}
