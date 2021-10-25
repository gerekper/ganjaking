<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSCashBackFrontend' ) ) {

    class RSCashBackFrontend {

        public static function init() {
            add_action( 'woocommerce_after_my_account' , array( __CLASS__ , 'cash_back_log' ) ) ;
        }

        public static function cash_back_log() {
            if ( get_option( 'rs_my_cashback_table' ) == '2' )
                return ;

            if ( get_option( 'rs_reward_content' ) != 'yes' )
                return ;

            $TableData = array(
                'title'    => get_option( 'rs_my_cashback_title' ) ,
                'sno'      => get_option( 'rs_my_cashback_sno_label' ) ,
                'username' => get_option( 'rs_my_cashback_userid_label' ) ,
                'request'  => get_option( 'rs_my_cashback_requested_label' ) ,
                'status'   => get_option( 'rs_my_cashback_status_label' ) ,
                'action'   => get_option( 'rs_my_cashback_action_label' )
                    ) ;
            self::cash_back_log_table( $TableData ) ;
        }

        public static function cash_back_log_table( $TableData ) {
            $UserId  = get_current_user_id() ;
            $BanType = check_banning_type( $UserId ) ;
            if ( $BanType == 'redeemingonly' || $BanType == 'both' )
                return ;
            
            global $wpdb ;
            $CashbackTable     = $wpdb->prefix . 'sumo_reward_encashing_submitted_data' ;
            ?>
            <style type="text/css">
            <?php echo get_option( 'rs_myaccount_custom_css' ) ; ?>
            </style>
            <?php
            $CashbackTableData = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $CashbackTable WHERE userid = %d" , $UserId ) , ARRAY_A ) ;
            if ( ! srp_check_is_array( $CashbackTableData ) )
                return ;

            ob_start() ;
            echo "<h2 class=rs_my_cashback_title>" . $TableData[ 'title' ] . "</h2>" ;
            ?>
            <table class = "examples demo shop_table my_account_orders table-bordered" data-filter = "#filters" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">
                <thead>
                    <tr>
                        <th data-toggle="true" data-sort-initial = "true"><?php echo $TableData[ 'sno' ] ; ?></th>
                        <th><?php echo $TableData[ 'username' ] ; ?></th>
                        <th><?php echo $TableData[ 'request' ] ; ?></th>
                        <th><?php echo $TableData[ 'status' ] ; ?></th>
                        <th><?php echo $TableData[ 'action' ] ; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ( get_option( 'rs_points_log_sorting' ) == '1' )
                        krsort( $CashbackTableData , SORT_NUMERIC ) ;

                    $i = 1 ;
                    foreach ( $CashbackTableData as $Data ) {

                        if ( ! srp_check_is_array( $Data ) )
                            continue ;

                        $NickName        = get_user_meta( $Data[ 'userid' ] , 'nickname' , true ) ;
                        $Log             = get_option( '_rs_localize_points_to_cash_log_in_my_cashback_table' ) ;
                        $PointReplaceLog = str_replace( '[pointstocashback]' , $Data[ 'pointstoencash' ] , $Log ) ;
                        $AmntReplacedLog = str_replace( '[cashbackamount]' , get_woocommerce_currency_symbol() . $Data[ 'pointsconvertedvalue' ] , $PointReplaceLog ) ;
                        $status          = $Data[ 'status' ] ;
                        $StatusToDisplay = array(
                            'Cancelled' => __( 'Cancelled' , SRP_LOCALE ) ,
                            'Paid'      => __( 'Paid' , SRP_LOCALE ) ,
                            'Due'       => __( 'Due' , SRP_LOCALE ) ,
                                ) ;
                        $BtnStatus       = __( 'Cancel' , SRP_LOCALE ) ;
                        ?>
                        <tr>
                            <td data-value="<?php echo $i ; ?>"><?php echo $i ; ?></td>
                            <td><?php echo $NickName ; ?> </td>
                            <td><?php echo $AmntReplacedLog ; ?></td>
                            <td><?php echo $StatusToDisplay[ $status ] ; ?></td>
                            <td>
                                <?php
                                if ( $status == 'Paid' ) {
                                    echo '-' ;
                                } else {
                                    ?>
                                    <input type="button" class = "cancelbutton" value= "<?php echo $BtnStatus ; ?>" data-id="<?php echo $Data[ 'id' ] ; ?>" data-status="<?php echo $status ; ?>"/>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        $i ++ ;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr style="clear:both;">
                        <td colspan="7">
                            <div class="pagination pagination-centered"></div>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php
            return ob_get_contents() ;
        }

    }

    RSCashBackFrontend::init() ;
}