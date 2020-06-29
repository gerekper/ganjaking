<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * @class      YITH_COG_Report_Stock
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Francisco Mendoza
 *
 */
class YITH_COG_Report_Stock extends WP_List_Table {


    /**
     * Max items.
     *
     * @var int
     */
    protected $max_items;

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
     * Display the table
     */
    public function display() {


        if ( isset( $_GET['report'] ) ){
            $report_name = $_GET['report'];
        }
        else{
            $report_name = 'all_stock';
        }

        if ( $report_name == 'all_stock' ){
            $items = $this->get_items_totals();
        }
        if ( $report_name == 'stock_by_product' ){
            $items = $this->items;
        }
        if ( $report_name == 'stock_by_category' ){
            $items = $this->items;
        }

        $total_stock = 0;
        $total_prices = 0;
        $total_cost = 0;
        $total_profit = 0;

        foreach  ( $items as $item ){
            $product = wc_get_product( $item->id );


            if ($product->is_type('variable')) {

                $product_variations = $product->get_available_variations();

                $product_stock = 0;
                $product_total_price = 0;
                $product_total_cost = 0;
                $product_total_profit = 0;

                foreach ($product_variations as $variation) {
                    $variation_stock = $variation['max_qty'];
                    if ( !is_numeric($variation_stock) ){
                        $variation_stock = 0;
                    }
                    $product_stock += $variation_stock;

                    ////

                    $variation_price = $variation['display_price'];
                    if ( !is_numeric($variation_stock) ){
                        $variation_stock = 0;
                    }
                    $product_total_price += ((float)$variation_price * $variation_stock);

                    ////

                    $variation_id = $variation['variation_id'];
                    $variation_obj = wc_get_product($variation_id);
                    $var_cost = YITH_COG_Product::get_cost($variation_obj);

                    $product_total_cost += ((float)$var_cost * $variation_stock);

                    ////

                    $variation_profit = ( (float)$variation_price - (float)$var_cost);
                    $product_total_profit += ( (float)$variation_profit  * $variation_stock );

                }

            }
            else{
                $product_stock = $product->get_stock_quantity();
                $product_total_price = (float)$product->get_price() * $product_stock;
                $product_total_cost = (float)YITH_COG_Product::get_cost($product) * $product_stock;
                $product_total_profit = ((float)$product->get_price() - (float)YITH_COG_Product::get_cost($product)) * $product->get_stock_quantity();
            }


            $total_stock += $product_stock;
            $total_prices += $product_total_price;
            $total_cost += $product_total_cost;
            $total_profit += $product_total_profit;

        }


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
                <th style="font-weight: bold" class="total_row" colspan="2"><?php echo $total_stock ;?></th>
                <th style="font-weight: bold" class="total_row" colspan="2"><?php echo wc_price($total_prices);?></th>
                <th style="font-weight: bold" class="total_row"><?php echo wc_price($total_cost);?></th>
                <th style="font-weight: bold" class="total_row" colspan="2"><?php echo wc_price($total_profit);?></th>

            </tr>
            </tfoot>
        </table>
        <?php
    }


