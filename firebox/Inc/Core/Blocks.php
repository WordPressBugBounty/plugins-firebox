<?php
/**
 * @package         FireBox
 * @version         3.1.14 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Blocks
{
    public function __construct()
    {
		add_action('enqueue_block_assets', [$this, 'blocks_assets']);

		// Register categories
		add_filter('block_categories_all', [$this, 'register_categories'], 10, 2);

		$this->register_blocks();
    }

	/**
	 * Adds the `FireBox` and `FirePlugins` custom Gutenberg block categories
     * to register all our blocks.
	 * 
	 * @param   array					 $categories
	 * @param   WP_Block_Editor_Context  $context
	 * 
	 * @return  array
	 */
    public function register_categories($categories, $context)
    {
		return array_merge(
			$categories,
			[
				[
					'slug'  => 'firebox',
					'title' => 'FireBox'
				]
			]
		);
    }

    /**
     * Initialize all blocks
     * 
     * @return  void
     */
    public function register_blocks()
    {
		$ds = DIRECTORY_SEPARATOR;

		$base_dir = implode($ds, [__DIR__, 'Blocks']);

		$cache_key = 'firebox_block_files';
		$files = wp_cache_get($cache_key, 'firebox');

		if ($files === false)
		{
			$files = array_diff(scandir($base_dir), ['.', '..', '.DS_Store', 'index.php', 'Block.php']);

			wp_cache_set($cache_key, $files, 'firebox', HOUR_IN_SECONDS);
		}

		$namespace = '\FireBox\Core\Blocks\\';

		foreach ($files as $item)
		{
            // Check if this is a single file and load it
            $file = implode($ds, [$base_dir, $item]);

            if (!is_file($file))
            {
				continue;
			}

			$class_name = preg_replace('/.php$/', '', $item);
			
			$class = $namespace . $class_name;
			
			$block = new $class();
			$block->register();
		}
    }

    /**
     * Blocks assets.
     * 
     * - Extends functionality of the button/image blocks by allowing to trigger a popup.
     *   It also allows us to set custom CSS Classes to these blocks without triggering a popup.
     */
    public function blocks_assets()
    {
		if (!is_admin())
		{
			return;
		}
		
		wp_enqueue_script(
			'fb-blocks-extend',
			FBOX_MEDIA_ADMIN_URL . 'js/blocks/fb_blocks_extend.js',
			['wp-i18n', 'wp-blocks', 'wp-editor', 'wp-components', 'wp-api-fetch', 'lodash'],
			FBOX_VERSION,
			true
		);
    }
}