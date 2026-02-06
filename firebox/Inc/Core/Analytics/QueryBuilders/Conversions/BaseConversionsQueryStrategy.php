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

namespace FireBox\Core\Analytics\QueryBuilders\Conversions;

use FireBox\Core\Analytics\QueryBuilders\BaseQueryStrategy;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

abstract class BaseConversionsQueryStrategy extends BaseQueryStrategy
{
	/**
	 * Conversions uses logs_details table as primary date column
	 */
	protected function getDateColumn(): string
	{
		return 'bld.date';
	}
	
	/**
	 * Conversions-specific FROM/JOIN logic
	 * Uses logs_details as primary table with JOIN to logs
	 */
	protected function getDefaultFromAndJoins(): string
	{
		return "{$this->metric->getTableDetails()} as bld
			LEFT JOIN {$this->metric->getTableLogs()} as bl ON bld.log_id = bl.id";
	}
	
	/**
	 * Conversions metrics use 'bl' alias for logs table
	 */
	protected function getTableAliases(): array
	{
		return [
			'logs' => 'bl',
			'details' => 'bld'
		];
	}
}
