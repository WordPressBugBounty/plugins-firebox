<?php
/**
 * @package         FireBox
 * @version         3.1.14 Free
 *
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Libs;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

/**
 * Excludes FireBox assets from WP Rocket's JS/CSS optimizations.
 *
 * WP Rocket's "Delay JavaScript Execution" holds every script back until the
 * visitor first interacts with the page, which silently breaks time, scroll,
 * exit-intent and auto-show campaigns. Its minify/combine and "Remove Unused
 * CSS" features strip or rewrite campaign assets the same way. These filters
 * register FireBox's own exclusions so campaigns keep working without the
 * user pasting exclusion patterns into WP Rocket's settings.
 */
class WPRocket
{
	public function __construct()
	{
		if (!defined('WP_ROCKET_VERSION'))
		{
			return;
		}

		add_filter('rocket_delay_js_exclusions', [$this, 'excludeFromDelayJS']);
		add_filter('rocket_exclude_js', [$this, 'excludeFromMinifyJS']);
		add_filter('rocket_exclude_defer_js', [$this, 'excludeFromDeferJS']);
		add_filter('rocket_exclude_css', [$this, 'excludeFromMinifyCSS']);
		add_filter('rocket_rucss_safelist', [$this, 'excludeFromRemoveUnusedCSS']);
	}

	/**
	 * Excludes FireBox scripts from "Delay JavaScript Execution".
	 *
	 * Patterns are matched against the whole script tag, so 'firebox' covers
	 * both the plugin's file URLs and the id attribute WordPress prints on
	 * the firebox-* handles' inline scripts (e.g. firebox-main-js-before).
	 *
	 * @param   array  $excluded
	 *
	 * @return  array
	 */
	public function excludeFromDelayJS($excluded)
	{
		$excluded = (array) $excluded;

		$excluded[] = 'firebox';

		return $excluded;
	}

	/**
	 * Excludes FireBox scripts from JS minification/combining.
	 *
	 * @param   array  $excluded
	 *
	 * @return  array
	 */
	public function excludeFromMinifyJS($excluded)
	{
		return $this->addPublicMediaPattern($excluded, 'js/(.*)');
	}

	/**
	 * Excludes FireBox scripts from "Load JavaScript deferred". The scripts
	 * already register with WordPress' own defer strategy; excluding them
	 * keeps WP Rocket from rewriting the tags a second time.
	 *
	 * @param   array  $excluded
	 *
	 * @return  array
	 */
	public function excludeFromDeferJS($excluded)
	{
		return $this->addPublicMediaPattern($excluded, 'js/(.*)');
	}

	/**
	 * Excludes FireBox stylesheets from CSS minification/combining.
	 *
	 * @param   array  $excluded
	 *
	 * @return  array
	 */
	public function excludeFromMinifyCSS($excluded)
	{
		return $this->addPublicMediaPattern($excluded, 'css/(.*)');
	}

	/**
	 * Safelists FireBox stylesheets for "Remove Unused CSS". Campaigns are
	 * hidden (or not yet in the DOM) when WP Rocket samples the page, so
	 * without this their styles are treated as unused and stripped.
	 *
	 * The pattern must contain a literal '.css' — WP Rocket's SaaS decides
	 * filename-vs-selector by that substring (see WP Rocket's 3.11.0.2
	 * update_safelist_items migration), and '(.*)' matches slashes, so this
	 * one pattern covers the vendor/shortcodes/blocks subdirectories too.
	 *
	 * @param   array  $safelist
	 *
	 * @return  array
	 */
	public function excludeFromRemoveUnusedCSS($safelist)
	{
		return $this->addPublicMediaPattern($safelist, 'css/(.*).css');
	}

	/**
	 * Appends a pattern scoped to the plugin's public media path, e.g.
	 * /wp-content/plugins/firebox/media/public/js/(.*)
	 *
	 * @param   array   $list
	 * @param   string  $pattern
	 *
	 * @return  array
	 */
	private function addPublicMediaPattern($list, $pattern)
	{
		$list = (array) $list;

		$path = wp_parse_url(FBOX_MEDIA_PUBLIC_URL, PHP_URL_PATH);

		// Never fall through to a bare 'js/(.*)' pattern that would exclude
		// every site script from the optimization.
		if (!is_string($path) || $path === '' || $path === '/')
		{
			return $list;
		}

		$list[] = $path . $pattern;

		return $list;
	}
}
