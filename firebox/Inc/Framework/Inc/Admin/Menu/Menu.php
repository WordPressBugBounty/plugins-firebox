<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.2
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Admin\Menu;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Admin\Menu\Manager;
use FPFramework\Libs\Registry;

abstract class Menu
{
	/**
	 * The Menu Manager
	 * 
	 * @var  Manager
	 */
	public $manager;

	/**
	 * Initializes the menus
	 * 
	 * @param   Manager  $manager
	 * 
	 * @return  void
	 */
	public function init($manager = null)
	{
		if (empty($manager))
		{
			$manager = new Manager();
		}
		
		$this->manager = $manager;

		// Register Menus
		$this->registerMenus();
	}

	/**
	 * Returns all plugin menu items
	 * 
	 * @return  array
	 */
	public function getPluginMenuItems()
	{
		if (!method_exists($this, 'getMenuItems'))
		{
			return [];
		}

		if (!$menu_items = $this->getMenuItems())
		{
			return [];
		}

		$data = [];

		foreach ($menu_items as $menu)
		{
			if (!isset($menu['menu_slug']))
			{
				continue;
			}

			$data[] = $menu['menu_slug'];
		}
		
		return array_unique($data);
	}

	/**
	 * Register plugin menus
	 * 
	 * @return  void
	 */
	public function registerMenus()
	{
		if (!method_exists($this, 'getMenuItems'))
		{
			return;
		}
		
		if (!$menu_items = apply_filters('fpframework/filter_menu_items', $this->getMenuItems()))
		{
			return;
		}
		
		foreach ($menu_items as $menu)
		{
			$menu = new Registry($menu);
			
			// Top level menu item
			if ($menu->get('is_parent')) {
				// Pass the already-built Registry through instead of rebuilding an array
				// (Manager::addMenuPage/addSubmenuPage skip re-wrapping when given a Registry).
				$this->manager->addMenuPage($menu);
			}
			else //second level menu items
			{
				$this->manager->addSubmenuPage($menu);
			}
		}
	}

	abstract function getMenuItems();
}