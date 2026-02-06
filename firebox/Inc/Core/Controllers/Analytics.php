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

namespace FireBox\Core\Controllers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Analytics extends BaseController
{
	/**
	 * Render view
	 * 
	 * @return  void
	 */
	public function render()
	{
		firebox()->renderer->admin->render('pages/analytics');
	}

	public function addMedia()
	{
		wp_register_script('firebox-react', FBOX_MEDIA_ADMIN_URL . 'js/vendor/react.production.min.js', [], FBOX_VERSION, true);
		wp_enqueue_script('firebox-react');
		wp_register_script('firebox-react-dom', FBOX_MEDIA_ADMIN_URL . 'js/vendor/react-dom.production.min.js', [], FBOX_VERSION, true);
		wp_enqueue_script('firebox-react-dom');

		wp_register_script(
			'firebox-analytics',
			FBOX_MEDIA_ADMIN_URL . 'js/analytics.js',
			['wp-api-fetch'],
			FBOX_VERSION,
			true
		);
		wp_enqueue_script('firebox-analytics');

		// Load appropriate revenue attribution bundle based on plan
		$this->loadRevenueAttributionBundle();
	}

	/**
	 * Load the appropriate revenue attribution bundle based on user's plan
	 */
	private function loadRevenueAttributionBundle()
	{
		$is_ra_plan = in_array(FBOX_LICENSE_PLAN, ['pro', 'growth']);
		$bundle_name = $is_ra_plan ? 'revenue_attribution_pro' : 'revenue_attribution_basic';

		wp_register_script(
			$bundle_name,
			FBOX_MEDIA_ADMIN_URL . 'js/blocks/' . $bundle_name . '.js',
			['firebox-analytics'],
			FBOX_VERSION,
			true
		);
		wp_enqueue_script($bundle_name);
	}
}