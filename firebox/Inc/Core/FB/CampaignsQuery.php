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

namespace FireBox\Core\FB;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

/**
 * Shared data layer for the Campaigns list, consumed by both the classic
 * WP_List_Table (CampaignsList) and the DataViews REST endpoint, so both
 * UIs query/sort/paginate campaigns identically.
 */
class CampaignsQuery
{
	const ALLOWED_ANALYTICS_ORDERBY = ['views', 'conversions', 'conversionrate'];

	/**
	 * Fetches campaigns (+ analytics) for the given args.
	 *
	 * @param   array  $args  {
	 *     @type  string  $status    all|mine|publish|draft|trash
	 *     @type  string  $search    title search term
	 *     @type  string  $orderby   id|title|date|views|conversions|conversionrate
	 *     @type  string  $order     asc|desc
	 *     @type  int     $page      1-based page number
	 *     @type  int     $per_page
	 * }
	 *
	 * @return  array  ['items' => array[], 'found_posts' => int]
	 */
	public function getItems(array $args = [])
	{
		$args = wp_parse_args($args, [
			'status'   => 'all',
			'search'   => '',
			'orderby'  => 'id',
			'order'    => 'desc',
			'page'     => 1,
			'per_page' => 30,
		]);

		$query_args = [
			'post_type'      => 'firebox',
			'posts_per_page' => $args['per_page'],
			'paged'          => $args['page'],
		];

		if ($args['search'] !== '')
		{
			$query_args['s'] = $args['search'];
		}

		switch ($args['status'])
		{
			case 'mine':
				$query_args['author'] = get_current_user_id();
				break;
			case 'publish':
				$query_args['post_status'] = 'publish';
				break;
			case 'draft':
				$query_args['post_status'] = 'draft';
				break;
			case 'trash':
				$query_args['post_status'] = 'trash';
				break;
			default:
				// "all" mirrors the legacy get_views() definition of "All": published + drafts, not trash.
				$query_args['post_status'] = ['publish', 'draft'];
				break;
		}

		$this->applyInitialSorting($query_args, $args['orderby'], $args['order']);

		$query = new \WP_Query($query_args);

		$items = [];

		if ($query->have_posts())
		{
			foreach ($query->posts as $post)
			{
				$items[] = [
					'ID'          => $post->ID,
					'label'       => $post->post_title,
					'post_type'   => $post->post_type,
					'post_status' => $post->post_status,
					'date'        => $post->post_modified_gmt,
					'analytics'   => $this->getCampaignAnalytics($post->ID),
				];
			}

			$this->applySecondarySorting($items, $args['orderby'], $args['order']);
		}

		return [
			'items'       => $items,
			'found_posts' => (int) $query->found_posts,
		];
	}

	/**
	 * Returns campaign counts per status, mirroring the legacy get_views() counts.
	 *
	 * @return  array  ['all' => int, 'mine' => int, 'publish' => int, 'draft' => int, 'trash' => int]
	 */
	public function getStatusCounts()
	{
		$counts = (array) wp_count_posts('firebox', 'readable');

		$publish = (int) ($counts['publish'] ?? 0);
		$draft   = (int) ($counts['draft'] ?? 0);
		$trash   = (int) ($counts['trash'] ?? 0);

		return [
			'all'     => $publish + $draft,
			'mine'    => $this->getMineCount(),
			'publish' => $publish,
			'draft'   => $draft,
			'trash'   => $trash,
		];
	}

	/**
	 * Counts campaigns authored by the current user (any readable status).
	 *
	 * @return  int
	 */
	public function getMineCount()
	{
		$query = new \WP_Query([
			'post_type' => 'firebox',
			'author'    => get_current_user_id(),
		]);

		return (int) $query->found_posts;
	}

	/**
	 * Fetches analytics metrics (views/conversions/conversionrate) for a campaign.
	 *
	 * @param   int  $campaign_id
	 *
	 * @return  array
	 */
	public function getCampaignAnalytics($campaign_id)
	{
		if (!$campaign_id)
		{
			return [];
		}

		$data = new \FireBox\Core\Analytics\Data();

		$metrics = self::ALLOWED_ANALYTICS_ORDERBY;

		$filters = [
			'campaign' => [
				'value' => [$campaign_id]
			]
		];

		$data->metrics($metrics)->filters($filters);

		return $data->getData('count');
	}

	/**
	 * Applies DB-level sorting (id/title/date) to the WP_Query args.
	 * Analytics-based sorting cannot happen at the DB level and is handled
	 * by applySecondarySorting() once the analytics values are attached.
	 *
	 * @param   array   $query_args
	 * @param   string  $orderby
	 * @param   string  $order
	 *
	 * @return  void
	 */
	private function applyInitialSorting(&$query_args, $orderby, $order)
	{
		switch ($orderby)
		{
			case 'id':
				$query_args['orderby'] = 'ID';
				$query_args['order'] = $order;
				break;
			case 'title':
				$query_args['orderby'] = 'title';
				$query_args['order'] = $order;
				break;
			case 'date':
				$query_args['orderby'] = 'modified';
				$query_args['order'] = $order;
				break;
		}
	}

	/**
	 * Sorts the fetched page's items by an analytics metric in PHP, since
	 * views/conversions/conversionrate don't live on the `firebox` post row.
	 * This preserves the pre-existing behavior of sorting only within the
	 * fetched/paginated set, not across the full result set.
	 *
	 * @param   array   $items
	 * @param   string  $orderby
	 * @param   string  $order
	 *
	 * @return  void
	 */
	private function applySecondarySorting(&$items, $orderby, $order)
	{
		if (!in_array($orderby, self::ALLOWED_ANALYTICS_ORDERBY, true))
		{
			return;
		}

		usort($items, function ($a, $b) use ($order, $orderby) {
			$value_a = (float) ($a['analytics'][$orderby] ?? 0);
			$value_b = (float) ($b['analytics'][$orderby] ?? 0);

			if ($order === 'asc')
			{
				return $value_a > $value_b ? 1 : -1;
			}
			else
			{
				return $value_a < $value_b ? 1 : -1;
			}
		});
	}
}
