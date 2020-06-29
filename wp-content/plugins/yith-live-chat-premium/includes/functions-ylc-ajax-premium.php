<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'ylc_ajax_offline_form' ) ) {

	/**
	 * Offline Form management
	 *
	 * @since   1.0.0
	 *
	 * @param   $form_data array
	 *
	 * @throws  Exception
	 * @return  string
	 * @author  Alberto Ruggiero
	 */
	function ylc_ajax_offline_form() {

		$form_data = $_REQUEST;
		$resp      = array(
			'offline-fail' => false,
			'user-fail'    => false,
			'db-fail'      => false,
		);

		$default_email = get_option( 'admin_email' );
		$user          = YITH_Live_Chat()->user;
		$from          = ( ! empty ( ylc_get_option( 'offline-mail-sender', ylc_get_default( 'offline-mail-sender' ) ) ) ) ? ylc_get_option( 'offline-mail-sender', ylc_get_default( 'offline-mail-sender' ) ) : $default_email;
		$to            = ( ! empty ( ylc_get_option( 'offline-mail-addresses', ylc_get_default( 'offline-mail-addresses' ) ) ) ) ? esc_html( ylc_get_option( 'offline-mail-addresses', ylc_get_default( 'offline-mail-addresses' ) ) ) : $default_email;
		$subject       = apply_filters( 'ylc_offline_mail_subject', esc_html__( 'New offline message', 'yith-live-chat' ) );
		$mail_body     = esc_html__( 'You have received an offline message', 'yith-live-chat' );

		if ( defined( 'YITH_WPV_PREMIUM' ) && get_option( 'yith_wpv_vendors_option_live_chat_management' ) == 'yes' ) {

			$to .= ylc_get_vendor_admins_email( $form_data['vendor_id'] );

		}

		//Send message to the administrators
		if ( ! ylc_send_offline_msg( $from, $to, $subject, $user, $form_data, $mail_body ) ) {
			$resp['offline-fail'] = true;
			YLC_Logger()->error( 'There was an error when sending the email to the admin. Email address: ' . $to );
		}

		//Send a copy to user
		if ( ylc_get_option( 'offline-send-visitor', ylc_get_default( 'offline-send-visitor' ) ) == 'yes' ) {

			$message_body = esc_html( ylc_get_option( 'offline-message-body', ylc_get_default( 'offline-message-body' ) ) );
			$to           = $form_data['email'];
			$subject      = apply_filters( 'ylc_offline_mail_subject_user', esc_html__( 'We have received your offline message', 'yith-live-chat' ) );
			$mail_body    = wp_strip_all_tags( $message_body ) . '<br /><br />' . apply_filters( 'ylc_offline_mail_data_header', esc_html__( 'Here follows a recap of the details you have entered', 'yith-live-chat' ) . ':' );

			if ( ! ylc_send_offline_msg( $from, $to, $subject, $user, $form_data, $mail_body, true ) ) {
				$resp['user-fail'] = true;
				YLC_Logger()->error( 'There was an error when sending the email to the customer. Email address: ' . $to );
			}

		}

		// Add offline message to db
		$args = array(
			'user_name'    => $form_data['name'],
			'user_email'   => $form_data['email'],
			'user_message' => $form_data['message'],
			'user_info'    => array(
				'os'              => $user->get_info( 'os' ),
				'browser'         => $user->get_info( 'browser' ),
				'version'         => $user->get_info( 'version' ),
				'ip'              => $user->get_info( 'ip' ),
				'page'            => $_SERVER['HTTP_REFERER'],
				'gdpr_acceptance' => isset( $form_data['gdpr_acceptance'] ) ? $form_data['gdpr_acceptance'] : '',
			),
			'vendor_id'    => $form_data['vendor_id']
		);

		if ( ! ylc_add_offline_message( $args ) ) {
			$resp['db-fail'] = true;
			YLC_Logger()->error( 'There was an error when storing the message in the database. Email address: ' . $from . ' - Form Data: ' . print_r( $form_data, true ) );
		}

		if ( $resp['offline-fail'] && $resp['db-fail'] ) {

			echo json_encode( array( 'error' => esc_html__( 'Something went wrong. Please try again', 'yith-live-chat' ) ) );
			exit;

		} else if ( $resp['user-fail'] ) {

			echo json_encode( array( 'warn' => esc_html__( 'An error occurred while sending a copy of your message. However, administrators received it correctly.', 'yith-live-chat' ) ) );
			exit;

		}

		echo json_encode( array( 'msg' => esc_html__( 'Successfully sent! Thank you', 'yith-live-chat' ) ) );
		exit;

	}

	add_action( 'wp_ajax_ylc_ajax_offline_form', 'ylc_ajax_offline_form' );
	add_action( 'wp_ajax_nopriv_ylc_ajax_offline_form', 'ylc_ajax_offline_form' );

}

