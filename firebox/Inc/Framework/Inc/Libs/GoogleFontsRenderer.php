<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.2
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Libs;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class GoogleFontsRenderer
{
    /**
     * Filter Instance
     * 
     * @var  Filter
     */
    public static $instance;
    
	/**
	 * All Google Fonts that should load on the current page.
	 * 
	 * @var  array
	 */
	protected static $fonts = [];
	
	public static function getInstance()
	{
		if (!is_object(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
    }

	/**
	 * Initializes events and renders given Google Fonts.
	 * 
	 * @return  void
	 */
	public function render()
	{
		add_action('wp_head', [$this, 'frontend_google_fonts'], 90);
	}

	/**
	 * Checks and renders all Google Fonts.
	 * 
	 * @return  void
	 */
	public function frontend_google_fonts()
	{
		if (empty(self::$fonts))
		{
			return;
		}
		
		$this->render_google_fonts();
	}

	/**
	 * Renderes all Google Fonts
	 * 
	 * @return  void
	 */
	public function render_google_fonts()
	{
		$link    = '';
		$subsets = [];

		foreach (self::$fonts as $key => $value)
		{
			// Append a new font
			if (!empty($link))
			{
				$link .= '%7C';
			}

			$link .= $value['fontfamily'];

			if (!empty($value['fontvariants']))
			{
				// Ensure unique font variants
				$value['fontvariants'] = array_filter($value['fontvariants']);

				$variants = array_map(function($variant)
				{
					$italic = (false !== strpos($variant, 'italic'));
					$weight = preg_replace('/[^0-9]/', '', $variant);
					$weight = $weight ? $weight : '400';

					return ($italic ? '1' : '0') . ',' . $weight;
				}, $value['fontvariants']);

				sort($variants);

				$link .= ':ital,wght@';
				$link .= implode(';', $variants);
			}

			if (!empty($value['fontsubsets']))
			{
				foreach ($value['fontsubsets'] as $subset)
				{
					if (!empty($subset) && !in_array($subset, $subsets))
					{
						array_push($subsets, $subset);
					}
				}
			}
		}

		if (!empty($subsets))
		{
			$link .= '&amp;subset=' . implode(',', $subsets);
		}

		if (apply_filters('fpframework_blocks_google_font_display_swap', true))
		{
			$link .= '&amp;display=swap';
		}

		$href = 'https://fonts.googleapis.com/css2?family=' . esc_attr(str_replace('|', '%7C', $link));

		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
		echo '<link rel="stylesheet" href="' . $href . '" media="print" onload="this.media=\'all\'">';
		echo '<noscript><link rel="stylesheet" href="' . $href . '"></noscript>';
	}

	/**
	 * Add font to the list.
	 * 
	 * @param   string  $fontFamily
	 * @param   array   $font
	 * 
	 * @return  mixed
	 */
	public function addFont($fontFamily, $font)
	{
		if (array_key_exists($fontFamily, self::$fonts))
		{
			// Add font variants
			if (isset($font['fontvariants']) && is_array($font['fontvariants']))
			{
				foreach ($font['fontvariants'] as $variant)
				{
					if (in_array($variant, self::$fonts[$fontFamily]['fontvariants']))
					{
						continue;
					}

					self::$fonts[$fontFamily]['fontvariants'][] = $variant;
				}
			}
			return;
		}
		
		self::$fonts[$fontFamily] = $font;
	}
}