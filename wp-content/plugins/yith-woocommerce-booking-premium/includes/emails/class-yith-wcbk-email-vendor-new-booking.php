<?php
/**
 * Class YITH_WCBK_Email_Vendor_New_Booking
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Vendor_New_Booking' ) ) {
	/**
	 * Class YITH_WCBK_Email_Vendor_New_Booking
	 * An email sent to the vendor when a new booking is created
	 *
	 * @since  1.0.8
	 */
	class YITH_WCBK_Email_Vendor_New_Booking extends YITH_WCBK_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id = 'yith_wcbk_vendor_new_booking';

			$this->template_base  = YITH_WCBK_TEMPLATE_PATH;
			$this->template_html  = 'emails/vendor-new-booking.php';
			$this->template_plain = 'emails/plain/vendor-new-booking.php';

			add_action( 'yith_wcbk_new_booking_notification', array( $this, 'trigger' ) );

			$this->placeholders = array_merge(
				array(
					'{vendor_name}' => '',
				),
				$this->placeholders
			);

			parent::__construct();
		}

		/**
		 * Set default params.
		 *
		 * @since 3.5.0
		 */
		public function set_default_params() {
			parent::set_default_params();

			$this->title       = __( 'New Booking (Vendor)', 'yith-booking-for-woocommerce' );
			$this->description = __( 'Sent to the vendor when a booking is created.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'New Booking', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Booking #{booking_id} created', 'yith-booking-for-woocommerce' );

			$this->custom_message = __( "Hi {vendor_name},\n\nGreat news! There is a new booking for the item \"{product_name}\"\n\n{booking_details}", 'yith-booking-for-woocommerce' );
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
		 * Initialize placeholders before sending.
		 */
		protected function init_placeholders_before_sending() {
			parent::init_placeholders_before_sending();
			if ( $this->object && $this->object->is_valid() ) {
				$this->placeholders['{vendor_name}'] = yith_get_vendor( $this->object->get_product_id(), 'product' )->name;
			}
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

return new YITH_WCBK_Email_Vendor_New_Booking();
