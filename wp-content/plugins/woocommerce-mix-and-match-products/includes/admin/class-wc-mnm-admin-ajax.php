<?php
/**
 * WC_MNM_Admin_Ajax class
 *
 * @package  WooCommerce Mix and Match/Admin/Ajax
 * @since    1.7.0
 * @deprecated 2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin AJAX meta-box handlers.
 *
 * @class    WC_MNM_Admin_Ajax
 * @version  2.1.0
 */
class WC_MNM_Admin_Ajax {

	/**
	 * Hook in.
	 */
	public static function init() {}

	/*
	|--------------------------------------------------------------------------
	| Edit-Order.
	|--------------------------------------------------------------------------
	*/

	/**
	 * True when displaying content in an edit-container order item modal.
	 *
	 * @return bool
	 */
	public static function is_container_edit_request() {
		return doing_action( 'wp_ajax_woocommerce_edit_container_in_order' );
	}

	/**
	 * Form content used to populate "Configure/Edit" container order item modals.
	 */
	public static function ajax_container_order_item_form() {

		$result = self::can_edit_container();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		global $product;

		// Populate $order, $product, and $order_item variables.
		extract( $result );

		// Initialize form state based on the actual configuration of the container.
		$configuration = WC_Mix_and_Match_Order::get_current_container_configuration( $order_item, $order );

		if ( ! empty( $configuration ) ) {
			$_REQUEST = array_merge( $_REQUEST, WC_Mix_and_Match()->cart->rebuild_posted_container_form_data( $configuration, $product ) );
		}

		// Force tabular layout.
		$product->set_layout( 'tabular' );

		// Hide links.
		add_filter( 'woocommerce_product_is_visible', '__return_false' );

		// Manually include theme hooks/functions.
		WC_Mix_and_Match()->theme_includes();

		// Prepare child items.
		$child_items = $product->get_child_items();

		ob_start();
		include( 'meta-boxes/views/html-mnm-container-edit-form.php' );
		$html = ob_get_clean();

		wp_send_json_success( $html );
	}

	/**
	 * Validates edited/configured containers and returns updated order items.
	 */
	public static function ajax_edit_container_in_order() {

		$result = self::can_edit_container();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		// Populate $order, $product, and $order_item variables.
		extract( $result );

		$current_configuration = WC_Mix_and_Match_Order::get_current_container_configuration( $order_item, $order );

		/**
		 * 'wc_mnm_editing_container_in_order_configuration' filter.
		 *
		 * Use this filter to modify the posted configuration.
		 *
		 * @param  $config     array
		 * @param  $product    WC_Product_Mix_and_Match
		 * @param  $order_item WC_Order_Item
		 * @param  $order      WC_Order
		 */
		$posted_configuration = apply_filters( 'wc_mnm_editing_container_in_order_configuration', WC_Mix_and_Match()->cart->get_posted_container_configuration( $product ), $product, $order_item, $order );

		// Compare posted against current configuration.
		if ( $posted_configuration !== $current_configuration ) {

			$added_to_order = WC_Mix_and_Match()->order->add_container_to_order(
				$product,
				$order,
				$order_item->get_quantity(),
				array(
					'configuration' => $posted_configuration
				)
			);

			// Invalid configuration?
			if ( is_wp_error( $added_to_order ) ) {

				$message = __( 'The submitted configuration is invalid.', 'woocommerce-mix-and-match-products' );
				$data    = $added_to_order->get_error_data( 'wc_mnm_container_configuration_invalid' );

				$notice = '';
				if ( isset( $data[ 'notices' ] ) ) {
					$notices = current( $data[ 'notices' ] );
					$notice = isset( $notices[ 'notice' ] ) ? html_entity_decode( $notices[ 'notice' ] ) : '';
				}

				if ( $notice ) {
					// translators: %1$s is "The submitted configuration is invalid" %2$s is the error reason.
					$message = sprintf(
						_x( '%1$s %2$s', 'edit container in order: formatted validation message', 'woocommerce-mix-and-match-products' ),
						$message,
						$notice
					);
				}

				wp_send_json_error( $message );

				// Adjust stock and remove old items.
			} else {

				$new_container_item = $order->get_item( $added_to_order );

				/**
				 * 'wc_mnm_editing_container_in_order' action.
				 *
				 * @since  1.7.0
				 *
				 * @param  WC_Order_Item_Product  $new_order_item
				 * @param  WC_Order_Item_Product  $order_item
				 * @param  WC_Order               $order
				 */
				do_action( 'wc_mnm_editing_container_in_order', $new_container_item, $order_item, $order );

				$items_to_remove = array( $order_item ) + wc_mnm_get_child_order_items( $order_item, $order );
				$pre_stock_map   = array();

				foreach ( $items_to_remove as $remove_item ) {

					$changed_stock = wc_maybe_adjust_line_item_product_stock( $remove_item, 0 );
					if ( $changed_stock && isset( $changed_stock[ 'from' ] ) ) {
						$child_item_id                   = $remove_item->get_id();
						$pre_stock_map[ $child_item_id ] = $changed_stock[ 'from' ];
					}

					$order->remove_item( $remove_item->get_id() );
					$remove_item->delete();
				}

				$child_order_items = wc_mnm_get_child_order_items( $order->get_item( $added_to_order ), $order );

				foreach ( $child_order_items as $order_item_id => $order_item ) {
					$product = $order_item->get_product();
					$qty     = $order_item->get_quantity();

					if ( $product->managing_stock() ) {
						$child_item_id = $order_item->get_id();
						$old_stock       = isset( $pre_stock_map[ $child_item_id ] ) ? $pre_stock_map[ $child_item_id ] : $product->get_stock_quantity();
						$new_stock       = wc_update_product_stock( $product, $qty, 'decrease' );

						if ( $old_stock && $old_stock !== $new_stock ) {
							// translators: %s formatted product name.
							$order->add_order_note( sprintf( __( 'Adjusted %s stock', 'woocommerce-mix-and-match-products' ), $product->get_formatted_name() ) . ' (' . $old_stock . '&rarr;' . $new_stock . ')', false, true );
						}

						$order_item->add_meta_data( '_reduced_stock', $qty, true );
						$order_item->save();
					}
				}

				unset( $pre_stock_map );

				if ( isset( $_POST[ 'country' ], $_POST[ 'state' ], $_POST[ 'postcode' ], $_POST[ 'city' ] ) ) {

					$calculate_tax_args = array(
						'country'  => strtoupper( wc_clean( $_POST[ 'country' ] ) ),
						'state'    => strtoupper( wc_clean( $_POST[ 'state' ] ) ),
						'postcode' => strtoupper( wc_clean( $_POST[ 'postcode' ] ) ),
						'city'     => strtoupper( wc_clean( $_POST[ 'city' ] ) ),
					);

					$order->calculate_taxes( $calculate_tax_args );
					$order->calculate_totals( false );

				} else {
					$order->save();
				}
			}
		}

		ob_start();
		include( WC_ABSPATH . 'includes/admin/meta-boxes/views/html-order-items.php' );
		$items_html = ob_get_clean();

		// Update order notes.
		ob_start();
		$notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );
		include( WC_ABSPATH . 'includes/admin/meta-boxes/views/html-order-notes.php' );
		$notes_html = ob_get_clean();

