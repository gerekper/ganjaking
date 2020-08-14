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

add_filter( 'wp_privacy_personal_data_exporters', 'ylc_register_messages_exporter' );
add_filter( 'wp_privacy_personal_data_exporters', 'ylc_register_chat_exporter' );
add_filter( 'wp_privacy_personal_data_erasers', 'ylc_register_messages_eraser' );
add_filter( 'wp_privacy_personal_data_erasers', 'ylc_register_chat_eraser' );

/**
 * Registers the personal data exporter for offline messages.
 *
 * @since   1.2.6
 *
 * @param   $exporters array
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ylc_register_messages_exporter( $exporters ) {
	$exporters['ylc-messages'] = array(
		'exporter_friendly_name' => esc_html__( 'Live Chat Offline Messages', 'yith-live-chat' ),
		'callback'               => 'ylc_messages_exporter',
	);

	return $exporters;
}

/**
 * Registers the personal data exporter for chat sessions.
 *
 * @since   1.2.6
 *
 * @param   $exporters array
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ylc_register_chat_exporter( $exporters ) {
	$exporters['ylc-chats'] = array(
		'exporter_friendly_name' => esc_html__( 'Live Chat Logs', 'yith-live-chat' ),
		'callback'               => 'ylc_chat_exporter',
	);

	return $exporters;
}

/**
 * Finds and exports personal data associated with an email address for offline message.
 *
 * @since   1.2.6
 *
 * @param   $email_address string
 * @param   $page          integer
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ylc_messages_exporter( $email_address, $page = 1 ) {
	// Limit us to 500 comments at a time to avoid timing out.
	global $wpdb;

	$number           = 500;
	$page             = (int) $page;
	$offset           = $number * ( $page - 1 );
	$data_to_export   = array();
	$offline_messages = $wpdb->get_results( $wpdb->prepare( "
                    SELECT    *
                    FROM      {$wpdb->prefix}ylc_offline_messages
                    WHERE user_email = %s 
                    ORDER BY  id ASC
                    LIMIT {$offset },{$number}
                    ", $email_address ) );

	$message_prop_to_export = array(
		'mail_date'    => esc_html__( 'Date', 'yith-live-chat' ),
		'user_name'    => esc_html__( 'User', 'yith-live-chat' ),
		'user_email'   => esc_html__( 'E-mail', 'yith-live-chat' ),
		'user_message' => esc_html__( 'Message', 'yith-live-chat' ),
		'user_info'    => esc_html__( 'User Info', 'yith-live-chat' ),
	);

	foreach ( (array) $offline_messages as $message ) {
		$message_data_to_export = array();

		foreach ( $message_prop_to_export as $key => $name ) {

			switch ( $key ) {
				case 'mail_date':
					$value = date_i18n( get_option( 'date_format' ), strtotime( $message->{$key} ) );
					break;

				case 'user_info':
					$items = maybe_unserialize( $message->{$key} );
					$value = esc_html__( 'IP Address', 'yith-live-chat' ) . ': ' . $items['ip'] . '<br />';
					$value .= esc_html__( 'OS', 'yith-live-chat' ) . ': ' . $items['os'] . '<br />';
					$value .= esc_html__( 'Browser', 'yith-live-chat' ) . ': ' . $items['browser'] . ' ' . $items['version'] . '<br />';
					$value .= esc_html__( 'Page', 'yith-live-chat' ) . ': ' . $items['page'] . '<br />';
					$value .= esc_html__( 'GDPR Acceptance', 'yith-live-chat' ) . ': ' . ( isset( $items['gdpr_acceptance'] ) ? esc_html__( 'Yes', 'yith-live-chat' ) : esc_html__( 'No', 'yith-live-chat' ) );
					break;

				default:
					$value = $message->{$key};

			}

			if ( ! empty( $value ) ) {
				$message_data_to_export[] = array(
					'name'  => $name,
					'value' => $value,
				);
			}
		}

		$data_to_export[] = array(
			'group_id'    => 'ylc_offline_messages',
			'group_label' => esc_html__( 'Live Chat Offline Messages', 'yith-live-chat' ),
			'item_id'     => "message-{$message->id}",
			'data'        => $message_data_to_export,
		);

	}

	$done = count( $offline_messages ) < $number;

	return array(
		'data' => $data_to_export,
		'done' => $done,
	);
}

/**
 * Finds and exports personal data associated with an email address for chat sessions.
 *
 * @since   1.2.6
 *
 * @param   $email_address string
 * @param   $page          integer
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ylc_chat_exporter( $email_address, $page = 1 ) {
	// Limit us to 500 comments at a time to avoid timing out.
	global $wpdb;

	$number         = 500;
	$page           = (int) $page;
	$offset         = $number * ( $page - 1 );
	$data_to_export = array();
	$chat_logs      = $wpdb->get_results( $wpdb->prepare( "
                    SELECT 
                    a.user_name, 
                    a.user_ip, 
                    a.user_email, 
                    b.conversation_id, 
                    b.evaluation, 
                    b.created_at, 
                    b.duration, 
                    b.receive_copy 
                    FROM {$wpdb->prefix}ylc_chat_visitors a INNER JOIN {$wpdb->prefix}ylc_chat_sessions b 
                    ON a.user_id = b.user_id
                    WHERE a.user_email = %s 
                    ORDER BY  b.created_at ASC
                    LIMIT {$offset },{$number}
                    ", $email_address ) );

	$message_prop_to_export = array(
		'created_at'   => esc_html__( 'Date', 'yith-live-chat' ),
		'user_name'    => esc_html__( 'User', 'yith-live-chat' ),
		'user_email'   => esc_html__( 'E-mail', 'yith-live-chat' ),
		'user_ip'      => esc_html__( 'IP Address', 'yith-live-chat' ),
		'evaluation'   => esc_html__( 'Evaluation', 'yith-live-chat' ),
		'duration'     => esc_html__( 'Chat duration', 'yith-live-chat' ),
		'receive_copy' => esc_html__( 'Request copy', 'yith-live-chat' ),
		'messages'     => esc_html__( 'Messages', 'yith-live-chat' )
	);

	foreach ( (array) $chat_logs as $chat ) {
		$chat_data_to_export = array();

		foreach ( $message_prop_to_export as $key => $name ) {
			$value = '';
			switch ( $key ) {
				case 'created_at':
					$value = ylc_convert_timestamp( $chat->{$key} );
					break;

				case 'user_ip':
					$value = long2ip( $chat->{$key} );
					break;

				case 'receive_copy':
					$value = ( $chat->{$key} ? esc_html__( 'Yes', 'yith-live-chat' ) : esc_html__( 'No', 'yith-live-chat' ) );
					break;

				case 'evaluation':
					switch ( $chat->{$key} ) {
						case 'good':
							$value = esc_html__( 'Good', 'yith-live-chat' );
							break;
						case 'bad':
							$value = esc_html__( 'Bad', 'yith-live-chat' );
							break;
						default:
							$value = '--';
					}

					break;

				case 'messages':
					$chat_logs = ylc_get_chat_conversation( $chat->conversation_id );
					foreach ( $chat_logs as $log ) {
						$value .= ylc_convert_timestamp( $log['msg_time'] ) . ' - ' . $log['user_name'] . ': ' . stripslashes( $log['msg'] ) . '<br/>';
					}
					break;

				default:
					$value = $chat->{$key};

			}

			if ( ! empty( $value ) ) {
				$chat_data_to_export[] = array(
					'name'  => $name,
					'value' => $value,
				);
			}
		}

		$data_to_export[] = array(
			'group_id'    => 'ylc_chat_logs',
			'group_label' => esc_html__( 'Live Chat Logs', 'yith-live-chat' ),
			'item_id'     => "chat-{$chat->conversation_id}",
			'data'        => $chat_data_to_export,
		);

	}

	$done = count( $chat_logs ) < $number;

	return array(
		'data' => $data_to_export,
		'done' => $done,
	);
}

/**
 * Registers the personal data eraser for offline messages.
 *
 * @since   1.2.6
 *
 * @param   $erasers array
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ylc_register_messages_eraser( $erasers ) {
	$erasers['ylc-messages'] = array(
		'eraser_friendly_name' => esc_html__( 'Live Chat Offline Messages', 'yith-live-chat' ),
		'callback'             => 'ylc_messages_eraser',
	);

	return $erasers;
}

/**
 * Registers the personal data eraser for chat sessions.
 *
 * @since   1.2.6
 *
 * @param   $erasers array
 *
 * @return  array
 * @author  Alberto Ruggiero
 */
