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

use FireBox\Core\Analytics\QueryBuilders\BaseQueryStrategy;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

abstract class BaseViewsQueryStrategy extends BaseQueryStrategy
{
	/**
	 * Views uses logs table as primary date column
	 */
	protected function getDateColumn(): string
	{
		return 'l.date';
	}
	
	/**
	 * Views-specific FROM/JOIN logic
	 * Uses logs table as primary with optional joins to logs_details
	 */
	protected function getDefaultFromAndJoins(): string
	{
		$filters = $this->metric->getFilters();
		
		// Check if we need JOIN to logs_details table
		$needs_join = (
			(array_key_exists('event', $filters) && 
			 isset($filters['event']['value']) && 
			 is_array($filters['event']['value']) && 
			 count($filters['event']['value'])) || 
			$this->metric->getType() === 'events'
		);
		
		if ($needs_join) {
			return "{$this->metric->getTableLogs()} as l
				LEFT JOIN {$this->metric->getTableDetails()} as ld ON ld.log_id = l.id";
		}
		
		return "{$this->metric->getTableLogs()} as l";
	}
	
	/**
	 * Views metrics use 'ld' alias for details table
	 */
	protected function getTableAliases(): array
	{
		return [
			'logs' => 'l',
			'details' => 'ld'
		];
	}
}
