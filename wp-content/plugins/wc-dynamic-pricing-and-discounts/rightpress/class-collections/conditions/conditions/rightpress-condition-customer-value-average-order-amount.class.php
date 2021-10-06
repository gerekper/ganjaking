<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-customer-value.class.php';

/**
 * Condition: Customer Value - Average Order Amount
 *
 * @class RightPress_Condition_Customer_Value_Average_Order_Amount
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Customer_Value_Average_Order_Amount extends RightPress_Condition_Customer_Value
{

    protected $key          = 'average_order_amount';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('timeframe_span'),
        'after'     => array('decimal'),
    );
    protected $main_field   = 'decimal';
    protected $position     = 20;

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

        return esc_html__('Spent - Average per order', 'rightpress');
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

        $total  = 0.0;
        $number = 0;
        $value  = 0.0;

        // Get order ids
        if ($order_ids = $this->get_order_ids_by_timeframe($params['condition']['timeframe_span'])) {

            // Get amount spent
            foreach ($order_ids as $order_id) {
                if ($order = wc_get_order($order_id)) {
                    $total += (float) RightPress_Conditions::order_get_total($order);
                    $number++;
                }
            }

            // Get average amount
            if ($total && $number) {
                return $total/$number;
            }
        }

        return $value;
    }





}
