<?php
/**
 * Assets Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_Admin_Assets' ) ) {
    /**
     * YITH WooCommerce Membership Assets Admin
     *
     * @since 1.0.0
     */
    class YITH_WCMBS_Admin_Assets {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCMBS_Admin_Assets
         * @since 1.0.0
         */
        protected static $instance;


        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCMBS_Admin_Assets
         * @since 1.0.0
         */
        public static function get_instance() {

            if ( is_null( self::$instance ) ) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Constructor
         *
         * @since 1.0.0
         */
        private function __construct() {
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

            add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ), 99, 1 );

        }

        /**
         * Add custom screen ids to standard WC
         *
         * @access public
         *
         * @param array $screen_ids
         *
         * @return array
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_screen_ids( $screen_ids ) {
            // used for example to include tip-tip css style
            $screen_ids[] = 'edit-yith-wcmbs-plan';
            $screen_ids[] = 'yith-wcmbs-plan';
            $screen_ids[] = 'users';

            return $screen_ids;
        }

        public function admin_styles() {

        }

        public function admin_scripts() {
            $suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            $premium_suffix = $this->is_premium() ? '_premium' : '';

            wp_register_style( 'yith-wcmbs-admin-styles', YITH_WCMBS_ASSETS_URL . '/css/admin' . $premium_suffix . '.css', array(), YITH_WCMBS_VERSION );
            wp_register_style( 'yith-wcmbs-membership-statuses', YITH_WCMBS_ASSETS_URL . '/css/membership-statuses.css', array(), YITH_WCMBS_VERSION );


            wp_register_script( 'yith_wcmbs_admin_m_metabox_js', YITH_WCMBS_ASSETS_URL . '/js/admin_m_metabox' . $suffix . '.js', array( 'jquery' ), YITH_WCMBS_VERSION, true );
            wp_register_script( 'yith_wcmbs_admin_js', YITH_WCMBS_ASSETS_URL . '/js/admin' . $premium_suffix . $suffix . '.js', array( 'jquery', 'jquery-tiptip', 'jquery-ui-sortable', 'select2'), YITH_WCMBS_VERSION, true );
            wp_localize_script( 'yith_wcmbs_admin_js', 'obj', array(
                'customer_nonce' => wp_create_nonce( "search-customers" )
            ) );

            wp_register_script( 'yith-wcmbs-admin-protected-links', YITH_WCMBS_ASSETS_URL . '/js/admin-protected-links' . $suffix . '.js', array( 'jquery', 'wc-enhanced-select' ), YITH_WCMBS_VERSION, true );

            wp_register_script( 'yith-wcmbs-enhanced-select', YITH_WCMBS_ASSETS_URL . '/js/enhanced-select' . $suffix . '.js', array( 'jquery', 'select2', 'woocommerce_admin' ), YITH_WCMBS_VERSION, true );
            wp_localize_script( 'yith-wcmbs-enhanced-select', 'yith_wcmbs_enhanced_select_params', array(
                'ajax_url'           => admin_url( 'admin-ajax.php' ),
                'search_posts_nonce' => wp_create_nonce( 'search-posts' ),
                'wc2_7'              => version_compare( WC()->version, '2.7', '>=' )
            ) );


            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'jquery-ui-style-css', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/themes/smoothness/jquery-ui.css' );
            wp_enqueue_style( 'OpenSans', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300' );

            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_script( 'jquery-ui-tabs' );
            wp_enqueue_script( 'jquery-ui-datepicker' );

            wp_enqueue_style( 'yith-wcmbs-admin-styles' );
            wp_enqueue_style( 'yith-wcmbs-membership-statuses' );
            wp_enqueue_script( 'yith_wcmbs_admin_js' );

            $protected_link_post_types = apply_filters( 'yith_wcmbs_protected_link_post_types', array( 'post', 'page', 'product' ) );

            if ( $this->is( $protected_link_post_types ) ) {
                wp_enqueue_script( 'yith-wcmbs-admin-protected-links' );
            }

            if ( $this->is( 'yith-wcmbs-plan' ) ) {
                global $post;
                $post_id = $post->ID;
                wp_localize_script( 'yith_wcmbs_admin_m_metabox_js', 'loc_obj', array(
                    'post_id' => $post_id,
                ) );
                wp_enqueue_script( 'yith_wcmbs_admin_m_metabox_js' );
                wp_enqueue_script( 'yith-wcmbs-enhanced-select' );
            }
        }

        public function is_premium() {
            return defined( 'YITH_WCMBS_PREMIUM' ) && YITH_WCMBS_PREMIUM;
        }

        public function is_in_membership_pages() {
            $is      = false;
            $allowed = array(
                'settings',
                'ywcmbs-membership',
                'edit-ywcmbs-membership',
                'yith-wcmbs-plan',
                'edit-yith-wcmbs-plan',
                'yith-wcmbs-thread',
                'edit-yith-wcmbs-thread',
                'yith-wcmbs-plan_page_yith-wcmbs-reports'
            );

            foreach ( $allowed as $a ) {
                $is = $is || $this->is( $a );
                if ( $is )
                    break;
            }

            return $is;
        }

        /**
         * @param array|string $id
         * @param string       $arg
         *
         * @return bool
         */
        public function is( $id, $arg = '' ) {
            $panel_page = 'yith_wcmbs_panel';
            $screen     = get_current_screen();

            //vd($screen);
            switch ( $id ) {
                case 'settings':
                    if ( strpos( $screen->id, 'page_' . $panel_page ) > 0 ) {
                        if ( !!$arg ) {
                            return isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === $arg;
                        }

                        return true;
                    }

                    return false;
                    break;
                default:
                    if ( is_array( $id ) ) {
                        return in_array( $screen->id, $id );
                    } elseif ( $id === $screen->id ) {
                        return true;
                    }
                    break;
            }

            return false;
        }
    }
}