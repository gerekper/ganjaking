<?php
/**
 * Class to handle the plugin emails
 *
 * @package WC_OD
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Emails' ) ) {
	/**
	 * Class WC_OD_Emails
	 */
	class WC_OD_Emails {

		/**
		 * Enable/disable the 'order_delivery_note' email.
		 *
		 * @var bool
		 */
		protected $order_delivery_note_email;

		/**
		 * Constructor.
		 *
		 * @since 1.4.0
		 */
		public function __construct() {
			add_filter( 'woocommerce_email_actions', array( $this, 'email_actions' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );

			/**
			 * Filter if the order_delivery_note email should be enabled or not.
			 *
			 * At this moment, the plugin does not use this email. But you can enable it to send notifications
			 * to the merchant related with the delivery of an order.
			 *
			 * This hook will be deprecated when the plugin needs to use this email by default.
			 *
			 * @since 1.4.0
			 *
			 * @param bool $enable Enable/disable the 'order_delivery_note' email.
			 */
			$this->order_delivery_note_email = apply_filters( 'wc_od_enable_order_delivery_note_email', false );

			// WooCommerce mailing hooks. (Subscription info is added with priority 15).
			add_action( 'woocommerce_email_after_order_table', array( $this, 'delivery_details' ), 10, 4 );
		}

		/**
		 * Register custom emails actions.
		 *
		 * @since 1.4.0
		 *
		 * @param array $actions The email actions.
		 * @return array
		 */
		public function email_actions( $actions ) {
			if ( $this->order_delivery_note_email ) {
				$actions[] = 'wc_od_added_shop_order_note';
			}

			return $actions;
		}

		/**
		 * Register custom emails classes.
		 *
		 * @since 1.4.0
		 *
		 * @param array $emails The email classes.
		 * @return array
		 */
		public function email_classes( $emails ) {
			include 'emails/class-wc-od-email-order-note.php';

			if ( $this->order_delivery_note_email ) {
				$emails['WC_OD_Email_Order_Delivery_Note'] = include 'emails/class-wc-od-email-order-delivery-note.php';
			}

			return $emails;
		}

		/**
		 * Displays the delivery information in the emails.
		 *
		 * @since 1.4.1
		 *
		 * @param WC_Order $order         Order instance.
		 * @param bool     $sent_to_admin If should sent to admin.
		 * @param bool     $plain_text    If is plain text email.
		 * @param WC_Email $email         Optional. The email instance.
		 */
		public function delivery_details( $order, $sent_to_admin = false, $plain_text = false, $email = null ) {
			$delivery_date = $order->get_meta( '_delivery_date' );

			if ( ! $delivery_date ) {
				return;
			}

			$has_delivery = false;

			if ( $email instanceof WC_Email ) {
				/**
				 * Filters the emails that will have the delivery information.
				 *
				 * @since 1.1.0
				 *
				 * @param array $email_ids An array with the email ids.
				 */
				$email_ids = apply_filters(
					'wc_od_emails_with_delivery_details',
					array(
						'new_order',
						'customer_note',
						'customer_on_hold_order',
						'customer_processing_order',
						'customer_completed_order',
						'customer_invoice',
					)
				);

				if ( in_array( $email->id, $email_ids, true ) ) {
					$has_delivery = true;
				}
			}

			/**
			 * Filters if the email should include the delivery information.
			 *
			 * @since 1.7.0
			 *
			 * @param bool     $has_delivery  Whether to include the delivery details.
			 * @param WC_Email $email         The email instance.
			 * @param WC_Order $order         Order instance.
			 */
			$has_delivery = apply_filters( 'wc_od_email_has_delivery_details', $has_delivery, $email, $order );

			if ( ! $has_delivery ) {
				return;
			}

			$delivery_date_i18n = wc_od_localize_date( $delivery_date );

			if ( $delivery_date_i18n ) {
				/**
				 * Filter the arguments used by the emails/email-delivery-date.php template.
				 *
				 * @since 1.1.0
				 * @since 1.5.0 Added `delivery_time_frame` parameter.
				 *
				 * @param array $args The arguments.
				 */
				$args = apply_filters(
					'wc_od_email_delivery_details_args',
					array(
						'title'               => __( 'Delivery details', 'woocommerce-order-delivery' ),
						'delivery_date'       => $delivery_date_i18n,
						'delivery_time_frame' => $order->get_meta( '_delivery_time_frame' ),
						'order'               => $order,
						'sent_to_admin'       => $sent_to_admin,
						'plain_text'          => $plain_text,
						'email'               => $email,
					)
				);

				$template_name = 'emails/' . ( $plain_text ? 'plain/' : '' ) . 'email-delivery-date.php';

				wc_od_get_template( $template_name, $args );
			}
		}

	}

}

return new WC_OD_Emails();
