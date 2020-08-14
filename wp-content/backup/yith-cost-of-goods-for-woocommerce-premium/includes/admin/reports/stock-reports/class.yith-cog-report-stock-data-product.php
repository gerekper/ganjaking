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
class YITH_COG_Report_Stock_Data_Product extends YITH_COG_Admin_Report {


    /**
     * Main Instance
     *
     * @var YITH_COG_Report_Data
     * @since 1.0
     */
    protected static $_instance = null;

    public $product_ids_titles = array();
    public $product_ids;


    /**
     * Construct
     *
     * @since 1.0
     */
    public function __construct() {

        $this->set_product_ids();

    }


    /**
     * Set the product IDs for the report
     */
    protected function set_product_ids() {

        // get the products selected for the report
        $this->product_ids = isset( $_GET['product_ids'] ) ? array_filter( array_map( 'absint', (array) $_GET['product_ids'] ) ) : array();
    }


    /**
     * Get the widgets for this report
     */
    public function get_chart_widgets() {

        $widgets = array();

        if ( ! empty( $this->product_ids ) ) {
            $widgets[] = array(
                'title'    => esc_html__( 'Showing reports for:', 'yith-cost-of-goods-for-woocommerce' ),
                'callback' => array( $this, 'current_filters' ),
            );
        }

        $widgets[] = array(
            'title'    => esc_html__( 'Product Search', 'yith-cost-of-goods-for-woocommerce' ),
            'callback' => array( $this, 'output_product_search_widget' ),
        );

        return $widgets;
    }


    /**
     * Output current filters.
     */
    public function current_filters() {

        $this->product_ids_titles = array();

        foreach ( $this->product_ids as $product_id ) {

            $product = wc_get_product( $product_id );

            if ( $product ) {
                $this->product_ids_titles[] = $product->get_formatted_name();
            } else {
                $this->product_ids_titles[] = '#' . $product_id;
            }
        }

        echo '<p>' . ' <strong>' . esc_html( implode( ', ', $this->product_ids_titles ) ) . '</strong></p>';
        echo '<p><a class="button" href="' . esc_url( remove_query_arg( 'product_ids' ) ) . '">' . esc_html__( 'Reset', 'yith-cost-of-goods-for-woocommerce' ) . '</a></p>';
    }


    /**
     * Show current product filters for the report
     */
    public function output_current_filters_widget() {

        $product_titles = array();

        foreach ( $this->product_ids as $product_id ) {

            $product = wc_get_product( $product_id );

            $product_titles[] = $product instanceof WC_Product ? $product->get_formatted_name() : '#' . $product_id;
        }

        printf( '<p><strong>%1$s</strong></p><p><a class="button" href="%2$s">%3$s</a></p>', implode( ', ', $product_titles ), esc_url( remove_query_arg( 'product_ids' ) ), esc_html__( 'Reset', 'yith-cost-of-goods-for-woocommerce' ) );
    }


    /**
     * Show the product search widget
     */
    public function output_product_search_widget() {


        ?>
        <div class="section">
            <form method="GET">
                <div>
                    <select
                            name="product_ids[]"
                            id="product_ids"
                            class="wc-product-search"
                            style="width:203px;"
                            data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'yith-cost-of-goods-for-woocommerce' ); ?>"
                            data-action="woocommerce_json_search_products_and_variations"
                            multiple ="multiple" >
                    </select>

                    <input type="submit" class="submit button" value="<?php esc_attr_e( 'Show', 'yith-cost-of-goods-for-woocommerce' ); ?>" />
                    <input type="hidden" name="page" value="<?php if ( ! empty( $_GET['page'] ) ) echo esc_attr( $_GET['page'] ); ?>" />
                    <input type="hidden" name="tab" value="<?php if ( ! empty( $_GET['tab'] ) ) echo esc_attr( $_GET['tab'] ); ?>" />
                    <input type="hidden" name="report" value="<?php if ( ! empty( $_GET['report'] ) ) echo esc_attr( $_GET['report'] ); ?>" />
                </div>
            </form>
        </div>


        <?php
    }


    /**
     * Render the report data, including legend and chart
     */
    public function output_report() {

        include( YITH_COG_TEMPLATE_PATH . '/html/html-report-stock.php');
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
