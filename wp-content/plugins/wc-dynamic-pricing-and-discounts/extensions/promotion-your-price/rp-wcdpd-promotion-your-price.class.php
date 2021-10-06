<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promotion: Your Price
 *
 * @class RP_WCDPD_Promotion_Your_Price
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Promotion_Your_Price
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    // RightPress Product Price component hook position
    private $rightpress_hook_position = 50;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Register settings structure
        add_filter('rp_wcdpd_settings_structure', array($this, 'register_settings_structure'), 100);

        // Set up promotion tool
        add_action('init', array($this, 'set_up_promotion_tool'));

        // Maybe change element position
        add_filter('rightpress_product_price_live_update_position_hook', array($this, 'maybe_change_element_position'));

        // Maybe toggle subtotal display
        add_filter('rightpress_product_price_live_update_display_subtotal', array($this, 'maybe_toggle_subtotal_display'), 0);

        // Maybe always display quantity
        add_filter('rightpress_product_price_live_update_always_display_quantity', array($this, 'maybe_always_display_quantity'), 0);
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

        $settings['promo']['children']['your_price'] = array(
            'title' => esc_html__('Your Price', 'rp_wcdpd'),
            'info'  => esc_html__('Displays a dynamically updated price on a single product page. This price reflects all pricing adjustments that would be applicable if a specified quantity was added to cart.', 'rp_wcdpd'),
            'children' => array(
                'promo_your_price' => array(
                    'title'     => esc_html__('Enable', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '0',
                ),
                'promo_your_price_always_display' => array(
                    'title'     => esc_html__('Display when product is not discounted', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '1',
                ),
                'promo_your_price_display_quantity' => array(
                    'title'     => esc_html__('Display quantity next to price', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '0',
                ),
                'promo_your_price_display_subtotal' => array(
                    'title'     => esc_html__('Display subtotal when displaying quantity', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '1',
                ),
                'promo_your_price_position' => array(
                    'title'     => esc_html__('Position on page', 'rp_wcdpd'),
                    'type'      => 'grouped_select',
                    'default'   => 'woocommerce_before_add_to_cart_button',
                    'required'  => true,
                    'options'   => array(
                        'replace'    => array(
                            'label'     => esc_html__('Display separately', 'rp_wcdpd'),
                            'options'   => array(
                                'woocommerce_before_add_to_cart_button' => esc_html__('Add to cart button - Before', 'rp_wcdpd'),
                                'woocommerce_after_add_to_cart_button'  => esc_html__('Add to cart button - After', 'rp_wcdpd'),
                                'woocommerce_before_add_to_cart_form'   => esc_html__('Add to cart form - Before', 'rp_wcdpd'),
                                'woocommerce_after_add_to_cart_form'    => esc_html__('Add to cart form - After', 'rp_wcdpd'),
                                'woocommerce_product_meta_start'        => esc_html__('Product meta - Before', 'rp_wcdpd'),
                                'woocommerce_product_meta_end'          => esc_html__('Product meta - After', 'rp_wcdpd'),
                            ),
                        ),
                        'separate'    => array(
                            'label'     => esc_html__('Replace main price', 'rp_wcdpd'),
                            'options'   => array(
                                'rightpress_replace_wc_price' => esc_html__('Replace main price', 'rp_wcdpd'),
                            ),
                        ),
                    ),
                ),
                'promo_your_price_label' => array(
                    'title'     => esc_html__('Label', 'rp_wcdpd'),
                    'type'      => 'text',
                    'default'   => esc_html__('Your Price:', 'rp_wcdpd'),
                    'required'  => false,
                ),
            ),
        );

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

        // Check if functionality is enabled
        if (!RP_WCDPD_Settings::get('promo_your_price')) {
            return;
        }

        // Enable product price live update
        add_filter('rightpress_product_price_live_update_enabled', '__return_true');

        // Maybe display element when product is not discounted
        if (RP_WCDPD_Settings::check('promo_your_price_always_display') || RP_WCDPD_Settings::get('promo_your_price_position') === 'rightpress_replace_wc_price') {
            add_filter('rightpress_product_price_live_update_always_display', '__return_true');
        }

        // Maybe change label
        add_filter('rightpress_product_price_live_update_label', array($this, 'maybe_change_label'), $this->rightpress_hook_position, 3);
    }

    /**
     * Maybe change label
     *
     * @access public
     * @param string $label
     * @param object $product
     * @param array $price_data
     * @return string
     */
    public function maybe_change_label($label, $product, $price_data = null)
    {

        if ($promo_your_price_label = RP_WCDPD_Settings::get('promo_your_price_label')) {
            $label = $promo_your_price_label;
        }

        return $label;
    }

    /**
     * Maybe change element position
     *
     * @access public
     * @param string $position
     * @return string
     */
    public function maybe_change_element_position($position)
    {

        // Get position from settings
        $position = RP_WCDPD_Settings::get('promo_your_price_position');

        // Allow developers to override
        return apply_filters('rp_wcdpd_promotion_your_price_position', $position);
    }

    /**
     * Maybe toggle subtotal display
     *
     * @access public
     * @param bool $display_subtotal
     * @return bool
     */
    public function maybe_toggle_subtotal_display($display_subtotal)
    {

        return RP_WCDPD_Settings::check('promo_your_price_display_subtotal');
    }

    /**
     * Maybe always display quantity
     *
     * @access public
     * @param bool $always_display_quantity
     * @return bool
     */
    public function maybe_always_display_quantity($always_display_quantity)
    {

        if (!$always_display_quantity) {
            $always_display_quantity = RP_WCDPD_Settings::check('promo_your_price_display_quantity');
        }

        return $always_display_quantity;
    }





}

RP_WCDPD_Promotion_Your_Price::get_instance();
