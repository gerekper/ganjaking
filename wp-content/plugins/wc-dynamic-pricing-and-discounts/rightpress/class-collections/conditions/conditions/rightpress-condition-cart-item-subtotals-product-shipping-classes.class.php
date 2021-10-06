<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-cart-item-subtotals.class.php';

/**
 * Condition: Cart Item Subtotals - Product Shipping Classes
 *
 * @class RightPress_Condition_Cart_Item_Subtotals_Product_Shipping_Classes
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Cart_Item_Subtotals_Product_Shipping_Classes extends RightPress_Condition_Cart_Item_Subtotals
{

    protected $key          = 'shipping_classes';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('shipping_classes'),
        'after'     => array('decimal'),
    );
    protected $main_field   = 'decimal';
    protected $position     = 60;

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

        return esc_html__('Cart item subtotal - Shipping classes', 'rightpress');
    }





}
