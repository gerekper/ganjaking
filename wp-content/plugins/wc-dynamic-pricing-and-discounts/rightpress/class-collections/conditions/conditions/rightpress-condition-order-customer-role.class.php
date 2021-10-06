<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-order-customer.class.php';

/**
 * Condition: Order Customer - Role
 *
 * @class RightPress_Condition_Order_Customer_Role
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Order_Customer_Role extends RightPress_Condition_Order_Customer
{

    protected $key      = 'role';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('roles'),
    );
    protected $position = 20;

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

        return esc_html__('User role', 'rightpress');
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

        // Get user roles
        return RightPress_Help::user_roles($customer_id);
    }





}
