<?php
if( !defined('ABSPATH') )
    exit;

if( !class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

if( !class_exists( 'YITH_WC_Pending_Order_Table' ) ){

    class YITH_WC_Pending_Order_Table extends  WP_List_Table{

        public function __construct()
        {
            parent::__construct( array(
                'singular' => _x('pending order', 'yith-woocommerce-pending-order-survey'),     //singular name of the listed records
                'plural' => _x('pending orders', 'yith-woocommerce-pending-order-survey'),    //plural name of the listed records
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
                'ywcpos_order_pending_from' => __( 'Pending order', 'yith-woocommerce-pending-order-survey'),
                'ywcpos_order_send_email'   => __( 'Send Email', 'yith-woocommerce-pending-order-survey')
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

                    $user_info = $order->get_user();
                    if( $user_info ){

                        $name = $user_info->user_nicename;
                    }
                    else{

                        $name = $order->get_formatted_billing_full_name();
                        if ( empty( $name ) ) {

                            $name = __( 'Guest', 'woocommerce' );
                        }
                    }
                    echo sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">#%d by %s </a></strong> %s', $action_edit_url, $label, $item, $name, $this->row_actions( $actions ) );

                    break;
                case 'ywcpos_order_total';

                    if ( $order->get_total_refunded() > 0 ) {
                        echo '<del>' . strip_tags( $order->get_formatted_order_total() ) . '</del> <ins>' . wc_price( $order->get_total() - $order->get_total_refunded(), array( 'currency' => $order->get_order_currency() ) ) . '</ins>';
                    } else {
                        echo esc_html( strip_tags( $order->get_formatted_order_total() ) );
                    }

                    $payment_method_title = yit_get_prop( $order, 'payment_method_title' );
                    if ( $payment_method_title) {

                        echo '<small class="meta">' . __( 'Via', 'yith-woocommerce-pending-order-survey' ) . ' ' . esc_html( $payment_method_title ) . '</small>';
                    }

                    break;
                case 'ywcpos_order_pending_from':

                    $gmt = is_a( $order, 'WC_Data' ) ?  1 : 0;
                    $current_time = current_time('timestamp', $gmt );

                    $date_pending = is_a( $order, 'WC_Data' ) ? $order->get_date_modified() : $order->post->post_modified;
                    $date_pending =  strtotime( $date_pending ) ;
                    echo sprintf('%s<br/>%s', __('Pending for','yith-woocommerce-pending-order-survey'), human_time_diff( $date_pending,
                        $current_time ) );
                    break;
                case 'ywcpos_order_send_email':
                    $html = '';
                    $templates = $this->get_email_templates();
                    if( ! empty( $templates ) ){
                        $select = '<select class="ywcpos_template_email">';
                        foreach( $templates as $em ){
                            $title = get_the_title( $em );
                            $select .= '<option value="'. $em .'">'.$title.'</option>';
                        }
                        $select.='</select>';
                        $html = $select .'<input type="button" id="ywcpos_sendemail" class="ywcpos_send_email button action"  value="' . __( 'Send email', 'yith-woocommerce-pending-order-survey' ) . '" data-order_id="'.$item.'">';

                    }else{
                        $html = __('Add a new email template', 'yith-woocommerce-pending-order-survey');
                    }

                    echo $html;
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
                'suppress_filter'   => false,
                'meta_query'    => array(
                    array(
                        'key' => '_ywcpos_enable_email',
                        'value' => '1',
                        'compare' => '='
                    )
                )
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
            $per_page = 15;

            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );

            $current_page  = $this->get_pagenum();

            $query_args = array(
                'post_type' => 'shop_order',
                'post_status' => 'wc-pending',
                'posts_per_page'  => $per_page,
                'post_parent'   => 0,
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

          //  usort( $items, array( $this, 'sort_by' ) );

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