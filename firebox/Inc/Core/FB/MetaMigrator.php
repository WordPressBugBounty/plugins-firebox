<?php
/**
 * @package         FireBox
 * @version         3.0.1 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\FB;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

/**
 * Handles migration of legacy meta data to new meta format
 */
class MetaMigrator
{
    /**
     * Apply a transformation to meta data
     *
     * @param string $transformType The type of transformation to apply
     * @param string $key The meta key being transformed
     * @param array $new_meta Reference to the new meta data
     * @param array $legacy_meta The legacy meta data
     * @param array $defaults Default values
     * @param bool $valueExistsInNewMeta Whether the value already exists in new meta
     * @return void
     */
    public function applyTransform($transformType, $key, &$new_meta, $legacy_meta, $defaults, $valueExistsInNewMeta)
    {
        switch ($transformType)
        {
            case 'migrate_unitvalue':
                $this->migrateUnitValue($key, $new_meta, $valueExistsInNewMeta);
                break;
            case 'border_migration':
                $this->migrateBorder($key, $new_meta, $legacy_meta, $valueExistsInNewMeta);
                break;
            case 'borderradius_migration':
                $this->migrateBorderRadius($key, $new_meta, $valueExistsInNewMeta);
                break;
            case 'dimension':
                $this->migrateDimension($key, $new_meta);
                break;
            case 'box_auto_close_migration':
                $this->migrateBoxAutoClose($key, $new_meta);
                break;
            case 'actions_migration':
                $this->migrateActions($key, $new_meta);
                break;
            case 'rules_migration':
                $this->migrateRules($key, $new_meta, $defaults);
                break;
            case 'animation_migration':
                $this->migrateAnimation($key, $new_meta, $valueExistsInNewMeta);
                break;
            case 'shadow_migration':
                $this->migrateShadow($key, $new_meta, $valueExistsInNewMeta);
                break;
            case 'floatingbutton_message_migration':
                $this->migrateFloatingButtonMessage($key, $new_meta, $defaults, $valueExistsInNewMeta);
                break;
            case 'overlay_enabled_migration':
                $this->migrateOverlayEnabled($key, $new_meta, $valueExistsInNewMeta);
                break;
            case 'overlayclick_migration':
                $this->migrateOverlayClick($key, $new_meta, $valueExistsInNewMeta);
                break;
            case 'overlayblurradius_migration':
                $this->migrateOverlayBlurRadius($key, $new_meta, $valueExistsInNewMeta);
                break;
            case 'triggermethod_migration':
                $this->migrateTriggerMethod($key, $new_meta, $legacy_meta, $valueExistsInNewMeta);
                break;
            case 'scroll_depth_migration':
                $this->migrateScrollDepth($key, $new_meta, $legacy_meta, $valueExistsInNewMeta);
                break;
            case 'firing_frequency_toggle_migration':
                $this->migrateFiringFrequencyToggle($key, $new_meta, $valueExistsInNewMeta);
                break;
        }
    }

    /**
     * Migrate unit values
     *
     * @param string $key
     * @param array $new_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateUnitValue($key, &$new_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        if (!isset($new_meta[$key]) || !is_array($new_meta[$key]))
        {
            return;
        }

        foreach ($new_meta[$key] as $subkey => &$subvalue)
        {
            if (!isset($subvalue['value']) && !isset($subvalue['unit']))
            {
                continue;
            }
            
            $value = isset($subvalue['value']) ? $subvalue['value'] : '';
            if (!$value)
            {
                $subvalue = '';

                // if empty height, set to auto
                if ($key === 'height')
                {
                    $subvalue = 'auto';
                }
                continue;
            }

            $unit = isset($subvalue['unit']) ? $subvalue['unit'] : '';
            $subvalue = $value . $unit;
        }
    }

    /**
     * Migrate border settings
     *
     * @param string $key
     * @param array $new_meta
     * @param array $legacy_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateBorder($key, &$new_meta, $legacy_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        if (!isset($legacy_meta['bordertype']) || $legacy_meta['bordertype'] === 'none')
        {
            return;
        }

        $borderStyle = isset($legacy_meta['bordertype']) ? $legacy_meta['bordertype'] : 'solid';
        if (!in_array($borderStyle, ['solid', 'dashed', 'dotted']))
        {
            $borderStyle = 'solid';
        }

        $new_meta['border'] = [
            'width' => isset($legacy_meta['borderwidth']['value']) ? rtrim($legacy_meta['borderwidth']['value'], 'px') . 'px' : '',
            'style' => $borderStyle,
            'color' => isset($legacy_meta['bordercolor']) ? $legacy_meta['bordercolor'] : ''
        ];
    }

    /**
     * Migrate border radius settings
     *
     * @param string $key
     * @param array $new_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateBorderRadius($key, &$new_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        if (!isset($new_meta[$key]) || !is_array($new_meta[$key]))
        {
            return;
        }

        foreach ($new_meta[$key] as $breakpoint => &$values)
        {
            $unit = isset($values['unit']) ? $values['unit'] : 'px';
            
            $values = [
                'topLeft' => isset($values['top_left']) ? $values['top_left'] . $unit : '',
                'topRight' => isset($values['top_right']) ? $values['top_right'] . $unit : '',
                'bottomLeft' => isset($values['bottom_left']) ? $values['bottom_left'] . $unit : '',
                'bottomRight' => isset($values['bottom_right']) ? $values['bottom_right'] . $unit : ''
            ];
        }
    }

    /**
     * Migrate dimension values
     *
     * @param string $key
     * @param array $new_meta
     * @return void
     */
    public function migrateDimension($key, &$new_meta)
    {
        if (!is_array($new_meta[$key]) || !count($new_meta[$key]))
        {
            return;
        }

        foreach ($new_meta[$key] as $breakpoint => &$values)
        {
            $unit = isset($values['unit']) ? $values['unit'] : '';

            if (!is_array($values) || !count($values))
            {
                continue;
            }

            foreach ($values as $_prop => &$val)
            {
                if (in_array($_prop, ['top', 'right', 'bottom', 'left']))
                {
                    if ($val)
                    {
                        $val = $val . $unit;
                    }
                } else {
                    unset($values[$_prop]);
                }
            }
        }
    }

