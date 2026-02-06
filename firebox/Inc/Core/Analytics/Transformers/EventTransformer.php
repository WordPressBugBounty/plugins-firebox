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

class EventTransformer implements TransformerInterface
{
	public function shouldApply($type, $options = [])
	{
		return $type === 'events';
	}

	public function transform(&$data, $type, $options = [])
	{
		foreach ($data as &$item) {
			$item->label = firebox()->_('FB_' . strtoupper($item->label) . '_EVENT');
		}
	}

	public function getPriority()
	{
		return 50;
	}
}
