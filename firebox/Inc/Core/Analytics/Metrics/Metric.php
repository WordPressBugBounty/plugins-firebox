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

namespace FireBox\Core\Analytics\Metrics;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

abstract class Metric
{
	protected $start_date = null;
	protected $end_date = null;
	protected $filters = [];
	protected $offset = null;
	protected $limit = null;
	protected $type = 'list';
	protected $query_placeholders = [];
	protected $sql_filters = '';
	protected $wpdb;
	protected $options = [];
	protected $is_single_day = false;
	protected $table_logs;
	protected $table_details;
	
	// Simple timezone offset cache for request duration
	protected static $timezone_offset_cache = null;
	
	// Query strategy pattern support
	protected $queryStrategy = null;
	
	/**
	 * @param string $start_date
	 * @param string $end_date  
	 * @param string $type Can be "list" or "count"
	 * @param array $options
	 */
	public function __construct($start_date = null, $end_date = null, $type = 'list', $options = [])
	{
		global $wpdb;
		$this->wpdb = $wpdb;
		
		// Initialize table names
		$this->table_logs = $wpdb->prefix . 'firebox_logs';
		$this->table_details = $wpdb->prefix . 'firebox_logs_details';
		
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		$this->type = $type;
		$this->query_placeholders = [$this->start_date, $this->end_date];
		$this->options = $options;
		$this->is_single_day = \FireBox\Core\Analytics\Helpers\Date::isSingleDay($this->start_date, $this->end_date);
	}

	protected function applyFilters()
	{
		$filter_builder = new \FireBox\Core\Analytics\Filters\FilterBuilder($this->query_placeholders);
		$result = $filter_builder->build($this->filters);
		
		$this->sql_filters = $result['sql'];
		$this->query_placeholders = $result['placeholders'];
	}

	abstract public function getData();

	public function onAfterGetData(&$data = [])
	{
		if (empty($data)) {
			return;
		}
		
		// Use transformer system for data processing
		\FireBox\Core\Analytics\Transformers\TransformerRegistry::transformData(
			$data,
			$this->type,
			[
				'is_single_day' => $this->isSingleDay(),
				'options' => $this->options
			]
		);
	}

	/**
	 * Get timezone offset string for SQL CONVERT_TZ function (cached)
	 * Returns offset in format '+05:30' or '-08:00'
	 * 
	 * @return string Timezone offset string
	 */
	protected function getTimezoneOffsetString()
	{
		// Cache timezone offset since it doesn't change during request
		if (self::$timezone_offset_cache === null) {
			$timezone_offset = get_option('gmt_offset');
			$hours = (int) $timezone_offset;
			$minutes = abs(($timezone_offset - (int) $timezone_offset) * 60);
			self::$timezone_offset_cache = sprintf('%+03d:%02d', $hours, $minutes);
		}
		
		return self::$timezone_offset_cache;
	}

	public function filters(array $filters)
	{
		$this->filters = $filters;
		return $this;
	}

	public function offset(int $offset)
	{
		$this->offset = $offset;
		return $this;
	}

	public function limit(int $limit)
	{
		$this->limit = $limit;
		return $this;
	}

	public function isSingleDay()
	{
		return $this->is_single_day;
	}
	
	/**
	 * Accessor methods for query strategies
	 */
	public function getWpdb()
	{
		return $this->wpdb;
	}
	
	public function getOptions()
	{
		return array_merge($this->options, [
			'start_date' => $this->start_date,
			'end_date' => $this->end_date,
			'limit' => $this->limit,
			'offset' => $this->offset,
			'type' => $this->type,
			'is_single_day' => $this->is_single_day
		]);
	}
	
	public function getFilters()
	{
		return $this->filters;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function getTableLogs()
	{
		return $this->table_logs;
	}
	
	public function getTableDetails()
	{
		return $this->table_details;
	}

	/**
	 * Smart timezone-aware SQL date function wrapper
	 * Automatically applies timezone conversion to any SQL date function
	 * 
	 * @param string $function SQL date function (e.g., 'DAYNAME', 'DATE_FORMAT', 'YEAR', 'MONTH')
	 * @param string $date_column Database date column (e.g., 'bld.date', 'l.date')
	 * @param string $format_args Additional format arguments for functions like DATE_FORMAT
	 * @return string Complete SQL expression with timezone conversion
	 */
	public function getTimezoneDateSQL($function, $date_column, $format_args = '')
	{
		$offset_string = $this->getTimezoneOffsetString();
		$timezone_converted_date = "CONVERT_TZ($date_column, '+00:00', '$offset_string')";
		
		if (!empty($format_args))
		{
			return "$function($timezone_converted_date, $format_args)";
		}
		
		return "$function($timezone_converted_date)";
	}
	
	/**
	 * Execute query with optimized result processing
	 */
	protected function executeQuery(string $sql)
	{
		if ($this->type === 'count') {
			$column_value = $this->wpdb->get_col($sql); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			return array_sum(array_map('intval', $column_value));
		}
		
		return $this->wpdb->get_results($sql); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}
	
	/**
	 * Get the query strategy instance (lazy initialization)
	 * Can be overridden by subclasses to provide custom strategy factories
	 */
	protected function getQueryStrategy()
	{
		if ($this->queryStrategy === null) {
			$this->queryStrategy = $this->createQueryStrategy();
		}
		return $this->queryStrategy;
	}
	
	/**
	 * Create the appropriate query strategy for this metric type
	 * Must be implemented by subclasses that use the strategy pattern
	 */
	protected function createQueryStrategy()
	{
		// Default implementation returns null - subclasses should override if they use strategies
		return null;
	}
}