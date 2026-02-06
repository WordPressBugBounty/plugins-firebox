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

class CountriesStrategy extends BaseClicksQueryStrategy
{
	public function getSelect(): string
	{
		return 'bl.country as label, COUNT(bld.id) as total';
	}

	public function getWhere(): string
	{
		return 'AND bld.event = \'click\'';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY bl.country';
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
