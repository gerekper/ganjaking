<?php
/**
 * Suborder class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCDP_Suborders' ) ) {
	/**
	 * WooCommerce Deposits and Down Payments
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Suborders {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCDP_Suborders
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Temp storage where to store real cart during plugin elaboration that requires a custom cart
		 *
		 * @var \WC_Cart
		 * @since 1.0.0
		 */
		protected $_cart;

		/**
		 * Temp storage where to store real applied coupon during plugin elaboration that requires a custom cart
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_coupons;

		/**
		 * Constructor.
		 *
		 * @return \YITH_WCDP_Suborders
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'create_balance_suborder' ), 10, 2 );
			add_filter( 'woocommerce_get_product_from_item', array( $this, 'woocommerce_get_product_from_item' ), 10, 3 );
			add_action( 'trashed_post', array( $this, 'trash_suborders' ) );
			add_action( 'untrashed_post', array( $this, 'untrash_suborders' ) );
			add_filter( 'yith_wcmv_get_suborder_ids', array( $this, 'remove_deposit_suborder_from_multi_vendor' ), 10, 2 );

			// synch suborders status with deposit order, when it switches to cancelled or failed.
			add_action( 'woocommerce_order_status_pending_to_cancelled', array( $this, 'synch_suborders_with_parent_status' ), 10, 2 );
			add_action( 'woocommerce_order_status_pending_to_failed', array( $this, 'synch_suborders_with_parent_status' ), 10, 2 );
			add_action( 'woocommerce_order_status_changed', array( $this, 'synch_suborders_with_parent_status_failed' ), 10, 3 );

			// avoid payment gateway to reduce stock order for suborders.
			add_filter( 'woocommerce_can_reduce_order_stock', array( $this, 'skip_reduce_stock_on_suborders' ), 10, 2 );

			// avoid WooCommerce to block suborder processing because of products out of stock (stock was already processed during deposit checkout).
			add_filter( 'woocommerce_order_item_product', array( $this, 'set_suborder_items_as_in_stock' ), 10, 2 );
		}

		/* === SUBORDER METHODS === */

		/**
		 * Create suborders during process checkout, to let user finalize all his/her deposit in a separate balance order
		 *
		 * @param int   $order_id    Processing order id.
		 * @param array $posted_data Array of data posted with checkout.
		 *
		 * @return bool Status of the operation
		 * @since 1.0.0
		 */
		public function create_balance_suborder( $order_id, $posted_data ) {

			if ( ! defined( 'YITH_WCDP_PROCESS_SUBORDERS' ) ) {
				define( 'YITH_WCDP_PROCESS_SUBORDERS', true );
			}

			do_action( 'yith_wcdp_before_suborders_create', $order_id, $posted_data );

			// retrieve order.
			$parent_order = wc_get_order( $order_id );
			$suborders    = array();

			// if no order found, exit.
			if ( ! $parent_order ) {
				return false;
			}

			// if order already process, exit.
			$suborders_meta = yit_get_prop( $parent_order, '_full_payment_orders' );

			if ( $suborders_meta ) {
				return false;
			}

			// retrieve order items.
			$items = $parent_order->get_items( 'line_item' );

			// if no items found, exit.
			if ( empty( $items ) ) {
				return false;
			}

			// retrieve balance_type.
			$balance_type = get_option( 'yith_wcdp_balance_type', 'multiple' );

			// create a balance for each item purchased as deposit.
			if ( 'multiple' == $balance_type ) {
				foreach ( $items as $item_id => $item ) {
					// create suborder(s).
					$new_suborder_id = $this->_build_suborder( $order_id, array( $item_id => $item ), $posted_data );

					// register suborder just created.
					if ( $new_suborder_id ) {
						$suborders[] = $new_suborder_id;
					}
				}
			} elseif ( 'single' == $balance_type ) { // create one balance order for all items purchased as deposit.
				// create suborder(s).
				$new_suborder_id = $this->_build_suborder( $order_id, $items, $posted_data );

				// register suborder just created.
				if ( $new_suborder_id ) {
					$suborders[] = $new_suborder_id;
				}
			} elseif ( 'none' == $balance_type ) {
				return true;
			}

			yit_save_prop( $parent_order, '_full_payment_orders', $suborders );

			do_action( 'yith_wcdp_after_suborders_create', $suborders, $order_id, $posted_data );

			return true;
		}

		/**
		 * Change item price, when adding it to temp cart, to let user pay only order balance
		 *
		 * @param array $cart_item_data Array of items added to temp cart.
		 *
		 * @return mixed Filtered cart item data
		 * @since 1.0.0
		 */
		public function set_item_full_amount_price( $cart_item_data ) {
			if ( ! isset( $cart_item_data['_deposit_balance'] ) ) {
				return $cart_item_data;
			}

			yit_set_prop( $cart_item_data['data'], 'price', $cart_item_data['_deposit_balance'] );
			yit_set_prop( $cart_item_data['data'], 'yith_wcdp_balance', true );

			return $cart_item_data;
		}

		/**
		 * Filter item product, when retrieving it from order item
		 *
		 * @param \WC_Product $product Product found.
		 * @param mixed       $item    Order item.
		 * @param \WC_Order   $order   Order object.
		 *
		 * @return \WC_Product Filtered product
		 * @since 1.0.0
		 */
		public function woocommerce_get_product_from_item( $product, $item, $order ) {
			if ( isset( $item['deposit'] ) && $item['deposit'] ) {
				if ( ! $product ) {
					return $product;
				}

				yit_set_prop( $product, 'price', $item['deposit_value'] );

				if ( apply_filters( 'yith_wcdp_virtual_on_deposit', true ) ) {
					yit_set_prop( $product, 'virtual', 'yes' );
				}

				if ( apply_filters( 'yith_wcdp_not_downloadable_on_deposit', true ) ) {
					yit_set_prop( $product, 'downloadable', 'no' );
				}
			}

			return $product;
		}

		/**
		 * Trash suborders on parent order trashing
		 *
		 * @param int $post_id Trashed post id.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function trash_suborders( $post_id ) {
			$order = wc_get_order( $post_id );

			if ( ! $order ) {
				return;
			}

			$suborders = $this->get_suborder( $post_id );

			if ( ! $suborders ) {
				return;
			}

			foreach ( $suborders as $suborder ) {
				( method_exists( $suborder, 'delete' ) ) ? $suborder->delete() : wp_trash_post( $suborder );
			}
		}

		/**
		 * Restore suborders on parent order restoring
		 *
		 * @param int $post_id Restore post id.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function untrash_suborders( $post_id ) {
			$order = wc_get_order( $post_id );

			if ( ! $order ) {
				return;
			}

			$suborders = $this->get_suborder( $post_id );

			if ( ! $suborders ) {
				return;
			}

			foreach ( $suborders as $suborder ) {
				wp_untrash_post( $suborder );
			}
		}

		/**
		 * Let WooCommerce skip stock decreasing for suborders
		 *
		 * @param bool      $skip  Whether to perform or not stock decreasing.
		 * @param \WC_Order $order Current order.
		 *
		 * @return bool Filtered \$skip value
		 */
		public function skip_reduce_stock_on_suborders( $skip, $order ) {
			if ( $this->is_suborder( yit_get_prop( $order, 'id' ) ) ) {
				return false;
			}

			return $skip;
		}

		/**
		 * Set products as in stock if they're retrieved for a balance payment
		 *
		 * @param \WC_Product            $product Currently retrieved product.
		 * @param \WC_Order_Item_Product $item    Current order item.
		 *
		 * @return \WC_Product filtered product
		 */
		public function set_suborder_items_as_in_stock( $product, $item ) {
			if ( isset( $product ) && $product instanceof WC_Product ) {
				$order_id = method_exists( $item, 'get_order_id' ) ? $item->get_order_id() : false;

				if ( ( $order_id && $this->is_suborder( $order_id ) ) || isset( $item['full_payment_id'] ) ) {
					$product->set_stock_status( 'instock' );
				}
			}

			return $product;
		}

		/**
		 * Set suborders status according to parent status when there is a failure and they're not completed yet
		 *
		 * @param int       $order_id Parent order id.
		 * @param \WC_Order $order    Parent order.
		 *
		 * @return void
		 * @since 1.2.4
		 */
		public function synch_suborders_with_parent_status( $order_id, $order ) {
			$order_status = $order->get_status();
			$suborders    = $this->get_suborder( $order_id );

			if ( $suborders ) {
				foreach ( $suborders as $suborder_id ) {
					$suborder = wc_get_order( $suborder_id );

					if ( ! in_array( $suborder->get_status(), array( 'pending', 'on-hold' ) ) ) {
						continue;
					}

					/**
					 * @since 1.2.4
					 */
					$suborder->set_status( $order_status, __( 'Suborder status changed to reflect parent order status change', 'yith-woocommerce-deposits-and-down-payments' ) );
					$suborder->save();
				}
			}
		}

		/**
		 * Set suborders status to pending payment when parent order status changed and old status was failed.
		 *
		 * @param int       $order_id Parent order id.
		 * @param \WC_Order $order    Parent order.
		 *
		 * @return void
		 * @since 1.2.4
		 */
		public function synch_suborders_with_parent_status_failed( $order_id, $old_status, $new_status ) {

			if ( 'failed' == $old_status ) {
				$suborders = $this->get_suborder( $order_id );

				if ( $suborders ) {
					$order_status = 'wc-pending';

					foreach ( $suborders as $suborder_id ) {
						$suborder = wc_get_order( $suborder_id );

						if ( ! in_array( $suborder->get_status(), array( 'failed' ) ) ) {
							continue;
						}

						/**
						 * @since 1.2.4
						 */
						$suborder->set_status( $order_status, __( 'Suborder status changed to reflect Pending payment status because parent\'s order status changed.', 'yith-woocommerce-deposits-and-down-payments' ) );
						$suborder->save();
					}
				}
			}
		}

		/**
		 * Create a single suborder with all the items included within second parameter
		 *
		 * @param int                      $order_id    Parent order id.
		 * @param \WC_Order_Item_Product[] $items       Array of order items to be processed for the suborder.
		 * @param mixed                    $posted_data Array of data submitted by the user.
		 *
		 * @return int|bool Suborder id; false on failure
		 * @since 1.2.1
		 */
		protected function _build_suborder( $order_id, $items, $posted_data ) {
			// retrieve order.
			$parent_order = wc_get_order( $order_id );

			// create support cart
			// we use an default WC_cart instead of YITH_WCDP_Support_Cart because WC()->checkout will create orders only
			// from default session cart.
			$this->create_support_cart();

			// retrieve deposit payment balance.
			$deposit_shipping_preference       = get_option( 'yith_wcdp_general_deposit_shipping', 'let_user_choose' );
			$deposit_admin_shipping_preference = get_option( 'yith_wcdp_general_deposit_shipping_admin_selection' );

			// cycle over order items.
			foreach ( $items as $item_id => $item ) {

				$deposit                 = wc_get_order_item_meta( $item_id, '_deposit', true );
				$deposit_balance         = wc_get_order_item_meta( $item_id, '_deposit_balance', true );
				$deposit_shipping_method = wc_get_order_item_meta( $item_id, '_deposit_shipping_method', true );
				$product                 = is_object( $item ) ? $item->get_product() : $parent_order->get_product_from_item( $item );

				// if not a deposit, continue.
				if ( ! $deposit ) {
					continue;
				}

				// set order item meta with deposit-related full payment order.
				wc_add_order_item_meta( $item_id, '_full_payment_id', false );

				// skip processing for other reason.
				if ( apply_filters( 'yith_wcdp_skip_suborder_creation', false, $item_id, $item, $order_id, $parent_order, $product ) ) {
					continue;
				}

				// set has_deposit meta.
				yit_save_prop( $parent_order, '_has_deposit', true );

				try {
					// if deposit, add elem to support cart (filters change price of the product to be added to the cart).
					add_filter( 'woocommerce_add_cart_item', array( $this, 'set_item_full_amount_price' ) );

					$product_id           = $product->is_type( 'variation' ) ? yit_get_prop( $product, 'parent_id' ) : $product->get_id();
					$variation_id         = $product->is_type( 'variation' ) ? $product->get_id() : '';
					$variation_attributes = $product->is_type( 'variation' ) ? $product->get_variation_attributes() : array();

					WC()->cart->add_to_cart(
						$product_id,
						$item['qty'],
						$variation_id,
						$variation_attributes,
						apply_filters(
							'yith_wcdp_suborder_add_cart_item_data',
							array(
								'_deposit_balance' => $deposit_balance,
							),
							$item,
							$product
						)
					);
					remove_filter( 'woocommerce_add_cart_item', array( $this, 'set_item_full_amount_price' ) );
				} catch ( Exception $e ) {
					// translators: 1. Item id 2. Product title.
					$parent_order->add_order_note( sprintf( __( 'There was an error while processing suborder for item #%$1d (%$2s)', 'yith-woocommerce-deposits-and-down-payments' ), $item_id, $product->get_title() ) );
					continue;
				}
			}

			// if no item was added to cart, proceed no further.
			if ( WC()->cart->is_empty() ) {
				$this->restore_original_cart();

				return false;
			}

			// apply coupons (when required and possible) to suborder.
			if ( apply_filters( 'yith_wcdp_propagate_coupons', false ) && ! empty( $this->_coupons ) ) {
				foreach ( $this->_coupons as $coupon ) {
					if ( apply_filters( 'yith_wcdp_propagate_coupon', true, $coupon ) ) {
						WC()->cart->add_discount( $coupon );
					}
				}
				wc_clear_notices();
			}

			// set shipping method for suborder.
			if ( 'let_user_choose' === $deposit_shipping_preference && $deposit_shipping_method && apply_filters( 'yith_wcdp_virtual_on_deposit', true, $parent_order ) ) {
				WC()->checkout()->shipping_methods = $deposit_shipping_method;
			} elseif ( 'admin_choose' === $deposit_shipping_preference && $deposit_admin_shipping_preference && apply_filters( 'yith_wcdp_virtual_on_deposit', true, $parent_order ) ) {
				WC()->checkout()->shipping_methods = (array) $deposit_admin_shipping_preference;
			} elseif ( ! apply_filters( 'yith_wcdp_virtual_on_deposit', true, $parent_order ) ) {
				WC()->checkout()->shipping_methods = array();
			}

			try {
				// create suborder.
				$new_suborder_id = WC()->checkout()->create_order( $posted_data );

				if ( ! $new_suborder_id || is_wp_error( $new_suborder_id ) ) {
					return false;
				}
			} catch ( Exception $e ) {
				$parent_order->add_order_note( __( 'There was an error while processing suborder', 'yith-woocommerce-deposits-and-down-payments' ) );

				return false;
			}

			// set new suborder post parent.
			$new_suborder = wc_get_order( $new_suborder_id );

			yit_save_prop(
				$new_suborder,
				array(
					'post_parent'          => $order_id,
					'post_status'          => apply_filters( 'yith_wcdp_suborder_status', 'pending', $new_suborder_id, $order_id ),

					// disable stock management for brand new order.
					'_has_full_payment'    => true,

					// disable stock management for brand new order.
					'_order_stock_reduced' => true,

					// update created_via meta.
					'_created_via'         => 'yith_wcdp_balance_order',

					// add plugin version.
					'_yith_wcdp_version'   => YITH_WCDP::YITH_WCDP_VERSION,

					// avoid counting sale twice.
					'_recorded_sales'      => 'yes',
				)
			);

			// update new suborder totals.
			$new_suborder->calculate_totals();

			// set suborder customer note (remove email notification for this action only during this call).
			add_filter( 'woocommerce_email_enabled_customer_note', '__return_false' );
			$new_suborder->add_order_note( sprintf( '%s <a href="%s">#%d</a>', __( 'This order has been created to allow payment of the balance', 'yith-woocommerce-deposits-and-down-payments' ), $parent_order->get_view_order_url(), $order_id ), apply_filters( 'yith_wcdp_suborder_note_is_customer_note', true ) );
			remove_filter( 'woocommerce_email_enabled_customer_note', '__return_false' );

			// update new suborder items.
			try {
				$new_suborder_items = $new_suborder->get_items( 'line_item' );
				if ( ! empty( $new_suborder_items ) ) {
					foreach ( $new_suborder_items as $suborder_item_id => $suborder_item ) {
						wc_add_order_item_meta( $suborder_item_id, '_deposit_id', $order_id );
						wc_add_order_item_meta( $suborder_item_id, '_full_payment', true );
					}
				}

				foreach ( $items as $item_id => $item ) {
					// set order item meta with deposit-related full payment order.
					wc_update_order_item_meta( $item_id, '_full_payment_id', $new_suborder_id );
				}
			} catch ( Exception $e ) {
				$new_suborder->add_order_note( __( 'There was an error while updating item meta', 'yith-woocommerce-deposits-and-down-payments' ) );

				return false;
			}

			// Let plugins add meta.
			do_action( 'yith_wcdp_update_suborder_meta', $new_suborder_id );

			// empty support cart, for next suborder.
			WC()->cart->empty_cart();

			// restore original cart.
			$this->restore_original_cart();

			return $new_suborder_id;
		}

		/* === SUPPORT CART METHODS === */

		/**
		 * Create a support cart, used to temporarily replace actual cart and make shipping/tax calculation, suborders checkout
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function create_support_cart() {
			// save current cart.
			$this->_cart    = WC()->session->get( 'cart' );
			$this->_coupons = WC()->session->get( 'applied_coupons' );

			WC()->cart->empty_cart( true );
			WC()->cart->remove_coupons();
		}

		/**
		 * Restore original cart, saved in \YITH_WCDP_Suborders::_cart property
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function restore_original_cart() {
			// delete current cart.
			WC()->cart->empty_cart( true );
			WC()->cart->remove_coupons();

			// reload cart.
			if ( apply_filters( 'yith_wcdp_reset_cart_after_suborder_processing', true ) ) {
				/**
				 * Depending on where \YITH_WCDP_Suborders::create_support_cart() was called, \YITH_WCDP_Suborders::_cart property may be
				 * an instance of WC_Cart class, or an array of cart contents (results of a previous WC_Cart::get_cart_for_session() )
				 *
				 * Instanceof prevents Fatal Error: method called on a non-object on single product pages, while
				 * WC_Cart::get_cart_for_session() avoid cart remaining empty after restore on process checkout
				 *
				 * @since 1.0.5
				 */
				WC()->session->set( 'cart', $this->_cart instanceof WC_Cart ? $this->_cart->get_cart_for_session() : $this->_cart );
				WC()->session->set( 'applied_coupons', $this->_coupons );


				WC()->cart->get_cart_from_session();

				/**
				 * Since we're sure cart has changed, let's force calculate_totals()
				 * Under some circumstances, not calculating totals at this point could effect WC()->cart->needs_payment() later,
				 * causing checkout process to redirect directly to Thank You page, instead of processing payment
				 *
				 * This was possibly caused by change in check performed at the end of get_cart_from_session() with WC 3.2
				 * Now conditions to recalculate totals after getting it from session are different then before
				 *
				 * @since 1.1.1
				 */
				WC()->cart->calculate_totals();
			}
		}

		/* === HELPER METHODS === */

		/**
		 * Check if order identified by $order_id has suborders, and eventually returns them
		 *
		 * @param int $order_id Id of the order to check.
		 *
		 * @return mixed Array of suborders, if any
		 * @since 1.0.0
		 */
		public function get_suborder( $order_id ) {
			global $wpdb;

			$suborder_ids = array();
			$parent_ids   = (array) absint( $order_id );

			while ( ! empty( $parent_ids ) ) {

				// todo: review code once WC switches to custom tables.
				$parents_list = implode( ', ', $parent_ids );
				$parent_ids   = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT ID FROM {$wpdb->posts} AS p 
						 LEFT JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id 
						 WHERE post_parent IN ({$parents_list})
						 AND post_type=%s 
						 AND meta_key=%s 
						 AND meta_value=%s ",
						'shop_order',
						'_created_via',
						'yith_wcdp_balance_order'
					)
				);

				$suborder_ids = array_merge( $suborder_ids, $parent_ids );
			}

			return apply_filters( 'yith_wcdp_suboder', $suborder_ids, $order_id );
		}

		/**
		 * Returns post parent of a Full payment order
		 * If order is not a full payment order, it will return false
		 *
		 * @param int $order_id Order id.
		 *
		 * @return int|bool If order is full payment, and has post parent, returns parent ID; false otherwise
		 */
		public function get_parent_order( $order_id ) {
			$order            = wc_get_order( $order_id );
			$has_full_payment = yit_get_prop( $order, '_has_full_payment' );

			if ( ! $has_full_payment ) {
				return false;
			}

			return yit_get_prop( $order, 'parent_id' );

		}

		/**
		 * Check if order identified by $order_id is a suborder (has post_parent)
		 *
		 * @param int $order_id Id of the order to check.
		 *
		 * @return bool Whether order is a suborder or no
		 * @since 1.0.0
		 */
		public function is_suborder( $order_id ) {
			$order = wc_get_order( absint( $order_id ) );

			if ( ! $order ) {
				return false;
			}

			$post_parent = yit_get_prop( $order, 'parent_id', true );
			$created_via = yit_get_prop( $order, '_created_via', true );

			return $post_parent && 'yith_wcdp_balance_order' == $created_via;
		}

		/**
		 * Get parent orders for current user
		 *
		 * @return \WP_Post[] Array of found orders
		 * @since 1.0.0
		 */
		public function get_parent_orders() {
			$customer_orders = yit_get_orders(
				apply_filters(
					'yith_wcdp_add_parent_orders',
					array(
						'posts_per_page' => - 1,
						'meta_query'     => array(
							array(
								'key'   => '_customer_user',
								'value' => get_current_user_id(),
							),
							array(
								'key' => '_has_deposit',
							),
						),
						'post_type'      => wc_get_order_types( 'view-orders' ),
						'post_status'    => array_keys( wc_get_order_statuses() ),
						'post_parent'    => 0,
					)
				)
			);

			return $customer_orders;
		}

		/**
		 * Get child orders for current user
		 *
		 * @return \WP_Post[] Array of found orders
		 * @since 1.0.0
		 */
		public function get_child_orders() {
			$customer_orders = get_posts(
				apply_filters(
					'yith_wcdp_add_child_orders',
					array(
						'posts_per_page' => - 1,
						'meta_query'     => array(
							array(
								'key'   => '_customer_user',
								'value' => get_current_user_id(),
							),
							array(
								'key' => '_has_full_payment',
							),
						),
						'post_type'      => wc_get_order_types( 'view-orders' ),
						'post_status'    => array_keys( wc_get_order_statuses() ),
					)
				)
			);

			return $customer_orders;
		}

		/**
		 * Return an array of ids of orders that contain deposit
		 *
		 * @return array Array of order ids
		 */
		public function get_all_deposits_ids() {
			global $wpdb;

			return $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", '_has_deposit', '1' ) );
		}

		/**
		 * Return an array of ids of orders that where created as balance orders
		 *
		 * @return array Array of order ids
		 */
		public function get_all_balances_ids() {
			global $wpdb;

			return $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", '_created_via', 'yith_wcdp_balance_order' ) );
		}

		/* === MULTI VENDOR COMPATIBILITY === */

		/**
		 * Remove deposit ssuborders from Multi Vendor suborders list
		 *
		 * @param mixed $suborder_ids    Multi Vendor suborders.
		 * @param int   $parent_order_id Parent order id.
		 *
		 * @return mixed Array diff between Multi Vendor suborders and deposit suborders
		 * @since 1.0.4
		 */
		public function remove_deposit_suborder_from_multi_vendor( $suborder_ids, $parent_order_id ) {
			if ( $parent_order_id && $suborder_ids ) {
				$deposit_suborder_ids = $this->get_suborder( $parent_order_id );
				if ( $deposit_suborder_ids ) {
					$suborder_ids = array_diff( $suborder_ids, $deposit_suborder_ids );
				}
			}

			return $suborder_ids;
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCDP_Suborders
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCDP_suborders class
 *
 * @return \YITH_WCDP_Suborders
 * @since 1.0.0
 */
function YITH_WCDP_Suborders() {
	return YITH_WCDP_Suborders::get_instance();
}