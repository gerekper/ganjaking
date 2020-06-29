<?php
/**
 * Admin class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Zoom Magnifier
 * @version 1.1.2
 */

if ( ! defined ( 'YITH_WCMG' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists ( 'YITH_WCMG_Admin' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since 1.0.0
     */
    class YITH_WCMG_Admin {
        /**
         * Plugin options
         *
         * @var array
         * @access public
         * @since  1.0.0
         */
        public $options = array ();

        /**
         * Various links
         *
         * @var string
         * @access public
         * @since  1.0.0
         */
        public $banner_url = 'http://cdn.yithemes.com/plugins/yith_magnifier.php?url';
        public $banner_img = 'http://cdn.yithemes.com/plugins/yith_magnifier.php';
        public $doc_url = 'https://docs.yithemes.com/yith-woocommerce-zoom-magnifier/';

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct () {

            //Actions
            add_action ( 'admin_enqueue_scripts', array ( $this, 'enqueue_styles_scripts' ) );

            add_action ( 'woocommerce_update_options_yith_wcmg', array ( $this, 'update_options' ) );

            add_action ( 'woocommerce_admin_field_banner', array ( $this, 'admin_fields_banner' ) );

            add_filter ( 'woocommerce_catalog_settings', array ( $this, 'add_catalog_image_size' ) );

            //Apply filters
            $this->banner_url = apply_filters ( 'yith_wcmg_banner_url', $this->banner_url );

            // YITH WCMG Loaded
            do_action ( 'yith_wcmg_loaded' );
        }

        /**
         * Add Zoom Image size to Woocommerce -> Catalog
         *
         * @access public
         *
         * @param array $settings
         *
         * @return array
         */
        public function add_catalog_image_size ( $settings ) {
            $tmp = $settings[ count ( $settings ) - 1 ];
            unset( $settings[ count ( $settings ) - 1 ] );

            $settings[] = array (
                'name'     => esc_html__( 'Image Size', 'yith-woocommerce-zoom-magnifier' ),
                'desc'     => esc_html__( 'The size of the images used within the magnifier box', 'yith-woocommerce-zoom-magnifier' ),
                'id'       => 'woocommerce_magnifier_image',
                'css'      => '',
                'type'     => 'image_width',
                'default'  => array (
                    'width'  => 600,
                    'height' => 600,
                    'crop'   => true,
                ),
                'std'      => array (
                    'width'  => 600,
                    'height' => 600,
                    'crop'   => true,
                ),
                'desc_tip' => true,
            );
            $settings[] = $tmp;

            return $settings;
        }

        


        /**
         * Enqueue admin styles and scripts
         *
         * @access public
         * @return void
         * @since  1.0.0
         */
        public function enqueue_styles_scripts () {
            wp_enqueue_script ( 'jquery-ui' );
            wp_enqueue_script ( 'jquery-ui-core' );
            wp_enqueue_script ( 'jquery-ui-mouse' );
            wp_enqueue_script ( 'jquery-ui-slider' );

            wp_enqueue_style ( 'yith_wcmg_admin', YITH_WCMG_URL . 'assets/css/admin.css' );
        }
    }
}
