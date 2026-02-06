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

namespace FireBox\Core\Analytics\Transformers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class TransformerRegistry
{
	/**
	 * @var TransformerInterface[]
	 */
	private static $transformers = [];

	/**
	 * @var bool
	 */
	private static $initialized = false;

	/**
	 * Register a transformer
	 * 
	 * @param TransformerInterface $transformer
	 */
	public static function register(TransformerInterface $transformer)
	{
		self::$transformers[] = $transformer;
		
		// Sort by priority (lower numbers first)
		usort(self::$transformers, function($a, $b) {
			return $a->getPriority() <=> $b->getPriority();
		});
	}

	/**
	 * Get applicable transformers for a type
	 * 
	 * @param string $type
	 * @param array $options
	 * @return TransformerInterface[]
	 */
	public static function getApplicableTransformers($type, $options = [])
	{
		self::initializeDefault();
		
		return array_filter(self::$transformers, function($transformer) use ($type, $options) {
			return $transformer->shouldApply($type, $options);
		});
	}

	/**
	 * Apply all applicable transformers to data
	 * 
	 * @param array $data
	 * @param string $type
	 * @param array $options
	 */
	public static function transformData(&$data, $type, $options = [])
	{
		$transformers = self::getApplicableTransformers($type, $options);
		
		foreach ($transformers as $transformer) {
			$transformer->transform($data, $type, $options);
		}
	}

	/**
	 * Initialize default transformers
	 */
	private static function initializeDefault()
	{
		if (self::$initialized) {
			return;
		}

		// Register core transformers
		self::register(new TimezoneTransformer());
		self::register(new CountryTransformer());
		self::register(new DeviceTransformer());
		self::register(new EventTransformer());
		self::register(new UrlTransformer());
		self::register(new CampaignTransformer());

		self::$initialized = true;
	}

	/**
	 * Reset registry (mainly for testing)
	 */
	public static function reset()
	{
		self::$transformers = [];
		self::$initialized = false;
	}
}
