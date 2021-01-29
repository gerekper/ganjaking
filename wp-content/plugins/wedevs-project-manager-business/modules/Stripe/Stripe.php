<?php
/**
 * Module Name: Invoice stripe payment gateway
 * Description: Get payment with stripe account
 * Module URI: https://wedevs.com/products/plugins/wp-project-manager-pro/invoice-stripe-gateway/
 * Thumbnail URL: /views/assets/images/invoice.png
 * Author: weDevs
 * Version: 1.0
 * Author URI: https://wedevs.com
 */

class PM_Stripe {

    function __construct () {

        add_action('pm-deactivation-invoice', [ $this, 'deactive_stripe_module'], 10);
        add_action('pm-activation-stripe', [ $this, 'active_invoice'], 10);
        add_action( 'admin_enqueue_scripts', [ $this, 'register_stripe_scripts'] );
        add_action( 'wp_enqueue_scripts', [ $this, 'register_stripe_scripts'] );
        add_action( 'admin_enqueue_scripts', [ $this, 'pm_pro_admin_load_stripe_scripts'] );
        add_action( 'pm_pro_invoice_front_end_script', [ $this, 'pm_pro_stripe_front_end_script'] );
        add_filter( 'pm_pro_load_router_files', [$this, 'add_route_files']);
    }

    public static function init () {
        new self();
    }

    public static function notice () {
        echo sprintf( __('<div class="error"><p><strong>Project Invoice</strong> module is not active. Please active <strong><a href="%s">Project Invoice</a>.</strong> </p> </div>'), get_site_url() . '/wp-admin/admin.php?page=pm_projects#/modules' );
    }

    public function active_invoice () {
        if ( !pm_pro_is_module_active( 'Invoice/Invoice.php' ) ) {
            pm_pro_activate_module( 'Invoice/Invoice.php' );
        }
    }

    public function deactive_stripe_module (  ) {
        pm_pro_deactivate_module('Stripe/Stripe.php');
    }

    public function register_stripe_scripts() {
        $view_path = dirname (__FILE__) . '/views/assets/';
        wp_register_script( 'pm-pro-stripe', plugins_url( 'views/assets/js/stripe.js', __FILE__ ), ['pm-const'], filemtime( $view_path . 'js/stripe.js' ), true );
    }

    public function pm_pro_admin_load_stripe_scripts() {
        if (
            isset( $_GET['page'] )
                &&
            $_GET['page'] == 'pm_projects'
        ) {
            $this->pm_pro_stripe_scripts();
        }
    }

    public function pm_pro_stripe_scripts() {
        wp_enqueue_script( 'pm-pro-stripe' );
    }

    public function pm_pro_stripe_front_end_script () {
        $view_path = dirname (__FILE__) . '/views/assets/';
        wp_enqueue_script( 'cpmi_stripe_site', 'https://js.stripe.com/v2/', false, false, true );
        wp_enqueue_script( 'pm-pro-stripe-front-end', plugins_url( 'views/assets/js/stripe-frontend.js', __FILE__ ), ['pm-config'], filemtime( $view_path . '/js/stripe-frontend.js' ), true );

        wp_localize_script( 'pm-pro-stripe-front-end', 'PM_Stripe', [
            'live_secret_publishable_key' => $this->get_publishable_key(),
            'redirect'                    => get_permalink(),
        ]);

        wp_enqueue_style( 'pm-pro-stripe', plugins_url( 'views/assets/css/stripe.css', __FILE__ ), [], filemtime( $view_path . 'css/stripe.css' ) );
    }

        /**
     * Get the public key
     *
     * @return string
     */
    function get_publishable_key() {
        $invoice_settings = pm_get_setting( 'invoice' );

        if ( isset( $invoice_settings['stripe_test_secret']) && $invoice_settings['stripe_test_secret']  == 'true') {
            return empty( $invoice_settings['secret_publishable_key' ] )? false : trim( $invoice_settings['secret_publishable_key' ] );
        } else {
            return empty( $invoice_settings['live_publishable_key'] ) ? false : trim( $invoice_settings['live_publishable_key'] );
        }
    }

    public function add_route_files ( $files ) {
        $router_files = glob( __DIR__ . "/routes/*.php" );

        return array_merge( $files, $router_files );
    }
}

PM_Stripe::init();
