<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-cart.class.php';

/**
 * Condition: Cart - Quantity
 *
 * @class RightPress_Condition_Cart_Quantity
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Cart_Quantity extends RightPress_Condition_Cart
{

    protected $key      = 'quantity';
    protected $method   = 'numeric';
    protected $fields   = array(
        'after' => array('decimal'),
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

        return __('Cart total quantity', 'rightpress');
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

        $cart_items = $this->get_cart_items($params);
        return RightPress_Help::get_wc_cart_sum_of_item_quantities($cart_items);
    }





}
