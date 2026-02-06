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

use FireBox\Core\Analytics\QueryBuilders\ConversionsQueryStrategyFactory;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Conversions extends Metric
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
				{$strategy->getFromAndJoins()}
			WHERE 
				1
				{$strategy->getWherePeriod()}
				{$strategy->getWhere()}
				{$strategy->getFilters()}
			{$strategy->getGroupBy()}
			{$strategy->getOrderBy()}
			{$strategy->getLimitOffset()}
		";

		return $this->executeQuery($sql);
	}

	/**
	 * Create query strategy for this metric
	 */
	protected function createQueryStrategy()
	{
		return ConversionsQueryStrategyFactory::create($this->type, $this);
	}

	/**
	 * Indicates this class handles timezone conversion in SQL
	 */
	public function hasTimezoneSQLConversion()
	{
		return true;
	}
}