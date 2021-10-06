<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-method.class.php';

/**
 * Condition Method: Time
 *
 * @class RightPress_Condition_Method_Time
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Method_Time extends RightPress_Condition_Method
{

    protected $key = 'time';

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
            'from'  => esc_html__('from', 'rightpress'),
            'to'    => esc_html__('to', 'rightpress'),
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

        // Make sure value is datetime
        if (is_a($value, 'DateTime')) {

            // Get condition time
            if ($condition_time = $this->get_datetime($option_key, $condition_value)) {

                // From
                if ($option_key === 'from' && $value >= $condition_time) {
                    return true;
                }
                // To
                else if ($option_key === 'to' && $value < $condition_time) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get datetime from condition value
     *
     * @access public
     * @param string $option_key
     * @param mixed $condition_value
     * @return object
     */
    public function get_datetime($option_key, $condition_value)
    {

        // Get condition time - new time picker
        if (RightPress_Help::is_date($condition_value, 'H:i')) {
            try {
                $condition_time = RightPress_Help::get_datetime_object($condition_value, false);
            }
            catch (Exception $e) {
                return false;
            }
        }
        // Get condition time - old time picker
        else {
            $condition_time = RightPress_Help::get_datetime_object();
            $condition_time->setTime($condition_value, 0, 0);
        }

        // Special case to handle - "to 00:00" actually means to tomorrows 00:00
        if ($option_key === 'to' && ($condition_value === '00:00' || $condition_value === '0')) {
            $condition_time->modify('+1 day');
        }

        // Return condition time
        return $condition_time;
    }





}
