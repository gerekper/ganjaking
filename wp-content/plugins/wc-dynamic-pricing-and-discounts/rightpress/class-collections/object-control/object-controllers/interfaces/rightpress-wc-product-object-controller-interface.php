<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * WooCommerce Product Controller Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_WC_Product_Object_Controller_Interface
{

    /**
     * Check if subscriptions are enabled for product without loading object
     *
     * @access public
     * @param object|int $product
     * @return string
     */
    public function is_enabled_for_product($product);





}
