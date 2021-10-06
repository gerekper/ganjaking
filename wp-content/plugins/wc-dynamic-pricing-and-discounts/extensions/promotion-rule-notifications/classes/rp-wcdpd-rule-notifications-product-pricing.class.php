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
                'title'     => esc_html__('Product Pricing', 'rp_wcdpd'),
                'type'      => 'checkbox',
                'default'   => '0',
            ),
            'promo_rule_notifications_product_pricing_message' => array(
                'title'     => esc_html__('Text', 'rp_wcdpd'),
                'type'      => 'textarea',
                'required'  => true,
                'class'     => 'if_rp_wcdpd_promo_rule_notifications_product_pricing',
                'default'   => esc_html__('Product discount has been applied to your cart.', 'rp_wcdpd'),
                'hint'      => esc_html__('Macro {{description}} displays public description.', 'rp_wcdpd'),
            ),
        ));

        return $settings;
    }





}

RP_WCDPD_Rule_Notifications_Product_Pricing::get_instance();
