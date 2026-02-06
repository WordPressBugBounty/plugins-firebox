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

namespace FireBox\Core\Analytics\QueryBuilders\Interfaces;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

interface QueryStrategyInterface
{
	/**
	 * Get the SELECT clause for the query
	 * 
	 * @return string The SELECT part of the SQL query
	 */
	public function getSelect(): string;
	
	/**
	 * Get the GROUP BY clause for the query
	 * 
	 * @return string The GROUP BY part of the SQL query (including "GROUP BY" keyword if needed)
	 */
	public function getGroupBy(): string;
	
	/**
	 * Get the ORDER BY clause for the query
	 * 
	 * @return string The ORDER BY part of the SQL query (including "ORDER BY" keyword if needed)
	 */
	public function getOrderBy(): string;
	
	/**
	 * Get the WHERE clause conditions for the query
	 * 
	 * @return string Additional WHERE conditions (typically starting with "AND")
	 */
	public function getWhere(): string;
	
	/**
	 * Get the HAVING clause for the query
	 * 
	 * @return string The HAVING part of the SQL query (including "HAVING" keyword if needed)
	 */
	public function getHaving(): string;
	
	/**
	 * Get the FROM/JOIN clauses for the query
	 * 
	 * @return string The FROM and JOIN parts of the SQL query
	 */
	public function getFromAndJoins(): string;
	
	/**
	 * Get the WHERE period clause for the query
	 * 
	 * @return string The date/time range WHERE conditions for filtering by period
	 */
	public function getWherePeriod(): string;
	
	/**
	 * Get the filters clause for the query
	 * 
	 * @return string Additional WHERE conditions based on applied filters
	 */
	public function getFilters(): string;
	
	/**
	 * Get the LIMIT/OFFSET clause for the query
	 * 
	 * @return string The LIMIT and OFFSET parts for pagination
	 */
	public function getLimitOffset(): string;
}
