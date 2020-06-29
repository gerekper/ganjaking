<?php

defined( 'ABSPATH' ) or exit;

/**
 * @class      YITH_COG_Report_Links
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
if ( ! class_exists( 'WC_Admin_Reports' ) ) {
    require_once( ABSPATH . 'wp-content/plugins/woocommerce/includes/admin/class-wc-admin-reports.php' );
}


/**
 * Class YITH_COG_Report_Links
 */
class YITH_COG_Report_Links extends WC_Admin_Reports {

    /**
     * Returns the definitions for the reports to show in admin.
     *
     * @return array
     */
    public static function get_reports() {
        $reports = array(
            'reports'     => array(
                'title'  => '',
                'reports' => array(
                    "sales_by_date" => array(
                        'title'       => esc_html__( 'Sales by date', 'yith-cost-of-goods-for-woocommerce' ),
                        'description' => '',
                        'hide_title'  => true,
                        'callback'    => array( __CLASS__, 'get_report' ),
                    ),
                    "sales_by_product" => array(
                        'title'       => esc_html__( 'Sales by product', 'yith-cost-of-goods-for-woocommerce' ),
                        'description' => '',
                        'hide_title'  => true,
                        'callback'    => array( __CLASS__, 'get_report' ),
                    ),
                    "sales_by_category" => array(
                        'title'       => esc_html__( 'Sales by category', 'yith-cost-of-goods-for-woocommerce' ),
                        'description' => '',
                        'hide_title'  => true,
                        'callback'    => array( __CLASS__, 'get_report' ),
                    ),
                ),
            ),
        );

        return $reports;
    }



}
