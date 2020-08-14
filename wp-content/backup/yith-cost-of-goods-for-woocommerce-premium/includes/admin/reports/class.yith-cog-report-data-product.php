<?php

defined( 'ABSPATH' ) or exit;



if ( ! class_exists( 'YITH_COG_Admin_Report' ) ) {
    require_once( YITH_COG_PATH . 'includes/admin/reports/abstract-yith-cog-admin-report.php' );
}

/**
 * @class      YITH_COG_Report_Data_Product
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
class YITH_COG_Report_Data_Product extends YITH_COG_Admin_Report {

    /** @var array product IDs for the report */
    public $product_ids;

    /**
     * Date variables
     */
    public $start_date;
    public $end_date;

    public $product_ids_titles = array();

    /**
     * Main Instance
     *
     * @var YITH_COG_Report_Data_Product
     * @since 1.0
     */
    protected static $_instance = null;


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
                    <input type="hidden" name="range" value="<?php if ( ! empty( $_GET['range'] ) ) echo esc_attr( $_GET['range'] ); ?>" />
                    <input type="hidden" name="start_date" value="<?php if ( ! empty( $_GET['start_date'] ) ) echo esc_attr( $_GET['start_date'] ); ?>" />
                    <input type="hidden" name="end_date" value="<?php if ( ! empty( $_GET['end_date'] ) ) echo esc_attr( $_GET['end_date'] ); ?>" />
                    <input type="hidden" name="page" value="<?php if ( ! empty( $_GET['page'] ) ) echo esc_attr( $_GET['page'] ); ?>" />
                    <input type="hidden" name="tab" value="<?php if ( ! empty( $_GET['tab'] ) ) echo esc_attr( $_GET['tab'] ); ?>" />
                    <input type="hidden" name="report" value="<?php if ( ! empty( $_GET['report'] ) ) echo esc_attr( $_GET['report'] ); ?>" />
                </div>
            </form>
        </div>
        <?php
    }


    /**
     * Return the currently selected date range for the report
     */
    protected function get_current_range() {

        return ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';
    }

    /**
     * Render the report data, including legend and chart
     */
    public function output_report() {

        $current_range = $this->get_current_range();

        if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ), true ) ) {
            $current_range = '7day';
        }

        $this->calculate_current_range( $current_range );

        // used in view
        $ranges = array(
            'year'         => esc_html__( 'Year', 'yith-cost-of-goods-for-woocommerce' ),
            'last_month'   => esc_html__( 'Last Month', 'yith-cost-of-goods-for-woocommerce' ),
            'month'        => esc_html__( 'This Month', 'yith-cost-of-goods-for-woocommerce' ),
            '7day'         => esc_html__( 'Last 7 Days', 'yith-cost-of-goods-for-woocommerce' )
        );

        include( YITH_COG_TEMPLATE_PATH . '/html/html-report-by-product.php');
    }

    /**
     * Render the report data, including legend and chart
     */
    public function output_report_secondary() {

        $current_range = $this->get_current_range();

        if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ), true ) ) {
            $current_range = '7day';
        }

        $this->calculate_current_range( $current_range );

        // used in view
        $ranges = array(
            'year'         => esc_html__( 'Year', 'yith-cost-of-goods-for-woocommerce' ),
            'last_month'   => esc_html__( 'Last Month', 'yith-cost-of-goods-for-woocommerce' ),
            'month'        => esc_html__( 'This Month', 'yith-cost-of-goods-for-woocommerce' ),
            '7day'         => esc_html__( 'Last 7 Days', 'yith-cost-of-goods-for-woocommerce' )
        );
    }

    /**
     * Render the export CSV button
     */
    public function output_export_button( $args = array() ) {

        $current_range = $this->get_current_range();
        ?>
        <a
                href="#"
                download="report-<?php echo 'YITH_COG_by_product_' . esc_attr( $current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time( 'timestamp' ) ); ?>.csv"
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


    /**
     * Get the data for the Report
     */
    public function get_report_data()
    {

        if ( !empty( $this->report_data ) ) {
            return $this->report_data;
        }

        if ( empty($this->product_ids ) ){
            return false;
        }

        $this->report_data = new stdClass();

        $order_status_option = get_option( 'yith_cog_order_status_report' );
        if ( $order_status_option == '0' ){
            $order_status = array( 'completed', 'refunded' );
        }
        else if ( $order_status_option == '1' ){
            $order_status = array( 'completed', 'processing', 'refunded' );
        }
        else{
            $order_status = array( 'completed', 'processing', 'on-hold', 'refunded' );
        }

        //Data Query
        $this->report_data->sales = $this->get_order_report_data(array(
            'data' => array(
                'ID' => array(
                    'type' => 'post_data',
                    'function' => 'DISTINCT',
                    'name' => 'order_id',
                ),
                'post_date' => array(
                    'type' => 'post_data',
                    'function' => '',
                    'name' => 'post_date',
                ),
                '_variation_id' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'variation_id',
                ),
                '_product_id' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'product_id',
                ),
                '_yith_cog_item_price' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'item_price',
                ),
                '_yith_cog_item_cost' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'item_cost',
                ),
                '_qty' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'item_count',
                ),
                'order_item_id' => array(
                    'type' => 'order_item',
                    'function' => '',
                    'name' => 'item_id',
                ),
            ),
            'where_meta' => array(
                'relation' => 'OR',
                array(
                    'type'       => 'order_item_meta',
                    'meta_key'   => array( '_product_id', '_variation_id' ),
                    'meta_value' => $this->product_ids,
                    'operator'   => 'IN',
                ),
            ),
            'query_type' => 'get_results',
            'filter_range' => true,
            'nocache' => true,
            'order_status'  => $order_status,
        ));

        $this->report_data->product_ids = wp_list_pluck($this->report_data->sales, 'product_id');
        $this->report_data->variation_ids = wp_list_pluck($this->report_data->sales, 'variation_id');
        $this->report_data->item_count = wp_list_pluck($this->report_data->sales, 'item_count');
        $this->report_data->item_id = wp_list_pluck($this->report_data->sales, 'item_id');
        $this->report_data->item_price = wp_list_pluck($this->report_data->sales, 'item_price');
        $this->report_data->item_cost = wp_list_pluck($this->report_data->sales, 'item_cost');
        $this->report_data->order_id = wp_list_pluck($this->report_data->sales, 'order_id');


        /* Taxes ****************************/
        $this->report_data->taxes = $this->get_order_report_data(array(
            'data' => array(
                'ID' => array(
                    'type' => 'post_data',
                    'function' => 'DISTINCT',
                    'name' => 'order_id',
                ),
                'order_item_id' => array(
                    'type' => 'order_item',
                    'function' => '',
                    'name' => 'item_id',
                ),
                'post_date' => array(
                    'type' => 'post_data',
                    'function' => '',
                    'name' => 'post_date',
                ),
                '_variation_id' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'variation_id',
                ),
                '_product_id' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'product_id',
                ),
                '_yith_cog_item_tax' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'item_tax',
                ),
            ),
            'query_type' => 'get_results',
            'filter_range' => true,
            'nocache' => true,
            'order_status'  => $order_status,
        ));

        $this->report_data->item_tax = wp_list_pluck($this->report_data->taxes, 'item_tax');


        //apply cost if no set or overriding
        $this->report_data->set_cost = $this->get_order_report_data(array(
            'data' => array(
                'ID' => array(
                    'type' => 'post_data',
                    'function' => 'DISTINCT',
                    'name' => 'order_id',
                ),
                'order_item_id' => array(
                    'type' => 'order_item',
                    'function' => '',
                    'name' => 'item_id',
                ),
                '_product_id' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'product_id',
                ),
                '_variation_id' => array(
                    'type' => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function' => '',
                    'name' => 'variation_id',
                ),
            ),
            'query_type' => 'get_results',
            'nocache' => true,
            'order_status'  => array( 'completed', 'processing', 'on-hold'),
        ));

        $this->report_data->item_id_ = wp_list_pluck($this->report_data->set_cost, 'item_id');
        $this->report_data->product_ids_ = wp_list_pluck($this->report_data->set_cost, 'product_id');
        $this->report_data->variation_ids_ = wp_list_pluck($this->report_data->set_cost, 'variation_id');
        $this->report_data->order_id_ = wp_list_pluck($this->report_data->set_cost, 'order_id');


        /*Operations for the totals table *************************/
        $item_qty_array = $this->report_data->item_count;
        $item_taxes_array = $this->report_data->item_tax;
        $item_price_array = $this->report_data->item_price;
        $item_cost_array = $this->report_data->item_cost;
        $order_id_array = $this->report_data->order_id;
        $order_id_array_unique = array_unique($order_id_array);


        $total_shipping = 0;
        foreach ( $order_id_array_unique as $order_id ){
            $order = wc_get_order( $order_id );
            $order_shipping = $order->get_shipping_total();
            $total_shipping += $order_shipping;
        }

        foreach ( $order_id_array_unique as $order_id ){
            $order = wc_get_order( $order_id );
            $order_fees = $order->get_fees();
        }

        if (! empty($order_fees)){
            $total_fees = array_sum($order_fees);
        }
        else{
            $total_fees = 0;
        }

        $this->report_data->report_total_sales = array_sum( $item_qty_array );
        $this->report_data->report_total_taxes = array_sum( $item_taxes_array);


        foreach ( $item_qty_array as $key => $value ){
            if ($item_qty_array[$key] < 0 ){
                $cost_value =  $item_cost_array[$key];
                $price_value =  $item_price_array[$key];
                $item_cost_array[$key] = -(1) * (float)$cost_value;
                $item_price_array[$key] = -(1) * $price_value;
            }
            else{
                $qty_value = $item_qty_array[$key];
                $cost_value =  $item_cost_array[$key];
                $price_value =  $item_price_array[$key];
                $item_cost_array[$key] = $qty_value * (float)$cost_value;
                $item_price_array[$key] = $price_value;
            }
        }
        
        foreach ($item_price_array as $key => $value){

            $item_id = $this->report_data->item_id[$key];
            $item_price_array[$key] = $item_qty_array[$key] * apply_filters( 'yith_cog_convert_amounts', $value, $item_id );
        }

        $this->report_data->report_total_prices = array_sum( $item_price_array );
        $this->report_data->report_total_cost = array_sum( $item_cost_array);


        if ( ! $this->exclude_shipping() ){
            $this->report_data->report_total_cost += $total_shipping;
        }
        if ( ! $this->exclude_taxes() ){
            $this->report_data->report_total_cost += $this->report_data->report_total_taxes;
        }
        if ( ! $this->exclude_fees() ){
            $this->report_data->report_total_cost += $total_fees;
        }

        $this->report_data->report_total_profit = $this->report_data->report_total_prices - $this->report_data->report_total_cost;


        return apply_filters( 'yith_cog_report_data', $this->report_data, $this->report_data->product_ids, $this );
    }

}
