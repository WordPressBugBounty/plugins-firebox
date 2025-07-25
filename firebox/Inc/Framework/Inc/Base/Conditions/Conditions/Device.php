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

use FPFramework\Base\Conditions\Condition;

class Device extends Condition
{
    /**
     *  Returns the assignment's value
     * 
     *  @return string Device type
     */
	public function value()
	{
		return $this->factory->getDevice();
	}

    /**
	 * A one-line text that describes the current value detected by the rule. Eg: The current time is %s.
    *
	 * @return string
	 */
	public function getValueHint()
	{
        return parent::getValueHint() . ' ' . fpframework()->_('FPF_ASSIGN_DEVICES_NOTE');
	}
}