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

namespace FireBox\Core\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Libs\Registry;

class BoxHelper
{
	/**
	 * Option holding the cached campaign lists, keyed by status/limit.
	 *
	 * An autoloaded option rather than a transient on purpose: a transient with an expiry is
	 * stored non-autoloaded, so reading it costs 2 queries per request on stock WordPress —
	 * exactly what the campaign query it replaces costs. Autoloaded means WordPress has
	 * already loaded it by the time Boxes::render() runs. There is no TTL either: the option
	 * is deleted on every campaign write, so the cache is always current.
	 *
	 * @var  string
	 */
	const BOXES_CACHE_OPTION = 'firebox_boxes_cache';

	/**
	 * Returns the prefix every FireBox cookie name is built from.
	 *
	 * Cookies are set with path=/, so on a subdirectory network every site of
	 * example.com/store, example.com/blog … shares one cookie namespace. Post
	 * IDs restart per site, so campaign 42 exists on both and one campaign's
	 * "already seen" cookie would suppress an unrelated campaign on another
	 * site. The blog ID in the name is what keeps them apart.
	 *
	 * Single sites — and the main site of a network, which owns the unprefixed
	 * namespace already — keep the original prefix, so upgrading does not show
	 * a campaign again to visitors who had dismissed it.
	 *
	 * @return  string
	 */
	public static function cookiePrefix()
	{
		if (!is_multisite() || is_main_site())
		{
			return 'firebox_';
		}

		return 'firebox_' . get_current_blog_id() . '_';
	}

	/**
	 * The path FireBox cookies are written to.
	 *
	 * WordPress derives COOKIEPATH from the site's home URL, so it already
	 * points at exactly the part of the domain this site occupies — the whole
	 * domain for a normal install, /store/ for that site of a subdirectory
	 * network. It is what WooCommerce and AffiliateWP scope their cookies with,
	 * and it keeps a network's sites from writing over each other.
	 *
	 * @return  string
	 */
	public static function cookiePath()
	{
		return \FPFramework\Helpers\CookieScope::path();
	}

	/**
	 * The domain FireBox cookies are written to.
	 *
	 * Empty unless the site defines COOKIE_DOMAIN, which is how a site tells
	 * WordPress its cookies belong to something other than the current host —
	 * shared between www and non-www, or across the subdomains of a network.
	 * Ignoring it means writing a cookie the site cannot read back.
	 *
	 * @return  string
	 */
	public static function cookieDomain()
	{
		return \FPFramework\Helpers\CookieScope::domain();
	}

	/**
	 * Paths a cookie of ours may have been written to before.
	 *
	 * Cookies are keyed by name *and* path, so a cookie written at '/' is a
	 * different cookie from the same name at '/store/'. FireBox wrote every
	 * cookie to '/' until it started honouring COOKIEPATH, and expiring only
	 * the new path would leave the old one behind — still sent, still
	 * suppressing the campaign it belongs to.
	 *
	 * @return  array
	 */
	public static function cookiePathsToClear()
	{
		return \FPFramework\Helpers\CookieScope::pathsToClear();
	}

	/**
	 * Get box meta
	 *
	 * @param   int  $id
	 *
	 * @return  array
	 */
	public static function getMeta($id)
	{
		$meta = get_post_meta($id, 'firebox_meta', false);
		$meta = isset($meta[0]) && is_array($meta[0]) ? $meta[0] : [];

		return $meta;
	}

	/**
	 * Attaches inline JavaScript that runs once the FireBox runtime has created all
	 * campaign instances.
	 *
	 * The script is attached in the "before" position on purpose: WordPress downgrades a
	 * defer-strategy script to a blocking one as soon as it carries an "after" inline
	 * script, so a single campaign with custom code would make the whole runtime
	 * parse-blocking. Printing before the runtime keeps defer intact.
	 *
	 * Waiting for DOMContentLoaded is what makes that safe: firebox-main is deferred, so
	 * the browser executes it once parsing is done — at which point FireBox.onReady()
	 * runs immediately (readyState is already "interactive") and builds every campaign
	 * instance. DOMContentLoaded fires after all deferred scripts, so by the time this
	 * code runs the instances exist.
	 *
	 * @param   string  $js
	 *
	 * @return  void
	 */
	public static function addInlineScript($js)
	{
		if (!is_string($js) || trim($js) === '')
		{
			return;
		}

		$wrapped = 'document.addEventListener("DOMContentLoaded", function(){' . $js . '});';

		wp_add_inline_script('firebox-main', $wrapped, 'before');
	}

