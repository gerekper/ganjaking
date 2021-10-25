<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSFunctionForPointURL' ) ) {

    class RSFunctionForPointURL {

        public static function init() {
            add_action( 'wp_head' , array( __CLASS__ , 'award_points_for_url_click' ) ) ;
        }

        public static function award_points_for_url_click() {
            if ( ! is_user_logged_in() )
                return ;

            if ( ! isset( $_GET[ 'rsid' ] ) )
                return ;

            $uniqueid = $_GET[ 'rsid' ] ;
            $UserId   = get_current_user_id() ;
            $BanType  = check_banning_type( $UserId ) ;
            if ( $BanType == 'earningonly' || $BanType == 'both' )
                return ;

            $PointUrlData = get_option( 'points_for_url_click' ) ;

            if ( ! srp_check_is_array( $PointUrlData ) )
                return ;

            $PointUrlData[ $uniqueid ][ 'used_by' ] = array() ;
            $OfferName                              = $PointUrlData[ $uniqueid ][ 'name' ] ;
            $URLUsedBy                              = $PointUrlData[ $uniqueid ][ 'used_by' ] ;
            $TimeLimit                              = $PointUrlData[ $uniqueid ][ 'time_limit' ] ;
            $Date                                   = strtotime( date( 'y-m-d' ) ) ;
            $ExpDate                                = strtotime( $PointUrlData[ $uniqueid ][ 'expiry_time' ] ) ;

            if ( ! in_array( $UserId , ( array ) $URLUsedBy ) ) {
                if ( $TimeLimit == '2' ) {
                    if ( $Date <= $ExpDate ) {
                        self::check_if_count_exceed( $PointUrlData , $uniqueid , $UserId , $OfferName ) ;
                    } else {
                        $MsgToDisplay = str_replace( '[offer_name]' , $OfferName , get_option( 'failure_msg_for_expired_url' ) )
                        ?>                            
                        <div class="sk_failure_msg_for_pointsurl"><?php echo $MsgToDisplay ; ?></div>
                        <?php
                    }
                } else {
                    self::check_if_count_exceed( $PointUrlData , $uniqueid , $UserId , $OfferName ) ;
                }
            } else {
                ?>                    
                <div class="sk_failure_msg_for_pointsurl"><?php echo get_option( 'failure_msg_for_accessed_url' ) ; ?></div>
                <?php
            }
        }

        public static function check_if_count_exceed( $PointUrlData , $uniqueid , $UserId , $OfferName ) {
            $UsageCount     = $PointUrlData[ $uniqueid ][ 'current_usage_count' ] ;
            $CountLimit     = $PointUrlData[ $uniqueid ][ 'count' ] ;
            $CountLimitType = $PointUrlData[ $uniqueid ][ 'count_limit' ] ;
            $BoolValue      = ($CountLimitType == '1') ? true : ($UsageCount < $CountLimit) ;
            $PointsForUrl   = $PointUrlData[ $uniqueid ][ 'points' ] ;

            if ( $BoolValue ) {
                $MsgToDisplay                                       = str_replace( '[points]' , $PointsForUrl , get_option( 'rs_success_message_for_pointurl' ) ) ;
                $MsgToDisplay                                       = str_replace( '[offer_name]' , $OfferName , $MsgToDisplay ) ;
                ?>                    
                <div class="rs_success_msg_for_pointurl"><?php echo wp_kses_post( $MsgToDisplay ) ; ?></div>
                <?php
                $PointUrlData[ $uniqueid ][ 'current_usage_count' ] = $UsageCount + 1 ;
                $PointUrlData[ $uniqueid ][ 'used_by' ][ '' ]       = $UserId ;

                update_option( 'points_for_url_click' , $PointUrlData ) ;
                $table_args = array(
                    'user_id'           => $UserId ,
                    'pointstoinsert'    => $PointsForUrl ,
                    'checkpoints'       => 'RPFURL' ,
                    'totalearnedpoints' => $PointsForUrl ,
                        ) ;
                RSPointExpiry::insert_earning_points( $table_args ) ;
                RSPointExpiry::record_the_points( $table_args ) ;
            } else {
                $MsgToDisplay = str_replace( '[points]' , $PointsForUrl , get_option( 'failure_msg_for_count_exceed' ) ) ;
                $MsgToDisplay = str_replace( '[offer_name]' , $OfferName , $MsgToDisplay ) ;
                ?>
                <div class="sk_failure_msg_for_pointsurl"><?php echo wp_kses_post( $MsgToDisplay ) ; ?></div>
                <?php
            }
        }

    }

    RSFunctionForPointURL::init() ;
}