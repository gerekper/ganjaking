<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


/**
 * @class      YITH_COG_Report_Totals
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
class YITH_COG_Report_Totals extends WP_List_Table {

    /**
     * Construct
     *
     * @since 1.0
     */
    public function __construct() {

        parent::__construct();
    }


    /**
     * No items found text.
     */
    public function no_items() {
        _e( 'No products found.', 'yith-cost-of-goods-for-woocommerce' );
    }


    /**
     * Display the table
     */
    public function display() {

        $singular = $this->_args['singular'];

        $this->display_tablenav( 'top' );

        $this->screen->render_screen_reader_content( 'heading_list' );
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
            <thead>
            <tr>
                <?php $this->print_column_headers(); ?>
            </tr>
            </thead>

            <tbody id="the-list"<?php
            if ( $singular ) {
                echo " data-wp-lists='list:$singular'";
            } ?>>
            <?php $this->display_rows_or_placeholder(); ?>
            </tbody>
        </table>
        <?php
    }


    /**
     * Output the report.
     */
    public function output_report() {

        $this->prepare_items();
        $this->display();
    }


    /**
     * Get column values.
     */
    public function column_default( $item, $column_name ) {


        switch ( $column_name ) {

            case 'report_total_sales':

                ?><div ><p style="font-weight: bold;"><?php echo $item['total_sales'] ?></p></div><?php

                break;

            case 'report_total_price' :

                if (  get_option('yith_cog_currency_report') == 'no' ) {
                    ?><div ><p style="font-weight: bold;"><?php echo round( $item['total_prices'], 2 ) ?></p></div><?php
                }
                else{
                    ?><div ><p style="font-weight: bold;"><?php echo wc_price( $item['total_prices'] ) ?></p></div><?php
                }

                break;

            case 'report_total_cost' :

                if (  get_option('yith_cog_currency_report') == 'no' ) {
                    ?><div ><p style="font-weight: bold;"><?php echo round( $item['total_cost'], 2 ) ?></p></div><?php
                }
                else{
                    ?><div ><p style="font-weight: bold;"><?php echo wc_price( $item['total_cost'] ) ?></p></div><?php
                }

                break;

            case 'report_total_profit' :

                if (  get_option('yith_cog_currency_report') == 'no' ) {
                    ?><div ><p style="font-weight: bold;"><?php echo round( $item['total_profit'], 2 ) ?></p></div><?php
                }
                else{
                    ?><div ><p style="font-weight: bold;"><?php echo wc_price( $item['total_profit'] ) ?></p></div><?php
                }

                break;

            case 'margin_percentage_totals' :

                if ( $item['total_prices'] == 0 ){
                    $percentage = 0;
                }
                else{
                    $percentage = round(( $item['total_profit'] / $item['total_prices'] ) * 100, 2);
                }

                ?><div ><p style="font-weight: bold;"><?php echo $percentage . '%' ?></p></div><?php

                break;
        }
    }


    /**
     * Get columns.
     */
    public function get_columns() {

        $columns = array(
            '_void'                 => '',
            'report_total_sales'    => apply_filters( 'yith_cog_report_table_totals_header_total_sales',  __( 'Total Quantity' , 'yith-cost-of-goods-for-woocommerce' ) ),
            '_void2'                => '',
            'report_total_price'    => apply_filters( 'yith_cog_report_table_totals_header_total_prices', __( 'Total Price' , 'yith-cost-of-goods-for-woocommerce' ) ),
            '_void3'                => '',
            'report_total_cost'     => apply_filters( 'yith_cog_report_table_totals_header_total_cost',   __( 'Total Cost'  , 'yith-cost-of-goods-for-woocommerce' ) ),
            '_void4'                => '',
            'report_total_profit'   => apply_filters( 'yith_cog_report_table_totals_header_total_profit', __( 'Total Margin', 'yith-cost-of-goods-for-woocommerce' ) ),
        );

        $columns = apply_filters( 'yith_add_custom_columns_void', $columns );

        $columns['_void_actions'] = '';

        return $columns;
    }


    /**
     * Get items from Query.
     */
    public function get_items( $current_page, $per_page )
    {
        $this->items = array();

        if ( isset( $_GET['report'] ) ){
            $report_name = $_GET['report'];
        }
        else{
            $report_name = 'sales_by_date';
        }

        if ( $report_name == 'sales_by_date' ) {

            $report = new YITH_COG_Report_Data();
            $report->output_report_secondary();
            $data = $report->get_report_data();

            $total_sales = $data->report_total_sales;
            $total_prices = $data->report_total_prices;
            $total_cost = $data->report_total_cost;
            $total_profit = $data->report_total_profit;

            $totals_array = array();

            $totals_array[0]['total_sales'] = $total_sales;
            $totals_array[0]['total_prices'] = $total_prices;
            $totals_array[0]['total_cost'] = $total_cost;
            $totals_array[0]['total_profit'] = $total_profit;

            $this->items = $totals_array;

            $this->filter_by_tag();
        }

        if ( $report_name == 'sales_by_product' ) {

            $report_prod = new YITH_COG_Report_Data_Product();
            $report_prod->output_report_secondary();
            $data = $report_prod->get_report_data();
            $prod_ids = $report_prod->product_ids;

            if ( empty($prod_ids)){
                return;
            }

            $total_sales = $data->report_total_sales;
            $total_prices = $data->report_total_prices;
            $total_cost = $data->report_total_cost;
            $total_profit = $data->report_total_profit;

            $totals_array = array();

            $totals_array[0]['total_sales'] = $total_sales;
            $totals_array[0]['total_prices'] = $total_prices;
            $totals_array[0]['total_cost'] = $total_cost;
            $totals_array[0]['total_profit'] = $total_profit;

            $this->items = $totals_array;

            $this->filter_by_tag();

        }

        if ( $report_name == 'sales_by_category' ) {

            $report_cat = new YITH_COG_Report_Data_Category();
            $report_cat->output_report_secondary();
            $data = $report_cat->get_report_data();
            $cat_ids = $report_cat->category_ids;

            if ( empty($cat_ids)){
                return;
            }

            $total_sales = $data->report_total_sales;
            $total_prices = $data->report_total_prices;
            $total_cost = $data->report_total_cost;
            $total_profit = $data->report_total_profit;

            $totals_array = array();

            $totals_array[0]['total_sales'] = $total_sales;
            $totals_array[0]['total_prices'] = $total_prices;
            $totals_array[0]['total_cost'] = $total_cost;
            $totals_array[0]['total_profit'] = $total_profit;

            $this->items = $totals_array;

            $this->filter_by_tag();

        }

    }


    /**
     * Filter the report by Tag
     */
    public function filter_by_tag(){


        $report_tag = new YITH_COG_Report_Data_Tag();

        if ( isset($_GET['product_tag'] ) ) {
            $report_tag->output_report();
            $data = $report_tag->get_report_data();

            $tag_id = $report_tag->tag_id;

            if ( empty($tag_id)){
                return;
            }

            $total_sales = $data->report_total_sales;
            $total_prices = $data->report_total_prices;
            $total_cost = $data->report_total_cost;
            $total_profit = $data->report_total_profit;

            $totals_array = array();

            $totals_array[0]['total_sales'] = $total_sales;
            $totals_array[0]['total_prices'] = $total_prices;
            $totals_array[0]['total_cost'] = $total_cost;
            $totals_array[0]['total_profit'] = $total_profit;

            $this->items = $totals_array;

        }

    }


    /**
     * Prepare list items.
     */
    public function prepare_items() {

        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
        $per_page = 1;
        $current_page = absint( $this->get_pagenum() );

        $this->get_items( $current_page, $per_page );

    }

}
