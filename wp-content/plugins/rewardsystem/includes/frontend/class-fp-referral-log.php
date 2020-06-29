<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RS_Referral_Log' ) ) {

    class RS_Referral_Log {

        public static function corresponding_referral_log( $referrer ) {
            $ReferralData = get_option( 'rs_referral_log' ) ;
            $UsersLog     = isset( $ReferralData[ $referrer ] ) ? $ReferralData[ $referrer ] : '' ;
            return $UsersLog ;
        }

        public static function corresponding_referral_count( $referrer ) {
            $ReferralData = get_option( 'rs_referral_log' ) ;
            $UsersLog     = isset( $ReferralData[ $referrer ] ) ? $ReferralData[ $referrer ] : array() ;
            return count( $UsersLog ) ;
        }

        public static function total_referral_points( $referrer ) {
            $ReferralData = get_option( 'rs_referral_log' ) ;
            $UsersLog     = isset( $ReferralData[ $referrer ] ) ? $ReferralData[ $referrer ] : array() ;
            $TotalPoints  = array() ;
            if ( srp_check_is_array( $UsersLog ) ) {
                foreach ( $UsersLog as $Points ) {
                    $TotalPoints[] = $Points ;
                }
            }
            return array_sum( $TotalPoints ) ;
        }

        public static function update_referral_log( $referrer , $referral , $points , $sample_data ) {
            $checkreferral = self::check_is_referral_or_referrer( $referrer , $referral , $sample_data ) ;
            if ( $checkreferral == 0 ) {
                $ReferralData = array( $referrer => array( $referral => $points ) ) ;
                arsort( $ReferralData ) ;
                foreach ( $sample_data as $key => $value ) {
                    $ReferralData[ $key ] = $value ;
                }
                update_option( 'rs_referral_log' , $ReferralData ) ;
                // Newly Inserting Datas
            } elseif ( $checkreferral == 1 ) {
                // Parent Key with Referral Person also there
                foreach ( $sample_data as $key => $value ) {
                    foreach ( $value as $subkey => $eachvalue ) {
                        $sample_data[ $key ][ $subkey ] = ( $subkey == $referral ) ? ($points + $eachvalue) : $eachvalue ;
                    }
                }
                update_option( 'rs_referral_log' , $sample_data ) ;
            } elseif ( $checkreferral == 2 ) {
                // Parent Key Found but Referral Person is not available
                $subdatas = array( $referral => $points ) ;
                foreach ( $sample_data as $key => $value ) {
                    foreach ( $value as $subkey => $eachvalue ) {
                        if ( $key == $referrer ) {
                            $subdatas[ $subkey ] = $eachvalue ;
                            arsort( $subdatas ) ;
                        }
                    }
                    if ( $key == $referrer ) {
                        $sample_data[ $key ] = $subdatas ;
                        arsort( $sample_data ) ;
                    }
                }
                update_option( 'rs_referral_log' , $sample_data ) ;
            }
        }

        public static function check_is_referral_or_referrer( $referrer , $referral , $sample_data ) {
            $listofkeys    = array() ;
            $sublistofkeys = array() ;
            foreach ( $sample_data as $key => $value ) {
                $listofkeys[] = $key ;
                foreach ( $value as $eachkey => $value ) {
                    if ( ! in_array( $eachkey , array_filter( $sublistofkeys ) ) ) {
                        $sublistofkeys[] = $eachkey ;
                    }
                }
            }
            if ( in_array( $referrer , array_filter( $listofkeys ) ) ) {
                if ( in_array( $referral , array_filter( $sublistofkeys ) ) ) {
                    return "1" ;  // Parent with Child also found
                } else {
                    return "2" ; // Parent is found but subchild is not found
                }
            } else {
                return "0" ; // None of them found
            }
        }

    }

}