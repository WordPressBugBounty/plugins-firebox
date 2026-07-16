<?php
/**
 * @package         FireBox
 * @version         3.1.9
 *
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\API\Routes\Open;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use \FireBox\Core\API\EndpointController;
use FireBox\Core\FB\CampaignsQuery;
use FireBox\Core\FB\Cookie;
use FireBox\Core\Helpers\BoxHelper;
use WP_REST_Server;
use WP_Error;

/**
 * REST endpoints backing the DataViews Campaigns admin page.
 */
class Campaigns extends EndpointController
{
	const ALLOWED_STATUSES = ['all', 'mine', 'publish', 'draft', 'trash'];
	const ALLOWED_ORDERBY = ['id', 'title', 'views', 'conversions', 'conversionrate', 'date'];

	/**
	 * Endpoint name
	 *
	 * @return  string
	 */
	public function get_name()
	{
		return '';
	}

	/**
	 * Register routes
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->register_route('/campaigns/list', WP_REST_Server::READABLE, [$this, 'get_list'], [], [$this, 'read_permission_callback']);
		$this->register_route('/campaigns/status', WP_REST_Server::CREATABLE, [$this, 'update_status'], [], [$this, 'edit_permission_callback']);
		$this->register_route('/campaigns/trash', WP_REST_Server::CREATABLE, [$this, 'trash'], [], [$this, 'edit_permission_callback']);
		$this->register_route('/campaigns/restore', WP_REST_Server::CREATABLE, [$this, 'restore'], [], [$this, 'edit_permission_callback']);
		$this->register_route('/campaigns/delete', WP_REST_Server::CREATABLE, [$this, 'delete'], [], [$this, 'edit_permission_callback']);
		$this->register_route('/campaigns/duplicate', WP_REST_Server::CREATABLE, [$this, 'duplicate'], [], [$this, 'edit_permission_callback']);
		$this->register_route('/campaigns/export', WP_REST_Server::CREATABLE, [$this, 'export'], [], [$this, 'edit_permission_callback']);
		$this->register_route('/campaigns/reset-stats', WP_REST_Server::CREATABLE, [$this, 'reset_stats'], [], [$this, 'edit_permission_callback']);
		$this->register_route('/campaigns/clear-cookie', WP_REST_Server::CREATABLE, [$this, 'clear_cookie'], [], [$this, 'edit_permission_callback']);
	}

	public function read_permission_callback($request)
	{
		return current_user_can('read_fireboxes');
	}

	public function edit_permission_callback($request)
	{
		return current_user_can('edit_fireboxes');
	}

	/**
	 * GET /campaigns/list
	 */
	public function get_list($request)
	{
		$page = max(1, (int) $request->get_param('page'));
		$per_page = (int) $request->get_param('per_page');
		$per_page = $per_page > 0 ? min(100, $per_page) : 30;

		$search = sanitize_text_field((string) $request->get_param('search'));

		$status = sanitize_key((string) $request->get_param('status'));
		if (!in_array($status, self::ALLOWED_STATUSES, true))
		{
			$status = 'all';
		}

		$orderby = sanitize_key((string) $request->get_param('orderby'));
		if (!in_array($orderby, self::ALLOWED_ORDERBY, true))
		{
			$orderby = 'id';
		}

		$order = strtolower(sanitize_key((string) $request->get_param('order')));
		if (!in_array($order, ['asc', 'desc'], true))
		{
			$order = 'desc';
		}

		$query = new CampaignsQuery();

		$result = $query->getItems([
			'status'   => $status,
			'search'   => $search,
			'orderby'  => $orderby,
			'order'    => $order,
			'page'     => $page,
			'per_page' => $per_page,
		]);

		return [
			'items'         => array_map([$this, 'prepareItemForResponse'], $result['items']),
			'total'         => $result['found_posts'],
			'total_pages'   => $per_page > 0 ? (int) ceil($result['found_posts'] / $per_page) : 1,
			'status_counts' => $query->getStatusCounts(),
		];
	}

