<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-method.class.php';

/**
 * Condition Method: Coupons
 *
 * @class RightPress_Condition_Method_Coupons
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Method_Coupons extends RightPress_Condition_Method
{

    protected $key = 'coupons';

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
            'at_least_one_any'  => esc_html__('at least one of any', 'rightpress'),
            'at_least_one'      => esc_html__('at least one of selected', 'rightpress'),
            'all'               => esc_html__('all of selected', 'rightpress'),
            'only'              => esc_html__('only selected', 'rightpress'),
            'none'              => esc_html__('none of selected', 'rightpress'),
            'none_at_all'       => esc_html__('none at all', 'rightpress'),
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

        // Normalize value
        $value = array_map('intval', (array) $value);
        sort($value);

        // Normalize condition value
        $condition_value = array_map('intval', (array) $condition_value);
        sort($condition_value);

        // At least one of any
        if ($option_key === 'at_least_one_any' && !empty($value)) {
            return true;
        }
        // At least one of selected
        else if ($option_key === 'at_least_one' && count(array_intersect($value, $condition_value)) > 0) {
            return true;
        }
        // All of selected
        else if ($option_key === 'all' && count(array_intersect($value, $condition_value)) == count($condition_value)) {
            return true;
        }
        // Only selected
        else if ($option_key === 'only' && $value === $condition_value) {
            return true;
        }
        // None of selected
        else if ($option_key === 'none' && count(array_intersect($value, $condition_value)) === 0) {
            return true;
        }
        // None at all
        else if ($option_key === 'none_at_all' && empty($value)) {
            return true;
        }

        return false;
    }





}
