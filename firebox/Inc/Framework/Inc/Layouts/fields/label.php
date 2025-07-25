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

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}
$allowed_tags = [
	'br' => true,
	'b' => true,
	'strong' => [
		'class' => true
	],
	'span' => [
		'class' => true
	],
	'a' => [
		'href' => true,
		'class' => true
	],
	'i' => [
		'class' => true
	],
	'img' => [
		'src' => true,
		'role' => true,
		'class' => true,
		'alt' => true
	]
];
?>
<div class="<?php echo esc_attr($this->data->get('input_class')); ?>"><?php echo wp_kses(fpframework()->_($this->data->get('text', '')), $allowed_tags); ?></div>