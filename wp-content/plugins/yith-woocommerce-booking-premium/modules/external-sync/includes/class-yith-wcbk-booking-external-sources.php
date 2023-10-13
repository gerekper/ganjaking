<?php
/**
 * Class YITH_WCBK_Booking_External_Sources
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Booking_External_Sources' ) ) {
	/**
	 * Class YITH_WCBK_Booking_External_Sources
	 *
	 * @since  2.0.0
	 */
	class YITH_WCBK_Booking_External_Sources {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Sources
		 *
		 * @var array
		 */
		private $sources = array();

		/**
		 * YITH_WCBK_Booking_External_Sources constructor.
		 */
		protected function __construct() {
			$sources = array(
				'yith-booking' => array(
					'search' => 'YITH Booking',
					'name'   => 'YITH Booking',
				),
				'booking-com'  => array(
					'search' => 'Booking.com',
					'name'   => 'Booking.com',
				),
				'airbnb'       => array(
					'search' => 'Airbnb',
					'name'   => 'Airbnb',
				),
			);

			$this->sources = apply_filters( 'yith_wcbk_booking_external_sources', $sources );
		}

		/**
		 * Get the source name
		 *
		 * @param string $source_slug Slug.
		 *
		 * @return string string
		 */
		public function get_name( $source_slug ) {
			return array_key_exists( $source_slug, $this->sources ) ? $this->sources[ $source_slug ]['name'] : $source_slug;
		}

		/**
		 * Get the source slug from the search string
		 *
		 * @param string $string The string to search for.
		 *
		 * @return string
		 */
		public function get_slug_from_string( $string ) {
			$slug = $string;
			foreach ( $this->sources as $source_slug => $source ) {
				if ( strpos( $string, $source['search'] ) !== false ) {
					$slug = $source_slug;
					break;
				}
			}

			return sanitize_key( $slug );
		}

		/**
		 * Get the source name from the search string
		 *
		 * @param string $string The string to search for.
		 *
		 * @return string
		 */
		public function get_name_from_string( $string ) {
			$slug = $this->get_slug_from_string( $string );

			return $this->is_valid_source( $slug ) ? $this->get_name( $slug ) : $string;
		}

		/**
		 * Is a valid source?
		 *
		 * @param string $source_slug Source slug.
		 *
		 * @return bool
		 */
		public function is_valid_source( $source_slug ) {
			return array_key_exists( $source_slug, $this->sources );
		}
	}
}

/**
 * Unique access to instance of YITH_WCBK_Booking_External_Sources class
 *
 * @return YITH_WCBK_Booking_External_Sources
 * @since 2.0.0
 */
function yith_wcbk_booking_external_sources() {
	return YITH_WCBK_Booking_External_Sources::get_instance();
}
