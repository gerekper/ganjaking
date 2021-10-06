<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-cart-item-subtotals.class.php';

/**
 * Condition: Cart Item Subtotals - Products
 *
 * @class RightPress_Condition_Cart_Item_Subtotals_Products
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Cart_Item_Subtotals_Products extends RightPress_Condition_Cart_Item_Subtotals
{

    protected $key          = 'products';
    protected $method       = 'numeric';
    protected $fields       = array(
        'before'    => array('products'),
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

        return esc_html__('Cart item subtotal - Products', 'rightpress');
    }





}
