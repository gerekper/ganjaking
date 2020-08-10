<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field.class.php';

/**
 * Condition Field Group: Number
 *
 * @class RightPress_Condition_Field_Number
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Number extends RightPress_Condition_Field
{

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Display field
     *
     * @access public
     * @param string $context
     * @param string $alias
     * @return void
     */
    public function display($context, $alias = 'condition')
    {
        RightPress_Forms::number($this->get_field_attributes($context, $alias));
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {
        return '0';
    }

    /**
     * Get validation rules
     *
     * @access public
     * @return string
     */
    public function get_validation_rules()
    {
        return 'required,number_min_0,number_whole';
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
        return isset($posted[$this->key]) && !RightPress_Help::is_empty($posted[$this->key]) && RightPress_Help::sanitize_numeric_value($posted[$this->key]) !== false;
    }

    /**
     * Sanitize field value
     *
     * @access public
     * @param array $posted
     * @param object $condition
     * @param string $method_option_key
     * @return mixed
     */
    public function sanitize($posted, $condition, $method_option_key)
    {
        $sanitized = RightPress_Help::sanitize_numeric_value($posted[$this->key]);
        return abs((int) $sanitized);
    }





}
