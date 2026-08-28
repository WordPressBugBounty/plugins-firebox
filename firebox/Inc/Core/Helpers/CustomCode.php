<?php
/**
 * @package         FireBox
 * @version         3.1.12 Free
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
 * Gatekeeper for campaign-authored JavaScript: the "Custom JavaScript" setting and the
 * "Run Javascript" campaign action. Both are stored in the campaign's meta — which any
 * edit_fireboxes user can write, through the block editor, REST or import — and are
 * emitted verbatim into every visitor's page. That is raw script, so it is limited to
 * campaigns whose author holds unfiltered_html — the bar WordPress applies to writing
 * script anywhere else (and the bar {fbExpr} already enforces) — or the dedicated
 * firebox_execute_js capability. The dedicated capability keeps custom JS working for
 * administrators where WordPress withholds unfiltered_html (multisite site admins,
 * DISALLOW_UNFILTERED_HTML hosts) and lets a site owner grant the permission to a
 * trusted role deliberately; it is granted to administrators by default (Capabilities
 * on install, Migrator on upgrade) and, like firebox_execute_php, is never conferred
 * by campaign editing.
 *
 * Like Expression and PHPExecution, the check is a stateless function of the post
 * author, so it applies however the meta was saved.
 */
class CustomCode
{
	/**
	 * Whether the given campaign's author may emit custom JavaScript.
	 *
	 * @param   int  $post_id  The campaign (firebox) post ID.
	 *
	 * @return  bool
	 */
	public static function isAllowedForCampaign($post_id)
	{
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

		return user_can($author_id, 'unfiltered_html') || user_can($author_id, 'firebox_execute_js');
	}

	/**
	 * Whether custom JavaScript is enabled site-wide. Lets a site switch it off
	 * entirely regardless of who authored the campaign.
	 *
	 * @return  bool
	 */
	public static function isFeatureEnabled()
	{
		/**
		 * @param  bool  $enabled
		 */
		return (bool) apply_filters('firebox/customcode/enabled', true);
	}
}
