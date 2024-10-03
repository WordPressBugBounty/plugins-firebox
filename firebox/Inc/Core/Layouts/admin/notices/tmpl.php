<?php
/**
 * @package         FireBox
 * @version         2.1.21 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2024 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

// get current url in WordPress


?>
<div
	class="firebox-notices"
	data-exclude="<?php esc_attr_e(htmlspecialchars(json_encode($this->data->get('exclude')))); ?>"
	data-ajaxurl="<?php esc_attr_e(admin_url('admin-ajax.php')); ?>"
    data-nonce="<?php esc_attr_e(wp_create_nonce('firebox_notices')); ?>"
>
</div>