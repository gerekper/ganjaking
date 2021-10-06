<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Method_Product_Pricing_Other')) {
    require_once('rp-wcdpd-method-product-pricing-other.class.php');
}

/**
 * Product Pricing Method: Exclude
 *
 * @class RP_WCDPD_Method_Product_Pricing_Other_Exclude
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Method_Product_Pricing_Other_Exclude extends RP_WCDPD_Method_Product_Pricing_Other
{

    protected $key      = 'exclude';
    protected $position = 10;

    // Singleton instance
    protected static $instance = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Construct parent
        parent::__construct();

        // Hook method
        $this->hook();

        // Exclude cart items based on rules
        add_filter('rp_wcdpd_product_pricing_cart_items', array($this, 'exclude_cart_items'));
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Exclude products from all rules', 'rp_wcdpd');
    }

    /**
     * Exclude cart items based on rules
     *
     * @access public
     * @param array $cart_items
     * @return array
     */
    public function exclude_cart_items($cart_items)
    {
        // Get all product pricing rules
        $rules = RP_WCDPD_Rules::get('product_pricing', array(
            'methods'       => array('exclude'),
            'cart_items'    => $cart_items,
        ));

        // Exclude items that have at least one matching exclude rule
        foreach ($cart_items as $cart_item_key => $cart_item) {
            if (RP_WCDPD_Controller_Conditions::exclude_item_by_rules($rules, array('cart_item' => $cart_item, 'cart_items' => $cart_items))) {
                unset($cart_items[$cart_item_key]);
            }
        }

        // Return remaining cart items
        return $cart_items;
    }



}

RP_WCDPD_Method_Product_Pricing_Other_Exclude::get_instance();
