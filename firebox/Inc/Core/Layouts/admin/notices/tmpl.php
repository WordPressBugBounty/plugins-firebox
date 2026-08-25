<?php
/**
 * @package         FireBox
 * @version         3.1.11 Free
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}
?>
<div
	class="firebox-notices"
	<?php // esc_attr() already encodes the JSON for an attribute. The flagless
	// htmlspecialchars() this replaced was redundant, and its encoding varied with the PHP
	// version (8.1 switched the default to ENT_QUOTES). ?>
	data-exclude="<?php echo esc_attr(wp_json_encode($this->data->get('exclude'))); ?>"
	data-ajaxurl="<?php echo esc_attr(admin_url('admin-ajax.php')); ?>"
    data-nonce="<?php echo esc_attr(wp_create_nonce('firebox_notices')); ?>"
>
</div>