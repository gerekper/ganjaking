<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress DateTime Class
 *
 * @class RightPress_DateTime
 * @package RightPress
 * @author RightPress
 */
class RightPress_DateTime extends DateTime
{

    /**
     * Constructor
     *
     * @access public
     * @param mixed $time
     * @param object $timezone
     * @return void
     */
    public function __construct($time = 'now', $timezone = null)
    {

        // Call parent constructor
        parent::__construct($time, $timezone);

        // Get local timezone if custom timezone was not provided
        $timezone = ($timezone === null ? RightPress_Help::get_time_zone() : $timezone);

        // Set correct timezone
        $this->setTimezone($timezone);
    }

    /**
     * Convert DateTime object to ISO 8601 string
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->format(DATE_ATOM);
    }


    /**
     * =================================================================================================================
     * FORMATTING HELPERS
     * =================================================================================================================
     */

    /**
     * Format date
     *
     * @access public
     * @return string
     */
    public function format_date()
    {

        // Get date format
        $date_format = $this->get_date_format();

        // Format and return date string
        return $this->format($date_format);
    }

    /**
     * Format time
     *
     * @access public
     * @return string
     */
    public function format_time()
    {

        // Get time format
        $time_format = $this->get_time_format();

        // Format and return time string
        return $this->format($time_format);
    }

    /**
     * Format datetime
     *
     * @access public
     * @return string
     */
    public function format_datetime()
    {

        // Get datetime format
        $datetime_format = $this->get_datetime_format();

        // Format and return datetime string
        return $this->format($datetime_format);
    }

    /**
     * Format human readable time difference
     *
     * @access public
     * @param int $limit        Limit in seconds from which regular date will be displayed
     * @param bool $show_time   Whether to show time in addition to date when displaying regular date
     * @return string
     */
    public function format_human_time_diff($limit = null, $show_time = false)
    {

        // Get datetime timestamp
        $datetime_timestamp = $this->getTimestamp();

        // Get current timestamp
        $current_timestamp = time();

        // Check if datetime timestamp is in the future
        $is_future = $datetime_timestamp > $current_timestamp;

        // Get difference in time
        $time_diff = abs($datetime_timestamp - $current_timestamp);

        // Format human time diff
        if ($limit === null || $time_diff < $limit) {

            // Less than a minute
            if ($time_diff < 60) {

                // In the past
                if ($datetime_timestamp < $current_timestamp) {

                    return sprintf(_n('%d second ago', '%d seconds ago', $time_diff, 'rightpress'), $time_diff);
                }
                // In the future
                else if ($datetime_timestamp > $current_timestamp) {

                    return sprintf(_n('In %d second', 'In %d seconds', $time_diff, 'rightpress'), $time_diff);
                }
                // Now
                else {

                    return esc_html__('Now!', 'rightpress');
                }
            }
            // More than a minute
            else {

                // Format prefix and suffix
                $prefix = $datetime_timestamp > $current_timestamp ? (esc_html__('In', 'rightpress') . ' ')     : '';
                $suffix = $datetime_timestamp < $current_timestamp ? (' ' . esc_html__('ago', 'rightpress'))    : '';

                // Format and return string
                return $prefix . human_time_diff($datetime_timestamp, $current_timestamp) . $suffix;
            }
        }
        // Format regular date (with optional time)
        else {

            return $show_time ? $this->format_datetime() : $this->format_date();
        }
    }

    /**
     * Get date format
     *
     * @access public
     * @return string
     */
    public function get_date_format()
    {

        // Get date format
        $date_format = function_exists('wc_date_format') ? wc_date_format() : get_option('date_format');

        // Allow developers to override and return
        return apply_filters('rightpress_datetime_date_format', $date_format);
    }

    /**
     * Get time format
     *
     * @access public
     * @return string
     */
    public function get_time_format()
    {

        // Get time format
        $time_format = function_exists('wc_time_format') ? wc_time_format() : get_option('time_format');

        // Allow developers to override and return
        return apply_filters('rightpress_datetime_time_format', $time_format);
    }

    /**
     * Get datetime format
     *
     * @access public
     * @return string
     */
    public function get_datetime_format()
    {

        // Get date format
        $date_format = $this->get_date_format();

        // Get time format
        $time_format = $this->get_time_format();

        // Format datetime format
        $datetime_format = $date_format . ' ' . $time_format;

        // Allow developers to override and return
        return apply_filters('rightpress_datetime_datetime_format', $datetime_format, $date_format, $time_format);
    }





}
