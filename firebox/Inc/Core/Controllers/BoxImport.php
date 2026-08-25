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

namespace FireBox\Core\Controllers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Form;

class BoxImport extends BaseController
{
	/**
	 * The form settings name
	 * 
	 * @var  string
	 */
	const settings_name = 'firebox_import';
	
	/**
	 * Render the page content
	 * 
	 * @return  void
	 */
	public function render()
	{
		// page content
		add_action('firebox/settings_page', [$this, 'settingsPageContent']);
		
		// render layout
		firebox()->renderer->admin->render('pages/settings');
	}

	/**
	 * Import box
	 * 
	 * @param   array  $input
	 * 
	 * @return  void
	 */
	public function processBoxesImport($input)
	{
		// run a quick security check
        if (!check_admin_referer('fpf_form_nonce_firebox_import', 'fpf_form_nonce_firebox_import'))
        {
			return; // get out if we didn't click the Activate button
        }

		/**
		 * Importing creates campaigns from a file the user supplies, so it needs an
		 * explicit capability check of its own. Today options.php also enforces
		 * manage_options for this option group, but this method must not depend on
		 * how it happens to be reached.
		 */
		if (!current_user_can('edit_fireboxes'))
		{
			\FPFramework\Libs\AdminNotice::displayError(fpframework()->_('FPF_CANNOT_VERIFY_REQUEST'));
			return;
		}

		if (!isset($_FILES['file']))
		{
			return;
		}
	
		$file = $_FILES['file'];

		// ensure a file was given
		if (!is_array($file) || !isset($file['name']) || empty($file['name']))
        {
			\FPFramework\Libs\AdminNotice::displayError(fpframework()->_('FPF_PLEASE_SELECT_A_FILE_TO_UPLOAD'));
			return;
		}
		
		$ext = explode('.', $file['name']);
		
		// ensure given file plugin was given
        if (!in_array($ext[count($ext) - 1], ['fbox']))
        {
			\FPFramework\Libs\AdminNotice::displayError(fpframework()->_('FPF_PLEASE_CHOOSE_A_VALID_FILE'));
			return;
		}
		
		$publish_all = isset($input['publish_all']) ? $input['publish_all'] : 0;

		// read file contents
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$data = file_get_contents($file['tmp_name']);
		
		// if empty data file then abort
        if (empty($data))
        {
			\FPFramework\Libs\AdminNotice::displayError(fpframework()->_('FPF_FILE_EMPTY'));
            return;
		}

		$items = json_decode($data, true);

		// Scalar JSON (e.g. "123") is valid JSON but not an export payload
		if (!$items || !is_array($items))
		{
			\FPFramework\Libs\AdminNotice::displayError(firebox()->_('FB_CAMPAIGN_IMPORT_CONTENTS_ERROR'));
            return;
		}

        if (is_null($items))
        {
            $items = [];
        }

		if (!$items)
		{
			return;
		}

		// import all boxes
		if (!$new_box_id = $this->importBoxes($items, $publish_all))
		{
			\FPFramework\Libs\AdminNotice::displayError(firebox()->_('FB_CAMPAIGN_IMPORT_CONTENTS_ERROR'));
            return;
		}

		\FPFramework\Libs\AdminNotice::displaySuccess(fpframework()->_('FPF_ITEMS_SAVED'));
		return $new_box_id;
	}

	/**
	 * Imports boxes data
	 * 
	 * @param   array  $items
	 * @param   int    $publish_all
	 * 
	 * @return  boolean
	 */
	protected function importBoxes($items, $publish_all = 0)
	{
		$success = true;

		foreach ($items as $item)
		{
			// The .fbox contents are user-supplied; validate the shape before
			// using it or a hand-edited file fatals instead of erroring.
			if (!is_array($item) || !isset($item['meta']))
			{
				$success = false;
				break;
			}

			// get meta
			$meta = $item['meta'];

			// remote meta from item
			unset($item['meta']);

			// Campaign settings are free-form, but they are always a map — never a scalar.
			if (!is_array($meta) && !is_object($meta))
			{
				$success = false;
				break;
			}

			if (!isset($item['box']) || !is_array($item['box']))
			{
				$success = false;
				break;
			}

			$box = $item['box'];

			/**
			 * The .fbox file carries a full wp_posts row, and every key in it used to be
			 * written straight to the database. Keep only the columns that actually
			 * describe a campaign; everything else (post_type, post_author, guid,
			 * post_parent, ...) is either forced below or dropped.
			 */
			$box = array_intersect_key($box, array_flip([
				'post_title',
				'post_content',
				'post_excerpt',
				'post_name',
				'menu_order'
			]));

			if (!isset($box['post_content']) || !is_string($box['post_content']))
			{
				$box['post_content'] = '';
			}

			$box['post_title'] = isset($box['post_title']) && is_string($box['post_title'])
				? sanitize_text_field($box['post_title'])
				: '';

			$box['post_excerpt'] = isset($box['post_excerpt']) && is_string($box['post_excerpt'])
				? sanitize_textarea_field($box['post_excerpt'])
				: '';

			$box['post_name'] = isset($box['post_name']) && is_string($box['post_name'])
				? sanitize_title($box['post_name'])
				: '';

			$box['menu_order'] = isset($box['menu_order']) ? (int) $box['menu_order'] : 0;

            $factory = new \FPFramework\Base\Factory();

			$tz = wp_timezone();
			$date_without_tz = $factory->getDate();
			$date_with_tz = $factory->getDate()->setTimezone($tz);

			$box['post_date'] = $date_with_tz->format('Y-m-d H:i:s');
			$box['post_date_gmt'] = $date_without_tz->format('Y-m-d H:i:s');

			// These are ours to decide, never the file's.
			$box['post_type'] = 'firebox';
			$box['post_author'] = get_current_user_id();

			\FireBox\Core\Helpers\Form\Form::ensureUniqueFormIDs($box['post_content']);

			// set publish status
            if (in_array($publish_all, [0, 1]))
            {
                $box['post_status'] = ($publish_all == 0) ? 'draft' : 'publish';
			}
			else
			{
				$box['post_status'] = 'draft';
			}

			/**
			 * Insert through wp_insert_post() rather than the raw table layer so that
			 * kses, slug uniqueness and the usual save_post hooks all apply.
			 */
			$new_box_id = wp_insert_post(wp_slash($box), true);

			if (is_wp_error($new_box_id) || !$new_box_id)
			{
				$success = false;
				break;
			}

			// add meta options for new box
			// TODO: In the future, use "firebox_meta". This is a temporary fix for backwards compatibility.
			$checkMeta = (array) $meta;
			$meta_key = isset($checkMeta['width']) ? 'firebox_meta' : 'fpframework_meta_settings';
			update_post_meta($new_box_id, $meta_key, wp_slash($meta));
			$success = $new_box_id;
		}

		return $success;
	}

	/**
	 * What the settings page will contain
	 * 
	 * @return  void
	 */
	public function settingsPageContent()
	{
		$form = new Form(\FireBox\Core\Admin\Forms\Import::getSettings(), [
			'fields_name_prefix' => self::settings_name,
			'section_name' => self::settings_name,
			'class' => 'settings-ui-inner-fields',
			'button_label' => 'FPF_IMPORT'
		]);
		
		echo $form->render(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}