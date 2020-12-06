<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
* Handling all the AJAX calls in LoginPress - Limit Login Attempts.
*
* @since 1.0.0
* @version 1.0.1
* @class LoginPress_Attempts_AJAX
*/

if ( ! class_exists( 'LoginPress_Attempts_AJAX' ) ) :

	class LoginPress_Attempts_AJAX {

		/*
		* * * * * * * * *
		* Class constructor
		* * * * * * * * * */
		public function __construct() {

			$this->init();
		}
		public function init() {

			$ajax_calls = array(
				'attempts_whitelist'  => false,
				'attempts_blacklist'  => false,
				'attempts_unlock'     => false,
				'whitelist_clear'     => false,
				'blacklist_clear'     => false,
				'white_black_list_ip' => false,
				'white_list_records'  => false,
				'black_list_records'  => false,
				'attempts_bulk'				=> false,
			);

			foreach ( $ajax_calls as $ajax_call => $no_priv ) {
				// code...
				add_action( 'wp_ajax_loginpress_' . $ajax_call, array( $this, $ajax_call ) );

				if ( $no_priv ) {
					add_action( 'wp_ajax_nopriv_loginpress_' . $ajax_call, array( $this, $ajax_call ) );
				}
			}
		}

		public function attempts_bulk() {

			check_ajax_referer( 'loginpress-llla-bulk-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}

			var_dump( $_POST );

			// global $wpdb;
			// $table = "{$wpdb->prefix}loginpress_limit_login_details";
			// $id    = $_POST['id'];
			// $ip    = $_POST['ip'];

			// $wpdb->query( $wpdb->prepare( "UPDATE `{$table}` SET blacklist = '1' WHERE ip = %s", $ip ) );

			wp_die();
		}

		public function attempts_whitelist() {

			check_ajax_referer( 'loginpress-user-llla-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}

			global $wpdb;
			$table = "{$wpdb->prefix}loginpress_limit_login_details";
			$id    = $_POST['id'];
			$ip    = $_POST['ip'];

			$wpdb->query( $wpdb->prepare( "UPDATE `{$table}` SET whitelist = '1' WHERE ip = %s", $ip ) );

			wp_die();

		}

		public function attempts_blacklist() {

			check_ajax_referer( 'loginpress-user-llla-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}

			global $wpdb;
			$table = "{$wpdb->prefix}loginpress_limit_login_details";
			$id    = $_POST['id'];
			$ip    = $_POST['ip'];

			$wpdb->query( $wpdb->prepare( "UPDATE `{$table}` SET blacklist = '1' WHERE ip = %s", $ip ) );

			wp_die();
		}

		public function attempts_unlock() {

			check_ajax_referer( 'loginpress-user-llla-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}

			global $wpdb;
			$table = "{$wpdb->prefix}loginpress_limit_login_details";
			$id    = $_POST['id'];
			$ip    = $_POST['ip'];

			$wpdb->query( $wpdb->prepare( "DELETE FROM `{$table}` WHERE `ip` = %s", $ip ) );

			wp_die();
		}

		public function whitelist_clear() {

			check_ajax_referer( 'loginpress-user-llla-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}

			global $wpdb;
			$table = "{$wpdb->prefix}loginpress_limit_login_details";
			$id    = $_POST['id'];
			$ip    = $_POST['ip'];

			$wpdb->query( $wpdb->prepare( "DELETE FROM `{$table}` WHERE `ip` = %s", $ip ) );
			echo 'Whitelist User Deleted';

			wp_die();
		}

		/**
		 *  Blacklist clear button Delete matched ip rows.
		 *
		 * @return [type] [description]
		 */
		public function blacklist_clear() {

			check_ajax_referer( 'loginpress-user-llla-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}

			global $wpdb;
			$table = "{$wpdb->prefix}loginpress_limit_login_details";
			$id    = $_POST['id'];
			$ip    = $_POST['ip'];

			$wpdb->query( $wpdb->prepare( "DELETE FROM `{$table}` WHERE `ip` = %s", $ip ) );

			echo 'Blacklist User is Deleted';

			wp_die();
		}

		public function white_black_list_ip() {
			check_ajax_referer( 'ip_add_remove', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}

			$ip     = $_POST['ip'];
			$action = sanitize_text_field( $_POST['ip_action'] );

			global $wpdb;
			$table = "{$wpdb->prefix}loginpress_limit_login_details";

			if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				wp_send_json_error( [ 'message' => __( 'Your Ip format is not correct.', 'loginpress-limit-login-attempts' ) ] );
			}

			$exist_record = $wpdb->get_results( "SELECT * FROM $table WHERE ip = '$ip'  limit  1" );

			if ( 'white_list' === $action ) {

				if ( count( $exist_record ) && '1' !== $exist_record[0]->whitelist ) {

					$wpdb->query( $wpdb->prepare( "UPDATE `{$table}` SET `whitelist` = '1' , `blacklist` = '0', `gateway` = 'Manually' WHERE ip = %s", $ip ) );
					wp_send_json_success(
						[
							'message' => __( 'IP address already exist, successfully moved from blacklist to whitelist.', 'loginpress-limit-login-attempts' ),
							'action'  => 'move_black_to_white',
						]
					);
				}

				if ( count( $exist_record ) < 1 ) {
					$wpdb->query( $wpdb->prepare( "INSERT INTO {$table} (ip,whitelist,gateway) values (%s,%s,%s)", $ip, '1', 'Manually' ) );
					wp_send_json_success(
						[
							'message' => __( 'Successfully added in white list', 'loginpress-limit-login-attempts' ),
							'action'  => 'new_whitelist',
						]
					);
				}

				wp_send_json_success(
					[
						'message' => __( 'IP address in whitelist.', 'loginpress-limit-login-attempts' ),
						'action'  => 'already_whitelist',
					]
				);

			}
			if ( 'black_list' === $action ) {

				if ( count( $exist_record ) && '1' !== $exist_record[0]->blacklist ) {

					$wpdb->query( $wpdb->prepare( "UPDATE `{$table}` SET `whitelist` = '0' , `blacklist` = '1', `gateway` = 'Manually' WHERE ip = %s", $ip ) );
					wp_send_json_success(
						[
							'message' => __( 'IP address already exist, successfully moved from whitelist to blacklist.', 'loginpress-limit-login-attempts' ),
							'action'  => 'move_white_to_black',
						]
					);
				}

				if ( count( $exist_record ) < 1 ) {
					$wpdb->query( $wpdb->prepare( "INSERT INTO {$table} (ip,blacklist,gateway) values (%s,%s,%s)", $ip, '1', 'Manually' ) );
					wp_send_json_success(
						[
							'message' => __( 'Successfully added in blacklist', 'loginpress-limit-login-attempts' ),
							'action'  => 'new_blacklist',
						]
					);
				}

				wp_send_json_success(
					[
						'message' => __( 'IP address in blacklist.', 'loginpress-limit-login-attempts' ),
						'action'  => 'already_blacklist',
					]
				);

			}
			wp_die();
		}
		/**
		 * Get whitelist records.
		 *
		 * @return void
		 */
		public function white_list_records() {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}

			global $wpdb;
			$table       = "{$wpdb->prefix}loginpress_limit_login_details";
			$mywhitelist = $wpdb->get_results( "SELECT DISTINCT ip,whitelist FROM {$table} WHERE `whitelist` = 1" );
			$html        = '';
			if ( $mywhitelist ) {
				$loginpress_user_wl_nonce = wp_create_nonce( 'loginpress-user-llla-nonce' );
				foreach ( $mywhitelist as $whitelist ) {
					$html .= '<tr>';
					$html .= '<td data-whitelist-ip="' . $whitelist->ip . '">' . $whitelist->ip . '</td>';
					$html .= '<td><input class="loginpress-whitelist-clear button button-primary" type="button" value="Clear" /><input type="hidden" class="loginpress__user-wl_nonce" name="loginpress__user-wl_nonce" value="' . $loginpress_user_wl_nonce . '"></td>';
					$html .= '</tr>';
				}
				wp_send_json_success( [ 'tbody' => $html ] );
			} else {
				wp_send_json_error( [ 'message' => 'record not found' ] );
			}
		}

		/**
		 * Get blacklist records.
		 *
		 * @return void
		 */
		public function black_list_records() {
			global $wpdb;
			$table       = "{$wpdb->prefix}loginpress_limit_login_details";
			$myblacklist = $wpdb->get_results( "SELECT DISTINCT ip,blacklist FROM {$table} WHERE `blacklist` = 1" );

			if ( $myblacklist ) {
				$loginpress_user_bl_nonce = wp_create_nonce( 'loginpress-user-llla-nonce' );
				$html                     = '';
				foreach ( $myblacklist as $blacklist ) {
					$html .= '<tr>';
					$html .= '<td>' . $blacklist->ip . '</td>';
					$html .= '<td><input class="loginpress-blacklist-clear button button-primary" type="button" value="Clear" /><input type="hidden" class="loginpress__user-bl_nonce" name="loginpress__user-bl_nonce" value="' . $loginpress_user_bl_nonce . '"></td>';
					$html .= '</tr>';
				}
				wp_send_json_success( [ 'tbody' => $html ] );
			} else {
				wp_send_json_error( [ 'message' => 'record not found' ] );
			}
		}

	}

endif;
new LoginPress_Attempts_AJAX();
