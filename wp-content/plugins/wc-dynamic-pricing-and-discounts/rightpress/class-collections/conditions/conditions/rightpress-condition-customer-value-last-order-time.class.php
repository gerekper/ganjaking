<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-customer-value.class.php';

/**
 * Condition: Customer Value - Last Order Time
 *
 * @class RightPress_Condition_Customer_Value_Last_Order_Time
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Customer_Value_Last_Order_Time extends RightPress_Condition_Customer_Value
{

    protected $key      = 'last_order_time';
    protected $method   = 'point_in_time';
    protected $fields   = array(
        'after' => array('timeframe_event'),
    );
    protected $position = 50;

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

        return esc_html__('Last order', 'rightpress');
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
        $customer_id = isset($params['customer_id']) ? $params['customer_id'] : ((RightPress_Help::is_request('frontend') && is_user_logged_in()) ? get_current_user_id() : null);

        // Get billing email
        if ($customer_id) {
            $customer = new WC_Customer($customer_id);
            $billing_email = $customer->get_billing_email();
        }
        else {
            $billing_email = RightPress_Conditions::get_checkout_billing_email();
        }

        // Get last order id
        if ($last_order_id = RightPress_Help::get_wc_last_user_paid_order_id($customer_id, $billing_email)) {

            // Return order date
            return RightPress_Help::date_create_from_format('Y-m-d', get_the_date('Y-m-d', $last_order_id));
        }

        return null;
    }





}