if ( ! function_exists( 'ylc_add_offline_message' ) ) {

	/**
	 * Insert offline message into database
	 *
	 * @since   1.0.0
	 *
	 * @param   $args array
	 *
	 * @return  boolean
	 * @author  Alberto Ruggiero
	 */
	function ylc_add_offline_message( $args ) {

		global $wpdb;

		$result = $wpdb->insert(
			$wpdb->prefix . 'ylc_offline_messages',
			array(
				'user_name'    => $args['user_name'],
				'user_email'   => $args['user_email'],
				'user_message' => stripslashes( $args['user_message'] ),
				'user_info'    => maybe_serialize( $args['user_info'] ),
				'mail_date'    => date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) ),
				'vendor_id'    => $args['vendor_id']
			),
			array( '%s', '%s', '%s', '%s', '%s', '%d' )
		);

		if ( $result === false ) {

			return false;

		} else {

			return true;

		}

	}

}

if ( ! function_exists( 'ylc_ajax_evaluation' ) ) {

	/**
	 * Updates Chat evaluation
	 *
	 * @since   1.0.0
	 *
	 * @param   $data array
	 *
	 * @throws  Exception
	 * @return  string
	 * @author  Alberto Ruggiero
	 */
	function ylc_ajax_evaluation() {

		$data      = $_POST;
		$error_msg = esc_html__( 'Something went wrong. Please try again', 'yith-live-chat' );

		global $wpdb;

		$resp = $wpdb->update(
			$wpdb->prefix . 'ylc_chat_sessions',
			array(
				'evaluation'   => $data['evaluation'],
				'receive_copy' => $data['receive_copy'],
			),
			array( 'conversation_id' => $data['conversation_id'] ),
			array(
				'%s',
				'%d'
			),
			array( '%s' )
		);

		if ( $resp === false ) {
			YLC_Logger()->error( 'There was an error when saving the chat evaluation. Chat ID: ' . $data['conversation_id'] );
			echo json_encode( array( 'error' => $error_msg ) );
			exit;
		}

		if ( ylc_count_messages( $data['conversation_id'] ) != 0 ) {

			$resp = ylc_send_chat_data_user( $data['conversation_id'], $data['receive_copy'], $data['user_email'] );

			if ( $resp === false ) {
				YLC_Logger()->error( 'There was an error when sending the conversation data to the customer. Email Address: ' . $data['user_email'] . ' Chat ID: ' . $data['conversation_id'] );
				echo json_encode( array( 'error' => $error_msg ) );
				exit;
			}

		}

		echo json_encode( array( 'msg' => esc_html__( 'Successfully saved!', 'yith-live-chat' ) ) );
		exit;

	}

	add_action( 'wp_ajax_ylc_ajax_evaluation', 'ylc_ajax_evaluation' );
	add_action( 'wp_ajax_nopriv_ylc_ajax_evaluation', 'ylc_ajax_evaluation' );

}

