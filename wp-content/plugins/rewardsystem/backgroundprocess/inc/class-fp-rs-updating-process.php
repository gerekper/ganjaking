<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'SRP_Updating_Process' ) ) {

	/**
	 * SRP_Updating_Process Class.
	 */
	class SRP_Updating_Process {

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
			check_ajax_referer( 'fp-srp-upgrade' , 'fp_srp_security' ) ;

			try {
				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request' , 'rewardsystem' ) ) ;
				}
				// Return if the current user does not have permission.
				if ( ! current_user_can( 'edit_posts' ) ) {
					throw new exception( esc_html__( "You don't have permission to do this action" , 'rewardsystem' ) ) ;
				}

				$percent = ( int ) get_site_option( 'fp_background_process_' . $this->identifier . '_progress' , 0 ) ;
								$Method = isset($_POST['method_value']) ? wc_clean(wp_unslash($_POST['method_value'])) : '';
								
				if (  'update_products' == $Method ) {
						$url           = add_query_arg( array( 'page' => 'sumo-reward-points-welcome-page' ) , SRP_ADMIN_URL ) ;
														/* translators: %s- Version */
						$responsemsg   = sprintf( __( 'Upgrade to v%s Completed Successfully.' , 'rewardsystem' ) , SRP_VERSION ) ;
				} elseif ( 'add_points'  == $Method || 'remove_points' == $Method ) {
						$url = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsaddremovepoints' ) , SRP_ADMIN_URL ) ;
					if ( 'add_points' == $Method ) {
										$responsemsg   = __( 'Adding Points for User(s) Completed Successfully.' , 'rewardsystem' ) ;
					} else {
											$responsemsg   = __( 'Removing Points for User(s) Completed Successfully.' , 'rewardsystem' ) ;
					}
				} elseif ( 'refresh_points' ==  $Method ) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsgeneral' ) , SRP_ADMIN_URL ) ;
						$responsemsg   = __( 'Refreshing Expired Points for User(s) Completed Successfully.' , 'rewardsystem' ) ;
				} elseif ( 'export_points' == $Method ) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpimportexport' , 'export_points' => 'yes' ) , SRP_ADMIN_URL ) ;
						$gobackurl     = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpimportexport' ) , SRP_ADMIN_URL ) ;
						$redirecturl   = "<a href='$gobackurl'>Go to Settings</a>" ;
						$responsemsg   = __( 'Exporting Points for User(s) Completed Successfully.' , 'rewardsystem' ) . ' ' . $redirecturl ;
				} elseif (  'export_report' == $Method ) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpreportsincsv' , 'export_report' => 'yes' ) , SRP_ADMIN_URL ) ;
						$gobackurl     = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpreportsincsv' ) , SRP_ADMIN_URL ) ;
						$redirecturl   = "<a href='$gobackurl'>Go to Settings</a>" ;
						$responsemsg   = __( 'Exporting Points for User(s) Completed Successfully.' , 'rewardsystem' ) . ' ' . $redirecturl ;
				} elseif (  'export_log' == $Method ) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmasterlog' , 'export_log' => 'yes' ) , SRP_ADMIN_URL ) ;
						$gobackurl     = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmasterlog' ) , SRP_ADMIN_URL ) ;
						$redirecturl   = "<a href='$gobackurl'>Go to Settings</a>" ;
						$responsemsg   = __( 'Exporting Log for User(s) Completed Successfully.' , 'rewardsystem' ) . ' ' . $redirecturl ;
				} elseif ( 'old_points' == $Method ) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsadvanced' ) , SRP_ADMIN_URL ) ;
						$responsemsg   = __( 'Adding Old Points for User(s) Completed Successfully.' , 'rewardsystem' ) ;
				} elseif ( 'bulk_update' == $Method  ) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpproductpurchase' ) , SRP_ADMIN_URL ) ;
						$responsemsg   = __( 'Updating Points for Product(s) Completed Successfully.' , 'rewardsystem' ) ;
				} elseif ( 'bulk_update_for_social'  == $Method) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpsocialreward' ) , SRP_ADMIN_URL ) ;
						$responsemsg   = __( 'Updating Social Reward Points for Product(s) Completed Successfully.' , 'rewardsystem' ) ;
				} elseif (  'bulk_update_buying_points' == $Method) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpbuyingpoints' ) , SRP_ADMIN_URL ) ;
						$responsemsg   = __( 'Updating Buying Points for Product(s) Completed Successfully.' , 'rewardsystem' ) ;
				} elseif ( 'bulk_update_point_price'  == $Method ) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fppointprice' ) , SRP_ADMIN_URL ) ;
						$responsemsg   = __( 'Updating Point Price for Product(s) Completed Successfully.' , 'rewardsystem' ) ;
				} elseif ( 'generate_voucher_code' == $Method ) {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsmodules' , 'section' => 'fpgiftvoucher' ) , SRP_ADMIN_URL ) ;
						$responsemsg   = __( 'Voucher Code generated Successfully.' , 'rewardsystem' ) ;
				} elseif ( 'update_earned_points' == $Method ) {
						$url           = add_query_arg( array( 'page' => 'sumo-reward-points-welcome-page' ) , SRP_ADMIN_URL ) ;
						$responsemsg   = __( 'Earned Points updated Successfully.' , 'rewardsystem' ) ;
				} else {
						$url           = add_query_arg( array( 'page' => 'rewardsystem_callback' , 'tab' => 'fprsadvanced' ) , SRP_ADMIN_URL ) ;
						$responsemsg   = __( 'Applying Points for Previous Order Completed Successfully.' , 'rewardsystem' ) ;
				}

				wp_send_json_success( array( 'percentage' => $percent ,'upgrade_success_url' => $url , 'response_msg' => $responsemsg) ) ;
			} catch ( Exception $ex ) {
				wp_send_json_error( array( 'error' => $ex->getMessage() ) ) ;
			}
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
			if (  'update_products' == $Method ) {
													/* translators: %s- Version */
					$processingmsg = sprintf( __( 'Upgrade to v%s is under Process...' , 'rewardsystem' ) , SRP_VERSION ) ;
			} elseif ( 'add_points'  == $Method || 'remove_points' == $Method ) {
				if ( 'add_points' == $Method ) {
								$processingmsg = __( 'Adding Points for User(s) is under Process...' , 'rewardsystem' ) ;
				} else {
									$processingmsg = __( 'Removing Points for User(s) is under Process...' , 'rewardsystem' ) ;
				}
			} elseif ( 'refresh_points' ==  $Method ) {
					$processingmsg = __( 'Refreshing Points for User(s) is under Process...' , 'rewardsystem' ) ;
			} elseif ( 'export_points' == $Method ) {
					$processingmsg = __( 'Exporting Points for User(s) is under Process...' , 'rewardsystem' ) ;
			} elseif (  'export_report' == $Method ) {
					$processingmsg = __( 'Exporting Points for User(s) is under Process...' , 'rewardsystem' ) ;
			} elseif (  'export_log' == $Method ) {
					$processingmsg = __( 'Exporting Log for User(s) is under Process...' , 'rewardsystem' ) ;
			} elseif ( 'old_points' == $Method ) {
					$processingmsg = __( 'Adding Old Points for User(s) is under Process...' , 'rewardsystem' ) ;
			} elseif ( 'bulk_update' == $Method  ) {
					$processingmsg = __( 'Updating Points for Product(s) is under Process...' , 'rewardsystem' ) ;
			} elseif ( 'bulk_update_for_social'  == $Method) {
					$processingmsg = __( 'Updating Social Reward Points for Product(s) is under Process...' , 'rewardsystem' ) ;
			} elseif (  'bulk_update_buying_points' == $Method) {
					$processingmsg = __( 'Updating Buying Points for Product(s) is under Process...' , 'rewardsystem' ) ;
			} elseif ( 'bulk_update_point_price'  == $Method ) {
					$processingmsg = __( 'Updating Point Price for Product(s) is under Process...' , 'rewardsystem' ) ;
			} elseif ( 'generate_voucher_code' == $Method ) {
					$processingmsg = __( 'Voucher Code generation is under Process...' , 'rewardsystem' ) ;
			} elseif ( 'update_earned_points' == $Method ) {
					$processingmsg = __( 'Updating earned points is under Process...' , 'rewardsystem' ) ;
			} else {
					$processingmsg = __( 'Applying Points for Previous Order is under Process...' , 'rewardsystem' ) ;
			}

			$contents = '.fp_inner{
                                width: ' . $percent . '%;
                        }' ;
												
			wp_register_style( 'fp-srp-updatingprocess-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
			wp_enqueue_style( 'fp-srp-updatingprocess-style' ) ;
			wp_add_inline_style( 'fp-srp-updatingprocess-style' , $contents ) ;     
						
			?>
			<div class="fp_prograssbar_wrapper">
				<h1><?php esc_html_e( 'SUMO Reward Points' , 'rewardsystem' ) ; ?></h1>
				<div id="fp_upgrade_label">
									<h3 class="fp-srp-processing-message"><?php echo wp_kses_post($processingmsg) ; ?></h3>
				</div>
				<div class = "fp_outer">
					<div class = "fp_inner fp-progress-bar">
					</div>
				</div>
				<div id="fp_progress_status">
					<span id = "fp_currrent_status"><?php echo esc_attr($percent) ; ?> </span><?php esc_html_e( ' % Completed' , 'rewardsystem' ) ; ?>
										<input class="fp_method_value" type="hidden" value="<?php echo esc_attr($Method); ?>"/>
				</div>
			</div>
			<?php
		}

	}

}
