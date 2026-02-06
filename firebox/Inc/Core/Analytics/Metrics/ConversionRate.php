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

use FireBox\Core\Analytics\QueryBuilders\ConversionRate\ConversionRateQueryStrategyFactory;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class ConversionRate extends Metric
{
	/**
	 * Get data using strategy pattern
	 */
	public function getData()
	{
		$strategy = $this->createQueryStrategy();
		
		$sql = "SELECT
				{$strategy->getSelect()}
			FROM 
				{$this->table_logs} as l
			LEFT JOIN 
				{$this->table_details} as bld ON bld.log_id = l.id AND bld.event = 'conversion'
			WHERE 
				1
				{$strategy->getWherePeriod()}
				{$strategy->getWhere()}
				{$strategy->getFilters()}
			{$strategy->getGroupBy()}
			{$strategy->getHaving()}
			{$strategy->getOrderBy()}
			{$strategy->getLimitOffset()}
		";

		$results = $this->executeQuery($sql);

		if ($this->type === 'count')
		{
			return isset($results[0]->total) ? (float) $results[0]->total : 0;
		}

		return $results;
	}

	/**
	 * Create query strategy for this metric
	 */
	protected function createQueryStrategy()
	{
		return ConversionRateQueryStrategyFactory::create($this->type, $this);
	}

	/**
	 * Override executeQuery to handle conversion rate calculation properly
	 * The base class executeQuery uses get_col() for count type which gets the first column,
	 * but we need the 'total' column which contains the conversion rate percentage
	 */
	protected function executeQuery(string $sql)
	{
		return $this->wpdb->get_results($sql); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	}

	/**
	 * Indicates this class handles timezone conversion in SQL
	 */
	public function hasTimezoneSQLConversion()
	{
		return true;
	}
}