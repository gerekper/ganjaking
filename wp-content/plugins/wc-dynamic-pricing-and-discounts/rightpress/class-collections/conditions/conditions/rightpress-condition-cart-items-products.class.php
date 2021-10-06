<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-cart-items.class.php';

/**
 * Condition: Cart Items - Products
 *
 * @class RightPress_Condition_Cart_Items_Products
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Cart_Items_Products extends RightPress_Condition_Cart_Items
{

    protected $key      = 'products';
    protected $method   = 'list_advanced';
    protected $fields   = array(
        'after' => array('products'),
    );
    protected $position = 10;

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

        return esc_html__('Cart items - Products', 'rightpress');
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

        $cart_items = isset($params['cart_items']) ? $params['cart_items'] : null;
        return RightPress_Help::get_wc_cart_product_ids($cart_items);
    }





}
