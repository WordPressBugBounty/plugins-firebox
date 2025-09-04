<?php
/**
 * @package         FireBox
 * @version         3.0.4 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\FB\Styling;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Libs\Registry;
use FPFramework\Helpers\Fields\DimensionsHelper;
use FPFramework\Helpers\CSS as CSSHelper;

class CSS
{
	/**
	 * Breakpoints.
	 * 
	 * @var  array
	 */
	private $breakpoints = [
		'desktop',
		'tablet',
		'mobile'
	];
	
	/**
	 * CSS per breakpoint.
	 * 
	 * @var  array
	 */
	private $responsive_css = [
		'desktop' => [],
		'tablet' => [],
		'mobile' => []
	];

	/**
	 * The box we are generating CSS for.
	 * 
	 * @var  Object
	 */
	private $box;
	
	public function __construct($box)
	{
		$this->box = $box;

		$this->setInstanceCSS();
		$this->setDialogCSS();
		$this->setCloseButtonCSS();
		$this->setFloatingButtonCSS();
	}

	/**
	 * Returns all popup CSS.
	 * 
	 * @return  string
	 */
	public function getCSS()
	{
		$css = '';

		foreach ($this->breakpoints as $breakpoint)
		{
			$finalcss = $this->getBreakpointCSS($breakpoint);

			// Update $responsive_css with final CSS
			$this->responsive_css[$breakpoint] =  $finalcss;

			$css .= $finalcss;
		}

		return $css;
	}

	/**
	 * Sets the instance CSS.
	 * 
	 * @return  void
	 */
	private function setInstanceCSS()
	{
		$style = [];

		// Z-index
		$zindex = $this->box->params->get('zindex', 99999) != 99999 ? $this->box->params->get('zindex') : null;
		if (intval($zindex))
		{
			$style['z-index'] = $zindex;
		}

		// Overlay blur
		$overlay_enabled = (bool) $this->box->params->get('overlay');
		if ($overlay_enabled && $radius = $this->box->params->get('overlayblurradius', 0))
		{
			$style['backdrop-filter'] = 'blur(' . $radius . 'px)';
			$style['-webkit-backdrop-filter'] = 'blur(' . $radius . 'px)';
		}

		// Add CSS (non-responsive) to desktop breakpoint
		$this->addCSS(CSSHelper::arrayToCSS($style), 'desktop', '.fb-inst');

		$css_properties = [
			'margin' => [
				'prefix' => 'padding'
			],
		];
		$this->parseCSSProperties($css_properties, '.fb-inst');
	}

	/**
	 * Sets the dialog CSS.
	 * 
	 * @return  void
	 */
	private function setDialogCSS()
	{
		// Dialog CSS
        $style = [
            'color' => $this->box->params->get('textcolor'),
		];

		$animation_duration = $this->box->params->get('animation_duration') ? (float) $this->box->params->get('animation_duration') : 0;
		$style['animation-duration'] = (float) $animation_duration . 's';

		// Border
		$border_width = $this->box->params->get('border.width', 0) ? trim($this->box->params->get('border.width', 0)) : 0;
		$border_style = is_string($this->box->params->get('border.style', 0)) ? trim($this->box->params->get('border.style', 0)) : '';
		$border_color = is_string($this->box->params->get('border.color')) ? trim($this->box->params->get('border.color')) : '';

		if (!empty($border_width) && !empty($border_style) && !empty($border_color))
		{
			$style['border'] = implode(' ' , [$border_width, $border_style, $border_color]);
		}

		// Background
		$background = $this->box->params->get('backgroundcolor');
		if (!empty($background))
		{
			$style['background'] = $background;
		}

        // Background Image
        if ($this->box->params->get('bgimage', false))
        {
			$bgrepeat = is_string($this->box->params->get('bgrepeat')) ? $this->box->params->get('bgrepeat') : '';
			$bgsize = is_string($this->box->params->get('bgsize')) ? $this->box->params->get('bgsize') : '';
			$bgposition = is_string($this->box->params->get('bgposition')) ? $this->box->params->get('bgposition') : '';
		
			$style['background-image']  = 'url(\'' . esc_url($this->box->params->get('bgimagefile', '')) . '\')';
			$style['background-repeat'] = strtolower($bgrepeat);
			$style['background-size'] = strtolower($bgsize);
			$style['background-position'] = strtolower($bgposition);
		}

		// Add CSS (non-responsive) to desktop breakpoint
		$this->addCSS(CSSHelper::arrayToCSS($style), 'desktop', ' .fb-dialog');

		$css_properties = [
			'width' => [],
			'height' => [],
			'padding' => [],
			'borderradius' => [
				'prefix' => 'border-radius',
				'parser' => 'parseDimensionsBorderRadiusData'
			],

			// Deprecated
			'fontsize' => [
				'prefix' => 'font-size'
			],
		];
		$this->parseCSSProperties($css_properties, ' .fb-dialog');
	}

	/**
	 * Set close button CSS.
	 * 
	 * @return  void
	 */
	private function setCloseButtonCSS()
	{
		// Delay the display of the close button using CSS animation
		$delay = (float) $this->box->params->get('closebutton.delay', 0);
		if ($delay > 0)
		{
			$style = [
				'visibility' => 'hidden'
			];
			$this->addCSS(CSSHelper::arrayToCSS($style), 'desktop', ' .fb-close');

			$style = [
				'animation' => $delay . 's ebFadeIn',
				'animation-fill-mode' => 'forwards'
			];
			$this->addCSS(CSSHelper::arrayToCSS($style), 'desktop', '.fb-visible .fb-close');
		}

		// Close Button Initial color
		$btnSource = is_string($this->box->params->get('closebutton.source', 'icon')) ? $this->box->params->get('closebutton.source', 'icon') : 'icon';
		$color = is_string($this->box->params->get('closebutton.color', null)) ? $this->box->params->get('closebutton.color', null) : null;
		if ($btnSource == 'icon' && !empty($color))
		{
			$style = [
				'color' => $color
			];
			$this->addCSS(CSSHelper::arrayToCSS($style), 'desktop', ' .fb-close');
		}
		
		// Add the hover color
		if ($hoverColor = $this->box->params->get('closebutton.hover', null))
		{
			$style = [
				'color' => esc_attr($hoverColor) . ' !important'
			];
			$this->addCSS(CSSHelper::arrayToCSS($style), 'desktop', ' .fb-close:hover');
		}
	}

	private function setFloatingButtonCSS()
	{
		$css_properties = [
			'margin' => [
				'prefix' => 'padding'
			],
		];
		$this->parseCSSProperties($css_properties, '.fb-floating-button');
	}

	/**
	 * Parses the given CSS properties and adds the CSS to the list.
	 * 
	 * @param   array    $props
	 * @param   string   $selector
	 * 
	 * @return  void
	 */
	private function parseCSSProperties($props = [], $selector = '')
	{
		foreach ($props as $prop => $prop_data)
		{
			$value = (array) $this->box->params->get($prop, []);

			foreach ($this->breakpoints as $breakpoint)
			{
				if (!isset($value[$breakpoint]))
				{
					continue;
				}

				$prefix = isset($prop_data['prefix']) ? $prop_data['prefix'] : $prop;
				
				$parserMethod = isset($prop_data['parser']) ? $prop_data['parser'] : 'parseDimensionsData';

				$_value = DimensionsHelper::$parserMethod($value[$breakpoint], $prefix);

				if (empty($_value))
				{
					continue;
				}
				
				$breakpoint_value = CSSHelper::arrayToCSS($_value);

				$this->addCSS($breakpoint_value, $breakpoint, $selector);
			}
		}
	}

	/**
	 * Add CSS to given breakpoint and selector.
	 * 
	 * @param   string  $css
	 * @param   string  $breakpoint
	 * @param   string  $selector
	 * 
	 * @return  void
	 */
	public function addCSS($css = '', $breakpoint = 'desktop', $selector = '')
	{
		if (empty($css))
		{
			return;
		}
		
		// No selector, append to breakpoint as new item
		if (empty($selector))
		{
			$this->responsive_css[$breakpoint][] = $css;
			return;
		}

		// With selector, if first breakpoint item, set it
		if (empty($this->responsive_css[$breakpoint][$selector]))
		{
			$this->responsive_css[$breakpoint][$selector] = $css;
		}
		// Otherwise, append to existing CSS
		else
		{
			$this->responsive_css[$breakpoint][$selector] .= ' ' . $css;
		}
	}

	/**
	 * Returns the CSS for a given breakpoint.
	 * 
	 * @param   string  $breakpoint
	 * 
	 * @return  string
	 */
	private function getBreakpointCSS($breakpoint = 'desktop')
	{
		if (!isset($this->responsive_css[$breakpoint]))
		{
			return;
		}
		
		$skeletonFunctionName = 'get' . ucfirst($breakpoint) . 'Skeleton';
		if (!method_exists($this, $skeletonFunctionName))
		{
			return;
		}

		$skeleton = $this->$skeletonFunctionName();

		/**
		 * This array holds all CSS grouped by selector.
		 * 
		 * The empty key,value pair indicates that the CSS does not have a custom selector
		 */
		$data = [
			// Global CSS
			'' => ''
		];

		// Group CSS by selector
		foreach ($this->responsive_css[$breakpoint] as $selector => $value)
		{
			$selector = is_string($selector) && $selector !== '' ? $selector : '';

			if (empty($selector))
			{
				$data[''] .= $value;
			}
			else
			{
				$data[$selector] = $value;
			}
		}
		
		// Get final CSS for the breakpoint
		$cssvalue = '';
		foreach ($data as $selector => $value)
		{
			if (empty($value))
			{
				continue;
			}

			$selectorSkeleton = $this->getSelector($selector);

			$cssvalue .= sprintf($selectorSkeleton, $value);
		}

		// Return breakpoint with final CSS
		return sprintf($skeleton, $cssvalue);
	}

	/**
	 * Returns the CSS Selector.
	 * 
	 * @return  string
	 */
	private function getSelector($selector = ' .fb-dialog')
	{
		return '.fb-' . esc_attr($this->box->ID . $selector) . ' { %s }';
	}

	/**
	 * Desktop CSS Skeleton.
	 * 
	 * @return  string
	 */
	private function getDesktopSkeleton()
	{
		return '%s';
	}

	/**
	 * Tablet CSS Skeleton.
	 * 
	 * @return  string
	 */
	private function getTabletSkeleton()
	{
		return '@media only screen and (max-width: 991px) { %s }';
	}

	/**
	 * Mobile CSS Skeleton.
	 * 
	 * @return  string
	 */
	private function getMobileSkeleton()
	{
		return '@media only screen and (max-width: 575px) { %s }';
	}

	public function getResponsiveCSS()
	{
		return $this->responsive_css;
	}
}