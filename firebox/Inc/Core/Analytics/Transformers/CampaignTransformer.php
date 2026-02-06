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

class CampaignTransformer implements TransformerInterface
{
	public function shouldApply($type, $options = [])
	{
		return $type === 'top_campaign';
	}

	public function transform(&$data, $type, $options = [])
	{
		$baseURL = admin_url('admin.php?page=firebox-analytics&campaign=');

		foreach ($data as &$item)
		{
			if (!isset($item->id))
			{
				continue;
			}
			
			$item->link = $baseURL . $item->id;
		}
	}

	public function getPriority()
	{
		return 70;
	}
}