	/**
	 * POST /campaigns/status
	 */
	public function update_status($request)
	{
		$ids = $this->sanitizeIds($request->get_param('ids'));
		$status = sanitize_key((string) $request->get_param('status'));

		if (!$ids)
		{
			return new WP_Error('firebox_missing_ids', firebox()->_('FB_NO_CAMPAIGNS_SELECTED'), ['status' => 400]);
		}

		if (!in_array($status, ['publish', 'draft'], true))
		{
			return new WP_Error('firebox_invalid_status', firebox()->_('FB_INVALID_STATUS'), ['status' => 400]);
		}

		if (is_wp_error($error = $this->validateFireboxIds($ids)))
		{
			return $error;
		}

		if (is_wp_error($error = $this->checkCapabilityForIds($ids, 'edit_post')))
		{
			return $error;
		}

		foreach ($ids as $id)
		{
			wp_update_post([
				'ID'          => $id,
				'post_status' => $status,
			]);
		}

		return ['success' => true];
	}

	/**
	 * POST /campaigns/trash
	 */
	public function trash($request)
	{
		$ids = $this->sanitizeIds($request->get_param('ids'));

		if (!$ids)
		{
			return new WP_Error('firebox_missing_ids', firebox()->_('FB_NO_CAMPAIGNS_SELECTED'), ['status' => 400]);
		}

		if (is_wp_error($error = $this->validateFireboxIds($ids)))
		{
			return $error;
		}

		if (is_wp_error($error = $this->checkCapabilityForIds($ids, 'delete_post')))
		{
			return $error;
		}

		foreach ($ids as $id)
		{
			wp_trash_post($id);
		}

		return ['success' => true];
	}

	/**
	 * POST /campaigns/restore
	 */
	public function restore($request)
	{
		$ids = $this->sanitizeIds($request->get_param('ids'));

		if (!$ids)
		{
			return new WP_Error('firebox_missing_ids', firebox()->_('FB_NO_CAMPAIGNS_SELECTED'), ['status' => 400]);
		}

		if (is_wp_error($error = $this->validateFireboxIds($ids)))
		{
			return $error;
		}

		if (is_wp_error($error = $this->checkCapabilityForIds($ids, 'delete_post')))
		{
			return $error;
		}

		foreach ($ids as $id)
		{
			wp_untrash_post($id);
		}

		return ['success' => true];
	}

	/**
	 * POST /campaigns/delete
	 */
	public function delete($request)
	{
		$ids = $this->sanitizeIds($request->get_param('ids'));

		if (!$ids)
		{
			return new WP_Error('firebox_missing_ids', firebox()->_('FB_NO_CAMPAIGNS_SELECTED'), ['status' => 400]);
		}

		if (is_wp_error($error = $this->validateFireboxIds($ids)))
		{
			return $error;
		}

		if (is_wp_error($error = $this->checkCapabilityForIds($ids, 'delete_post')))
		{
			return $error;
		}

		foreach ($ids as $id)
		{
			wp_delete_post($id, true);
		}

		return ['success' => true];
	}

	/**
	 * POST /campaigns/duplicate
	 */
	public function duplicate($request)
	{
		$id = absint($request->get_param('id'));

		if (!$id)
		{
			return new WP_Error('firebox_missing_id', firebox()->_('FB_NO_CAMPAIGNS_SELECTED'), ['status' => 400]);
		}

		if (is_wp_error($error = $this->validateFireboxIds([$id])))
		{
			return $error;
		}

		if (is_wp_error($error = $this->checkCapabilityForIds([$id], 'edit_post')))
		{
			return $error;
		}

		if (!$new_id = BoxHelper::duplicateBox($id))
		{
			return new WP_Error('firebox_duplicate_failed', firebox()->_('FB_CAMPAIGN_DUPLICATION_FAILED'), ['status' => 500]);
		}

		$query = new CampaignsQuery();

		$item = [
			'ID'          => $new_id,
			'label'       => get_the_title($new_id),
			'post_type'   => 'firebox',
			'post_status' => get_post_status($new_id),
			'date'        => get_post_field('post_modified_gmt', $new_id),
			'analytics'   => $query->getCampaignAnalytics($new_id),
		];

		return [
			'success' => true,
			'item'    => $this->prepareItemForResponse($item),
		];
	}

	/**
	 * POST /campaigns/export
	 */
	public function export($request)
	{
		$ids = $this->sanitizeIds($request->get_param('ids'));

		if (!$ids)
		{
			return new WP_Error('firebox_missing_ids', firebox()->_('FB_NO_CAMPAIGNS_SELECTED'), ['status' => 400]);
		}

		if (is_wp_error($error = $this->validateFireboxIds($ids)))
		{
			return $error;
		}

		if (!$payload = BoxHelper::getExportPayload($ids))
		{
			return new WP_Error('firebox_export_failed', firebox()->_('FB_CAMPAIGN_EXPORT_FAILED'), ['status' => 500]);
		}

		return [
			'filename' => $payload['filename'] . '.fbox',
			'data'     => $payload['exported'],
		];
	}

