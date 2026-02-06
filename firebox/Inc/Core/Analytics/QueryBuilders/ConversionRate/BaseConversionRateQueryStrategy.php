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

namespace FireBox\Core\Analytics\QueryBuilders\ConversionRate;

use FireBox\Core\Analytics\QueryBuilders\BaseQueryStrategy;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

abstract class BaseConversionRateQueryStrategy extends BaseQueryStrategy
{
	/**
	 * ConversionRate uses logs table date column
	 */
	protected function getDateColumn(): string
	{
		return 'l.date';
	}
	
	/**
	 * ConversionRate-specific FROM/JOIN logic
	 * Uses logs as primary table with LEFT JOIN to logs_details
	 */
	protected function getDefaultFromAndJoins(): string
	{
		return "{$this->metric->getTableLogs()} as l
			LEFT JOIN {$this->metric->getTableDetails()} as bld ON l.id = bld.log_id AND bld.event = 'conversion'";
	}
}
