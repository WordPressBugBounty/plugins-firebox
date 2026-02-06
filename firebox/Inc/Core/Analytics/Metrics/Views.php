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

use FireBox\Core\Analytics\QueryBuilders\ViewsQueryStrategyFactory;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Views extends Metric
{
	/**
	 * Create the Views-specific query strategy
	 */
	protected function createQueryStrategy()
	{
		return ViewsQueryStrategyFactory::create($this->type, $this);
	}

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
			{$strategy->getHaving()}
			{$strategy->getOrderBy()}
			{$strategy->getLimitOffset()}
		";

		return $this->executeQuery($sql);
	}

	/**
	 * Indicates this class handles timezone conversion in SQL
	 */
	public function hasTimezoneSQLConversion()
	{
		return true;
	}
}