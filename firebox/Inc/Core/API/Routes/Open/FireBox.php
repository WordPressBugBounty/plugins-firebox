<?php
/**
 * @package         FireBox
 * @version         3.1.4
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\API\Routes\Open;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use \FireBox\Core\API\EndpointController;
use WP_REST_Server;

class FireBox extends EndpointController
{
	/**
	 * Endpoint name
	 * 
	 * @return  string
	 */
	public function get_name()
	{
		return '';
	}

	/**
	 * Register routes
	 * 
	 * @return  void
	 */
	public function register()
	{
		$this->register_route('/campaigns', WP_REST_Server::READABLE, [$this, 'get_campaigns']);
		$this->register_route('/embeds', WP_REST_Server::READABLE, [$this, 'get_embeds']);
	}

	public function get_permission_callback($request)
	{
		return current_user_can('read_fireboxes');
	}

	public function get_campaigns()
	{
		$campaigns = \FireBox\Core\Helpers\BoxHelper::getAllBoxes(['publish']);
		$campaigns = $campaigns->posts;

		if (!count($campaigns))
		{
			return [];
		}
		
		$data = [];
		
		foreach ($campaigns as $campaign)
		{
			$data[] = [
				'value' => $campaign->ID,
				'label' => $campaign->post_title
			];
		}
		
		return $data;
	}

	public function get_embeds()
	{
		$boxes = \FireBox\Core\Helpers\BoxHelper::getAllBoxes(['publish']);
		$boxes = $boxes->posts;

		if (!count($boxes))
		{
			return [];
		}
		
		$data = [];
		
		foreach ($boxes as $box)
		{
			$data[] = [
				'value' => $box->ID,
				'label' => $box->post_title
			];
		}
		
		return $data;
	}
}