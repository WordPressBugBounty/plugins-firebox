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

namespace FireBox\Core\Admin\Includes\Cpts;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FireBox\Core\Helpers\BoxHelper;

class Firebox
{
	public $singular;
	public $plural;
	
	public function __construct()
	{
		$this->singular = 'FireBox';
		$this->plural = firebox()->_('FB_FIREBOX_CAMPAIGNS');
		
		register_post_type('firebox', $this->getPayload());

		$this->init();
	}

	public function init()
	{
		add_action('admin_init', [$this, 'custom_post_edit_redirect']);
		
		// set default post title
		add_filter('default_title', [$this, 'set_default_campaign_title'], 10, 2);

		// handle box duplication
		add_action('admin_action_fb_duplicate_post_as_draft', [$this, 'handle_box_duplication']);

		// handle box cookie clear
		add_action('admin_action_fb_clear_cookie', [$this, 'handle_box_cookie_clear']);
		
		// delete hook for FireBox
		add_action('delete_post', [$this, 'before_delete_firebox_callback']);

		add_action('load-firebox_page_firebox-campaigns', [$this, 'handle_bulk_actions']);

		add_filter('save_post', [$this, 'after_save'], 10, 3);

		// Exclude FireBox from sitemaps
		add_filter('wp_sitemaps_post_types', [$this, 'remove_post_type_from_wp_sitemap']);
		add_filter('wpseo_sitemap_exclude_post_type', [$this, 'yoastseo_exclude_post_type'], 10, 2);

		// Add noindex meta tag for single FireBox pages
		add_filter('wp_robots', [$this, 'add_noindex_to_single_firebox']);
	}

	/**
	 * Exclude FireBox from Yoast SEO sitemaps
	 * 
	 * @param   bool    $value
	 * @param   string  $post_type
	 * 
	 * @return  bool
	 */
	public function yoastseo_exclude_post_type($value, $post_type)
	{
	    $post_type_to_exclude = [
			'firebox'
		];

	    if (in_array($post_type, $post_type_to_exclude))
		{
	        $value = true;
	    }

		return $value;
	}

	/**
	 * Remove the FireBox custom post type from the sitemap.
	 * 
	 * This is not needed as published FireBox campaigns are not public.
	 * 
	 * @param   array  $cpts
	 * 
	 * @return  array
	 */
	public function remove_post_type_from_wp_sitemap($cpts)
	{
		if (isset($cpts['firebox']))
		{
			unset($cpts['firebox']);
		}

		return $cpts;
	}

