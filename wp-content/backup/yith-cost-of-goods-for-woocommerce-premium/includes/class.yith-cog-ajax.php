<?php


if ( !defined( 'YITH_COG_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

if ( !class_exists( 'YITH_COG_Ajax' ) ) {
    /**
     * YITH_COG_Ajax
     *
     * @since 1.0.0
     */
    class YITH_COG_Ajax {

        /**
         * Single instance of the class
         *
         * @var \YITH_COG_Ajax
         * @since 1.0.0
         */
        protected static $instance;

        public $limit;
        public $offset;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_COG_Ajax
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$instance ) ) {
                $self::$instance = new $self;
            }

            return $self::$instance;
        }

        /**
         * Constructor
         *
         */
        public function __construct() {

            //Ajax methods for Apply buttons
            add_action( 'wp_ajax_yith_apply_cost_button', array( $this, 'apply_cost' ) );
            add_action( 'wp_ajax_nopriv_yith_apply_cost_button', array( $this, 'apply_cost' ) );
            add_action( 'wp_ajax_yith_apply_cost_overriding_button', array( $this, 'apply_cost_overriding' ) );
            add_action( 'wp_ajax_nopriv_yith_apply_cost_overriding_button', array( $this, 'apply_cost_overriding' ) );

            add_action( 'wp_ajax_yith_apply_cost_selected_order_button', array( $this, 'apply_cost_selected_order' ) );
            add_action( 'wp_ajax_nopriv_yith_apply_cost_selected_order_button', array( $this, 'apply_cost_selected_order' ) );

            //Ajax methods for Apply buttons
            add_action( 'wp_ajax_yith_import_cost_button', array( $this, 'yith_import_cost' ) );
            add_action( 'wp_ajax_nopriv_yith_import_cost_button', array( $this, 'yith_import_cost' ) );

        }

        /**
         * Apply costs to orders that do not have costs set.
         */
        public function apply_cost(){

            //If is set '_yith_cog_item_cost' continue, if not, set it.
            $this->offset  = intval( $_POST['offset']);
            $this->limit   = intval($_POST['limit']);

            if ( $this->limit == 0 ) {
                $this->limit =apply_filters('yith_cog_limit_generate_apply_cost', 50 );
            }

            //Set the actual CoG to all items, overriding it if necesary.
            $orders = new YITH_COG_Orders();
            $report = new YITH_COG_Report_Data();
            $data = $report->get_report_data();

            $item_id_array = $data->item_id_;
            $order_id_array = $data->order_id_;

            $item = array(
                'order_id' => $order_id_array,
                'item_id' => $item_id_array,
            );

            $number_of_items = count( $item_id_array );

            if ( $this->limit > $number_of_items ){
                $counter = $number_of_items;
            }
            else{
                $counter = $this->offset + $this->limit;
            }

            for ($i = $this->offset; $i < $counter; $i++){

                $cost = wc_get_order_item_meta( $item['item_id'][$i], '_yith_cog_item_cost', true );

                if ( empty( $cost ) ){
                    //Set new Cost
                    $orders->set_order_cost_meta($item['order_id'][$i]);
                }
            }

            $new_offset = $this->offset + $this->limit;

            if (($number_of_items - $new_offset) < $this->limit){
                $this->limit = $number_of_items - $new_offset;
            }

            if ( $new_offset < $number_of_items ){

                $data=array(
                    "limit"=> "$this->limit",
                    "offset" => "$new_offset",
                    "loop" => "1",
                );

                wp_send_json( $data );
            }
            else{
                $data=array(
                    "limit"=> "$this->limit",
                    "offset" => "$new_offset",
                    "loop" => "0",
                );
                wp_send_json( $data );
            }
        }


        /**
         * Apply costs to all orders, overriding previous costs.
         */
        public function apply_cost_overriding(){


            $this->offset  = intval( $_POST['offset']);
            $this->limit   = intval($_POST['limit']);

            if ( $this->limit == 0 ) {
                $this->limit =apply_filters('yith_cog_limit_generate_apply_cost_overriding', 50 );
            }

            //Set the actual CoG to all items, overriding it if necesary.
            $orders = new YITH_COG_Orders();
            $report = new YITH_COG_Report_Data();
            $data = $report->get_report_data();

            $item_id_array = $data->item_id_;
            $order_id_array = $data->order_id_;

            $item = array(
                'order_id' => $order_id_array,
                'item_id' => $item_id_array,
            );

            $number_of_items = count( $item_id_array );

            if ( $this->limit > $number_of_items ){
                $counter = $number_of_items;
            }
            else{
                $counter = $this->offset + $this->limit;
            }

            for ($i = $this->offset; $i < $counter; $i++){
//              Set new Cost
                $orders->set_order_cost_meta($item['order_id'][$i]);
            }

            $new_offset = $this->offset + $this->limit;

            if (($number_of_items - $new_offset) < $this->limit){
                $this->limit = $number_of_items - $new_offset;
            }

            if ( $new_offset < $number_of_items ){

                $data=array(
                    "limit"=> "$this->limit",
                    "offset" => "$new_offset",
                    "loop" => "1",
                );

                wp_send_json( $data );
            }
            else{
                $data=array(
                    "limit"=> "$this->limit",
                    "offset" => "$new_offset",
                    "loop" => "0",
                );
                wp_send_json( $data );
            }
        }


        public function apply_cost_selected_order(){

            $order_id = $_POST[ 'order_id' ];

            if ( isset ($order_id) ){
                $orders = new YITH_COG_Orders();
                $orders->set_order_cost_meta($order_id);
            }
        }

        /**
         * Apply costs to orders that do not have costs set.
         */
        public function yith_import_cost(){

            global $wpdb;

            $this->offset  = intval( $_POST['offset'] );
            $this->limit   = intval( $_POST['limit'] );

            if ( $this->limit == 0 ) {
                $this->limit =apply_filters('yith_cog_limit_generate_import_cost', 100 );
            }

            $query_from = "FROM {$wpdb->posts} as posts
			INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			WHERE 1=1
			AND posts.post_type IN ( 'product' )
			AND posts.post_status = 'publish' ";

            $products_array = $wpdb->get_results("SELECT DISTINCT posts.ID as id {$query_from} ");

            $total_products_number = $wpdb->get_var("SELECT COUNT( DISTINCT posts.ID ) {$query_from};");

            if ( $this->limit > $total_products_number ){
                $counter = $total_products_number;
            }
            else{
                $counter = $this->offset + $this->limit;
            }

            foreach ($products_array as $products){

                $product = wc_get_product($products->id);
                $product_id = $product->get_id();

                for ($i = $this->offset; $i < $counter; $i++){

                    if ( $product->is_type( 'variable' ) ) {

                        foreach ( $product->get_children() as $child_id ) {

                            $variation_cost = get_post_meta( $child_id, 'yith_cog_cost', true );
                            $woo_variation_cost = get_post_meta( $child_id, '_wc_cog_cost', true );

                            if ( empty( $variation_cost ) && !empty( $woo_variation_cost ) ){
                                update_post_meta($child_id, 'yith_cog_cost', $woo_variation_cost);
                            }
                        }
                        $cost = get_post_meta( $product_id, 'yith_cog_cost', true );
                        $cost_variable = get_post_meta( $product_id, 'yith_cog_cost_variable', true );
                        $min_variation_cost = get_post_meta( $product_id, 'yith_cog_min_variation_cost', true );
                        $max_variation_cost = get_post_meta( $product_id, 'yith_cog_max_variation_cost', true );
                    }
                    else {
                        $cost = get_post_meta( $product_id, 'yith_cog_cost', true );
                    }

                    $woo_cost = get_post_meta( $product_id, '_wc_cog_cost', true );
                    $woo_cost_variable = get_post_meta( $product_id, '_wc_cog_cost_variable', true );
                    $woo_min_variation_cost = get_post_meta( $product_id, '_wc_cog_min_variation_cost', true );
                    $woo_max_variation_cost = get_post_meta( $product_id, '_wc_cog_max_variation_cost', true );

                    if ( empty( $cost ) && !empty( $woo_cost ) ){
                        update_post_meta($product_id, 'yith_cog_cost', $woo_cost);
                    }
                    if ( empty( $cost_variable ) && !empty( $woo_cost_variable ) ){
                        update_post_meta($product_id, 'yith_cog_cost_variable', $woo_cost_variable);
                    }
                    if ( empty( $min_variation_cost ) && !empty( $woo_min_variation_cost ) ){
                        update_post_meta($product_id, 'yith_cog_min_variation_cost', $woo_min_variation_cost);
                    }
                    if ( empty( $max_variation_cost ) && !empty( $woo_max_variation_cost ) ){
                        update_post_meta($product_id, 'yith_cog_max_variation_cost', $woo_max_variation_cost);
                    }
                }
            }

            $new_offset = $this->offset + $this->limit;

            if (($total_products_number - $new_offset) < $this->limit){
                $this->limit = $total_products_number - $new_offset;
            }

            if ( $new_offset < $total_products_number ){
                $data=array(
                    "limit"=> "$this->limit",
                    "offset" => "$new_offset",
                    "loop" => "1",
                );

                wp_send_json( $data );
            }
            else{
                $data=array(
                    "limit"=> "$this->limit",
                    "offset" => "$new_offset",
                    "loop" => "0",
                );
                wp_send_json( $data );
            }
        }

    }
}