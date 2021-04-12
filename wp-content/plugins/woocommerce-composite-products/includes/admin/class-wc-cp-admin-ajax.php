<?php
/**
 * WC_CP_Admin_Ajax class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin AJAX meta-box handlers.
 *
 * @class     WC_CP_Admin_Ajax
 * @version   8.0.0
 */
class WC_CP_Admin_Ajax {

	/**
	 * Hook in.
	 */
	public static function init() {

		/*
		 * Notices.
		 */

		// Dismiss notices.
		add_action( 'wp_ajax_woocommerce_dismiss_composite_notice', array( __CLASS__ , 'dismiss_notice' ) );

		// Ajax handler for performing loopback tests.
		add_action( 'wp_ajax_woocommerce_composite_loopback_test', array( __CLASS__, 'ajax_loopback_test' ) );

		/*
		 * Edit-Product screens.
		 */

		// Save composite config.
		add_action( 'wp_ajax_woocommerce_bto_composite_save', array( __CLASS__, 'ajax_composite_save' ) );

		// Add component.
		add_action( 'wp_ajax_woocommerce_add_composite_component', array( __CLASS__, 'ajax_add_component' ) );

		// Add scenario.
		add_action( 'wp_ajax_woocommerce_add_composite_scenario', array( __CLASS__, 'ajax_add_scenario' ) );

		// Add scenario.
		add_action( 'wp_ajax_woocommerce_add_composite_state', array( __CLASS__, 'ajax_add_state' ) );

		// Search products and variations.
		add_action( 'wp_ajax_woocommerce_json_search_component_options', array( __CLASS__, 'search_component_options' ) );
		add_action( 'wp_ajax_woocommerce_json_search_products_in_categories', array( __CLASS__, 'search_products_in_categories' ) );
		add_action( 'wp_ajax_woocommerce_json_search_products_and_variations_in_component', array( __CLASS__, 'search_products_and_variations_in_component' ) );

		// Fetch the categories of a product.
		add_action( 'wp_ajax_woocommerce_get_product_categories', array( __CLASS__, 'get_product_categories' ) );

		/*
		 * Edit-Order screens.
		 */

		// Ajax handler used to fetch form content for populating "Configure/Edit" composite order item modals.
		add_action( 'wp_ajax_woocommerce_configure_composite_order_item', array( __CLASS__, 'ajax_composite_order_item_form' ) );

		// Ajax handler for editing composites in manual/editable orders.
		add_action( 'wp_ajax_woocommerce_edit_composite_in_order', array( __CLASS__, 'ajax_edit_composite_in_order' ) );

		// Search products.
		add_action( 'wp_ajax_woocommerce_json_search_products_in_component', array( __CLASS__, 'search_products_in_component' ) );

		// Show selection details.
		add_action( 'wp_ajax_woocommerce_get_composited_product_data', array( __CLASS__ , 'ajax_show_composited_product' ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Notices.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Dismisses notices.
	 *
	 * @since  3.14.0
	 *
	 * @return void
	 */
	public static function dismiss_notice() {

		$failure = array(
			'result' => 'failure'
		);

		if ( ! check_ajax_referer( 'wc_cp_dismiss_notice_nonce', 'security', false ) ) {
			wp_send_json( $failure );
		}

		if ( empty( $_POST[ 'notice' ] ) ) {
			wp_send_json( $failure );
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json( $failure );
		}

		$dismissed = WC_CP_Admin_Notices::dismiss_notice( wc_clean( $_POST[ 'notice' ] ) );

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
	 * @since  7.0.3
	 *
	 * @return void
	 */
	public static function ajax_loopback_test() {

		$failure = array(
			'result' => 'failure',
			'reason' => ''
		);

		if ( ! check_ajax_referer( 'wc_cp_loopback_notice_nonce', 'security', false ) ) {
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

		WC_CP_Admin_Notices::set_notice_option( 'loopback', 'last_tested', gmdate( 'U' ) );
		WC_CP_Admin_Notices::set_notice_option( 'loopback', 'last_result', $passes_test ? 'pass' : 'fail' );

		if ( ! $passes_test ) {
			$failure[ 'reason' ]  = 'status';
			$failure[ 'status' ]  = $result->status;
			$failure[ 'message' ] = $result->message;
			wp_send_json( $failure );
		}

		WC_CP_Admin_Notices::remove_maintenance_notice( 'loopback' );

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
	 * Fetches the categories of a product.
	 *
	 * @since  3.14.0
	 * @return void
	 */
	public static function get_product_categories() {

		check_ajax_referer( 'wc_bto_get_product_categories', 'security' );

		if ( empty( $_POST[ 'product_id' ] ) ) {
			die();
		}

		$product = wc_get_product( absint( $_POST[ 'product_id' ] ) );

		if ( ! $product ) {
			die();
		}

		wp_send_json( array(
			'result'       => 'success',
			'category_ids' => $product->get_category_ids()
		) );
	}

	/**
	 * Handles saving composite config via ajax.
	 *
	 * @return void
	 */
	public static function ajax_composite_save() {

		check_ajax_referer( 'wc_bto_save_composite', 'security' );

		$posted_composite_data = array();
		if ( isset( $_POST[ 'data' ] ) ) {
			parse_str( $_POST[ 'data' ], $posted_composite_data ); // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		$post_id = isset( $_POST[ 'post_id' ] )  ? absint( $_POST[ 'post_id' ] ) : 0;

		WC_CP_Meta_Box_Product_Data::save_configuration( $post_id, $posted_composite_data );

		wc_delete_product_transients( $post_id );

		ob_start();

		WC_CP_Meta_Box_Product_Data::composite_data_panel();

		$html = ob_get_clean();

		wp_send_json( array(
			'result'  => 'success',
			'notices' => WC_CP_Meta_Box_Product_Data::$ajax_notices,
			'html'    => $html
		) );
	}

	/**
	 * Handles adding components via ajax.
	 *
	 * @return void
	 */
	public static function ajax_add_component() {

		check_ajax_referer( 'wc_bto_add_component', 'security' );

		$id      = isset( $_POST[ 'id' ] ) ? intval( $_POST[ 'id' ] ) : 0;
		$post_id = isset( $_POST[ 'post_id' ] ) ? intval( $_POST[ 'post_id' ] ) : 0;

		$component_data = array( 'composite_id' => $post_id );

		/**
		 * Action 'woocommerce_composite_component_admin_html'.
		 *
		 * @param  int     $id
		 * @param  array   $component_data
		 * @param  int     $post_id
		 * @param  string  $state
		 *
		 * @hooked {@see component_admin_html} - 10
		 */
		do_action( 'woocommerce_composite_component_admin_html', $id, $component_data, $post_id, 'open' );

		die();
	}

	/**
	 * Handles adding scenarios via ajax.
	 *
	 * @return void
	 */
	public static function ajax_add_scenario() {

		check_ajax_referer( 'wc_bto_add_scenario', 'security' );

		$id      = isset( $_POST[ 'id' ] ) ? intval( $_POST[ 'id' ] ) : 0;
		$post_id = isset( $_POST[ 'post_id' ] ) ? intval( $_POST[ 'post_id' ] ) : 0;

		$composite      = new WC_Product_Composite( $post_id );
		$composite_data = $composite->get_composite_data( 'edit' );
		$scenario_data  = array( 'is_ajax' => true );

		WC_CP_Meta_Box_Product_Data::set_global_object_data( $composite );

		/**
		 * Action 'woocommerce_composite_scenario_admin_html'.
		 *
		 * @param  int     $id
		 * @param  array   $scenario_data
		 * @param  array   $composite_data
		 * @param  int     $post_id
		 * @param  string  $state
		 *
		 */
		do_action( 'woocommerce_composite_scenario_admin_html', $id, $scenario_data, $composite_data, $post_id, 'open' );

		die();
	}

	/**
	 * Handles adding states via ajax.
	 *
	 * @return void
	 */
	public static function ajax_add_state() {

		check_ajax_referer( 'wc_bto_add_state', 'security' );

		$id      = isset( $_POST[ 'id' ] ) ? intval( $_POST[ 'id' ] ) : 0;
		$post_id = isset( $_POST[ 'post_id' ] ) ? intval( $_POST[ 'post_id' ] ) : 0;

		$composite      = new WC_Product_Composite( $post_id );
		$composite_data = $composite->get_composite_data( 'edit' );
		$state_data     = array( 'is_ajax' => true, 'is_state' => true );

		WC_CP_Meta_Box_Product_Data::set_global_object_data( $composite );

		/**
		 * Action 'woocommerce_composite_scenario_admin_html'.
		 *
		 * @param  int     $id
		 * @param  array   $state_data
		 * @param  array   $composite_data
		 * @param  int     $post_id
		 * @param  string  $state
		 *
		 */
		do_action( 'woocommerce_composite_state_admin_html', $id, $state_data, $composite_data, $post_id, 'open' );

		die();
	}

	/**
	 * Search for products and variations in component.
	 *
	 * @since  3.14.0
	 *
	 * @return void
	 */
	public static function search_products_in_categories() {

		$include_category_ids = ! empty( $_GET[ 'include' ] ) ? array_map( 'absint', explode( ',', wc_clean( $_GET[ 'include' ] ) ) ) : array();

		if ( empty( $include_category_ids ) ) {
			wp_die();
		}

		$include_category_slugs = get_terms( 'product_cat', array(
			'include' => $include_category_ids,
			'fields'  => 'id=>slug'
		) );

		if ( empty( $include_category_slugs ) ) {
			wp_die();
		}

		$product_ids = wc_get_products( array(
			'category' => array_values( $include_category_slugs ),
			'return'   => 'ids',
			'limit'    => -1
		) );

		$_GET[ 'include' ] = $product_ids;

		WC_AJAX::json_search_products();
	}

	/**
	 * Search for products and variations in component.
	 *
	 * @return void
	 */
	public static function search_products_and_variations_in_component() {
		self::search_products_in_component( array( 'include_variations' => true ) );
	}

	/**
	 * Search for products and variations in component.
	 *
	 * @param  array  $args
	 * @return void
	 */
	public static function search_products_in_component( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'include_variations' => false
		) );

		if ( ! empty( $_GET[ 'include' ] ) ) {

			$include           = wc_clean( $_GET[ 'include' ] );
			$composite_id      = isset( $include[ 'composite_id' ] ) ? absint( $include[ 'composite_id' ] ) : false;
			$component_id      = isset( $include[ 'component_id' ] ) ? absint( $include[ 'component_id' ] ) : false;
			$composite         = $composite_id && $component_id ? wc_get_product( $composite_id ) : false;

			if ( ! $composite ) {
				wp_die();
			}

			$component         = $composite->get_component( $component_id );
			$component_options = $component ? WC_CP_Component::query_component_options( $component->get_data() ) : array();

			if ( empty( $component_options ) ) {
				wp_die();
			}

			if ( $args[ 'include_variations' ] ) {

				$_GET[ 'include_parent_ids' ] = $composite->get_data_store()->get_expanded_component_options( $component_options, 'mapped' );
				$_GET[ 'include' ]            = $composite->get_data_store()->get_expanded_component_options( $component_options, 'merged' );

				// Add 'Any Variation' suffix to variable products.
				add_filter( 'woocommerce_json_search_found_products', array( __CLASS__, 'component_options_in_scenario_search_results' ) );

			} else {

				$_GET[ 'include' ] = $component_options;
			}
		}

		WC_AJAX::json_search_products( '', $args[ 'include_variations' ] );
	}

	/**
	 * Ajax search for Component Options: Show results for supported product types only.
	 */
	public static function search_component_options() {

		add_filter( 'woocommerce_json_search_found_products', array( __CLASS__, 'component_options_search_results' ) );
		WC_AJAX::json_search_products( '', false );
	}

	/**
	 * Include only supported product types in Component Options search results.
	 *
	 * @param  array  $search_results
	 * @return array
	 */
	public static function component_options_search_results( $search_results ) {

		if ( ! empty( $search_results ) ) {

			$search_results_filtered = array();

			foreach ( $search_results as $product_id => $product_title ) {

				$product = wc_get_product( $product_id );

				if ( is_object( $product ) && in_array( $product->get_type(), WC_Product_Composite::get_supported_component_option_types() ) ) {
					$search_results_filtered[ $product_id ] = $product_title;
				}
			}

			$search_results = $search_results_filtered;
		}

		return $search_results;
	}

	/**
	 * Modify variable product titles when searching in scenarios.
	 *
	 * @since  3.14.3
	 *
	 * @param  array  $search_results
	 * @return array
	 */
	public static function component_options_in_scenario_search_results( $search_results ) {

		if ( ! empty( $search_results ) && ! empty( $_GET[ 'include_parent_ids' ] ) ) {

			$variable_product_ids = wc_clean( $_GET[ 'include_parent_ids' ] );

			foreach ( $search_results as $product_id => $product_title ) {

				if ( in_array( $product_id, $variable_product_ids ) ) {
					$search_results[ $product_id ] = WC_CP_Helpers::format_product_title( $search_results[ $product_id ], '', __( 'Any Variation', 'woocommerce-composite-products' ), false );
				}
			}
		}

		return $search_results;
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
	public static function is_composite_edit_request() {
		return doing_action( 'wp_ajax_woocommerce_configure_composite_order_item' ) || doing_action( 'wp_ajax_woocommerce_get_composited_product_data' );
	}

	/**
	 * Form content used to populate "Configure/Edit" composite order item modals.
	 *
	 * @since  3.14.0
	 *
	 * @return void
	 */
	public static function ajax_composite_order_item_form() {

		global $product;

		$failure = array(
			'result' => 'failure'
		);

		if ( ! check_ajax_referer( 'wc_bto_edit_composite', 'security', false ) ) {
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

		if ( empty( $product ) ) {
			wp_send_json( $failure );
		}

		// Filter component config.
		add_filter( 'woocommerce_composite_component_data', array( __CLASS__, 'filter_component_data' ), 10 );

		$components = $product->get_components();

		// Initialize form state based on the actual configuration of the composite.
		$configuration = WC_CP_Order::get_current_composite_configuration( $item, $order );

		if ( ! empty( $configuration ) ) {
			$_REQUEST = array_merge( $_REQUEST, WC_CP()->cart->rebuild_posted_composite_form_data( $configuration ) );
		}

		// Force 'single' layout.
		$product->set_layout( 'single' );

		ob_start();
		include( WC_CP_ABSPATH . 'includes/admin/meta-boxes/views/html-composite-edit-form.php' );
		$html = ob_get_clean();

		$response = array(
			'result' => 'success',
			'html'   => $html
		);

		wp_send_json( $response );
	}

	/**
	 * Validates edited/configured composites and returns updated order items.
	 *
	 * @since  3.14.0
	 *
	 * @return void
	 */
	public static function ajax_edit_composite_in_order() {

		$failure = array(
			'result' => 'failure'
		);

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_send_json( $failure );
		}

		if ( ! check_ajax_referer( 'wc_bto_edit_composite', 'security', false ) ) {
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

		if ( ! ( $product instanceof WC_Product_Composite ) ) {
			wp_send_json( $failure );
		}

		if ( ! empty( $_POST[ 'fields' ] ) ) {
			parse_str( $_POST[ 'fields' ], $posted_form_fields ); // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$_POST = array_merge( $_POST, $posted_form_fields );
		}

		$posted_configuration  = WC_CP()->cart->get_posted_composite_configuration( $product );
		$current_configuration = WC_CP_Order::get_current_composite_configuration( $item, $order );

		// Compare posted against current configuration.
		if ( $posted_configuration !== $current_configuration ) {

			$added_to_order = WC_CP()->order->add_composite_to_order( $product, $order, $item->get_quantity(), array(

				/**
				 * 'woocommerce_editing_composite_in_order_configuration' filter.
				 *
				 * Use this filter to modify the posted configuration.
				 *
				 * @param  $config   array
				 * @param  $product  WC_Product_Composite
				 * @param  $item     WC_Order_Item
				 * @param  $order    WC_Order
				 */
				'configuration' => apply_filters( 'woocommerce_editing_composite_in_order_configuration', $posted_configuration, $product, $item, $order )
			) );

			// Invalid configuration?
			if ( is_wp_error( $added_to_order ) ) {

				$message = __( 'The submitted configuration is invalid.', 'woocommerce-composite-products' );
				$data    = $added_to_order->get_error_data();
				$notice  = isset( $data[ 'notices' ] ) ? current( $data[ 'notices' ] ) : '';

				if ( $notice ) {
					$notice_text = WC_CP_Core_Compatibility::is_wc_version_gte( '3.9' ) ? $notice[ 'notice' ] : $notice;
					$message     = sprintf( _x( '%1$s %2$s', 'edit composite in order: formatted validation message', 'woocommerce-composite-products' ), $message, html_entity_decode( $notice_text ) );
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
				 * 'woocommerce_editing_composite_in_order' action.
				 *
				 * @since  3.15.1
				 *
				 * @param  WC_Order_Item_Product  $new_item
				 * @param  WC_Order_Item_Product  $old_item
				 */
				do_action( 'woocommerce_editing_composite_in_order', $new_container_item, $item, $order );

				$components_to_remove = wc_cp_get_composited_order_items( $item, $order );
				$items_to_remove      = array( $item ) + wc_cp_get_composited_order_items( $item, $order, false, true );

				/*
				 * Adjust stock.
				 */
				if ( WC_CP_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {

					if ( $item_reduced_stock = $item->get_meta( '_reduced_stock', true ) ) {
						$new_container_item->add_meta_data( '_reduced_stock', $item_reduced_stock, true );
						$new_container_item->save();
					}

					$stock_map   = array();
					$changes_map = array();

					foreach ( $components_to_remove as $component_to_remove ) {

						$component_id = $component_to_remove->get_meta( '_composite_item', true );
						$product_id   = $component_to_remove->get_product_id();

						if ( $variation_id = $component_to_remove->get_variation_id() ) {
							$product_id = $variation_id;
						}

						// Store change to add in order note.
						$changes_map[ $component_id ] = array(
							'id'      => $product_id,
							'actions' => array(
								'remove' => array(
									'title' => $component_to_remove->get_name(),
									'sku'   => '#' . $product_id
								)
							)
						);

						$changed_stock = wc_maybe_adjust_line_item_product_stock( $component_to_remove, 0 );

						if ( $changed_stock && ! is_wp_error( $changed_stock ) ) {

							$composited_product     = $component_to_remove->get_product();
							$composited_product_sku = $composited_product->get_sku();
							$stock_managed_by_id    = $composited_product->get_stock_managed_by_id();

							if ( ! $composited_product_sku ) {
								$composited_product_sku = '#' . $composited_product->get_id();
							}

							// Associate change with stock.
							$changes_map[ $component_id ][ 'actions' ][ 'remove' ][ 'stock_managed_by_id' ] = $stock_managed_by_id;
							$changes_map[ $component_id ][ 'actions' ][ 'remove' ][ 'sku' ]                 = $composited_product_sku;

							if ( isset( $stock_map[ $stock_managed_by_id ] ) ) {
								$stock_map[ $stock_managed_by_id ][ 'to' ] = $changed_stock[ 'to' ];
							} else {
								$stock_map[ $stock_managed_by_id ] = array(
									'from' => $changed_stock[ 'from' ],
									'to'   => $changed_stock[ 'to' ]
								);
							}
						}
					}

					$components_to_add = wc_cp_get_composited_order_items( $new_container_item, $order );

					foreach ( $components_to_add as $order_item_id => $order_item ) {

						$component_id          = $order_item->get_meta( '_composite_item', true );
						$composited_product    = $order_item->get_product();
						$composited_product_id = $composited_product->get_id();
						$action                = 'add';

						// Store change to add in order note.
						if ( isset( $changes_map[ $component_id ] ) ) {

							// If the selection didn't change, log it as an adjustment.
							if ( $composited_product_id === $changes_map[ $component_id ][ 'id' ] ) {

								$action = 'adjust';

								$changes_map[ $component_id ][ 'actions' ] = array(
									'adjust' => array(
										'title' => $order_item->get_name(),
										'sku'   => '#' . $composited_product_id
									)
								);

							// Otherwise, log another 'add' action.
							} else {

								$changes_map[ $component_id ][ 'actions' ][ 'add' ] = array(
									'title' => $order_item->get_name(),
									'sku'   => '#' . $composited_product_id
								);
							}

						// If we're seeing this item for the first time, log an 'add' action.
						} else {

							$changes_map[ $component_id ] = array(
								'id'      => $composited_product_id,
								'actions' => array(
									'add' => array(
										'title' => $order_item->get_name(),
										'sku'   => '#' . $composited_product_id
									)
								)
							);
						}

						if ( $composited_product && $composited_product->managing_stock() ) {

							$composited_product_sku = $composited_product->get_sku();
							$stock_managed_by_id    = $composited_product->get_stock_managed_by_id();
							$qty                    = $order_item->get_quantity();

							if ( ! $composited_product_sku ) {
								$composited_product_sku = '#' . $composited_product->get_id();
							}

							// Associate change with stock.
							$changes_map[ $component_id ][ 'actions' ][ $action ][ 'stock_managed_by_id' ] = $stock_managed_by_id;
							$changes_map[ $component_id ][ 'actions' ][ $action ][ 'sku' ]                 = $composited_product_sku;

							$old_stock = $composited_product->get_stock_quantity();
							$new_stock = wc_update_product_stock( $composited_product, $qty, 'decrease' );

							if ( isset( $stock_map[ $stock_managed_by_id ] ) ) {
								$stock_map[ $stock_managed_by_id ][ 'to' ] = $new_stock;
							} else {
								$stock_map[ $stock_managed_by_id ] = array(
									'from'    => $old_stock,
									'to'      => $new_stock
								);
							}

							$order_item->add_meta_data( '_reduced_stock', $qty, true );
							$order_item->save();
						}
					}

					$stock_strings = array(
						'add'    => array(),
						'remove' => array(),
						'adjust' => array()
					);

					foreach ( $changes_map as $component_id => $item_changes ) {

						$actions         = array( 'add', 'remove', 'adjust' );
						$component       = $product->get_component( $component_id );
						$component_title = $component ? $component->get_title() : sprintf( __( 'Component #%s', 'woocommerce-composite-products' ), $component_id );

						foreach ( $actions as $action ) {

							if ( isset( $item_changes[ 'actions' ][ $action ] ) ) {

								$stock_changes        = isset( $item_changes[ 'actions' ][ $action ][ 'stock_managed_by_id' ] ) && isset( $stock_map[ $item_changes[ 'actions' ][ $action ][ 'stock_managed_by_id' ] ] ) ? $stock_map[ $item_changes[ 'actions' ][ $action ][ 'stock_managed_by_id' ] ] : false;
								$stock_from_to_string = $stock_changes && $stock_changes[ 'from' ] && $stock_changes[ 'from' ] !== $stock_changes[ 'to' ] ? ( $stock_changes[ 'from' ] . '&rarr;' . $stock_changes[ 'to' ] ) : '';

								if ( $stock_from_to_string ) {
									$stock_strings[ $action ][] = sprintf( _x( '%1$s: %2$s (%3$s) &ndash; %4$s', 'component stock change note format', 'woocommerce-composite-products' ), $component_title, $item_changes[ 'actions' ][ $action ][ 'title' ], $item_changes[ 'actions' ][ $action ][ 'sku' ], $stock_from_to_string );
								} else {
									$stock_strings[ $action ][] = sprintf( _x( '%1$s: %2$s (%3$s)', 'component change note format', 'woocommerce-composite-products' ), $component_title, $item_changes[ 'actions' ][ $action ][ 'title' ], $item_changes[ 'actions' ][ $action ][ 'sku' ] );
								}
							}
						}
					}

					if ( ! empty( $stock_strings[ 'remove' ] ) ) {
						$order->add_order_note( sprintf( __( 'Deleted component line items: %s', 'woocommerce-composite-products' ), implode( ', ', $stock_strings[ 'remove' ] ) ), false, true );
					}

					if ( ! empty( $stock_strings[ 'add' ] ) ) {
						$order->add_order_note( sprintf( __( 'Added component line items: %s', 'woocommerce-composite-products' ), implode( ', ', $stock_strings[ 'add' ] ) ), false, true );
					}

					if ( ! empty( $stock_strings[ 'adjust' ] ) ) {
						$order->add_order_note( sprintf( __( 'Adjusted component line items: %s', 'woocommerce-composite-products' ), implode( ', ', $stock_strings[ 'adjust' ] ) ), false, true );
					}
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

		if ( WC_CP_Core_Compatibility::is_wc_version_gte( '3.6' ) ) {

			ob_start();
			$notes = wc_get_order_notes( array( 'order_id' => $order->get_id() ) );
			include ( WC_ABSPATH . 'includes/admin/meta-boxes/views/html-order-notes.php' );
			$notes_html = ob_get_clean();
			$response   = array(
				'result'     => 'success',
				'html'       => $html,
				'notes_html' => $notes_html
			);

		} else {
			$response = array(
				'result' => 'success',
				'html'   => $html
			);
		}
		wp_send_json( $response );
	}

	/**
	 * Fetches selection data.
	 *
	 * @since  3.14.0
	 *
	 * @return void
	 */
	public static function ajax_show_composited_product() {

		// Filter component config.
		add_filter( 'woocommerce_composite_component_data', array( __CLASS__, 'filter_component_data' ), 10 );

		return WC_CP_AJAX::show_composited_product_ajax();
	}

	/**
	 * Filter component data in edit-order context.
	 *
	 * @since  3.14.0
	 *
	 * @param  array  $component_data
	 * @return array
	 */
	public static function filter_component_data( $component_data ) {

		// Disable Add-Ons.
		$component_data[ 'disable_addons' ] = true;

		// Disable Sorting/Filtering.
		$component_data[ 'show_orderby' ] = 'no';
		$component_data[ 'show_filters' ] = 'no';

		// Selection title/image visibility.
		$component_data[ 'hide_product_title' ]     = 'yes';
		$component_data[ 'hide_product_price' ]     = 'yes';
		$component_data[ 'hide_product_thumbnail' ] = 'no';

		// Force 'dropdowns' style.
		$component_data[ 'selection_mode' ] = 'dropdowns';

		// Hide prices.
		$component_data[ 'display_prices' ] = 'hidden';

		return $component_data;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Add variations to component product search results.
	 *
	 * @deprecated  3.14.0
	 *
	 * @param  array  $search_results
	 * @return array
	 */
	public static function add_variations_to_component_search_results( $search_results ) {

		$search_results_incl_variations = array();

		if ( ! empty( $search_results ) ) {

			$search_result_objects = array_map( 'wc_get_product', array_keys( $search_results ) );

			foreach ( $search_result_objects as $product ) {
				if ( $product ) {

					$product_id                                    = $product->get_id();
					$search_results_incl_variations[ $product_id ] = WC_CP_Helpers::get_product_title( $product, '', $product->is_type( 'variable' ) ? __( 'Any Variation', 'woocommerce-composite-products' ) : '' );

					if ( $product->is_type( 'variable' ) ) {

						$child_ids     = $product->get_children();
						$child_objects = array_map( 'wc_get_product', $child_ids );

						if ( ! empty( $child_objects ) ) {
							foreach ( $child_objects as $child ) {
								if ( $child ) {
									$child_id                                    = $child->get_id();
									$search_results_incl_variations[ $child_id ] = rawurldecode( WC_CP_Helpers::get_product_variation_title( $child, 'flat' ) );
								}
							}
						}
					}
				}
			}
		}

		return $search_results_incl_variations;
	}
}

WC_CP_Admin_Ajax::init();
