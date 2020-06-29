<?php
/**
 * Privacy class; added to let customer export personal data
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Stripe
 * @version 1.5.1
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Privacy' ) ) {
	/**
	 * YITH WCStripe Privacy class
	 *
	 * @since 1.5.1
	 */
	class YITH_WCStripe_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCStripe_Privacy
		 * @since 1.5.1
		 */
		public function __construct() {
			parent::__construct( 'YITH WooCommerce Stripe' );

			// set up stripe data eraser
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * Retrieves privacy example text for stripe plugin
		 *
		 * @return string Privacy message
		 * @since 1.5.1
		 */
		public function get_privacy_message( $section ) {
			$content = '';

			switch ( $section ) {
				case 'collect_and_store':
					$content = '<p>' . __( 'While you visit our site, we’ll track:', 'yith-woocommerce-stripe' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Card unique identifiers: these IDs, returned by Stripe when submitting card details, will be stored to help you check out faster next time you make a purchase on our store.', 'yith-woocommerce-stripe' ) . '</li>' .
							   '<li>' . __( 'User unique identifier: this ID is used to uniquely identify the user on Stripe platform and create charges/subscriptions.', 'yith-woocommerce-stripe' ) . '</li>' .
							   '<li>' . __( 'Subscription unique identifiers: these IDs will identify subscriptions on Stripe and will help us manage renewals that come from Stripe platform.', 'yith-woocommerce-stripe' ) . '</li>' .
							   '</ul>';
					break;
				case 'has_access':
					$content = '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-woocommerce-stripe' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'All data returned by Stripe, including (but not limited to) cards’ unique ID, subscriptions’ data and users’ details.', 'yith-woocommerce-stripe' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'Our team members have access to this information to track user identity on Stripe’s server.', 'yith-woocommerce-stripe' ) . '</p>';
					break;
				case 'payments':
					$content = '<p>' . __( 'We accept payments through Stripe. When processing payments, some of your data will be passed to Stripe, including information required to process or support the payment, such as the purchase total and billing information.', 'yith-woocommerce-stripe' ) . '</p>' .
							   '<p>' . __( 'Please see the <a href="https://stripe.com/us/privacy/">Stripe Worldwide Privacy Policy</a> for more details.', 'yith-woocommerce-stripe' ) . '</p>';
					break;
				case 'share':
				default:
					break;
			}

			return apply_filters( 'yith_wcstripe_privacy_policy_content', $content, $section );
		}

		/**
		 * Register eraser for stripe plugin
		 *
		 * @param $erasers array Array of currently registered erasers
		 *
		 * @return array Array of filtered erasers
		 * @since 1.5.1
		 */
		public function register_eraser( $erasers ) {
			$erasers['yith_wcstripe_eraser'] = array(
				'eraser_friendly_name' => __( 'Stripe data', 'yith-woocommerce-stripe' ),
				'callback'             => array( $this, 'stripe_data_eraser' )
			);

			return $erasers;
		}

		/**
		 * Deletes stripe data for the user
		 *
		 * @param $email_address string Email of the users that requested export
		 * @param $page          int Current page processed
		 *
		 * @return array Result of the operation
		 * @since 1.5.1
		 */
		public function stripe_data_eraser( $email_address, $page ) {
			$user     = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.
			$response = array(
				'items_removed'  => false,
				'items_retained' => false,
				'messages'       => array(),
				'done'           => true,
			);

			if ( ! $user instanceof WP_User ) {
				return $response;
			}

			delete_user_meta( $user->ID, 'failed_invoices' );
			YITH_WCStripe_Customer()->delete_usermeta_info( $user->ID );

			$response['messages'][]    = __( 'Removed stripe\'s customer data.', 'yith-woocommerce-stripe' );
			$response['items_removed'] = true;

			return $response;
		}
	}
}