	/**
	 * POST /campaigns/reset-stats
	 */
	public function reset_stats($request)
	{
		$ids = $this->sanitizeIds($request->get_param('ids'));

		if (!$ids)
		{
			return new WP_Error('firebox_missing_ids', firebox()->_('FB_NO_CAMPAIGNS_SELECTED'), ['status' => 400]);
		}

		if (is_wp_error($error = $this->validateFireboxIds($ids)))
		{
			return $error;
		}

		if (is_wp_error($error = $this->checkCapabilityForIds($ids, 'edit_post')))
		{
			return $error;
		}

		BoxHelper::resetBoxStats($ids);

		return ['success' => true];
	}

	/**
	 * POST /campaigns/clear-cookie
	 */
	public function clear_cookie($request)
	{
		$id = absint($request->get_param('id'));

		if (!$id)
		{
			return new WP_Error('firebox_missing_id', firebox()->_('FB_NO_CAMPAIGNS_SELECTED'), ['status' => 400]);
		}

		if (is_wp_error($error = $this->validateFireboxIds([$id])))
		{
			return $error;
		}

		if (is_wp_error($error = $this->checkCapabilityForIds([$id], 'edit_post')))
		{
			return $error;
		}

		if ($box = firebox()->box->get($id))
		{
			(new Cookie($box))->remove();
		}

		return ['success' => true];
	}

	/**
	 * Formats an internal item (as returned by CampaignsQuery) into the public REST shape.
	 *
	 * @param   array  $item
	 *
	 * @return  array
	 */
	private function prepareItemForResponse(array $item)
	{
		$id = (int) $item['ID'];
		$analytics = $item['analytics'];

		$preview_url = null;
		if (in_array($item['post_status'], ['pending', 'draft', 'future'], true) && current_user_can('edit_post', $id))
		{
			$preview_url = get_preview_post_link($id);
		}

		$box = firebox()->box->get($id);

		return [
			'id'               => $id,
			'title'            => sanitize_text_field($item['label']),
			'status'           => $item['post_status'],
			'date'             => $item['date'],
			'views'            => isset($analytics['views']) ? (int) $analytics['views'] : 0,
			'conversions'      => isset($analytics['conversions']) ? (int) $analytics['conversions'] : 0,
			'conversion_rate'  => !empty($analytics['conversionrate']) ? (float) $analytics['conversionrate'] : null,
			'edit_url'         => esc_url_raw(get_edit_post_link($id, 'raw')),
			'preview_url'      => $preview_url ? esc_url_raw($preview_url) : null,
			'analytics_url'    => esc_url_raw(admin_url('admin.php?page=firebox-analytics&campaign=' . $id)),
			'can_clear_cookie' => $box ? (bool) (new Cookie($box))->exist() : false,
		];
	}

	/**
	 * Sanitizes a raw `ids` request param into a de-duplicated array of positive ints.
	 *
	 * @param   mixed  $value
	 *
	 * @return  int[]
	 */
	private function sanitizeIds($value)
	{
		return array_values(array_unique(array_filter(array_map('absint', (array) $value))));
	}

	/**
	 * Ensures every id is an existing `firebox` post, guarding against wrong-CPT abuse.
	 *
	 * @param   int[]  $ids
	 *
	 * @return  true|WP_Error
	 */
	private function validateFireboxIds(array $ids)
	{
		foreach ($ids as $id)
		{
			if (get_post_type($id) !== 'firebox')
			{
				return new WP_Error('firebox_invalid_id', sprintf(firebox()->_('FB_INVALID_CAMPAIGN_ID'), $id), ['status' => 400]);
			}
		}

		return true;
	}

	/**
	 * Ensures the current user has the given per-post capability for every id.
	 *
	 * @param   int[]    $ids
	 * @param   string   $capability  'edit_post'|'delete_post'
	 *
	 * @return  true|WP_Error
	 */
	private function checkCapabilityForIds(array $ids, $capability)
	{
		foreach ($ids as $id)
		{
			if (!current_user_can($capability, $id))
			{
				return new WP_Error('firebox_forbidden', firebox()->_('FB_CAMPAIGNS_ACTION_FORBIDDEN'), ['status' => 403]);
			}
		}

		return true;
	}
}
