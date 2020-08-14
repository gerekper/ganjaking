<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Orders
 * @package    Yithemes
 * @since      Version 1.6
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Orders' ) ) {

	class YITH_Orders {

		/**
		 * Main instance
		 *
		 * @var string
		 * @since 1.4.0
		 */
		protected static $_instance = null;

		/**
		 * Order Sync Enabled
		 *
		 * @var bool
		 * @since 1.4.0
		 */
		public $sync_enabled = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'check_suborder' ), 20, 2 );

			if ( function_exists( 'YITH_WOCC' ) ) {
				add_action( 'yith_wooc_update_order_meta', array( $this, 'check_suborder' ), 20, 1 );
			}

			/* Prevent duplicate order if the user use externa payment gateway */
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'check_awaiting_payment' ) );
			add_action( 'before_delete_post', array( $this, 'delete_order_items' ) );
			add_action( 'before_delete_post', array( $this, 'delete_order_downloadable_permissions' ) );

			/* Prevent Multiple Email Notifications for Suborders */
			add_filter( 'woocommerce_email_recipient_new_order', array(
				$this,
				'woocommerce_email_recipient_new_order'
			), 10, 2 );
			add_filter( 'woocommerce_email_recipient_cancelled_order', array(
				$this,
				'woocommerce_email_recipient_new_order'
			), 10, 2 );
			add_filter( 'woocommerce_email_enabled_customer_processing_order', array(
				$this,
				'woocommerce_email_enabled_new_order'
			), 10, 2 );
			add_filter( 'woocommerce_email_enabled_new_order', array(
				$this,
				'woocommerce_email_enabled_new_order'
			), 10, 2 );
			add_filter( 'woocommerce_email_enabled_customer_completed_order', array(
				$this,
				'woocommerce_email_enabled_new_order'
			), 10, 2 );
			add_filter( 'woocommerce_email_enabled_customer_partially_refunded_order', array(
				$this,
				'woocommerce_email_enabled_new_order'
			), 10, 2 );
			add_filter( 'woocommerce_email_enabled_customer_refunded_order', array(
				$this,
				'woocommerce_email_enabled_new_order'
			), 10, 2 );
			add_filter( 'woocommerce_email_enabled_customer_on_hold_order', array(
				$this,
				'woocommerce_email_enabled_new_order'
			), 10, 2 );

			/* Order Refund */
			add_action( 'woocommerce_order_refunded', array( $this, 'order_refunded' ), 10, 2 );
			add_action( 'woocommerce_refund_deleted', array( $this, 'refund_deleted' ), 10, 2 );

			/* Single Order Page for Vendor */
			add_filter( 'woocommerce_attribute_label', array( $this, 'commissions_attribute_label' ), 10, 3 );

			/* Order Item Meta */
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ) );

			/* Order Table */
			add_filter( 'manage_shop_order_posts_columns', array( $this, 'shop_order_columns' ), 20);
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_shop_order_columns' ) );

			/* Order MetaBoxes */
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

			/* Vendor Order List */
			add_filter( 'yith_wcmv_shop_order_request', array( $this, 'vendor_order_list' ), 20 );

			/* Trash Sync */
			add_action( 'trashed_post', array( $this, 'trash_suborder' ), 10, 1 );

			/* YITH WooCommerce Stripe Support */
			add_filter( 'yith_stripe_skip_capture_charge', array( $this, 'skip_stripe_charge_for_suborders' ), 10, 2 );

			/* Add shipping addresses to vendor email */
			add_filter( 'woocommerce_order_needs_shipping_address', array(
				$this,
				'order_needs_shipping_address'
			), 10, 3 );

			add_action( 'woocommerce_recorded_sales', array( $this, 'recorded_sales_hack' ) );

			//the revoke download permission and the grant download permission would be always synchronized
			add_action( 'woocommerce_ajax_revoke_access_to_product_download', array(
				$this,
				'revoke_access_to_product_download'
			), 10, 3 );
			add_action( 'wp_ajax_woocommerce_grant_access_to_download', array( $this, 'grant_access_to_download' ), 5 );

			// Create order from admin area
			//  WooCommerce complete all process order meta with priority set to 50 or greater
			add_action( 'woocommerce_process_shop_order_meta', array( $this, 'create_suborder_in_admin_area' ), 100, 2);

			// add bubble notification for Vendor's order
			add_action( 'admin_head', array( $this, 'menu_order_count' ) );

			$this->sync_enabled = 'yes' == get_option( 'yith_wpv_vendors_option_order_synchronization', 'yes' );

			if ( $this->sync_enabled ) {
				/* SubOrder Sync */
				add_action( 'woocommerce_order_status_changed', array(
					$this,
					'suborder_status_synchronization'
				), 30, 3 );
				/* Order Meta Synchronization */
				add_action( 'woocommerce_process_shop_order_meta', array(
					$this,
					'suborder_meta_synchronization'
				), 65, 2 );

				/* SenangPay Payment Gateway for WooCommerce by senangPay Support */
				if ( class_exists( 'senangpay' ) ) {
					add_action( 'woocommerce_payment_complete', array( $this, 'suborder_status_synchronization' ) );
				}

				if( defined( 'YITH_YWSBS_PREMIUM' ) ){
					add_action( 'yith_suborder_renew_created', array( $this, 'suborder_status_synchronization' ) );
				}

				add_filter( 'woocommerce_can_restore_order_stock', array( $this, 'can_restore_order_stock' ), 10, 2 );
				/**
				 * Other Ajax Action:
				 *
				 * load_order_items
				 * woocommerce_EVENT => nopriv
				 */
				$ajax_events = array(
					'add_order_item'            => false,
					/*'add_order_fee'           => false,*/
					/*'add_order_shipping'      => false,*/
					'add_order_tax'             => false,
					'remove_order_item'         => false,
					'remove_order_tax'          => false,
					'reduce_order_item_stock'   => false,
					'increase_order_item_stock' => false,
					/*'add_order_item_meta'     => false, */
					'remove_order_item_meta'    => false,
					'calc_line_taxes'           => false,
					'save_order_items'          => false,
					'add_order_note'            => false,
					'delete_order_note'         => false,
				);

				foreach ( $ajax_events as $ajax_event => $nopriv ) {
					add_action( "wp_ajax_woocommerce_{$ajax_event}", array( __CLASS__, $ajax_event ), 5 );
					$nopriv && add_action( "wp_ajax_nopriv_woocommerce_{$ajax_event}", array(
						__CLASS__,
						$ajax_event,
						5
					) );
				}
			}
		}

		/**
		 * Check for vendor sub-order
		 *
		 * $parent_order_id string The parent order id
		 * $posted          mixed  Array of posted form data.
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since   1.6
		 * @return  array|void
		 */
		public function check_suborder( $parent_order_id, $posted = array(), $return = false ) {
			//check if is parent order
			if ( wp_get_post_parent_id( $parent_order_id ) != 0 ) {
				return false;
			}

			$parent_order       = wc_get_order( $parent_order_id );
			$items              = $parent_order->get_items();
			$products_by_vendor = array();
			$suborder_ids       = array();

			//check for vendor product
			foreach ( $items as $item ) {
				$vendor = yith_get_vendor( $item['product_id'], 'product' );
				if ( $vendor->is_valid() ) {
					$products_by_vendor[ $vendor->id ][] = $item;
				}
			}

			$vendor_count = count( $products_by_vendor );

			//Vendor's items ? NO
			if ( $vendor_count == 0 ) {
				return false;
			} //Vendor's items ? YES
			else {
				//add sub-order to parent
				$parent_order->add_meta_data( 'has_sub_order', true, true );

				foreach ( $products_by_vendor as $vendor_id => $vendor_products ) {
					//create sub-orders
					$suborder_ids[] = $this->create_suborder( $parent_order, $vendor_id, $vendor_products, $posted );
				}

				if ( ! empty( $suborder_ids ) ) {
					foreach ( $suborder_ids as $suborder_id ) {
						do_action( 'yith_wcmv_checkout_order_processed', $suborder_id );
					}
				}

				$parent_order->save_meta_data();

				if ( $return ) {
					return $suborder_ids;
				}
			}
		}

		/**
		 * Create vendor sub-order
		 *
		 * @param WC_Order $parent_order
		 * @param int $vendor_id
		 * @param array $vendor_products
		 * @param array $posted
		 *
		 * Create an order. Error codes:
		 *        520 - Cannot insert order into the database.
		 *        521 - Cannot get order after creation.
		 *        522 - Cannot update order.
		 *        525 - Cannot create line item.
		 *        526 - Cannot create fee item.
		 *        527 - Cannot create shipping item.
		 *        528 - Cannot create tax item.
		 *        529 - Cannot create coupon item.
		 *
		 * @throws Exception
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.6
		 * @return int|WP_ERROR
		 */
		public function create_suborder( $parent_order, $vendor_id, $vendor_products, $posted ) {
			/** @var $parent_order WC_Order */
			$vendor          = yith_get_vendor( $vendor_id, 'vendor' );
			$parent_order_id = yit_get_prop( $parent_order, 'id' );
			$order_data      = apply_filters( 'woocommerce_new_order_data', array(
					'post_type'     => 'shop_order',
					'post_title'    => sprintf( __( 'Order &ndash; %s', 'yith-woocommerce-product-vendors' ), strftime( _x( '%b %d, %Y @ %I:%M %p', 'Order date parsed by strftime', 'yith-woocommerce-product-vendors' ) ) ),
					'post_status'   => 'wc-' . apply_filters( 'woocommerce_default_order_status', 'pending' ),
					'ping_status'   => 'closed',
					'post_excerpt'  => isset( $posted['order_comments'] ) ? $posted['order_comments'] : '',
					'post_author'   => $vendor->get_owner(),
					'post_parent'   => $parent_order_id,
					'post_password' => uniqid( 'order_' ) // Protects the post just in case
				)
			);

			$suborder_id        = wp_insert_post( $order_data );
			$is_wpml_configured = apply_filters( 'wpml_setting', false, 'setup_complete' );
			$suborder           = wc_get_order( $suborder_id );

			if( $is_wpml_configured ){
				// Propagate order language to sub-orders.
				$wpml_language_from_parent_order = get_post_meta( $parent_order_id, 'wpml_language', true );
				$suborder->update_meta_data( 'wpml_language', $wpml_language_from_parent_order );
			}


			$parent_line_items = $parent_order->get_items( 'line_item' );

			if ( ! empty( $suborder_id ) && ! is_wp_error( $suborder_id ) ) {
				$order_total = $discount = $order_tax = 0;
				$product_ids = $order_taxes = $order_shipping_tax_amount = array();

				// now insert line items
				/** @var $item WC_Order_Item_Product */
				foreach ( $vendor_products as $item ) {

					$order_total += (float) $item['line_total'];
					//Tax calculation
					$line_tax_data = maybe_unserialize( $item['line_tax_data'] );
					if ( isset( $line_tax_data['total'] ) ) {
						foreach ( $line_tax_data['total'] as $tax_rate_id => $tax ) {
							if ( ! isset( $order_taxes[ $tax_rate_id ] ) ) {
								$order_taxes[ $tax_rate_id ] = 0;
							}
							$order_taxes[ $tax_rate_id ] += $tax;
							//TODO: Shipping Tax
							$order_shipping_tax_amount[ $tax_rate_id ] = 0;
						}
					}

					$order_tax += (float) $item['line_tax'];
					$product_ids[] = $item['product_id'];

					$item_id = 0;

					if ( YITH_Vendors()->is_wc_2_7_or_greather ) {
						$args = array();

						$args['variation_id'] = ( ! empty( $item['variation_id'] ) ) ? $item['variation_id'] : array();
						$args['product_id']   = ( ! empty( $item['product_id'] ) ) ? $item['product_id'] : array();

						if ( ! empty( $item['name'] ) ) {
							$args['name'] = $item['name'];
						}

						if ( isset( $item['line_subtotal'] ) ) {
							$args['totals']['subtotal'] = $item['line_subtotal'];
						}

						if ( isset( $item['line_total'] ) ) {
							$args['totals']['total'] = $item['line_total'];
						}

						if ( isset( $item['line_subtotal_tax'] ) ) {
							$args['totals']['subtotal_tax'] = $item['line_subtotal_tax'];
						}

						if ( isset( $item['line_tax'] ) ) {
							$args['totals']['tax'] = $item['line_tax'];
						}

						if ( isset( $item['line_tax_data'] ) ) {
							$args['totals']['tax_data'] = $item['line_tax_data'];
						}

						$item_id = $suborder->add_product( wc_get_product( $item['product_id'] ), $item['quantity'], $args );

						if ( $item_id ) {
							$suborder_item = $suborder->get_item( $item_id );
							$suborder_item->add_meta_data( '_parent_line_item_id', $item->get_id(), true );
                            $item->add_meta_data( '_reduced_stock', $item['quantity'], true );
							$suborder_item->save_meta_data();
							$suborder_item->save();
						}
					} else {
						$item_id = wc_add_order_item( $suborder_id, array(
								'order_item_name' => $item['name'],
								'order_item_type' => 'line_item',
							)
						);
					}

					if ( $item_id ) {
						$default_meta_to_exclude = apply_filters( 'yith_wcmv_order_item_meta_no_sync', array(
								'_child__commission_id',
								'_commission_included_tax',
								'_commission_included_coupon',
							)
						);

						$item_meta_data = $item->get_meta_data();

						foreach( $item_meta_data as $item_obj ){
							$line_item_data = $item_obj->get_data();

							if ( ! in_array( $line_item_data['key'], $default_meta_to_exclude ) ) {
								wc_add_order_item_meta( $item_id, $line_item_data['key'], $line_item_data['value'] );
							}

							if ( ! YITH_Vendors()->is_wc_2_7_or_greather && '_product_id' == $line_item_data['key'] ) {
								foreach ( $parent_line_items as $line_item_id => $line_item_value ) {
									/**
									 * @internal $key == 'product_id'
									 *
									 * Check for Variable product.
									 * Use the variation id instead of product id
									 */
									$product_id        = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
									$parent_product_id = ! empty( $line_item_value['variation_id'] ) ? $line_item_value['variation_id'] : $line_item_value['product_id'];

									if ( $product_id == $parent_product_id ) {
										// add line item to retrieve simply the parent line_item_id
										wc_update_order_item_meta( $item_id, '_parent_line_item_id', $line_item_id );
										break;
									}
								}
							}
						}
					}

					//Calculate Discount
					$discount += ( $item['line_subtotal'] - $item['line_total'] );
				}

				//Shipping: Store shipping for all packages
				$shipping_cost = 0;

				$wc_checkout     = WC()->checkout();
				$checkout_fields = array();

				//if the current action is not a renew , not load the checkout fields ( avoid possible fatal error )
				if( ! is_admin() && ! is_ajax() && 'ywsbs_renew_subscription' !== current_action() ){
					try{
						$checkout_fields = $wc_checkout->checkout_fields;
					} catch (Exception $e){
						$checkout_fields = array();
					}
				}


				if ( empty( $checkout_fields ) ) {
					$types = apply_filters( 'yith_wcmv_create_order_address_fields', array(
							'billing',
							'shipping'
						)
					);

					foreach( $types as $type ){
						$fields = $parent_order->get_address( $type );
						$suborder->set_address( $fields, $type );
					}
				}

				if( ! empty( $wc_checkout ) ){
					foreach ( $checkout_fields as $section => $order_meta_keys ) {
						if ( 'account' != $section ) {
							foreach ( $order_meta_keys as $order_meta_key => $order_meta_values ) {
								$meta_key           = 'shipping' == $section || 'billing' == $section ? '_' . $order_meta_key : $order_meta_key;
								$meta_value_to_save = isset( $posted[ $order_meta_key ] ) ? $posted[ $order_meta_key ] : yit_get_prop( $parent_order, $order_meta_key );
								yit_save_prop( $suborder, $meta_key, $meta_value_to_save );
							}
						}
					}

					foreach ( WC()->shipping->get_packages() as $package_key => $package ) {
						if ( ! empty( $package['yith-vendor'] ) && $package['yith-vendor'] instanceof YITH_Vendor && $package['yith-vendor']->id == $vendor_id ) {
							if ( isset( $package['rates'][ $wc_checkout->shipping_methods[ $package_key ] ] ) ) {

								$shipping_item_id = $this->add_shipping( $suborder, $package['rates'][ $wc_checkout->shipping_methods[ $package_key ] ], $vendor_id );
								$shipping_cost += $package['rates'][ $wc_checkout->shipping_methods[ $package_key ] ]->cost;

								if ( ! $shipping_item_id ) {
									throw new Exception( sprintf( __( 'Error %d: Unable to create order. Please try again.', 'yith-woocommerce-product-vendors' ), 527 ) );
								}
							}
						}
					}
				}

				// Allows plugins to add order item meta to shipping
				do_action( 'yith_wcmv_add_shipping_order_item', $suborder_id, $this, $vendor_id );

				//Coupons
				/**
				 * $order->get_used_coupons() was deprecated from WooCommerce 3.7
				 */
				$order_coupons = YITH_Vendors()->is_wc_3_7_or_greather ? $parent_order->get_coupon_codes() : $parent_order->get_used_coupons();

				if ( ! empty( $order_coupons ) ) {
					foreach ( $order_coupons as $order_coupon ) {
						$coupon = new WC_Coupon( $order_coupon );

						if ( apply_filters( 'yith_wcmv_check_is_vendor_coupon_in_create_suborder', $coupon && $vendor_id == $coupon->get_meta( 'vendor_id', true ), $coupon, $vendor_id ) ) {
							$order_item_id = wc_add_order_item( $suborder_id, array(
									'order_item_name' => $order_coupon,
									'order_item_type' => 'coupon',
								)
							);

							// Add line item meta
							if ( $order_item_id ) {
								$order_item_value = isset( WC()->cart->coupon_discount_amounts[ $order_coupon ] ) ? WC()->cart->coupon_discount_amounts[ $order_coupon ] : 0;
								$meta_key         = 'discount_amount';
								wc_add_order_item_meta( $order_item_id, $meta_key, $order_item_value );
							}
						}
					}
				}

				if ( YITH_Vendors()->is_wc_2_6 ) {
					//Calculate Total
					$order_in_total = $order_total + $shipping_cost + $order_tax;

					$totals = array(
						'shipping'           => wc_format_decimal( $shipping_cost ),
						'cart_discount'      => wc_format_decimal( $discount ),
						'cart_discount_tax'  => 0,
						'tax'                => wc_format_decimal( $order_tax ),
						'order_shipping_tax' => 0,
						'total'              => wc_format_decimal( $order_in_total ),
					);

					//Set tax. N.B.: needs total to works
					if ( function_exists( 'WC' ) && WC()->cart instanceof WC_Cart ) {
						/** @var WC_Cart $cart */
						$_cart           = WC()->cart;
						$line_item_taxes = array_keys( $_cart->taxes + $_cart->shipping_taxes );

						foreach ( $line_item_taxes as $tax_rate_id ) {
							if (
								$_cart
								&&
								$tax_rate_id
								&&
								isset( $order_taxes[ $tax_rate_id ] )
								&&
								isset( $order_shipping_tax_amount[ $tax_rate_id ] )
								&&
								apply_filters( 'woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated' ) !== $tax_rate_id
							) {
								$suborder->add_tax( $tax_rate_id, $order_taxes[ $tax_rate_id ], $order_shipping_tax_amount[ $tax_rate_id ] );
							}
						}
					}

					//Set totals
					foreach ( $totals as $meta_key => $meta_value ) {
						$suborder->set_total( $meta_value, $meta_key );
					}
				}

				//Set other order meta
				$order_meta = array(
					'_payment_method'       => yit_get_prop( $parent_order, 'payment_method' ),
					'_payment_method_title' => yit_get_prop( $parent_order, 'payment_method_title' ),
					'_order_key'            => apply_filters( 'woocommerce_generate_order_key', uniqid( 'order_' ) ),
					'_customer_user'        => $parent_order->get_user_id( 'edit' ),
					'_prices_include_tax'   => yit_get_prop( $parent_order, 'prices_include_tax' ),
					'_order_currency'       => get_post_meta( $parent_order_id, '_order_currency', true ),
					'_customer_ip_address'  => get_post_meta( $parent_order_id, '_customer_ip_address', true ),
					'_customer_user_agent'  => get_post_meta( $parent_order_id, '_customer_user_agent', true ),
				);

				foreach ( $order_meta as $meta_key => $meta_value ) {
					$suborder->update_meta_data( $meta_key, $meta_value );
				}
				// Let plugins add meta
				do_action( 'yith_wcmv_checkout_update_order_meta', $suborder_id, $posted );
				// update order version meta
				$suborder->add_meta_data( '_order_version', YITH_Vendors()->version );

				// update created_via meta
				$suborder->add_meta_data( '_created_via', 'yith_wcmv_vendor_suborder' );

				// Add Vendor ID in order meta
				$suborder->add_meta_data( 'vendor_id', $vendor_id, true );

				if ( YITH_Vendors()->is_wc_2_7_or_greather ) {
					$suborder->calculate_totals();
				}

				$suborder->save();

				do_action( 'yith_wcmv_suborder_created', $suborder_id, $parent_order_id, $vendor_id );
			}

			return $suborder_id;
		}

		/**
		 * Parent to Child synchronization
		 *
		 *
		 * @param $parent_order_id  The parent id order
		 * @param $old_status       Old Status
		 * @param $new_status       New Status
		 *
		 * @internal param \WC_Order $parent_order
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6
		 * @return void
		 */
		public function suborder_status_synchronization( $parent_order_id, $old_status = '', $new_status = '' ) {
			//Check if order have sub-order
			if ( wp_get_post_parent_id( $parent_order_id ) ) {
				return false;
			}

			$suborder_ids = self::get_suborder( $parent_order_id );
			if ( ! empty( $suborder_ids ) ) {
				remove_action( 'woocommerce_order_status_completed', 'wc_paying_customer' );

				if ( empty( $new_status ) ) {
					$parent_order = wc_get_order( $parent_order_id );
					$new_status   = $parent_order->get_status( 'edit' );
				}

				if( 'refunded' != $new_status ){
					foreach ( $suborder_ids as $suborder_id ) {
						/** @var $suborder WC_Order */
						$suborder = wc_get_order( $suborder_id );

						$suborder->update_status( $new_status, _x( 'Updated by admin: ', 'Order note', 'yith-woocommerce-product-vendors' ) );
					}
				}

				add_action( 'woocommerce_order_status_completed', 'wc_paying_customer' );
			}
		}

		/**
		 * Parent to Child synchronization
		 *
		 *
		 * @param $parent_order_id  The parent id order
		 * @param $parent_order     The parent order
		 *
		 * @internal param \WC_Order $parent_order
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6
		 * @return void
		 */
		public function suborder_meta_synchronization( $parent_order_id, $parent_order ) {
			//Check if order have sub-order
			if ( wp_get_post_parent_id( $parent_order_id ) ) {
				return false;
			}

			/** @var $suborder WC_Order */
			/** @var $parent_order WC_Order */
			$suborder_ids = self::get_suborder( $parent_order_id );
			$parent_order = wc_get_order( $parent_order_id );

            $vendor_id = 0;

            if ( ! empty( $suborder_ids ) ) {
				foreach ( $suborder_ids as $suborder_id ) {
					$suborder               = wc_get_order( $suborder_id );
					$child_items            = array_keys( $suborder->get_items() );
					$_post                  = $_POST;
					$_post['order_item_id'] = $child_items;
					$suborder_line_total    = 0;
                    $vendor_id = get_post_field( 'post_author', $suborder_id );

                    foreach ( $child_items as $child_items_id ) {
						$parent_item_id = wc_get_order_item_meta( $child_items_id, '_parent_line_item_id', true );
						$parent_item_id = absint( is_array( $parent_item_id ) ? array_shift( $parent_item_id ) : $parent_item_id );

						foreach ( $_post as $meta_key => $meta_value ) {
							//TODO: Shipping Cost

							switch ( $meta_key ) {
								case 'line_total':
								case 'line_subtotal':
								case 'order_item_tax_class':
								case 'order_item_qty':
								case 'refund_line_total':
								case 'refund_order_item_qty':
								case 'line_tax':
								case 'line_subtotal_tax':
								case 'line_tax_data':
								case 'refund_line_tax':

									if ( isset( $_post[ $meta_key ][ $parent_item_id ] ) ) {
										$_post[ $meta_key ][ $child_items_id ] = $_post[ $meta_key ][ $parent_item_id ];
										unset( $_post[ $meta_key ][ $parent_item_id ] );
									}
									break;

								case 'shipping_cost':
									if ( isset( $_post[ $meta_key ][ $parent_item_id ] ) ) {
										$_post[ $meta_key ][ $child_items_id ] = 0;
										unset( $_post[ $meta_key ][ $parent_item_id ] );
									}
									break;
								default: //nothing to do
									break;
							}
						}

						//Calculate Order Total
						if ( isset( $_post['line_total'][ $child_items_id ] ) ) {
							$suborder_line_total += wc_format_decimal( $_post['line_total'][ $child_items_id ] );
						}
					}

					//New Order Total
					$_post['_order_total'] = wc_format_decimal( $suborder_line_total );

					/**
					 * Don't use save method by WC_Meta_Box_Order_Items class because I need to filter the POST information
					 * use wc_save_order_items( $order_id, $items ) function directly.
					 *
					 * @see WC_Meta_Box_Order_Items::save( $suborder_id, $suborder ); in woocommerce\includes\admin\meta-boxes\class-wc-meta-box-order-items.php:45
					 * @see wc_save_order_items( $order_id, $items ); in woocommerce\includes\admin\wc-admin-functions.php:176
					 */
					wc_save_order_items( $suborder_id, $_post );
					WC_Meta_Box_Order_Downloads::save( $suborder_id, $suborder );
					WC_Meta_Box_Order_Data::save( $suborder_id, $suborder );
					WC_Meta_Box_Order_Actions::save( $suborder_id, $suborder );

                    if( $vendor_id ){
                        wp_update_post( array( 'ID' => $suborder_id, 'post_author' => $vendor_id ) );
                    }
				}
			}
		}

		/**
		 * Get suborder from parent_order_id
		 *
		 *
		 * @param bool|int $parent_order_id The parent id order
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6
		 * @return array
		 */
		public static function get_suborder( $parent_order_id = false ) {
			$suborder_ids = array();
			if ( $parent_order_id ) {
				global $wpdb;

				$parent_ids = array( absint( $parent_order_id ) );


				while ( $parent_ids ) {
					$parents_list = implode( ',', $parent_ids );
					$parent_ids = $wpdb->get_col( $wpdb->prepare(
						"SELECT ID FROM {$wpdb->posts} AS p
                     LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
                     WHERE post_parent IN ({$parents_list})
                     AND post_type=%s
                     AND meta_key=%s
                     AND meta_value=%s ",
						'shop_order',
						'_created_via',
						'yith_wcmv_vendor_suborder'
					) );
					$suborder_ids = array_merge( $suborder_ids, $parent_ids );

				}
			}
			return apply_filters( 'yith_wcmv_get_suborder_ids', $suborder_ids, $parent_order_id );
		}

		/**
		 * Get parent item id from child item id
		 *
		 *
		 * @param $suborder         The suborder object
		 * @param $child_item_id    The child item id
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6
		 * @return   int|bool The parent item id if exist, false otherwise
		 */
		public static function get_parent_item_id( $suborder = false, $child_item_id ) {
			global $wpdb;
			$parent_item_id = false;

			if ( ! $suborder ) {
				$parent_item_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT order_item_id FROM {$wpdb->order_itemmeta} WHERE meta_id=%d", $child_item_id ) );
				$parent_item_id = ! empty( $parent_item_id ) ? $parent_item_id : false;
			} else {
				$parent_item_id = wc_get_order_item_meta( $child_item_id, '_parent_line_item_id', true );
			}


			return $parent_item_id;
		}

		/**
		 * Get parent item id from child item id
		 *
		 * @param $parent_item_id
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6
		 * @return   int|bool The parent item id if exist, false otherwise
		 */
		public static function get_child_item_id( $parent_item_id ) {
			global $wpdb;
			$child_item_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_item_id FROM {$wpdb->order_itemmeta} WHERE meta_key=%s AND meta_value=%d", '_parent_line_item_id', absint( $parent_item_id ) ) );

			return $child_item_id;
		}

		/**
		 * Get line item id from parent item id
		 *
		 * @param $order_item_id The parent order_item_id
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6
		 * @return   int|bool The child item id if exist, false otherwise
		 */
		public static function get_line_item_id_from_parent( $order_item_id ) {
			global $wpdb;

			return $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT order_item_id FROM {$wpdb->order_itemmeta} WHERE meta_key=%s AND meta_value=%d", '_parent_line_item_id', $order_item_id ) );
		}

		/**
		 * Save order items ajax sync
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6
		 * @return void
		 * @access   public static
		 */
		public static function save_order_items() {
			check_ajax_referer( 'order-item', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			if ( isset( $_POST['order_id'] ) && isset( $_POST['items'] ) ) {
				$parent_order_id = absint( $_POST['order_id'] );
				//Check if order have sub-order
				if ( ! wp_get_post_parent_id( $parent_order_id ) ) {
					global $wpdb;
					// Parse the jQuery serialized items
					$_post = $_POST;
					parse_str( $_post['items'], $_post['items'] );
					$suborder_ids = self::get_suborder( $parent_order_id );
					foreach ( $suborder_ids as $suborder_id ) {
						$order_total                     = 0;
						$suborder                        = wc_get_order( $suborder_id );
						$child_items                     = array_keys( $suborder->get_items() );
						$_post['items']['order_item_id'] = $child_items;
						foreach ( $child_items as $child_item_id ) {
							$parent_item_id = self::get_parent_item_id( $suborder, $child_item_id );
							foreach ( $_post['items'] as $meta_key => $meta_value ) {
								if ( ! in_array( $meta_key, array(
										'order_item_id',
										'_order_total'
									) ) && isset( $_post['items'][ $meta_key ][ $parent_item_id ] )
								) {
									$_post['items'][ $meta_key ][ $child_item_id ] = $_post['items'][ $meta_key ][ $parent_item_id ];
									unset( $_post['items'][ $meta_key ][ $parent_item_id ] );
								}
							}

							/* === Calc Order Totals === */
							if ( ! empty( $_post['items']['line_total'][ $child_item_id ] ) ) {
								$order_total += wc_format_decimal( $_post['items']['line_total'][ $child_item_id ] );
								if ( isset( $_post['items']['line_tax'][ $child_item_id ] ) ) {
									$line_taxes = $_post['items']['line_tax'][ $child_item_id ];
									foreach ( $line_taxes as $line_tax ) {
										$order_total += wc_format_decimal( $line_tax );
									}
								}
							}

							/* === Calc Refund Totals === */
							if ( ! empty( $_post['items']['refund_line_total'][ $child_item_id ] ) ) {
								$order_total += wc_format_decimal( $_post['items']['refund_line_total'][ $child_item_id ] );
							}
							/* ======================== */
						}

						/* === Save Parent Meta === */

						$meta_keys      = isset( $_post['items']['meta_key'] ) ? $_post['items']['meta_key'] : array();
						$meta_values    = isset( $_post['items']['meta_value'] ) ? $_post['items']['meta_value'] : array();
						$order_item_ids = ! empty( $_post['items']['order_item_id'] ) ? $_post['items']['order_item_id'] : 0;

						if ( YITH_Vendors()->is_wc_2_7_or_greather && ! empty( $order_item_ids ) && is_array( $order_item_ids ) ) {
							foreach ( $order_item_ids as $order_item_id ) {
								if ( ! empty( $meta_keys[ $order_item_id ] ) && ! empty( $meta_values[ $order_item_id ] ) ) {
									self::save_parent_meta( $meta_keys[ $order_item_id ], $meta_values[ $order_item_id ] );
								}
							}

							if ( isset( $_post['items']['meta_key'] ) ) {
								unset( $_post['items']['meta_key'] );
							}
						} else {
							self::save_parent_meta( $meta_keys, $meta_values );
						}

						/* ======================== */

						// Add order total
						$_post['items']['_order_total'] = $order_total;

						// Save order items
						wc_save_order_items( $suborder_id, $_post['items'] );
					}
				} else {
					//is suborder
					do_action( 'yith_wcmv_save_suborder_items' );
				}
			}

		}

		/**
		 * Save parent meta
		 *
		 * @since WooCommerce 2.7
		 *
		 * @param $meta_keys
		 * @param $meta_values
		 * @param array $meta_to_exclude
		 */
		public static function save_parent_meta( $meta_keys, $meta_values, $meta_to_exclude = array() ) {
			$default_meta_to_exclude = apply_filters( 'yith_wcmv_order_item_meta_no_sync', array(
				'_child__commission_id',
				'_commission_included_tax',
				'_commission_included_coupon'
			) );
			$meta_to_exclude = array_merge( $default_meta_to_exclude, $meta_to_exclude );

			foreach ( $meta_keys as $meta_id => $meta_key ) {
				$meta_value           = ( empty( $meta_values[ $meta_id ] ) && ! is_numeric( $meta_values[ $meta_id ] ) ) ? '' : $meta_values[ $meta_id ];
				$parent_order_item_id = self::get_parent_item_id( false, $meta_id );
				$child_order_item_id  = self::get_child_item_id( $parent_order_item_id );

				if ( ! in_array( $meta_key, $meta_to_exclude ) ) {
					wc_update_order_item_meta( $child_order_item_id, '_parent_' . $meta_key, '_commission_id' != $meta_key ? $meta_id : $meta_value );
				}
			}
		}

		/**
		 * Remove order items ajax sync
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6
		 * @return void
		 * @access   public static
		 */
		public static function remove_order_item() {
			check_ajax_referer( 'order-item', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$order_item_ids = $_POST['order_item_ids'];
			if ( ! is_array( $order_item_ids ) && is_numeric( $order_item_ids ) ) {
				$order_item_ids = array( $order_item_ids );
			}
			//TODO: add check order_id if ( ! wp_get_post_parent_id( $parent_order_id ) ) {
			if ( sizeof( $order_item_ids ) > 0 ) {
				/** @var $wpdb wpdb */
				global $wpdb;
				foreach ( $order_item_ids as $order_item_id ) {
					$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->order_itemmeta} WHERE meta_key=%s AND order_item_id=%d", '_product_id', absint( $order_item_id ) ) );
					$vendor     = yith_get_vendor( $product_id, 'product' );
					if ( $vendor->is_valid() ) {
						$child_order_item_id = self::get_line_item_id_from_parent( $order_item_id );
						! empty( $child_order_item_id ) && wc_delete_order_item( absint( $child_order_item_id ) );
					}
				}
			}
		}

		/**
		 * Add WooCommerce order notes to suborder
		 *
		 * @since    1.6
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  void
		 */
		public static function add_order_note() {

			check_ajax_referer( 'add-order-note', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$post_id   = absint( $_POST['post_id'] );
			$note      = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
			$note_type = $_POST['note_type'];

			$is_customer_note = $note_type == 'customer' ? 1 : 0;

			if ( $post_id > 0 ) {
				if ( ! wp_get_post_parent_id( $post_id ) ) {
					//Add the order note to parent order
					$order          = wc_get_order( $post_id );
					$parent_note_id = $order->add_order_note( $note, $is_customer_note, true );

					echo '<li rel="' . esc_attr( $parent_note_id ) . '" class="note ';
					if ( $is_customer_note ) {
						echo 'customer-note';
					}
					echo '"><div class="note_content">';
					echo wpautop( wptexturize( $note ) );
					echo '</div><p class="meta"><a href="#" class="delete_note">' . __( 'Delete note', 'yith-woocommerce-product-vendors' ) . '</a></p>';
					echo '</li>';

					$suborder_ids = self::get_suborder( $post_id );
					if ( ! empty( $suborder_ids ) ) {
						foreach ( $suborder_ids as $suborder_id ) {
							$suborder = wc_get_order( $suborder_id );
							$note_id  = $suborder->add_order_note( _x( 'Updated by admin: ', 'Order note', 'yith-woocommerce-product-vendors' ) . $note, $is_customer_note, true );
							add_comment_meta( $note_id, 'parent_note_id', $parent_note_id );
						}
					}
					/**
					 * Call die(); to prevent WooCommerce action.
					 * Updated Parent and Child orders
					 */
					die();
				} else {
					//is suborder
					//TODO: Suborder sub-routine
				}
			}
		}

		/**
		 * Remove WooCommerce order notes to suborder
		 *
		 * @since    1.6
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return  void
		 */
		public static function delete_order_note() {
			check_ajax_referer( 'delete-order-note', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			global $wpdb;
			$parent_note_id = absint( $_POST['note_id'] );
			$note_ids       = $wpdb->get_col( $wpdb->prepare( "SELECT comment_id FROM {$wpdb->commentmeta} WHERE meta_key=%s  AND meta_value=%d", 'parent_note_id', $parent_note_id ) );

			if ( ! empty( $note_ids ) ) {
				foreach ( $note_ids as $note_id ) {
					wp_delete_comment( $note_id );
				}
			}
		}

		/**
		 * Reduce order item stock
		 */
		public static function reduce_order_item_stock() {
			self::order_item_stock( 'reduce' );
		}

		/**
		 * Increase order item stock
		 */
		public static function increase_order_item_stock() {
			self::order_item_stock( 'increase' );
		}

		/**
		 * Reduce order item stock
		 */
		public static function order_item_stock( $ajax_call_type ) {
			check_ajax_referer( 'order-item', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$order_id = absint( $_POST['order_id'] );
			if ( ! wp_get_post_parent_id( $order_id ) ) {
				$order_item_ids = isset( $_POST['order_item_ids'] ) ? $_POST['order_item_ids'] : array();
				$order_item_qty = isset( $_POST['order_item_qty'] ) ? $_POST['order_item_qty'] : array();
				$order          = wc_get_order( $order_id );
				$order_items    = $order->get_items();

				if ( $order && ! empty( $order_items ) && sizeof( $order_item_ids ) > 0 ) {

					foreach ( $order_items as $item_id => $order_item ) {
						// Only reduce checked items
						if ( ! in_array( $item_id, $order_item_ids ) ) {
							continue;
						}

						$_product = null;

						if ( YITH_Vendors()->is_wc_2_7_or_greather && is_callable( array(
								$order_item,
								'get_product'
							) )
						) {
							$_product = $order_item->get_product();
						} else {
							$_product = $order->get_product_from_item( $order_item );
						}

						$vendor = yith_get_vendor( $_product, 'product' );
						if ( $vendor->is_valid() && $_product->exists() && $_product->managing_stock() && isset( $order_item_qty[ $item_id ] ) && $order_item_qty[ $item_id ] > 0 ) {
							global $wpdb;

							$old_stock           = $_product->get_stock_quantity();
							$child_order_item_id = self::get_line_item_id_from_parent( $item_id );
							$suborder_id         = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id=%d", absint( $child_order_item_id ) ) );
							$suborder            = wc_get_order( $suborder_id );
							$note                = '';
							if ( 'reduce' == $ajax_call_type ) {
								$stock_change = apply_filters( 'woocommerce_reduce_order_stock_quantity', $order_item_qty[ $item_id ], $item_id );
								$new_stock    = $old_stock - $stock_change;
								$note         = sprintf( __( 'Item #%s stock reduced from %s to %s.', 'yith-woocommerce-product-vendors' ), $order_item['product_id'], $old_stock, $new_stock );
							} elseif ( 'increase' == $ajax_call_type ) {
								$stock_change = apply_filters( 'woocommerce_restore_order_stock_quantity', $order_item_qty[ $item_id ], $item_id );
								$new_stock    = $old_stock + $stock_change;
								$note         = sprintf( __( 'Item #%s stock increased from %s to %s.', 'yith-woocommerce-product-vendors' ), $order_item['product_id'], $old_stock, $new_stock );
							}

							! empty( $note ) && $suborder->add_order_note( $note );
						}
					}
				}
			} else {
				//is suborder
				//TODO: Suborder sub-routine
			}
		}

		/**
		 * Remove order item meta
		 */
		public static function remove_order_item_meta() {
			global $wpdb;

			check_ajax_referer( 'order-item', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$parent_meta_id  = absint( $_POST['meta_id'] );
			$parent_meta_key = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT meta_key FROM {$wpdb->order_itemmeta} WHERE meta_id=%d", $parent_meta_id ) );
			$child_meta_id   = $wpdb->get_var( $wpdb->prepare( "SELECT DISTINCT meta_id FROM {$wpdb->order_itemmeta} WHERE meta_value=%d AND meta_key=%s", $parent_meta_id, '_parent_' . $parent_meta_key ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->order_itemmeta} WHERE meta_key=%s AND meta_id=%d", '_parent_' . $parent_meta_key, $child_meta_id ) );
		}

		/**
		 * Add order item via ajax
		 */
		public static function add_order_item() {
			check_ajax_referer( 'order-item', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$item_to_add = array();

			if ( ! empty( $_POST['item_to_add'] ) ) {
				$item_to_add = $_POST['item_to_add'];
				if ( is_array( $item_to_add ) ) {
					$item_to_add = sanitize_text_field( array_shift( $item_to_add ) );
				} else {
					$item_to_add = sanitize_text_field( $item_to_add );
				}
			}

			$order_id = absint( $_POST['order_id'] );
			$vendor   = yith_get_vendor( $item_to_add, 'product' );

			if ( ! wp_get_post_parent_id( $order_id ) && ! empty( $item_to_add ) && $vendor->is_valid() && ! $vendor->is_super_user() ) {
				// Find the item
				if ( ! is_numeric( $item_to_add ) ) {
					die();
				}

				$post = get_post( $item_to_add );

				if ( ! $post || ( 'product' !== $post->post_type && 'product_variation' !== $post->post_type ) ) {
					die();
				}

				$_product     = wc_get_product( $item_to_add );
				$order        = wc_get_order( $order_id );
				$order_taxes  = $order->get_taxes();
				$class        = 'new_row';
				$suborders_id = 0;

				$vendor_suborder_id = $vendor->get_orders( 'suborder' );
				$suborders_ids      = self::get_suborder( $order_id );
				$suborder_id        = array_intersect( $vendor_suborder_id, $suborders_ids );

				if ( is_array( $suborder_id ) && count( $suborder_id ) == 1 ) {
					$suborder_id = array_shift( $suborder_id );
				}

				// Set values
				$item     = array();
				$item_ids = array();

				/**
				 * Product fields
				 */
				$_product_id                 = yit_get_base_product_id( $_product );
				$variation_id                = yit_get_prop( $_product, YITH_Vendors()->is_wc_2_7_or_greather ? 'id' : 'variation_id' );
				$product_price_excluding_tax = wc_format_decimal( yit_get_price_excluding_tax( $_product ) );
				$item['product_id']          = $_product_id;
				$item['variation_id']        = ! empty( $variation_id ) ? $variation_id : '';
				$item['variation_data']      = $item['variation_id'] ? $_product->get_variation_attributes() : '';
				$item['name']                = $_product->get_title();
				$item['tax_class']           = $_product->get_tax_class();
				$item['qty']                 = 1;
				$item['line_subtotal']       = $product_price_excluding_tax;
				$item['line_subtotal_tax']   = '';
				$item['line_total']          = $product_price_excluding_tax;
				$item['line_tax']            = '';
				$item['type']                = 'line_item';

				// Add line item
				foreach ( array( 'parent_id' => $order_id, 'child_id' => $suborder_id ) as $type => $id ) {
					$item_ids[ $type ] = wc_add_order_item( $id, array(
						'order_item_name' => $item['name'],
						'order_item_type' => 'line_item',
					) );
				}


				wc_add_order_item_meta( $item_ids['child_id'], '_parent_line_item_id', $item_ids['parent_id'] );

				foreach ( $item_ids as $key => $item_id ) {
					// Add line item meta
					if ( $item_id ) {
						wc_add_order_item_meta( $item_id, '_qty', $item['qty'] );
						wc_add_order_item_meta( $item_id, '_tax_class', $item['tax_class'] );
						wc_add_order_item_meta( $item_id, '_product_id', $item['product_id'] );
						wc_add_order_item_meta( $item_id, '_variation_id', $item['variation_id'] );
						wc_add_order_item_meta( $item_id, '_line_subtotal', $item['line_subtotal'] );
						wc_add_order_item_meta( $item_id, '_line_subtotal_tax', $item['line_subtotal_tax'] );
						wc_add_order_item_meta( $item_id, '_line_total', $item['line_total'] );
						wc_add_order_item_meta( $item_id, '_line_tax', $item['line_tax'] );

						// Since 2.2
						wc_add_order_item_meta( $item_id, '_line_tax_data', array(
							'total'    => array(),
							'subtotal' => array()
						) );

						// Store variation data in meta
						if ( $item['variation_data'] && is_array( $item['variation_data'] ) ) {
							foreach ( $item['variation_data'] as $key => $value ) {
								wc_add_order_item_meta( $item_id, str_replace( 'attribute_', '', $key ), $value );
							}
						}

						do_action( 'woocommerce_ajax_add_order_item_meta', $item_id, $item );
					}
				}

				$item['item_meta']       = wc_get_order_item_meta( $item_ids['parent_id'], '', false );
				$item['item_meta_array'] = $order->get_item_meta_array( $item_ids['parent_id'] );


				if ( YITH_Vendors()->is_wc_lower_2_6 ) {
					$item = $order->expand_item_meta( $item );
				}


				$item = apply_filters( 'woocommerce_ajax_order_item', $item, $item_ids['parent_id'] );

				/**
				 * WooCommerce Template Hack:
				 * Copy the parent item id into the variable $item_id
				 */
				$item_id = $item_ids['parent_id'];
				include( WC()->plugin_path() . '/includes/admin/meta-boxes/views/html-order-item.php' );

				/**
				 * Prevent call default WooCommerce add_order_item() method
				 */
				die();
			}
		}

		/**
		 * Add order tax column via ajax
		 */
		public static function add_order_tax() {
			check_ajax_referer( 'order-item', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$order_id = absint( $_POST['order_id'] );

			if ( ! wp_get_post_parent_id( $order_id ) ) {
				$rate_id      = absint( $_POST['rate_id'] );
				$suborder_ids = self::get_suborder( $order_id );

				foreach ( $suborder_ids as $suborder_id ) {
					$suborder = ! empty( $suborder_id ) ? wc_get_order( absint( $suborder_id ) ) : false;
					$suborder && $suborder->add_tax( $rate_id, 0, 0 );
				}
			} else {
				//is suborder
				//TODO: Suborder sub-routine
			}
		}

		/**
		 * Calc line tax
		 */
		public static function calc_line_taxes() {
			check_ajax_referer( 'calc-totals', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$order_id = absint( $_POST['order_id'] );

			if ( ! wp_get_post_parent_id( $order_id ) ) {
				$_post        = $_POST;
				$suborder_ids = self::get_suborder( $order_id );

				foreach ( $suborder_ids as $suborder_id ) {
					self::add_line_taxes( $suborder_id );
				}
			} else {
				//is suborder
				do_action( 'yith_wcmv_calc_suborder_line_taxes' );
			}
		}

		public static function add_line_taxes( $order_id ) {
			global $wpdb;

			check_ajax_referer( 'calc-totals', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$tax            = new WC_Tax();
			$items          = array();
			$country        = strtoupper( esc_attr( $_POST['country'] ) );
			$state          = strtoupper( esc_attr( $_POST['state'] ) );
			$postcode       = strtoupper( esc_attr( $_POST['postcode'] ) );
			$city           = wc_clean( esc_attr( $_POST['city'] ) );
			$order          = wc_get_order( absint( $order_id ) );
			$taxes          = array();
			$shipping_taxes = array();

			// Parse the jQuery serialized items
			parse_str( $_POST['items'], $items );

			// Prevent undefined warnings
			if ( ! isset( $items['line_tax'] ) ) {
				$items['line_tax'] = array();
			}

			if ( ! isset( $items['line_subtotal_tax'] ) ) {
				$items['line_subtotal_tax'] = array();
			}

			$items['order_taxes'] = array();

			// Action
			$items = apply_filters( 'woocommerce_ajax_calc_line_taxes', $items, $order_id, $country, $_POST );

			// Get items and fees taxes
			if ( isset( $items['order_item_id'] ) ) {
				$line_total = $line_subtotal = $order_item_tax_class = array();
				foreach ( $items['order_item_id'] as $parent_item_id ) {
					$parent_item_id = absint( $parent_item_id );
					$item_id        = self::get_child_item_id( $parent_item_id );

					if ( empty( $item_id ) ) {
						//no current suborder items
						continue;
					}

					$line_total[ $item_id ]           = isset( $items['line_total'][ $parent_item_id ] ) ? wc_format_decimal( $items['line_total'][ $parent_item_id ] ) : 0;
					$line_subtotal[ $item_id ]        = isset( $items['line_subtotal'][ $parent_item_id ] ) ? wc_format_decimal( $items['line_subtotal'][ $parent_item_id ] ) : $line_total[ $parent_item_id ];
					$order_item_tax_class[ $item_id ] = isset( $items['order_item_tax_class'][ $parent_item_id ] ) ? sanitize_text_field( $items['order_item_tax_class'][ $parent_item_id ] ) : '';
					$product_id                       = $parent_item_id = wc_get_order_item_meta( $item_id, '_product_id', true );

					$vendor = yith_get_vendor( $product_id, 'product' );

					if ( ! $vendor->is_valid() ) {
						// no vnedor products
						continue;
					}

					$vendor_order_ids = $vendor->get_orders( 'suborder' );

					if ( ! in_array( $order_id, $vendor_order_ids ) ) {
						// the current product isn't in the current suborder
						continue;
					}

					// Get product details
					if ( get_post_type( $product_id ) == 'product' ) {
						$_product        = wc_get_product( $product_id );
						$item_tax_status = $_product->get_tax_status();
					} else {
						$item_tax_status = 'taxable';
					}

					if ( '0' !== $order_item_tax_class[ $item_id ] && 'taxable' === $item_tax_status ) {
						$tax_rates = WC_Tax::find_rates( array(
							'country'   => $country,
							'state'     => $state,
							'postcode'  => $postcode,
							'city'      => $city,
							'tax_class' => $order_item_tax_class[ $item_id ],
						) );

						$line_taxes          = WC_Tax::calc_tax( $line_total[ $item_id ], $tax_rates, false );
						$line_subtotal_taxes = WC_Tax::calc_tax( $line_subtotal[ $item_id ], $tax_rates, false );

						// Set the new line_tax
						foreach ( $line_taxes as $_tax_id => $_tax_value ) {
							$items['line_tax'][ $item_id ][ $_tax_id ] = $_tax_value;
						}

						// Set the new line_subtotal_tax
						foreach ( $line_subtotal_taxes as $_tax_id => $_tax_value ) {
							$items['line_subtotal_tax'][ $item_id ][ $_tax_id ] = $_tax_value;
						}

						// Sum the item taxes
						foreach ( array_keys( $taxes + $line_taxes ) as $key ) {
							$taxes[ $key ] = ( isset( $line_taxes[ $key ] ) ? $line_taxes[ $key ] : 0 ) + ( isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0 );
						}
					}
				}
			}

			// Get shipping taxes
			if ( isset( $items['shipping_method_id'] ) ) {
				$matched_tax_rates = array();

				$tax_rates = WC_Tax::find_rates( array(
					'country'   => $country,
					'state'     => $state,
					'postcode'  => $postcode,
					'city'      => $city,
					'tax_class' => '',
				) );

				if ( $tax_rates ) {
					foreach ( $tax_rates as $key => $rate ) {
						if ( isset( $rate['shipping'] ) && 'yes' == $rate['shipping'] ) {
							$matched_tax_rates[ $key ] = $rate;
						}
					}
				}

				$shipping_cost = $shipping_taxes = array();

				foreach ( $items['shipping_method_id'] as $item_id ) {
					$item_id                   = absint( $item_id );
					$shipping_cost[ $item_id ] = isset( $items['shipping_cost'][ $parent_item_id ] ) ? wc_format_decimal( $items['shipping_cost'][ $parent_item_id ] ) : 0;
					$_shipping_taxes           = WC_Tax::calc_shipping_tax( $shipping_cost[ $item_id ], $matched_tax_rates );

					// Set the new shipping_taxes
					foreach ( $_shipping_taxes as $_tax_id => $_tax_value ) {
						$items['shipping_taxes'][ $item_id ][ $_tax_id ] = $_tax_value;

						$shipping_taxes[ $_tax_id ] = isset( $shipping_taxes[ $_tax_id ] ) ? $shipping_taxes[ $_tax_id ] + $_tax_value : $_tax_value;
					}
				}
			}

			// Remove old tax rows
			$order->remove_order_items( 'tax' );

			// Add tax rows
			foreach ( array_keys( $taxes + $shipping_taxes ) as $tax_rate_id ) {
				$order->add_tax( $tax_rate_id, isset( $taxes[ $tax_rate_id ] ) ? $taxes[ $tax_rate_id ] : 0, isset( $shipping_taxes[ $tax_rate_id ] ) ? $shipping_taxes[ $tax_rate_id ] : 0 );
			}

			// Create the new order_taxes
			foreach ( $order->get_taxes() as $tax_id => $tax_item ) {
				$items['order_taxes'][ $tax_id ] = absint( $tax_item['rate_id'] );
			}

			foreach ( $items as $meta_key => $meta_values ) {
				if ( is_array( $meta_values ) ) {
					foreach ( $meta_values as $key => $meta_value ) {
						if ( 'order_taxes' == $meta_key ) {
							continue;
						} else if ( 'order_item_id' == $meta_key ) {
							$child_item_id = self::get_child_item_id( $meta_value );
							if ( $child_item_id ) {
								$items[ $meta_key ][ $key ] = $child_item_id;
							} else {
								unset( $items[ $meta_key ][ $key ] );
							}
						} else if ( 'meta_key' == $meta_key || 'meta_value' == $meta_key ) {
							unset( $items[ $meta_key ][ $key ] );
						} else {
							if ( 'line_tax' == $meta_key || 'line_subtotal_tax' == $meta_key || 'refund_line_tax' == $meta_key ) {
								$line_tax_ids   = $items[ $meta_key ];
								$child_item_ids = array_keys( $order->get_items() );
								foreach ( $line_tax_ids as $line_tax_id => $line_tax_value ) {
									if ( ! in_array( $line_tax_id, $child_item_ids ) ) {
										unset( $items[ $meta_key ][ $line_tax_id ] );
									}
								}
							} else {
								$child_item_id = self::get_child_item_id( $meta_value );
								if ( $child_item_id ) {
									$items[ $meta_key ][ $child_item_id ] = $items[ $meta_key ][ $key ];
									unset( $items[ $meta_key ][ $key ] );
								}
							}
						}
					}
				} else if ( '_order_total' == $meta_key ) {
					$items['_order_total'] = $order->get_total();
				}
			}

			if ( ! empty( $items['order_item_id'] ) ) {
				wc_save_order_items( $order_id, $items );
			}
		}

		/**
		 * Remove an order tax
		 */
		public static function remove_order_tax() {

			check_ajax_referer( 'order-item', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$order_id = absint( $_POST['order_id'] );

			if ( ! wp_get_post_parent_id( $order_id ) ) {
				$rate_id              = absint( $_POST['rate_id'] );
				$parent_order         = wc_get_order( $order_id );
				$parent_taxes         = $parent_order->get_taxes();
				$suborder_ids         = self::get_suborder( $order_id );
				$parent_tax_to_remove = $parent_taxes[ $rate_id ];

				foreach ( $suborder_ids as $suborder_id ) {
					$suborder       = wc_get_order( $suborder_id );
					$suborder_taxes = $suborder->get_taxes();
					foreach ( $suborder_taxes as $suborder_tax_key => $suborder_tax_item ) {
						$suborder_tax_item['rate_id'] == $parent_tax_to_remove['rate_id']
						&&
						$suborder_tax_item['name'] == $parent_tax_to_remove['name']
						&&
						$suborder_tax_item['label'] == $parent_tax_to_remove['label']
						&&
						wc_delete_order_item( $suborder_tax_key );
					}
				}
			} else {
				//is suborder
				//TODO: Suborder sub-routine
			}
		}

		/**
		 * Prevent duplicated email for customer
		 */
		public function woocommerce_email_enabled_new_order( $enabled, $object ) {

			$is_editpost_action = ! empty( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], array(
					'editpost',
					'edit'
				) );

			if ( $is_editpost_action && ! empty( $_REQUEST['post_ID'] ) && wp_get_post_parent_id( $_REQUEST['post_ID'] ) == 0 && $_REQUEST['post_ID'] != yit_get_prop( $object, 'id' ) ) {
				return false;
			}

			$checksuborder = $object instanceof WC_Order && wp_get_post_parent_id( yit_get_prop( $object, 'id' ) ) != 0;

			if( ! empty( $_REQUEST['ywot_picked_up'] ) ){
				$checksuborder = ! $checksuborder;
			}
			return $enabled && $checksuborder ? false : $enabled;
		}

		/**
		 * Check for email recipient
		 */
		public function woocommerce_email_recipient_new_order( $recipient, $object ) {
			return ( $recipient == get_option( 'recipient' ) || $recipient == get_option( 'admin_email' ) ) && $object instanceof WC_Order && wp_get_post_parent_id( yit_get_prop( $object, 'id' ) ) ? false : $recipient;
		}

		/**
		 * Handle a refund via the edit order screen.
		 * Called after wp_ajax_woocommerce_refund_line_items action
		 *
		 * @use woocommerce_order_refunded action
		 * @see woocommerce\includes\class-wc-ajax.php:2295
		 */
		public function order_refunded( $order_id, $parent_refund_id ) {
			remove_action( 'woocommerce_order_refunded', array( $this, 'order_refunded' ), 10 );
			if ( ! wp_get_post_parent_id( $order_id ) ) {
				$create_refund           = true;
				$refund                  = false;
				$parent_line_item_refund = 0;
				$refund_amount           = wc_format_decimal( sanitize_text_field( $_POST['refund_amount'] ) );
				$refund_reason           = ! empty( $_POST['refund_reason'] ) ? sanitize_text_field( $_POST['refund_reason'] ) : '';
				$line_item_qtys          = ! empty( $_POST['line_item_qtys'] ) ? json_decode( sanitize_text_field( stripslashes( $_POST['line_item_qtys'] ) ), true ) : array();
				$line_item_totals        = ! empty( $_POST['line_item_totals'] ) ? json_decode( sanitize_text_field( stripslashes( $_POST['line_item_totals'] ) ), true ) : array();
				$line_item_tax_totals    = ! empty( $_POST['line_item_tax_totals'] ) ? json_decode( sanitize_text_field( stripslashes( $_POST['line_item_tax_totals'] ) ), true ) : array();
				$api_refund              = ! empty( $_POST['api_refund'] ) && $_POST['api_refund'] === 'true' ? true : false;
				$restock_refunded_items  = ! empty( $_POST['restock_refunded_items'] ) && $_POST['restock_refunded_items'] === 'true' ? true : false;
				$order                   = wc_get_order( $order_id );
				$parent_order_total      = wc_format_decimal( $order->get_total() );
				$suborder_ids            = self::get_suborder( $order_id );

				//calculate line items total from parent order
				foreach ( $line_item_totals as $item_id => $total ) {
					$parent_line_item_refund += wc_format_decimal( $total );
				}

				foreach ( $suborder_ids as $suborder_id ) {
					$suborder               = wc_get_order( $suborder_id );
					$suborder_items_ids     = array_keys( $suborder->get_items( array( 'line_item', 'shipping' ) ) );
					$suborder_total         = wc_format_decimal( $suborder->get_total() );
					$max_refund             = wc_format_decimal( $suborder_total - $suborder->get_total_refunded() );
					$child_line_item_refund = 0;

					// Prepare line items which we are refunding
					$line_items = array();
					$item_ids   = array_unique( array_merge( array_keys( $line_item_qtys ), array_keys( $line_item_totals ) ) );

					foreach ( $item_ids as $item_id ) {
						$child_item_id = self::get_child_item_id( $item_id );
						if ( $child_item_id && in_array( $child_item_id, $suborder_items_ids ) ) {
							$line_items[ $child_item_id ] = array(
								'qty'          => 0,
								'refund_total' => 0,
								'refund_tax'   => array()
							);
						}
					}

					foreach ( $line_item_qtys as $item_id => $qty ) {
						$child_item_id = self::get_child_item_id( $item_id );
						if ( $child_item_id && in_array( $child_item_id, $suborder_items_ids ) ) {
							$line_items[ $child_item_id ]['qty'] = max( $qty, 0 );
						}
					}

					foreach ( $line_item_totals as $item_id => $total ) {
						$child_item_id = self::get_child_item_id( $item_id );
						if ( $child_item_id && in_array( $child_item_id, $suborder_items_ids ) ) {
							$total = wc_format_decimal( $total );
							$child_line_item_refund += $total;
							$line_items[ $child_item_id ]['refund_total'] = $total;
						}
					}

					foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
						$child_item_id = self::get_child_item_id( $item_id );
						if ( $child_item_id && in_array( $child_item_id, $suborder_items_ids ) ) {
							$line_items[ $child_item_id ]['refund_tax'] = array_map( 'wc_format_decimal', $tax_totals );
						}
					}

					//calculate refund amount percentage
					$suborder_refund_amount = ( ( ( $refund_amount - $parent_line_item_refund ) * $suborder_total ) / $parent_order_total );
					$suborder_total_refund  = wc_format_decimal( $child_line_item_refund + $suborder_refund_amount );

					if ( ! $refund_amount || $max_refund < $suborder_total_refund || 0 > $suborder_total_refund ) {
						/**
						 * Invalid refund amount.
						 * Check if suborder total != 0 create a partial refund, exit otherwise
						 */
						$surplus               = wc_format_decimal( $suborder_total_refund - $max_refund );
						$suborder_total_refund = $suborder_total_refund - $surplus;
						$create_refund         = $suborder_total_refund > 0 ? true : false;
					}

					if ( $create_refund ) {

						// Create the refund object
						$refund = wc_create_refund( array(
								'amount'     => $suborder_total_refund,
								'reason'     => $refund_reason,
								'order_id'   => $suborder->get_id(),
								'line_items' => $line_items,
							)
						);

						$refund->add_meta_data( '_parent_refund_id', $parent_refund_id , true );
						$refund->save_meta_data();
					}
				}
			}
			add_action( 'woocommerce_order_refunded', array( $this, 'order_refunded' ), 10, 2 );
		}

		/**
		 * Handle a refund via the edit order screen.
		 * Called after wp_ajax_woocommerce_delete_refund action
		 *
		 * @use woocommerce_refund_deleted action
		 * @see woocommerce\includes\class-wc-ajax.php:2328
		 */
		public static function refund_deleted( $refund_id, $parent_order_id ) {
			check_ajax_referer( 'order-item', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			if ( ! wp_get_post_parent_id( $parent_order_id ) ) {
				global $wpdb;
				$child_refund_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key=%s AND meta_value=%s", '_parent_refund_id', $refund_id ) );

				foreach ( $child_refund_ids as $child_refund_id ) {
					if ( $child_refund_id && 'shop_order_refund' === get_post_type( $child_refund_id ) ) {
						$order_id = wp_get_post_parent_id( $child_refund_id );
						$deleted = YITH_Commissions()->delete_commission_refund( $child_refund_id, $order_id, $parent_order_id );
						wc_delete_shop_order_transients( $order_id );
						wp_delete_post( $child_refund_id );
					}
				}
			}
		}

		/**
		 * Change commission label value
		 *
		 * @param           $attribute_label  The Label Value
		 * @param           $meta_key         The Meta Key value
		 * @param bool|\The $product The Product object
		 *
		 * @return string           The label value
		 */
		public function commissions_attribute_label( $attribute_label, $meta_key, $product = false ) {
			global $pagenow;

			$order = ! empty( $_GET['post'] ) ? wc_get_order( $_GET['post'] ) : false;
			$order = apply_filters( 'yith_wcmv_commissions_attribute_label_order_object', $order );

			$is_edit_order_page = apply_filters( 'yith_wcmv_commissions_attribute_label_is_edit_order_page', isset( $_GET['post'] ) && 'shop_order' == get_post_type( $_GET['post'] ) && 'post.php' == $pagenow );

			if ( $is_edit_order_page && $order ) {
				/**
				 * remove_filter for WPML Compatibility
				 */
				remove_filter( 'woocommerce_attribute_label', array( $this, 'commissions_attribute_label' ), 10 );
				$line_items          = $order->get_items( 'line_item' );
				add_filter( 'woocommerce_attribute_label', array( $this, 'commissions_attribute_label' ), 10, 3 );
				$item_meta_key       = wp_get_post_parent_id( $order->get_id() ) ? '_commission_id' : '_child__commission_id';
				$is_variable_product = $product instanceof WC_Product_Variation;

				foreach ( $line_items as $line_item_id => $line_item ) {
					$check = false;
					$commission_id = 0;

					if ( $is_variable_product ) {
						$product_id = YITH_Vendors()->is_wc_2_7_or_greather ? $product->get_id() : $product->get_variation_id();
						$check = $line_item['variation_id'] == $product_id;
					}

					elseif ( $product instanceof WC_Product ) {
						$check = $line_item['product_id'] == $product->get_id();
					}

					else {
						foreach( $order->get_shipping_methods() as $shipping_item_id => $shipping_items ){
							$commission_id = wc_get_order_item_meta( $shipping_item_id, $meta_key, true );
							if( $commission_id ){
								$check = true;
								break;
							}
						}
					}

					if ( $check ) {
						$commission_id = ! empty( $commission_id ) ? $commission_id : wc_get_order_item_meta( $line_item_id, $item_meta_key, true );
						$commission    = YITH_Commission( $commission_id );
						$admin_url     = apply_filters( 'yith_wcmv_commissions_list_table_commission_url', $commission->get_view_url( 'admin' ), $commission );

						$url_attribute_label = sprintf(
							"<a href='%s' class='%s'>%s</a> <small>(%s: <strong>%s</strong>)</small>",
							$admin_url,
							'commission-id-label',
							__( 'commission_id', 'yith-woocommerce-product-vendors' ),
							__( 'status', 'yith-woocommerce-product-vendors' ),
							strtolower( $commission->get_status( 'display' ) )
						);
						$attribute_label     = $item_meta_key == $meta_key ? $url_attribute_label : $attribute_label;
					}
				}
			}

			return $attribute_label;
		}

		/**
		 * Filters meta to hide, to add to the list item order meta added by author class
		 *
		 * @param $to_hidden Array of order_item_meta meta_key to hide
		 *
		 * @return array New array of order item meta to hide
		 * @since    1.0
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function hidden_order_itemmeta( $to_hidden ) {
			if ( apply_filters( 'yith_show_commissions_order_item_meta', YITH_Commissions()->show_order_item_meta )
			     &&
			     ( ! defined( 'WP_DEBUG' ) || ( defined( 'WP_DEBUG' ) && ! WP_DEBUG ) )
			) {
				$to_hidden[] = '_parent_line_item_id';
				$to_hidden[] = '_commission_included_tax';
				$to_hidden[] = '_commission_included_coupon';
			}

			return $to_hidden;
		}

		/**
		 * Add and reorder order table column
		 *
		 * @param $order_columns The order table column
		 *
		 * @return string           The label value
		 */
		public function shop_order_columns( $order_columns ) {
			$vendor = yith_get_vendor( 'current', 'user' );
			$order_number_col_name = YITH_Vendors()->is_wc_3_3_or_greather ? 'order_number' : 'order_title';

			if ( $vendor->is_super_user() ) {
				if ( ( ! isset( $_GET['post_status'] ) || ( isset( $_GET['post_status'] ) && 'trash' != $_GET['post_status'] ) ) ) {
					$suborder      = array( 'suborder' => _x( 'Suborders', 'Admin: Order table column', 'yith-woocommerce-product-vendors' ) );
					$ref_pos       = array_search( $order_number_col_name, array_keys( $order_columns ) );
					$order_columns = array_slice( $order_columns, 0, $ref_pos + 1, true ) + $suborder + array_slice( $order_columns, $ref_pos + 1, count( $order_columns ) - 1, true );
				}

				else {
					$vendor        = array( 'vendor' => _x( 'Vendor', 'Admin: Order table column', 'yith-woocommerce-product-vendors' ) );
					$ref_pos       = array_search( $order_number_col_name, array_keys( $order_columns ) );
					$order_columns = array_slice( $order_columns, 0, $ref_pos + 1, true ) + $vendor + array_slice( $order_columns, $ref_pos + 1, count( $order_columns ) - 1, true );
				}
			}

			else {
				if ( ( ! isset( $_GET['post_status'] ) || ( isset( $_GET['post_status'] ) && 'trash' != $_GET['post_status'] ) ) ) {
					$suborder      = array( 'parent_order' => _x( 'Parent Order', 'Admin: Order table column', 'yith-woocommerce-product-vendors' ) );
					$ref_pos       = array_search( $order_number_col_name, array_keys( $order_columns ) );
					$order_columns = array_slice( $order_columns, 0, $ref_pos + 1, true ) + $suborder + array_slice( $order_columns, $ref_pos + 1, count( $order_columns ) - 1, true );
				}
			}

			return $order_columns;
		}

		/**
		 * Output custom columns for coupons
		 *
		 * @param  string $column
		 */
		public function render_shop_order_columns( $column, $order = false ) {
			global $post, $the_order;

			if ( ! empty( $order ) ) {
				$_the_order = $order;
			} else if ( empty( $the_order ) || yit_get_prop( $the_order, 'id' ) != $post->ID ) {
				$_the_order = wc_get_order( $post->ID );
			} else {
				$_the_order = $the_order;
			}

			$_the_order_id = yit_get_prop( $_the_order, 'id' );

			switch ( $column ) {
				case 'parent_order':
					$parent_order_id = wp_get_post_parent_id( $_the_order_id );

					if ( $parent_order_id ) {
						$parent_order = wc_get_order( $parent_order_id );
						printf(
							'<strong>#%s</strong>',
							esc_html( $parent_order->get_order_number() )
						);

						do_action( 'yith_wcmv_after_parent_order_details', $parent_order );

					} else {
						echo '<span class="na">&ndash;</span>';
					}
					break;
				case 'suborder' :
					$suborder_ids = self::get_suborder( $_the_order_id );

					if ( $suborder_ids ) {
						foreach ( $suborder_ids as $suborder_id ) {
							$suborder          = wc_get_order( $suborder_id );
							$vendor            = yith_get_vendor( get_post_field( 'post_author', $suborder_id ), 'user' );
							$order_uri         = apply_filters( 'yith_wcmv_edit_order_uri', esc_url( 'post.php?post=' . absint( $suborder_id ) . '&action=edit' ), absint( $suborder_id ) );
							$order_status_name = wc_get_order_status_name( $suborder->get_status() );

							printf( '<mark class="%s tips" data-tip="%s">%s</mark> <strong><a href="%s">#%s</a></strong> <small class="yith-wcmv-suborder-owner">(%s %s)</small>',
								esc_attr( $suborder->get_status() ),
								$order_status_name,
								$order_status_name,
								$order_uri,
								$suborder->get_order_number(),
								_x( 'in', 'Order table details', 'yith-woocommerce-product-vendors' ),
								$vendor->name
							);

							do_action( 'yith_wcmv_after_suborder_details', $suborder );
						}
					} else {
						echo '<span class="na">&ndash;</span>';
					}

					break;

				case 'vendor':
					$order_author_id = get_post_field( 'post_author', $_the_order_id );
					$vendor          = yith_get_vendor( $order_author_id, 'user' );
					if ( $vendor->is_valid() ) {
						printf( '<a href="%s">%s</a>', $vendor->get_url( 'admin' ), $vendor->name );
					} else {
						echo '<span class="na">&ndash;</span>';
					}
					break;
			}
		}

		/**
		 * Add suborder metaboxes for Vendors order
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function add_meta_boxes() {
			if ( 'shop_order' != get_current_screen()->id ) {
				return;
			}

			global $post;
			$vendor       = yith_get_vendor( 'current', 'user' );
			$has_suborder = self::get_suborder( absint( $post->ID ) );
			$is_suborder  = wp_get_post_parent_id( absint( $post->ID ) );

			if ( $vendor->is_super_user() ) {
				if ( $has_suborder ) {
					$metabox_suborder_description = _x( 'Suborders', 'Admin: Single order page. Suborder details box', 'yith-woocommerce-product-vendors' ) . ' <span class="tips" data-tip="' . esc_attr__( 'Note: from this box you can monitor the status of suborders associated to individual vendors.', 'yith-woocommerce-product-vendors' ) . '">[?]</span>';
					add_meta_box( 'woocommerce-suborders', $metabox_suborder_description, array(
						$this,
						'output'
					), 'shop_order', 'side', 'core', array( 'metabox' => 'suborders' ) );
				} else if ( $is_suborder ) {
					$metabox_parent_order_description = _x( 'Parent order', 'Admin: Single order page. Parent order details box', 'yith-woocommerce-product-vendors' );
					add_meta_box( 'woocommerce-parent-order', $metabox_parent_order_description, array(
						$this,
						'output'
					), 'shop_order', 'side', 'high', array( 'metabox' => 'parent-order' ) );
				}
			} elseif ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				//@since 2.0.2
				$order_id = wp_get_post_parent_id( absint( $post->ID ) );
				$order = wc_get_order( $order_id );
				if( $order instanceof WC_Order ){
					$metabox_parent_order_description = sprintf( '%s: <em>#%s</em>', _x( 'Parent order id', 'Admin: Single order page. Parent order details box', 'yith-woocommerce-product-vendors' ), $order->get_order_number() );
					add_meta_box( 'woocommerce-parent-order', $metabox_parent_order_description, array( $this, 'output' ), 'shop_order', 'side', 'high', array( 'metabox' => 'vendor' ) );
				}
			}
		}

		/**
		 * Output the suborder metaboxes
		 *
		 * @param $post     The post object
		 * @param $param    Callback args
		 *
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function output( $post, $param ) {
			switch ( $param['args']['metabox'] ) {
				case 'suborders':
					$suborder_ids = self::get_suborder( absint( $post->ID ) );
					echo '<ul class="suborders-list single-orders">';
					foreach ( $suborder_ids as $suborder_id ) {
						$suborder     = wc_get_order( absint( $suborder_id ) );
						$order_author = get_post_field( 'post_author', $suborder_id );
						$vendor       = yith_get_vendor( $order_author, 'user' );
						$suborder_uri = esc_url( 'post.php?post=' . absint( $suborder_id ) . '&action=edit' );
						echo '<li class="suborder-info">';
						printf( '<mark class="%s tips" data-tip="%s">%s</mark> <strong><a href="%s">#%s</a></strong> <small class="single-order yith-wcmv-suborder-owner">%s %s</small><br/>',
							sanitize_title( $suborder->get_status() ),
							wc_get_order_status_name( $suborder->get_status() ),
							wc_get_order_status_name( $suborder->get_status() ),
							$suborder_uri,
							$suborder->get_order_number(),
							$vendor->is_valid() ? _x( 'in', 'Order table details', 'yith-woocommerce-product-vendors' ) : '-',
							$vendor->is_valid() ? $vendor->name : __( 'Vendor deleted', 'yith-woocommerce-product-vendors' )
						);
						echo '<li>';
						do_action( 'yith_wcmv_after_suborder_vendor_info', $suborder, $vendor );
					}
					echo '</ul>';
					break;

				case 'parent-order':
					$parent_order_id  = wp_get_post_parent_id( absint( $post->ID ) );
					$parent_order_uri = esc_url( 'post.php?post=' . absint( $parent_order_id ) . '&action=edit' );
					printf( '<a href="%s">&#8592; %s</a>', $parent_order_uri, _x( 'Return to main order', 'Admin: single order page. Link to parent order', 'yith-woocommerce-product-vendors' ) );
					break;

				case 'vendor':
					//@since 2.0.2
					_e( 'Pass this ID over to the website administrator for any communication related to this order', 'yith-woocommerce-product-vendors' );
					break;
			}
		}

		/**
		 * Retrieve all items from an order, grouping all by vendor
		 *
		 * @param int $parent_order_id the parent order id
		 * @param array $args additional parameters
		 *
		 * @return array
		 * @author Lorenzo Giuffrida
		 * @since  1.6.0
		 */
		public static function get_order_items_by_vendor( $parent_order_id, $args = array() ) {

			/**
			 * Define the array of defaults
			 */
			$defaults = array(
				'hide_no_vendor'        => false,
				'hide_without_shipping' => false,
			);

			/**
			 * Parse incoming $args into an array and merge it with $defaults
			 */
			$args = wp_parse_args( $args, $defaults );

			$parent_order      = wc_get_order( $parent_order_id );
			$items             = $parent_order->get_items();
			$product_by_vendor = array();

			//check for vendor product
			foreach ( $items as $item_id => $item ) {
				$vendor = yith_get_vendor( $item['product_id'], 'product' );

				$vendor_id = 0;
				if ( $vendor->is_valid( $vendor ) ) {
					$vendor_id = $vendor->id;
				}

				//  optionally skip product without vendor
				if ( $args["hide_no_vendor"] && ! $vendor_id ) {
					continue;
				}

				//  optionally skip product without ship
				if ( $args["hide_without_shipping"] ) {
					$product_id = $item["product_id"];
					if ( 0 != $item["variation_id"] ) {
						$product_id = $item["variation_id"];
					}

					$product = wc_get_product( $product_id );
					if ( ! $product->needs_shipping() ) {
						continue;
					}
				}

				$product_by_vendor[ $vendor_id ][ $item_id ] = $item;
			}

			return $product_by_vendor;
		}

		/**
		 * Check if the current page is an order details page for vendor
		 *
		 * @param mixed $vendor The vendor object
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6.0
		 * @return   bool
		 */
		public function is_vendor_order_page( $vendor = false ) {
			if ( ! $vendor ) {
				$vendor = yith_get_vendor( 'current', 'user' );
			}
			$is_ajax          = defined( 'DOING_AJAX' ) && DOING_AJAX;
			$current_screen   = get_current_screen();
			$is_order_details = is_admin() && ! is_null( $current_screen ) && 'edit-shop_order' == $current_screen->id;

			return apply_filters( 'yith_wcmv_is_vendor_order_page', $vendor->is_valid() && $vendor->has_limited_access() && $is_order_details && ! $is_ajax );
		}

		/**
		 * Check if the current page is an order details page for vendor
		 *
		 * @param mixed $vendor The vendor object
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    1.6.0
		 * @return   bool
		 */
		public function is_vendor_order_details_page( $vendor = false ) {
			global $theorder;
			if ( ! $vendor ) {
				$vendor = yith_get_vendor( 'current', 'user' );
			}
			$is_ajax          = defined( 'DOING_AJAX' ) && DOING_AJAX;
			$is_order_details = is_admin() && 'shop_order' == get_current_screen()->id;

			return apply_filters( 'yith_wcmv_is_vendor_order_details_page', $vendor->is_valid() && $vendor->has_limited_access() && $is_order_details && ! $is_ajax );
		}

		/**
		 * Only show vendor's order
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 *
		 * @param  arr $request Current request
		 *
		 * @return arr          Modified request
		 * @since  1.6
		 */
		public function vendor_order_list( $query ) {
			$vendor = yith_get_vendor( 'current', 'user' );

			if ( is_admin() && $vendor->is_valid() && $vendor->has_limited_access() ) {
				//Remove Exclude Order Comments to vendor admin dashboard
				remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

				$suborders = $vendor->get_orders( 'suborder' );
				$quotes    = array();

				if ( 'no' == get_option( 'yith_wpv_vendors_enable_request_quote', 'no' ) && ! empty( YITH_Vendors()->addons ) && YITH_Vendors()->addons->has_plugin( 'request-quote' ) ) {
					$quotes = $vendor->get_orders( 'quote', YITH_YWRAQ_Order_Request()->raq_order_status );
				}

				$query['post__in'] = ! empty( $quotes ) ? array_diff( $suborders, $quotes ) : $suborders;
				$query['author']   = absint( $vendor->get_owner() );


				if( ! empty( $query['s'] ) ){
					$query['s'] = esc_attr( $query['s'] );
					$parent_order_ids = self::get_suborder( $query['s'] );


					if( ! empty( $parent_order_ids ) ){

						$query['post_parent'] = $query['s'];
						$query['s'] = '';
					}
				}

				/**
				 * YITH Deposits and down payments support
				 */
				if ( isset( $query['post_parent'] ) && $query['post_parent'] == 0 ) {
					unset( $query['post_parent'] );
				}
			}

			return apply_filters('yith_mv_order_list_query_args' ,$query );
		}

		public function check_awaiting_payment( $posted ) {
			// Insert or update the post data
			$order_id = absint( WC()->session->order_awaiting_payment );

			// Resume the unpaid order if its pending
			if ( $order_id > 0 && ( $order = wc_get_order( $order_id ) ) && $order->has_status( array(
					'pending',
					'failed'
				) )
			) {
				$suborder_ids = $this->get_suborder( $order_id );
				YITH_Commissions()->bulk_action( $suborder_ids, 'delete' );

				foreach ( $suborder_ids as $suborder_id ) {
					wc_delete_shop_order_transients( $suborder_id );
					wp_delete_post( $suborder_id, true );
				}
			}
		}

		/**
		 * Remove item meta on permanent deletion.
		 */
		public function delete_order_items( $postid ) {
			global $wpdb;

			if ( in_array( get_post_type( $postid ), wc_get_order_types() ) && wp_get_post_parent_id( $postid ) != 0 ) {
				$wpdb->query( "
				DELETE {$wpdb->prefix}woocommerce_order_items, {$wpdb->prefix}woocommerce_order_itemmeta
				FROM {$wpdb->prefix}woocommerce_order_items
				JOIN {$wpdb->prefix}woocommerce_order_itemmeta ON {$wpdb->prefix}woocommerce_order_items.order_item_id = {$wpdb->prefix}woocommerce_order_itemmeta.order_item_id
				WHERE {$wpdb->prefix}woocommerce_order_items.order_id = '{$postid}';
				" );
			}
		}

		/**
		 * Remove downloadable permissions on permanent order deletion.
		 */
		public function delete_order_downloadable_permissions( $postid ) {
			global $wpdb;

			if ( in_array( get_post_type( $postid ), wc_get_order_types() ) && wp_get_post_parent_id( $postid ) != 0 ) {

				$wpdb->query( $wpdb->prepare( "
				DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
				WHERE order_id = %d
			", $postid ) );
			}
		}

		/**
		 * Trashed parent order sync
		 */
		public function trash_suborder( $order_id ) {
			if ( wp_get_post_parent_id( $order_id ) == 0 ) {
				$suborder_ids = $this->get_suborder( $order_id );
				if ( ! empty( $suborder_ids ) ) {
					foreach ( $suborder_ids as $suborder_id ) {
						wp_trash_post( $suborder_id );
					}
				}
			}
		}

		public function skip_stripe_charge_for_suborders( $skip, $order_id ) {
			if ( wp_get_post_parent_id( $order_id ) != 0 ) {
				$skip = false;
			}

			return $skip;
		}

		public function revoke_access_to_product_download( $download_id, $product_id, $order_id ) {

			check_ajax_referer( 'revoke-access', 'security' );


			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}

			$parent_order_id  = 0;
			$current_order_id = 0;
			if ( wp_get_post_parent_id( $order_id ) ) {
				$order           = wc_get_order( $order_id );
				$parent_order_id = get_post_field( 'post_parent', $order_id );
			}

			global $wpdb;

			if ( $parent_order_id == 0 ) {
				$suborders     = self::get_suborder( $order_id );
				$vendor        = yith_get_vendor( $product_id, 'product' );
				$vendor_orders = $vendor->get_orders();
				$suborder_id   = array_intersect( $vendor_orders, $suborders );

				if ( count( $suborder_id ) == 1 ) {

					$current_order_id = implode( '', $suborder_id );
				}

			} else {
				$current_order_id = $parent_order_id;
			}

			$query = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s;", $current_order_id, $product_id, $download_id );
			$wpdb->query( $query );

			die();
		}

		public static function grant_access_to_download() {


			check_ajax_referer( 'grant-access', 'security' );

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				die( - 1 );
			}


			global $wpdb;

			$wpdb->hide_errors();

			$order_id        = intval( $_POST['order_id'] );
			$product_ids     = $_POST['product_ids'];
			$loop            = intval( $_POST['loop'] );
			$file_counter    = 0;
			$parent_order_id = 0;

			if ( wp_get_post_parent_id( $order_id ) ) {
				$order           = wc_get_order( $order_id );
				$parent_order_id = get_post_field( 'post_parent', $order_id );
			}

			$suborders = self::get_suborder( $order_id );

			if ( ! is_array( $product_ids ) ) {
				$product_ids = array( $product_ids );
			}

			foreach ( $product_ids as $product_id ) {

				$product       = wc_get_product( $product_id );
				$get_downloads = YITH_Vendors()->is_wc_2_7_or_greather ? 'get_downloads' : 'get_files';
				$files         = $product->$get_downloads();

				if ( $parent_order_id == 0 ) {
					$vendor        = yith_get_vendor( $product_id, 'product' );
					$vendor_orders = $vendor->get_orders();
					$suborder_id   = array_intersect( $vendor_orders, $suborders );

					if ( count( $suborder_id ) == 1 ) {
						$suborder_id = implode( '', $suborder_id );
						$order       = wc_get_order( $suborder_id );
					}
				} else {
					$order = wc_get_order( $parent_order_id );
				}

				$billing_email = ! empty( $order ) ? yit_get_prop( $order, 'billing_email' ) : false;

				if ( ! $billing_email ) {
					return;
				}


				if ( ! empty( $files ) ) {
					foreach ( $files as $download_id => $file ) {
						if ( $inserted_id = wc_downloadable_file_permission( $download_id, $product_id, $order ) ) {

							// insert complete - get inserted data
							$download = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE permission_id = %d", $inserted_id ) );

							$loop ++;
							$file_counter ++;

							if ( isset( $file['name'] ) ) {
								$file_count = $file['name'];
							} else {
								$file_count = sprintf( __( 'File %d', 'yith-woocommerce-product-vendors' ), $file_counter );
							}
							//   include( WC()->plugin_path().'/includes/admin/meta-boxes/views/html-order-download-permission.php' );
						}
					}
				}
			}
		}

		/**
		 * Checks if an order needs display the shipping address, based on shipping method.
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return boolean
		 */
		public function order_needs_shipping_address( $needs_address, $hide, $order ) {
			/** @var WC_Order $order */
			$raq_order_meta = $order->get_meta( 'ywraq_raq' );
			$is_quote       = ! empty( $raq_order_meta );

			$post_parent = get_post_field( 'post_parent', $order->get_id() );

			if ( $post_parent && ! $is_quote ) {
				$parent_order = wc_get_order( $post_parent );

				$shipping_enabled = function_exists( 'wc_shipping_enabled' ) ? wc_shipping_enabled() : 'yes' == get_option( 'woocommerce_calc_shipping' );

				if ( ! $shipping_enabled ) {
					return false;
				}

				$hide          = apply_filters( 'woocommerce_order_hide_shipping_address', array( 'local_pickup' ), $this );
				$needs_address = false;

				foreach ( $parent_order->get_shipping_methods() as $shipping_method ) {
					if ( ! in_array( $shipping_method['method_id'], $hide ) ) {
						$needs_address = true;
						break;
					}
				}
			}

			return $needs_address;
		}

		/**
		 * @param $tax_rate_id
		 * @param int $tax_amount
		 * @param int $shipping_tax_amount
		 */
		public function add_tax( $order, $tax_rate_id, $tax_amount = 0, $shipping_tax_amount = 0 ) {
			if ( YITH_Vendors()->is_wc_2_7_or_greather ) {
				$item = new WC_Order_Item_Tax();
				$item->set_props( array(
					'rate_id'            => $tax_rate_id,
					'tax_total'          => $tax_amount,
					'shipping_tax_total' => $shipping_tax_amount,
				) );
				$item->set_rate( $tax_rate_id );
				$item->set_order_id( $order->get_id() );
				$item->save();
				$order->add_item( $item );
			} else {
				$order->add_tax( $tax_rate_id, $tax_amount, $shipping_tax_amount );
			}
		}

		/**
		 * Wrapper to add shipping to suborder.
		 *
		 * @param WC_Order $order Vendor suborder object
		 * @param WC_Shipping_Rate $shipping_rate Shipping rate object from cart
		 * @param string $string Vendor ID
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since   1.6
		 * @return  int shipping item id
		 */
		public function add_shipping( $order, $shipping_rate, $vendor_id ) {
			$shipping_item_id = 0;

			if ( YITH_Vendors()->is_wc_2_7_or_greather ) {
				$item = new WC_Order_Item_Shipping();
				$item->set_props( array(
					'method_title' => $shipping_rate->label,
					'method_id'    => $shipping_rate->id,
					'total'        => wc_format_decimal( $shipping_rate->cost ),
					'taxes'        => $shipping_rate->taxes,
					'order_id'     => $order->get_id(),
				) );

				foreach ( $shipping_rate->get_meta_data() as $key => $value ) {
					$item->add_meta_data( $key, $value, true );
				}

				$item->add_meta_data( '_vendor_package_id', $shipping_rate->id, true );
				$item->add_meta_data( 'vendor_id', $vendor_id, true );

				$parent_order_id = wp_get_post_parent_id( $order->get_id() );
				$parent_order    = wc_get_order( $parent_order_id );

				if( ! empty( $parent_order ) && $parent_order instanceof WC_Order ){
					foreach( $parent_order->get_items( 'shipping' ) as $shipping_item ){
						$vendor_package_id  = $shipping_item->get_meta( '_vendor_package_id', true, 'edit' );
						$vendor_parent_id   = $shipping_item->get_meta( 'vendor_id', true, 'edit' );

						if( $vendor_package_id == $shipping_rate->id && $vendor_parent_id == $vendor_id ){
							$item->add_meta_data( '_parent_line_item_id', $shipping_item->get_id(), true );
						}
					}
				}

				$item->save();
				$order->add_item( $item );
				$shipping_item_id = $item->get_id();
			} else {
				$shipping_item_id = $order->add_shipping( $shipping_rate );
			}


			return $shipping_item_id;
		}

		/**
		 * Update total sales amount for each product within a paid order.
		 *
		 * @param int $order_id
		 *
		 * @author  Andrea Grillo <andrea.grillo@yithemes.com>
		 * @return void
		 */
		public function recorded_sales_hack( $order_id ){
			if( wp_get_post_parent_id( $order_id ) ){
				$order = wc_get_order( $order_id );
				if ( $order instanceof WC_Order && sizeof( $order->get_items() ) > 0 ) {
					foreach ( $order->get_items() as $item ) {
						if ( $product_id = $item->get_product_id() ) {
							$data_store = WC_Data_Store::load( 'product' );
							$data_store->update_product_sales( $product_id, absint( $item['qty'] ), 'decrease' );
						}
					}
				}
				$order->get_data_store()->set_recorded_sales( $order, true );
			}
		}

		/**
		 * Create Suborder via admin area
		 *
		 * @param $post_id  string post ID
		 * @param $post     WP_Post WP_post object
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    3.4.0
		 * @return void
		 */
		public function create_suborder_in_admin_area( $post_id, $post ){
			$parent_order = wc_get_order( $post_id );
			$has_suborder = $parent_order->get_meta( 'has_sub_order' );

			if( empty( $has_suborder ) && is_admin() && ! is_ajax() ){
				$suborder_ids = $this->check_suborder( $post_id, array(), true );
				if( ! empty( $suborder_ids ) ){
					do_action( 'yith_wcmv_suborders_created_via_dashboard', $suborder_ids );
				}
			}
		}

		/**
		 * Adds the order processing count to the menu.
		 *
		 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since    3.4.1
		 * @return void
		 */
		public function menu_order_count() {
			$vendor = yith_get_vendor( 'current', 'user' );

			if( $vendor->is_valid() ){
				global $submenu;

				if ( isset( $submenu['edit.php?post_type=shop_order'] ) ) {
					// Remove 'WooCommerce' sub menu item.
					unset( $submenu['edit.php?post_type=shop_order'][0] );

					// Add count if user has access.
					if ( apply_filters( 'yith_wcmv_woocommerce_include_processing_order_count_in_menu', true ) ) {
						$order_count = wc_processing_order_count();

						if ( $order_count && isset( $submenu['woocommerce'] ) ) {
							foreach ( $submenu['woocommerce'] as $key => $menu_item ) {
								if ( 0 === strpos( $menu_item[0], _x( 'Orders', 'Admin menu name', 'woocommerce' ) ) ) {
									$submenu['edit.php?post_type=shop_order'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $order_count ) . '"><span class="processing-count">' . number_format_i18n( $order_count ) . '</span></span>'; // WPCS: override ok.
									break;
								}
							}
						}
					}
				}
			}
		}

		/**
		 * Check if the current order need to be restocked or not
		 *
		 * @param $can bool True if the current order need to restock, false otherwise
		 * @param $order WC_Order
		 *
		 * @return bool   True if the current order need to restock, false otherwise
		 */
		public function can_restore_order_stock( $can, $order ){
			$can = 'yith_wcmv_vendor_suborder' === $order->get_created_via() ? false : true;
			return $can;
		}
	}
}
