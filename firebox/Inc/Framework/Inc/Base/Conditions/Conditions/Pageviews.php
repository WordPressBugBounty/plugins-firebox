<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.5
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
 * Pageviews condition.
 *
 * Client-side: the frontend runtime increments the `fpf_session_counter`
 * cookie on every page view and evaluates the rule in the browser, so the
 * count stays accurate on cached pages. PHP only reads the cookie.
 */
class Pageviews extends Condition
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
     *  Returns the assignment's value
     *
     *  @return int Number of page visits
     */
    public function value()
    {
        return (int) $this->factory->getSession()->get('fpf.session.counter', 0);
    }
}