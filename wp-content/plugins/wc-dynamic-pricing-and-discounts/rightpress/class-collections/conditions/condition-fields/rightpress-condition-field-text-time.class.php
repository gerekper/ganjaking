<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-text.class.php';

/**
 * Condition Field: Text - Time
 *
 * @class RightPress_Condition_Field_Text_Time
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Text_Time extends RightPress_Condition_Field_Text
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
     * Get class
     *
     * @access public
     * @param string $context
     * @param string $alias
     * @return string
     */
    public function get_class($context = null, $alias = 'condition')
    {

        return parent::get_class($context, $alias) . $this->get_plugin_prefix() . 'time ';
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {
        return esc_html__('select time', 'rightpress');
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
        // New time picker
        if (RightPress_Help::is_date($posted[$this->key], 'H:i')) {
            return true;
        }
        // Old datepicker (in case old value is submitted again without opening new time picker)
        else if (in_array($posted[$this->key], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23))) {
            return true;
        }

        return false;
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

            // Already in H:i format
            if (RightPress_Help::is_date($posted[$this->key], 'H:i')) {
                return (string) $posted[$this->key];
            }
            // Convert from old format
            else {
                $value = strlen((string) $posted[$this->key]) === 1 ? ('0' . $posted[$this->key]) : $posted[$this->key];
                return $value . ':00';
            }
        }

        return null;
    }





}
