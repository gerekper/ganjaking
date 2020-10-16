<?php

// Integrate WP List Table for Master Log

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ) ;
}

class WP_List_Table_for_Master_Log extends WP_List_Table {

    // Prepare Items
    public function prepare_items() {
        global $wpdb ;
        $columns  = $this->get_columns() ;
        $hidden   = $this->get_hidden_columns() ;
        $sortable = $this->get_sortable_columns() ;

        $user        = get_current_user_id() ;
        $screen      = get_current_screen() ;
        $perPage     = RSTabManagement::rs_get_value_for_no_of_item_perpage( $user , $screen ) ;
        $currentPage = $this->get_pagenum() ;
        $startpoint  = ($currentPage - 1) * $perPage ;
        $userprefix  = $wpdb->prefix . "rsrecordpoints" ;
        $num_rows    = $wpdb->get_var( "SELECT COUNT(*) FROM $userprefix WHERE showmasterlog = false and  userid NOT IN(0)" ) ;
        $data        = $this->table_data( $startpoint , $perPage ) ;

        if ( isset( $_REQUEST[ 's' ] ) ) {
            $searchvalue = $_REQUEST[ 's' ] ;
            $userobject  = get_user_by( 'login' , "$searchvalue" ) ;
            if ( ! empty( $userobject ) ) {
                $mydata  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $userprefix WHERE userid = %d" , $userobject->ID ) , ARRAY_A ) ;
                $newdata = $this->get_user_data_for_master_log( $mydata ) ;

                usort( $newdata , array( &$this , 'sort_data' ) ) ;

                $this->_column_headers = array( $columns , $hidden , $sortable ) ;
                $this->items           = $newdata ;
            }
        } else {
            usort( $data , array( &$this , 'sort_data' ) ) ;

            $this->set_pagination_args( array(
                'total_items' => $num_rows ,
                'per_page'    => $perPage
            ) ) ;

            $this->_column_headers = array( $columns , $hidden , $sortable ) ;
            $this->items           = $data ;
        }
    }

    public function get_user_data_for_master_log( $subdatas ) {
        $data = array() ;
        if ( ! srp_check_is_array( $subdatas ) )
            return $data ;

        $i         = 1 ;
        $eventname = '' ;
        foreach ( $subdatas as $values ) {
            $getuserbyid = get_user_by( 'id' , @$values[ 'userid' ] ) ;
            if ( isset( $values[ 'earnedpoints' ] ) && ! empty( $values[ 'earnedpoints' ] ) ) {
                $total = round_off_type( $values[ 'earnedpoints' ] ) ;
            } elseif ( isset( $values[ 'totalvalue' ] ) ) {
                $total = round_off_type( $values[ 'totalvalue' ] ) ;
            }

            if ( $values != '' ) {
                if ( isset( $values[ 'earnedpoints' ] ) ) {
                    $refuserid    = get_user_meta( $values[ 'refuserid' ] , 'nickname' , true ) ;
                    $nomineeid    = get_user_meta( $values[ 'nomineeid' ] , 'nickname' , true ) ;
                    $usernickname = get_user_meta( $values[ 'userid' ] , 'nickname' , true ) ;
                    $eventname    = RSPointExpiry::msg_for_log( false , true , true , $values[ 'earnedpoints' ] , $values[ 'checkpoints' ] , $values[ 'productid' ] , $values[ 'orderid' ] , $values[ 'variationid' ] , $values[ 'userid' ] , $refuserid , $values[ 'reasonindetail' ] , $values[ 'redeempoints' ] , true , $nomineeid , $usernickname , $values[ 'nomineepoints' ] ) ;
                    $total        = ! empty( $total ) ? $total : $values[ 'redeempoints' ] ;
                } else {
                    if ( get_option( 'rsoveralllog' ) != '' ) {
                        $eventname              = $values[ 'eventname' ] ;
                        $values[ 'earneddate' ] = $values[ 'date' ] ;
                    }
                }

                $data[] = array(
                    'sno'         => $i ,
                    'user_name'   => $getuserbyid->user_login ,
                    'points'      => round_off_type( $total ) ,
                    'event'       => $eventname == '' ? '-' : $eventname ,
                    'date'        => date_display_format( $values[ 'earneddate' ] ) ,
                    'expiry_date' => 999999999999 != $values[ 'expirydate' ] ? date_display_format( $values[ 'expirydate' ] ) : '-' ,
                        ) ;
                $i ++ ;
            }
        }
        return $data ;
    }

