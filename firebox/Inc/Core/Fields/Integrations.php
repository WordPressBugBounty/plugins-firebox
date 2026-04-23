<?php
/**
 * @package         FireBox
 * @version         3.1.6 Free
 *
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace FireBox\Core\Fields;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Field;
use FPFramework\Libs\Registry;

class Integrations extends Field
{
	/**
	 * Set specific field options.
	 *
	 * @param   array  $options
	 *
	 * @return  void
	 */
	protected function setFieldOptions($options)
	{
		$options = new Registry($options);

		$this->field_options = [
			'render_group' => false,
			'render_top' => false,
			'integrations' => $options->get('integrations', [])
		];
	}
}

