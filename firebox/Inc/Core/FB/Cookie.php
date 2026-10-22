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

namespace FireBox\Core\FB;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

class Cookie
{
    /**
     * The box instance
     *
     * @var object
     */
    private $box;

    /**
     * The cookie instance
     * 
     * @var object
     */
    private $cookie;

    /**
     * The value of the cookie
     *
     * @var int
     */
    private $cookie_value = 1;

    /**
     * Class constructor
     *
     * @param integer $box_id   The box's ID
     */
    public function __construct($box)
    {
        $this->box = $box;
        $this->cookie = new \FPFramework\Libs\Cookie();
    }

    /**
     * Get the name of the cookie
     *
     * @return string
     */
    private function getName()
    {
        return isset($this->box->ID) ? \FireBox\Core\Helpers\BoxHelper::cookiePrefix() . $this->box->ID : '';
    }

    /**
     * Store cookie in the browser 
     *
     * @return void
     */
    public function set()
    {
        if (!is_object($this->box) || !isset($this->box->params))
        {
            return;
        }

        $cookie_type = $this->box->params->get('assign_cookietype', 'days');

        switch ($cookie_type)
        {
            case 'never':
                return;
                
            case 'ever':
                $expire = strtotime('now +20 years'); // forever
                break;

            case 'custom':
                $assign_cookietype_param_custom_period_times = (int) $this->box->params->get('assign_cookietype_param_custom_period_times', 1);
                $assign_cookietype_param_custom_period = $this->box->params->get('assign_cookietype_param_custom_period', 'days');
                
                $expire = strtotime('now +' . (int) $assign_cookietype_param_custom_period_times . ' ' . $assign_cookietype_param_custom_period);
                break;

            default:
                $expire = 0; // session
        }

        $this->cookie->set(
            $this->getName(),
            $this->cookie_value,
            $expire,
            \FireBox\Core\Helpers\BoxHelper::cookiePath(),
            \FireBox\Core\Helpers\BoxHelper::cookieDomain(),
            true
        );
    }
    
    /**
     * Check if the cookie of the box exist
     *
     * @return bool
     */
    public function exist()
    {
        return $this->cookie->get($this->getName());
    }

    /**
     * Removes the cookie from the browser
     *
     * @return void
     */
    public function remove()
    {
        // Expire it on every path it may have been written to, or an older
        // cookie at '/' would keep suppressing the campaign.
        foreach (\FireBox\Core\Helpers\BoxHelper::cookiePathsToClear() as $path)
        {
            $this->cookie->set(
                $this->getName(),
                $this->cookie_value,
                strtotime('-1 day'),
                $path,
                \FireBox\Core\Helpers\BoxHelper::cookieDomain()
            );
        }
    }
}