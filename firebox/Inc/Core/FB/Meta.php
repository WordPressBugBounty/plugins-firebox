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

namespace FireBox\Core\FB;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Meta
{
    private $is_running = false;

    public function __construct()
    {
        add_filter('get_post_metadata', [$this, 'get_legacy_meta'], 10, 4);

        $this->register();
    }

    public function get_legacy_meta($value, $post_id, $meta_key, $single)
    {
        if ($this->is_running || $meta_key !== 'firebox_meta')
        {
            return $value;
        }

        $this->is_running = true;

        // Get new format meta
        $new_meta = get_post_meta($post_id, 'firebox_meta', true) ?: [];

        // If we have new meta data, return it
        if (is_array($new_meta) && count($new_meta))
        {
            $this->is_running = false;
            return $value;
        }

        $defaults = $this->getDefaults();
        
        // Get legacy meta
        $legacy_meta = get_post_meta($post_id, 'fpframework_meta_settings', true);

        // Merge legacy values if not already in new format
        if (!empty($legacy_meta))
        {
            $map = [
                // Campaign Format
                'mode' => [],

                // Trigger
                'triggermethod' => [
                    'transform' => 'triggermethod_migration'
                ],
                'triggerelement' => [],
                'close_out_viewport' => [],
                'threshold' => [],
                'exittimer' => [],
                'idle_time' => [],
                'triggerdelay' => [],
                'scroll_depth' => [
                    'rename' => 'scroll_amount',
                    'transform' => 'scroll_depth_migration'
                ],
                'autohide' => [],
                'firing_frequency' => [
                    'transform' => 'firing_frequency_toggle_migration'
                ],
                'floating_button_show_on_close' => [],
                'floatingbutton_message' => [
                    'transform' => 'floatingbutton_message_migration'
                ],
                'floating_button_position' => [],
                'assign_impressions_param_type' => [],
                'assign_impressions_param_custom_period_times' => [],
                'assign_impressions_param_custom_period' => [],
                'opening_sound' => [],
                'assign_cookietype' => [],
                'assign_cookietype_param_custom_period_times' => [],
                'assign_cookietype_param_custom_period' => [],
                'box_auto_close' => [
                    'transform' => 'box_auto_close_migration'
                ],
                'box_auto_close_seconds' => [],
                'autofocus' => [],
                'close_on_esc' => [],

                // Actions
                'actions' => [
                    'transform' => 'actions_migration'
                ],

                // Display Conditions
                'display_conditions_type' => [],
                'mirror_box' => [],
                'rules' => [
                    'transform' => 'rules_migration'
                ],

                // Position
                'position' => [],
                
                // Design
                'width_control.width' => [
                    'rename' => 'width',
                    'transform' => 'migrate_unitvalue'
                ],
                'height_control.height' => [
                    'rename' => 'height',
                    'transform' => 'migrate_unitvalue'
                ],
                'fontsize_control.fontsize' => [
                    'rename' => 'fontsize',
                    'transform' => 'migrate_unitvalue'
                ],
                'padding_control.padding' => [
                    'rename' => 'padding',
                    'transform' => 'dimension'
                ],
                'margin_control.margin' => [
                    'rename' => 'margin',
                    'transform' => 'dimension'
                ],
                'textcolor' => [],
                'backgroundcolor' => [],
                'aligncontent' => [],
                'boxshadow' => [
                    'transform' => 'shadow_migration'
                ],
                'animationin' => [
                    'transform' => 'animation_migration'
                ],
                'animationout' => [
                    'transform' => 'animation_migration'
                ],
                'duration' => [],
                'border' => [
                    'transform' => 'border_migration'
                ],
                'borderradius_control.borderradius' => [
                    'rename' => 'borderradius',
                    'transform' => 'borderradius_migration'
                ],
                // Background Overlay
                'overlay' => [
                    'transform' => 'overlay_enabled_migration'
                ],
                'overlay_color' => [],
                'overlayblurradius' => [
                    'transform' => 'overlayblurradius_migration'
                ],
                'overlayclick' => [],
                // Background Image
                'bgimage' => [],
                'bgimagefile' => [],
                'bgrepeat' => [],
                'bgsize' => [],
                'bgposition' => [],

                // Close Button
                'closebutton' => [],

                // Advanced
                'customcode' => [],
                'customcss' => [],
                'testmode' => [],
                'preventpagescroll' => [],
                'stats' => [],
                'classsuffix' => [],
                'zindex' => [],

                
            ];

            foreach ($map as $key => $value)
            {
                $legacyValue = $this->getValueFromLegacyMeta($key, $value, $new_meta, $legacy_meta);
                
                // Renames
                if (isset($value['rename']))
                {
                    $key = $value['rename'];
                }
                
                // Does the new meta value exist?
                $valueExistsInNewMeta = isset($new_meta[$key]);
                
                // Set new meta value from legacy meta value. Always assign (even when
                // $legacyValue is null, i.e. no legacy counterpart exists) so the key
                // exists throughout the transform stage below — direct array access on
                // an existing-but-null key doesn't warn, whereas leaving the key entirely
                // absent does. Null placeholders are stripped before the final defaults
                // backfill so they don't clobber a field's documented default.
                if (!isset($new_meta[$key]))
                {
                    $new_meta[$key] = $legacyValue;
                }

                // Transforms
                if (isset($value['transform']))
                {
                    $migrator = new MetaMigrator();
                    $migrator->applyTransform($value['transform'], $key, $new_meta, $legacy_meta, $defaults, $valueExistsInNewMeta);
                }
            }
        }

        // Drop null placeholders (keys with no legacy counterpart, kept in the array
        // only so the transform stage above could access them without warnings), then
        // backfill any key still missing — including every key, if there was no legacy
        // meta at all, i.e. a brand new campaign — with its documented default, without
        // clobbering keys that were actually migrated from legacy data.
        $new_meta = array_filter($new_meta, function ($v) { return $v !== null; });
        $new_meta = array_merge($defaults, $new_meta);

        $this->is_running = false;

        // WP always expects filters on `get_post_metadata` to return the value
        // wrapped in an array; it unwraps to $value[0] itself when $single is true.
        return [$new_meta];
    }

