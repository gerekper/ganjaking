<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 *
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    /**
     * Class constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Load text domains
        load_textdomain('rp_wcdpd', WP_LANG_DIR . '/' . RP_WCDPD_PLUGIN_KEY . '/rp_wcdpd-' . apply_filters('plugin_locale', get_locale(), 'rp_wcdpd') . '.mo');
        load_textdomain('rightpress', WP_LANG_DIR . '/' . RP_WCDPD_PLUGIN_KEY . '/rightpress-' . apply_filters('plugin_locale', get_locale(), 'rightpress') . '.mo');
        load_plugin_textdomain('rp_wcdpd', false, RP_WCDPD_PLUGIN_KEY . '/languages/');
        load_plugin_textdomain('rightpress', false, RP_WCDPD_PLUGIN_KEY . '/languages/');

        // Additional Plugins page links
        add_filter('plugin_action_links_' . (RP_WCDPD_PLUGIN_KEY . '/' . RP_WCDPD_PLUGIN_KEY . '.php'), array($this, 'plugins_page_links'));

        // Include RightPress library loaded class
        require_once RP_WCDPD_PLUGIN_PATH . 'rightpress/rightpress-loader.class.php';

        // Execute other code when all plugins are loaded
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), 1);
    }

    /**
     * Code executed when all plugins are loaded
     *
     * @access public
     * @return void
     */
    public function on_plugins_loaded()
    {

        // Load helper classes
        RightPress_Loader::load();

        // Load shared product pricing component
        RightPress_Loader::load_component(array(
            'rightpress-assets-component',
            'rightpress-product-price',
        ));

        // Check environment
        if (!RP_WCDPD::check_environment()) {
            return;
        }

        // Load class collections
        RightPress_Loader::load_class_collection(array(
            'item-control',
            'conditions',
        ));

        // Load method related controllers
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/rp-wcdpd-controller-methods-cart-discount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/rp-wcdpd-controller-methods-checkout-fee.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/rp-wcdpd-controller-methods-product-pricing.class.php';

        // Load methods
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-cart-discount-simple.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-checkout-fee-simple.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-other-exclude.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-other-restrict-purchase.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-quantity-bogo-xx-once.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-quantity-bogo-xx-repeat.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-quantity-bogo-xy-once.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-quantity-bogo-xy-repeat.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-quantity-group-once.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-quantity-group-repeat.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-simple.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-volume-bulk.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/methods/methods/rp-wcdpd-method-product-pricing-volume-tiered.class.php';

        // Load condition related controllers
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/rp-wcdpd-controller-condition-fields.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/rp-wcdpd-controller-condition-methods.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/rp-wcdpd-controller-conditions.class.php';

        // Load conditions
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-count.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-coupons.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-quantities-product-attributes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-quantities-product-categories.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-quantities-product-shipping-classes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-quantities-product-tags.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-quantities-product-variations.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-quantities-products.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-subtotals-product-attributes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-subtotals-product-categories.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-subtotals-product-shipping-classes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-subtotals-product-tags.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-subtotals-product-variations.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-item-subtotals-products.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-items-product-attributes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-items-product-categories.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-items-product-shipping classes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-items-product-tags.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-items-product-variations.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-items-products.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-quantity.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-subtotal.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-cart-weight.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-checkout-payment-method.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-checkout-shipping-method.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-custom-taxonomy-product.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-capability.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-customer.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-logged-in.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-meta.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-role.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-value-amount-spent.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-value-average-order-amount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-value-last-order-amount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-value-last-order-time.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-value-order-count.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-customer-value-review-count.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-other-pricing-rules-applied.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-attributes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-category.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-other-pricing-rules-applied.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-other-wc-coupons-applied.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-product.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-property-meta.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-property-on-sale.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-property-regular-price.class.php';
        // require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-property-sale-price.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-property-shipping-class.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-property-stock-quantity.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-tags.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-product-variation.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-product-attributes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-product-categories.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-product-tags.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-product-variations.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-products.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-quantity-product-attributes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-quantity-product-categories.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-quantity-product-tags.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-quantity-product-variations.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-quantity-products.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-value-product-attributes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-value-product-categories.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-value-product-tags.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-value-product-variations.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-purchase-history-value-products.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-shipping-country.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-shipping-postcode.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-shipping-state.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-shipping-zone.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-time-date.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-time-datetime.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-time-time.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/conditions/rp-wcdpd-condition-time-weekdays.class.php';

        // Load condition methods
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-boolean.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-coupons.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-date.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-datetime.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-list-advanced.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-list.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-meta.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-numeric.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-point-in-time.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-postcode.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-methods/rp-wcdpd-condition-method-time.class.php';

        // Load condition fields
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-decimal-decimal.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-capabilities.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-countries.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-coupons.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-custom-taxonomy.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-payment-methods.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-product-attributes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-product-categories.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-product-tags.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-product-variations.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-products.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-roles.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-shipping-classes.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-shipping-methods.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-shipping-zones.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-states.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-users.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-multiselect-weekdays.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-number-number.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-select-timeframe-event.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-select-timeframe-span.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-text-date.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-text-datetime.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-text-meta-key.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-text-postcode.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-text-text.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/conditions/condition-fields/rp-wcdpd-condition-field-text-time.class.php';

        // Load pricing method related controllers
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/rp-wcdpd-controller-pricing-methods.class.php';

        // Load pricing methods
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount-amount-per-group.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount-amount-per-product.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount-amount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount-per-cart-item-amount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount-per-cart-item-percentage.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount-per-cart-item.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount-per-cart-line-amount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount-per-cart-line.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount-percentage.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-discount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fee-amount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fee-per-cart-item-amount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fee-per-cart-item-percentage.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fee-per-cart-item.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fee-per-cart-line-amount.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fee-per-cart-line.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fee-percentage.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fee.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fixed-price-per-group.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fixed-price-per-product.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fixed-price-per-range.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fixed-price.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method-fixed.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/pricing-methods/pricing-methods/rp-wcdpd-pricing-method.class.php';

        // Load limits
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/limit/rp-wcdpd-limit-cart-discounts.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/limit/rp-wcdpd-limit-checkout-fees.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/limit/rp-wcdpd-limit-product-pricing.class.php';

        // Load extensions
        require_once RP_WCDPD_PLUGIN_PATH . 'extensions/promotion-countdown-timer/rp-wcdpd-promotion-countdown-timer.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'extensions/promotion-rule-notifications/rp-wcdpd-promotion-rule-notifications.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'extensions/promotion-total-saved/rp-wcdpd-promotion-total-saved.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'extensions/promotion-upsell-notifications/rp-wcdpd-promotion-upsell-notifications.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'extensions/promotion-volume-pricing-table/rp-wcdpd-promotion-volume-pricing-table.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'extensions/promotion-your-price/rp-wcdpd-promotion-your-price.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'extensions/promotion-product-banners/rp-wcdpd-promotion-product-banners.class.php';

        // Load integrations
        require_once RP_WCDPD_PLUGIN_PATH . 'integrations/rp-wcdpd-integration-generic-product-feed.class.php';

        // Load other classes
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-ajax.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-assets.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-cart-discounts.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-helper.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-legacy.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-pricing.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-product-price-shop.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-product-pricing.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-rules.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-wc-cart.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-wc-checkout.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-wc-order.class.php';

        // These classes must always be loaded after other classes are loaded
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-data-updater.class.php';
        require_once RP_WCDPD_PLUGIN_PATH . 'classes/rp-wcdpd-settings.class.php';

        // Load includes
        require_once RP_WCDPD_PLUGIN_PATH . 'includes/functions.php';
    }

    /**
     * Checks if developer functions are ready to use, throws exception if not
     *
     * @access public
     * @param string $function
     * @return bool
     */
    public static function ready_or_fail($function)
    {

        // Wait for rightpress_init
        if (!did_action('rightpress_init') || doing_action('rightpress_init')) {
            throw new Exception("Function $function can only be called after WordPress init action position 9.");
        }

        // Wait for cart to be loaded from session
        if (!did_action('woocommerce_cart_loaded_from_session')) {
            throw new Exception("Function $function can only be called after WooCommerce cart is loaded from session.");
        }

        // Ready
        return true;
    }

    /**
     * Check if current user has admin capability
     *
     * @access public
     * @return bool
     */
    public static function is_admin()
    {

        return current_user_can(RP_WCDPD::get_admin_capability());
    }

    /**
     * Get admin capability
     *
     * @access public
     * @return string
     */
    public static function get_admin_capability()
    {

        return apply_filters('rp_wcdpd_capability', RP_WCDPD_ADMIN_CAPABILITY);
    }

    /**
     * Check if environment meets requirements
     *
     * @access public
     * @return bool
     */
    public static function check_environment()
    {

        $is_ok = true;

        // Check PHP version
        if (!version_compare(PHP_VERSION, RP_WCDPD_SUPPORT_PHP, '>=')) {
            add_action('admin_notices', array('RP_WCDPD', 'php_version_notice'));
            return false;
        }

        // Check WordPress version
        if (!RightPress_Help::wp_version_gte(RP_WCDPD_SUPPORT_WP)) {
            add_action('admin_notices', array('RP_WCDPD', 'wp_version_notice'));
            $is_ok = false;
        }

        // Check if WooCommerce is enabled
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array('RP_WCDPD', 'wc_disabled_notice'));
            $is_ok = false;
        }
        else if (!RightPress_Help::wc_version_gte(RP_WCDPD_SUPPORT_WC)) {
            add_action('admin_notices', array('RP_WCDPD', 'wc_version_notice'));
            $is_ok = false;
        }

        return $is_ok;
    }

    /**
     * Display PHP version notice
     *
     * @access public
     * @return void
     */
    public static function php_version_notice()
    {

        echo '<div class="error"><p>' . sprintf(esc_html__('%1$s requires PHP %2$s or later. Please update PHP on your server to use this plugin.', 'rp_wcdpd'), '<strong>WooCommerce Dynamic Pricing & Discounts</strong>', RP_WCDPD_SUPPORT_PHP) . ' ' . sprintf(esc_html__('If you have any questions, please contact %s.', 'rp_wcdpd'), ('<a href="http://url.rightpress.net/new-support-ticket">' . esc_html__('RightPress Support', 'rp_wcdpd') . '</a>')) . '</p></div>';
    }

    /**
     * Display WP version notice
     *
     * @access public
     * @return void
     */
    public static function wp_version_notice()
    {

        echo '<div class="error"><p>' . sprintf(esc_html__('%1$s requires WordPress version %2$s or later. Please update WordPress to use this plugin.', 'rp_wcdpd'), '<strong>WooCommerce Dynamic Pricing & Discounts</strong>', RP_WCDPD_SUPPORT_WP) . ' ' . sprintf(esc_html__('If you have any questions, please contact %s.', 'rp_wcdpd'), ('<a href="http://url.rightpress.net/new-support-ticket">' . esc_html__('RightPress Support', 'rp_wcdpd') . '</a>')) . '</p></div>';
    }

    /**
     * Display WC disabled notice
     *
     * @access public
     * @return void
     */
    public static function wc_disabled_notice()
    {

        echo '<div class="error"><p>' . sprintf(esc_html__('%1$s requires WooCommerce to be active. You can download WooCommerce %2$s.', 'rp_wcdpd'), '<strong>WooCommerce Dynamic Pricing & Discounts</strong>', ('<a href="http://url.rightpress.net/woocommerce-download-page">' . esc_html__('here', 'rp_wcdpd') . '</a>')) . ' ' . sprintf(esc_html__('If you have any questions, please contact %s.', 'rp_wcdpd'), ('<a href="http://url.rightpress.net/new-support-ticket">' . esc_html__('RightPress Support', 'rp_wcdpd') . '</a>')) . '</p></div>';
    }

    /**
     * Display WC version notice
     *
     * @access public
     * @return void
     */
    public static function wc_version_notice()
    {

        echo '<div class="error"><p>' . sprintf(esc_html__('%1$s requires WooCommerce version %2$s or later. Please update WooCommerce to use this plugin.', 'rp_wcdpd'), '<strong>WooCommerce Dynamic Pricing & Discounts</strong>', RP_WCDPD_SUPPORT_WC) . ' ' . sprintf(esc_html__('If you have any questions, please contact %s.', 'rp_wcdpd'), ('<a href="http://url.rightpress.net/new-support-ticket">' . esc_html__('RightPress Support', 'rp_wcdpd') . '</a>')) . '</p></div>';
    }

    /**
     * Add settings link on plugins page
     *
     * @access public
     * @param array $links
     * @return void
     */
    public function plugins_page_links($links)
    {

        // Support
        $settings_link = '<a href="http://url.rightpress.net/7119279-support">' . esc_html__('Support', 'rp_wcdpd') . '</a>';
        array_unshift($links, $settings_link);

        // Settings
        if (RP_WCDPD::check_environment()) {
            $settings_link = '<a href="admin.php?page=rp_wcdpd_settings">'. esc_html__('Settings', 'rp_wcdpd') . '</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }





}

RP_WCDPD::get_instance();
