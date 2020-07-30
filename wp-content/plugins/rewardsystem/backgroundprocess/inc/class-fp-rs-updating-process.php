<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'FP_Updating_Process_for_RS' ) ) {

    /**
     * FP_RAC_Updating_Process Class.
     */
    class FP_Updating_Process_for_RS {

        public $progress_batch ;
        public $identifier = 'fp_progress_ui' ;

        public function __construct() {
            $this->progress_batch = ( int ) get_site_option( 'fp_background_process_' . $this->identifier . '_progress' , 0 ) ;
            add_action( 'wp_ajax_fp_progress_bar_status' , array( $this , 'fp_updating_status' ) ) ;
        }

        /*
         * Get Updated Details using ajax
         * 
         */

        public function fp_updating_status() {
            $percent = ( int ) get_site_option( 'fp_background_process_' . $this->identifier . '_progress' , 0 ) ;
            echo json_encode( array( $percent ) ) ;
            exit() ;
        }

        public function fp_delete_option() {
            delete_site_option( 'fp_background_process_' . $this->identifier . '_progress' ) ;
        }

        public function fp_increase_progress( $progress = 0 ) {
            update_site_option( 'fp_background_process_' . $this->identifier . '_progress' , $progress ) ;
        }

        /*
         * Get Updated Details using ajax
         * 
         */

        public function fp_display_progress_bar( $Method = '' ) {
            $percent = $this->progress_batch ;
            if ( $Method == 'update_products' ) {
                $url           = add_query_arg( array( 'page' => 'sumo-reward-points-welcome-page' ) , SRP_ADMIN_URL ) ;
                $processingmsg = sprintf( __( 'Upgrade to v%s is under Process...' , SRP_LOCALE ) , SRP_VERSION ) ;
                $responsemsg   = sprintf( __( 'Upgrade to v%s Completed Successfully.' , SRP_LOCALE ) , SRP_VERSION ) ;
            } elseif ( $Method == 'add_points' || $Method == 'remove_points' ) {
                $url = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsaddremovepoints' ) , SRP_ADMIN_URL ) ;
                if ( $Method == 'add_points' ) {
                    $processingmsg = __( 'Adding Points for User(s) is under Process...' , SRP_LOCALE ) ;
                    $responsemsg   = __( 'Adding Points for User(s) Completed Successfully.' , SRP_LOCALE ) ;
                } else {
                    $processingmsg = __( 'Removing Points for User(s) is under Process...' , SRP_LOCALE ) ;
                    $responsemsg   = __( 'Removing Points for User(s) Completed Successfully.' , SRP_LOCALE ) ;
                }
            } elseif ( $Method == 'refresh_points' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsgeneral' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Refreshing Points for User(s) is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Refreshing Expired Points for User(s) Completed Successfully.' , SRP_LOCALE ) ;
            } elseif ( $Method == 'export_points' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpimportexport' , 'export_points' => 'yes' ) , SRP_ADMIN_URL ) ;
                $gobackurl     = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpimportexport' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Exporting Points for User(s) is under Process...' , SRP_LOCALE ) ;
                $redirecturl   = "<a href='$gobackurl'>Go to Settings</a>" ;
                $responsemsg   = __( 'Exporting Points for User(s) Completed Successfully.' , SRP_LOCALE ) . ' ' . $redirecturl ;
            } elseif ( $Method == 'export_report' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpreportsincsv' , 'export_report' => 'yes' ) , SRP_ADMIN_URL ) ;
                $gobackurl     = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpreportsincsv' ) , SRP_ADMIN_URL ) ;
                $redirecturl   = "<a href='$gobackurl'>Go to Settings</a>" ;
                $processingmsg = __( 'Exporting Points for User(s) is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Exporting Points for User(s) Completed Successfully.' , SRP_LOCALE ) . ' ' . $redirecturl ;
            } elseif ( $Method == 'export_log' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmasterlog' , 'export_log' => 'yes' ) , SRP_ADMIN_URL ) ;
                $gobackurl     = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmasterlog' ) , SRP_ADMIN_URL ) ;
                $redirecturl   = "<a href='$gobackurl'>Go to Settings</a>" ;
                $processingmsg = __( 'Exporting Log for User(s) is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Exporting Log for User(s) Completed Successfully.' , SRP_LOCALE ) . ' ' . $redirecturl ;
            } elseif ( $Method == 'old_points' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsadvanced' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Adding Old Points for User(s) is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Adding Old Points for User(s) Completed Successfully.' , SRP_LOCALE ) ;
            } elseif ( $Method == 'bulk_update' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpproductpurchase' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Updating Points for Product(s) is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Updating Points for Product(s) Completed Successfully.' , SRP_LOCALE ) ;
            } elseif ( $Method == 'bulk_update_for_social' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpsocialreward' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Updating Social Reward Points for Product(s) is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Updating Social Reward Points for Product(s) Completed Successfully.' , SRP_LOCALE ) ;
            } elseif ( $Method == 'bulk_update_buying_points' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpbuyingpoints' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Updating Buying Points for Product(s) is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Updating Buying Points for Product(s) Completed Successfully.' , SRP_LOCALE ) ;
            } elseif ( $Method == 'bulk_update_point_price' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fppointprice' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Updating Point Price for Product(s) is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Updating Point Price for Product(s) Completed Successfully.' , SRP_LOCALE ) ;
            } elseif ( $Method == 'generate_voucher_code' ) {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpgiftvoucher' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Voucher Code generation is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Voucher Code generated Successfully.' , SRP_LOCALE ) ;
            } elseif ( $Method == 'update_earned_points' ) {
                $url           = add_query_arg( array( 'page' => 'sumo-reward-points-welcome-page' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Updating earned points is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Earned Points updated Successfully.' , SRP_LOCALE ) ;
            } else {
                $url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsadvanced' ) , SRP_ADMIN_URL ) ;
                $processingmsg = __( 'Applying Points for Previous Order is under Process...' , SRP_LOCALE ) ;
                $responsemsg   = __( 'Applying Points for Previous Order Completed Successfully.' , SRP_LOCALE ) ;
            }
            ?>
            <style type="text/css">
                .fp_prograssbar_wrapper{
                    width:500px;
                    margin:20% auto;
                }
                .fp_outer{
                    height: 20px;
                    width: 500px;
                    background:#d5d4d3;
                    box-shadow:0 1px 2px rgba(0, 0, 0, 0.1) inset;
                    border-radius:50px;
                }
                .fp_inner{
                    height: 20px;
                    width: <?php echo $percent ; ?>%;
                    background:#5cb85c;
                    border-radius:50px;
                }
            </style>
            <div class="fp_prograssbar_wrapper">
                <h1><?php _e( 'SUMO Reward Points' , SRP_LOCALE ) ; ?></h1>
                <div id="fp_upgrade_label">
                    <h3 style="font-weight:normal"><?php echo $processingmsg ; ?></h3>
                </div>
                <div class = "fp_outer">
                    <div class = "fp_inner fp-progress-bar">
                    </div>
                </div>
                <div id="fp_progress_status">
                    <span id = "fp_currrent_status"><?php echo $percent ; ?> </span><?php _e( ' % Completed' , SRP_LOCALE ) ; ?>
                </div>
            </div>
            <script type = "text/javascript">
                jQuery( document ).ready( function ( $ ) {
                    fp_prepare_progress_bar() ;
                    function fp_prepare_progress_bar() {
                        var data = {
                            action : 'fp_progress_bar_status' ,
                        } ;
                        jQuery.ajax( {
                            type : 'POST' ,
                            url : ajaxurl ,
                            data : data ,
                            dataType : 'json' ,
                        } ).done( function ( $response ) {
                            if ( $response[0] < 100 ) {
                                jQuery( '#fp_currrent_status' ).html( $response[0] ) ;
                                jQuery( '.fp-progress-bar' ).css( "width" , $response[0] + " %" ) ;
                                fp_prepare_progress_bar() ;
                            } else {
                                jQuery( '#fp_upgrade_label' ).css( "display" , "none" ) ;
                                jQuery( '.fp-progress-bar' ).css( "width" , "100%" ) ;
                                jQuery( '#fp_progress_status' ).html( "<h4><?php echo $responsemsg ; ?></h4>" ) ;
                                window.location.href = '<?php echo $url ; ?>' ;
                            }
                        } ) ;
                    }
                } ) ;
            </script>
            <?php
        }

    }

}