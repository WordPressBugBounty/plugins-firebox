<?php
/**
 * @package         FireBox
 * @version         3.1.15 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Form\Fields\Fields;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Filter;

class Rating extends \FireBox\Core\Form\Fields\Field
{
	protected $type = 'rating';

	/**
	 * Validate the field.
	 *
	 * A rating is always numeric, so anything else is rejected rather than merely
	 * sanitized. Without this the raw submitted value reaches the database and is
	 * later re-read by the "raw" Smart Tag and by the submissions list.
	 *
	 * @param   mixed  $value
	 *
	 * @return  bool
	 */
	public function validate(&$value = '')
	{
		$value = Filter::getInstance()->clean($value);

		// Nothing submitted. Let the parent decide based on "required".
		if ($value === '' || $value === null)
		{
			$value = '';
			return parent::validate($value);
		}

		if (!is_numeric($value))
		{
			$this->validation_message = firebox()->_('FB_FORM_ERROR_INVALID_RATING');
			return false;
		}

		$max = (float) $this->getOptionValue('maxRating', 5);
		$max = $max > 0 ? $max : 5;

		// Clamp into range so a crafted request cannot store an arbitrary number.
		$value = (string) max(0, min($max, (float) $value));

		return parent::validate($value);
	}

	/**
	 * Returns the field input.
	 *
	 * @return  void
	 */
	public function getInput()
	{
		$selectedValue = $this->getOptionValue('value') ? $this->getOptionValue('value') : ($this->getOptionValue('placeholder') ? '' : '');

		$payload = [
			'value' => $selectedValue,
			'icon' => $this->getOptionValue('icon'),
			'size' => $this->getOptionValue('size'),
			'max_rating' => $this->getOptionValue('maxRating'),
			'half_ratings' => $this->getOptionValue('halfRatings'),
			'selected_color' => $this->getOptionValue('selectedColor'),
			'unselected_color' => $this->getOptionValue('unselectedColor'),
			'input_class' => implode(' ', $this->getOptionValue('inputCssClass', [])) . ' fb-form-input',
			'css_class' => implode(' ', $this->getOptionValue('cssClass', [])),
			'required' => $this->getOptionValue('required'),
			'name' => 'fb_form[' . $this->getOptionValue('name') . ']'
		];

		echo \FPFramework\Base\Widgets\Helper::render('Rating', $payload); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}