if ( ! function_exists( 'ylc_save_chat_data' ) ) {

	/**
	 * Save chat transcripts
	 *
	 * @since   1.0.0
	 *
	 * @param   $data array
	 *
	 * @return  array
	 * @author  Alberto Ruggiero
	 */
	function ylc_save_chat_data( $data ) {

		$error_msg = esc_html__( 'Something went wrong. Please try again', 'yith-live-chat' );

		global $wpdb;

		$user_data = array(
			'user_id'     => $data['user_id'],
			'user_type'   => $data['user_type'],
			'user_name'   => @$data['user_name'],
			'user_ip'     => sprintf( '%u', ip2long( $data['user_ip'] ) ), // Support 32bit systems as well not to show up negative val.
			'user_email'  => @$data['user_email'],
			'last_online' => @$data['last_online'] || 0,
			'vendor_id'   => @$data['vendor_id']
		);

		$resp = $wpdb->replace( $wpdb->prefix . 'ylc_chat_visitors', $user_data, array( '%s', '%s', '%s', '%d', '%s', '%s', '%d' ) );

		if ( $resp === false ) {
			YLC_Logger()->error( 'There was an error when saving the visitor data. User Data: ' . print_r( $user_data, true ) );

			return array( 'error' => $error_msg );
		}

		$cnv_data = array(
			'conversation_id' => $data['conversation_id'],
			'user_id'         => $data['user_id'],
			'created_at'      => $data['created_at'],
			'evaluation'      => $data['evaluation'],
			'duration'        => $data['duration'],
			'receive_copy'    => $data['receive_copy']
		);

		$resp = $wpdb->replace( $wpdb->prefix . 'ylc_chat_sessions', $cnv_data, array( '%s', '%s', '%s', '%s', '%s', '%d' ) );

		if ( $resp === false ) {
			YLC_Logger()->error( 'There was an error when saving the conversation data. User Data: ' . print_r( $cnv_data, true ) );

			return array( 'error' => $error_msg );
		}

		if ( ! empty( $data['msgs'] ) ) {

			foreach ( $data['msgs'] as $msg_id => $msg ) {

				$msg_data = array(
					'message_id'      => $msg_id,
					'conversation_id' => $msg['conversation_id'],
					'user_id'         => $msg['user_id'],
					'user_name'       => $msg['user_name'],
					'msg'             => $msg['msg'],
					'msg_time'        => $msg['msg_time']
				);

				$resp = $wpdb->replace( $wpdb->prefix . 'ylc_chat_rows', $msg_data, array( '%s', '%s', '%s', '%s', '%s', '%s' ) );

				if ( $resp === false ) {
					YLC_Logger()->error( 'There was an error when saving the message data. User Data: ' . print_r( $msg_data, true ) );

					return array( 'error' => $error_msg );
				}

			}

		}

		if ( isset( $data['send_email'] ) && $data['send_email'] == 'true' && ylc_count_messages( $data['conversation_id'] ) != 0 ) {

			$resp = ylc_send_chat_data_user( $data['conversation_id'], $data['receive_copy'], $data['user_email'] );

			if ( $resp === false ) {
				YLC_Logger()->error( 'There was an error when sending the conversation data to the customer. Email Address: ' . $data['user_email'] . ' Chat ID: ' . $data['conversation_id'] );

				return array( 'error' => $error_msg );
			}

			$resp = ylc_send_chat_data_admin( $data['conversation_id'], $data['chat_with'], 'operator' );

			if ( $resp === false ) {
				YLC_Logger()->error( 'There was an error when sending the conversation data to the admin. Chat ID: ' . $data['conversation_id'] );

				return array( 'error' => $error_msg );
			}

		}

		return array( 'msg' => esc_html__( 'Successfully saved!', 'yith-live-chat' ) );

	}

}

if ( ! function_exists( 'ylc_send_chat_data_user' ) ) {

	/**
	 * Send chat transcripts to user
	 *
	 * @since   1.0.0
	 *
	 * @param   $cnv_id       string
	 * @param   $receive_copy string
	 * @param   $user_email   string
	 *
	 * @return  boolean
	 * @author  Alberto Ruggiero
	 */
	function ylc_send_chat_data_user( $cnv_id, $receive_copy, $user_email ) {

		$transcript_send = ylc_get_option( 'transcript-send', ylc_get_default( 'transcript-send' ) );

		if ( $transcript_send == 'yes' && ( $receive_copy == 'true' || $receive_copy == '1' ) ) {

			$from = ( ! empty ( ylc_get_option( 'transcript-mail-sender', ylc_get_default( 'transcript-mail-sender' ) ) ) ) ? ylc_get_option( 'transcript-mail-sender', ylc_get_default( 'transcript-mail-sender' ) ) : get_option( 'admin_email' );

			$transcript_message = esc_html( ylc_get_option( 'transcript-message-body', ylc_get_default( 'transcript-message-body' ) ) );

			return ylc_send_chat_data( $cnv_id, $from, $user_email, $transcript_message );

		} else {

			return true;

		}

	}

}

