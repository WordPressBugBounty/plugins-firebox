<?php
/**
 * @package         FireBox
 * @version         3.1.15 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\FB;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class BoxBlocksParser
{
    public function __construct()
    {
		add_filter('render_block', [$this, 'parse_block'], 10, 2);
	}

	/**
	 * Single entry point for all render_block work on this class. Bails immediately
	 * when the block carries none of our FireBox attributes, before any preg_ /
	 * get_post_type() work runs for every block on the page.
	 *
	 * @param   string  $block_content
	 * @param   array   $block
	 *
	 * @return  string
	 */
	public function parse_block($block_content, $block)
	{
		if (empty($block['attrs']) || !$this->hasFireBoxAttrs($block['attrs']))
		{
			return $block_content;
		}

		$block_content = $this->maybe_load_block_extension_script($block_content, $block);
		$block_content = $this->modify_block_output($block_content, $block);

		return $block_content;
	}

	/**
	 * Whether the block's attributes contain any FireBox-specific key.
	 *
	 * @param   array  $attrs
	 *
	 * @return  bool
	 */
	private function hasFireBoxAttrs($attrs)
	{
		return isset($attrs['dataFBoxOnClick']) || isset($attrs['dataFBoxOnClickURLAddParameters']);
	}

	public function maybe_load_block_extension_script($block_content, $block)
	{
		if (isset($block['attrs']['dataFBoxOnClick']) && in_array($block['attrs']['dataFBoxOnClick'], ['copy_clipboard', 'download_file']))
		{
			wp_enqueue_style(
				'firebox-block-extensions',
				FBOX_MEDIA_PUBLIC_URL . 'css/block_extensions.css',
				[],
				FBOX_VERSION
			);
			wp_enqueue_script(
				'firebox-block-extensions',
				FBOX_MEDIA_PUBLIC_URL . 'js/block_extensions.js',
				[],
				FBOX_VERSION,
				true
			);
		}

		return $block_content;
	}

	/**
	 * Filters the block output by appending out custom data attributes
	 * on our supported blocks
	 *
	 * @param   string  $block_content
	 * @param   array   $block
	 *
	 * @return  string
	 */
	public function modify_block_output($block_content, $block)
	{
		$atts = $this->getBlockAttributes($block);
		if (!$atts['utmParamsEnabled'])
		{
			return $block_content;
		}

		if (!isset($block['blockName']))
		{
			return $block_content;
		}

		$blockName = $block['blockName'];

		$parsable_blocks = [
			'firebox/button',
			'firebox/image',
			'core/button',
			'core/image'
		];

		if (!in_array($blockName, $parsable_blocks))
		{
			return $block_content;
		}

		// Check if href attribute contains ? then prefix is & else prefix is ?, use regex
		$utmPrefix = preg_match('/href="([^"]*)\?/', $block_content) ? '&' : '?';

		// Find utm_content
		$utmContent = '';
		if ($blockName === 'core/button')
		{
			$utmContent = trim(wp_strip_all_tags($block['innerHTML']));
		}
		else if ($blockName === 'firebox/button')
		{
			$utmContent = isset($block['attrs']['text']) ? trim(wp_strip_all_tags($block['attrs']['text'])) : '';
		}
		else if ($blockName === 'core/image')
		{
			// Get the src attribute value from $block['innerHTML'] using regex
			$utmContent = preg_match('/src="([^"]*)"/', $block['innerHTML'], $matches) ? $matches[1] : '';
		}
		else if ($blockName === 'firebox/image')
		{
			$utmContent = isset($block['attrs']['image']['desktop']['url']) ? $block['attrs']['image']['desktop']['url'] : '';
		}
		
        // Get the UTM Parameters
		$utmParams = $utmPrefix . 'utm_source=' . get_post_type() . '&utm_medium=' . $blockName . '&utm_campaign=' . get_the_title() . '&utm_content=' . $utmContent;

		// Find the href attribute and append to it the utm params
		$block_content = preg_replace('/href="([^"]*)"/', 'href="$1' . $utmParams . '"', $block_content);

		return $block_content;
	}

	/**
	 * Retrieves the block attributes
	 * 
	 * @param   array  $block
	 * 
	 * @return  array
	 */
	private function getBlockAttributes($block)
	{
		$atts = [
			'utmParamsEnabled' => false
		];

		if (isset($block['attrs']['dataFBoxOnClickURLAddParameters']) && $block['attrs']['dataFBoxOnClickURLAddParameters'])
		{
			$atts['utmParamsEnabled'] = true;
		}

		return $atts;
	}
}