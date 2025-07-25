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

$forms = $this->data->get('forms');
$selected_form = isset($_GET['form_id']) ? sanitize_text_field(wp_unslash($_GET['form_id'])) : (isset($forms[0]['id']) ? sanitize_text_field($forms[0]['id']) : ''); //phpcs:ignore WordPress.Security.NonceVerification.Recommended

$submissions = new \FireBox\Core\Form\Tables\Submissions();

// Process bulk actions and show notices
$submissions->process_bulk_action();
do_action('fpframework/admin/notices');

$current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : ''; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>
<h1 class="mb-3 text-default text-[32px] dark:text-white flex gap-1 items-center fp-admin-page-title"><?php echo esc_html(firebox()->_('FB_SUBMISSIONS_PAGE_TITLE')); ?></h1>
<select class="fb-form-selection">
	<option disabled<?php echo empty($forms) ? ' selected' : ''; ?>><?php echo esc_html(firebox()->_('FB_PLEASE_SELECT_A_FORM_CAMPAIGN')); ?></option>
	<?php foreach($forms as $key => $value): ?>
		<option value="<?php echo esc_attr($value['id']); ?>"<?php echo $value['id'] === $selected_form ? ' selected' : ''; ?>><?php echo esc_html($value['name']); ?></option>
	<?php endforeach; ?>
</select>
<div class="fb-submissions">
	<form method="GET">
		<?php
		// Show submissions table
		$submissions->prepare_items();
		$submissions->views();
		$submissions->display();
		// Nonce
		wp_nonce_field('fb_form_submission_action', 'fb_form_submission_field', false, true);
		?>
		<input type="hidden" name="page" value="<?php echo esc_attr($current_page); ?>" />
		<input type="hidden" name="form_id" value="<?php echo esc_attr($selected_form); ?>" />
	</form>
</div>
<?php