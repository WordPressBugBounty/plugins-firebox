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

namespace FireBox\Core\Admin\Includes;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class UpgradeToPlanModal
{
	/**
	 * The Modal ID.
	 * 
	 * @var  String
	 */
	const modal_id = 'fireboxUpgradeToPlan';
	
	static $run = false;
	
	public function __construct()
	{
		add_action('admin_footer', [$this, 'addModal'], 13);
	}

	/**
	 * Adds Upgrade to Plan Modal to the page
	 * 
	 * @return  void
	 */
	public function addModal()
	{
		if (self::$run)
		{
			return;
		}
		
		if (!function_exists('get_current_screen'))
		{
			return;
		}

		$current_screen = get_current_screen();

		// Don't load within the block editor
		$isBlockEditor = $current_screen->is_block_editor && $current_screen->post_type === 'firebox';
		if ($isBlockEditor)
		{
			return;
		}

		$isPluginPage = strpos($current_screen->id, 'firebox') !== false;
		
		if (!$isPluginPage)
		{
			return;
		}

		self::$run = true;

		wp_register_style(
			'fpframework-pro-modal',
			FPF_MEDIA_URL . 'admin/css/fpf_pro_modal.css',
			[],
			FPF_VERSION,
			false
		);
		wp_enqueue_style('fpframework-pro-modal');
		
		// JS
		wp_register_script(
			'firebox-upgrade-to-plan-modal',
			FBOX_MEDIA_URL . 'admin/js/upgrade_to_plan_modal.js',
			[],
			FBOX_VERSION,
			false
		);
		wp_enqueue_script('firebox-upgrade-to-plan-modal');

		$content = firebox()->renderer->admin->render('modals/upgrade_to_plan', [], true);
		
		$payload = [
			'id' => self::modal_id,
			'class' => ['upgrade-to-plan', 'upgrade-pro-modal'],
			'content' => $content,
			'width' => '480px',
			'overlay_click' => false
		];
		
		// render modal
		\FPFramework\Helpers\HTML::renderModal($payload);
	}
}