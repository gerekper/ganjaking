<?php
! defined( 'YITH_POS' ) && exit; // Exit if accessed directly


if ( ! class_exists( 'YITH_POS_Stock_Management' ) ) {
	/**
	 * Class YITH_POS_Stock_Management
	 * Multistock Class
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	class YITH_POS_Stock_Management {

		/** @var YITH_POS_Stock_Management */
		private static $_instance;


		/** @var bool */
		private $_is_enabled;

		/** @var bool */
		private $_condition;


		/**
		 * Singleton implementation
		 *
		 * @return YITH_POS_Stock_Management
		 */
		public static function get_instance() {
			return ! is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
		}

		/**
		 * YITH_POS_Stock_Management constructor.
		 */
		private function __construct() {
			$this->_is_enabled = get_option( 'yith_pos_multistock_enabled', 'no' ) === 'yes';
			add_action( 'woocommerce_product_options_stock_status', array( $this, 'add_options' ), 10 );
			add_action( 'woocommerce_variation_options_inventory', array( $this, 'add_options_on_variations' ), 10, 3 );

			if ( ! $this->_is_enabled ) {
				return;
			}

			$this->_condition = get_option( 'yith_pos_multistock_condition', 'allowed' );


			add_action( 'woocommerce_admin_process_product_object', array( $this, 'set_product_meta_before_saving' ), 10, 1 );

			add_action( 'woocommerce_save_product_variation', array( $this, 'save_custom_fields_for_variation_products' ), 10, 2 );

			add_filter( 'woocommerce_can_reduce_order_stock', array( $this, 'reduce_order_stock' ), 100, 2 );
			add_filter( 'woocommerce_can_restore_order_stock', array( $this, 'restore_order_stock' ), 100, 2 );

			add_action( 'admin_enqueue_scripts', array( $this, 'prevent_restock_items_js' ), 20 );
			/**
			 * todo: automatic restock items for refunds
			 * this code is commented since a filter is missing in WooCommerce
			 * see this Pull Request: https://github.com/woocommerce/woocommerce/pull/25257
			 * when the filter will be added, we can de-comment this line of code
			 * and remove the 'prevent_restock_items_js' method and JS
			 */
			//add_action( 'woocommerce_refund_created', array( $this, 'restock_items_on_refund' ), 10, 2 );
		}

		public function prevent_restock_items_js() {
			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			if ( $screen && 'shop_order' === $screen->id && isset( $_GET[ 'post' ] ) ) {
				$order_id = absint( $_GET[ 'post' ] );
				if ( yith_pos_is_pos_order( $order_id ) ) {
					wp_enqueue_script( 'yith-pos-admin-prevent-restock-items' );

				}
			}
		}

		/**
		 * @param WC_Order_Refund $refund
		 * @param array           $args
		 */
		public function restock_items_on_refund( $refund, $args ) {
			if ( yith_pos_is_pos_order( $args[ 'order_id' ] ) ) {
				$order = wc_get_order( $args[ 'order_id' ] );
				if ( $order ) {
					$store_id = $order->get_meta( '_yith_pos_store' );
					if ( $store_id ) {
						$refunded_line_items = $args[ 'line_items' ];
						$line_items          = $order->get_items();

						foreach ( $line_items as $item_id => $item ) {
							if ( ! isset( $refunded_line_items[ $item_id ], $refunded_line_items[ $item_id ][ 'qty' ] ) ) {
								continue;
							}
							$product            = $item->get_product();
							$item_stock_reduced = $item->get_meta( '_reduced_stock', true );
							$qty_to_refund      = $refunded_line_items[ $item_id ][ 'qty' ];

							if ( ! $item_stock_reduced || ! $qty_to_refund || ! $product || ! $product->managing_stock() ) {
								continue;
							}

							$old_stock   = $this->get_stock_amount( $product, $store_id );
							$new_stock   = false;
							$restored_in = false;
							if ( $old_stock !== false && $store_id === $item->get_meta( '_yith_pos_reduced_stock_by_store' ) ) {
								$new_stock   = $this->update_product_stock( $product, $qty_to_refund, $store_id, $old_stock, 'increase' );
								$restored_in = 'store';
							} elseif ( $item->get_meta( '_yith_pos_reduced_stock_by_general' ) ) {
								$new_stock   = wc_update_product_stock( $product, $qty_to_refund, 'increase' );
								$restored_in = 'general';
							}

							// Update _reduced_stock meta to track changes.
							$item_stock_reduced = $item_stock_reduced - $qty_to_refund;

							if ( 0 < $item_stock_reduced ) {
								$item->update_meta_data( '_reduced_stock', $item_stock_reduced );
								switch ( $restored_in ) {
									case 'store':
										$item->update_meta_data( '_yith_pos_reduced_stock_by_store_qty', $item_stock_reduced );
										break;
									case 'general':
										$item->update_meta_data( '_yith_pos_reduced_stock_by_general', $item_stock_reduced );
										break;
								}
							} else {
								$item->delete_meta_data( '_reduced_stock' );
								switch ( $restored_in ) {
									case 'store':
										$item->delete_meta_data( '_yith_pos_reduced_stock_by_store' );
										$item->delete_meta_data( '_yith_pos_reduced_stock_by_store_qty' );
										break;
									case 'general':
										$item->delete_meta_data( '_yith_pos_reduced_stock_by_general' );
										break;
								}
							}

							/* translators: 1: product ID 2: old stock level 3: new stock level */
							$order->add_order_note( sprintf( __( 'Item #%1$s stock increased from %2$s to %3$s.', 'woocommerce' ), $product->get_id(), $old_stock, $new_stock ) );

							$item->save();

							do_action( 'woocommerce_restock_refunded_item', $product->get_id(), $old_stock, $new_stock, $order, $product );
						}
					}
				}
			}
		}


		/**
		 * Check if the order is a POS order and reduce stock level
		 *
		 * @param $response bool
		 * @param $order    WC_Order
		 *
		 * @return mixed
		 */
		public function reduce_order_stock( $response, $order ) {
			if ( $response ) {
				if ( yith_pos_is_pos_order( $order ) ) {
					$pos_store = $order->get_meta( '_yith_pos_store' );
					! empty( $pos_store ) && $this->reduce_stock_levels( $order, $pos_store );

					return false;
				}
			}

			return $response;
		}

		/**
		 * Check if the order is a POS order and restore stock level
		 *
		 * @param $response bool
		 * @param $order    WC_Order
		 *
		 * @return mixed
		 */
		public function restore_order_stock( $response, $order ) {
			if ( $response ) {
				if ( yith_pos_is_pos_order( $order ) ) {
					$pos_store = $order->get_meta( '_yith_pos_store' );
					! empty( $pos_store ) && $this->restore_stock_levels( $order, $pos_store );

					return false;
				}
			}

			return $response;
		}


		/**
		 * @param WC_Order $order
		 * @param int      $store_id
		 */
		public function reduce_stock_levels( $order, $store_id ) {

			// We need an order, and a store with stock management to continue.
			if ( ! $order || 'yes' !== get_option( 'woocommerce_manage_stock' ) || ! apply_filters( 'yith_pos_can_reduce_order_stock', true, $order ) ) {
				return;
			}

			$changes = array();

			// Loop over all items.
			foreach ( $order->get_items() as $item ) {
				if ( ! $item->is_type( 'line_item' ) ) {
					continue;
				}

				// Only reduce stock once for each item.
				/** @var WC_Product $product */
				$product            = $item->get_product();
				$item_stock_reduced = $item->get_meta( '_reduced_stock', true );

				if ( $item_stock_reduced || ! $product || ! $product->managing_stock() ) {
					continue;
				}

				$qty       = apply_filters( 'woocommerce_order_item_quantity', $item->get_quantity(), $order, $item );
				$item_name = $product->get_formatted_name();

				$product_id_with_stock = $product->get_stock_managed_by_id();
				$product               = $product_id_with_stock !== $product->get_id() ? wc_get_product( $product_id_with_stock ) : $product;

				$stock_amount = $this->get_stock_amount( $product, $store_id );
				$new_stock    = false;
				$reduced_by   = false;

				if ( $stock_amount !== false ) {
					$new_stock  = $this->update_product_stock( $product, $qty, $store_id, $stock_amount, 'decrease' );
					$reduced_by = 'store';
				} elseif ( 'no' === $product->get_meta( '_yith_pos_multistock_enabled' ) || 'general' === $this->_condition ) {
					$new_stock  = wc_update_product_stock( $product, $qty, 'decrease' );
					$reduced_by = 'general';
				}


				if ( is_wp_error( $new_stock ) ) {
					/* translators: %s item name. */
					$order->add_order_note( sprintf( __( 'Unable to reduce stock for item %s.', 'woocommerce' ), $item_name ) );
					continue;
				}

				if ( false !== $new_stock ) {
					$item->add_meta_data( '_reduced_stock', $qty, true );
					switch ( $reduced_by ) {
						case 'store':
							$item->add_meta_data( '_yith_pos_reduced_stock_by_store', $store_id, true );
							$item->add_meta_data( '_yith_pos_reduced_stock_by_store_qty', $qty, true );
							break;
						case 'general':
							$item->add_meta_data( '_yith_pos_reduced_stock_by_general', $qty, true );
							break;
					}

					$item->save();

					$changes[] = array(
						'product' => $product,
						'from'    => $new_stock + $qty,
						'to'      => $new_stock,
					);
				}
			}

			if ( $changes ) {
				wc_trigger_stock_change_notifications( $order, $changes );
				do_action( 'yith_pos_reduce_order_stock', $order );
			}
		}

		/**
		 * @param WC_Order $order
		 * @param int      $store_id
		 */
		public function restore_stock_levels( $order, $store_id ) {
			// We need an order, and a store with stock management to continue.
			if ( ! $order || 'yes' !== get_option( 'woocommerce_manage_stock' ) || ! apply_filters( 'yith_pos_can_restore_order_stock', true, $order ) ) {
				return;
			}

			$changes = array();

			// Loop over all items.
			foreach ( $order->get_items() as $item ) {
				if ( ! $item->is_type( 'line_item' ) ) {
					continue;
				}

				// Only increase stock once for each item.
				/** @var WC_Product $product */
				$product            = $item->get_product();
				$item_stock_reduced = $item->get_meta( '_reduced_stock', true );

				$product_id_with_stock = $product->get_stock_managed_by_id();
				$product               = $product_id_with_stock !== $product->get_id() ? wc_get_product( $product_id_with_stock ) : $product;

				if ( ! $item_stock_reduced || ! $product || ! $product->managing_stock() ) {
					continue;
				}

				$item_name    = $product->get_formatted_name();
				$stock_amount = $this->get_stock_amount( $product, $store_id );
				$new_stock    = false;
				$restored_in  = false;
				if ( $stock_amount !== false && $store_id === $item->get_meta( '_yith_pos_reduced_stock_by_store' ) ) {
					$new_stock   = $this->update_product_stock( $product, $item_stock_reduced, $store_id, $stock_amount, 'increase' );
					$restored_in = 'store';
				} elseif ( $item->get_meta( '_yith_pos_reduced_stock_by_general' ) ) {
					$new_stock   = wc_update_product_stock( $product, $item_stock_reduced, 'increase' );
					$restored_in = 'general';
				}


				if ( is_wp_error( $new_stock ) ) {
					/* translators: %s item name. */
					$order->add_order_note( sprintf( __( 'Unable to restore stock for item %s.', 'woocommerce' ), $item_name ) );
					continue;
				}

				$item->delete_meta_data( '_reduced_stock' );
				switch ( $restored_in ) {
					case 'store':
						$item->delete_meta_data( '_yith_pos_reduced_stock_by_store' );
						$item->delete_meta_data( '_yith_pos_reduced_stock_by_store_qty' );
						break;
					case 'general':
						$item->delete_meta_data( '_yith_pos_reduced_stock_by_general' );
						break;
				}
				$item->save();

				$changes[] = $item_name . ' ' . ( $new_stock - $item_stock_reduced ) . '&rarr;' . $new_stock;
			}

			if ( $changes ) {
				$order->add_order_note( __( 'Stock levels increased:', 'woocommerce' ) . ' ' . implode( ', ', $changes ) );
			}

			do_action( 'yith_pos_restore_order_stock', $order );
		}

		/**
		 * @param $product WC_Product
		 * @param $qty
		 * @param $store_id
		 * @param $stock_amount
		 * @param $operation
		 *
		 * @return mixed
		 */
		public function update_product_stock( $product, $qty, $store_id, $stock_amount, $operation ) {

			// Calculate new value.
			switch ( $operation ) {
				case 'increase':
					$new_stock = $stock_amount + wc_stock_amount( $qty );
					break;
				default:
					$new_stock = $stock_amount - wc_stock_amount( $qty );
					break;
			}


			$multistock = $product->get_meta( '_yith_pos_multistock' );
			if ( isset( $multistock[ $store_id ] ) ) {

				$multistock[ $store_id ] = $new_stock;
				$product->update_meta_data( '_yith_pos_multistock', $multistock );
				$product->save();
			}

			return $new_stock;
		}


		/**
		 * get the product stock based of the store id
		 *
		 * @param WC_Product $product
		 * @param int        $store_id
		 *
		 * @return bool|mixed
		 */
		public function get_stock_amount( $product, $store_id ) {
			$stock_quantity      = false;
			$multi_stock_enabled = $product->get_meta( '_yith_pos_multistock_enabled' );

			if ( 'yes' === $multi_stock_enabled ) {
				$multi_stock = $product->get_meta( '_yith_pos_multistock' );
				if ( ! empty( $multi_stock ) && isset( $multi_stock[ $store_id ] ) ) {
					$stock_quantity = $multi_stock[ $store_id ];
				}
			}

			return $stock_quantity;

		}


		/**
		 * Add opitions in products
		 */
		public function add_options() {
			global $thepostid;
			$product = wc_get_product( $thepostid );

			if ( ! in_array( $product->get_type(), self::get_allowed_product_types() ) ) {
				return;
			}

			$is_enabled = $product->get_meta( '_yith_pos_multistock_enabled' );
			$is_enabled = empty( $is_enabled ) ? 'no' : $is_enabled;

			$args = array(
				'is_enabled'         => $is_enabled,
				'multistock_enabled' => $this->is_enabled(),
				'multistock'         => $product->get_meta( '_yith_pos_multistock' )
			);

			yith_pos_get_view( 'product/product-data-inventory.php', $args );
		}

		/**
		 * Set product meta before saving
		 *
		 * @param WC_Product $product
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function set_product_meta_before_saving( $product ) {
			if ( ! in_array( $product->get_type(), self::get_allowed_product_types() ) ) {
				return;
			}

			$multi_stock_enabled = isset( $_POST[ '_yith_pos_multistock_enabled' ] ) ? 'yes' : 'no';

			$multi_stock      = array();
			$post_multi_stock = isset( $_POST[ '_yith_pos_multistock' ] ) ? $_POST[ '_yith_pos_multistock' ] : array();

			if ( $post_multi_stock ) {
				foreach ( $post_multi_stock as $stock ) {
					if ( ! empty( $stock[ 'store' ] ) ) {
						$multi_stock[ $stock[ 'store' ] ] = intval( $stock[ 'stock' ] );
					}
				}
			}

			$product->update_meta_data( '_yith_pos_multistock_enabled', $multi_stock_enabled );
			$product->update_meta_data( '_yith_pos_multistock', $multi_stock );
		}


		/**
		 * @param $loop
		 * @param $variation_data
		 * @param $variation WC_Product_Variation
		 */
		public function add_options_on_variations( $loop, $variation_data, $variation ) {
			$variation  = wc_get_product( $variation->ID );
			$is_enabled = $variation->get_meta( '_yith_pos_multistock_enabled' );
			$is_enabled = empty( $is_enabled ) ? 'no' : $is_enabled;

			$args = array(
				'is_enabled'         => $is_enabled,
				'multistock_enabled' => $this->is_enabled(),
				'multistock'         => $variation->get_meta( '_yith_pos_multistock' ),
				'loop'               => $loop
			);

			yith_pos_get_view( 'product/product-data-inventory.php', $args );
		}

		/**
		 * Save custom fields for variation products
		 *
		 * @param int $variation_id
		 * @param int $index
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function save_custom_fields_for_variation_products( $variation_id, $index ) {
			$product                   = wc_get_product( $variation_id );
			$multi_stock_enabled_array = ! empty( $_POST[ '_yith_pos_multistock_enabled' ] ) ? $_POST[ '_yith_pos_multistock_enabled' ] : array();
			$post_multi_stock_array    = ! empty( $_POST[ '_yith_pos_multistock' ] ) ? $_POST[ '_yith_pos_multistock' ] : array();

			$multi_stock_enabled = isset( $multi_stock_enabled_array[ $index ] ) ? 'yes' : 'no';
			$multi_stock         = array();
			$post_multi_stock    = isset( $post_multi_stock_array[ $index ] ) ? $post_multi_stock_array[ $index ] : array();

			foreach ( $post_multi_stock as $stock ) {
				if ( ! empty( $stock[ 'store' ] ) ) {
					$multi_stock[ $stock[ 'store' ] ] = intval( $stock[ 'stock' ] );
				}
			}

			$product->update_meta_data( '_yith_pos_multistock_enabled', $multi_stock_enabled );
			$product->update_meta_data( '_yith_pos_multistock', $multi_stock );

			$product->save_meta_data();
		}

		/**
		 * Return the allowed product types to manage stock.
		 *
		 * @return mixed|void
		 */
		public static function get_allowed_product_types() {
			$allowed_product_types = array( 'simple', 'variable' );

			return apply_filters( 'yith_pos_multistock_product_types', $allowed_product_types );
		}

		/**
		 * Return if the general option to manage multistock is enabled.
		 *
		 * @return bool
		 */
		public function is_enabled() {
			return $this->_is_enabled;
		}


	}


	/**
	 * Unique access to instance of YITH_POS_Stock_Management class
	 *
	 * @return YITH_POS_Stock_Management
	 * @since 1.0.0
	 */
	function YITH_POS_Stock_Management() {
		return YITH_POS_Stock_Management::get_instance();
	}
}
