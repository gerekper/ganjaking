<?php
/**
 * WC_MNM_Ajax class
 *
 * @package  WooCommerce Mix and Match/Ajax
 * @since    2.2.0
 * @version  2.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX handlers - used in order metabox and subscriptions frontend.
 *
 * @class    WC_MNM_Ajax
 */
class WC_MNM_Ajax {

	/**
	 * Hook in.
	 */
	public static function init() {

		/**
		 * Edit-Order callbacks.
		 */

		// Ajax handler used to fetch form content for editing container order items.
		add_action( 'wc_ajax_mnm_get_edit_container_order_item_form', array( __CLASS__, 'edit_container_order_item_form' ) );

		// Ajax handler for editing containers in order.
		add_action( 'wc_ajax_mnm_update_container_order_item', [ __CLASS__ , 'update_container_order_item' ] );

		// Force some styles when editing.
		add_action( 'wc_mnm_edit_container_order_item_in_shop_order', array( __CLASS__, 'force_container_styles' ), 0 );
		add_action( 'wc_mnm_edit_container_order_item_in_shop_subscription', array( __CLASS__, 'force_container_styles' ), 0 );

	}

	/*
	|--------------------------------------------------------------------------
	| Edit-Order.
	|--------------------------------------------------------------------------
	*/


	/**
	 * Form content used to populate "Configure/Edit" container order item modals.
	 */
	public static function edit_container_order_item_form() {

		$result = self::can_edit_container();
		
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		// Populate $order, $product, and $order_item variables.
		$order      = $result[ 'order' ];
		$product    = $result[ 'product' ];
		$order_item = $result[ 'order_item' ];
		$context    = $result[ 'context' ];

		// Initialize form state based on the actual configuration of the container.
		$configuration = WC_Mix_and_Match_Order::get_current_container_configuration( $order_item, $order );

		if ( ! empty( $configuration ) ) {
			$_REQUEST = array_merge( $_REQUEST, WC_Mix_and_Match()->cart->rebuild_posted_container_form_data( $configuration, $product ) );
		}

		ob_start();
		echo '<div class="wc-mnm-edit-container wc-mnm-edit-container-' . $order->get_type() . '">'; // Restore wrapping class as fragments replaces it.

		/*
		 * `wc_mnm_edit_container_order_item_in_shop_order` hook
		 * 'wc_mnm_edit_container_order_item_in_shop_subscription' hook.
		 * 
		 * @param  $product  WC_Product_Mix_and_Match
		 * @param  $order_item WC_Order_Item
		 * @param  $order      WC_Order
		 * @param  string $context The originating source loading this template
		 */

		do_action( 'wc_mnm_edit_container_order_item_in_' . $order->get_type(), $product, $order_item, $order, $context );
		echo '</div>';

		$form = ob_get_clean();
		
		/*
		 * `wc_mnm_edit_container_in_shop_order_fragments` filter
		 * 'wc_mnm_edit_container_in_shop_subscription_fragments' filter.
		 * 
		 * @param  array $fragments
		 * @param  $order_item WC_Order_Item
		 * @param  $order      WC_Order
		 * @param  string $context The originating source loading this template
		 */
		$response = apply_filters( 'wc_mnm_edit_container_in_' . $order->get_type() . '_fragments', array( 'div.wc-mnm-edit-container' => $form ), $order_item, $order, $context );

		wp_send_json_success( $response );
	}

	/**
	 * Updates the MNM container config.
	 */
	public static function update_container_order_item() {

		$result = self::can_edit_container();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		}

		// Populate $order, $product, and $order_item variables.
		$order      = $result[ 'order' ];
		$product    = $result[ 'product' ];
		$order_item = $result[ 'order_item' ];
		$context    = $result[ 'context' ];

		// The current configuration of the container order item.
		$current_configuration = WC_Mix_and_Match_Order::get_current_container_configuration( $order_item, $order );

