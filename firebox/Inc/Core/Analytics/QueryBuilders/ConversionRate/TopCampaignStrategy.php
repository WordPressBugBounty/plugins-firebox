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

class TopCampaignStrategy extends BaseConversionRateQueryStrategy
{
	public function getSelect(): string
	{
		$wpdb = $this->metric->getWpdb();
		return 'l.box as id, (select p.post_title from ' . $wpdb->prefix . 'posts as p WHERE p.ID = l.box) as label, CASE WHEN COUNT(DISTINCT l.id) = 0 THEN 0 ELSE (COUNT(DISTINCT bld.id) / COUNT(DISTINCT l.id)) * 100 END AS total';
	}

	public function getWhere(): string
	{
		return '';
	}

	public function getGroupBy(): string
	{
		return 'GROUP BY l.box';
	}

	public function getOrderBy(): string
	{
		$orderby = 'total desc';
		$options = $this->metric->getOptions();
		
		if (isset($options['orderby']))
		{
			$orderby = $options['orderby'];
		}

		return 'ORDER BY ' . $orderby;
	}

	public function getHaving(): string
	{
		return 'HAVING total > 0';
	}
}