    /**
     * Returns null (not '') when no legacy counterpart exists, so the caller can tell
     * "no legacy value" apart from "legacy value is an empty string" and fall back to
     * the field's documented default instead of persisting an empty string.
     */
    private function getValueFromLegacyMeta($key, $value, $new_meta, $legacy_meta)
    {
        $_value = null;

        if (strpos($key, '.') !== false)
        {
            list($first_part, $second_part) = explode('.', $key, 2);
            if (!isset($new_meta[$value['rename']]) && isset($legacy_meta[$first_part][$second_part]))
            {
                $_value = $legacy_meta[$first_part][$second_part];
            }
        }
        else
        {
            if (!isset($new_meta[$key]) && isset($legacy_meta[$key]))
            {
                $_value = $legacy_meta[$key];
            }
        }

        return $_value;
    }

    private function getDefaults()
    {
        return [
            'mode' => 'popup',

            // For fullscreen
            'center_content' => false,

            'triggermethod' => 'pageload',
            'triggerelement' => '',
            'close_out_viewport' => false,
            'threshold' => 25,
            'exittimer' => 1,
            'idle_time' => 10,
            'triggerdelay' => 0,
            'scroll_amount' => '80%',
            'autohide' => false,
            'firing_frequency' => '1',
            'floating_button_show_on_close' => false,
            'floatingbutton_message' => [
                'text' => 'Open Popup',
                'bgcolor' => '#4285F4',
                'textcolor' => '#fff',
                'fontsize' => '15px'
            ],
            'floating_button_position' => 'bottom-right',
            'assign_impressions_param_type' => 'always',
            'assign_impressions_param_custom_period_times' => 1,
            'assign_impressions_param_custom_period' => 'session',
            'opening_sound' => [
                'source' => 'none',
                'file' => '',
                'url' => '',
            ],
            'assign_cookietype' => 'never',
            'assign_cookietype_param_custom_period_times' => 1,
            'assign_cookietype_param_custom_period' => 'days',
            'box_auto_close' => false,
            'box_auto_close_seconds' => 5,
            'autofocus' => false,
            'close_on_esc' => false,

            // Actions
            'actions' => [],

            // Display Conditions
            'display_conditions_type' => 'all',
            'mirror_box' => '',
            'rules' => [
                [
                    'matching_method' => 'all',
                    'enabled' => true,
                    'rules' => [
                        [
                            'enabled' => true,
                            'name' => '',
                            'operator' => '',
                            'value' => ''
                        ]
                    ]
                ]
            ],

            'position' => 'center',
            
            'width' => [
                'desktop' => '500px'
            ],
            'height' => [
                'desktop' => 'auto'
            ],
            'padding' => [
                'desktop' => [
                    'top' => '30px',
                    'right' => '30px',
                    'bottom' => '30px',
                    'left' => '30px'
                ]
            ],
            'margin' => [
                'desktop' => [
                    'top' => '',
                    'right' => '',
                    'bottom' => '',
                    'left' => ''
                ]
            ],
            'closebutton' => [
                'show' => '1',
                'source' => 'icon',
                'color' => 'rgba(136, 136, 136, 1)',
                'hover' => 'rgba(85, 85, 85, 1)',
                'size' => 30,
                'image' => '',
                'mediaId' => false,
                'delay' => 0
            ],
            'backgroundcolor' => '#ffffff',
            'boxshadow' => true,
            'animationin' => 'fadeIn',
            'animationout' => 'fadeOut',
            'animation_type' => 'slide',
            'duration' => 0.3,
            'border' => [
                'width' => '0',
                'style' => 'solid',
                'color' => 'rgba(0, 0, 0, 0.4)'
            ],
            'borderradius' => [
                'desktop' => [
                    'topLeft' => '0px',
                    'topRight' => '0px',
                    'bottomLeft' => '0px',
                    'bottomRight' => '0px'
                ]
            ],
            // Background Overlay
            'overlay' => true,
            'overlay_color' => 'rgba(0, 0, 0, 0.2)',
            'overlayblurradius' => 0,
            'overlayclick' => true,
            // Background Image
            'bgimage' => false,
            'bgimagefile' => '',
            'bgrepeat' => 'Repeat',
            'bgsize' => 'Auto',
            'bgposition' => 'Left Top',

            // Advanced
            'customcode' => '',
            'customcss' => '',
            'testmode' => false,
            'preventpagescroll' => false,
            'stats' => true,
            'classsuffix' => '',
            'zindex' => 99999,

            

            // Deprecated
            'fontsize' => [
                'desktop' => '16px'
            ],
            'textcolor' => '#444444',
            'aligncontent' => ''
        ];
    }

    public function register()
    {
        register_meta('post', 'firebox_meta', [
			'object_subtype' => 'firebox',
            'show_in_rest' => [
                'schema' => [
                    'type' => 'object',
                    'additionalProperties' => true
                ]
            ],
            'type' => 'object',
            'single' => true,
            'auth_callback' => function($allowed, $meta_key, $post_id, $user_id, $cap, $caps) {
				return current_user_can('edit_fireboxes');
            }
        ]);
    }
}