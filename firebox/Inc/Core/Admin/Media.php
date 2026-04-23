<?php
/**
 * @package         FireBox
 * @version         3.1.6 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
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

		add_filter('block_editor_settings_all', [$this, 'filterBlockEditorSettings'], 10, 2);
	}

	/**
	 * Disable template mode for FireBox campaigns.
	 *
	 * @param   array  $settings
	 * @param   mixed  $editor_context
	 *
	 * @return  array
	 */
	public function filterBlockEditorSettings($settings, $editor_context)
	{
		if (!is_admin() || !is_array($settings))
		{
			return $settings;
		}

		if (
			!is_object($editor_context) ||
			!property_exists($editor_context, 'post') ||
			!is_object($editor_context->post) ||
			!property_exists($editor_context->post, 'post_type') ||
			$editor_context->post->post_type !== 'firebox'
		)
		{
			return $settings;
		}

		$settings['supportsTemplateMode'] = false;

		return $settings;
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

				// Required for ProOnlyButton hover icon swap (closed/open lock) across plans.
				wp_enqueue_style(
					'firebox-editor-free',
					FBOX_MEDIA_ADMIN_URL . 'css/editor-free.css',
					[],
					FBOX_VERSION
				);

				if ($this->shouldLoadFreeUpgradeModalsScript())
				{
					wp_enqueue_script(
						'firebox-plugins-free-modals',
						FBOX_MEDIA_ADMIN_URL . 'js/blocks/plugins/free-modals.js',
						[],
						FBOX_VERSION,
						true
					);
				}
				
				if (FBOX_LICENSE_TYPE === 'lite')
				{
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
						false
					);
					
					wp_enqueue_script(
						'firebox-slotfills-general',
						FBOX_MEDIA_ADMIN_URL . 'js/blocks/slotfills/general.js',
						[],
						FBOX_VERSION,
						true
					);

					// Required for ProOnlyButton hover icon swap (closed/open lock) across plans.
					wp_enqueue_style(
						'firebox-editor-free',
						FBOX_MEDIA_ADMIN_URL . 'css/editor-free.css',
						[],
						FBOX_VERSION
					);

					if ($this->shouldLoadFreeUpgradeModalsScript())
					{
						wp_enqueue_script(
							'firebox-plugins-free-modals',
							FBOX_MEDIA_ADMIN_URL . 'js/blocks/plugins/free-modals.js',
							[],
							FBOX_VERSION,
							true
						);
					}

					
					if (FBOX_LICENSE_TYPE === 'lite')
					{
						wp_enqueue_script(
							'firebox-slotfills-free',
							FBOX_MEDIA_ADMIN_URL . 'js/blocks/slotfills/free.js',
							[],
							FBOX_VERSION,
							true
						);
					}
					

					
				}

					

					$integrations_registry = \FireBox\Core\Helpers\Integrations::getFormEditorIntegrationsRegistry();
					$integration_connections = [];
					foreach ($integrations_registry as $integration)
					{
						if (!is_array($integration) || empty($integration['slug']))
						{
							continue;
						}

						$integration_connections[$integration['slug']] = !empty($integration['connected']);
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
						'integrations_registry' => $integrations_registry,
						'integration_connections' => $integration_connections,
						'license_type' => defined('FBOX_LICENSE_TYPE') ? FBOX_LICENSE_TYPE : '',
						'license_plan' => defined('FBOX_LICENSE_PLAN') ? FBOX_LICENSE_PLAN : '',
						'media_url' => FBOX_MEDIA_URL,
						
				];

				wp_register_script('firebox-block-editor-script', false);
				wp_enqueue_script('firebox-block-editor-script');
				wp_localize_script('firebox-block-editor-script', 'fbox_block_editor_object', $data);
			}
		}
	}

	private function shouldLoadFreeUpgradeModalsScript()
	{
		$license_type = defined('FBOX_LICENSE_TYPE') ? strtolower(trim((string) FBOX_LICENSE_TYPE)) : '';
		$license_plan = defined('FBOX_LICENSE_PLAN') ? strtolower(trim((string) FBOX_LICENSE_PLAN)) : '';

		return $license_type === 'lite' || in_array($license_plan, ['free', 'basic'], true);
	}
}
