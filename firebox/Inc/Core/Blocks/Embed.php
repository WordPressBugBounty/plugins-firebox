<?php
/**
 * @package         FireBox
 * @version         2.1.31 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Blocks;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Embed extends \FireBox\Core\Blocks\Block
{
	/**
	 * Block identifier.
	 * 
	 * @var  string
	 */
	protected $name = 'embed';

	/**
	 * Adds Google Fonts.
	 * 
	 * @param   array   $attributes
	 * @param   string  $content
	 * 
	 * @return  string
	 */
	public function render_callback($attributes, $content)
	{
		$campaign = isset($attributes['campaign']) ? intval($attributes['campaign']) : null;

		if (!$campaign)
		{
			return;
		}

		return \FireBox\Core\Helpers\Embed::renderCampaign($campaign);
	}

	/**
	 * Registers block assets.
	 * 
	 * @return  void
	 */
	public function public_assets()
	{
		wp_register_style(
			'fb-block-embed-campaign',
			FBOX_MEDIA_PUBLIC_URL . 'css/blocks/embed-campaign.css',
			[],
			FBOX_VERSION
		);
	}

	/**
	 * Registers assets both on front-end and back-end.
	 * 
	 * @return  void
	 */
	public function enqueue_block_assets()
	{
		wp_register_style(
			'firebox',
			FBOX_MEDIA_PUBLIC_URL . 'css/firebox.css',
			[],
			FBOX_VERSION
		);

		wp_register_style(
			'fb-block-embed-campaign',
			FBOX_MEDIA_PUBLIC_URL . 'css/blocks/embed-campaign.css',
			[],
			FBOX_VERSION
		);
	}
}