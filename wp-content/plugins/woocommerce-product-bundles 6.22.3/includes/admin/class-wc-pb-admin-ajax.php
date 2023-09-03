<?php
/**
 * WC_PB_Admin_Ajax class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin AJAX meta-box handlers.
 *
 * @class    WC_PB_Admin_Ajax
 * @version  6.16.1
 */
class WC_PB_Admin_Ajax {

	/**
	 * Used by 'ajax_search_bundled_variations'.
	 *
	 * @var int
	 */
	private static $searching_variations_of;

	/**
	 * Hook in.
	 */
	public static function init() {

		/*
		 * Notices.
		 */

		// Dismiss notices.
		add_action( 'wp_ajax_woocommerce_dismiss_bundle_notice', array( __CLASS__ , 'dismiss_notice' ) );

		// Ajax handler for performing loopback tests.
		add_action( 'wp_ajax_woocommerce_bundles_health-check-loopback_test', array( __CLASS__, 'ajax_loopback_test' ) );

		/*
		 * Edit-Product screens.
		 */

		// Ajax add bundled product.
		add_action( 'wp_ajax_woocommerce_add_bundled_product', array( __CLASS__, 'ajax_add_bundled_product' ) );

		// Ajax search bundled item variations.
		add_action( 'wp_ajax_woocommerce_search_bundled_variations', array( __CLASS__, 'ajax_search_bundled_variations' ) );

		/*
		 * Edit-Order screens.
		 */

		// Ajax handler used to fetch form content for populating "Configure/Edit" bundle order item modals.
		add_action( 'wp_ajax_woocommerce_configure_bundle_order_item', array( __CLASS__, 'ajax_bundle_order_item_form' ) );

		// Ajax handler for editing bundles in manual/editable orders.
		add_action( 'wp_ajax_woocommerce_edit_bundle_in_order', array( __CLASS__, 'ajax_edit_bundle_in_order' ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Notices.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Dismisses notices.
	 *
	 * @since  5.8.0
	 *
	 * @return void
	 */
	public static function dismiss_notice() {

		$failure = array(
			'result' => 'failure'
		);

		if ( ! check_ajax_referer( 'wc_pb_dismiss_notice_nonce', 'security', false ) ) {
			wp_send_json( $failure );
		}

		if ( empty( $_POST[ 'notice' ] ) ) {
			wp_send_json( $failure );
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json( $failure );
		}

		$dismissed = WC_PB_Admin_Notices::dismiss_notice( wc_clean( $_POST[ 'notice' ] ) );

		if ( ! $dismissed ) {
			wp_send_json( $failure );
		}

		$response = array(
			'result' => 'success'
		);

		wp_send_json( $response );
	}

	/**
	 * Checks if loopback requests work.
	 *
	 * @since  6.3.0
	 *
	 * @return void
	 */
	public static function ajax_loopback_test() {

		$failure = array(
			'result' => 'failure',
			'reason' => ''
		);

		if ( ! check_ajax_referer( 'wc_pb_loopback_notice_nonce', 'security', false ) ) {
			$failure[ 'reason' ] = 'nonce';
			wp_send_json( $failure );
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			$failure[ 'reason' ] = 'user_role';
			wp_send_json( $failure );
		}

		if ( ! class_exists( 'WP_Site_Health' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/class-wp-site-health.php' );
		}

		$site_health = method_exists( 'WP_Site_Health', 'get_instance' ) ? WP_Site_Health::get_instance() : new WP_Site_Health();
		$result      = $site_health->can_perform_loopback();
		$passes_test = 'good' === $result->status;

		WC_PB_Admin_Notices::set_notice_option( 'loopback', 'last_tested', gmdate( 'U' ) );
		WC_PB_Admin_Notices::set_notice_option( 'loopback', 'last_result', $passes_test ? 'pass' : 'fail' );

		if ( ! $passes_test ) {
			$failure[ 'reason' ]  = 'status';
			$failure[ 'status' ]  = $result->status;
			$failure[ 'message' ] = $result->message;
			wp_send_json( $failure );
		}

		WC_PB_Admin_Notices::remove_maintenance_notice( 'loopback' );

		$response = array(
			'result' => 'success'
		);

		wp_send_json( $response );
	}

	/*
	|--------------------------------------------------------------------------
	| Edit-Product.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Ajax search for bundled variations.
	 */
	public static function ajax_search_bundled_variations() {

		if ( ! empty( $_GET[ 'include' ] ) ) {
			if ( $product = wc_get_product( absint( $_GET[ 'include' ] ) ) ) {
				self::$searching_variations_of = $product->get_id();
				$_GET[ 'include' ] = $product->get_children();
			} else {
				self::$searching_variations_of = 0;
				$_GET[ 'include' ] = array();
			}
		}

		add_filter( 'woocommerce_json_search_found_products', array( __CLASS__, 'tweak_variation_titles' ) );
		WC_AJAX::json_search_products( '', true );
		remove_filter( 'woocommerce_json_search_found_products', array( __CLASS__, 'tweak_variation_titles' ) );
	}

	/**
	 * Tweak variation titles for consistency across different WC versions.
	 *
	 * @param  array  $search_results
	 * @return array
	 */
	public static function tweak_variation_titles( $search_results ) {

		if ( ! empty( $search_results ) ) {

			// Bug in WC -- parent IDs are always included when the 'include' parameter is specified.
			if ( self::$searching_variations_of ) {
				$search_results = array_diff_key( $search_results, array( self::$searching_variations_of => 1 ) );
			}

			$search_result_objects = array_map( 'wc_get_product', array_keys( $search_results ) );

			foreach ( $search_result_objects as $variation ) {
				if ( $variation && $variation->is_type( 'variation' ) ) {
					$variation_id                    = $variation->get_id();
					$search_results[ $variation_id ] = rawurldecode( WC_PB_Helpers::get_product_variation_title( $variation, 'flat' ) );
				}
			}
		}

		return $search_results;
	}

	/**
	 * Handles adding bundled products via ajax.
	 */
	public static function ajax_add_bundled_product() {

		check_ajax_referer( 'wc_bundles_add_bundled_product', 'security' );

		$loop         = isset( $_POST[ 'id' ] ) ? intval( $_POST[ 'id' ] ) : 0;
		$post_id      = isset( $_POST[ 'post_id' ] ) ? intval( $_POST[ 'post_id' ] ) : 0;
		$product_id   = isset( $_POST[ 'product_id' ] ) ? intval( $_POST[ 'product_id' ] ) : 0;
		$item_id      = false;
		$toggle       = 'open';
		$tabs         = WC_PB_Meta_Box_Product_Data::get_bundled_product_tabs();
		$product      = wc_get_product( $product_id );
		$title        = $product->get_title();
		$sku          = $product->get_sku();
		$stock_status = 'in_stock';

		/**
		 * 'woocommerce_add_bundled_product_item_data' filter.
		 *
		 * Use this filter to modify the bundled item data when the product is first added.
		 *
		 * @param  $item_data   array
		 * @param  $context     string
		 * @param  $product_id  int
		 */
		$item_data = apply_filters( 'woocommerce_add_bundled_product_item_data', array(), 'create', $product_id );
		$response  = array(
			'markup'  => '',
			'message' => ''
		);

		if ( $product ) {

			if ( in_array( $product->get_type(), array( 'simple', 'variable', 'subscription', 'variable-subscription' ) ) ) {

				if ( ! $product->is_in_stock() ) {
					$stock_status       = 'out_of_stock';
				} elseif ( $product->is_on_backorder( 1 ) ) {
					$stock_status       = 'on_backorder';
				}

				ob_start();
				include( WC_PB_ABSPATH . 'includes/admin/meta-boxes/views/html-bundled-product.php' );
				$response[ 'markup' ] = ob_get_clean();

			} else {
				$response[ 'message' ] = __( 'The selected product cannot be bundled. Please select a simple product, a variable product, or a simple/variable subscription.', 'woocommerce-product-bundles' );
			}

		} else {
			$response[ 'message' ] = __( 'The selected product is invalid.', 'woocommerce-product-bundles' );
		}

		wp_send_json( $response );
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
	public static function is_bundle_edit_request() {
		return doing_action( 'wp_ajax_woocommerce_edit_bundle_in_order' );
	}

	/**
	 * Form content used to populate "Configure/Edit" bundle order item modals.
	 *
	 * @since  5.8.0
	 *
	 * @return void
	 */
	public static function ajax_bundle_order_item_form() {

		global $product;

		$failure = array(
			'result' => 'failure'
		);

		if ( ! check_ajax_referer( 'wc_bundles_edit_bundle', 'security', false ) ) {
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
		$bundled_items = $product ? $product->get_bundled_items() : false;

		if ( empty( $bundled_items ) ) {
			wp_send_json( $failure );
		}

		// Initialize form state based on the actual configuration of the bundle.
		$configuration = WC_PB_Order::get_current_bundle_configuration( $item, $order );

		if ( ! empty( $configuration ) ) {
			$_REQUEST = array_merge( $_REQUEST, WC_PB()->cart->rebuild_posted_bundle_form_data( $configuration ) );
		}

		// Force tabular layout.
		$product->set_layout( 'tabular' );

		// Hide prices.
		add_filter( 'woocommerce_bundled_item_is_priced_individually', '__return_false' );
		// Hide descriptions.
		add_filter( 'woocommerce_bundled_item_description', '__return_false' );

		ob_start();
		include( WC_PB_ABSPATH . 'includes/admin/meta-boxes/views/html-bundle-edit-form.php' );
		$html = ob_get_clean();

		$response = array(
			'result' => 'success',
			'html'   => $html
		);

		wp_send_json( $response );
	}

	/**
	 * Validates edited/configured bundles and returns updated order items.
	 *
	 * @since  5.8.0
	 *
	 * @return void
	 */
	public static function ajax_edit_bundle_in_order() {

		$failure = array(
			'result' => 'failure'
		);

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_send_json( $failure );
		}

		if ( ! check_ajax_referer( 'wc_bundles_edit_bundle', 'security', false ) ) {
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

		if ( ! ( $product instanceof WC_Product_Bundle ) ) {
			wp_send_json( $failure );
		}

		if ( ! empty( $_POST[ 'fields' ] ) ) {
			parse_str( $_POST[ 'fields' ], $posted_form_fields ); // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$_POST = array_merge( $_POST, $posted_form_fields );
		}

		$posted_configuration  = WC_PB()->cart->get_posted_bundle_configuration( $product );
		$current_configuration = WC_PB_Order::get_current_bundle_configuration( $item, $order );

		// Compare posted against current configuration.
		if ( $posted_configuration !== $current_configuration ) {

			$added_to_order = WC_PB()->order->add_bundle_to_order( $product, $order, $item->get_quantity(), array(

				/**
				 * 'woocommerce_editing_bundle_in_order_configuration' filter.
				 *
				 * Use this filter to modify the posted configuration.
				 *
				 * @param  $config   array
				 * @param  $product  WC_Product_Bundle
				 * @param  $item     WC_Order_Item
				 * @param  $order    WC_Order
				 */
				'configuration' => apply_filters( 'woocommerce_editing_bundle_in_order_configuration', $posted_configuration, $product, $item, $order )
			) );

			// Invalid configuration?
			if ( is_wp_error( $added_to_order ) ) {

				$message = __( 'The submitted configuration is invalid.', 'woocommerce-product-bundles' );
				$notice  = $added_to_order->get_error_data();

				if ( $notice ) {
					/* translators: %1$s: error, %2$s: reason */
					$message = sprintf( _x( '%1$s %2$s', 'edit bundle in order: formatted validation message', 'woocommerce-product-bundles' ), $message, html_entity_decode( $notice ) );
				}

				$response = array(
					'result' => 'failure',
					'error'  => $message
				);

				wp_send_json( $response );

			// Adjust stock and remove old items.
			} else {

				$new_container_item = $order->get_item( $added_to_order );

				/**
				 * 'woocommerce_editing_bundle_in_order' action.
				 *
				 * @since  5.9.2
				 *
				 * @param  WC_Order_Item_Product  $new_item
				 * @param  WC_Order_Item_Product  $old_item
				 */
				do_action( 'woocommerce_editing_bundle_in_order', $new_container_item, $item, $order );

				$bundled_items_to_remove = wc_pb_get_bundled_order_items( $item, $order );
				$items_to_remove         = array( $item ) + $bundled_items_to_remove;

				$changes_map = array();
				$product_ids = array();

				foreach ( $bundled_items_to_remove as $bundled_item_to_remove ) {

					$bundled_item_id = $bundled_item_to_remove->get_meta( '_bundled_item_id', true );
					$product_id      = $bundled_item_to_remove->get_product_id();

					if ( $variation_id = $bundled_item_to_remove->get_variation_id() ) {
						$product_id = $variation_id;
					}

					$product_ids[ $bundled_item_id ] = $product_id;

					// Store change to add in order note.
					$changes_map[ $bundled_item_id ] = array(
						'id'      => $product_id,
						'actions' => array(
							'remove' => array(
								'title' => $bundled_item_to_remove->get_name(),
								'sku'   => '#' . $product_id
							)
						)
					);
				}

				$bundled_order_items = wc_pb_get_bundled_order_items( $new_container_item, $order );

				foreach ( $bundled_order_items as $order_item_id => $order_item ) {

					$bundled_item_id = $order_item->get_meta( '_bundled_item_id', true );
					$product         = $order_item->get_product();
					$product_id      = $product->get_id();
					$action          = 'add';

					$product_ids[ $bundled_item_id ] = $product_id;

					// Store change to add in order note.
					if ( isset( $changes_map[ $bundled_item_id ] ) ) {

						// If the selection didn't change, log it as an adjustment.
						if ( $product_id === $changes_map[ $bundled_item_id ][ 'id' ] ) {

							$action = 'adjust';

							$changes_map[ $bundled_item_id ][ 'actions' ] = array(
								'adjust' => array(
									'title' => $order_item->get_name(),
									'sku'   => '#' . $product_id
								)
							);

						// Otherwise, log another 'add' action.
						} else {

							$changes_map[ $bundled_item_id ][ 'actions' ][ 'add' ] = array(
								'title' => $order_item->get_name(),
								'sku'   => '#' . $product_id
							);
						}

					// If we're seeing this bundled item for the first, time, log an 'add' action.
					} else {

						$changes_map[ $bundled_item_id ] = array(
							'id'      => $product_id,
							'actions' => array(
								'add' => array(
									'title' => $order_item->get_name(),
									'sku'   => '#' . $product_id
								)
							)
						);
					}
				}

				$duplicate_product_ids              = array_diff_assoc( $product_ids, array_unique( $product_ids ) );
				$duplicate_product_bundled_item_ids = array_keys( array_intersect( $product_ids, $duplicate_product_ids ) );

				$change_strings = array(
					'add'    => array(),
					'remove' => array(),
					'adjust' => array()
				);

				foreach ( $changes_map as $item_id => $item_changes ) {

					$actions = array( 'add', 'remove', 'adjust' );

					foreach ( $actions as $action ) {

						if ( isset( $item_changes[ 'actions' ][ $action ] ) ) {

							if ( in_array( $item_id, $duplicate_product_bundled_item_ids ) ) {
								/* translators: %1$s: SKU, %2$s: Bundled item ID */
								$stock_id = sprintf( _x( '%1$s:%2$s', 'bundled items stock change note sku with id format', 'woocommerce-product-bundles' ), $item_changes[ 'actions' ][ $action ][ 'sku' ], $item_id );
							} else {
								$stock_id = $item_changes[ 'actions' ][ $action ][ 'sku' ];
							}

							/* translators: %1$s: Product title, %2$s: SKU */
							$change_strings[ $action ][] = sprintf( _x( '%1$s (%2$s)', 'bundled items change note format', 'woocommerce-product-bundles' ), $item_changes[ 'actions' ][ $action ][ 'title' ], $stock_id );
						}
					}
				}

				if ( ! empty( $change_strings[ 'remove' ] ) ) {
						/* translators: List of items */
					$order->add_order_note( sprintf( __( 'Deleted bundled line items: %s', 'woocommerce-product-bundles' ), implode( ', ', $change_strings[ 'remove' ] ) ), false, true );
				}

				if ( ! empty( $change_strings[ 'add' ] ) ) {
					/* translators: List of items */
					$order->add_order_note( sprintf( __( 'Added bundled line items: %s', 'woocommerce-product-bundles' ), implode( ', ', $change_strings[ 'add' ] ) ), false, true );
				}

				if ( ! empty( $change_strings[ 'adjust' ] ) ) {
					/* translators: List of items */
					$order->add_order_note( sprintf( __( 'Adjusted bundled line items: %s', 'woocommerce-product-bundles' ), implode( ', ', $change_strings[ 'adjust' ] ) ), false, true );
				}

				/*
				 * Remove old items.
				 */
				foreach ( $items_to_remove as $remove_item ) {
					$order->remove_item( $remove_item->get_id() );
					$remove_item->delete();
				}

				/*
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
			}
		}

		ob_start();
		include ( WC_ABSPATH . 'includes/admin/meta-boxes/views/html-order-items.php' );
		$html = ob_get_clean();

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

WC_PB_Admin_Ajax::init();
