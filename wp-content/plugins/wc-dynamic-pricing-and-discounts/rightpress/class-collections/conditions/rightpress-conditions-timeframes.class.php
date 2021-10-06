<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Timeframes management class
 *
 * @class RightPress_Conditions_Timeframes
 * @package RightPress
 * @author RightPress
 */
final class RightPress_Conditions_Timeframes
{

    protected $timeframes = null;

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Get timeframes
     *
     * @access public
     * @return array
     */
    public static function get_timeframes()
    {

        // Get instance
        $instance = self::get_instance();

        // Check if timeframes are defined
        if ($instance->timeframes === null) {

            // Define timeframes
            $instance->timeframes = array(

                // Current
                'current' => array(
                    'label'     => esc_html__('Current', 'rightpress'),
                    'children'  => array(
                        'current_day'   => array(
                            'label' => esc_html__('current day', 'rightpress'),
                            'value' => 'midnight',
                        ),
                        'current_week'   => array(
                            'label' => esc_html__('current week', 'rightpress'),
                            'value' => RightPress_Conditions_Timeframes::get_current_week_value(),
                        ),
                        'current_month'   => array(
                            'label' => esc_html__('current month', 'rightpress'),
                            'value' => 'midnight first day of this month',
                        ),
                        'current_year'   => array(
                            'label' => esc_html__('current year', 'rightpress'),
                            'value' => 'midnight first day of january',
                        ),
                    ),
                ),

                // Days
                'days' => array(
                    'label'     => esc_html__('Days', 'rightpress'),
                    'children'  => array(),
                ),

                // Weeks
                'weeks' => array(
                    'label'     => esc_html__('Weeks', 'rightpress'),
                    'children'  => array(),
                ),

                // Months
                'months' => array(
                    'label'     => esc_html__('Months', 'rightpress'),
                    'children'  => array(),
                ),

                // Years
                'years' => array(
                    'label'     => esc_html__('Years', 'rightpress'),
                    'children'  => array(),
                ),
            );

            // Generate list of days
            for ($i = 1; $i <= 6; $i++) {
                $instance->timeframes['days']['children'][$i . '_day'] = array(
                    'label' => $i . ' ' . _n('day', 'days', $i, 'rightpress'),
                    'value' => '-' . $i . ($i === 1 ? ' day' : ' days'),
                );
            }

            // Generate list of weeks
            for ($i = 1; $i <= 4; $i++) {
                $instance->timeframes['weeks']['children'][$i . '_week'] = array(
                    'label' => $i . ' ' . _n('week', 'weeks', $i, 'rightpress'),
                    'value' => '-' . $i . ($i === 1 ? ' week' : ' weeks'),
                );
            }

            // Generate list of months
            for ($i = 1; $i <= 12; $i++) {
                $instance->timeframes['months']['children'][$i . '_month'] = array(
                    'label' => $i . ' ' . _n('month', 'months', $i, 'rightpress'),
                    'value' => '-' . $i . ($i === 1 ? ' month' : ' months'),
                );
            }

            // Generate list of years
            for ($i = 2; $i <= 10; $i++) {
                $instance->timeframes['years']['children'][$i . '_year'] = array(
                    'label' => $i . ' ' . _n('year', 'years', $i, 'rightpress'),
                    'value' => '-' . $i . ($i === 1 ? ' year' : ' years'),
                );
            }

            // Allow developers to override
            $instance->timeframes = apply_filters('rightpress_conditions_timeframes', $instance->timeframes);
        }

        return $instance->timeframes;
    }

    /**
     * Get start of current week value
     *
     * @access public
     * @return string
     */
    public static function get_current_week_value()
    {

        // Today is first day of week
        if ((int) RightPress_Help::get_adjusted_datetime(null, 'w') === RightPress_Help::get_start_of_week()) {
            return 'midnight';
        }
        else {
            return 'midnight last ' . RightPress_Help::get_literal_start_of_week();
        }
    }

    /**
     * Get date from timeframe
     *
     * @access public
     * @param string $option_key
     * @return object
     */
    public static function get_date_from_timeframe($option_key)
    {

        // Get timeframes
        $timeframes = RightPress_Conditions_Timeframes::get_timeframes();

        // Iterate over timeframes
        foreach ($timeframes as $timeframe_group_key => $timeframe_group) {

            // Check if current timeframe was requested
            if (isset($timeframe_group['children'][$option_key])) {

                // Get datetime object
                return RightPress_Help::get_datetime_object($timeframe_group['children'][$option_key]['value'], false);
            }
        }

        return null;
    }





}

RightPress_Conditions_Timeframes::get_instance();
