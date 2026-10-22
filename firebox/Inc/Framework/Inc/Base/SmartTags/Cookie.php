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

namespace FPFramework\Base\SmartTags;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Filter;

class Cookie extends SmartTag
{
	/**
	 * Returns the value of a cookie as stored in the visitor’s browser.
	 *
	 * @param   string  $key
	 *
	 * @return  string
	 */
	public function fetchValue($key)
	{
		$value = $this->factory->getCookie($key);

		if ($value === null || $value === '')
		{
			return '';
		}

		// Sanitize by removing HTML. Cookie values are attacker-controlled and substituted
		// into rendered content, so returning them raw would reflect attacker markup.
		return Filter::getInstance()->clean($value, 'HTML');
	}
}