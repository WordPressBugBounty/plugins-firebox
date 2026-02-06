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

namespace FireBox\Core\Analytics\QueryBuilders\Conversions;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class TopCampaignStrategy extends BaseConversionsQueryStrategy
{
	public function getSelect(): string
	{
		$wpdb = $this->metric->getWpdb();
		return 'bl.box as id, (select p.post_title from ' . $wpdb->prefix . 'posts as p WHERE p.ID = bl.box) as label, COUNT(bld.id) as total';
	}

	public function getWhere(): string
	{
		return 'AND bld.event = \'conversion\'';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY bl.box';
	}

	public function getOrderBy(): string
	{
		$orderby = 'total DESC';
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
