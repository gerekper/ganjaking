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
 * Checkout Fee notifications
 *
 * @class RP_WCDPD_Rule_Notifications_Checkout_Fees
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
class RP_WCDPD_Rule_Notifications_Checkout_Fees extends RP_WCDPD_Rule_Notifications
{

    // Singleton control
    protected static $instance = false; public static function get_instance() { return self::$instance ? self::$instance : (self::$instance = new self()); }

    protected $context = 'checkout_fees';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        // Register settings structure
        add_filter('rp_wcdpd_settings_structure', array($this, 'register_settings_structure'), 173);

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
            'promo_rule_notifications_checkout_fees' => array(
                'title'     => esc_html__('Checkout Fees', 'rp_wcdpd'),
                'type'      => 'checkbox',
                'default'   => '0',
            ),
            'promo_rule_notifications_checkout_fees_message' => array(
                'title'     => esc_html__('Text', 'rp_wcdpd'),
                'type'      => 'textarea',
                'required'  => true,
                'class'     => 'if_rp_wcdpd_promo_rule_notifications_checkout_fees',
                'default'   => sprintf(esc_html__('Fee %s has been applied to your cart.', 'rp_wcdpd'), '<strong>{{title}}</strong>'),
                'hint'      => (esc_html__('Macro {{title}} displays fee title.', 'rp_wcdpd') . '<br>' . esc_html__('Macro {{description}} displays public description', 'rp_wcdpd')),
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
        return isset($data['fee_label']) ? $data['fee_label'] : $data['rule']['title'];
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
            $controller = RP_WCDPD_Controller_Methods_Checkout_Fee::get_instance();

            // Get applicable adjustments
            if ($adjustments = $controller->applicable_adjustments) {

                // Get public descriptions
                if ($descriptions = RP_WCDPD_Rules::get_public_descriptions($this->context, array_keys($adjustments))) {

                    // Allow developers to override
                    if ($descriptions = apply_filters('rp_wcdpd_promotion_rule_notifications_descriptions_to_combine_checkout_fees', $descriptions, $adjustments, $identifier, $data)) {

                        // Join descriptions
                        $combined = '<br>' . join('<br>', $descriptions);

                        // Allow developers to override and return
                        return apply_filters('rp_wcdpd_promotion_rule_notifications_combined_description_checkout_fees', $combined, $descriptions, $adjustments, $identifier, $data);
                    }
                }
            }
        }

        // Try parent
        return parent::get_description_value($identifier, $data);
    }





}

RP_WCDPD_Rule_Notifications_Checkout_Fees::get_instance();
