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

namespace FPFramework\Helpers\DataProviders;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Interfaces\GetSelectedItems;
use FPFramework\Base\Interfaces\GetSearchItems;
use FPFramework\Base\Interfaces\GetItems;
use FPFramework\Helpers\SearchDropdownHelper;

class EDDCategoryProvider implements GetSelectedItems, GetSearchItems, GetItems
{
	/**
	 * Returns items based on offset and limit
	 * 
	 * @param   integer  $offset
	 * @param   integer  $limit
	 * 
	 * @return  array
	 */
	public function getItems($offset = 0, $limit = SearchDropdownHelper::SELECTION_ITEMS)
	{
		if (!function_exists('EDD'))
		{
			return [];
		}

		$categories = get_categories([
			'number' => $limit,
			'offset' => $offset,
			'taxonomy' => 'download_category',
			'orderby' => 'date',
			'order' => 'DESC'
		]);

		$data = [];
		
		foreach ($categories as $cat)
		{
			$data[] = [
				'id' => $cat->term_id,
				'title' => $cat->name
			];
		}

		return $data;
	}

	/**
	 * Gets boxes from the Selected Items
	 * 
	 * @param   array   $items
	 * 
	 * @return  array
	 */
    public function getSelectedItems($items = [])
    {
		if (!function_exists('EDD'))
		{
			return [];
		}

		$categories = get_categories([
			'include' => $items,
			'taxonomy' => 'download_category',
			'orderby' => 'date',
			'order' => 'DESC',
			'number' => 99999
		]);

		$data = [];
		
		foreach ($categories as $cat)
		{
			$data[] = [
				'id' => $cat->term_id,
				'title' => $cat->name
			];
		}

		return $data;
    }

	/**
	 * Searches and returns an array of items via the name
	 * 
	 * @param   string  $name
	 * @param   array  	$no_ids  List of already added items
	 * 
	 * @return  array
	 */
    public function getSearchItems($name, $no_ids = [])
    {
		if (!function_exists('EDD'))
		{
			return [];
		}

		$categories = get_categories([
			'name__like' => $name,
			'exclude' => $no_ids,
			'taxonomy' => 'download_category',
			'orderby' => 'date',
			'order' => 'DESC'
		]);

		$data = [];
		
		foreach ($categories as $cat)
		{
			$data[] = [
				'id' => $cat->term_id,
				'title' => $cat->name
			];
		}

		return $data;
	}
}