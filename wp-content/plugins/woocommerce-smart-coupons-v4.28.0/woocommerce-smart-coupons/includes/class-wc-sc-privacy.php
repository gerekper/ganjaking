<?php
/**
 * Handle privacy of customers
 *
 * @author      StoreApps
 * @since       3.3.0
 * @version     1.0
 *
 * @package     woocommerce-smart-coupons/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

if ( ! class_exists( 'WC_SC_Privacy' ) ) {

	/**
	 * Class for handling privacy for customer & maintaining GDPR compliance of the plugin
	 */
	class WC_SC_Privacy extends WC_Abstract_Privacy {

		/**
		 * Variable to hold instance of WC_SC_Privacy
		 *
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Plugin data
		 *
		 * @var $plugin_data
		 */
		public $plugin_data = array();

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->plugin_data = WC_Smart_Coupons::get_smart_coupons_plugin_data();

			parent::__construct( $this->plugin_data['Name'] );

			/* translators: Plugin's name */
			$this->add_exporter( WC_SC_PLUGIN_DIRNAME . '-coupon-data-exporter', sprintf( __( '%s - Coupon Personal Data Exporter', 'woocommerce-smart-coupons' ), $this->plugin_data['Name'] ), array( $this, 'wc_sc_coupon_data_exporter' ) );
			/* translators: Plugin's name */
			$this->add_eraser( WC_SC_PLUGIN_DIRNAME . '-coupon-data-eraser', sprintf( __( '%s - Coupon Personal Data Eraser', 'woocommerce-smart-coupons' ), $this->plugin_data['Name'] ), array( $this, 'wc_sc_coupon_data_eraser' ) );

			/* translators: Plugin's name */
			$this->add_exporter( WC_SC_PLUGIN_DIRNAME . '-order-data-exporter', sprintf( __( '%s - Order Personal Data Exporter', 'woocommerce-smart-coupons' ), $this->plugin_data['Name'] ), array( $this, 'wc_sc_order_data_exporter' ) );
			/* translators: Plugin's name */
			$this->add_eraser( WC_SC_PLUGIN_DIRNAME . '-order-data-eraser', sprintf( __( '%s - Order Personal Data Eraser', 'woocommerce-smart-coupons' ), $this->plugin_data['Name'] ), array( $this, 'wc_sc_order_data_eraser' ) );

			/* translators: Plugin's name */
			$this->add_exporter( WC_SC_PLUGIN_DIRNAME . '-user-data-exporter', sprintf( __( '%s - User Personal Data Exporter', 'woocommerce-smart-coupons' ), $this->plugin_data['Name'] ), array( $this, 'wc_sc_user_data_exporter' ) );
			/* translators: Plugin's name */
			$this->add_eraser( WC_SC_PLUGIN_DIRNAME . '-user-data-eraser', sprintf( __( '%s - User Personal Data Eraser', 'woocommerce-smart-coupons' ), $this->plugin_data['Name'] ), array( $this, 'wc_sc_user_data_eraser' ) );

			add_action( 'woocommerce_privacy_before_remove_order_personal_data', array( $this, 'remove_order_personal_data' ) );

			add_filter( 'woocommerce_get_settings_account', array( $this, 'account_settings' ) );
		}

		/**
		 * Get single instance of WC_SC_Privacy
		 *
		 * @return WC_SC_Privacy Singleton object of WC_SC_Privacy
		 */
		public static function get_instance() {
			// Check if instance is already exists.
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param string $function_name The function name.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Gets the message of the privacy to display.
		 */
		public function get_privacy_message() {

			$content = '<h2>' . esc_html__( 'Store Credit/Gift Certificate', 'woocommerce-smart-coupons' ) . '</h2>
						<strong>' . esc_html__( 'What we access?', 'woocommerce-smart-coupons' ) . '</strong>
						<ul>
							<li>' . esc_html__( 'If you are logged in: We access your billing email address saved in your account & billing email address entered during purchase', 'woocommerce-smart-coupons' ) . '</li>
							<li>' . esc_html__( 'If you are a visitor: We access your billing email address entered during purchase', 'woocommerce-smart-coupons' ) . '</li>
						</ul>
						<strong>' . esc_html__( 'What we store & why?', 'woocommerce-smart-coupons' ) . '</strong>
						<ul>
							<li>' . esc_html__( 'Coupon code generated for you', 'woocommerce-smart-coupons' ) . '</li>
							<li>' . esc_html__( 'Coupon code passed via URL', 'woocommerce-smart-coupons' ) . '</li>
							<li>' . esc_html__( 'Coupon amount, email & message entered for gift card receiver', 'woocommerce-smart-coupons' ) . '</li>
						</ul>
						<p>' . esc_html__( 'We store these data so that we can process it for you whenever required.', 'woocommerce-smart-coupons' ) . '</p>';

			return $content;

		}

		/**
		 * Returns Smart Coupons data based on email.
		 *
		 * @param string $email_address The email address.
		 * @param int    $page Pagination.
		 *
		 * @return array
		 */
		protected function get_wc_sc_data( $email_address, $page ) {

			global $wpdb;

			$wpdb->query( $wpdb->prepare( 'SET SESSION group_concat_max_len=%d', 999999 ) ); // phpcs:ignore

			$results = wp_cache_get( 'wc_sc_coupon_data_' . sanitize_key( $email_address ), 'woocommerce_smart_coupons' );

			if ( false === $results ) {
				$results = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						"SELECT p.ID, 
							p.post_title,
							p.post_date,
							GROUP_CONCAT( pm.meta_key ORDER BY pm.meta_id SEPARATOR '###' ) AS meta_keys, 
							GROUP_CONCAT( pm.meta_value ORDER BY pm.meta_id SEPARATOR '###' ) AS meta_values
							FROM $wpdb->posts AS p
							LEFT JOIN $wpdb->postmeta AS pm
								ON ( p.ID = pm.post_id AND p.post_type = %s AND pm.meta_key IN ( %s, %s, %s ) )
							WHERE pm.meta_value = %s
								OR pm.meta_value LIKE %s
								OR pm.meta_value <> ''
							GROUP BY p.ID
							ORDER BY p.ID",
						'shop_coupon',
						'discount_type',
						'customer_email',
						'generated_from_order_id',
						'smart_coupon',
						'%' . $wpdb->esc_like( $email_address ) . '%'
					),
					ARRAY_A
				);
				wp_cache_set( 'wc_sc_coupon_data_' . sanitize_key( $email_address ), $results, 'woocommerce_smart_coupons' );
				$this->maybe_add_cache_key( 'wc_sc_coupon_data_' . sanitize_key( $email_address ) );
			}

			$coupon_data = array();

			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {

					$meta_keys   = ( ! empty( $result['meta_keys'] ) ) ? explode( '###', $result['meta_keys'] ) : array();
					$meta_values = ( ! empty( $result['meta_values'] ) ) ? explode( '###', $result['meta_values'] ) : array();

					if ( count( $meta_keys ) === count( $meta_values ) ) {
						$meta_values = array_map( 'maybe_unserialize', $meta_values );
						$meta        = array_combine( $meta_keys, $meta_values );
						if ( empty( $meta['discount_type'] ) || 'smart_coupon' !== $meta['discount_type'] ) {
							continue;
						}
						unset( $meta['discount_type'] );
						if ( empty( $meta['customer_email'] ) ) {
							continue;
						}
						$customer_emails = array_unique( $meta['customer_email'] );
						$common_email    = array_intersect( array( $email_address ), $customer_emails );
						if ( empty( $common_email ) ) {
							continue;
						}
						$meta['customer_email'] = current( $common_email );
					} else {
						continue;
					}

					if ( empty( $coupon_data[ $result['ID'] ] ) || ! is_array( $coupon_data[ $result['ID'] ] ) ) {
						$coupon_data[ $result['ID'] ] = array();
					}

					$coupon_data[ $result['ID'] ]['coupon_id']    = $result['ID'];
					$coupon_data[ $result['ID'] ]['coupon_code']  = $result['post_title'];
					$coupon_data[ $result['ID'] ]['created_date'] = $result['post_date'];

					if ( ! empty( $meta ) ) {
						foreach ( $meta as $key => $value ) {
							$coupon_data[ $result['ID'] ][ $key ] = $value;
						}
					}
				}
			}

			return $coupon_data;
		}

		/**
		 * Handle exporting data for Smart Coupons' Coupon data.
		 *
		 * @param string $email_address E-mail address to export.
		 * @param int    $page          Pagination of data.
		 *
		 * @return array
		 */
		public function wc_sc_coupon_data_exporter( $email_address, $page = 0 ) {
			$done           = false;
			$data_to_export = array();

			$coupon_data = $this->get_wc_sc_data( $email_address, (int) $page );

			if ( 0 < count( $coupon_data ) ) {
				$data  = array();
				$index = 0;
				foreach ( $coupon_data as $coupon_id => $coupon ) {
					$data[] = array(
						'name'  => $coupon['coupon_code'],
						'value' => $coupon['customer_email'],
					);
				}
				$data_to_export[] = array(
					'group_id'    => 'wc_smart_coupons_coupon_data',
					'group_label' => __( 'Store Credit/Gift Certificate - Coupon Data', 'woocommerce-smart-coupons' ),
					'item_id'     => 'wc-smart-coupons-coupon-data-' . sanitize_title( $email_address ),
					'data'        => $data,
				);

				$done = 10 > count( $coupon_data );
			} else {
				$done = true;
			}

			return array(
				'data' => $data_to_export,
				'done' => $done,
			);
		}

		/**
		 * Finds and erases Smart Coupons by email address.
		 *
		 * @param string $email_address The user email address.
		 * @param int    $page  Pagination.
		 * @return array An array of personal data in name value pairs
		 */
		public function wc_sc_coupon_data_eraser( $email_address, $page ) {
			$coupon_data = $this->get_wc_sc_data( $email_address, (int) $page );

			$done           = false;
			$items_removed  = false;
			$items_retained = false;
			$messages       = array();

			foreach ( $coupon_data as $coupon ) {
				list( $removed, $retained, $msgs ) = $this->maybe_handle_coupon_data( $coupon );
				$items_removed                    |= $removed;
				$items_retained                   |= $retained;
				$messages                          = array_merge( $messages, $msgs );
			}

			// Tell core if we have more coupons to work on still.
			$done = count( $coupon_data ) < 10;

			return array(
				'items_removed'  => $items_removed,
				'items_retained' => $items_retained,
				'messages'       => $messages,
				'done'           => $done,
			);
		}

		/**
		 * Handle eraser of Coupon data
		 *
		 * @param array $coupon The coupon data.
		 * @return array
		 */
		protected function maybe_handle_coupon_data( $coupon ) {
			if ( empty( $coupon ) || ! $this->is_retention_expired( $coupon['created_date'] ) ) {
				return array( false, false, array() );
			}

			delete_post_meta( $coupon['coupon_id'], 'customer_email' );
			delete_post_meta( $coupon['coupon_id'], 'generated_from_order_id' );
			wp_delete_post( $coupon['coupon_id'], true );

			return array( true, false, array( '<strong>' . __( 'Store Credit/Gift Certificate', 'woocommerce-smart-coupons' ) . '</strong> - ' . __( 'Removed Coupon Personal Data', 'woocommerce-smart-coupons' ) ) );
		}

		/**
		 * Returns Smart Coupons data based on email.
		 *
		 * @param string $email_address The email addres.
		 * @param int    $page Pagination.
		 *
		 * @return array
		 */
		protected function get_wc_sc_user_data( $email_address, $page ) {

			$user_data = array();

			$user = get_user_by( 'email', $email_address );

			if ( ! empty( $user->ID ) ) {

				$sc_shortcode_generated_coupons = get_user_meta( $user->ID, '_sc_shortcode_generated_coupons', true );

				if ( ! empty( $sc_shortcode_generated_coupons ) ) {
					$user_data['shortcode'] = array();
					foreach ( $sc_shortcode_generated_coupons as $sc_shortcode_generated_coupons ) {
						$user_data['shortcode'][] = implode( ', ', $sc_shortcode_generated_coupons );
					}
				}

				$sc_applied_coupon_from_url = get_user_meta( $user->ID, 'sc_applied_coupon_from_url', true );

				if ( ! empty( $sc_applied_coupon_from_url ) ) {
					$user_data['url'] = implode( ', ', $sc_applied_coupon_from_url );
				}
			}

			return $user_data;
		}

		/**
		 * Handle exporting data for Smart Coupons User data.
		 *
		 * @param string $email_address E-mail address to export.
		 * @param int    $page          Pagination of data.
		 *
		 * @return array
		 */
		public function wc_sc_user_data_exporter( $email_address, $page = 0 ) {
			$done           = false;
			$data_to_export = array();

			$user_data = $this->get_wc_sc_user_data( $email_address, (int) $page );

			if ( 0 < count( $user_data ) ) {
				$shortcode = array();
				$url       = array();
				$index     = 0;
				foreach ( $user_data as $key => $value ) {
					if ( 'shortcode' === $key ) {
						foreach ( $value as $val ) {
							$shortcode[] = array(
								'name'  => __( 'Coupon', 'woocommerce-smart-coupons' ),
								'value' => $val,
							);
						}
						$data_to_export[] = array(
							'group_id'    => 'wc_smart_coupons_coupon_shortcode_data',
							'group_label' => __( 'Generated Coupon Data', 'woocommerce-smart-coupons' ),
							'item_id'     => 'wc-smart-coupons-shorcode-data-' . sanitize_title( $email_address ),
							'data'        => $shortcode,
						);
					} elseif ( 'url' === $key ) {
						$url[]            = array(
							'name'  => __( 'Coupon' ),
							'value' => $value,
						);
						$data_to_export[] = array(
							'group_id'    => 'wc_smart_coupons_url_data',
							'group_label' => __( 'Coupon passed in URL', 'woocommerce-smart-coupons' ),
							'item_id'     => 'wc-smart-coupons-url-data-' . sanitize_title( $email_address ),
							'data'        => $url,
						);
					}
				}

				$done = 10 > count( $user_data );
			} else {
				$done = true;
			}

			return array(
				'data' => $data_to_export,
				'done' => $done,
			);
		}

		/**
		 * Finds and erases Smart Coupons by email address.
		 *
		 * @param string $email_address The user email address.
		 * @param int    $page  Pagination.
		 * @return array An array of personal data in name value pairs
		 */
		public function wc_sc_user_data_eraser( $email_address, $page ) {

			$user = get_user_by( 'email', $email_address );

			$done           = false;
			$items_removed  = false;
			$items_retained = false;
			$messages       = array();

			if ( ! empty( $user->ID ) ) {

				$meta_keys = array( '_sc_shortcode_generated_coupons', 'sc_applied_coupon_from_url' );

				foreach ( $meta_keys as $meta_key ) {
					delete_user_meta( $user->ID, $meta_key );
					$removed         = true;
					$retained        = false;
					$msgs            = array( '<strong>' . __( 'Store Credit/Gift Certificate', 'woocommerce-smart-coupons' ) . '</strong> - ' . __( 'Removed User Personal Data', 'woocommerce-smart-coupons' ) );
					$items_removed  |= $removed;
					$items_retained |= $retained;
					$messages        = array_merge( $messages, $msgs );
				}
			}

			// Tell core if we have more coupons to work on still.
			$done = true;

			return array(
				'items_removed'  => $items_removed,
				'items_retained' => $items_retained,
				'messages'       => $messages,
				'done'           => $done,
			);
		}

		/**
		 * Returns Smart Coupons data based on email.
		 *
		 * @param string $email_address The email address.
		 * @param int    $page Pagination.
		 *
		 * @return array
		 */
		protected function get_wc_sc_order_data( $email_address, $page ) {

			global $wpdb;

			$user = get_user_by( 'email', $email_address );

			$order_ids = wp_cache_get( 'wc_sc_order_ids_for_' . sanitize_key( $email_address ), 'woocommerce_smart_coupons' );

			if ( false === $order_ids ) {
				$order_ids = $wpdb->get_col( // phpcs:ignore
					$wpdb->prepare(
						"SELECT pm.post_id
							FROM {$wpdb->posts} AS p
								LEFT JOIN {$wpdb->postmeta} AS pm
									ON ( p.ID = pm.post_id AND p.post_type = %s )
							WHERE pm.meta_key = %s
								AND pm.meta_value = %d",
						'shop_order',
						'_customer_user',
						$user->ID
					)
				);
				wp_cache_set( 'wc_sc_order_ids_for_' . sanitize_key( $email_address ), $order_ids, 'woocommerce_smart_coupons' );
				$this->maybe_add_cache_key( 'wc_sc_order_ids_for_' . sanitize_key( $email_address ) );
			}

			if ( empty( $order_ids ) ) {
				return array();
			}

			$how_many     = count( $order_ids );
			$placeholders = array_fill( 0, $how_many, '%d' );

			$query = $wpdb->prepare(
				"SELECT p.ID, 
					pm.meta_value AS sc_coupon_receiver_details
					FROM $wpdb->posts AS p
					LEFT JOIN $wpdb->postmeta AS pm
						ON ( p.ID = pm.post_id AND p.post_type = %s AND pm.meta_key = %s )",
				'shop_order',
				'sc_coupon_receiver_details'
			);

			$query .= $wpdb->prepare( 'WHERE p.ID IN (' . implode( ',', $placeholders ) . ') ', $order_ids ); // phpcs:ignore

			$query .= $wpdb->prepare(
				'	AND pm.meta_value <> %s
										GROUP BY p.ID
										ORDER BY p.ID',
				''
			);

			$unique_order_ids = array_unique( $order_ids );

			$results = wp_cache_get( 'wc_sc_receiver_detail_in_orders_' . implode( '_', $unique_order_ids ), 'woocommerce_smart_coupons' );

			if ( false === $results ) {
				$results = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore
				wp_cache_set( 'wc_sc_receiver_detail_in_orders_' . implode( '_', $unique_order_ids ), $results, 'woocommerce_smart_coupons' );
				$this->maybe_add_cache_key( 'wc_sc_receiver_detail_in_orders_' . implode( '_', $unique_order_ids ) );
			}

			$order_data = array();

			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					$order_data[ $result['ID'] ] = maybe_unserialize( $result['sc_coupon_receiver_details'] );
				}
			}

			return $order_data;
		}

		/**
		 * Handle exporting data for Smart Coupons User data.
		 *
		 * @param string $email_address E-mail address to export.
		 * @param int    $page          Pagination of data.
		 *
		 * @return array
		 */
		public function wc_sc_order_data_exporter( $email_address, $page = 0 ) {
			$done           = false;
			$data_to_export = array();

			$order_data = $this->get_wc_sc_order_data( $email_address, (int) $page );

			if ( 0 < count( $order_data ) ) {
				$index = 0;
				foreach ( $order_data as $key => $value ) {
					foreach ( $value as $val ) {
						$index++;
						$data = array();
						foreach ( $val as $k => $v ) {
							if ( $val['email'] !== $email_address && 'code' === $k ) {
								continue;
							}
							switch ( $k ) {
								case 'code':
									$name = __( 'Coupon Code', 'woocommerce-smart-coupons' );
									break;
								case 'amount':
									$name = __( 'Coupon Amount', 'woocommerce-smart-coupons' );
									break;
								case 'email':
									$name = __( 'Coupon For', 'woocommerce-smart-coupons' );
									break;
								case 'message':
									$name = __( 'Message', 'woocommerce-smart-coupons' );
									break;
							}
							$data[] = array(
								'name'  => $name,
								'value' => $v,
							);
						}
						$data_to_export[] = array(
							'group_id'    => 'wc_smart_coupons_order_data_' . $index,
							'group_label' => __( 'Store Credit/Gift Certificate - Order Data', 'woocommerce-smart-coupons' ),
							'item_id'     => 'wc-smart-coupons-order-data-' . $index,
							'data'        => $data,
						);
					}
				}

				$done = 10 > count( $order_data );
			} else {
				$done = true;
			}

			return array(
				'data' => $data_to_export,
				'done' => $done,
			);
		}

		/**
		 * Finds and erases Smart Coupons by email address.
		 *
		 * @param string $email_address The user email address.
		 * @param int    $page  Pagination.
		 * @return array An array of personal data in name value pairs
		 */
		public function wc_sc_order_data_eraser( $email_address, $page ) {

			global $wpdb;

			$user = get_user_by( 'email', $email_address );

			$orders = wp_cache_get( 'wc_sc_order_by_email_for_' . $user->ID, 'woocommerce_smart_coupons' );

			if ( false === $orders ) {
				$orders = $wpdb->get_results( // phpcs:ignore
					$wpdb->prepare(
						"SELECT p.post_date AS created_date,
								pm.post_id
							FROM {$wpdb->posts} AS p
								LEFT JOIN {$wpdb->postmeta} AS pm
									ON ( p.ID = pm.post_id AND p.post_type = %s )
							WHERE pm.meta_key = %s
								AND pm.meta_value = %d",
						'shop_order',
						'_customer_user',
						$user->ID
					),
					ARRAY_A
				);
				wp_cache_set( 'wc_sc_order_by_email_for_' . $user->ID, $orders, 'woocommerce_smart_coupons' );
				$this->maybe_add_cache_key( 'wc_sc_order_by_email_for_' . $user->ID );
			}

			$done           = false;
			$items_removed  = false;
			$items_retained = false;
			$messages       = array();

			if ( ! empty( $orders ) ) {
				foreach ( $orders as $order ) {
					list( $removed, $retained, $msgs ) = $this->maybe_handle_order_data( $order );
					$items_removed                    |= $removed;
					$items_retained                   |= $retained;
					$messages                          = array_merge( $messages, $msgs );
				}
			}

			// Tell core if we have more coupons to work on still.
			$done = count( $orders ) < 10;

			return array(
				'items_removed'  => $items_removed,
				'items_retained' => $items_retained,
				'messages'       => $messages,
				'done'           => $done,
			);
		}

		/**
		 * Handle eraser of Coupon data
		 *
		 * @param array $order The order data.
		 * @return array
		 */
		protected function maybe_handle_order_data( $order ) {
			if ( empty( $order ) || ! $this->is_retention_expired( $order['created_date'] ) ) {
				return array( false, false, array() );
			}

			delete_post_meta( $order['post_id'], 'sc_coupon_receiver_details' );
			delete_post_meta( $order['post_id'], 'gift_receiver_email' );
			delete_post_meta( $order['post_id'], 'gift_receiver_message' );
			delete_post_meta( $order['post_id'], 'gift_sending_timestamp' );

			return array( true, false, array( '<strong>' . __( 'Store Credit/Gift Certificate', 'woocommerce-smart-coupons' ) . '</strong> - ' . __( 'Removed Order Personal Data', 'woocommerce-smart-coupons' ) ) );
		}

		/**
		 * Remove Smart Coupons order personal data
		 *
		 * @param  WC_Order $order The order object.
		 */
		public function remove_order_personal_data( $order ) {
			$created_date = $order->get_date_created();
			$args         = array(
				'post_id'      => $order->get_id(),
				'created_date' => $created_date,
			);
			$result       = $this->maybe_handle_order_data( $args );
		}

		/**
		 * Checks if create date is passed retention duration.
		 *
		 * @param string $created_date The date.
		 */
		public function is_retention_expired( $created_date ) {
			$retention  = wc_parse_relative_date_option( get_option( 'woocommerce_smart_coupons_retention' ) );
			$is_expired = false;
			$time_span  = time() - strtotime( $created_date );

			if ( empty( $retention ) || empty( $created_date ) ) {
				return false;
			}

			switch ( $retention['unit'] ) {
				case 'days':
					$retention = $retention['number'] * DAY_IN_SECONDS;

					if ( $time_span > $retention ) {
						$is_expired = true;
					}
					break;
				case 'weeks':
					$retention = $retention['number'] * WEEK_IN_SECONDS;

					if ( $time_span > $retention ) {
						$is_expired = true;
					}
					break;
				case 'months':
					$retention = $retention['number'] * MONTH_IN_SECONDS;

					if ( $time_span > $retention ) {
						$is_expired = true;
					}
					break;
				case 'years':
					$retention = $retention['number'] * YEAR_IN_SECONDS;

					if ( $time_span > $retention ) {
						$is_expired = true;
					}
					break;
			}

			return $is_expired;
		}

		/**
		 * Add retention settings to account tab.
		 *
		 * @param array $settings Settings.
		 * @return array $settings Updated
		 */
		public function account_settings( $settings ) {
			$insert_setting = array(
				array(
					'title'       => __( 'Retain Store Credit/Gift Certificate', 'woocommerce-smart-coupons' ),
					'desc_tip'    => __( 'Store Credit/Gift Certificate that are stored for customers via coupons. If erased, the customer will not be able to use the coupons.', 'woocommerce-smart-coupons' ),
					'id'          => 'woocommerce_smart_coupons_retention',
					'type'        => 'relative_date_selector',
					'placeholder' => __( 'N/A', 'woocommerce-smart-coupons' ),
					'default'     => '',
					'autoload'    => false,
				),
			);

			array_splice( $settings, ( count( $settings ) - 1 ), 0, $insert_setting );

			return $settings;
		}

	}

}

WC_SC_Privacy::get_instance();