	/**
	 * Whether any of the given campaigns uses session-dependent behavior:
	 * the Pageviews/Time on Site display conditions, or an impressions
	 * limit scoped to the visitor's session (fpfsid).
	 *
	 * These are stamped and evaluated against JS-set session cookies, so
	 * when in use the frontend runtime must be enqueued on every page.
	 * Skipped entirely when no campaign needs them, so other sites load no
	 * extra JS or cookies.
	 *
	 * @param   \WP_Query  $boxes
	 *
	 * @return  bool
	 */
	public static function sessionConditionsInUse($boxes)
	{
		// The answer only changes when a campaign is written to, so keep it in an
		// autoloaded option instead of re-scanning every campaign's meta per page view.
		$cached = get_option('firebox_session_conditions_in_use');

		if ($cached === 'yes' || $cached === 'no')
		{
			return $cached === 'yes';
		}

		$inUse = self::computeSessionConditionsInUse($boxes);

		update_option('firebox_session_conditions_in_use', $inUse ? 'yes' : 'no', true);

		return $inUse;
	}

	/**
	 * Drops the cached session-conditions flag so it is recomputed on the next
	 * frontend request. Hooked on every write path that can change the answer.
	 *
	 * @return  void
	 */
	public static function invalidateSessionConditionsFlag()
	{
		delete_option('firebox_session_conditions_in_use');
	}

	/**
	 * Drops every campaign-derived cache: the session-conditions flag and the
	 * cached box list. Hooked on each write path that can change either answer.
	 *
	 * @return  void
	 */
	public static function invalidateCaches()
	{
		self::invalidateSessionConditionsFlag();
		self::invalidateBoxesCache();
	}

	/**
	 * Registers the invalidation hooks for the campaign-derived caches.
	 *
	 * @return  void
	 */
	public static function registerCacheInvalidation()
	{
		add_action('save_post_firebox', [self::class, 'invalidateCaches']);

		// These fire for every post type, so only react to campaigns — otherwise
		// ordinary content edits would keep busting the campaign caches.
		$onPostChange = function($post_id, $post = null)
		{
			if (self::isCampaign($post_id, $post))
			{
				self::invalidateCaches();
			}
		};

		add_action('deleted_post', $onPostChange, 10, 2);
		add_action('trashed_post', $onPostChange, 10, 1);
		add_action('untrashed_post', $onPostChange, 10, 1);

		// firebox_meta is also written outside the save_post flow (Gutenberg REST meta
		// updates, the campaign importer, duplication).
		$onMetaWrite = function($meta_id, $post_id, $meta_key)
		{
			if ($meta_key === 'firebox_meta')
			{
				self::invalidateCaches();
			}
		};

		add_action('added_postmeta', $onMetaWrite, 10, 3);
		add_action('updated_postmeta', $onMetaWrite, 10, 3);
	}

	/**
	 * Whether the given post is a campaign. `deleted_post` passes the post object
	 * along because the row is already gone by the time it fires.
	 *
	 * @param   int       $post_id
	 * @param   \WP_Post  $post
	 *
	 * @return  bool
	 */
	private static function isCampaign($post_id, $post = null)
	{
		if ($post instanceof \WP_Post)
		{
			return $post->post_type === 'firebox';
		}

		return get_post_type($post_id) === 'firebox';
	}

