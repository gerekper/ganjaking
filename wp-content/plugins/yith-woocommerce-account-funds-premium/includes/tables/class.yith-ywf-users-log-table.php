<?php
if( !defined('ABSPATH')){
    exit;
}

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if( !class_exists('YITH_YWF_Users_Log_Table' ) ){

    class YITH_YWF_Users_Log_Table extends WP_List_Table{

    	protected $user_id;
        public function __construct( $user_id ) {

        	$this->user_id = $user_id;
            parent::__construct( array(
                'singular' => _x( 'user log', 'yith-woocommerce-account-funds' ),     //singular name of the listed records
                'plural' => _x( 'user logs', 'yith-woocommerce-account-funds' ),    //plural name of the listed records
                'ajax' => false        //does this table support ajax?
            ) );

        }

        public function get_columns() {

            $columns_user = array();

            $columns = array(
                'date_mov' => __('Date', 'yith-woocommerce-account-funds'),
                'desc' => __('Description', 'yith-woocommerce-account-funds'),
                'user_mov' =>__('Transaction','yith-woocommerce-account-funds'),
	            'editor_name' => _x( 'Funds edited by','Who changed the funds','yith-woocommerce-account-funds')

            );

            return array_merge( $columns_user,$columns );
        }

        /** get sortable columns
         * @author YITHEMES
         * @since 1.0.0
         * @return array
         */
        public function get_sortable_columns()
        {
            $sortable_columns = array(
                'user_name' => array( 'user_name',true ),
                'date_mov' => array( 'date_mov' , true )
            );
            return $sortable_columns;
        }

        public function column_default( $item, $column_name ) {

            /** @var WP_User $user*/
            $column_value = '';

            switch( $column_name ){

                case 'user_mov':
                    $class_fund = $item->fund_user >= 0 ? 'positive' : 'negative';
                    $column_value = sprintf('<span class="ywf_user_mov %s">%s</span>',$class_fund,wc_price( $item->fund_user ) );
                    break;
                case 'desc':
                    $type_op = $item->type_operation;

                    $order_id = $item->order_id;
                    $order_url  = '';
                    if( $order_id!=='' ){

                        $order_url = get_edit_post_link( $order_id );
                        $order_url = sprintf('<a href="%s" target="_blank">#%s</a>', $order_url, $order_id );
                    }
                    
                    switch ( $type_op ){
                        
                        case 'pay':
                            $column_value = sprintf('<span class="ywf_desc_log">%s %s</span>', __('Funds used in order','yith-woocommerce-account-funds'), $order_url );
                            break;
                        case 'deposit':
                            $column_value = sprintf('<span class="ywf_desc_log">%s %s</span>', __('Funds deposited through order','yith-woocommerce-account-funds'), $order_url );
                            break;
                        case 'restore':
                            $column_value = sprintf('<span class="ywf_desc_log">%s %s</span>', __('Funds restored for the order','yith-woocommerce-account-funds'), $order_url );
                            break;
                        case 'remove':
                            $column_value = sprintf('<span class="ywf_desc_log">%s %s</span>', __('Funds refunded for the order','yith-woocommerce-account-funds'), $order_url );
                            break;
                       default:
                            $column_value = sprintf('<span class="ywf_desc_log">%s</span>', $item->description );
                            break;
                    }

                    break;
                case 'date_mov' :
                    $date = date( get_option( 'date_format' ), strtotime($item->date_added));
                    $column_value = sprintf('<p>%s</p>', $date );
                    break;

                    case 'editor_name';

                     $column_value = _x( 'N/A', 'Not available','yith-woocommerce-account-funds' );

                     if( $item->editor_id > 0 ){

                     	$user = get_user_by( 'id', $item->editor_id );

                     	$column_value = $user->display_name;
                     }
                    break;
            }

            echo $column_value;
        }

        public function prepare_items() {

            $per_page = 15;
            $columns = $this->get_columns();
            $hidden = array();
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );

            $current_page = $this->get_pagenum();
            $offset = ( $current_page - 1 ) * $per_page;
            
            $query_args = array(
                'limit' => $per_page,
                'offset' => $offset,
            );

          if(  isset( $this->user_id ) ){

              $query_args['user_id'] = $this->user_id ;
              $total_items = YWF_Log()->count_log( array('user_id' => $this->user_id  ) ) ;

          }
            $items = YWF_Log()->get_log( $query_args );
            @usort( $items, array( $this, 'sort_by' ) );
            
            $this->items = $items;


            /**
             * REQUIRED. We also have to register our pagination options & calculations.
             */
            $this->set_pagination_args( array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page' => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
            ) );
        }

        /**
         * sort items
         * @author YITHEMES
         * @since 1.0.0
         * @param $a
         * @param $b
         * @return int
         */
        public function sort_by( $a, $b )
        {

            $orderby = ( !empty( $_REQUEST[ 'orderby' ] ) ) ? $_REQUEST[ 'orderby' ] : 'date_mov'; //If no sort, default to priority rule
            $order = ( !empty( $_REQUEST[ 'order' ] ) ) ? $_REQUEST[ 'order' ] : 'desc'; //If no order, default to desc

            if ( $orderby == 'user_name' ) {

                $user1 = get_user_by('id', $a->user_id );
                $user2 = get_user_by('id', $b->user_id );
                $n1 = $user1->display_name;
                $n2 = $user2->display_name;

                $result = strcmp( $n1, $n2 );
            } elseif ( $orderby === 'date_mov' ) {

                $pa =  $a->date_added;
                $pb =  $b->date_added;

                $date1 = strtotime( $pa);
                $date2 = strtotime( $pb );

                if ( $date1 < $date2 )
                    $result = -1;
                else if ( $date1 > $date2 )
                    $result = 1;
                else
                    $result = 0;
            }

            return $order === 'asc' ? $result : -$result;
        }

    }
}