if ( ! function_exists( 'ylc_send_chat_data_admin' ) ) {

	/**
	 * Send chat transcripts to admin
	 *
	 * @since   1.0.0
	 *
	 * @param   $cnv_id    string
	 * @param   $chat_with string
	 * @param   $closed_by string
	 *
	 * @return  boolean
	 * @author  Alberto Ruggiero
	 */
	function ylc_send_chat_data_admin( $cnv_id, $chat_with, $closed_by ) {


		$transcript_send = ylc_get_option( 'transcript-send-admin', ylc_get_default( 'transcript-send-admin' ) );

		if ( $transcript_send == 'yes' ) {

			if ( $chat_with == 'free' ) {

				$op_name = esc_html__( 'No operator has replied', 'yith-live-chat' );

			} else {

				$op_id       = str_replace( 'ylc-op-', '', $chat_with );
				$op_nickname = get_the_author_meta( 'ylc_operator_nickname', $op_id );
				$op_name     = ( $op_nickname != '' ) ? $op_nickname : get_the_author_meta( 'nickname', $op_id );

			}

			$item          = ylc_get_chat_info( $cnv_id );
			$default_email = get_option( 'admin_email' );

			$from      = ( ! empty ( ylc_get_option( 'transcript-mail-sender', ylc_get_default( 'transcript-mail-sender' ) ) ) ) ? ylc_get_option( 'transcript-mail-sender', ylc_get_default( 'transcript-mail-sender' ) ) : $default_email;
			$to        = ( ! empty ( ylc_get_option( 'transcript-send-admin-emails', ylc_get_default( 'transcript-send-admin-emails' ) ) ) ) ? esc_html( ylc_get_option( 'transcript-send-admin-emails', ylc_get_default( 'transcript-send-admin-emails' ) ) ) : $default_email;
			$message   = esc_html__( 'Below you can find a copy of the chat conversation', 'yith-live-chat' );
			$chat_data = array(
				'operator'   => $op_name,
				'user_name'  => $item['user_name'],
				'user_ip'    => long2ip( $item['user_ip'] ),
				'user_email' => $item['user_email'],
				'duration'   => $item['duration'],
				'evaluation' => ( $item['evaluation'] == '' ) ? esc_html__( 'Not received', 'yith-live-chat' ) : ucfirst( $item['evaluation'] ),
				'closed_by'  => ( $closed_by == 'operator' ) ? esc_html__( 'Operator', 'yith-live-chat' ) : esc_html__( 'User', 'yith-live-chat' )
			);

			if ( defined( 'YITH_WPV_PREMIUM' ) && get_option( 'yith_wpv_vendors_option_live_chat_management' ) == 'yes' ) {

				$to .= ylc_get_vendor_admins_email( $item['vendor_id'] );

			}

			return ylc_send_chat_data( $cnv_id, $from, $to, $message, $chat_data, $item['user_name'] );

		} else {

			return true;

		}

	}

}

if ( ! function_exists( 'ylc_get_vendor_admins_email' ) ) {

	/**
	 * Get vendor admins email
	 *
	 * @since   1.1.0
	 *
	 * @param   $vendor_id integer
	 *
	 * @return  string
	 * @author  Alberto Ruggiero
	 */
	function ylc_get_vendor_admins_email( $vendor_id ) {

		$vendor        = yith_get_vendor( $vendor_id, 'vendor' );
		$vendor_admins = $vendor->get_admins();
		$vendor_emails = '';

		foreach ( $vendor_admins as $admin_id ) {

			$admin = get_userdata( $admin_id );

			$vendor_emails .= ', ' . $admin->user_email;

		}

		return $vendor_emails;

	}

}
