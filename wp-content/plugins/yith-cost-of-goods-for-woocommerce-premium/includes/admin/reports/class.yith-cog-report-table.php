<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


/**
 * @class      YITH_COG_Report
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
class YITH_COG_Report extends WP_List_Table {

    /**
     * Max items.
     *
     * @var int
     */
    protected $max_items;

    /**
     * Array with the products ids.
     */
    protected $product_ids_array = array();

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
     * Table position
     */
    public function display_tablenav( $position ) {

        if ( 'top' !== $position ) {
            parent::display_tablenav( $position );
        }
    }

    /**
     * Display the table
     */
    public function display() {

        if ( isset( $_GET['report'] ) ){
            $report_name = $_GET['report'];
        }
        else{
            $report_name = 'sales_by_date';
        }

        if ( $report_name == 'sales_by_date' )
            $report = new YITH_COG_Report_Data();
        if ( $report_name == 'sales_by_product' )
            $report = new YITH_COG_Report_Data_Product();
        if ( $report_name == 'sales_by_category' )
            $report = new YITH_COG_Report_Data_Category();

        $report->output_report_secondary();
        $data = $report->get_report_data();

        $total_sales = is_object($data) ? $data->report_total_sales : 0;
        $total_prices = is_object($data) ? $data->report_total_prices : 0;
        $total_cost = is_object($data) ? $data->report_total_cost : 0;
        $total_profit = is_object($data) ? $data->report_total_profit : 0;
        $margin_percentage = $total_prices != 0 ? round(( $total_profit / $total_prices ) * 100, 2) : 0;


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


           <tfoot>
				<tr>
					<th style="font-weight: bold" scope="total_row"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
					<th style="font-weight: bold" class="total_row" colspan="2"><?php echo $total_sales ;?></th>
					<th style="font-weight: bold" class="total_row" colspan="2"><?php echo wc_price($total_prices);?></th>
					<th style="font-weight: bold" class="total_row" colspan="2"><?php echo wc_price($total_cost);?></th>
					<th style="font-weight: bold" class="total_row" colspan="2"><?php echo wc_price($total_profit);?></th>

					<?php if ('yes' === get_option( 'yith_cog_percentage_column' )){ ?>
					    <th style="font-weight: bold" class="total_row" colspan="2"><?php echo $margin_percentage . '%';?></th>
					<?php } ?>

				</tr>
			</tfoot>
        </table>
        <?php
    }

    /**
     * Output the report.
     */
    public function output_report() {

        $this->prepare_items();
        echo '<div id="table-content" style="float: right;">';
        $this->display();
        $this->display_tablenav('bottom');
        echo '</div>';
    }

    /**
     * Get column values.
     */
    public function column_default( $item, $column_name ) {

        global $product;

        if ( ! $product || $product->get_id() !== $item['prod_id']) {
            $product = wc_get_product( $item['prod_id'] );
        }

        if ( ! $item['prod_id']  ){
            return;
        }

        if ( ! $product ) {
            $item_id_array = $item['item_id'];

            foreach ($item_id_array as $item_id) {
                $product_type = wc_get_order_item_meta( $item_id, '_yith_cog_item_product_type', true );
            }
        }
        else{
            $product_type = $product->get_type();
        }

          if ( $product_type == 'gift-card' ) {
            return;
        }

        //Columns content in the Report
        switch ( $column_name ) {

             case 'product' :

                if ( $product_type == 'variable' ) {

                    $item_id_array = $item['item_id'];

                    foreach ($item_id_array as $item_id) {
                        $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                        if ( $refund_id <= 0) {
                            $product_name = $item['item_name'];
                        }
                    }
                    if ( isset($product_name) && $product ){
                        $url = $product->get_permalink();
                    ?><a href="<?php echo $url ?>"><?php echo $product->get_name() . ' ' ?></a><span class="dashicons dashicons-arrow-down desplegable"></span><?php
                    ?><br><p></p><?php
                    }
                    else{
                        ?><p><?php echo $product_name . ' ' ?></p><span class="dashicons dashicons-arrow-down desplegable"></span><?php
                    ?><br><p></p><?php
                    }

                    if ( isset( $item['var_id'] ) && is_array( $item['var_id'] ) ){
                        $variation_id_array = array_unique($item['var_id']);
                    }
                    else{
                        $variation_id_array = array();
                    }

                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {

                                $variation_name = wc_get_order_item_meta($item_id, '_yith_cog_item_name_sortable', true) ;
                            }
                        }
                        if (isset($variation_name) ) {
                            ?><div class="childs" style="display: none"> <?php echo $variation_name; ?></div><?php
                        }
                    }
                }
                else{
                    $item_id_array = $item['item_id'];

                    foreach ($item_id_array as $item_id) {
                        $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                        if ( $refund_id <= 0) {
                            $product_name = $item['item_name'];
                        }
                    }
                    if (isset($product_name) && $product ){

                        $url = $product->get_permalink();
                        ?><a href="<?php echo $url ?>"><?php echo $product_name . ' ' ?><?php
                    }
                    else{
                        echo $product_name . ' ';
                    }
                }

                break;


            case 'total_sales':

                if ( $product_type == 'variable' ) {

                    if ( isset( $item['var_id'] ) && is_array( $item['var_id'] ) ){
                        $variation_id_array = array_unique($item['var_id']);
                    }
                    else{
                        $variation_id_array = array();
                    }                    //total sales
                    $total_quantity = 0;
                    foreach ($variation_id_array as $var_id) {
                        //variation sales
                        $quantity = $item['var_qty'][$var_id];
                        $total_quantity += $quantity;
                    }
                    ?><div ><p><?php echo $total_quantity ?></p></div><?php

                    if ($total_quantity > 0 ) {
                        foreach ($variation_id_array as $var_id) {
                            //variation sales
                            $quantity = $item['var_qty'][$var_id];
                            ?><div class="childs" style="display: none"> <?php echo $quantity ?></div><?php
                        }
                    }
                }
                else{
                    $item_id_array = $item['item_id'];

                    $total_qty = 0;
                    foreach ($item_id_array as $item_id) {
                        $item_qty = $item['item_qty'][$item_id];
                        $total_qty += $item_qty;
                    }
                    if ( isset($total_qty) ) {
                        ?><div><p><?php echo $total_qty ?></p></div><?php
                    }
                }

                break;


            case 'product_price' :

                if ( $product_type == 'variable' ) {

                    if ( isset( $item['var_id'] ) && is_array( $item['var_id'] ) ){
                        $variation_id_array = array_unique($item['var_id']);
                    }
                    else{
                        $variation_id_array = array();
                    }                    $min_max_array = array();


                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_price = apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id ) ;
                                $min_max_array[] = $variation_price;
                            }
                        }
                    }

                    if (isset($variation_price)) {
                        if (min($min_max_array) == max($min_max_array)){
                            if (  get_option('yith_cog_currency_report') == 'no' ){
                               ?><div ><p><?php echo round( min($min_max_array), 2 ) ?></p></div><?php
                            }
                            else{
                                ?><div ><p><?php echo wc_price(min($min_max_array)) ?></p></div><?php
                            }
                        }
                        else{
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div ><p><?php echo round(min($min_max_array), 2 ) . ' – ' . round( max($min_max_array), 2 ) ?></p></div><?php
                            }
                            else{
                                ?><div ><p><?php echo wc_price(min($min_max_array)) . ' – ' . wc_price(max($min_max_array)) ?></p></div><?php

                            }
                        }
                    }

                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];

                        $variation_price_cnt = 0;
                        $variation_price_sum = 0;

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_price_cnt++;
                                $variation_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id );
                            }
                        }

                        if ( isset($variation_price_sum) &&  isset($variation_price_cnt) && $variation_price_cnt != '0' ){
                            $variation_price_avg = $variation_price_sum / $variation_price_cnt;
                        }

                        if (isset($variation_price_avg) ) {
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div class="childs" style="display: none"> <?php echo round( $variation_price_avg, 2 ) . ' (p/u)' ?></div><?php
                            }
                            else{
                                ?><div class="childs" style="display: none"> <?php echo wc_price($variation_price_avg) . ' (p/u)' ?></div><?php
                            }
                        }
                    }
                }
                else{
                    $item_id_array = $item['item_id'];

                    $product_price_sum = 0;
                    $product_price_cnt = 0;

                    foreach ($item_id_array as $item_id) {
                        $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                        if ( $refund_id <= 0) {
                            $product_price_cnt++;
                            $product_price_sum +=  apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$item_id], $item_id );
                        }
                    }

                     if ( isset($product_price_sum) &&  isset($product_price_cnt) && $product_price_cnt != '0'){
                         $product_price_avg = $product_price_sum / $product_price_cnt;
                     }

                    if (isset($product_price_avg)){
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            echo round( $product_price_avg, 2 );
                        }
                        else{
                            echo wc_price( $product_price_avg );
                        }
                    }
                }

                break;


            case 'product_total_price' :

                if ( $product_type == 'variable' ) {

                    if ( isset( $item['var_id'] ) && is_array( $item['var_id'] ) ){
                        $variation_id_array = array_unique($item['var_id']);
                    }
                    else{
                        $variation_id_array = array();
                    }
                    $total_price = 0;
                    foreach ($variation_id_array as $var_id) {
                        $item_id_array = $item['item_id'][$var_id];
                        $quantity = $item['var_qty'][$var_id];

                        $variation_price_sum = 0;
                        $variation_price_cnt = 0;

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_price_cnt++;
                                $variation_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id ) ;
                            }
                        }

                        if ( isset($variation_price_sum) &&  isset($variation_price_cnt) && $variation_price_cnt != '0'){
                            $variation_price_avg = $variation_price_sum / $variation_price_cnt;
                            $variation_total_price = $quantity * $variation_price_avg;
                        }

                        if (isset($variation_total_price)){
                            $total_price += $variation_total_price;
                        }
                    }
                    //total prices
                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        ?><div><p><?php echo round( $total_price, 2 ) ?></p></div><?php
                    }
                    else{
                        ?><div><p><?php echo wc_price($total_price) ?></p></div><?php
                    }

                    foreach ($variation_id_array as $var_id) {
                        $item_id_array = $item['item_id'][$var_id];
                        $quantity = $item['var_qty'][$var_id];

                        $variation_price_sum = 0;
                        $variation_price_cnt = 0;

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_price_cnt++;
                                $variation_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id ) ;
                            }
                        }

                        if ( isset($variation_price_sum) &&  isset($variation_price_cnt) && $variation_price_cnt != '0'){
                            $variation_price_avg = $variation_price_sum / $variation_price_cnt;
                            $variation_total_price = $quantity * $variation_price_avg;
                        }

                        //variation total price
                        if (isset($variation_total_price)) {
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div class="childs" style="display: none"> <?php echo round( $variation_total_price, 2 ) ?></div><?php
                            }
                            else{
                                ?><div class="childs" style="display: none"> <?php echo wc_price($variation_total_price) ?></div><?php
                            }
                        }
                    }
                }
                else {
                    $item_id_array = $item['item_id'];

                    $product_price_sum = 0;
                    $product_price_cnt = 0;

                    foreach ($item_id_array as $item_id) {
                        $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                        if ( $refund_id <= 0) {
                            $product_price_cnt++;
                            $product_price_sum +=  apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$item_id], $item_id );
                        }
                    }
                    if ( isset($product_price_sum) &&  isset($product_price_cnt)){
                         $product_price_avg = $product_price_sum / $product_price_cnt;
                         $total_price = $product_price_cnt * $product_price_avg;
                     }

                    if ( isset($total_price) ) {
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            ?><div><p><?php echo round( $total_price, 2 ) ?></p></div><?php
                        }
                        else{
                            ?><div><p><?php echo wc_price($total_price) ?></p></div><?php
                        }
                    }
                }

                break;


            case 'product_cost' :

                if ( $product_type == 'variable' ) {

                    if ( isset( $item['var_id'] ) && is_array( $item['var_id'] ) ){
                        $variation_id_array = array_unique($item['var_id']);
                    }
                    else{
                        $variation_id_array = array();
                    }                    $min_max_array = array();

                    foreach ($variation_id_array as $var_id) {
                        $item_id_array = $item['item_id'][$var_id];

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ($refund_id <= 0) {
                                $variation_cost = $item['item_cost'][$var_id][$item_id];
                                $min_max_array[] = $variation_cost;
                            }
                        }
                    }
                    if (isset($variation_cost)) {
                        if (min($min_max_array) == max($min_max_array)){
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div ><p><?php echo round(min($min_max_array), 2 ) ?></p></div><?php
                            }
                            else{
                                ?><div ><p><?php echo wc_price(min($min_max_array)) ?></p></div><?php
                            }

                        }
                        else{
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div ><p><?php echo round( min($min_max_array), 2 ) . ' – ' . round( max($min_max_array), 2 ) ?></p></div><?php
                            }
                            else{
                                ?><div ><p><?php echo wc_price(min($min_max_array)) . ' – ' . wc_price(max($min_max_array)) ?></p></div><?php
                            }
                        }
                    }

                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_cost = $item['item_cost'][$var_id][$item_id];
                            }
                        }
                        if (isset($variation_cost) ) {
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div class="childs" style="display: none"> <?php echo round($variation_cost, 2 ) . ' (p/u)' ?></div><?php
                            }
                            else{
                                ?><div class="childs" style="display: none"> <?php echo wc_price($variation_cost) . ' (p/u)' ?></div><?php
                            }
                        }
                    }
                }
                else{
                    $item_id_array = $item['item_id'];

                    foreach ($item_id_array as $item_id) {
                        $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                        if ( $refund_id <= 0) {
                            $product_cost = (float)$item['item_cost'][$item_id];
                        }
                    }
                    if (isset($product_cost)) {
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            echo round( $product_cost, 2 );
                        }
                        else{
                            echo wc_price($product_cost);
                        }
                    }
                }

                break;


            case 'product_total_cost' :

                if ( $product_type == 'variable' ) {

                    if ( isset( $item['var_id'] ) && is_array( $item['var_id'] ) ){
                        $variation_id_array = array_unique($item['var_id']);
                    }
                    else{
                        $variation_id_array = array();
                    }
                    $total_cost = 0;
                    foreach ($variation_id_array as $var_id) {
                        $item_id_array = $item['item_id'][$var_id];
                        $quantity = $item['var_qty'][$var_id];

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ($refund_id <= 0) {
                                $variation_cost = $item['item_cost'][$var_id][$item_id];
                                $variation_total_cost = $quantity * $variation_cost;
                            }
                        }
                        if (isset($variation_total_cost)) {
                            $total_cost += $variation_total_cost;
                        }
                    }
                    if ( isset($total_cost) ){
                        //total cost
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            ?><div ><p><?php echo round( $total_cost, 2 ) ?></p></div><?php
                        }
                        else{
                            ?><div ><p><?php echo wc_price($total_cost) ?></p></div><?php
                        }

                    }
                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];
                        $quantity = $item['var_qty'][$var_id];

                        $total_cost = 0;
                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_cost = (float)$item['item_cost'][$var_id][$item_id] ;
                                $total_cost = $quantity * $variation_cost;
                            }
                        }
                        if ( isset($total_cost) ) {
                            //variation total cost
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div class="childs" style="display: none"> <?php echo round( $total_cost, 2 ) ?></div><?php
                            }
                            else{
                                ?><div class="childs" style="display: none"> <?php echo wc_price($total_cost) ?></div><?php
                            }

                        }
                    }
                }
                else{
                    $item_id_array = $item['item_id'];

                    $total_cost = 0;
                    foreach ($item_id_array as $item_id) {
                        $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                        if ( $refund_id <= 0) {
                            $product_cost = (float)$item['item_cost'][$item_id];
                        }
                        if (isset($product_cost)) {
                            $item_qty = $item['item_qty'][$item_id];
                            $total_cost += $item_qty * (float)$product_cost;
                        }
                    }
                    if ( isset($total_cost) ) {
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            ?><div ><p><?php echo round( $total_cost, 2 ) ?></p></div><?php
                        }
                        else{
                            ?><div ><p><?php echo wc_price($total_cost) ?></p></div><?php
                        }
                    }
                }

                break;


            case 'product_profit' :

                if ( $product_type == 'variable' ) {

                    if ( isset( $item['var_id'] ) && is_array( $item['var_id'] ) ){
                        $variation_id_array = array_unique($item['var_id']);
                    }
                    else{
                        $variation_id_array = array();
                    }
                    $min_max_array = array();

                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];

                        $variation_price_sum = 0;
                        $variation_price_cnt = 0;

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_cost = $item['item_cost'][$var_id][$item_id] ;
                                $variation_price_cnt++;
                                $variation_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id ) ;
                            }
                            if ( isset($variation_price_sum) && isset($variation_price_cnt) && isset($variation_cost) && $variation_price_cnt != '0'){
                                $variation_price_avg = $variation_price_sum / $variation_price_cnt;
                                $variation_profit = $variation_price_avg - $variation_cost;
                                $min_max_array[] = $variation_profit;
                            }
                        }
                    }
                    if (isset($min_max_array)) {
                        if (min($min_max_array) == max($min_max_array)){
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div ><p><?php echo round( min($min_max_array), 2 ) ?></p></div><?php
                            }
                            else{
                                ?><div ><p><?php echo wc_price(min($min_max_array)) ?></p></div><?php
                            }

                        }
                        else{
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div ><p><?php echo round( min($min_max_array), 2 ) . ' – ' . round( max($min_max_array), 2 ) ?></p></div><?php
                            }
                            else{
                               ?><div ><p><?php echo wc_price(min($min_max_array)) . ' – ' . wc_price(max($min_max_array)) ?></p></div><?php
                            }

                        }
                    }

                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];

                        $variation_price_sum = 0;
                        $variation_price_cnt = 0;

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_cost = $item['item_cost'][$var_id][$item_id] ;
                                $variation_price_cnt++;
                                $variation_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id ) ;
                            }
                            if ( isset($variation_price_sum) && isset($variation_price_cnt) && isset($variation_cost) && $variation_price_cnt != '0'){
                                $variation_price_avg = $variation_price_sum / $variation_price_cnt;
                                $variation_profit = $variation_price_avg - $variation_cost ;
                            }
                        }

                        if (isset($variation_profit)) {
                             if (  get_option('yith_cog_currency_report') == 'no' ) {
                                 ?><div class="childs" style="display: none"> <?php echo round( $variation_profit, 2 ) . ' (p/u)' ?></div><?php
                             }
                             else{
                                 ?><div class="childs" style="display: none"> <?php echo wc_price($variation_profit) . ' (p/u)' ?></div><?php
                             }

                        }
                    }
                }
                else{
                    $item_id_array = $item['item_id'];

                    $product_price_sum = 0;
                    $product_price_cnt = 0;

                    foreach ($item_id_array as $item_id) {
                        $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                        if ( $refund_id <= 0) {
                            $product_cost = (float)$item['item_cost'][$item_id];
                            $product_price_cnt++;
                            $product_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$item_id], $item_id );
                        }
                        if ( isset($product_price_sum) &&  isset($product_price_cnt) && $product_price_cnt != '0'){
                         $product_price_avg = $product_price_sum / $product_price_cnt;
                        }
                        if (isset($product_price_avg) && isset($product_cost)){
                            $product_profit = $product_price_avg - (float)$product_cost;
                        }
                    }


                    if (isset($product_profit)){
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            echo round( $product_profit, 2 );
                        }
                        else{
                            echo wc_price($product_profit);
                        }
                    }
                }
                break;


            case 'product_total_profit' :

                if ( $product_type == 'variable' ) {

                    if ( isset( $item['var_id'] ) && is_array( $item['var_id'] ) ){
                        $variation_id_array = array_unique($item['var_id']);
                    }
                    else{
                        $variation_id_array = array();
                    }
                    $total_profit = 0;
                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];
                        $quantity = $item['var_qty'][$var_id];

                        $variation_price_sum = 0;
                        $variation_price_cnt = 0;

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_cost = $item['item_cost'][$var_id][$item_id] ;
                                $variation_price_cnt++;
                                $variation_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id ) ;
                            }
                            if ( isset($variation_price_sum) && isset($variation_price_cnt) && isset($variation_cost) && $variation_price_cnt != '0'){
                                $variation_price_avg = $variation_price_sum / $variation_price_cnt;
                                $variation_profit = $variation_price_avg - $variation_cost;
                                $variation_total_profit = $quantity * $variation_profit;
                            }
                        }
                        if (isset($variation_total_profit)){
                            $total_profit += $variation_total_profit;
                        }
                    }
                    if ( isset($total_profit) ) {
                        //total profit
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            ?><div><p><?php echo round( $total_profit, 2 ) ?></p></div><?php
                        }
                        else{
                            ?><div><p><?php echo wc_price($total_profit) ?></p></div><?php
                        }
                    }
                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];
                        $quantity = $item['var_qty'][$var_id];

                        $variation_price_sum = 0;
                        $variation_price_cnt = 0;

                        $variation_total_profit = 0;
                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_cost = $item['item_cost'][$var_id][$item_id] ;
                                $variation_price_cnt++;
                                $variation_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id ) ;
                            }
                            if ( isset($variation_price_sum) && isset($variation_price_cnt) && isset($variation_cost) && $variation_price_cnt != '0'){
                                $variation_price_avg = $variation_price_sum / $variation_price_cnt;
                                $variation_profit = $variation_price_avg - $variation_cost ;
                                $variation_total_profit = $quantity * $variation_profit;
                            }
                        }
                        if ( isset($variation_total_profit) ) {
                            //variation profit
                            if (  get_option('yith_cog_currency_report') == 'no' ) {
                                ?><div class="childs" style="display: none"> <?php echo round( $variation_total_profit, 2 ) ?></div><?php
                            }
                            else{
                                ?><div class="childs" style="display: none"> <?php echo wc_price($variation_total_profit) ?></div><?php
                            }
                        }
                    }
                }
                else {
                    $item_id_array = $item['item_id'];

                    $product_price_sum = 0;
                    $product_price_cnt = 0;
                    $total_qty = 0;

                    foreach ($item_id_array as $item_id) {
                        $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                        if ( $refund_id <= 0) {
                            $item_qty = $item['item_qty'][$item_id];
                            $total_qty += $item_qty;
                            $product_cost = (float)$item['item_cost'][$item_id];
                            $product_price_cnt++;
                            $product_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$item_id], $item_id );
                        }
                         if ( isset($product_price_sum) &&  isset($product_price_cnt) && $product_price_cnt != '0'){
                         $product_price_avg = $product_price_sum / $product_price_cnt;
                        }
                        if (isset($product_price_avg) && isset($product_cost)){
                            $product_profit = $product_price_avg - (float)$product_cost;
                            $total_profit = $total_qty * $product_profit;
                        }

                    }
                    if ( isset($total_profit) ) {
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            ?><div><p><?php echo round( $total_profit, 2 ) ?></p></div><?php
                        }
                        else{
                            ?><div><p><?php echo wc_price($total_profit) ?></p></div><?php
                        }
                    }
                }

                break;


           case 'tag':

                $terms = get_the_terms( $product->get_id(), 'product_tag' );
                $tag_id_array = array();
                $term_array = array();
                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    foreach ($terms as $term) {
                        $term_array[] = $term->name;
                        $tag_id_array[] = $term->term_id;
                    }
                    $tag_data_array = array_combine($tag_id_array, $term_array);

                    foreach ($tag_data_array as $tag_id => $tag){
                        ?>
                        <a class="tag_link" href="<?php echo esc_url( add_query_arg( 'product_tag', $tag_id ) )?>"><?php echo $tag ?></a>
                        <?php
                    }
                }

                break;


           case 'margin_percentage':

                if ( $product_type == 'variable' ) {

                    if ( isset( $item['var_id'] ) && is_array( $item['var_id'] ) ){
                        $variation_id_array = array_unique($item['var_id']);
                    }
                    else{
                        $variation_id_array = array();
                    }
                    $total_profit = 0;
                    $total_price = 0;
                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];
                        $quantity = $item['var_qty'][$var_id];

                        $variation_price_sum = 0;
                        $variation_price_cnt = 0;

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_cost = $item['item_cost'][$var_id][$item_id] ;
                                $variation_price_cnt++;
                                $variation_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id ) ;
                            }
                            if ( isset($variation_price_sum) && isset($variation_price_cnt) && isset($variation_cost) && $variation_price_cnt != '0'){
                                $variation_price_avg = $variation_price_sum / $variation_price_cnt;
                                $variation_profit = $variation_price_avg - $variation_cost ;
                                $variation_total_profit = $quantity * $variation_profit;
                                $variation_total_price = $quantity * $variation_price_avg;
                            }
                        }
                        if (isset($variation_total_profit)){
                            $total_profit += $variation_total_profit;
                        }
                        if (isset($variation_total_price)){
                            $total_price += $variation_total_price;
                        }
                    }
                    if ( isset($total_profit) && isset($total_price) ) {
                            ?><div><p><?php echo round(($total_profit / $total_price) * 100, 2) . '%' ?></p></div><?php
                    }
                    foreach ($variation_id_array as $var_id){
                        $item_id_array = $item['item_id'][$var_id];
                        $quantity = $item['var_qty'][$var_id];

                        $variation_price_sum = 0;
                        $variation_price_cnt = 0;

                        foreach ($item_id_array as $item_id) {
                            $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                            if ( $refund_id <= 0) {
                                $variation_cost = $item['item_cost'][$var_id][$item_id] ;
                                $variation_price_cnt++;
                                $variation_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$var_id][$item_id], $item_id ) ;
                            }
                            if ( isset($variation_price_sum) && isset($variation_price_cnt) && isset($variation_cost) && $variation_price_cnt != '0' ){
                                $variation_price_avg = $variation_price_sum / $variation_price_cnt;
                                $variation_profit = $variation_price_avg - $variation_cost ;
                                $variation_total_profit = $quantity * $variation_profit;
                                $variation_total_price = $quantity * $variation_price_avg;
                            }
                        }
                        if ( isset($variation_total_profit) && isset($variation_total_price) ) {
                                ?><div class="childs" style="display: none"> <?php echo round(( $variation_total_profit / $variation_total_price ) * 100, 2) . '%' ?></div><?php
                        }
                    }
                }
                else {
                    $item_id_array = $item['item_id'];

                    $product_price_sum = 0;
                    $product_price_cnt = 0;

                    foreach ($item_id_array as $item_id) {
                        $refund_id = wc_get_order_item_meta($item_id, '_refunded_item_id', true);
                        if ( $refund_id <= 0) {
                            $product_cost = (float)$item['item_cost'][$item_id];
                            $product_price_cnt++;
                            $product_price_sum += apply_filters( 'yith_cog_convert_amounts', $item['item_price'][$item_id], $item_id );
                        }

                        if ( isset($product_price_sum) &&  isset($product_price_cnt) && $product_price_cnt != '0'){
                         $product_price_avg = $product_price_sum / $product_price_cnt;
                        }
                        if ( isset($product_price_avg) && isset($product_cost) ) {
                            $product_profit = $product_price_avg - (float)$product_cost;
                            $total_profit = $product_price_cnt * $product_profit;
                            $total_price = $product_price_cnt * $product_price_avg;
                        }
                    }
                    if ( isset($total_profit) && isset($total_price) ) {
                            ?><div><p><?php echo round(( $total_profit / $total_price ) * 100, 2) . '%' ?></p></div><?php
                    }
                }

                break;


            case 'wc_actions' :

                ?><p><?php
                $actions = array();

                $action_id = $item['prod_id'];

                if ( $product ){
                     $actions['edit'] = array(
                    'url'       => admin_url( 'post.php?post=' . $action_id . '&action=edit' ),
                    'name'      => esc_html__( 'Edit', 'woocommerce' ),
                    'action'    => "edit",
                );
                }
                if ( $product && $product->is_visible() ) {
                    $actions['view'] = array(
                        'url'       => get_permalink( $action_id ),
                        'name'      => esc_html__( 'View', 'woocommerce' ),
                        'action'    => "view",
                    );
                }

                foreach ( $actions as $action ) {
                    printf(
                        '<a class="button tips %1$s" href="%2$s" data-tip="%3$s">%4$s</a>',
                        esc_attr( $action['action'] ),
                        esc_url( $action['url'] ),
                        sprintf( esc_attr__( '%s product', 'woocommerce' ), $action['name'] ),
                        esc_html( $action['name'] )
                    );
                }
                ?></p><?php
                break;

            default:

                apply_filters( 'yith_columns_switch' , $column_name );

        }
    }


    /**
     * Get columns.
     */
    public function get_columns() {

        $columns = array(
            'product'               => apply_filters( 'yith_cog_report_table_header_product_name',   esc_html__( 'Product', 'yith-cost-of-goods-for-woocommerce' ) ),
            'total_sales'           => apply_filters( 'yith_cog_report_table_header_total_sales',    esc_html__( 'Quantity', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_price'         => apply_filters( 'yith_cog_report_table_header_product_price',  esc_html__( 'AVG Product Prices', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_total_price'   => apply_filters( 'yith_cog_report_table_header_total_price',    esc_html__( 'Revenue', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_cost'          => apply_filters( 'yith_cog_report_table_header_product_cost',   esc_html__( 'AVG Product Cost', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_total_cost'    => apply_filters( 'yith_cog_report_table_header_total_cost',     esc_html__( 'Cost', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_profit'        => apply_filters( 'yith_cog_report_table_header_product_profit', esc_html__( 'AVG Product Margin', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_total_profit'  => apply_filters( 'yith_cog_report_table_header_total_profit',   esc_html__( 'Margin', 'yith-cost-of-goods-for-woocommerce' ) ),
        );

        //Filter to add more columns to the table.
        $columns = apply_filters( 'yith_add_custom_columns', $columns );

        //Set the Actions column to the final.
        $columns['wc_actions'] = esc_html__( 'Actions', 'yith-cost-of-goods-for-woocommerce' );

        return $columns;
    }

    
    /**
     * Get items from Query.
     */
    public function get_items( $current_page, $per_page ) {

        $this->max_items = 0;
        $this->items = array();

        $report = new YITH_COG_Report_Data();
        $report_cat = new YITH_COG_Report_Data_Category();
        $report_prod = new YITH_COG_Report_Data_Product();


        if ( isset( $_GET['report'] ) ){
            $report_name = $_GET['report'];
        }
        else{
            $report_name = 'sales_by_date';
        }


        // Get items depending of the report *****************************

        // Sales by date Report ***************
        if ( $report_name == 'sales_by_date' ) {

            $report->output_report();
            $data = $report->get_report_data();

            $item_taxes_array = $data->item_tax;
            $product_id_array = $data->product_ids;
            $variation_id_array = $data->variation_ids;
            $item_qty_array = $data->item_count;
            $item_id_array = $data->item_id;
            $item_price_array = $data->item_price;
            $item_cost_array = $data->item_cost;

            // $item data array
            $item = array(
                'prod_id' => $product_id_array,
                'var_id' => $variation_id_array,
                'qty' => $item_qty_array,
                'item_id' => $item_id_array,
                'item_price' => $item_price_array,
                'item_cost' => $item_cost_array,
                'item_tax' => $item_taxes_array,
            );

            /*IMPORTANT:
             *   In the following lines we have to structure the data for pass it to each column correctly as an item
             *   Recommended to use a log to se the structure and know how the data is structured
             */
            $array_by_product_id = array();
            $total_quantity = 0;
            $total_price = 0;
            $total_cost = 0;
            $total_var_quantity = 0;
            for ($i = 0; $i < count($item['var_id']); $i++) {

                //variable product item structure
                if ($item['var_id'][$i] != 0) {

                    $total_var_quantity += $item['qty'][$i];
                    $total_price += $item['qty'][$i] * $item['item_price'][$i];
                    $total_cost += $item['qty'][$i] * (float)$item['item_cost'][$i];
                    $total_profit = $total_price - $total_cost;

                    $product_name = wc_get_order_item_meta($item['item_id'][$i], '_yith_cog_item_name_sortable', true);

                    $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = $item['prod_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['var_id'][] = $item['var_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['total_qty'] = $total_var_quantity;
                    $array_by_product_id[$item['prod_id'][$i]]['var_qty'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_id'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['item_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_price'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['item_price'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_name'] = $product_name;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_price_sortable'] = $total_price;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_qty'] = $total_var_quantity;

                    // change item cost value if exclude taxes
                    if ($report->exclude_taxes() == true) {
                        $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost;
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit;

                    } else {
                        if (isset($item['item_tax'][$i])) {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i] + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit - $item['item_tax'][$i];

                        } else {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost;
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit;
                        }
                    }
                }
                //simple product item structure
                else {
                    $total_quantity += $item['qty'][$i];
                    if ($total_quantity <= 0) {
                        $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = 0;
                    } else {
                        $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = $item['prod_id'][$i];
                    }

                    $product_name = wc_get_order_item_meta($item['item_id'][$i], '_yith_cog_item_name_sortable', true);

                    $array_by_product_id[$item['prod_id'][$i]]['item_id'][$i] = $item['item_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['total_qty'] = $total_quantity;
                    $array_by_product_id[$item['prod_id'][$i]]['item_qty'][$item['item_id'][$i]] = $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_price'][$item['item_id'][$i]] = $item['item_price'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_name'] = $product_name;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_price_sortable'] = $item['item_price'][$i] * $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_qty'] = $item['qty'][$i];

                    // change item cost value if exclude taxes
                    if ($report->exclude_taxes() == true) {

                        $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = (float)$item['item_cost'][$i] * $item['qty'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( (float)$item['item_cost'][$i] * $item['qty'][$i] ) ;
                    }
                    else {
                        if (isset($item['item_tax'][$i])) {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i] + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = ( (float)$item['item_cost'][$i] + $item['item_tax'][$i] ) * $item['qty'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( ( (float)$item['item_cost'][$i] + $item['item_tax'][$i] ) * $item['qty'][$i] );
                        } else {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = (float)$item['item_cost'][$i] * $item['qty'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( (float)$item['item_cost'][$i] * $item['qty'][$i] ) ;
                        }
                    }
                }
            }

            // calculate the total quantity of each variation
            foreach ($array_by_product_id as $prod_id => &$prod) {
                if (isset($prod['var_id'])) {
                    foreach ($prod['var_qty'] as $var_id => $var) {
                        $prod['var_qty'][$var_id] = array_sum($var);
                    }
                }
            }

            //In this foreach, unset the refunded items because they appear hidden in the table.
            $items_array = array();
            foreach ($array_by_product_id as $prod_id => &$prod) {
                if ($prod['total_qty'] <= 0) {
                    unset($prod);
                } else {
                    $items_array[$prod_id] = $prod;
                }
            }

            //$item array for the column_default function.
            $raw_items = array_slice( $items_array,( ( $current_page - 1 ) * $per_page ), $per_page );

            $aux_items = array();

            foreach ($raw_items as $item ){

                $product = wc_get_product( $item['prod_id'] );

                if ( is_object($product) && $product->is_type( 'gift-card' )  ) {
                    continue;
                }
                $aux_items[] = $item;
            }

            $this->items =  $aux_items;

            $this->max_items = count( $items_array );
            
            $this->filter_by_tag( $report, $current_page, $per_page );

        }


        // Sales by product Report ****************
        if ( $report_name == 'sales_by_product' ) {

            $report_prod->output_report();
            $data = $report_prod->get_report_data();
            $prod_ids = $report_prod->product_ids;

            if ( empty($prod_ids)){
                return;
            }

            $item_taxes_array = $data->item_tax;
            $product_id_array = $data->product_ids;
            $variation_id_array = $data->variation_ids;
            $item_qty_array = $data->item_count;
            $item_id_array = $data->item_id;
            $item_price_array = $data->item_price;
            $item_cost_array = $data->item_cost;

            // $item data array
            $item = array(
                'prod_id' => $product_id_array,
                'var_id' => $variation_id_array,
                'qty' => $item_qty_array,
                'item_id' => $item_id_array,
                'item_price' => $item_price_array,
                'item_cost' => $item_cost_array,
                'item_tax' => $item_taxes_array,
            );

            /*IMPORTANT:
             *   In the following lines we have to structure the data for pass it to each column correctly as an item
             *   Recommended to use a log to se the structure and know how the data is structured
             */
            $array_by_product_id = array();
            $total_quantity = 0;
            $total_price = 0;
            $total_cost = 0;
            $total_var_quantity = 0;

            for ($i = 0; $i < count($item['var_id']); $i++) {

                //variable product item structure
                if ($item['var_id'][$i] != 0) {
                    $total_var_quantity += $item['qty'][$i];
                    $total_price += $item['qty'][$i] * $item['item_price'][$i];
                    $total_cost += $item['qty'][$i] * (float)$item['item_cost'][$i];
                    $total_profit = $total_price - $total_cost;
                    $product = wc_get_product($item['prod_id'][$i]);

                    $product_name = is_object($product) ? $product->get_name() : '';

                    $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = $item['prod_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['var_id'][] = $item['var_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['total_qty'] = $total_var_quantity;
                    $array_by_product_id[$item['prod_id'][$i]]['var_qty'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_id'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['item_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_price'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['item_price'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_name'] = $product_name;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_price_sortable'] = $total_price;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_qty'] = $total_var_quantity;

                    // change item cost value if exclude taxes
                    if ($report->exclude_taxes() == true) {
                        $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost;
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit;
                    }
                    else {
                        if (isset($item['item_tax'][$i])) {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i] + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit - $item['item_tax'][$i];
                        }
                        else {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost;
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit;
                        }
                    }
                }
                //simple product item structure
                else {
                    $total_quantity += $item['qty'][$i];
                    if ($total_quantity <= 0) {
                        $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = 0;
                    } else {
                        $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = $item['prod_id'][$i];
                    }

                    $product = wc_get_product($item['prod_id'][$i]);
                    $product_name = is_object($product) ? $product->get_name() : '';

                    $array_by_product_id[$item['prod_id'][$i]]['item_id'][$i] = $item['item_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['total_qty'] = $total_quantity;
                    $array_by_product_id[$item['prod_id'][$i]]['item_qty'][$item['item_id'][$i]] = $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_price'][$item['item_id'][$i]] = $item['item_price'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_name'] = $product_name;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_price_sortable'] = $item['item_price'][$i] * $item['qty'][$i] ;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_qty'] = $item['qty'][$i];

                    // change item cost value if exclude taxes
                    if ($report->exclude_taxes() == true) {
                        $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = (float)$item['item_cost'][$i] * $item['qty'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( (float)$item['item_cost'][$i] * $item['qty'][$i] ) ;
                    }
                    else {
                        if (isset($item['item_tax'][$i])) {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i] + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = ( (float)$item['item_cost'][$i] + $item['item_tax'][$i] ) * $item['qty'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( ( (float)$item['item_cost'][$i] + $item['item_tax'][$i] ) * $item['qty'][$i] );
                        }
                        else {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = (float)$item['item_cost'][$i] * $item['qty'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( (float)$item['item_cost'][$i] * $item['qty'][$i] ) ;
                        }
                    }
                }
            }

            // calculate the total quantity of each variation
            foreach ($array_by_product_id as $prod_id => &$prod) {
                if (isset($prod['var_id'])) {
                    foreach ($prod['var_qty'] as $var_id => $var) {
                        $prod['var_qty'][$var_id] = array_sum($var);
                    }
                }
            }

            //In this foreach, unset the refunded items because they appear hidden in the table.
            $items_array = array();
            foreach ($array_by_product_id as $prod_id => &$prod) {
                if ($prod['total_qty'] <= 0) {
                    unset($prod);
                } else {
                    $items_array[$prod_id] = $prod;
                }
            }

            //$item array for the column_default function.
            $raw_items =  $this->items = array_slice( $items_array,( ( $current_page - 1 ) * $per_page ), $per_page );

            $aux_items = array();

            foreach ($raw_items as $item ){

                $product = wc_get_product( $item['prod_id'] );

                if (is_object($product) && $product->is_type( 'gift-card' )  ) {
                    continue;
                }
                $aux_items[] = $item;
            }

            $this->items =  $aux_items;

            $this->max_items = count( $items_array );

            $this->filter_by_tag( $report, $current_page, $per_page );
        }


        // Sales by category Report ****************
        if ( $report_name == 'sales_by_category' ) {

            $report_cat->output_report();
            $data = $report_cat->get_report_data();
            $cat_ids = $report_cat->category_ids;

            if ( empty($cat_ids)){
                return;
            }

            $item_taxes_array = $data->item_tax;
            $product_id_array = $data->product_ids;
            $variation_id_array = $data->variation_ids;
            $item_qty_array = $data->item_count;
            $item_id_array = $data->item_id;
            $item_price_array = $data->item_price;
            $item_cost_array = $data->item_cost;

            // $item data array
            $item = array(
                'prod_id' => $product_id_array,
                'var_id' => $variation_id_array,
                'qty' => $item_qty_array,
                'item_id' => $item_id_array,
                'item_price' => $item_price_array,
                'item_cost' => $item_cost_array,
                'item_tax' => $item_taxes_array,
            );

            /*IMPORTANT:
             *   In the following lines we have to structure the data for pass it to each column correctly as an item
             *   Recommended to use a log to se the structure and know how the data is structured
             */
            $array_by_product_id = array();
            $total_quantity = 0;
            $total_price = 0;
            $total_cost = 0;
            $total_var_quantity = 0;
            for ($i = 0; $i < count($item['var_id']); $i++) {

                //variable product item structure
                if ($item['var_id'][$i] != 0) {
                    $total_var_quantity += $item['qty'][$i];
                    $total_price += $item['qty'][$i] * $item['item_price'][$i];
                    $total_cost += $item['qty'][$i] * (float)$item['item_cost'][$i];
                    $total_profit = $total_price - $total_cost;
                    $product = wc_get_product($item['prod_id'][$i]);
                    $product_name = is_object($product) ? $product->get_name() : '';

                    $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = $item['prod_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['var_id'][] = $item['var_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['total_qty'] = $total_var_quantity;
                    $array_by_product_id[$item['prod_id'][$i]]['var_qty'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_id'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['item_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_price'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['item_price'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_name'] = $product_name;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_price_sortable'] = $total_price;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_qty'] = $total_var_quantity;

                    // change item cost value if exclude taxes
                    if ($report->exclude_taxes() == true) {
                        $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost;
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit;

                    }
                    else {
                        if (isset($item['item_tax'][$i])) {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i] + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit - $item['item_tax'][$i];
                        }
                        else {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost;
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit;
                        }
                    }
                }
                //simple product item structure
                else {
                    $total_quantity += $item['qty'][$i];
                    if ($total_quantity <= 0) {
                        $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = 0;
                    }
                    else {
                        $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = $item['prod_id'][$i];
                    }

                    $product = wc_get_product($item['prod_id'][$i]);
                    $product_name = is_object($product) ? $product->get_name() : '';

                    $array_by_product_id[$item['prod_id'][$i]]['item_id'][$i] = $item['item_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['total_qty'] = $total_quantity;
                    $array_by_product_id[$item['prod_id'][$i]]['item_qty'][$item['item_id'][$i]] = $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_price'][$item['item_id'][$i]] = $item['item_price'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_name'] = $product_name;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_price_sortable'] = $item['item_price'][$i] * $item['qty'][$i] ;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_qty'] = $item['qty'][$i];

                    // change item cost value if exclude taxes
                    if ($report->exclude_taxes() == true) {
                        $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = (float)$item['item_cost'][$i] * $item['qty'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( (float)$item['item_cost'][$i] * $item['qty'][$i] ) ;
                    }
                    else {
                        if (isset($item['item_tax'][$i])) {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] =(float) $item['item_cost'][$i] + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = ( (float)$item['item_cost'][$i] + $item['item_tax'][$i] ) * $item['qty'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( ( (float)$item['item_cost'][$i] + $item['item_tax'][$i] ) * $item['qty'][$i] );
                        }
                        else {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = (float)$item['item_cost'][$i] * $item['qty'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( (float)$item['item_cost'][$i] * $item['qty'][$i] ) ;
                        }
                    }
                }
            }

            // calculate the total quantity of each variation
            foreach ($array_by_product_id as $prod_id => &$prod) {
                if (isset($prod['var_id'])) {
                    foreach ($prod['var_qty'] as $var_id => $var) {
                        $prod['var_qty'][$var_id] = array_sum($var);
                    }
                }
            }

            //In this foreach, unset the refunded items because they appear hidden in the table.
            $items_array = array();
            foreach ($array_by_product_id as $prod_id => &$prod) {
                if ($prod['total_qty'] <= 0) {
                    unset($prod);
                }
                else {
                    $items_array[$prod_id] = $prod;
                }
            }

            //$item array for the column_default function.
            $raw_items = array_slice( $items_array,( ( $current_page - 1 ) * $per_page ), $per_page );

            $aux_items = array();

            foreach ($raw_items as $item ){

                $product = wc_get_product( $item['prod_id'] );

                if ( is_object($product) && $product->is_type( 'gift-card' )  ) {
                    continue;
                }
                $aux_items[] = $item;
            }

            $this->items =  $aux_items;

            $this->max_items = count( $items_array );

            $this->filter_by_tag( $report, $current_page, $per_page );

        }
    }

    /**
     * Filter the report by Tag
     */
    public function filter_by_tag( $report, $current_page, $per_page ){


        $report_tag = new YITH_COG_Report_Data_Tag();

        if ( isset($_GET['product_tag'] ) ){
            $report_tag->output_report();
            $data = $report_tag->get_report_data();

            $item_taxes_array = $data->item_tax;
            $product_id_array = $data->product_ids;
            $variation_id_array = $data->variation_ids;
            $item_qty_array = $data->item_count;
            $item_id_array = $data->item_id;
            $item_price_array = $data->item_price;
            $item_cost_array = $data->item_cost;

            // $item data array
            $item = array(
                'prod_id' => $product_id_array,
                'var_id' => $variation_id_array,
                'qty' => $item_qty_array,
                'item_id' => $item_id_array,
                'item_price' => $item_price_array,
                'item_cost' => $item_cost_array,
                'item_tax' => $item_taxes_array,
            );

            /*IMPORTANT:
             *   In the following lines we have to structure the data for pass it to each column correctly as an item
             *   Recommended to use a log to se the structure and know how the data is structured
             */
            $array_by_product_id = array();
            $total_quantity = 0;
            $total_price = 0;
            $total_cost = 0;
            $total_var_quantity = 0;
            for ($i = 0; $i < count($item['var_id']); $i++) {

                //variable product item structure
                if ($item['var_id'][$i] != 0) {
                    $total_var_quantity += $item['qty'][$i];
                    $total_price += $item['qty'][$i] * $item['item_price'][$i];
                    $total_cost += $item['qty'][$i] * (float)$item['item_cost'][$i];
                    $total_profit = $total_price - $total_cost;
                    $product = wc_get_product($item['prod_id'][$i]);
                    $product_name = is_object($product) ? $product->get_name() : '';

                    $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = $item['prod_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['var_id'][] = $item['var_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['total_qty'] = $total_var_quantity;
                    $array_by_product_id[$item['prod_id'][$i]]['var_qty'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_id'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['item_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_price'][$item['var_id'][$i]][$item['item_id'][$i]] = $item['item_price'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_name'] = $product_name;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_price_sortable'] = $total_price;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_qty'] = $total_var_quantity;


                    // change item cost value if exclude taxes
                    if ($report->exclude_taxes() == true) {
                        $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost;
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit;

                    }
                    else {
                        if (isset($item['item_tax'][$i])) {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i] + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit - $item['item_tax'][$i];

                        }
                        else {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['var_id'][$i]][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = $total_cost;
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = $total_profit;
                        }
                    }
                }
                //simple product item structure
                else {
                    $total_quantity += $item['qty'][$i];
                    if ($total_quantity <= 0) {
                        $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = 0;
                    }
                    else {
                        $array_by_product_id[$item['prod_id'][$i]]['prod_id'] = $item['prod_id'][$i];
                    }

                    $product = wc_get_product($item['prod_id'][$i]);
                    $product_name = is_object($product) ? $product->get_name() : '';

                    $array_by_product_id[$item['prod_id'][$i]]['item_id'][$i] = $item['item_id'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['total_qty'] = $total_quantity;
                    $array_by_product_id[$item['prod_id'][$i]]['item_qty'][$item['item_id'][$i]] = $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_price'][$item['item_id'][$i]] = $item['item_price'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['item_name'] = $product_name;
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_price_sortable'] = $item['item_price'][$i] * $item['qty'][$i];
                    $array_by_product_id[$item['prod_id'][$i]]['product_total_qty'] = $item['qty'][$i];

                    // change item cost value if exclude taxes
                    if ($report->exclude_taxes() == true) {
                        $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = (float)$item['item_cost'][$i] * $item['qty'][$i];
                        $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( (float)$item['item_cost'][$i] * $item['qty'][$i] ) ;
                    }
                    else {
                        if (isset($item['item_tax'][$i])) {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i] + $item['item_tax'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = ( (float)$item['item_cost'][$i] + $item['item_tax'][$i] ) * $item['qty'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( ( (float)$item['item_cost'][$i] + $item['item_tax'][$i] ) * $item['qty'][$i] );
                        }
                        else {
                            $array_by_product_id[$item['prod_id'][$i]]['item_cost'][$item['item_id'][$i]] = (float)$item['item_cost'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_cost_sortable'] = (float)$item['item_cost'][$i] * $item['qty'][$i];
                            $array_by_product_id[$item['prod_id'][$i]]['product_total_profit_sortable'] = ( $item['item_price'][$i] * $item['qty'][$i] ) - ( (float)$item['item_cost'][$i] * $item['qty'][$i] ) ;
                        }
                    }
                }
            }

            // calculate the total quantity of each variation
            foreach ($array_by_product_id as $prod_id => &$prod) {
                if (isset($prod['var_id'])) {
                    foreach ($prod['var_qty'] as $var_id => $var) {
                        $prod['var_qty'][$var_id] = array_sum($var);
                    }
                }
            }

            //In this foreach, unset the refunded items because they appear hidden in the table.
            $items_array = array();
            foreach ($array_by_product_id as $prod_id => &$prod) {
                if ($prod['total_qty'] <= 0) {
                    unset($prod);
                } else {
                    $items_array[$prod_id] = $prod;
                }
            }

            //$item array for the column_default function.
           $raw_items = array_slice( $items_array,( ( $current_page - 1 ) * $per_page ), $per_page );

            $aux_items = array();

            foreach ($raw_items as $item ){

                $product = wc_get_product( $item['prod_id'] );

                if ( is_object($product) && $product->is_type( 'gift-card' )  ) {
                    continue;
                }

                $aux_items[] = $item;
            }

            $this->items =  $aux_items;

            $this->max_items = count( $items_array );

        }
    }


     /**
     * Filter the report by Tag
     */
    public function filter_totals_by_tag(){

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

        $number_of_items_option = get_option('yith_cog_set_pagination_report_table');
        $number_of_items = ( !is_null( $number_of_items_option ) ? $number_of_items_option : 20 );

        $per_page = apply_filters( 'yith_cog_report_by_date_products_per_page', $number_of_items);
        $current_page = absint( $this->get_pagenum() );

        $this->get_items( $current_page, $per_page );

        if( $per_page > 0 ){
            $total_pages = ceil( $this->max_items / $per_page );
        }
        else{
            $per_page = 20;
            $total_pages = ceil( $this->max_items / $per_page );
        }


        /**
         * Pagination.
         */
        $this->set_pagination_args( array(
            'total_items' => $this->max_items,
            'per_page'    => $per_page,
            'total_pages' => $total_pages,
        ) );
    }

}
