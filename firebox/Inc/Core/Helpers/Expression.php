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

namespace FireBox\Core\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

/**
 * Gatekeeper for the {fbExpr ...} shortcode, which evaluates JavaScript from campaign
 * content in the visitor's browser.
 *
 * "{fbExpr ...}" is plain text, so kses passes it through untouched — it is the one way a
 * user without unfiltered_html could smuggle executable JavaScript into a campaign. The
 * feature is therefore limited to campaigns whose author holds that capability, which is
 * the same bar WordPress applies to writing raw script anywhere else.
 *
 * The check is deliberately a plain function of the post author, with no stored state:
 * an earlier version recorded the decision at save time and that machinery twice grew
 * bugs of its own. The trade is documented — see docs/audits/security-audit.md F10.
 */
class Expression
{
	/**
	 * Marker that switches the feature on for a campaign.
	 *
	 * @var  string
	 */
	const SHORTCODE_PREFIX = '{fbExpr';

	/**
	 * Returns whether the given campaign may run expressions.
	 *
	 * @param   int     $post_id
	 * @param   string  $content
	 *
	 * @return  bool
	 */
	public static function isAllowedForCampaign($post_id, $content = '')
	{
		// Nothing to run.
		if (!is_string($content) || strpos($content, self::SHORTCODE_PREFIX) === false)
		{
			return false;
		}

		if (!self::isFeatureEnabled())
		{
			return false;
		}

		$post_id = (int) $post_id;

		if (!$post_id)
		{
			return false;
		}

		$author_id = (int) get_post_field('post_author', $post_id);

		if (!$author_id)
		{
			return false;
		}

		return user_can($author_id, 'unfiltered_html');
	}

	/**
	 * Whether the feature is enabled site-wide.
	 *
	 * @return  bool
	 */
	public static function isFeatureEnabled()
	{
		/**
		 * Allows a site to switch off JavaScript expressions entirely, regardless of who
		 * authored the campaign.
		 *
		 * @param  bool  $enabled
		 */
		return (bool) apply_filters('firebox/expressions/enabled', true);
	}
}
