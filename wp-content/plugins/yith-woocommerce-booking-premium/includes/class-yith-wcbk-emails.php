<?php
/**
 * Class YITH_WCBK_Emails
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Emails' ) ) {
	/**
	 * Class YITH_WCBK_Emails
	 * handle notifications behavior
	 *
	 * @since  5.0.0
	 */
	class YITH_WCBK_Emails {
		use YITH_WCBK_Extensible_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ) );
			add_filter( 'woocommerce_email_actions', array( $this, 'add_email_actions' ) );
			add_filter( 'woocommerce_email_styles', array( $this, 'email_style' ), 1000 ); // use 1000 as priority to allow support for YITH  Email Templates.

			add_action( 'yith_wcbk_print_emails_tab', array( $this, 'print_emails_tab' ) );
			add_action( 'yith_wcbk_admin_ajax_switch_email_activation', array( $this, 'ajax_switch_email_activation' ) );
			add_action( 'yith_wcbk_admin_ajax_update_email_options', array( $this, 'ajax_update_email_options' ) );
		}

		/**
		 * Add email actions to WooCommerce email actions
		 *
		 * @param array $actions Actions.
		 *
		 * @return array
		 */
		public function add_email_actions( $actions ) {
			foreach ( array_keys( yith_wcbk_get_booking_statuses( true ) ) as $status ) {
				$actions[] = 'yith_wcbk_booking_status_' . $status;
			}

			$actions[] = 'yith_wcbk_new_booking';
			$actions[] = 'yith_wcbk_new_customer_note';

			return $actions;
		}

		/**
		 * Add email classes to WooCommerce
		 *
		 * @param array $emails Emails.
		 *
		 * @return array
		 */
		public function add_email_classes( $emails ) {
			require_once YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email.php';
			$emails['YITH_WCBK_Email_Booking_Status']               = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-booking-status.php';
			$emails['YITH_WCBK_Email_Admin_New_Booking']            = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-admin-new-booking.php';
			$emails['YITH_WCBK_Email_Customer_New_Booking']         = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-new-booking.php';
			$emails['YITH_WCBK_Email_Customer_Confirmed_Booking']   = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-confirmed-booking.php';
			$emails['YITH_WCBK_Email_Customer_Unconfirmed_Booking'] = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-unconfirmed-booking.php';
			$emails['YITH_WCBK_Email_Customer_Cancelled_Booking']   = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-cancelled-booking.php';
			$emails['YITH_WCBK_Email_Customer_Paid_Booking']        = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-paid-booking.php';
			$emails['YITH_WCBK_Email_Customer_Completed_Booking']   = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-completed-booking.php';
			$emails['YITH_WCBK_Email_Customer_Booking_Note']        = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-customer-booking-note.php';

			return $emails;
		}

		/**
		 * Custom email styles.
		 *
		 * @param string $style WooCommerce style.
		 *
		 * @return string
		 */
		public function email_style( $style ) {
			$style .= $this->get_email_style();

			return $style;
		}

		/**
		 * Retrieve the email style for Booking emails.
		 *
		 * @return string
		 */
		private function get_email_style() {
			ob_start();
			include YITH_WCBK_ASSETS_PATH . '/css/emails.css';

			return ob_get_clean();
		}

		/**
		 * Return true if is a Booking email.
		 *
		 * @param string $email_class The email class name.
		 *
		 * @return bool
		 * @since 5.0.0
		 */
		private function is_booking_email( $email_class ) {
			return 0 === strpos( $email_class, 'YITH_WCBK_Email_' );
		}

		/**
		 * Print the Emails tab
		 *
		 * @since 5.0.0
		 */
		public function print_emails_tab() {
			$mailer             = WC()->mailer();
			$woocommerce_emails = $mailer->get_emails();
			$emails             = array_filter(
				$woocommerce_emails,
				function ( $value, $key ) {
					return $this->is_booking_email( $key );
				},
				ARRAY_FILTER_USE_BOTH
			);

			yith_wcbk_get_view( 'settings-tabs/html-emails.php', compact( 'emails' ) );
		}

		/**
		 * Switch email activation.
		 *
		 * @since 5.0.0
		 */
		public function ajax_switch_email_activation() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$email_class_name = sanitize_text_field( wp_unslash( $_REQUEST['email'] ?? '' ) );
			$enabled          = sanitize_title( wp_unslash( $_REQUEST['enabled'] ?? '' ) );

			$mailer             = WC()->mailer();
			$woocommerce_emails = $mailer->get_emails();

			if ( ! current_user_can( 'manage_options' ) || ! $this->is_booking_email( $email_class_name ) || ! in_array( $email_class_name, array_keys( $woocommerce_emails ), true ) || ! in_array( $enabled, array( 'yes', 'no' ), true ) ) {
				wp_send_json_error();
			}

			$email = $woocommerce_emails[ $email_class_name ];
			$data  = array();

			foreach ( $email->get_form_fields() as $key => $field ) {
				if ( 'title' !== $email->get_field_type( $field ) ) {
					$field_key  = $email->get_field_key( $key );
					$field_type = $email->get_field_type( $field );
					$value      = $email->settings[ $key ] ?? $field['default'] ?? '';

					if ( 'checkbox' === $field_type ) {
						if ( 'yes' === $value ) {
							$data[ $field_key ] = 1;
						}
					} else {
						$data[ $field_key ] = $value;
					}
				}
			}

			$field_key = $email->get_field_key( 'enabled' );
			if ( 'yes' === $enabled ) {
				$data[ $field_key ] = 1;
			} else {
				unset( $data[ $field_key ] );
			}

			$email->set_post_data( $data );
			$email->process_admin_options();

			wp_send_json_success();

			// phpcs:enable
		}

		/**
		 * Switch email activation.
		 *
		 * @since 5.0.0
		 */
		public function ajax_update_email_options() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			$email_class_name   = sanitize_text_field( wp_unslash( $_REQUEST['email'] ?? '' ) );
			$data               = wp_unslash( $_REQUEST['data'] ?? array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$mailer             = WC()->mailer();
			$woocommerce_emails = $mailer->get_emails();

			if ( ! current_user_can( 'manage_options' ) || ! $this->is_booking_email( $email_class_name ) || ! in_array( $email_class_name, array_keys( $woocommerce_emails ), true ) ) {
				wp_send_json_error();
			}

			$email       = $woocommerce_emails[ $email_class_name ];
			$enabled_key = $email->get_field_key( 'enabled' );
			if ( $email->is_enabled() ) {
				$data[ $enabled_key ] = 1;
			} else {
				unset( $data[ $enabled_key ] );
			}

			$email->set_post_data( $data );
			$email->process_admin_options();

			wp_send_json_success();

			// phpcs:enable
		}
	}
}
