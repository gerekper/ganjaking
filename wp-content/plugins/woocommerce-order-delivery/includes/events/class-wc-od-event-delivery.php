<?php
/**
 * Class to handle a delivery event
 *
 * @class   WC_OD_Event_Delivery
 * @extends WC_OD_Event
 * @package WC_OD
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Event_Delivery' ) ) {

	class WC_OD_Event_Delivery extends WC_OD_Event {

		/**
		 * Constructor.
		 *
		 * @since 1.2.0
		 *
		 * @param array $args     The event data.
		 * @param mixed $timezone The event timezone.
		 */
		public function __construct( $args, $timezone = null ) {
			if ( isset( $args['states'] ) && ! is_array( $args['states'] ) ) {
				$args['states'] = preg_split( '/\,/', $args['states'] );
			}

			parent::__construct( $args, $timezone );
		}

		/**
		 * Gets if the event pass all the filters.
		 *
		 *     filters = array(
		 *         'start'   => UTC date with 00:00:00 time
		 *         'end'     => UTC date with 00:00:00 time
		 *         'country' => (Optional) WooCommerce country code
		 *         'state'   => (Optional) WooCommerce state code
		 *     )
		 *
		 * @since 1.2.0
		 *
		 * @param array $filters The filters for validate the event.
		 * @return boolean Gets if the event is valid or not.
		 */
		public function is_valid( $filters = array() ) {
			$is_valid = parent::is_valid( $filters );

			// Check country and state parameters if exists.
			if ( $is_valid && isset( $this->properties['country'] ) && isset( $filters['country'] ) ) {
				$is_valid = ( $filters['country'] === $this->properties['country'] );

				if ( $is_valid && isset( $this->properties['states'] ) && isset( $filters['state'] ) ) {
					$is_valid = in_array( $filters['state'], $this->properties['states'] );
				}
			}

			return $is_valid;
		}
	}
}