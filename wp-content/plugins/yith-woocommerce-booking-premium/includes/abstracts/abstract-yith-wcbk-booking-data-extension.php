<?php
/**
 * Class YITH_WCBK_Booking_Data_Extension
 * Allow extending booking data.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Booking_Data_Extension' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Data_Extension
	 */
	abstract class YITH_WCBK_Booking_Data_Extension extends YITH_WCBK_Data_Extension {

		/**
		 * The constructor.
		 */
		protected function __construct() {
			parent::__construct();

			// Admin product tabs and saving data.
			$internal_meta_keys = $this->get_internal_meta_keys();
			$csv_fields         = $this->get_csv_fields();

			add_action( 'yith_wcbk_process_booking_meta', array( $this, 'set_meta_before_saving' ) );

			// Data store.
			add_filter( 'yith_wcbk_booking_data_store_update_props', array( $this, 'update_props' ), 10, 3 );
			add_action( 'yith_wcbk_booking_data_store_read_data', array( $this, 'read_data' ), 10, 1 );
			add_action( 'yith_wcbk_booking_data_store_updated_props', array( $this, 'handle_updated_props' ), 10, 2 );
			add_action( 'yith_wcbk_booking_data_store_clear_caches', array( $this, 'clear_caches' ), 10, 1 );
			if ( $internal_meta_keys ) {
				add_filter( 'yith_wcbk_booking_data_store_internal_meta_keys', array( $this, 'filter_internal_meta_keys' ), 10, 1 );
			}

			// Delete.
			add_action( 'delete_post', array( $this, 'handle_delete_post' ), 10, 1 );

			if ( $csv_fields ) {
				add_filter( 'yith_wcbk_csv_fields', array( $this, 'filter_csv_fields' ), 10, 1 );
				add_filter( 'yith_wcbk_csv_field_value', array( $this, 'filter_csv_field_value' ), 10, 3 );
			}
		}

		/*
		|--------------------------------------------------------------------------
		| Methods to override.
		|--------------------------------------------------------------------------
		*/

		/**
		 * Save booking meta.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		public function set_meta_before_saving( YITH_WCBK_Booking $booking ) {
			// Do nothing! You can use it by overriding.
		}

		/**
		 * Triggered before updating props.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		protected function before_updating_props( YITH_WCBK_Booking $booking ) {
			// Do nothing! You can use it by overriding.
		}

		/**
		 * Triggered before updating props.
		 *
		 * @param mixed             $value   The value.
		 * @param string            $prop    The prop.
		 * @param YITH_WCBK_Booking $booking The booking.
		 *
		 * @return mixed The sanitized value.
		 */
		protected function sanitize_prop_value_before_saving( $value, string $prop, YITH_WCBK_Booking $booking ) {
			// Do nothing! You can use it by overriding.
			return $value;
		}

		/**
		 * Update extra data.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 * @param bool              $force   Force flag.
		 *
		 * @return array Array of updated props.
		 */
		protected function update_extra_data( YITH_WCBK_Booking $booking, bool $force ): array {
			// Do nothing! You can use it by overriding.
			return array();
		}

		/**
		 * Read extra data.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		protected function read_extra_data( YITH_WCBK_Booking $booking ) {
			// Do nothing! You can use it by overriding.
		}

		/**
		 * Read extra data.
		 *
		 * @param YITH_WCBK_Booking $booking       The booking.
		 * @param array             $updated_props The updated props.
		 */
		public function handle_updated_props( YITH_WCBK_Booking $booking, array $updated_props ) {
			// Do nothing! You can use it by overriding.
		}

		/**
		 * Clear caches booking data.
		 *
		 * @param YITH_WCBK_Booking $booking The booking product.
		 */
		public function clear_caches( YITH_WCBK_Booking $booking ) {
			// Do nothing! You can use it by overriding.
		}

		/**
		 * Filter CSV field value.
		 *
		 * @param string            $value   The value to filter.
		 * @param string            $field   The field.
		 * @param YITH_WCBK_Booking $booking The booking product.
		 *
		 * @return string
		 */
		public function filter_csv_field_value( string $value, string $field, YITH_WCBK_Booking $booking ): string {
			// Do nothing! You can use it by overriding.

			return $value;
		}

		/**
		 * Handle booking delete.
		 *
		 * @param int $booking_id The booking ID.
		 */
		protected function handle_booking_delete( int $booking_id ) {
			// Do nothing! You can use it by overriding.
		}

		/*
		|--------------------------------------------------------------------------
		| Settings
		|--------------------------------------------------------------------------
		*/

		/**
		 * Get csv_fields.
		 *
		 * @return array
		 */
		final protected function get_csv_fields(): array {
			return $this->get_single_settings( 'csv_fields', array() );
		}

		/*
		|--------------------------------------------------------------------------
		| Hooks handlers
		|--------------------------------------------------------------------------
		*/

		/**
		 * Save booking product meta for resources.
		 *
		 * @param array             $updated_props Updated props.
		 * @param YITH_WCBK_Booking $booking       The booking .
		 * @param bool              $force         Force flag.
		 */
		public function update_props( $updated_props, $booking, $force ) {
			$this->before_updating_props( $booking );

			$meta_keys_to_props   = $this->get_meta_keys_to_props();
			$props_to_update      = $force ? $meta_keys_to_props : $this->get_props_to_update( $booking, $meta_keys_to_props );
			$custom_updated_props = array();

			foreach ( $props_to_update as $meta_key => $prop ) {
				if ( is_callable( array( $booking, "get_$prop" ) ) ) {
					$value = $booking->{"get_$prop"}( 'edit' );
					$value = $this->sanitize_prop_value_before_saving( $value, $prop, $booking );

					$updated = update_post_meta( $booking->get_id(), $meta_key, $value );

					if ( $updated ) {
						$custom_updated_props[] = $prop;
					}
				}
			}

			$extra_updated_props = $this->update_extra_data( $booking, $force );

			if ( $extra_updated_props ) {
				$custom_updated_props = array_merge( $custom_updated_props, $extra_updated_props );
			}

			if ( $custom_updated_props ) {
				$updated_props = array_merge( $updated_props, $custom_updated_props );
			}

			return $updated_props;
		}

		/**
		 * Gets a list of props and meta keys that need updated based on change state
		 * or if they are present in the database or not.
		 *
		 * @param YITH_WCBK_Booking $booking           The booking.
		 * @param array             $meta_key_to_props A mapping of meta keys => prop names.
		 * @param string            $meta_type         The internal WP meta type (post, user, etc).
		 *
		 * @return array                        A mapping of meta keys => prop names, filtered by ones that should be updated.
		 */
		protected function get_props_to_update( YITH_WCBK_Booking $booking, array $meta_key_to_props, string $meta_type = 'post' ): array {
			$props_to_update = array();
			$changed_props   = $booking->get_changes();

			// Props should be updated if they are a part of the $changed array or don't exist yet.
			foreach ( $meta_key_to_props as $meta_key => $prop ) {
				if ( array_key_exists( $prop, $changed_props ) || ! metadata_exists( $meta_type, $booking->get_id(), $meta_key ) ) {
					$props_to_update[ $meta_key ] = $prop;
				}
			}

			return $props_to_update;
		}

		/**
		 * Read product data.
		 *
		 * @param YITH_WCBK_Booking $booking The booking.
		 */
		public function read_data( YITH_WCBK_Booking $booking ) {
			$meta_keys_to_props = $this->get_meta_keys_to_props();
			$post_meta_values   = get_post_meta( $booking->get_id() );
			$props_to_set       = array();

			foreach ( $meta_keys_to_props as $meta_key => $prop ) {
				$meta_value            = $post_meta_values[ $meta_key ][0] ?? null;
				$props_to_set[ $prop ] = maybe_unserialize( $meta_value );
			}

			$booking->set_props( $props_to_set );

			$this->read_extra_data( $booking );
		}

		/**
		 * Add meta keys to internal ones for bookable products.
		 *
		 * @param array $internal_meta_keys The internal meta keys.
		 *
		 * @return array
		 */
		public function filter_internal_meta_keys( array $internal_meta_keys ): array {
			$to_add = $this->get_internal_meta_keys();

			return array_merge( $internal_meta_keys, $to_add );
		}

		/**
		 * Add csv fields for bookable products.
		 *
		 * @param array $fields The fields.
		 *
		 * @return array
		 */
		public function filter_csv_fields( array $fields ): array {
			$to_add = $this->get_csv_fields();

			return array_merge( $fields, $to_add );
		}

		/**
		 * Handle post delete
		 *
		 * @param int $id ID of post being deleted.
		 *
		 * @since 4.0.0
		 */
		public function handle_delete_post( $id ) {
			if ( ! $id ) {
				return;
			}

			if ( YITH_WCBK_Post_Types::BOOKING === get_post_type( $id ) ) {
				$this->handle_booking_delete( $id );
			}
		}
	}
}