	/**
	 * Scans the given campaigns for session-dependent behavior.
	 *
	 * @param   \WP_Query  $boxes
	 *
	 * @return  bool
	 */
	private static function computeSessionConditionsInUse($boxes)
	{
		if (!$boxes || empty($boxes->posts))
		{
			return false;
		}

		foreach ($boxes->posts as $box)
		{
			$meta = self::getMeta($box->ID);

			if (!$meta)
			{
				continue;
			}

			// Impressions limit scoped to the browser session
			$impressionsPeriod = isset($meta['assign_impressions_param_type']) ? $meta['assign_impressions_param_type'] : '';

			if ($impressionsPeriod === 'custom')
			{
				$impressionsPeriod = isset($meta['assign_impressions_param_custom_period']) ? $meta['assign_impressions_param_custom_period'] : '';
			}

			if ($impressionsPeriod === 'session')
			{
				return true;
			}

			// Session-dependent display conditions
			$metaJson = wp_json_encode($meta);

			if (strpos($metaJson, 'Pageviews') !== false || strpos($metaJson, 'TimeOnSite') !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets all Boxes.
	 *
	 * Only the campaign IDs are cached, in an autoloaded option so the list also
	 * survives across requests on hosts without a persistent object cache — where
	 * wp_cache is per-request and the query therefore ran on every page view. The
	 * autoloaded options are already in memory by the time this runs, so a cache hit
	 * costs no query of its own.
	 *
	 * There is no TTL: the cache is dropped on every campaign write, which also means
	 * editors see their changes immediately.
	 *
	 * @param   array  $status
	 * @param   int    $limit
	 *
	 * @return  \WP_Query
	 */
	public static function getAllBoxes($status = ['publish'], $limit = -1)
	{
		$key = self::getBoxesCacheKey($status, $limit);
		$cache = get_option(self::BOXES_CACHE_OPTION, []);
		$cache = is_array($cache) ? $cache : [];

		if (isset($cache[$key]) && is_array($cache[$key]))
		{
			return self::hydrateBoxesQuery($cache[$key]);
		}

		$args = [
			'post_status' => $status,
			'post_type' => 'firebox',
			'posts_per_page' => $limit,
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'cache_results' => true
		];

		// Get the query.
		$query = new \WP_Query($args);

		wp_reset_postdata();

		$cache[$key] = wp_list_pluck($query->posts, 'ID');

		update_option(self::BOXES_CACHE_OPTION, $cache, true);

		return $query;
	}

	/**
	 * Rebuilds a WP_Query from a cached list of campaign IDs.
	 *
	 * @param   array  $ids
	 *
	 * @return  \WP_Query
	 */
	private static function hydrateBoxesQuery($ids)
	{
		$query = new \WP_Query();

		// A hand-built query has no query vars of its own, and the loop reads several
		// while setting up each post. "fields => all" states that $posts already holds
		// full post objects, which is what it takes for the loop to use them as-is.
		$query->query_vars = $query->fill_query_vars(['fields' => 'all']);

		$posts = [];

		if ($ids)
		{
			// Two queries, both by primary key, priming the same post and postmeta
			// caches the original WP_Query did.
			_prime_post_caches($ids, false, true);

			// array_filter drops campaigns deleted since the cache was written.
			$posts = array_values(array_filter(array_map('get_post', $ids)));
		}

		$query->posts = $posts;
		$query->post_count = count($posts);
		$query->found_posts = $query->post_count;

		return $query;
	}

	/**
	 * The cache entry a box list is stored under.
	 *
	 * @param   array  $status
	 * @param   int    $limit
	 *
	 * @return  string
	 */
	private static function getBoxesCacheKey($status, $limit)
	{
		return md5(implode(',', (array) $status) . '_' . (int) $limit);
	}

	/**
	 * Drops every cached box list.
	 *
	 * @return  void
	 */
	public static function invalidateBoxesCache()
	{
		delete_option(self::BOXES_CACHE_OPTION);
	}

	/**
	 * Retrieves all boxes in a key => value array of ID => title
	 * 
	 * @return  array
	 */
	public static function getAllBoxesParsedByKeyValue()
	{
		if (!$boxes = self::getAllBoxes())
		{
			return [];
		}

		return self::produceKeyValueBoxes($boxes->posts);
	}

	/**
	 * Produce a key,value pair of boxes containg their ID,title
	 * 
	 * @return  array
	 */
	public static function produceKeyValueBoxes($boxes)
	{
		if (!$boxes)
		{
			return [];
		}
		
		$data = [];

		foreach ($boxes as $key => $box)
		{
			$data[$box->ID] = $box->post_title;
		}
		
		return $data;
	}

	/**
	 * Gets all published Boxes except the given id.
	 * The array structure is [ID, title] to properly appear in a Dropdown field.
	 * 
	 * @param   integer  $id
	 * 
	 * @return  array
	 */
	public static function getAllMirrorBoxesExceptID($id)
	{
		if (!$id)
		{
			return [];
		}

		$boxes = firebox()->tables->box->getResults([
			'where' => [
				'ID' => ' NOT IN (' . absint($id) . ')',
				'post_status' => " = 'publish'",
				'post_type' => " = 'firebox'"
			]
		]);

		$boxes_parsed = [];

		foreach ($boxes as $key => $p)
		{
			$boxes_parsed[$p->ID] = $p->post_title . ' (' . $p->ID . ')';
		}

		return $boxes_parsed;
	}

	/**
	 * Get box data
	 * 
	 * @param   int    $box
	 * 
	 * @return  array
	 */
	public static function getBoxData($box)
	{
		if (!$box)
		{
			return false;
		}

		$box = (int) $box;

		$box = firebox()->tables->box->getResults([
			'where' => [
				'ID' => " = '" . absint($box) . "'"
			]
		]);

		return isset($box[0]) ? $box[0] : [];
	}

	/**
	 * Checks whether the box exist
	 * 
	 * @param   int      $box
	 * 
	 * @return  boolean
	 */
	public static function boxExist($box)
	{
		if (!$box)
		{
			return false;
		}
		
		$box = (int) $box;

		$box = firebox()->tables->box->getResults([
			'where' => [
				'ID' => " = '" . absint($box) . "'"
			]
		]);

		if (!$box)
		{
			return false;
		}

		return true;
	}

	/**
	 * Gets boxes in a [id, title] pair from a list of Box IDs
	 * 
	 * @param   array  $items
	 * 
	 * @return  array
	 */
	public static function getSelectedSearchItems($items)
	{
		$boxes = firebox()->tables->box->getResults([
			'where' => [
				'ID' => ' IN(' . implode(',', array_map('intval', $items)) . ')',
				'post_status' => " = 'publish'",
				'post_type' => " = 'firebox'"
			]
		]);

		$boxes_parsed = [];

		foreach ($boxes as $key => $p)
		{
			$boxes_parsed[] = [
				'id' => $p->ID,
				'title' => $p->post_title
			];
		}

		return $boxes_parsed;
	}

	/**
	 * Gets Settings Data
	 * 
	 * @return  array
	 */
	public static function getParams()
	{
		// cache key
		$cache_key = md5('fboxSettings');

		// check cache
		if ($params = wp_cache_get($cache_key, 'firebox'))
		{
			return $params;
		}

		// get params
		$params = get_option('firebox_settings');

		// set cache
		wp_cache_set($cache_key, $params, 'firebox', 5 * MINUTE_IN_SECONDS);

		return $params;
	}
	
	/**
	 * Duplicates a box
	 * 
	 * @param   integer  $box_id
	 * 
	 * @return  bool
	 */
	public static function duplicateBox($box_id)
	{
		// get box
		$box = firebox()->tables->box->getResults([
			'where' => [
				'ID' => " = '" . absint($box_id) . "'",
				'post_status' => " = '" . sanitize_key(get_post_status($box_id)) . "'",
				'post_type' => " = 'firebox'"
			],
			'limit' => 1
		]);

		if (empty($box))
		{
			return false;
		}

		// reset box ID and make it a draft
		$box = $box[0];
		$box->ID = '';
		$box->post_title = 'Copy of ' . $box->post_title;
		$box->post_status = 'draft';

		/**
		 * The copy belongs to whoever duplicates, never the source author — the same
		 * rule the importer applies. The PHP and custom-JavaScript gates are a
		 * function of the post author, so keeping the source author would let a copy
		 * of a higher-privileged user's campaign keep running code its new owner is
		 * not allowed to write.
		 */
		$box->post_author = get_current_user_id();

		$factory = new \FPFramework\Base\Factory();
			
		$tz = wp_timezone();
		$date_without_tz = $factory->getDate();
		$date_with_tz = $factory->getDate()->setTimezone($tz);

		$box->post_date = $date_with_tz->format('Y-m-d H:i:s');
		$box->post_date_gmt = $date_without_tz->format('Y-m-d H:i:s');

		Form\Form::ensureUniqueFormIDs($box->post_content);

		// get meta options
		$meta = self::getMeta($box_id);

		// insert new box
		$new_box_id = firebox()->tables->box->insert($box);

		// add meta options for new box
		// TODO: In the future, use "firebox_meta". This is a temporary fix for backwards compatibility.
		$checkMeta = (array) $meta;
		$meta_key = isset($checkMeta['width']) ? 'firebox_meta' : 'fpframework_meta_settings';
		update_post_meta($new_box_id, $meta_key, wp_slash($meta));

		return $new_box_id;
	}

	/**
	 * Reset Box Stats
	 * 
	 * @param   array  $box_ids
	 * 
	 * @return  void
	 */
	public static function resetBoxStats($box_ids)
	{
		$logs_table = firebox()->tables->boxlog->getFullTableName();
		$logs_details_table = firebox()->tables->boxlogdetails->getFullTableName();

		// delete box logs details
		firebox()->tables->boxlogdetails->executeRaw("DELETE FROM `$logs_details_table` WHERE log_id IN (SELECT id FROM `$logs_table` WHERE box IN (" . implode(",", $box_ids) . "))");

		// delete box logs
		firebox()->tables->boxlog->deleteRaw('WHERE box IN (' . implode(',', $box_ids) . ')');
	}

	/**
	 * Builds the export payload (boxes + meta) and a suggested filename for the given box ids,
	 * without emitting any headers or output. Shared by exportBoxes() (legacy file-download
	 * action) and the Campaigns REST export endpoint.
	 *
	 * @param   array  $box_ids
	 *
	 * @return  array|null  ['filename' => string, 'exported' => array] or null if no boxes matched
	 */
	public static function getExportPayload($box_ids)
	{
		// get boxes
		$boxes = firebox()->tables->box->getResults([
			'where' => [
				'ID' => ' IN (' . implode(',', array_map('intval', $box_ids)) . ')',
				'post_type' => " = 'firebox'"
			]
		]);

		$boxes = (array) $boxes;

		if (!count($boxes))
		{
			return null;
		}

		$exported = [];

		$filename = firebox()->_('FB_PLUGIN_NAME') . ' Items';

		// name for 1 box
		if (count($boxes) == 1)
		{
			$name = mb_strtolower(html_entity_decode($boxes['0']->post_title));
			// preg_replace() returns null when PCRE fails (backtrack limit, invalid UTF-8),
			// and passing null on is deprecated since PHP 8.1. Keep the previous value.
			$name = preg_replace('#[^a-z0-9_-]#', '_', $name) ?? $name;
			$name = trim(preg_replace('#__+#', '_', $name) ?? $name, '_-');

			$filename = firebox()->_('FB_PLUGIN_NAME') .  ' Item (' . $name . ')';
		}

		foreach ($boxes as $box)
		{
			$meta = self::getMeta($box->ID);

			$exported[] = [
				'box' => $box,
				'meta' => $meta
			];
		}

		return [
			'filename' => $filename,
			'exported' => $exported,
		];
	}

	/**
	 * Exports boxes
	 *
	 * @param   array  $box_ids
	 *
	 * @return  string
	 */
	public static function exportBoxes($box_ids)
	{
		if (!$payload = self::getExportPayload($box_ids))
		{
			return;
		}

		$filename = $payload['filename'];
		$exported = $payload['exported'];

		// SET DOCUMENT HEADER
		$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';
		if (preg_match('#Opera(/| )([0-9].[0-9]{1,2})#', $userAgent))
		{
			$UserBrowser = "Opera";
		}
		elseif (preg_match('#MSIE ([0-9].[0-9]{1,2})#', $userAgent))
		{
			$UserBrowser = "IE";
		}
		else
		{
			$UserBrowser = '';
		}
		$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
		@ob_end_clean();
		ob_start();

		header('Content-Type: ' . $mime_type);
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

		if ($UserBrowser == 'IE')
		{
			header('Content-Disposition: inline; filename="' . $filename . '.fbox"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		}
		else
		{
			header('Content-Disposition: attachment; filename="' . $filename . '.fbox"');
			header('Pragma: no-cache');
		}

		// PRINT STRING
		echo wp_json_encode($exported);

		if (ob_get_level())
		{
			@ob_end_flush();
		}
		die();
	}

	/**
	 * Returns the last date viewed of a campaign.
	 * 
	 * @param   int		$id
	 * 
	 * @return  string
	 */
	public static function getCampaignLastDateViewed($id = null)
	{
		if (!$id)
		{
			return;
		}

		$last_date_viewed = firebox()->tables->boxlog->getResults([
			'select' => [
				'date as last_date_viewed'
			],
			'where' => [
				'box' => ' = ' . absint($id),
			],
			'orderby' => ' date desc',
			'limit' => 1
		]);

		return isset($last_date_viewed[0]->last_date_viewed) ? get_date_from_gmt($last_date_viewed[0]->last_date_viewed) : null;
	}

	
}
