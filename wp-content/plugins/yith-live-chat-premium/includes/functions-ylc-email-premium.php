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

if ( ! function_exists( 'ylc_send_email_message' ) ) {

	/**
	 * Send email message
	 *
	 * @since   1.0.0
	 *
	 * @param $from      string
	 * @param $to        string
	 * @param $subject   string
	 * @param $message   string
	 * @param $reply_to  string
	 * @param $from_name string
	 *
	 * @return  boolean
	 * @author  Alberto Ruggiero
	 */
	function ylc_send_email_message( $from, $to, $subject, $message, $reply_to, $from_name = '' ) {

		$email = new YLC_Mailer( $from, $to, $subject, $message, $reply_to, $from_name );

		return $email->send();

	}

}

if ( ! function_exists( 'ylc_get_mail_body' ) ) {

	/**
	 * Get mail body
	 *
	 * @since   1.0.0
	 *
	 * @param   $template string
	 * @param   $args     array
	 *
	 * @return  string
	 * @author  Alberto Ruggiero
	 */
	function ylc_get_mail_body( $template, $args ) {

		ob_start();

		ylc_get_template( 'email/' . $template . '.php', $args );

		return ob_get_clean();

	}

}

if ( ! function_exists( 'ylc_send_chat_data' ) ) {

	/**
	 * Send chat transcripts
	 *
	 * @since   1.0.0
	 *
	 * @param   $cnv_id    string
	 * @param   $from      string
	 * @param   $to        string
	 * @param   $message   string
	 * @param   $chat_data array
	 * @param   $user      string
	 *
	 * @return  boolean
	 * @author  Alberto Ruggiero
	 */
	function ylc_send_chat_data( $cnv_id, $from, $to, $message, $chat_data = array(), $user = '' ) {

		$subject = esc_html__( 'Chat conversation copy', 'yith-live-chat' ) . ( ( $user != '' ) ? ': ' . $user : '' );

		$args = array(
			'subject'   => $subject,
			'mail_body' => wp_strip_all_tags( $message ),
			'cnv_id'    => $cnv_id,
			'chat_data' => $chat_data
		);

		$message = ylc_get_mail_body( 'chat-copy', $args );

		return ylc_send_email_message( $from, $to, $subject, $message, $from );

	}

}

if ( ! function_exists( 'ylc_send_offline_msg' ) ) {

	/**
	 * Send offline message
	 *
	 * @since   1.0.0
	 *
	 * @param   $from      string
	 * @param   $to        string
	 * @param   $subject   string
	 * @param   $user      YLC_User
	 * @param   $form_data array
	 * @param   $mail_body string
	 * @param   $user_copy boolean
	 *
	 * @return  boolean
	 * @author  Alberto Ruggiero
	 */
	function ylc_send_offline_msg( $from, $to, $subject, $user, $form_data, $mail_body, $user_copy = false ) {

		$args = array(
			'subject'    => $subject,
			'mail_body'  => $mail_body,
			'name'       => $form_data['name'],
			'email'      => $form_data['email'],
			'message'    => $form_data['message'],
			'os'         => $user->get_info( 'os' ),
			'browser'    => $user->get_info( 'browser' ),
			'version'    => $user->get_info( 'version' ),
			'ip_address' => $user->get_info( 'ip' ),
			'page'       => $_SERVER['HTTP_REFERER'],
		);

		$message    = ylc_get_mail_body( 'offline-message', $args );
		$reply_to   = ( $user_copy ) ? $from : $form_data['email'];
		$from_name  = ( $user_copy ) ? '' : $form_data['name'];
		$from_email = ( $user_copy ) ? $from : $form_data['email'];

		return ylc_send_email_message( $from_email, $to, $subject, $message, $reply_to, $from_name );

	}

}