<?php
/**
 * @package         FireBox
 * @version         2.1.26 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2024 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FireBox\Core\Form\Fields\Fields;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

use FPFramework\Base\Filter;

class Turnstile extends \FireBox\Core\Form\Fields\Field
{
	protected $type = 'turnstile';

	protected $siteKey = '';

	public function __construct($options = [])
	{
		parent::__construct($options);

		$this->siteKey = $this->getSiteKey();

		// Empty the name in order to exclude this field from being stored in the database.
		$this->setOptionValue('name', '');
	}

	/**
	 * Get the site key.
	 * 
	 * @return  string
	 */
	private function getSiteKey()
	{
		$settings = get_option('firebox_settings');
		return isset($settings['cloudflare_turnstile_site_key']) ? $settings['cloudflare_turnstile_site_key'] : '';
	}

	/**
	 * Get Turnstile Secret Key
	 * 
	 * @return  string
	 */
	private function getSecretKey()
	{
		$settings = get_option('firebox_settings');
		return isset($settings['cloudflare_turnstile_secret_key']) ? $settings['cloudflare_turnstile_secret_key'] : '';
	}
	
	/**
	 * Validate the field.
	 * 
	 * @param   mixed  $value
	 * 
	 * @return  void
	 */
	public function validate(&$value = '')
	{
        $integration = new \FPFramework\Base\Integrations\Turnstile(
            ['secret' => $this->getSecretKey()]
        );

		$response = isset($this->data['cf-turnstile-response']) ? $this->data['cf-turnstile-response'] : null;

        $integration->validate($response);

        if (!$integration->success())
        {
			$this->validation_message = $integration->getLastError();
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
		if (empty($this->siteKey) || empty($this->getSecretKey()))
		{
			?>
			<div class="form-error-message"><?php echo esc_html(firebox()->_('FB_ENTER_CLOUDFLARE_TURNSTILE_KEYS')); ?>
			<?php
			return;
		}
		
		wp_enqueue_script(
			'firebox-cloudflare-turnstile',
			'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=FireBoxInitCloudflareTurnstile',
			[],
			FBOX_VERSION,
			true
		);
		wp_enqueue_script('firebox-turnstile',
			FBOX_MEDIA_PUBLIC_URL . 'js/turnstile.js',
			[],
			FBOX_VERSION,
			true
		);
		
		?>
		<div
			class="firebox-form-field-turnstile"
			data-sitekey="<?php echo esc_attr($this->siteKey); ?>"
			data-language="<?php echo esc_attr(get_bloginfo ('language')); ?>"
			data-theme="<?php echo esc_attr($this->getOptionValue('theme')); ?>"
			data-size="<?php echo esc_attr($this->getOptionValue('size')); ?>"></div>
		<?php
	}
}