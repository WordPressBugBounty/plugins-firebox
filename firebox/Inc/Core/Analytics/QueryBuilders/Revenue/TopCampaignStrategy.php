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

class TopCampaignStrategy extends BaseRevenueQueryStrategy
{
	public function getSelect(): string
	{
		$wpdb = $this->metric->getWpdb();
		// Global counts come from the subquery JOIN in getDefaultFromAndJoins()
		return 'bl.box as id, 
			(select p.post_title from ' . $wpdb->prefix . 'posts as p WHERE p.ID = bl.box) as label, 
			JSON_EXTRACT(bld.event_label, \'$.order_type\') as order_type, 
			JSON_EXTRACT(bld.event_label, \'$.order_id\') as order_id, 
			bld.event_source,
			counts.global_contribution_count,
			counts.global_conversion_count,
			counts.global_impression_count';
	}

	public function getWhere(): string
	{
		return 'AND bld.event = \'revenue\'';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY bl.box, JSON_EXTRACT(bld.event_label, \'$.order_id\'), JSON_EXTRACT(bld.event_label, \'$.order_type\')';
	}

	public function getOrderBy(): string
	{
		$orderby = 'bl.box';
		$options = $this->metric->getOptions();
		
		if (isset($options['orderby']))
		{
			$orderby = $options['orderby'];
		}

		return 'ORDER BY ' . $orderby;
	}

	public function getHaving(): string
	{
		return '';
	}
}
