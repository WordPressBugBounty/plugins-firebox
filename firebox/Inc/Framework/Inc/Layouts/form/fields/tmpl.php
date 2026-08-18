<?php
/**
 * @package         FireBox
 * @version         3.1.10 Free
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
$isRadioCheckbox = in_array($this->data->get('type'), ['checkbox', 'radio']);
$labelElement = $isRadioCheckbox ? 'legend' : 'label';
// implode() throws a TypeError on non-arrays since PHP 8.0, and a form field's CSS class
// is authored by the user, so it very plausibly arrives as a plain string.
$css_class_list = \FPFramework\Helpers\FieldsHelper::toClassList($this->data->get('css_class'));
?>
<div id="field-<?php echo esc_attr($this->data->get('id')); ?>" class="fb-form-control-group field-<?php echo esc_attr($this->data->get('id')); ?><?php echo $css_class_list ? ' ' . esc_attr(implode(' ', $css_class_list)) : ''; ?>" data-field-id="<?php echo esc_attr($this->data->get('id')); ?>">
	<?php if (!$this->data->get('hideLabel')): ?>
	<<?php echo esc_attr($labelElement); ?> class="fb-form-control-label"<?php echo $isRadioCheckbox ? '' : ' for="fb-form-input-' . esc_attr($this->data->get('id')) . '"'; ?>>
		<?php echo esc_html($this->data->get('label')); ?>
		
		<?php if ($this->data->get('required') && $this->data->get('requiredFieldIndication')): ?>
			<span class="fb-form-control-required">*</span>
		<?php endif; ?>
	</<?php echo esc_attr($labelElement); ?>>
	<?php endif; ?>

	<div class="fb-form-control-input">
		<?php echo $this->data->get('input'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<?php if ($this->data->get('description', '')): ?>
		<div class="fb-form-control-helptext"><?php echo esc_html($this->data->get('description')); ?></div>
	<?php endif; ?>
</div>