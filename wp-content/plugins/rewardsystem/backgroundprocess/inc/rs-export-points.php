<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ;
}
if ( ! class_exists( 'RS_Export_Points_For_User' ) ) {

	/**
	 * RS_Export_Points_For_User Class.
	 */
	class RS_Export_Points_For_User extends WP_Background_Process {

		/**
				 * Action Name.
				 * 
		 * @var string
		 */
		protected $action = 'rs_export_points_for_user_updater' ;

		/**
		 * Task
		 *
		 * Override this method to perform any actions required on each
		 * queue item. Return the modified item for further processing
		 * in the next pass through. Or, return false to remove the
		 * item from the queue.
		 *
		 * @param mixed $item Queue item to iterate over
		 *
		 * @return mixed
		 */
		protected function task( $item ) {
			$this->export_points_for_user( $item ) ;
			return false ;
		}

		/**
		 * Complete
		 *
		 * Override if applicable, but ensure that the below actions are
		 * performed, or, call parent::complete().
		 */
		protected function complete() {
			parent::complete() ;
			$offset            = get_option( 'rs_export_points_background_updater_offset' ) ;
			$UserSelectionType = get_option( 'rs_user_selection_type_to_export_points' ) ;
			$Selecteduser      = get_option( 'rs_selected_user_to_export_points' ) ;
			if ( '1' == $UserSelectionType ) {
				$args   = array( 'fields' => 'ID' ) ;
				$UserIds = get_users( $args ) ;
			} else if ( '2' == $UserSelectionType  ) {
				$UserIds = is_array( $Selecteduser ) ? $Selecteduser : explode( ',', $Selecteduser ) ;
			} else {
				$selected_user_roles  = get_option( 'rs_selected_user_role_to_export_points' ) ;
				$UserIds              = srp_check_is_array( $selected_user_roles ) ? get_users( array( 'fields' => 'ids', 'role__in' => $selected_user_roles ) ) : array() ;
			}
			$SlicedArray = array_slice( $UserIds , $offset , 1000 ) ;
			if ( srp_check_is_array( $SlicedArray ) ) {
				SRP_Background_Process::callback_to_export_points_for_user( $offset ) ;
				SRP_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
			} else {
				SRP_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
				FP_WooCommerce_Log::log( 'Points for User(s) updated Successfully' ) ;
				delete_option( 'rs_export_points_background_updater_offset' ) ;
			}
		}

		public function export_points_for_user( $UserId ) {
			if ( 'no_users'  != $UserId) {
				$ExportCSVBasedon = get_option( 'selected_format' ) ;
				$UserInfo         = get_user_by( 'id' , $UserId ) ;
				$UserName         = ( '1'  == $ExportCSVBasedon ) ? $UserInfo->user_login : $UserInfo->user_email ;
				$PointsData       = new RS_Points_Data( $UserId ) ;
				$where            = 'AND earnedpoints NOT IN(0)' ;
				$PointsLog        = $PointsData->points_log_for_specific_user( $where ) ;
				if ( ! srp_check_is_array( $PointsLog ) ) {
					return ;
				}

				foreach ( $PointsLog as $Log ) {
					if ( '1' == get_option( 'fp_date_type_selection' ) ) {
						$BoolValue = true ;
					} else {
						$FromDate  = strtotime( get_option( 'selected_start_date' ) 
														. ' ' 
														. '00:00:00' ) ;
						$EndDate   = strtotime( get_option( 'selected_end_date' ) 
														. ' ' 
														. '23:59:00' ) ;
						$BoolValue = ( $FromDate <= $Log[ 'earneddate' ] && $EndDate >= $Log[ 'earneddate' ] ) ;
					}
					if ( ! $BoolValue ) {
						continue ;
					}

					$AvailablePoints = $Log[ 'earnedpoints' ] - $Log[ 'usedpoints' ] ;
					$DateFormat      = get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ;
					$ConvertedExpiry = ( 999999999999 != $Log[ 'expirydate' ] ) ? date_i18n( $DateFormat , $Log[ 'expirydate' ] ) : '-' ;
					$DataToMerge[]   = array(
						'user_name' => $UserName ,
						'points'    => round_off_type( $AvailablePoints ) ,
						'date'      => $ConvertedExpiry ,
							) ;
				}
				$OldData    = get_option( 'rs_data_to_impexp' ) ;
				$MergedData = array_merge( $OldData , $DataToMerge ) ;
				update_option( 'rs_data_to_impexp' , $MergedData ) ;
				return $UserId ;
			}
		}

	}

}