    /**
     * Get column value.
     *
     * @param mixed $item
     * @param string $column_name
     */
    public function column_default( $item, $column_name ) {

        global $product;

        if ( ! $product || $product->get_id() !== $item->id ) {
            $product = wc_get_product( $item->id );
        }
        if ( ! $product ) {
            return ;
        }

        //Columns content in the Report
        switch ( $column_name ) {

            case 'product' :

                if ($product->is_type('variable')) {

                    $url = $product->get_permalink();
                    ?><a href="<?php echo $url ?>"><?php echo $product->get_name() . ' ' ?></a></a><span class="dashicons dashicons-arrow-down desplegable"></span><?php
                    ?><br><p></p><?php
                    $product_variations = $product->get_available_variations();
                    foreach ($product_variations as $variation) {
                        $variation_id = $variation['variation_id'];
                        $variation_obj = wc_get_product($variation_id);

                        ?><div class="childs" style="display: none"><?php echo $variation_obj->get_name() ?></div><?php
                    }
                }
                else{

                    $url = $product->get_permalink();
                    ?><a href="<?php echo $url ?>"><?php echo $product->get_name() . ' ' ?></a><?php
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


            case 'stock_status' :

                if ($product->is_type('variable')) {
                    $product_variations = $product->get_available_variations();

                    $product_stock = 0;

                    foreach ($product_variations as $variation) {
                        $variation_stock = $variation['max_qty'];
                        if ( !is_numeric($variation_stock) ){
                            $variation_stock = 0;
                        }
                        $product_stock += $variation_stock;
                    }

                    if ( $product->is_in_stock() ) {
                        $stock_html = '<mark class="instock">' . esc_html__('In stock', 'yith-cost-of-goods-for-woocommerce') . '</mark>';
                    } else {
                        $stock_html = '<mark class="outofstock">' . esc_html__('Out of stock', 'yith-cost-of-goods-for-woocommerce') . '</mark>';
                    }

                    $product->set_stock_quantity($product_stock);

                    echo apply_filters('yith_cog_report_stock_status_variable', $stock_html, $product);

                    if ( $product->get_stock_quantity() > 0 ){
                        echo ' (' . $product->get_stock_quantity() . ')';
                    }

                    foreach ($product_variations as $variation) {

                        $product = wc_get_product( $variation['variation_id']);

                        if ( $product->get_stock_quantity() > 0 ){
                            $stock_number = ' (' . $product->get_stock_quantity() . ')';
                        }
                        else{
                            $stock_number = '';
                        }

                        if ( $product->is_in_stock() ) {
                            $stock_html = '<mark class="instock">' . esc_html__('In stock', 'yith-cost-of-goods-for-woocommerce') . '</mark>';
                        } else {
                            $stock_html = '<mark class="outofstock">' . esc_html__('Out of stock', 'yith-cost-of-goods-for-woocommerce') . '</mark>';
                        }
                        ?><div class="childs" style="display: none"> <?php echo $stock_html . $stock_number ?></div><?php
                    }
                }
                else {
                    if ($product->is_in_stock()) {
                        $stock_html = '<mark class="instock">' . esc_html__('In stock', 'yith-cost-of-goods-for-woocommerce') . '</mark>';
                    } else {
                        $stock_html = '<mark class="outofstock">' . esc_html__('Out of stock', 'yith-cost-of-goods-for-woocommerce') . '</mark>';
                    }

                    echo apply_filters('yith_cog_report_stock_status', $stock_html, $product);

                    if ( $product->get_stock_quantity() > 0 ){
                        echo ' (' . $product->get_stock_quantity() . ')';
                    }


                }

                break;

            case 'product_price' :

                if ($product->is_type('variable')) {
                    $product_variations = $product->get_available_variations();

                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        echo $product->get_price();
                    }
                    else{
                        echo $product->get_price_html();
                    }

                    foreach ($product_variations as $variation) {
                        $variation_price = $variation['display_price'];
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            ?><div class="childs" style="display: none"> <?php echo round( $variation_price, 2 ) ?></div><?php
                        }
                        else{
                            ?><div class="childs" style="display: none"> <?php echo wc_price($variation_price) ?></div><?php
                        }
                    }
                }
                else{
                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        echo $product->get_price();
                    }
                    else{
                        echo $product->get_price_html();

                    }

                }

                break;


            case 'product_total_price' :

                if ($product->is_type('variable')) {
                    $product_variations = $product->get_available_variations();
                    $var_price = 0;
                    foreach ($product_variations as $variation) {
                        $variation_price = $variation['display_price'];
                        $variation_stock = $variation['max_qty'];
                        if ( !is_numeric($variation_stock) ){
                            $variation_stock = 0;
                        }
                        $var_price += ((float)$variation_price * $variation_stock);

                    }

                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        echo round( $var_price, 2 );
                    }
                    else{
                        echo wc_price($var_price);
                    }