		/**
		 * 'wc_mnm_edit_container_configuration' filter.
		 *
		 * Use this filter to modify the posted configuration.
		 *
		 * @param  $config     array
		 * @param  $product    WC_Product_Mix_and_Match
		 * @param  $order_item WC_Order_Item
		 * @param  $order      WC_Order
		 * @param  string $context The originating source loading this template
		 */
		$new_config = apply_filters( 'wc_mnm_edit_container_configuration', WC_Mix_and_Match()->cart->get_posted_container_configuration( $product, $_POST[ 'config' ] ), $product, $order_item, $order, $context );

		/**
		 * 'wc_mnm_edit_container_order_item_args' filter.
		 *
		 * Use this filter to modify the posted configuration.
		 *
		 * @param  $args       array
		 * @param  $product    WC_Product_Mix_and_Match
		 * @param  $order_item WC_Order_Item
		 * @param  $order      WC_Order
		 * @param  string $context The originating source loading this template
		 */
		$order_item_args = apply_filters( 'wc_mnm_edit_container_order_item_args', array( 'configuration' => $new_config ), $product, $order_item, $order, $context );

		// Config is new.
		if ( $new_config !== $current_configuration ) {

			// Add new container to the order
			$added_to_order = WC_Mix_and_Match()->order->add_container_to_order( $product, $order, $order_item->get_quantity(), $order_item_args );

			// If new container is added successfully, delete old container and its child items.
			if ( is_wp_error( $added_to_order ) ) {

				/*
				 * `wc_mnm_upate_container_failure_message` filter for upating failure message
				 * 
				 * @since 2.2.0
				 * 
				 * @param string $message
				 * @param obj Exception
				 */
				// translators: %s is the validation failure reason.
				$error = apply_filters( 'wc_mnm_upate_container_failure_message', esc_html__( 'Cannot update this mix and match product. Reason: %s.', 'woocommerce-mix-and-match-products' ), $e );

				wp_send_json_error( sprintf( $error, $added_to_order->get_error_message() ) );

			// Adjust stock and remove old items.
			}	else {

				$new_container_item = $order->get_item( $added_to_order );

				/**
				 * 'wc_mnm_editing_container_in_order' action.
				 *
				 * @since  2.1.0
				 *
				 * @param  WC_Order_Item_Product  $new_order_item
				 * @param  WC_Order_Item_Product  $order_item - the old order item.
				 * @param  WC_Order               $order
				 * @param  string $context The originating source loading this template
				 */
				do_action( 'wc_mnm_editing_container_in_order', $new_container_item, $order_item, $order, $context );

				$child_items_to_remove = wc_mnm_get_child_order_items( $order_item, $order );
				$items_to_remove = array( $order_item ) + $child_items_to_remove;

				// Keep track of child item stock changes, keyed by product|variation ID.
				$changes_map = array();
				$product_ids = array();

				foreach ( $child_items_to_remove as $child_item_to_remove ) {

					$product_id = $child_item_to_remove->get_variation_id() ? $child_item_to_remove->get_variation_id() : $child_item_to_remove->get_product_id();

					$product_ids[] = $product_id;

					// Store change to add in order note.
					$changes_map[ $product_id ] = array(
						'id' => $product_id,
						'actions' => array(
							'remove' => array(
								'title' => $child_item_to_remove->get_name(),
								'sku'   => '#' . $product_id,
							)
						)
					);

				}

				$new_child_items = wc_mnm_get_child_order_items( $order->get_item( $added_to_order ), $order );

				if ( ! empty( $new_child_items ) ) {
			
					foreach ( $new_child_items as $new_child_item ) {

						$product_id = $new_child_item->get_variation_id() ? $new_child_item->get_variation_id() : $new_child_item->get_product_id();

						if ( isset( $changes_map[ $product_id ] ) ) {

							$action = 'adjust';

							$changes_map[ $product_id ][ 'actions' ] = array(
								'adjust' => array(
									'title' => $new_child_item->get_name(),
									'sku'   => '#' . $product_id
								)
							);

						// If we're seeing this child item for the first, time, log an 'add' action.
						} else {

							$changes_map[ $product_id ][ 'actions' ][ 'add' ] = array(
								'title' => $new_child_item->get_name(),
								'sku'   => '#' . $product_id
							);
						}

					}

				}

				$change_strings = array(
					'add'    => array(),
					'remove' => array(),
					'adjust' => array()
				);

				foreach ( $changes_map as $item_id => $item_changes ) {

					$actions = array( 'add', 'remove', 'adjust' );

					foreach ( $actions as $action ) {

						if ( isset( $item_changes[ 'actions' ][ $action ] ) ) {							
							/* translators: %1$s: Product title, %2$s: SKU */
							$change_strings[ $action ][] = sprintf( _x( '%1$s (%2$s)', 'child items change note format', 'woocommerce-mix-and-match-products' ), $item_changes[ 'actions' ][ $action ][ 'title' ], $item_changes[ 'actions' ][ $action ][ 'sku' ] );
						}
					}
				}

				if ( ! empty( $change_strings[ 'remove' ] ) ) {
					/* translators: List of items */
					$order->add_order_note( sprintf( __( 'Deleted child line items: %s', 'woocommerce-mix-and-match-products' ), implode( ', ', $change_strings[ 'remove' ] ) ), false, true );
				}

				if ( ! empty( $change_strings[ 'add' ] ) ) {
					/* translators: List of items */
					$order->add_order_note( sprintf( __( 'Added child line items: %s', 'woocommerce-mix-and-match-products' ), implode( ', ', $change_strings[ 'add' ] ) ), false, true );
				}

				if ( ! empty( $change_strings[ 'adjust' ] ) ) {
					/* translators: List of items */
					$order->add_order_note( sprintf( __( 'Adjusted child line items: %s', 'woocommerce-mix-and-match-products' ), implode( ', ', $change_strings[ 'adjust' ] ) ), false, true );
				}

				/**
				 * Remove old items.
				 */
				foreach ( $items_to_remove as $remove_item ) {
					$order->remove_item( $remove_item->get_id() );
					$remove_item->delete();
				}

				unset( $changes_map );
				
				/**
				 * 'wc_mnm_updated_container_in_order' action.
				 *
				 * @since  2.2.0
				 *
				 * @param  WC_Order_Item_Product  $new_order_item
				 * @param  WC_Order               $order
				 * @param  string $context The originating source loading this template
				 */
				do_action( 'wc_mnm_updated_container_in_order', $new_container_item, $order, $context );

				/**
				 * Recalculate totals.
				 */
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

				// Build fragments response.
				$fragments = array();

				if ( 'metabox' === $context ) {
					ob_start();
					include( WC_ABSPATH . 'includes/admin/meta-boxes/views/html-order-items.php' );
					$fragments[ 'html' ] = ob_get_clean();
			
					// Update order notes.
					ob_start();
					$notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );
					include( WC_ABSPATH . 'includes/admin/meta-boxes/views/html-order-notes.php' );
					$fragments[ 'notes_html' ] = ob_get_clean();
				}

				/**
				 * 'wc_mnm_updated_container_in_shop_order_fragments' filter.
				 * 'wc_mnm_updated_container_in_shop_subscription_fragments' filter.
				 *
				 * @param  array $fragments     
				 * @param  $new_container_item WC_Order_Item
				 * @param  $order      WC_Order
				 * @param  string $context The originating source loading this template
				 */
				$fragments = apply_filters( 'wc_mnm_updated_container_in_'. $order->get_type() . '_fragments', $fragments, $new_container_item, $order, $context );

				wp_send_json_success( $fragments );

			}
		} else {

			wp_send_json_success( 'nochange' );
		}

	}

	/**
	 * Validates user can edit this product.
	 *
	 * @return mixed - If editable will return an array. Otherwise, will return WP_Error.
	 */
	protected static function can_edit_container() {

		try {

			// Did a specific script call this?
			$context = isset( $_POST[ 'context' ] ) ? sanitize_title( wc_clean( $_POST[ 'context' ] ) ) : 'metabox';

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

			if ( $order instanceof WC_Subscription ) {

				if ( ! current_user_can( 'edit_shop_orders' ) && ! current_user_can( 'switch_shop_subscription', $order->get_id() ) ) {
					$error = esc_html__( 'You do not have authority to edit this subscription', 'woocommerce-mix-and-match-products' );
					throw new Exception( $error );
				}

			} else if ( ! current_user_can( 'edit_shop_orders' ) ) {
				$error = esc_html__( 'You do not have authority to edit this order', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			// Check for a configuration IF updating.
			if ( doing_action( 'wc_ajax_mnm_update_container_order_item' ) && empty( $_POST[ 'config' ] ) ) {
				$error = esc_html__( 'No configuration found', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			$order_item = $order->get_item( $item_id );

			if ( ! ( $order_item instanceof WC_Order_Item ) ) {
				$error = esc_html__( 'Not a valid order item', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			/**
			 * `wc_mnm_get_product_from_edit_order_item` filter for editing container
			 * 
			 * @param obj WC_Product $product
			 * @param obj WC_Order_Item
			 * @param obj WC_Order
			 * @param  string $context The originating source loading this template
			 */
			$product = apply_filters( 'wc_mnm_get_product_from_edit_order_item', $order_item->get_product(), $order_item, $order, $context );

			if ( ! $product ) {
				$error = esc_html__( 'This product does not exist and so can not be edited', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			if ( ! wc_mnm_is_product_container_type( $product ) ) {
				$error = esc_html__( 'Product is not mix and match container type and so cannot be edited', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			if ( ! $product->has_child_items() ) {
				$error = esc_html__( 'Container product does not have any available child items', 'woocommerce-mix-and-match-products' );
				throw new Exception( $error );
			}

			/**
			 * `wc_mnm_can_edit_container_validation` action for validating container can be edited
			 * 
			 * @param obj WC_Product $product
			 * @param obj WC_Order_Item
			 * @param obj WC_Order
			 * @param  string $context The originating source loading this template
			 * 
			 * @throws Exception in order to fail validation.
			 */
			do_action( 'wc_mnm_can_edit_container_validation', $product, $order_item, $order, $context );

			return array (
				'product'    => $product,
				'order'      => $order,
				'order_item' => $order_item,
				'context'    => $context,
			);

		} catch ( Exception $e ) {

			/**
			 * `wc_mnm_edit_container_failure_message` filter for editing failure message
			 * 
			 * @since 2.2.0
			 * 
			 * @param string $message
			 * @param obj Exception
			 * @param  string $context The originating source loading this template
			 */
			// translators: %s is the validation failure reason.
			$error = apply_filters( 'wc_mnm_edit_container_failure_message', esc_html__( 'Cannot edit this mix and match product. Reason: %s.', 'woocommerce-mix-and-match-products' ), $e, $context );

			return new WP_Error( 'mnm_edit_container_failure', sprintf( $error, $e->getMessage() ) );
		}
	}

	/**
	 * Force tabular layout and hide child links.
	 */
	public static function force_container_styles() {

		// Force default location.
		add_filter( 'woocommerce_product_get_add_to_cart_form_location', function() { return 'default'; } );

		// Force tabular layout.
		add_filter( 'woocommerce_product_get_layout', function() { return 'tabular'; } );

		// Hide links.
		add_filter( 'woocommerce_product_is_visible', '__return_false' );
		
	}

	/**
	 * Load the scripts required for order editing.
	 * 
	 * @param int $item_id The subscription line item ID.
	 * @param WC_Order_Item|array $item The subscription line item.
	 * @param WC_Subscription $subscription The subscription.
	 */
	public static function load_edit_scripts() {
		wp_enqueue_script( 'jquery-blockui' );
		wc_mix_and_match()->display->frontend_scripts();

		do_action( 'wc_mnm_container_editing_enqueue_scripts' );
	}


}
WC_MNM_Ajax::init();
