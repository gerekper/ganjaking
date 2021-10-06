<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-customer.class.php';

/**
 * Condition: Customer - Capability
 *
 * @class RightPress_Condition_Customer_Capability
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Customer_Capability extends RightPress_Condition_Customer
{

    protected $key      = 'capability';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('capabilities'),
    );
    protected $position = 30;

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
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {

        return esc_html__('User capability', 'rightpress');
    }

    /**
     * Get value to compare against condition
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_value($params)
    {

        $value = array();

        if (RightPress_Help::is_request('frontend')) {
            $value = RightPress_Help::current_user_capabilities();
        }

        return $value;
    }





}
