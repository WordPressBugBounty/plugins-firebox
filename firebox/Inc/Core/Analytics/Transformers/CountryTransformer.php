<?php
/**
 * @package         FireBox
 * @version         3.1.4 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Analytics\Transformers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class CountryTransformer implements TransformerInterface
{
	public function shouldApply($type, $options = [])
	{
		return $type === 'countries';
	}

	public function transform(&$data, $type, $options = [])
	{
		foreach ($data as &$item) {
			$item->code = $item->label;
			$item->label = \FPFramework\Helpers\CountriesHelper::getCountryName($item->label) ?? $item->label;
		}
	}

	public function getPriority()
	{
		return 50;
	}
}
