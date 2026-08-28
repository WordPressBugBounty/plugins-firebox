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

namespace FireBox\Core\FB;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Libs\Registry;
use FireBox\Core\Helpers\BoxHelper;

class Boxes
{
	/**
	 * Render boxes
	 * 
	 * @return  string
	 */
	public function render()
	{
		if (!$boxes = BoxHelper::getAllBoxes())
		{
			return;
		}

		// Session-dependent conditions (Pageviews, Time on Site) are stamped and evaluated
		// in the browser, so the stamper must run on every page — not just pages where a
		// campaign renders — for the pageview counter and session start to stay accurate.
		//
		// On pages where a campaign does render, the full runtime already contains the
		// stamper, so only the standalone ~1 KB script is needed elsewhere. The priority
		// runs late, after the per-box enqueues added from Box::render().
		if (BoxHelper::sessionConditionsInUse($boxes))
		{
			add_action('wp_enqueue_scripts', function()
			{
				if (!wp_script_is('firebox-main', 'enqueued'))
				{
					wp_enqueue_script('firebox-session-stamp');
				}
			}, 100);
		}

		return $this->renderAll($boxes);
	}
	
	/**
	 * Renders all boxes
	 * 
	 * @param   array  $boxes
	 * 
	 * @return  string
	 */
	public function renderAll($boxes)
	{
		$html = '';

		if ($boxes instanceof \WP_Query)
		{
			if (!$boxes->have_posts())
			{
				return;
			}
			
			// Backup global state
			global $post, $authordata;
			$originalPost = $post;
			$originalAuthorData = $authordata;
			
			// Setup the post data for our custom query
			while ($boxes->have_posts())
			{
				$boxes->the_post();

				global $post;

				$this->prepare($post);
				
				// If the mode is embed, abort.
				if ($post->params->get('mode') == 'embed')
				{
					continue;
				}

				$html .= $this->get_output($post);
			}

			// Reset post data
			wp_reset_postdata();
			
			// Restore global state
			$post         = $originalPost;
			$authordata   = $originalAuthorData;
		}
		else if (is_array($boxes))
		{
			foreach ($boxes as $box)
			{
				$this->prepare($box);
				
				// If the mode is embed, abort.
				if ($box->params->get('mode') == 'embed')
				{
					continue;
				}

				$html .= $this->get_output($box);
			}
		}
		
		return $html;
	}

	/**
	 * Prepares the box by setting its meta settings.
	 * 
	 * @param   object  $box
	 * 
	 * @return  void
	 */
	private function prepare(&$box)
	{
		$meta = BoxHelper::getMeta($box->ID);
		$box->params = new Registry($meta);
	}

	/**
	 * Returns the box output
	 * 
	 * @param   object  $box
	 * 
	 * @return  string
	 */
	private function get_output($box)
	{
		$logged_and_admin = (is_user_logged_in() && current_user_can('edit_fireboxes'));

		$testmode = $box->params->get('testmode', false);
		
		// if box is in test mode only logged-in admin users can see it.
		if ($testmode == '1' && !$logged_and_admin)
		{
			return '';
		}
		
		return firebox()->box->setBox($box)->render();
	}
}