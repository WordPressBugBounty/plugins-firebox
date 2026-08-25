<?php
/**
 * @package         FireBox
 * @version         3.1.11 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Admin;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Capabilities
{
	public function __construct()
	{
		$this->setup();
	}

	public static function getCapabilities()
	{
		return [
			'edit_firebox',
			'read_firebox',
			'delete_firebox',
			'edit_fireboxes',
			'edit_others_fireboxes',
			'publish_fireboxes',
			'read_private_fireboxes',
			'read_fireboxes',
			'delete_fireboxes',
			'delete_private_fireboxes',
			'delete_published_fireboxes',
			'delete_others_fireboxes',
			'edit_private_fireboxes',
			'edit_published_fireboxes',

			// Standalone permission to run campaign-authored PHP (display condition and
			// PHP Scripts). Deliberately NOT part of getPostTypeCapabilities(), so editing
			// campaigns never grants it implicitly — only administrators get it by default,
			// and a site owner can grant it to another role on purpose.
			'firebox_execute_php',

			// Standalone permission to emit campaign-authored JavaScript (the Custom
			// JavaScript setting and the "Run Javascript" action). unfiltered_html also
			// satisfies that gate; this capability exists so administrators keep custom JS
			// where WordPress withholds unfiltered_html (multisite site admins,
			// DISALLOW_UNFILTERED_HTML hosts), and so a site owner can grant it to a
			// trusted role deliberately. Same rules as firebox_execute_php otherwise.
			'firebox_execute_js'
		];
	}

	private function setup()
	{
		$capabilities = self::getCapabilities();

		$admin = get_role('administrator');

		if ($admin)
		{
			self::addCapabilities($admin, $capabilities);
		}
		else
		{
			$roles = get_editable_roles();

			foreach ($roles as $role_name => $data)
			{
				if (isset($data['capabilities']['manage_options']) && $data['capabilities']['manage_options'])
				{
					$role = get_role($role_name);

					if ($role)
					{
						self::addCapabilities($role, $capabilities);
					}
				}
			}
		}

        wp_get_current_user()->get_role_caps();
	}

	/**
	 * Adds the capabilities a role is missing.
	 *
	 * add_cap() writes the role option on every call, and this now runs on
	 * every site of a network, so only the missing ones are added.
	 *
	 * @param   \WP_Role  $role
	 * @param   array     $capabilities
	 *
	 * @return  void
	 */
	private static function addCapabilities($role, $capabilities)
	{
		foreach ($capabilities as $cap)
		{
			if ($role->has_cap($cap))
			{
				continue;
			}

			$role->add_cap($cap);
		}
	}
}