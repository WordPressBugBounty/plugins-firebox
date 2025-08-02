<?php
/**
 * @package         FireBox
 * @version         3.0.2 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Admin\Includes;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Library extends \FPFramework\Admin\Library\Library
{
	public function __construct()
	{
		parent::__construct($this->getLibrarySettings());

		add_action('current_screen', [$this, 'validate']);
	}

	/**
	 * Returns the library settings.
	 * 
	 * @return  array
	 */
	private function getLibrarySettings()
	{
		return [
			'id' => 'fbSelectTemplate',
			'title' => firebox()->_('FB_CAMPAIGN_LIBRARY'),
			'create_new_template_link' => admin_url('post-new.php?post_type=firebox'),
			'main_category_label' => firebox()->_('FB_CAMPAIGN_TYPE'),
			'plugin_license_settings_url' => admin_url('admin.php?page=firebox-settings#license_key'),
			'plugin_dir' => FBOX_PLUGIN_DIR,
			'plugin' => 'firebox',
			'plugin_version' => FBOX_VERSION,
			'plugin_license_type' => FBOX_LICENSE_TYPE,
			'plugin_name' => firebox()->_('FB_PLUGIN_NAME'),
			
			'license_key' => false,
			'license_key_status' => false,
			
			
			'blank_template_label' => fpframework()->_('FPF_BLANK_TEMPLATE'),
			'template_use_url' => 'post.php?action=edit&post='
		];
	}

	/**
	 * Old function used to find templates.
	 * 
	 * @param   string  $template
	 * 
	 * @deprecated  1.1.0
	 * 
	 * @return  null
	 */
	public function find($template = '')
	{
		return;
	}

	/**
	 * Runs only on specific FireBox pages
	 * 
	 * @return  void
	 */
	public function validate()
	{
		$current_screen = get_current_screen();

		$allowed_pages = [
			'toplevel_page_firebox',
			'firebox_page_firebox-analytics',
			'firebox_page_firebox-campaigns',
			'firebox_page_firebox-submissions',
			'firebox_page_firebox-settings',
			'edit-firebox'
		];

		if (!in_array($current_screen->id, $allowed_pages))
		{
			return false;
		}

		$this->init();
	}
}