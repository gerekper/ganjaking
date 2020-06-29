<?php

// Integrate WP List Table for Users

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ) ;
}

class WP_List_Table_for_Users extends WP_List_Table {

// Prepare Items
    public function prepare_items() {
        global $wpdb ;
        $args       = array() ;
        $columns    = $this->get_columns() ;
        $sortable   = $this->get_sortable_columns() ;
        $userprefix = $wpdb->base_prefix . "users" ;
        $num_rows   = $wpdb->get_var( "SELECT COUNT(*) FROM $userprefix" ) ;

        $user        = get_current_user_id() ;
        $screen      = get_current_screen() ;
        $perPage     = RSTabManagement::rs_get_value_for_no_of_item_perpage( $user , $screen ) ;
        $currentPage = $this->get_pagenum() ;
        $startpoint  = ($currentPage - 1) * $perPage ;
        $data        = $this->table_data( $startpoint , $perPage ) ;
        if ( isset( $_REQUEST[ "rs_submit_for_user_role_log" ] ) ) {
            if ( isset( $_REQUEST[ 'rs_userrole_for_reward_log' ] ) ) {
                $args = array(
                    'role__in' => ( array ) ($_REQUEST[ 'rs_userrole_for_reward_log' ]) ,
                        ) ;
            } else if ( isset( $_REQUEST[ 'rs_select_user_for_reward_log' ] ) ) {
                $args = array(
                    'include' => ( array ) ($_REQUEST[ 'rs_select_user_for_reward_log' ]) ,
                        ) ;
            }
            $this->search_user( $args , $user , $screen , $columns , $sortable ) ;
        } else if ( isset( $_REQUEST[ 's' ] ) ) {
            $searchvalue = $_REQUEST[ 's' ] ;
            $args        = array(
                'search' => "$searchvalue" ,
                    ) ;
            $this->search_user( $args , $user , $screen , $columns , $sortable ) ;
        } elseif ( isset( $_REQUEST[ 'orderby' ] ) && ($_REQUEST[ 'orderby' ]) && $_REQUEST[ 'order' ] ) {
            $paged                 = isset( $_REQUEST[ 'paged' ] ) ? $_REQUEST[ 'paged' ] : 1 ;
            $order                 = $_REQUEST[ 'order' ] ;
            $order_by              = $_REQUEST[ 'orderby' ] ;
            $startpoint            = ($paged - 1) * $perPage ;
            $data                  = $this->get_sorting_data( $order , $startpoint , $perPage , $order_by ) ;
            $this->_column_headers = array( $columns , array() , $sortable ) ;
            $totalItems            = $num_rows ;

            $this->set_pagination_args( array(
                'total_items' => $totalItems ,
                'per_page'    => $perPage
            ) ) ;
            $this->items = $data ;
        } else {
            usort( $data , array( &$this , 'sort_data' ) ) ;

            $totalItems = $num_rows ;

            $this->set_pagination_args( array(
                'total_items' => $totalItems ,
                'per_page'    => $perPage
            ) ) ;

            $this->_column_headers = array( $columns , array() , $sortable ) ;

            $this->items = $data ;
        }
    }

    public function search_user( $args , $user , $screen , $columns , $sortable ) {
        $newdata  = array() ;
        $UserData = get_users( $args ) ;
        if ( srp_check_is_array( $UserData ) ) {
            $sr = 1 ;
            foreach ( $UserData as $eacharray => $value ) {
                $newdata[] = $this->get_data_of_users( $value->ID , $sr ) ;
                $sr ++ ;
            }
        }

        $this->_column_headers = array( $columns , array() , $sortable ) ;
        $this->items           = $newdata ;
    }

