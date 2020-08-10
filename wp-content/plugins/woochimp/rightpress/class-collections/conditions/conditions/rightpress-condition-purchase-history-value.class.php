<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition.class.php';

/**
 * Condition Group: Purchase History Value
 *
 * @class RightPress_Condition_Purchase_History_Value
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Purchase_History_Value extends RightPress_Condition
{

    protected $group_key        = 'purchase_history_value';
    protected $group_position   = 190;
    protected $is_customer      = true;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook_group();
    }

    /**
     * Get group label
     *
     * @access public
     * @return string
     */
    public function get_group_label()
    {

        return __('Purchase History - Value', 'rightpress');
    }

    /**
     * Get value to compare against condition
     *
     * @access public
     * @param array $params
     * @return float
     */
    public function get_value($params)
    {

        $value = 0.0;

        // Get all order ids for this customer
        if ($order_ids = RightPress_Conditions::get_order_ids()) {

            // Iterate over matching order ids
            foreach ($order_ids as $order_id) {

                // Load order
                if ($order = wc_get_order($order_id)) {

                    // Add sum of matching order item values
                    $value += RightPress_Help::get_wc_order_sum_of_item_values($order->get_items(), array(
                        $this->key => $params['condition'][$this->key],
                    ), $this->get_controller()->amounts_include_tax());
                }
            }
        }

        return $value;
    }





}
