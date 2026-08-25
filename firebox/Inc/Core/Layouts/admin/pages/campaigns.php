<?php
/**
 * @package         FireBox
 * @version         3.1.11 Free
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

if (!current_user_can('read_fireboxes'))
{
	esc_html_e('Sorry, you are not allowed to access this page.', 'firebox');
	return;
}
?>
<h1 class="mb-3 text-default text-[32px] dark:text-white flex gap-1 items-center fp-admin-page-title"><?php echo esc_html(firebox()->_('FB_CAMPAIGNS')); ?></h1>

<?php if (\FireBox\Core\Controllers\Campaigns::useDataViews()) : ?>
	<div id="fbox-campaigns-root">
		<?php // Pre-mount placeholder: mirrors the React LoadingSkeleton so the table
		// structure is visible before the bundle loads. React replaces it on mount. ?>
		<div class="fbox-campaigns-app">
			<div class="fbox-campaigns-skeleton" role="status" aria-label="<?php esc_attr_e('Loading campaigns…', 'firebox'); ?>">
				<?php for ($row = 0; $row < 8; $row++) : ?>
					<div class="fbox-campaigns-skeleton__row">
						<?php for ($cell = 0; $cell < 6; $cell++) : ?>
							<div class="fbox-campaigns-skeleton__cell"></div>
						<?php endfor; ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	</div>
<?php else :
	$table = new \FireBox\Core\FB\CampaignsList();

	// Process bulk actions and show notices
	$table->process_bulk_action();
	do_action('fpframework/admin/notices');
	?>
	<form action="" method="get">
		<?php
		$table->views();
		$table->prepare_items();
		$table->search_box(firebox()->_('FB_SEARCH_CAMPAIGNS'), 'firebox-campaign' );
		$table->display();
		?>
		<input type="hidden" name="page" value="firebox-campaigns" />
	</form>
<?php endif; ?>