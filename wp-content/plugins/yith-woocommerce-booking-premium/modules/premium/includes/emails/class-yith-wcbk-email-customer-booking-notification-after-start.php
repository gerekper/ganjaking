<?php
/**
 * Class YITH_WCBK_Email_Customer_Booking_Notification_After_Start
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Email_Customer_Booking_Notification_After_Start' ) ) {
	/**
	 * Class YITH_WCBK_Email_Customer_Booking_Notification_After_Start
	 * An email sent to the customer X days after starting.
	 */
	class YITH_WCBK_Email_Customer_Booking_Notification_After_Start extends YITH_WCBK_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'yith_wcbk_customer_booking_notification_after_start';
			$this->customer_email = true;

			$this->template_base  = yith_wcbk_get_module_path( 'premium', 'templates/' );
			$this->template_html  = 'emails/customer-booking-notification-after-start.php';
			$this->template_plain = 'emails/plain/customer-booking-notification-after-start.php';

			parent::__construct();
		}

		/**
		 * Set default params.
		 *
		 * @since 3.5.0
		 */
		public function set_default_params() {
			parent::set_default_params();

			$this->title       = __( 'Booking notification after start date', 'yith-booking-for-woocommerce' );
			$this->description = __( 'Sent to the customer XX days after the start date.', 'yith-booking-for-woocommerce' );
			$this->heading     = __( 'Your booking has started', 'yith-booking-for-woocommerce' );
			$this->subject     = __( 'Booking #{booking_id} has started', 'yith-booking-for-woocommerce' );

			$this->custom_message = sprintf(
			// translators: %s is the site name.
				__( "Hi {customer_name},\n\nYour booking {booking_id_link} has started!\n\n{booking_details}\n\nRegards,\n%s Staff", 'yith-booking-for-woocommerce' ),
				get_bloginfo( 'name' )
			);
		}

		/**
		 * Initialize Form fields.
		 */
		public function init_form_fields() {
			parent::init_form_fields();

			$this->form_fields['enabled']['default'] = 'no';

			yith_wcbk_array_add_after(
				$this->form_fields,
				'heading',
				'days',
				array(
					'title'                => __( 'Send notification', 'yith-booking-for-woocommerce' ),
					'type'                 => 'yith_wcbk_field',
					'yith_wcbk_field_type' => 'number',
					'class'                => 'yith-wcbk-number-field-mini',
					'yith-wcbk-after-html' => __( 'day(s) after the booking start date', 'yith-booking-for-woocommerce' ),
					'default'              => 1,
					'min'                  => 0,
				)
			);

			yith_wcbk_array_add_before(
				$this->form_fields,
				'email_type',
				'booking_status',
				array(
					'title'   => __( 'Send email for these statuses', 'yith-booking-for-woocommerce' ),
					'type'    => 'multiselect',
					'default' => array( 'paid', 'completed' ),
					'class'   => 'email_type wc-enhanced-select',
					'options' => yith_wcbk_get_booking_statuses(),
				)
			);
		}
	}
}

return new YITH_WCBK_Email_Customer_Booking_Notification_After_Start();
