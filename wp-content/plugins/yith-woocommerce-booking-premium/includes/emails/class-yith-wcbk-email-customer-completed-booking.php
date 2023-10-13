<?php
/**
 * Class YITH_WCBK_Email_Customer_Completed_Booking
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Customer_Completed_Booking' ) ) {
	/**
	 * Class YITH_WCBK_Email_Customer_Completed_Booking
	 * An email sent to the customer when a new booking is completed
	 *
	 * @since  2.0.6
	 */
	class YITH_WCBK_Email_Customer_Completed_Booking extends YITH_WCBK_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'yith_wcbk_customer_completed_booking';
			$this->customer_email = true;

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/customer-completed-booking.php';
			$this->template_plain = 'emails/plain/customer-completed-booking.php';

			add_action( 'yith_wcbk_booking_status_completed_notification', array( $this, 'trigger' ) );

			parent::__construct();
		}

		/**
		 * Set default params.
		 *
		 * @since 3.5.0
		 */
		public function set_default_params() {
			parent::set_default_params();

			$this->title       = __( 'Completed Booking', 'yith-booking-for-woocommerce' );
			$this->description = __( 'Sent to the customer when a booking is completed.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'Thanks for your Booking', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Booking #{booking_id} was completed', 'yith-booking-for-woocommerce' );

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi {customer_name}, \n\nYour booking {booking_id_link} was completed!\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
				get_bloginfo( 'name' )
			);
		}
	}
}

return new YITH_WCBK_Email_Customer_Completed_Booking();
