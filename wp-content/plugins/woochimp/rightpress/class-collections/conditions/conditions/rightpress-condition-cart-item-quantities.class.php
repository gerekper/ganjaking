<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition.class.php';

/**
 * Condition Group: Cart Item Quantities
 *
 * @class RightPress_Condition_Cart_Item_Quantities
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Cart_Item_Quantities extends RightPress_Condition
{

    protected $group_key        = 'cart_item_quantities';
    protected $group_position   = 130;
    protected $is_cart          = true;

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

        return __('Cart Items - Quantity', 'rightpress');
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

        // Check if items are defined
        if (empty($params['condition'][$this->key])) {
            throw new RightPress_Condition_Exception('rightpress_condition_value_error', 'RightPress Condition: Items are not defined.');
        }

        // Get cart items
        $cart_items = $this->get_cart_items($params);

        // Get value
        return RightPress_Help::get_wc_cart_sum_of_item_quantities($cart_items, array(
            $this->key => $params['condition'][$this->key],
        ));
    }





}
