<?php
/**
 * Filtering session object
 * Offers method to read and set properties of the view ()
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\AjaxProductFilter\Classes\Sessions
 * @version 4.0.0
 */

if ( ! defined( 'YITH_WCAN' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAN_Session' ) ) {
	/**
	 * Filter Presets Handling
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAN_Session extends WC_Data {

		/**
		 * Session token (Unique identifier)
		 *
		 * @var string
		 */
		protected $token = '';

		/**
		 * Session hash (md5 digested params)
		 *
		 * @var string
		 */
		protected $hash = '';

		/**
		 * Session Data array
		 *
		 * @var array
		 */
		protected $data;

		/**
		 * Contains a reference to the data store for this class.
		 *
		 * @var YITH_WCAN_Session_Data_Store|null
		 */
		protected $data_store;

		/**
		 * Constructor
		 *
		 * @param int|string|\YITH_WCAN_Session $session Session identifier.
		 *
		 * @throws Exception When not able to load Data Store class.
		 */
		public function __construct( $session = 0 ) {
			// set default values.
			$this->data = array(
				'hash'       => '',
				'token'      => '',
				'origin_url' => apply_filters( 'yith_wcan_default_session_url', yit_get_woocommerce_layered_nav_link() ),
				'query_vars' => apply_filters( 'yith_wcan_default_session_query_vars', array() ),
				'expiration' => '',
			);

			parent::__construct();

			if ( is_numeric( $session ) && $session > 0 ) {
				$this->set_id( $session );
			} elseif ( $session instanceof self ) {
				$this->set_id( $session->get_id() );
			} elseif ( is_string( $session ) && 10 === strlen( $session ) ) {
				$this->set_token( $session );
			} elseif ( is_string( $session ) && 32 === strlen( $session ) ) {
				$this->set_hash( $session );
			} else {
				$this->set_object_read( true );
			}

			$this->data_store = WC_Data_Store::load( 'filter_session' );

			if ( $this->get_id() > 0 || ! empty( $this->get_token() ) || ! empty( $this->get_hash() ) ) {
				$this->data_store->read( $this );
			}
		}

		/* === GETTERS === */

		/**
		 * Get session origin url
		 *
		 * @param string $context Context of the operation.
		 *
		 * @return string Session origin url
		 */
		public function get_origin_url( $context = 'view' ) {
			return $this->get_prop( 'origin_url', $context );
		}

		/**
		 * Get session hash
		 *
		 * @param string $context Context of the operation.
		 *
		 * @return string Session hash
		 */
		public function get_hash( $context = 'view' ) {
			$hash = $this->hash;

			// if object is missing a token, generate one.
			if ( ! $hash && $this->get_origin_url() && $this->get_query_vars() ) {
				$hash = $this->generate_hash();

				$this->hash = $hash;
			}

			return $hash;
		}

		/**
		 * Get session token
		 *
		 * @return string Session token
		 */
		public function get_token() {
			// if object is missing a token, generate one.
			if ( ! $this->token && $this->get_object_read() ) {
				$this->token = $this->generate_token();
			}

			return $this->token;
		}

		/**
		 * Get session origin url
		 *
		 * @param string $context Context of the operation.
		 *
		 * @return array Array of session query vars
		 */
		public function get_query_vars( $context = 'view' ) {
			return $this->get_prop( 'query_vars', $context );
		}

		/**
		 * Get preset selector
		 *
		 * @param string $context Context of the operation.
		 *
		 * @return \WC_DateTime|string Session expiration
		 */
		public function get_expiration( $context = 'view' ) {
			$expiration = $this->get_prop( 'expiration', $context );

			if ( $expiration && 'view' === $context ) {
				return $expiration->date_i18n( 'Y-m-d H:i:s' );
			}

			return $expiration;
		}

		/**
		 * Get formatted wishlist expiration added
		 *
		 * @param string $format Date format (if empty, WP date format will be applied).
		 * @return string Session date of expiration
		 */
		public function get_expiration_formatted( $format = '' ) {
			$expiration = $this->get_expiration( 'edit' );

			if ( $expiration ) {
				$format = $format ? $format : get_option( 'date_format' );
				return $expiration->date_i18n( $format );
			}

			return '';
		}

		/**
		 * Return session share url
		 *
		 * @return string Session share url.
		 */
		public function get_share_url() {
			global $wp_rewrite;

			$origin_url = $this->get_origin_url();
			$token      = $this->get_token();

			if ( ! $origin_url || ! $token ) {
				return false;
			}

			if ( $wp_rewrite->using_mod_rewrite_permalinks() ) {
				$share_url = trailingslashit( $origin_url ) . YITH_WCAN_Session_Factory::get_session_query_param() . "/{$token}/";
			} else {
				$share_url = add_query_arg( YITH_WCAN_Session_Factory::get_session_query_param(), $token, $origin_url );
			}

			return esc_url( apply_filters( 'yith_wcan_session_share_url', $share_url, $this->get_id(), $this ) );
		}

		/* === SETTERS === */

		/**
		 * Set preset slug
		 *
		 * This method is used for internal processing only.
		 *
		 * @param string $hash Filter preset unique token.
		 */
		public function set_hash( $hash ) {
			$this->hash = $hash;
		}

		/**
		 * Generates hash for current url and query vars
		 *
		 * @erturn string Hash for current session
		 */
		public function generate_hash() {
			$origin_url = $this->get_origin_url();
			$query_vars = $this->get_query_vars();

			if ( ! $origin_url || ! $query_vars ) {
				return '';
			}

			$hash = YITH_WCAN_Session_Factory::calculate_hash( $origin_url, $query_vars );

			$this->set_prop( 'hash', $hash );

			return $hash;
		}

		/**
		 * Set session slug
		 *
		 * This method is used for internal processing only.
		 *
		 * @param string $token Session unique token.
		 */
		public function set_token( $token ) {
			$this->token = $token;
		}

		/**
		 * Generates hash for current url and query vars
		 *
		 * @erturn string Hash for current session
		 */
		public function generate_token() {
			$hash = $this->get_hash();

			if ( ! $hash ) {
				return '';
			}

			try {
				return $this->data_store->generate_token( $hash );
			} catch ( Exception $e ) {
				return '';
			}
		}

		/**
		 * Set origin url
		 *
		 * @param string $url Origin url for this session.
		 */
		public function set_origin_url( $url ) {
			$this->set_prop( 'origin_url', esc_url( $url ) );
		}

		/**
		 * Set query vars
		 *
		 * @param array $query_vars Array of query vars for this session.
		 */
		public function set_query_vars( $query_vars ) {
			$query_vars = array_map( 'wc_clean', $query_vars );
			$this->set_prop( 'query_vars', $query_vars );
		}

		/**
		 * Set session expiration date
		 *
		 * @param int|string $expiration Expiration date for current session (timestamp or date).
		 */
		public function set_expiration( $expiration ) {
			$this->set_date_prop( 'expiration', $expiration );
		}

		/**
		 * Checks whether current session is expiring (will expire in less than 24 hours)
		 *
		 * @return bool Whether current session is expiring.
		 */
		public function is_expiring() {
			$expiration         = $this->get_expiration( 'edit' );
			$expiring_threshold = apply_filters( 'yith_wcan_expiring_threshold', DAY_IN_SECONDS, $this->get_id(), $this );

			// if no expiration is set, session isn't expiring.
			if ( ! $expiration ) {
				return false;
			}

			$current_time = time();

			// if session is already expired, it isn't expiring.
			if ( $expiration->getTimestamp() < $current_time ) {
				return false;
			}

			return $current_time >= $expiration->getTimestamp() - $expiring_threshold;
		}

		/**
		 * Increment session's expiration, when it is about to expire
		 */
		public function maybe_extend_duration() {
			$expiration           = $this->get_expiration( 'edit' );
			$expiration_increment = apply_filters( 'yith_wcan_expiration_increment', 2 * DAY_IN_SECONDS, $this->get_id(), $this );

			if ( ! $expiration || ! $this->is_expiring() ) {
				return false;
			}

			$this->set_expiration( $expiration->getTimestamp() + $expiration_increment );
			return true;
		}

		/* === CRUD METHODS === */

		/**
		 * Save data to the database.
		 *
		 * @return int Preset ID
		 */
		public function save() {
			if ( $this->data_store ) {
				// Trigger action before saving to the DB. Allows you to adjust object props before save.
				do_action( 'yith_wcan_before_' . $this->object_type . '_object_save', $this, $this->data_store );

				try {
					if ( $this->get_id() || $this->data_store->get_equivalent_session( $this ) ) {
						$this->data_store->update( $this );
					} else {
						$this->data_store->create( $this );
					}
				} catch ( Exception $e ) {
					wc_caught_exception( $e, __FUNCTION__, func_get_args() );
				}
			}
			return $this->get_id();
		}
	}
}
