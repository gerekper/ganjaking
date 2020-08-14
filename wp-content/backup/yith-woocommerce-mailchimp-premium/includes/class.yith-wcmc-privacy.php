<?php
/**
 * Privacy class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMC_Privacy' ) ) {
	/**
	 * WooCommerce Mailchimp Privacy class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMC_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * Constructor
		 *
		 * @return \YITH_WCMC_Privacy
		 */
		public function __construct() {
			parent::__construct( _x( 'YITH WooCommerce Mailchimp', 'Privacy Policy Content', 'yith-woocommerce-mailchimp' ) );

			// hook to order exporter, to export personal data sent to MailChimp
			add_filter( 'woocommerce_privacy_export_order_personal_data_props', array(
				$this,
				'register_props_to_export_within_order'
			), 10, 2 );
			add_filter( 'woocommerce_privacy_export_order_personal_data_prop', array(
				$this,
				'retrieve_prop_to_export_within_order'
			), 10, 3 );

			// hook to order anonymizer, to remove personal data sent to MailChimp and unsubscribe user
			add_action( 'woocommerce_privacy_before_remove_order_personal_data', array(
				$this,
				'remove_props_from_order'
			), 10, 1 );

			// add list eraser
			add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
		}

		/**
		 * Retrieves privacy example text for stripe plugin
		 *
		 * @return string Privacy message
		 */
		public function get_privacy_message( $section ) {
			$content = '';

			switch ( $section ) {
				case 'collect_and_store':
					$content = '<p>' . __( 'During checkout, we’ll track:', 'yith-woocommerce-mailchimp' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Email address, first name, last name: we’ll use this information in order to populate basic information of your account on MailChimp.', 'yith-woocommerce-mailchimp' ) . '</li>' .
							   '<li>' . __( 'Billing or shipping data: we’ll use this information in order to complete your account on MailChimp', 'yith-woocommerce-mailchimp' ) . '</li>' .
							   '<li>' . __( 'Order total, payment method, customer IP: we’ll use this information in order to improve your account, and send you targeted newsletters', 'yith-woocommerce-mailchimp' ) . '</li>' .
							   '<li>' . __( 'Preferences, products purchased, categories of products purchased: we’ll use this information in order to improve your profile, and send you targeted newsletters', 'yith-woocommerce-mailchimp' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'When subscribing to newsletter, we’ll track:', 'yith-woocommerce-mailchimp' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Email address: we’ll use this in order to populate basic information of your account on MailChimp', 'yith-woocommerce-mailchimp' ) . '</li>' .
							   '<li>' . __( 'Any other information submitted through the registration form: we’ll use this information in order to complete your account on MailChimp and send you targeted emails.', 'yith-woocommerce-mailchimp' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'We’ll ask for your explicit consent before sending any data to MailChimp servers.', 'yith-woocommerce-mailchimp' ) . '</p>';
					break;
				case 'has_access':
					$content = '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-woocommerce-mailchimp' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'All personal data sent to MailChimp;', 'yith-woocommerce-mailchimp' ) . '</li>' .
							   '<li>' . __( 'Log of your consent;', 'yith-woocommerce-mailchimp' ) . '</li>' .
							   '<li>' . __( 'MailChimp lists to which you are subscribed.', 'yith-woocommerce-mailchimp' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'Our team members have access to these data in order to review what our plugin sent to MailChimp servers', 'yith-woocommerce-mailchimp' ) . '</p>';
					break;
				case 'share':
					$content = '<p>' . __( 'We contact MailChimp in order to subscribe users and retrieve information about lists and groups. When processing subscriptions, some of your data will be passed to MailChimp, including information required to target your account, such as the billing information and the product(s) you’re purchasing.', 'yith-woocommerce-mailchimp' ) . '</p>' .
							   '<p>' . __( 'Please see the <a href="https://mailchimp.com/legal/privacy/">MailChimp Privacy Policy</a> for more details.', 'yith-woocommerce-mailchimp' ) . '</p>';
					break;
				case 'payments':
				default:
					break;
			}

			return apply_filters( 'yith_wcmc_privacy_policy_content', $content, $section );
		}

		/**
		 * Register erasers
		 *
		 * @param $erasers array Array of registered erasers
		 *
		 * @return array Filtered array of erasers
		 */
		public function register_eraser( $erasers ) {
			if ( apply_filters( 'yith_wcmc_remove_personal_data_unsubscribe_user', false ) ) {
				// exports data about affiliate, and overall activity on the site
				$erasers['yith_wcmc_subscribed_lists'] = array(
					'eraser_friendly_name' => __( 'Mailchimp Subscribed Lists', 'yith-woocommerce-mailchimp' ),
					'callback'             => array( $this, 'subscribed_lists_eraser' )
				);
			}

			return $erasers;
		}

		/**
		 * Unsubscribe user from mailing lists
		 *
		 * @param $email_address string Email address
		 *
		 * @return array Result of the operation
		 */
		public function subscribed_lists_eraser( $email_address ) {
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

			$subscribed_lists = get_user_meta( $user->ID, '_yith_wcmc_subscribed_lists', true );

			if ( ! empty( $subscribed_lists ) ) {
				foreach ( $subscribed_lists as $list_id => $emails ) {

					if ( ! empty( $emails ) ) {
						foreach ( $emails as $email ) {
							YITH_WCMC()->unsubscribe( $list_id, $email, array( 'delete_member' => true ) );
						}
					}
				}

				update_user_meta( $user->ID, '_yith_wcmc_subscribed_lists', array() );

				$response['messages'][]    = sprintf( __( 'User unsubscribed from %d lists.', 'yith-woocommerce-affiliates' ), count( $subscribed_lists ) );
				$response['items_removed'] = true;
			}

			return $response;
		}

		/**
		 * Register props to export within the order section
		 *
		 * @param $props array Array of props to export
		 * @param $order \WC_Order Current order being exported
		 *
		 * @return array Array of filtered props
		 */
		public function register_props_to_export_within_order( $props, $order ) {
			$props['yith_wcmc_personal_data'] = __( 'Mailchimp Data', 'yith-woocommerce-mailchimp' );

			return $props;
		}

		/**
		 * Retrieve props to export within the order section
		 *
		 * @param $value string Calculated value for current prop
		 * @param $prop  string Current prop
		 * @param $order \WC_Order Current order
		 *
		 * @return string Calculated prop value
		 */
		public function retrieve_prop_to_export_within_order( $value, $prop, $order ) {
			if ( 'yith_wcmc_personal_data' == $prop ) {
				$personal_data   = yit_get_prop( $order, '_yith_wcmc_personal_data', true );
				$formatted_value = array();

				if ( ! empty( $personal_data ) ) {
					foreach ( $personal_data as $id => $data ) {
						$formatted_value[] = $data['label'] . ': ' . $data['value'];
					}
				}

				return implode( ', ', $formatted_value );
			}

			return $value;
		}

		/**
		 * Anonymize personal data from the order, and unsubscribe user from MailChimp lists
		 *
		 * @param $order \WC_Order Current order
		 *
		 * @return void
		 */
		public function remove_props_from_order( $order ) {
			$personal_data    = yit_get_prop( $order, '_yith_wcmc_personal_data', true );
			$subscribed_lists = yit_get_prop( $order, '_yith_wcmc_subscribed_lists', true );

			$props_to_remove = apply_filters( 'yith_wcmc_privacy_remove_personal_data_props', array(
				'customer_ip_address' => 'ip',
				'customer_user_agent' => 'text',
				'billing_first_name'  => 'text',
				'billing_last_name'   => 'text',
				'billing_company'     => 'text',
				'billing_address_1'   => 'text',
				'billing_address_2'   => 'text',
				'billing_city'        => 'text',
				'billing_postcode'    => 'text',
				'billing_state'       => 'address_state',
				'billing_country'     => 'address_country',
				'billing_phone'       => 'phone',
				'billing_email'       => 'email',
				'shipping_first_name' => 'text',
				'shipping_last_name'  => 'text',
				'shipping_company'    => 'text',
				'shipping_address_1'  => 'text',
				'shipping_address_2'  => 'text',
				'shipping_city'       => 'text',
				'shipping_postcode'   => 'text',
				'shipping_state'      => 'address_state',
				'shipping_country'    => 'address_country',
				'customer_user'       => 'numeric_id',
				'transaction_id'      => 'numeric_id',
			), $order );

			if ( ! empty( $personal_data ) ) {
				foreach ( $personal_data as $id => $data ) {
					$data_type = isset( $props_to_remove[ $id ] ) ? $props_to_remove[ $id ] : 'text';

					if ( function_exists( 'wp_privacy_anonymize_data' ) ) {
						$anon_value = wp_privacy_anonymize_data( $data_type, $data['value'] );
					} else {
						$anon_value = '';
					}

					$personal_data[ $id ]['value'] = apply_filters( 'yith_wcmc_privacy_remove_personal_data_prop_value', $anon_value, $id, $data['value'], $data_type, $order );
				}

				yit_save_prop( $order, '_yith_wcmc_personal_data', $personal_data );
			}


			if ( ! empty( $subscribed_lists ) && apply_filters( 'yith_wcmc_remove_personal_data_unsubscribe_user', false ) ) {
				foreach ( $subscribed_lists as $list_id => $emails ) {
					if ( ! empty( $emails ) ) {
						foreach ( $emails as $email ) {

							if ( apply_filters( 'yith_wcmc_remove_personal_data_unsubscribe_user_from_list', true, $list_id, $email, $order ) ) {
								YITH_WCMC()->unsubscribe( $list_id, $email, array( 'delete_member' => true ) );
							}
						}
					}
				}

				yit_save_prop( $order, '_yith_wcmc_subscribed_lists', array() );
			}
		}
	}
}

