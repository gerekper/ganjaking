<?php

// Integrate WP List Table for viewing Referral Table

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ) ;
}

class WP_List_Table_for_View_Gift_Voucher extends WP_List_Table {

    // Prepare Items
    public function prepare_items() {
        $columns  = $this->get_columns() ;
        $hidden   = $this->get_hidden_columns() ;
        $sortable = $this->get_sortable_columns() ;

        $data   = $this->table_data() ;
        $user   = get_current_user_id() ;
        $screen = get_current_screen() ;
        if ( isset( $_REQUEST[ 's' ] ) ) {
            $searchvalue = $_REQUEST[ 's' ] ;
            $keyword     = "/$searchvalue/" ;
            $newdata     = array() ;
            $data        = $this->get_data_of_searched_code( $keyword ) ;
            foreach ( $data as $eacharray => $value ) {
                $searchfunction = preg_grep( $keyword , $value ) ;
                if ( ! empty( $searchfunction ) ) {
                    $newdata[] = $data[ $eacharray ] ;
                }
            }
            usort( $newdata , array( &$this , 'sort_data' ) ) ;

            $perPage     = RSTabManagement::rs_get_value_for_no_of_item_perpage( $user , $screen ) ;
            $currentPage = $this->get_pagenum() ;
            $totalItems  = count( $newdata ) ;

            $this->set_pagination_args( array(
                'total_items' => $totalItems ,
                'per_page'    => $perPage
            ) ) ;

            $newdata = array_slice( $newdata , (($currentPage - 1) * $perPage ) , $perPage ) ;

            $this->_column_headers = array( $columns , $hidden , $sortable ) ;

            $this->items = $newdata ;
        } else {
            usort( $data , array( &$this , 'sort_data' ) ) ;
            $perPage     = RSTabManagement::rs_get_value_for_no_of_item_perpage( $user , $screen ) ;
            $currentPage = $this->get_pagenum() ;
            $totalItems  = count( $data ) ;

            $this->set_pagination_args( array(
                'total_items' => $totalItems ,
                'per_page'    => $perPage
            ) ) ;

            $data = array_slice( $data , (($currentPage - 1) * $perPage ) , $perPage ) ;

            $this->_column_headers = array( $columns , $hidden , $sortable ) ;

            $this->items = $data ;
        }
    }

    public function get_columns() {
        $columns = array(
            'sno'          => __( 'S.No' , SRP_LOCALE ) ,
            'voucher_code' => __( 'Voucher Code' , SRP_LOCALE ) ,
            'username'     => __( 'UserName' , SRP_LOCALE ) ,
        ) ;

        return $columns ;
    }

    public function get_hidden_columns() {
        return array() ;
    }

    public function get_sortable_columns() {
        return array( 'username' => array( 'username' , false ) ,
            'sno'      => array( 'sno' , false ) ,
            'username' => array( 'username' , false ) ,
        ) ;
    }

    public function get_data_of_searched_code( $keyword ) {
        global $wpdb ;
        $table_name = $wpdb->prefix . 'rsgiftvoucher' ;
        $subdata    = $wpdb->get_results( "SELECT memberused FROM $table_name WHERE vouchercode = '$keyword'" , ARRAY_A ) ;
        $subdata    = $subdata[ 0 ][ 'memberused' ] != '' ? unserialize( $subdata[ 0 ][ 'memberused' ] ) : array() ;
        $i          = 1 ;
        if ( is_array( $subdata ) && ! empty( $subdata ) ) {
            foreach ( $subdata as $key ) {
                $getuserbyid = get_user_by( 'id' , $key ) ;
                if ( is_object( $getuserbyid ) ) {
                    $data[] = array(
                        'sno'          => $i ,
                        'voucher_code' => $keyword ,
                        'username'     => $getuserbyid->user_login != '' ? $getuserbyid->user_login : '-' ,
                    ) ;
                    $i ++ ;
                }
            }
        }
        return $data ;
    }

    private function table_data() {
        $data         = array() ;
        $i            = 1 ;
        global $wpdb ;
        $table_name   = $wpdb->prefix . 'rsgiftvoucher' ;
        $voucher_code = $_GET[ 'vouchercode' ] ;
        $subdata      = $wpdb->get_results( "SELECT memberused FROM $table_name WHERE vouchercode = '$voucher_code'" , ARRAY_A ) ;
        $datas        = $subdata[ 0 ][ 'memberused' ] != '' ? unserialize( $subdata[ 0 ][ 'memberused' ] ) : array() ;
        if ( is_array( $datas ) ) {
            foreach ( $datas as $key ) {
                $getuserbyid = get_user_by( 'id' , $key ) ;
                if ( is_object( $getuserbyid ) ) {
                    $data[] = array(
                        'sno'          => $i ,
                        'voucher_code' => $voucher_code ,
                        'username'     => $getuserbyid->user_login != '' ? $getuserbyid->user_login : '-' ,
                    ) ;
                    $i ++ ;
                }
            }
        }
        return $data ;
    }

    public function column_id( $item ) {
        return $item[ 'sno' ] ;
    }

    public function column_default( $item , $column_name ) {
        switch ( $column_name ) {
            case 'sno':
            case 'voucher_code':
            case 'username':

                return $item[ $column_name ] ;
            default:
                return print_r( $item , true ) ;
        }
    }

    private function sort_data( $a , $b ) {

        $orderby = 'sno' ;
        $order   = 'asc' ;

        if ( ! empty( $_GET[ 'orderby' ] ) ) {
            $orderby = $_GET[ 'orderby' ] ;
        }

        if ( ! empty( $_GET[ 'order' ] ) ) {
            $order = $_GET[ 'order' ] ;
        }

        $result = strnatcmp( $a[ $orderby ] , $b[ $orderby ] ) ;

        if ( $order === 'asc' ) {
            return $result ;
        }

        return -$result ;
    }

}
