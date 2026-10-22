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

namespace FPFramework\API;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class API
{
	public function __construct()
	{
        add_action( 'rest_api_init', [$this, 'register_routes'] );
	}

	/**
	 * Register all found routes
	 * 
	 * @return  void
	 */
	public function register_routes()
	{
		// explicit route list — avoids a scandir() on every rest_api_init request
		$routes = [
			'CPT'
		];

		foreach ($routes as $route)
		{
			$class = '\FPFramework\API\Routes\\' . $route;
			$api = new $class();
			$api->register();
		}
	}
}