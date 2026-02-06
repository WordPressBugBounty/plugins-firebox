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

namespace FireBox\Core\Analytics\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Date
{
	public static function isSingleDay($start_date = null, $end_date = null)
	{
		if (!$start_date || !$end_date)
		{
			return false;
		}
		
		if (!\DateTime::createFromFormat('Y/m/d H:i:s', $start_date) || !\DateTime::createFromFormat('Y/m/d H:i:s', $end_date))
		{
			return false;
		}
		
		$start_date = new \DateTime($start_date);
		$end_date = new \DateTime($end_date);

		$interval = $start_date->diff($end_date);
		$totalDays = (int) $interval->format('%a');

		return $totalDays === 0;
	}

	public static function fixTimezoneInHourlyData($data = [])
	{
		if (!is_array($data))
		{
			return $data;
		}
		
		$utcTimeZone = new \DateTimeZone('UTC');
		$tz = new \DateTimeZone(wp_timezone()->getName());

		foreach ($data as &$item)
		{
			if (!isset($item->label))
			{
				continue;
			}

			if (!$item->label)
			{
				continue;
			}
			
			if (!$dateTime = \DateTime::createFromFormat('H:i', $item->label, $utcTimeZone))
			{
				continue;
			}

			$dateTime->setTimezone($tz);

			$item->label = $dateTime->format('H:i');
		}

		return $data;
	}

	public static function transformStartEndDateToUTC(&$start_date = '', &$end_date = '')
	{
		$utcTimeZone = new \DateTimeZone('UTC');
		$tz = new \DateTimeZone(wp_timezone()->getName());

		// Make start in UTC
		if ($start_date_obj = \DateTime::createFromFormat('Y/m/d H:i:s', $start_date, $tz))
		{
			$start_date_obj->setTimezone($utcTimeZone);
			$start_date = $start_date_obj->format('Y/m/d H:i:s');
		}

		// Make end_date in UTC
		if ($end_date_obj = \DateTime::createFromFormat('Y/m/d H:i:s', $end_date, $tz))
		{
			$end_date_obj->setTimezone($utcTimeZone);
			$end_date = $end_date_obj->format('Y/m/d H:i:s');
		}
	}
}