<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC Slack Class
 *
 * @package  WooCommerce Slack
 * @author   Bryce <bryce@bryce.se>
 * @since    1.1.0
 */

if ( ! class_exists( 'WC_Slack_API' ) ) {

	class WC_Slack_API {

		protected static $instance = null;

		public function __construct() {

		}

		/**
		 * Start the Class when called
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;

		}


		/**
		 * List all channels
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 */

		public function all_channels( $api_key ) {

			$url = 'https://slack.com/api/conversations.list?types=public_channel,private_channel&token=' . $api_key;

			$resp = wp_remote_get( $url, array(
				'user-agent' => get_bloginfo( 'name' ) . ' / 1.0',
				'headers'=> array(
					'Content-Type' => 'application/json',
				),
			) );

			$channels_array = array( 'select' => 'Select Channel...' );

			$body    = wp_remote_retrieve_body( $resp );
			$decoded = json_decode( $body, true );

			if ( empty( $decoded['channels'] ) ) {
				return $channels_array;
			}

			$channels = $decoded['channels'];

			foreach ( $channels as $channel ) {

				$channels_array[$channel['id']] = '#' . $channel['name'];

			}

			return $channels_array;

		}

		/**
		 * List all groups
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.1.0
		 */

		public function all_groups( $api_key ) {

			$url = 'https://slack.com/api/groups.list?token=' . $api_key;

			$resp = wp_remote_get( $url, array(
				'user-agent' => get_bloginfo( 'name' ) . ' / 1.0',
				'headers'=> array(
					'Content-Type' => 'application/json',
				),
			) );

			$body    = wp_remote_retrieve_body( $resp );
			$decoded = json_decode( $body, true );

			$groups_array = array();

			if ( empty( $decoded['groups'] ) ) {
				return $groups_array;
			}

			$groups = $decoded['groups'];

			if ( ! empty( $groups ) ) {

				foreach ( $groups as $group ) {

					$groups_array[$group['id']] = $group['name'];

				}

			}

			return $groups_array;

		}


		/**
		 * Send Message / Notification
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 *
		 * @param $api_key
		 * @param $channels
		 * @param $emoji
		 * @param $message
		 * @param string $attachment
		 *
		 * @return WP_Error
		 */

		public function send_message( $api_key, $channels, $emoji, $message, $attachment = '' ) {

			$wrapper = $this->wrapper();

			// Check 'attachment' for content. If it has, json encode it (@todo json_encode in attachments method instead and remove this)
			if ( $attachment ) {
				$attachment = array(
					'attachments' => urlencode( json_encode( $attachment ) ),
				);
			} else {
				$attachment = '';
			}

			// Set user to site name (can filter it too)
			$user = ( $wrapper['name'] ) ? $wrapper['name'] : apply_filters( 'wcslack_message_username', get_bloginfo( 'name' ) );

			// Slack API URL to send message & query arg fields for it
			$fields = array(
				'token'       => $api_key,
				'username'    => urlencode( htmlspecialchars_decode( $user ) ),
				'text'        => urlencode( $message ),
				'icon_emoji'  => urlencode( $emoji ),
			);

			// If there's an attachment, merge it with the $fields array
			if ( $attachment ) {
				$fields = array_merge( $fields, $attachment );
			}

			/**
			 * Send the message. If channels is an array, we will
			 * need to send to each of them through separate
			 * API calls for each channel.
			 */
			if ( is_array( $channels ) ) {
				foreach( $channels as $channel ) {
					$fields['channel'] = urlencode( $channel );
					$this->send( $fields );
				}
			} else {
				$fields['channel'] = urlencode( $channels );
				$this->send( $fields );
			}



		}

		/**
		 * Helper method for sending the actual message
		 * to chat.postMessage Slack API.
		 *
		 * @param array $fields
		 *
		 * @return WP_Error
		 */
		public function send( $fields = array() ) {

			$url = add_query_arg( $fields, 'https://slack.com/api/chat.postMessage' );

			// Post to URL
			$resp = wp_remote_post( $url, array(
				'user-agent' => get_bloginfo( 'name' ) . ' / 1.0',
				'headers'=> array(
					'Content-Type' => 'application/json',
				),
			) );

			if ( is_wp_error( $resp ) ) {

				return $resp;

			} else {

				$status = wp_remote_retrieve_response_code( $resp );
				if ( true !== $status ) {

					$body    = wp_remote_retrieve_body( $resp );
					$decoded = (array) json_decode( $body, true );

					if ( ! empty( $decoded['ok'] ) ) {

						return new WP_Error( 'slack_unexpected_response', $decoded['ok'] );

					}

				}

				return $resp;

			}

		}


		/**
		 * Array of data for Attachment feature
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 * @return  array
		 */

		public function attachment( $fields = array(), $title = '', $summary = '' ) {

			$attachment = array(

				array(
					'fallback'  => $summary,
					'title' => $title,
					'text' => $summary,
					'color' => 'good',
					'fields' => $fields,
				),

			);

			return $attachment;

		}


		/**
		 * Checks if submitted API Key is valid
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.0.0
		 * @return  HTTP Status (true / false / ok etc. )
		 */

		public function valid( $api_key ) {

			$resp = wp_remote_get( 'https://slack.com/api/auth.test?token=' . $api_key );

			if ( is_wp_error( $resp ) ) {

				return $resp;

			} else {

				$body    = wp_remote_retrieve_body( $resp );
				$decoded = (array) json_decode( $body, true );

				if ( array_key_exists( 'ok', $decoded ) ) {
					$status = $decoded['ok'];
				}

				return $status;

			}

		}

		/**
		 * Settings Wrapper
		 *
		 * @package WooCommerce Slack
		 * @author  Bryce <bryce@bryce.se>
		 * @since   1.1.0
		 */

		public function wrapper() {

			$WC_Slack_Settings = new WC_Slack_Settings();
			return $WC_Slack_Settings->wrapper();

		}

	}

}