		wp_send_json_success(
			array(
				'html'       => $items_html,
				'notes_html' => $notes_html,
			)
		);
	}

	/**
	 * Validates user can edit this product.
	 *
	 * @return mixed - If editable will return an array. Otherwise, will return WP_Error.
	 */
	protected static function can_edit_container() {

		try {

			if ( ! check_ajax_referer( 'wc_mnm_edit_container', 'security', false ) ) {
				$error = esc_html__( 'Security failure', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			if ( empty( $_POST[ 'order_id' ] ) || empty( $_POST[ 'item_id' ] ) ) {
				$error = esc_html__( 'Missing order ID or item ID', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			$order   = wc_get_order( wc_clean( $_POST[ 'order_id' ] ) );
			$item_id = absint( wc_clean( $_POST[ 'item_id' ] ) );

			if ( ! ( $order instanceof WC_Order ) ) {
				$error = esc_html__( 'Not a valid order', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			if ( ! current_user_can( 'edit_shop_orders' ) ) {
				if ( 'shop_subscription' === $order->get_type() ) {
					$error = esc_html__( 'You do not have authority to edit this order', 'woocommerce-mix-and-match-products' );
				} else {
					$error = esc_html__( 'You do not have authority to edit this subscription', 'woocommerce-mix-and-match-products' );
				}
				throw new Exception( $error );
			}

			$order_item = $order->get_item( $item_id );

			if ( ! ( $order_item instanceof WC_Order_Item ) ) {
				$error = esc_html__( 'Not a valid order item', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			$product = $order_item->get_product();

			if ( ! wc_mnm_is_product_container_type( $product ) ) {
				$error = esc_html__( 'Product is not mix and match container type and so cannot be edited', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			if ( ! $product->has_child_items() ) {
				$error = esc_html__( 'Container product does not have any available child items', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			return array (
				'product'    => $product,
				'order'      => $order,
				'order_item' => $order_item,
			);

		} catch ( Exception $e ) {

			// translators: %s is the validation error message.
			$error = sprintf( esc_html__( 'Cannot edit this mix and match container. Reason: %s.', 'woocommerce-mix-and-match-products' ), $e->getMessage() );

			return new WP_Error( 'mnm_edit_failure', $error );

		}
	}

}