    public function get_columns() {
        $columns = array(
            'sno'         => __( 'S.No' , SRP_LOCALE ) ,
            'user_name'   => __( 'Username' , SRP_LOCALE ) ,
            'points'      => __( 'Points' , SRP_LOCALE ) ,
            'event'       => __( 'Event' , SRP_LOCALE ) ,
            'date'        => __( "Earned/Redeemed Date" , SRP_LOCALE ) ,
            'expiry_date' => __( 'Expiry Date' , SRP_LOCALE ) ,
                ) ;

        return $columns ;
    }

    public function get_hidden_columns() {
        return array() ;
    }

    public function get_sortable_columns() {
        return array(
            'points'      => array( 'points' , false ) ,
            'sno'         => array( 'sno' , false ) ,
            'date'        => array( 'date' , false ) ,
            'expiry_date' => array( 'expiry_date' , false ) ,
                ) ;
    }

    private function table_data( $startpoint , $perPage ) {
        global $wpdb ;
        $table_name = $wpdb->prefix . 'rsrecordpoints' ;
        $data       = array() ;
        $subdatas   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE showmasterlog = false ORDER BY ID DESC LIMIT %d,%d" , $startpoint , $perPage ) , ARRAY_A ) ;
        $subdatas   = $subdatas + ( array ) get_option( 'rsoveralllog' ) ;
        if ( ! srp_check_is_array( $subdatas ) )
            return $data ;

        $i         = $startpoint + 1 ;
        $eventname = '' ;
        foreach ( $subdatas as $values ) {
            $getuserbyid = get_user_by( 'id' , @$values[ 'userid' ] ) ;
            if ( isset( $values[ 'earnedpoints' ] ) && ! empty( $values[ 'earnedpoints' ] ) ) {
                $total = round_off_type( $values[ 'earnedpoints' ] ) ;
            } elseif ( isset( $values[ 'totalvalue' ] ) ) {
                $total = round_off_type( $values[ 'totalvalue' ] ) ;
            }

            if ( $values != '' ) {
                if ( isset( $values[ 'earnedpoints' ] ) ) {
                    $refuserid    = get_user_meta( $values[ 'refuserid' ] , 'nickname' , true ) ;
                    $nomineeid    = get_user_meta( $values[ 'nomineeid' ] , 'nickname' , true ) ;
                    $usernickname = get_user_meta( $values[ 'userid' ] , 'nickname' , true ) ;
                    $eventname    = RSPointExpiry::msg_for_log( false , true , true , $values[ 'earnedpoints' ] , $values[ 'checkpoints' ] , $values[ 'productid' ] , $values[ 'orderid' ] , $values[ 'variationid' ] , $values[ 'userid' ] , $refuserid , $values[ 'reasonindetail' ] , $values[ 'redeempoints' ] , true , $nomineeid , $usernickname , $values[ 'nomineepoints' ] ) ;
                    $total        = ! empty( $values[ 'redeempoints' ] ) ? $values[ 'redeempoints' ] : $total ;
                } else {
                    if ( get_option( 'rsoveralllog' ) != '' ) {
                        $eventname              = $values[ 'eventname' ] ;
                        $values[ 'earneddate' ] = $values[ 'date' ] ;
                    }
                }

                if ( $getuserbyid != '' ) {
                    $data[] = array(
                        'sno'         => $i ,
                        'user_name'   => $getuserbyid->user_login ,
                        'points'      => round_off_type( $total ) ,
                        'event'       => $eventname == '' ? '-' : $eventname ,
                        'date'        => date_display_format( $values[ 'earneddate' ] ) ,
                        'expiry_date' => 999999999999 != $values[ 'expirydate' ] ? date_display_format( $values[ 'expirydate' ] ) : '-' ,
                            ) ;
                }
                $i ++ ;
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
            case 'user_name':
            case 'points':
            case 'event':
            case 'date':
            case 'expiry_date':
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
