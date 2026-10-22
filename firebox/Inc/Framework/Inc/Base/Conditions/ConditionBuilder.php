<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.7
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\Conditions;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Conditions\ConditionsHelper;
use FPFramework\Base\Extension;
use FPFramework\Base\FieldsParser;

class ConditionBuilder
{
    public static function pass($rules, $factory = null)
    {
        $rules = self::prepareRules($rules);

        if (empty($rules))
        {
            return true;
        }

        return ConditionsHelper::getInstance($factory)->passSets($rules);
    }

    /**
     * Like pass(), but defers client-side conditions (Condition::isClientSide())
     * to the frontend runtime instead of evaluating them on the server.
     *
     * Rule groups are OR'ed together; within a group, rules combine per its
     * matching_method (all|any). Server-side rules are evaluated here; when a
     * group's outcome still depends on client-side rules, those rules are
     * returned so the caller can ship them to the browser.
     *
     * Returns:
     * - pass:          whether the campaign should be rendered on the page.
     * - client_groups: groups the browser must resolve before showing the
     *                  campaign (empty when a group already passed outright —
     *                  the campaign then shows unconditionally).
     *
     * @param   array   $rules
     * @param   object  $factory
     *
     * @return  array   ['pass' => bool, 'client_groups' => array]
     */
    public static function passWithClientRules($rules, $factory = null)
    {
        $rules = self::prepareRules($rules);

        if (empty($rules))
        {
            return ['pass' => true, 'client_groups' => []];
        }

        $helper = ConditionsHelper::getInstance($factory);

        $clientGroups = [];

        foreach ($rules as $group)
        {
            $matchingMethod = isset($group['matching_method']) ? $group['matching_method'] : 'all';

            $serverRules = [];
            $clientRules = [];

            foreach ($group['rules'] as $rule)
            {
                if ($helper->isClientSideCondition($rule['name']))
                {
                    $clientRules[] = [
                        'name'     => $rule['name'],
                        'operator' => isset($rule['operator']) ? $rule['operator'] : '',
                        'value'    => isset($rule['value']) ? $rule['value'] : ''
                    ];
                    continue;
                }

                $serverRules[] = $rule;
            }

            $serverPass = empty($serverRules) ? null : $helper->passSet($serverRules, $matchingMethod);

            if ($matchingMethod === 'any')
            {
                // A passing server rule settles the group, and a passing group settles the check.
                if ($serverPass === true)
                {
                    return ['pass' => true, 'client_groups' => []];
                }

                // The group can still pass if a client rule passes in the browser.
                if (!empty($clientRules))
                {
                    $clientGroups[] = ['matching_method' => 'any', 'rules' => $clientRules];
                }

                continue;
            }

            // 'all': a failing server rule sinks the group regardless of client rules.
            if ($serverPass === false)
            {
                continue;
            }

            // Every server rule passed; the group is settled unless client rules remain.
            if (empty($clientRules))
            {
                return ['pass' => true, 'client_groups' => []];
            }

            $clientGroups[] = ['matching_method' => 'all', 'rules' => $clientRules];
        }

        // No group passed outright: render only if the browser can still resolve one.
        return [
            'pass'          => !empty($clientGroups),
            'client_groups' => $clientGroups
        ];
    }

    /**
     * Prepare rules object to run checks
     *
     * @return  array
     */
    public static function prepareRules($rules = [])
    {
        if (!$rules || !is_array($rules))
        {
            return [];
        }
        
        $rules_ = [];

        foreach ($rules as $key => $group)
        {
            if (isset($group['enabled']) AND !(bool) $group['enabled'])
            {
                continue;
            }

            // A group without rules, doesn't make sense.
            if (!isset($group['rules']) OR (isset($group['rules']) AND empty($group['rules'])))
            {
                continue;
            }

            $validRules = [];

            foreach ($group['rules'] as $rule)
            {
                // Make sure rule has a name.
                if (!isset($rule['name']) OR (isset($rule['name']) AND empty($rule['name'])))
                {
                    continue;
                }

                // Rule is invalid if both value and params properties are empty
                if (!isset($rule['value']) && !isset($rule['params']))
                {
                    continue;
                }

                if (isset($rule['value']) && is_array($rule['value']) && empty($rule['value']))
                {
                    continue;
                }

                // Skip disabled rules
                if (isset($rule['enabled']) && !(bool) $rule['enabled'])
                {
                    continue;
                }

                // We don't need this property.
                unset($rule['enabled']);

                // Prepare rule value if necessary
                if (isset($rule['value']))
                {
                    $rule['value'] = self::prepareRepeaterValue($rule['value']);
                }

                // Verify operator
                if (!isset($rule['operator']) OR (isset($rule['operator']) && empty($rule['operator'])))
                {
                    $rule['operator'] = isset($rule['params']['operator']) ? $rule['params']['operator'] : '';
                }

                $validRules[] = $rule;
            }

            if (count($validRules) > 0)
            {
                $group['rules'] = $validRules;

                if (!isset($group['matching_method']) OR (isset($group['matching_method']) AND empty($group['matching_method'])))
                {
                    $group['matching_method'] = 'all';
                }

                unset($group['enabled']);
                $rules_[] = $group;
            }
        }

        return $rules_;
    }
    
    /**
     * Parse the value of the FPF Repeater Input field.
     * 
     * @param   array  $selection
     * 
     * @return  mixed
     */
    public static function prepareRepeaterValue($selection)
    {
        // Only proceed when we have an array of arrays selection.
        if (!is_array($selection))
        {
            return $selection;
        }

        $new_selection = [];

        foreach ($selection as $value)
        {
            /**
            * We expect a `value` key for Repeater fields or a key,value pair
            * for plain arrays.
            */
            if (!isset($value['value']))
            {
                $new_selection[] = $value;
                continue;
            }

            // value must not be empty
            if (empty(trim($value['value'])))
            {
                continue;
            }

            $new_selection[] = count($value) === 1 ? $value['value'] : $value;
        }

        return $new_selection;
    }
}