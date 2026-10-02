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

namespace FPFramework\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class FieldsHelper
{
	/**
	 * The meta option prefix used to store all meta data
	 * 
	 * @var  string
	 */
	public static $fields_prefix = 'fpframework_fields';

	/**
	 * Normalises a field option that holds CSS classes into a list.
	 *
	 * Field definitions come from PHP arrays, block attributes and template JSON, so a
	 * "class" option can arrive as an array, as a single class string, as a space
	 * separated string, or be missing altogether (Registry::get() returns null). Every
	 * consumer wants a list it can hand to implode()/count(), both of which throw a
	 * TypeError on anything else since PHP 8.0.
	 *
	 * @param   mixed  $value
	 *
	 * @return  array
	 */
	public static function toClassList($value)
	{
		// Every call site previously treated a falsy option as "no classes". Keep that,
		// so a stored 0/''/false/'0' does not turn into a class named "0".
		if (!$value)
		{
			return [];
		}

		if (is_array($value))
		{
			return array_values(array_filter($value, function ($class) {
				return is_scalar($class) && (string) $class !== '';
			}));
		}

		if (is_scalar($value) && (string) $value !== '')
		{
			return array_values(array_filter(explode(' ', (string) $value), 'strlen'));
		}

		return [];
	}

	/**
	 * Transforms an array(key, value) into html attributes
	 *
	 * @param   array  $atts
	 *
	 * @return  string
	 */
	public static function getHTMLAttributes($atts)
	{
		if (!$atts)
		{
			return '';
		}
		
		$atts = (array) $atts;

		if (!is_array($atts))
		{
			return '';
		}

		$html = implode(
			'',
			array_map(
				function ($k, $v)
				{
					if ($v == '' || (empty($v) && $v != 0))
					{
						return '';
					}

					// Flags are pinned so the encoding does not change with the runtime:
					// PHP 8.1 changed the default from ENT_COMPAT to ENT_QUOTES.
					$v = is_array($v) ? htmlspecialchars(wp_json_encode($v), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : $v;

					return $k . '="' . esc_attr($v) . '" ';
				},
				array_keys($atts),
				$atts
			)
		);
					
		return !empty($html) ? ' ' . $html : '';
	}

	/**
	 * Searches for a name in an array and returns its value or a default value.
	 * Can find value of keys : [name][name2][name3] in array
	 * 
	 * @param   string  $name
	 * @param   string  $default
	 * @param   array   $values
	 * 
	 * @return  string
	 */
	public static function findFieldValueInArray($name, $default, $values)
	{
		if (!is_string($name))
		{
			return null;
		}

		if (!is_array($values) || empty($values))
		{
			return null;
		}
		
		$values = is_object($values) ? (array) $values : $values;

		// field name: [name][name2][name3]
		if (substr_count($name, '[') > 1)
		{
			// split name
			$splited_name = explode('[', $name);
			unset($splited_name[0]);

			// find value
			$tmp_value = $values;

			foreach ($splited_name as $key => $_name)
			{
				$tmp_value = is_object($tmp_value) ? (array) $tmp_value : $tmp_value;
				
				$_name = str_replace(']', '', $_name);
				if (!isset($tmp_value[$_name]))
				{
					// key does not exist, return default
					$tmp_value = $default;
					break;
				}
				else
				{
					if (empty($tmp_value[$_name]) && $tmp_value[$_name] !== '0')
					{
						$tmp_value = $default;
						break;
					}
				}

				$tmp_value = $tmp_value[$_name];
			}

			// return value
			return $tmp_value;
		}
		else
		{
			// if the name contains the name prefix, remove it
			$name = strstr($name, '[') ? strstr($name, '[') : $name;
			// remove all brackets so we can search the data with a clean name
			$name = str_replace(['[', ']'], '', $name);

			return isset($values[$name]) && (!empty($values[$name]) || $values[$name] == '0') ? $values[$name] : $default;
		}
	}
}