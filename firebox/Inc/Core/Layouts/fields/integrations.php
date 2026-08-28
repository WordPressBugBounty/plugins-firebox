<?php
/**
 * @package         FireBox
 * @version         3.1.12 Free
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

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- renders a trusted internal template.
echo firebox()->renderer->admin->render('pages/settings/integrations', [
	'integrations' => $this->data->get('integrations', [])
], true);
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

