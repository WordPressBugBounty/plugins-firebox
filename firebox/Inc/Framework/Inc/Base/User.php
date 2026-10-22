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

class User
{
	/**
     * Per-request memoized result of get() for the current active user (no field/value given),
     * keyed by the logged-in user ID (0 for guests) so it stays correct if the current user
     * changes within the same PHP process (e.g. wp_set_current_user() in tests/WP-CLI/cron).
     *
     * @var  array
     */
    private static $currentUserCache = [];

	/**
     * Return the user object
     *
     * @param   mixed  $value  The user identifier
     * @param   mixed  $field  The field to search for (id | ID | slug | email | login)
     *
     * @return  mixed  object  on success, null on failure
     */
    public static function get($value = null, $field = 'ID')
    {
        // Return current active user
		if (empty($field) || empty($value))
		{
			$current_user_id = get_current_user_id();

			if (array_key_exists($current_user_id, self::$currentUserCache))
			{
				return self::$currentUserCache[$current_user_id];
			}

			if (!is_user_logged_in())
			{
				return (self::$currentUserCache[$current_user_id] = new \WP_User);
			}

			$field = 'ID';
			$value = $current_user_id;

			if (!self::exists($value))
			{
				return (self::$currentUserCache[$current_user_id] = null);
			}

			return (self::$currentUserCache[$current_user_id] = get_user_by($field, $value));
        }

        if (!self::exists($value))
        {
            return;
        }

        return get_user_by($field, $value);
    }

    /**
     * Checks whether the user does exist in the database
     *
     * @param   integer  $value  The user identifier
     *
     * @return  bool
     */
    public static function exists($value)
    {
        $hash = 'fpf_user' . $value;

		// check cache
		if ($user = wp_cache_get($hash))
		{
			return $user;
        }

        $user = get_user_by('ID', $value);

		// set cache
		wp_cache_set($hash, $user);
		
		return $user;
    }

    /**
     * Get the IP address of the user.
     *
     * Forwarded headers (X-Forwarded-For, Client-IP) are visitor-controlled, so they are
     * only honoured when REMOTE_ADDR is a proxy the site has declared trusted through the
     * `fpframework/trusted_proxies` filter. Otherwise REMOTE_ADDR is the answer.
     *
     * @return string
     */
    public static function getIP()
    {
        $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? self::validIP(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';

        $trusted_proxies = apply_filters('fpframework/trusted_proxies', []);
        $trusted_proxies = is_array($trusted_proxies) ? $trusted_proxies : [];

        /**
         * A private or loopback connection address can only be a proxy on the site's own
         * network (nginx in front of Apache, Docker, an internal load balancer), so its
         * forwarded headers are honoured without configuration. Public addresses are only
         * trusted when the site lists them.
         */
        $behind_proxy = $remote_addr !== '' && (!self::isRoutable($remote_addr) || in_array($remote_addr, $trusted_proxies, true));

        if (!$behind_proxy)
        {
            return $remote_addr;
        }

        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP'] as $header)
        {
            if (empty($_SERVER[$header]))
            {
                continue;
            }

            // The proxy appends the address it saw last; earlier entries are whatever the client sent.
            $hops = array_reverse(array_map('trim', explode(',', wp_unslash($_SERVER[$header]))));

            foreach ($hops as $hop)
            {
                if (in_array($hop, $trusted_proxies, true) || ($hop !== '' && !self::isRoutable($hop)))
                {
                    continue;
                }

                if ($ip = self::validIP($hop))
                {
                    return $ip;
                }
            }
        }

        return $remote_addr;
    }

    /**
     * Returns the value when it is a syntactically valid IPv4/IPv6 address, otherwise ''.
     *
     * @param   string  $value
     *
     * @return  string
     */
    /**
     * Whether the address is public (not private, loopback or otherwise reserved).
     *
     * @param   string  $ip
     *
     * @return  bool
     */
    private static function isRoutable($ip)
    {
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    private static function validIP($value)
    {
        $value = is_string($value) ? trim($value) : '';

        return filter_var($value, FILTER_VALIDATE_IP) ? $value : '';
    }
}