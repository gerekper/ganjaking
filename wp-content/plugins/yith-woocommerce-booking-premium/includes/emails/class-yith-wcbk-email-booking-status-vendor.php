<?php
/**
 * Class YITH_WCBK_Email_Booking_Status_Vendor
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Booking_Status_Vendor' ) ) {
	/**
	 * Class YITH_WCBK_Email_Booking_Status_Vendor
	 * An email sent to the vendor when a new booking changes status
	 */
	class YITH_WCBK_Email_Booking_Status_Vendor extends YITH_WCBK_Email {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id = 'yith_wcbk_booking_status_vendor';

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/admin-booking-status-vendor.php';
			$this->template_plain = 'emails/plain/admin-booking-status-vendor.php';

			$statuses = $this->get_option( 'status' );
			$statuses = is_array( $statuses ) ? $statuses : array();
			foreach ( $statuses as $status ) {
				add_action( 'yith_wcbk_booking_status_' . $status . '_notification', array( $this, 'trigger' ) );
			}

			parent::__construct();

			$this->recipient = YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' );
		}

		/**
		 * Set default params.
		 *
		 * @since 3.5.0
		 */
		public function set_default_params() {
			parent::set_default_params();

			$this->title       = __( 'Booking status (Vendor)', 'yith-booking-for-woocommerce' );
			$this->description = __( 'Sent to the vendor when a booking status changes.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'Booking status changed', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Booking #{booking_id} is now {status}', 'yith-booking-for-woocommerce' );

			$this->custom_message = __( "Hi {customer_name},\n\nthe booking {booking_id_link} is now <strong>{status}</strong>!\n\n{booking_details}", 'yith-booking-for-woocommerce' );
		}

		/**
		 * Maybe set booking recipient email.
		 */
		protected function maybe_set_booking_recipient() {
			$this->recipient = false;
			if ( $this->object ) {
				$vendor = yith_get_vendor( $this->object->get_id(), 'product' );
				if ( $vendor->is_valid() ) {
					$vendor_email = $vendor->store_email;

					if ( empty( $vendor_email ) ) {
						$vendor_owner = get_user_by( 'id', absint( $vendor->get_owner() ) );
						$vendor_email = $vendor_owner instanceof WP_User ? $vendor_owner->user_email : false;
					}

					$this->recipient = $vendor_email;
				}
			}
		}

		/**
		 * Initialize Form fields.
		 */
		public function init_form_fields() {
			parent::init_form_fields();
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

		/**
		 * Return the recipient to be shown in settings list.
		 *
		 * @return string
		 */
		public function get_recipient_to_show_in_settings_list() {
			return YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' );
		}
	}
}

return new YITH_WCBK_Email_Booking_Status_Vendor();
