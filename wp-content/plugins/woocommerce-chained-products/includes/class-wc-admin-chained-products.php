<?php
/**
 * Class to handle all backend related functionalities in chained products
 *
 * @version     1.3.2
 * @package     woocommerce-chained-products/includes/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

if ( ! class_exists( 'WC_Admin_Chained_Products' ) ) {

	/**
	 * Class to handle all backend related functionalities
	 */
	class WC_Admin_Chained_Products {

		/**
		 * Constructor
		 */
		public function __construct() {

			// For adding/updating chained products to a subscription.
			if ( class_exists( 'WC_Subscriptions' ) ) {
				add_action( 'woocommerce_checkout_subscription_created', array( &$this, 'add_chained_products_to_subscription' ), 10, 3 );
				add_action( 'woocommerce_subscription_item_switched', array( &$this, 'update_chained_products_for_switched_subscription' ), 10, 4 );
			}

			// For adding / saving chained products.
			add_action( 'woocommerce_product_options_related', array( &$this, 'on_product_write_panels' ), 20 );
			add_action( 'save_post', array( &$this, 'on_process_product_meta' ), 1, 2 );

			// Actions for adding / removing products from order.
			add_action( 'wp_ajax_woocommerce_add_order_item', array( &$this, 'on_add_order_item_manually' ), 1 );
			add_action( 'woocommerce_ajax_add_order_item_meta', array( &$this, 'add_order_item_meta_manually' ), 10, 2 );
			add_action( 'wp_ajax_remove_chained_order_items_manually', array( &$this, 'remove_chained_order_items_manually' ) );

			add_filter( 'wp_insert_post_data', array( &$this, 'remove_shortcode_from_post_content' ) );

			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_chained_products_js_css' ), 20 );
			add_action( 'admin_footer', array( &$this, 'chained_products_footer_js' ), 20 );

			if ( $this->is_wc_gte_30() ) {
				add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'set_chained_product_property_in_order_item' ), 10, 3 );
				add_action( 'woocommerce_new_order_item', array( &$this, 'add_chained_products_order_item_meta_3_0' ), 10, 2 );
			} else {
				add_action( 'woocommerce_add_order_item_meta', array( &$this, 'add_chained_products_order_item_meta' ), 10, 2 );
			}

			add_filter( 'woocommerce_hidden_order_itemmeta', array( &$this, 'woocommerce_hide_chained_products_order_itemmeta' ) );

			add_action( 'save_post_shop_order', array( $this, 'add_chained_products_in_given_order' ), 10, 3 );

			add_action( 'woocommerce_product_after_variable_attributes', array( &$this, 'on_product_write_panels' ), 20, 3 );

			add_action( 'woocommerce_ajax_save_product_variations', array( $this, 'cp_save_product_variations' ) );

			// Action to exclude parent variable product from search result.
			add_action( 'wp_ajax_exclude_parent_variable_product_from_search', array( $this, 'exclude_parent_variable_product_from_search' ) );

			if ( $this->is_wc_gte_32() ) {
				add_filter( 'woocommerce_order_get_items', array( $this, 'set_chained_item_meta' ) );
			}

			add_filter( 'woocommerce_get_sections_products', array( &$this, 'cp_register_section' ) );
			add_filter( 'woocommerce_get_settings_products', array( &$this, 'cp_add_settings' ), 10, 2 );

			add_action( 'admin_footer', array( $this, 'cp_styles_and_scripts' ) );
		}

		/**
		 * Function to handle WC compatibility related function call from appropriate class.
		 *
		 * @param string $function_name Function to call.
		 * @param array  $arguments Array of arguments passed while calling $function_name.
		 * @return mixed Result of function call.
		 */
		public function __call( $function_name = '', $arguments = array() ) {

			if ( ! is_callable( 'Chained_Products_WC_Compatibility', $function_name ) ) {
				return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( 'Chained_Products_WC_Compatibility::' . $function_name, $arguments );
			} else {
				return call_user_func( 'Chained_Products_WC_Compatibility::' . $function_name );
			}
		}

		/**
		 * Function to exclude variable parent from search result
		 */
		public function exclude_parent_variable_product_from_search() {
			$search_result = ( isset( $_POST['result'] ) ) ? $_POST['result'] : ''; // phpcs:ignore

			if ( ! empty( $search_result ) ) {
				foreach ( $search_result as $product_id => $product_name ) {

					$product = wc_get_product( $product_id );
					if ( $product instanceof WC_Product_Variable ) {
						unset( $search_result[ $product_id ] );
					}
				}
			}
			echo wp_json_encode( $search_result );
			die();
		}

		/**
		 * Function to set chained item meta while manually adding order from backend ( 3.2 Compatibility )
		 *
		 * @param array $items Order item ID.
		 *
		 * @return array.
		 */
		public function set_chained_item_meta( $items = array() ) {

			if ( empty( $items ) || ! is_array( $items ) ) {
				return $items;
			}

			foreach ( $items as $order_item_id => $item ) {

				if ( ! $item instanceof WC_Order_Item_Product ) {
					continue;
				}

				$chained_item_of     = wc_get_order_item_meta( $order_item_id, '_chained_product_of' );
				$priced_individually = wc_get_order_item_meta( $order_item_id, '_cp_priced_individually' );
				$priced_individually = ( ! empty( $priced_individually ) ) ? $priced_individually : 'no';

				if ( ! empty( $chained_item_of ) && 'no' === $priced_individually ) {
					$quantity = wc_get_order_item_meta( $order_item_id, '_qty' );

					$tax_class = wc_get_order_item_meta( $order_item_id, '_tax_class' );
					$set_tax   = 0;
					if ( ! empty( $tax_class ) ) {
						// Find WC's valid tax classes.
						$valid_tax_classes = is_callable( array( 'WC_Tax', 'get_tax_class_slugs' ) ) ? WC_Tax::get_tax_class_slugs() : array();
						if ( ! empty( $valid_tax_classes ) && is_array( $valid_tax_classes ) && in_array( $tax_class, $valid_tax_classes, true ) ) {
							$set_tax = 1;
						}
					}

					if ( is_callable( array( $item, 'set_total' ) ) ) {
						$item->set_total( wc_format_decimal( 0 ) );
					}

					if ( is_callable( array( $item, 'set_subtotal' ) ) ) {
						$item->set_subtotal( wc_format_decimal( 0 ) );
					}

					if ( is_callable( array( $item, 'set_quantity' ) ) ) {
						$item->set_quantity( $quantity );
					}

					// Only set valid tax class if found.
					if ( 1 === $set_tax && is_callable( array( $item, 'set_tax_class' ) ) ) {
						$item->set_tax_class( $tax_class );
					}
				}
			}

			return $items;
		}

		/**
		 * Function to add order item meta when order is created manually from backend
		 *
		 * @param int    $item_id Order item ID.
		 * @param object $item The order item details.
		 */
		public function add_order_item_meta_manually( $item_id = 0, $item = null ) {
			global $cp_skip_main_order_items;

			if ( empty( $item ) || ! $item instanceof WC_Order_Item ) {
				return;
			}

			$order_id    = ! empty( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0; // phpcs:ignore
			$order    = ! empty( $order_id ) ? wc_get_order( $order_id ) : null;
			if ( empty( $order ) || ! $order instanceof WC_Order ) {
				return;
			}

			$variation_id = is_callable( array( $item, 'get_variation_id' ) ) ? $item->get_variation_id() : 0;
			$item_to_add  = ! empty( $variation_id ) ? $variation_id : ( is_callable( array( $item, 'get_product_id' ) ) ? $item->get_product_id() : 0 );
			$_product     = ! empty( $item_to_add ) ? wc_get_product( $item_to_add ) : null;

			if ( empty( $_product ) || ! $_product instanceof WC_Product ) {
				return;
			}

			$cp_skip_main_order_items = ! empty( $cp_skip_main_order_items ) ? $cp_skip_main_order_items : array();
			$chained_product_details = ! empty( $_POST['chained_product_details'] ) ? $_POST['chained_product_details'] : array(); // phpcs:ignore

			if ( empty( $chained_product_details ) ) {
				return;
			}

			$item_price                 = is_callable( array( $item, 'get_total' ) ) ? floatval( $item->get_total() ) : 0;
			$parent_chained_product_ids = ! empty( $chained_product_details ) ? array_keys( $chained_product_details ) : array();

			if ( is_array( $parent_chained_product_ids ) && in_array( $item_to_add, $parent_chained_product_ids, true ) && is_array( $cp_skip_main_order_items ) && ! in_array( $item_to_add, $cp_skip_main_order_items, true ) ) {
				// Insert the main order item to the variable to not behave like main order item for next order items.
				$cp_skip_main_order_items[] = $item_to_add;
				return;
			}

			foreach ( $chained_product_details as $parent_id => $chained_values ) {

				$priced_individually = ( ! empty( $chained_values[ $item_to_add ]['priced_individually'] ) ) ? $chained_values[ $item_to_add ]['priced_individually'] : 'no';
				$quantity            = ( ! empty( $chained_values[ $item_to_add ]['unit'] ) ) ? intval( $chained_values[ $item_to_add ]['unit'] ) : 1;
				$item_price          = ( 'yes' === $priced_individually ) ? ( is_callable( array( $item, 'get_total' ) ) ? floatval( $item->get_total() ) : 0 ) : 0;

				if ( 'yes' === $priced_individually ) {
					$args = array(
						'_qty'                => $quantity,
						'_chained_product_of' => $parent_id,
						'_line_total'         => $item_price,
					);
				} else {
					$args = array(
						'_product_id'         => $item_to_add,
						'_variation_id'       => isset( $variation_id ) ? $variation_id : 0,
						'_variation_data'     => isset( $variation_id ) && is_callable( array( $_product, 'get_variation_attributes' ) ) ? $_product->get_variation_attributes() : array(),
						'_name'               => is_callable( array( $_product, 'get_title' ) ) ? $_product->get_title() : '',
						'_tax_class'          => is_callable( array( $_product, 'get_tax_class' ) ) ? $_product->get_tax_class() : '',
						'_qty'                => $quantity,
						'_line_subtotal'      => wc_format_decimal( $item_price ),
						'_line_subtotal_tax'  => '',
						'_line_total'         => wc_format_decimal( $item_price ),
						'_line_tax'           => '',
						'_chained_product_of' => $parent_id,
						'_line_tax_data'      => array(
							'total'    => array(),
							'subtotal' => array(),
						),
					);
				}

				foreach ( $args as $meta_key => $meta_value ) {
					if ( '_variation_data' === $meta_key && is_array( $meta_value ) ) {
						foreach ( $meta_value as $key => $value ) {
							wc_update_order_item_meta( $item_id, str_replace( 'attribute_', '', $key ), $value );
						}
					} else {
						wc_update_order_item_meta( $item_id, $meta_key, $meta_value );
					}
				}

				wc_update_order_item_meta( $item_id, '_cp_priced_individually', $priced_individually );

				if ( 'no' === $priced_individually ) {
					$item->set_tax_class( $_product->get_tax_class() );
					$item->set_total( wc_format_decimal( 0 ) );
					$item->set_subtotal( wc_format_decimal( 0 ) );
					$item->set_quantity( $quantity );
				}

				$item->read_meta_data();
			}
		}

		/**
		 * When adding order item manually from order edit admin page
		 *
		 * @global WC_Chained_Products $wc_cp WooCommerce Chained Products object.
		 */
		public function on_add_order_item_manually() {
			check_ajax_referer( 'order-item', 'security' );

			global $wc_cp;
			if ( $this->is_wc_gte_35() ) {
				$item_to_add = ( ! empty( $_POST['data'] ) ) ? array_filter( wp_unslash( (array) $_POST['data'] ) ) : array(); // phpcs:ignore
			} else {
				$item_to_add = $this->is_wc_gte_30() ? ( ( ! empty( $_POST['item_to_add'] ) && is_array( $_POST['item_to_add'] ) ) ? wp_parse_id_list( $_POST['item_to_add'] ) : wp_parse_id_list( array( $_POST['item_to_add'] ) ) ) : (array) absint( $_POST['item_to_add'] ); // phpcs:ignore
			}

			$order_id = ! empty( $_POST['order_id'] ) ? absint( wc_clean( $_POST['order_id'] ) ) : 0; // phpcs:ignore
			$order    = wc_get_order( $order_id );

			$is_ignore_main_product_price = ( 'yes' === get_option( 'sa_chained_products_ignore_main_product_price', 'no' ) );

			if ( ! $order instanceof WC_Order || ! is_array( $item_to_add ) || empty( $item_to_add ) ) {
				return;
			}

			$chained_product_detail = $total_item_ids = $total_chained_product_details = $chained_product_ids = array();  // @codingStandardsIgnoreLine

			if ( $this->is_wc_gte_35() ) {

				foreach ( $item_to_add as $item_data ) {
					if ( ! empty( $item_data['id'] ) ) {
						$item_id       = absint( $item_data['id'] );
						$item_quantity = ! empty( $item_data['qty'] ) ? intval( $item_data['qty'] ) : 1;

						$chained_product_details = $this->get_all_chained_product_details( $item_id, $item_quantity );

						if ( ! empty( $chained_product_details ) ) {
							foreach ( $chained_product_details as $chained_item_id => $chained_item_data ) {

								// Do not add the chained products if the main product is duplicating.
								if ( $is_ignore_main_product_price && intval( $item_id ) === intval( $chained_item_id ) ) {
									continue;
								}

								$item_to_add[] = array(
									'id'  => intval( $chained_item_id ),
									'qty' => ! empty( $chained_item_data['unit'] ) ? intval( $chained_item_data['unit'] ) : 1,
								);

								$chained_product_details[ $chained_item_id ]['chained_item_of'] = $item_id;

							}

							$total_chained_product_details[ $item_id ] = $chained_product_details;
						}
					}
				}

				if ( ! empty( $item_to_add ) ) {
					$_POST['chained_product_details'] = $total_chained_product_details;
					$_POST['data']                    = $item_to_add;
					$_POST['order_id']                = $order_id;
				}
			} else {

				foreach ( $item_to_add as $item ) {
					$chained_product_detail = is_callable( array( $wc_cp, 'get_chained_product_data_by_product_id' ) ) ? $wc_cp->get_chained_product_data_by_product_id( $item ) : array();

					if ( ! empty( $chained_product_detail ) ) {
						$temp_chained_product_ids = is_array( $chained_product_detail ) ? array_keys( $chained_product_detail ) : null;
						$chained_product_ids     += $temp_chained_product_ids;

						$total_chained_product_details[ $item ] = $chained_product_detail;

						$chained_product_ids = is_array( $chained_product_detail ) ? array_keys( $chained_product_detail ) : null;

						array_unshift( $chained_product_ids, $item );
						$total_item_ids = array_merge( $total_item_ids, $chained_product_ids );
					}
				}

				if ( ! empty( $total_item_ids ) ) {
					$_POST['chained_product_details'] = $total_chained_product_details;
					$_POST['item_to_add']             = $total_item_ids;
					$_POST['order_id']                = $order_id;
				}
			}
			WC_AJAX::add_order_item();
		}

		/**
		 * Function to remove chained items manually
		 */
		public function remove_chained_order_items_manually() {

			check_ajax_referer( 'remove-chained-order-items-manually', 'security' );

			$order_item_id = ( ! empty( $_POST['order_item_id'] ) ) ? absint( $_POST['order_item_id'] ) : 0; // WPCS: input var ok.
			$order_id      = ( ! empty( $_POST['order_id'] ) ) ? absint( $_POST['order_id'] ) : 0; // WPCS: input var ok.

			if ( empty( $order_item_id ) || empty( $order_id ) ) {
				die( wp_json_encode( array() ) );
			}

			global $wpdb;

			$item_product_id   = wc_get_order_item_meta( $order_item_id, '_product_id' );
			$item_variation_id = wc_get_order_item_meta( $order_item_id, '_variation_id' );

			$item_to_remove = ( ! empty( $item_variation_id ) ) ? $item_variation_id : $item_product_id;

			$chained_product_detail = $this->get_all_chained_product_details( $item_to_remove );
			$chained_product_ids    = is_array( $chained_product_detail ) ? array_keys( $chained_product_detail ) : null;

			if ( empty( $chained_product_ids ) ) {
				die( wp_json_encode( array() ) );
			}

			$order_item_ids = $wpdb->get_col( // phpcs:ignore
				$wpdb->prepare(
					"SELECT DISTINCT oi.order_item_id
										FROM wp_woocommerce_order_items AS oi
										JOIN wp_woocommerce_order_itemmeta AS oim
											ON ( oi.order_item_id = oim.order_item_id AND oim.meta_key = '_chained_product_of' )
										WHERE oim.meta_value = %d
											AND oi.order_id = %d",
					$item_to_remove,
					$order_id
				)
			); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching

			if ( empty( $order_item_ids ) ) {
				die( wp_json_encode( array() ) );
			}

			foreach ( $order_item_ids as $order_item_id ) {
				wc_delete_order_item( $order_item_id );
			}

			echo wp_json_encode( array( 'order_item_ids' => $order_item_ids ) );
			die();
		}

		/**
		 * Hide Chained Products order meta from order dashboard
		 *
		 * @param array $itemmeta Order item meta.
		 * @return array $itemmeta
		 */
		public function woocommerce_hide_chained_products_order_itemmeta( $itemmeta ) {

			$itemmeta[] = '_chained_product_of';
			$itemmeta[] = '_cp_priced_individually';
			return $itemmeta;
		}

		/**
		 * Function to add chained products to a new subscription
		 *
		 * @param WC_Subscription $subscription Subscription object.
		 * @param WC_Order        $order Order object.
		 * @param WC_Cart         $recurring_cart Recurring cart object.
		 */
		public function add_chained_products_to_subscription( $subscription, $order, $recurring_cart ) {
			foreach ( $recurring_cart->cart_contents as $cart_item_key => $cart_item ) {
				$parent_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];

				$this->add_chained_item_to_subscription( $subscription, $parent_id );
			}
		}

		/**
		 * Function to add chained products to a subscription
		 *
		 * @param WC_Subscription $subscription Subscription object.
		 * @param int             $parent_id Parent product ID.
		 */
		public function add_chained_item_to_subscription( $subscription, $parent_id ) {
			if ( $subscription instanceof WC_Subscription && ! empty( $parent_id ) ) {
				$chained_product_details = $this->get_all_chained_product_details( $parent_id );
				$chained_product_ids     = is_array( $chained_product_details ) ? array_keys( $chained_product_details ) : null;

				if ( ! empty( $chained_product_ids ) ) {
					foreach ( $chained_product_ids as $chained_product_id ) {
						$product  = wc_get_product( $chained_product_id );
						$quantity = $chained_product_details[ $chained_product_id ]['unit'];

						if ( $product instanceof WC_Product_Variation ) {
							$varation_data = $product->get_variation_attributes();

							if ( ! empty( $varation_data ) ) {
								$args['variation'] = $varation_data;
							}
						}

						$args = array(
							'totals'             => array(
								'subtotal'     => wc_format_decimal( 0 ),
								'total'        => wc_format_decimal( 0 ),
								'subtotal_tax' => '',
								'tax'          => '',
							),
							'chained_product_of' => $parent_id,
						);

						$item_id = $subscription->add_product( $product, $quantity, $args );

						if ( $item_id ) {
							wc_add_order_item_meta( $item_id, '_chained_product_of', $parent_id );
						}
					}
				}
			}
		}

		/**
		 * Function to add/remove chained products to a switched subscription
		 *
		 * @param WC_Order        $order Order object.
		 * @param WC_Subscription $subscription Subscription object.
		 * @param int             $add_line_item Line item to add after switching subscription.
		 * @param int             $remove_line_item Line item to remove after switching subscription.
		 */
		public function update_chained_products_for_switched_subscription( $order, $subscription, $add_line_item, $remove_line_item ) {

			$cart_contents = WC()->cart->cart_contents;

			// Add chained products for a switched subscription.
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( isset( $cart_item['subscription_switch'] ) ) {
					$parent_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];

					$this->add_chained_item_to_subscription( $subscription, $parent_id );
				}
			}

			// Remove chained products of previous subscription.
			$subscription_items = $subscription->get_items();

			foreach ( $subscription_items as $item_id => $item ) {
				if ( isset( $item['chained_product_of'] ) ) {
					$product_id = $subscription_items[ $remove_line_item ]['variation_id'] ? $subscription_items[ $remove_line_item ]['variation_id'] : $subscription_items[ $remove_line_item ]['product_id'];

					if ( $product_id === (int) $item['chained_product_of'] ) {
						wc_delete_order_item( $item_id );
					}
				}
			}
		}

		/**
		 * Function to add single chained item in order
		 *
		 * @param int      $item_to_add Item to add.
		 * @param WC_Order $order Order object.
		 * @param int      $chained_product_of Chained product of.
		 * @param array    $chained_product_detail Chained product details.
		 * @param int      $qty Quantity to add.
		 */
		public function add_chained_item_in_order( $item_to_add = 0, $order = null, $chained_product_of = 0, $chained_product_detail = null, $qty = 1 ) {
			if ( empty( $item_to_add ) || empty( $order ) || ! $order instanceof WC_Order || empty( $chained_product_of ) || empty( $chained_product_detail ) ) {
				return;
			}

			if ( ! is_numeric( $item_to_add ) ) {
				return false;
			}

			$item_to_add = intval( $item_to_add );

			$_product = wc_get_product( $item_to_add );

			if ( ! $_product instanceof WC_Product ) {
				return false;
			}

			$product_id   = is_callable( array( $_product, 'get_id' ) ) ? intval( $_product->get_id() ) : 0;
			$variation_id = 0;

			if ( $_product instanceof WC_Product_Variation ) {
				$variation_id = $product_id;
				$product_id   = is_callable( array( $product, 'get_parent_id' ) ) ? intval( $_product->get_parent_id() ) : 0;
			}

			$order_id = is_callable( array( $order, 'get_id' ) ) ? intval( $order->get_id() ) : 0;

			$cp_unit = ( ! empty( $chained_product_detail[ $item_to_add ]['unit'] ) ) ? intval( $chained_product_detail[ $item_to_add ]['unit'] ) : 1;
			// Set values.
			$item = array();

			$item['product_id']        = $product_id;
			$item['variation_id']      = ! empty( $variation_id ) ? intval( $variation_id ) : 0;
			$item['variation_data']    = ! empty( $item['variation_id'] ) && is_callable( array( $_product, 'get_variation_attributes' ) ) ? $_product->get_variation_attributes() : array();
			$item['name']              = is_callable( array( $_product, 'get_title' ) ) ? $_product->get_title() : '';
			$item['tax_class']         = is_callable( array( $_product, 'get_tax_class' ) ) ? $_product->get_tax_class() : '';
			$item['qty']               = intval( $qty ) * $cp_unit;
			$item['line_subtotal']     = wc_format_decimal( 0 );
			$item['line_subtotal_tax'] = '';
			$item['line_total']        = wc_format_decimal( 0 );
			$item['line_tax']          = '';

			// Add line item.
			$item_id = wc_add_order_item(
				$order_id,
				array(
					'order_item_name' => $item['name'],
					'order_item_type' => 'line_item',
				)
			);

			// Add line item meta.
			if ( $item_id ) {
				wc_add_order_item_meta( $item_id, '_qty', $item['qty'] );
				wc_add_order_item_meta( $item_id, '_tax_class', $item['tax_class'] );
				wc_add_order_item_meta( $item_id, '_product_id', $item['product_id'] );
				wc_add_order_item_meta( $item_id, '_variation_id', $item['variation_id'] );
				wc_add_order_item_meta( $item_id, '_line_subtotal', $item['line_subtotal'] );
				wc_add_order_item_meta( $item_id, '_line_subtotal_tax', $item['line_subtotal_tax'] );
				wc_add_order_item_meta( $item_id, '_line_total', $item['line_total'] );
				wc_add_order_item_meta( $item_id, '_line_tax', $item['line_tax'] );

				// Since 2.2.
				wc_add_order_item_meta(
					$item_id,
					'_line_tax_data',
					array(
						'total'    => array(),
						'subtotal' => array(),
					)
				);

				// Store variation data in meta.
				if ( ! empty( $item['variation_data'] ) && is_array( $item['variation_data'] ) ) {
					foreach ( $item['variation_data'] as $key => $value ) {
						wc_add_order_item_meta( $item_id, str_replace( 'attribute_', '', $key ), $value );
					}
				}

				wc_add_order_item_meta( $item_id, '_chained_product_of', $chained_product_of );
			}

			return $item_id;
		}

		/**
		 * Function to grant download permission for a chained item
		 *
		 * @param int      $chained_item_id Chained item.
		 * @param WC_Order $order Order object.
		 */
		public function grant_download_permission_for_chained_item( $chained_item_id = 0, $order = null ) {
			if ( empty( $chained_item_id ) || empty( $order ) || ! $order instanceof WC_Order ) {
				return;
			}

			$_product     = wc_get_product( $chained_item_id );
			$order_status = $this->is_wc_gte_30() ? $order->get_status() : $order->status;

			if ( 'completed' === $order_status || 'processing' === $order_status ) {

				$files = $this->is_wc_gte_30() ? $_product->get_downloads() : $_product->get_files();

				if ( $files ) {
					foreach ( $files as $download_id => $file ) {
						wc_downloadable_file_permission( $download_id, $chained_item_id, $order );
					}
				}
			}
		}

		/**
		 * Add chained products in given order
		 *
		 * @param int      $item_to_add Chained parent.
		 * @param WC_Order $order Order object.
		 */
		public function add_chained_items_of_product_in_order( $item_to_add = 0, $order = null ) {
			if ( empty( $item_to_add ) || empty( $order ) ) {
				return;
			}
			$chained_product_detail = $this->get_all_chained_product_details( $item_to_add );
			$chained_product_ids    = is_array( $chained_product_detail ) ? array_keys( $chained_product_detail ) : null;

			if ( null !== $chained_product_ids ) {

				foreach ( $chained_product_ids as $chained_product_id ) {

					$item_id = $this->add_chained_item_in_order( $chained_product_id, $order, $item_to_add, $chained_product_detail );

				}//end foreach
			}
		}

		/**
		 * Add chained products in given order
		 *
		 * @param int     $order_id Order ID.
		 * @param WP_Post $post    Post object.
		 * @param bool    $update Whether this is an existing post being updated or not.
		 */
		public function add_chained_products_in_given_order( $order_id = 0, $post = null, $update = false ) {
			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}

			if ( in_array( 'woocommerce-give-products/woocommerce-give-products.php', $active_plugins, true ) || array_key_exists( 'woocommerce-give-products/woocommerce-give-products.php', $active_plugins ) ) {
				if ( empty( $_GET['page'] ) || 'give_products' !== $_GET['page'] ) { // phpcs:ignore
					return;
				}
				if ( empty( $_GET['give_products_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['give_products_nonce'], 'give_products' ) ) ) { // phpcs:ignore
					return;
				}
				if ( empty( $order_id ) ) {
					return;
				}
				if ( empty( $post ) ) {
					return;
				}
				if ( ! $update ) {
					return;
				}

				$order = wc_get_order( $order_id );
				if ( ! $order instanceof WC_Order ) {
					return;
				}
				$order_items = $order->get_items();

				if ( empty( $order_items ) ) {
					return;
				}
				foreach ( $order_items as $order_item_id => $order_item ) {

					$product_id = ( ! empty( $order_item['variation_id'] ) ) ? $order_item['variation_id'] : $order_item['product_id'];

					$this->add_chained_items_of_product_in_order( $product_id, $order );
				}
			}
		}

		/**
		 * Add Chained Products order meta in new order
		 *
		 * @param int   $item_id Order item ID.
		 * @param array $product_values Product data.
		 */
		public function add_chained_products_order_item_meta( $item_id = 0, $product_values = array() ) {
			$cart_object = WC()->cart;

			if ( empty( $cart_object ) || ! $cart_object instanceof WC_Cart ) {
				return;
			}

			$cart = is_callable( array( $cart_object, 'get_cart' ) ) ? $cart_object->get_cart() : array();

			if ( empty( $cart ) || ! is_array( $cart ) ) {
				return;
			}

			foreach ( $cart as $values ) {

				if ( $product_values === $values && isset( $values['chained_item_of'] ) ) {

					if ( empty( $cart[ $values['chained_item_of'] ]['variation_id'] ) ) {
						$product_id = $cart[ $values['chained_item_of'] ]['product_id'];
					} else {
						$product_id = $cart[ $values['chained_item_of'] ]['variation_id'];
					}

					wc_add_order_item_meta( $item_id, '_chained_product_of', $product_id );

					break;

				}
			}
		}

		/**
		 * Add Chained Products order meta in new order for WooCommerce > 3.0
		 *
		 * @param int                   $item_id Order item ID.
		 * @param WC_Order_Item_Product $item Order item object.
		 */
		public function add_chained_products_order_item_meta_3_0( $item_id = 0, $item = null ) {

			$cart = WC()->cart;

			if ( empty( $cart ) || ! $cart instanceof WC_Cart ) {
				return;
			}

			$cart_contents = is_callable( array( $cart, 'get_cart' ) ) ? $cart->get_cart() : array();

			if ( empty( $cart_contents ) || ! is_array( $cart_contents ) ) {
				return;
			}

			foreach ( $cart_contents as $values ) {
				if ( ( isset( $item->chained_item_of ) ) && isset( $values['chained_item_of'] ) ) {
					if ( empty( $cart_contents[ $values['chained_item_of'] ]['variation_id'] ) ) {
						$product_id = $cart_contents[ $values['chained_item_of'] ]['product_id'];
					} else {
						$product_id = $cart_contents[ $values['chained_item_of'] ]['variation_id'];
					}

					wc_add_order_item_meta( $item_id, '_chained_product_of', $product_id );

					break;
				}
			}

			if ( ( isset( $item->chained_item_of ) ) && isset( $item->cp_priced_individually ) ) {
				wc_add_order_item_meta( $item_id, '_cp_priced_individually', $item->cp_priced_individually );
			}
		}

		/**
		 * Function to add 'chained_item_of' property in order item. The same is verified in 'add_chained_products_order_item_meta_3_0' function before adding order item meta.
		 *
		 * @param WC_Order_Item_Product $item Order item object.
		 * @param string                $cart_item_key Cart item key.
		 * @param array                 $values Cart item data.
		 */
		public function set_chained_product_property_in_order_item( $item = null, $cart_item_key = '', $values = array() ) {
			if ( ! empty( $values['chained_item_of'] ) ) {
				$item->chained_item_of        = $values['chained_item_of'];
				$item->cp_priced_individually = ( ! empty( $values['priced_individually'] ) ) ? $values['priced_individually'] : 'no';
			}
		}

		/**
		 * Enqueue CSS style in admin page.
		 */
		public function enqueue_chained_products_js_css() {

			$plugin_data = is_callable( array( 'WC_Chained_Products', 'get_plugin_data' ) ) ? WC_Chained_Products::get_plugin_data() : array();

			wp_register_style( 'woocommerce_chained_products_css', plugins_url( 'woocommerce-chained-products/assets/css/chained-products-admin.css' ), array(), ! empty( $plugin_data['Version'] ) ? $plugin_data['Version'] : WC()->version );
			wp_enqueue_style( 'woocommerce_chained_products_css' );

			if ( wp_script_is( 'select2' ) ) {
				wp_localize_script(
					'select2',
					'cp_select_params',
					array(
						'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce-chained-products' ),
						'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce-chained-products' ),
						'ajax_url'                  => admin_url( 'admin-ajax.php' ),
						'search_products_nonce'     => wp_create_nonce( 'search-products' ),
						'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
					)
				);
			}
		}

		/**
		 * Enqueue JS in admin footer
		 */
		public function chained_products_footer_js() {

			global $wc_cp;

			if ( ! $this->is_wc_order_admin_page() ) {
				return;
			}

			$order_id = 0;

			if ( is_callable( array( $wc_cp, 'wc_cp_is_hpos_enabled' ) ) && $wc_cp::wc_cp_is_hpos_enabled() ) {
				global $theorder;
				$order_id = is_callable( array( $theorder, 'get_id' ) ) ? $theorder->get_id() : 0;
			} else {
				global $post;
				$order_id = ! empty( $post->ID ) ? $post->ID : 0;
			}

			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery('#order_line_items').on( 'click', 'a.delete-order-item', function(){
						var order_item_id = jQuery(this).parents('tr.item').attr( 'data-order_item_id' );
						jQuery.ajax({
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							dataType: 'json',
							type: 'post',
							data: {
								action: 'remove_chained_order_items_manually',
								order_item_id: order_item_id,
								order_id: '<?php echo absint( $order_id ); ?>',
								security: '<?php echo esc_html( wp_create_nonce( 'remove-chained-order-items-manually' ) ); ?>'
							},
							success: function( response ) {
								if ( response.order_item_ids != undefined && response.order_item_ids != '' ) {
									jQuery.each( response.order_item_ids, function( index, value ){
										jQuery('#order_line_items').find('tr[data-order_item_id=' + value + ']').remove();
									});
								}
							}
						});
					});
				});
			</script>
			<?php
		}

		/**
		 * Add fields for chained products on product edit admin page.
		 *
		 * @global woocommerce $woocommerce - Main instance of WooCommerce.
		 * @global WP_Post $post current post instance.
		 * @global wpdb $wpdb WPDB main instance.
		 * @global WC_Chained_Products $wc_cp Main instance of Chained Products.
		 *
		 * @param int                  $loop Variation loop count.
		 * @param array                $variation_data Variation data.
		 * @param WC_Product_Variation $variation Product Variation object.
		 */
		public function on_product_write_panels( $loop = 0, $variation_data = array(), $variation = null ) {
			// @codingStandardsIgnoreStart
			global $woocommerce, $post, $wpdb, $wc_cp;

			$product 	= !empty( $post ) ? wc_get_product( $post->ID ) : wc_get_product( $variation->ID );
			$row_loop 	= 0;
			$chained_parent_id = empty( $variation ) ? $post->ID : $variation->ID;

			$is_subscription = false;

			$classes = array();
			$wc_product_types = array_keys( wc_get_product_types() );
			foreach( $wc_product_types as $type ){
				$classes[] = "show_if_" . $type;
				}

			if ( class_exists( 'WC_Subscriptions_Product' ) ) {
				$is_subscription = WC_Subscriptions_Product::is_subscription( $product );
				}

			if ( !empty( $variation ) ) {

				$class = 'woocommerce_options_panel';
				$style = 'style = "float: none; width: auto; padding: 1px;"';
				echo "</td></tr></tbody></table>";
				}

			if( $product instanceof WC_Product_Variable && $product->is_type('variable') ) {

				$class = 'woocommerce_options_panel';
				$style = 'style = "background: #f5f5f5; display: none; width: 100%; padding: 1px;"';
				echo "</td></tr></tbody></table>";

				}

			$exclude_query = "SELECT ID FROM {$wpdb->posts} WHERE post_parent = {$post->ID} AND post_type = 'product_variation'";
			$exclude_ids = $wpdb->get_col( $exclude_query );

			if ( ! empty( $exclude_ids ) && is_array( $exclude_ids ) ) {
				$total_ids_to_exclude = implode( ',', array_merge( array( $post->ID ), $exclude_ids ) );
				} else {
			$total_ids_to_exclude = $post->ID;
				}

		?>
			<div id="chained_products_setting_fields_<?php echo $chained_parent_id; ?>" class="options_group grouping <?php if( isset( $class ) ) echo $class; ?> chained_products_admin_settings" <?php if( isset( $style ) ) echo $style; ?>>
				<div id="chained_products_list_<?php echo $chained_parent_id; ?>">
				<?php
				$product_detail = is_callable( array( $wc_cp, 'chained_product_details' ) ) ? $wc_cp::chained_product_details( $chained_parent_id ) : array();
				if ( ! empty( $product_detail ) ) {
					$total_chained_details = $this->get_all_chained_product_details( $chained_parent_id );
					foreach ( $total_chained_details as $product_id => $product_data ) {
						?>
							<p class="form-field <?php if( ! isset( $product_detail[$product_id] ) ) echo 'nested_chained_products_'.$chained_parent_id; ?>" id="chained_products_row_<?php echo $chained_parent_id . '_' . $row_loop; ?>">
								<label for="chained_products_ids_<?php echo $chained_parent_id . '_' . $row_loop; ?>"><?php if( $row_loop == 0 ) _e( 'Chained Products', 'woocommerce-chained-products' ); ?>
									<span style="display: inline;" class="description chained_product_description"> </span>
								</label>

							<?php
								$product      = wc_get_product( $product_id );
								$product_name = ( ! empty( $product ) && $product instanceof WC_Product && is_callable( array( $product, 'get_formatted_name' ) ) ) ? wp_kses_post( $product->get_formatted_name() ) : $product_id;
							?>
							<?php

							if ( $this->is_wc_gte_30() ) { ?>
									<select class="wc-product-search" style="width: 50%;" id="chained_products_ids_<?php echo $chained_parent_id . '_' . $row_loop; ?>" name="<?php if( ! isset( $product_detail[$product_id] ) ) echo 'nested_'; ?>chained_products_ids[<?php echo $chained_parent_id; ?>][<?php echo $row_loop; ?>]" data-placeholder="<?php _e( 'Search for a product...', 'woocommerce-chained-products' ); ?>"
										data-action="woocommerce_json_search_products_and_variations" data-allow_clear="true" data-exclude="<?php echo $chained_parent_id; ?>" <?php if( ! isset( $product_detail[$product_id] ) ) echo 'disabled';?> data-limit="00">
										<?php
										echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . $product_name . '</option>';
										?>
									</select> <?php

									// Support for select2 verion 4 : The 'disabled' attribute didn't supported posting data so the nested products weren't getting added while updating existing order with the chained products
									if ( ! isset( $product_detail[$product_id] ) ) { ?>
										<input type="hidden" value="<?php if( ! isset( $product_detail[$product_id] ) ) echo $product_id; ?>" name="<?php if( ! isset( $product_detail[$product_id] ) ) echo 'nested_'; ?>chained_products_ids[<?php echo $chained_parent_id; ?>][<?php echo $row_loop; ?>]"> <?php
									}

								} else { ?>
									<input type="hidden" class="wc-product-search" style="width: 50%;" id="chained_products_ids_<?php echo $chained_parent_id . '_' . $row_loop; ?>" name="<?php if( ! isset( $product_detail[$product_id] ) ) echo 'nested_'; ?>chained_products_ids[<?php echo $chained_parent_id; ?>][<?php echo $row_loop; ?>]" data-placeholder="<?php _e( 'Search for a product...', 'woocommerce-chained-products' ); ?>"
										data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo $total_ids_to_exclude; ?>" data-multiple="true"
										data-selected="<?php
										$json_ids    = array();
										$json_ids[ $product_id ] = $product_name;

										echo esc_attr( json_encode( $json_ids ) );
										?>"
										value="<?php echo $product_id; ?>" <?php if( ! isset( $product_detail[$product_id] ) ) echo 'readonly';?> /> <?php
								} ?>
								<input type="number" class="chained_products_quantity short" name="chained_products_quantity[<?php echo $chained_parent_id; ?>][<?php echo $row_loop; ?>]" value="<?php echo ( ! empty( $product_data['unit'] ) ) ? $product_data['unit'] : '1'; ?>" placeholder="<?php _e( 'Qty', 'woocommerce-chained-products' ); ?>" min="1" <?php  if ( ! isset( $product_detail[$product_id] ) ) echo 'readonly'; ?>/>

								<?php
								// Show priced individually checkbox only for WC > 3.0
								if ( $this->is_wc_gte_30() && ! $is_subscription ) { ?>
										<span class="cp_priced_individually"><input type="checkbox" name="chained_products_priced_individually[<?php echo $chained_parent_id; ?>][<?php echo $row_loop; ?>]" value="yes" <?php if ( ! empty( $product_data['priced_individually'] ) && 'yes' ===  $product_data['priced_individually'] ) echo 'checked="checked"'; ?><?php  if ( ! isset( $product_detail[$product_id] ) ) echo 'disabled="disabled"'; ?>><span><?php echo esc_html__( 'Priced Individually', 'woocommerce-chained-products'  ); ?></span></span><?php
									}
								?>

								<?php
								if( isset( $product_detail[$product_id] ) ) {

									if( $row_loop == 0 ) {
								?>
										<span class="add_remove_chained_products_row dashicons-plus" id="add_chained_products_row_<?php echo $chained_parent_id; ?>" title="<?php _e( 'Add Product', 'woocommerce-chained-products' ); ?>"></span>
								<?php } else { ?>
										<span class="add_remove_chained_products_row dashicons-no remove_chained_products_row_<?php echo $chained_parent_id; ?>" id="<?php echo $row_loop; ?>" title="<?php _e( 'Remove Product', 'woocommerce-chained-products' ); ?>"></span>
								<?php }
								}
								?>
							</p>
							<?php
							$row_loop++;
						}

					} else { ?>
						<p class="form-field" id="chained_products_row_<?php echo $chained_parent_id . '_' . $row_loop; ?>">
							<label for="chained_products_ids_<?php echo $chained_parent_id . '_' . $row_loop; ?>"><?php if( $row_loop == 0 ) _e( 'Chained Products', 'woocommerce-chained-products' ); ?>
								<span style="display: inline;" class="description chained_product_description"> </span>
							</label> <?php

							if ( $this->is_wc_gte_30() ) { ?>
								<select class="wc-product-search" style="width: 50%;" id="chained_products_ids_<?php echo $chained_parent_id . '_' . $row_loop; ?>" name="chained_products_ids[<?php echo $chained_parent_id; ?>][<?php echo $row_loop; ?>]" data-placeholder="<?php _e( 'Search for a product...', 'woocommerce-chained-products' ); ?>"
									data-action="woocommerce_json_search_products_and_variations" data-allow_clear="true" data-exclude="<?php echo $chained_parent_id; ?>" data-limit="00"/></select> <?php
							}
							else { ?>
								<input type="hidden" class="wc-product-search" style="width: 50%;" id="chained_products_ids_<?php echo $chained_parent_id . '_' . $row_loop; ?>" name="chained_products_ids[<?php echo $chained_parent_id; ?>][<?php echo $row_loop; ?>]" data-placeholder="<?php _e( 'Search for a product...', 'woocommerce-chained-products' ); ?>"
									data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo $total_ids_to_exclude; ?>" data-multiple="true"/> <?php
							} ?>

							<input type="number" class="chained_products_quantity short" name="chained_products_quantity[<?php echo $chained_parent_id; ?>][<?php echo $row_loop; ?>]" value="1" placeholder="<?php _e( 'Qty', 'woocommerce-chained-products' ); ?>" min="1">

							<?php
							// Show priced individually checkbox only for WC > 3.0
							if ( $this->is_wc_gte_30() && ! $is_subscription ) { ?>
									<span class="cp_priced_individually"><input type="checkbox" name="chained_products_priced_individually[<?php echo $chained_parent_id; ?>][<?php echo $row_loop; ?>]" value="yes"><span><?php echo  esc_html__( 'Priced Individually', 'woocommerce-chained-products'  ); ?></span></span><?php
								}
							?>

							<span class="add_remove_chained_products_row  dashicons-plus" id="add_chained_products_row_<?php echo $chained_parent_id; ?>" title="<?php _e( 'Add Product', 'woocommerce-chained-products' ); ?>"></span>
						</p>
						<?php
						$row_loop++;
						}
				?>
				</div>
				<?php

				if ( get_option( 'woocommerce_manage_stock' ) == 'yes' ) {
					$chained_parent_product = wc_get_product( $chained_parent_id );
					?>
					<p class="form-field chained_products_manage_stock_field">
						<label for="chained_products_manage_stock_<?php echo $chained_parent_id; ?>"><?php _e( 'Manage stock?', 'woocommerce-chained-products' ); ?></label>
						<input type="checkbox" class="checkbox" name="chained_products_manage_stock[<?php echo $chained_parent_id; ?>]" id="chained_products_manage_stock_<?php echo $chained_parent_id; ?>" <?php if ( 'yes' === $chained_parent_product->get_meta( '_chained_product_manage_stock', true ) ) echo 'checked="checked"'; ?>>
						<span style="display: inline;" class="description"><?php _e( 'Enable stock management for chained products', 'woocommerce-chained-products' ); ?></span>
						<?php
							echo wc_help_tip( __( 'Enable this option to manage stock for above listed chained products, uncheck otherwise. When enabled, if any of the above chained product is out of stock, then this product will not be allowed to added to cart.', 'woocommerce-chained-products' ) );
						?>
					</p> <?php
					} ?>

				<p class="form-field chained_product_update_order">
					<label for="chained_product_update_order_<?php echo $chained_parent_id; ?>"><?php _e( 'Update existing orders?', 'woocommerce-chained-products' ); ?></label>
					<input type="checkbox" class="checkbox" name="chained_product_update_order[<?php echo $chained_parent_id;?>]" id="chained_product_update_order_<?php echo $chained_parent_id; ?>">
					<span style="display: inline;" class="description"><?php _e( 'Update existing orders with above chained products', 'woocommerce-chained-products' ); ?></span>
					<?php
					echo wc_help_tip( __( 'Check to update existing orders containing this main product. Existing orders will be affected.', 'woocommerce-chained-products' ) );
					?>
					<br>
					<span style=""><?php echo '<strong>' . esc_html__( 'Note: ', 'woocommerce-chained-products' ) . '</strong>' . esc_html__( 'Upating existing orders with chained products will not update the order total.', 'woocommerce-chained-products' ); ?></span>
				</p>
				<div id="message" class="updated below-h2 chained_products_shortcode">
					<p><?php _e( 'To show Chained Products on product page,', 'woocommerce-chained-products' ); ?>
						<a class="insert_shortcode"><?php _e( 'click here to insert shortcode in the product description', 'woocommerce-chained-products' ); ?></a>
					</p>
					<p>
					<?php _e( 'Know how to configure shortcode from', 'woocommerce-chained-products' ); ?>
						<a class="wc_cp_shortcode_info" target="_blank" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'cp-shortcode' ), 'admin.php' ) ) ); ?>"><?php _e( 'here', 'woocommerce-chained-products' ); ?></a>
					</p>
				</div>
			</div>
				<?php

				// Javascript
				ob_start();

				?>
			jQuery( function() {

				init_select2();

				jQuery('select#product-type').on( 'change', function() {
					productType = jQuery(this).find('option:selected').val();

					if ( 'external' === productType || 'variable' === productType ) {
						jQuery('div#chained_products_setting_fields_<?php echo $chained_parent_id; ?>').hide();
					} else {
						var chained_post_id = jQuery('#post_ID').val();

						jQuery('div#chained_products_setting_fields_'+chained_post_id).show();
						jQuery('span.chained_product_description').text('');
					}

					if ( 'subscription' === productType || 'variable-subscription' === productType ) {
						jQuery( 'span.cp_priced_individually').hide();
					}

					init_select2();

				});

				jQuery( '#woocommerce-product-data' ).on( 'woocommerce_variations_added woocommerce_variations_loaded', function(){

					setTimeout( function() {

						init_select2();

						jQuery('[id^="add_chained_products_row"]').each(function() {
							var id_prefix = 'add_chained_products_row_',
								chained_id = jQuery(this).attr('id').substr(id_prefix.length);
								chained_products_add_row(chained_id);
						});

						// Tooltips
						var tiptip_args = {
							'attribute' : 'data-tip',
							'fadeIn' : 50,
							'fadeOut' : 50,
							'delay' : 200
						};
						jQuery(".tips, .help_tip").tipTip( tiptip_args );

					}, 100);
				});

				jQuery('.wc-metaboxes-wrapper').on('click', '.wc-metabox h3', function(event){

					if (jQuery(event.target).filter(':input, option').length)
						return;
					if( jQuery(this).next('.wc-metabox-content').css('display') == 'none' ) {
						jQuery(this).parent().find('.chained_products_admin_settings').hide();
					} else {
						jQuery(this).parent().find('.chained_products_admin_settings').show();
					}
					init_select2();

				})
				.on('click', '.expand_all', function(event){
					jQuery(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox').find('.chained_products_admin_settings').show();
					init_select2();
				})
				.on('click', '.close_all', function(event){
					jQuery(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox').find('.chained_products_admin_settings').hide();
					init_select2();

				});

				var row_id = '<?php echo $row_loop; ?>';

				var wc_table_background = jQuery('#variable_product_options .woocommerce_variation table').css('background');
				jQuery('#chained_products_setting_fields_<?php echo $chained_parent_id; ?>').css( 'background', wc_table_background );

				for (var i = 0; i < row_id; i++) {
					set_unique_product_field( 'chained_products_ids_<?php echo $chained_parent_id; ?>_'+i );
				}

				function set_unique_product_field( changed_id ) {

					jQuery('div#'+changed_id+'_chosen ul.chosen-choices li.search-field').css( 'display' , 'list-item' );
					jQuery('div#'+changed_id+'_chosen div.chosen-drop').css( 'display' , 'initial' );
					setTimeout(function() {

						if( jQuery('div#'+changed_id+'_chosen ul.chosen-choices li').length >= 2 ) {

							jQuery('div#'+changed_id+'_chosen ul.chosen-choices li.search-field').css( 'display' , 'none' );
							jQuery('div#'+changed_id+'_chosen div.chosen-drop').css( 'display' , 'none' );

						}

					}, 200 );
				}

				function getEnhancedSelectFormatString() { <?php
				if( $this->is_wc_gte_30() ) { ?>
						var formatString = {
							noResults: function() {
								return wc_enhanced_select_params.i18n_no_matches;
							},
							errorLoading: function() {
								return wc_enhanced_select_params.i18n_searching;
							},
							inputTooShort: function( args ) {
								var remainingChars = args.minimum - args.input.length;

								if ( 1 === remainingChars ) {
									return wc_enhanced_select_params.i18n_input_too_short_1;
								}

								return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
							},
							inputTooLong: function( args ) {
								var overChars = args.input.length - args.maximum;

								if ( 1 === overChars ) {
									return wc_enhanced_select_params.i18n_input_too_long_1;
								}

								return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
							},
							maximumSelected: function( args ) {
								if ( args.maximum === 1 ) {
									return wc_enhanced_select_params.i18n_selection_too_long_1;
								}

								return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
							},
							loadingMore: function() {
								return wc_enhanced_select_params.i18n_load_more;
							},
							searching: function() {
								return wc_enhanced_select_params.i18n_searching;
							}
						};

						var language = { 'language' : formatString };

						return language; <?php
						} else { ?>
						var formatString = {};

						formatString = {
							formatMatches: function( matches ) {
								if ( 1 === matches ) {
								return cp_select_params.i18n_matches_1;
								}

								return cp_select_params.i18n_matches_n.replace( '%qty%', matches );
							},
							formatNoMatches: function() {
								return cp_select_params.i18n_no_matches;
							},
							formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
								return cp_select_params.i18n_ajax_error;
							},
							formatInputTooShort: function( input, min ) {
								var number = min - input.length;

								if ( 1 === number ) {
								return cp_select_params.i18n_input_too_short_1
								}

								return cp_select_params.i18n_input_too_short_n.replace( '%qty%', number );
							},
							formatInputTooLong: function( input, max ) {
								var number = input.length - max;

								if ( 1 === number ) {
								return cp_select_params.i18n_input_too_long_1
								}

								return cp_select_params.i18n_input_too_long_n.replace( '%qty%', number );
							},
							formatSelectionTooBig: function( limit ) {
								if ( 1 === limit ) {
								return cp_select_params.i18n_selection_too_long_1;
								}

								return cp_select_params.i18n_selection_too_long_n.replace( '%qty%', number );
							},
							formatLoadMore: function( pageNumber ) {
								return cp_select_params.i18n_load_more;
							},
							formatSearching: function() {
								return cp_select_params.i18n_searching;
							}
						};

						return formatString; <?php
						} ?>
				}

				function init_select2() {

					// Ajax product search box
					jQuery( '[id^= "chained_products_ids"]' ).filter( ':not(.chained_enhanced)' ).each( function() { <?php
					if ( $this->is_wc_gte_30() ) { ?>
							var select2_args = {
								allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
								placeholder: jQuery( this ).data( 'placeholder' ),
								minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
								escapeMarkup: function( m ) {
									return m;
								},
								maximumSelectionSize : 1,
								ajax: {
									url:         wc_enhanced_select_params.ajax_url,
									dataType:    'json',
									quietMillis: 250,
									data: function( params, page ) {
										return {
											term:     params.term,
											action:   jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
											security: wc_enhanced_select_params.search_products_nonce,
											exclude:  jQuery( this ).data( 'exclude' ),
											limit: jQuery( this ).data( 'limit' )
										};
									},
									processResults: function( data, page ) {
										var terms = [];

										jQuery.ajax({
											url: wc_enhanced_select_params.ajax_url,
											type: 'POST',
											async: false,
											dataType: 'json',
											data: {
												action: 'exclude_parent_variable_product_from_search',
												result: data
											},
											success: function( response ) {
												if ( response ) {
													jQuery.each( response, function( id, text ) {
														terms.push( { id: id, text: text } );
													});
												}
											},
										});

										return { results: terms };
									},
									cache: true
								}
							}; <?php
							} else { ?>
							var select2_args = {
								allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
								placeholder: jQuery( this ).data( 'placeholder' ),
								minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
								escapeMarkup: function( m ) {
									return m;
								},
								maximumSelectionSize : 1,
								ajax: {
									url:         wc_enhanced_select_params.ajax_url,
									dataType:    'json',
									quietMillis: 250,
									data: function( term, page ) {
									return {
									term:     term,
									action:   jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
									security: wc_enhanced_select_params.search_products_nonce,
									exclude:  jQuery( this ).data( 'exclude' )
									};
									},
									results: function( data, page ) {
									var terms = [];
									if ( data ) {
									jQuery.each( data, function( id, text ) {
									terms.push( { id: id, text: text } );
									});
									}
									return { results: terms };
									},
									cache: true
								}
							};


							if ( jQuery( this ).data( 'multiple' ) === true ) {
								select2_args.multiple = true;
								select2_args.initSelection = function( element, callback ) {
									var data     = jQuery.parseJSON( element.attr( 'data-selected' ) );
									var selected = [];
									jQuery( element.val().split( "," ) ).each( function( i, val ) {
									selected.push( { id: val, text: data[ val ] } );
									});
									return callback( selected );
								};
								select2_args.formatSelection = function( data ) {
									return '<div class=\"selected-option\" data-id=\"' + data.id + '\">' + data.text + '</div>';
								};
							} else {
								select2_args.multiple = false;
								select2_args.initSelection = function( element, callback ) {
									var data = {id: element.val(), text: element.attr( 'data-selected' )};
									return callback( data );
								};
							} <?php
							} ?>

						select2_args = jQuery.extend( select2_args, getEnhancedSelectFormatString() );

						jQuery( this ).select2( select2_args ).addClass( 'enhanced' ).addClass( 'chained_enhanced' );
					});

					if (jQuery('div[id^=chained_products_list_] ul.select2-choices').length <= 1){
						jQuery('div[id^=chained_products_list_] li.select2-search-field input').css('width','100%');
					}

				}

				function chained_products_add_row(chained_id) {
					jQuery('#add_chained_products_row_'+chained_id).off('click').on( 'click', function() {
						jQuery('.nested_chained_products_'+chained_id).remove();
						var row_id = jQuery(' [id^= "chained_products_ids_'+chained_id+'"] ').length;
						var current_row_element = jQuery(this).parent();
						var new_row = " <p class='form-field' id='chained_products_row_"+chained_id+"_"+row_id+"'>\
											<label for='chained_products_ids_"+chained_id+"_"+row_id+"'>\
											<?php echo __( "Chained Products", 'woocommerce-chained-products' ); ?>\
												<span class='description chained_product_description'></span>\
											</label>\
										<?php if ( $this->is_wc_gte_30() ){?>\
												<select class='wc-product-search' style='width: 50%;' id='chained_products_ids_"+chained_id+"_"+row_id+"' name='chained_products_ids["+chained_id+"]["+row_id+"]' data-placeholder='<?php _e( 'Search for a product...', 'woocommerce-chained-products' ); ?>' \
												data-action='woocommerce_json_search_products_and_variations' data-exclude='"+chained_id+"' data-limit='00'/></select>\
											<?php } else { ?>\
												<input type='hidden' class='wc-product-search' style='width: 50%;' id='chained_products_ids_"+chained_id+"_"+row_id+"' name='chained_products_ids["+chained_id+"]["+row_id+"]' data-placeholder='<?php _e( 'Search for a product...', 'woocommerce-chained-products' ); ?>' \
												data-action='woocommerce_json_search_products_and_variations' data-exclude='<?php echo $total_ids_to_exclude; ?>' data-multiple='true'/>\
											<?php } ?>\
											<input type='number' class='chained_products_quantity short' name='chained_products_quantity["+chained_id+"]["+row_id+"]' value='1' placeholder='<?php _e( 'Qty', 'woocommerce-chained-products' ); ?>' min='1'>\
										<?php if ( $this->is_wc_gte_30() &&  ! $is_subscription ){?>\
												<span class='cp_priced_individually'>\
													<input type='checkbox' name='chained_products_priced_individually["+chained_id+"]["+row_id+"]' value='yes'/>\
													<span><?php echo esc_html__( 'Priced Individually', 'woocommerce-chained-products'  ); ?></span>\
												</span>\
											<?php } ?>\
											<span class='add_remove_chained_products_row dashicons-plus' id='add_chained_products_row_"+chained_id+"' title='<?php _e( "Add Product", 'woocommerce-chained-products' ); ?>'></span>\
										</p>\
										";

						jQuery('div#chained_products_list_'+chained_id).prepend(new_row);
						current_row_element.find('label').text('');
						current_row_element.find('span.add_remove_chained_products_row')
											.removeClass('dashicons-plus')
											.addClass('dashicons-no')
											.addClass('remove_chained_products_row_'+chained_id)
											.attr('title', '<?php echo __( 'Remove Product', 'woocommerce-chained-products' ); ?>')
											.removeAttr('id')
											.off('click')
											.on('click', function(){
												var id_prefix = 'chained_products_row_',
													ids = current_row_element.attr('id').substr(id_prefix.length).split("_"),
													prev_row_id = ids[1];

												jQuery( this ).closest( 'div' ).parent().parent().parent().parent().addClass( 'variation-needs-update' );
												jQuery( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
												jQuery( '#variable_product_options' ).trigger( 'woocommerce_variations_input_changed' );

												jQuery('p#chained_products_row_'+chained_id+'_'+prev_row_id).remove();
												jQuery('.nested_chained_products_'+chained_id).remove();
											});
						chained_products_add_row(chained_id);

						init_select2();

					});
				}

				chained_products_add_row(<?php echo $chained_parent_id; ?>);

				jQuery('.wc-metaboxes-wrapper, .woocommerce_options_panel').on('click', '[class^="add_remove_chained_products_row dashicons-no remove_chained_products_row_"]', function() {
					var id_prefix = 'chained_products_row_',
						ids = jQuery(this).parent().attr('id').substr(id_prefix.length).split("_"),
						chained_id = ids[0],
						remove_row = jQuery(this).attr('id');

					jQuery( this ).closest( 'div' ).parent().parent().parent().parent().addClass( 'variation-needs-update' );
					jQuery( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
					jQuery( '#variable_product_options' ).trigger( 'woocommerce_variations_input_changed' );

					jQuery('p#chained_products_row_'+chained_id+'_'+remove_row).remove();
					jQuery('.nested_chained_products_'+chained_id).remove();
					

				});

				function display_insert_shortcode_message() {

					setTimeout(function() {

						des_content = jQuery( 'textarea#content' ).val();

						if( des_content.indexOf( "[chained_products" ) == -1 ) { <?php
						if (  $this->is_wc_gte_30() ) { ?>
								if( jQuery('div[id^=chained_products_list_] span.select2-selection').length > 0  )
									jQuery('div.chained_products_shortcode').css( 'display', 'block' );
								else
									jQuery('div.chained_products_shortcode').css( 'display', 'none' ); <?php
								} else { ?>
								if( jQuery('div[id^=chained_products_list_] li.search-choice').length > 0 || jQuery('div[id^=chained_products_list_] li.select2-search-choice').length > 0 )
									jQuery('div.chained_products_shortcode').css( 'display', 'block' );
								else
									jQuery('div.chained_products_shortcode').css( 'display', 'none' ); <?php
								} ?>
						} else {
							jQuery('div.chained_products_shortcode').css( 'display', 'none' );
						}
					}, 700 );
				}

				jQuery('.wc-metaboxes-wrapper, .woocommerce_options_panel').on('click', 'a.insert_shortcode', function() {
					des_content = jQuery( 'textarea#content' ).val();

					if( des_content.indexOf( "[chained_products" ) == -1 ) {

						if((jQuery( 'textarea#content' ).css( 'display') == 'none' ) ) {
							jQuery( '#content-html' ).trigger( 'click' );
							jQuery( 'textarea#content' ).val( jQuery( 'textarea#content' ).val() + "[chained_products]" );
							jQuery( '#content-tmce' ).trigger( 'click' );
						} else {
							jQuery( 'textarea#content' ).val( jQuery( 'textarea#content' ).val() + "[chained_products]" );
						}
						window.scrollTo(0,0);
					}

				});

				setTimeout( function(){
					jQuery('[class*=nested_chained_products] .chosen-container-multi .chosen-choices .search-choice .search-choice-close').remove();

					jQuery('[class*=nested_chained_products] .chained_products_quantity').attr('readonly', 'readonly')
				}, 500 );

			});
				<?php

				wc_enqueue_js( ob_get_clean() );

				// @codingStandardsIgnoreEnd
		}

		/**
		 * Function to save chained products detail via both ajax & form submit
		 */
		public function cp_save_product_variations() {

			check_ajax_referer( 'save-variations', 'security' );

			$variable_product_ids = ! empty( $_POST['variable_post_id'] ) ? $_POST['variable_post_id'] : array(); // phpcs:ignore

			if ( ! empty( $variable_product_ids ) ) {
				$update_order_for_products = array();

				foreach ( $variable_product_ids as $variation_id ) {
					if ( ! empty( $_POST['chained_product_update_order'][ $variation_id ] ) && 'on' === $_POST['chained_product_update_order'][ $variation_id ] ) { // WPCS: input var ok.
						$update_order_for_products[] = $variation_id;
					}
					$this->update_chained_product_data( $variation_id );
				}

				if ( ! empty( $update_order_for_products ) ) {
					$this->update_chained_products_order( $update_order_for_products );
				}
			}
		}

		/**
		 * Save chained products details in product's meta
		 *
		 * @param int    $post_id Post ID being saved.
		 * @param object $post post object being saved.
		 */
		public function on_process_product_meta( $post_id, $post ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) { // WPCS: input var ok.
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) { // phpcs:ignore
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			if ( 'product' !== $post->post_type ) {
				return;
			}

			if ( ! empty( $_POST['product-type'] ) ) { // WPCS: input var ok.
				$this->update_chained_product_data( $post_id );
				if ( ! empty( $_POST['chained_product_update_order'][ $post_id ] ) && 'on' === $_POST['chained_product_update_order'][ $post_id ] ) { // WPCS: input var ok.
					$this->update_chained_products_order( $post_id );
				}
			}
		}

		/**
		 * Update previous orders with new chained products
		 *
		 * @global wpdb $wpdb
		 * @param int|array $chained_parent_id Chained parent ID.
		 */
		public function update_chained_products_order( $chained_parent_id ) {
			global $wpdb;

			$query = "SELECT order_items.order_id, order_itemmeta.meta_key, order_itemmeta.meta_value, order_items.order_item_id
						FROM {$wpdb->prefix}woocommerce_order_items AS order_items
							LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
								ON ( order_items.order_item_id = order_itemmeta.order_item_id )
						WHERE order_itemmeta.meta_key IN ( '_product_id', '_variation_id', '_qty', '_chained_product_of' )
							AND order_items.order_id IN ( SELECT oi.order_id
															FROM {$wpdb->prefix}woocommerce_order_items AS oi
																LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
																	ON ( oi.order_item_id = oim.order_item_id )
															WHERE oim.meta_key IN ( '_product_id', '_variation_id' )
																AND oim.meta_value ";

			if ( is_array( $chained_parent_id ) && count( $chained_parent_id ) > 1 ) {
				$query .= 'IN ( ' . implode( ',', $chained_parent_id ) . ' )';
			} else {
				if ( is_array( $chained_parent_id ) ) {
					$chained_parent_id = current( $chained_parent_id );
				}
				$query .= "= {$chained_parent_id}";
			}

			$query .= ')';

			// Fetch all orders having this chained product/s.
			$order_items = $wpdb->get_results( $query, 'ARRAY_A' ); // phpcs:ignore

			$order_with_product            = array();
			$order_with_product_details    = array();
			$order_with_chained_parent_qty = array();
			$revoke_download               = array();

			/*
			As discussed we will keep this comment for track in future as it was complicated to apply our required logic.
			Loop through query result to get order details in following format:

			array(
					order_id => array(
										item_id => array(
															meta_key => meta_value,
															...
														),
										...
									),
					...
				)
			*/
			if ( ! empty( $order_items ) ) {
				foreach ( $order_items as $item ) {
					if ( empty( $order_with_product_details[ $item['order_id'] ] ) ) {
						$order_with_product_details[ $item['order_id'] ] = array();
					}
					if ( empty( $order_with_product_details[ $item['order_id'] ][ $item['order_item_id'] ] ) ) {
						$order_with_product_details[ $item['order_id'] ][ $item['order_item_id'] ] = array();
					}
					$order_with_product_details[ $item['order_id'] ][ $item['order_item_id'] ][ $item['meta_key'] ] = $item['meta_value']; // phpcs:ignore
				}
			}
			if ( ! empty( $order_with_product_details ) ) {
				/*
				Loop through $order_with_product_details

				Perform following 2 things:
				1. Create array containing all orders with chained parent & its quantity
				2. Create array containing all orders with products in respective order
				*/
				foreach ( $order_with_product_details as $order_id => $items ) {
					if ( ! empty( $items ) ) {
						foreach ( $items as $item ) {
							$product_id = ( ! empty( $item['_variation_id'] ) ) ? $item['_variation_id'] : $item['_product_id'];

							/*
							As discussed we will keep this comment for track in future as it was complicated to apply our required logic.
							Collect chained parent with its qty in following format

							array(
									order_id => array(
														chained_parent => qty,
														...
													),
									...
								)

							*/
							if ( empty( $item['_chained_product_of'] ) ) {
								if ( empty( $order_with_chained_parent_qty[ $order_id ] ) ) {
									$order_with_chained_parent_qty[ $order_id ] = array();
								}
								$order_with_chained_parent_qty[ $order_id ][ $product_id ] = $item['_qty'];

								/*
								As discussed we will keep this comment for track in future as it was complicated to apply our required logic.
									Collect order with product in following format
									Array(
										order_id => array(
															chained_parent => array(
																						chained_item,
																						...
																					),
															...
														),
										...
									)
								*/
							} else {
								if ( empty( $order_with_product[ $order_id ] ) ) {
									$order_with_product[ $order_id ] = array();
								}
								if ( empty( $order_with_product[ $order_id ][ $item['_chained_product_of'] ] ) ) {
									$order_with_product[ $order_id ][ $item['_chained_product_of'] ] = array();
								}
								$order_with_product[ $order_id ][ $item['_chained_product_of'] ][] = $product_id;
							}
						}
					}
				}

				/*
				As discussed we will keep this comment for track in future as it was complicated to apply our required logic.
				Collect all nested chained products & merge with chained products
				array(
					chained_parent => array(
												chained_item,
												...
											),
					...
				)
				*/
				$all_chained_products_ids    = ( ! empty( $_POST['chained_products_ids'] ) ) ? $_POST['chained_products_ids'] : array(); // phpcs:ignore
				$nested_chained_products_ids = ( ! empty( $_POST['nested_chained_products_ids'] ) ) ? $_POST['nested_chained_products_ids'] : array(); // phpcs:ignore

				if ( ! empty( $nested_chained_products_ids ) ) {
					foreach ( $nested_chained_products_ids as $parent_id => $chained_ids ) {
						if ( empty( $all_chained_products_ids[ $parent_id ] ) ) {
							$all_chained_products_ids[ $parent_id ] = array();
						}
						$all_chained_products_ids[ $parent_id ] += $nested_chained_products_ids[ $parent_id ];
					}
				}

				/*
				Loop through existing orders
				Perform following 3 things:
				1. Add new chained items in order, if it is added in main product
				2. Update quantity of chained items, if chained item's qty is changed
				3. Remove order item, if chained item is removed from main product
				*/
				foreach ( $order_with_product_details as $order_id => $items ) {

					if ( ! empty( $items ) ) {
						$order = wc_get_order( $order_id );
						$added = $updated = $deleted = array(); // @codingStandardsIgnoreLine
						foreach ( $items as $item_id => $item ) {

							// Add new chained item.
							if ( empty( $item['_chained_product_of'] ) ) {
								$chained_parent_id      = ( ! empty( $item['_variation_id'] ) ) ? $item['_variation_id'] : $item['_product_id'];
								$chained_product_detail = $this->get_all_chained_product_details( $chained_parent_id );
								if ( ! empty( $all_chained_products_ids[ $chained_parent_id ] ) && ( ! empty( $order_with_product[ $order_id ][ $chained_parent_id ] ) || ! empty( $order_with_chained_parent_qty[ $order_id ][ $chained_parent_id ] ) ) ) {
									$new_chained_items = array();
									if ( ! empty( $order_with_product[ $order_id ][ $chained_parent_id ] ) ) {
										$new_chained_items = array_diff( $all_chained_products_ids[ $chained_parent_id ], $order_with_product[ $order_id ][ $chained_parent_id ] );
									} elseif ( ! empty( $order_with_chained_parent_qty[ $order_id ][ $chained_parent_id ] ) ) {
										/*
										Following line will handle those cases where
										a chained product is added to that product
										which didn't had any chained products earlier
										therefore considering all chained product ids as new chained items
										*/
										$new_chained_items = $all_chained_products_ids[ $chained_parent_id ];
									}
									if ( ! empty( $new_chained_items ) ) {
										foreach ( $new_chained_items as $item_to_add ) {
											$parent_qty  = ( ! empty( $item['_qty'] ) ) ? $item['_qty'] : 1;
											$new_item_id = $this->add_chained_item_in_order( $item_to_add, $order, $chained_parent_id, $chained_product_detail, $parent_qty );
											if ( ! empty( $new_item_id ) ) {
												$this->grant_download_permission_for_chained_item( $item_to_add, $order );
												$added[] = $this->get_product_title( $item_to_add );
											}
										}
									}
								}
								continue;
							}
							$product_id        = ( ! empty( $item['_variation_id'] ) ) ? $item['_variation_id'] : $item['_product_id'];
							$chained_parent_id = $item['_chained_product_of'];

							// Update qty.
							if ( ! empty( $all_chained_products_ids[ $chained_parent_id ] ) && in_array( $product_id, $all_chained_products_ids[ $chained_parent_id ], true ) ) {
								$index                       = array_search( $product_id, $all_chained_products_ids[ $chained_parent_id ], true );
								$unit                        = ( ! empty( $_POST['chained_products_quantity'][ $chained_parent_id ][ $index ] ) ) ? wc_clean( wp_unslash( $_POST['chained_products_quantity'][ $chained_parent_id ][ $index ] ) ) : 1; // phpcs:ignore
								$chained_parent_qty_in_order = ( ! empty( $order_with_chained_parent_qty[ $order_id ][ $chained_parent_id ] ) ) ? intval( $order_with_chained_parent_qty[ $order_id ][ $chained_parent_id ] ) : 1;
								$new_qty                     = $chained_parent_qty_in_order * intval( $unit );
								$old_qty                     = ( ! empty( $item['_qty'] ) ) ? intval( $item['_qty'] ) : 1;
								if ( $new_qty !== $old_qty ) {
									wc_update_order_item_meta( $item_id, '_qty', $new_qty );
									$updated[] = $this->get_product_title( $product_id );
								}

								// Remove chained item.
							} elseif ( ! empty( $all_chained_products_ids[ $chained_parent_id ] ) && ! in_array( $product_id, $all_chained_products_ids[ $chained_parent_id ], true ) ) {
								wc_delete_order_item( $item_id );
								$revoke_download[] = array(
									'order_id'   => $order_id,
									'product_id' => $product_id,
								);
								$deleted[]         = $this->get_product_title( $product_id );
							}
						}
						$note = '';

						if ( ! empty( $added ) ) {
							/* translators: Chained order item name(s) */
							$note .= sprintf( _n( 'Chained order item %s was added.', 'Chained order items %s were added.', count( $added ), 'woocommerce-chained-products' ), implode( ', ', $added ) );
						}
						if ( ! empty( $updated ) ) {
							/* translators: Chained order item name(s) */
							$note .= sprintf( _n( 'Quantity of chained order item %s was updated.', 'Quantity of chained order items %s were added.', count( $updated ), 'woocommerce-chained-products' ), implode( ', ', $updated ) );
						}
						if ( ! empty( $deleted ) ) {
							/* translators: Chained order item name(s) */
							$note .= sprintf( _n( 'Chained order item %s was removed.', 'Chained order items %s were removed.', count( $deleted ), 'woocommerce-chained-products' ), implode( ', ', $deleted ) );
						}
						if ( ! empty( $note ) ) {
							$order->add_order_note( $note, 0 );
						}
					}
				}

				if ( ! empty( $revoke_download ) ) {
					$revoke_download_query   = "DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE ";
					$revoke_download_segment = array();

					foreach ( $revoke_download as $row ) {
						if ( empty( $row['order_id'] ) || empty( $row['product_id'] ) ) {
							continue;
						}
						$revoke_download_segment[] = "( order_id = {$row['order_id']} AND product_id = {$row['product_id']} )";
					}

					$revoke_download_query .= implode( ' OR ', $revoke_download_segment );
					$is_revoked             = $wpdb->query( $revoke_download_query ); // phpcs:ignore

					if ( false === $is_revoked ) {
						update_option( '_chained_products_revoke_failed_' . time(), $revoke_download );
					}
				}
			}
		}

		/**
		 * Remove shortcode if present in post content
		 *
		 * @param array $data An array of post data.
		 * @return array $data
		 */
		public function remove_shortcode_from_post_content( $data = array() ) {
			global $wc_cp;
			if ( isset( $_POST['post_type'] ) && 'product' === wc_clean( wp_unslash( $_POST['post_type'] ) ) ) { // phpcs:ignore

				$product_id = ! empty( $_POST['ID'] ) ? absint( $_POST['ID'] ) : ''; // phpcs:ignore
				$product    = wc_get_product( $product_id );

				$remove_shortcode = true;

				if ( $product instanceof WC_Product_Variable ) {
					$variations = is_callable( array( $product, 'get_children' ) ) ? $product->get_children() : array();

					if ( ! empty( $variations ) ) {

						foreach ( $variations as $variation_id ) {
							$chained_details = is_callable( array( $wc_cp, 'chained_product_details' ) ) ? $wc_cp::chained_product_details( $variation_id ) : array();
							if ( ! empty( $chained_details ) ) {
								$remove_shortcode = false;
								break;
							}
						}
					}
				} elseif ( $product instanceof WC_Product ) {
					$chained_details = is_callable( array( $wc_cp, 'chained_product_details' ) ) ? $wc_cp::chained_product_details( $product_id ) : array();

					if ( ! empty( $chained_details ) ) {
						$remove_shortcode = false;
					}
				}

				if ( $remove_shortcode ) {
					$post_data['post_content'] = ! empty( $data['post_content'] ) ? $data['post_content'] : '';

					$shortcode_start = strpos( $post_data['post_content'], '[chained_products' );

					if ( false !== $shortcode_start ) {

						$shortcode_end = strpos( $post_data['post_content'], ']', $shortcode_start );

						if ( false !== $shortcode_end ) {

							$shortcode_length     = $shortcode_end - $shortcode_start + 1;
							$shortcode            = substr( $post_data['post_content'], $shortcode_start, $shortcode_length );
							$data['post_content'] = str_replace( $shortcode, '', $post_data['post_content'] );

						}
					}
				}
			}

			return $data;
		}

		/**
		 * Update chained product and quantity bundle detail in database
		 *
		 * @param int $chained_parent_id Chained parent ID.
		 */
		public function update_chained_product_data( $chained_parent_id = 0 ) {
			if ( empty( $chained_parent_id ) ) {
				return;
			}
			$chained_parent_id      = intval( $chained_parent_id );
			$chained_parent_product = wc_get_product( $chained_parent_id );

			if ( ! $chained_parent_product instanceof WC_Product ) {
				return;
			}

			if ( ! empty( $_POST['chained_products_ids'][ $chained_parent_id ] ) ) { // phpcs:ignore

				$chained_products_ids = array_filter( $_POST['chained_products_ids'][ $chained_parent_id ] ); // phpcs:ignore

				if ( empty( $chained_products_ids ) ) {
					return;
				}

				$chained_products_quantity            = ! empty( $_POST['chained_products_quantity'][ $chained_parent_id ] ) ? $_POST['chained_products_quantity'][ $chained_parent_id ] : 1; // phpcs:ignore
				$chained_products_priced_individually = ! empty( $_POST['chained_products_priced_individually'][ $chained_parent_id ] ) ? $_POST['chained_products_priced_individually'][ $chained_parent_id ] : ''; // phpcs:ignore

				foreach ( $chained_products_ids as $index => $product_id ) {

					if ( ! isset( $chained_products[ $chained_parent_id ][ $product_id ] ) ) {
						$chained_products[ $chained_parent_id ][ $product_id ] = 0;
					}

					$quantity            = ! empty( $chained_products_quantity[ $index ] ) ? $chained_products_quantity[ $index ] : 1;
					$priced_individually = ! empty( $chained_products_priced_individually[ $index ] ) ? $chained_products_priced_individually[ $index ] : 'no';

					$chained_products[ $chained_parent_id ][ $product_id ] = array(
						'quantity'            => $quantity,
						'priced_individually' => $priced_individually,
					);
				}

				// Disable priced_individually option for WooCommerce Subscriptions.
				if ( ! empty( $_POST['product-type'] ) && ( 'variable-subscription' === $_POST['product-type'] || 'subscription' === $_POST['product-type'] ) ) { // phpcs:ignore
					$disallow_priced_individually = true;
				}

				$chained_products_detail = array();

				foreach ( $chained_products[ $chained_parent_id ] as $product_id => $chained_item_data ) {

					$product = wc_get_product( $product_id );

					if ( ! empty( $product ) ) {

						$chained_products_detail[ $product_id ] = array(
							'unit'                => $chained_item_data['quantity'],
							'priced_individually' => ( isset( $disallow_priced_individually ) && true === $disallow_priced_individually ) ? 'no' : $chained_item_data['priced_individually'],
							'product_name'        => $this->get_product_title( $product_id ),
						);
						$chained_products_ids[]                 = $product_id;

					}
				}

				$chained_parent_product->update_meta_data( '_chained_product_detail', $chained_products_detail );

				$post_product_type = wp_unslash( $_POST['product-type'] ); // phpcs:ignore
				if ( ! empty( $post_product_type ) ) {
					$chained_parent_product->update_meta_data( '_chained_product_ids', $chained_products_ids );
				}

				if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {

					if ( isset( $_POST['chained_products_manage_stock'][ $chained_parent_id ] ) && 'on' === wc_clean( wp_unslash( $_POST['chained_products_manage_stock'][ $chained_parent_id ] ) ) ) { // phpcs:ignore
						$chained_parent_product->update_meta_data( '_chained_product_manage_stock', 'yes' );
					} else {
						$chained_parent_product->update_meta_data( '_chained_product_manage_stock', 'no' );
					}
				}
			} else {

				$chained_parent_product->delete_meta_data( '_chained_product_detail' );
				$chained_parent_product->delete_meta_data( '_chained_product_manage_stock' );

				$post_product_type = wp_unslash( $_POST['product-type'] ); // phpcs:ignore
				if ( ! empty( $post_product_type ) ) {
					$chained_parent_product->delete_meta_data( '_chained_product_ids' );
				}
			}

			$chained_parent_product->save();
		}

		/**
		 * Function to get formatted Product's Name
		 *
		 * @param int $product_id Product ID.
		 * @return string $product_title
		 */
		public function get_product_title( $product_id = 0 ) {
			if ( empty( $product_id ) ) {
				return '';
			}

			$_product = wc_get_product( $product_id );

			$product_title = '';

			if ( $_product instanceof WC_Product_Variation ) {
				$parent_product_id = is_callable( array( $_product, 'get_parent_id' ) ) ? $_product->get_parent_id() : 0;
				if ( ! empty( $parent_product_id ) ) {
					$_parent_product = wc_get_product( $parent_product_id );
					$product_title   = $_parent_product instanceof WC_Product && is_callable( array( $_parent_product, 'get_title' ) ) ? $_parent_product->get_title() : '';
				}
				$variation_data = is_callable( array( $_product, 'get_variation_attributes' ) ) ? $_product->get_variation_attributes() : array();

				if ( ! empty( $variation_data ) ) {
					$formatted_variation = wc_get_formatted_variation( $variation_data, true );
					if ( ! empty( $formatted_variation ) ) {
						$product_title .= ' ( ' . $formatted_variation . ' )';
					}
				}
			} else {
				$product_title = is_callable( array( $_product, 'get_title' ) ) ? $_product->get_title() : '';
			}

			return $product_title;
		}

		/**
		 * Function to find whether product is chained to any product.
		 *
		 * @param int $product_id Product ID.
		 * @return boolean
		 */
		public function is_chained_product( $product_id = 0 ) {
			global $wpdb;

			if ( empty( $product_id ) ) {
				return false;
			}

			$chained_product_ids = array();
			$results = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE meta_key = %s", '_chained_product_detail' ) ); // phpcs:ignore

			foreach ( $results as $result ) {
				$result_unserialized = maybe_unserialize( $result );
				$results_ids         = ( ! empty( $result ) && is_array( $result_unserialized ) ) ? array_keys( $result_unserialized ) : array();
				$chained_product_ids = array_merge( $chained_product_ids, $results_ids );
			}

			if ( in_array( $product_id, $chained_product_ids, true ) ) {
				return true;
			}

			return false;
		}

		/**
		 * Function to find whether product has chained items associated with it.
		 *
		 * @param int $product_id Product ID.
		 * @return boolean
		 */
		public function has_chained_products( $product_id = 0 ) {
			global $wc_cp;
			if ( empty( $product_id ) ) {
				return false;
			}
			$chained_product_detail = is_callable( array( $wc_cp, 'chained_product_details' ) ) ? $wc_cp::chained_product_details( $product_id ) : array();
			$chained_product_ids    = ( ! empty( $chained_product_detail ) ) ? array_keys( $chained_product_detail ) : array();

			return ! empty( $chained_product_ids );
		}

		/**
		 * Function to return parent_id if parent_id is greater than 0 or product_id if parent_id is 0
		 *
		 * @param int $product_id Product ID.
		 * @return int
		 */
		public function get_parent( $product_id = 0 ) {
			if ( empty( $product_id ) ) {
				return 0;
			}

			$_product = wc_get_product( $product_id );

			if ( $_product instanceof WC_Product_Variation ) {
				return is_callable( array( $_product, 'get_parent_id' ) ) ? $_product->get_parent_id() : 0;
			}

			return $product_id;
		}

		/**
		 * Function for creating array of chained product details of all chained products
		 *
		 * @global array $total_chained_details
		 * @global WC_Chained_Products $wc_cp The main instance of Chained Products.
		 *
		 * @param int $chained_parent_id Chained Parent ID.
		 * @param int $parent_unit       The parent products's unit.
		 * @param int $visited           The array contains the visited products id to fetch their chained items.
		 *
		 * @return array
		 */
		public function get_all_chained_product_details( $chained_parent_id = 0, $parent_unit = 1, &$visited = array() ) {
			global $total_chained_details, $wc_cp;

			if ( empty( $chained_parent_id ) ) {
				return array();
			}

			// Check if the product has been visited before.
			if ( is_array( $visited ) && ! empty( $visited ) && in_array( $chained_parent_id, $visited, true ) ) {
				return array();
			}

			$chained_parent_id = intval( $chained_parent_id );

			// Mark the product as visited.
			$visited[] = $chained_parent_id;

			// Get the direct chained items of the main product from the database.
			$direct_chained_items = is_callable( array( $wc_cp, 'chained_product_details' ) ) ? $wc_cp::chained_product_details( $chained_parent_id ) : array();

			// If there are no direct chained items, return an empty array.
			if ( empty( $direct_chained_items ) ) {
				return array();
			}

			// Array to store the chained products.
			$chained_products = array();

			// Iterate through the direct chained items.
			foreach ( $direct_chained_items as $item_id => $item ) {
				// Multiply the unit by the parent unit.
				$unit = intval( $parent_unit ) * ( ! empty( $item['unit'] ) ? intval( $item['unit'] ) : 1 );

				$chained_products[ $item_id ]         = $item;
				$chained_products[ $item_id ]['unit'] = $unit;

				$nested_chained_items = $this->get_all_chained_product_details( $item_id, $unit, $visited );

				// Merge the current chained products with nested chained products.
				if ( ! empty( $chained_products ) ) {
					$chained_products += $nested_chained_items;
				}
			}

			$total_chained_details = $chained_products;
			return $chained_products;
		}

		/**
		 * Function to get Product's Instance
		 *
		 * @param int $product_id Product ID.
		 * @return WC_Product $_product
		 */
		public function get_product_instance( $product_id ) {
			$_product = wc_get_product( $product_id );

			return $_product;
		}

		/**
		 * Function to check whether to show chained items to the customer
		 *
		 * @return boolean
		 */
		public function is_show_chained_items() {
			$is_show = get_option( 'sa_show_chained_items_to_customer', 'yes' );

			if ( 'no' === $is_show ) {
				$bool = false;
			} else {
				$bool = true;
			}

			$bool = apply_filters( 'sa_cp_show_chained_items', $bool, $is_show );

			if ( ! $bool ) {
				add_filter( 'woocommerce_cart_contents_count', array( 'WC_Chained_Products', 'sa_cp_get_cart_count' ) );
			}

			return $bool;
		}

		/**
		 * Function to check whether to show chained item's price.
		 *
		 * @return boolean
		 */
		public function is_show_chained_item_price() {
			$is_show = get_option( 'sa_show_chained_item_price', 'no' );

			return apply_filters( 'sa_cp_show_chained_item_price', 'yes' === $is_show, $is_show );
		}

		/**
		 * Function to fetch plugin's data.
		 */
		public static function get_chained_products_plugin_data() {
			return get_plugin_data( WC_CP_PLUGIN_FILE );
		}

		/**
		 * Function to register section for chained product global settings.
		 *
		 * @param array $sections Existing settings.
		 * @return array $sections
		 */
		public function cp_register_section( $sections ) {
			$sections['wc_chained_products'] = __( 'Chained products', 'woocommerce-chained-products' );
			return $sections;
		}

		/**
		 * Function to add chained product global settings for admin
		 *
		 * @param array $settings Existing settings.
		 * @param array $current_section Current section.
		 * @return array $settings
		 */
		public function cp_add_settings( $settings, $current_section ) {
			if ( 'wc_chained_products' === $current_section ) {

				/* translators: Woocommerce Currency Symbol */
				$show_price_desc = sprintf( __( 'This will show chained item price as %s in cart, cart widget/mini cart, checkout & order received page.', 'woocommerce-chained-products' ), '<b><del>' . get_woocommerce_currency_symbol() . '22.55</del></b>' );

				if ( Chained_Products_WC_Compatibility::is_wc_gte_32() ) {
					$show_price_desc .= '<br>' . __( 'For chained items having Priced Individually option enabled, the chained item price will be shown.', 'woocommerce-chained-products' );
				}

				$settings = array(
					array(
						'title' => __( 'Settings', 'woocommerce-chained-products' ),
						'type'  => 'title',
						'desc'  => '',
						'id'    => 'wc_cp_settings',
					),
					array(
						'title'    => __( 'Visibility', 'woocommerce-chained-products' ),
						'desc'     => __( 'Show chained items to customers', 'woocommerce-chained-products' ),
						'desc_tip' => __( 'This will show chained items in cart, cart widget/mini cart, checkout & order received page.', 'woocommerce-chained-products' ),
						'id'       => 'sa_show_chained_items_to_customer',
						'default'  => 'yes',
						'type'     => 'checkbox',
						'autoload' => false,
					),
					array(
						'title'    => __( 'Show price', 'woocommerce-chained-products' ),
						'desc'     => __( 'Show chained item price', 'woocommerce-chained-products' ),
						'desc_tip' => $show_price_desc,
						'id'       => 'sa_show_chained_item_price',
						'default'  => 'no',
						'type'     => 'checkbox',
						'autoload' => false,
					),
					array(
						'title'    => _x( 'Housekeeping', 'Title for Housekeeping option', 'woocommerce-chained-products' ),
						'desc'     => _x( 'Enable housekeeping', 'Description for Housekeeping option', 'woocommerce-chained-products' ),
						'desc_tip' => sprintf( '<strong>%1$s</strong> <a href="%2$s" target="_blank">%3$s</a>', esc_html_x( 'Note: It is recommended to keep this option enabled.', 'Note for Housekeeping option', 'woocommerce-chained-products' ), esc_url( 'https://woocommerce.com/document/chained-products/#section-14' ), esc_html_x( 'Know more about Housekeeping', 'Link title for Housekeeping documentation', 'woocommerce-chained-products' ) ),
						'id'       => 'sa_chained_products_housekeeping',
						'default'  => 'yes',
						'type'     => 'checkbox',
						'autoload' => false,
					),
					array(
						'title'    => _x( 'Remove duplicate items', 'Title for removing duplicate item option', 'woocommerce-chained-products' ),
						'desc'     => _x( 'This will remove the main item from the chained items while manually adding an order', 'Description for removing duplicate item option', 'woocommerce-chained-products' ),
						'id'       => 'sa_chained_products_ignore_main_product_price',
						'default'  => 'no',
						'type'     => 'checkbox',
						'autoload' => false,
					),
					array(
						'type' => 'sectionend',
						'id'   => 'wc_cp_settings',
					),
				);
			}

			return $settings;
		}

		/**
		 * Function to add styles & scripts for chained products settings.
		 */
		public function cp_styles_and_scripts() {
			if ( ! empty( $_GET['tab'] ) && ! empty( $_GET['section'] ) && 'products' === $_GET['tab'] && 'wc_chained_products' === $_GET['section'] ) { // phpcs:ignore
				if ( ! wp_script_is( 'jquery' ) ) {
					wp_enqueue_script( 'jquery' );
				}
				?>
				<script type="text/javascript">
					jQuery( function() {
						var visibility_enabled = jQuery( '#sa_show_chained_items_to_customer' ).is( ':checked' );
						var price_setting      = jQuery( '#sa_show_chained_item_price' ).closest( 'tr' );

						if ( ! visibility_enabled ) {
							price_setting.hide();
						}

						jQuery( '#sa_show_chained_items_to_customer' ).on( 'change', function() {
							if ( jQuery( this ).is( ':checked' )  ) {
								price_setting.fadeIn(500);
							} else {
								price_setting.fadeOut(500);
							}
						});
					});
				</script>
				<?php
			}
		}

		/**
		 * Function to check whether the current page is WooCommerce admin order page or not.
		 *
		 * @return bool Return true if order admin page otherwise false.
		 */
		public function is_wc_order_admin_page() {

			global $wc_cp;

			$order_screen = ( ( class_exists( 'Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) && is_callable( array( 'Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController', 'custom_orders_table_usage_is_enabled' ) ) ) && ( wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ) ) ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';

			$current_screen = get_current_screen();

			return $current_screen instanceof WP_Screen && ! empty( $current_screen->id ) && $order_screen === $current_screen->id;
		}
	}

	global $wc_chained_products;

	$wc_chained_products = new WC_Admin_Chained_Products();
}
