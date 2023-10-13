<?php
/**
 * Class YITH_WCBK_Cache
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Cache' ) ) {
	/**
	 * Class YITH_WCBK_Cache
	 *
	 * @since  2.0.0
	 */
	class YITH_WCBK_Cache {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The current cache context.
		 *
		 * @var string
		 */
		protected $context = '';

		/**
		 * Transient expiration in seconds.
		 *
		 * @var int
		 */
		public $transient_expiration = MONTH_IN_SECONDS;

		/**
		 * Is cache disabled?
		 *
		 * @return bool
		 * @deprecated since 2.0.5: use YITH_WCBK_Cache::is_enabled() instead
		 */
		public function no_cache() {
			return ! $this->is_enabled();
		}

		/**
		 * Is cache enabled?
		 *
		 * @return bool
		 * @since 2.0.5
		 */
		public function is_enabled() {
			return yith_wcbk()->settings->is_cache_enabled();
		}

		/**
		 * Get the cache context.
		 *
		 * @return string
		 * @since 5.0.0
		 */
		public function get_context(): string {
			return $this->context;
		}

		/**
		 * Set the cache context.
		 *
		 * @param string $context The context (use a small string).
		 *
		 * @return void
		 * @since 5.0.0
		 */
		public function set_context( string $context ) {
			$this->context = $context;
		}

		/**
		 * Set the cache context.
		 *
		 * @return void
		 * @since 5.0.0
		 */
		public function set_default_context() {
			$this->set_context( '' );
		}

		/**
		 * Get the cache prefix for a specific object type.
		 *
		 * @param string $object_type The object type.
		 *
		 * @return string
		 * @since 5.0.0
		 */
		protected function get_prefix( $object_type ) {
			$parts = array(
				'yith_wcbk',
				$this->get_context(),
				get_option( 'yith_wcbk_' . $object_type . '_cache_prefix', '' ),
			);

			return implode( '_', array_filter( $parts ) ) . '_';
		}

		/**
		 * Invalidate the cache for a specific object type
		 *
		 * @param string $object_type The object type.
		 *
		 * @since 5.0.0
		 */
		private function invalidate( $object_type ) {
			$new_value = str_replace( '.', 'd', (string) microtime( true ) );
			update_option( 'yith_wcbk_' . $object_type . '_cache_prefix', $new_value );
		}

		/**
		 * Get the transient key by arguments.
		 *
		 * @param string|array $key_args The arguments.
		 *
		 * @return string
		 */
		public function get_transient_key( $key_args ) {
			if ( is_array( $key_args ) ) {
				$key_string = '';
				if ( isset( $key_args['function'] ) ) {
					$key_string .= $key_args['function'] . '_';
					unset( $key_args['function'] );
				}
				$key_string_to_encode = '';
				foreach ( $key_args as $current_key => $current_value ) {
					$key_string_to_encode .= $current_key . '_';
					if ( is_array( $current_value ) || is_object( $current_value ) ) {
						$key_string_to_encode .= wp_json_encode( $current_value ) . '_';
					} else {
						$key_string_to_encode .= (string) $current_value . '_';
					}
				}
				$key_args = $key_string . md5( $key_string_to_encode );
			}

			return (string) $key_args;
		}

		/**
		 * Retrieve the transient name
		 *
		 * @param string $object_type Object type.
		 * @param int    $id          Object ID.
		 *
		 * @return string
		 */
		public function get_transient_name( $object_type, $id ) {
			return $this->get_prefix( $object_type ) . $object_type . '_' . $id;
		}

		/**
		 * Delete the object transient
		 *
		 * @param string $object_type Object type.
		 * @param int    $id          Object ID.
		 *
		 * @return bool
		 */
		public function delete_object_transient( $object_type, $id ) {
			$transient_name = $this->get_transient_name( $object_type, $id );

			return delete_transient( $transient_name );
		}

		/**
		 * Get the object data
		 *
		 * @param string       $object_type Object type.
		 * @param int          $id          Object ID.
		 * @param string|array $key_args    The key arguments.
		 *
		 * @return mixed
		 */
		public function get_object_data( $object_type, $id, $key_args = '' ) {
			if ( ! $this->is_enabled() ) {
				return null;
			}
			$id = apply_filters( 'yith_wcbk_cache_get_object_data_object_id', $id, $object_type, $key_args );
			$id = apply_filters( "yith_wcbk_cache_get_object_data_{$object_type}_id", $id, $object_type, $key_args );

			$transient_data = get_transient( $this->get_transient_name( $object_type, $id ) );
			if ( '' === $key_args ) {
				$data = $transient_data;
			} else {
				$key_args = $this->get_transient_key( $key_args );
				$data     = null;
				if ( ! ! $transient_data && array_key_exists( $key_args, $transient_data ) ) {
					$data = $transient_data[ $key_args ];
				}
			}

			return $data;
		}

		/**
		 * Set the object data
		 *
		 * @param string       $object_type Object type.
		 * @param int          $id          Object ID.
		 * @param string|array $key_args    The key arguments.
		 * @param mixed        $value       The value to be cached.
		 *
		 * @return bool
		 */
		public function set_object_data( $object_type, $id, $key_args, $value ) {
			if ( ! $this->is_enabled() ) {
				return null;
			}
			$key_args       = $this->get_transient_key( $key_args );
			$transient_name = $this->get_transient_name( $object_type, $id );
			$transient_data = get_transient( $transient_name );
			$transient_data = ! ! $transient_data && is_array( $transient_data ) ? $transient_data : array();

			$transient_data[ $key_args ] = $value;

			return set_transient( $transient_name, $transient_data, $this->transient_expiration );
		}

		/**
		 * Delete the object data
		 *
		 * @param string       $object_type Object type.
		 * @param int          $id          Object ID.
		 * @param string|array $key_args    The key arguments.
		 *
		 * @return bool
		 */
		public function delete_object_data( $object_type, $id, $key_args = '' ) {
			if ( ! $this->is_enabled() ) {
				return null;
			}
			if ( '' === $key_args ) {
				$response = $this->delete_object_transient( $object_type, $id );
			} else {
				$key_args       = $this->get_transient_key( $key_args );
				$transient_name = $this->get_transient_name( $object_type, $id );
				$transient_data = get_transient( $transient_name );
				$transient_data = ! ! $transient_data && is_array( $transient_data ) ? $transient_data : array();
				if ( isset( $transient_data[ $key_args ] ) ) {
					unset( $transient_data[ $key_args ] );
				}

				$response = set_transient( $transient_name, $transient_data, $this->transient_expiration );
			}

			do_action( "yith_wcbk_cache_delete_{$object_type}_data", $id, $key_args, $response );
			do_action( 'yith_wcbk_cache_delete_object_data', $object_type, $id, $key_args, $response );

			return $response;
		}

		/**
		 * Get the product data
		 *
		 * @param int          $id       Object ID.
		 * @param string|array $key_args The key arguments.
		 *
		 * @return mixed
		 */
		public function get_product_data( $id, $key_args = '' ) {
			return $this->get_object_data( 'product', $id, $key_args );
		}

		/**
		 * Set product data.
		 *
		 * @param int          $id       Object ID.
		 * @param string|array $key_args The key arguments.
		 * @param mixed        $value    The value to be cached.
		 *
		 * @return bool
		 */
		public function set_product_data( $id, $key_args, $value ) {
			return $this->set_object_data( 'product', $id, $key_args, $value );
		}

		/**
		 * Delete product data.
		 *
		 * @param int          $id       Object ID.
		 * @param string|array $key_args The key arguments.
		 *
		 * @return bool
		 */
		public function delete_product_data( $id, $key_args = '' ) {
			return $this->delete_object_data( 'product', $id, $key_args );
		}

		/**
		 * Get the booking data
		 *
		 * @param int          $id       Object ID.
		 * @param string|array $key_args The key arguments.
		 *
		 * @return mixed
		 */
		public function get_booking_data( $id, $key_args = '' ) {
			return $this->get_object_data( 'booking', $id, $key_args );
		}

		/**
		 * Set the booking data
		 *
		 * @param int          $id       Object ID.
		 * @param string|array $key_args The key arguments.
		 * @param mixed        $value    The value to be cached.
		 *
		 * @return bool
		 */
		public function set_booking_data( $id, $key_args, $value ) {
			return $this->set_object_data( 'booking', $id, $key_args, $value );
		}

		/**
		 * Delete booking data.
		 *
		 * @param int          $id       Object ID.
		 * @param string|array $key_args The key arguments.
		 *
		 * @return bool
		 */
		public function delete_booking_data( $id, $key_args = '' ) {
			return $this->delete_object_data( 'booking', $id, $key_args );
		}

		/**
		 * Invalidate product cache.
		 *
		 * @since 5.0.0
		 */
		public function invalidate_product_cache() {
			$this->invalidate( 'product' );
		}
	}
}

if ( ! function_exists( 'yith_wcbk_cache' ) ) {
	/**
	 * Access to the Chache class instance.
	 *
	 * @return YITH_WCBK_Cache
	 */
	function yith_wcbk_cache() {
		return YITH_WCBK_Cache::get_instance();
	}
}
