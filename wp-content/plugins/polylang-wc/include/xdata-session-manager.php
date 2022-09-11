<?php
/**
 * @package Polylang-WC
 */

/**
 * A class to store cross domain data in the WC session table.
 *
 * @since 0.3
 */
class PLLWC_Xdata_Session_Manager {
	/**
	 * Writes cross domain data to the session.
	 *
	 * @since 0.3
	 *
	 * @param string $key     A unique hash key.
	 * @param array  $data    Data to store in the session.
	 * @param int    $user_id Optional, user id.
	 * @return void
	 */
	public function set( $key, $data, $user_id = 0 ) {
		global $wpdb;

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		if ( ! empty( $user_id ) ) {
			$data['user_id'] = $user_id;
		}

		$wpdb->insert(
			$wpdb->prefix . 'woocommerce_sessions',
			array(
				'session_key'    => $key,
				'session_value'  => maybe_serialize( $data ),
				'session_expiry' => time() + 2 * MINUTE_IN_SECONDS,
			),
			array(
				'%s',
				'%s',
				'%d',
			)
		);
	}

	/**
	 * Reads cross domain data in the session
	 * and deletes the session to avoid a replay.
	 *
	 * @since 0.3
	 *
	 * @param string $key Session key.
	 * @return array $data
	 */
	public function get( $key ) {
		global $wpdb;

		/** @var stdClass */
		$value = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_sessions WHERE session_key = %s", $key ) );

		if ( ! empty( $value->session_value ) && time() < $value->session_expiry ) {
			$wpdb->delete( $wpdb->prefix . 'woocommerce_sessions', array( 'session_key' => $key ) );
			return maybe_unserialize( $value->session_value );
		}

		wp_die( esc_html__( 'An error has occurred.', 'polylang-wc' ) );
	}
}
