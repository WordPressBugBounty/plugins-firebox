<?php
/**
 * @package         FireBox
 * @version         2.1.23 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2024 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Blocks;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Email extends \FireBox\Core\Blocks\Block
{
	/**
	 * Block identifier.
	 * 
	 * @var  string
	 */
	protected $name = 'email';
}