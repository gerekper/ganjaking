<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promotion: Total Saved
 *
 * @class RP_WCDPD_Promotion_Total_Saved
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Promotion_Total_Saved
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
        // Register settings structure
        add_filter('rp_wcdpd_settings_structure', array($this, 'register_settings_structure'), 110);

        // Set up promotion tool
        add_action('init', array($this, 'set_up_promotion_tool'));
    }

    /**
     * Register settings structure
     *
     * @access public
     * @param array $settings
     * @return array
     */
    public function register_settings_structure($settings)
    {
        $settings['promo']['children']['total_saved'] = array(
            'title' => __('You Saved', 'rp_wcdpd'),
            'info'  => __('Displays a total amount saved by customer on cart and checkout pages.', 'rp_wcdpd'),
            'children' => array(
                'promo_total_saved' => array(
                    'title'     => __('Enable', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '0',
                ),
                'promo_total_saved_label' => array(
                    'title'     => __('Label', 'rp_wcdpd'),
                    'type'      => 'text',
                    'default'   => __('You Saved', 'rp_wcdpd'),
                    'required'  => true,
                ),
                'promo_total_saved_position_cart' => array(
                    'title'     => __('Position in cart', 'rp_wcdpd'),
                    'type'      => 'grouped_select',
                    'default'   => 'woocommerce_cart_totals_after_order_total',
                    'required'  => true,
                    'options'   => array(
                        'positions'   => array(
                            'label'     => __('Positions', 'rp_wcdpd'),
                            'options'  => array(
                                'woocommerce_before_cart_table'                 => __('Cart table - Before', 'rp_wcdpd'),       // <div>
                                'woocommerce_after_cart_table'                  => __('Cart table - After', 'rp_wcdpd'),        // <div>
                                'woocommerce_before_cart_totals'                => __('Cart totals - Before', 'rp_wcdpd'),      // <div>
                                'woocommerce_after_cart_totals'                 => __('Cart totals - After', 'rp_wcdpd'),       // <div>
                                'woocommerce_cart_totals_before_order_total'    => __('Order total - Before', 'rp_wcdpd'),      // <tr>
                                'woocommerce_cart_totals_after_order_total'     => __('Order total - After', 'rp_wcdpd'),       // <tr>
                                'woocommerce_proceed_to_checkout__before'       => __('Checkout button - Before', 'rp_wcdpd'),  // </div>
                                'woocommerce_proceed_to_checkout__after'        => __('Checkout button - After', 'rp_wcdpd'),   // </div>
                            ),
                        ),
                        'disabled'   => array(
                            'label'     => __('Disabled', 'rp_wcdpd'),
                            'options'  => array(
                                '_disabled' => __('Disabled', 'rp_wcdpd'),
                            ),
                        ),
                    ),
                ),
                'promo_total_saved_position_checkout' => array(
                    'title'     => __('Position in checkout', 'rp_wcdpd'),
                    'type'      => 'grouped_select',
                    'default'   => 'woocommerce_review_order_after_order_total',
                    'required'  => true,
                    'options'   => array(
                        'positions'   => array(
                            'label'     => __('Positions', 'rp_wcdpd'),
                            'options'  => array(
                                'woocommerce_before_checkout_form'              => __('Checkout form - Before', 'rp_wcdpd'),      // <div>
                                'woocommerce_after_checkout_form'               => __('Checkout form - After', 'rp_wcdpd'),       // <div>
                                'woocommerce_review_order_before_payment'       => __('Payment details - Before', 'rp_wcdpd'),    // <div>
                                'woocommerce_review_order_after_payment'        => __('Payment details - After', 'rp_wcdpd'),     // <div>
                                'woocommerce_review_order_before_order_total'   => __('Order total - Before', 'rp_wcdpd'),        // <tr>
                                'woocommerce_review_order_after_order_total'    => __('Order total - After', 'rp_wcdpd'),         // <tr>
                            ),
                        ),
                        'disabled'   => array(
                            'label'     => __('Disabled', 'rp_wcdpd'),
                            'options'  => array(
                                '_disabled' => __('Disabled', 'rp_wcdpd'),
                            ),
                        ),
                    ),
                ),
                'promo_total_saved_discount_threshold' => array(
                    'title'         => __('Discount amount threshold', 'rp_wcdpd'),
                    'type'          => 'decimal',
                    'placeholder'   => __('0.01', 'rp_wcdpd'),
                    'required'      => false,
                ),
                'promo_total_saved_include_cart_discounts' => array(
                    'title'     => __('Include cart discounts and coupons', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '1',
                ),
            ),
        );

        // Include tax - display only if settings are enabled on current website
        if (wc_tax_enabled()) {
            $settings['promo']['children']['total_saved']['children']['promo_total_saved_include_tax'] = array(
                'title'     => __('Include tax', 'rp_wcdpd'),
                'type'      => 'checkbox',
                'default'   => '1',
            );
        }

        return $settings;
    }

    /**
     * Set up promotion tool
     *
     * @access public
     * @return void
     */
    public function set_up_promotion_tool()
    {
        // Check this promotion tool is active
        if (!RP_WCDPD_Settings::get('promo_total_saved')) {
            return;
        }

        // Add hooks
        foreach (array('cart', 'checkout') as $page) {

            // Get hook
            $hook = RP_WCDPD_Settings::get('promo_total_saved_position_' . $page);

            // Check if hook is enabled
            if ($hook && $hook !== '_disabled') {

                // Add hook
                add_action($this->clean_hook($hook), array($this, 'maybe_print_total_saved'), $this->get_position($hook));

                // Add extra checkout hooks so that totals element is updated using WC transport
                if ($page === 'checkout') {
                    add_filter('woocommerce_update_order_review_fragments', array($this, 'maybe_change_checkout_order_review_fragments'));
                }
            }
        }
    }

    /**
     * Clean hook
     *
     * Removes __before and __after suffixes
     *
     * @access public
     * @param string $hook
     * @return string
     */
    public function clean_hook($hook)
    {
        return str_replace(array('__before', '__after'), array('', ''), $hook);
    }

    /**
     * Get hook position
     *
     * @access public
     * @param string $hook
     * @return int
     */
    public function get_position($hook)
    {
        switch ($hook) {

            // Position 1
            case 'woocommerce_proceed_to_checkout__before':
                return 1;

            // Position 99
            case 'woocommerce_proceed_to_checkout__after':
                return 99;

            // Position 11
            default:
                return 11;
        }
    }

    /**
     * Maybe print total saved
     *
     * @access public
     * @return void
     */
    public function maybe_print_total_saved()
    {

        // Get current hook
        $hook = current_action();

        // Print position holder for div element
        if (!$this->get_table_column_count($hook)) {
            echo '<div class="rp_wcdpd_promotion_total_saved_div_position" style="display: none;"></div>';
        }

        // Print element
        echo $this->get_total_saved_html($hook);

        // Enqueue scripts and styles
        wp_enqueue_script('rp-wcdpd-promotion-total-saved-scripts', RP_WCDPD_PLUGIN_URL . '/extensions/promotion-total-saved/assets/scripts.js', array('jquery'), RP_WCDPD_VERSION);
        RightPress_Help::enqueue_or_inject_stylesheet('rp-wcdpd-promotion-total-saved-styles', RP_WCDPD_PLUGIN_URL . '/extensions/promotion-total-saved/assets/styles.css', RP_WCDPD_VERSION);
    }

    /**
     * Maybe add our element to order review fragments
     *
     * @access public
     * @param array $fragments
     * @return array
     */
    public function maybe_change_checkout_order_review_fragments($fragments)
    {

        // Check if correct element is set
        if (is_array($fragments) && isset($fragments['.woocommerce-checkout-review-order-table'])) {

            // Get original checkout hook
            $hook = $this->clean_hook(RP_WCDPD_Settings::get('promo_total_saved_position_checkout'));

            // This is only needed when element is displayed as div
            if (!$this->get_table_column_count($hook)) {

                // Append element html
                $fragments['.woocommerce-checkout-review-order-table'] .= $this->get_total_saved_html($hook);
            }
        }

        return $fragments;
    }

    /**
     * Get total saved html
     *
     * @access public
     * @return string
     */
    public function get_total_saved_html($hook)
    {

        // Get total discount amount
        $amount = $this->get_total_discount_amount($hook);

        // Discount must be bigger than threshold
        if (!$amount || ((string) $amount < (string) $this->get_discount_threshold($amount))) {
            return;
        }

        // Allow developers to override
        if (!apply_filters('rp_wcdpd_promotion_total_saved_display', true, $amount, $hook)) {
            return;
        }

        // Get label
        $label = apply_filters('rp_wcdpd_promotion_total_saved_label', RP_WCDPD_Settings::get('promo_total_saved_label'), $amount, $hook);

        // Format amount
        $formatted_amount = apply_filters('rp_wcdpd_promotion_total_saved_formatted_amount', wc_price($amount), $amount, $hook);

        // Get table column count
        $column_count = $this->get_table_column_count($hook);
        $display_as = $column_count ? 'tr' : 'div';

        // Start output buffer
        ob_start();

        // Include template
        RightPress_Help::include_extension_template('promotion-total-saved', $display_as, RP_WCDPD_PLUGIN_PATH, RP_WCDPD_PLUGIN_KEY, array(
            'label'             => $label,
            'formatted_amount'  => $formatted_amount,
            'amount'            => $amount,
            'column_count'      => $column_count,
            'hook'              => $hook,
        ));

        // Return html
        return ob_get_clean();
    }

    /**
     * Get total discount amount
     *
     * @access public
     * @param string $hook
     * @return float
     */
    public function get_total_discount_amount($hook)
    {
        $amount = 0.0;

        // Get cart items
        $cart_items = RightPress_Help::get_wc_cart_items();

        // Cart is empty or not loaded
        if (empty($cart_items)) {
            return $amount;
        }

        // Check if tax should be included
        $include_tax = wc_tax_enabled() && RP_WCDPD_Settings::get('promo_total_saved_include_tax');

        // Iterate over cart items
        foreach ($cart_items as $cart_item_key => $cart_item) {

            // Get line subtotal
            $final_subtotal = (float) $cart_item['line_subtotal'];

            // Get line subtotal tax
            if ($include_tax) {
                $final_subtotal += (float) $cart_item['line_subtotal_tax'];
            }

            // Get cart item price changes
            $price_changes = RightPress_Product_Price_Cart::get_cart_item_price_changes($cart_item_key);

            // Get full product price from our data
            if (!empty($price_changes)) {

                // Get highest prices subtotal from cart item price changes
                $initial_subtotal = RightPress_Product_Price_Changes::get_highest_prices_subtotal_from_cart_item_price_changes($price_changes);
            }
            // Get full product price from product
            else {

                // Load new product object
                $product = wc_get_product($cart_item['data']->get_id());

                // Get full price from product
                $full_price = $product->get_regular_price('edit');
                $full_price = RightPress_Help::get_amount_in_currency($full_price);

                // Calculate subtotal based on initial price
                $initial_subtotal = (float) ($full_price * $cart_item['quantity']);
            }

            // Tax adjustment
            if ($include_tax) {
                $initial_subtotal = wc_get_price_including_tax($cart_item['data'], array('qty' => 1, 'price' => $initial_subtotal));
            }
            else {
                $initial_subtotal = wc_get_price_excluding_tax($cart_item['data'], array('qty' => 1, 'price' => $initial_subtotal));
            }

            // Convert currency
            $initial_subtotal = RightPress_Help::get_amount_in_currency_realmag777($initial_subtotal);


            // Check if cart item was discounted
            if (RightPress_Product_Price::price_is_smaller_than($final_subtotal, $initial_subtotal)) {

                // Add the difference to total discount amount
                $amount += (RightPress_Product_Price::round($initial_subtotal) - RightPress_Product_Price::round($final_subtotal));
            }
        }

        // Add all applied cart discounts and coupons
        if (RP_WCDPD_Settings::get('promo_total_saved_include_cart_discounts')) {

            // Iterate over disocunts and add all discount amounts
            foreach (WC()->cart->get_applied_coupons() as $coupon) {
                $amount += WC()->cart->get_coupon_discount_amount($coupon, !$include_tax);
            }
        }

        // Return total discount amount
        return apply_filters('rp_wcdpd_promotion_total_saved_amount', $amount, $hook, $include_tax);
    }

    /**
     * Check if total saved should be formatted as table row and return column count
     *
     * @access public
     * @param string $hook
     * @return int|bool
     */
    public function get_table_column_count($hook)
    {
        // Define hooks and column count
        $hooks_inside_table = array(
            'woocommerce_cart_totals_before_order_total'    => 2,
            'woocommerce_cart_totals_after_order_total'     => 2,
            'woocommerce_review_order_before_order_total'   => 2,
            'woocommerce_review_order_after_order_total'    => 2,
        );

        // Display as table row - return column count
        if (isset($hooks_inside_table[$hook])) {
            return $hooks_inside_table[$hook];
        }

        // Display as div
        return false;
    }

    /**
     * Get discount threshold
     *
     * @access public
     * @param float $discount_amount
     * @return string
     */
    public function get_discount_threshold($discount_amount)
    {
        return apply_filters('rp_wcdpd_promotion_total_saved_discount_threshold', RP_WCDPD_Settings::get('promo_total_saved_discount_threshold'), $discount_amount);
    }





}

RP_WCDPD_Promotion_Total_Saved::get_instance();
