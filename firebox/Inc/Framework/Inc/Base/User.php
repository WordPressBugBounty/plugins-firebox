<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.3
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
     * Get the IP address of the user
     *
     * @return string
     */
    public static function getIP()
    {
        // Whether ip is from the share internet  
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {  
            return sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        }  

        //whether ip is from the proxy  
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {  
            return sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        }  

        return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    }
}