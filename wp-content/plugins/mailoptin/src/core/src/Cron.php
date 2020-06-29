<?php

namespace MailOptin\Core;

use Carbon\Carbon;

class Cron
{
    public function __construct()
    {
        add_action('init', [$this, 'create_recurring_schedule']);

        add_action('mo_daily_recurring_job', [$this, 'cleanup_old_leadbank_data']);
    }

    public function create_recurring_schedule()
    {
        if ( ! wp_next_scheduled('mo_hourly_recurring_job')) {
            // we are adding 10 mins to give room for timestamp/hourly checking to be correct.
            $tz = Carbon::now(0)->endOfHour()->addMinute(10)->timestamp;

            wp_schedule_event($tz, 'hourly', 'mo_hourly_recurring_job');
        }

        if ( ! wp_next_scheduled('mo_daily_recurring_job')) {
            wp_schedule_event(time(), 'daily', 'mo_daily_recurring_job');
        }
    }

    public function cleanup_old_leadbank_data()
    {
        if (defined('MAILOPTIN_DETACH_LIBSODIUM')) return;

        global $wpdb;

        $table = $wpdb->prefix . Core::conversions_table_name;

        return $wpdb->query(
            "DELETE FROM $table WHERE DATEDIFF(NOW(), date_added) >= 90"
        );
    }

    /**
     * Singleton.
     *
     * @return Cron
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}