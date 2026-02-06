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

namespace FireBox\Core\API;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use WP_REST_Server;
use FPFramework\API\Endpoint;

abstract class EndpointController extends Endpoint
{
	/**
	 * Get API namespace
	 * 
	 * @return  string
	 */
	public function get_namespace()
	{
		return Manager::ROOT_NAMESPACE . '/v' . Manager::VERSION;
	}

	/**
	 * Get API REST base
	 * 
	 * @return  string
	 */
	public function get_rest_base()
	{
		return Manager::REST_BASE;
	}
}