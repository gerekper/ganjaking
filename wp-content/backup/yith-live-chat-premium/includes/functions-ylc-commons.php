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

if ( ! function_exists( 'ylc_sanitize_text' ) ) {

	/**
	 * Sanitize strings
	 *
	 * @param   $string
	 * @param   $html
	 *
	 * @return  string
	 * @since   1.0.0
	 *
	 * @author  Alberto ruggiero
	 */
	function ylc_sanitize_text( $string, $html = false ) {
		if ( $html ) {
			return html_entity_decode( $string );
		} else {
			return esc_html( $string );
		}
	}

}

if ( ! function_exists( 'ylc_get_plugin_options' ) ) {

	/**
	 * Get plugin options
	 *
	 * @return  array
	 * @since   1.0.0
	 * @author  Alberto ruggiero
	 */
	function ylc_get_plugin_options() {

		$user_prefix = '';
		$user_type   = 'visitor';

		if ( ( defined( 'YLC_OPERATOR' ) || current_user_can( 'answer_chat' ) ) && ( is_admin() || ylc_frontend_manager() ) ) {
			$user_prefix = 'ylc-op-';
			$user_type   = 'operator';

		} elseif ( is_user_logged_in() ) {

			$user_prefix = 'usr-';

		}

		$options = array(
			'app_id'    => esc_html( ylc_get_option( 'firebase-appurl', ylc_get_default( 'firebase-appurl' ) ) ),
			'api_key'   => esc_html( ylc_get_option( 'firebase-apikey', ylc_get_default( 'firebase-apikey' ) ) ),
			'user_info' => array(
				'user_id'      => $user_prefix . YITH_Live_Chat()->user->ID,
				'user_name'    => apply_filters( 'ylc_nickname', YITH_Live_Chat()->user->display_name ),
				'user_email'   => YITH_Live_Chat()->user->user_email,
				'gravatar'     => md5( YITH_Live_Chat()->user->user_email ),
				'user_type'    => $user_type,
				'avatar_type'  => apply_filters( 'ylc_avatar_type', 'default' ),
				'avatar_image' => apply_filters( 'ylc_avatar_image', '' ),
				'current_page' => YITH_Live_Chat()->user->get_info( 'current_page' ),
				'user_ip'      => YITH_Live_Chat()->user->get_info( 'ip' )
			),
		);

		if ( ! is_admin() && ylc_check_premium() ) {
			$options['styles'] = apply_filters( 'ylc_plugin_opts_premium', array() );
		}

		return $options;

	}

}

if ( ! function_exists( 'ylc_get_current_page' ) ) {

	/**
	 * Get the current page name
	 *
	 * @return  string
	 * @since   1.0.0
	 * @author  Alberto ruggiero
	 */
	function ylc_get_current_page() {

		global $pagenow;

		if ( ! empty( $_GET['page'] ) ) {

			return $_GET['page'];
		} else {
			return $pagenow;
		}

	}

}

if ( ! function_exists( 'ylc_check_premium' ) ) {

	/**
	 * Check if premium version
	 *
	 * @return  boolean
	 * @since   1.2.1
	 * @author  Alberto ruggiero
	 */
	function ylc_check_premium() {

		return ( defined( 'YLC_PREMIUM' ) && YLC_PREMIUM );

	}

}

if ( ! function_exists( 'ylc_frontend_manager' ) ) {

	/**
	 * Check if is frontend dashboard
	 *
	 * @return  boolean
	 * @since   1.2.5
	 * @author  Alberto ruggiero
	 */
	function ylc_frontend_manager() {

		if ( function_exists( 'YITH_Frontend_Manager' ) && isset( YITH_Frontend_Manager()->gui ) ) {
			return YITH_Frontend_Manager()->gui->is_main_page();
		}

		return false;

	}

}

