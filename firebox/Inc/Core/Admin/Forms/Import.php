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

namespace FireBox\Core\Admin\Forms;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Import
{
	/**
	 * All Settings for the FireBox Import Page
	 * 
	 * @return  array
	 */
	public static function getSettings()
	{
		$settings = [
			'data' => [
				'import' => [
					'title' => fpframework()->_('FPF_IMPORT'),
					'content' => [
						'import' => [
							'wrapper' => [
								'class' => ['grid-x', 'grid-margin-y']
							],
							'fields' => [
								[
									'type' => 'Heading',
									'title' => firebox()->_('FB_IMPORT_CAMPAIGNS')
								],
								[
									'name' => 'file',
									'name_clean' => true,
									'type' => 'File',
									'label' => fpframework()->_('FPF_SELECT_IMPORT_FILE')
								],
								[
									'name' => 'publish_all',
									'type' => 'Dropdown',
									'label' => firebox()->_('FB_PUBLISH_CAMPAIGNS'),
									'default' => 2,
									'choices' => [
										0 => fpframework()->_('FPF_NO'),
										1 => fpframework()->_('FPF_YES'),
										2 => fpframework()->_('FPF_AS_EXPORTED')
									]
								]
							]
						]
					]
				]
			]
		];

		return apply_filters('firebox/forms/import/edit', $settings);
	}
}