<?php
/**
 * @package         FireBox
 * @version         3.1.4 Free
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

class Boxlogdetails extends DB
{
	public function __construct()
	{
		$this->table_name  = 'firebox_logs_details';
	}
	
	/**
	 * Get columns and formats 
	 *
	 * @return  array
	*/
	public function get_columns()
	{
		return [
			'id'		    => '%d',
			'log_id'	    => '%s',
			'event'		    => '%s',
			'event_source'  => '%s',
			'event_label'   => '%s',
			'date'		    => '%s',
		];
	}
}