<?php
/**
 * @package         FireBox
 * @version         3.0.5 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\API;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class API
{
	private $open_routes = [
		'FireBox',
		
	];
	
	
	
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_open_routes']);

		
    }

	
	
	public function register_open_routes()
	{
		foreach ($this->open_routes as $route)
		{
			$class = '\FireBox\Core\API\Routes\Open\\' . $route;
			$api = new $class();
			$api->register();
		}
	}
	
	
}