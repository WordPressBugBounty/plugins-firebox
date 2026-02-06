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

namespace FireBox\Core\Analytics\QueryBuilders;

use FireBox\Core\Analytics\QueryBuilders\Interfaces\QueryStrategyInterface;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

abstract class BaseQueryStrategy implements QueryStrategyInterface
{
	protected $metric;

	public function __construct($metric)
	{
		$this->metric = $metric;
	}

	/**
	 * Get the FROM/JOIN clauses for the query
	 * Default implementation - should be overridden by subclasses for specific table structures
	 */
	public function getFromAndJoins(): string
	{
		return $this->getDefaultFromAndJoins();
	}
	
	/**
	 * Get the WHERE period clause for the query
	 * Uses the configured date column and actual date values
	 */
	public function getWherePeriod(): string
	{
		$options = $this->metric->getOptions();
		
		if (!$options['start_date'] ?? null || !$options['end_date'] ?? null)
		{
			return '';
		}

		$dateColumn = $this->getDateColumn();
		$startDate = $options['start_date'];
		$endDate = $options['end_date'];
		
		return " AND {$dateColumn} BETWEEN '{$startDate}' AND '{$endDate}'";
	}
	
	/**
	 * Get the filters clause for the query
	 * Uses the configured table alias for filtering
	 */
	public function getFilters(): string
	{
		$filters = $this->metric->getFilters();
		
		if (empty($filters))
		{
			return '';
		}
		
		// Build filters using the FilterBuilder with correct table aliases
		$table_aliases = $this->getTableAliases();
		$filter_builder = new \FireBox\Core\Analytics\Filters\FilterBuilder([], $table_aliases);
		$result = $filter_builder->build($filters);
		
		return $result['sql'];
	}
	
	/**
	 * Get table aliases for FilterBuilder
	 * Can be overridden by subclasses to provide metric-specific aliases
	 */
	protected function getTableAliases(): array
	{
		// Default aliases for Views/ConversionRate pattern
		return [
			'logs' => 'l',
			'details' => 'bld'
		];
	}
	
	/**
	 * Get the LIMIT/OFFSET clause for the query
	 */
	public function getLimitOffset(): string
	{
		$limit = $this->getLimit();
		$offset = $this->getOffset();
		
		$result = '';
		if ($limit) {
			$result .= $limit;
		}
		if ($offset) {
			$result .= ' ' . $offset;
		}
		
		return $result;
	}
	
	/**
	 * Get the LIMIT clause
	 */
	protected function getLimit(): string
	{
		$limit = $this->metric->getOptions()['limit'] ?? null;
		
		if (!is_scalar($limit))
		{
			return '';
		}

		return "LIMIT {$limit}";
	}

	/**
	 * Get the OFFSET clause
	 */
	protected function getOffset(): string
	{
		$offset = $this->metric->getOptions()['offset'] ?? null;
		
		if (!is_scalar($offset))
		{
			return '';
		}

		return "OFFSET {$offset}";
	}

	/**
	 * Default HAVING implementation - can be overridden
	 */
	public function getHaving(): string
	{
		return '';
	}
	
	/**
	 * Default ORDER BY implementation - can be overridden
	 */
	public function getOrderBy(): string
	{
		$dateColumn = $this->getDateColumn();
		return "ORDER BY {$dateColumn} DESC";
	}
	
	/**
	 * Default WHERE implementation - can be overridden
	 */
	public function getWhere(): string
	{
		return '';
	}
	
	/**
	 * Default GROUP BY implementation - can be overridden
	 */
	public function getGroupBy(): string
	{
		return '';
	}
	
	// Abstract methods that subclasses must implement to define their specific behavior
	
	/**
	 * Get the default FROM/JOIN structure for this metric type
	 * Should be implemented by metric-specific base classes
	 */
	protected abstract function getDefaultFromAndJoins(): string;
	
	/**
	 * Get the date column used for period filtering and ordering
	 * Should be implemented by metric-specific base classes (e.g., 'l.date', 'bld.date')
	 */
	protected abstract function getDateColumn(): string;
	
	/**
	 * Must be implemented by concrete strategy classes
	 */
	public abstract function getSelect(): string;
	
	/**
	 * Template for standard ORDER BY with total DESC fallback and options support
	 */
	protected function getTotalOrderBy(): string
	{
		$options = $this->metric->getOptions();
		
		if (isset($options['orderby']))
		{
			return 'ORDER BY ' . $options['orderby'];
		}
		
		return 'ORDER BY total DESC';
	}
}
