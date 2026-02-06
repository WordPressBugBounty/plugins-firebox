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

interface TransformerInterface
{
	/**
	 * Check if this transformer should be applied
	 * 
	 * @param string $type Metric type
	 * @param array $options Metric options
	 * @return bool
	 */
	public function shouldApply($type, $options = []);

	/**
	 * Transform the data
	 * 
	 * @param array $data Reference to data array
	 * @param string $type Metric type
	 * @param array $options Metric options
	 * @return void
	 */
	public function transform(&$data, $type, $options = []);

	/**
	 * Get transformer priority (lower numbers run first)
	 * 
	 * @return int
	 */
	public function getPriority();
}
