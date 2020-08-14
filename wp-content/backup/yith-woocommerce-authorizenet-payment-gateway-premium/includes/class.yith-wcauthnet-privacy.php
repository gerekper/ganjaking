<?php
/**
 * Privacy class; added to let customer export personal data
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Authorize.net Payment Gateway
 * @version 1.1.4
 */

if ( ! defined( 'YITH_WCAUTHNET' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAUTHNET_Privacy' ) ) {
	/**
	 * YITH Authorize.net Privacy class
	 *
	 * @since 1.1.4
	 */
	class YITH_WCAUTHNET_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAUTHNET_Privacy
		 * @since 1.1.4
		 */
		public function __construct() {
			parent::__construct( 'YITH WooCommerce Authorize.net Payment Gateway' );

			// set up Authorize.net data eraser
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * Retrieves privacy example text for authorize plugin
		 *
		 * @return string Privacy message
		 * @since 1.1.4
		 */
		public function get_privacy_message( $section ) {
			$content = '';

			switch ( $section ) {
				case 'collect_and_store':
					$content = '<p>' . __( 'While you visit our site, we’ll track:', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Cards unique identifiers: these IDs, returned by Authorize.net when submitting cards, will be stored to help you process a faster checkout next time you purchase on our store', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</li>' .
							   '<li>' . __( 'User unique identifier: this ID is used to uniquely identify user on Authorize platform, and create charge/refunds.', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</li>' .
							   '</ul>';
					break;
				case 'has_access':
					$content = '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'All data returned by Authorize.net, including (but not limited to) cards’ unique ID and users’ details', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'Our team members have access to this information to track user’s identity on Authorize.net’s server', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</p>';
					break;
				case 'payments':
					$content = '<p>' . __( 'We accept payments through Authorize.net. When processing payments, some of your data will be passed to Authorize.net, including information required to process or support the payment, such as the purchase total and billing information.', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</p>' .
							   '<p>' . __( 'Please see the <a href="https://www.authorize.net/company/privacy/">Authorize.net Privacy Policy</a> for more details.', 'yith-woocommerce-authorizenet-payment-gateway' ) . '</p>';
					break;
				case 'share':
				default:
					break;
			}

			return apply_filters( 'yith_wcauthnet_privacy_policy_content', $content, $section );
		}

		/**
		 * Register eraser for authorize plugin
		 *
		 * @param $erasers array Array of currently registered erasers
		 *
		 * @return array Array of filtered erasers
		 * @since 1.1.4
		 */
		public function register_eraser( $erasers ) {
			$erasers['yith_authorize_eraser'] = array(
				'eraser_friendly_name' => __( 'Authorize.net data', 'yith-woocommerce-authorizenet-payment-gateway' ),
				'callback'             => array( $this, 'authorize_data_eraser' )
			);

			return $erasers;
		}

		/**
		 * Deletes Authorize data for the user
		 *
		 * @param $email_address string Email of the users that requested export
		 * @param $page          int Current page processed
		 *
		 * @return array Result of the operation
		 * @since 1.1.4
		 */
		public function authorize_data_eraser( $email_address, $page ) {
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

			delete_user_meta( $user->ID, '_authorize_net_payment_profiles' );
			delete_user_meta( $user->ID, '_authorize_net_profile_id' );
			delete_user_meta( $user->ID, '_authorize_net_profile_id' );

			$response['messages'][]    = __( 'Removed Authorize\'s customer data.', 'yith-woocommerce-authorizenet-payment-gateway' );
			$response['items_removed'] = true;

			return $response;
		}
	}
}