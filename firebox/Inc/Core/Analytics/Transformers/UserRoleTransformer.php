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

namespace FireBox\Core\Analytics\Transformers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

/**
 * Example: Custom transformer for user roles
 * Shows how easy it is to extend the system
 */
class UserRoleTransformer implements TransformerInterface
{
	public function shouldApply($type, $options = [])
	{
		return $type === 'user_roles';
	}

	public function transform(&$data, $type, $options = [])
	{
		global $wp_roles;
		
		foreach ($data as &$item) {
			$role_name = isset($wp_roles->role_names[$item->label]) 
				? translate_user_role($wp_roles->role_names[$item->label])
				: $item->label;
			
			$item->original_role = $item->label;
			$item->label = $role_name;
		}
	}

	public function getPriority()
	{
		return 50;
	}
}
