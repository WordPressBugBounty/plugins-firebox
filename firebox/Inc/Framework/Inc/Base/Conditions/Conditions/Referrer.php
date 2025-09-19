<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.138
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\Conditions\Conditions;

defined('ABSPATH') or die;

class Referrer extends URLBase
{
   	/**
   	 *  Pass Referrer URL. 
   	 *
   	 *  @return  bool   Returns true if the Referrer URL contains any of the selection URLs 
   	 */
   	public function pass()
   	{
		// Make sure the referer server variable is available
		if (!isset($_SERVER['HTTP_REFERER']))
		{
			return;
		}
		
		return $this->passURL($this->value());
    }

    /**
     *  Returns the condition's value
     * 
     *  @return string Referrer URL
     */
	public function value()
	{
		return sanitize_url(wp_unslash($_SERVER['HTTP_REFERER']));
	}
}