<?php
/**
 * Class to handle a delivery event
 *
 * @class   WC_OD_Delivery_Event
 * @extends WC_OD_Event_Delivery
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Delivery_Event' ) ) {

	/**
	 * Class WC_OD_Delivery_Event
	 *
	 * @deprecated 1.2.0 WC_OD_Event_Delivery class is used instead.
	 */
	class WC_OD_Delivery_Event extends WC_OD_Event_Delivery {

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args     The event data.
		 * @param mixed $timezone The event timezone.
		 */
		public function __construct( $args, $timezone = null ) {
			wc_deprecated_function( 'WC_OD_Delivery_Event::__construct', '1.2.0', 'WC_OD_Event_Delivery' );

			parent::__construct( $args, $timezone );
		}
	}
}
