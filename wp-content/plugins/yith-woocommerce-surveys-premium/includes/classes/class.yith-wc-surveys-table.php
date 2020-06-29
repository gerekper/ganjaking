<?php
if( !defined( 'ABSPATH' ) )
    exit;

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if( !class_exists( 'YITH_WC_Surveys_Table' ) ) {
    /**
     * Class YITH_WC_Surveys_Table
     *
     * array(
     *  array(
     *          'answer' => '',
     *          'tot_votes' => '',
     *          'visible_in' => '',
     *          'tot_order' => '',
     *          'order_details => '',
     *          'visible_in' => ''
     *
     *     )
     * )
     *
     * hidden column survey_id
     */
    class YITH_WC_Surveys_Table extends WP_List_Table
    {

       public $survey_id;

        public function __construct( $survey_id )
        {
            global $status, $page;
            parent::__construct(array(
                'singular' => _x('answer', 'yith-woocommerce-surveys'),     //singular name of the listed records
                'plural' => _x('answers', 'yith-woocommerce-surveys'),    //plural name of the listed records
                'ajax' => false        //does this table support ajax?
            ));

            $this->survey_id = $survey_id;
            do_action( 'yith_wc_surveys_table_init', $this );
        }

        /**
         * @author YIThemes
         * @since 1.0.0
         * @param object $item
         * @param string $column_name
         * @return mixed
         */
        public function column_default( $item, $column_name )
        {
            $visible_in = $item['visible_in'];
            switch( $column_name ) {

                case 'tot_votes' :
                    echo $item['tot_votes'];
                    break;
                case 'visible_in' :

                    switch( $visible_in ){
                        case 'checkout':
                            _e( 'Checkout', 'yith-woocomerce-surveys' );
                            break;
                        case 'product':
                            _e( 'Product', 'yith-woocommerce-surveys' );
                            break;
                        case 'other_page':
                            _e( 'Other Pages', 'yith-woocommerce-surveys' );
                            break;
                        }

                    break;
                case 'tot_order':

                    if( 'product' === $visible_in || 'other_page' === $visible_in ){
                        $class = 'hide';
                        $tip   = __( 'No Orders', 'yith-woocommece-surveys' );
                        return sprintf( '<mark class="%s tips" data-tip="%s">%s</mark>', $class, $tip, $tip );
                    }
                    else
                        echo wc_price( $item['tot_order'] );
                    break;
                case 'survey_id':
                    echo $item['survey_id'];
                    break;
                case 'order_details':

                    if( 'checkout' === $visible_in ) {
                        $view_orders = '<a href="#" class="survey_report_view_order_details">' . __('View Details', 'yith-woocommerce-surveys') . '</a>';
                        $list_orders = '<ul class="survey_report_show_details">';

                        $orders_id = $item['order_details'];

                        foreach ($orders_id as $order_id) {

                            $order = wc_get_order($order_id);
                            $order_query_args = array(
                                'post' => $order_id,
                                'action' => 'edit'
                            );
                            $order_url = esc_url(add_query_arg($order_query_args, admin_url('post.php')));

                            if( $order instanceof WC_Order ) {
	                            $user_query_args = array(
		                            'user_id' => $order->get_user_id(),
	                            );


	                            $user_url = esc_url( add_query_arg( $user_query_args, admin_url( 'user-edit.php' ) ) );
                            }
                            $user_info = sprintf('<a href="%s" target="_blank">%s</a><small class="meta mail"><a href="mailto:%s">%s</a></small>', $user_url, $order->get_formatted_billing_full_name(), $order->get_billing_email(), $order->get_billing_email());
                            $order_info = sprintf('<a class="tips" target="_blank" href="%s" data-tip="%s">#%d </a>by %s', $order_url, __('View order', 'yith-woocommerce-surveys'), $order_id, $user_info);

                            $div = '<div>' . $order_info . '</div>';

                            $list_orders .= '<li>' . $div . '</li>';
                        }

                        $list_orders .= '</ul>';
                        echo $view_orders . $list_orders;
                    }
                    else
                        _e( 'No Details', 'yith-woocommerce-surveys' );
                    break;

            }
        }

        /** get column answer
         * @author YIThemes
         * @since 1.0.0
         * @param $item
         * @return string
         */
        public function column_answer($item)
        {

            return sprintf('<strong>%s</strong>', $item['answer']);
        }

        /**
         * get columns
         * @author YIThemes
         * @since 1.0.0
         * @return array
         */
        public function get_columns()
        {
            $columns = array(
                'answer' => __( 'Answer', 'yith-woocommece-surveys' ),
                'tot_votes' => __( 'Votes', 'yith-woocommece-surveys' ),
                'visible_in' => __( 'Visible in', 'yith-woocommece-surveys' ),
                'tot_order' => __( 'Order totals', 'yith-woocommece-surveys' ),
                'order_details' => __( 'Order Details', 'yith-woocommece-surveys' )
            );
            return $columns;
        }

        public function get_hidden_columns()
        {

            $columns = array( 'survey_id' => _x( 'Survey ID', 'yith-woocommerce-surveys' ) );

            return $columns;
        }

        /** get sortable columns
         * @author YIThemes
         * @since 1.0.0
         * @return array
         */
        public function get_sortable_columns()
        {
            $sortable_columns = array(
                'answer' => array( 'answer', false ),
                'tot_votes' => array( 'tot_votes', false ),
                'tot_order' => array( 'tot_order', false ) //true means it's already sorted
            );
            return $sortable_columns;
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
            $hidden = $this->get_hidden_columns();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );


            $items = $this->generate_data_for_survey( $this->survey_id);
          //  $items = YITH_WC_Surveys_Utility::generate_data();

            usort( $items, array( &$this, 'ywcsurv_usort_reorder' ) );

            //filter by type
           // $items = $this->filter_by_type( $items );

            //filter by survey name
         //   $items = $this->filter_by_name( $items );

            $current_page = $this->get_pagenum();
            $total_items = count( $items );

            $items = array_slice( $items, ( ( $current_page - 1 ) * $per_page ), $per_page );


            /**
             * REQUIRED. Now we can add our *sorted* data to the items property, where
             * it can be used by the rest of the class.
             */
            $this->items = $items;


            /**
             * REQUIRED. We also have to register our pagination options & calculations.
             */
            $this->set_pagination_args(array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page' => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items / $per_page)   //WE have to calculate the total number of pages
            ));


           }

        /**
         * sort table
         * @author YIThemes
         * @since 1.0.0
         * @param $a
         * @param $b
         * @return int
         */
        public function ywcsurv_usort_reorder($a, $b)
        {

            $orderby = ( !empty($_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'tot_votes'; //If no sort, default to category name
            $order = ( !empty($_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc

            if ($orderby == 'answer')
                $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            else {
                if ($a[$orderby] < $b[$orderby])
                    $result = -1;
                elseif ($a[$orderby] > $b[$orderby])
                    $result = 1;
                else
                    $result = 0;
            }

            return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
        }


        /**
         * Generate the table navigation above or below the table
         *
         * @since 3.1.0
         * @access protected
         * @param string $which
         */
        protected function display_tablenav( $which ) {
            if ( 'top' == $which )
                wp_nonce_field( 'bulk-' . $this->_args['plural'], "_wpnonce", false );
            ?>
            <div class="tablenav <?php echo esc_attr( $which ); ?>">

                <div class="alignleft actions bulkactions">
                    <?php $this->bulk_actions( $which ); ?>
                </div>
                <?php
                $this->extra_tablenav( $which );
                $this->pagination( $which );
                ?>

                <br class="clear" />
            </div>
        <?php
        }

        /**
         * print dropdown filters
         * @author YIThemes
         * @since 1.0.0
         * @param string $which
         */
      /*  protected function extra_tablenav($which)
        {

            if ( 'top' == $which) {

                echo '<div class="alignleft actions">';
                $this->get_dropdown_survey_type();
                $this->get_dropdown_survey_name();
                submit_button(__('Filter'), 'button', 'filter_action', false, array('id' => 'post-query-submit'));
                echo '</div>';

            }


        }
*/

        /**
         * print survey type filter
         * @author YIThemes
         * @since 1.0.0
         */
        private function get_dropdown_survey_type()
        {
            $visible_option = array(
                'checkout' => __( 'WooCommerce Checkout', 'yith-woocommerce-surveys' ),
                'product' => __( 'WooCommerce Product', 'yith-woocommerce-surveys' ),
                'other_page' => __( 'Other Pages', 'yith-woocommerce-surveys' )
            );

            $current_filter = isset( $_GET['survey_type'] ) ? $_GET['survey_type'] : '';
            ?>
            <label class="screen-reader-text" for="filter-by-survey-type"><?php _e( 'Filter by survey type', 'yith-woocommerce-surveys' ); ?></label>
            <select id="filter-by-survey-type" name="survey_type">
                <option value="" <?php selected( $current_filter, '' );?> ><?php _e( 'All survey types','yith-woocommerce-surveys' ); ?></option>
                <?php foreach( $visible_option as $key => $value ): ?>
                    <option
                        value="<?php esc_attr_e($key); ?>" <?php selected($current_filter, $key); ?>><?php echo $value; ?></option>
                <?php endforeach;?>
            </select>
        <?php

        }

        /**
         * print survey type filter
         * @author YIThemes
         * @since 1.0.0
         */
        private function get_dropdown_survey_name()
        {
            $survey_ids = YITH_Surveys_Type()->get_surveys();

            $current_filter = isset( $_GET['survey_name'] ) ? $_GET['survey_name'] : '';
            ?>
            <label class="screen-reader-text" for="filter-by-survey-name"><?php _e( 'Filter by survey','yith-woocommerce-surveys' ); ?></label>
            <select id="filter-by-survey-name" name="survey_name">
                <option value="" <?php selected($current_filter, '');?> ><?php _e( 'All surveys', 'yith-woocommerce-surveys' ); ?></option>
                <?php foreach( $survey_ids as $survey_id ):
                    $value = get_the_title( $survey_id );?>
                    <option
                        value="<?php esc_attr_e( $survey_id ); ?>" <?php selected( $current_filter, $survey_id ); ?>><?php echo $value; ?></option>
                <?php endforeach;?>
            </select>
        <?php

        }

        /**
         * filter by survey type
         * @author YIThemes
         * @since 1.0.0
         * @param $items
         * @return array
         */
        private function filter_by_type( $items )
        {

            if ( isset( $_GET['survey_type'] ) && $_GET['survey_type'] != '' ) {

                $index = 'visible_in';
                $value = $_GET['survey_type'];

                $new_items = array();

                foreach( array_keys( $items ) as $key ) {

                    $temp[$key] = $items[$key][$index];

                    if ( $temp[$key] == $value ) {
                        $new_items[$key] = $items[$key];

                    }
                }

                return $new_items;
            }

            return $items;

        }

        /**
         * filter by survey name
         * @author YIThemes
         * @since 1.0.0
         * @param $items
         * @return array
         */
        private function filter_by_name( $items )
        {


            if ( $this->survey_id != '' ) {

                $index = 'survey_id';


                $new_items = array();

                foreach( array_keys( $items ) as $key ) {

                    $temp[$key] = $items[$key][$index];

                    if ( $temp[$key] == $this->survey_id ) {
                        $new_items[$key] = $items[$key];

                    }
                }

                return $new_items;
            }

            return $items;

        }

	    public function generate_data_for_survey( $survey_id ) {

		    $items         = YITH_WC_Surveys_Utility::get_items( $survey_id );

		    return $items;

	    }


    }
}