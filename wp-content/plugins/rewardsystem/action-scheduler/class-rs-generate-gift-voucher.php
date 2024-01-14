<?php

/**
 * Generate Gift Voucher Action Scheduler.
 * */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RS_Generate_Gift_Voucher' ) ) {

	/**
	 * Class.
	 * */
	class RS_Generate_Gift_Voucher extends SRP_Action_Scheduler {

		/**
		 * Class Constructor.
		 */
		public function __construct() {

			$this->id                            = 'rs_generate_gift_voucher';
			$this->action_scheduler_name         = 'rs_generate_gift_voucher';
			$this->chunked_action_scheduler_name = 'rs_chunked_generate_gift_voucher_data';
			$this->option_name                   = 'rs_generate_gift_voucher_data';
			$this->settings_option_name          = 'rs_generate_gift_voucher_settings_args';

			// Do ajax action.
			add_action( 'wp_ajax_generate_voucher_code', array( $this, 'do_ajax_action' ) );

			parent::__construct();
		}

		/*
		 * Get progress bar label.
		 */

		public function get_progress_bar_label() {
			$label = __( 'Voucher Code Generation is under process...', 'rewardsystem' );
			return $label;
		}

		/**
		 * Get success message.
		 */
		public function get_success_message() {
			$msg = __( 'Voucher Code generated Successfully.', 'rewardsystem' );
			return $msg;
		}

		/**
		 * Get redirect URL.
		 */
		public function get_redirect_url() {
			return add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpgiftvoucher' ), SRP_ADMIN_URL );
		}

		/*
		 * Do ajax action.
		 */

		public function do_ajax_action() {

			check_ajax_referer( 'fp-create-code', 'sumo_security' );

			try {

				if ( ! isset( $_POST ) ) {
					throw new exception( esc_html__( 'Invalid Request', 'rewardsystem' ) );
				}

				$setting_values = array(
					'codetype'        => isset( $_POST[ 'codetype' ] ) ? wc_clean( wp_unslash( $_POST[ 'codetype' ] ) ) : '',
					'codelength'      => isset( $_POST[ 'codelength' ] ) ? wc_clean( wp_unslash( $_POST[ 'codelength' ] ) ) : '',
					'voucherpoint'    => isset( $_POST[ 'voucherpoint' ] ) ? wc_clean( wp_unslash( $_POST[ 'voucherpoint' ] ) ) : '',
					'noofvoucher'     => isset( $_POST[ 'noofvoucher' ] ) ? wc_clean( wp_unslash( $_POST[ 'noofvoucher' ] ) ) : '',
					'expiry_date'     => isset( $_POST[ 'expirydate' ] ) ? wc_clean( wp_unslash( $_POST[ 'expirydate' ] ) ) : '',
					'excludecontent'  => isset( $_POST[ 'excludecontent' ] ) ? wc_clean( wp_unslash( $_POST[ 'excludecontent' ] ) ) : '',
					'vouchercreated'  => isset( $_POST[ 'vouchercreated' ] ) ? wc_clean( wp_unslash( $_POST[ 'vouchercreated' ] ) ) : '',
					'usertype'        => isset( $_POST[ 'usertype' ] ) ? wc_clean( wp_unslash( $_POST[ 'usertype' ] ) ) : '',
					'usagelimit'      => isset( $_POST[ 'usagelimit' ] ) ? wc_clean( wp_unslash( $_POST[ 'usagelimit' ] ) ) : '',
					'usagelimitvalue' => isset( $_POST[ 'usagelimitvalue' ] ) ? wc_clean( wp_unslash( $_POST[ 'usagelimitvalue' ] ) ) : '',
				);

				$enable_prefix = isset( $_POST[ 'enableprefix' ] ) ? wc_clean( wp_unslash( $_POST[ 'enableprefix' ] ) ) : '';
				$enable_suffix = isset( $_POST[ 'enablesuffix' ] ) ? wc_clean( wp_unslash( $_POST[ 'enablesuffix' ] ) ) : '';
				$prefix_value  = isset( $_POST[ 'prefixvalue' ] ) ? wc_clean( wp_unslash( $_POST[ 'prefixvalue' ] ) ) : '';
				$suffix_value  = isset( $_POST[ 'suffixvalue' ] ) ? wc_clean( wp_unslash( $_POST[ 'suffixvalue' ] ) ) : '';

				if ( 'yes' == $enable_prefix && 'yes' == $enable_suffix ) {
					$setting_values[ 'prefixvalue' ] = $prefix_value;
					$setting_values[ 'suffixvalue' ] = $suffix_value;
				} elseif ( 'yes' == $enable_prefix && 'yes' != $enable_suffix ) {
					$setting_values[ 'prefixvalue' ] = $prefix_value;
				} elseif ( 'yes' != $enable_prefix && 'yes' == $enable_suffix ) {
					$setting_values[ 'suffixvalue' ] = $suffix_value;
				}

				$noofvouchers = absint( $setting_values[ 'noofvoucher' ] );
				$voucher_ids  = array();
				if ( 'numeric' == $setting_values[ 'codetype' ] ) {
					for ( $k = 0; $k < $noofvouchers; $k++ ) {
						$random_code = '';
						for ( $j = 1; $j <= $setting_values[ 'codelength' ]; $j++ ) {
							$random_code .= rand( 0, 9 );
						}

						$voucher_ids[] = $prefix_value . $random_code . $suffix_value;
					}
				} else {
					$list_of_characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
					$character_length   = strlen( $list_of_characters );
					for ( $k = 0; $k < $noofvouchers; $k++ ) {
						$randomstring = '';
						for ( $j = 1; $j <= $setting_values[ 'codelength' ]; $j++ ) {
							$randomstring .= $list_of_characters[ rand( 0, $character_length - 1 ) ];
						}

						if ( '' != $setting_values[ 'excludecontent' ] ) {
							$exclude_string = explode( ',', $setting_values[ 'excludecontent' ] );
							$new_array      = array();
							foreach ( $exclude_string as $value ) {
								$new_array[ $value ] = rand( 0, 9 );
							}

							$randomstring = strtr( $randomstring, $new_array );
						}

						$voucher_ids[] = $prefix_value . $randomstring . $suffix_value;
					}
				}

				if ( empty( $voucher_ids ) ) {
					throw new exception( esc_html__( 'No Data Found', 'rewardsystem' ) );
				}

				$this->schedule_action( $voucher_ids, $setting_values );
				$redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback', 'rs_action_scheduler' => $this->get_id() ), SRP_ADMIN_URL ) );
				wp_send_json_success( array( 'redirect_url' => $redirect_url ) );
			} catch ( Exception $e ) {
				wp_send_json_error( array( 'error' => $e->getMessage() ) );
			}
		}

		/*
		 * Chunked scheduler action.
		 */

		public function chunked_scheduler_action( $voucher_ids ) {

			if ( ! srp_check_is_array( $voucher_ids ) ) {
				return;
			}

			$settings_data = $this->get_settings_data();
			$voucher_ids   = array_unique( $voucher_ids );
			foreach ( $voucher_ids as $voucher_id ) {
				global $wpdb;
				$table_name = "{$wpdb->prefix}rsgiftvoucher";
				$wpdb->insert(
						$table_name, array(
					'points'                       => isset( $settings_data[ 'voucherpoint' ] ) ? $settings_data[ 'voucherpoint' ] : 0,
					'vouchercode'                  => $voucher_id,
					'vouchercreated'               => isset( $settings_data[ 'vouchercreated' ] ) ? $settings_data[ 'vouchercreated' ] : '',
					'voucherexpiry'                => $settings_data[ 'expiry_date' ],
					'memberused'                   => '',
					'voucher_code_usage'           => isset( $settings_data[ 'usertype' ] ) ? $settings_data[ 'usertype' ] : '',
					'voucher_code_usage_limit'     => isset( $settings_data[ 'usagelimit' ] ) ? $settings_data[ 'usagelimit' ] : 0,
					'voucher_code_usage_limit_val' => isset( $settings_data[ 'usagelimitvalue' ] ) ? $settings_data[ 'usagelimitvalue' ] : 0,
						)
				);
			}
		}
	}

}
