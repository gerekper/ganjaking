<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Time Class
 *
 * @class RightPress_Time
 * @package RightPress
 * @author RightPress
 */
class RightPress_Time
{

    // Time periods
    protected static $time_periods = null;


    /**
     * =================================================================================================================
     * DYNAMIC TIME PERIOD MANAGEMENT
     * =================================================================================================================
     */

    /**
     * Define and return time periods
     *
     * @access public
     * @return array
     */
    public static function get_time_periods()
    {

        // Check if time periods are set
        if (!isset(RightPress_Time::$time_periods)) {

            $time_periods = array();

            // Define minutes and hours if RightPress testing mode is enabled
            if (defined('RIGHTPRESS_TESTING_MODE_ENABLED')) {

                // Minute
                $time_periods['minute'] = array(
                    'key'           => 'minute', // Must be supported by the parser used for strtotime()
                    'label'         => _n_noop('minute', 'minutes', 'rightpress'),
                    'label_count'   => _n_noop('%d minute', '%d minutes', 'rightpress'),
                );

                // Hour
                $time_periods['hour'] = array(
                    'key'           => 'hour', // Must be supported by the parser used for strtotime()
                    'label'         => _n_noop('hour', 'hours', 'rightpress'),
                    'label_count'   => _n_noop('%d hour', '%d hours', 'rightpress'),
                );
            }

            // Day
            $time_periods['day'] = array(
                'key'           => 'day', // Must be supported by the parser used for strtotime()
                'label'         => _n_noop('day', 'days', 'rightpress'),
                'label_count'   => _n_noop('%d day', '%d days', 'rightpress'),
            );

            // Week
            $time_periods['week'] = array(
                'key'           => 'week', // Must be supported by the parser used for strtotime()
                'label'         => _n_noop('week', 'weeks', 'rightpress'),
                'label_count'   => _n_noop('%d week', '%d weeks', 'rightpress'),
            );

            // Month
            $time_periods['month'] = array(
                'key'           => 'month', // Must be supported by the parser used for strtotime()
                'label'         => _n_noop('month', 'months', 'rightpress'),
                'label_count'   => _n_noop('%d month', '%d months', 'rightpress'),
            );

            // Year
            $time_periods['year'] = array(
                'key'           => 'year', // Must be supported by the parser used for strtotime()
                'label'         => _n_noop('year', 'years', 'rightpress'),
                'label_count'   => _n_noop('%d year', '%d years', 'rightpress'),
            );

            // Allow developers to add custom time periods
            RightPress_Time::$time_periods = apply_filters('rightpress_time_periods', $time_periods);
        }

        // Return time periods
        return RightPress_Time::$time_periods;
    }

    /**
     * Get time periods for display
     *
     * @access public
     * @param int $time_length
     * @return array
     */
    public static function get_time_periods_for_display($time_length = 2)
    {

        $time_periods_with_names = array();

        foreach (RightPress_Time::get_time_periods() as $time_period => $time_period_data) {
            $time_periods_with_names[$time_period] = translate_nooped_plural($time_period_data['label'], $time_length, 'rightpress');
        }

        return $time_periods_with_names;
    }

    /**
     * Get time period name
     *
     * @access public
     * @param int $time_length
     * @param string $time_period
     * @return string
     */
    public static function get_time_period_name($time_length, $time_period)
    {

        // Get all time periods
        $time_periods = RightPress_Time::get_time_periods();

        // Time period does not exists
        if (!isset($time_periods[$time_period])) {
            throw new RightPress_Exception('rightpress_time_invalid_time_period', esc_html__('Invalid time period.', 'rightpress'));
        }

        // Translate and return
        return translate_nooped_plural($time_periods[$time_period]['label'], $time_length, 'rightpress');
    }

