<?php
/**
 * @package         FireBox
 * @version         3.1.14 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Analytics\Ajax;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

Trait Shared
{
    protected function getPreviousPeriodDates($startDate, $period)
    {
		if (!\DateTime::createFromFormat('Y/m/d 00:00:00', $startDate))
		{
			return false;
		}

        $newStartDate = new \DateTime($startDate);
        $newStartDate->sub(new \DateInterval("P{$period}D"));
        $newStartDateStr = $newStartDate->format('Y/m/d 00:00:00');
        
        $period--;
        $newEndDate = clone $newStartDate;
        $newEndDate->add(new \DateInterval("P{$period}D"));
        $newEndDateStr = $newEndDate->format('Y/m/d 23:59:59');

        return [$newStartDateStr, $newEndDateStr];
    }

    /**
     * Determine the transient TTL to use for cached analytics data.
     * Only fully-past date ranges are cached. Ranges that include today are
     * still being collected, and caching them makes freshly tracked views and
     * conversions appear to be missing from the dashboard.
     *
     * @param   string  $end_date  The end date of the range (e.g. 'Y/m/d H:i:s').
     *
     * @return  int  TTL in seconds, or 0 when the range must not be cached.
     */
    protected function getAnalyticsCacheTtl($end_date)
    {
        $end_date_ts = strtotime($end_date);

        if ($end_date_ts && $end_date_ts < strtotime('today midnight', current_time('timestamp')))
        {
            // Fully-past range: data is no longer being collected for it.
            return HOUR_IN_SECONDS;
        }

        // Range includes today: always serve live data.
        return 0;
    }
}