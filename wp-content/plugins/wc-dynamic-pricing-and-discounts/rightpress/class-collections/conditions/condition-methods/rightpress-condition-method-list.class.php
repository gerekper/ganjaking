<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-method.class.php';

/**
 * Condition Method: List
 *
 * @class RightPress_Condition_Method_List
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Method_List extends RightPress_Condition_Method
{

    protected $key = 'list';

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
            'in_list'       => esc_html__('in list', 'rightpress'),
            'not_in_list'   => esc_html__('not in list', 'rightpress'),
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
        $value = array_map('strval', (array) $value);

        // Fix multidimensional condition value array since this method does not need parent/child relationship data
        if (RightPress_Help::array_is_multidimensional($condition_value)) {
            $condition_value = call_user_func_array('array_merge', $condition_value);
        }

        // Normalize condition value
        $condition_value = array_map('strval', (array) $condition_value);

        // Proceed depending on method
        if ($option_key === 'not_in_list') {
            if (count(array_intersect($value, $condition_value)) == 0) {
                return true;
            }
        }
        else {
            if (count(array_intersect($value, $condition_value)) > 0) {
                return true;
            }
        }

        return false;
    }





}
