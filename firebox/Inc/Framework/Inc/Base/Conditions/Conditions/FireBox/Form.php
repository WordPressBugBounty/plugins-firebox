<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.6
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\Conditions\Conditions\FireBox;

defined('ABSPATH') or die;

use FPFramework\Base\Conditions\Condition;

class Form extends Condition
{
    /**
     * Checks if the user has submitted a FireBox Form.
     * 
     * @return  bool
     */
    public function pass()
    {
        if (!function_exists('firebox'))
        {
            return false;
        }
        
        // Skip if the visitorID is not set
        if (!$visitorID = $this->factory->getVisitorID())
        {
            return true;
        }

        $form_ids  = $this->selection;
        if (!is_array($form_ids) || empty($form_ids))
        {
            return true;
        }

        // The selection is stored campaign data, so reduce each form ID to key characters before it reaches the query.
        $form_ids = array_filter(array_map('sanitize_key', $form_ids));
        if (empty($form_ids))
        {
            return false;
        }

        $results = firebox()->tables->submission->getResults([
            'where' => [
                'form_id' => " IN('" . implode("','", $form_ids) . "')",
                'visitor_id' => " = '" . sanitize_key($visitorID) . "'"
            ]
        ], false, true);

        $pass = (int) $results;

        return (bool) $pass;
	}
}