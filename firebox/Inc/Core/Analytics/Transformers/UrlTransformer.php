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

class UrlTransformer implements TransformerInterface
{
	public function shouldApply($type, $options = [])
	{
		return in_array($type, ['referrers', 'pages']);
	}

	public function transform(&$data, $type, $options = [])
	{
		switch ($type) {
			case 'referrers':
				$this->transformReferrerUrls($data);
				break;
			case 'pages':
				$this->transformPageUrls($data);
				break;
		}
	}

	public function getPriority()
	{
		return 60;
	}

	private function transformReferrerUrls(&$data)
	{
		$regex = '/^(https?:\/\/(www\.)?|www\.)/i';
		
		foreach ($data as &$item) {
			$item->full_label = $item->label;
			$item->label = rtrim(preg_replace($regex, '', $item->label), '/');
		}
	}

	private function transformPageUrls(&$data)
	{
		$site_url = get_site_url();
		
		foreach ($data as &$item) {
			$item->full_label = $item->label;
			$item->label = str_replace($site_url, '', $item->label);
		}
	}
}
