<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.6
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base\SmartTags;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Referrer extends SmartTag
{
    /**
     * Returns the URL referrer
     *
     * The Referer header is visitor-controlled and the replacement lands unescaped
     * in page HTML — so escape it here, the same way the URL smart tag does.
     *
     * @return  string
     */
    public function getReferrer()
    {
        return esc_url($this->factory->getReferrer());
    }
}