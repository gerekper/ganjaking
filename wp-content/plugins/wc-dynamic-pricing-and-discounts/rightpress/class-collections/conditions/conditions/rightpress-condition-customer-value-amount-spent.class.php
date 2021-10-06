<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-customer-value.class.php';

/**
 * Condition: Customer Value - Amount Spent
 *
 * @class RightPress_Condition_Customer_Value_Amount_Spent
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Customer_Value_Amount_Spent extends RightPress_Condition_Customer_Value
{

    protected $key          = 'amount_spent';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('timeframe_span'),
        'after'     => array('decimal'),
    );
    protected $main_field   = 'decimal';
    protected $position     = 10;

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

        return esc_html__('Spent - Total', 'rightpress');
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

        $value = 0.0;

        // Get order ids
        if ($order_ids = $this->get_order_ids_by_timeframe($params['condition']['timeframe_span'])) {

            // Get amount spent
            foreach ($order_ids as $order_id) {
                if ($order = wc_get_order($order_id)) {
                    $value += (float) RightPress_Conditions::order_get_total($order);
                }
            }
        }

        return $value;
    }





}
