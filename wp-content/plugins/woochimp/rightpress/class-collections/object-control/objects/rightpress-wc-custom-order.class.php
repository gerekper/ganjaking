<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * WooCommerce Custom Order
 *
 * @class RightPress_WC_Custom_Order
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Custom_Order extends WC_Order
{

    /**
     * Constructor
     *
     * @access public
     * @param mixed $order
     * @return void
     */
    public function __construct($order = 0)
    {

        // Call parent constructor
        parent::__construct($order);
    }





}
