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

namespace FPFramework\Base\Factory;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

/**
 * Cookie-backed session store.
 *
 * Native PHP sessions are intentionally not used: session_start() disables
 * full-page caching (Cache-Control: no-store + PHPSESSID cookie) and its file
 * lock serializes concurrent requests, breaking REST API loopback requests
 * (Site Health "active PHP session" warning, cURL error 28 in the editor).
 *
 * Values are stored in browser-session cookies (expire on browser close),
 * which is the closest equivalent to the PHP session cookie lifetime.
 */
class Session
{
	/**
	 * Cookie name storing the visitor's session ID.
	 *
	 * @var  string
	 */
	const SESSION_ID_COOKIE = 'fpfsid';

	/**
	 * The Cookie instance.
	 *
	 * @var  object
	 */
	private $cookie;

	public function __construct($cookie = null)
	{
		if (!$cookie)
		{
			$cookie = new \FPFramework\Libs\Cookie();
		}

		$this->cookie = $cookie;
	}

	/**
	 * Get Session ID.
	 *
	 * The ID is normally minted by the frontend runtime (a JS-set session
	 * cookie), which works on full-page-cache hits and never races the
	 * headers. This method only reads it; the generation below is a fallback
	 * for contexts the stamper hasn't reached yet (first-ever AJAX/REST hit,
	 * JS disabled, crawlers).
	 *
	 * @return  string
	 */
	public function getSessionID()
	{
		if ($id = $this->cookie->get($this->getCookieName(self::SESSION_ID_COOKIE)))
		{
			// The ID is hex; strip anything else from the visitor-controlled cookie.
			if ($id = preg_replace('/[^a-f0-9]/', '', strtolower($id)))
			{
				return $id;
			}
		}

		$id = bin2hex(random_bytes(13));

		$this->setCookie($this->getCookieName(self::SESSION_ID_COOKIE), $id);

		return $id;
	}

	/**
	 * Gets Session value
	 *
	 * @param   string  $key
	 * @param   mixed   $fallback
	 *
	 * @return  string
	 */
	public function get($key, $fallback = null)
	{
		$value = $this->cookie->get($this->getCookieName($key));

		return $value !== null ? wp_unslash($value) : $fallback;
	}

	/**
	 * Sets session key, value
	 *
	 * @return  void
	 */
	public function set($key, $value)
	{
		$this->setCookie($this->getCookieName($key), $value);
	}

	public function getSession()
	{
		return $_COOKIE;
	}

	/**
	 * Maps a session key to a valid cookie name.
	 *
	 * Dots are not valid in cookie names (PHP normalizes them to underscores).
	 *
	 * @param   string  $key
	 *
	 * @return  string
	 */
	private function getCookieName($key)
	{
		/**
		 * Scoped to the site: on a subdirectory network every site shares one
		 * cookie namespace, so without this the pageview counter and session
		 * clock of one site are read as another's.
		 */
		return \FPFramework\Helpers\CookieScope::name(str_replace('.', '_', $key));
	}

	/**
	 * Sets a browser-session cookie.
	 *
	 * @param   string  $name
	 * @param   mixed   $value
	 *
	 * @return  void
	 */
	private function setCookie($name, $value)
	{
		if (headers_sent($file, $line))
		{
			// Too late to send the cookie; preserve the value for this request only.
			// It will not persist to the next request. Persistent writes must
			// occur before output is sent or be handled in the browser.
			$_COOKIE[$name] = $value;
			return;
		}

		// Before the real write, or the stale copy below would be the one read back.
		$this->expireStaleCopies($name);

		$this->cookie->set($name, $value, \FPFramework\Helpers\CookieScope::options([
			'expires'  => 0,
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		]));
	}

	/**
	 * Expires copies of this cookie left at a path we no longer write to.
	 *
	 * Runs before the real write on purpose: Cookie::set() mirrors the value
	 * into $_COOKIE, so clearing afterwards would blank the value for the rest
	 * of this request.
	 *
	 * @param   string  $name
	 *
	 * @return  void
	 */
	private function expireStaleCopies($name)
	{
		foreach (\FPFramework\Helpers\CookieScope::stalePaths() as $path)
		{
			$this->cookie->set($name, '', [
				'expires'  => time() - YEAR_IN_SECONDS,
				'path'     => $path,
				'domain'   => \FPFramework\Helpers\CookieScope::domain(),
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Lax',
			]);
		}
	}
}
