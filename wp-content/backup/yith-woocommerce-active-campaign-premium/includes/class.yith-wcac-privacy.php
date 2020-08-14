<?php
/**
 * Privacy class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAC_Privacy' ) ) {
	/**
	 * WooCommerce Active Campaign Privacy class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAC_Privacy extends YITH_Privacy_Plugin_Abstract {

		/**
		 * Constructor
		 *
		 * @return \YITH_WCAC_Privacy
		 */
		public function __construct() {
			parent::__construct( 'YITH Active Campaign for WooCommerce' );

			// hook to order exporter, to export personal data sent to Active Campaign
			add_filter( 'woocommerce_privacy_export_order_personal_data_props', array(
				$this,
				'register_props_to_export_within_order'
			), 10, 2 );
			add_filter( 'woocommerce_privacy_export_order_personal_data_prop', array(
				$this,
				'retrieve_prop_to_export_within_order'
			), 10, 3 );

			// hook to order anonymizer, to remove personal data sent to Active Campaign and unsubscribe user
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
					$content = '<p>' . __( 'During checkout, we’ll track:', 'yith-woocommerce-active-campaign' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Email address, first name, last name: we’ll use this information in order to populate the basic details of your account on Active Campaign', 'yith-woocommerce-active-campaign' ) . '</li>' .
							   '<li>' . __( 'Billing or shipping data: we’ll use this information in order to complete your account on Active Campaign', 'yith-woocommerce-active-campaign' ) . '</li>' .
							   '<li>' . __( 'Order total, payment method, customer IP: we’ll use this information in order to improve your account, and send you targeted newsletters', 'yith-woocommerce-active-campaign' ) . '</li>' .
							   '<li>' . __( 'Preferences, products purchased, categories of products purchased: we’ll use this information in order to improve your profile and send you targeted newsletters', 'yith-woocommerce-active-campaign' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'When subscribing to a newsletter, we’ll track:', 'yith-woocommerce-active-campaign' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'Email address: we’ll use this in order to populate basic information of your account on Active Campaign', 'yith-woocommerce-active-campaign' ) . '</li>' .
							   '<li>' . __( 'Any other information submitted through the registration form: we’ll use this information in order to complete your account on Active Campaign, and send you targeted emails.', 'yith-woocommerce-active-campaign' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'We’ll ask for your explicit consent before sending any data to Active Campaign servers.', 'yith-woocommerce-active-campaign' ) . '</p>';
					break;
				case 'has_access':
					$content = '<p>' . __( 'Members of our team have access to the information you provide us. For example, both Administrators and Shop Managers can access:', 'yith-woocommerce-active-campaign' ) . '</p>' .
							   '<ul>' .
							   '<li>' . __( 'All personal data sent to Active Campaign,', 'yith-woocommerce-active-campaign' ) . '</li>' .
							   '<li>' . __( 'Log of your consent,', 'yith-woocommerce-active-campaign' ) . '</li>' .
							   '<li>' . __( 'Active Campaign lists to which you are subscribed,', 'yith-woocommerce-active-campaign' ) . '</li>' .
							   '</ul>' .
							   '<p>' . __( 'Our team members have access to these data in order to review what our plugin sent to Active Campaign servers.', 'yith-woocommerce-active-campaign' ) . '</p>';
					break;
				case 'share':
					$content = '<p>' . __( 'We contact Active Campaign in order to subscribe users, and retrieve information about lists and groups. When processing subscriptions, some of your data will be passed to Active Campaign, including information required to target your account, such as the billing information and the product(s) you’re purchasing.', 'yith-woocommerce-active-campaign' ) . '</p>' .
							   '<p>' . __( 'Please see the <a href="https://www.activecampaign.com/privacy-policy/">Active Campaign Privacy Policy</a> for more details.', 'yith-woocommerce-active-campaign' ) . '</p>';
					break;
				case 'payments':
				default:
					break;
			}

			return apply_filters( 'yith_wcac_privacy_policy_content', $content, $section );
		}

		/**
		 * Register erasers
		 *
		 * @param $erasers array Array of registered erasers
		 *
		 * @return array Filtered array of erasers
		 */
		public function register_eraser( $erasers ) {
			// exports data about affiliate, and overall activity on the site
			if ( apply_filters( 'yith_wcac_remove_personal_data_unsubscribe_user', false ) ) {
				$erasers['yith_wcac_subscribed_lists'] = array(
					'eraser_friendly_name' => __( 'Active Campaign Subscribed Lists', 'yith-woocommerce-active-campaign' ),
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

			$subscribed_lists = get_user_meta( $user->ID, '_yith_wcac_subscribed_lists', true );

			if ( ! empty( $subscribed_lists ) ) {
				foreach ( $subscribed_lists as $list_id => $emails ) {

					if ( ! empty( $emails ) ) {
						foreach ( $emails as $email ) {
							YITH_WCAC()->unsubscribe( $list_id, $email );
						}
					}
				}

				update_user_meta( $user->ID, '_yith_wcac_subscribed_lists', array() );

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
			$props['yith_wcac_personal_data'] = __( 'Active Campaign Data', 'yith-woocommerce-active-campaign' );

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
			if ( 'yith_wcac_personal_data' == $prop ) {
				$personal_data   = yit_get_prop( $order, '_yith_wcac_personal_data', true );
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
		 * Anonymize personal data from the order, and unsubscribe user from Active Campaign lists
		 *
		 * @param $order \WC_Order Current order
		 *
		 * @return void
		 */
		public function remove_props_from_order( $order ) {
			$personal_data    = yit_get_prop( $order, '_yith_wcac_personal_data', true );
			$subscribed_lists = yit_get_prop( $order, '_yith_wcac_subscribed_lists', true );

			$props_to_remove = apply_filters( 'yith_wcac_privacy_remove_personal_data_props', array(
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

					$personal_data[ $id ]['value'] = apply_filters( 'yith_wcac_privacy_remove_personal_data_prop_value', $anon_value, $id, $data['value'], $data_type, $order );
				}

				yit_save_prop( $order, '_yith_wcac_personal_data', $personal_data );
			}

			if ( ! empty( $subscribed_lists ) && apply_filters( 'yith_wcac_remove_personal_data_unsubscribe', false ) ) {
				foreach ( $subscribed_lists as $list_id => $emails ) {
					if ( ! empty( $emails ) ) {
						foreach ( $emails as $email ) {

							if ( apply_filters( 'yith_wcac_remove_personal_data_unsubscribe_user_from_list', true, $list_id, $email, $order ) ) {
								YITH_WCAC()->unsubscribe( $list_id, $email );
							}
						}
					}
				}

				yit_save_prop( $order, '_yith_wcac_subscribed_lists', array() );
			}
		}
	}
}