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

namespace FireBox\Core\Form\Fields\Fields;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Filter;

class Email extends \FireBox\Core\Form\Fields\Field
{
	protected $type = 'email';

	/**
	 * Validate the field.
	 * 
	 * @param   string  $value
	 * 
	 * @return  void
	 */
	public function validate(&$value = '')
	{
		$value = Filter::getInstance()->clean($value, 'sanitize_email');
		
		if (!parent::validate($value))
		{
			return false;
		}

		if (!filter_var($value, FILTER_VALIDATE_EMAIL))
		{
			$this->validation_message = firebox()->_('FB_PLEASE_ENTER_A_VALID_EMAIL_ADDRESS');
			return false;
		}
		
		return true;
	}

	/**
	 * Returns the field input.
	 * 
	 * @return  void
	 */
	public function getInput()
	{
		?>
		<input
			type="email"
			name="fb_form[<?php echo esc_attr($this->getOptionValue('name')); ?>]"
			value="<?php echo esc_attr($this->getOptionValue('value')); ?>"
			placeholder="<?php echo esc_attr($this->getOptionValue('placeholder')); ?>"
			class="<?php echo esc_attr(implode(' ', $this->getOptionValue('input_css_class'))); ?>"
		/>
		<?php
	}
}