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
 * Product Pricing Method: Restrict Purchase
 *
 * @class RP_WCDPD_Method_Product_Pricing_Other_Restrict_Purchase
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Method_Product_Pricing_Other_Restrict_Purchase extends RP_WCDPD_Method_Product_Pricing_Other
{

    protected $key      = 'restrict_purchase';
    protected $position = 20;

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

        // Maybe make product not purchasable
        add_filter('woocommerce_is_purchasable', array($this, 'woocommerce_product_is_purchasable'), PHP_INT_MAX, 2);

        // Maybe hide product variation if it is not purchasable
        add_filter('woocommerce_variation_is_visible', array($this, 'woocommerce_product_variation_is_visible'), PHP_INT_MAX, 4);
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {
        return esc_html__('Restrict purchase of matched products', 'rp_wcdpd');
    }

    /**
     * Maybe make WooCommerce product not purchasable
     *
     * @access public
     * @param bool $is_purchasable
     * @param object $product
     * @return bool
     */
    public function woocommerce_product_is_purchasable($is_purchasable, $product)
    {

        // Check if purchase of this product is restricted
        if ($this->purchase_is_restricted($product)) {
            $is_purchasable = false;
        }

        return $is_purchasable;
    }

    /**
     * Maybe hide product variation if it is not purchasable
     *
     * @access public
     * @param bool $is_visible
     * @param int $variation_id
     * @param int $product_id
     * @param object $product
     * @return bool
     */
    public function woocommerce_product_variation_is_visible($is_visible, $variation_id, $product_id, $product)
    {

        // Check if purchase of this product is restricted
        if ($this->purchase_is_restricted($product)) {
            $is_visible = false;
        }

        return $is_visible;
    }

    /**
     * Check if purchase of specific product is restricted
     *
     * @access public
     * @param object $product
     * @return bool
     */
    public function purchase_is_restricted($product)
    {

        // Get product purchase restriction rules
        if ($rules = RP_WCDPD_Rules::get('product_pricing', array('methods' => array('restrict_purchase')))) {

            // Get condition params from product
            $params = RP_WCDPD_Controller_Conditions::get_condition_params_from_product($product);

            // Filter rules by conditions
            if ($rules = RP_WCDPD_Controller_Conditions::filter_objects($rules, $params)) {

                // Remaining rules indicate that there is at least one purchase restriction rule matching specified product
                return true;
            }
        }

        // Purchase is not restricted
        return false;
    }



}

RP_WCDPD_Method_Product_Pricing_Other_Restrict_Purchase::get_instance();
