<?php
/**
 * Main Premium class
 *
 * @author YITH
 * @package YITH WooCommerce Social Login
 * @version 1.0.0
 */

if ( ! defined( 'YITH_YWSL_INIT' ) ) {
    exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WC_Social_Login_Premium' ) ){
    /**
     * YITH WooCommerce Social Login main class
     *
     * @since 1.0.0
     */
    class YITH_WC_Social_Login_Premium extends YITH_WC_Social_Login {

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Social_Login
         * @since 1.0.0
         */
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        /**
         * Constructor.
         *
         * @return \YITH_WC_Social_Login_Premium
         * @since 1.0.0
         */
        public function __construct() {

            parent::__construct();

            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            //add filter for social icon
            $this->icon_filters();
            add_filter( 'ywsl_redirect_to_after_login', array( $this, 'redirect_to_options' ) );

            //premium hooks for frontend
            if ( !is_admin() ) {
                if ( get_option( 'ywsl_show_in_checkout' ) == 'yes' ) {
                    add_action( 'woocommerce_after_template_part', array( YITH_WC_Social_Login_Frontend(), 'social_buttons_in_checkout' ) );
                }

                if ( get_option( 'ywsl_show_in_my_account_login_form' ) == 'yes' ) {
                    add_action( 'woocommerce_login_form', array( YITH_WC_Social_Login_Frontend(), 'social_buttons' ) );
                }

                if ( get_option( 'ywsl_show_in_my_account_register_form' ) == 'yes' ) {
                    add_action( 'register_form', array( YITH_WC_Social_Login_Frontend(), 'social_buttons' ) );
	                add_action( 'woocommerce_register_form', array( YITH_WC_Social_Login_Frontend(), 'social_buttons' ) );
                }

                if ( get_option( 'ywsl_show_in_wp_login' ) == 'yes' ) {
                    add_action( 'login_form', array( YITH_WC_Social_Login_Frontend(), 'social_buttons' ) );
	                add_action( 'woocommerce_login_form', array( YITH_WC_Social_Login_Frontend(), 'social_buttons' ) );
                }

                if ( get_option( 'ywsl_show_in_comments' ) == 'yes' ) {
                    add_action( 'comment_form_top', array( $this, 'social_buttons_in_comments' ) );
                }

                if ( get_option( 'ywsl_show_in_comments_after_form' ) == 'yes' ) {
                    add_action( 'comment_form_after', array( $this, 'social_buttons_in_comments' ) );
                }

                if ( get_option( 'ywsl_myaccount_show_list' ) != 'none' ) {
                    add_action( 'woocommerce_' . get_option( 'ywsl_myaccount_show_list' ) . '_my_account', array( $this, 'my_account_social_connection' ) );
                }

            }

            add_action( 'init', array( $this, 'social_unlink' ) );

            //register widget
            add_action( 'widgets_init', array( $this, 'register_widgets' ) );
            add_shortcode( 'yith_wc_social_login', array( $this, 'yith_wc_social_login_shortcode' ) );

        }

        /**
         * Add a social connection section in my-account page
         *
         * @return array
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        public function my_account_social_connection(){
            $user_id = get_current_user_id();
            $connections = $this->get_social_login_connection( $user_id, 30, 'list');
            $args = array(
                'user_connections' => $connections,
                'user_unlinked_social' => $this->get_user_unlinked_connections( $connections ),
            );
            yit_plugin_get_template( YITH_YWSL_DIR, 'myaccount/social-connections.php', $args );
        }

        /**
         * Return the a list of social not linked to user
         *
         * @return array
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        public function get_user_unlinked_connections( $connections ) {
            $enabled = $this->enabled_social;
            foreach ( $connections as $key => $connection ) {
                unset( $enabled[$key] );
            }
            return $enabled;
        }

        /**
         * Return the connections of a user
         *
         * @return array/string
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function get_social_login_connection( $user_id, $image_size = 30, $output = "buttons" ) {
            global $wpdb;
            $query        = $wpdb->prepare( "SELECT meta_key from $wpdb->usermeta WHERE meta_key LIKE '%%_login_id' AND user_id=%d", $user_id );
            $connections  = $wpdb->get_results( $query, ARRAY_A );
            $buttons      = '';
            $list         = array();
            $ordered_list = get_option( 'ywsl_social_networks' );
            foreach ( $connections as $connection ) {
                $net = str_replace( '_login_id', '', $connection['meta_key'] );
                if ( YITH_WC_Social_Login()->is_enabled( $net ) ) {
                    $button        = '<span class="user_table_social"><img src="' . apply_filters( 'ywsl_custom_icon_' . $net, YITH_YWSL_ASSETS_URL . '/images/' . $net . '.png', $net ) . '" width="' . $image_size . '"></span>';
                    $unlink_button = '<form class="social_unlink" method="post">
                                <input type="hidden" name="provider" value="' . $net . '">'
                        . wp_nonce_field( 'unlink-social-' . $net, 'nonce_social' ) .
                        '<input type="submit" class="button" name="submit" value="' . __( 'Unlink', 'yith-woocommerce-social-login' ) . '"></form>';
                    $list[$net]    = array(
                        'displayName'   => $this->get_user_info( $user_id, 'displayName', $net ),
                        'profileURL'    => $this->get_user_info( $user_id, 'profileURL', $net ),
                        'button'        => $button,
                        'unlink_button' => $unlink_button,
                    );
                    $buttons .= $button;
                }
            }
            if ( $output == "list" ) {
                $new_ordered_list = $ordered_list;
                $i                = 0;
                if( is_array($ordered_list)){
                    foreach ( $ordered_list as $key => $value ) {
                        if ( !isset( $list[$value] ) ) {
                            unset( $new_ordered_list[$i] );
                        }
                        $i ++;
                    }

                    $list = array_merge( array_flip( $new_ordered_list ), $list );
                }


                return $list;
            }

            return $buttons;
        }

        /**
         * Return the provider info stored in $provide_login_data user meta
         *
         * @return array|bool
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function get_user_info( $userid, $info, $provider ) {
            $obj = get_user_meta( $userid, $provider . '_login_data', true );
            if ( isset( $obj[$info] ) ) {
                return $obj[$info];
            }
            return false;
        }

        /**
         * Unlink the social connection to the user
         *
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        function social_unlink() {
            if (  ( defined( 'DOING_AJAX' ) &&  DOING_AJAX ) || !isset( $_REQUEST['nonce_social'] ) || !isset( $_REQUEST['provider'] ) || !wp_verify_nonce( $_POST['nonce_social'], 'unlink-social-' . $_REQUEST['provider'] ) ) {
                return;
            }

            $user_id  = get_current_user_id();
            $provider = $_REQUEST['provider'];
            delete_user_meta( $user_id, $provider . '_login_data' );
            delete_user_meta( $user_id, $provider . '_login_id' );

            wp_redirect( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
            exit;
        }

        /**
         * Add filters for each social icons
         *
         * @return  void
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        public function icon_filters() {
            $social_list = $this->social_list;

            foreach ( $social_list as $key => $social ) {
                add_filter( 'ywsl_custom_icon_' . $key, array( $this, 'get_icon' ), 10, 2 );
            }
        }

        /**
         * Get the icon of a social
         *
         * @return  string
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        public function get_icon( $icon, $social){
            $custom_image = get_option('ywsl_'.$social.'_icon');
            if ( ! empty( $custom_image ) ) {
                $icon = $custom_image;
            }

            return $icon;
        }

        /**
         * Get redirect url from setting options
         *
         * @return  string
         * @since    1.0.0
         * @author   Emanuela Castorina
         */
        public function redirect_to_options( $return_url = '' ){
            $redirect_url = get_option('ywsl_redirect_url');

            switch ( $redirect_url ) {
                case 'shop':
                    $return_url = get_permalink( wc_get_page_id( 'shop' ) );
                    break;
                case 'myaccount':
                    $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
                    if ( $myaccount_page_id ) {
                        $return_url = get_permalink( $myaccount_page_id );
                    }
                    break;
                case 'cart':
                    $return_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : WC()->cart->get_cart_url();
                    break;
                case 'checkout':
                    $return_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : WC()->cart->get_checkout_url();
                    break;
                case 'custom':
                    $redirect_custom_url = get_option( 'ywsl_redirect_custom_url' );
                    $return_url          = ( $redirect_custom_url && $redirect_custom_url != '' ) ? $redirect_custom_url : $return_url;
                    break;
                default:

            }
            return $return_url;
        }

        /**
         * Print the Social Login Buttons
         *
         * @since  1.0.0
         * @return string
         * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
         */
        public function yith_wc_social_login_shortcode( $atts  ){

            $args = shortcode_atts( array(
                'label' => __( 'Login with:', 'yith-woocommerce-social-login' ),
                'redirect_url' => $this->redirect_to_options( ywsl_curPageURL() )
            ), $atts );

            return YITH_WC_Social_Login_Frontend()->social_buttons( '', true, $args );

        }

        /**
         * Register the widgets
         *
         * @since   1.0.0
         * @author  Emanuela Castorina
         * @return  void
         */
        public function register_widgets(){
            register_widget( 'YWSL_Social_Login_Widget' );
        }

        /**
         * Show
         *
         * @since   1.0.0
         * @author  Emanuela Castorina
         * @return  void
         */
        public function social_buttons_in_comments(){
            if ( is_single() ) {
                YITH_WC_Social_Login_Frontend()->social_buttons();
            }
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation() {
            if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
                require_once( YITH_YWSL_DIR . 'plugin-fw/licence/lib/yit-licence.php' );
                require_once( YITH_YWSL_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php' );
            }
            YIT_Plugin_Licence()->register( YITH_YWSL_INIT, YITH_YWSL_SECRET_KEY, YITH_YWSL_SLUG );
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates() {
            if( ! class_exists( 'YIT_Upgrade' ) ) {
                require_once YITH_YWSL_DIR.'plugin-fw/lib/yit-upgrade.php';
            }
            YIT_Upgrade()->register( YITH_YWSL_SLUG, YITH_YWSL_INIT );
        }

    }

    /**
     * Unique access to instance of YITH_WC_Social_Login_Premium class
     *
     * @return \YITH_WC_Social_Login_Premium
     */
    function YITH_WC_Social_Login_Premium() {
        return YITH_WC_Social_Login_Premium::get_instance();
    }


}

