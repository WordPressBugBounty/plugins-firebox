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

namespace FireBox\Core\Admin;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Media
{
	public function __construct()
	{
		if (!is_admin())
		{
			return;
		}

		add_action('enqueue_block_editor_assets', [$this, 'block_editor_only_assets']);

		add_action('enqueue_block_assets', [$this, 'enqueue_block_assets']);
	}

	public function block_editor_only_assets()
	{
		if (!is_admin())
		{
			return;
		}

		// Enqueue block editor script only in Gutenberg editor
		if (function_exists('get_current_screen'))
		{
			$screen = get_current_screen();
			if ($screen->is_block_editor)
			{
				wp_enqueue_script(
					'firebox-gutenberg-store',
					FBOX_MEDIA_ADMIN_URL . 'js/blocks/gutenberg_store.js',
					['wp-data'],
					FBOX_VERSION,
					true
				);
				
				if (FBOX_LICENSE_TYPE === 'lite')
				{
					wp_enqueue_style(
						'firebox-editor-free',
						FBOX_MEDIA_ADMIN_URL . 'css/editor-free.css',
						[],
						FBOX_VERSION
					);
					
					wp_enqueue_script(
						'firebox-plugins-free-modals',
						FBOX_MEDIA_ADMIN_URL . 'js/blocks/plugins/free-modals.js',
						[],
						FBOX_VERSION,
						true
					);

					wp_enqueue_script('firebox-blocks-free', FBOX_MEDIA_ADMIN_URL . 'js/blocks/blocks-free.js', [], FBOX_VERSION, true);
				}
				else if (FBOX_LICENSE_TYPE === 'pro')
				{
					wp_enqueue_script('firebox-blocks-pro', FBOX_MEDIA_ADMIN_URL . 'js/blocks/blocks-pro.js', [], FBOX_VERSION, true);
				}
			}
		}
	}

	/**
	 * Loads Gutenberg editor assets
	 * 
	 * @return  void
	 */
	public function enqueue_block_assets()
	{
		if (!is_admin())
		{
			return;
		}
		
		// Enqueue block editor script only in Gutenberg editor
		if (function_exists('get_current_screen'))
		{
			$screen = get_current_screen();
			if ($screen->is_block_editor)
			{
				wp_enqueue_code_editor(['type' => 'text/css']);
				wp_enqueue_code_editor(['type' => 'javascript']);

				// Add the block editor styling for our blocks
				wp_enqueue_style(
					'firebox-blocks-editor-styles',
					FBOX_MEDIA_ADMIN_URL . 'css/admin/blocks.css',
					[],
					FBOX_VERSION
				);

				// Add the FireBox block editor script only to FireBox post type
				if (get_post_type() === 'firebox')
				{
					wp_enqueue_script(
						'firebox-gutenberg-store',
						FBOX_MEDIA_ADMIN_URL . 'js/blocks/gutenberg_store.js',
						['wp-data'],
						FBOX_VERSION,
						true
					);

					// FireBox main CSS file
					wp_enqueue_style(
						'firebox',
						FBOX_MEDIA_PUBLIC_URL . 'css/firebox.css',
						[],
						FBOX_VERSION
					);

					wp_enqueue_style(
						'firebox-animations',
						FBOX_MEDIA_PUBLIC_URL . 'css/vendor/animate.min.css',
						[],
						FBOX_VERSION
					);

					wp_enqueue_style(
						'firebox-block-editor',
						FBOX_MEDIA_ADMIN_URL . 'css/block-editor.css',
						[],
						FBOX_VERSION
					);

					wp_enqueue_script(
						'firebox-editor',
						FBOX_MEDIA_ADMIN_URL . 'js/blocks/editor.js',
						['wp-edit-post'],
						FBOX_VERSION,
						true
					);
					
					wp_enqueue_script(
						'firebox-slotfills-general',
						FBOX_MEDIA_ADMIN_URL . 'js/blocks/slotfills/general.js',
						[],
						FBOX_VERSION,
						true
					);

					
					if (FBOX_LICENSE_TYPE === 'lite')
					{
						wp_enqueue_style(
							'firebox-editor-free',
							FBOX_MEDIA_ADMIN_URL . 'css/editor-free.css',
							[],
							FBOX_VERSION
						);
					
						wp_enqueue_script(
							'firebox-plugins-free-modals',
							FBOX_MEDIA_ADMIN_URL . 'js/blocks/plugins/free-modals.js',
							[],
							FBOX_VERSION,
							true
						);

						wp_enqueue_script(
							'firebox-slotfills-free',
							FBOX_MEDIA_ADMIN_URL . 'js/blocks/slotfills/free.js',
							[],
							FBOX_VERSION,
							true
						);
					}
					

					
				}

				
		
				$data = [
					'google_fonts' => \FPFramework\Libs\GoogleFonts::getFonts(),
					'google_fonts_names' => \FPFramework\Libs\GoogleFonts::getFontsNames(),
					'icons' => \FireBox\Core\Libs\Icons::getAll(),
					'turnstile_site_key' => \FireBox\Core\Helpers\Captcha\Turnstile::getSiteKey(),
					'turnstile_secret_key' => \FireBox\Core\Helpers\Captcha\Turnstile::getSecretKey(),
					'hcaptcha_site_key' => \FireBox\Core\Helpers\Captcha\HCaptcha::getSiteKey(),
					'hcaptcha_secret_key' => \FireBox\Core\Helpers\Captcha\HCaptcha::getSecretKey(),
					'settings_url' => admin_url('admin.php?page=firebox-settings'),
					'media_url' => FBOX_MEDIA_URL,
					
				];

				wp_register_script('firebox-block-editor-script', false);
				wp_enqueue_script('firebox-block-editor-script');
				wp_localize_script('firebox-block-editor-script', 'fbox_block_editor_object', $data);
			}
		}
	}
}