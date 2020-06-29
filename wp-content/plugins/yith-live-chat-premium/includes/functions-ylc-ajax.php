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

if ( ! function_exists( 'ylc_ajax_get_token' ) ) {

	/**
	 * Get token
	 *
	 * @since   1.0.0
	 * @return  string
	 * @author  Alberto Ruggiero
	 */
	function ylc_ajax_get_token() {

		$token = ylc_user_auth();
		$resp  = array( 'token' => $token );

		echo json_encode( $resp );
		exit;

	}

	add_action( 'wp_ajax_ylc_ajax_get_token', 'ylc_ajax_get_token' );
	add_action( 'wp_ajax_nopriv_ylc_ajax_get_token', 'ylc_ajax_get_token' );

}

if ( ! function_exists( 'ylc_ajax_save_chat' ) ) {

	/**
	 * Save chat transcripts if premium active
	 *
	 * @since   1.0.0
	 *
	 * @param   $data array
	 *
	 * @return  string
	 * @author  Alberto Ruggiero
	 */
	function ylc_ajax_save_chat() {

		$data = $_POST;
		$msg  = array( 'msg' => esc_html__( 'Successfully closed!', 'yith-live-chat' ) );

		if ( ylc_check_premium() ) {
			$msg = ylc_save_chat_data( $data );
		}

		echo json_encode( $msg );
		exit;

	}

	add_action( 'wp_ajax_ylc_ajax_save_chat', 'ylc_ajax_save_chat' );
	add_action( 'wp_ajax_nopriv_ylc_ajax_save_chat', 'ylc_ajax_save_chat' );

}