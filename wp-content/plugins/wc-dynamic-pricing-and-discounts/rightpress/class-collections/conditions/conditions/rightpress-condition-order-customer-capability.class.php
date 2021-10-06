<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-order-customer.class.php';

/**
 * Condition: Order Customer - Capability
 *
 * @class RightPress_Condition_Order_Customer_Capability
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Order_Customer_Capability extends RightPress_Condition_Order_Customer
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

        // Get customer id
        $customer_id = $this->get_order($params)->get_customer_id();

        // Get user capabilities
        $capabilities = RightPress_Help::user_capabilities($customer_id);

        // Allow developers to add custom capabilities
        return apply_filters('rightpress_user_capabilities', $capabilities, $customer_id);
    }





}
