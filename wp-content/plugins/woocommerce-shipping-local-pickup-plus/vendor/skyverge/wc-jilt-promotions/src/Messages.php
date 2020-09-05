<?php
/**
 * Jilt for WooCommerce Promotions
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Jilt_Promotions;

defined( 'ABSPATH' ) or exit;

/**
 * The messages handler class.
 *
 * @since 1.1.0
 */
class Messages {


	/** @var string user meta key name for storing enabled messages */
	const META_KEY_ENABLED_MESSAGES = '_sv_wc_jilt_enabled_messages';

	/** @var string user meta key name for storing dismissed messages */
	const META_KEY_DISMISSED_MESSAGES = '_sv_wc_jilt_dismissed_messages';

	/** @var string AJAX action hook name for enabling messages */
	const AJAX_ACTION_ENABLE_MESSAGE = 'sv_wc_jilt_enable_message';

	/** @var string AJAX action hook name for dismissing messages */
	const AJAX_ACTION_DISMISS_MESSAGE = 'sv_wc_jilt_dismiss_message';


	/**
	 * Messages constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {

		$this->add_hooks();
	}


	/**
	 * Adds hooks.
	 *
	 * @since 1.1.0
	 */
	private function add_hooks() {

		add_action( 'wp_ajax_' . self::AJAX_ACTION_ENABLE_MESSAGE, [ $this, 'ajax_enable_message' ] );

		add_action( 'wp_ajax_' . self::AJAX_ACTION_DISMISS_MESSAGE, [ $this, 'ajax_dismiss_message' ] );
	}


	/**
	 * Marks a message as enabled for the current user.
	 *
	 * @since 1.1.0
	 *
	 * @param string $message_id message identifier
	 * @return bool
	 */
	public static function enable_message( $message_id ) {

		if ( ! is_string( $message_id ) || self::is_message_enabled( $message_id ) ) {

			return false;
		}

		$enabled_messages   = self::get_enabled_messages();
		$enabled_messages[] = $message_id;

		return (bool) update_user_meta( get_current_user_id(), self::META_KEY_ENABLED_MESSAGES, $enabled_messages );
	}


	/**
	 * Marks a message as dismissed for the current user.
	 *
	 * @since 1.1.0
	 *
	 * @param string $message_id message identifier
	 * @return bool
	 */
	public static function dismiss_message( $message_id ) {

		if ( ! is_string( $message_id ) || self::is_message_dismissed( $message_id ) ) {

			return false;
		}

		$dismissed_messages   = self::get_dismissed_messages();
		$dismissed_messages[] = $message_id;

		return (bool) update_user_meta( get_current_user_id(), self::META_KEY_DISMISSED_MESSAGES, $dismissed_messages );
	}


	/**
	 * Gets the enabled messages for the current user.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public static function get_enabled_messages() {

		return array_filter( (array) get_user_meta( get_current_user_id(), self::META_KEY_ENABLED_MESSAGES, true ) );
	}


	/**
	 * Gets the dismissed messages for the current user.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public static function get_dismissed_messages() {

		return array_filter( (array) get_user_meta( get_current_user_id(), self::META_KEY_DISMISSED_MESSAGES, true ) );
	}


	/**
	 * Determines whether a message is enabled for the current user.
	 *
	 * @since 1.1.0
	 *
	 * @param string $message_id message identifier
	 * @return bool
	 */
	public static function is_message_enabled( $message_id ) {

		$enabled_messages = self::get_enabled_messages();

		return in_array( $message_id, $enabled_messages, true );
	}


	/**
	 * Determines whether a message has been dismissed for the current user.
	 *
	 * @since 1.1.0
	 *
	 * @param string $message_id message identifier
	 * @return bool
	 */
	public static function is_message_dismissed( $message_id ) {

		$dismissed_messages = self::get_dismissed_messages();

		return in_array( $message_id, $dismissed_messages, true );
	}


	/**
	 * Enables a message via AJAX.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function ajax_enable_message() {

		check_ajax_referer( self::AJAX_ACTION_ENABLE_MESSAGE, 'nonce' );

		$message_id = ! empty( $_POST['message_id'] ) ? wc_clean( $_POST['message_id'] ) : '';

		try {

			if ( '' === $message_id || empty( $message_id ) ) {
				throw new \Exception( __( 'Message ID is required', 'sv-wc-jilt-promotions' ) );
			}

			if ( self::is_message_enabled( $message_id ) ) {
				throw new \Exception( __( 'Message already enabled', 'sv-wc-jilt-promotions' ) );
			}

			wp_send_json_success( [
				'is_enabled' => self::enable_message( $message_id ),
			] );

		} catch ( \Exception $exception ) {

			wp_send_json_error( [
				'message' => sprintf(
					/* translators: Placeholder: %s - enable message */
					__( 'Could not enable promotion message. %s', 'sv-wc-jilt-promotions' ),
					$exception->getMessage()
				),
			] );
		}
	}


	/**
	 * Dismisses a message via AJAX.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function ajax_dismiss_message() {

		check_ajax_referer( self::AJAX_ACTION_DISMISS_MESSAGE, 'nonce' );

		$message_id = ! empty( $_POST['message_id'] ) ? wc_clean( $_POST['message_id'] ) : '';

		try {

			if ( '' === $message_id || empty( $message_id ) ) {
				throw new \Exception( __( 'Message ID is required', 'sv-wc-jilt-promotions' ) );
			}

			if ( self::is_message_dismissed( $message_id ) ) {
				throw new \Exception( __( 'Message already dismissed', 'sv-wc-jilt-promotions' ) );
			}

			wp_send_json_success( [
				'is_dismissed' => self::dismiss_message( $message_id ),
			] );

		} catch ( \Exception $exception ) {

			wp_send_json_error( [
				'message' => sprintf(
					/* translators: Placeholder: %s - enable message */
					__( 'Could not enable promotion message. %s', 'sv-wc-jilt-promotions' ),
					$exception->getMessage()
				),
			] );
		}
	}


}