if ( ! function_exists( 'ylc_multivendor_check' ) ) {

	/**
	 * Check if YITH Multi Vendor Premium is active and current vendor has Live Chat enabled
	 *
	 * @return  boolean
	 * @since   1.1.4
	 * @author  Alberto Ruggiero
	 */
	function ylc_multivendor_check() {

		if ( defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM ) {

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( get_option( 'yith_wpv_vendors_option_live_chat_management' ) != 'yes' && $vendor->is_valid() && $vendor->has_limited_access() && ! current_user_can( 'answer_chat' ) ) {
				return false;
			}

		}

		return true;

	}
}

if ( ! function_exists( 'ylc_get_template' ) ) {

	/**
	 * Get template
	 *
	 * @param   $template_name string
	 * @param   $args          array
	 * @param   $template_path string
	 * @param   $default_path  string
	 *
	 * @return  void
	 * @since   1.2.3
	 *
	 * @author  Alberto ruggiero
	 */
	function ylc_get_template( $template_name, $args, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = 'yith-live-chat/';
		}

		if ( ! $default_path ) {
			$default_path = trailingslashit( YLC_TEMPLATE_PATH ) . '/';
		}

		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		include( $template );

	}

}

if ( ! function_exists( 'ylc_get_option' ) ) {

	/**
	 * Get plugin option
	 *
	 * @param   $option  string
	 * @param   $default mixed
	 *
	 * @return  mixed
	 * @since   1.0.0
	 *
	 * @author  Alberto Ruggiero
	 */
	function ylc_get_option( $option, $default = false ) {
		return YLC_Settings::get_instance()->get_option( 'live_chat', $option, $default );
	}

}

if ( ! function_exists( 'ylc_get_default' ) ) {

	/**
	 * Get options defaults
	 *
	 * @param $param string
	 *
	 * @return  mixed
	 * @since   1.1.0
	 *
	 * @author  Alberto Ruggiero
	 */
	function ylc_get_default( $param ) {

		$defaults = array(
			'plugin-enable'   => 'no',
			'text-chat-title' => esc_html__( 'Chat with us', 'yith-live-chat' ),
			'text-welcome'    => esc_html__( 'Have you got question? Write to us!', 'yith-live-chat' ),
			'text-start-chat' => esc_html__( 'Questions, doubts, issues? We\'re here to help you!', 'yith-live-chat' ),
			'text-close'      => esc_html__( 'This chat session has ended', 'yith-live-chat' ),
			'text-offline'    => esc_html__( 'None of our operators are available at the moment. Please, try again later.', 'yith-live-chat' ),
			'text-busy'       => esc_html__( 'Our operators are busy. Please try again later', 'yith-live-chat' ),
		);

		$defaults = apply_filters( 'ylc_default_options', $defaults );

		if ( isset( $defaults[ $param ] ) ) {
			return $defaults[ $param ];
		}

		return false;

	}

}

if ( ! function_exists( 'ylc_user_auth' ) ) {

	/**
	 * User Authentication
	 *
	 * @return  string
	 * @since   1.0.0
	 * @author  Alberto Ruggiero
	 */
	function ylc_user_auth() {

		if ( get_option( 'ylc_authentication_method' ) == '1.4.0' ) {

			if ( ! ylc_is_private_key_valid() ) {
				return '';
			}

			$private_key = json_decode( ylc_get_option( 'firebase-private-key' ) );
			$credentials = array(
				'service_account' => $private_key->client_email,
				'private_key'     => $private_key->private_key
			);

		} else {

			$credentials = array(
				'secret' => esc_html( ylc_get_option( 'firebase-appsecret' ) ),
			);

		}

		$prefix = ( is_user_logged_in() && ! defined( 'YLC_OPERATOR' ) ) ? 'usr-' : '';
		$data   = array(
			'uid'         => $prefix . YITH_Live_Chat()->user->ID,
			'is_operator' => ( defined( 'YLC_OPERATOR' ) ) ? true : false,
		);
		$opts   = array(
			'admin' => ( current_user_can( 'manage_options' ) ) ? true : false,
		);

		$token_gen = new YLC_Token( $credentials );

		return $token_gen->get_token( $data, $opts );

	}

}

