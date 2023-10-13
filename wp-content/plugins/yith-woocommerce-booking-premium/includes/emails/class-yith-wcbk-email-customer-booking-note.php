<?php
/**
 * Class YITH_WCBK_Email_Customer_Booking_Note
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Customer_Booking_Note' ) ) {

	/**
	 * Class YITH_WCBK_Email_Customer_Booking_Note
	 * An email sent to the customer when a new customer note is created
	 *
	 * @since  2.0.0
	 */
	class YITH_WCBK_Email_Customer_Booking_Note extends YITH_WCBK_Email {
		/**
		 * The booking note
		 *
		 * @var string
		 */
		public $note = '';

		/**
		 * Contructor.
		 */
		public function __construct() {
			$this->id             = 'yith_wcbk_customer_booking_note';
			$this->customer_email = true;
			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/customer-booking-note.php';
			$this->template_plain = 'emails/plain/customer-booking-note.php';

			$this->placeholders = array_merge(
				array(
					'{note}' => '',
				),
				$this->placeholders
			);

			add_action( 'yith_wcbk_new_customer_note', array( $this, 'custom_trigger' ) );

			parent::__construct();
		}

		/**
		 * Set default params.
		 *
		 * @since 3.5.0
		 */
		public function set_default_params() {
			parent::set_default_params();

			$this->title       = __( 'Customer Booking Note', 'yith-booking-for-woocommerce' );
			$this->description = __( 'Sent to the customer when you add a note to a booking.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'A note has been added to your Booking #{booking_id}', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Note added to your Booking #{booking_id}', 'yith-booking-for-woocommerce' );

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi {customer_name},\n\na note has just been added to your booking {booking_id_link}:\n\n{note}\n\nFor your reference, your booking details are shown below.\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
				get_bloginfo( 'name' )
			);
		}

		/**
		 * Trigger.
		 *
		 * @param array $params Parameters.
		 */
		public function custom_trigger( $params ) {
			$defaults = array(
				'booking_id' => '',
				'note'       => '',
			);
			$params   = wp_parse_args( $params, $defaults );

			if ( ! ! $params['booking_id'] && ! ! $params['note'] ) {
				$this->object                 = yith_get_booking( $params['booking_id'] );
				$this->placeholders['{note}'] = '<blockquote class="booking-note">' . $params['note'] . '</blockquote>';

				$this->prepare_and_send();
			}
		}
	}
}

return new YITH_WCBK_Email_Customer_Booking_Note();
