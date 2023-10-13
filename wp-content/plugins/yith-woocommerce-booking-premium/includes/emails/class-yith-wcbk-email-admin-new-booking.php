<?php
/**
 * Class YITH_WCBK_Email_Admin_New_Booking
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Admin_New_Booking' ) ) {
	/**
	 * Class YITH_WCBK_Email_Admin_New_Booking
	 * An email sent to the admin when a booking is created
	 *
	 * @since  1.0.8
	 */
	class YITH_WCBK_Email_Admin_New_Booking extends YITH_WCBK_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id = 'yith_wcbk_admin_new_booking';

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/admin-new-booking.php';
			$this->template_plain = 'emails/plain/admin-new-booking.php';

			add_action( 'yith_wcbk_new_booking_notification', array( $this, 'trigger' ) );

			parent::__construct();

			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
		}

		/**
		 * Set default params.
		 *
		 * @since 3.5.0
		 */
		public function set_default_params() {
			parent::set_default_params();

			$this->title       = __( 'New Booking (Admin)', 'yith-booking-for-woocommerce' );
			$this->description = __( 'Sent to the admin when a booking is created.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'New Booking', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Booking #{booking_id} created', 'yith-booking-for-woocommerce' );

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi Admin,\n\nGreat news! There is a new booking for the item \"{product_name}\"\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
				get_bloginfo( 'name' )
			);
		}

		/**
		 * Initialize Form fields.
		 */
		public function init_form_fields() {
			parent::init_form_fields();
			yith_wcbk_array_add_after(
				$this->form_fields,
				'enabled',
				'recipient',
				array(
					'title'       => __( 'Recipient(s)', 'yith-booking-for-woocommerce' ),
					'type'        => 'text',
					'description' => esc_html__( 'Enter recipients (comma separated) for this email.', 'yith-booking-for-woocommerce' ),
					'placeholder' => esc_html( get_option( 'admin_email' ) ),
					'default'     => get_option( 'admin_email' ),
				)
			);
		}
	}
}

return new YITH_WCBK_Email_Admin_New_Booking();