    /**
     * Migrate box auto close setting
     *
     * @param string $key
     * @param array $new_meta
     * @return void
     */
    public function migrateBoxAutoClose($key, &$new_meta)
    {
        if (!is_array($new_meta[$key]) || !count($new_meta[$key]))
        {
            return;
        }

        $new_meta[$key] = ($new_meta[$key][0] === 'yes');
    }

    /**
     * Migrate actions settings
     *
     * @param string $key
     * @param array $new_meta
     * @return void
     */
    public function migrateActions($key, &$new_meta)
    {
        if (!is_array($new_meta[$key]) || !count($new_meta[$key]))
        {
            return;
        }

        $actions = array_values($new_meta[$key]);
        foreach ($actions as &$action)
        {
            $action['enabled'] = isset($action['enabled']) && $action['enabled'] == '1';
        }

        $new_meta[$key] = $actions;
    }

    /**
     * Migrate rules settings
     *
     * @param string $key
     * @param array $new_meta
     * @param array $defaults
     * @return void
     */
    public function migrateRules($key, &$new_meta, $defaults)
    {
        $rules = is_string($new_meta[$key]) ? json_decode($new_meta[$key], true) : $new_meta[$key];

        if (empty($rules))
        {
            // Set default rules if empty
            $new_meta[$key] = $defaults['rules'];
            return;
        }

        $rules = array_values($rules);
        foreach ($rules as &$set)
        {
            $set['enabled'] = isset($set['enabled']) && $set['enabled'] == '1';
            
            if (!isset($set['rules']) || !is_array($set['rules']) || !count($set['rules']))
            {
                continue;
            }

            foreach ($set['rules'] as &$rule)
            {
                $rule['enabled'] = isset($rule['enabled']) && $rule['enabled'] == '1';

                // Fix name
                if (isset($rule['name']))
                {
                    $rule['name'] = str_replace('\\\\', '\\', $rule['name']);
                }

                // Fix "params.exclude_shipping_cost"
                if (isset($rule['params']['exclude_shipping_cost']))
                {
                    $rule['params']['exclude_shipping_cost'] = isset($rule['params']['exclude_shipping_cost']) && $rule['params']['exclude_shipping_cost'] == '1';
                }

                // Migrate value from array of objects to array of values
                $migrateValueConditions = [
                    'IP',
                    'Referrer',
                    'URL',
                    'Geo\City',
                    'Geo\Region'
                ];
                if (isset($rule['name']) && in_array($rule['name'], $migrateValueConditions))
                {
                    if (isset($rule['value']) && is_array($rule['value']))
                    {
                        $migratedValues = [];
                        foreach ($rule['value'] as $item)
                        {
                            $old_value = isset($item['value']) ? $item['value'] : '';

                            if (!$old_value)
                            {
                                continue;
                            }

                            $migratedValues[] = $old_value;
                        }
                        $rule['value'] = $migratedValues;
                    }
                }

                // Fix regex
                if (isset($rule['name']) && $rule['name'] === 'URL')
                {
                    $rule['params']['regex'] = isset($rule['params']['regex']) && $rule['params']['regex'] == '1';
                }

                // Fix quantity operators for WooCommerce Cart Contains Products rule
                if (isset($rule['name']) && in_array($rule['name'], ['WooCommerce\CartContainsProducts']))
                {
                    $value = isset($rule['value']) ? $rule['value'] : [];

                    if (is_array($value))
                    {
                        foreach ($value as &$item)
                        {
                            if (isset($item['quantity_operator']))
                            {
                                if ($item['quantity_operator'] === 'less_than_equals')
                                {
                                    $item['quantity_operator'] = 'less_than_or_equal_to';
                                }
                                elseif ($item['quantity_operator'] === 'greater_than_equals')
                                {
                                    $item['quantity_operator'] = 'greater_than_or_equal_to';
                                }
                            }
                        }
                    }

                    $rule['value'] = $value;
                }
            }
        }

        $new_meta[$key] = $rules;
    }

