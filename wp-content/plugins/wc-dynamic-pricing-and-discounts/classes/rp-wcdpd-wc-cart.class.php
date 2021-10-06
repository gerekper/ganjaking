<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Methods related to WooCommerce Cart
 *
 * @class RP_WCDPD_WC_Cart
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_WC_Cart
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Enable cart item price display override
        add_filter('rightpress_product_price_cart_item_display_price_enabled', '__return_true');

        // Maybe automatically add BXGYF product to cart
        add_action('woocommerce_add_to_cart', array($this, 'maybe_add_free_product_to_cart'), 10, 6);
    }

    /**
     * Remove all regular coupons
     *
     * @access public
     * @return void
     */
    public static function remove_all_regular_coupons()
    {

        // Iterate over applied coupons
        foreach (WC()->cart->get_applied_coupons() as $applied_coupon) {

            // Check if current coupon is regular coupon
            if (!RP_WCDPD_Controller_Methods_Cart_Discount::coupon_is_cart_discount($applied_coupon)) {

                // Remove coupon
                WC()->cart->remove_coupon($applied_coupon);

                // Add notice
                wc_add_notice(sprintf(esc_html__('Sorry, coupon "%s" is not valid when other discounts are applied to the cart.', 'rp_wcdpd'), $applied_coupon), 'error');
            }
        }
    }

    /**
     * Maybe automatically add BXGYF product to cart
     *
     * @access public
     * @param string $cart_item_key
     * @param int $product_id
     * @param int $quantity
     * @param int $variation_id
     * @param array $variation
     * @param array $cart_item_data
     * @return void
     */
    public function maybe_add_free_product_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {

        // Functionality disabled
        if (!RP_WCDPD_Settings::get('product_pricing_bxgyf_auto_add')) {
            return;
        }

        // Get BXGY rules
        foreach (RP_WCDPD_Rules::get('product_pricing', array('methods' => array('bogo', 'bogo_repeat'))) as $rule_key => $rule) {

            // Check if current rule is applicable to product that was just added to cart
            $matched = RP_WCDPD_Controller_Conditions::object_conditions_are_matched($rule, array(
                'item_id'               => ($product_id ? $product_id : null),
                'child_id'              => ($variation_id ? $variation_id : null),
                'variation_attributes'  => ($variation ? $variation : null),
            ));

            // Not applicable
            if (!$matched) {
                continue;
            }

            // Rule must provide an explicit 100% discount
            if (!($rule['bogo_pricing_method'] === 'discount__percentage' && $rule['bogo_pricing_value'] == 100) && !($rule['bogo_pricing_method'] === 'fixed__price' && $rule['bogo_pricing_value'] == 0)) {
                continue;
            }

            // Rule must have exactly one "get" product condition defined
            if (empty($rule['bogo_product_conditions']) || !is_array($rule['bogo_product_conditions']) || count($rule['bogo_product_conditions']) > 1) {
                continue;
            }

            // Reference "get" condition
            $condition = array_pop($rule['bogo_product_conditions']);

            // Condition must be either product or product variation
            if (!RP_WCDPD_Controller_Conditions::is_type($condition, array('product__product', 'product__variation'))) {
                continue;
            }

            $is_variation = RP_WCDPD_Controller_Conditions::is_type($condition, 'product__variation');

            // Product condition must have exactly one product selected
            if (!$is_variation && (empty($condition['products']) || !is_array($condition['products']) || count($condition['products']) > 1)) {
                continue;
            }

            // Product variation condition must have exactly one product variation selected
            if ($is_variation && (empty($condition['product_variations']) || !is_array($condition['product_variations']) || count($condition['product_variations']) > 1)) {
                continue;
            }

            // Get id
            $id = $is_variation ? array_pop($condition['product_variations']) : array_pop($condition['products']);

            // Load product
            $product = wc_get_product($id);

            // Unable to load product
            if (!$product) {
                continue;
            }

            // Product type mismatch
            if ((!$is_variation && !$product->is_type('simple')) || ($is_variation && !$product->is_type('variation'))) {
                continue;
            }

            // Product price is empty (not available for purchase)
            if ($product->get_price('edit') === '') {
                continue;
            }

            // Get variation attributes
            $variation_attributes = $is_variation ? $product->get_variation_attributes() : array();

            // Variation contains undefined attributes
            if ($is_variation) {

                $found = false;

                foreach ($variation_attributes as $attribute) {
                    if ($attribute == '') {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    continue;
                }
            }

            // Make sure product is not in cart yet
            if ($cart_items = RightPress_Help::get_wc_cart_items()) {

                $found = false;

                foreach ($cart_items as $cart_item) {
                    if (($is_variation && $cart_item['variation_id'] == $id) || (!$is_variation && $cart_item['product_id'] == $id)) {
                        $found = true;
                        break;
                    }
                }

                if ($found) {
                    continue;
                }
            }

            // Purchase quantity
            $purchase_quantity = $rule['bogo_purchase_quantity'];

            // Quantity added to cart is lower than purchase quantity
            if ($quantity < $purchase_quantity) {
                continue;
            }

            // Get multiplier
            $multiplier = (int) ($quantity / $purchase_quantity);

            // Receive quantity
            $receive_quantity = $rule['bogo_receive_quantity'] * ($rule['method'] === 'bogo_repeat' ? $multiplier : 1);

            // Run product price test to make sure this product would be free if it was added to cart
            $price_data = RightPress_Product_Price_Test::run($product, 1, $variation_attributes, true);

            // Price is not zero
            if (!isset($price_data['price']) || !is_numeric($price_data['price']) || !RightPress_Product_Price::price_is_zero($price_data['price'])) {
                continue;
            }

            // No pricing rules adjust current cart item
            if (empty($price_data['all_changes']['rp_wcdpd'])) {
                continue;
            }

            // Get list of rule uids
            $rule_uids = RP_WCDPD_Rules::get_rule_uids_from_adjustments($price_data['all_changes']['rp_wcdpd']);

            // Current rule did not adjust product price
            if (!in_array($rule['uid'], $rule_uids, true)) {
                continue;
            }

            // Allow other plugins to skip this action
            if (apply_filters('rp_wcdpd_add_free_product_to_cart', true, $product, $rule, $cart_item_key)) {

                $product_id = RightPress_Help::get_wc_product_absolute_id($product);
                $variation_id = $is_variation ? $id : 0;

                // Add free product to cart
                remove_action('woocommerce_add_to_cart', array($this, 'maybe_add_free_product_to_cart'));
                WC()->cart->add_to_cart($product_id, $receive_quantity, $variation_id, $variation_attributes);
                add_action('woocommerce_add_to_cart', array($this, 'maybe_add_free_product_to_cart'), 10, 6);
            }

            // Do not check other rules
            break;
        }
    }





}

RP_WCDPD_WC_Cart::get_instance();
