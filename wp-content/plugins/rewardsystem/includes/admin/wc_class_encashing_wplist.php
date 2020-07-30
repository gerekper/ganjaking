<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ) ;
}

if ( ! class_exists( 'FPRewardSystemEncashTabList' ) ) {

    class FPRewardSystemEncashTabList extends WP_List_Table {

        function __construct() {
            global $status , $page ;
            parent::__construct( array(
                'singular' => 'encashing_application' ,
                'plural'   => 'encashing_applications' ,
                'ajax'     => true
            ) ) ;
        }

        function column_default( $item , $column_name ) {
            return $item[ $column_name ] ;
        }

        function column_userloginname( $item ) {


            if ( $item[ 'status' ] == 'Paid' ) {
                //Build row actions
                $actions = array(
                    'cancel' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Cancel</a>' , $_REQUEST[ 'page' ] , $_REQUEST[ 'tab' ] , $_REQUEST[ 'section' ] , 'cancel' , $item[ 'id' ] ) ,
                    'delete' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Delete</a>' , $_REQUEST[ 'page' ] , $_REQUEST[ 'tab' ] , $_REQUEST[ 'section' ] , 'encash_application_delete' , $item[ 'id' ] ) ,
                        ) ;

                //Return the title contents
                return sprintf( '%1$s %3$s' ,
                        /* $1%s */ $item[ 'userloginname' ] ,
                        /* $2%s */ $item[ 'id' ] ,
                        /* $3%s */ $this->row_actions( $actions )
                        ) ;
            } elseif ( $item[ 'status' ] == 'Cancelled' ) {
                //Build row actions
                $actions = array(
                    'delete' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Delete</a>' , $_REQUEST[ 'page' ] , $_REQUEST[ 'tab' ] , $_REQUEST[ 'section' ] , 'encash_application_delete' , $item[ 'id' ] ) ,
                        ) ;

                //Return the title contents
                return sprintf( '%1$s %3$s' ,
                        /* $1%s */ $item[ 'userloginname' ] ,
                        /* $2%s */ $item[ 'id' ] ,
                        /* $3%s */ $this->row_actions( $actions )
                        ) ;
            } else {
                //Build row actions
                $actions = array(
                    'accept' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Accept</a>' , $_REQUEST[ 'page' ] , $_REQUEST[ 'tab' ] , $_REQUEST[ 'section' ] , 'accept' , $item[ 'id' ] ) ,
                    'cancel' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Cancel</a>' , $_REQUEST[ 'page' ] , $_REQUEST[ 'tab' ] , $_REQUEST[ 'section' ] , 'cancel' , $item[ 'id' ] ) ,
                    //'edit' => sprintf('<a href="?page=rewardsystem_callback&tab=rewardsystem_request_for_cash_back&encash_application_id=%s">Edit</a>', $item['id']),
                    'delete' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Delete</a>' , $_REQUEST[ 'page' ] , $_REQUEST[ 'tab' ] , $_REQUEST[ 'section' ] , 'delete' , $item[ 'id' ] ) ,
                        ) ;

                //Return the title contents
                return sprintf( '%1$s %3$s' ,
                        /* $1%s */ $item[ 'userloginname' ] ,
                        /* $2%s */ $item[ 'id' ] ,
                        /* $3%s */ $this->row_actions( $actions )
                        ) ;
            }
        }

        function column_cb( $item ) {
            return sprintf(
                    '<input type="checkbox" name="id[]" value="%s" />' , $item[ 'id' ]
                    ) ;
        }

        function get_columns() {
            $columns = array(
                'cb'                   => '<input type="checkbox" />' , //Render a checkbox instead of text            
                'userloginname'        => __( 'Username' , SRP_LOCALE ) ,
                'pointstoencash'       => __( 'Points for Cashback' , SRP_LOCALE ) ,
                'pointsconvertedvalue' => __( 'Points equivalent in Amount ' . get_woocommerce_currency_symbol() , SRP_LOCALE ) ,
                'reasonforencash'      => __( 'Reason for Cashback' , SRP_LOCALE ) ,
                'paypalemailid'        => __( 'Paypal Address ' , SRP_LOCALE ) ,
                'otherpaymentdetails'  => __( 'Other Payment Details' , SRP_LOCALE ) ,
                'status'               => __( 'Application Status' , SRP_LOCALE ) ,
                'date'                 => __( 'Date' , SRP_LOCALE )
                    ) ;
            return $columns ;
        }

        function get_sortable_columns() {
            $sortable_columns = array(
                'userloginname'        => array( 'userloginname' , false ) , //true means it's already sorted            
                'pointstoencash'       => array( 'pointstoencash' , false ) ,
                'pointsconvertedvalue' => array( 'pointsconvertedvalue' , false ) ,
                'reasonforencash'      => array( 'reasonforencash' , false ) ,
                'paypalemailid'        => array( 'paypalemailid' , false ) ,
                'otherpaymentdetails'  => array( 'otherpaymentdetails' , false ) ,
                'status'               => array( 'status' , false ) ,
                'date'                 => array( 'date' , false )
                    ) ;
            return $sortable_columns ;
        }

        function get_bulk_actions() {
            $actions = array(
                'encash_application_delete' => __( 'Delete' , SRP_LOCALE ) ,
                'rspaid'                    => __( 'Mark as Approve' , SRP_LOCALE ) ,
                'rsdue'                     => __( 'Mark as Reject' , SRP_LOCALE ) ,
                    ) ;
            return $actions ;
        }

        function process_bulk_action() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'sumo_reward_encashing_submitted_data' ; // do not forget about tables prefix
            $ids        = isset( $_REQUEST[ 'id' ] ) ? $_REQUEST[ 'id' ] : array() ;
            $ids        = srp_check_is_array( $ids ) ? $ids : (empty( $_REQUEST[ 'id' ] ) ? array() : explode( ',' , $ids )) ;

            if ( ! srp_check_is_array( $ids ) )
                return ;

            if ( 'encash_application_delete' === $this->current_action() ) {
                foreach ( $ids as $eachid ) {
                    $user_ids = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d" , $eachid ) , ARRAY_A ) ;
                    if ( ! srp_check_is_array( $user_ids ) )
                        continue ;

                    foreach ( $user_ids as $value ) {
                        $user_id     = $value[ 'userid' ] ;
                        $updatoption = $value[ 'id' ] . 'cashbackreturn' ;
                        if ( get_user_meta( $user_id , $updatoption , true ) == '1' )
                            continue ;

                        $table_args   = array(
                            'user_id'           => $user_id ,
                            'pointstoinsert'    => $value[ 'pointstoencash' ] ,
                            'checkpoints'       => 'RCBRP' ,
                            'totalearnedpoints' => $value[ 'pointstoencash' ] ,
                                ) ;
                        RSPointExpiry::insert_earning_points( $table_args ) ;
                        RSPointExpiry::record_the_points( $table_args ) ;
                        $wallet_label = get_option( 'rs_encashing_wallet_menu_label' ) != '' ? get_option( 'rs_encashing_wallet_menu_label' ) : 'Hoicker Wallet' ;
                        if ( check_whether_hoicker_is_active() && $value[ 'otherpaymentdetails' ] == $wallet_label ) {
                            $log_message = get_option( 'hr_wallet_actions_rs_cashback_debited' ) != '' ? get_option( 'hr_wallet_actions_rs_cashback_debited' ) : "Cashback Debited" ;
                            hr_wallet_remove_funds_function( $user_id , $value[ 'pointsconvertedvalue' ] , $log_message ) ;
                            hr_wallet_mail_function( '' , $user_id , '' , 'usage_funds_user' , 'user' ) ;
                            hr_wallet_mail_function( '' , $user_id , '' , 'usage_funds_admin' , 'admin' ) ;
                        }
                        update_user_meta( $user_id , $updatoption , '1' ) ;
                    }
                }
                $idstodelete = implode( ',' , $ids ) ;
                $wpdb->query( "DELETE FROM $table_name WHERE id IN($idstodelete)" ) ;
            } elseif ( 'rspaid' === $this->current_action() ) {
                $countids = count( $ids ) ;
                foreach ( $ids as $eachid ) {
                    $wpdb->update( $table_name , array( 'status' => 'Paid' ) , array( 'id' => $eachid ) ) ;
                    $message = __( $countids . ' Status Changed to Paid' , SRP_LOCALE ) ;
                }
                if ( ! empty( $message ) ):
                    ?>
                    <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php
                endif ;
            }elseif ( 'accept' === $this->current_action() ) {
                $countids = count( $ids ) ;
                foreach ( $ids as $eachid ) {
                    $user_ids = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d" , $eachid ) , ARRAY_A ) ;
                    if ( ! srp_check_is_array( $user_ids ) )
                        continue ;

                    foreach ( $user_ids as $value ) {
                        $user_id     = $value[ 'userid' ] ;
                        $updatoption = $value[ 'id' ] . 'walletia_cashback' ;
                        if ( get_user_meta( $user_id , $updatoption , true ) == '1' )
                            continue ;

                        $wallet_label = get_option( 'rs_encashing_wallet_menu_label' ) != '' ? get_option( 'rs_encashing_wallet_menu_label' ) : 'Hoicker Wallet' ;
                        if ( check_whether_hoicker_is_active() && $value[ 'otherpaymentdetails' ] == $wallet_label ) {
                            //Cashback on wallet       
                            $log_message = get_option( 'hr_wallet_actions_rs_cashback_credited' ) != '' ? get_option( 'hr_wallet_actions_rs_cashback_credited' ) : "Cashback Credited" ;
                            hr_wallet_add_credit_updates( '' , $user_id , $value[ 'pointsconvertedvalue' ] , $log_message ) ;
                            hr_wallet_mail_function( '' , $user_id , '' , 'add_funds_user' , 'user' ) ;
                            hr_wallet_mail_function( '' , $user_id , '' , 'add_funds_admin' , 'admin' ) ;
                            update_user_meta( $user_id , $updatoption , '1' ) ;
                        }
                    }
                    $wpdb->update( $table_name , array( 'status' => 'Paid' ) , array( 'id' => $eachid ) ) ;
                    $message = __( $countids . ' Status Changed to Paid' , SRP_LOCALE ) ;
                }
                if ( ! empty( $message ) ):
                    ?>
                    <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php
                endif ;
            }elseif ( 'cancel' === $this->current_action() ) {
                $countids = count( $ids ) ;
                foreach ( $ids as $eachid ) {
                    $wpdb->update( $table_name , array( 'status' => 'Cancelled' ) , array( 'id' => $eachid ) ) ;
                    $message  = __( $countids . ' Status Changed to Cancelled' , SRP_LOCALE ) ;
                    $user_ids = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d" , $eachid ) , ARRAY_A ) ;
                    if ( ! srp_check_is_array( $user_ids ) )
                        continue ;

                    foreach ( $user_ids as $value ) {
                        $user_id     = $value[ 'userid' ] ;
                        $updatoption = $value[ 'id' ] . 'cashbackreturn' ;
                        if ( get_user_meta( $user_id , $updatoption , true ) == '1' )
                            continue ;

                        $table_args   = array(
                            'user_id'           => $user_id ,
                            'pointstoinsert'    => $value[ 'pointstoencash' ] ,
                            'checkpoints'       => 'RCBRP' ,
                            'totalearnedpoints' => $value[ 'pointstoencash' ] ,
                                ) ;
                        RSPointExpiry::insert_earning_points( $table_args ) ;
                        RSPointExpiry::record_the_points( $table_args ) ;
                        update_user_meta( $user_id , $updatoption , '1' ) ;
                        $wallet_label = get_option( 'rs_encashing_wallet_menu_label' ) != '' ? get_option( 'rs_encashing_wallet_menu_label' ) : 'Hoicker Wallet' ;
                        if ( check_whether_hoicker_is_active() && $value[ 'otherpaymentdetails' ] == $wallet_label ) {
                            $log_message = get_option( 'hr_wallet_actions_rs_cashback_debited' ) != '' ? get_option( 'hr_wallet_actions_rs_cashback_debited' ) : "Cashback Debited" ;
                            hr_wallet_remove_funds_function( $user_id , $value[ 'pointsconvertedvalue' ] , $log_message ) ;
                            hr_wallet_mail_function( '' , $user_id , '' , 'usage_funds_user' , 'user' ) ;
                            hr_wallet_mail_function( '' , $user_id , '' , 'usage_funds_admin' , 'admin' ) ;
                        }
                    }
                }
                if ( ! empty( $message ) ):
                    ?>
                    <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php
                endif ;
            }elseif ( 'delete' === $this->current_action() ) {
                foreach ( $ids as $eachid ) {
                    $user_ids = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d" , $eachid ) , ARRAY_A ) ;
                    if ( ! srp_check_is_array( $user_ids ) )
                        continue ;

                    foreach ( $user_ids as $value ) {
                        $user_id     = $value[ 'userid' ] ;
                        $updatoption = $value[ 'id' ] . 'cashbackreturn' ;
                        if ( get_user_meta( $user_id , $updatoption , true ) == '1' )
                            continue ;

                        $table_args = array(
                            'user_id'           => $user_id ,
                            'pointstoinsert'    => $value[ 'pointstoencash' ] ,
                            'checkpoints'       => 'RCBRP' ,
                            'totalearnedpoints' => $value[ 'pointstoencash' ] ,
                                ) ;
                        RSPointExpiry::insert_earning_points( $table_args ) ;
                        RSPointExpiry::record_the_points( $table_args ) ;
                        update_user_meta( $user_id , $updatoption , '1' ) ;
                    }
                }
                $idtodelete = implode( ',' , $ids ) ;
                $wpdb->query( "DELETE FROM $table_name WHERE id IN($idtodelete)" ) ;
                if ( ! empty( $message ) ):
                    ?>
                    <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php
                endif ;
            }else {
                $countids = count( $ids ) ;
                foreach ( $ids as $eachid ) {
                    $wpdb->update( $table_name , array( 'status' => 'Due' ) , array( 'id' => $eachid ) ) ;
                    $message = __( $countids . ' Status Changed to Due' , SRP_LOCALE ) ;
                }
                if ( ! empty( $message ) ):
                    ?>
                    <div id="message" class="updated"><p><?php echo $message ?></p></div>
                    <?php
                endif ;
            }
            $redirect = remove_query_arg( array( 'action' , 'id' ) , get_permalink() ) ;
            wp_safe_redirect( $redirect ) ;
        }

        function extra_tablenav( $which ) {
            global $wpdb ;
            $mainlistarray                 = array() ;
            $mainlistarray_alldata         = array() ;
            $mainlistarray_paypal          = array() ;
            $mainlistarray_alldata_heading = '' ;
            $tablename                     = $wpdb->prefix . 'sumo_reward_encashing_submitted_data' ;
            if ( $which == 'top' ) {
                ?>
                <input type="submit" class="button-primary" name="fprs_encash_export_csv_paypal" id="fprs_encash_export_csv_paypal" value="<?php _e( 'Export Due Points as CSV for Paypal Mass Payment' , SRP_LOCALE ) ; ?>"/>
                <input type="submit" class="button-primary" name="fprs_encash_export_csv_alldata" id="fprs_encash_export_csv_alldata" value="<?php _e( 'Export All Cashback Requests' , SRP_LOCALE ) ; ?>"/>
                <?php
                $getallresults = $wpdb->get_results( "SELECT * FROM $tablename WHERE status='Due'" , ARRAY_A ) ;
                if ( isset( $getallresults ) ) {
                    foreach ( $getallresults as $value ) {
                        if ( $value[ 'pointstoencash' ] != '' && $value[ 'paypalemailid' ] != '' ) {
                            $mainlistarray_paypal[] = array( $value[ 'paypalemailid' ] , $value[ 'pointsconvertedvalue' ] , get_woocommerce_currency() , $value[ 'userid' ] , get_option( 'rs_encashing_paypal_custom_notes' ) ) ;
                        }
                    }
                    if ( isset( $_POST[ 'fprs_encash_export_csv_paypal' ] ) ) {
                        if ( is_array( $mainlistarray_paypal ) && ( ! empty( $mainlistarray_paypal )) ) {
                            $dateformat = get_option( 'date_format' ) ;
                            $name       = date_i18n( 'Y-m-d' ) ;
                            ob_end_clean() ;
                            header( "Content-type: text/csv" ) ;
                            header( "Content-Disposition: attachment; filename=sumoreward_cashback_paypal" . $name . ".csv" ) ;
                            header( "Pragma: no-cache" ) ;
                            header( "Expires: 0" ) ;
                            $output     = fopen( "php://output" , "w" ) ;
                            foreach ( $mainlistarray_paypal as $row ) {
                                if ( $row != false ) {
                                    fputcsv( $output , $row ) ; // here you can change delimiter/enclosure
                                }
                            }
                            fclose( $output ) ;
                            exit() ;
                        }
                    }
                }

                if ( isset( $getallresults ) ) {
                    foreach ( $getallresults as $allvalue ) {
                        if ( $allvalue[ 'pointstoencash' ] != '' ) {
                            $mainlistarray_alldata_heading = "Username,UserCurrentPoints,PointsforCashback,CurrencyCode,AmountforCashback,ReasonforEncashing,PaypalAddress,OtherPaymentDetails,ApplicationStatus,CashbackRequestedDate" . "\n" ;
                            $mainlistarray_alldata[]       = array( $allvalue[ 'userloginname' ] , $allvalue[ 'encashercurrentpoints' ] , $allvalue[ 'pointstoencash' ] , get_woocommerce_currency() , $allvalue[ 'pointsconvertedvalue' ] , $allvalue[ 'reasonforencash' ] , $allvalue[ 'paypalemailid' ] , $allvalue[ 'otherpaymentdetails' ] , $allvalue[ 'status' ] , $allvalue[ 'date' ] ) ;
                        }
                    }
                    if ( isset( $_POST[ 'fprs_encash_export_csv_alldata' ] ) ) {
                        $dateformat = get_option( 'date_format' ) ;
                        $name       = date_i18n( 'Y-m-d' ) ;
                        ob_end_clean() ;
                        echo $mainlistarray_alldata_heading ;
                        header( "Content-type: text/csv" ) ;
                        header( "Content-Disposition: attachment; filename=sumoreward_cashback_alldata" . $name . ".csv" ) ;
                        header( "Pragma: no-cache" ) ;
                        header( "Expires: 0" ) ;
                        $output     = fopen( "php://output" , "w" ) ;
                        if ( is_array( $mainlistarray_alldata ) && ( ! empty( $mainlistarray_alldata )) ) {
                            foreach ( $mainlistarray_alldata as $row ) {
                                if ( $row != false ) {
                                    fputcsv( $output , $row ) ; // here you can change delimiter/enclosure
                                }
                            }
                        }
                        fclose( $output ) ;
                        exit() ;
                    }
                }
            }
        }

        function prepare_items() {
            global $wpdb ;
            $table_name = $wpdb->prefix . 'sumo_reward_encashing_submitted_data' ; // do not forget about tables prefix

            $per_page = 10 ; // constant, how much records will be shown per page

            $columns  = $this->get_columns() ;
            $hidden   = array() ;
            $sortable = $this->get_sortable_columns() ;

            // here we configure table headers, defined in our methods
            $this->_column_headers = array( $columns , $hidden , $sortable ) ;

            // [OPTIONAL] process bulk action if any
            $this->process_bulk_action() ;

            // will be used in pagination settings
            $total_items = $wpdb->get_var( "SELECT COUNT(id) FROM $table_name" ) ;

            // prepare query params, as usual current page, order by and order direction
            $paged   = isset( $_REQUEST[ 'paged' ] ) ? max( 0 , intval( $_REQUEST[ 'paged' ] ) - 1 ) : 0 ;
            $orderby = (isset( $_REQUEST[ 'orderby' ] ) && in_array( $_REQUEST[ 'orderby' ] , array_keys( $this->get_sortable_columns() ) )) ? $_REQUEST[ 'orderby' ] : 'id' ;
            $order   = (isset( $_REQUEST[ 'order' ] ) && in_array( $_REQUEST[ 'order' ] , array( 'asc' , 'desc' ) )) ? $_REQUEST[ 'order' ] : 'asc' ;

            // [REQUIRED] define $items array
            // notice that last argument is ARRAY_A, so we will retrieve array
            $this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d" , $per_page , $paged ) , ARRAY_A ) ;

            // [REQUIRED] configure pagination
            $this->set_pagination_args( array(
                'total_items' => $total_items , // total items defined above
                'per_page'    => $per_page , // per page constant defined at top of method
                'total_pages' => ceil( $total_items / $per_page ) // calculate pages count
            ) ) ;
        }

    }

}