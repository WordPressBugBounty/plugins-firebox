<?php
/**
 * @package         FireBox
 * @version         3.1.13 Free
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

class Box extends DB
{
	/**
	 * The column list below covers wp_posts in full, so inserts can safely be
	 * restricted to it.
	 *
	 * @var  bool
	 */
	protected $enforce_insert_columns = true;

	public function __construct()
	{
		$this->table_name  = 'posts';
		$this->primary_key = 'ID';
	}

	/**
	 * Get columns and formats
	 *
	 * Keys are lower-cased because DB::insert()/update() normalise incoming data with
	 * array_change_key_case() before intersecting against this list.
	 *
	 * @return  array
	*/
	public function get_columns()
	{
		return array(
			'id'					=> '%d',
			'post_author'			=> '%d',
			'post_date'				=> '%s',
			'post_date_gmt'			=> '%s',
			'post_content'			=> '%s',
			'post_title'			=> '%s',
			'post_excerpt'			=> '%s',
			'post_status'			=> '%s',
			'comment_status'		=> '%s',
			'ping_status'			=> '%s',
			'post_password'			=> '%s',
			'post_name'				=> '%s',
			'to_ping'				=> '%s',
			'pinged'				=> '%s',
			'post_modified'			=> '%s',
			'post_modified_gmt'		=> '%s',
			'post_content_filtered'	=> '%s',
			'post_parent'			=> '%d',
			'guid'					=> '%s',
			'menu_order'			=> '%d',
			'post_type'				=> '%s',
			'post_mime_type'		=> '%s',
			'comment_count'			=> '%d'
		);
	}
}