    /**
     * Get formatted time period string
     *
     * @access public
     * @param int $time_length
     * @param string $time_period
     * @param bool $always_display_time_length
     * @return string
     */
    public static function get_formatted_time_period_string($time_length, $time_period, $always_display_time_length = true)
    {

        // Get all time periods
        $time_periods = RightPress_Time::get_time_periods();

        // Time period does not exist
        if (!isset($time_periods[$time_period])) {
            throw new RightPress_Exception('rightpress_time_invalid_time_period', esc_html__('Invalid time period.', 'rightpress'));
        }

        // Check if time length needs to be displayed
        $display_time_length = ($time_length != 1 || $always_display_time_length);

        // Get correct label to use
        $label = $display_time_length ? $time_periods[$time_period]['label_count'] : $time_periods[$time_period]['label'];

        // Translate label
        $label = translate_nooped_plural($label, $time_length, 'rightpress');

        // Maybe add time length and return
        return ($display_time_length ? sprintf($label, $time_length) : $label);
    }

    /**
     * Validate time period
     *
     * @access public
     * @param string $time_period
     * @return bool
     */
    public static function validate_time_period($time_period)
    {

        // Get all time periods
        $time_periods = RightPress_Time::get_time_periods();

        // Check if time period is defined
        return isset($time_periods[$time_period]);
    }


    /**
     * =================================================================================================================
     * PERIOD LENGTH STRING CONVERSION
     * =================================================================================================================
     */

    /**
     * Get length from period length
     *
     * Note: This method expects a valid period length or null, it does not do its own validation
     *
     * @access public
     * @param string|null $period_length
     * @return string|null
     */
    public static function get_length_from_period_length($period_length)
    {

        return $period_length === null ? null : (int) explode(' ', $period_length)[0];
    }

    /**
     * Get period from period length
     *
     * Note: This method expects a valid period length or null, it does not do its own validation
     *
     * @access public
     * @param string|null $period_length
     * @return string|null
     */
    public static function get_period_from_period_length($period_length)
    {

        return $period_length === null ? null : explode(' ', $period_length)[1];
    }


    /**
     * =================================================================================================================
     * TIME PERIOD CALCULATION
     * =================================================================================================================
     */

    /**
     * Add period length to datetime
     *
     * @access public
     * @param object $datetime
     * @param string $period_length
     * @return void
     */
    public static function add_period_length_to_datetime(&$datetime, $period_length)
    {

        RightPress_Time::modify_datetime_with_period_length($datetime, $period_length, '+');
    }

    /**
     * Subtract period length from datetime
     *
     * @access public
     * @param object $datetime
     * @param string $period_length
     * @return void
     */
    public static function subtract_period_length_from_datetime(&$datetime, $period_length)
    {

        RightPress_Time::modify_datetime_with_period_length($datetime, $period_length, '-');
    }

    /**
     * Modify datetime with period length
     *
     * @access public
     * @param object $datetime
     * @param string $period_length
     * @param string $operator
     * @return void
     */
    public static function modify_datetime_with_period_length(&$datetime, $period_length, $operator)
    {

        // Get time periods
        $time_periods = RightPress_Time::get_time_periods();

        // Invalid datetime object
        if (!is_a($datetime, 'DateTime')) {
            throw new RightPress_Exception('rightpress_modify_datetime_with_period_length_invalid_datetime', esc_html__('Invalid datetime.', 'rightpress'));
        }

        // Get length and period
        $length = RightPress_Time::get_length_from_period_length($period_length);
        $period = RightPress_Time::get_period_from_period_length($period_length);

        // Invalid length
        if (!$length || !is_numeric($length) || !ctype_digit((string) $length)) {
            throw new RightPress_Exception('rightpress_modify_datetime_with_period_length_invalid_datetime', esc_html__('Invalid time period length.', 'rightpress'));
        }

        // Invalid period
        if (!isset($time_periods[$period])) {
            throw new RightPress_Exception('rightpress_modify_datetime_with_period_length_invalid_datetime', esc_html__('Invalid time period.', 'rightpress'));
        }

        // Modify datetime with period length
        $datetime->modify("{$operator}{$length} {$time_periods[$period]['key']}");
    }

