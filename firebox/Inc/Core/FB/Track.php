<?php
/**
 * @package         FireBox
 * @version         3.1.13 Free
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

class Track
{
    /**
     * Factory
     * 
     * @var  Factory
     */
    private $factory;

    /**
     * Conversions read from the cookie, waiting to be written on shutdown.
     *
     * @var  array
     */
    private $pendingConversions = [];

    public function __construct($factory = null)
    {
        if (!$factory)
        {
            $factory = new \FPFramework\Base\Factory();
        }
        $this->factory = $factory;

        /**
         * The cookie is read and cleared now — deleting it requires headers that have not
         * been sent yet — but the inserts it asks for run on shutdown, off the render path.
         */
        if ($conversions = $this->consumeConversionsCookie())
        {
            $this->pendingConversions = $conversions;
            add_action('shutdown', [$this, 'trackPendingConversions']);
        }

		$this->setupAjax();
    }

	/**
	 * Setup ajax requests
	 * 
	 * @return  void
	 */
	public function setupAjax()
	{
		// FB Track event AJAX
		add_action('wp_ajax_firebox_trackevent', [$this, 'firebox_trackevent']);
        add_action('wp_ajax_nopriv_firebox_trackevent', [$this, 'firebox_trackevent']);

        // FB Track conversion AJAX
        add_action('wp_ajax_firebox_trackconversion', [$this, 'firebox_trackconversion']);
        add_action('wp_ajax_nopriv_firebox_trackconversion', [$this, 'firebox_trackconversion']);

        // FB Refresh nonce AJAX
        add_action('wp_ajax_firebox_refresh_nonce', [$this, 'firebox_refresh_nonce']);
        add_action('wp_ajax_nopriv_firebox_refresh_nonce', [$this, 'firebox_refresh_nonce']);
    }

    /**
     * Reads the conversions cookie and deletes it.
     *
     * @return  array|false  The conversions the cookie asks us to record, false when there are none.
     */
    protected function consumeConversionsCookie()
    {
        $key = \FireBox\Core\Helpers\BoxHelper::cookiePrefix() . 'conversions_tracker';

        // Get conversions
        $conversions = isset($_COOKIE[$key]) ? json_decode(sanitize_text_field(wp_unslash($_COOKIE[$key])), true) : false;
        if (!$conversions || !is_array($conversions))
        {
            return false;
        }

        // Delete cookie
        unset($_COOKIE[$key]);

        foreach (\FireBox\Core\Helpers\BoxHelper::cookiePathsToClear() as $path)
        {
            setcookie($key, '', time() - 3600, $path, \FireBox\Core\Helpers\BoxHelper::cookieDomain());
        }

        return $conversions;
    }

    /**
     * Writes the conversions queued during this request, after the response has been sent.
     *
     * @return  void
     */
    public function trackPendingConversions()
    {
        if (!$this->pendingConversions)
        {
            return;
        }

        $conversions = $this->pendingConversions;
        $this->pendingConversions = [];

        /**
         * Hand the response back to the visitor before writing rows. On other SAPIs the
         * work still happens after the page has been fully generated.
         */
        if (function_exists('fastcgi_finish_request'))
        {
            fastcgi_finish_request();
        }
        else if (function_exists('litespeed_finish_request'))
        {
            litespeed_finish_request();
        }

        $this->storeConversions($conversions);
    }

    /**
     * Track Conversions
     *
     * @return  void
     */
    public function trackConversions()
    {
        if (!$conversions = $this->consumeConversionsCookie())
        {
            return;
        }

        $this->storeConversions($conversions);
    }

    /**
     * Stores the given conversions in the database.
     *
     * @param   array  $conversions
     *
     * @return  void
     */
    protected function storeConversions($conversions)
    {
        /**
         * The cookie is written by our own JavaScript, which means it is entirely
         * client-controlled: its contents are a request for us to write analytics rows,
         * not a fact. Cap how many a single request can ask for so a crafted cookie
         * cannot insert thousands of rows in one page load.
         */
        $max_conversions = (int) apply_filters('firebox/track/max_cookie_conversions', 20);
        $processed = 0;

        // Track conversions in db
        foreach ($conversions as $campaign_id => $data)
        {
            if ($processed >= $max_conversions)
            {
                break;
            }

            if (!is_array($data))
            {
                continue;
            }

            if (!isset($data['box_log_id']) || !$data['box_log_id'])
            {
                continue;
            }

            if (!isset($data['source']))
            {
                continue;
            }

            if (!isset($data['label']))
            {
                continue;
            }

            /**
             * Only record the conversion when the impression it claims to belong to
             * actually exists for this campaign.
             */
            if (!firebox()->tables->boxlog->belongsToCampaign($data['box_log_id'], $campaign_id))
            {
                continue;
            }

            if (!self::isAllowedEventSource($data['source']))
            {
                continue;
            }

            $data = [
                'log_id' => (int) $data['box_log_id'],
                'event' => 'conversion',
                'event_source' => sanitize_text_field($data['source']),
                'event_label' => self::sanitizeEventLabel($data['label']),
                'date' => $this->factory->getDate()->format('Y-m-d H:i:s')
            ];

            firebox()->tables->boxlogdetails->insert($data);

            $processed++;
        }
    }

    /**
     * Normalises a client-supplied URL before it is stored against an impression.
     *
     * @param   mixed  $url
     *
     * @return  string
     */
    public static function sanitizeTrackedUrl($url)
    {
        if (!is_scalar($url))
        {
            return '';
        }

        $url = esc_url_raw(trim((string) $url), ['http', 'https']);

        if ($url === '')
        {
            return '';
        }

        return function_exists('mb_substr') ? mb_substr($url, 0, 255) : substr($url, 0, 255);
    }

    /**
     * Returns whether the given event source is one we recognise.
     *
     * @param   mixed  $source
     *
     * @return  bool
     */
    public static function isAllowedEventSource($source)
    {
        if (!is_string($source) || $source === '')
        {
            return false;
        }

        /**
         * The client tracker sends 'form', 'button', 'image' or the clicked element's
         * tag name, so a fixed allowlist would drop legitimate conversions. Accept any
         * short slug-shaped token instead; that still rejects markup, URLs and padding.
         */
        return (bool) preg_match('/^[a-z][a-z0-9_-]{0,29}$/', $source);
    }

    /**
     * Trims an event label to something safe to store.
     *
     * @param   mixed  $label
     *
     * @return  string
     */
    public static function sanitizeEventLabel($label)
    {
        if (!is_scalar($label))
        {
            return '';
        }

        $label = sanitize_text_field((string) $label);

        // Keep well inside the column width regardless of what was submitted.
        return function_exists('mb_substr') ? mb_substr($label, 0, 200) : substr($label, 0, 200);
    }
    /**
     * Box Track Event
     *
     * @return  void
     */
    public function firebox_trackevent()
    {
        $nonce = isset($_GET['nonce']) ? sanitize_text_field(wp_unslash($_GET['nonce'])) : '';

        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fbox_js_nonce'))
        {
            self::dieWithInvalidNonce();
        }

		$event = isset($_GET['event']) ? sanitize_text_field(wp_unslash($_GET['event'])) : '';
        $box_id = isset($_GET['box']) ? sanitize_text_field(wp_unslash($_GET['box'])) : '';
        $page = '';
        $referrer = '';
        $box_log_id = '';

        $response['success'] = false;

        // ensure non-empty values
        if (empty($event) || empty($box_id))
        {
            echo wp_json_encode($response);
            wp_die();
        }

        // ensure valid event
        if (!in_array($event, ['open', 'close']))
        {
            echo wp_json_encode($response);
            wp_die();
        }

        // If its a close event, it should also provide the box log ID in order to add close date of box
        if ($event == 'close')
        {
            if (!isset($_GET['box_log_id']))
            {
                return;
            }

            $box_log_id = sanitize_text_field(wp_unslash($_GET['box_log_id']));
        }
        else if ($event == 'open')
        {
            if (!isset($_GET['page']) && !isset($_GET['referrer']))
            {
                return;
            }

            /**
             * These end up in the "Top Pages" and "Top Referrers" reports, so bound them
             * to something URL-shaped and within the column width rather than storing
             * whatever the query string contained.
             */
            $page = isset($_GET['page']) ? self::sanitizeTrackedUrl(wp_unslash($_GET['page'])) : '';
            $referrer = isset($_GET['referrer']) ? self::sanitizeTrackedUrl(wp_unslash($_GET['referrer'])) : '';
        }


        // Load box settings
        if (!$box = firebox()->box->get($box_id))
        {
            return;
        }

        // Don't track events on unpublished campaigns
        $track_unpublished = apply_filters('firebox/track_unpublished', false);
        if (!$track_unpublished && $box->post_status !== 'publish')
        {
            return;
        }

        /**
         * Trigger Open & Close Event.
         */
        do_action('firebox/box/on_' . $event, $box);

        $response['success'] = true;
        if ($event == 'open')
        {
            $response['box_log_id'] = $this->handleOpenEvent($box, $box_id, $page, $referrer);
        }
        
        // Log impression in the database
        if ($event == 'close')
        {
            // get options
            $options = (isset($_GET['options']) && is_array($_GET['options']))
                ? array_map('sanitize_text_field', wp_unslash($_GET['options']))
                : [];
            $this->handleCloseEvent($box, $box_id, $box_log_id, $options, $response);
        }

        echo wp_json_encode($response);
        wp_die();
    }
    
    /**
     * Handle Open Event
     * 
     * @param   object  $box
     * @param   int     $box_id
     * @param   string  $page
     * @param   string  $referrer
     *
     * @return  mixed
     */
    protected function handleOpenEvent($box, $box_id, $page, $referrer)
    {
        // Do not track when box is on test mode
        if (!$box->params->get('testmode'))
        {
            return firebox()->box->logOpenEvent($box_id, $page, $referrer);
        }
    }

    /**
     * Handle Close Event
     * 
     * @param   object  $box
     * @param   int     $box_id
     * @param   int     $box_log_id
     * @param   array   $options
     * @param   array   $response
     * 
     * @return  void
     */
    protected function handleCloseEvent($box, $box_id, $box_log_id, $options, &$response)
    {
        // Do not track when box is on test mode
        if (!$box->params->get('testmode'))
        {
            /**
             * box_log_id comes from the client. Only stamp a close time on it when it
             * really is an impression of this campaign.
             */
            if (firebox()->tables->boxlog->belongsToCampaign($box_log_id, $box_id))
            {
                firebox()->box->logCloseEvent($box_id, $box_log_id);
            }
        }

        // Do not set any cookie if box is on test mode
        if (!$box->params->get('testmode') && !isset($options['temporary']))
        {
            // allow to prevent cookie from being set
            if (!apply_filters('firebox/box/close/cookie_set', true, $box_id))
            {
                return;
            }

            if ($this->cookieExist($box_id))
            {
                $response['action'] = 'stop';
            }
        }
    }

    /**
     * Check whether the cookie exists.
     * 
     * @param   int   $box_id
     * 
     * @return  bool
     */
    protected function cookieExist($box_id)
    {
        $cookie = new \FireBox\Core\FB\Cookie(firebox()->box->get($box_id));
        $cookie->set();
        
        return $cookie->exist();
    }

    /**
     * Box Track Conversion
     *
     * @return  void
     */
    public function firebox_trackconversion()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

        // verify nonce
        if (!$verify = wp_verify_nonce($nonce, 'fbox_js_nonce'))
        {
            self::dieWithInvalidNonce();
        }

        $this->trackConversions();
    }

    /**
     * Hands the client a fresh nonce.
     *
     * Page caches routinely outlive the 12–24h nonce baked into the cached HTML, which
     * would otherwise make tracking fail silently. No nonce is required to call this: it
     * exposes nothing the page HTML does not already expose.
     *
     * This is deliberately not throttled. All it does is hand back a token the page HTML
     * already carries, so there is nothing here worth rationing — while throttling it
     * risks the opposite: on a busy cached site a nonce rollover sends a burst of
     * visitors here at once, and turning any of them away loses their tracking silently.
     *
     * @return  void
     */
    public function firebox_refresh_nonce()
    {
        echo wp_json_encode([
            'success' => true,
            'nonce' => wp_create_nonce('fbox_js_nonce')
        ]);
        wp_die();
    }

    /**
     * Tells the client its nonce is stale, so it can refresh it and replay the request.
     *
     * @return  void
     */
    protected static function dieWithInvalidNonce()
    {
        echo wp_json_encode([
            'success' => false,
            'error' => 'invalid_nonce'
        ]);
        wp_die();
    }
}