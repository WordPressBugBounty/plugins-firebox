<?php
/**
 * @package         FireBox
 * @version         3.1.15
 *
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\API;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FireBox\Core\Helpers\RateLimit;

/**
 * Shared authentication for the key-protected ("closed") REST routes.
 *
 * These routes return every stored submission, so the key is as sensitive as the data.
 * It is accepted from a request header in preference to the URL path: a secret in a path
 * segment is written to access logs, proxy logs and browser history.
 */
class ApiKeyAuth
{
	/**
	 * Header carrying the key.
	 *
	 * WP_REST_Request::get_header() normalises "X-FireBox-Api-Key" to this form.
	 *
	 * @var  string
	 */
	const HEADER = 'x_firebox_api_key';

	/**
	 * Shortest key we will authenticate against.
	 *
	 * This defaults to 1 — that is, "not blank" — deliberately. Sites have existing keys
	 * of arbitrary length and raising the bar here would silently break their
	 * integrations on upgrade. Brute force is instead made impractical by the failure
	 * throttle below, and guessing is made harder by the constant-time comparison.
	 *
	 * Sites that want a hard floor can raise it via the firebox/api/minimum_key_length
	 * filter; 32 is a sensible value once existing keys have been rotated.
	 *
	 * @var  int
	 */
	const MINIMUM_KEY_LENGTH = 1;

	/**
	 * Authenticates a request against the configured API key.
	 *
	 * @param   \WP_REST_Request  $request
	 *
	 * @return  bool
	 */
	public static function authenticate($request)
	{
		$stored = \FireBox\Core\Helpers\Settings::findSettingsOption('api_key');
		$stored = is_scalar($stored) ? trim((string) $stored) : '';

		if (strlen($stored) < self::getMinimumKeyLength())
		{
			return false;
		}

		$provided = self::extractKey($request);

		if ($provided === '')
		{
			return false;
		}

		/**
		 * Throttle failures so the key cannot be brute-forced. Successful requests are
		 * not counted, so a legitimate integration polling the API is unaffected.
		 *
		 * This throttle is what makes MINIMUM_KEY_LENGTH above safe to leave at 1, so it
		 * fails closed: where a caller cannot be identified every caller shares one
		 * bucket, rather than the limit lifting and the key becoming guessable at will.
		 */
		if (RateLimit::isLimited('api_key_auth', 10, false))
		{
			return false;
		}

		// Constant-time comparison: a plain === leaks how much of the key matched.
		if (!hash_equals($stored, $provided))
		{
			RateLimit::hit('api_key_auth', 5 * MINUTE_IN_SECONDS, false);
			return false;
		}

		return true;
	}

	/**
	 * Reads the key from the request, preferring the header over the URL path.
	 *
	 * @param   \WP_REST_Request  $request
	 *
	 * @return  string
	 */
	private static function extractKey($request)
	{
		if (!is_object($request))
		{
			return '';
		}

		$header = method_exists($request, 'get_header') ? $request->get_header(self::HEADER) : '';
		$header = is_scalar($header) ? trim((string) $header) : '';

		if ($header !== '')
		{
			return $header;
		}

		/**
		 * Deprecated: the key as a URL path segment. Retained so existing integrations
		 * keep working; prefer the header.
		 */
		$param = method_exists($request, 'get_param') ? $request->get_param('api_key') : '';

		return is_scalar($param) ? trim((string) $param) : '';
	}

	/**
	 * Returns the minimum acceptable key length.
	 *
	 * @return  int
	 */
	private static function getMinimumKeyLength()
	{
		/**
		 * Allows a site with a shorter legacy key to keep using it while it rotates.
		 *
		 * @param  int  $length
		 */
		$length = (int) apply_filters('firebox/api/minimum_key_length', self::MINIMUM_KEY_LENGTH);

		// Never allow this to be reduced to nothing.
		return max(1, $length);
	}
}
