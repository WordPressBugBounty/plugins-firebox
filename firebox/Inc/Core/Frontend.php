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

namespace FireBox\Core;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Frontend
{
    public function __construct()
    {
		// ensure we run only on front-end
		if (is_admin())
		{
			return;
		}

		wp_register_script(
			'firebox-main',
			FBOX_MEDIA_PUBLIC_URL . 'js/firebox.js',
			[],
			FBOX_VERSION,
			['in_footer' => true, 'strategy' => 'defer']
		);

		// Prepare Gutenberg Blocks that contain attributes by FireBox
		new \FireBox\Core\FB\BoxBlocksParser();

		/**
		 * Event that runs before any rendering of popups.
		 */
		do_action('firebox/boxes/before_render');

		// Run Actions
		\FireBox\Core\Helpers\Actions::run();

		add_action('template_redirect', [$this, 'render']);

		add_action('wp_enqueue_scripts', [$this, 'enqueueEmbedStylesEarly']);

		/**
		 * Event that runs after popups have been rendered.
		 */
		do_action('firebox/boxes/after_render');
	}

	/**
	 * Enqueues the embed-mode campaign CSS early (wp_enqueue_scripts, before wp_head closes)
	 * when the currently queried post/page contains an Embed Campaign block. This avoids a
	 * flash of unstyled content, since renderEmbed() otherwise only enqueues this CSS later,
	 * during content rendering.
	 *
	 * @return  void
	 */
	public function enqueueEmbedStylesEarly()
	{
		$post = get_queried_object();

		if (!($post instanceof \WP_Post))
		{
			return;
		}
		
		if (!has_block('firebox/embed', $post))
		{
			return;
		}

		wp_enqueue_style(
			'firebox',
			FBOX_MEDIA_PUBLIC_URL . 'css/firebox.css',
			[],
			FBOX_VERSION
		);

		wp_enqueue_style(
			'fb-block-embed-campaign',
			FBOX_MEDIA_PUBLIC_URL . 'css/blocks/embed-campaign.css',
			[],
			FBOX_VERSION
		);
	}
	
	/**
	 * Renders all boxes on front-end
	 * 
	 * @return  void
	 */
	public function render()
	{
		if ($this->checkForBoxPreview())
		{
			return;
		}

		// Don't render the popup if previewing with third party page builders
		if (!$this->canRenderInEditors())
		{
			return;
		}

		/**
		 * Adds all boxes at the end of page.
		 */
		firebox()->boxes->render();
	}

	/**
	 * Don't render when previewing with third party page builders.
	 * 
	 * @return  boolean
	 */
	private function canRenderInEditors()
	{
		// Don't render the popup if previewing with Elementor
		if (class_exists('\Elementor\Plugin') && 
			property_exists(\Elementor\Plugin::$instance, 'preview') && 
			method_exists(\Elementor\Plugin::$instance->preview, 'is_preview_mode') && 
			\Elementor\Plugin::$instance->preview->is_preview_mode()
		)
		{
			return;
		}

		// Don't render the popup if previewing with Beaver Builder
		if (class_exists('\FLBuilderModel') && method_exists('\FLBuilderModel', 'is_builder_active') && \FLBuilderModel::is_builder_active())
		{
			return;
		}

		// Don't render the popup if previewing with Bricks Builder
		if (function_exists('bricks_is_builder') && bricks_is_builder())
		{
			return;
		}
		
		return true;
	}

	/**
	 * Check if we are previewing a box and do not show other boxes.
	 * 
	 * @return  boolean
	 */
	private function checkForBoxPreview()
	{
		$previewer = new Previewer();
		return $previewer->init();
	}
}
