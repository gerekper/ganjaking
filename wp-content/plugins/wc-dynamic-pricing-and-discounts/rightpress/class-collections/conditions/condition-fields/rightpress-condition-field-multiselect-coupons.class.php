<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-multiselect.class.php';

/**
 * Condition Field: Multiselect - Coupons
 *
 * @class RightPress_Condition_Field_Multiselect_Coupons
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Multiselect_Coupons extends RightPress_Condition_Field_Multiselect
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
     * Get disabled value
     *
     * @access public
     * @return string
     */
    public function get_disabled()
    {

        return 'disabled';
    }

    /**
     * Validate field value
     *
     * @access public
     * @param array $posted
     * @param object $condition
     * @param string $method_option_key
     * @return bool
     */
    public function validate($posted, $condition, $method_option_key)
    {

        // At least one item selected
        if (isset($posted[$this->key]) && !RightPress_Help::is_empty($posted[$this->key])) {
            return true;
        }
        // Items not required
        else if (in_array($method_option_key, array('at_least_one_any', 'none_at_all'), true)) {
            return true;
        }

        return false;
    }

    /**
     * Load multiselect options
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public function load_multiselect_options($ids = array(), $query = '')
    {

        return RightPress_Conditions::get_all_coupons($ids, $query);
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {

        return esc_html__('Select coupons', 'rightpress');
    }





}