    /**
     * Migrate animation settings
     *
     * @param string $key
     * @param array $new_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateAnimation($key, &$new_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        // Fade animations
        $fade_animations = [
            'transition.fadeIn' => 'fadeIn',
            'transition.fadeOut' => 'fadeOut',
            'transition.slideUpIn' => 'fadeInUp',
            'transition.slideDownIn' => 'fadeInDown',
            'transition.slideLeftIn' => 'fadeInLeft',
            'transition.slideRightIn' => 'fadeInRight',
            'transition.slideUpBigIn' => 'fadeInUpBig',
            'transition.slideDownBigIn' => 'fadeInDownBig',
            'transition.slideLeftBigIn' => 'fadeInLeftBig',
            'transition.slideRightBigIn' => 'fadeInRightBig',
            'transition.slideUpOut' => 'fadeOutUp',
            'transition.slideDownOut' => 'fadeOutDown',
            'transition.slideLeftOut' => 'fadeOutLeft',
            'transition.slideRightOut' => 'fadeOutRight',
            'transition.slideUpBigOut' => 'fadeOutUpBig',
            'transition.slideDownBigOut' => 'fadeOutDownBig',
            'transition.slideLeftBigOut' => 'fadeOutLeftBig',
            'transition.slideRightBigOut' => 'fadeOutRightBig',
        ];

        // Slide animations
        $slide_animations = [
            'slideDown' => 'slideInDown',
            'slideUp' => 'slideOutUp',
            'firebox.slideUpIn' => 'slideInUp',
            'firebox.slideDownIn' => 'slideInDown',
            'firebox.slideLeftIn' => 'slideInLeft',
            'firebox.slideRightIn' => 'slideInRight',
            'firebox.slideUpOut' => 'fadeOutDown',
            'firebox.slideDownOut' => 'slideOutUp',
            'firebox.slideLeftOut' => 'slideOutLeft',
            'firebox.slideRightOut' => 'slideOutRight',
            'transition.perspectiveUpIn' => 'slideInUp',
            'transition.perspectiveDownIn' => 'slideInDown',
            'transition.perspectiveLeftIn' => 'slideInLeft',
            'transition.perspectiveRightIn' => 'slideInRight',
            'transition.perspectiveUpOut' => 'slideOutUp',
            'transition.perspectiveDownOut' => 'slideOutDown',
            'transition.perspectiveLeftOut' => 'slideOutLeft',
            'transition.perspectiveRightOut' => 'slideOutRight',
        ];

        $animation_map = array_merge([
            // Other animations
            'callout.bounce' => 'bounce',
            'callout.shake' => 'shakeX',
            'callout.flash' => 'flash',
            'callout.pulse' => 'pulse',
            'callout.swing' => 'swing',
            'callout.tada' => 'tada',
            'transition.swoopIn' => 'zoomIn',
            'transition.whirlIn' => 'zoomIn',
            'transition.shrinkIn' => 'zoomIn',
            'transition.expandIn' => 'zoomIn',
            'transition.flipXIn' => 'flipInX',
            'transition.flipYIn' => 'flipInY',
            'transition.flipBounceXIn' => 'flipInX',
            'transition.flipBounceYIn' => 'flipInY',
            'transition.bounceIn' => 'bounceIn',
            'transition.bounceUpIn' => 'bounceInUp',
            'transition.bounceDownIn' => 'bounceInDown',
            'transition.bounceLeftIn' => 'bounceInLeft',
            'transition.bounceRightIn' => 'bounceInRight',
            'transition.swoopOut' => 'zoomOut',
            'transition.whirlOut' => 'zoomOut',
            'transition.shrinkOut' => 'zoomOut',
            'transition.expandOut' => 'zoomOut',
            'transition.flipXOut' => 'flipOutX',
            'transition.flipYOut' => 'flipOutY',
            'transition.flipBounceXOut' => 'flipOutX',
            'transition.flipBounceYOut' => 'flipOutY',
            'transition.bounceOut' => 'bounceOut',
            'transition.bounceUpOut' => 'bounceOutUp',
            'transition.bounceDownOut' => 'bounceOutDown',
            'transition.bounceLeftOut' => 'bounceOutLeft',
            'transition.bounceRightOut' => 'bounceOutRight',
        ], $fade_animations, $slide_animations);
        
        $new_meta[$key] = array_key_exists($new_meta[$key], $animation_map) ? $animation_map[$new_meta[$key]] : $new_meta[$key];

        // Validate page slide animation in/out
        if (isset($new_meta['mode']) && $new_meta['mode'] === 'pageslide')
        {
            $isFadeAnimation = in_array($new_meta[$key], $fade_animations);
            $isSlideAnimation = in_array($new_meta[$key], $slide_animations);

            if ($key === 'animationin')
            {
                if (!$isFadeAnimation && !$isSlideAnimation)
                {
                    $new_meta[$key] = 'slideInDown';
                }
            }
            else if ($key === 'animationout')
            {
                if (!$isFadeAnimation && !$isSlideAnimation)
                {
                    $new_meta[$key] = 'slideOutUp';
                }
            }
        }
    }

    /**
     * Migrate shadow settings
     *
     * @param string $key
     * @param array $new_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateShadow($key, &$new_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        $new_meta[$key] = isset($new_meta[$key]) && $new_meta[$key] != '0';
    }

    /**
     * Migrate floating button message settings
     *
     * @param string $key
     * @param array $new_meta
     * @param array $defaults
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateFloatingButtonMessage($key, &$new_meta, $defaults, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        if (isset($new_meta[$key]) && empty($new_meta[$key]))
        {
            $new_meta[$key] = $defaults['floatingbutton_message'];
        }
        else if (isset($new_meta[$key]['fontsize']) && is_numeric($new_meta[$key]['fontsize']))
        {
            $new_meta[$key]['fontsize'] = $new_meta[$key]['fontsize'] . 'px';
        }
    }

    /**
     * Migrate overlay enabled setting
     *
     * @param string $key
     * @param array $new_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateOverlayEnabled($key, &$new_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        $overlay = isset($new_meta[$key]) ? (int) $new_meta[$key] : 0;
        $new_meta[$key] = $overlay == 1;
    }

    /**
     * Migrate overlay click setting
     *
     * @param string $key
     * @param array $new_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateOverlayClick($key, &$new_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        $overlayClick = isset($new_meta[$key]) ? (int) $new_meta[$key] : 0;
        $new_meta[$key] = !empty($overlayClick) && $overlayClick != 0;
    }

    /**
     * Migrate overlay blur radius setting
     *
     * @param string $key
     * @param array $new_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateOverlayBlurRadius($key, &$new_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        $overlayBlurRadius = isset($new_meta[$key]) ? (int) $new_meta[$key] : 0;
        if (empty($overlayBlurRadius) || $overlayBlurRadius == 0)
        {
            $new_meta[$key] = 0;
        }
        elseif ($overlayBlurRadius <= 10)
        {
            $new_meta[$key] = 2;
        }
        elseif ($overlayBlurRadius <= 30)
        {
            $new_meta[$key] = 14;
        } else {
            $new_meta[$key] = 45;
        }
    }

    /**
     * Migrate trigger method setting
     *
     * @param string $key
     * @param array $new_meta
     * @param array $legacy_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateTriggerMethod($key, &$new_meta, $legacy_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        if (isset($new_meta[$key]))
        {
            if ($new_meta[$key] === 'pageready')
            {
                $new_meta[$key] = 'pageload';
                $new_meta['early_trigger'] = true;
            }
            else if ($new_meta[$key] === 'floatingbutton')
            {
                $floating_button_position = isset($legacy_meta['floating_button_position']) ? $legacy_meta['floating_button_position'] : null;

                if ($floating_button_position)
                {
                    $new_meta['position'] = $floating_button_position;
                }
            }
        }
    }

    /**
     * Migrate scroll depth setting
     *
     * @param string $key
     * @param array $new_meta
     * @param array $legacy_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateScrollDepth($key, &$new_meta, $legacy_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        $scroll_amount = isset($legacy_meta['scroll_amount']) ? $legacy_meta['scroll_amount'] : 0;
        
        if ($scroll_amount)
        {
            $_value = isset($scroll_amount['value']) ? $scroll_amount['value'] : '80';
            $_unit = isset($scroll_amount['unit']) ? $scroll_amount['unit'] : '%';
            $new_meta[$key] = $_value . $_unit;
        }
    }

    /**
     * Migrate firing frequency toggle setting
     *
     * @param string $key
     * @param array $new_meta
     * @param bool $valueExistsInNewMeta
     * @return void
     */
    public function migrateFiringFrequencyToggle($key, &$new_meta, $valueExistsInNewMeta)
    {
        if ($valueExistsInNewMeta)
        {
            return;
        }

        $new_meta[$key] = $new_meta[$key] == '1';
    }
}
