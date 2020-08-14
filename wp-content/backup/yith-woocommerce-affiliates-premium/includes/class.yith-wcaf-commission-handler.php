<?php
/**
 * Commission Handler class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
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

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Commission_Handler' ) ) {
	/**
	 * WooCommerce Commission Handler
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Commission_Handler {

		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Commission_Handler
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Available commission status
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_available_commission_status = array(
			'pending',
			'pending-payment',
			'paid',
			'not-confirmed',
			'cancelled',
			'refunded',
			'trash'
		);

		/**
		 * Available commission status labels
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_status_labels_map = array();

		/**
		 * List of status that allows referred user to receive commissions
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_unassigned_status = array(
			'not-confirmed',
			'cancelled',
			'refunded',
			'trash'
		);

		/**
		 * List of status that don't allows referred user to receive commissions
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_assigned_status = array(
			'pending',
			'pending-payment',
			'paid'
		);

		/**
		 * List of status that force commission amount to bu summed to total affiliate payments
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_payment_status = array(
			'paid',
			'pending-payment'
		);

		/**
		 * List of status that doesn't allow any modification
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_dead_status = array(
			'pending-payment',
			'paid'
		);

		/**
		 * Map of order - commission available statuses relationships
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_commission_order_status_map = array(
			'pending'         => array( 'completed', 'processing' ),
			'pending-payment' => array(),
			'paid'            => array(),
			'not-confirmed'   => array( 'pending', 'on-hold' ),
			'cancelled'       => array( 'cancelled', 'failed' ),
			'refunded'        => array( 'refunded' )
		);

		/**
		 * Map of allowed status change
		 *
		 * @var mixed
		 * @since 1.0.0
		 */
		protected $_available_commission_status_change = array(
			'pending'         => array(
				'pending-payment',
				'not-confirmed',
				'cancelled',
				'refunded',
				'paid',
				'trash'
			),
			'pending-payment' => array(
				'pending',
				'paid',
				'trash'
			),
			'paid'            => array(
				'pending-payment',
				'trash'
			),
			'not-confirmed'   => array(
				'pending',
				'cancelled',
				'refunded',
				'trash'
			),
			'cancelled'       => array(
				'pending',
				'not-confirmed',
				'refunded',
				'trash'
			),
			'refunded'        => array(
				'pending',
				'not-confirmed',
				'cancelled',
				'trash'
			),
			'trash'           => array(
				'pending',
				'pending-payment',
				'paid',
				'not-confirmed',
				'cancelled',
				'refunded'
			)
		);

		/**
		 * Whether to exclude tax from commission calculation
		 *
		 * @var bool
		 * @since 1.0.0
		 */
		protected $_exclude_tax;

		/**
		 * Whether to exclude discount from commission calculation
		 *
		 * @var bool
		 * @since 1.0.0
		 */
		protected $_exclude_discounts;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Commission_Handler
		 * @since 1.0.0
		 */
		public function __construct() {
			// retrieve options
			$this->_retrieve_options();

			$this->_status_labels_map                  = apply_filters( 'yith_wcaf_commission_status_labels_map', array(
				'pending'         => __( 'Pending', 'yith-woocommerce-affiliates' ),
				'pending-payment' => __( 'Pending Payment', 'yith-woocommerce-affiliates' ),
				'paid'            => __( 'Paid', 'yith-woocommerce-affiliates' ),
				'not-confirmed'   => __( 'Not confirmed', 'yith-woocommerce-affiliates' ),
				'cancelled'       => __( 'Cancelled', 'yith-woocommerce-affiliates' ),
				'refunded'        => __( 'Refunded', 'yith-woocommerce-affiliates' ),
				'trash'           => __( 'Trashed', 'yith-woocommerce-affiliates' )
			) );
			$this->_available_commission_status_change = apply_filters( 'yith_wcaf_available_commission_status_change', $this->_available_commission_status_change );

			// add commissions panel handling
			add_action( 'yith_wcaf_commission_panel', array( $this, 'print_commission_panel' ) );
			add_action( 'current_screen', array( $this, 'add_screen_option' ) );
			add_filter( 'manage_yith-plugins_page_yith_wcaf_panel_columns', array( $this, 'add_screen_columns' ) );
			add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );
			add_action( 'load-yith-plugins_page_yith_wcaf_panel', array( $this, 'process_bulk_actions' ) );

			// register order metabox
			add_action( 'add_meta_boxes', array( $this, 'add_order_metabox' ) );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_meta' ) );

			// handle order status change
			add_action( 'woocommerce_order_status_changed', array( $this, 'status_order_changing_handler' ), 10, 3 );

			// handle order trashing
			add_action( 'trashed_post', array( $this, 'order_trashing_handler' ) );
			add_action( 'untrashed_post', array( $this, 'order_untrashing_handler' ) );

			// handle refunds
			add_action( 'woocommerce_refund_created', array( $this, 'order_refund_handler' ) );
			add_action( 'deleted_post_meta', array( $this, 'refund_deleted_handler' ), 10, 4 );

			// handle notifications
			add_action( 'yith_wcaf_commission_status_changed', array( WC(), 'mailer' ), 5 );

			// handles ajax actions
			add_action( 'wp_ajax_yith_wcaf_add_commission_note', array( $this, 'ajax_add_note' ) );
			add_action( 'wp_ajax_yith_wcaf_delete_commission_note', array( $this, 'ajax_delete_note' ) );
		}

		/* === INIT METHODS === */

		/**
		 * Init class attributes for admin options
		 *
		 * @return void
		 * @since 1.0.0
		 */
		protected function _retrieve_options() {
			$this->_exclude_tax       = get_option( 'yith_wcaf_commission_exclude_tax', 'yes' );
			$this->_exclude_discounts = get_option( 'yith_wcaf_commission_exclude_discount', 'yes' );
		}

		/* === ORDER HANDLING METHODS === */

		/**
		 * Create orders commissions, on process checkout action, and when an order is untrashed
		 *
		 * @param $order_id     int Order id
		 * @param $token        string Referral token
		 * @param $token_origin string Referral token origin
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function create_order_commissions( $order_id, $token, $token_origin = 'undefined' ) {
			$order     = wc_get_order( $order_id );
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $token );

			// if no order or user, return
			if ( ! $order || ! $affiliate || ! apply_filters( 'yith_wcaf_create_order_commissions', true, $order_id, $token, $token_origin ) ) {
				return;
			}

			// map commission status on order status
			$commission_status = $this->map_commission_status( $order->get_status() );

			yit_save_prop( $order, '_yith_wcaf_referral', $token );

			// process commission, add order item meta, register order as processed
			$items = $order->get_items( 'line_item' );
			if ( ! empty( $items ) ) {
				foreach ( $items as $item_id => $item ) {
					$product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];

					$rate = wc_get_order_item_meta( $item_id, '_yith_wcaf_commission_rate', true );

					if ( ! $rate ) {
						$rate = YITH_WCAF_Rate_Handler()->get_rate( $affiliate, intval( $product_id ), $order_id );
					}

					$commission = $this->_calculate_line_item_commission( $order, $item_id, $item, $rate );

					$commission_args = array(
						'order_id'     => $order_id,
						'affiliate_id' => $affiliate['ID'],
						'line_item_id' => $item_id,
						'rate'         => $rate,
						'amount'       => $commission,
						'status'       => $commission_status,
						'created_at'   => apply_filters( 'yith_wcaf_create_order_commission_use_current_date', true ) ? current_time( 'mysql' ) : yit_get_prop( $order, 'order_date' )
					);

					if ( ! apply_filters( 'yith_wcaf_create_item_commission', true, $item, $item_id, $product_id, $commission_args ) ) {
						continue;
					}

					$old_id = wc_get_order_item_meta( $item_id, '_yith_wcaf_commission_id', true );

					if ( $old_id ) {
						$id = $old_id;
						$this->update( $id, $commission_args );
					} else {
						$id = $this->add( $commission_args );
					}

					if ( $commission_status == 'pending' ) {
						YITH_WCAF_Affiliate_Handler()->update_affiliate_total( $affiliate['ID'], $commission );
					}

					wc_update_order_item_meta( $item_id, '_yith_wcaf_commission_id', $id );
					wc_update_order_item_meta( $item_id, '_yith_wcaf_commission_rate', $rate );
					wc_update_order_item_meta( $item_id, '_yith_wcaf_commission_amount', $commission );
				}
			}
		}

		/**
		 * Regenerate order commissions, deleting old ones, and regenerating them
		 *
		 * @param $order_id int Order id
		 * @param $token    string Affiliate token
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function regenerate_order_commissions( $order_id, $token = false ) {
			$order = wc_get_order( $order_id );
			$token = $token ? $token : yit_get_prop( $order, '_yith_wcaf_referral', true );

			if ( ! $order || ! $token ) {
				return;
			}

			// retrieve old commissions, to check if we'll need to delete something
			$commissions = $this->get_commissions( array( 'order_id' => $order_id ) );
			$res         = true;

			// delete previous commissions (if one commission is paid or pending-payment, process is aborted)
			if ( ! empty( $commissions ) ) {
				$res = $this->delete_order_commissions( $order_id, true, true );
			}

			// re-create commissions
			if ( $res ) {
				$this->create_order_commissions( $order_id, $token );
			}
		}

		/**
		 * Delete order commissions, when an order is trashed
		 *
		 * @param $order_id     int Order id
		 * @param $force        bool Force deletion, even if status is a dead_status
		 * @param $delete_rates bool Delete rates stored within order items, to get fresh values when adding new affiliate
		 *
		 * @return bool Operation status
		 * @since 1.0.0
		 */
		public function delete_order_commissions( $order_id, $force = false, $delete_rates = false ) {
			$order     = wc_get_order( $order_id );
			$token     = yit_get_prop( $order, '_yith_wcaf_referral', true );
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $token );

			if ( ! $order || ! $token || ! $affiliate ) {
				return false;
			}

			$order_commissions = $this->get_commissions( array( 'order_id' => $order_id ) );

			if ( ! empty( $order_commissions ) ) {
				foreach ( $order_commissions as $commission ) {
					if ( in_array( $commission['status'], $this->_dead_status ) && ! $force ) {
						return false;
					}
				}

				foreach ( $order_commissions as $commission ) {
					$this->delete_single_commission( $commission['ID'], $force, $delete_rates );
				}
			}

			// make sure there is no orphan reference
			yith_wcaf_delete_order_data( $order_id );

			return true;
		}

		/**
		 * Changes status of commissions related to an order, after of a status change for the order
		 *
		 * @param $order_id   int Order id
		 * @param $old_status string Old order status
		 * @param $new_status string New order status
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function status_order_changing_handler( $order_id, $old_status, $new_status ) {
			$order = wc_get_order( $order_id );

			if ( empty( $order ) ) {
				return;
			}

			if ( yit_get_prop( $order, 'post_status' ) == 'trash' ) {
				return;
			}

			$items = $order->get_items( 'line_item' );

			if ( empty( $items ) ) {
				return;
			}

			foreach ( $items as $item_id => $item ) {
				$commission_id = wc_get_order_item_meta( $item_id, '_yith_wcaf_commission_id', true );

				if ( empty( $commission_id ) ) {
					continue;
				}

				$commission = $this->get_commission( $commission_id );

				// if we're paying commission, please skip any user total change
				if ( in_array( $commission['status'], $this->_dead_status ) ) {
					continue;
				}

				$this->change_commission_status( $commission_id, $this->map_commission_status( $new_status ) );
			}
		}

		/**
		 * Handle order trashing action
		 *
		 * @param $post_id int Post id
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function order_trashing_handler( $post_id ) {
			$order = wc_get_order( $post_id );

			if ( empty( $order ) ) {
				return;
			}

			$this->delete_order_commissions( $post_id, true );
		}

		/**
		 * Handle order untrashing action
		 *
		 * @param $post_id int Post id
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function order_untrashing_handler( $post_id ) {
			$order = wc_get_order( $post_id );
			$token = yit_get_prop( $order, '_yith_wcaf_referral', true );

			if ( empty( $order ) ) {
				return;
			}

			$this->create_order_commissions( $post_id, $token );
		}

		/**
		 * Handle order refund creation
		 *
		 * @param $new_refund_id int Refund id
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function order_refund_handler( $new_refund_id ) {
			$refund         = new WC_Order_Refund( $new_refund_id );
			$order          = wc_get_order( yit_get_prop( $refund, 'parent_id' ) );
			$refunds        = array();
			$global_refunds = array();  // save the refund objects of global refunds
			$total_refunded = array();

			if ( $order->get_status() == 'refunded' ) {
				return;
			}

			// reset commissions calculating (must be before next foreach)
			foreach ( $order->get_refunds() as $_refund ) {
				$refunded_commissions = yit_get_prop( $_refund, '_refunded_commissions', true );

				// change definitely commissions amount
				if ( ! empty( $refunded_commissions ) ) {
					foreach ( $refunded_commissions as $commission_id => $amount ) {
						$commission = $this->get_commission( $commission_id );

						if ( in_array( $commission['status'], $this->_dead_status ) ) {
							continue;
						}

						// update commission
						$this->change_commission_amount( $commission_id, abs( $amount ) );
						$this->change_commission_refund( $commission_id, $amount );

						unset( $refunded_commissions[ $commission_id ] );
					}

					// remove post meta to delete every track of refunds
					yit_save_prop( $_refund, '_refunded_commissions', $refunded_commissions );
				}
			}

			// single refunds
			foreach ( $order->get_refunds() as $_refund ) {

				// count the line refunds total, to detect if there is some global refund
				$line_items_refund = 0;

				/** @var WC_Order_Refund $_refund */
				foreach ( $_refund->get_items() as $item_id => $item ) {
					$original_item_id = $item['refunded_item_id'];
					if ( $commission_id = wc_get_order_item_meta( $original_item_id, '_yith_wcaf_commission_id', true ) ) {
						$refund_amount = $item['line_total'];
						$refund_amount += ( $this->_exclude_tax != 'yes' ) ? $item['line_tax'] : 0;

						$commission = $this->get_commission( $commission_id );

						if ( ! isset( $total_refunded[ $commission_id ] ) ) {
							$total_refunded[ $commission_id ] = $refund_amount;
						} else {
							$total_refunded[ $commission_id ] += $refund_amount;
						}

						$line_items_refund += $item['line_total'] + $item['line_tax'];
						$amount            = (double) $refund_amount * (double) $commission['rate'] / 100;

						// register the amount
						$refunds[ yit_get_order_id( $_refund ) ][ $commission_id ] = $amount;
					}
				}

				// detect if there is some global refund applied in this refund
				if ( yit_get_refund_amount( $_refund ) - abs( $line_items_refund ) > 0 ) {
					yit_set_refund_amount( $_refund, yit_get_refund_amount( $_refund ) - abs( $line_items_refund ) );
					$global_refunds[] = $_refund;
				}

			}

			// manage the global refunds
			foreach ( $global_refunds as $_refund ) {
				$refund_id      = yit_get_order_id( $_refund );
				$rate_to_refund = yit_get_refund_amount( $_refund ) / ( $order->get_total() - abs( array_sum( $total_refunded ) ) );

				foreach ( $order->get_items() as $item_id => $item ) {
					$commission_id = wc_get_order_item_meta( $item_id, '_yith_wcaf_commission_id', true );
					if ( $commission_id ) {
						$commission = $this->get_commission( $commission_id );

						$to_refund = ( $order->get_line_total( $item, false, false ) - $order->get_total_refunded_for_item( $item_id ) ) * $rate_to_refund;
						$amount    = (float) abs( $to_refund * (double) $commission['rate'] / 100 ) * - 1;

						// register the amount
						if ( ! isset( $refunds[ $refund_id ][ $commission_id ] ) ) {
							$refunds[ $refund_id ][ $commission_id ] = $amount;
						} else {
							$refunds[ $refund_id ][ $commission_id ] += $amount;
						}
					}
				}
			}

			// update the refunded commissions in the order to easy manage these in future
			foreach ( $refunds as $refund_id => $commissions_refunded ) {
				foreach ( $commissions_refunded as $commission_id => $amount ) {
					$commission = $this->get_commission( $commission_id );
					$note       = $refund_id == $new_refund_id ? sprintf( __( 'Refunded %s due to refund #%s creation', 'yith-woocommerce-affiliates' ), wc_price( abs( $amount ) ), $refund_id ) : '';

					$this->change_commission_amount( $commission_id, $amount, $note );
					$this->change_commission_refund( $commission_id, abs( $amount ) );
				}

				update_post_meta( $refund_id, '_refunded_commissions', $commissions_refunded );
			}

		}

		/**
		 * Handle order refund deletion
		 *
		 * @param $meta_ids   mixed Order meta ids (meta containing refunded commission id stored within the order)
		 * @param $object_id  int Order id
		 * @param $meta_key   string Meta key (_refunded_commission)
		 * @param $meta_value mixed Meta value (commission refunded)
		 */
		public function refund_deleted_handler( $meta_ids, $object_id, $meta_key, $meta_value ) {
			if ( $meta_key == '_refunded_commissions' ) {

				$refund = get_post( $object_id );

				if ( ! $refund ) {
					return;
				}

				$order_id = $refund->post_parent;
				$order    = wc_get_order( $order_id );

				if ( ! $order || $order->get_status() == 'refunded' ) {
					return;
				}

				if ( ! empty( $meta_value ) ) {
					foreach ( $meta_value as $commission_id => $refund ) {
						$commission = $this->get_commission( $commission_id );
						$this->change_commission_amount( $commission_id, abs( (double) $refund ), sprintf( __( 'Restored %s due to refund #%s deletion', 'yith-woocommerce-affiliates' ), wc_price( abs( $refund ) ), $object_id ) );
						$this->change_commission_refund( $commission_id, (double) $refund );
					}
				}
			}
		}

		/* === COMMISSION HANDLING METHODS === */

		/**
		 * Add a new commission
		 *
		 * @param $commission_args mixed Data of the commission to add<br/>
		 *                         [<br/>
		 *                         'order_id' => 0,                             // Commission related order id (int)<br/>
		 *                         'affiliate_id' => 0,                         // Commission related affiliate id (int)<br/>
		 *                         'line_item_id' => 0,                         // Commission related line item id (int)<br/>
		 *                         'rate' => 0,                                 // Commission rate (float)<br/>
		 *                         'amount' => 0,                               // Commission amount (float)<br/>
		 *                         'status' => 'pending',                       // Commission status ({@link \YITH_WCAF_Commission_Handler::$_available_commission_status})<br/>
		 *                         'created_at' => current_time( 'mysql' ),     // Date of commission creation (mysql date format; default to server current time)<br/>
		 *                         'last_edit' => current_time( 'mysql' )       // Date of last commission edit (mysql date format; default to server current time)<br/>
		 *                         ]
		 *
		 * @return int|bool Id of commission added to DB; false on failure
		 * @see   \YITH_WCAF_Commission_Handler::$_available_commission_status
		 * @since 1.0.0
		 */
		public function add( $commission_args = array() ) {
			global $wpdb;

			$defaults = array(
				'order_id'     => 0,
				'affiliate_id' => 0,
				'line_item_id' => 0,
				'rate'         => 0,
				'amount'       => 0,
				'refunds'      => 0,
				'status'       => 'pending',
				'created_at'   => current_time( 'mysql' ),
				'last_edit'    => current_time( 'mysql' )
			);

			$args = wp_parse_args( $commission_args, $defaults );
			$res  = $wpdb->insert( $wpdb->yith_commissions, $args );

			if ( ! $res ) {
				return false;
			}

			return $wpdb->insert_id;
		}

		/**
		 * Update commission db row.
		 * To change commission status or commission amount, refer to {@link \YITH_WCAF_Commission_Handler::change_commission_status} and {@link \YITH_WCAF_Commission_Handler::change_commission_amount}
		 *
		 * @param $commission_id int Commission id
		 * @param $args          mixed Args to use on update procedure<br/>
		 *                       [<br/>
		 *                       'order_id' => 0,                             // Commission related order id (int)<br/>
		 *                       'affiliate_id' => 0,                         // Commission related affiliate id (int)<br/>
		 *                       'line_item_id' => 0,                         // Commission related line item id (int)<br/>
		 *                       'rate' => 0,                                 // Commission rate (float)<br/>
		 *                       'amount' => 0,                               // Commission amount (float)<br/>
		 *                       'status' => 'pending',                       // Commission status ({@link \YITH_WCAF_Commission_Handler::$_available_commission_status})<br/>
		 *                       'created_at' => current_time( 'mysql' ),     // Date of commission creation (mysql date format; default to server current time)<br/>
		 *                       ]
		 *
		 * @return int|bool Number of update rows; false on failure
		 * @see   \YITH_WCAF_Commission_Handler::$_available_commission_status
		 * @since 1.0.0
		 */
		public function update( $commission_id, $args ) {
			global $wpdb;

			$args = array_merge( $args, array( 'last_edit' => current_time( 'mysql' ) ) );

			return $wpdb->update( $wpdb->yith_commissions, $args, array( 'ID' => $commission_id ) );
		}

		/**
		 * Delete commission row from db
		 *
		 * @param $commission_id int Commission to delete
		 *
		 * @return int|bool Number of deleted row; false on error
		 * @since 1.0.0
		 */
		public function delete( $commission_id ) {
			global $wpdb;

			$res = $wpdb->delete( $wpdb->yith_commissions, array( 'ID' => $commission_id ) );

			if ( $res ) {
				$this->delete_commission_notes( $commission_id );
			}

			return $res;
		}

		/**
		 * Delete a single commissions, removing in the same time data stored within order item meta
		 *
		 * @param $commission_id int Commission id
		 * @param $force         bool Force deletion, even if status is a dead_status
		 * @param $delete_rate   bool Delete rate stored within order items, to get fresh values when adding new affiliate
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function delete_single_commission( $commission_id, $force = false, $delete_rate = false ) {
			$commission = $this->get_commission( $commission_id );

			if ( ! $commission ) {
				return;
			}

			if ( in_array( $commission['status'], $this->_dead_status ) && ! $force ) {
				return;
			}

			if ( in_array( $commission['status'], $this->_assigned_status ) ) {
				YITH_WCAF_Affiliate_Handler()->update_affiliate_total( $commission['affiliate_id'], - 1 * (double) $commission['amount'] );
			}

			$this->delete( $commission['ID'] );

			wc_delete_order_item_meta( $commission['line_item_id'], '_yith_wcaf_commission_id' );
			wc_delete_order_item_meta( $commission['line_item_id'], '_yith_wcaf_commission_amount' );

			if ( $delete_rate ) {
				wc_delete_order_item_meta( $commission['line_item_id'], '_yith_wcaf_commission_rate' );
			}
		}

		/**
		 * Restore item form trash
		 *
		 * @TODO: system currently do not store commission status before trashing; this means that we need to restore item to a status inferred by the order
		 *
		 * @param $commission_id int Commission id
		 *
		 * @return bool operation status
		 */
		public function restore_from_trash( $commission_id ) {
			$commission = $this->get_commission( $commission_id );

			if ( $commission && $commission['status'] != 'trash' ) {
				return false;
			}

			$order = wc_get_order( $commission['order_id'] );

			if ( ! $order ) {
				return false;
			}

			// map commission status on order status
			$commission_status = $this->map_commission_status( $order->get_status() );
			$user              = wp_get_current_user();
			$message           = sprintf( __( '%s restored this item; system automatically assigned %s status', 'yith-woocommerce-affiliates' ), $user->user_login, $this->get_readable_status( $commission_status ) );

			return $this->change_commission_status( $commission_id, $commission_status, $message );
		}

		/**
		 * Calculate single line item commission, for a give order, item and rate
		 *
		 * @param $order        \WC_Order Order object
		 * @param $line_item    mixed Order item array
		 * @param $line_item_id int Order item id
		 * @param $rate         float Commission rate
		 *
		 * @return float Commission amount
		 * @since 1.0.0
		 */
		protected function _calculate_line_item_commission( $order, $line_item_id, $line_item, $rate ) {
			// If total is 0 after discounts then go no further
			if ( ! $rate ) {
				return 0;
			}

			$get_item_amount = 'yes' == $this->_exclude_discounts ? 'get_item_total' : 'get_item_subtotal';

			// Retrieve the real amount of single item, with right discounts applied and without taxes
			$line_total = (float) $order->$get_item_amount( $line_item, 'yes' != $this->_exclude_tax, false ) * $line_item['qty'];

			// If total is 0 after discounts then go no further
			if ( ! $line_total ) {
				return 0;
			}

			$use_percentage_rates = apply_filters( 'yith_wcaf_use_percentage_rates', true, $order );

			// Get total amount for commission
			if ( $use_percentage_rates ) {
				$amount = (float) $line_total * $rate / 100;
			} else {
				$amount = $rate;
			}

			// If commission amount is 0 then go no further
			if ( ! $amount ) {
				return 0;
			}

			// If commission result greater than line item total, return line item total
			if ( $amount >= $line_total ) {
				return $line_total;
			}

			return apply_filters( 'yith_wcaf_line_item_commission', $amount, $order, $line_item_id, $line_item, $rate, $use_percentage_rates );
		}

		/* === COMMISSION NOTES METHODS === */

		/**
		 * Get existing notes for a given commission
		 *
		 * @param $commission_id int Commission id
		 *
		 * @return mixed Array with registered notes, or false if no note was registered yet
		 * @since 1.0.0
		 */
		public function get_commission_notes( $commission_id ) {
			global $wpdb;

			$res = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->yith_commission_notes} WHERE commission_id = %d ORDER BY note_date DESC", $commission_id ), ARRAY_A );

			return $res;
		}

		/**
		 * Delete existing notes for a given commission
		 *
		 * @param $commission_id int Commission id
		 *
		 * @return int|bool Number of rows deleted, or false on failure
		 * @since 1.0.0
		 */
		public function delete_commission_notes( $commission_id ) {
			global $wpdb;

			$res = $wpdb->delete( $wpdb->yith_commission_notes, array( 'commission_id' => $commission_id ) );

			return $res;
		}

		/**
		 * Add note to a commission
		 *
		 * @param $commission_note mixed Array of commission note arguments<br/>
		 *                         [<br/>
		 *                         'commission_id' => 0,                    // Commission id (int)<br/>
		 *                         'note_content' => '',                    // Note content (string)<br/>
		 *                         'note_date' => current_time( 'mysql' )   // Note date (mysql date format; default to current server time)<br/>
		 *                         ]
		 *
		 * @return int Added note id; 0 on failure
		 * @since 1.0.0
		 */
		public function add_note( $commission_note ) {
			global $wpdb;

			$defaults = array(
				'commission_id' => 0,
				'note_content'  => '',
				'note_date'     => current_time( 'mysql' )
			);

			$query_args = wp_parse_args( $commission_note, $defaults );

			$res = $wpdb->insert( $wpdb->yith_commission_notes, $query_args );

			if ( ! $res ) {
				return 0;
			}

			return $wpdb->insert_id;
		}

		/**
		 * Delete a given note
		 *
		 * @param $commission_note_id int Commission note id
		 *
		 * @return int|bool Number of rows deleted, or false on failure
		 * @since 1.0.0
		 */
		public function delete_note( $commission_note_id ) {
			global $wpdb;

			$res = $wpdb->delete( $wpdb->yith_commission_notes, array( 'ID' => $commission_note_id ) );

			return $res;
		}

		/**
		 * Handle ajax request to add note; excepts commission_id and note_content params in the request
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ajax_add_note() {
			if ( empty( $_REQUEST['commission_id'] ) || empty( $_REQUEST['note_content'] ) ) {
				wp_send_json( false );
			}

			$commission_id = intval( $_REQUEST['commission_id'] );
			$note_content  = trim( esc_html( $_REQUEST['note_content'] ) );
			$note_date     = current_time( 'mysql' );
			$template      = '';
			$res           = $this->add_note( array(
				'commission_id' => $commission_id,
				'note_content'  => $note_content,
				'note_date'     => $note_date
			) );

			if ( $res ) {
				$template = sprintf( '<li rel="%s" class="note">
								<div class="note_content">
									<p>%s</p>
								</div>
								<p class="meta">
									<abbr class="exact-date" title="%s">%s</abbr>
									<a href="#" class="delete_note">%s</a>
								</p>
							 </li>',
					$res,
					$note_content,
					$note_date,
					sprintf( __( 'added on %1$s at %2$s', 'yith-woocommerce-affiliates' ), date_i18n( wc_date_format(), strtotime( $note_date ) ), date_i18n( wc_time_format(), strtotime( $note_date ) ) ),
					__( 'Delete note', 'yith-woocommerce-affiliates' )
				);
			}

			wp_send_json( array( 'res' => $res, 'template' => $template ) );
		}

		/**
		 * Handle ajax requests to delete note; excepts note_id in the request
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function ajax_delete_note() {
			if ( empty( $_REQUEST['note_id'] ) ) {
				wp_send_json( false );
			}

			$note_id = intval( $_REQUEST['note_id'] );

			wp_send_json( $this->delete_note( $note_id ) );
		}

		/* === HELPER METHODS === */

		/**
		 * Let users access class attributes, protecting them for set operations
		 *
		 * @param $value string Attribute name (initial underscores for protected attributes can be omitted)
		 *
		 * @return mixed Attribute value, if found; false otherwise
		 * @since 1.0.0
		 */
		public function __get( $value ) {
			if ( isset( $this->$value ) ) {
				return $value;
			} elseif ( isset( $this->{'_' . $value} ) ) {
				return $this->{'_' . $value};
			} else {
				return false;
			}
		}

		/**
		 * Returns value of the private fields of the class
		 *
		 * @param $option string Attribute name (it willi be prefixed with '_' character)
		 *
		 * @return mixed Attribute value
		 * @since 1.0.0
		 */
		public function get_option( $option ) {
			$attr_name = '_' . $option;

			return isset( $this->$attr_name ) ? $this->$attr_name : false;
		}

		/**
		 * Count commissions matching search params
		 *
		 * @param $args mixed Search params<br/>
		 *              [<br/>
		 *              'ID' => false,           // commission ID (int)<br/>
		 *              'order_id' => false,     // commission related order id (int)<br/>
		 *              'user_id' => false,      // commission related affiliate user id (int)<br/>
		 *              'affiliate_id' => false, // commission related affiliate id (int)<br/>
		 *              'status' => false,       // commission status ({@link \YITH_WCAF_Commission_Handler::$_available_commission_status})<br/>
		 *              'user_login' => false,   // commission related affiliate user login, or part of it (string)<br/>
		 *              'user_email' => false,   // commission related affiliate user email, or part of it (string)<br/>
		 *              'product_id' => false,   // commission related line item product id (int)<br/>
		 *              'product_name' => false, // commission related line item product name, or part of it (string)<br/>
		 *              'rate' => false,         // commission rate range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'amount' => false,       // commission amount range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'interval' => false      // commission date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/>
		 *              ]
		 *
		 * @return int Commission count
		 * @see   \YITH_WCAF_Commission_Handler::get_commissions
		 * @since 1.0.0
		 */
		public function count_commission( $args = array() ) {
			$defaults = array(
				'ID'           => false,
				'order_id'     => false,
				'user_id'      => false,
				'affiliate_id' => false,
				'status'       => false,
				'user_login'   => false,
				'user_email'   => false,
				'product_id'   => false,
				'product_name' => false,
				'rate'         => false,
				'amount'       => false,
				'interval'     => false
			);

			$args = wp_parse_args( $args, $defaults );

			return count( $this->get_commissions( $args ) );
		}

		/**
		 * Retrieve commissions matching search params
		 *
		 * @param $args mixed Search params<br/>
		 *              [<br/>
		 *              'ID' => false,             // commission ID (int)<br/>
		 *              'include' => array()       // array of ids to include in the final set<br/>
		 *              'exclude' => array()       // array of ids to exclude from the final set<br/>
		 *              'order_id' => false,       // commission related order id (int)<br/>
		 *              'user_id' => false,        // commission related affiliate user id (int)<br/>
		 *              'affiliate_id' => false,   // commission related affiliate id (int)<br/>
		 *              'status' => false,         // commission status ({@link \YITH_WCAF_Commission_Handler::$_available_commission_status})<br/>
		 *              'status__not_in' => false, // commission status differs ({@link \YITH_WCAF_Commission_Handler::$_available_commission_status})<br/>
		 *              'user_login' => false,     // commission related affiliate user login, or part of it (string)<br/>
		 *              'user_email' => false,     // commission related affiliate user email, or part of it (string)<br/>
		 *              'product_id' => false,     // commission related line item product id (int)<br/>
		 *              'product_name' => false,   // commission related line item product name, or part of it (string)<br/>
		 *              'rate' => false,           // commission rate range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'amount' => false,         // commission amount range (array, with at lest one of this index: [min(float)|max(float)])<br/>
		 *              'include' => false         // a list of commissions valid ID (array of integers, or single integer, or false)
		 *              'interval' => false        // commission date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/>
		 *              'orderby' => 'ID',         // sorting direction (ASC/DESC)<br/>
		 *              'order' => 'ASC',          // sorting column (any table valid column)<br/>
		 *              'limit' => 0,              // limit (int)<br/>
		 *              'offset' => 0              // offset (int)<br/>
		 *              ]
		 *
		 * @return mixed Array with found commissions, or false on failure
		 * @since 1.0.0
		 */
		public function get_commissions( $args = array() ) {
			global $wpdb;

			$defaults = array(
				'ID'             => false,
				'include'        => array(),
				'exclude'        => array(),
				'order_id'       => false,
				'user_id'        => false,
				'affiliate_id'   => false,
				'status'         => false,
				'status__not_in' => false,
				'user_login'     => false,
				'user_email'     => false,
				'product_id'     => false,
				'product_name'   => false,
				'rate'           => false,
				'amount'         => false,
				'interval'       => false,
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'limit'          => 0,
				'offset'         => 0
			);

			$args = wp_parse_args( $args, $defaults );

			$query     = '';
			$query_arg = array();

			$query .= "SELECT
					    yc.*,
					    u.ID AS user_id,
					    p.ID AS product_id,
					    p.post_title AS product_name,
					    u.user_login AS user_login,
					    u.user_email AS user_email
				       FROM {$wpdb->yith_commissions} AS yc
				       LEFT JOIN {$wpdb->yith_affiliates} AS ya ON ya.ID = yc.affiliate_id
				       LEFT JOIN {$wpdb->users} AS u ON u.ID = ya.user_id
				       LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON im.order_item_id = yc.line_item_id
				       LEFT JOIN {$wpdb->posts} AS p ON p.ID = im.meta_value
				       WHERE ( im.meta_key = '_product_id' || im.meta_key = '_variation_id' )
				       AND im.meta_value <> 0";

			if ( ! empty( $args['ID'] ) ) {
				$query       .= ' AND yc.ID = %d';
				$query_arg[] = $args['ID'];
			}

			if ( ! empty( $args['include'] ) ) {
				if ( ! is_array( $args['include'] ) ) {
					$args['include'] = (array) $args['include'];
				}

				$query .= ' AND yc.ID IN (' . esc_sql( implode( ',', $args['include'] ) ) . ')';
			}

			if ( ! empty( $args['exclude'] ) ) {
				if ( ! is_array( $args['exclude'] ) ) {
					$args['exclude'] = (array) $args['exclude'];
				}

				$query .= ' AND yc.ID IN (' . esc_sql( implode( ',', $args['exclude'] ) ) . ')';
			}

			if ( ! empty( $args['order_id'] ) ) {
				$query       .= ' AND yc.order_id = %d';
				$query_arg[] = $args['order_id'];
			}

			if ( ! empty( $args['user_id'] ) ) {
				$query       .= ' AND ya.user_id = %d';
				$query_arg[] = $args['user_id'];
			}

			if ( ! empty( $args['affiliate_id'] ) ) {
				$query       .= ' AND yc.affiliate_id = %d';
				$query_arg[] = $args['affiliate_id'];
			}

			if ( ! empty( $args['status'] ) ) {
				if ( ! is_array( $args['status'] ) && in_array( $args['status'], $this->_available_commission_status ) ) {
					$query       .= ' AND yc.status = %s';
					$query_arg[] = $args['status'];
				} elseif ( is_array( $args['status'] ) && $filtered_status = array_intersect( $args['status'], $this->_available_commission_status ) ) {
					$query .= ' AND yc.status IN ( "' . implode( '","', $filtered_status ) . '" )';
				}
			}

			if ( ! empty( $args['status__not_in'] ) ) {
				if ( ! is_array( $args['status__not_in'] ) && in_array( $args['status__not_in'], $this->_available_commission_status ) ) {
					$query       .= ' AND yc.status <> %s';
					$query_arg[] = $args['status__not_in'];
				} elseif ( is_array( $args['status__not_in'] ) && $filtered_status = array_intersect( $args['status__not_in'], $this->_available_commission_status ) ) {
					$query .= ' AND yc.status NOT IN ( "' . implode( '","', $filtered_status ) . '" )';
				}
			}

			if ( ! empty( $args['user_login'] ) ) {
				$query       .= ' AND u.user_login LIKE %s';
				$query_arg[] = '%' . $args['user_login'] . '%';
			}

			if ( ! empty( $args['user_email'] ) ) {
				$query       .= ' AND u.user_email LIKE %s';
				$query_arg[] = '%' . $args['user_email'] . '%';
			}

			if ( ! empty( $args['product_id'] ) ) {
				$query       .= ' AND p.ID = %d';
				$query_arg[] = $args['product_id'];
			}

			if ( ! empty( $args['product_name'] ) ) {
				$query       .= ' AND p.post_title LIKE %s';
				$query_arg[] = '%' . $args['product_name'] . '%';
			}

			if ( ! empty( $args['rate'] ) && is_array( $args['rate'] ) && ( isset( $args['rate']['min'] ) || isset( $args['rate']['max'] ) ) ) {
				if ( ! empty( $args['rate']['min'] ) ) {
					$query       .= ' AND yc.rate >= %f';
					$query_arg[] = $args['rate']['min'];
				}

				if ( ! empty( $args['rate']['max'] ) ) {
					$query       .= ' AND yc.rate <= %f';
					$query_arg[] = $args['rate']['max'];
				}
			}

			if ( ! empty( $args['amount'] ) && is_array( $args['amount'] ) && ( isset( $args['amount']['min'] ) || isset( $args['amount']['max'] ) ) ) {
				if ( ! empty( $args['amount']['min'] ) ) {
					$query       .= ' AND yc.amount >= %f';
					$query_arg[] = $args['amount']['min'];
				}

				if ( ! empty( $args['amount']['max'] ) ) {
					$query       .= ' AND yc.amount <= %f';
					$query_arg[] = $args['amount']['max'];
				}
			}

			if ( ! empty( $args['interval'] ) && is_array( $args['interval'] ) && ( isset( $args['interval']['start_date'] ) || isset( $args['interval']['end_date'] ) ) ) {
				if ( ! empty( $args['interval']['start_date'] ) ) {
					$query       .= ' AND yc.created_at >= %s';
					$query_arg[] = $args['interval']['start_date'];
				}

				if ( ! empty( $args['interval']['end_date'] ) ) {
					$query       .= ' AND yc.created_at <= %s';
					$query_arg[] = $args['interval']['end_date'];
				}
			}

			$query .= ' GROUP BY yc.ID';

			if ( ! empty( $args['orderby'] ) ) {
				$query .= sprintf( ' ORDER BY %s %s', $args['orderby'], $args['order'] );
			}

			if ( ! empty( $args['limit'] ) ) {
				$query .= sprintf( ' LIMIT %d, %d', ! empty( $args['offset'] ) ? $args['offset'] : 0, $args['limit'] );
			}

			// prepare query, if necessary
			$prepared_query = ! empty( $query_arg ) ? $wpdb->prepare( $query, $query_arg ) : $query;

			$res = $wpdb->get_results( $prepared_query, ARRAY_A );

			return $res;

		}

		/**
		 * Retrieve the commission with the given commission id
		 *
		 * @param $commission_id int Commission id
		 *
		 * @return mixed Commission row, or false on failure
		 * @since 1.0.0
		 */
		public function get_commission( $commission_id ) {
			global $wpdb;

			$query = "SELECT
			           yc.*,
			           u.ID AS user_id,
					   p.ID AS product_id,
					   p.post_title AS product_name,
					   u.user_login AS user_login,
					   u.user_email AS user_email
				      FROM {$wpdb->yith_commissions} AS yc
				      LEFT JOIN {$wpdb->yith_affiliates} AS ya ON ya.ID = yc.affiliate_id
				      LEFT JOIN {$wpdb->users} AS u ON u.ID = ya.user_id
				      LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON im.order_item_id = yc.line_item_id
			          LEFT JOIN {$wpdb->posts} AS p ON p.ID = im.meta_value
			          WHERE ( im.meta_key = '_product_id' || im.meta_key = '_variation_id' )
				      AND im.meta_value <> 0
				      AND yc.ID = %d";

			$query_args = array(
				$commission_id
			);

			$res = $wpdb->get_row( $wpdb->prepare( $query, $query_args ), ARRAY_A );

			return $res;
		}

		/**
		 * Get commission stats
		 *
		 * @param $stat string Id of the stat to retrieve [total_amount/total_refunds]
		 * @param $args mixed Filtering params<br/>
		 *              [<br/>
		 *              'status' => false,        // status of the commission ({@link \YITH_WCAF_Commission_Handler::$_available_commission_status})<br/>
		 *              'affiliate_id' => false,  // commission related affiliate ID<br/>
		 *              'user_id' => false,       // commission related user ID<br/>
		 *              'interval' => false       // commission date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/>
		 *              ]
		 *
		 * @return mixed Stat value, or false if stat does not match acceptable values
		 * @since 1.0.0
		 */
		public function get_commission_stats( $stat, $args = array() ) {
			global $wpdb;

			$available_stats = array(
				'total_amount'  => 'amount',
				'total_refunds' => 'refunds',
				'total_earned'  => 'meta_value'
			);

			if ( ! in_array( $stat, array_keys( $available_stats ) ) ) {
				return false;
			}

			$defaults = array(
				'status'       => false,
				'affiliate_id' => false,
				'user_id'      => false,
				'interval'     => false
			);

			$args = wp_parse_args( $args, $defaults );

			$query     = '';
			$query_arg = array();

			$query = "SELECT";

			if ( $stat == 'total_earned' ) {
				$query .= " SUM( im.{$available_stats[$stat]} - yc.amount )";
			} else {
				$query .= " SUM( yc.{$available_stats[$stat]} )";
			}

			$query .= " FROM {$wpdb->yith_commissions} AS yc
			          LEFT JOIN {$wpdb->yith_affiliates} AS ya ON yc.affiliate_id = ya.ID
			          LEFT JOIN {$wpdb->users} AS u ON ya.user_id = u.ID";

			if ( $stat == 'total_earned' ) {
				$query .= " LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON yc.line_item_id = im.order_item_id";
			}

			$query .= " WHERE 1=1";

			if ( $stat == 'total_earned' ) {
				$query       .= " AND meta_key = %s";
				$query_arg[] = '_line_total';
			}

			if ( ! empty( $args['status'] ) && ! is_array( $args['status'] ) ) {
				$query       .= ' AND yc.status = %s';
				$query_arg[] = $args['status'];
			} elseif ( ! empty( $args['status'] ) && is_array( $args['status'] ) ) {
				$first        = true;
				$param_string = '';

				foreach ( $args['status'] as $status ) {
					if ( ! $first ) {
						$param_string .= ', ';
					}
					$param_string .= '%s';

					$first = false;
				}

				$query .= ' AND yc.status IN ( ' . $param_string . ' )';

				$query_arg = array_merge(
					$query_arg,
					$args['status']
				);
			}

			if ( ! empty( $args['affiliate_id'] ) ) {
				$query       .= ' AND yc.affiliate_id = %d';
				$query_arg[] = $args['affiliate_id'];
			}

			if ( ! empty( $args['user_id'] ) ) {
				$query       .= ' AND yc.user_id = %d';
				$query_arg[] = $args['user_id'];
			}

			if ( ! empty( $args['interval'] ) && is_array( $args['interval'] ) && ( isset( $args['interval']['start_date'] ) || isset( $args['interval']['end_date'] ) ) ) {
				if ( ! empty( $args['interval']['start_date'] ) ) {
					$query       .= ' AND yc.created_at >= %s';
					$query_arg[] = $args['interval']['start_date'];
				}

				if ( ! empty( $args['interval']['end_date'] ) ) {
					$query       .= ' AND yc.created_at <= %s';
					$query_arg[] = $args['interval']['end_date'];
				}
			}

			if ( ! empty( $query_arg ) ) {
				$query = $wpdb->prepare( $query, $query_arg );
			}

			$res = $wpdb->get_var( $query );

			return $res;
		}

		/**
		 * Check if a commission with the given id exists in DB
		 *
		 * @param $commission_id int Commission id
		 *
		 * @return bool Whether commission exists or not
		 * @since 1.0.0
		 */
		public function commission_exists( $commission_id ) {
			global $wpdb;

			return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( yc.ID ) FROM {$wpdb->yith_commissions} AS yc WHERE yc.ID = %d", $commission_id ) );
		}

		/**
		 * Return an array of refunds registered for given commission
		 *
		 * @param $commission_id int Commission id
		 *
		 * @return mixed Array of registered commission refunds
		 * @since 1.0.0
		 */
		public function get_commission_refunds( $commission_id ) {
			$refunds    = array();
			$commission = $this->get_commission( $commission_id );

			if ( $commission ) {
				$order = wc_get_order( $commission['order_id'] );

				if ( $order ) {
					$refunds_objects = $order->get_refunds();

					if ( $refunds_objects ) {
						foreach ( $refunds_objects as $refund ) {
							$refund_id = yit_get_order_id( $refund );

							if ( $refund_id ) {
								$refunded_commissions = yit_get_prop( $refund, '_refunded_commissions', true );

								if ( isset( $refunded_commissions[ $commission_id ] ) ) {
									$refunds[ $refund_id ] = $refunded_commissions[ $commission_id ];
								}
							}
						}
					}
				}
			}

			return $refunds;
		}

		/**
		 * Return sum of all refunds registered for a given commission
		 *
		 * @param $commission_id int Commission id
		 *
		 * @return float Total refund
		 * @since 1.0.0
		 */
		public function get_total_commission_refund( $commission_id ) {
			return array_sum( $this->get_commission_refunds( $commission_id ) );
		}

		/**
		 * Return a human friendly version of a commission status
		 *
		 * @param $status string Status to convert to human friendly form
		 *
		 * @return string Human friendly status
		 * @since 1.0.0
		 */
		public function get_readable_status( $status ) {
			if ( isset( $this->_status_labels_map[ $status ] ) ) {
				$label = $this->_status_labels_map[ $status ];
			} else {
				$label = ucfirst( str_replace( '-', ' ', $status ) );
			}

			return apply_filters( "yith_wcaf_{$status}_commission_status_name", $label );
		}

		/**
		 * Change a commission amount, updating also referral amounts, if necessary
		 *
		 * @param $commission_id int Commission id
		 * @param $difference    float Signed amount to sum to commission total
		 * @param $note          string Note to register within the commission, to document amount change
		 *
		 * @return int|bool Number of commission table rows update, or false on failure
		 * @since 1.0.0
		 */
		public function change_commission_amount( $commission_id, $difference, $note = '' ) {
			$commission = $this->get_commission( $commission_id );

			if ( ! $commission ) {
				return false;
			}

			if ( in_array( $commission['status'], $this->_dead_status ) ) {
				return false;
			}

			$new_amount = $difference + (double) $commission['amount'];
			$new_amount = ( $new_amount > 0 ) ? $new_amount : 0;

			if ( in_array( $commission['status'], $this->_assigned_status ) ) {
				YITH_WCAF_Affiliate_Handler()->update_affiliate_total( $commission['affiliate_id'], (double) $difference );
			}

			wc_update_order_item_meta( $commission['line_item_id'], '_yith_wcaf_commission_amount', $new_amount );

			if ( ! empty( $note ) ) {
				$this->add_note(
					array(
						'commission_id' => $commission_id,
						'note_content'  => $note
					)
				);
			}

			return $this->update( $commission_id, array( 'amount' => $new_amount ) );
		}

		/**
		 * Change affiliate id of a specific commission, updating in the same time totals for both affiliates
		 *
		 * @param $commission_id    int Id of the commission
		 * @param $new_affiliate_id int Id of the receiver affiliate
		 * @param $note             string Note to add to the commission
		 *
		 * @return void
		 * @since 1.2.4
		 */
		public function change_commission_affiliate( $commission_id, $new_affiliate_id, $note = '' ) {
			$commission = $this->get_commission( $commission_id );

			if ( ! $commission ) {
				return;
			}

			$old_affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $commission['affiliate_id'] );
			$new_affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $new_affiliate_id );

			if ( ! $new_affiliate ) {
				return;
			}

			if ( $old_affiliate ) {
				if ( in_array( $commission['status'], $this->_assigned_status ) ) {
					YITH_WCAF_Affiliate_Handler()->update_affiliate_total( $old_affiliate['ID'], - 1 * $commission['amount'] );
				}

				if ( 'refunded' == $commission['status'] ) {
					YITH_WCAF_Affiliate_Handler()->update_affiliate_refunds( $old_affiliate['ID'], - 1 * $commission['amount'] );
				}
			}

			if ( ! empty( $note ) ) {
				$this->add_note(
					array(
						'commission_id' => $commission_id,
						'note_content'  => $note
					)
				);
			}

			YITH_WCAF_Commission_Handler()->update( $commission['ID'], array(
				'affiliate_id' => $new_affiliate_id
			) );

			if ( in_array( $commission['status'], $this->_assigned_status ) ) {
				YITH_WCAF_Affiliate_Handler()->update_affiliate_total( $new_affiliate_id, $commission['amount'] );
			}

			if ( 'refunded' == $commission['status'] ) {
				YITH_WCAF_Affiliate_Handler()->update_affiliate_refunds( $old_affiliate['ID'], $commission['amount'] );
			}
		}

		/**
		 * Change commission refunds
		 *
		 * @param $commission_id int Commission id
		 * @param $difference    float Signed amount to sum to commission total refunds
		 * @param $note          string Note to register within the commission, to document amount change
		 *
		 * @return int|bool Number of commission table rows update, or false on failure
		 * @since 1.0.0
		 */
		public function change_commission_refund( $commission_id, $difference, $note = '' ) {
			$commission = $this->get_commission( $commission_id );

			if ( ! $commission ) {
				return false;
			}

			if ( in_array( $commission['status'], $this->_dead_status ) ) {
				return false;
			}

			YITH_WCAF_Affiliate_Handler()->update_affiliate_refunds( $commission['affiliate_id'], (double) $difference );

			$new_amount = $difference + (double) $commission['refunds'];
			$new_amount = ( $new_amount > 0 ) ? $new_amount : 0;

			if ( ! empty( $note ) ) {
				$this->add_note(
					array(
						'commission_id' => $commission_id,
						'note_content'  => $note
					)
				);
			}

			return $this->update( $commission_id, array( 'refunds' => $new_amount ) );
		}

		/**
		 * Change commission status, updating referral totals if necessary
		 *
		 * @param $commission_id int Commission id
		 * @param $new_status    string New commission status
		 * @param $note          string Note to register within the commission, to document status change
		 *
		 * @return int|bool Number of commission table rows update, or false on failure
		 * @since 1.0.0
		 */
		public function change_commission_status( $commission_id, $new_status, $note = '' ) {
			$commission = $this->get_commission( $commission_id );

			if ( ! $commission ) {
				return false;
			}

			$old_status = $commission['status'];

			if ( ! isset( $this->_available_commission_status_change[ $old_status ] ) || ! in_array( $new_status, $this->_available_commission_status_change[ $old_status ] ) ) {
				return false;
			}

			$res = $this->update( $commission_id, array( 'status' => $new_status ) );

			// update affiliate 'earnings' field
			if ( in_array( $old_status, $this->_unassigned_status ) && in_array( $new_status, $this->_assigned_status ) ) {
				if ( $old_status == 'refunded' ) {
					$this->change_commission_amount( $commission_id, $commission['refunds'] + $this->get_total_commission_refund( $commission_id ) );
					$this->change_commission_refund( $commission_id, - 1 * (double) $commission['refunds'] - $this->get_total_commission_refund( $commission_id ) );
				}

				YITH_WCAF_Affiliate_Handler()->update_affiliate_total( $commission['affiliate_id'], (double) $commission['amount'] );
			} elseif ( in_array( $new_status, $this->_unassigned_status ) && in_array( $old_status, $this->_assigned_status ) ) {
				if ( $new_status == 'refunded' ) {
					$this->change_commission_amount( $commission_id, - 1 * (double) $commission['amount'] );
					$this->change_commission_refund( $commission_id, (double) $commission['amount'] );
				}

				YITH_WCAF_Affiliate_Handler()->update_affiliate_total( $commission['affiliate_id'], - 1 * (double) $commission['amount'] );
			}

			// update affiliate 'paid' fields
			if ( ! in_array( $old_status, $this->_payment_status ) && in_array( $new_status, $this->_payment_status ) ) {
				YITH_WCAF_Affiliate_Handler()->update_affiliate_payments( $commission['affiliate_id'], (double) $commission['amount'] );
			} elseif ( ! in_array( $new_status, $this->_payment_status ) && in_array( $old_status, $this->_payment_status ) ) {
				YITH_WCAF_Affiliate_Handler()->update_affiliate_payments( $commission['affiliate_id'], - 1 * (double) $commission['amount'] );
			}

			if ( ! empty( $note ) ) {
				$this->add_note(
					array(
						'commission_id' => $commission_id,
						'note_content'  => $note
					)
				);
			}

			do_action( 'yith_wcaf_commission_status_' . $new_status, $commission_id );
			do_action( 'yith_wcaf_commission_status_' . $old_status . '_to_' . $new_status, $commission_id );
			do_action( 'yith_wcaf_commission_status_changed', $commission_id, $new_status, $old_status );

			return $res;
		}

		/**
		 * Return commission default status for a given order status
		 *
		 * @param $order_status string Order status
		 *
		 * @return string Commission status
		 * @since 1.0.0
		 */
		public function map_commission_status( $order_status ) {
			foreach ( $this->_commission_order_status_map as $commission_status => $mapped_order_statuses ) {
				if ( in_array( $order_status, $mapped_order_statuses ) ) {
					return apply_filters( 'yith_wcaf_map_commission_status', $commission_status, $order_status );
				}
			}

			return apply_filters( 'yith_wcaf_default_commission_status', 'pending', $order_status );
		}

		/**
		 * Return a list of available commission status
		 *
		 * @return mixed Available status
		 * @since 1.0.0
		 */
		public function get_available_status() {
			return apply_filters( 'yith_wcaf_available_commission_status', $this->_available_commission_status );
		}

		/**
		 * Returns an array of available status for a given commission
		 *
		 * @param $commission_id int Commission id
		 *
		 * @return mixed Array of available status to switch to
		 * @since 1.0.0
		 */
		public function get_available_status_change( $commission_id ) {
			$commission = $this->get_commission( $commission_id );

			if ( ! $commission ) {
				return array();
			}

			if ( ! in_array( $commission['status'], array_keys( $this->_available_commission_status_change ) ) ) {
				return array();
			}

			return $this->_available_commission_status_change [ $commission['status'] ];
		}

		/**
		 * Return "dead status", that don't allow any status change
		 *
		 * @return array Dead status
		 * @since 1.0.0
		 */
		public function get_dead_status() {
			return apply_filters( 'yith_wcaf_dead_commission_status', $this->_dead_status );
		}

		/**
		 * Returns count of commissions, grouped by status
		 *
		 * @param $status string Specific status to count, or all to obtain a global statistic
		 * @param $args   mixed Array of arguments to filter status query<br/>
		 *                [<br/>
		 *                'user_id' => false,        // commission related affiliate user id (int)<br/>
		 *                'product_id' => false,     // commission related line item product id (int)<br/>
		 *                'interval' => false        // commission date range (array, with at lest one of this index: [start_date(string; mysql date format)|end_date(string; mysql date format)])<br/>
		 *                ]
		 *
		 * @return int|mixed Count per state, or array indexed by status, with status count
		 * @since 1.0.0
		 */
		public function per_status_count( $status = 'all', $args = array() ) {
			global $wpdb;

			$res = wp_cache_get( 'yith_wcaf_commissions_per_status_count' );

			if ( empty( $res ) ) {
				$query     = "SELECT yc.status, COUNT( yc.status ) AS status_count 
                      FROM {$wpdb->yith_commissions} AS yc
                      LEFT JOIN {$wpdb->yith_affiliates} AS ya ON ya.ID = yc.affiliate_id
                      LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON im.order_item_id = yc.line_item_id
                      LEFT JOIN {$wpdb->posts} AS p ON p.ID = im.meta_value 
                      WHERE ( im.meta_key = '_product_id' || im.meta_key = '_variation_id' )
				      AND im.meta_value <> 0";
				$query_arg = array();

				if ( ! empty( $args['product_id'] ) ) {
					$query       .= ' AND p.ID = %d';
					$query_arg[] = $args['product_id'];
				}

				if ( ! empty( $args['user_id'] ) ) {
					$query       .= ' AND ya.user_id = %d';
					$query_arg[] = $args['user_id'];
				}

				if ( ! empty( $args['interval'] ) && is_array( $args['interval'] ) && ( isset( $args['interval']['start_date'] ) || isset( $args['interval']['end_date'] ) ) ) {
					if ( ! empty( $args['interval']['start_date'] ) ) {
						$query       .= ' AND yc.created_at >= %s';
						$query_arg[] = $args['interval']['start_date'];
					}

					if ( ! empty( $args['interval']['end_date'] ) ) {
						$query       .= ' AND yc.created_at <= %s';
						$query_arg[] = $args['interval']['end_date'];
					}
				}

				$query .= " GROUP BY status";

				if ( ! empty( $query_arg ) ) {
					$query = $wpdb->prepare( $query, $query_arg );
				}

				$res = $wpdb->get_results( $query, ARRAY_A );

				wp_cache_set( 'yith_wcaf_commissions_per_status_count', $res );
			}

			$statuses = yith_wcaf_array_column( $res, 'status' );
			$counts   = yith_wcaf_array_column( $res, 'status_count' );

			if ( $status == 'all' ) {
				if ( $index = array_search( 'trash', $statuses ) ) {
					unset( $counts[ $index ] );
				}

				return array_sum( $counts );
			} elseif ( in_array( $status, $statuses ) ) {
				$index = array_search( $status, $statuses );

				if ( $index === false ) {
					return 0;
				} else {
					return $counts[ $index ];
				}
			} else {
				return 0;
			}
		}

		/* === PANEL COMMISSION METHODS === */

		/**
		 * Print commission panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_commission_panel() {
			// prepare user rates table items
			$commissions_table = new YITH_WCAF_Commissions_Table();
			$commissions_table->prepare_items();

			// require rate panel template
			include( YITH_WCAF_DIR . 'templates/admin/commission-panel-table.php' );
		}

		/**
		 * Add Screen option
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_screen_option() {
			if ( 'yith-plugins_page_yith_wcaf_panel' == get_current_screen()->id && ( ! isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && $_GET['tab'] == 'commissions' ) ) ) {
				add_screen_option( 'per_page', array(
					'label'   => __( 'Commissions', 'yith-woocommerce-affiliates' ),
					'default' => 20,
					'option'  => 'edit_commissions_per_page'
				) );

			}
		}

		/**
		 * Save custom screen options
		 *
		 * @param $set    bool Value to filter (default to false)
		 * @param $option string Custom screen option key
		 * @param $value  mixed Custom screen option value
		 *
		 * @return mixed Value to be saved as user meta; false if no value should be saved
		 */
		public function set_screen_option( $set, $option, $value ) {
			return ( ( ! isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && $_GET['tab'] == 'commissions' ) ) && 'edit_commissions_per_page' == $option ) ? $value : $set;
		}

		/**
		 * Add columns filters to commissions page
		 *
		 * @param $columns mixed Available columns
		 *
		 * @return mixed The columns array to print
		 * @since 1.0.0
		 */
		public function add_screen_columns( $columns ) {
			if ( ! isset( $_GET['tab'] ) || ( isset( $_GET['tab'] ) && $_GET['tab'] == 'commissions' ) ) {
				$columns = array_merge(
					$columns,
					array(
						'id'                  => __( 'ID', 'yith-woocommerce-affiliates' ),
						'status'              => __( 'Status', 'yith-woocommerce-affiliates' ),
						'order'               => __( 'Order', 'yith-woocommerce-affiliates' ),
						'user'                => __( 'User', 'yith-woocommerce-affiliates' ),
						'product'             => __( 'Product', 'yith-woocommerce-affiliates' ),
						'category'            => __( 'Category', 'yith-woocommerce-affiliates' ),
						'line_item_total'     => __( 'Total', 'yith-woocommerce-affiliates' ),
						'line_item_discounts' => __( 'Discounts', 'yith-woocommerce-affiliates' ),
						'line_item_refunds'   => __( 'Refunds', 'yith-woocommerce-affiliates' ),
						'rate'                => __( 'Rate', 'yith-woocommerce-affiliates' ),
						'amount'              => __( 'Amount', 'yith-woocommerce-affiliates' ),
						'payments'            => __( 'Payment', 'yith-woocommerce-affiliates' ),
						'date'                => __( 'Date', 'yith-woocommerce-affiliates' ),
						'actions'             => __( 'Action', 'yith-woocommerce-affiliates' ),
					)
				);
			}

			return $columns;
		}

		/**
		 * Process bulk action for current view
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function process_bulk_actions() {
			if ( ! empty( $_REQUEST['commissions'] ) ) {
				$current_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
				$current_action = ( empty( $current_action ) && isset( $_REQUEST['action2'] ) ) ? $_REQUEST['action2'] : $current_action;
				$redirect       = esc_url_raw( remove_query_arg( array( 'action', 'action2', 'commissions' ) ) );

				switch ( $current_action ) {
					case 'switch-to-pending':
						foreach ( $_REQUEST['commissions'] as $commission_id ) {
							$res      = YITH_WCAF_Commission_Handler()->change_commission_status( $commission_id, 'pending' );
							$redirect = esc_url_raw( add_query_arg( 'commission_status_change', $res, $redirect ) );
						}
						break;
					case 'switch-to-not-confirmed':
						foreach ( $_REQUEST['commissions'] as $commission_id ) {
							$res      = YITH_WCAF_Commission_Handler()->change_commission_status( $commission_id, 'not-confirmed' );
							$redirect = esc_url_raw( add_query_arg( 'commission_status_change', $res, $redirect ) );
						}
						break;
					case 'switch-to-cancelled':
						foreach ( $_REQUEST['commissions'] as $commission_id ) {
							$res      = YITH_WCAF_Commission_Handler()->change_commission_status( $commission_id, 'cancelled' );
							$redirect = esc_url_raw( add_query_arg( 'commission_status_change', $res, $redirect ) );
						}
						break;
					case 'switch-to-refunded':
						foreach ( $_REQUEST['commissions'] as $commission_id ) {
							$res      = YITH_WCAF_Commission_Handler()->change_commission_status( $commission_id, 'refunded' );
							$redirect = esc_url_raw( add_query_arg( 'commission_status_change', $res, $redirect ) );
						}
						break;
					case 'pay':
						$proceed_with_payment = false;
						$to_pay               = $_REQUEST['commissions'];

						// pay filtered commissions
						$res = YITH_WCAF_Payment_Handler()->register_payment( $to_pay, $proceed_with_payment );

						if ( ! $res['status'] ) {
							$errors   = is_array( $res['messages'] ) ? implode( ',', $res['messages'] ) : $res['messages'];
							$redirect = esc_url_raw( add_query_arg( array( 'commission_payment_failed' => urlencode( $errors ) ), $redirect ) );
						} else {
							$redirect = esc_url_raw( add_query_arg( array(
								'commission_paid'   => implode( ',', $res['can_be_paid'] ),
								'commission_unpaid' => implode( ',', $res['cannot_be_paid'] )
							), $redirect ) );
						}

						break;
					case 'delete':
						foreach ( $_REQUEST['commissions'] as $commission_id ) {
							$this->delete_single_commission( $commission_id, true, true );
						}
						break;
					case 'move-to-trash':
						$user    = wp_get_current_user();
						$message = sprintf( __( 'Moved to trash by %s', 'yith-woocommerce-affiliates' ), $user->user_login );

						foreach ( $_REQUEST['commissions'] as $commission_id ) {
							$this->change_commission_status( $commission_id, 'trash', $message );
						}
						break;
					case 'restore':
						foreach ( $_REQUEST['commissions'] as $commission_id ) {
							$this->restore_from_trash( $commission_id );
						}
						break;
				}

				if ( isset( $_GET['commission_id'] ) ) {
					return;
				}

				wp_redirect( $redirect );
				die();
			}
		}

		/* === ORDER METABOX METHODS === */

		/**
		 * Hide order item meta related to the plugin
		 *
		 * @param $to_hide mixed Array of order item meta to hide
		 *
		 * @return mixed Filtered array of values
		 * @since 1.0.0
		 */
		public function hide_order_item_meta( $to_hide ) {
			$to_hide = array_merge(
				$to_hide,
				array(
					'_yith_wcaf_commission_id',
					'_yith_wcaf_commission_rate',
					'_yith_wcaf_commission_amount'
				)
			);

			return $to_hide;
		}

		/**
		 * Add metabox to order edit page
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_order_metabox() {
			add_meta_box( 'yith_wcaf_order_referral_commissions', __( 'Referral Commissions', 'yith-woocommerce-affiliates' ), array(
				$this,
				'print_referral_commissions_metabox'
			), array( 'shop_order', 'shop_subscription' ), 'side' );
		}

		/**
		 * Print commission order metabox
		 *
		 * @param $post \WP_Post Current order post object
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_referral_commissions_metabox( $post ) {
			// set order id
			$order_id = $post->ID;

			// if we're on wc subscription page, use subscription parent order
			if ( 'shop_subscription' == $post->post_type ) {
				$order_id = $post->post_parent;
			}

			$order = wc_get_order( $order_id );

			// define variables to be used on template
			$token     = yit_get_prop( $order, '_yith_wcaf_referral', true );
			$affiliate = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_token( $token );

			if ( $affiliate ) {
				$referral = $affiliate['user_id'];

				if ( $referral ) {
					$user_data = get_userdata( $referral );

					if ( ! $user_data ) {
						return;
					}

					$user_email = $user_data->user_email;

					$username = '';
					if ( $user_data->first_name || $user_data->last_name ) {
						$username .= esc_html( ucfirst( $user_data->first_name ) . ' ' . ucfirst( $user_data->last_name ) );
					} else {
						$username .= esc_html( ucfirst( $user_data->display_name ) );
					}

					$commissions      = YITH_WCAF_Commission_Handler()->get_commissions( array(
						'order_id'       => $order_id,
						'status__not_in' => 'trash'
					) );
					$referral_history = yit_get_prop( $order, '_yith_wcaf_referral_history', true );
				}
			}

			$args = array(
				'order'            => $order,
				'referral'         => isset( $referral ) ? $referral : '',
				'user_email'       => isset( $user_email ) ? $user_email : '',
				'username'         => isset( $username ) ? $username : '',
				'commissions'      => isset( $commissions ) ? $commissions : '',
				'referral_history' => isset( $referral_history ) ? $referral_history : ''

			);

			yith_wcaf_get_template( 'referral-commissions-metabox.php', $args, 'admin' );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Commission_Handler
		 * @since 1.0.2
		 */
		public static function get_instance() {
			if ( class_exists( 'YITH_WCAF_Commission_Handler_Premium' ) ) {
				return YITH_WCAF_Commission_Handler_Premium::get_instance();
			} else {
				if ( is_null( YITH_WCAF_Commission_Handler::$instance ) ) {
					YITH_WCAF_Commission_Handler::$instance = new YITH_WCAF_Commission_Handler;
				}

				return YITH_WCAF_Commission_Handler::$instance;
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Commission_Handler class
 *
 * @return \YITH_WCAF_Commission_Handler
 * @since 1.0.0
 */
function YITH_WCAF_Commission_Handler() {
	return YITH_WCAF_Commission_Handler::get_instance();
}
