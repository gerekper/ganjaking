<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit ;
}
if ( ! class_exists( 'RS_Add_Points_For_User' ) ) {

	/**
	 * RS_Add_Points_For_User Class.
	 */
	class RS_Add_Points_For_User extends WP_Background_Process {

		/**
				 * Action Name.
				 * 
		 * @var string
		 */
		protected $action ;

		/**
				 * User Id.
				 * 
		 * @var string
		 */
		protected $user_id ;

		/**
		 * Initiate new background process
		 */
		public function __construct() {

			$this->user_id = get_current_user_id() ;

			$this->action = 'rs_add_points_for_user_updater_' . $this->user_id ;

			parent::__construct() ;
		}

		/**
		 * Get User Id
		 */
		public function get_user_id() {
			return $this->user_id ;
		}

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
			$this->add_points_for_user( $item ) ;
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
			$offset        = get_user_meta( $this->get_user_id() , 'rs_add_points_background_updater_offset' , true ) ;
			$selected_user = !empty(get_user_meta( $this->get_user_id() , 'selected_user' , true)) ? get_user_meta( $this->get_user_id() , 'selected_user' , true):array() ;
			$SlicedArray   = array_slice( $selected_user , $offset , 1000 ) ;
			if ( srp_check_is_array( $SlicedArray ) ) {
				SRP_Background_Process::callback_to_add_points_for_user( $selected_user , $offset ) ;
				SRP_Background_Process::$rs_progress_bar->fp_increase_progress( 75 ) ;
			} else {
				SRP_Background_Process::$rs_progress_bar->fp_increase_progress( 100 ) ;
				FP_WooCommerce_Log::log( 'Points for User(s) added Successfully' ) ;
				delete_user_meta( $this->get_user_id() , 'rs_add_points_background_updater_offset' ) ;
				delete_user_meta( $this->get_user_id() , 'selected_user' ) ;
				delete_user_meta( $this->get_user_id() , 'selected_options' ) ;
			}
		}

		public function add_points_for_user( $UserId ) {
			if ( 'no_users' != $UserId ) {
				$selected_options = get_user_meta( $this->get_user_id() , 'selected_options' , true ) ;
				if ( 'yes' == $selected_options[ 'state' ] ) {
					update_option( 'rs_email_subject_message' , $selected_options[ 'subject' ] ) ;
					update_option( 'rs_email_message' , $selected_options[ 'message' ] ) ;
					$to         = is_object( get_userdata( $UserId ) ) ? get_userdata( $UserId )->user_email : '' ;
					$new_obj    = new RewardPointsOrder( 0 , 'no' ) ;
					$PointsData = new RS_Points_data( $UserId ) ;
					if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
						$new_obj->check_point_restriction( $selected_options[ 'points' ] , 0 , 'MAP' , $UserId , '' , '' , '' , '' , $selected_options[ 'reason' ] ) ;
					} else {
						$expired_date   = isset( $selected_options[ 'expdate' ] ) && '' != $selected_options[ 'expdate' ] ? $selected_options[ 'expdate' ] . ' ' . gmdate( 'H:i:s' ) : $selected_options[ 'expdate' ] ;
						$valuestoinsert = array( 'expireddate' => strtotime( $expired_date ) , 'manualaddpoints' => 'yes' , 'pointstoinsert' => $selected_options[ 'points' ] , 'event_slug' => 'MAP' , 'user_id' => $UserId , 'reasonindetail' => $selected_options[ 'reason' ] , 'totalearnedpoints' => $selected_options[ 'points' ] ) ;
						$new_obj->total_points_management( $valuestoinsert ) ;
					}

					if ( ( 'true' == $selected_options[ 'enablemail' ] ) && ( '' != $selected_options[ 'points' ] ) ) {
						$Expiry            = ''!=$selected_options[ 'expdate' ] ?  date_display_format(strtotime($selected_options[ 'expdate' ])) : __('All Time Usage', 'rewardsystem') ;
						$shortcode_message = str_replace( '[rs_earned_points]' , $selected_options[ 'points' ] , str_replace( '[rs_expiry]' , $Expiry , $selected_options[ 'message' ] ) ) ;
						$replaced_message  = str_replace( '[balance_points]' , $PointsData->total_available_points() , $shortcode_message ) ;
						$replaced_message  = str_replace( '[site_name]' , get_option( 'blogname' ) , $replaced_message ) ;
											  $user_login        = is_object( get_userdata( $UserId ) ) ? get_userdata( $UserId )->user_login : '' ;
											  $replaced_message  = str_replace( '[username]' , $user_login , $replaced_message ) ;
											  $my_acccount_url   = sprintf('<a href="%s">%s</a>', esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))), __('My Account', 'rewardsystem'));
											  $finalmsg          = str_replace( '[my_account_page]' , $my_acccount_url , $replaced_message ) ;
												
						$headers = "MIME-Version: 1.0\r\n" ;
						$headers .= "Content-Type: text/html; charset=UTF-8\r\n" ;
						$headers .= 'From: ' . get_option( 'woocommerce_email_from_name' ) . ' <' . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;
						$headers .= 'Reply-To: ' . get_option( 'woocommerce_email_from_name' ) . ' <' . get_option( 'woocommerce_email_from_address' ) . ">\r\n" ;

						if ( '2' == $selected_options[ 'email_type' ] ) {
							// Plain Text Type.
							wp_mail( $to , $selected_options[ 'subject' ] , $finalmsg , $headers ) ;
						} else {
							// WC Template.
							global $unsublink2 ;
							$wpnonce    = wp_create_nonce( 'rs_unsubscribe_' . $UserId ) ;
							$unsublink  = esc_url_raw( add_query_arg( array( 'userid' => $UserId , 'unsub' => 'yes' , 'nonce' => $wpnonce ) , site_url() ) ) ;
							$unsublink2 = str_replace( '{rssitelinkwithid}' , $unsublink , get_option( 'rs_unsubscribe_link_for_email' ) ) ;
							add_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
							ob_start() ;
							wc_get_template( 'emails/email-header.php' , array( 'email_heading' => $selected_options[ 'subject' ] ) ) ;
							echo wp_kses_post($finalmsg );
							wc_get_template( 'emails/email-footer.php' ) ;

							$woo_temp_msg = ob_get_clean() ;

							if ( WC_VERSION <= ( float ) ( '2.2.0' ) ) {
								wp_mail( $to , $selected_options[ 'subject' ] , $woo_temp_msg , $headers = '' ) ;
							} else {
								$mailer = WC()->mailer() ;
								$mailer->send( $to , $selected_options[ 'subject' ] , $woo_temp_msg , $headers ) ;
							}

							remove_filter( 'woocommerce_email_footer_text' , 'srp_footer_link' ) ;
						}
					}
				}
			}
			return $UserId ;
		}

	}

}
