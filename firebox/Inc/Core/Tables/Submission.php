<?php
/**
 * @package         FireBox
 * @version         3.1.14 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Tables;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Includes\DB;

class Submission extends DB
{
	/**
	 * The column list below covers this table in full, so inserts can safely be
	 * restricted to it.
	 *
	 * @var  bool
	 */
	protected $enforce_insert_columns = true;

	public function __construct()
	{
		$this->table_name  = 'firebox_submissions';
	}
	
	/**
	 * Get columns and formats 
	 *
	 * @return  array
	*/
	public function get_columns()
	{
		return [
			'id'		  => '%d',
			'form_id'	  => '%s',
			'visitor_id'  => '%s',
			'user_id'	  => '%d',
			'state'		  => '%d',
			'created_at'  => '%s',
			'modified_at' => '%s'
		];
	}
}