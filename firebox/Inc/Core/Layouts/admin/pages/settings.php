<?php
/**
 * @package         FireBox
 * @version         3.0.0 Free
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
$class = $this->data->get('class') ? ' ' . $this->data->get('class') : '';
?>
<h1 class="mb-3 text-default text-[32px] dark:text-white flex gap-1 items-center fp-admin-page-title"><?php echo esc_html(fpframework()->_('FPF_SETTINGS')); ?></h1>
<div class="fpf-settings-page<?php echo esc_attr($class); ?>">
	<?php do_action('firebox/settings_page'); ?>
</div>