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

class EventsStrategy extends BaseViewsQueryStrategy
{
	public function getSelect(): string
	{
		$filter = '';
		$filters = $this->metric->getFilters();
		
		if (array_key_exists('campaign', $filters) && isset($filters['campaign']['value']) && is_array($filters['campaign']['value']))
		{
			$filter .= 'AND ll.box IN (' . implode(',', array_map('intval', $filters['campaign']['value'])) . ')';
		}
		
		$options = $this->metric->getOptions();
		$start_date = $options['start_date'] ?? '';
		$end_date = $options['end_date'] ?? '';
		
		return '
		IF(ld.event IS NOT NULL, ld.event, \'open\') AS label,
		IF(ld.event IS NOT NULL, COUNT(l.id), (
			SELECT COUNT(ll.id)
			FROM ' . $this->metric->getTableLogs() . ' AS ll
			WHERE (ll.date >= \'' . esc_sql($start_date) . '\'
			AND ll.date <= \'' . esc_sql($end_date) . '\')
			' . $filter . '
		)) AS total
		';
	}
	
	public function getGroupBy(): string
	{
		return 'GROUP BY ld.event';
	}
	
	public function getOrderBy(): string
	{
		return $this->getTotalOrderBy();
	}
}
