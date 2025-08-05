<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.135
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\Conditions\Conditions\Date;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Date extends DateBase
{
    /**
	 *  Checks if current date passes the given date range. 
	 *  Dates must be always passed in format: Y-m-d H:i:s
	 *
	 *  @return  bool
	 */
	public function pass()
	{
		$publish_up   = $this->params->get('publish_up');
		$publish_down = $this->params->get('publish_down');

        // No valid dates
		if (!$publish_up && !$publish_up)
		{
			return false;
		}
		
		$up   = $publish_up   ? $this->getDate($publish_up)   : null;
		$down = $publish_down ? $this->getDate($publish_down) : null;

        return $this->checkRange($up, $down);
    }
    
    /**
     *  Returns the assignment's value
     * 
     *  @return \Date Current date
     */
	public function value()
	{
		return $this->date->format('Y-m-d H:i:s');
	}
}