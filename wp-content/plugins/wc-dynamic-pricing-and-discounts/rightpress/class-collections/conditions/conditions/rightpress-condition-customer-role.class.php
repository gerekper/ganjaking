<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-customer.class.php';

/**
 * Condition: Customer - Role
 *
 * @class RightPress_Condition_Customer_Role
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Customer_Role extends RightPress_Condition_Customer
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

        $value = array();

        if (RightPress_Help::is_request('frontend')) {
            $value = RightPress_Help::current_user_roles();
        }

        return $value;
    }





}
