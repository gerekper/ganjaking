<?php
/*
* This file belongs to the YIT Framework.
*
* This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://www.gnu.org/licenses/gpl-3.0.txt
*/
if ( ! defined( 'YITH_WCGPF_VERSION' ) ) {
exit( 'Direct access forbidden.' );
}

/**
* @class      YITH_WCGPF_Admin_Premium
* @package    Yithemes
* @since      Version 1.0.0
* @author     Your Inspiration Themes
*
*/

if ( ! class_exists( 'YITH_WCGPF_Admin_Premium' ) ) {
    
    class YITH_WCGPF_Admin_Premium extends YITH_WCGPF_Admin {

        /**
         * Main Instance
         *
         * @var YITH_WCGPF_Admin_Premium
         * @since 1.0
         * @access protected
         */
        protected static $_instance = null;

        /**
         * Main plugin Instance
         *
         * @return
         * @var YITH_WCGPF_Admin_Premium instance
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         */
        public static function get_instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$_instance ) ) {
                $self::$_instance = new $self;
            }

            return $self::$_instance;
        }
        
        public function __construct()
        {
            parent::__construct();

            $this->show_premium_landing = false;
            add_filter('yith_wcgpf_admin_tabs',array($this,'add_premium_tabs'));
            add_action('yith_wcgpf_template_feed_tab',array($this,'show_template_feed_table'));
            add_action('yith_wcgpf_google_custom_fields', array($this,'google_custom_fields'));
            //add metabox
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
            add_action( 'woocommerce_process_product_meta', array( $this, 'save_meta_box_product_feed' ), 1, 2 );
            //add product feed configuration in variation product
            add_action('woocommerce_product_after_variable_attributes', array($this,'variation_product_configuration'),99,3);
            add_action('woocommerce_save_product_variation', array( $this, 'save_variation' ), 10, 2 );

            add_filter('yith_wcgpf_google_product_information_options',array($this,'google_product_information_options'));

            /* Register plugin to licence/update system */
            add_action('wp_loaded', array($this, 'register_plugin_for_activation'), 99);
            add_action('admin_init', array($this, 'register_plugin_for_updates'));


        }

        public function add_premium_tabs( $tabs ) {

            $tabs['template-feed']  = esc_html__( 'Feed Configuration Templates', 'yith-google-product-feed-for-woocommerce');
            $tabs['google-custom-fields'] = esc_html__( 'Google Custom Fields', 'yith-google-product-feed-for-woocommerce');
            $tabs['general'] = esc_html__( 'General options', 'yith-google-product-feed-for-woocommerce');

            return $tabs;
        }

        public function show_template_feed_table() {
            wc_get_template( 'admin/template-feed-table.php', array(), '', YITH_WCGPF_TEMPLATE_PATH );
        }

        public function google_custom_fields() {
            wc_get_template('admin/google-custom-template.php' ,array(), '', YITH_WCGPF_TEMPLATE_PATH );
        }
        
        /***
         * Register metabox in product settings
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         */
        public function add_meta_boxes() {
                // Products
                add_meta_box('yith-wcgpf-google-product-feed-information', esc_html__('Google feed fields', 'yith-google-product-feed-for-woocommerce'), array($this, 'google_product_feed_content'), 'product', 'normal', 'low');
        }
        /***
         * Add content metabox in product settings
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         */
        function google_product_feed_content($post){

            if( file_exists( YITH_WCGPF_TEMPLATE_PATH . 'admin/product-feed-information/product-information.php' ) ) {
                include_once( YITH_WCGPF_TEMPLATE_PATH . 'admin/product-feed-information/product-information.php' );
            }
        }

        /***
         * Add template variation information in variation product
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         */
        public function variation_product_configuration($loop, $variation_data, $variation) {
            if( file_exists( YITH_WCGPF_TEMPLATE_PATH . 'admin/product-feed-information/variation-information.php' ) ) {
                include( YITH_WCGPF_TEMPLATE_PATH . 'admin/product-feed-information/variation-information.php' );
            }
        }
        /***
         * Save product configuration feed
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         */
        public function save_meta_box_product_feed($post_id,$post)
        {
            if ( isset( $_POST[ 'yith-wcgpf-product-feed-configuration' ] ) ) {

                $product_feed_configuration = array_filter(array_values($_POST['yith-wcgpf-product-feed-configuration']));

                if( !empty( $product_feed_configuration ) ) {

                    update_post_meta($post_id, 'yith_wcgpf_product_feed_configuration', $_POST['yith-wcgpf-product-feed-configuration']);
                }
            }


            if ( isset( $_POST[ 'yith-wcgpf-shipping-feed-configuration' ] ) ) {

                $shipping_feed = array_filter(array_values($_POST[ 'yith-wcgpf-shipping-feed-configuration' ]));

                if( !empty( $shipping_feed ) ) {

                    update_post_meta($post_id, 'yith_wcgpf_shipping_feed_configuration', $_POST['yith-wcgpf-shipping-feed-configuration']);
                }
            }
        }
        /***
         * Save variation configuration feed
         *
         * @author Carlos Rodríguez <carlos.rodriguez@yourinspiration.it>
         * @since  1.0
         * @return void
         */
        public function save_variation($variation_id, $i)
        {
            if (isset($_POST['yith-wcgpf-product-feed-configuration'][$variation_id])) {

                $product_feed_configuration = array_filter(array_values($_POST['yith-wcgpf-product-feed-configuration'][$variation_id]));

                if( !empty( $product_feed_configuration ) ) {

                    update_post_meta($variation_id, 'yith_wcgpf_product_feed_configuration', $_POST['yith-wcgpf-product-feed-configuration'][$variation_id]);
                }

            }
            if ( isset( $_POST[ 'yith-wcgpf-shipping-feed-configuration' ][$variation_id] ) ) {

                $shipping_feed = array_filter(array_values($_POST['yith-wcgpf-shipping-feed-configuration'][$variation_id]));

                if( !empty( $shipping_feed ) ) {

                    update_post_meta($variation_id, 'yith_wcgpf_shipping_feed_configuration', $_POST['yith-wcgpf-shipping-feed-configuration'][$variation_id]);
                }
            }
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation()
        {
            if (!class_exists('YIT_Plugin_Licence')) {
                require_once YITH_WCGPF_PATH . '/plugin-fw/licence/lib/yit-licence.php';
                require_once YITH_WCGPF_PATH . '/plugin-fw/licence/lib/yit-plugin-licence.php';
            }
            YIT_Plugin_Licence()->register(YITH_WCGPF_INIT, YITH_WCGPF_SECRETKEY, YITH_WCGPF_SLUG);

        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates()
        {
            if (!class_exists('YIT_Upgrade')) {
                require_once(YITH_WCGPF_PATH . '/plugin-fw/lib/yit-upgrade.php');
            }
            YIT_Upgrade()->register(YITH_WCGPF_SLUG, YITH_WCGPF_INIT);
        }

        public function enqueue_styles_scripts() {
            parent::enqueue_styles_scripts();

            wp_enqueue_script( 'yith_wcgpf_custom_fields_tab_js', YITH_WCGPF_ASSETS_URL . 'js/yith-wcgpf-custom-fields.js', array( 'jquery' ), '1.0.0', true );
            wp_localize_script( 'yith_wcgpf_custom_fields_tab_js', 'yith_wcgpf_custom_fields_tab_js', apply_filters( 'yith_wcgpf_custom_fields_tab_localize',array(
                'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
            )));
        }

        /**
         * Show product feed table
         *
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return void
         */

        public function show_product_feeds_table() {
            wc_get_template( 'admin/product-feed-table-premium.php', array(), '', YITH_WCGPF_TEMPLATE_PATH );
        }

        /**
         * Add google product information options
         *
         * @author Carlos Rodriguez <carlos.rodriguez@yourinspiration.it>
         * @since 1.0.0
         * @return array
         */
        public function google_product_information_options($options) {
            $function_product = YITH_Google_Product_Feed()->product_function;

            $new_options = array(
                'google_tab_google_options_energy_efficiency_class' => array(
                    'title'   => esc_html_x( 'Energy efficiency class', 'Energy efficiency class: A+', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'select',
                    'id'      => 'yith_wcgpf_tab_google_energy_efficiency_class',
                    'options' => $function_product->energy_efficiency(),
                    'class'   => 'yith-wcgpf-general-tab-options-google-select yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_gender' => array(
                    'title'   => esc_html_x( 'Gender', 'Admin option: Gender: Female', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'select',
                    'id'      => 'yith_wcgpf_tab_google_gender',
                    'options' => $function_product->gender(),
                    'class'   => 'yith-wcgpf-general-tab-options-google-select yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_age_group' => array(
                    'title'   => esc_html_x( 'Age group', 'Admin option: Age group: Infant', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'select',
                    'id'      => 'yith_wcgpf_tab_google_age_group',
                    'options' => $function_product->age_group(),
                    'class'   => 'yith-wcgpf-general-tab-options-google-select yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_material' => array(
                    'title'   => esc_html_x( 'Material', 'Admin option: Material: Plastic', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'text',
                    'id'      => 'yith_wcgpf_tab_google_material',
                    'class'   => 'yith-wcgpf-general-tab-options-google-text yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_pattern' => array(
                    'title'   => esc_html_x( 'Pattern', 'Admin option: Pattern: text ', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'text',
                    'id'      => 'yith_wcgpf_tab_google_pattern',
                    'class'   => 'yith-wcgpf-general-tab-options-google-text yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_size' => array(
                    'title'   => esc_html_x( 'Size', 'Admin option: Size: XL', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'text',
                    'id'      => 'yith_wcgpf_tab_google_size',
                    'class'   => 'yith-wcgpf-general-tab-options-google-text yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_size_type' => array(
                    'title'   => esc_html_x( 'Size type', 'Admin option: Size type: Petite', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'select',
                    'id'      => 'yith_wcgpf_tab_google_size_type',
                    'options' => $function_product->size_type(),
                    'class'   => 'yith-wcgpf-general-tab-options-google-select yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_size_system' => array(
                    'title'   => esc_html_x( 'Size system', 'Admin option: Size system: United Kingdom sizing ', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'select',
                    'id'      => 'yith_wcgpf_tab_google_size_system',
                    'options' => $function_product->size_system(),
                    'class'   => 'yith-wcgpf-general-tab-options-google-select yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_custom_label_0' => array(
                    'title'   => esc_html_x( 'Custom label 0', 'Admin option: custom label 0', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'text',
                    'id'      => 'yith_wcgpf_tab_google_custom_label_0',
                    'class'   => 'yith-wcgpf-general-tab-options-google-text yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_custom_label_1' => array(
                    'title'   => esc_html_x( 'Custom label 1', 'Admin option: custom label 1', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'text',
                    'id'      => 'yith_wcgpf_tab_google_custom_label_1',
                    'class'   => 'yith-wcgpf-general-tab-options-google-text yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_custom_label_2' => array(
                    'title'   => esc_html_x( 'Custom label 2', 'Admin option: custom label 2', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'text',
                    'id'      => 'yith_wcgpf_tab_google_custom_label_2',
                    'class'   => 'yith-wcgpf-general-tab-options-google-text yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_custom_label_3' => array(
                    'title'   => esc_html_x( 'Custom label 3', 'Admin option: custom label 3 ', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'text',
                    'id'      => 'yith_wcgpf_tab_google_custom_label_3',
                    'class'   => 'yith-wcgpf-general-tab-options-google-text yith-wcgpf-general-feed-fields'
                ),
                'google_tab_google_options_custom_label_4' => array(
                    'title'   => esc_html_x( 'Custom label 4', 'Admin option: custom label 4', 'yith-google-product-feed-for-woocommerce' ),
                    'type'    => 'text',
                    'id'      => 'yith_wcgpf_tab_google_custom_label_4',
                    'class'   => 'yith-wcgpf-general-tab-options-google-text yith-wcgpf-general-feed-fields'
                ),
            );

            array_splice($options, count($options) -1, 0, $new_options);

            return $options;
        }

        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.1.1
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_WCGPF_INIT' ) {
            $new_row_meta_args = parent::plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file );

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ){
                $new_row_meta_args['is_premium'] = true;
            }

            return $new_row_meta_args;
        }
        /**
         * Regenerate auction prices
         *
         * Action Links
         *
         * @return void
         * @since    1.1.1
         * @author   Carlos Rodríguez <carlos.rodriguez@youirinspiration.it>
         */
        public function action_links( $links ) {
            $links = yith_add_action_links( $links, $this->_panel_page, true );
            return $links;
        }
    }
    
    
}