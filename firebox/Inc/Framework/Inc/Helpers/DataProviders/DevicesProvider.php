<?php
/**
 * @package         FirePlugins Framework
 * @version         1.1.129
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Helpers\DataProviders;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Interfaces\GetSelectedItems;

class DevicesProvider implements GetSelectedItems
{
	/**
	 * Gets items from the Selected Items IDs
	 * 
	 * @param   array   $items
	 * 
	 * @return  array
	 */
    public function getSelectedItems($items)
    {
		$devices = fpframework()->helper->devices;
		
		$parsed = [];

		foreach ($devices::getDevices() as $key => $value)
		{
			if (in_array($key, $items))
			{
				$parsed[] = [
					'id' => $key,
					'title' => $value
				];
			}
		}
		
		return $parsed;
    }
}