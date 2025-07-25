<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.133
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Functions
{
    /**
     * Applies the site's or the user's timezone to a given date.
     * 
     * @param   string  $date
     * @param   string  $format
     * 
     * @return  string
     */
    public static function applySiteTimezoneToUTCDate($date, $format = 'Y-m-d H:i:s')
    {
        // Get timezone
        $tz = new \DateTimeZone(wp_timezone()->getName());

        // Apply server timezone
        $date = (new \DateTime($date))->setTimezone($tz)->format($format);
        
        return $date;
    }

    /**
     *  Return's a URL with the Google Analytics Campaign Parameters appended to the end
     *
     *  @param   string  $url       The URL             The URL to which UTM parameters will be applied to
     *  @param   string  $campaign  Campaign Name       The campaign name i.e. black friday sale, freeversion
     *  @param   string  $medium    Campaign Medium     How did the visitor find your link? i.e. social, email, cpc, upgradebutton
     *  @param   string  $content   Campaign Content    The call to action that brought the traffic, i.e. buy now, support, read more
     *
     *  @return  string
     */
    public static function getUTMURL($url, $campaign = null, $medium = 'upgradebutton', $content = '')
    {
        if (!$url)
        {
            return;
        }

        if (!$campaign)
        {
            $campaign = 'firebox-' . (FBOX_LICENSE_TYPE === 'lite' ? 'free' : 'pro');
        }

        // Make UTM Campaign filterable
        $campaign = apply_filters('fpframework/utmurl/campaign', $campaign);

        $utm  = 'utm_source=product&utm_campaign=' . $campaign . '&utm_medium=' . $medium;

        if ($content)
        {
            $utm .= '&utm_content=' . $content;
        }
        
        $char = strpos($url, '?') === false ? '?' : '&';

        // Check if the URL has a hashtag and something, add it at the end
        if (strpos($url, '#') !== false)
        {
            $url_parts = explode('#', $url);
            $url = $url_parts[0] . $char . $utm . '#' . $url_parts[1];
        }
        else
        {
            $url .= $char . $utm;
        }

        return $url;
    }
    
    /**
     *  Attempt to convert a subject to array
     *
     *  @param  mixed  $subject
     * 
     *  @return array
     */
    public static function makeArray($subject)
    {
        if (empty($subject))
        {
            return [];
        }

        if (is_object($subject))
        {
            return (array) $subject;
        }

        if (!is_array($subject))
        {
            // replace newlines with commas
            $subject = str_replace(PHP_EOL, ',', $subject);
    
            // split keywords on commas
            $subject = explode(',', $subject);
        }

        // Now that we have an array, run some housekeeping.
        $arr = $subject;
        
        // Trim entries
        $arr = array_map('trim', $subject);

        // Remove empty items. We use a custom callback here because the default behavior of array_filter removes 0 values as well.
        $arr = array_filter($arr, function($value)
        {
            return ($value !== null && $value !== false && $value !== ''); 
        });

        // Unique only items
        $arr = array_unique($arr);

        // Reset keys
        $arr = array_values($arr);

        return $arr;
    }
	
	/**
	 *  Checks if an array of values (needle) exists in a text (haystack)
	 *
	 *  @param   array   $needle            The searched array of values.
	 *  @param   string  $haystack          The text
	 *  @param   bool    $case_insensitive  Indicates whether the letter case plays any role
	 *
	 *  @return  bool
	 */
	public static function strpos_arr($needles, $haystack, $case_insensitive = false)
	{
        $needles = !is_array($needles) ? (array) $needles : $needles;
        $haystack = $case_insensitive ? strtolower($haystack) : $haystack;

		foreach ($needles as $needle)
		{
            $needle = $case_insensitive ? strtolower($needle) : $needle;

			if (strpos($haystack, $needle) !== false) 
			{
				// stop on first true result
				return true; 
			}
		}

		return false;
	}

    /**
     * Tries to fix date based on its syntax. Sets it to null if incorrect.
     * 
     * @param   string  $date
     * 
     * @return  void
     */
    public static function fixDate(&$date)
    {
        if (!$date)
        {
            $date = null;

            return;
        }

        $date = trim($date);
        
        // Check if date has correct syntax: 00-00-00 00:00:00
        if (preg_match('#^[0-9]+-[0-9]+-[0-9]+( [0-9][0-9]:[0-9][0-9]:[0-9][0-9])$#', $date))
        {
            return;
        }
        
        // Check if date has syntax: 00-00-00 00:00
        // If so, add :00 (seconds)
        if (preg_match('#^[0-9]+-[0-9]+-[0-9]+ [0-9][0-9]:[0-9][0-9]$#', $date))
        {
            $date .= ':00';

            return;
        }

        // Check if date has a prepending date syntax: 00-00-00 ...
        // If so, add 00:00:00 (hours:mins;secs)
        if (preg_match('#^([0-9]+-[0-9]+-[0-9]+)#', $date, $match))
        {
            $date = $match[1] . ' 00:00:00';
            
            return;
        }

        // Date format is not correct, so return null
        $date = null;
    }

    /**
     * Change date's timezone to UTC by modyfing the offset
     *
     * @param  string   $date   The date in timezone other than UTC
     * 
     * @return string   The date in UTC
     * 
     * @deprecated Use dateToUTC()
     */
    public static function fixDateOffset(&$date)
    {
        $date = self::dateToUTC($date);
    }

    /**
     * Fixes date by adding offset
     * 
     * @param   string  $date
     * 
     * @return  string
     */
    public static function dateToUTC($date)
    {
        if ($date <= 0)
        {
            $date = 0;

            return;
        }

        return get_gmt_from_date($date, 'Y-m-d H:i:s');
    }

    /**
     * Returns current date
     * 
     * @return  string
     */
    public static function dateTimeNow()
    {
        $factory = new \FPFramework\Base\Factory();
        return $factory->getDate()->format("Y-m-d H:i:s");
    }
    
	/**
	 * Checks whether all array values are equal.
	 * 
	 * @param   array  $array
	 * 
	 * @return  bool
	 */
	public static function allArrayValuesEqual($array)
	{
		if (count($array) !== self::getTotalNonEmptyArrayValues($array))
		{
			return false;
		}

		return count(array_unique($array)) === 1;
	}

	/**
	 * Given a one dimension array, it returns the total non empty values.
	 * 
	 * @param   array  $array
	 * 
	 * @return  int
	 */
	public static function getTotalNonEmptyArrayValues($array)
	{
		$total = 0;

		foreach ($array as $value)
		{
			if (is_array($value))
			{
				continue;
			}
			
			if ($value === '')
			{
				continue;
			}

			$total++;
		}
		
		return $total;
	}
}