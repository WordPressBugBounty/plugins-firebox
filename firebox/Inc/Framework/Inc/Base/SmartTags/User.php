<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.6
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\SmartTags;

if (!defined('ABSPATH'))
{
    exit; // Exit if accessed directly.
}

class User extends SmartTag
{
    /**
     * Fetch a property from the User object
     *
     * @param   string  $key   The name of the property to return
     *
     * @return  mixed   Null if property is not found, mixed if property is found
     */
    public function fetchValue($key)
    {
        switch ($key) {
            case 'id':
                $key = 'ID';
                break;
        }

        // WP_User::__get falls through to user meta, so only profile fields may be read.
        if (!in_array(strtolower($key), array_map('strtolower', self::getAllowedKeys()), true))
        {
            return;
        }

        $user_id = isset($this->options['user']) ? $this->options['user'] : null;
        $user = $this->factory->getUser($user_id);

        // Make sure the property does exist
        if (is_null($user) || !$user || !isset($user->{$key}))
        {
            return;
        }

        return $user->{$key};
    }

    /**
     * Returns the user's name
     * 
     * @return  string
     */
    public function getName()
    {
        return $this->fetchValue('first_name') . ' ' . $this->fetchValue('last_name');
    }

    /**
     * Returns the user's firstname
     * 
     * @return  string
     */
    public function getFirstname()
    {
        return $this->fetchValue('first_name');
    }

    /**
     * Returns the user's lastname
     * 
     * @return  string
     */
    public function getLastname()
    {
        return $this->fetchValue('last_name');
    }

    /**
     * Returns the user's login
     * 
     * @return  string
     */
    public function getLogin()
    {
        return $this->fetchValue('user_login');
    }

    /**
     * Returns the user's registration date
     * 
     * @return  string
     */
    public function getRegisterDate()
    {
        return $this->fetchValue('user_registered');
    }

    /**
     * Returns the user's email
     * 
     * @return  string
     */
    public function getEmail()
    {
        return $this->fetchValue('user_email');
    }

    /**
     * Profile properties the {user.KEY} tag may expose. Anything else (session tokens,
     * activation keys, plugin meta) stays server-side.
     *
     * @return  array
     */
    public static function getAllowedKeys()
    {
        return apply_filters('fpframework/smarttags/user/allowed_keys', [
            'ID', 'user_login', 'user_nicename', 'user_email', 'user_url', 'user_registered',
            'display_name', 'name', 'first_name', 'last_name', 'firstname', 'lastname',
            'user_firstname', 'user_lastname', 'nickname', 'description', 'user_description',
            'locale', 'roles'
        ]);
    }
}
