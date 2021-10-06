<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-method.class.php';

/**
 * Condition Method: Numeric
 *
 * @class RightPress_Condition_Method_Numeric
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Method_Numeric extends RightPress_Condition_Method
{

    protected $key = 'numeric';

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
            'at_least'      => esc_html__('at least', 'rightpress'),
            'more_than'     => esc_html__('more than', 'rightpress'),
            'not_more_than' => esc_html__('not more than', 'rightpress'),
            'less_than'     => esc_html__('less than', 'rightpress'),
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

        // Check if value is set
        if ($value !== null) {

            // Convert floats to strings (floats have precision problems and can't be compared directly)
            if (is_float($value) || is_float($condition_value)) {
                $value = sprintf('%.10f', (float) $value);
                $condition_value = sprintf('%.10f', (float) $condition_value);
            }

            // Compare
            if ($option_key === 'less_than' && $value < $condition_value) {
                return true;
            }
            else if ($option_key === 'not_more_than' && $value <= $condition_value) {
                return true;
            }
            else if ($option_key === 'at_least' && $value >= $condition_value) {
                return true;
            }
            else if ($option_key === 'more_than' && $value > $condition_value) {
                return true;
            }
        }

        return false;
    }





}
