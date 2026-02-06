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

namespace FireBox\Core\UsageTracking;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class PluginData
{
    public function getViews()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'firebox_logs';
        $total_views = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
        return $total_views;
    }

    public function getCampaigns($status = '')
    {
        if (!$status)
        {
            return;
        }
        
        global $wpdb;
        $query = "
            SELECT COUNT(id) as total
            FROM {$wpdb->posts} 
            WHERE post_type = 'firebox' 
            AND post_status IN ('" . $status . "')
        ";
        $results = $wpdb->get_var($query);
        return $results;
    }

    public function getSubmissions()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'firebox_submissions';
        $total_submissions = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
        return $total_submissions;
    }

    /**
     * Get total view-through revenue across all campaigns
     * View-through revenue is when someone views a campaign and purchases later without converting
     * 
     * @return float
     */
    public function getViewThroughRevenue()
    {
        return $this->getRevenueBySource('impression');
    }

    /**
     * Get total click-through (conversion-through) revenue across all campaigns
     * Click-through revenue is when someone views a campaign, converts through it, and then purchases
     * 
     * @return float
     */
    public function getClickThroughRevenue()
    {
        return $this->getRevenueBySource('conversion');
    }

    /**
     * Get cached revenue totals by attribution source
     * Uses a single optimized query and caches results
     * 
     * @param string $source Attribution source ('impression' or 'conversion')
     * @return float
     */
    private function getRevenueBySource($source)
    {
        // Check cache first
        $cache_key = "firebox_revenue_totals_{$source}";
        $cached_result = wp_cache_get($cache_key, 'firebox_revenue');
        
        if ($cached_result !== false)
        {
            return (float) $cached_result;
        }

        // Get revenue data with single query
        $revenue_data = $this->getCachedRevenueData();
        
        $total_revenue = 0.0;
        
        foreach ($revenue_data as $row)
        {
            if ($row->event_source !== $source || !$row->order_id || !$row->order_type)
            {
                continue;
            }
            
            $total_revenue += $this->getOrderTotalCached($row->order_id, $row->order_type);
        }
        
        // Cache result for 1 hour
        wp_cache_set($cache_key, $total_revenue, 'firebox_revenue', HOUR_IN_SECONDS);
        
        return $total_revenue;
    }

    /**
     * Get all revenue data with a single optimized query and cache it
     * 
     * @return array
     */
    private function getCachedRevenueData()
    {
        $cache_key = 'firebox_all_revenue_data';
        $cached_results = wp_cache_get($cache_key, 'firebox_revenue');

        if ($cached_results === false)
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'firebox_logs_details';
            
            $query = "
                SELECT 
                    event_source,
                    JSON_UNQUOTE(JSON_EXTRACT(event_label, '$.order_id')) AS order_id,
                    JSON_UNQUOTE(JSON_EXTRACT(event_label, '$.order_type')) AS order_type
                FROM {$table_name}
                WHERE event = 'revenue' 
                AND event_source IN ('impression', 'conversion')
                AND JSON_VALID(event_label)
                AND JSON_EXTRACT(event_label, '$.order_id') IS NOT NULL
            ";
            
            $cached_results = $wpdb->get_results($query);
            if (!is_array($cached_results))
            {
                $cached_results = [];
            }
            
            // Cache for 1 hour
            wp_cache_set($cache_key, $cached_results, 'firebox_revenue', HOUR_IN_SECONDS);
        }

        return $cached_results;
    }

    /**
     * Get order total with caching to avoid duplicate queries
     * 
     * @param string $order_id
     * @param string $order_type
     * @return float
     */
    private function getOrderTotalCached($order_id, $order_type)
    {
        if (!$order_id || !$order_type)
        {
            return 0.0;
        }

        $cache_key = "firebox_order_total_{$order_type}_{$order_id}";
        $cached_total = wp_cache_get($cache_key, 'firebox_orders');
        
        if ($cached_total !== false)
        {
            return (float) $cached_total;
        }

        $total = 0.0;
        if (class_exists('\FireBox\Core\RevenueAttribution\OrderHelper'))
        {
            $total = \FireBox\Core\RevenueAttribution\OrderHelper::getOrderTotal($order_id, $order_type);
        }
        
        // Cache for 6 hours (orders don't change frequently)
        wp_cache_set($cache_key, $total, 'firebox_orders', 6 * HOUR_IN_SECONDS);
        
        return (float) $total;
    }

    public function getTotalsBySetting($setting_name = '', $setting_value = '')
    {
        if (!$setting_name || !$setting_value)
        {
            return;
        }
        
        $posts = $this->getCachedCampaigns();

        $count = 0;
        foreach ($posts as $post)
        {
            $tmp_setting_name = $setting_name;
            $tmp_setting_value = $setting_value;
            
            $meta = maybe_unserialize($post->meta_value);

            if (strpos($setting_name, '.') !== false)
            {
                $keys = explode('.', $setting_name);
                
                $meta = isset($meta[$keys[0]]) && is_array($meta[$keys[0]]) ? $meta[$keys[0]] : false;

                if (!$meta)
                {
                    continue;
                }
                    
                $tmp_setting_name = $keys[1];
            }
            
            if (isset($meta[$tmp_setting_name]))
            {
                if (strpos($tmp_setting_value, 'cond:') === 0)
                {
                    $condition = substr($tmp_setting_value, 5);
                    switch ($condition)
                    {
                        case 'not:none':
                            if ($meta[$tmp_setting_name] !== 'none')
                            {
                                $count++;
                            }
                            break;
                        case 'not:empty':
                            if (!empty($meta[$tmp_setting_name]) && !is_null($meta[$tmp_setting_name]))
                            {
                                $count++;
                            }
                            break;
                        case 'not:emptyArray':
                            if (is_array($meta[$tmp_setting_name]) && count($meta[$tmp_setting_name]))
                            {
                                $count++;
                            }
                            break;
                    }
                }
                else if ($tmp_setting_value === 'boolean')
                {
                    // Check if the value is a boolean
                    if ((is_bool($meta[$tmp_setting_name]) && $meta[$tmp_setting_name]) || $meta[$tmp_setting_name] === '1')
                    {
                        $count++;
                    }
                }
                
                // Equal comparison
                if ($meta[$tmp_setting_name] === $tmp_setting_value)
                {
                    $count++;
                }
                // In array comparison
                else if (is_array($meta[$tmp_setting_name]) && in_array($tmp_setting_value, $meta[$tmp_setting_name]))
                {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function getTotalsByCondition($condition = '')
    {
        if (!$condition)
        {
            return;
        }

        $posts = $this->getCachedCampaigns();

        $count = 0;
        foreach ($posts as $post)
        {
            $meta = maybe_unserialize($post->meta_value);

            if (!isset($meta['rules']))
            {
                continue;
            }

            if (!$rules = $meta['rules'])
            {
                continue;
            }

            if (is_string($rules))
            {
                $rules = json_decode($rules, true);
            }

            if (!is_array($rules))
            {
                continue;
            }

            foreach ($rules as $key => $group)
            {
                if (!isset($group['rules']) || !is_array($group['rules']))
                {
                    continue;
                }
                
                foreach ($group['rules'] as $groupRule)
                {
                    if (!isset($groupRule['name']))
                    {
                        continue;
                    }

                    $groupName = str_replace('\\\\', '\\', $groupRule['name']);
                    
                    if ($groupName === $condition)
                    {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    public function getDimensionsData()
    {
        $posts = $this->getCachedCampaigns();
        $dimensions = [];

        foreach ($posts as $post)
        {
            $meta = maybe_unserialize($post->meta_value);

            $width = isset($meta['width_control']['width']['desktop']['value']) ? $meta['width_control']['width']['desktop']['value'] : '';
            $height = isset($meta['height_control']['height']['desktop']['value']) && $meta['height_control']['height']['desktop']['value'] ? $meta['height_control']['height']['desktop']['value'] : 'auto';

            if (!$width || !$height)
            {
                continue;
            }

            // Format: "400xauto" or "500x300"
            $dimension_key = $width . 'x' . $height;

            if (!isset($dimensions[$dimension_key]))
            {
                $dimensions[$dimension_key] = 0;
            }

            $dimensions[$dimension_key]++;
        }

        return $dimensions;
    }

    private function getCachedCampaigns()
    {
        global $wpdb;
        $cache_key = 'firebox_campaigns_for_search';
        $cached_results = wp_cache_get($cache_key);

        if ($cached_results === false)
        {
            $query = "
                SELECT p.ID, pm.meta_value
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                WHERE p.post_type = 'firebox'
                AND p.post_status IN ('draft', 'trash', 'publish')
                AND (pm.meta_key = 'fpframework_meta_settings' OR pm.meta_key = 'firebox_meta')
            ";
            $cached_results = $wpdb->get_results($query);
            wp_cache_set($cache_key, $cached_results, 'firebox', 6 * DAY_IN_SECONDS + 12 * HOUR_IN_SECONDS);
        }

        return $cached_results;
    }
}