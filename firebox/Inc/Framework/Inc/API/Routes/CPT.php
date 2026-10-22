<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.7
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\API\Routes;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use WP_REST_Server;
use \FPFramework\API\EndpointController;

class CPT extends EndpointController
{
	/**
	 * Endpoint name
	 * 
	 * @return  string
	 */
	public function get_name()
	{
		return 'cpt';
	}

	/**
	 * Register routes
	 * 
	 * @return  void
	 */
	public function register()
	{
		$base_endpoint = '(?:\/(?P<ID>\d+))?';

		// update post status
		$this->register_route($base_endpoint . '/draft', WP_REST_Server::CREATABLE, [$this, 'update_post_status_draft']);
		$this->register_route($base_endpoint . '/publish', WP_REST_Server::CREATABLE, [$this, 'update_post_status_publish']);
	}

	/**
	 * Permissions callback to validate request
	 * 
	 * @param   object   $request
	 * 
	 * @return  boolean
	 */
	public function get_permission_callback($request)
	{
		$post_id = (int) $request->get_param('ID');

		if (!$post_id)
		{
			return false;
		}

		$post = get_post($post_id);

		if (is_null($post))
		{
			return false;
		}

		/**
		 * This route flips the status of the campaign list's toggle, nothing else.
		 * Without the post type restriction it would let any FireBox user draft or
		 * publish arbitrary posts of any type site-wide, and without the per-post
		 * capability check any read-level user could modify campaigns they cannot
		 * edit. edit_post resolves through map_meta_cap to the CPT's own
		 * capabilities (edit_firebox / edit_others_fireboxes / ...).
		 */
		if ($post->post_type !== 'firebox')
		{
			return false;
		}

		if (!current_user_can('edit_post', $post_id))
		{
			return false;
		}

		return true;
	}

	/**
	 * Set Post Status to Draft
	 * 
	 * @param   object  $request
	 * 
	 * @return  string
	 */
	public function update_post_status_draft($request)
	{
		$post_id = $request->get_param('ID');

		$post = ['ID' => $post_id, 'post_status' => 'draft'];
		wp_update_post($post);

		wp_send_json_success([
			'message' => 'Post status updated to Draft',
			'post_status' => 'draft',
			'post_id' => $post_id
		]);
	}

	/**
	 * Set Post Status to Published
	 * 
	 * @param   object  $request
	 * 
	 * @return  string
	 */
	public function update_post_status_publish($request)
	{
		$post_id = $request->get_param('ID');

		$post = ['ID' => $post_id, 'post_status' => 'publish'];
		wp_update_post($post);

		wp_send_json_success([
			'message' => 'Post status updated to Publish',
			'post_status' => 'publish',
			'post_id' => $post_id
		]);
	}
}