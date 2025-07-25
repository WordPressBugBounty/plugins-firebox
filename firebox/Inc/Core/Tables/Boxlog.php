<?php
/**
 * @package         FireBox
 * @version         3.0.0 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Tables;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Includes\DB;

class Boxlog extends DB
{
	public function __construct()
	{
		$this->table_name  = 'firebox_logs';
	}
	
	/**
	 * Get columns and formats 
	 *
	 * @return  array
	*/
	public function get_columns()
	{
		return [
			'id'		 => '%d',
			'sessionid'	 => '%s',
			'visitorid'	 => '%s',
			'user'		 => '%d',
			'box'		 => '%d',
			'page'		 => '%s',
			'country'    => '%s',
			'device'	 => '%s',
			'referrer'	 => '%s',
			'date'		 => '%s',
		];
	}
}