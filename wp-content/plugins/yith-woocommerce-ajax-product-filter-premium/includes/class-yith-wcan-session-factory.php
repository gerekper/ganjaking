<?php
/**
 * Session Factory
 *
 * Defines a couple of static methods to allow easy access to Session classes
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Sessions
 * @version 4.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Session_Factory' ) ) {
	/**
	 * Session factory class.
	 */
	class YITH_WCAN_Session_Factory {

		/**
		 * Get session query param
		 *
		 * @return string Name of the query param used to share session's token
		 */
		public static function get_session_query_param() {
			return apply_filters( 'yith_wcan_session_query_param', 'filter_session' );
		}

		/**
		 * Get session query arg
		 *
		 * @return string Session token, if any in the query string.
		 */
		public static function get_session_query_var() {
			global $wp, $wp_query;

			$param     = self::get_session_query_param();
			$query_var = '';

			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( isset( $_REQUEST[ $param ] ) ) {
				$query_var = sanitize_text_field( wp_unslash( $_REQUEST[ $param ] ) );
			} elseif ( ! empty( $wp->query_vars[ $param ] ) ) {
				$query_var = sanitize_text_field( wp_unslash( $wp->query_vars[ $param ] ) );
			}
			// phpcs:enable WordPress.Security.NonceVerification.Recommended

			if ( ! $query_var && $wp_query ) {
				$query_var = get_query_var( $param );
			}

			/**
			 * Pagination fix!
			 *
			 * When using filter session in conjunction with page links, it may happen that session endpoint value contains
			 * trailing /page/{page_number} string.
			 * To make WP process these parameters, we explode session value, check if it contains page number, and only in
			 * that case manually apply 'paged' parameter to the main query.
			 * This method will in any case return session query var, stripping away pagination params when needed.
			 */
			$exploded  = explode( '/', $query_var );
			$query_var = array_shift( $exploded );

			if ( ! empty( $exploded ) && 'page' === $exploded[0] && $wp_query ) {
				$wp_query->set( 'paged', $exploded[1] );
			}

			return $query_var;
		}

		/**
		 * Get current filtering session
		 *
		 * @return YITH_WCAN_Session|bool Current session, or false if no session is found.
		 */
		public static function get_current_session() {
			$token = self::get_session_query_var();

			if ( ! $token ) {
				return false;
			}

			return self::get_session_by_token( $token );
		}

		/**
		 * Get a filter session.
		 *
		 * @param int $session_id Session id.
		 *
		 * @return bool|YITH_WCAN_Session Session to retrieve, or false on failure
		 */
		public static function get_session_by_id( $session_id ) {
			return self::get_session( $session_id );
		}

		/**
		 * Get a filter session.
		 *
		 * @param string $session_token Session token.
		 *
		 * @return bool|YITH_WCAN_Session Session to retrieve, or false on failure
		 */
		public static function get_session_by_token( $session_token ) {
			return self::get_session( $session_token );
		}

		/**
		 * Get a filter session.
		 *
		 * @param string $session_hash Session hash.
		 *
		 * @return bool|YITH_WCAN_Session Session to retrieve, or false on failure
		 */
		public static function get_session_by_hash( $session_hash ) {
			return self::get_session( $session_hash );
		}

		/**
		 * Returns session, given a set of query_vars and the origin url
		 *
		 * @param string $origin_url Filtering url.
		 * @param array  $query_vars Filter parameters.
		 *
		 * @return string Hash for specified parameters.
		 */
		public static function get_session_by_args( $origin_url, $query_vars ) {
			$hash = self::calculate_hash( $origin_url, $query_vars );

			if ( ! $hash ) {
				return false;
			}

			return self::get_session_by_hash( $hash );
		}

		/**
		 * Get a filter session.
		 *
		 * @param string|int $session Session token or id.
		 *
		 * @return bool|YITH_WCAN_Session Session to retrieve, or false on failure
		 */
		public static function get_session( $session ) {
			try {
				return new YITH_WCAN_Session( $session );
			} catch ( Exception $e ) {
				return false;
			}
		}

		/**
		 * Returns hash, given a set of query_vars and the origin url
		 *
		 * @param string $origin_url Filtering url.
		 * @param array  $query_vars Filter parameters.
		 *
		 * @return string Hash for specified parameters.
		 */
		public static function calculate_hash( $origin_url, $query_vars ) {
			$formatted_vars = http_build_query( $query_vars );
			$origin_string  = "{$origin_url}_{$formatted_vars}";

			return md5( $origin_string );
		}

		/**
		 * Generates a session for the passed parameters; if one already exists, that will be returned, without any additional change to db
		 *
		 * @param string $origin_url Filtering url.
		 * @param array  $query_vars Filter parameters.
		 *
		 * @return YITH_WCAN_Session|bool Session, when one was found, or it was possible to create one.
		 */
		public static function generate_session( $origin_url, $query_vars ) {
			$session = self::get_session_by_args( $origin_url, $query_vars );

			if ( $session ) {
				return $session;
			}

			$session = new YITH_WCAN_Session();
			$session->set_query_vars( $query_vars );
			$session->set_origin_url( $origin_url );
			$session->save();

			return $session;
		}
	}
}
