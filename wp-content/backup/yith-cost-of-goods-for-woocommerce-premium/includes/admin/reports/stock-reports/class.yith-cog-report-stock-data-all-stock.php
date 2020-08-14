<?php

defined( 'ABSPATH' ) or exit;



if ( ! class_exists( 'YITH_COG_Admin_Report' ) ) {
    require_once( YITH_COG_PATH . 'includes/admin/reports/abstract-yith-cog-admin-report.php' );
}

/**
 * @class      YITH_COG_Report_Stock_Data
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
class YITH_COG_Report_Stock_Data_All_Stock extends YITH_COG_Admin_Report {


    /**
     * Main Instance
     *
     * @var YITH_COG_Report_Stock_Data_All_Stock
     */
    protected static $_instance = null;


    /**
     * Construct
     *
     */
    public function __construct() {}


    /**
     * Output the report
     *
     */
    public function output_report() {

        include( YITH_COG_TEMPLATE_PATH . '/html/html-report-all-stock.php');
    }


    /**
     * Render the export CSV button
     */
    public function output_export_button( $args = array() ) {

        ?>
        <a
            href="#"
            download="report-<?php echo 'YITH_COG_Stock_' . esc_attr( date('Y-m-d') ); ?>.csv"
            class="yith_export_csv export_csv"
            data-export="table"
        >
            <?php _e( 'Export CSV', 'yith-cost-of-goods-for-woocommerce' ); ?>
        </a>
        <?php
    }


    /**
     * Render the "Export to CSV" button
     */
    public function get_export_button() {
        $this->output_export_button();
    }



}
