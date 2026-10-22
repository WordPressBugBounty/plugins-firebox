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

namespace FPFramework\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

/**
 * Where a cookie belongs.
 *
 * Cookies are keyed by name, domain and path, and WordPress already knows what
 * this site's share of those should be: COOKIEPATH is the path portion of the
 * site's home URL, COOKIE_DOMAIN is set by sites that share cookies across
 * hosts. Writing to '/' with no domain — which is what this framework did —
 * means a site with COOKIE_DOMAIN set cannot read back what it wrote, and the
 * sites of a subdirectory network all write over each other.
 */
class CookieScope
{
	/**
	 * The path cookies are written to.
	 *
	 * @return  string
	 */
	public static function path()
	{
		return defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
	}

	/**
	 * The domain cookies are written to, empty when the site has not set one.
	 *
	 * @return  string
	 */
	public static function domain()
	{
		return defined('COOKIE_DOMAIN') && COOKIE_DOMAIN ? COOKIE_DOMAIN : '';
	}

	/**
	 * Scopes a cookie name to the current site.
	 *
	 * Path alone is not enough on a subdirectory network: the main site's path
	 * is '/', which matches every subsite's path too, so both cookies are sent
	 * and the one PHP keeps is the shorter path's. Only the name is
	 * unambiguous.
	 *
	 * Single sites, and the main site of a network, keep the bare name they
	 * have always used — an upgrade must not reset anyone's session.
	 *
	 * @param   string  $name
	 *
	 * @return  string
	 */
	public static function name($name)
	{
		if (!is_multisite() || is_main_site())
		{
			return $name;
		}

		return 'blog' . get_current_blog_id() . '_' . $name;
	}

	/**
	 * Every path a cookie of this name may have been written to before.
	 *
	 * Expiring only the current path would leave behind the copy written to
	 * '/' before this framework honoured COOKIEPATH — still sent, still read.
	 *
	 * @return  array
	 */
	public static function pathsToClear()
	{
		return array_values(array_unique([self::path(), '/']));
	}

	/**
	 * The paths a cookie of ours was written to before, minus the current one.
	 *
	 * A cookie is keyed by name, domain *and* path, so moving from '/' to
	 * COOKIEPATH does not replace the old cookie — it adds a second one beside
	 * it. Both are then sent on every request, and because RFC 6265 orders the
	 * more specific path first, the stale '/' copy is the one PHP parses last
	 * and keeps. Left alone, the value written here would never be read back
	 * again. Writers must expire these before writing the real cookie.
	 *
	 * @return  array
	 */
	public static function stalePaths()
	{
		return array_values(array_diff(self::pathsToClear(), [self::path()]));
	}

	/**
	 * The cookie options every writer should start from.
	 *
	 * @param   array  $options  Options to merge over the defaults.
	 *
	 * @return  array
	 */
	public static function options($options = [])
	{
		$defaults = [
			'path'   => self::path(),
			'domain' => self::domain()
		];

		return array_merge($defaults, is_array($options) ? $options : []);
	}
}
