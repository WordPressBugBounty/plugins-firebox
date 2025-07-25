<?php
/**
 * @package         FireBox
 * @version         3.0.0 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Helpers\Form;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class SubmissionMeta
{
	/**
	 * Creates the submision meta.
	 * 
	 * @param   int    $submission_id
	 * @param   array  $fields_values
	 * @param   bool   $save
	 * 
	 * @return  array
	 */
	public static function create($submission_id = null, $fields_values = [], $save = true)
	{
		if (!$fields_values)
		{
			return;
		}

		$meta = [];
		
		// Save submission meta
		foreach ($fields_values as $key => $value)
		{
			$submission_meta_payload = [
				'submission_id' => $submission_id,
				'meta_type' => '',
				'meta_key' => $key,
				'meta_value' => is_array($value) ? wp_json_encode($value) : $value,
				'created_at' => gmdate('Y-m-d H:i:s'),
				'modified_at' => null
			];
			
			if ($save)
			{
				if (!firebox()->tables->submissionmeta->insert($submission_meta_payload))
				{
					return;
				}
			}

			$meta[] = $submission_meta_payload;
		}

		return $meta;
	}

    /**
     * Retrieves the meta row.
     * 
     * @param   int     $submission_id
     * @param   string  $type
     * @param   string  $key
     * 
     * @return  mixed
     */
    public static function getMeta($submission_id, $type = '', $key = '')
    {
        if (!$submission_id)
        {
            return;
        }

		$where = [
			'submission_id = ' => "'" . esc_sql($submission_id) . "'",
			'meta_type = ' => "'" . esc_sql($type) . "'"
		];

        if (!empty($key))
        {
            $where['meta_key = '] = "'" . esc_sql($key) . "'";
        }

		return firebox()->tables->submissionmeta->getResults([
			'where' => $where
		], true);
    }
}