if ( ! function_exists( 'ylc_get_strings' ) ) {

	/**
	 * Get all strings for frontend and backend
	 *
	 * @param   $context string
	 *
	 * @return  array
	 * @since   1.1.0
	 *
	 * @author  Alberto Ruggiero
	 */
	function ylc_get_strings( $context ) {

		if ( $context == 'console' ) {

			$msg = array(
				'no_msg'            => esc_html__( 'No messages found', 'yith-live-chat' ),
				'connecting'        => esc_html__( 'Connecting', 'yith-live-chat' ),
				'writing'           => esc_html__( '%s is writing', 'yith-live-chat' ),
				'please_wait'       => esc_html__( 'Please wait', 'yith-live-chat' ),
				'conn_err'          => esc_html__( 'Connection error!', 'yith-live-chat' ),
				'online_btn'        => esc_html__( 'Online', 'yith-live-chat' ),
				'offline_btn'       => esc_html__( 'Offline', 'yith-live-chat' ),
				'connect'           => esc_html__( 'Connect', 'yith-live-chat' ),
				'disconnect'        => esc_html__( 'Disconnect', 'yith-live-chat' ),
				'you_offline'       => esc_html__( 'You are offline', 'yith-live-chat' ),
				'ntf_close_console' => esc_html__( 'If you leave the chat, you will be logged out. However you will be able to save the conversations into your server when you will come back in the console!', 'yith-live-chat' ),
				'new_msg'           => esc_html__( 'New Message', 'yith-live-chat' ),
				'new_user_online'   => esc_html__( 'New User Online', 'yith-live-chat' ),
				'saving'            => esc_html__( 'Saving', 'yith-live-chat' ),
				'waiting_users'     => ( ylc_check_premium() ) ? esc_html__( 'User queue: %d', 'yith-live-chat' ) : esc_html__( 'There are people waiting to talk', 'yith-live-chat' ),
				'talking_label'     => esc_html__( 'Talking with %s', 'yith-live-chat' ),
				'current_shop'      => esc_html__( '%s shop', 'yith-live-chat' ),
				'macro_title'       => esc_html__( 'Apply Macro', 'yith-live-chat' ),
				'macro_err'         => esc_html__( 'No results match', 'yith-live-chat' ),
				'visitor'           => esc_html__( 'Visitor', 'yith-live-chat' ),
				'operator'          => esc_html__( 'Operator', 'yith-live-chat' ),
				'registered'        => esc_html__( 'Registered User', 'yith-live-chat' ),
				'no_users'          => esc_html__( 'No users connected', 'yith-live-chat' ),
			);

		} else {

			$msg = array(
				'close_msg_user'   => esc_html__( 'The user has closed the conversation', 'yith-live-chat' ),
				'no_op'            => esc_html__( 'No operators online', 'yith-live-chat' ),
				'connecting'       => esc_html__( 'Connecting', 'yith-live-chat' ),
				'writing'          => esc_html__( '%s is writing', 'yith-live-chat' ),
				'sending'          => esc_html__( 'Sending', 'yith-live-chat' ),
				'field_empty'      => esc_html__( 'Please fill out all required fields', 'yith-live-chat' ),
				'invalid_username' => esc_html__( 'Username is invalid', 'yith-live-chat' ),
				'invalid_email'    => esc_html__( 'Email is invalid', 'yith-live-chat' ),
				'already_logged'   => esc_html__( 'A user is already logged in with the same email address', 'yith-live-chat' ),
			);

		}

		return array(
			'months'       => array(
				'jan' => esc_html__( 'January', 'yith-live-chat' ),
				'feb' => esc_html__( 'February', 'yith-live-chat' ),
				'mar' => esc_html__( 'March', 'yith-live-chat' ),
				'apr' => esc_html__( 'April', 'yith-live-chat' ),
				'may' => esc_html__( 'May', 'yith-live-chat' ),
				'jun' => esc_html__( 'June', 'yith-live-chat' ),
				'jul' => esc_html__( 'July', 'yith-live-chat' ),
				'aug' => esc_html__( 'August', 'yith-live-chat' ),
				'sep' => esc_html__( 'September', 'yith-live-chat' ),
				'oct' => esc_html__( 'October', 'yith-live-chat' ),
				'nov' => esc_html__( 'November', 'yith-live-chat' ),
				'dec' => esc_html__( 'December', 'yith-live-chat' )
			),
			'months_short' => array(
				'jan' => esc_html__( 'Jan', 'yith-live-chat' ),
				'feb' => esc_html__( 'Feb', 'yith-live-chat' ),
				'mar' => esc_html__( 'Mar', 'yith-live-chat' ),
				'apr' => esc_html__( 'Apr', 'yith-live-chat' ),
				'may' => esc_html__( 'May', 'yith-live-chat' ),
				'jun' => esc_html__( 'Jun', 'yith-live-chat' ),
				'jul' => esc_html__( 'Jul', 'yith-live-chat' ),
				'aug' => esc_html__( 'Aug', 'yith-live-chat' ),
				'sep' => esc_html__( 'Sep', 'yith-live-chat' ),
				'oct' => esc_html__( 'Oct', 'yith-live-chat' ),
				'nov' => esc_html__( 'Nov', 'yith-live-chat' ),
				'dec' => esc_html__( 'Dec', 'yith-live-chat' )
			),
			'time'         => array(
				'suffix'  => esc_html__( 'ago', 'yith-live-chat' ),
				'seconds' => esc_html__( 'less than a minute', 'yith-live-chat' ),
				'minute'  => esc_html__( 'about a minute', 'yith-live-chat' ),
				'minutes' => esc_html__( '%d minutes', 'yith-live-chat' ),
				'hour'    => esc_html__( 'about an hour', 'yith-live-chat' ),
				'hours'   => esc_html__( 'about %d hours', 'yith-live-chat' ),
				'day'     => esc_html__( 'a day', 'yith-live-chat' ),
				'days'    => esc_html__( '%d days', 'yith-live-chat' ),
				'month'   => esc_html__( 'about a month', 'yith-live-chat' ),
				'months'  => esc_html__( '%d months', 'yith-live-chat' ),
				'year'    => esc_html__( 'about a year', 'yith-live-chat' ),
				'years'   => esc_html__( '%d years', 'yith-live-chat' ),
			),
			'msg'          => $msg
		);

	}

}

