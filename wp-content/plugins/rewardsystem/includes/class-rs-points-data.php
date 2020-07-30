<?php

/*
 * Reward Points Data
 */
if ( ! defined( 'ABSPATH' ) )
    exit ; // Exit if accessed directly.

if ( ! class_exists( 'RS_Points_Data' ) ) {

    /**
     * RS_Points_Data Class.
     */
    class RS_Points_Data {

        /**
         * ID
         */
        protected $id = '' ;

        /**
         * Total Points
         */
        protected static $total_earned = array() ;

        /**
         * Available Points
         */
        protected static $available_points = array() ;

        /**
         * Redeemed Points
         */
        protected static $total_redeemed = array() ;

        /**
         * Expired Points
         */
        protected static $expired_points = array() ;

        /**
         * Class initialization.
         */
        public function __construct( $_id = '' ) {
            $this->id = $_id ;
        }

        /* Total Earned Points in Site for User */

        public function total_earned_points() {
            if ( ! $this->id )
                return 0 ;

            if ( ! empty( self::$total_earned[ $this->id ] ) )
                return self::$total_earned[ $this->id ] ;

            global $wpdb ;
            $PointsTable = $wpdb->prefix . "rspointexpiry" ;
            $TotalPoints = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(earnedpoints) FROM $PointsTable WHERE earnedpoints NOT IN(0) and userid = %d" , $this->id ) ) ;

            if ( ! srp_check_is_array( $TotalPoints ) )
                return 0 ;

            $DeletedPoints               = get_user_meta( $this->id , 'rs_earned_points_before_delete' , true ) ;
            $PointsInOldMeta             = get_user_meta( $this->id , 'rs_user_total_earned_points' , true ) ;
            $PointsInOldVersion          = get_user_meta( $this->id , '_my_reward_points' , true ) ;
            $RemainingPointsInOldVersion = ( $PointsInOldMeta > $PointsInOldVersion ) ? ( float ) $PointsInOldMeta - ( float ) $PointsInOldVersion : 0 ;

            $Point = array_sum( $TotalPoints ) + ( float ) $DeletedPoints + ( float ) $RemainingPointsInOldVersion ;

            self::$total_earned[ $this->id ] = round_off_type( $Point ) ;
            return self::$total_earned[ $this->id ] ;
        }

        /* Available Points for User */

        public function total_available_points() {
            if ( ! $this->id )
                return 0 ;

            if ( ! empty( self::$available_points[ $this->id ] ) )
                return self::$available_points[ $this->id ] ;

            global $wpdb ;
            $PointsTable     = $wpdb->prefix . 'rspointexpiry' ;
            $AvailablePoints = $wpdb->get_col( $wpdb->prepare( "SELECT SUM((earnedpoints-usedpoints)) FROM $PointsTable WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid = %d" , $this->id ) ) ;
            if ( ! srp_check_is_array( $AvailablePoints ) )
                return 0 ;

            $Point = array_sum( $AvailablePoints ) ;

            self::$available_points[ $this->id ] = round_off_type( $Point ) ;
            return self::$available_points[ $this->id ] ;
        }

        /* Available Points for User */

        public function total_available_points_as_currency() {
            if ( ! $this->id )
                return 0 ;

            global $wpdb ;
            $PointsTable     = $wpdb->prefix . 'rspointexpiry' ;
            $AvailablePoints = $wpdb->get_col( $wpdb->prepare( "SELECT SUM((earnedpoints-usedpoints)) FROM $PointsTable WHERE earnedpoints-usedpoints NOT IN(0) and expiredpoints IN(0) and userid = %d" , $this->id ) ) ;
            if ( ! srp_check_is_array( $AvailablePoints ) )
                return 0 ;

            $Point = array_sum( $AvailablePoints ) ;

            $ConvertedRate = redeem_point_conversion( $Point , $this->id , 'price' ) ;
            return srp_formatted_price( round_off_type_for_currency( $ConvertedRate ) ) ;
        }

        /* Total Redeemed Points in Site for User */

        public function total_redeemed_points() {
            if ( ! $this->id )
                return 0 ;

            if ( ! empty( self::$total_redeemed[ $this->id ] ) )
                return self::$total_redeemed[ $this->id ] ;

            global $wpdb ;
            $PointsTable    = $wpdb->prefix . "rspointexpiry" ;
            $RedeemedPoints = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(usedpoints) FROM $PointsTable WHERE usedpoints NOT IN(0) and userid=%d" , $this->id ) ) ;
            if ( ! srp_check_is_array( $RedeemedPoints ) )
                return 0 ;

            $PointsinOlderVersion = self::redeemed_points_in_older_version() ;
            $DeletedPoints        = ( float ) get_user_meta( $this->id , 'rs_redeem_points_before_delete' , true ) ;
            $Point                = array_sum( $RedeemedPoints ) + $PointsinOlderVersion + $DeletedPoints ;

            self::$total_redeemed[ $this->id ] = round_off_type( $Point ) ;
            return self::$total_redeemed[ $this->id ] ;
        }

        public function redeemed_points_in_older_version() {
            $Point                = 0 ;
            $PointsInOlderVersion = get_user_meta( $this->id , '_my_points_log' , true ) ;
            if ( ! srp_check_is_array( $PointsInOlderVersion ) )
                return $Point ;

            foreach ( $PointsInOlderVersion as $Points ) {
                if ( ! isset( $Points[ 'points_redeemed' ] ) )
                    continue ;

                if ( ! empty( $Points[ 'points_redeemed' ] ) )
                    $Point += $Points[ 'points_redeemed' ] ;
            }
            return round_off_type( $Point ) ;
        }

        /* Total Expired Points in Site for User */

        public function total_expired_points() {
            if ( ! $this->id )
                return 0 ;

            if ( ! empty( self::$expired_points[ $this->id ] ) )
                return self::$expired_points[ $this->id ] ;

            global $wpdb ;
            $PointsTable   = $wpdb->prefix . "rspointexpiry" ;
            $ExpiredPoints = $wpdb->get_col( $wpdb->prepare( "SELECT SUM(expiredpoints) FROM $PointsTable WHERE expiredpoints NOT IN(0) and userid=%d" , $this->id ) ) ;
            if ( ! srp_check_is_array( $ExpiredPoints ) )
                return 0 ;

            $DeletedPoints = ( float ) get_user_meta( $this->id , 'rs_expired_points_before_delete' , true ) ;
            $Point         = array_sum( $ExpiredPoints ) + $DeletedPoints ;

            self::$expired_points[ $this->id ] = round_off_type( $Point ) ;

            return self::$expired_points[ $this->id ] ;
        }

        /* Points Log for Specific User */

        public function points_log_for_specific_user( $where = '' ) {
            if ( ! $this->id )
                return array() ;

            global $wpdb ;
            $PointsTable = $wpdb->prefix . 'rspointexpiry' ;
            $PointsLog   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $PointsTable WHERE userid = %d $where" , $this->id ) , ARRAY_A ) ;
            if ( ! srp_check_is_array( $PointsLog ) )
                return array() ;

            return $PointsLog ;
        }

        /* Update User Meta */

        public function update_meta( $meta_key , $value ) {
            if ( ! $this->id )
                return false ;

            update_user_meta( $this->id , $meta_key , $value ) ;
        }

        /* Reset Property */

        public function reset( $user_id ) {

            if ( ! empty( self::$total_earned[ $user_id ] ) )
                unset( self::$total_earned[ $user_id ] ) ;

            if ( ! empty( self::$available_points[ $user_id ] ) )
                unset( self::$available_points[ $user_id ] ) ;

            if ( ! empty( self::$total_redeemed[ $user_id ] ) )
                unset( self::$total_redeemed[ $user_id ] ) ;

            if ( ! empty( self::$expired_points[ $user_id ] ) )
                unset( self::$expired_points[ $user_id ] ) ;
        }

    }

}