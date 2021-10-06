<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-text.class.php';

/**
 * Condition Field: Text - Datetime
 *
 * @class RightPress_Condition_Field_Text_Datetime
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Text_Datetime extends RightPress_Condition_Field_Text
{

    protected $key = 'datetime';

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

        return parent::get_class($context, $alias) . $this->get_plugin_prefix() . 'datetime ';
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {
        return esc_html__('select date and time', 'rightpress');
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
        return isset($posted[$this->key]) && RightPress_Help::is_date($posted[$this->key], 'Y-m-d H:i');
    }





}
