<?php
/**
 * WC_MNM_Admin_Ajax class
 *
 * @author   Kathy Darling
 * @category Admin
 * @package  WooCommerce Mix and Match/Admin/Ajax
 * @since    1.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin AJAX meta-box handlers.
 *
 * @class    WC_MNM_Admin_Ajax
 * @version  1.7.0
 */
class WC_MNM_Admin_Ajax {

	/**
	 * Hook in.
	 */
	public static function init() {

		/*
		 * Edit-Order screens.
		 */

		// Ajax handler used to fetch form content for populating "Configure/Edit" container order item modals.
		add_action( 'wp_ajax_woocommerce_configure_container_order_item', array( __CLASS__, 'ajax_container_order_item_form' ) );

		// Ajax handler for editing containers in manual/editable orders.
		add_action( 'wp_ajax_woocommerce_edit_container_in_order', array( __CLASS__, 'ajax_edit_container_in_order' ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Edit-Order.
	|--------------------------------------------------------------------------
	*/

	/**
	 * True when displaying content in an edit-composite order item modal.
	 *
	 * @since  3.14.0
	 *
	 * @return void
	 */
	public static function is_container_edit_request() {
		return doing_action( 'wp_ajax_woocommerce_edit_container_in_order' );
	}

	/**
	 * Form content used to populate "Configure/Edit" container order item modals.
	 *
	 * @since  5.8.0
	 *
	 * @return void
	 */
	public static function ajax_container_order_item_form() {

		global $product;

		$failure = array(
			'result' => 'failure'
		);

		if ( ! check_ajax_referer( 'wc_edit_container', 'security', false ) ) {
			wp_send_json( $failure );
		}

		if ( empty( $_POST[ 'order_id' ] ) || empty( $_POST[ 'item_id' ] ) ) {
			wp_send_json( $failure );
		}

		$order   = wc_get_order( wc_clean( $_POST[ 'order_id' ] ) );
		$item_id = absint( wc_clean( $_POST[ 'item_id' ] ) );

		if ( ! ( $order instanceof WC_Order ) ) {
			wp_send_json( $failure );
		}

		$item = $order->get_item( $item_id );

		if ( ! ( $item instanceof WC_Order_Item ) ) {
			wp_send_json( $failure );
		}

		$product       = $item->get_product();
		$child_items = $product ? $product->get_children() : false;

		if ( empty( $child_items ) ) {
			wp_send_json( $failure );
		}

		// Initialize form state based on the actual configuration of the container.
		$configuration = WC_Mix_and_Match_Order::get_current_container_configuration( $item, $order );

		if ( ! empty( $configuration ) ) {
			$_REQUEST = array_merge( $_REQUEST, WC_Mix_and_Match()->cart->rebuild_posted_container_form_data( $configuration, $product ) );
		}

		// Force tabular layout.
		$product->set_layout( 'tabular' );

		// Hide links.
		add_filter( 'woocommerce_product_is_visible', '__return_false' );

		ob_start();
		include( 'meta-boxes/views/html-mnm-container-edit-form.php' );
		$html = ob_get_clean();

		$response = array(
			'result' => 'success',
			'html'   => $html
		);

		wp_send_json( $response );
	}

	/**
	 * Validates edited/configured containers and returns updated order items.
	 *
	 * @since  5.8.0
	 *
	 * @return void
	 */
	public static function ajax_edit_container_in_order() {

		$failure = array(
			'result' => 'failure'
		);

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_send_json( $failure );
		}

		if ( ! check_ajax_referer( 'wc_edit_container', 'security', false ) ) {
			wp_send_json( $failure );
		}

		if ( empty( $_POST[ 'order_id' ] ) || empty( $_POST[ 'item_id' ] ) ) {
			wp_send_json( $failure );
		}

		$order   = wc_get_order( wc_clean( $_POST[ 'order_id' ] ) );
		$item_id = absint( wc_clean( $_POST[ 'item_id' ] ) );

		if ( ! ( $order instanceof WC_Order ) ) {
			wp_send_json( $failure );
		}

		$item = $order->get_item( $item_id );

		if ( ! ( $item instanceof WC_Order_Item ) ) {
			wp_send_json( $failure );
		}

		$product = $item->get_product();

		if ( ! ( $product instanceof WC_Product_Mix_and_Match ) ) {
			wp_send_json( $failure );
		}

		if ( ! empty( $_POST[ 'fields' ] ) ) {
			parse_str( $_POST[ 'fields' ], $posted_form_fields );
			$_POST = array_merge( $_POST, $posted_form_fields );
		}

		$posted_configuration  = WC_Mix_and_Match()->cart->get_posted_container_configuration( $product );
		$current_configuration = WC_Mix_and_Match_Order::get_current_container_configuration( $item, $order );

		// Compare posted against current configuration.
		if ( $posted_configuration !== $current_configuration ) {

			$added_to_order = WC_Mix_and_Match()->order->add_container_to_order( $product, $order, $item->get_quantity(), array(
				'configuration' => $posted_configuration
			) );

			// Invalid configuration?
			if ( is_wp_error( $added_to_order ) ) {

				$message = __( 'The submitted configuration is invalid.', 'woocommerce-mix-and-match-products' );
				$data    = $added_to_order->get_error_data( 'wc_mnm_container_configuration_invalid' );

				$notice = '';
				if ( isset( $data[ 'notices' ] ) ) {
					$notices = current( $data[ 'notices' ] );
					$notice = isset( $notices['notice'] ) ? html_entity_decode( $notices['notice'] ) : '';
				}

				if ( $notice ) {
					// translators: %1$s is "The submitted configuration is invalid" %2$s is the error reason.
					$message = sprintf( _x( '%1$s %2$s', 'edit container in order: formatted validation message', 'woocommerce-mix-and-match-products' ),
						$message,
						$notice
					);
				}

				$response = array(
					'result' => 'failure',
					'error'  => $message
				);

				wp_send_json( $response );

				// Remove old items.
			} else {

				if ( has_action( 'wc_mnm_editing_container_in_order' ) ) {

					$new_container_item = $order->get_item( $added_to_order );

					/**
					 * 'wc_mnm_editing_container_in_order' action.
					 *
					 * @since  1.7.0
					 *
					 * @param  WC_Order_Item_Product  $new_item
					 * @param  WC_Order_Item_Product  $old_item
					 */
					do_action( 'wc_mnm_editing_container_in_order', $new_container_item, $item, $order );
				}

				$items_to_remove = array( $item ) + wc_mnm_get_child_order_items( $item, $order );
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
		include ( WC_ABSPATH . 'includes/admin/meta-boxes/views/html-order-items.php' );
		$html = ob_get_clean();

		// Update order notes.
		ob_start();
		$notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );
		include ( WC_ABSPATH . 'includes/admin/meta-boxes/views/html-order-notes.php' );
		$notes_html = ob_get_clean();
		$response = array(
			'result'     => 'success',
			'html'       => $html,
			'notes_html' => $notes_html
		);

		wp_send_json( $response );
	}
}

WC_MNM_Admin_Ajax::init();
