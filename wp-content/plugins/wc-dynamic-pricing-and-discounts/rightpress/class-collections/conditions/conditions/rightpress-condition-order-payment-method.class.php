<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-order.class.php';

/**
 * Condition: Order - Payment Method
 *
 * @class RightPress_Condition_Order_Payment_Method
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Order_Payment_Method extends RightPress_Condition_Order
{

    protected $key      = 'payment_method';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('payment_methods'),
    );
    protected $position = 90;

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

        return esc_html__('Payment method', 'rightpress');
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

        // Get payment method
        return $this->get_order($params)->get_payment_method();
    }





}
