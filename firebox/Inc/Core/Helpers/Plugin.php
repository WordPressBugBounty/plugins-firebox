<?php
/**
 * @package         FireBox
 * @version         3.1.4
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2025 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Plugin
{
	public static function getLatestVersionData()
	{
		$url = FPF_GET_LICENSE_VERSION_URL . 'firebox';

		$response = wp_remote_get($url);

		if (!is_array($response))
		{
			return;
		}

		$response_decoded = null;

		try
		{
			$response_decoded = json_decode($response['body'], true);
		}
		catch (Exception $ex)
		{
			return;
		}

		if (!isset($response_decoded['version']))
		{
			return;
		}

		return $response_decoded;
	}
	
	/**
	 * Checks whether the plugin is outdated.
	 * 
	 * @param   int     $days_old
	 * 
	 * @return  bool
	 */
	public static function isOutdated($days_old = 120)
	{
        if (!defined('FBOX_RELEASE_DATE'))
        {
			return false;
		}
		
		if (!$then = strtotime(FBOX_RELEASE_DATE))
		{
			return false;
		}

		$days_old = (int) $days_old;
		$now = time();
		$diff = $now - $then;
		$days_diff = round($diff / (60 * 60 * 24));

		if ($days_diff <= $days_old)
		{
			return false;
		}

		return true;
	}

	/**
	 * Returns the installation date.
	 * 
	 * @return  string
	 */
	public static function getInstallationDate()
	{
		$path = self::getExtensionsDataFilePath();
		
		// If file does not exist, abort
		if (!file_exists($path))
		{
			return;
		}

		// If file exists, retrieve its contents
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents($path);

		// Decode it
		if (!$content = json_decode($content, true))
		{
			return;
		}

		// Ensure install date exists
		if (!isset($content['install_date']))
		{
			return;
		}
		
		return $content['install_date'];
	}

	public static function getExtensionItemValue($key = null)
	{
		$path = self::getExtensionsDataFilePath();

		// If file does not exist, abort
		if (!file_exists($path))
		{
			return;
		}

		// If file exists, retrieve its contents
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents($path);

		// Decode it
		if (!$content = json_decode($content, true))
		{
			return;
		}

		// Ensure key exists
		if (!isset($content[$key]))
		{
			return;
		}

		return $content[$key];
	}

	/**
	 * Sets or updates a key-value pair in the extensions data file.
	 * 
	 * @param   string  $key           The key to set
	 * @param   mixed   $value         The value to set
	 * @param   bool    $force_replace Whether to replace existing value regardless of age
	 * @param   int     $max_age_days  Maximum age in days before replacing existing value (only used if force_replace is false)
	 * 
	 * @return  bool    True if the value was set/updated, false if key already exists and no update was needed
	 */
	public static function setExtensionData($key, $value, $force_replace = false, $max_age_days = 0)
	{
		$path = self::getExtensionsDataFilePath();

		// If file does not exist, create it with the key-value pair
		if (!file_exists($path))
		{
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			file_put_contents($path, wp_json_encode(
				[
					$key => $value
				]
			));
			return true;
		}

		// If file exists, retrieve its contents
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$content = file_get_contents($path);

		// Decode it
		$content = json_decode($content, true);

		// If decoding failed, initialize as empty array
		if ($content === null)
		{
			$content = [];
		}

		// Check if key already exists
		if (isset($content[$key]))
		{
			// If not forcing replacement, check age conditions
			if (!$force_replace)
			{
				// If max_age_days is 0, don't replace existing value
				if ($max_age_days <= 0)
				{
					return false;
				}

				// Check if file is older than max_age_days
				$file_modified_time = filemtime($path);
				$current_time = time();
				$age_in_seconds = $current_time - $file_modified_time;
				$age_in_days = floor($age_in_seconds / (60 * 60 * 24));
				
				// Only replace if file is older than max_age_days
				if ($age_in_days >= $max_age_days)
				{
					$content[$key] = $value;
				}
				else
				{
					return false;
				}
			}
			else
			{
				// Force replacement
				$content[$key] = $value;
			}
		}
		else
		{
			// Key doesn't exist, set it
			$content[$key] = $value;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		file_put_contents($path, wp_json_encode($content));
		
		return true;
	}

	/**
	 * The file path that stores all extensions data.
	 * 
	 * @return  string
	 */
	public static function getExtensionsDataFilePath()
	{
		return \FPFramework\Helpers\WPHelper::getPluginUploadsDirectory('firebox') . '/data.json';
	}
}