<?php
if( !defined( 'ABSPATH' ) )
    exit;

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


if( !class_exists( 'YITH_WC_Surveys_List_Table' ) ){

    class YITH_WC_Surveys_List_Table extends WP_List_Table
    {

        public function __construct()
        {
            global $status, $page;
            parent::__construct(array(
                'singular' => _x('survey', 'yith-woocommerce-surveys'),     //singular name of the listed records
                'plural' => _x('surveys', 'yith-woocommerce-surveys'),    //plural name of the listed records
                'ajax' => false        //does this table support ajax?
            ));
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
                'title' => __( 'Survey', 'yith-woocommece-surveys' ),
                'n_items'=> __( 'Items', 'yith-woocommerce-surveys' ),
                'survey_type' => __( 'Type', 'yith-woocommerce-surveys' ),
                'date'  => __( 'Date', 'yith-woocommerce-surveys')
            );

            return $columns;
        }

        /**
         * @author YIThemes
         * @since 1.0.0
         * @param object $item
         * @param string $column_name
         * @return mixed
         */
        public function column_default( $item, $column_name ){

            switch( $column_name ){

                case 'n_items' :

                    $n_item = count( YITH_Surveys_Type()->get_survey_children( array( 'post_parent' => $item ) )  );
                    echo $n_item;
                    break;
                case 'survey_type':
                    $type = get_post_meta( $item, '_yith_survey_visible_in', true );
                    echo $type;
                    break;

                case 'date' :

                    echo get_the_date( '', $item );
                    break;
            }

        }

        public function column_title( $item )
        {
            $view_result_query_args = array(
                'page'    => $_GET['page'],
                'action'  => 'view_result',
                'survey_id'      => $item
            );

            $view_url   =   esc_url( add_query_arg( $view_result_query_args, admin_url( 'edit.php?post_type=yith_wc_surveys&page=survey-report' ) ) );

            $actions = array(
                'view_result'    => '<a href="' . $view_url . '">' . __( 'View results', 'yith-woocommece-surveys' ) . '</a>',
            );

            $title = get_the_title( $item );
            return sprintf( '<strong><a class="tips" href="%s" data-tip="%s">%s</a></strong> %s',
                $view_url, __( 'View result', 'yith-woocommece-surveys' ), $title, $this->row_actions( $actions ) );
        }

        /** get sortable columns
         * @author YIThemes
         * @since 1.0.0
         * @return array
         */
        public function get_sortable_columns()
        {
            $sortable_columns = array(
                'title' => array( 'title', false ), //true means it's already sorted
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
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );


            $current_page = $this->get_pagenum();


            $query_args = array(
                'per_page' => 20,
                'paged'               => $current_page,

            );

            $items = YITH_Surveys_Type()->get_surveys( $query_args );

            usort( $items, array( &$this, 'ywcsurv_usort_reorder' ) );

            $this->items  = $items;

            $total_items = count( $this->items );

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

            $orderby = ( !empty($_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'title'; //If no sort, default to survey title
            $order = ( !empty($_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc

            $title_a = get_the_title( $a );
            $title_b = get_the_title( $b );
            $result = strcmp( $title_a, $title_b ); //Determine sort order


            return ($order === 'asc') ? $result : -$result; //Send final sort direction to usort
        }
    }
}