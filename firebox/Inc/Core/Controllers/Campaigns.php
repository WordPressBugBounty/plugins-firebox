<?php
/**
 * @package         FireBox
 * @version         3.1.9 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Controllers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Campaigns extends BaseController
{
	/**
	 * The minimum WordPress version required to render the DataViews Campaigns page.
	 * Below this, the classic WP_List_Table fallback (CampaignsList) is used instead.
	 * See docs/plans/campaigns-dataviews-migration.md §3.1.
	 *
	 * Pinned to 7.0: @wordpress/dataviews depends on @wordpress/ui, whose ThemeProvider
	 * opts into WordPress's private/unstable APIs identifying itself as "@wordpress/theme".
	 * That module name is only on WordPress core's allow-list for private-API consumers
	 * starting WP 7.0 (it's also only WP 7.0+ that registers a `wp-theme` script handle
	 * at all) - on earlier WP the call throws at runtime and the whole bundle fails to
	 * mount, leaving the page stuck on the server-rendered loading skeleton.
	 */
	const DATAVIEWS_MIN_WP_VERSION = '7.0';

	/**
	 * Render Campaigns page.
	 *
	 * @return  void
	 */
	public function render()
	{
		firebox()->renderer->admin->render('pages/campaigns');
	}

	/**
	 * Enqueues the DataViews React app on WP >= self::DATAVIEWS_MIN_WP_VERSION.
	 * On older WP, the classic list table (CampaignsList) is used and needs no extra assets.
	 *
	 * @return  void
	 */
	public function addMedia()
	{
		if (!self::useDataViews())
		{
			return;
		}

		// Build artifact, committed and shipped in every tier - always present.
		$asset = include FBOX_PLUGIN_DIR . 'media/admin/js/blocks/campaigns-dataviews.asset.php';

		wp_register_script(
			'firebox-campaigns-dataviews',
			FBOX_MEDIA_ADMIN_URL . 'js/blocks/campaigns-dataviews.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_localize_script('firebox-campaigns-dataviews', 'fboxCampaignsData', [
			'capabilities' => [
				'edit'   => current_user_can('edit_fireboxes'),
				'delete' => current_user_can('delete_fireboxes'),
			],
			'urls' => [
				'newCampaign' => admin_url('post-new.php?post_type=firebox'),
				'import'      => admin_url('admin.php?page=firebox-import'),
			],
		]);

		wp_set_script_translations('firebox-campaigns-dataviews', 'firebox', FBOX_PLUGIN_DIR . 'languages');

		wp_enqueue_script('firebox-campaigns-dataviews');

		// The bundle emits one stylesheet: the @wordpress/dataviews styles followed
		// by our own overrides (see the import order in campaigns/index.js).
		wp_enqueue_style(
			'firebox-campaigns-dataviews',
			FBOX_MEDIA_ADMIN_URL . 'js/blocks/style-campaigns-dataviews.css',
			['wp-components'],
			$asset['version']
		);
	}

	/**
	 * Whether the current WordPress version can render the DataViews Campaigns page.
	 *
	 * @return  bool
	 */
	public static function useDataViews()
	{
		return version_compare(get_bloginfo('version'), self::DATAVIEWS_MIN_WP_VERSION, '>=');
	}
}