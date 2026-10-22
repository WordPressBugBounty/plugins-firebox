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

use FPFramework\Libs\Registry;
use FireBox\Core\Helpers\BoxHelper;

class Log
{
    /**
     * Factory
     *
     * @var  Factory
     */
    private $factory;

    /**
     * The hook name used to run the log retention cleanup via WP-Cron.
     *
     * @var  string
     */
    const CLEANUP_HOOK = 'firebox_logs_cleanup';

    /**
     * Max number of batches (of 1000 rows) deleted per cron run, to avoid long lock holds.
     *
     * @var  int
     */
    const CLEANUP_MAX_BATCHES = 20;

    public function __construct($factory = null)
    {
        if (!$factory)
        {
            $factory = new \FPFramework\Base\Factory();
        }

        $this->factory = $factory;

        add_action(self::CLEANUP_HOOK, [$this, 'cleanupCron']);

        self::maybeScheduleCleanup();
    }

    /**
     * Schedules the daily log retention cleanup, if not already scheduled.
     *
     * Static so the installer can schedule the job without constructing a Log:
     * activation runs after `init`, and the framework's autoloader is
     * registered on that hook, so the Factory this class takes is not
     * available there.
     *
     * @return  void
     */
    public static function maybeScheduleCleanup()
    {
        if (wp_next_scheduled(self::CLEANUP_HOOK))
        {
            return;
        }

        wp_schedule_event(time(), 'daily', self::CLEANUP_HOOK);
    }

    /**
     * Unschedules the log retention cleanup.
     *
     * @return  void
     */
    public static function unscheduleCleanup()
    {
        $timestamp = wp_next_scheduled(self::CLEANUP_HOOK);

        if ($timestamp)
        {
            wp_unschedule_event($timestamp, self::CLEANUP_HOOK);
        }
    }
    
    /**
     *  Logs box events to the database
     *
     *  @param   integer  $boxid       The box id
     *  @param   string   $page        The page the box appeared
     *  @param   string   $referrer    The referrer
     *  @param   integer  $event_id     Event id: 1=Open, 2=Close
     *  @param   integer  $box_log_id  The box log ID of the open event that we are closing
     *
     *  @return  bool     Returns a boolean indicating if the event logged successfully
     */
    public function track($boxid, $event_id = 1, $box_log_id = null, $page = '', $referrer = '')
    {
    	// Making sure we have a valid Boxid
        if (!$boxid)
        {
            return;
        }

        // open event
        if ($event_id == 1)
        {
            // Get visitor's token id
            if (!$visitorID = $this->factory->getVisitorID())
            {
                return;
            }

            
            $country_value = '';
            

            
    
            // Everything seems OK. Let's save data to db.
            $data = (object) [
                'sessionid'  => $this->factory->getSession()->getSessionID(),
                'visitorid'  => $visitorID,
                'user'       => $this->factory->getUser()->ID,
                'box'        => $boxid,
                'page'       => $page,
                'country'    => $country_value,
                'device'     => $this->factory->getDevice(),
                'referrer'   => $referrer,
                'date'       => $this->factory->getDate()->format('Y-m-d H:i:s')
            ];

            return $this->handleOpen($data);
        }

        // other events
        return $this->handleOtherEvents($box_log_id, $event_id);
    }

    /**
     * Handle the logging when opening a box
     * 
     * @param   array  $passed_data
     * 
     * @return  mixed
     */
    private function handleOpen($passed_data)
    {
        // Insert the object into the firebox logs table
        try
        {
            $log_id = firebox()->tables->boxlog->insert($passed_data);

            return $log_id;
        }
        catch (\Exception $e)
        {}
    }

    /**
     * Handle the logging of other events
     * 
     * @param   int     $box_log_id
     * @param   int     $event_id
     * @param   string  $event_source
     * @param   string  $event_label
     * 
     * @return  mixed
     */
    private function handleOtherEvents($box_log_id, $event_id, $event_source = '', $event_label = '')
    {
        if (!$box_log_id)
        {
            return;
        }
        
        // Insert the object into the firebox logs details table
        try
        {
            $data = [
                'log_id' => $box_log_id,
                'event' => $this->parseEvent($event_id),
                'event_source' => $event_source,
                'event_label' => $event_label,
                'date' => $this->factory->getDate()->format('Y-m-d H:i:s')
            ];
            
            firebox()->tables->boxlogdetails->insert($data);

            return;
        }
        catch (\Exception $e)
        {
        }
    }

    /**
     * Translates the event ID to name
     * 
     * @param   int     $id
     * 
     * @return  string
     */
    private function parseEvent($id)
    {
        $name = '';

        switch ($id)
        {
            case 1:
                $name = 'open';
                break;
            case 2:
                $name = 'close';
                break;
            case 3:
                $name = 'conversion';
                break;
        }

        return $name;
    }

    /**
     *  Removes old rows from the logs table in small batches, to avoid holding
     *  long locks on large tables. Runs daily via WP-Cron (see self::CLEANUP_HOOK).
     *
     *  @return void
     */
    public function cleanupCron()
    {
        // Removes rows older than x days
        $stats = \FireBox\Core\Helpers\BoxHelper::getParams();
        $stats = new Registry($stats);

        $days = $stats->get('statsdays', 730);

        global $wpdb;

        $logTable = firebox()->tables->boxlog->getFullTableName();
        $logDetailsTable = firebox()->tables->boxlogdetails->getFullTableName();

        for ($i = 0; $i < self::CLEANUP_MAX_BATCHES; $i++)
        {
            $ids = $wpdb->get_col($wpdb->prepare(
                "SELECT id FROM `$logTable` WHERE date < DATE_SUB(NOW(), INTERVAL %d DAY) ORDER BY id LIMIT 1000",
                $days
            ));

            if (!$ids)
            {
                break;
            }

            $ids_placeholder = implode(',', array_map('intval', $ids));

            $wpdb->query("DELETE FROM `$logDetailsTable` WHERE log_id IN ($ids_placeholder)");
            $wpdb->query("DELETE FROM `$logTable` WHERE id IN ($ids_placeholder)");

            if (count($ids) < 1000)
            {
                break;
            }
        }

        return true;
    }
}