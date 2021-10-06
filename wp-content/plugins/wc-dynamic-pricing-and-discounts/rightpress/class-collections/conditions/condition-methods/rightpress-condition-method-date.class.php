<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-method.class.php';

/**
 * Condition Method: Date
 *
 * @class RightPress_Condition_Method_Date
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Method_Date extends RightPress_Condition_Method
{

    protected $key = 'date';

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
            'from'          => esc_html__('from', 'rightpress'),
            'to'            => esc_html__('to', 'rightpress'),
            'specific_date' => esc_html__('specific date', 'rightpress'),
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

            // Get condition date
            if ($condition_date = $this->get_datetime($option_key, $condition_value)) {

                // From
                if ($option_key === 'from' && $value >= $condition_date) {
                    return true;
                }
                // To
                else if ($option_key === 'to' && $value <= $condition_date) {
                    return true;
                }
                // Specific date
                else if ($option_key === 'specific_date' && $value == $condition_date) {
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

        // Get condition date
        try {
            $condition_date = RightPress_Help::get_datetime_object($condition_value, false);
            $condition_date->setTime(0, 0, 0);
            return $condition_date;
        }
        catch (Exception $e) {
            return false;
        }
    }





}
