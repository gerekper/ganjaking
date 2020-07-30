<?php
/**
 * Order Functions and Filters
 *
 * @author   Kathy Darling
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Orders
 * @since    1.0.0
 * @version  1.9.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_Order Class.
 *
 * Functions and filters for ordering Mix and Match type products.
 */
class WC_Mix_and_Match_Order {

	/**
	 * Flag to short-circuit 'WC_Mix_and_Match_Order::get_order_items'.
	 *
	 * @var bool
	 */
	public static $override_order_items_filter = false;

	/**
	 * Flag to short-circuit 'WC_Mix_and_Match_Order::get_product_from_item'.
	 *
	 * @var bool
	 */
	public static $override_product_from_item_filter = false;

	/**
	 * The single instance of the class.
	 * @var WC_Mix_and_Match_Order
	 *
	 * @since 1.9.2
	 */
	protected static $_instance = null;

	/**
	 * Main class instance. Ensures only one instance of class is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_Mix_and_Match_Order
	 * @since  1.9.2
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Setup order class.
	 */
	public function __construct() {

		// Filter price output shown in cart, review-order & order-details templates.
		add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'order_item_subtotal' ), 10, 3 );

		// Containers should not affect order status.
		add_filter( 'woocommerce_order_item_needs_processing', array( $this, 'container_item_needs_processing' ), 10, 3 );

		// Modify order items to include container meta.
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_meta' ), 10, 3 );

		// Hide container configuration metadata in order line items.
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_item_meta' ) );

		// Filter order item count in the front-end.
		add_filter( 'woocommerce_get_item_count', array( $this, 'order_item_count' ), 10, 3 );

		// Filter admin dashboard item count and classes.
		if( is_admin() ) {
			add_filter( 'woocommerce_admin_html_order_item_class', array( $this, 'html_order_item_class' ), 10, 3 );
			add_filter( 'woocommerce_admin_html_order_preview_item_class', array( $this, 'html_order_item_class' ), 10, 3 );
		}

		// Modify product while completing payment - @see 'get_processing_product_from_item()' and 'container_item_needs_processing()'.
		add_action( 'woocommerce_pre_payment_complete', array( $this, 'apply_order_item_product_filter' ) );
		add_action( 'woocommerce_payment_complete', array( $this, 'remove_get_product_from_item_filter' ) );
	}


	/*
	|--------------------------------------------------------------------------
	| API functions.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Reads the configuration array stored on a container order item and modifies it based on the actual state of the container.
	 *
	 * @since  1.7.0
	 *
	 * @param  WC_Order_Item  $order_item
	 * @param  WC_Order       $order
	 * @return array
	 */
	public static function get_current_container_configuration( $order_item, $order ) {

		if ( ! wc_mnm_is_container_order_item( $order_item ) ) {
			return false;
		}

		// The original saved config.
		$configuration       = $order_item->get_meta( '_mnm_config', true );

		// This returns children that are actually in the ORDER right now.
		$child_order_items = wc_mnm_get_child_order_items( $order_item, $order );		

		/*
		 * There's no chance something might have been added to the stored configuration.
		 * However, admins may have deleted items or modified their quantities.
		 */
		foreach ( $configuration as $child_item_id => $child_item_configuration ) {

			$child_order_item_qty = false;

			foreach ( $child_order_items as $child_order_item ) {
				// Compare variation ID or product ID depending on if variation or simple.
				$child_order_item_mnm_id = $child_order_item->get_variation_id() > 0 ? $child_order_item->get_variation_id() : $child_order_item->get_product_id();
				if ( absint( $child_item_id ) === absint( $child_order_item_mnm_id ) ) {
					$child_order_item_qty = $child_order_item->get_quantity();
					break;
				}
			}

			if ( ! $child_order_item_qty ) {
				unset( $configuration[ $child_item_id ] );
				continue;
			}

			// Normalize with the quantity of the parent.
			$configuration[ $child_item_id ][ 'quantity' ] = $child_order_item_qty / $order_item->get_quantity();
		}

		return $configuration;
	}

	/**
	 * Validates a container configuration and adds all associated line items to an order. Relies on specifying a container configuration array with all necessary data.
	 * The configuration array is passed as a 'configuration' key of the $args method argument. Example:
	 *
	 *    $args = array(
	 *        'configuration' => array(
	 *            134 => array(                      // ID of child item. Product ID if simple, Variation ID if variation.
	 *                'quantity'          => 2,      // Qty of child product, will fall back to min.
	 *                'variation_id'      => 134,    // ID of chosen variation, if applicable.
	 *                'variation'         => array() // array of attributes
	 *                'product_id'        => 200     // ID of the product.
	 *                'args'              => array() // Custom child item args to pass into 'WC_Order::add_product()'.
	 *            )
	 *        )
	 *    );
	 *
	 * Returns the container order item ID if sucessful, or false otherwise.
	 *
	 * Note: Container/child order item totals are calculated without taxes, based on their pricing setup.
	 * - Container item totals can be overridden by passing a 'totals' array in $args, as with 'WC_Order::add_product()'.
	 * - Child item totals can be overridden in the 'configuration' array, as shown in the example above.
	 *
	 *
	 * @param  WC_Product_Mix_and_Match  $container
	 * @param  WC_Order                 $order
	 * @param  integer                  $quantity
	 * @param  array                    $args
	 * @return integer|WP_Error
	 */
	public function add_container_to_order( $container, $order, $quantity = 1, $args = array() ) {

		$added_to_order = false;

		$args = wp_parse_args( $args, array(
			'configuration' => array(),
			'silent'        => true
		) );

		if ( $container && $container->is_type( 'mix-and-match' ) ) {

			$configuration = $args[ 'configuration' ];

			if ( WC_Mix_and_Match()->cart->validate_container_configuration( $container, $quantity, $configuration, 'add-to-order' ) ) {

				// Add container item.
				$container_order_item_id = $order->add_product( $container, $quantity, $args );
				$added_to_order          = $container_order_item_id;

				// Unique hash to use in place of the cart item ID.
				$container_item_hash = md5( $container_order_item_id );

				// Add child items.
				$child_items = $container->get_children();

				$aggregate_weight = 0.0;
				$items_need_processing = false;

				if ( ! empty( $child_items ) ) {
					foreach ( $child_items as $child_item_id => $child_product ) {

						$child_item_configuration  = isset( $configuration[ $child_item_id ] ) ? $configuration[ $child_item_id ] : array();
						$child_item_quantity       = isset( $child_item_configuration[ 'quantity' ] ) ? absint( $child_item_configuration[ 'quantity' ] ) : apply_filters( 'woocommerce_mnm_quantity_input', 0, $child_product, $container );
						$child_item_variation_data = $child_product->get_parent_id() > 0 ? $child_product->get_variation_attributes() : array();
						$child_item_args           = isset( $child_item_configuration[ 'args' ] ) ? $child_item_configuration[ 'args' ] : array();
						// Unique hash to use in place of the cart item ID.
						$child_item_hash           = md5( $child_item_id );

						if ( 0 === $child_item_quantity ) {
							continue;
						}

						if ( $container->is_priced_per_product() ) {
							// @todo: are these subtotal/total amounts actually in the args?
							$child_item_args[ 'subtotal' ]     = isset( $child_item_args[ 'subtotal' ] ) ? $child_item_args[ 'subtotal' ] : wc_get_price_excluding_tax( $child_product, array( 'qty' => $child_item_quantity * $quantity ) );
							$child_item_args[ 'total' ]        = isset( $child_item_args[ 'total' ] ) ? $child_item_args[ 'total' ] : wc_get_price_excluding_tax( $child_product, array( 'qty' => $child_item_quantity * $quantity ) );
							$child_item_args[ 'subtotal_tax' ] = isset( $child_item_args[ 'subtotal_tax' ] ) ? $child_item_args[ 'subtotal_tax' ] : 0;
							$child_item_args[ 'total_tax' ]    = isset( $child_item_args[ 'total_tax' ] ) ? $child_item_args[ 'total_tax' ] : 0;

						} else {
							$child_item_args[ 'subtotal' ]     = 0;
							$child_item_args[ 'total' ]        = 0;
							$child_item_args[ 'subtotal_tax' ] = 0;
							$child_item_args[ 'total_tax' ]    = 0;
						}

						// Args to pass into 'add_product()'.
						$child_item_args[ 'variation' ] = $child_item_variation_data;

						// Add child item.
						$child_order_item_id = $order->add_product( $child_product, $child_item_quantity * $quantity, $child_item_args );

						if ( ! $child_order_item_id ) {
							continue;
						}

						// Locate the item.
						$order_items        = $order->get_items( 'line_item' );
						$child_order_item = $order_items[ $child_order_item_id ];

						/*
						 * Add child order item meta.
						 */
						$child_order_item->add_meta_data( '_mnm_container', $container_item_hash, true );

						// Old Part of: Meta
						if( apply_filters( 'woocommerce_mnm_order_item_legacy_part_of_meta', false, $child_order_item ) ) {
							// Use "Purchased with" to imply that item is physically shipped separately from its container.
							// Use "Part of" to imply that item is physically assembled or packaged in its container.
							$part_of_meta_name = $child_product->needs_shipping() ? 'mnm_purchased_with' : 'mnm_part_of';


							if ( has_filter( 'woocommerce_mnm_order_item_part_of_meta_name' ) ) {

								$msg = __( 'woocommerce_mnm_order_item_part_of_meta_name filter is deprecated. You should filter the meta label via woocommerce_order_item_display_meta_key instead.', 'woocommerce-mix-and-match-products' );
								wc_doing_it_wrong( 'woocommerce_mnm_order_item_part_of_meta_name', $msg, '1.10.0' );

								/**
								 * "Part Of" Meta Name.
								 *
								 * @param  string   	  $part_of_meta_name
								 * @param  array          $cart_item_values
					 			 * @param  string         $cart_item_key
								 */	
								$part_of_meta_name = apply_filters( 'woocommerce_mnm_order_item_part_of_meta_name', $part_of_meta_name, $cart_item_values, $cart_item_key );

							}

							if ( $part_of_meta_name ) {
								$child_order_item->add_meta_data( $part_of_meta_name, $container->get_title(), true );
							}
	
						}

						// If any children need processing, the container needs processing. Mimic mnm_items_need_processing without cart.
						if( false == $items_need_processing ) {
							if ( false === $child_product->is_downloadable() || false === $child_product->is_virtual() ) {
								$processing_needed = true;
							}
						}

						// Shipping Setup.
						if ( $child_product->needs_shipping() ) {

							/**
							 * Is child item shipped individually or as part of container.
							 *
							 * @param bool $per_product_shipping
							 * @param obj WC_Product $product
							 * @param  int $child_item_id Product or Variation ID of child item.
							 * @param obj WC_Product_Mix_and_Match $container Product object of parent container.
							 */
							if ( false === apply_filters( 'woocommerce_mnm_item_shipped_individually', $container->is_shipped_per_product(), $child_product, $child_item_id, $container ) ) {

								/**
								 * Does the child item have weight?
								 *
								 * @param bool $has_weight
								 * @param obj WC_Product  $cart_item['data']
								 * @param  int $item_id Product or Variation ID of child item.
								 * @param obj WC_Product_Mix_and_Match $container Product object of parent container.
								 */
								if ( apply_filters( 'woocommerce_mnm_item_has_bundled_weight', false, $child_product, $child_item_id, $container ) ) {
									$aggregate_weight += (double) $child_product->get_weight( 'edit' ) * $child_item_quantity;
								}


							}
						}

						// Store shipping data.
						$child_order_item->add_meta_data( '_mnm_item_needs_shipping', wc_bool_to_string( $child_product->needs_shipping() ), true );

						// Save the item.
						$child_order_item->save();

						/**
						 * 'woocommerce_mnm_child_add_to_order' action.
						 *
						 * @param  int                $child_order_item_id
						 * @param  WC_Order           $order
						 * @param  WC_Product         $child_product
						 * @param  int                $child_item_quantity
						 * @param  WC_Product    	  $child_product
						 * @param  WC_Product_Bundle  $container
						 * @param  int                $quantity
						 * @param  array              $child_item_args
						 * @param  array              $args
						 */
						do_action( 'woocommerce_mnm_child_add_to_order', $child_order_item_id, $order, $child_product, $child_item_quantity, $child_product, $container, $quantity, $child_item_args, $args );
					}
				}

				// Locate the item.
				$order_items          = $order->get_items( 'line_item' );
				$container_order_item = $order_items[ $container_order_item_id ];


				/*
				 * Add container order item meta.
				 */

				/**
				 * "Container Size" meta data.
				 *
				 * Allows changes to the order item object before it is saved.
				 *
				 * @param  str          $container_size_meta_value
	 			 * @param  int 			$item_id
				 * @param  array        $cart_item_values
	 			 * @param  string       $cart_item_key
				 */	
				$container_size_meta_value = array_sum( wp_list_pluck( $configuration, 'quantity' ) );

				$container_size_meta_value = apply_filters( 'woocommerce_mnm_order_item_container_size_meta_value', $container_size_meta_value, $container_order_item->get_id(), $child_item_args, $child_item_hash );

				if ( $container_size_meta_value ) {
					$container_order_item->add_meta_data( 'mnm_container_size', $container_size_meta_value, true );
					//$container_order_item->add_meta_data( __( 'Container size', 'woocommerce-mix-and-match-products' ), $container_size_meta_value, true );
				}

				$container_order_item->add_meta_data( '_mnm_config', $configuration, true );
				$container_order_item->add_meta_data( '_mnm_cart_key', $container_item_hash, true );
				$container_order_item->add_meta_data( '_per_product_pricing', wc_bool_to_string( $container->is_priced_per_product() ), true );

				// If it's a physical container item, grab its aggregate weight from the package data.
				if ( $container->needs_shipping() ) {
					$container_order_item->add_meta_data( '_bundle_weight', (double) $container->get_weight( 'edit' ) + $aggregate_weight, true );
					// If it's a virtual container item, look at its children to see if any of them needs processing.
				} elseif ( false === $items_need_processing() ) {
					$container_order_item->add_meta_data( '_mnm_items_need_processing', 'no', true );
				}

				/*
				 * Add initial item meta.
				 */

				if ( ! empty( $args[ 'meta_data' ] ) ) {
					foreach ( $args[ 'meta_data' ] as $meta ) {

						if ( $meta instanceof WC_Meta_Data ) {
							$meta = $meta->get_data();
						}

						if ( ! isset( $meta[ 'key' ] ) || ! isset( $meta[ 'value' ] ) ) {
							continue;
						}

						if ( in_array( $meta[ 'key' ], array( '_mnm_container_size', '_mnm_config', '_mnm_cart_key', '_per_product_pricing','_bundle_weight' ) ) ) {
							continue;
						}

						$container_order_item->add_meta_data( $meta[ 'key' ], $meta[ 'value' ] );
					}
				}

				// Save the item.
				$container_order_item->save();

				/**
				 * 'wc_mnm_container_added_to_order' action.
				 *
				 * @since  1.7.0
				 *
				 * @param  WC_Order_Item      $container_order_item
				 * @param  WC_Order           $order
				 * @param  WC_Product_Mix_and_Match  $container
				 * @param  array              $args
				 */
				do_action( 'wc_mnm_container_added_to_order', $container_order_item, $order, $container, $quantity, $args );

			} else {

				$error_data = array( 'notices' => wc_get_notices( 'error' ) );
				$message    = __( 'The submitted container configuration could not be added to this order.', 'woocommerce-mix-and-match-products' );

				if ( $args[ 'silent' ] ) {
					wc_clear_notices();
				}

				$added_to_order = new WP_Error( 'wc_mnm_container_configuration_invalid', $message, $error_data );
			}

		} else {
			$message        = __( 'A Mix and Match product with this ID does not exist.', 'woocommerce-mix-and-match-products' );
			$added_to_order = new WP_Error( 'wc_mnm_container_invalid', $message );
		}

		return $added_to_order;
	}

	/**
	 * Modifies container/child order items depending on their shipping setup. Reconstructs an accurate representation of a container for shipping purposes.
	 * Used in combination with 'get_product_from_item', right below.
	 *
	 * Adds the totals of "packaged" items to the container totals and creates a container "Contents" meta field to provide a description of the included items.
	 *
	 * @param  array     $items
	 * @param  WC_Order  $order
	 * @return array
	 */
	public function get_order_items( $items, $order ) {

		// If short circuited, return the unmodified value.
		if ( self::$override_order_items_filter ) {
			return $items;
		}

		// Nobody likes infinite loops.
		$override_order_items_filter       = self::$override_order_items_filter;
		self::$override_order_items_filter = true;

		// We have no need for this here.
		$override_product_from_item_filter       = self::$override_product_from_item_filter;
		self::$override_product_from_item_filter = true;

		$return_items = array();

		foreach ( $items as $item_id => $item ) {

			if ( wc_mnm_is_container_order_item( $item ) ) {

				/*
				 * Add the totals of "packaged" items to the container totals and create a container "Contents" meta field to provide a description of the included products.
				 */
				$product = wc_get_product( $item->get_product_id() );

				if ( $product && $product->needs_shipping() && $child_items = wc_mnm_get_child_order_items( $item, $items ) ) {

					if ( ! empty( $child_items ) ) {

						// Aggregate contents.
						$contents = array();

						// Aggregate prices.
						$container_totals = array(
							'subtotal'     => $item->get_subtotal(),
							'total'        => $item->get_total(),
							'subtotal_tax' => $item->get_subtotal_tax(),
							'total_tax'    => $item->get_total_tax(),
							'taxes'        => $item->get_taxes()
						);

						foreach ( $child_items as $child_item_id => $child_item ) {

							// If the child is "packaged" in its parent...
							if ( 'no' === $child_item->get_meta( '_mnm_item_needs_shipping', true ) ) {

								$child_variation_id = $child_item->get_variation_id();
								$child_product_id   = $child_item->get_product_id();
								$child_id           = $child_variation_id ? $child_variation_id : $child_product_id;
								$child              = wc_get_product( $child_id );

								if ( ! $child || ! $child->needs_shipping() ) {
									continue;
								}

								/*
								 * Add item into a new container "Contents" meta.
								 */
								$sku = $child->get_sku();

								if ( ! $sku ) {
									$sku = '#' . $child_id;
								}

								$meta_data       = WC_MNM_Core_Compatibility::is_wc_version_gte( '3.1' ) ? $child_item->get_formatted_meta_data( '_', true ) : $child_item->get_formatted_meta_data();
								$meta_desc_array = array();

								if ( ! empty( $meta_data ) ) {
									foreach ( $meta_data as $meta_id => $meta ) {
										if ( $meta->key !== __( 'Part of', 'woocommerce-mix-and-match-products' ) ) {
											$meta_desc_array[] = array(
												'key'   => wp_kses_post( $meta->display_key ),
												'value' => wp_kses_post( trim( strip_tags( $meta->display_value ) ) )
											);
										}
									}
								}

								$meta_desc_array[] = array(
									'key'   => _x( 'Qty', 'child order item qty meta key', 'woocommerce-mix-and-match-products' ),
									'value' => $child_item->get_quantity()
								);

								$meta_desc_array[] = array(
									'key'   => _x( 'SKU', 'child order item SKU meta key', 'woocommerce-mix-and-match-products' ),
									'value' => $sku
								);

								foreach ( $meta_desc_array as $meta_desc_array_key => $meta_desc_array_value ) {
									$meta_desc_array[ $meta_desc_array_key ][ 'description' ] = $meta_desc_array_value[ 'key' ] . ' - ' . $meta_desc_array_value [ 'value' ];
								}

								$contents[] = array(
									/**
									 * Child order item meta title.
									 *
									 * @param  str          $title
						 			 * @param  int 			$item_id
									 * @param  array        $cart_item_values
						 			 * @param  string       $cart_item_key
									 */	
									'title'       => apply_filters( 'woocommerce_mnm_order_item_meta_title', $child->get_title(), $meta_desc_array, $child_item, $item, $order ),
									/**
									 * Child order item meta description
									 *
						 			 * @param  str 				  $description Comma delimited list.
									 * @param  array        	  $meta_desc_array
						 			 * @param  obj WC_Order_Item  $child_item
						 			 * @param  obj WC_Order 	  $order
									 */	
									'description' => apply_filters( 'woocommerce_mnm_order_item_meta_description', implode( ', ', wp_list_pluck( $meta_desc_array, 'description' ) ), $meta_desc_array, $child_item, $item, $order )
								);

								/*
								 * Add item totals to the container totals.
								 */
								$container_totals[ 'subtotal' ]     += $child_item->get_subtotal();
								$container_totals[ 'total' ]        += $child_item->get_total();
								$container_totals[ 'subtotal_tax' ] += $child_item->get_subtotal_tax();
								$container_totals[ 'total_tax' ]    += $child_item->get_total_tax();

								$child_item_tax_data = $child_item->get_taxes();

								$container_totals[ 'taxes' ][ 'total' ]    = array_merge( $container_totals[ 'taxes' ][ 'total' ], $child_item_tax_data[ 'total' ] );
								$container_totals[ 'taxes' ][ 'subtotal' ] = array_merge( $container_totals[ 'taxes' ][ 'subtotal' ], $child_item_tax_data[ 'subtotal' ] );
							}
						}

						// Build list of meta to set, with 'id' props intact.
						$item_meta_data_to_set = array();

						foreach ( $item->get_meta_data() as $item_meta ) {
							if ( isset( $item_meta->key, $item_meta->value, $item_meta->id ) ) {
								$item_meta_data_to_set[] = array(
									'key'   => $item_meta->key,
									'value' => $item_meta->value,
									'id'    => $item_meta->id
								);
							}
						}

						// Create a clone to ensure item totals will not be modified permanently.
						$cloned_item = clone $item;

						// Delete meta without 'id' prop.
						$cloned_item_meta_data = $cloned_item->get_meta_data();

						foreach ( $cloned_item_meta_data as $cloned_item_meta ) {
							$cloned_item->delete_meta_data( $cloned_item_meta->key );
						}

						// Copy back meta with 'id' prop intact.
						$cloned_item->set_meta_data( $item_meta_data_to_set );

						// Replace original with clone.
						$item = $cloned_item;

						// Find highest 'id'.
						$max_id = 1;
						foreach ( $item->get_meta_data() as $item_meta ) {
							if ( isset( $item_meta->id ) ) {
								if ( $item_meta->id >= $max_id ) {
									$max_id = $item_meta->id;
								}
							}
						}

						$item->set_props( $container_totals );

						// Create a meta field with product details for each child item.
						if ( ! empty( $contents ) ) {
							$added_keys = array();
							// Create a meta field for each child item.
							foreach ( $contents as $contained ) {
								$item->add_meta_data( $contained[ 'title' ], $contained[ 'description' ] );
								$added_keys[] = $contained[ 'title' ];
							}
							// Ensure meta objects have an 'id' prop so they can be picked up by 'get_formatted_meta_data'.
							foreach ( $item->get_meta_data() as $item_meta ) {
								if ( in_array( $item_meta->key, $added_keys ) && ! isset( $item_meta->id ) ) {
									$item_meta->id = $max_id + 1;
									$max_id++;
								}
							}
						}
					}
				}

			} elseif ( wc_mnm_is_child_order_item( $item, $items ) ) {

				$variation_id = $item->get_variation_id();
				$product_id   = $item->get_product_id();
				$id           = $variation_id ? $variation_id : $product_id;
				$product      = wc_get_product( $id );

				if ( $product && $product->needs_shipping() && 'no' === $item->get_meta( '_mnm_item_needs_shipping', true ) ) {

					$item_totals = array(
						'subtotal'     => 0,
						'total'        => 0,
						'subtotal_tax' => 0,
						'total_tax'    => 0,
						'taxes'        => array( 'total' => array(), 'subtotal' => array() )
					);

					// Build list of meta to set, with 'id' props intact.
					$item_meta_data_to_set = array();

					foreach ( $item->get_meta_data() as $item_meta ) {
						if ( isset( $item_meta->key, $item_meta->value, $item_meta->id ) ) {
							$item_meta_data_to_set[] = array(
								'key'   => $item_meta->key,
								'value' => $item_meta->value,
								'id'    => $item_meta->id
							);
						}
					}

					// Create a clone to ensure item totals will not be modified permanently.
					$item = clone $item;

					// Delete meta without 'id' prop.
					$cloned_item_meta_data = $item->get_meta_data();

					foreach ( $cloned_item_meta_data as $cloned_item_meta ) {
						$item->delete_meta_data( $cloned_item_meta->key );
					}

					// Copy back meta with 'id' prop intact.
					$item->set_meta_data( $item_meta_data_to_set );

					// Set props.
					$item->set_props( $item_totals );
				}
			}

			$return_items[ $item_id ] = $item;
		}

		// End of my awesome infinite looping prevention mechanism.
		self::$override_order_items_filter = $override_order_items_filter;

		// Undo 'WC_Mix_and_Match_Order::get_product_from_item' short circuit.
		self::$override_product_from_item_filter = $override_product_from_item_filter;

		return $return_items;
	}

	/**
	 * Modifies parent/child order products in order to reconstruct an accurate representation of a bundle for shipping purposes:
	 *
	 * - If it's a container, then its weight is modified to include the weight of "packaged" children.
	 * - If a child is "packaged" inside its parent, then it is marked as virtual.
	 *
	 * Used in combination with 'get_order_items', right above.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $item
	 * @param  WC_Order    $order
	 * @return WC_Product
	 */
	public function get_product_from_item( $product, $item, $order = false ) {

		if ( ! $product ) {
			return $product;
		}

		// If short circuited, return the unmodified value.
		if ( self::$override_product_from_item_filter ) {
			return $product;
		}

		// Nobody likes infinite loops.
		$override_product_from_item_filter       = self::$override_product_from_item_filter;
		self::$override_product_from_item_filter = true;

		// We have no need for this here.
		$override_order_items_filter       = self::$override_order_items_filter;
		self::$override_order_items_filter = true;

		// If it's a container item...
		if ( wc_mnm_is_container_order_item( $item ) ) {

			if ( $product->needs_shipping() ) {

				// If it needs shipping, modify its weight to include the weight of all "packaged" items.
				if ( $bundle_weight = $item->get_meta( '_bundle_weight', true ) ) {
					$product->set_weight( $bundle_weight );
				}

				// Override SKU with kit/bundle SKU if needed.
				$child_items         = wc_mnm_get_child_order_items( $item, $order );
				$packaged_products   = array();
				$packaged_quantities = array();

				// Find items shipped in the container:
				foreach ( $child_items as $child_item ) {

					if ( 'no' === $child_item->get_meta( '_mnm_item_needs_shipping', true ) ) {

						$child_variation_id = $child_item->get_variation_id();
						$child_product_id   = $child_item->get_product_id();
						$child_id           = $child_variation_id ? $child_variation_id : $child_product_id;

						$child_product = wc_get_product( $child_id );

						if ( ! $child_product || ! $child_product->needs_shipping() ) {
							continue;
						}

						$packaged_products[]              = $child_product;
						$packaged_quantities[ $child_id ] = $child_item->get_quantity();
					}
				}

				$sku     = $product->get_sku( 'edit' );

				/**
				 * Get SKU from order item.
				 *
	 			 * @param  str 				  $sku
	 			 * @param  obj WC_Product 	  $product
	 			 * @param  obj WC_Order_Item  $item
	 			 * @param  obj WC_Order 	  $order
	 			 * @param  array        	  $packaged_products
	 			 * @param  array        	  $packaged_quantities
				 */	
				$new_sku = apply_filters( 'woocommerce_mnm_sku_from_order_item', $sku, $product, $item, $order, $packaged_products, $packaged_quantities );

				if ( $sku !== $new_sku ) {
					$product->set_sku( $new_sku );
				}
			}

			// If it's a child item...
		} elseif ( wc_mnm_is_child_order_item( $item, $order ) ) {

			if ( $product->needs_shipping() ) {

				// If it's "packaged" in its container, set it to virtual.
				if ( 'no' === $item->get_meta( '_mnm_item_needs_shipping', true ) ) {
					$product->set_virtual( 'yes' );
					$product->set_weight( '' );
				}

			} else {
				$product->set_weight( 0 );
			}
		}

		// End of my awesome infinite looping prevention mechanism.
		self::$override_product_from_item_filter = $override_product_from_item_filter;

		// Undo 'WC_MNM_Order::get_order_items' short circuit.
		self::$override_order_items_filter = $override_order_items_filter;

		return $product;
	}


	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/


	/**
	 * Modify the subtotal of order-items (order-details.php) depending on the bundles's pricing strategy.
	 *
	 * @param  string   $subtotal   the item subtotal
	 * @param  array    $item       the items
	 * @param  WC_Order $order      the order
	 * @return string               modified subtotal string.
	 */
	public function order_item_subtotal( $subtotal, $item, $order ) {

		// If it's a child item...
		if ( $parent_item = wc_mnm_get_order_item_container( $item, $order ) ) {

			$per_product_pricing = $parent_item->get_meta( '_per_product_pricing', true );

			if ( $per_product_pricing === 'no' ) {
				return '';
			} else {

				if ( function_exists( 'is_account_page' ) && is_account_page() || function_exists( 'is_checkout' ) && is_checkout() ) {
					$wrap_start = '';
					$wrap_end   = '';
				} else {
					$wrap_start = '<small>';
					$wrap_end   = '</small>';
				}

				$subtotal_desc = __( 'Item subtotal', 'woocommerce-mix-and-match-products' ) . ': ';
				$subtotal      = $wrap_start . $subtotal_desc . $subtotal . $wrap_end;
			}
		}

		// If it's a container...
		if ( wc_mnm_is_container_order_item( $item ) ) {

			// Create a clone to ensure item totals will not be modified permanently.
			$item = clone $item;

			if ( ! isset( $item->child_subtotals_added ) ) {

				$children = wc_mnm_get_child_order_items( $item, $order );

				if ( ! empty( $children ) ) {

					foreach ( $children as $child ) {
						$item->set_subtotal( $item->get_subtotal( 'edit' ) + $child->get_subtotal( 'edit' ) );
						$item->set_subtotal_tax( $item->get_subtotal_tax( 'edit' ) + $child->get_subtotal_tax( 'edit' ) );
					}

					$item->child_subtotals_added = 'yes';

					$subtotal = $order->get_formatted_line_subtotal( $item );
				}
			}
		}

		return $subtotal;
	}


	/**
	 * Filters the reported number of order items.
	 * Do not count child items.
	 *
	 * @param  int          $count      initial reported count
	 * @param  string       $type       line item type
	 * @param  WC_Order     $order      the order
	 * @return int                      modified count
	 */
	public function order_item_count( $count, $type, $order ) {

		$subtract = 0;

		foreach ( $order->get_items( 'line_item' ) as $item ) {
			// If it's a child item...
			if ( wc_mnm_is_child_order_item( $item, $order ) ) {
				$subtract += $item->get_quantity();
			}
		}

		$new_count = $count - $subtract;

		return $new_count;
	}


	/**
	 * Filters the order item admin class.
	 *
	 * @param  string   $class     class
	 * @param  array    $item      the order item
	 * @param  WC_Order  $order
	 * @return string              modified class
	 */
	public function html_order_item_class( $class, $item, $order ) {

		// If it is a mnm container...
		if ( wc_mnm_is_container_order_item( $item ) ) {
			$class .= ' mnm_table_container';
		}

		// If it's a child item...
		if ( $container_item = wc_mnm_get_order_item_container( $item, $order ) ) {
			$class .= ' mnm_table_item';

			$child_item_ids = wc_mnm_get_child_order_items( $container_item, $order, true );

			if ( end( $child_item_ids ) === $item->get_id() ) {
				$class .= ' last';
			}
		}

		return $class;
	}


	/**
	 * MnM Bundle Containers need no processing - let it be decided by child items only.
	 *
	 * @param  bool      $is_needed   product needs processing: true/false
	 * @param  WC_Product   $product     the product
	 * @param  int          $order_id    the order id
	 * @return bool                   modified product needs processing status
	 */
	public function container_item_needs_processing( $is_needed, $product, $order_id ) {

		if ( $product->is_type( 'mix-and-match' ) && isset( $product->bundle_needs_processing ) && 'no' === $product->bundle_needs_processing ) {
			$is_needed = false;
		}

		return $is_needed;
	}


	/**
	 * Hides bundle metadata.
	 *
	 * @param  array    $hidden     hidden meta strings
	 * @return array                modified hidden meta strings
	 */
	public function hidden_order_item_meta( $hidden ) {
		return array_merge( $hidden, array( '_mnm_config', '_mnm_container', '_per_product_pricing', '_per_product_shipping', '_mnm_cart_key', '_bundled_shipping', '_bundled_weight', '_mnm_item_needs_shipping', '_bundle_weight', '_mnm_items_need_processing' ) );
	}


	/**
	 * Add MnM bundle info meta to order items.
	 *
	 * @param  WC_Order_Item  $order_item
	 * @param  string         $cart_item_key
	 * @param  array          $cart_item_values
	 */
	public function add_order_item_meta( $order_item, $cart_item_key, $cart_item_values ) {

		// Add metadata to child items.
		if ( wc_mnm_is_child_cart_item( $cart_item_values ) ) {

			$order_item->add_meta_data( '_mnm_container', $cart_item_values[ 'mnm_container' ], true );

			// Store shipping data.
			$needs_shipping = $cart_item_values[ 'data' ]->needs_shipping();
			$order_item->add_meta_data( '_mnm_item_needs_shipping', wc_bool_to_string( $needs_shipping, true ) );

			if ( $container = wc_mnm_get_cart_item_container( $cart_item_values ) ) {

				$part_of_meta_name = '';

				/*
				 * Version 1.5.0 stops saving this string by default, set filter to true to continue saving/displaying it.
				 */
				if ( apply_filters( 'woocommerce_mnm_order_item_legacy_part_of_meta', false, $order_item ) ) {
					// Use "Purchased with" to imply that item is physically shipped separately from its container.
					// Use "Part of" to imply that item is physically assembled or packaged in its container.
					$part_of_meta_name = $needs_shipping ? 'mnm_purchased_with' : 'mnm_part_of';

				}

				if ( has_filter( 'woocommerce_mnm_order_item_part_of_meta_name' ) ) {

					$msg = __( 'woocommerce_mnm_order_item_part_of_meta_name filter is deprecated. You should filter the meta label via woocommerce_order_item_display_meta_key instead.', 'woocommerce-mix-and-match-products' );
					wc_doing_it_wrong( 'woocommerce_mnm_order_item_part_of_meta_name', $msg, '1.10.0' );

					/**
					 * "Part Of" Meta Name.
					 *
					 * @param  string   	  $part_of_meta_name
					 * @param  array          $cart_item_values
		 			 * @param  string         $cart_item_key
					 */	
					$part_of_meta_name = apply_filters( 'woocommerce_mnm_order_item_part_of_meta_name', $part_of_meta_name, $cart_item_values, $cart_item_key );

				}

				if ( $part_of_meta_name ) {
					$order_item->add_meta_data( $part_of_meta_name, $container[ 'data' ]->get_title(), true );
				}
			}

			/**
			 * 'woocommerce_mnm_item_add_order_item_meta' hook.
			 *
			 * Allows changes to the child order item object before it is saved.
			 *
			 * @param  array          $cart_item_values
 			 * @param  string         $cart_item_key
 			 * @param  int $item_id
 			 * @param  obj WC_Order_Item $order_item
			 */	
			do_action( 'woocommerce_mnm_item_add_order_item_meta', $cart_item_values, $cart_item_key, $order_item->get_id(), $order_item );
		}

		// Add data to the container item.
		if ( wc_mnm_is_container_cart_item( $cart_item_values ) && $cart_item_values[ 'data' ]->is_type( 'mix-and-match' ) ) {

			/**
			 * "Container Size" meta data.
			 *
			 * Allows changes to the order item object before it is saved.
			 *
			 * @param  str          $container_size_meta_value
 			 * @param  int 			$item_id
			 * @param  array        $cart_item_values
 			 * @param  string       $cart_item_key
			 */
			$container_size_meta_value = array_sum( wp_list_pluck( $cart_item_values[ 'mnm_config' ], 'quantity' ) );

			$container_size_meta_value = apply_filters( 'woocommerce_mnm_order_item_container_size_meta_value', $container_size_meta_value, $order_item->get_id(), $cart_item_values, $cart_item_key );

			if ( $container_size_meta_value ) {
				$order_item->add_meta_data( 'mnm_container_size', $container_size_meta_value, true );
			}

			$order_item->add_meta_data( '_mnm_config', $cart_item_values[ 'mnm_config' ], true );
			$order_item->add_meta_data( '_mnm_cart_key', $cart_item_key, true );

			$order_item->add_meta_data( '_per_product_pricing', wc_bool_to_string( $cart_item_values[ 'data' ]->is_priced_per_product() ), true );

			// Store shipping data.
			// If it's a physical container item, grab its aggregate weight from the package data.
			if ( $cart_item_values[ 'data' ]->needs_shipping() ) {

				$packaged_item_values = false;

				foreach ( WC()->cart->get_shipping_packages() as $package ) {
					if ( isset( $package[ 'contents' ][ $cart_item_key ] ) ) {
						$packaged_item_values = $package[ 'contents' ][ $cart_item_key ];
						break;
					}
				}

				if ( ! empty( $packaged_item_values ) ) {
					$aggregate_weight = $packaged_item_values[ 'data' ]->get_weight();
					$order_item->add_meta_data( '_bundle_weight', $aggregate_weight, true );
				}

				// If it's a virtual container item, look at its children to see if any of them needs processing.
			} elseif ( false === $this->mnm_items_need_processing( $cart_item_values ) ) {
				$order_item->add_meta_data( '_mnm_items_need_processing', 'no', true );
			}

			/**
			 * 'woocommerce_mnm_container_add_order_item_meta' hook.
			 *
			 * Allows changes to the container's order item object before it is saved.
			 *
			 * @param  array          $cart_item_values
 			 * @param  string         $cart_item_key
 			 * @param  int $item_id
 			 * @param  obj WC_Order_Item $order_item
			 */	
			do_action( 'woocommerce_mnm_container_add_order_item_meta', $cart_item_values, $cart_item_key, $order_item->get_id(), $order_item );
		}
	}


	/**
	 * Given a virtual MnM container cart item, find if any of its children need processing.
	 *
	 * @since  1.2.0
	 *
	 * @param  array  $item_values
	 * @return mixed
	 */
	private function mnm_items_need_processing( $item_values ) {

		$child_keys        = wc_mnm_get_child_cart_items( $item_values, WC()->cart->cart_contents, true, true );
		$processing_needed = false;

		if ( ! empty( $child_keys ) && is_array( $child_keys ) ) {
			foreach ( $child_keys as $child_key ) {
				$child_product = WC()->cart->cart_contents[ $child_key ][ 'data' ];
				if ( false === $child_product->is_downloadable() || false === $child_product->is_virtual() ) {
					$processing_needed = true;
					break;
				}
			}
		}

		return $processing_needed;
	}


	/**
	 * Activates the 'get_product_from_item' filter below.
	 *
	 * @param  string  $order_id
	 */
	public function apply_order_item_product_filter( $order_id ) {
		add_filter( 'woocommerce_order_item_product', array( $this, 'get_processing_order_item_product' ), 10, 2 );
	}


	/**
	 * Deactivates the 'get_product_from_item' filter below.
	 *
	 * @param  string  $order_id
	 */
	public function remove_get_product_from_item_filter( $order_id ) {
		remove_filter( 'woocommerce_order_item_product', array( $this, 'get_processing_order_item_product' ), 10, 2 );
	}


	/**
	 * Filters 'get_product_from_item' to add data used for 'woocommerce_order_item_needs_processing'.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $item
	 * @param  WC_Order    $order
	 * @return WC_Product
	 */
	public function get_processing_order_item_product( $product, $item ) {

		if ( ! empty( $product ) && $product->is_virtual() ) {

			// Process container.
			if ( $child_items = wc_mnm_get_child_order_items( $item ) ) {

				// If no child requires processing and the container is virtual, it should not require processing - @see 'container_item_needs_processing()'.
				if ( sizeof( $child_items ) > 0 ) {
					if ( 'no' === $item->get_meta( '_mnm_items_need_processing', true ) ) {
						$product->bundle_needs_processing = 'no';
					}
				}
			}
		}

		return $product;
	}


	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	public function get_bundled_order_item_container( $item, $order ) {
		_deprecated_function( __METHOD__ . '()', '1.2.0', 'wc_mnm_get_order_item_container()' );
		return wc_mnm_get_order_item_container( $item, $order );
	}
}
