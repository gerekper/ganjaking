<?php
if( !defined( 'ABSPATH' ) )
    exit;

if( !class_exists( 'WP_List_Table' ) )
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

if( !class_exists( 'YITH_WC_Pending_Survey_Table' ) ){

    class YITH_WC_Pending_Survey_Table extends  WP_List_Table{

        private $post_type;
        private $post_author;

        /**
         * YITH_WC_Pending_Survey_Table constructor.
         */
        public function __construct()
        {

            parent::__construct( array(
                'singular' => _x('pending order survey', 'yith-woocommerce-pending-order-survey'),     //singular name of the listed records
                'plural' => _x('pending order surveys', 'yith-woocommerce-pending-order-survey'),    //plural name of the listed records
                'ajax' => false        //does this table support ajax?
            ));

            $this->post_type = YITH_Pending_Order_Survey_Type()->post_type_name;
            $this->post_author = apply_filters( 'ywcpos_post_author', -1 );
        }

        /**
         * @return array
         */
        public function get_columns()
        {
           $columns = array(
             'cb' => '<input type="checkbox"/>',
             'post_title' => __( 'Title','yith-woocommerce-pending-order-survey' ),
             'post_author' => __( 'Author', 'yith-woocommerce-pending-order-survey' ),
             'number_response' => __('Number of surveys responded', 'yith-woocommerce-pending-order-survey'),
             'post_date'  => __( 'Date','yith-woocommerce-pending-order-survey' )
           );

            return $columns;
        }

        /**
         * @param object $item
         * @param string $column_name
         */
        public function column_default( $item, $column_name ){


            switch( $column_name ){

                case 'post_title':

                    $action_edit_query_args = array(
                        'action' => 'edit',
                        'post' => $item->ID
                    );

                    $action_edit_url = esc_url( add_query_arg( $action_edit_query_args, admin_url('post.php' ) ) );

                    $actions            = array(
                        'edit'      => '<a href="' . $action_edit_url . '">' . __( 'Edit', 'yith-woocommerce-pending-order-survey' ) . '</a>'
                    );

                    echo sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">#%d %s </a></strong> %s', $action_edit_url, __( 'Edit','yith-woocommerce-pending-order-survey' ), $item->ID, $item->post_title, $this->row_actions( $actions ) );

                    break;

                case 'post_author':

                    $author_query_args = array(
                        'user_id' => $item->post_author,
                    );

                    $user_link = esc_url( add_query_arg( $author_query_args, admin_url('user-edit.php' ) ) );
                    $user = get_user_by( 'id',$item->post_author );

                    echo sprintf( '<a href="%s" target="_blank">%s</a>', $user_link, $user->user_nicename );

                    break;

                case 'number_response':

                    $tot_anw = get_post_meta( $item->ID, '_ywcpos_tot_answ', true );
                    $tot_anw = empty( $tot_anw ) ? 0 : $tot_anw;

                    echo $tot_anw;
                    break;

                case 'post_date':

                    echo $item->post_modified;
                    break;

            }
        }

        /**
         * @return array|false|string
         */
        public function get_bulk_actions() {

            $actions = $this->current_action();

            if( isset( $_REQUEST['ywcpos_survey_ids'] ) ){

                $pending_surveys = $_REQUEST['ywcpos_survey_ids'];

                if( $actions == 'delete' ){
                        foreach ( $pending_surveys as $pending_survey_id ) {
                            wp_delete_post( $pending_survey_id, true );
                        }
                    }

                    $this->prepare_items();
                }

            $actions = array(
                'delete'    => __('Delete', 'yith-woocommerce-pending-order-survey')
            );

            return $actions;
        }

        /**
         * @param object $item
         * @return string
         */
        public function column_cb($item) {
            return sprintf(
                '<input type="checkbox" name="ywcpos_survey_ids[]" value="%s" />',  $item->ID
            );
        }
        /** get sortable columns
         * @author YIThemes
         * @since 1.0.0
         * @return array
         */
        public function get_sortable_columns()
        {
            $sortable_columns = array(
                'post_title' => array( 'post_title', false ),
                'post_date' => array( 'post_date', true ),
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
            $per_page = 15;

            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );

            $current_page  = $this->get_pagenum();

            $query_args = array(
                'post_type' => $this->post_type,
                'post_status' => 'publish',
                'orderby' => 'modified',
                'order'   => 'DESC',
                'per_page'  => $per_page,
                'paged' => $current_page,
                'suppress_filter'   => false
            );

            if( $this->post_author !== -1 )
                $query_args['author'] = $this->post_author;

            $items = get_posts( $query_args );

            usort( $items, array( $this, 'sort_by' ) );

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

        public function sort_by( $a, $b ){

            $orderby = ( !empty($_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'post_date'; //If no sort, default to survey title
            $order = ( !empty($_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc

            if( $orderby === 'post_date' ){

                $date1 = strtotime( $a->post_modified );
                $date2 = strtotime( $b->post_modified );

                if( $date1<$date2 )
                    $result = -1;
                else if( $date1 > $date2 )
                    $result = 1;
                else
                    $result = 0;
            }else{

                $result = strcmp( $a->post_title, $b->post_title );
            }

            return $order === 'asc' ? $result : -$result;
        }
    }
}