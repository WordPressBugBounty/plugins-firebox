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

namespace FireBox\Core\Analytics\Transformers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class TimezoneTransformer implements TransformerInterface
{
	public function shouldApply($type, $options = [])
	{
		// Skip if metric already handles timezone conversion in SQL
		if (isset($options['has_timezone_sql_conversion']) && $options['has_timezone_sql_conversion']) {
			return false;
		}
		
		$isSingleDay = isset($options['is_single_day']) ? $options['is_single_day'] : false;
		return $isSingleDay || in_array($type, ['popular_view_times']);
	}

	public function transform(&$data, $type, $options = [])
	{
		\FireBox\Core\Analytics\Helpers\Date::fixTimezoneInHourlyData($data);
	}

	public function getPriority()
	{
		return 10; // Run early
	}
}
