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
 * Cart Discount notifications
 *
 * @class RP_WCDPD_Rule_Notifications_Cart_Discounts
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Rule_Notifications_Cart_Discounts extends RP_WCDPD_Rule_Notifications
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    protected $context = 'cart_discounts';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Register settings structure
        add_filter('rp_wcdpd_settings_structure', array($this, 'register_settings_structure'), 172);

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
            'promo_rule_notifications_cart_discounts' => array(
                'title'     => esc_html__('Cart Discounts', 'rp_wcdpd'),
                'type'      => 'checkbox',
                'default'   => '0',
            ),
            'promo_rule_notifications_cart_discounts_message' => array(
                'title'     => esc_html__('Text', 'rp_wcdpd'),
                'type'      => 'textarea',
                'required'  => true,
                'class'     => 'if_rp_wcdpd_promo_rule_notifications_cart_discounts',
                'default'   => sprintf(esc_html__('Discount %s has been applied to your cart.', 'rp_wcdpd'), '<strong>{{title}}</strong>'),
                'hint'      => (esc_html__('Macro {{title}} displays discount title.', 'rp_wcdpd') . '<br>' . esc_html__('Macro {{description}} displays public description', 'rp_wcdpd')),
            ),
        ));

        return $settings;
    }

    /**
     * Get title value
     *
     * @access public
     * @param string $identifier
     * @param array $data
     * @return string
     */
    public function get_title_value($identifier, $data)
    {
        return $data['rule']['title'];
    }

    /**
     * Get description value
     *
     * @access public
     * @param string $identifier
     * @param array $data
     * @return string
     */
    public function get_description_value($identifier, $data)
    {
        // Try to get description for combined rules
        if (!empty($data) && $data['rule']['uid'] === 'rp_wcdpd_combined') {

            // Get controller
            $controller = RP_WCDPD_Controller_Methods_Cart_Discount::get_instance();

            // Get applicable adjustments
            if ($adjustments = $controller->applicable_adjustments) {

                // Get public descriptions
                if ($descriptions = RP_WCDPD_Rules::get_public_descriptions($this->context, array_keys($adjustments))) {

                    // Allow developers to override
                    if ($descriptions = apply_filters('rp_wcdpd_promotion_rule_notifications_descriptions_to_combine_cart_discounts', $descriptions, $adjustments, $identifier, $data)) {

                        // Join descriptions
                        $combined = '<br>' . join('<br>', $descriptions);

                        // Allow developers to override and return
                        return apply_filters('rp_wcdpd_promotion_rule_notifications_combined_description_cart_discounts', $combined, $descriptions, $adjustments, $identifier, $data);
                    }
                }
            }
        }

        // Try parent
        return parent::get_description_value($identifier, $data);
    }





}

RP_WCDPD_Rule_Notifications_Cart_Discounts::get_instance();
