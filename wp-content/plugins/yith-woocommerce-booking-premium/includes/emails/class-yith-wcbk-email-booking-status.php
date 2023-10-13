<?php
/**
 * Class YITH_WCBK_Email_Booking_Status
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Booking_Status' ) ) {
	/**
	 * Class YITH_WCBK_Email_Booking_Status
	 * An email sent to the admin when a new booking changes status
	 */
	class YITH_WCBK_Email_Booking_Status extends YITH_WCBK_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id = 'yith_wcbk_booking_status';

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/admin-booking-status.php';
			$this->template_plain = 'emails/plain/admin-booking-status.php';

			$statuses = $this->get_option( 'status' );
			$statuses = is_array( $statuses ) ? $statuses : array();
			foreach ( $statuses as $status ) {
				add_action( 'yith_wcbk_booking_status_' . $status . '_notification', array( $this, 'trigger' ) );
			}

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

			$this->title       = __( 'Booking Status', 'yith-booking-for-woocommerce' );
			$this->description = __( 'Sent to the administrator when a booking status changes.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'Booking status changed', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Booking #{booking_id} is now {status}', 'yith-booking-for-woocommerce' );

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi Admin,\n\nThe booking {booking_id_link} is now <strong>{status}</strong>!\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
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

			yith_wcbk_array_add_before(
				$this->form_fields,
				'email_type',
				'status',
				array(
					'title'   => __( 'Send email for these statuses', 'yith-booking-for-woocommerce' ),
					'type'    => 'multiselect',
					'default' => array( 'unpaid', 'cancelled_by_user', 'pending-confirm' ),
					'class'   => 'email_type wc-enhanced-select',
					'options' => yith_wcbk_get_booking_statuses( true ),
				)
			);
		}
	}
}

return new YITH_WCBK_Email_Booking_Status();
