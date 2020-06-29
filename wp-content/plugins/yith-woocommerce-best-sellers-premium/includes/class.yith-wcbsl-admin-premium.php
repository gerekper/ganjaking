<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Best Sellers Premium
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBSL' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBSL_Admin_Premium' ) ) {
    /**
     * Admin class.
     * The class manage all the Admin behaviors.
     *
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     * @since    1.0.0
     */
    class YITH_WCBSL_Admin_Premium extends YITH_WCBSL_Admin {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCBSL_Admin_Premium
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        protected function __construct() {
            parent::__construct();

            add_filter( 'yith_wcbsl_settings_admin_tabs', array( $this, 'add_premium_tabs' ) );
            add_filter( 'yith_wcbsl_panel_settings_options', array( $this, 'add_options_in_settings_tab' ) );

            // Add shortcode button in TinyMCE
            add_action( 'init', array( $this, 'add_shortcode_btn_mce' ) );

            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
        }

        /*
         * modify Tabs in Best Sellers Settings Panel
         *
         * @access public
         * @since  1.0.0
         */
        public function add_premium_tabs( $tabs ) {
            unset( $tabs[ 'premium' ] );

            return $tabs;
        }

        /*
         * add premium options in Settings Tab
         *
         * @access public
         * @since  1.0.0
         */
        public function add_options_in_settings_tab( $settings ) {
            $bestseller_limit = isset( $_REQUEST[ 'yith-wcbsl-bestsellers-limit' ] ) ? $_REQUEST[ 'yith-wcbsl-bestsellers-limit' ] : YITH_WCBSL()->get_limit();

            $premium_settings = array(
                'display-options'                       => array(
                    'title' => __( 'Display Options', 'yith-woocommerce-best-sellers' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith-wcbsl-display-options'
                ),
                'bestsellers-limit'                     => array(
                    'id'      => 'yith-wcbsl-bestsellers-limit',
                    'name'    => __( 'Bestsellers shown', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'number',
                    'desc'    => __( 'Select the number of products to show as best sellers.', 'yith-woocommerce-best-sellers' ),
                    'default' => 100
                ),
                'bestsellers-badge-only-for-top'        => array(
                    'id'      => 'yith-wcbsl-bestsellers-badge-only-for-top',
                    'name'    => sprintf( __( '"Best seller" badge only for Top %s', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'type'    => 'checkbox',
                    'desc'    => sprintf( __( 'Select this to display the "Best Seller" badge only for products in general Top %s.', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'default' => 'no'
                ),
                'show-position-in-bestsellers'          => array(
                    'id'      => 'yith-wcbsl-show-position-in-bestsellers',
                    'name'    => __( 'Show Position in Product Page', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'checkbox',
                    'desc'    => __( 'Select to show bestseller ranking position in single product page.', 'yith-woocommerce-best-sellers' ),
                    'default' => 'yes'
                ),
                'show-link-bestseller-category-in-prod' => array(
                    'id'      => 'yith-wcbsl-show-link-bestseller-category-in-prod',
                    'name'    => sprintf( __( 'Show Category Top %s link in Product Page', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'type'    => 'checkbox',
                    'desc'    => sprintf( __( 'Select to show the link to category Top %s in single product pages.', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'default' => 'no'
                ),
                'show-link-bestseller-category-in-cat'  => array(
                    'id'      => 'yith-wcbsl-show-link-bestseller-category-in-cat',
                    'name'    => sprintf( __( 'Show Category Top %s link in Category Pages', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'type'    => 'checkbox',
                    'desc'    => sprintf( __( 'Select to show the link to category Top %s in category pages.', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'default' => 'no'
                ),
                'show-bestseller-indicator'             => array(
                    'id'      => 'yith-wcbsl-show-bestseller-indicator',
                    'name'    => __( 'Show ranking indicator', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'checkbox',
                    'desc'    => __( 'Select to show ranking indicator in Best Sellers Pages.', 'yith-woocommerce-best-sellers' ),
                    'default' => 'yes'
                ),
                'show-rss-link'                         => array(
                    'id'      => 'yith-wcbsl-show-rss-link-for-bestsellers',
                    'name'    => __( 'Show rss link', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'checkbox',
                    'desc'    => __( 'Select to show RSS link in Best Sellers Pages.', 'yith-woocommerce-best-sellers' ),
                    'default' => 'yes'
                ),
                'display-options-end'                   => array(
                    'type' => 'sectionend',
                    'id'   => 'yith-wcbsl-display-options'
                ),
                'advanced-options'                      => array(
                    'title' => __( 'Advanced Options', 'yith-woocommerce-best-sellers' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith-wcbsl-advanced-options'
                ),
                'update-time'                           => array(
                    'name'    => __( 'Bestseller Update Time', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'select',
                    'desc'    => __( 'Select update time for Best Sellers Reporting', 'yith-woocommerce-best-sellers' ),
                    'id'      => 'yith-wcpsc-update-time',
                    'options' => array(
                        'yesterday' => __( '1 Day', 'yith-woocommerce-best-sellers' ),
                        '2day'      => __( '2 Days', 'yith-woocommerce-best-sellers' ),
                        '3day'      => __( '3 Days', 'yith-woocommerce-best-sellers' ),
                        '7day'      => __( '7 Days', 'yith-woocommerce-best-sellers' ),
                        'month'     => __( 'Solar Month', 'yith-woocommerce-best-sellers' ),
                        'year'      => __( 'Solar Year', 'yith-woocommerce-best-sellers' ),
                        'ever'      => __( 'Unlimited', 'yith-woocommerce-best-sellers' ),
                    ),
                    'default' => '7day'
                ),
                'badge-text'                            => array(
                    'name'    => __( 'Badge text', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'text',
                    'desc'    => __( 'Select the text for "Best Seller" Badges', 'yith-woocommerce-best-sellers' ),
                    'id'      => 'yith-wcbsl-badge-text',
                    'default' => _x( 'Best Seller', 'Text of "Bestseller" Badge', 'yith-woocommerce-best-sellers' )
                ),
                'slider-title'                          => array(
                    'name'    => __( 'Slider Title', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'text',
                    'desc'    => __( 'Enter the text you want to display as title of Best Sellers Slider', 'yith-woocommerce-best-sellers' ),
                    'id'      => 'yith-wcbsl-slider-title',
                    'default' => _x( 'Best Sellers', 'Text of "Bestsellers" Slider', 'yith-woocommerce-best-sellers' )
                ),
                'advanced-options-end'                  => array(
                    'type' => 'sectionend',
                    'id'   => 'yith-wcbsl-advanced-options'
                ),
                'colors-options'                        => array(
                    'title' => __( 'Colors', 'yith-woocommerce-best-sellers' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith-wcbsl-colors-options'
                ),
                'badge-color'                           => array(
                    'name'      => __( 'Badge background color', 'yith-woocommerce-best-sellers' ),
                    'type'      => 'yith-field',
                    'yith-type' => 'colorpicker',
                    'desc'      => __( 'Select background color for "Best Seller" Badges', 'yith-woocommerce-best-sellers' ),
                    'id'        => 'yith-wcbsl-badge-bg-color',
                    'default'   => '#A00000'
                ),
                'badge-text-color'                      => array(
                    'name'      => __( 'Badge text color', 'yith-woocommerce-best-sellers' ),
                    'type'      => 'yith-field',
                    'yith-type' => 'colorpicker',
                    'desc'      => __( 'Select color for the text of "Best Seller" Badges', 'yith-woocommerce-best-sellers' ),
                    'id'        => 'yith-wcbsl-badge-text-color',
                    'default'   => '#ffffff'
                ),
                'link-color'                            => array(
                    'name'      => __( 'Links background color', 'yith-woocommerce-best-sellers' ),
                    'type'      => 'yith-field',
                    'yith-type' => 'colorpicker',
                    'desc'      => sprintf( __( 'Select background color for "Top %s" links', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'id'        => 'yith-wcbsl-link-bg-color',
                    'default'   => '#A00000'
                ),
                'link-text-color'                       => array(
                    'name'      => __( 'Links text color', 'yith-woocommerce-best-sellers' ),
                    'type'      => 'yith-field',
                    'yith-type' => 'colorpicker',
                    'desc'      => sprintf( __( 'Select color for the text of "Top %s" links', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'id'        => 'yith-wcbsl-link-text-color',
                    'default'   => '#ffffff'
                ),
                'colors-options-end'                    => array(
                    'type' => 'sectionend',
                    'id'   => 'yith-wcbsl-colors-options'
                ),
                'bestsellers-icon-options'              => array(
                    'title' => __( 'Best Seller Seal', 'yith-woocommerce-best-sellers' ),
                    'type'  => 'title',
                    'desc'  => '',
                    'id'    => 'yith-wcbsl-bestsellers-icon-options'
                ),
                'show-icon-in-bestsellers'              => array(
                    'id'      => 'yith-wcbsl-show-icon-in-bestsellers',
                    'name'    => __( 'Show Seal in Best Sellers', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'checkbox',
                    'desc'    => __( 'Select to show seal for Best Sellers in their single product page.', 'yith-woocommerce-best-sellers' ),
                    'default' => 'no'
                ),
                'bestseller-icon'                       => array(
                    'id'      => 'yith-wcbsl-bestseller-icon',
                    'name'    => __( 'Best Seller Seal', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'yith_wcbsl_upload',
                    'desc'    => __( 'Upload your custom Best Seller Seal', 'yith-woocommerce-best-sellers' ),
                    'default' => YITH_WCBSL_ASSETS_URL . '/images/best-seller.png'
                ),
                'bestseller-icon-position'              => array(
                    'name'    => __( 'Position', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'select',
                    'desc'    => __( 'Select the position you want to apply to Best Seller Seals.', 'yith-woocommerce-best-sellers' ),
                    'id'      => 'yith-wcbsl-bestseller-icon-position',
                    'options' => array(
                        'before_summary'     => __( 'Before summary', 'yith-woocommerce-best-sellers' ),
                        'before_description' => __( 'Before description', 'yith-woocommerce-best-sellers' ),
                        'after_description'  => __( 'Below description', 'yith-woocommerce-best-sellers' ),
                        'after_add_to_cart'  => __( 'Below "Add to Cart" Button', 'yith-woocommerce-best-sellers' ),
                        'after_summary'      => __( 'Below summary', 'yith-woocommerce-best-sellers' ),
                    ),
                    'default' => 'after_add_to_cart'
                ),
                'bestseller-icon-align'                 => array(
                    'name'    => __( 'Align', 'yith-woocommerce-best-sellers' ),
                    'type'    => 'select',
                    'desc'    => __( 'Select alignment you want to apply to Best Seller Seals.', 'yith-woocommerce-best-sellers' ),
                    'id'      => 'yith-wcbsl-bestseller-icon-align',
                    'options' => array(
                        'left'   => __( 'Left', 'yith-woocommerce-best-sellers' ),
                        'center' => __( 'Center', 'yith-woocommerce-best-sellers' ),
                        'right'  => __( 'Right', 'yith-woocommerce-best-sellers' ),
                    ),
                    'default' => 'right'
                ),
                'bestseller-icon-only-for-top'          => array(
                    'id'      => 'yith-wcbsl-bestseller-icon-only-for-top',
                    'name'    => sprintf( __( 'Display only for Top %s', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'type'    => 'checkbox',
                    'desc'    => sprintf( __( 'Select this to display the "Best Seller" seal only for products in general Top %s.', 'yith-woocommerce-best-sellers' ), $bestseller_limit ),
                    'default' => 'no'
                ),
                'bestsellers-icon-options-end'          => array(
                    'type' => 'sectionend',
                    'id'   => 'yith-wcbsl-bestsellers-icon-options'
                ),
            );

            $settings[ 'settings' ] = array_merge( $settings[ 'settings' ], $premium_settings );

            return $settings;
        }


        /**
         * Add js for tinymce button
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_shortcode_btn_mce() {
            // Add js for tinymce button
            add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_plugin' ) );
            // Add button in tinymce
            add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ) );
        }

        /**
         * Add js for tinymce button
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_shortcode_plugin( $plugin_array ) {
            $plugin_array[ 'yith_wcbsl' ] = YITH_WCBSL_ASSETS_URL . '/js/shortcode-mce.js';

            return $plugin_array;
        }

        /**
         * Add button in tinymce
         *
         * @access   public
         * @since    1.0.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function register_shortcode_button( $buttons ) {
            array_push( $buttons, 'add_bestsellers_slider' );

            return $buttons;
        }

        /**
         * Add a panel under YITH Plugins tab
         *
         * @return   void
         * @since    1.0
         * @author   Leanza Francesco <leanzafrancesco@gmail.com>
         * @use      /Yit_Plugin_Panel class
         * @see      plugin-fw/lib/yit-plugin-panel.php
         */
        public function register_panel() {
            parent::register_panel();
            add_action( 'woocommerce_admin_field_yith_wcbsl_upload', array( $this->_panel, 'yit_upload' ), 10, 1 );
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since 1.0.0
         */
        public function register_plugin_for_activation() {
            if ( function_exists( 'YIT_Plugin_Licence' ) ) {
                YIT_Plugin_Licence()->register( YITH_WCBSL_INIT, YITH_WCBSL_SECRET_KEY, YITH_WCBSL_SLUG );
            }
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since 1.0.0
         */
        public function register_plugin_for_updates() {
            if ( function_exists( 'YIT_Upgrade' ) ) {
                YIT_Upgrade()->register( YITH_WCBSL_SLUG, YITH_WCBSL_INIT );
            }
        }
    }
}

/**
 * Unique access to instance of YITH_WCBSL_Admin class
 *
 * @return YITH_WCBSL_Admin_Premium
 * @deprecated since 1.1.0 use YITH_WCBSL_Admin() instead
 * @since      1.0.0
 */
function YITH_WCBSL_Admin_Premium() {
    return YITH_WCBSL_Admin();
}