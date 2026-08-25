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
			return [];
		}

		$meta = [];
		$rows = [];

		// Build submission meta
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

			$rows[] = $submission_meta_payload;
			$meta[] = $submission_meta_payload;
		}

		if ($save)
		{
			if (!self::insertMany($rows))
			{
				return;
			}
		}

		return $meta;
	}

	/**
	 * Batch-inserts multiple submission meta rows using a single multi-row INSERT
	 * instead of one INSERT per field.
	 *
	 * @param   array  $rows
	 *
	 * @return  bool
	 */
	private static function insertMany($rows)
	{
		if (!$rows)
		{
			return true;
		}

		global $wpdb;

		$table = firebox()->tables->submissionmeta->getFullTableName();

		$placeholders = [];
		$values = [];

		foreach ($rows as $row)
		{
			$placeholders[] = '(%d, %s, %s, %s, %s, %s)';

			$values[] = $row['submission_id'];
			$values[] = $row['meta_type'];
			$values[] = $row['meta_key'];
			$values[] = $row['meta_value'];
			$values[] = $row['created_at'];
			$values[] = $row['modified_at'];
		}

		$sql = "INSERT INTO `{$table}` (`submission_id`, `meta_type`, `meta_key`, `meta_value`, `created_at`, `modified_at`) VALUES " . implode(', ', $placeholders);

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->query($wpdb->prepare($sql, $values));

		return false !== $result;
	}

	/**
	 * Retrieves submission meta for multiple submissions in chunked IN() queries,
	 * grouped by submission_id, to avoid one query per submission.
	 *
	 * @param   array  $submission_ids
	 *
	 * @return  array
	 */
	public static function getMetaForSubmissions($submission_ids = [])
	{
		if (!$submission_ids)
		{
			return [];
		}

		$meta_by_submission = [];

		foreach (array_chunk($submission_ids, 500) as $id_chunk)
		{
			$ids_in = implode(',', array_map('absint', $id_chunk));

			$chunk_meta = firebox()->tables->submissionmeta->getResults([
				'where' => [
					'submission_id' => " IN (" . $ids_in . ")"
				]
			]);

			foreach ($chunk_meta as $meta_item)
			{
				$meta_by_submission[$meta_item->submission_id][] = $meta_item;
			}
		}

		return $meta_by_submission;
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
			'submission_id = ' => "'" . sanitize_key($submission_id) . "'",
			'meta_type = ' => "'" . sanitize_key($type) . "'"
		];

        if (!empty($key))
        {
            $where['meta_key = '] = "'" . sanitize_key($key) . "'";
        }

		return firebox()->tables->submissionmeta->getResults([
			'where' => $where
		], true);
    }
}