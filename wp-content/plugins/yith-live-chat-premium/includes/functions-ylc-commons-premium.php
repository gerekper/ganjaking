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

if ( ! function_exists( 'ylc_get_chat_info' ) ) {

	/**
	 * Get chat info
	 *
	 * @since   1.0.0
	 *
	 * @param   $cnv_id integer
	 *
	 * @return  array
	 * @author  Alberto ruggiero
	 */
	function ylc_get_chat_info( $cnv_id ) {

		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare( "
                            SELECT      a.conversation_id,
                                        a.user_id,
                                        a.evaluation,
                                        a.created_at,
                                        a.duration,
                                        a.receive_copy,
                                        b.user_id,
                                        b.user_type,
                                        b.user_name,
                                        b.user_ip,
                                        b.user_email,
                                        b.last_online,
                                        b.vendor_id
                            FROM        {$wpdb->prefix}ylc_chat_sessions a LEFT JOIN {$wpdb->prefix}ylc_chat_visitors b ON a.user_id = b.user_id
                            WHERE       a.conversation_id = %s
                            GROUP BY    a.conversation_id
                            LIMIT       1
                            ", $cnv_id ), ARRAY_A );

	}

}

if ( ! function_exists( 'ylc_get_chat_conversation' ) ) {

	/**
	 * Get chat conversation
	 *
	 * @since   1.0.0
	 *
	 * @param   $cnv_id integer
	 *
	 * @return  array
	 * @author  Alberto ruggiero
	 */
	function ylc_get_chat_conversation( $cnv_id ) {

		global $wpdb;

		return $wpdb->get_results(
			$wpdb->prepare( "
                            SELECT      a.message_id,
                                        a.conversation_id,
                                        a.user_id,
                                        a.user_name,
                                        a.msg,
                                        a.msg_time,
                                        IFNULL( b.user_type, 'operator' ) AS user_type
                            FROM        {$wpdb->prefix}ylc_chat_rows a LEFT JOIN {$wpdb->prefix}ylc_chat_visitors b ON a.user_id = b.user_id
                            WHERE       a.conversation_id = %s
                            ORDER BY    a.msg_time
                            ", $cnv_id ), ARRAY_A );

	}

}

if ( ! function_exists( 'ylc_count_messages' ) ) {

	/**
	 * Count messages in a conversation
	 *
	 * @since   1.0.0
	 *
	 * @param   $cnv_id integer
	 *
	 * @return  integer
	 * @author  Alberto ruggiero
	 */
	function ylc_count_messages( $cnv_id ) {

		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}ylc_chat_rows WHERE conversation_id = %s", $cnv_id ) );

	}
}

if ( ! function_exists( 'ylc_convert_timestamp' ) ) {

	/**
	 * Converts a timestamp in a dd/mm/yyyy HH:MM string
	 *
	 * @since   1.0.0
	 *
	 * @param   $time string
	 *
	 * @return  string
	 * @author  Alberto ruggiero
	 */
	function ylc_convert_timestamp( $time ) {

		$gmt_offset = get_option( 'gmt_offset' );
		$timestamp  = ( $time / 1000 ) + ( $gmt_offset * 3600 );

		return date_i18n( 'd/m/Y H:i', $timestamp );

	}

}

if ( ! function_exists( 'ylc_update_1_1_0' ) ) {

	/**
	 * Add columns for YITH WooCommerce Product Vendors compatibility
	 *
	 * @since   1.1.0
	 * @return  void
	 * @author  Alberto ruggiero
	 */
	function ylc_update_1_1_0() {

		$ylc_db_option = get_option( 'ylc_db_version' );

		if ( empty( $ylc_db_option ) || version_compare( $ylc_db_option, '1.1.0', '<' ) ) {

			global $wpdb;

			$sql = "ALTER TABLE {$wpdb->prefix}ylc_offline_messages ADD vendor_id INT NOT NULL DEFAULT 0";
			$wpdb->query( $sql );

			$sql = "ALTER TABLE {$wpdb->prefix}ylc_chat_visitors ADD vendor_id INT NOT NULL DEFAULT 0";
			$wpdb->query( $sql );

			update_option( 'ylc_db_version', '1.1.0' );

		}

	}

	add_action( 'admin_init', 'ylc_update_1_1_0' );

}

if ( ! function_exists( 'ylc_add_defaults_premium' ) ) {

	/**
	 * Get premium options defaults
	 *
	 * @since   1.1.0
	 *
	 * @param   $defaults array
	 *
	 * @return  array
	 * @author  Alberto Ruggiero
	 */
	function ylc_add_defaults_premium( $defaults ) {

		$premium_defaults = array(
			'offline-mail-sender'          => '',
			'offline-mail-addresses'       => '',
			'offline-send-visitor'         => 'yes',
			'offline-gdpr-compliance'      => 'no',
			'offline-gdpr-checkbox-label'  => esc_html__( 'I agree to the collection and storage of data sent with this form', 'yith-live-chat' ),
			'offline-gdpr-checkbox-desc'   => esc_html__( 'The data collected by this form is used to get in touch with you. For more information, please check out our {privacy policy}', 'yith-live-chat' ),
			'chat-gdpr-compliance'         => 'no',
			'chat-gdpr-checkbox-label'     => esc_html__( 'I agree to the collection and storage of chat logs', 'yith-live-chat' ),
			'chat-gdpr-checkbox-desc'      => esc_html__( 'The data collected by the chat form is used to get in touch with you. For more information, please check out our {privacy policy}', 'yith-live-chat' ),
			'offline-gdpr-privacy-page'    => '',
			'offline-message-body'         => esc_html__( 'Thanks for contacting us. We will answer as soon as possible.', 'yith-live-chat' ),
			'offline-busy'                 => 'no',
			'chat-evaluation'              => 'yes',
			'transcript-send'              => 'yes',
			'transcript-mail-sender'       => '',
			'transcript-message-body'      => esc_html__( 'Below you can find a copy of the chat conversation you have requested.', 'yith-live-chat' ),
			'transcript-send-admin'        => 'no',
			'transcript-send-admin-emails' => '',
			'header-button-color'          => '#009EDB',
			'chat-button-width'            => 260,
			'chat-button-diameter'         => 60,
			'chat-conversation-width'      => 370,
			'form-width'                   => 260,
			'chat-position'                => 'right-bottom',
			'border-radius'                => 5,
			'chat-animation'               => 'bounceIn',
			'chat-button-type'             => 'classic',
			'custom-css'                   => '',
			'operator-role'                => 'editor',
			'operator-avatar'              => '',
			'max-chat-users'               => 2,
			'only-vendor-chat'             => 'no',
			'hide-chat-offline'            => 'no',
			'showing-pages'                => array(),
			'showing-pages-all'            => 'yes',
			'autoplay-delay'               => 10
		);

		return array_merge( $defaults, $premium_defaults );

	}

	add_filter( 'ylc_default_options', 'ylc_add_defaults_premium' );

}

if ( ! function_exists( 'ylc_get_image' ) ) {

	/**
	 * Get user image
	 *
	 * @since   1.1.0
	 *
	 * @param   $type string
	 * @param   $user WP_User
	 *
	 * @return  string
	 * @author  Alberto Ruggiero
	 */
	function ylc_get_image( $type, $user ) {

		switch ( $type ) {

			case 'image':
				$file = esc_attr( get_the_author_meta( 'ylc_operator_avatar', $user->ID ) );
				if ( ! preg_match( '/(jpg|jpeg|png|gif|ico)$/', $file ) ) {
					$file = YLC_ASSETS_URL . '/images/sleep.png';
				}
				break;

			case 'gravatar':
				$email_hash = md5( $user->user_email );
				$file       = 'https://www.gravatar.com/avatar/' . $email_hash . '.jpg?s=60&d=' . YLC_ASSETS_URL . '/images/default-avatar-admin.png';
				break;

			default:

				$op_avatar = ylc_get_option( 'operator-avatar', ylc_get_default( 'operator-avatar' ) );

				if ( $op_avatar != '' ) {

					$file = $op_avatar;

				} else {

					$file = YLC_ASSETS_URL . '/images/default-avatar-admin.png';

				}

		}

		return $file;

	}

}

if ( ! function_exists( 'ylc_save_capabilities' ) ) {

	/**
	 * Save users capabilities
	 *
	 * @since   1.4.0
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	function ylc_save_capabilities() {

		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$roles = $wp_roles->get_names();

		foreach ( $roles as $role_slug => $rolename ) {

			if ( $role_slug == 'administrator' || $role_slug == 'ylc_chat_op' ) {
				continue;
			}

			$role = get_role( $role_slug );
			$role->remove_cap( 'answer_chat' );

			if ( $role_slug == 'yith_vendor' ) {
				update_option( 'yith_wpv_vendors_option_live_chat_management', 'no' );
			}

			if ( isset( $_POST['ylc_enable'] ) && in_array( $role_slug, array_keys( $_POST['ylc_enable'] ) ) ) {
				$role->add_cap( 'answer_chat' );


			}

		}


	}

}

if ( ! function_exists( 'ylc_check_current_page' ) ) {

	/**
	 * Check current page
	 *
	 * @since   1.4.0
	 *
	 * @param $page string
	 *
	 * @return  boolean
	 * @author  Alberto Ruggiero
	 */
	function ylc_check_current_page( $page ) {

		global $post;

		switch ( $page ) {

			case 'blog':
				$posttype = get_post_type( $post );

				return ( ( ( is_archive() ) || ( is_author() ) || ( is_category() ) || ( is_home() ) || ( is_single() ) || ( is_tag() ) ) && ( $posttype == 'post' ) ) ? true : false;
				break;

			case 'home':
				return ( is_home() || is_front_page() );
				break;

			case 'shop':
				return ( is_product() || is_shop() );
				break;

			default:
				return false;

		}


	}

}