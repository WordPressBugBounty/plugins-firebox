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

namespace FPFramework\Base\Conditions\Conditions;

defined('ABSPATH') or die;

use FPFramework\Base\Conditions\Condition;

/**
 * Time on Site condition.
 *
 * Client-side: the authoritative evaluation happens in the frontend runtime,
 * which stamps the session start in the `fpf_session_starttime` cookie on the
 * first page view and compares elapsed time in the browser. This keeps the
 * condition correct on cached pages and lets campaigns appear the moment the
 * threshold is crossed, without a new page load.
 *
 * PHP never writes the cookie (a mid-output write would silently fail once
 * headers are sent); value() only reads it for server-side consumers.
 */
class TimeOnSite extends Condition
{
	/**
	 * Evaluated in the browser against the JS-maintained session cookie.
	 *
	 * @return  bool
	 */
	public function isClientSide()
	{
		return true;
	}

	/**
	 *  Returns the condition's value
	 *
	 *  @return int Time on site in seconds
	 */
	public function value()
	{
		return $this->getTimeOnSite();
	}

	/**
	 *  Returns the user's time on site in seconds
	 *
	 *  @return int|null
	 */
	public function getTimeOnSite()
	{
		if (!$sessionStartTime = $this->getSessionStartTime())
		{
			return;
		}

		return time() - $sessionStartTime;
	}

	/**
	 *  Returns the session's start time as a Unix timestamp
	 *
	 *  @return int|null
	 */
	private function getSessionStartTime()
	{
		$sessionStartTime = (int) $this->factory->getSession()->get('fpf.session.starttime', 0);

		return $sessionStartTime > 0 ? $sessionStartTime : null;
	}
}
