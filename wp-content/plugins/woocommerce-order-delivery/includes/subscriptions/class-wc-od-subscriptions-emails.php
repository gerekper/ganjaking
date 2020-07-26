<?php
/**
 * Class to handle the subscriptions emails
 *
 * @package WC_OD/Subscriptions
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Subscriptions_Emails' ) ) {

	class WC_OD_Subscriptions_Emails {

		/**
		 * Constructor.
		 *
		 * @since 1.4.0
		 */
		public function __construct() {
			add_filter( 'woocommerce_email_actions', array( $this, 'email_actions' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );

			add_filter( 'wc_od_emails_with_delivery_details', array( $this, 'emails_with_delivery_details' ) );
			add_action( 'wc_od_email_after_delivery_details', array( $this, 'email_after_delivery_details' ) );
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
			$actions[] = 'wc_od_added_shop_subscription_note';

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
			$emails['WC_OD_Email_Subscription_Delivery_Note'] = include WC_OD_PATH . 'includes/emails/class-wc-od-email-subscription-delivery-note.php';

			return $emails;
		}

		/**
		 * Registers the emails of a subscription that will include the delivery information.
		 *
		 * @since 1.4.1
		 *
		 * @param array $email_ids The email IDs.
		 * @return array An array with the email IDs.
		 */
		public function emails_with_delivery_details( $email_ids ) {
			$email_ids = array_merge( $email_ids, array(
				'new_renewal_order',
				'customer_processing_renewal_order',
				'customer_completed_renewal_order',
			) );

			return $email_ids;
		}

		/**
		 * Additional delivery information for the subscription emails.
		 *
		 * @since 1.4.1
		 *
		 * @param array $args The arguments.
		 */
		public function email_after_delivery_details( $args ) {
			// No links for the plain text emails.
			if ( $args['plain_text'] ) {
				return;
			}

			$order = $args['order'];

			// It's a subscription.
			if ( wcs_is_subscription( $order ) ) {
				$subscription_ids = array( $order->get_id() );
			} else {
				// The order may contain more than one subscription.
				$subscription_ids = array_keys( wcs_get_subscriptions_for_order( $order, array( 'order_type' => 'any' ) ) );
			}

			$links = array();
			foreach ( $subscription_ids as $subscription_id ) {
				if ( wc_od_subscription_has_delivery_preferences( $subscription_id ) ) {
					$links[] = sprintf( '<a href="%1$s">%2$s</a>',
						esc_url( wc_od_edit_delivery_endpoint( $subscription_id ) ),
						esc_attr( "#{$subscription_id}")
					);
				}
			}

			if ( ! empty( $links ) ) {
				$text = wp_kses( _n(
					'Edit the delivery preferences for the subscription: %s',
					'Edit the delivery preferences for the subscriptions: %s',
					count( $links ),
					'woocommerce-order-delivery'
				), array( 'a' => array( 'href' => array() ) ) );

				printf( '<p>%s</p>', sprintf( $text, join( ', ', $links ) ) );
			}
		}

	}
}

return new WC_OD_Subscriptions_Emails();
