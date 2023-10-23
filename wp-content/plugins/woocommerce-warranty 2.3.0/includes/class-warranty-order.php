<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Automattic\WooCommerce\Utilities\OrderUtil;

class Warranty_Order {

	public function __construct() {
		// Initialize order meta. Need to be called after plugins_loaded because of WC_VERSION check.
		add_action( 'plugins_loaded', array( $this, 'init_order_meta' ), 20 );
	}

	/**
	 * Actions for order item meta.
	 *
	 * @since 1.8.6
	 * @return void
	 */
	public function init_order_meta() {
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_item_meta' ), 10, 3 );

		// order status changed.
		add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 10, 4 );

		// Display item warranty.
		add_action( 'woocommerce_before_order_itemmeta', array( $this, 'render_order_item_warranty' ), 10, 2 );
		add_action( 'woocommerce_order_item_meta_end', array( $this, 'render_order_item_warranty' ), 10, 2 );

		add_action( 'woocommerce_email_after_order_table', array( $this, 'display_custom_form_key' ), 10, 4 );
		add_filter( 'woocommerce_order_query_args', array( $this, 'add_warranty_key_query_vars_for_hpos' ), 10, 1 );
		add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( $this, 'add_warranty_key_query_vars_for_cpt' ), 10, 2 );
	}

	/**
	 * Listens to order status changes and sets the completed date if the current
	 * order status matches the start status of the warranty period
	 *
	 * @param int      $order_id
	 * @param string   $old_status
	 * @param string   $new_status
	 * @param WC_Order $order Actual order
	 *
	 * @throws \WC_Data_Exception
	 */
	public function order_status_changed( $order_id, $old_status, $new_status, $order ) {
		// update order's date of completion
		$handler = function () use ( $order ) {
			$order->set_date_completed( current_time( 'mysql' ) );
			$order->save();
		};

		$this->handle_status_change( $order_id, $new_status, $handler );
	}

	/**
	 * Handler for the order status change.
	 *
	 * @param int      $order_id
	 * @param string   $new_status
	 * @param callable $handler
	 */
	private function handle_status_change( $order_id, $new_status, $handler ) {
		global $woocommerce;

		$order = wc_get_order( $order_id );

		if ( 'completed' !== $new_status ) {
			return;
		}

		$items        = $order->get_items();
		$has_warranty = false;

		foreach ( $items as $item ) {
			$warranty    = false;
			$addon_index = false;
			$metas       = ( isset( $item['item_meta'] ) ) ? $item['item_meta'] : array();

			foreach ( $metas as $key => $value ) {
				if ( '_item_warranty' === $key ) {
					$warranty = maybe_unserialize( $value );
				}
			}

			if ( $warranty ) {
				// update order's date of completion.
				$handler( $order );

				break; // only need to update once per order.
			}
		}
	}

	/**
	 * Include add-ons line item meta.
	 *
	 * @param  WC_Order_Item_Product $item          Order item data.
	 * @param  string                $cart_item_key Cart item key.
	 * @param  array                 $values        Order item values.
	 */
	public function order_item_meta( $item, $cart_item_key, $values ) {
		$_product       = $values['data'];
		$_prod_id       = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->get_id();
		$warranty       = warranty_get_product_warranty( $_prod_id );
		$warranty_label = $warranty['label'];

		if ( $warranty ) {
			if ( $warranty['type'] == 'addon_warranty' ) {
				$warranty_index = isset( $values['warranty_index'] ) ? $values['warranty_index'] : false;

				$item->update_meta_data( '_item_warranty_selected', $warranty_index );
			}

			if ( 'no_warranty' !== $warranty['type'] ) {
				$item->update_meta_data( '_item_warranty', $warranty );
			}
		}
	}

	/**
	 * Check if an order contain items that have valid warranties
	 *
	 * @param WC_Order $order
	 *
	 * @return bool
	 */
	public static function order_has_warranty( $order ) {

		$items        = $order->get_items();
		$has_warranty = false;
		$warranty     = false;
		$addon_index  = null;

		foreach ( $items as $item ) {
			$metas = ! empty( $item['item_meta'] ) ? $item['item_meta'] : array();

			foreach ( $metas as $key => $value ) {
				if ( $key == '_item_warranty' ) {
					$warranty = maybe_unserialize( $value );
				} elseif ( $key == '_item_warranty_selected' ) {
					$addon_index = $value;
				}
			}

			if ( $warranty !== false ) {
				// order's date of completion must be within the warranty period.
				$completed = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;

				if ( 'addon_warranty' === $warranty['type'] ) {
					$valid_until = false;
					$addon       = ( isset( $warranty['addons'][ $addon_index ] ) ) ? $warranty['addons'][ $addon_index ] : null;

					if ( ! $addon ) {
						continue;
					}

					if ( ! empty( $completed ) ) {
						$valid_until = strtotime( $completed . ' +' . $addon['value'] . ' ' . $addon['duration'] );
					}

					if ( $valid_until && current_time( 'timestamp' ) < $valid_until ) {
						$has_warranty = true;
						break;
					}
				} elseif ( 'included_warranty' === $warranty['type'] ) {
					if ( 'lifetime' === $warranty['length'] ) {
						$has_warranty = true;
						break;
					} else {
						// order's date of completion must be within the warranty period
						$valid_until = false;

						if ( ! empty( $completed ) ) {
							$valid_until = strtotime( $completed . ' +' . $warranty['value'] . ' ' . $warranty['duration'] );
						}

						if ( $valid_until && current_time( 'timestamp' ) < $valid_until ) {
							$has_warranty = true;
							break;
						}
					}
				}
			}
		}

		if ( ! $has_warranty ) {
			$query_args = array(
				'post_type' => 'warranty_request',
				'orderby'   => 'date',
				'order'     => 'DESC',
			);

			$query_args['meta_query'][] = array(
				'key'     => '_order_id',
				'value'   => $order->get_id(),
				'compare' => '=',
			);

			$wp_query = new WP_Query();
			$wp_query->query( $query_args );

			$total_items = $wp_query->found_posts;
			wp_reset_postdata();

			if ( $total_items > 0 ) {
				$has_warranty = true;
			}
		}

		return apply_filters( 'order_has_warranty', $has_warranty, $order );
	}

	/**
	 * Display an order item's warranty data
	 *
	 * @param int    $item_id
	 * @param array  $item
	 * @param object $object Can be WC_Product or WC_Order.
	 */
	public function render_order_item_warranty( $item_id, $item ) {
		global $post;

		if ( $item['type'] != 'line_item' ) {
			return;
		}

		$warranty = warranty_get_order_item_warranty( $item );

		if ( is_callable( array( $item, 'get_order_id' ) ) ) {
			$order_id = $item->get_order_id();
		} elseif ( $post ) {
			$order_id = $post->ID;
      // Security is taken care of in the hook trigger (hooks: woocommerce_before_order_itemmeta, woocommerce_order_item_meta_end)
      // phpcs:ignore WordPress.Security.NonceVerification.Missing
		} elseif ( isset( $_POST['order_id'] ) ) {
      // phpcs:ignore WordPress.Security.NonceVerification.Missing
      $order_id = filter_var( $_POST['order_id'], FILTER_SANITIZE_NUMBER_INT );
		}

		if ( $warranty && ! empty( $order_id ) ) {
			include WooCommerce_Warranty::$base_path . '/templates/admin/order-item-warranty.php';
		}
	}

	/**
	 * Display custom form key.
	 *
	 * @param WC_Order $order         Order object.
	 * @param bool     $sent_to_admin Whether the email is being sent to admin or not.
	 * @param bool     $plain_text    Whether email is in plain text or not.
	 * @param WC_Email $email         Email object.
	 */
	public function display_custom_form_key( $order, $sent_to_admin, $plain_text = null, $email = null ) {
		/*
		Check if :
		- $order is really an order object.
		- current email is for customer completed order.
		- current order has a product with warranty.
		- current order is submitted by guest customer.
		*/
		if ( ! $order instanceof WC_Order || ! $email instanceof WC_Email_Customer_Completed_Order || ! self::order_has_warranty( $order ) || false !== $order->get_user() ) {
			return;
		}

		$order_key       = $order->get_order_key();
		$warranty_key    = 'WC_Warranty_' . md5( $order_key );
		$return_form_url = get_option( 'warranty_return_form_url' );

		if ( empty( $return_form_url ) ) {
			return;
		}

		$order->update_meta_data( '_wc_warranty_key', $warranty_key );
		$order->save();

		$claim_url = add_query_arg(
			array(
				'wak' => $warranty_key,
			),
			$return_form_url
		);
		?>
		<h2><?php esc_html_e( 'Claim Warranty', 'wc_warranty' ); ?></h2>
		<div style="margin-bottom:40px;">
		<?php
		// translators: %1$s : link opener for the claim URL, %2$s : link closer, %3$s : claim URL.
		echo sprintf( esc_html__( 'You can claim the warranty by clicking this %1$slink%2$s or go to this URL : %3$s.', 'wc_warranty' ), '<a href="' . esc_url( $claim_url ) . '">', '</a>', esc_url( $claim_url ) );
		?>
		</div>
		<?php
	}

	/**
	 * Handle a custom 'customvar' query var to get orders with the 'customvar' meta for HPOS.
	 *
	 * @param array $query_vars - Query vars from WC_Order_Query.
	 *
	 * @return array modified $query_vars
	 */
	public function add_warranty_key_query_vars_for_hpos( $query_vars ) {
		if ( ! OrderUtil::custom_orders_table_usage_is_enabled() ) {
			return $query_vars;
		}

		if ( ! empty( $query_vars['warranty_key'] ) ) {
			$query_vars['meta_query'][] = array(
				'key'   => '_wc_warranty_key',
				'value' => esc_attr( $query_vars['warranty_key'] ),
			);
		}

		return $query_vars;
	}

	/**
	 * Handle a custom 'customvar' query var to get orders with the 'customvar' meta for `shop_order` custom post type.
	 *
	 * @param array $query      Main query of WC_Order_Query.
	 * @param array $query_vars Query vars from WC_Order_Query.
	 *
	 * @return array modified $query.
	 */
	public function add_warranty_key_query_vars_for_cpt( $query, $query_vars ) {
		if ( ! empty( $query_vars['warranty_key'] ) ) {
			$query['meta_query'][] = array(
				'key'   => '_wc_warranty_key',
				'value' => esc_attr( $query_vars['warranty_key'] ),
			);
		}

		return $query;
	}
}

new Warranty_Order();
