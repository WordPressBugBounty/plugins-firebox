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

namespace FireBox\Core\Helpers;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Activation
{
	/**
	 * Creates library directories.
	 *
	 * @return  void
	 */
	public static function createLibraryDirectories()
	{
		/**
		 * This runs during activation, where the framework's autoloader does not
		 * exist yet: it is registered on `init`, and WordPress includes the
		 * plugin for the first time long after that hook has fired. The two
		 * classes below are therefore loaded by hand rather than left to the
		 * autoloader — every caller needed to remember this, and one that forgot
		 * took activation down with a fatal.
		 */
		require_once FBOX_PLUGIN_DIR . 'Inc/Framework/Inc/Helpers/Directory.php';
		require_once FBOX_PLUGIN_DIR . 'Inc/Framework/Inc/Helpers/WPHelper.php';

		// Create /wp-content/uploads/firebox and secure it
		\FPFramework\Helpers\Directory::createDirs(\FPFramework\Helpers\WPHelper::getPluginUploadsDirectory('firebox'));

		// Create /wp-content/uploads/firebox/templates and secure it
		\FPFramework\Helpers\Directory::createDirs(\FPFramework\Helpers\WPHelper::getPluginUploadsDirectory('firebox', 'templates'));
	}
}