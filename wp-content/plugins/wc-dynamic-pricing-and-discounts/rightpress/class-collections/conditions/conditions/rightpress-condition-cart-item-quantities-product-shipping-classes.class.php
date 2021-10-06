<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-cart-item-quantities.class.php';

/**
 * Condition: Cart Item Quantities - Product Shipping Classes
 *
 * @class RightPress_Condition_Cart_Item_Quantities_Product_Shipping_Classes
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Cart_Item_Quantities_Product_Shipping_Classes extends RightPress_Condition_Cart_Item_Quantities
{

    protected $key          = 'shipping_classes';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('shipping_classes'),
        'after'     => array('number'),
    );
    protected $main_field   = 'number';
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

        return esc_html__('Cart item quantity - Shipping classes', 'rightpress');
    }





}
