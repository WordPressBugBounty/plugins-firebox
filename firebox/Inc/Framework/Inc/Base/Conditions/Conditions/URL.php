<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.133
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\Conditions\Conditions;

defined('ABSPATH') or die;

class URL extends URLBase
{
    /**
     * Returns the assignment's value
     * 
     * @return  string  Current URL
     */
	public function value()
	{
		return $this->factory->getURL();
	}
}