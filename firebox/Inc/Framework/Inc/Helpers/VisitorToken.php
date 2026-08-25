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

namespace FPFramework\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class VisitorToken
{
   /**
     *  Class instance
     *
     *  @var  object
     */
    private static $instance;

	/**
	 *  Cookie Name, before it is scoped to the current site.
	 *
	 *  @var  string
	 */
	private $cookieName = 'fpfid';

	/**
	 *  The cookie name for the current site.
	 *
	 *  Resolved on each call rather than stored: this class is a singleton, so
	 *  a cached name would survive a switch_to_blog() and hand one site's
	 *  visitor token to another.
	 *
	 *  Path alone cannot separate them — the main site of a subdirectory
	 *  network writes to '/', which is also sent to every subsite, and PHP
	 *  keeps the last copy it parses. Only the name is unambiguous.
	 *
	 *  @return  string
	 */
	private function cookieName()
	{
		return \FPFramework\Helpers\CookieScope::name($this->cookieName);
	}

	/**
	 *  Represents the maximum age of the visitor's cookie in seconds.
	 *
	 *  @var  Integer
	 */
	private $expire = 90000000;

    /**
     *  Cookie Object
     *
     *  @var  object
     */
    private $cookie;

    /**
     * Class constructor
     * 
     * @param   object  $cookie
     * 
     * @return  void
     */
    private function __construct($cookie = null)
    {
        if (!$cookie)
        {
            $cookie = new \FPFramework\Libs\Cookie();
        }
        
        $this->cookie = $cookie;

        $token = $this->cookie->get($this->cookieName(), null);

        if ($token === null)
        {
            $this->store($this->create());
        }
    }

    /**
     *  Returns class instance
     *
     *  @return  object
     */
    public static function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     *  Get a visitor's unique token id, if a token isn't set yet one will be generated.
     *
     *  @param   boolean   $forceNew  If true, force a new token to be created
     *  
     *  @return  string    The session token
     */
    public function get($forceNew = false)
    {
        $id = $this->cookie->get($this->cookieName());

        // Tokens are generated via bin2hex(random_bytes(...)), so a legitimate value is
        // hex-only. Strip anything else at the read boundary: the value is concatenated
        // into SQL string literals by callers, and sanitize_text_field() does not remove
        // quotes/SQL metacharacters. This mirrors Factory\Session::getSessionID().
        return is_string($id) ? preg_replace('/[^a-f0-9]/', '', strtolower($id)) : $id;
    }

    /**
     *  Deprecated
     */
    public function getToken($forceNew = false)
    {
        return $this->get($forceNew);
    }

    /**
     *  Create a token-string
     *
     *  @param   integer $length  Length of string
     *
     *  @return  string  Generated token
     */
    private function create($length = 8)
    {
        return bin2hex(random_bytes($length));
    }

    /**
     *  Saves the cookie to the visitor's browser
     *
     *  @param   string  $value  Cookie Value
     *
     *  @return  void
     */
    private function store($value)
    {
        $name = $this->cookieName();

        /**
         * A copy of this cookie left at a path we no longer write to would be
         * parsed after the real one and win, so the token stored here would
         * never be read back. Cleared before the write because Cookie::set()
         * mirrors the value into $_COOKIE.
         */
        foreach (\FPFramework\Helpers\CookieScope::stalePaths() as $path)
        {
            $this->cookie->set($name, '', [
                'expires' => time() - YEAR_IN_SECONDS,
                'path'    => $path,
                'domain'  => \FPFramework\Helpers\CookieScope::domain(),
                'secure'  => true
            ]);
        }

        $options = \FPFramework\Helpers\CookieScope::options([
            'expires' => time() + $this->expire,
            'secure' => true
        ]);

        $this->cookie->set($name, $value, $options);
    }
}