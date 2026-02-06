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

namespace FireBox\Core\Analytics;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Data
{
	private $start_date = null;
	private $end_date = null;
	private $metrics = [];
	private $filters = [];
	private $offset = null;
	private $limit = null;
	protected $options = [];
	
	public function __construct($start_date = '', $end_date = '', $options = [])
	{
		Helpers\Date::transformStartEndDateToUTC($start_date, $end_date);
		
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		$this->options = $options;
	}

	/**
	 * Fluent interface for building analytics queries
	 * 
	 * @param array $metrics Array of metric slugs
	 * @return self
	 */
	public function metrics(array $metrics)
	{
		$this->metrics = $metrics;
		return $this;
	}

	/**
	 * Fluent interface for adding filters
	 * 
	 * @param array $filters
	 * @return self
	 */
	public function filters(array $filters)
	{
		$this->filters = $filters;
		return $this;
	}

	/**
	 * Fluent interface for pagination
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @return self
	 */
	public function paginate($limit, $offset = 0)
	{
		$this->limit = (int) $limit;
		$this->offset = (int) $offset;
		return $this;
	}

	/**
	 * Fluent interface for setting limit
	 * 
	 * @param int|null $limit
	 * @return self
	 */
	public function limit($limit)
	{
		$this->limit = $limit !== null ? (int) $limit : null;
		return $this;
	}

	/**
	 * Fluent interface for setting offset
	 * 
	 * @param int $offset
	 * @return self
	 */
	public function offset($offset)
	{
		$this->offset = (int) $offset;
		return $this;
	}

	/**
	 * Get analytics data with improved error handling and performance
	 * 
	 * @param string $type
	 * @return array
	 */
	public function getData($type = 'list')
	{
		if (empty($this->metrics)) {
			return [];
		}

		$data = [];
		$metric_configs = $this->buildMetricConfigs($type);

		foreach ($metric_configs as $metric_slug => $config) {
			try {
				$data[$metric_slug] = $this->getMetricData($config);
			} catch (\Exception $e) {
				$data[$metric_slug] = $this->getEmptyMetricData($type);
			}
		}

		return $data;
	}

	/**
	 * Build metric configurations for batch processing
	 * 
	 * @param string $type
	 * @return array
	 */
	private function buildMetricConfigs($type)
	{
		$configs = [];

		foreach ($this->metrics as $metric_slug) {
			$class_name = \FireBox\Core\Analytics\Helpers\Metrics::getClassFromSlug($metric_slug);
			
			if (!$class_name) {
				continue;
			}

			$class_path = '\FireBox\Core\Analytics\Metrics\\' . $class_name;
			
			if (!class_exists($class_path)) {
				continue;
			}

			$configs[$metric_slug] = [
				'class' => $class_path,
				'type' => $type,
				'start_date' => $this->start_date,
				'end_date' => $this->end_date,
				'options' => $this->options,
				'filters' => $this->filters,
				'limit' => $this->limit,
				'offset' => $this->offset
			];
		}

		return $configs;
	}

	/**
	 * Get data for a single metric using dependency injection
	 * 
	 * @param array $config
	 * @return mixed
	 */
	private function getMetricData(array $config)
	{
		$metric = $this->createMetric($config);
		$data = $metric->getData();
		
		// Apply transformations through the registry system
		if (!empty($data)) {
			\FireBox\Core\Analytics\Transformers\TransformerRegistry::transformData(
				$data,
				$config['type'],
				array_merge($config['options'], [
					'is_single_day' => \FireBox\Core\Analytics\Helpers\Date::isSingleDay(
						$config['start_date'],
						$config['end_date']
					),
					'has_timezone_sql_conversion' => method_exists($metric, 'hasTimezoneSQLConversion') ? $metric->hasTimezoneSQLConversion() : false
				])
			);
		}

		return $data;
	}

	/**
	 * Create metric instance with all dependencies injected
	 * 
	 * @param array $config
	 * @return object
	 */
	private function createMetric(array $config)
	{
		$metric = new $config['class'](
			$config['start_date'],
			$config['end_date'],
			$config['type'],
			$config['options']
		);

		// Inject dependencies in one go
		if (!empty($config['filters'])) {
			$metric->filters($config['filters']);
		}

		if (!empty($config['limit'])) {
			$metric->limit($config['limit']);
		}

		if (!empty($config['offset'])) {
			$metric->offset($config['offset']);
		}

		return $metric;
	}

	/**
	 * Get empty data structure for failed metrics
	 * 
	 * @param string $type
	 * @return mixed
	 */
	private function getEmptyMetricData($type)
	{
		return $type === 'count' ? 0 : [];
	}
}