<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field.class.php';

/**
 * Condition Field Group: Text
 *
 * @class RightPress_Condition_Field_Text
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Text extends RightPress_Condition_Field
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
        RightPress_Forms::text($this->get_field_attributes($context, $alias));
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {
        return esc_html__('value', 'rightpress');
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
        if (isset($posted[$this->key])) {
            return (string) $posted[$this->key];
        }

        return null;
    }





}
