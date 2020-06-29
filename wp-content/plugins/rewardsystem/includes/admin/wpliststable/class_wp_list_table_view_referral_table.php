<?php

// Integrate WP List Table for viewing Referral Table

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ) ;
}

class WP_List_Table_for_View_Referral_Table extends WP_List_Table {

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

            $newdata = array() ;
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
            'sno'             => __( 'S.No' , SRP_LOCALE ) ,
            'referral_name'   => __( 'Referral Username' , SRP_LOCALE ) ,
            'referral_email'  => __( 'Referral Email' , SRP_LOCALE ) ,
            'referral_points' => __( 'Total Referral Points' , SRP_LOCALE ) ,
                ) ;

        return $columns ;
    }

    public function get_hidden_columns() {
        return array() ;
    }

    public function get_sortable_columns() {
        return array( 'referral_name'   => array( 'referral_name' , false ) ,
            'sno'             => array( 'sno' , false ) ,
            'referral_points' => array( 'referral_points' , false ) ,
                ) ;
    }

    private function table_data() {
        $data     = array() ;
        $i        = 1 ;
        $subdatas = RS_Referral_Log::corresponding_referral_log( $_GET[ 'view' ] ) ;
        if ( ! srp_check_is_array( $subdatas ) )
            return $data ;

        foreach ( $subdatas as $key => $values ) {
            $values      = RSMemberFunction::earn_points_percentage( $_GET[ 'view' ] , ( float ) $values ) ;
            $getuserbyid = get_user_by( 'id' , $key ) ;
            $data[]      = array(
                'sno'             => $i ,
                'referral_name'   => (is_object( $getuserbyid ) && ! empty( $getuserbyid->user_login )) ? $getuserbyid->user_login : 'Guest' ,
                'referral_email'  => (is_object( $getuserbyid ) && ! empty( $getuserbyid->user_email )) ? $getuserbyid->user_email : $key ,
                'referral_points' => round_off_type( $values ) ,
                    ) ;
            $i ++ ;
        }
        return $data ;
    }

    public function column_id( $item ) {
        return $item[ 'sno' ] ;
    }

    public function column_default( $item , $column_name ) {
        switch ( $column_name ) {
            case 'sno':
            case 'referral_name':
            case 'referral_email':
            case 'referral_points':
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
