<?php
/**
 * Frontend class
 *
 * @author YITH
 * @package YITH WooCommerce Social Login
 * @version 1.0.0
 */

if ( ! defined( 'YITH_YWSL_INIT' ) ) {
    exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WC_Social_Login_Frontend' ) ){
    /**
     * YITH WooCommerce Social Login Admin class
     *
     * @since 1.0.0
     */
    class YITH_WC_Social_Login_Frontend {
        /**
         * Single instance of the class
         *
         * @var \YITH_WC_Social_Login_Frontend
         * @since 1.0.0
         */
        protected static $instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WC_Social_Login_Frontend
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
         * @return \YITH_WC_Social_Login_Frontend
         * @since 1.0.0
         */
	    public function __construct() {

		    //custom styles and javascripts
		    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
		    add_action( 'login_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
	    }

        /**
         * Enqueue Scripts and Styles
         *
         * @return void
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function enqueue_styles_scripts() {

	        $socials     = YITH_WC_Social_Login()->enabled_social;
	        $redirect_to = YITH_WC_Social_Login()->get_redirect_to();
	        $social_args = array();

	        foreach ( $socials as $key => $value ) {
		        $enabled = get_option( 'ywsl_' . $key . '_enable' );

		        if ( $enabled == 'yes' ) {
			        $social_args[ strtolower( $value['label'] ) ] = esc_url( add_query_arg( array(
				                                                                                'ywsl_social' => $key,
				                                                                                'redirect'    => urlencode( $redirect_to )
			                                                                                ), site_url( 'wp-login.php' ) ) );
		        }
	        }

            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_script( 'ywsl_frontend_social', YITH_YWSL_ASSETS_URL . '/js/frontend' . $suffix . '.js', array( 'jquery' ), time(), true );
	        wp_localize_script( 'ywsl_frontend_social', 'ywsl', $social_args );

	        wp_enqueue_style( 'ywsl_frontend', YITH_YWSL_ASSETS_URL . '/css/frontend.css' );
        }

        /**
         * Print social buttons
         *
         * @return void
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function social_buttons( $template_part = '', $is_shortcode = false, $atts = array() ) {

            $enabled_social = YITH_WC_Social_Login()->enabled_social;
            $template_part  = empty( $template_part ) ? 'social-buttons' : $template_part;


            if ( $is_shortcode ) {
                ob_start();
            }

            $args = array(
                'label'          => get_option( 'ywsl_social_label' ),
                'socials'        => $enabled_social,
                'label_checkout' => get_option( 'ywsl_social_label_checkout' ),
                'redirect_to'    => YITH_WC_Social_Login()->get_redirect_to()
            );

            $args = wp_parse_args( $atts, $args );

            if ( !is_user_logged_in() && !empty( $enabled_social ) ) {
               wc_get_template( $template_part . '.php', $args,  '', YITH_YWSL_TEMPLATE_PATH.'/');
            }

            if ( $is_shortcode ) {
                return ob_get_clean();
            }
        }

        /**
         * Show social buttons in checkout page
         *
         * @return void
         * @since  1.0.0
         * @author Emanuela Castorina
         */
        public function social_buttons_in_checkout( $template_name ) {
            if ( $template_name == 'checkout/form-login.php' ) {

               $this->social_buttons('social-buttons-checkout');
            }
        }

    }

    /**
     * Unique access to instance of YITH_WC_Social_Login_Frontend class
     *
     * @return \YITH_WC_Social_Login_Frontend
     */
    function YITH_WC_Social_Login_Frontend() {
        return YITH_WC_Social_Login_Frontend::get_instance();
    }


}