function ylc_register_chat_eraser( $erasers ) {
	$erasers['ylc-chats'] = array(
		'eraser_friendly_name' => esc_html__( 'Live Chat Logs', 'yith-live-chat' ),
		'callback'             => 'ylc_chat_eraser',
	);

	return $erasers;
}

/**
 * Erases personal data associated with an email address for offline messages.
 *
 * @since 1.2.6
 *
 * @param  $email_address string
 * @param  $page          integer
 *
 * @return array
 */
function ylc_messages_eraser( $email_address, $page = 1 ) {

	global $wpdb;

	if ( empty( $email_address ) ) {
		return array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	$items_removed = false;
	$deleted       = $wpdb->query( $wpdb->prepare( "
                    DELETE
                    FROM      {$wpdb->prefix}ylc_offline_messages
                    WHERE user_email = %s 
                    ", $email_address ) );

	if ( $deleted > 0 ) {

		$items_removed = true;

	}

	return array(
		'items_removed'  => $items_removed,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => true,
	);

}

/**
 * Erases personal data associated with an email address for chat sessions.
 *
 * @since 1.2.6
 *
 * @param  $email_address string
 * @param  $page          integer
 *
 * @return array
 */
function ylc_chat_eraser( $email_address, $page = 1 ) {

	global $wpdb;

	if ( empty( $email_address ) ) {
		return array(
			'items_removed'  => false,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	$items_removed = false;
	$number        = 500;
	$page          = (int) $page;
	$offset        = $number * ( $page - 1 );
	$deleted       = 0;

	$users = $wpdb->get_results( $wpdb->prepare( "
                    SELECT 
                    user_id AS id
                    FROM {$wpdb->prefix}ylc_chat_visitors
                    WHERE user_email = %s 
                    LIMIT {$offset },{$number}
                    ", $email_address ) );

	foreach ( $users as $user ) {

		$conversations = $wpdb->get_results( $wpdb->prepare( "
                    SELECT 
                    conversation_id AS id
                    FROM {$wpdb->prefix}ylc_chat_sessions
                    WHERE user_id = %s 
                    LIMIT {$offset },{$number}
                    ", $user->id ) );

		foreach ( $conversations as $conversation ) {

			$wpdb->query( $wpdb->prepare( "
                    DELETE
                    FROM      {$wpdb->prefix}ylc_chat_rows
                    WHERE conversation_id = %s 
                    ", $conversation->id ) );

		}

		$deleted = $wpdb->query( $wpdb->prepare( "
                    DELETE
                    FROM      {$wpdb->prefix}ylc_chat_sessions
                    WHERE user_id = %s 
                    ", $user->id ) );

	}

	$wpdb->query( $wpdb->prepare( "
                    DELETE
                    FROM      {$wpdb->prefix}ylc_chat_visitors
                    WHERE user_email = %s 
                    ", $email_address ) );

	if ( $deleted > 0 ) {

		$items_removed = true;

	}

	return array(
		'items_removed'  => $items_removed,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => true,
	);

}

