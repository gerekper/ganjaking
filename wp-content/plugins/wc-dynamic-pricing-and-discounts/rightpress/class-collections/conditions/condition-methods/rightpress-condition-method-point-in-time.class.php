<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-method.class.php';

/**
 * Condition Method: Point In Time
 *
 * @class RightPress_Condition_Method_Point_In_Time
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Method_Point_In_Time extends RightPress_Condition_Method
{

    protected $key = 'point_in_time';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook();
    }

    /**
     * Get method options
     *
     * @access public
     * @return array
     */
    public function get_options()
    {

        return array(
            'later'     => esc_html__('within past', 'rightpress'),
            'earlier'   => esc_html__('earlier than', 'rightpress'),
        );
    }

    /**
     * Check against condition method
     *
     * @access public
     * @param string $option_key
     * @param mixed $value
     * @param mixed $condition_value
     * @return bool
     */
    public function check($option_key, $value, $condition_value)
    {

        // Get condition date
        $condition_date = RightPress_Conditions_Timeframes::get_date_from_timeframe($condition_value);

        // Earlier than or no order at all
        if ($option_key === 'earlier' && ($value === null || $value < $condition_date)) {
            return true;
        }
        // Within past
        else if ($option_key === 'later' && $value !== null && $value >= $condition_date) {
            return true;
        }

        return false;
    }





}
