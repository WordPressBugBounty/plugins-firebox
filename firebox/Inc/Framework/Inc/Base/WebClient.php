<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.7
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Helpers\Vendors\Mobile_Detect;

class WebClient
{
	/**
	 *  WordPress Application Client
	 *
	 *  @var  object
	 */
	public static $client;

	/**
	 *  Per-request memoized results of getDeviceType()/getOS()/getBrowser(), keyed by UA string.
	 *
	 *  @var  array
	 */
	private static $resultCache = array();

	/**
	 *  Memoized constants from JApplicationWebClient, keyed by class name.
	 *
	 *  @var  array
	 */
	private static $constantsCache = array();

	/**
	 *  Get visitor's Device Type
	 *
	 *  @param	 string	   $ua User Agent string, if null use the implicit one from the server's enviroment
	 *
	 *  @return  string    The client's device type. Can be: tablet, mobile, desktop
	 */
	public static function getDeviceType($ua = null)
	{
		$cacheKey = 'deviceType_' . ($ua === null ? '' : $ua);

		if (isset(self::$resultCache[$cacheKey]))
		{
			return self::$resultCache[$cacheKey];
		}

        $detect = new Mobile_Detect(null, $ua);

        return (self::$resultCache[$cacheKey] = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'mobile') : 'desktop'));
	}

	/**
	 *  Get visitor's Operating System
	 *
	 *  @param	 string	    $ua User Agent string, if null use the implicit one from the server's enviroment
	 *
	 *  @return  string     Possible values: any of JApplicationWebClient's OS constants (except 'iphone' and 'ipad'),
     *                                       'ios', 'chromeos'
	 */
	public static function getOS($ua = null)
	{
		$cacheKey = 'os_' . ($ua === null ? '' : $ua);

		if (isset(self::$resultCache[$cacheKey]))
		{
			return self::$resultCache[$cacheKey];
		}

        // detect iOS and CromeOS (not handled by JApplicationWebClient)
        $client = self::getClient($ua);
        $userAgent = $client->userAgent;

		if (!$userAgent)
		{
			return;
		}

        $ios_regex = '/iPhone|iPad|iPod/i';
        if (preg_match($ios_regex, $userAgent))
        {
            return (self::$resultCache[$cacheKey] = 'ios');
        }

        $chromeos_regex = '/CrOS/i';
        if (preg_match($chromeos_regex, $userAgent))
        {
            return (self::$resultCache[$cacheKey] = 'chromeos');
        }

        // use JApplicationWebClient for OS detection
		$platformInt = $client->platform;
		$constants   = self::getClientConstants();

		if (isset($constants[$platformInt]))
		{
			return (self::$resultCache[$cacheKey] = strtolower($constants[$platformInt]));
		}
	}

	/**
	 *  Get visitor's Browser name / version
	 *
	 *  @param	 string	   $ua User Agent string, if null use the implicit one from the server's enviroment
	 *
	 *  @return  array
	 */
	public static function getBrowser($ua = null)
	{
		$cacheKey = 'browser_' . ($ua === null ? '' : $ua);

		if (isset(self::$resultCache[$cacheKey]))
		{
			return self::$resultCache[$cacheKey];
		}

		$client     = self::getClient($ua);
		$browserInt = $client->browser;
		$constants  = self::getClientConstants();

		if (isset($constants[$browserInt]))
		{
			return (self::$resultCache[$cacheKey] = array(
				'name'    => strtolower($constants[$browserInt]),
				'version' => $client->browserVersion
			));
		}
	}

	/**
	 *  Get the constants from JApplicationWebClient as an array using the Reflection API
	 *
	 *  @return  array
	 */
	private static function getClientConstants()
	{
		$class = '\\FPFramework\\Helpers\\Vendors\\WebClient';

		if (isset(self::$constantsCache[$class]))
		{
			return self::$constantsCache[$class];
		}

		$r = new \ReflectionClass($class);
		$constantsArray = $r->getConstants();

		// flip the associative array
		return (self::$constantsCache[$class] = array_flip($constantsArray));
	}

	/**
	 *  Get the Application Client helper
	 * 
	 *  @param	 string	   $ua User Agent string, if null use the implicit one from the server's enviroment
	 *
	 *  @return  object
	 */
	public static function getClient($ua = null)
	{
		if (is_object(self::$client) && $ua == null)
		{
			return self::$client;
		}

		return (self::$client = new \FPFramework\Helpers\Vendors\WebClient($ua));
	}
}