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

namespace FireBox\Core\Analytics\QueryBuilders\Revenue;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class AttributionBreakdownStrategy extends BaseRevenueQueryStrategy
{
	public function getSelect(): string
	{
		return 'bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_type\') as order_type, JSON_EXTRACT(bld.event_label, \'$.order_id\') as order_id, ' . $this->getGlobalCountColumns();
	}

	public function getWhere(): string
	{
		return 'AND bld.event = \'revenue\'';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY bld.event_source, JSON_EXTRACT(bld.event_label, \'$.order_id\'), JSON_EXTRACT(bld.event_label, \'$.order_type\')';
	}

	public function getOrderBy(): string
	{
		return '';
	}

	public function getHaving(): string
	{
		return '';
	}

	/**
	 * Process results for attribution breakdown
	 */
	public function processResults($results)
	{
		return $this->metric->calculateAttributionBreakdown($results);
	}
}
