<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.5
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

class URL extends SmartTag
{
    /**
     * Returns the URL
     *
     * The value derives from the raw request URI, which the visitor controls, and the
     * replacement lands unescaped in page HTML — so escape it here. esc_url() drops
     * the characters (quotes, angle brackets) that would make it reflected XSS while
     * keeping it usable in both text and href contexts.
     *
     * @return  string
     */
    public function getURL()
    {
        return esc_url($this->factory->getURL());
    }

    /**
     * Returns the URL encoded
     * 
     * @return  string
     */
    public function getEncoded()
    {
        return urlencode($this->factory->getURL());
    }

    /**
     * Returns the URL path
     * 
     * @return  string
     */
    public function getPath()
    {
        $path = $this->factory->getSiteData()->get('url');

        if (is_admin())
        {
            $path = get_admin_url();
        }
        
        return $path;
    }
}