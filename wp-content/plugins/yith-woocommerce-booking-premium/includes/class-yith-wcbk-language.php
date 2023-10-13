<?php
/**
 * Class YITH_WCBK_Language
 * handle booking labels
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Language' ) ) {
	/**
	 * Class YITH_WCBK_Language
	 * handle booking labels
	 */
	class YITH_WCBK_Language {

		/**
		 * Single instance of the class.
		 *
		 * @var YITH_WCBK_Language
		 */
		private static $instance;

		/**
		 * The labels.
		 *
		 * @var array
		 */
		private $labels = array();

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Language
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBK_Language constructor.
		 */
		private function __construct() {

		}

		/**
		 * Get a label
		 *
		 * @param string $key The label key.
		 *
		 * @return string
		 */
		public function get_label( $key ) {
			if ( isset( $this->labels[ $key ] ) ) {
				return $this->labels[ $key ];
			}

			$label = get_option( 'yith-wcbk-label-' . sanitize_text_field( $key ), '' );
			$label = ! $label ? $this->get_default_label( $key ) : call_user_func( '__', $label, 'yith-booking-for-woocommerce' );

			$this->labels[ $key ] = apply_filters( 'yith_wcbk_language_get_label', $label, $key );

			return $this->labels[ $key ];
		}

		/**
		 * Get the default label
		 *
		 * @param string $key The label key.
		 *
		 * @return string
		 */
		public function get_default_label( $key ) {
			$labels = $this->get_default_labels();

			return isset( $labels[ $key ] ) ? $labels[ $key ] : '';
		}

		/**
		 * Get the default label array
		 *
		 * @return array
		 */
		public function get_default_labels() {
			static $labels = null;

			if ( is_null( $labels ) ) {
				$labels = apply_filters(
					'yith_wcbk_language_default_labels',
					array(
						'add-to-cart'          => _x( 'Add to cart', 'Text of add-to-cart-button for bookable products', 'yith-booking-for-woocommerce' ),
						'bookable'             => __( 'Bookable', 'yith-booking-for-woocommerce' ),
						'booking-of'           => __( 'Booking of:', 'yith-booking-for-woocommerce' ),
						'check-in'             => __( 'Check-in', 'yith-booking-for-woocommerce' ),
						'check-out'            => __( 'Check-out', 'yith-booking-for-woocommerce' ),
						'dates'                => __( 'Dates', 'yith-booking-for-woocommerce' ),
						'duration'             => __( 'Duration', 'yith-booking-for-woocommerce' ),
						'end-date'             => __( 'End date', 'yith-booking-for-woocommerce' ),
						'from'                 => __( 'From', 'yith-booking-for-woocommerce' ),
						'not-bookable'         => __( 'Not-bookable', 'yith-booking-for-woocommerce' ),
						'read-more'            => _x( 'Read more', 'Add-to-cart button text for bookable products', 'yith-booking-for-woocommerce' ),
						'request-confirmation' => _x( 'Request Confirmation', 'Add-to-cart button text for bookable products', 'yith-booking-for-woocommerce' ),
						'start-date'           => __( 'Start date', 'yith-booking-for-woocommerce' ),
						'time'                 => __( 'Time', 'yith-booking-for-woocommerce' ),
						'to'                   => __( 'To', 'yith-booking-for-woocommerce' ),
					)
				);

				asort( $labels );
			}

			return $labels;
		}
	}
}