                    foreach ($product_variations as $variation) {
                        $variation_price = $variation['display_price'];
                        $variation_stock = $variation['max_qty'];

                        if ( !is_numeric($variation_stock) ){
                            $variation_stock = 0;
                        }

                        $total_var_price = (float)$variation_price * $variation_stock;

                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            ?><div class="childs" style="display: none"> <?php echo round( $total_var_price, 2 ) ?></div><?php
                        }
                        else{
                            ?><div class="childs" style="display: none"> <?php echo wc_price($total_var_price) ?></div><?php
                        }
                    }
                }
                else{
                    $total_price = (float)$product->get_price() * $product->get_stock_quantity();
                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        echo round( $total_price, 2 );
                    }
                    else{
                        echo wc_price($total_price);
                    }
                }

                break;

            case 'product_cost' :

                if ($product->is_type('variable')) {
                    $product_variations = $product->get_available_variations();

                    $cost = YITH_COG_Product::get_cost_html($product);

                    if ( ! empty( $cost )  ){
                        echo $cost;
                    }
                    else{
                        echo wc_price( 0 );
                    }

                    foreach ($product_variations as $variation) {

                        $variation_id = $variation['variation_id'];
                        $variation_obj = wc_get_product($variation_id);
                        $variation_cost = YITH_COG_Product::get_cost($variation_obj);

                        if ( !empty( $variation_cost ) ) {

                            if (get_option('yith_cog_currency_report') == 'no') {
                                ?>
                                <div class="childs"
                                     style="display: none"> <?php echo round($variation_cost, 2) ?></div><?php
                            } else {
                                ?>
                                <div class="childs"
                                     style="display: none"> <?php echo wc_price($variation_cost) ?></div><?php
                            }
                        }
                        else{
                            ?>
                            <div class="childs"
                                 style="display: none"> <?php echo wc_price( 0 );; ?></div><?php
                        }

                    }
                }
                else {
                    $cost = YITH_COG_Product::get_cost_html($product);

                    if ( ! empty( $cost ) ){
                        echo $cost;
                    }
                    else{
                        echo wc_price( 0 );
                    }
                }

                break;

            case 'product_total_cost' :

                if ($product->is_type('variable')) {

                    $product_variations = $product->get_available_variations();
                    $var_cost_total = 0;

                    foreach ($product_variations as $variation) {

                        $variation_stock = $variation['max_qty'];
                        if ( !is_numeric($variation_stock) ){
                            $variation_stock = 0;
                        }
                        $variation_id = $variation['variation_id'];
                        $variation_obj = wc_get_product($variation_id);
                        $var_cost = YITH_COG_Product::get_cost($variation_obj);

                        $var_cost_total += ((float)$var_cost * $variation_stock);
                    }

                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        echo round( $var_cost_total, 2 );
                    }
                    else{
                        echo wc_price($var_cost_total);
                    }

                    foreach ($product_variations as $variation) {
                        $variation_stock = $variation['max_qty'];
                        $variation_id = $variation['variation_id'];
                        $variation_obj = wc_get_product($variation_id);
                        $var_cost = YITH_COG_Product::get_cost($variation_obj);

                        if ( !is_numeric($variation_stock) ){
                            $variation_stock = 0;
                        }

                        $total_var_cost = (float)$var_cost * $variation_stock;

                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            ?><div class="childs" style="display: none"> <?php echo round( $total_var_cost, 2 ) ?></div><?php
                        }
                        else{
                            ?><div class="childs" style="display: none"> <?php echo wc_price($total_var_cost) ?></div><?php
                        }
                    }
                }
                else {

                    $cost = YITH_COG_Product::get_cost($product);
                    if (!empty($cost)){
                        $total_cost =  (float)$cost * $product->get_stock_quantity();
                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            echo round( $total_cost, 2 );
                        }
                        else{
                            echo wc_price( $total_cost );
                        }
                    }
                    else{
                        echo wc_price( 0 );
                    }
                }

                break;


            case 'potential_profit' :

                $cost = (float) YITH_COG_Product::get_cost( $product );

                $total_profit = ((float)$product->get_price() - (float)$cost) * $product->get_stock_quantity();


                if ( $product->is_type( 'variable' ) ) {

                    $product_variations = $product->get_available_variations();
                    $var_profit = 0;

                    foreach ( $product_variations as $variation){
                        $variation_stock = $variation['max_qty'];
                        if ( !is_numeric($variation_stock) ){
                            $variation_stock = 0;
                        }
                        $variation_price = $variation['display_price'];
                        $variation_id = $variation['variation_id'];
                        $variation_obj = wc_get_product($variation_id);
                        $variation_cost = YITH_COG_Product::get_cost($variation_obj);
                        $variation_profit = ( (float)$variation_price - (float)$variation_cost);
                        $var_profit += ( (float)$variation_profit  * $variation_stock );
                    }
                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        echo round( $var_profit, 2 );
                    }
                    else{
                        echo wc_price( $var_profit );
                    }

                    foreach ( $product_variations as $variation) {
                        $variation_stock = $variation['max_qty'];
                        $variation_price = $variation['display_price'];
                        $variation_id = $variation['variation_id'];
                        $variation_obj = wc_get_product($variation_id);
                        $variation_cost = YITH_COG_Product::get_cost($variation_obj);
                        $variation_profit = ( (float)$variation_price - (float)$variation_cost);

                        if ( !is_numeric($variation_stock) ){
                            $variation_stock = 0;
                        }

                        $total_var_profit = ((float)$variation_profit * $variation_stock);

                        if (  get_option('yith_cog_currency_report') == 'no' ) {
                            ?><div class="childs" style="display: none"> <?php echo round( $total_var_profit, 2 ) ?></div><?php
                        }
                        else{
                            ?><div class="childs" style="display: none"> <?php echo wc_price( $total_var_profit ) ?></div><?php
                        }
                    }
                }
                else{
                    if (  get_option('yith_cog_currency_report') == 'no' ) {
                        echo round( $total_profit, 2 );
                    }
                    else{
                        echo wc_price( $total_profit );
                    }
                }

                break;

            case 'wc_actions' :
                ?><p><?php
                $actions = array();
                $action_id = $product->is_type( 'variation' ) ? $item->parent : $item->id;

                $actions['edit'] = array(
                    'url'       => admin_url( 'post.php?post=' . $action_id . '&action=edit' ),
                    'name'      => esc_html__( 'Edit', 'yith-cost-of-goods-for-woocommerce' ),
                    'action'    => "edit",
                );

                if ( $product->is_visible() ) {
                    $actions['view'] = array(
                        'url'       => get_permalink( $action_id ),
                        'name'      => esc_html__( 'View', 'yith-cost-of-goods-for-woocommerce' ),
                        'action'    => "view",
                    );
                }
                $actions = apply_filters( 'yith_cog_admin_stock_report_product_actions', $actions, $product );

                foreach ( $actions as $action ) {
                    printf(
                        '<a class="button tips %1$s" href="%2$s" data-tip="%3$s">%4$s</a>',
                        esc_attr( $action['action'] ),
                        esc_url( $action['url'] ),
                        sprintf( esc_attr__( '%s product', 'yith-cost-of-goods-for-woocommerce' ), $action['name'] ),
                        esc_html( $action['name'] )
                    );
                }
                ?></p><?php

                break;

            default:
                apply_filters( 'yith_columns_switch_stock' , $column_name );
        }
    }


    /**
     * Get columns.
     */
    public function get_columns() {

        $columns = array(
            'product'               => apply_filters( 'yith_cog_stock_table_header_product_name',     esc_html__( 'Product', 'yith-cost-of-goods-for-woocommerce' ) ),
            'stock_status'          => apply_filters( 'yith_cog_stock_table_header_stock_status',     esc_html__( 'Stock status', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_price'         => apply_filters( 'yith_cog_stock_table_header_product_price',    esc_html__( 'Product Price', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_total_price'   => apply_filters( 'yith_cog_stock_table_header_total_price',      esc_html__( 'Product Total Price', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_cost'          => apply_filters( 'yith_cog_stock_table_header_product_cost',     esc_html__( 'Product Cost', 'yith-cost-of-goods-for-woocommerce' ) ),
            'product_total_cost'    => apply_filters( 'yith_cog_stock_table_header_total_cost',       esc_html__( 'Product Total Cost', 'yith-cost-of-goods-for-woocommerce' ) ),
            'potential_profit'      => apply_filters( 'yith_cog_stock_table_header_potential_profit', esc_html__( 'Potential Profit', 'yith-cost-of-goods-for-woocommerce' ) ),
        );

        //Filter to add more columns to the table.
        $columns = apply_filters( 'yith_add_custom_columns_stock', $columns );

        //Set the Actions column to the final.
        $columns['wc_actions'] = esc_html__( 'Actions', 'yith-cost-of-goods-for-woocommerce' );

        return $columns;
    }


    /**
     * Get items from Query.
     */
    public function get_items( $current_page, $per_page ){

        global $wpdb;

        $this->max_items = 0;
        $this->items = array();

        if (isset($_GET['order'])){
            $order = $_GET['order'];
        }
        else{
            $order = 'ASC';
        }
        if (isset($_GET['orderby'])){
            $orderby = $_GET['orderby'];
        }
        else{
            $orderby = '_stock';
        }

        $data_product = new YITH_COG_Report_Stock_Data_Product();
        $data_category = new YITH_COG_Report_Stock_Data_Category();
        $data_all_stock = new YITH_COG_Report_Stock_Data_All_Stock();

        if ( isset( $_GET['report'] ) ){
            $report_name = $_GET['report'];
        }
        else{
            $report_name = 'all_stock';
        }


        if ( $report_name == 'all_stock' ){

            $data_all_stock->output_report();

            $query_from = "FROM {$wpdb->posts} as posts
			INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
		
			WHERE 1=1
			AND posts.post_type IN ( 'product' , 'product_variation' )
			AND posts.post_status = 'publish'
			AND postmeta.meta_key = '_manage_stock' 
			AND postmeta.meta_value = 'yes'
			AND postmeta2.meta_key = '_stock' 
			AND postmeta2.meta_value > '0'
			";


            $query_from = apply_filters('yith_cog_report_stock_all_stock', $query_from);

            $raw_items = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as id, posts.post_parent as parent {$query_from} GROUP BY posts.ID ORDER BY postmeta2.meta_value {$order} LIMIT %d, %d;", ($current_page - 1) * $per_page, $per_page));

            $aux_items = array();

            foreach ($raw_items as $item ){

                $product = wc_get_product( $item->id );

                if ($product->is_type( 'gift-card' )  ) {
                    continue;
                }

                $aux_items[] = $item;
            }

            $this->items =  $aux_items;

            $this->max_items = $wpdb->get_var("SELECT COUNT( DISTINCT posts.ID ) {$query_from};");

            $this->filter_by_tag( $current_page, $per_page, $orderby, $order );

        }


        if ( $report_name == 'stock_by_product' ){

            $data_product->output_report();

            $product_id_array = $data_product->product_ids;
            $product_ids = join(",", $product_id_array);

            if ( empty($product_ids)){
                return;
            }

            $query_from = "FROM {$wpdb->posts} as posts
			    INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			    INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
		
			    WHERE 1=1
			    AND posts.post_type IN ( 'product' , 'product_variation' )
			    AND posts.post_status = 'publish'
			    AND postmeta.meta_key = '_manage_stock' 
			    AND postmeta.meta_value = 'yes'
			    AND postmeta2.meta_key = '_stock' 
			    AND postmeta2.meta_value > '0'
			    AND postmeta.post_id IN ( {$product_ids} )
			    ";

            $query_from = apply_filters('yith_cog_report_stock_by_product', $query_from);

            $raw_items = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as id, posts.post_parent as parent {$query_from} GROUP BY posts.ID ORDER BY postmeta2.meta_value {$order} LIMIT %d, %d;", ($current_page - 1) * $per_page, $per_page));

            $aux_items = array();

            foreach ($raw_items as $item ){

                $product = wc_get_product( $item->id );

                if ($product->is_type( 'gift-card' )  ) {
                    continue;
                }

                $aux_items[] = $item;
            }

            $this->items =  $aux_items;

            $this->max_items = $wpdb->get_var("SELECT COUNT( DISTINCT posts.ID ) {$query_from};");

            $this->filter_by_tag( $current_page, $per_page, $orderby, $order );

        }


        if ( $report_name == 'stock_by_category' ){

            $data_category->output_report();

            $category_array = $data_category->category_ids;
            $get_products_in_categories = $data_category->get_product_ids_in_category($category_array);
            $product_ids = join(",", $get_products_in_categories);

            if ( empty($category_array)){
                return;
            }

            $query_from = "FROM {$wpdb->posts} as posts
			    INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			    INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
		
			    WHERE 1=1
			    AND posts.post_type IN ( 'product' , 'product_variation' )
			    AND posts.post_status = 'publish'
                AND postmeta.meta_key = '_manage_stock' 
			    AND postmeta.meta_value = 'yes'
			    AND postmeta2.meta_key = '_stock' 
			    AND postmeta2.meta_value > '0'			    
			    AND postmeta.post_id IN ( {$product_ids} )
			    ";


            $query_from = apply_filters('yith_cog_report_stock_by_category', $query_from);

            $raw_items = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as id, posts.post_parent as parent {$query_from} GROUP BY posts.ID ORDER BY postmeta2.meta_value {$order} LIMIT %d, %d;", ($current_page - 1) * $per_page, $per_page));

            $aux_items = array();

            foreach ($raw_items as $item ){

                $product = wc_get_product( $item->id );

                if ($product->is_type( 'gift-card' )  ) {
                    continue;
                }

                $aux_items[] = $item;
            }

            $this->items =  $aux_items;

            $this->max_items = $wpdb->get_var("SELECT COUNT( DISTINCT posts.ID ) {$query_from};");

            $this->filter_by_tag( $current_page, $per_page, $orderby, $order );
        }

    }

    /**
     * Get items totals from Query.
     */
    public function get_items_totals(){

        global $wpdb;

        $query_from = "FROM {$wpdb->posts} as posts
			INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
		
			WHERE 1=1
			AND posts.post_type IN ( 'product' , 'product_variation' )
			AND posts.post_status = 'publish'
			AND postmeta.meta_key = '_manage_stock' 
			AND postmeta.meta_value = 'yes'
			AND postmeta2.meta_key = '_stock' 
			AND postmeta2.meta_value > '0'
			";

        $raw_items = $wpdb->get_results("SELECT posts.ID as id, posts.post_parent as parent {$query_from} " );

        $aux_items = array();

        foreach ($raw_items as $item ){

            $product = wc_get_product( $item->id );

            if ($product->is_type( 'gift-card' )  ) {
                continue;
            }

            $aux_items[] = $item;
        }

        return $aux_items;

    }



    /**
     * Filter the report by Tag
     */
    public function filter_by_tag( $current_page, $per_page , $orderby, $order ){

        global $wpdb;

        $data_by_tag = new YITH_COG_Report_Data_Tag();

        if ( isset($_GET['product_tag'] ) ){
            $data_by_tag->output_report();

            $get_products_in_tag = $data_by_tag->get_product_ids_in_tag($data_by_tag->tag_id);
            $product_ids = join(",", $get_products_in_tag);


            if ( empty($get_products_in_tag)){
                return;
            }

            $query_from = "FROM {$wpdb->posts} as posts
			    INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			    INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
		
			    WHERE 1=1
			    AND posts.post_type IN ( 'product' , 'product_variation' )
			    AND posts.post_status = 'publish'
			    AND postmeta.meta_key = '_stock_status' AND postmeta.meta_value = 'instock'
			    AND postmeta.post_id IN ( {$product_ids} )
			    
			    ";

            $query_from = apply_filters('yith_cog_report_stock_by_tag', $query_from);

            $raw_items = $wpdb->get_results($wpdb->prepare("SELECT posts.ID as id, posts.post_parent as parent {$query_from} GROUP BY posts.ID ORDER BY CAST(postmeta2.meta_value AS SIGNED) {$order} LIMIT %d, %d;", ($current_page - 1) * $per_page, $per_page));

            $aux_items = array();

            foreach ($raw_items as $item ){

                $product = wc_get_product( $item->id );

                if ($product->is_type( 'gift-card' )  ) {
                    continue;
                }

                $aux_items[] = $item;
            }

            $this->items =  $aux_items;

            $this->max_items = $wpdb->get_var("SELECT COUNT( DISTINCT posts.ID ) {$query_from};");
        }

    }


    /**
     * Prepare list items.
     */
    public function prepare_items() {

        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
        $current_page = absint( $this->get_pagenum() );

        $number_of_items_option = get_option('yith_cog_set_pagination_stock_table');

        $number_of_items = (  ($number_of_items_option > 0) ? $number_of_items_option : 20 );

        $per_page = apply_filters( 'yith_cog_admin_stock_report_products_per_page', $number_of_items );

        if( $per_page > 0 ){
            $total_pages = ceil( $this->max_items / $per_page );
        }
        else{
            $per_page = 20;
            $total_pages = ceil( $this->max_items / $per_page );
        }

        $this->get_items( $current_page, $per_page );

        $this->set_pagination_args( array(
            'total_items' => $this->max_items,
            'per_page'    => $per_page,
            'total_pages' => $total_pages,
        ) );
    }




}
