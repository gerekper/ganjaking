<?php
if( !defined('ABSPATH') )
    exit;

if( !class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

if( !class_exists( 'YITH_WC_Recovered_Order_Table' ) ){

    class YITH_WC_Recovered_Order_Table extends  WP_List_Table{

        public function __construct()
        {
            parent::__construct( array(
                'singular' => _x('recovered order', 'yith-woocommerce-pending-order-survey'),     //singular name of the listed records
                'plural' => _x('recovered orders', 'yith-woocommerce-pending-order-survey'),    //plural name of the listed records
                'ajax' => false        //does this table support ajax?
            ));


        }

        /**
         * @return array columns
         */
        public function get_columns()
        {
            $columns = array(
                'ywcpos_order_title' => __('Order','yith-woocommerce-pending-order-survey'),
                'ywcpos_order_total'    => __( 'Order Total', 'yith-woocommerce-pending-order-survey'),
            );

            return $columns;
        }

        public function column_default($item, $column_name)
        {
            $order = wc_get_order( $item );

            switch( $column_name ){

                case 'ywcpos_order_title' :
                    $action_edit_query_args = array(
                        'action' => 'edit',
                        'post' => $item
                    );

                    $action_edit_url = esc_url( add_query_arg( $action_edit_query_args, admin_url('post.php' ) ) );

                    $label =  __( 'View Order', 'yith-woocommerce-pending-order-survey' );
                    $actions            = array(
                        'edit'      => '<a href="' . $action_edit_url . '">' . $label . '</a>'
                    );

                    $user = $order->get_user();

                    if( $user )
                        $nice_name = $user->user_nicename;
                    else
                        $nice_name = is_a( $order, 'WC_Data' ) ? $order->get_billing_email() : $order->billing_email;

                    echo sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">#%d by %s </a></strong> %s', $action_edit_url, $label, $item, $nice_name, $this->row_actions( $actions ) );

                    break;
                case 'ywcpos_order_total';

                    if ( $order->get_total_refunded() > 0 ) {
                        $currency = is_a( $order, 'WC_Data' ) ? $order->get_currency() : $order->get_order_currency();
                        echo '<del>' . strip_tags( $order->get_formatted_order_total() ) . '</del> <ins>' . wc_price( $order->get_total() - $order->get_total_refunded(), array( 'currency' => $currency ) ) . '</ins>';
                    } else {
                        echo esc_html( strip_tags( $order->get_formatted_order_total() ) );
                    }

                    $payment_method_title = yit_get_prop( $order, 'payment_method_title' );
                    if ( $payment_method_title) {

                        echo '<small class="meta">' . __( 'Via', 'yith-woocommerce-pending-order-survey' ) . ' ' . esc_html( $payment_method_title ) . '</small>';
                    }

                    break;
            }
        }

        /**
         * get all Pending Email Template
         * @author YIThemes
         * @since 1.0.0
         * @return array
         */
        public function get_email_templates(){

            $default = array(
                'posts_per_page' => -1,
                'post_type' => 'ywcpos_survey_email',
                'post_status' => 'publish',
                'suppress_filter'   => false
            );

            $results = array();

            $query = new WP_Query( $default );
            if( $query->have_posts() ) {

                while( $query->have_posts() ) {

                    $query->the_post();
                    $results[] = $query->post->ID;
                }
            }

            wp_reset_query();
            wp_reset_postdata();

            return $results;
        }

        /**
         * prepare items
         * @author YIThemes
         * @since 1.0.0
         */
        public function prepare_items()
        {
            $per_page = 20;

            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );

            $current_page  = $this->get_pagenum();

            $query_args = array(
                'post_type' => 'shop_order',
                'post_status' => 'wc-completed',
                'post_parent'   => 0,
                'posts_per_page'  => $per_page,
                'paged' => $current_page,
                'suppress_filter'   => false,
                'meta_query'    => array(

                    array(
                        'key'   => '_ywcpos_is_pending',
                        'value' => 'yes',
                        'compare'   => 'LIKE'
                    )
                ),
            );


            $items = $this->get_orders( $query_args );

            $this->items = $items;
            $total_items = count( $this->items );

            /**
             * REQUIRED. We also have to register our pagination options & calculations.
             */
            $this->set_pagination_args(array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page' => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
            ));

        }

        /**
         * @param $params
         * @return array
         */
        public function get_orders( $params ){

            $results = array();

            $query = new WP_Query( $params );
            if( $query->have_posts() ) {

                while( $query->have_posts() ) {

                    $query->the_post();
                    $results[] = $query->post->ID;
                }
            }

            wp_reset_query();
            wp_reset_postdata();

            return $results;
        }


    }
}