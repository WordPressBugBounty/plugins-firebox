<?php
/**
 * @package         FireBox
 * @version         3.1.11 Free
 *
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

/**
 * Per-client throttle for the public endpoints.
 *
 * The public endpoints are protected only by a nonce that is printed into every page a
 * campaign renders on, is identical for all logged-out visitors and — because those pages
 * are usually cached — has to stay valid for hours. It is a CSRF token, not a rate limit.
 */
class RateLimit
{
	/**
	 * Prefix for the transients backing each bucket.
	 *
	 * @var  string
	 */
	const TRANSIENT_PREFIX = 'fbox_rl_';

	/**
	 * Records a hit and returns whether the caller is still within its allowance.
	 *
	 * @param   string  $bucket     Identifies the action being limited.
	 * @param   int     $limit      Hits permitted per window.
	 * @param   int     $window     Window length in seconds.
	 * @param   bool    $fail_open  What to do when the caller cannot be identified. See getClientIdentifier().
	 *
	 * @return  bool  True while under the limit, false once it is exceeded.
	 */
	public static function attempt($bucket = '', $limit = 10, $window = MINUTE_IN_SECONDS, $fail_open = true)
	{
		if (self::isLimited($bucket, $limit, $fail_open))
		{
			return false;
		}

		self::hit($bucket, $window, $fail_open);

		return true;
	}

	/**
	 * Returns whether the caller has exhausted its allowance, without recording a hit.
	 *
	 * @param   string  $bucket     Identifies the action being limited.
	 * @param   int     $limit      Hits permitted per window.
	 * @param   bool    $fail_open  What to do when the caller cannot be identified. See getClientIdentifier().
	 *
	 * @return  bool
	 */
	public static function isLimited($bucket = '', $limit = 10, $fail_open = true)
	{
		/**
		 * Allows a site to tune or switch off throttling for an endpoint.
		 *
		 * Returning 0 (or a negative limit) disables the limit for that bucket.
		 *
		 * @param  int     $limit
		 * @param  string  $bucket
		 */
		$limit = (int) apply_filters('firebox/rate_limit/limit', $limit, $bucket);

		if ($limit <= 0)
		{
			return false;
		}

		$key = self::getKey($bucket, $fail_open);

		if ($key === '')
		{
			// No way to identify the caller, so no way to limit them fairly.
			return false;
		}

		return self::readCount($key) >= $limit;
	}

	/**
	 * Returns the hits recorded in the window that is currently open.
	 *
	 * A window that has already elapsed counts as zero: the stored value is only cleared
	 * lazily, and a value written by an older version has a shape we no longer read.
	 *
	 * @param   string  $key
	 *
	 * @return  int
	 */
	private static function readCount($key)
	{
		$stored = get_transient($key);

		if (!is_array($stored) || !isset($stored['count'], $stored['reset']))
		{
			return 0;
		}

		return (int) $stored['reset'] > time() ? (int) $stored['count'] : 0;
	}

	/**
	 * Records a hit against the caller's bucket.
	 *
	 * The window is fixed: it opens on the first hit and the count resets once it
	 * elapses. The expiry is deliberately not pushed forward on later hits — doing so
	 * would mean the count never resets for a bucket that sees steady traffic, and a
	 * shared address (an office, a mobile carrier, a proxy) would creep to the limit
	 * over many minutes while staying well under the intended rate.
	 *
	 * Note the get/set pair is not atomic, so concurrent requests can slightly
	 * exceed the limit. The limiter fails open by design; it is a brake, not a lock.
	 *
	 * @param   string  $bucket     Identifies the action being limited.
	 * @param   int     $window     Window length in seconds.
	 * @param   bool    $fail_open  What to do when the caller cannot be identified. See getClientIdentifier().
	 *
	 * @return  void
	 */
	public static function hit($bucket = '', $window = MINUTE_IN_SECONDS, $fail_open = true)
	{
		$window = (int) apply_filters('firebox/rate_limit/window', $window, $bucket);
		$window = $window > 0 ? $window : MINUTE_IN_SECONDS;

		$key = self::getKey($bucket, $fail_open);

		if ($key === '')
		{
			return;
		}

		$now = time();
		$stored = get_transient($key);

		if (!is_array($stored) || !isset($stored['count'], $stored['reset']) || (int) $stored['reset'] <= $now)
		{
			// No window open (or the last one elapsed), so this hit opens a new one.
			$stored = ['count' => 0, 'reset' => $now + $window];
		}

		$stored['count'] = (int) $stored['count'] + 1;

		set_transient($key, $stored, max(1, (int) $stored['reset'] - $now));
	}

	/**
	 * Returns the transient key for a bucket and the current client.
	 *
	 * @param   string  $bucket
	 * @param   bool    $fail_open
	 *
	 * @return  string
	 */
	private static function getKey($bucket = '', $fail_open = true)
	{
		$client = self::getClientIdentifier($fail_open);

		if ($client === '')
		{
			return '';
		}

		return self::TRANSIENT_PREFIX . md5($bucket . '|' . $client);
	}

	/**
	 * Identifies the current client.
	 *
	 * Logged-in users are identified by ID. Everyone else is identified by REMOTE_ADDR.
	 *
	 * Forwarded headers (X-Forwarded-For, CF-Connecting-IP and friends) are deliberately
	 * not consulted. They are attacker-controlled wherever the origin can be reached
	 * directly, and trusting them does not merely let an attacker slip their own limit:
	 * it lets them choose *whose* bucket to fill. Sending a burst under a victim's
	 * address would lock that visitor out of a form they are entitled to use. A site
	 * behind a proxy it trusts can supply the real address through the filter below.
	 *
	 * A private or loopback REMOTE_ADDR means a proxy did not pass the visitor's address
	 * on, so every visitor arrives looking like the same client. That leaves no honest
	 * way to tell them apart, and $fail_open decides what to do about it:
	 *
	 *   - true  — do not limit at all. Right for the public brakes, where sharing one
	 *             bucket would cap the whole site instead of one visitor.
	 *   - false — put everyone in one bucket. Right where the limiter guards a secret
	 *             rather than a resource, and letting every caller through unthrottled
	 *             would remove the protection entirely.
	 *
	 * @param   bool  $fail_open
	 *
	 * @return  string
	 */
	private static function getClientIdentifier($fail_open = true)
	{
		if ($user_id = get_current_user_id())
		{
			return 'user:' . $user_id;
		}

		$ip = isset($_SERVER['REMOTE_ADDR'])
			? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']))
			: '';

		if ($ip !== '' && !self::isRoutable($ip))
		{
			$ip = '';
		}

		/**
		 * Lets a site behind a trusted reverse proxy supply the real client IP.
		 *
		 * Hook this only where the proxy is guaranteed to overwrite the header it reads
		 * — otherwise any client can forge the value, both to slip its own limit and to
		 * fill another visitor's. Returning an empty string leaves the caller
		 * unidentified, which $fail_open then resolves.
		 *
		 * @param  string  $ip
		 */
		$ip = (string) apply_filters('firebox/rate_limit/client_ip', $ip);

		if ($ip !== '')
		{
			return 'ip:' . $ip;
		}

		return $fail_open ? '' : 'shared';
	}

	/**
	 * Returns whether an address could belong to a visitor out on the internet.
	 *
	 * @param   string  $ip
	 *
	 * @return  bool
	 */
	private static function isRoutable($ip)
	{
		return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}
}
