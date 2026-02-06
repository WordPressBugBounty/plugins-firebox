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

namespace FireBox\Core\Analytics\Filters;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class FilterBuilder
{
	protected $sql_filters = '';
	protected $query_placeholders = [];
	protected $table_aliases = [];
	
	public function __construct(array $query_placeholders = [], array $table_aliases = [])
	{
		$this->query_placeholders = $query_placeholders;
		
		// Set default table aliases for backward compatibility
		$this->table_aliases = array_merge([
			'logs' => 'l',        // Default to Views/ConversionRate pattern
			'details' => 'bld'    // Default details table alias
		], $table_aliases);
	}
	
	/**
	 * Build SQL filters from filters array
	 * 
	 * @param array $filters
	 * @return array ['sql' => string, 'placeholders' => array]
	 */
	public function build(array $filters): array
	{
		$this->sql_filters = '';
		
		foreach ($filters as $filter_type => $filter_config) {
			$this->applyFilter($filter_type, $filter_config);
		}
		
		return [
			'sql' => $this->sql_filters,
			'placeholders' => $this->query_placeholders
		];
	}
	
	/**
	 * Apply individual filter based on type
	 * 
	 * @param string $filter_type
	 * @param array $filter_config
	 */
	protected function applyFilter(string $filter_type, array $filter_config): void
	{
		switch ($filter_type) {
			case 'campaign':
				$this->applyCampaignFilter($filter_config);
				break;
			case 'country':
				$this->applyCountryFilter($filter_config);
				break;
			case 'device':
				$this->applyDeviceFilter($filter_config);
				break;
			case 'event':
				$this->applyEventFilter($filter_config);
				break;
			case 'page':
				$this->applyPageFilter($filter_config);
				break;
			case 'referrer':
				$this->applyReferrerFilter($filter_config);
				break;
		}
	}
	
	/**
	 * Apply campaign filter
	 */
	protected function applyCampaignFilter(array $config): void
	{
		if (isset($config['value']) && is_array($config['value'])) {
			$logs_alias = $this->table_aliases['logs'];
			$this->sql_filters .= "AND {$logs_alias}.box IN (" . implode(',', array_map('intval', $config['value'])) . ')';
		}
	}
	
	/**
	 * Apply country filter
	 */
	protected function applyCountryFilter(array $config): void
	{
		if (isset($config['value']) && is_array($config['value'])) {
			$placeholders = array_fill(0, count($config['value']), '%s');
			$logs_alias = $this->table_aliases['logs'];
			$this->sql_filters .= "AND {$logs_alias}.country IN (" . implode(',', $placeholders) . ')';
			$this->query_placeholders = array_merge($this->query_placeholders, $config['value']);
		}
	}
	
	/**
	 * Apply device filter
	 */
	protected function applyDeviceFilter(array $config): void
	{
		if (isset($config['value']) && is_array($config['value'])) {
			$placeholders = array_fill(0, count($config['value']), '%s');
			$logs_alias = $this->table_aliases['logs'];
			$this->sql_filters .= "AND {$logs_alias}.device IN (" . implode(',', $placeholders) . ')';
			$this->query_placeholders = array_merge($this->query_placeholders, $config['value']);
		}
	}
	
	/**
	 * Apply event filter
	 */
	protected function applyEventFilter(array $config): void
	{
		if (isset($config['value']) && is_array($config['value'])) {
			$placeholders = array_fill(0, count($config['value']), '%s');
			$details_alias = $this->table_aliases['details'];
			$this->sql_filters .= "AND {$details_alias}.event IN (" . implode(',', $placeholders) . ')';
			$this->query_placeholders = array_merge($this->query_placeholders, $config['value']);
		}
	}
	
	/**
	 * Apply page filter with different types
	 */
	protected function applyPageFilter(array $config): void
	{
		if (!isset($config['value']) || !is_array($config['value']) || !isset($config['type'])) {
			return;
		}
		
		$allowed_types = ['contains', 'not_contains', 'equals'];
		if (!in_array($config['type'], $allowed_types, true)) {
			return;
		}
		
		foreach ($config['value'] as $page) {
			$logs_alias = $this->table_aliases['logs'];
			switch ($config['type']) {
				case 'contains':
					$this->sql_filters .= "AND {$logs_alias}.page LIKE %s";
					$this->query_placeholders[] = '%' . $page . '%';
					break;
				case 'not_contains':
					$this->sql_filters .= "AND {$logs_alias}.page NOT LIKE %s";
					$this->query_placeholders[] = '%' . $page . '%';
					break;
				case 'equals':
					$this->sql_filters .= "AND {$logs_alias}.page = %s";
					$this->query_placeholders[] = $page;
					break;
			}
		}
	}
	
	/**
	 * Apply referrer filter with different types
	 */
	protected function applyReferrerFilter(array $config): void
	{
		if (!isset($config['value']) || !is_array($config['value']) || !isset($config['type'])) {
			return;
		}
		
		$allowed_types = ['contains', 'not_contains', 'equals'];
		if (!in_array($config['type'], $allowed_types, true)) {
			return;
		}
		
		foreach ($config['value'] as $referrer) {
			$logs_alias = $this->table_aliases['logs'];
			switch ($config['type']) {
				case 'contains':
					$this->sql_filters .= "AND {$logs_alias}.referrer LIKE %s";
					$this->query_placeholders[] = '%' . $referrer . '%';
					break;
				case 'not_contains':
					$this->sql_filters .= "AND {$logs_alias}.referrer NOT LIKE %s";
					$this->query_placeholders[] = '%' . $referrer . '%';
					break;
				case 'equals':
					$this->sql_filters .= "AND {$logs_alias}.referrer = %s";
					$this->query_placeholders[] = $referrer;
					break;
			}
		}
	}
}
