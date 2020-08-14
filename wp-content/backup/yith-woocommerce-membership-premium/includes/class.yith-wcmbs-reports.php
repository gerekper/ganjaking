<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Members Class
 *
 * @class   YITH_WCMBS_Reports
 * @package Yithemes
 * @since   1.0.5
 * @author  Yithemes
 */
class YITH_WCMBS_Reports {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Reports
     * @since 1.0.5
     */
    private static $_instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCMBS_Reports
     * @since 1.0.5
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.5
     */
    private function __construct() {
        YITH_WCMBS_Downloads_Report();

        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'add_reports_submenu' ) );
            add_action( 'yith_wcmbs_membership_reports', array( $this, 'render_membership_reports' ) );
            add_action( 'yith_wcmbs_download_reports', array( $this, 'render_download_reports' ) );

            add_action( 'wp_ajax_yith_wcmbs_get_download_table_reports', array( $this, 'get_download_table_reports' ) );
            add_action( 'wp_ajax_yith_wcmbs_get_download_report', array( $this, 'get_download_report' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 11 );
            add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_id_to_woocommerce' ), 10, 1 );

            $is_ajax = function_exists( 'wp_doing_ajax' ) ? wp_doing_ajax() : ( defined( 'DOING_AJAX' ) && DOING_AJAX );

            if ( YITH_WCMBS_Products_Manager()->is_allowed_download() && !$is_ajax ) {
                add_action( 'add_meta_boxes', array( $this, 'add_user_donwloads_metabox_in_orders' ) );
            }

            // downloads by user
            add_action( 'wp_ajax_yith_wcmbs_get_download_reports_by_user', array( $this, 'get_download_reports_by_user' ) );
            add_action( 'wp_ajax_yith_wcmbs_get_download_reports_details_by_user', array( $this, 'get_download_reports_details_by_user' ) );
            add_action( 'wp_ajax_yith_wcmbs_get_download_reports_details_by_user_table', array( $this, 'get_download_reports_details_by_user_table' ) );
        }
    }

    public function get_download_reports_by_user() {
        $current_error_reporting = error_reporting();
        error_reporting( 0 );

        $table = new YITH_WCMBS_Download_Reports_By_User_Table();
        $table->ajax_response();

        // Enable display_errors
        error_reporting( $current_error_reporting );
        die();
    }

    public function get_download_reports_details_by_user() {
        $current_error_reporting = error_reporting();
        error_reporting( 0 );

        $table = new YITH_WCMBS_Download_Reports_Details_By_User_Table();
        $table->ajax_response();

        // Enable display_errors
        error_reporting( $current_error_reporting );
        die();
    }

    public function get_download_reports_details_by_user_table() {
        $user_id = isset( $_REQUEST[ 'user_id' ] ) ? absint( $_REQUEST[ 'user_id' ] ) : false;
        if ( !$user_id ) {
            die();
        }

        ?>
        <div id="yith-wcmbs-reports-downloads-content-downloads-by-user-<?php echo $user_id ?>" class="yith-wcmbs-reports-downloads-content" style="display:none;">
            <?php include YITH_WCMBS_TEMPLATE_PATH . '/reports/download-reports-downloads-details-by-user.php'; ?>
        </div>
        <?php

        die();
    }

    /**
     * Add Metaboxes
     *
     * @param string $post_type
     * @since    1.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    public function add_user_donwloads_metabox_in_orders( $post_type ) {
        if ( $post_type == 'shop_order' ) {
            add_meta_box( 'yith-wcmbs-user-download-reports', __( 'Membership download reports', 'yith-woocommerce-membership' ), array(
                $this,
                'render_user_donwloads_metabox_in_orders'
            ), null, 'normal', 'low' );
        }
    }

    public function render_user_donwloads_metabox_in_orders( $post ) {
        $order   = wc_get_order( $post->ID );
        $user_id = $order->get_user_id();

        echo '<div class="yith-wcmbs-reports-content">';
        wc_get_template( '/reports/download-reports-graphics.php', array( 'user_id' => $user_id ), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
        echo '</div>';

        echo '<div id="yith-wcmbs-download-reports-downloads-by-product"><div class="yith-wcmbs-reports-download-reports-table">';
        wc_get_template( '/reports/download-reports-table.php', array( 'user_id' => $user_id ), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
        echo '</div></div>';
    }

    /**
     * add Reports Submenu to Membership Admin Menu
     */
    public function add_reports_submenu() {
        add_submenu_page( 'edit.php?post_type=yith-wcmbs-plan',     //parent_slug
                          __( 'Reports', 'yith-woocommerce-membership' ),         //page_title
                          __( 'Reports', 'yith-woocommerce-membership' ),         //menu_title
                          'edit_users',                                           // capability
                          'yith-wcmbs-reports',                                   // menu_slug
                          array( $this, 'render_reports_page' )                   // callback function
        );
    }

    public function render_reports_page() {
        wc_get_template( '/reports/reports.php', array(), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
    }

    public function render_membership_reports() {
        wc_get_template( '/reports/membership-reports.php', array(), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
    }

    public function render_download_reports() {
        wc_get_template( '/reports/download-reports.php', array(), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
    }

    /*
     * get download table reports [AJAX]
     */
    public function get_download_table_reports() {
        wc_get_template( '/reports/download-reports-table.php', array(), YITH_WCMBS_TEMPLATE_PATH, YITH_WCMBS_TEMPLATE_PATH );
        die();
    }

    public function get_download_report() {
        $type = isset( $_REQUEST[ 'type' ] ) ? $_REQUEST[ 'type' ] : 'downloads-by-product';

        $file = YITH_WCMBS_TEMPLATE_PATH . "/reports/download-reports-$type.php";

        if ( file_exists( $file ) )
            include $file;

        die();
    }

    public function admin_enqueue_scripts() {
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $screen = get_current_screen();

        wp_register_style( 'yith_wcmbs_menu_styles', YITH_WCMBS_ASSETS_URL . '/css/menu.css' );
        wp_register_style( 'yith_wcmbs_reports_styles', YITH_WCMBS_ASSETS_URL . '/css/reports.css' );

        wp_register_script( 'yith_wcmbs_menu_js', YITH_WCMBS_ASSETS_URL . '/js/menu' . $suffix . '.js', array( 'jquery' ), YITH_WCMBS_VERSION, true );
        wp_register_script( 'yith_wcmbs_reports_js', YITH_WCMBS_ASSETS_URL . '/js/reports' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-tiptip' ), YITH_WCMBS_VERSION, true );

        if ( in_array( $screen->id, array( 'yith-wcmbs-plan_page_yith-wcmbs-reports', 'shop_order' ) ) ) {
            wp_enqueue_style( 'yith_wcmbs_menu_styles' );
            wp_enqueue_style( 'yith_wcmbs_reports_styles' );

            wp_enqueue_script( 'wc-enhanced-select' );
            wp_enqueue_script( 'yith_wcmbs_menu_js' );
            wp_enqueue_script( 'yith_wcmbs_reports_js' );
        }

    }

    public function add_screen_id_to_woocommerce( $screen_ids ) {
        $screen_ids[] = 'yith-wcmbs-plan_page_yith-wcmbs-reports';
        return $screen_ids;
    }

}

/**
 * Unique access to instance of YITH_WCMBS_Reports class
 *
 * @return YITH_WCMBS_Reports
 * @since 1.0.5
 */
function YITH_WCMBS_Reports() {
    return YITH_WCMBS_Reports::get_instance();
}