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

namespace FireBox\Core\Blocks;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Heading extends \FireBox\Core\Blocks\Block
{
	/**
	 * Block identifier.
	 * 
	 * @var  string
	 */
	protected $name = 'heading';

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
		wp_enqueue_style('fb-block-heading');

		if (isset($attributes['blockFontType']) && isset($attributes['blockFontFamily']) && $attributes['blockFontType'] === 'google')
		{
			$fontWeight = isset($attributes['blockFontWeight']) ? $attributes['blockFontWeight'] : 400;
			$fontFamily = $attributes['blockFontFamily']['value'];
			
			$font = [
				'fontfamily' => $fontFamily,
				'fontvariants' => [$fontWeight]
			];

			\FPFramework\Libs\GoogleFontsRenderer::getInstance()->addFont($fontFamily, $font);
		}

		return parent::render_callback($attributes, $content);
	}
	
	/**
	 * Registers block assets.
	 * 
	 * @return  void
	 */
	public function public_assets()
	{
		wp_register_style(
			'fb-block-heading',
			FBOX_MEDIA_PUBLIC_URL . 'css/blocks/heading.css',
			[],
			FBOX_VERSION
		);
	}
}