	/**
	 * When going to edit.php?post_type=firebox, then
	 * redirect to: admin.php?page=firebox-campaigns
	 * 
	 * @return  void
	 */
	function custom_post_edit_redirect()
	{
		global $pagenow;
		if ($pagenow !== 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'firebox') //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		{
			wp_redirect(admin_url('admin.php?page=firebox-campaigns'));
			exit;
		}
	}

	public function handle_bulk_actions()
	{
		if (empty($_GET))
		{
			return;
		}

		$action = isset($_GET['action']) ? sanitize_key($_GET['action']) : false;
		if (!$action)
		{
			return;
		}

		if ($action !== 'fb_export')
		{
			return;
		}

		// Ensure we have IDs
		$ids = isset($_GET['id']) ? array_map('intval', $_GET['id']) : [];
		if (!$ids)
		{
			return;
		}

		// Get nonce
		$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
		if (!$nonce)
		{
			return;
		}

		// Verify nonce
		$nonce_action = 'bulk-fireboxes';
		if (!wp_verify_nonce($nonce, $nonce_action))
		{
			return;
		}
		
		BoxHelper::exportBoxes($ids);
		\FPFramework\Libs\AdminNotice::displaySuccess(sprintf(firebox()->_('FB_X_CAMPAIGNS_HAVE_BEEN_EXPORTED'), count($ids)));
	}

	public function set_default_campaign_title($post_title, $post)
	{
		if (!isset($post->post_type))
		{
			return $post_title;
		}

		if ($post->post_type !== 'firebox')
		{
			return $post_title;
		}

        if (empty(trim($post_title)))
		{
            $post_title = firebox()->_('FB_UNTITLED_CAMPAIGN');
        }
		
		return $post_title;
	}

	/**
	 * Prepare conditions after save.
	 * 
	 * @param   array   $fields
	 * @param   int     $post_id
	 * @param   string  $cpt
	 * 
	 * @return  array
	 */
	public function after_save($post_id)
	{
		if (!isset($_POST['post_type']) || $_POST['post_type'] !== 'firebox')
		{
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		{
			return;
		}

		if ($parent_id = wp_is_post_revision($post_id))
		{
			$post_id = $parent_id;
		}

		\FPFramework\Libs\Cache::invalidate();
	}

	/**
	 * Before deleting the FireBox, delete its box logs and box logs details data
	 * 
	 * @param   int  $ID
	 * 
	 * @return  void
	 */
	public function before_delete_firebox_callback($ID)
	{
		$post = get_post($ID);

		if (!$post)
		{
			return;
		}

		if ($post->post_type !== 'firebox')
		{
			return;
		}

		$logs_table = firebox()->tables->boxlog->getFullTableName();
		$logs_details_table = firebox()->tables->boxlogdetails->getFullTableName();

		// Get all popup forms
		$forms = \FPFramework\Helpers\Plugins\FireBox\Form::getCampaignForms($ID);
		
		// delete logs details
		firebox()->tables->boxlogdetails->executeRaw("DELETE FROM `$logs_details_table` WHERE log_id IN (SELECT id FROM `$logs_table` WHERE box = %d)", [$ID]);
		
		// delete logs
		firebox()->tables->boxlog->delete([
			'box' => $ID
		]);

		if ($forms)
		{
			foreach ($forms as $form_id => $form_label)
			{
				global $wpdb;
				
				// Delete submission meta records first
				$wpdb->query(
					$wpdb->prepare(
						"DELETE sm
						FROM {$wpdb->prefix}firebox_submission_meta AS sm
						INNER JOIN {$wpdb->prefix}firebox_submissions AS s ON sm.submission_id = s.id
						WHERE s.form_id = %s"
						,
						$form_id
					)
				);

				// Delete submissions
				$wpdb->delete("{$wpdb->prefix}firebox_submissions", ['form_id' => $form_id]);
			}
		}
	}

	/**
	 * Handles Box duplication
	 * 
	 * @return  void
	 */
	public function handle_box_duplication()
	{
		check_admin_referer('duplicate-firebox-campaign');

		$post_id = isset($_GET['post']) ? intval($_GET['post']) : '';
		
		$redirect_url = admin_url() . 'admin.php?page=firebox-campaigns';
		
		if (empty($post_id))
		{
			wp_redirect($redirect_url);
			exit();
		}

		BoxHelper::duplicateBox($post_id);

		\FPFramework\Libs\AdminNotice::displaySuccess(firebox()->_('FB_CAMPAIGN_DUPLICATED'));

		// redirect back to box list
		wp_redirect($redirect_url);
	}

	/**
	 * Clears cookie from box
	 * 
	 * @return  void
	 */
	public function handle_box_cookie_clear()
	{
		check_admin_referer('clearcookie-firebox-campaign');

		$post_id = isset($_GET['post']) ? intval($_GET['post']) : '';

		$redirect_url = admin_url() . 'admin.php?page=firebox-campaigns';
		
		if (empty($post_id))
		{
			wp_redirect($redirect_url);
			exit();
		}

		$cookie = new \FireBox\Core\FB\Cookie(firebox()->box->get($post_id));
		$cookie->remove();

		// redirect back to box list
		wp_redirect($redirect_url);
	}

	/**
	 * Adds noindex directive to single FireBox pages using WordPress robots filter.
	 * This integrates properly with WordPress core and other SEO plugins.
	 * 
	 * @param array $robots Associative array of robots directives.
	 * @return array Modified robots directives.
	 */
	public function add_noindex_to_single_firebox($robots)
	{
		if (is_singular('firebox'))
		{
			$robots['noindex'] = true;
			$robots['nofollow'] = true;
		}

		return $robots;
	}

	/**
	 * Returns CPT payload
	 * 
	 * @return  array
	 */
	protected function getPayload()
	{
		return [
			'label' => firebox()->_('FB_PLUGIN_NAME'),
			'public' => true,
			'exclude_from_search' => true,
			'show_ui' => true,
			'show_in_menu' => false,
			'has_archive' => false,
			'capability_type' => 'post',
			'hierarchical' => false,
			'publicly_queryable' => true,
			'rewrite' => ['slug' => 'firebox'],
			'query_var' => true,
			'show_in_rest' => true,
			'supports' => ['title', 'editor', 'custom-fields'],
			'labels' => [
				'name' => firebox()->_('FB_PLUGIN_NAME'),
				'singular_name' => $this->singular,
				'add_new' => firebox()->_('FB_ADD_NEW_CAMPAIGN'),
				'add_new_item' => firebox()->_('FB_ADD_NEW_CAMPAIGN'),
				'edit_item' => firebox()->_('FB_EDIT_CAMPAIGN'),
				'new_item' => firebox()->_('FB_NEW_CAMPAIGN'),
				'all_items' => $this->plural,
				'view_item' => firebox()->_('FB_VIEW_CAMPAIGN'),
				'search_items' => firebox()->_('FB_SEARCH_CAMPAIGNS'),
				'not_found' => firebox()->_('FB_NO_CAMPAIGNS_FOUND'),
				'not_found_in_trash' => firebox()->_('FB_NO_CAMPAIGNS_FOUND_IN_TRASH'),
				'parent_item_colon' => sprintf(firebox()->_('FB_PARENT_X'), $this->singular),
				'menu_name' => firebox()->_('FB_PLUGIN_NAME'),
			]
		];
	}
}