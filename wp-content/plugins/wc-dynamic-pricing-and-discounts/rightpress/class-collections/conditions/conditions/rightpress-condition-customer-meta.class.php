<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-customer.class.php';

/**
 * Condition: Customer - Meta
 *
 * @class RightPress_Condition_Customer_Meta
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Customer_Meta extends RightPress_Condition_Customer
{

    protected $key          = 'meta';
    protected $method       = 'meta';
    protected $fields       = array(
        'before'    => array('meta_key'),
        'after'     => array('text'),
    );
    protected $main_field   = 'text';
    protected $position     = 40;

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

        return esc_html__('User meta', 'rightpress');
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

        // Customer must be logged in
        if (RightPress_Help::is_request('frontend') && is_user_logged_in()) {

            // Load customer
            if ($customer = new WC_Customer(get_current_user_id())) {

                $meta_key = $params['condition']['meta_key'];

                // Handle meta as customer property
                if (RightPress_WC::is_internal_meta($customer, $meta_key, true)) {

                    // Paying customer
                    if ($meta_key === 'paying_customer') {
                        return array($customer->get_is_paying_customer() ? '1' : '0');
                    }
                    // Order count
                    else if ($meta_key === '_order_count') {
                        return array($customer->get_order_count());
                    }
                    // Money spent
                    else if ($meta_key === '_money_spent') {
                        return array($customer->get_total_spent());
                    }
                    // Regular getter
                    else {

                        // Format getter method name
                        $getter = 'get_' . $meta_key;

                        // Check if getter method exists
                        if (method_exists($customer, $getter)) {

                            // Return property value
                            return array($customer->$getter());
                        }
                    }
                }
                // Regular meta handling
                else {

                    // Get meta from database
                    $user_meta = RightPress_WC::customer_get_meta($customer, $meta_key, false, 'edit');
                    return RightPress_WC::normalize_meta_data($user_meta);
                }
            }
        }

        return array();
    }





}
