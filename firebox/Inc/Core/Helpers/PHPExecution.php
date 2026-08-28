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
 * Decides whether a campaign may run server-side PHP: the "PHP" display condition
 * and the Pro "PHP Scripts" hooks. Both execute campaign-authored PHP through
 * \FPFramework\Base\Executer, so they are limited to campaigns whose author holds
 * the firebox_execute_php capability.
 *
 * That capability is separate from the campaign-editing capabilities on purpose:
 * editing a campaign never grants the ability to run PHP. Administrators hold it by
 * default (see Admin\Capabilities); a site owner can grant it to another role
 * deliberately. The check is a stateless function of the post author, so it applies
 * however the meta was saved (block editor, REST, import, or legacy).
 */
class PHPExecution
{
	/**
	 * Whether the given campaign's author may run server-side PHP.
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

		return user_can($author_id, 'firebox_execute_php');
	}

	/**
	 * Whether server-side PHP features are enabled site-wide. Lets a site switch
	 * them off entirely regardless of who authored the campaign.
	 *
	 * @return  bool
	 */
	public static function isFeatureEnabled()
	{
		/**
		 * @param  bool  $enabled
		 */
		return (bool) apply_filters('firebox/php/enabled', true);
	}
}