if ( ! function_exists( 'ylc_is_setup_complete' ) ) {

	function ylc_is_setup_complete() {

		$auth_method = get_option( 'ylc_authentication_method' );

		if ( $auth_method == '' ) {

			$app_url    = ylc_get_option( 'firebase-appurl' );
			$app_secret = ylc_get_option( 'firebase-appsecret' );

			if ( ! $app_url || ! $app_secret ) {
				return false;
			}

		}

		if ( $auth_method == '1.4.0' ) {

			$api_key     = ylc_get_option( 'firebase-apikey', ylc_get_default( 'firebase-apikey' ) );
			$private_key = ylc_get_option( 'firebase-private-key', ylc_get_default( 'firebase-private-key' ) );

			if ( ! $api_key || ! $private_key ) {
				return false;
			}

			if ( ! ylc_is_private_key_valid() ) {
				return false;
			}

		}

		return true;

	}

}

if ( ! function_exists( 'ylc_is_private_key_valid' ) ) {

	function ylc_is_private_key_valid() {

		$private_key         = ylc_get_option( 'firebase-private-key' );
		$private_key_decoded = json_decode( ylc_get_option( 'firebase-private-key' ) );
		if ( empty( $private_key_decoded ) && ! empty( $private_key ) ) {
			return false;
		}

		return true;

	}

}

if ( ! function_exists( 'ylc_check_valid_user' ) ) {

	function ylc_check_valid_user( $user ) {

		if ( $user == null ) {
			return false;
		}

		if ( is_wp_error( $user ) ) {
			return false;
		}

		return true;

	}

}