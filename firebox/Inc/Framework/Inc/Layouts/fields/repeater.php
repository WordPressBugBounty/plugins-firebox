<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.5
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}
// Field definitions may omit these keys entirely, or carry a single class as a string.
// count()/implode() throw a TypeError on anything but an array since PHP 8.0, so
// normalise every class list to an array before using it.
$btn_class_list = \FPFramework\Helpers\FieldsHelper::toClassList($this->data->get('btn_class'));
$actions_class_list = \FPFramework\Helpers\FieldsHelper::toClassList($this->data->get('actions_class'));
$remove_action_class_list = \FPFramework\Helpers\FieldsHelper::toClassList($this->data->get('remove_action_class'));
$class_list = \FPFramework\Helpers\FieldsHelper::toClassList($this->data->get('class'));
$repeater_item_class_list = \FPFramework\Helpers\FieldsHelper::toClassList($this->data->get('repeater_item_class'));

$btn_classes = $btn_class_list ? ' ' . implode(' ', $btn_class_list) : '';
$actions_classes = $actions_class_list ? ' ' . implode(' ', $actions_class_list) : '';
$remove_action_class = $remove_action_class_list ? ' ' . implode(' ', $remove_action_class_list) : '';
$class = $class_list ? ' ' . esc_attr(implode(' ', $class_list)) : '';
$repeater_item_class = $repeater_item_class_list ? ' ' . esc_attr(implode(' ', $repeater_item_class_list)) : '';

$items = $this->data->get('value');
$items = is_array($items) ? $items : [];
$actions = '
    <ul class="actions">'
        . $this->data->get('actions_prepend', '') . '
        <li class="fpf-repeater-field-add-item-btn add" title="' . esc_attr(fpframework()->_('FPF_ADD_ITEM')) . '"><i class="dashicons dashicons-plus-alt"></i></li>
        <li class="fpf-repeater-field-remove-item-btn remove' . esc_attr($remove_action_class) . '" title="' . esc_attr(fpframework()->_('FPF_REMOVE_ITEM')) . '"><i class="dashicons dashicons-no-alt"></i></li>
        <li class="fpf-repeater-field-move-item-btn" title="' . esc_attr(fpframework()->_('FPF_MOVE_ITEM')) . '"><i class="dashicons dashicons-move"></i></li>
        ' . $this->data->get('actions_append', '') . '
    </ul>';
$default = $this->data->get('default', []);
$value_raw = $this->data->get('value_raw');
$default_values = $this->data->get('default_values');
$default_html = $this->data->get('default_html');
// If the repeater has default values and no actual value is given, then show the fields with default values
if ($value_raw === null && $default_values)
{
    $items = $default_html;
}
?>
<div class="fpf-control-group-field fpf-repeater-field<?php echo esc_attr($class); ?>"
     data-name="<?php echo esc_attr($this->data->get('name_key')); ?>">
    <div class="items">
        <?php
        if (count($items))
        {
            $i = 1;
            foreach ($items as $key => $html)
            {
                ?><div class="fpf-repeater-item<?php echo esc_attr($repeater_item_class); ?>" data-item-id="<?php echo esc_attr($i); ?>">
                    <?php echo $html . $actions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div><?php
                $i++;
            }
        }
        ?>
    </div>
    <div class="template fpf-repeater-item<?php echo esc_attr($repeater_item_class); ?>" data-item-id="[ITEM_ID]">
        <?php echo $this->data->get('template', '') . $actions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </div>
    <div class="repeater-actions<?php echo esc_attr($actions_classes); ?>">
        <a href="#" class="fpf-button fpf-repeater-field-add-item-btn<?php echo esc_attr($btn_classes); ?>"><i class="icon dashicons dashicons-plus-alt"></i><?php echo esc_html(fpframework()->_($this->data->get('btn_label'))); ?></a>
    </div>
</div>