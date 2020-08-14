<?php

defined( 'ABSPATH' ) or exit;

/**
 * @class      YITH_COG_Admin_Report
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
if ( ! class_exists( 'WC_Admin_Report' ) ) {

    $path = WP_PLUGIN_DIR.'/woocommerce/includes/admin/reports/class-wc-admin-report.php';

    require_once($path);

    //require_once( ABSPATH . 'wp-content/plugins/woocommerce/includes/admin/reports/class-wc-admin-report.php' );
}

/**
 * Abstract Class YITH_COG_Admin_Report
 */
abstract class YITH_COG_Admin_Report extends WC_Admin_Report {

    /**
     * @var stdClass|array for caching multiple calls to get_report_data()
     */
    protected $report_data;


    /**
     * Return false if fees should be excluded from net profit calculations
     * Note that taxes on fees are already included in the order tax amount.
     */
    public function exclude_fees() {

        return 'no' === get_option( 'yith_cog_settings_tab_fees' );
    }


    /**
     * Return false if taxes should be excluded from net profit calculations
     */
    public function exclude_taxes() {

        return 'no' === get_option( 'yith_cog_settings_tab_tax' );
    }


    /**
     * Return false if shipping should be excluded from net profit calculations
     */
    public function exclude_shipping() {

        return 'no' === get_option( 'yith_cog_settings_tab_shipping' );
    }


    /**
     * Helper to format an amount
     */
    protected function format_decimal( $amount ) {

        if ( is_array( $amount ) ) {
            return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
        } else {
            return wc_format_decimal( $amount, wc_get_price_decimals() );
        }
    }

}