    /**
     * Check if first period length is longer than second period length
     *
     * @access public
     * @param string $first_period_length
     * @param string $second_period_length
     * @return int
     */
    public static function period_length_is_longer_than($first_period_length, $second_period_length)
    {

        // Get current timestamp
        $timestamp = time();

        // Get datetime with first period length added
        $first_period_length_dt = new RightPress_DateTime("@$timestamp");
        RightPress_Time::add_period_length_to_datetime($first_period_length_dt , $first_period_length);

        // Get datetime with second period length added
        $second_period_length_dt = new RightPress_DateTime("@$timestamp");
        RightPress_Time::add_period_length_to_datetime($second_period_length_dt , $second_period_length);

        // Check if first period length is longer than second period length
        return $first_period_length_dt > $second_period_length_dt;
    }

    /**
     * Convert period length to seconds, minutes, hours, days or weeks
     *
     * @access public
     * @param string $period_length
     * @param string $target
     * @return int
     */
    public static function convert_period_length_to($period_length, $target)
    {

        // Define target lengths in seconds
        $target_lengths_in_seconds = array('seconds' => 1, 'minutes' => 60, 'hours' => 3600, 'days' => 86400, 'weeks' => 604800);

        // Target length is not defined
        if (!isset($target_lengths_in_seconds[$target])) {
            throw new RightPress_Exception('rightpress_target_length_undefined', esc_html__('Target length undefined.', 'rightpress'));
        }

        // Get current datetime
        $current_datetime = new RightPress_DateTime();

        // Get period length datetime
        $period_length_datetime = clone $current_datetime;
        RightPress_Time::add_period_length_to_datetime($period_length_datetime, $period_length);

        // Get difference between datetimes in seconds
        $difference_in_seconds = $period_length_datetime->getTimestamp() - $current_datetime->getTimestamp();

        // Calculate how many target lengths fit into difference in seconds (rounded mathematically)
        $result = (int) round($difference_in_seconds / $target_lengths_in_seconds[$target]);

        return $result;
    }


    /**
     * =================================================================================================================
     * TIME HELPER METHODS
     * =================================================================================================================
     */

    /**
     * Ensure datetime is in the future by a given offset
     *
     * @access public
     * @param object $datetime
     * @param string $offset
     * @return void
     */
    public static function ensure_future_datetime(&$datetime, $offset = '+1 second')
    {

        // Create future datetime
        $future_datetime = new RightPress_DateTime($offset);

        // Datetime is earlier than future datetime
        if ($datetime < $future_datetime) {

            // Set datetime to future datetime
            $datetime = $future_datetime;
        }
    }

    /**
     * Check if datetime is in the past
     *
     * @access public
     * @param object $datetime
     * @return void
     */
    public static function is_past($datetime)
    {

        // Get current datetime
        $current_datetime = new RightPress_DateTime();

        // Compare datetimes
        return $datetime < $current_datetime;
    }

    /**
     * Check if datetime is in the future
     *
     * @access public
     * @param object $datetime
     * @return void
     */
    public static function is_future($datetime)
    {

        // Get current datetime
        $current_datetime = new RightPress_DateTime();

        // Compare datetimes
        return $datetime > $current_datetime;
    }

    /**
     * Get "day" name
     *
     * Allows substituting 'day' with some other time period to override some settings during testing
     * Returned value must be supported by the parser used for strtotime()
     *
     * @access public
     * @return string
     */
    public static function get_day_name()
    {

        $day_name = 'day';

        // RightPress testing mode is enabled
        if (defined('RIGHTPRESS_TESTING_MODE_ENABLED')) {

            // Allow day substituting
            $day_name = apply_filters('rightpress_testing_mode_day_override', $day_name);
        }

        return $day_name;
    }

    /**
     * Get "day" length in seconds
     *
     * @access public
     * @return int
     */
    public static function get_day_length_in_seconds()
    {

        $day_length_in_seconds = 86400;

        // RightPress testing mode is enabled
        if (defined('RIGHTPRESS_TESTING_MODE_ENABLED')) {

            // Get called class
            $called_class = get_called_class();

            // Get "day" name
            $day_name = $called_class::get_day_name();

            // Get "day" length in seconds
            $day_length_in_seconds = $called_class::convert_period_length_to("1 $day_name", 'seconds');
        }

        return $day_length_in_seconds;
    }





}