    private function get_data_of_users( $UserId , $i ) {
        $UserData        = get_user_by( 'id' , $UserId ) ;
        $PointsData      = new RS_Points_Data( $UserId ) ;
        $AvailablePoints = $PointsData->total_available_points() ;
        $EarnedPoints    = $PointsData->total_earned_points() ;
        $RedeemedPoints  = $PointsData->total_redeemed_points() ;
        $ExpiredPoints   = $PointsData->total_expired_points() ;
        $data            = array(
            'sno'                  => $i ,
            'user_name'            => $UserData->user_login ,
            'user_email'           => $UserData->user_email ,
            'total_earned_points'  => $EarnedPoints ,
            'total_points'         => $AvailablePoints ,
            'total_redeem_points'  => $RedeemedPoints ,
            'total_expired_points' => $ExpiredPoints ,
            'view'                 => "<a href=" . add_query_arg( 'view' , $UserId , admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsuserrewardpoints' ) ) . ">View Log</a>" ,
            'edit'                 => "<a href=" . add_query_arg( 'edit' , $UserId , admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsuserrewardpoints' ) ) . ">Edit Total Points</a>" ,
                ) ;
        return $data ;
    }

    public function get_columns() {
        $columns = array(
            'sno'                  => __( 'S.No' , SRP_LOCALE ) ,
            'user_name'            => __( 'Username' , SRP_LOCALE ) ,
            'user_email'           => __( 'User Email' , SRP_LOCALE ) ,
            'total_earned_points'  => __( 'Total Earned Points' , SRP_LOCALE ) ,
            'total_points'         => __( 'Current Available Points' , SRP_LOCALE ) ,
            'total_redeem_points'  => __( 'Total Redeemed Points' , SRP_LOCALE ) ,
            'total_expired_points' => __( 'Total Expired Points' , SRP_LOCALE ) ,
            'view'                 => __( 'View' , SRP_LOCALE ) ,
            'edit'                 => __( 'Edit' , SRP_LOCALE ) ,
                ) ;

        return $columns ;
    }

    public function get_sortable_columns() {
        return array(
            'user_name'            => array( 'user_name' , false ) ,
            'total_points'         => array( 'total_points' , false ) ,
            'total_earned_points'  => array( 'total_earned_points' , false ) ,
            'total_redeem_points'  => array( 'total_redeem_points' , false ) ,
            'total_expired_points' => array( 'total_expired_points' , false ) ,
                ) ;
    }

    private function get_sorting_data( $sort_type , $startpoint , $perpage , $order_by ) {
        global $wpdb ;
        $UserTable   = is_multisite() ? $wpdb->base_prefix . 'users' : $wpdb->prefix . 'users' ;
        $PointsTable = $wpdb->prefix . 'rspointexpiry' ;
        // for ascending
        if ( $order_by == 'user_name' ) {
            $UserData = $wpdb->get_results( $wpdb->prepare( "SELECT distinct Usertable.ID FROM $UserTable as Usertable LEFT JOIN $PointsTable as Pointstable ON Usertable.ID = Pointstable.userid ORDER BY Usertable.user_login $sort_type LIMIT %d , %d" , $startpoint , $perpage ) , ARRAY_A ) ;
        } else if ( $order_by == 'total_points' ) {
            $PointsQuery = "SELECT SUM((Pointstable.earnedpoints-Pointstable.usedpoints)) FROM $PointsTable as Pointstable WHERE Usertable.ID = Pointstable.userid AND (Pointstable.earnedpoints-Pointstable.usedpoints) NOT IN(0) and Pointstable.expiredpoints IN(0)" ;
            $UserData    = $wpdb->get_results( $wpdb->prepare( "SELECT distinct Usertable.ID ,($PointsQuery) as Points FROM $UserTable as Usertable LEFT JOIN $PointsTable as Pointstable ON Usertable.ID = Pointstable.userid ORDER BY Points $sort_type LIMIT %d , %d" , $startpoint , $perpage ) , ARRAY_A ) ;
        } else if ( $order_by == 'total_earned_points' ) {
            $PointsQuery = "SELECT SUM(Pointstable.earnedpoints) FROM $PointsTable as Pointstable WHERE Usertable.ID = Pointstable.userid AND Pointstable.earnedpoints NOT IN(0)" ;
            $UserData    = $wpdb->get_results( $wpdb->prepare( "SELECT distinct Usertable.ID ,($PointsQuery) as Points FROM $UserTable as Usertable LEFT JOIN $PointsTable as Pointstable ON Usertable.ID = Pointstable.userid ORDER BY Points $sort_type LIMIT %d , %d" , $startpoint , $perpage ) , ARRAY_A ) ;
        } else if ( $order_by == 'total_redeem_points' ) {
            $PointsQuery = "SELECT SUM(Pointstable.usedpoints) FROM $PointsTable as Pointstable WHERE Usertable.ID = Pointstable.userid AND Pointstable.usedpoints NOT IN(0)" ;
            $UserData    = $wpdb->get_results( $wpdb->prepare( "SELECT distinct Usertable.ID ,($PointsQuery) as Points FROM $UserTable as Usertable LEFT JOIN $PointsTable as Pointstable ON Usertable.ID = Pointstable.userid ORDER BY Points $sort_type LIMIT %d , %d" , $startpoint , $perpage ) , ARRAY_A ) ;
        } else if ( $order_by == 'total_expired_points' ) {
            $PointsQuery = "SELECT SUM(Pointstable.expiredpoints) FROM $PointsTable as Pointstable WHERE Usertable.ID = Pointstable.userid AND Pointstable.expiredpoints NOT IN(0)" ;
            $UserData    = $wpdb->get_results( $wpdb->prepare( "SELECT distinct Usertable.ID ,($PointsQuery) as Points FROM $UserTable as Usertable LEFT JOIN $PointsTable as Pointstable ON Usertable.ID = Pointstable.userid ORDER BY Points $sort_type LIMIT %d , %d" , $startpoint , $perpage ) , ARRAY_A ) ;
        }
        $i = 1 ;
        if ( ! srp_check_is_array( $UserData ) )
            return array() ;

        foreach ( $UserData as $Data ) {
            $UserInfo        = get_user_by( 'id' , $Data[ 'ID' ] ) ;
            $PointsData      = new RS_Points_Data( $Data[ 'ID' ] ) ;
            $AvailablePoints = $PointsData->total_available_points() ;
            $EarnedPoints    = $PointsData->total_earned_points() ;
            $RedeemedPoints  = $PointsData->total_redeemed_points() ;
            $ExpiredPoints   = $PointsData->total_expired_points() ;
            $data[]          = array(
                'sno'                  => $startpoint + $i ,
                'user_name'            => $UserInfo->user_login ,
                'user_email'           => $UserInfo->user_email ,
                'total_earned_points'  => $EarnedPoints ,
                'total_points'         => $AvailablePoints ,
                'total_redeem_points'  => $RedeemedPoints ,
                'total_expired_points' => $ExpiredPoints ,
                'view'                 => "<a href=" . add_query_arg( 'view' , $Data[ 'ID' ] , admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsuserrewardpoints' ) ) . ">View Log</a>" ,
                'edit'                 => "<a href=" . add_query_arg( 'edit' , $Data[ 'ID' ] , admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsuserrewardpoints' ) ) . ">Edit Total Points</a>" ,
                    ) ;
            $i ++ ;
        }
        return $data ;
    }

    private function table_data( $startpoint , $perpage ) {
        global $wpdb ;
        $table_name     = $wpdb->prefix . 'rspointexpiry' ;
        $data           = array() ;
        $i              = 1 ;
        $table_user     = $wpdb->base_prefix . 'users' ;
        $table_usermeta = $wpdb->base_prefix . 'usermeta' ;
        if ( is_multisite() ) {
            $id           = get_current_blog_id() ;
            $blog_prefix  = $wpdb->get_blog_prefix( $id ) ;
            $blog_prefix  = $blog_prefix . 'capabilities' ;
            $getusermeta1 = $wpdb->get_results( "SELECT $table_user.ID FROM $table_user INNER JOIN  $table_usermeta ON ( $table_user.ID = $table_usermeta.user_id ) WHERE  1=1 AND ($table_usermeta.meta_key = '$blog_prefix')LIMIT $startpoint, $perpage" ) ;
            foreach ( $getusermeta1 as $user ) {
                $UserData        = get_user_by( 'id' , $user->ID ) ;
                $PointsData      = new RS_Points_Data( $user->ID ) ;
                $AvailablePoints = $PointsData->total_available_points() ;
                $EarnedPoints    = $PointsData->total_earned_points() ;
                $RedeemedPoints  = $PointsData->total_redeemed_points() ;
                $ExpiredPoints   = $PointsData->total_expired_points() ;
                $data[]          = array(
                    'sno'                  => $startpoint + $i ,
                    'user_name'            => $UserData->user_login ,
                    'user_email'           => $UserData->user_email ,
                    'total_earned_points'  => $EarnedPoints ,
                    'total_points'         => $AvailablePoints ,
                    'total_redeem_points'  => $RedeemedPoints ,
                    'total_expired_points' => $ExpiredPoints ,
                    'view'                 => "<a href=" . add_query_arg( 'view' , $user->ID , admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsuserrewardpoints' ) ) . ">View Log</a>" ,
                    'edit'                 => "<a href=" . add_query_arg( 'edit' , $user->ID , admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsuserrewardpoints' ) ) . ">Edit Total Points</a>" ,
                        ) ;
                $i ++ ;
            }

            return $data ;
        } else {
            $query_data = $wpdb->get_results( "SELECT * FROM $table_user LIMIT $startpoint, $perpage" ) ;
            foreach ( $query_data as $user ) {
                $UserData        = get_user_by( 'id' , $user->ID ) ;
                $PointsData      = new RS_Points_Data( $user->ID ) ;
                $AvailablePoints = $PointsData->total_available_points() ;
                $EarnedPoints    = $PointsData->total_earned_points() ;
                $RedeemedPoints  = $PointsData->total_redeemed_points() ;
                $ExpiredPoints   = $PointsData->total_expired_points() ;
                $data[]          = array(
                    'sno'                  => $startpoint + $i ,
                    'user_name'            => $UserData->user_login ,
                    'user_email'           => $UserData->user_email ,
                    'total_earned_points'  => $EarnedPoints ,
                    'total_points'         => $AvailablePoints ,
                    'total_redeem_points'  => $RedeemedPoints ,
                    'total_expired_points' => $ExpiredPoints ,
                    'view'                 => "<a href=" . add_query_arg( 'view' , $user->ID , admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsuserrewardpoints' ) ) . ">View Log</a>" ,
                    'edit'                 => "<a href=" . add_query_arg( 'edit' , $user->ID , admin_url( 'admin.php?page=rewardsystem_callback&tab=fprsuserrewardpoints' ) ) . ">Edit Total Points</a>" ,
                        ) ;
                $i ++ ;
            }

            return $data ;
        }
    }

    public function column_id( $item ) {
        return $item[ 'sno' ] ;
    }

    public function column_default( $item , $column_name ) {
        switch ( $column_name ) {
            case 'sno':
            case 'user_name':
            case 'user_email':
            case 'total_earned_points':
            case 'total_points':
            case 'total_redeem_points':
            case 'total_expired_points':
            case 'view':
            case 'edit':
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
