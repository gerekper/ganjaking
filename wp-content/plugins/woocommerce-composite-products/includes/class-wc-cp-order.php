<?php
/**
 * WC_CP_Order class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    2.2.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Composite order-related filters and functions.
 *
 * @class    WC_CP_Order
 * @version  7.0.6
 */
class WC_CP_Order {

	/**
	 * Flag to short-circuit 'WC_CP_Order::get_order_items'.
	 * @var boolean
	 */
	public static $override_order_items_filter = false;

	/**
	 * Flag to short-circuit 'WC_CP_Order::get_product_from_item'.
	 *
	 * @var boolean
	 */
	public static $override_product_from_item_filter = false;

	/**
	 * The single instance of the class.
	 * @var WC_CP_Order
	 *
	 * @since 3.7.0
	 */
	protected static $_instance = null;

	/**
	 * Main WC_CP_Order instance.
	 *
	 * Ensures only one instance of WC_CP_Order is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_CP_Order
	 * @since  3.7.0
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 3.7.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-composite-products' ), '3.7.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 3.7.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'woocommerce-composite-products' ), '3.7.0' );
	}

	/**
	 * Construct, man.
	 */
	public function __construct() {

		// Virtual composite containers should not affect order status unless one of their children does.
		add_filter( 'woocommerce_order_item_needs_processing', array( $this, 'container_item_needs_processing' ), 10, 3 );

		// Modify order items to include composite meta.
		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'add_order_item_meta' ), 10, 3 );

		// Hide composite configuration metadata in order line items.
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_meta' ) );

		// Filter admin dashboard item count and classes.
		if ( is_admin() ) {
			add_filter( 'woocommerce_admin_html_order_item_class', array( $this, 'html_order_item_class' ), 10, 3 );
			add_filter( 'woocommerce_admin_html_order_preview_item_class', array( $this, 'html_order_item_class' ), 10, 3 );
		}

		// Modify product while completing payment - @see 'get_processing_order_item_product()' and 'container_item_needs_processing()'.
		add_action( 'woocommerce_pre_payment_complete', array( $this, 'apply_order_item_product_filter' ) );
		add_action( 'woocommerce_payment_complete', array( $this, 'remove_order_item_product_filter' ) );
	}

	/*
	|--------------------------------------------------------------------------
	| API functions.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Reads the configuration array stored on a container order item and modifies it based on the actual state of the composite.
	 *
	 * @since  3.14.0
	 *
	 * @param  WC_Order_Item  $order_item
	 * @param  WC_Order       $order
	 * @return array
	 */
	public static function get_current_composite_configuration( $order_item, $order ) {

		if ( ! wc_cp_is_composite_container_order_item( $order_item ) ) {
			return false;
		}

		$composite     = wc_get_product( $order_item->get_product_id() );
		$configuration = $order_item->get_meta( '_composite_data', true );
		$child_items   = wc_cp_get_composited_order_items( $order_item, $order );

		/*
		 * There's no chance something might have been added to the stored configuration.
		 * However, admins may have deleted items or modified their quantities.
		 */
		foreach ( $configuration as $component_id => $component_configuration ) {

			if ( empty( $component_configuration[ 'product_id' ] ) ) {
				continue;
			}

			$child_item_qty = false;

			foreach ( $child_items as $child_item ) {
				if ( $component_id == $child_item->get_meta( '_composite_item', true ) ) {
					$child_item_qty = $child_item->get_quantity();
					break;
				}
			}

			if ( ! $child_item_qty ) {
				unset( $configuration[ $component_id ] );
				continue;
			}

			// Normalize with the quantity of the parent.
			$configuration[ $component_id ][ 'quantity' ] = $child_item_qty / $order_item->get_quantity();
		}

		// Finally, parse the configuration to add data for any new components.
		return $composite ? WC_CP()->cart->parse_composite_configuration( $composite, $configuration ) : $configuration;
	}

	/**
	 * Validates a composite configuration and adds all associated line items to an order. Relies on specifying a composite configuration array with all necessary data.
	 * The configuration array is passed as a 'configuration' key of the $args method argument. Example:
	 *
	 *    $args = array(
	 *        'configuration' => array(
	 *            134567890 => array(                       // ID of the component.
	 *                'quantity'          => 2,             // Qty of composited product, will fall back to min.
	 *                'discount'          => 50.0,          // Composited product discount, defaults to the defined value.
	 *                'attributes'        => array(         // Array of selected variation attribute names, sanitized.
	 *                    'attribute_color' => 'black',
	 *                    'attribute_size'  => 'medium'
	 *                 ),
	 *                'variation_id'      => 43,            // ID of chosen variation, if applicable.
	 *                'args'              => array()        // Custom composited item args to pass into 'WC_Order::add_product()'.
	 *            )
	 *        )
	 *    );
	 *
	 * Returns the container order item ID if sucessful, or false otherwise.
	 *
	 * Note: Container/child order item totals are calculated without taxes, based on their pricing setup.
	 * - Container item totals can be overridden by passing a 'totals' array in $args, as with 'WC_Order::add_product()'.
	 * - Composited item totals can be overridden in the 'configuration' array, as shown in the example above.
	 *
	 *
	 * @param  WC_Product_Composite  $composite
	 * @param  WC_Order              $order
	 * @param  integer               $quantity
	 * @param  array                 $args
	 * @return integer|WP_Error
	 */
	public function add_composite_to_order( $composite, $order, $quantity = 1, $args = array() ) {

		$added_to_order = false;

		$args = wp_parse_args( $args, array(
			'configuration' => array(),
			'silent'        => true
		) );

		if ( $composite && 'composite' === $composite->get_type() ) {

			$configuration = $args[ 'configuration' ];

			if ( WC_CP()->cart->validate_composite_configuration( $composite, $quantity, $configuration, 'add-to-order' ) ) {

				// Add container item.
				$container_order_item_id = $order->add_product( $composite, $quantity, $args );
				$added_to_order          = $container_order_item_id;

				// Unique hash to use in place of the cart item ID.
				$container_item_hash = md5( $container_order_item_id );

				// Add components.
				$components = $composite->get_components();

				// Hashes of children.
				$component_hashes = array();

				$bundled_weight = 0.0;

				if ( ! empty( $components ) ) {
					foreach ( $components as $component_id => $component ) {

						$component_configuration         = isset( $configuration[ $component_id ] ) ? $configuration[ $component_id ] : array();
						$component_quantity              = isset( $component_configuration[ 'quantity' ] ) ? absint( $component_configuration[ 'quantity' ] ) : $component->get_quantity();
						$component_option_id             = isset( $component_configuration[ 'product_id' ] ) ? $component_configuration[ 'product_id' ] : '';
						$component_option_product_id     = isset( $component_configuration[ 'variation_id' ] ) ? $component_configuration[ 'variation_id' ] : $component_configuration[ 'product_id' ];
						$component_option_variation_data = isset( $component_configuration[ 'attributes' ] ) ? $component_configuration[ 'attributes' ] : array();
						$component_discount              = isset( $component_configuration[ 'discount' ] ) ? wc_format_decimal( $component_configuration[ 'discount' ] ) : $component->get_discount();
						$component_args                  = isset( $component_configuration[ 'args' ] ) ? $component_configuration[ 'args' ] : array();

						if ( '' === $component_option_id ) {
							$component_quantity = 0;
						}

						if ( 0 === $component_quantity ) {
							continue;
						}

						$component_option              = $component->get_option( $component_option_id );
						$component_option_product_type = $component_option->get_product()->get_type();
						$component_option_product      = 'variable' === $component_option_product_type ? wc_get_product( $component_option_product_id ) : $component_option->get_product();

						if ( $component_option->is_priced_individually() ) {
							if ( $component_discount ) {
								$component_args[ 'subtotal' ]     = isset( $component_args[ 'subtotal' ] ) ? $component_args[ 'subtotal' ] : wc_get_price_excluding_tax( $component_option_product, array( 'qty' => $component_quantity * $quantity, 'price' => $component_option->is_discount_allowed_on_sale_price() ? $component_option_product->get_price() : $component_option_product->get_regular_price() ) ) * ( 1 - (float) $component_discount / 100 );
								$component_args[ 'total' ]        = isset( $component_args[ 'total' ] ) ? $component_args[ 'total' ] : wc_get_price_excluding_tax( $component_option_product, array( 'qty' => $component_quantity * $quantity, 'price' => $component_option->is_discount_allowed_on_sale_price() ? $component_option_product->get_price() : $component_option_product->get_regular_price() ) ) * ( 1 - (float) $component_discount / 100 );
								$component_args[ 'subtotal_tax' ] = isset( $component_args[ 'subtotal_tax' ] ) ? $component_args[ 'subtotal_tax' ] : 0;
								$component_args[ 'total_tax' ]    = isset( $component_args[ 'total_tax' ] ) ? $component_args[ 'total_tax' ] : 0;
							}
						} else {
							$component_args[ 'subtotal' ]     = isset( $component_args[ 'subtotal' ] ) ? $component_args[ 'subtotal' ] : 0;
							$component_args[ 'total' ]        = isset( $component_args[ 'total' ] ) ? $component_args[ 'total' ] : 0;
							$component_args[ 'subtotal_tax' ] = isset( $component_args[ 'subtotal_tax' ] ) ? $component_args[ 'subtotal_tax' ] : 0;
							$component_args[ 'total_tax' ]    = isset( $component_args[ 'total_tax' ] ) ? $component_args[ 'total_tax' ] : 0;
						}

						// Args to pass into 'add_product()'.
						$component_args[ 'variation' ] = 'variable' === $component_option_product_type ? $component_option_variation_data : array();

						/**
						 * Custom callback for adding child items.
						 *
						 * @since  3.14.0
						 *
						 * @param  array|false|null      $callback
						 * @param  WC_CP_Component       $component
						 * @param  WC_Product_Composite  $composite
						 * @param  WC_Order              $order
						 * @param  integer               $quantity
						 * @param  array                 $args
						 */
						$add_component_to_order_callback = apply_filters( 'woocommerce_add_component_to_order_callback', false, $component, $composite, $order, $quantity, $args );

						// Do not add item.
						if ( is_null( $add_component_to_order_callback ) ) {
							continue;
						// Use custom callback to add item.
						} if ( is_callable( $add_component_to_order_callback ) ) {
							$component_order_item_id = call_user_func_array( $add_component_to_order_callback, array( $component, $composite, $order, $quantity, $args ) );
						// Use standard method.
						} else {
							$component_order_item_id = $order->add_product( $component_option_product, $component_quantity * $quantity, $component_args );
						}

						if ( ! $component_order_item_id || is_wp_error( $component_order_item_id ) ) {
							continue;
						}

						// Locate the item.
						$order_items          = $order->get_items( 'line_item' );
						$component_order_item = $order_items[ $component_order_item_id ];

						/*
						 * Add bundled order item meta.
						 */

						$component_order_item->add_meta_data( '_composite_parent', $container_item_hash, true );
						$component_order_item->add_meta_data( '_composite_data', $configuration, true );
						$component_order_item->add_meta_data( '_composite_item', $component_id, true );

						if ( false === $component->is_subtotal_visible( 'orders' ) ) {
							$component_order_item->add_meta_data( '_component_subtotal_hidden', 'yes', true );
						}

						// Pricing setup.
						$component_order_item->add_meta_data( '_component_priced_individually', $component_option->is_priced_individually() ? 'yes' : 'no', true );

						// Unique hash to use in place of the cart item ID.
						$component_hash     = md5( $component_order_item_id );
						$component_hashes[] = $component_hash;

						$component_order_item->add_meta_data( '_composite_cart_key', $component_hash, true );

						// Shipping setup.
						$shipped_individually = false;

						if ( $component_option_product->needs_shipping() && $component_option->is_shipped_individually( $component_option_product ) ) {
							$shipped_individually = true;
						} elseif ( $component_option_product->needs_shipping() && $component_option->is_weight_aggregated( $component_option_product ) ) {
							$bundled_weight += (double) $component_option_product->get_weight( 'edit' ) * $component_quantity;
						}

						$component_order_item->add_meta_data( '_composite_item_needs_shipping', $shipped_individually ? 'yes' : 'no', true );

						// Save the item.
						$component_order_item->save();

						/**
						 * 'woocommerce_composite_component_add_to_order' action.
						 *
						 * @param  int                   $component_order_item_id
						 * @param  WC_Order              $order
						 * @param  WC_Product            $component_option_product
						 * @param  int                   $composited_item_quantity
						 * @param  WC_CP_Component       $component
						 * @param  WC_Composite_Product  $composite
						 * @param  int                   $quantity
						 * @param  array                 $component_args
						 * @param  array                 $args
						 */
						do_action( 'woocommerce_composite_component_add_to_order', $component_order_item_id, $order, $component_option_product, $component_quantity, $component, $composite, $quantity, $component_args, $args );
					}
				}

				// Locate the item.
				$order_items          = $order->get_items( 'line_item' );
				$container_order_item = $order_items[ $container_order_item_id ];

				/*
				 * Add container order item meta.
				 */

				$container_order_item->add_meta_data( '_composite_data', $configuration, true );
				$container_order_item->add_meta_data( '_composite_children', $component_hashes, true );
				$container_order_item->add_meta_data( '_composite_cart_key', $container_item_hash, true );

				if ( $composite->needs_shipping() ) {
					$container_order_item->add_meta_data( '_composite_weight', (double) $composite->get_weight( 'edit' ) + $bundled_weight, true );
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

						if ( in_array( $meta[ 'key' ], array( '_composite_configuration', '_composite_data', '_composite_children', '_composite_cart_key', '_composite_weight' ) ) ) {
							continue;
						}

						$container_order_item->add_meta_data( $meta[ 'key' ], $meta[ 'value' ] );
					}
				}

				// Save the item.
				$container_order_item->save();

				/**
				 * 'woocommerce_composite_added_to_order' action.
				 *
				 * @since  4.1.0
				 *
				 * @param  WC_Order_Item         $composited_order_item
				 * @param  WC_Order              $order
				 * @param  WC_Composite_Product  $composite
				 * @param  int                   $quantity
				 * @param  array                 $args
				 */
				do_action( 'woocommerce_composite_added_to_order', $container_order_item, $order, $composite, $quantity, $args );

			} else {

				$error_data = array( 'notices' => wc_get_notices( 'error' ) );
				$message    = __( 'The submitted composite configuration could not be added to this order.', 'woocommerce-composite-products' );

				if ( $args[ 'silent' ] ) {
					wc_clear_notices();
				}

				$added_to_order = new WP_Error( 'woocommerce_composite_configuration_invalid', $message, $error_data );
			}

		} else {
			$message        = __( 'A composite with this ID does not exist.', 'woocommerce-composite-products' );
			$added_to_order = new WP_Error( 'woocommerce_composite_invalid', $message );
		}

		return $added_to_order;
	}

	/**
	 * Modifies composite parent/child order items depending on their shipping setup. Reconstructs an accurate representation of a composite for shipping purposes.
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

			if ( wc_cp_is_composite_container_order_item( $item ) ) {

				/*
				 * Add the totals of "packaged" items to the container totals and create a container "Contents" meta field to provide a description of the included products.
				 */

				$product   = wc_get_product( $item->get_product_id() );
				$aggregate = true;

				if ( ! $product ) {
					$aggregate = false;
				}

				if ( $product && ! $product->needs_shipping() ) {
					$aggregate = false;
				}

				// Get all items physically assembled in this container.
				$assembled_items = $this->get_assembled_items( $item, $items );

				if ( empty( $assembled_items ) ) {
					$aggregate = false;
				}

				if ( $aggregate ) {

					// Aggregate contents.
					$contents = array();

					// Aggregate prices.
					$bundle_totals = array(
						'subtotal'     => $item->get_subtotal(),
						'total'        => $item->get_total(),
						'subtotal_tax' => $item->get_subtotal_tax(),
						'total_tax'    => $item->get_total_tax(),
						'taxes'        => $item->get_taxes()
					);

					foreach ( $assembled_items as $child_item_id => $child_item ) {

						$child_item_id      = $child_item->get_id();
						$child_variation_id = $child_item->get_variation_id();
						$child_product_id   = $child_item->get_product_id();
						$child_id           = $child_variation_id ? $child_variation_id : $child_product_id;
						$child              = WC_CP_Helpers::cache_get( 'order_item_product_' . $child_item_id );

						if ( null === $child ) {
							$child = wc_get_product( $child_id );
							WC_CP_Helpers::cache_set( 'order_item_product_' . $child_item_id, $child );
						}

						/*
						 * Add item into a new container "Contents" meta.
						 */

						$meta_data               = WC_CP_Core_Compatibility::is_wc_version_gte( '3.1' ) ? $child_item->get_formatted_meta_data( '_', true ) : $child_item->get_formatted_meta_data();
						$meta_desc_array         = array();
						$bundled_meta_desc_array = array();

						if ( ! empty( $meta_data ) ) {

							foreach ( $meta_data as $meta_id => $meta ) {
								$meta_desc_array[] = array(
									'key'   => wp_kses_post( $meta->display_key ),
									'value' => wp_kses_post( trim( strip_tags( $meta->display_value ) ) )
								);
							}
						}

						if ( $child->is_type( 'bundle' ) ) {
							$bundled_meta_desc_array = $meta_desc_array;
							$meta_desc_array         = array();
						}

						$meta_desc_array[] = array(
							'key'   => _x( 'Qty', 'component order item qty meta key', 'woocommerce-composite-products' ),
							'value' => $child_item->get_quantity()
						);

						if ( $sku = $child->get_sku() ) {
							$meta_desc_array[] = array(
								'key'   => _x( 'SKU', 'component order item SKU meta key', 'woocommerce-composite-products' ),
								'value' => $sku
							);
						}

						foreach ( $meta_desc_array as $meta_desc_array_key => $meta_desc_array_value ) {
							$meta_desc_array[ $meta_desc_array_key ][ 'description' ] = $meta_desc_array_value[ 'key' ] . ' - ' . $meta_desc_array_value[ 'value' ];
						}

						$title      = $child->get_title();
						$meta_title = $title;
						$meta_desc  = implode( ', ', wp_list_pluck( $meta_desc_array, 'description' ) );

						if ( $component_id = $child_item->get_meta( '_composite_item', true ) && $product->is_type( 'composite' ) ) {
							if ( $component = $product->get_component( $component_id ) ) {
								$meta_desc  = $title . ', ' . $meta_desc;
								$meta_title = $component->get_title();
							}
						}

						$meta_desc = apply_filters( 'woocommerce_component_order_item_meta_description', $meta_desc, $meta_desc_array, $child_item, $item, $order );

						if ( $meta_desc ) {
							$contents[] = array(
								'title'       => apply_filters( 'woocommerce_component_order_item_meta_title', $meta_title, $meta_desc_array, $child_item, $item, $order ),
								'description' => $meta_desc
							);
						}

						if ( ! empty( $bundled_meta_desc_array ) ) {

							foreach ( $bundled_meta_desc_array as $bundled_meta_desc_array_key => $bundled_meta_desc_array_value ) {
								$bundled_meta_desc_array[ $bundled_meta_desc_array_key ][ 'description' ] = $bundled_meta_desc_array_value[ 'key' ] . ', ' . $bundled_meta_desc_array_value[ 'value' ];
							}

							$contents[] = array(
								'title'       => apply_filters( 'woocommerce_component_bundled_order_item_meta_title', $meta_title, $bundled_meta_desc_array, $child_item, $item, $order ),
								'description' => apply_filters( 'woocommerce_component_bundled_order_item_meta_description', implode( ', ', wp_list_pluck( $bundled_meta_desc_array, 'description' ) ), $bundled_meta_desc_array, $child_item, $item, $order )
							);
						}

						/*
						 * Add item totals to the container totals.
						 */

						$bundle_totals[ 'subtotal' ]     += $child_item->get_subtotal();
						$bundle_totals[ 'total' ]        += $child_item->get_total();
						$bundle_totals[ 'subtotal_tax' ] += $child_item->get_subtotal_tax();
						$bundle_totals[ 'total_tax' ]    += $child_item->get_total_tax();

						$child_item_tax_data = $child_item->get_taxes();

						$bundle_totals[ 'taxes' ][ 'total' ]    = array_merge( $bundle_totals[ 'taxes' ][ 'total' ], $child_item_tax_data[ 'total' ] );
						$bundle_totals[ 'taxes' ][ 'subtotal' ] = array_merge( $bundle_totals[ 'taxes' ][ 'subtotal' ], $child_item_tax_data[ 'subtotal' ] );
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

					$item->set_props( $bundle_totals );

					// Create a meta field with product details for each component.
					if ( ! empty( $contents ) ) {
						$added_keys = array();
						// Create a meta field for each bundled item.
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

			} elseif ( wc_cp_is_composited_order_item( $item, $items ) ) {

				$item_id      = $item->get_id();
				$variation_id = $item->get_variation_id();
				$product_id   = $item->get_product_id();
				$id           = $variation_id ? $variation_id : $product_id;
				$product      = WC_CP_Helpers::cache_get( 'order_item_product_' . $item_id );

				if ( null === $product ) {
					$product = wc_get_product( $id );
					WC_CP_Helpers::cache_set( 'order_item_product_' . $item_id, $product );
				}

				if ( $product && $product->needs_shipping() && 'no' === $item->get_meta( '_composite_item_needs_shipping', true ) ) {

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

		// Undo 'WC_CP_Order::get_product_from_item' short circuit.
		self::$override_product_from_item_filter = $override_product_from_item_filter;

		return $return_items;
	}


	/**
	 * Modifies parent/child order products in order to reconstruct an accurate representation of a composite for shipping purposes:
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
		if ( wc_cp_is_composite_container_order_item( $item ) ) {

			if ( $product->needs_shipping() ) {

				// If it needs shipping, modify its weight to include the weight of all "packaged" items.
				if ( $composite_weight = $item->get_meta( '_composite_weight', true ) ) {
					$product->set_weight( $composite_weight );
				}

				$assembled_items = $this->get_assembled_items( $item, $order );
				$sku             = $product->get_sku( 'edit' );

				/**
				 * Allows you to construct a dynamic SKU for the product kit depending on its contents.
				 *
				 * @since  3.14.0
				 *
				 * @param  string                $sku
				 * @param  WC_Product_Composite  $composite
				 * @param  WC_Order_Item         $item
				 * @param  array                 $assembled_items
				 * @param  WC_Order              $order
				 */
				$new_sku = apply_filters( 'woocommerce_composite_container_order_item_sku', $sku, $product, $item, $assembled_items, $order );

				if ( $sku !== $new_sku ) {
					$product->set_sku( $new_sku );
				}
			}

		// If it's a child item...
		} elseif ( wc_cp_is_composited_order_item( $item, $order ) ) {

			if ( $product->needs_shipping() ) {

				// If it's "packaged" in its container, set it to virtual.
				if ( 'no' === $item->get_meta( '_composite_item_needs_shipping', true ) ) {
					$product->set_virtual( 'yes' );
					$product->set_weight( '' );
				}
			}
		}

		// End of my awesome infinite looping prevention mechanism.
		self::$override_product_from_item_filter = $override_product_from_item_filter;

		// Undo 'WC_CP_Order::get_order_items' short circuit.
		self::$override_order_items_filter = $override_order_items_filter;

		return $product;
	}

	/**
	 * Returns a list of products/quantities physically assembled in a parent order item.
	 *
	 * @since  3.14.0
	 *
	 * @param  WC_Order_Item_Product  $item
	 * @param  WC_Order               $order
	 * @return array
	 */
	public function get_assembled_items( $item, $order ) {

		$assembled_items = WC_CP_Helpers::cache_get( 'assembled_order_items_' . $item->get_id() );

		if ( null !== $assembled_items ) {
			return $assembled_items;
		}

		// Override SKU with kit/bundle SKU if needed.
		$child_items     = wc_cp_get_composited_order_items( $item, $order );
		$assembled_items = array();

		// Find items shipped in the container:
		foreach ( $child_items as $child_item ) {

			if ( 'no' === $child_item->get_meta( '_composite_item_needs_shipping', true ) ) {

				$child_item_id      = $child_item->get_id();
				$child_variation_id = $child_item->get_variation_id();
				$child_product_id   = $child_item->get_product_id();
				$child_id           = $child_variation_id ? $child_variation_id : $child_product_id;
				$child_product      = WC_CP_Helpers::cache_get( 'order_item_product_' . $child_item_id );

				if ( null === $child_product ) {
					$child_product = wc_get_product( $child_id );
					WC_CP_Helpers::cache_set( 'order_item_product_' . $child_item_id, $child_product );
				}

				if ( ! $child_product ) {
					continue;
				}

				if ( ! $child_product->needs_shipping() && ! $child_product->is_type( 'bundle' ) ) {
					continue;
				}

				$assembled_items[] = $child_item;
			}
		}

		WC_CP_Helpers::cache_set( 'assembled_order_items_' . $item->get_id(), $assembled_items );

		return $assembled_items;
	}

	/**
	 * Modify product objects using order item meta in order to construct an accurate value/volume/weight representation of a composite for shipping purposes.
	 *
	 * Virtual containers/children are assigned a zero weight and tiny dimensions in order to maintain the value of the associated item in shipments:
	 *
	 * - If a bundled item is not shipped individually (virtual), its value must be included to ensure an accurate calculation of shipping costs (value/insurance).
	 * - If a composite is not shipped as a physical item (virtual), it may have a non-zero value that also needs to be included to ensure an accurate calculation of shipping costs (value/insurance).
	 *
	 * In both cases, the workaround is to assign a tiny weight and miniscule dimensions to the non-shipped order items, in order to:
	 *
	 * - ensure that they are included in the exported data, by having 'needs_shipping' return 'true', but also
	 * - minimize the impact of their inclusion on shipping costs.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $item
	 * @param  WC_Order    $order
	 * @return WC_Product
	 */
	public function get_legacy_shipstation_product_from_item( $product, $item, $order ) {

		// If it's a container item...
		if ( wc_cp_is_composite_container_order_item( $item ) ) {

			if ( $product->needs_shipping() ) {

				if ( $composite_weight = $item->get_meta( '_composite_weight', true ) ) {
					$product->set_weight( $composite_weight );
				}

			} else {

				// Process container.
				if ( $child_items = wc_cp_get_composited_order_items( $item, $order ) ) {

					$non_virtual_child_exists = false;

					// Virtual container converted to non-virtual with zero weight and tiny dimensions if it has non-virtual bundled children.
					foreach ( $child_items as $child_item_id => $child_item ) {
						if ( 'yes' === $child_item->get_meta( '_composite_item_needs_shipping', true ) ) {
							$non_virtual_child_exists = true;
							break;
						}
					}

					if ( $non_virtual_child_exists ) {
						$product->set_virtual( 'no' );
					}
				}

				if ( $product->get_weight() > 0 ) {
					$product->set_weight( '' );
				}
				if ( $product->length > 0 ) {
					$product->set_length( 0.001 );
				}
				if ( $product->height > 0 ) {
					$product->set_height( 0.001 );
				}
				if ( $product->width > 0 ) {
					$product->set_width( 0.001 );
				}
			}

		// If it's a child item...
		} elseif ( wc_cp_is_composited_order_item( $item, $order ) ) {

			if ( $product->needs_shipping() ) {

				// If it's "packaged" in its container, set it to virtual.
				if ( 'no' === $item->get_meta( '_composite_item_needs_shipping', true ) ) {

					if ( $product->get_weight() > 0 ) {
						$product->set_weight( '' );
					}
					if ( $product->length > 0 ) {
						$product->set_length( 0.001 );
					}
					if ( $product->height > 0 ) {
						$product->set_height( 0.001 );
					}
					if ( $product->width > 0 ) {
						$product->set_width( 0.001 );
					}
				}
			}
		}

		return $product;
	}


	/*
	|--------------------------------------------------------------------------
	| Filter hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Composite Containers should not affect order status - let it be decided by composited items only.
	 *
	 * @param  bool        $is_needed
	 * @param  WC_Product  $product
	 * @param  int         $order_id
	 * @return bool
	 */
	public function container_item_needs_processing( $is_needed, $product, $order_id ) {

		if ( $product->is_type( 'composite' ) && isset( $product->composite_needs_processing ) && 'no' === $product->composite_needs_processing ) {
			$is_needed = false;
		}

		return $is_needed;
	}

	/**
	 * Hides composite metadata.
	 *
	 * @param  array  $hidden
	 * @return array
	 */
	public function hide_order_item_meta( $hidden ) {

		$current_meta = array( '_composite_parent', '_composite_item', '_composite_children', '_composite_cart_key', '_composite_item_needs_shipping', '_composite_weight', '_component_priced_individually', '_components_need_processing', '_component_subtotal_hidden' );
		$legacy_meta  = array(  '_per_product_pricing', '_per_product_shipping', '_bundled_shipping', '_bundled_weight' );

		return array_merge( $hidden, $current_meta, $legacy_meta );
	}

	/**
	 * Adds composite info to order items.
	 *
	 * @param  WC_Order_Item  $order_item
	 * @param  string         $cart_item_key
	 * @param  array          $cart_item
	 * @return void
	 */
	public function add_order_item_meta( $order_item, $cart_item_key, $cart_item ) {

		if ( wc_cp_is_composite_container_cart_item( $cart_item ) ) {

			$order_item->add_meta_data( '_composite_children', $cart_item[ 'composite_children' ], true );

			if ( isset( $cart_item[ 'composite_data' ] ) ) {
				$order_item->add_meta_data( '_composite_data', $cart_item[ 'composite_data' ], true );
			}

			$order_item->add_meta_data( '_composite_cart_key', $cart_item_key, true );

			/*
			 * Store shipping data.
			 */

			$needs_shipping = $cart_item[ 'data' ]->needs_shipping() ? 'yes' : 'no';

			// If it's a physical container item, grab its aggregate weight from the package data.
			if ( 'yes' === $needs_shipping ) {

				$packaged_item_values = false;

				foreach ( WC()->cart->get_shipping_packages() as $package ) {
					if ( isset( $package[ 'contents' ][ $cart_item_key ] ) ) {
						$packaged_item_values = $package[ 'contents' ][ $cart_item_key ];
						break;
					}
				}

				if ( ! empty( $packaged_item_values ) ) {
					$bundled_weight = $packaged_item_values[ 'data' ]->get_weight( 'edit' );
					$order_item->add_meta_data( '_composite_weight', $bundled_weight, true );
				}

			// If it's a virtual container item, look at its children to see if any of them needs processing.
			} elseif ( false === $this->components_need_processing( $cart_item ) ) {
				$order_item->add_meta_data( '_components_need_processing', 'no', true );
			}

		} elseif ( $composite_container_item = wc_cp_get_composited_cart_item_container( $cart_item ) ) {

			$order_item->add_meta_data( '_composite_parent', $cart_item[ 'composite_parent' ], true );
			$order_item->add_meta_data( '_composite_item', $cart_item[ 'composite_item' ], true );

			$composite    = $composite_container_item[ 'data' ];
			$component_id = $cart_item[ 'composite_item' ];
			$product_id   = $cart_item[ 'product_id' ];

			if ( $component = $composite->get_component( $component_id ) ) {
				if ( false === $component->is_subtotal_visible( 'orders' ) ) {
					$order_item->add_meta_data( '_component_subtotal_hidden', 'yes', true );
				}
			}

			if ( $component_option = $composite->get_component_option( $component_id, $product_id ) ) {
				$order_item->add_meta_data( '_component_priced_individually', $component_option->is_priced_individually() ? 'yes' : 'no', true );
			}

			if ( isset( $cart_item[ 'composite_data' ] ) ) {
				$order_item->add_meta_data( '_composite_data', $cart_item[ 'composite_data' ], true );
			}

			$order_item->add_meta_data( '_composite_cart_key', $cart_item_key, true );

			/*
			 * Store shipping data.
			 */

			$order_item->add_meta_data( '_composite_item_needs_shipping', $cart_item[ 'data' ]->needs_shipping() ? 'yes' : 'no', true );
		}
	}

	/**
	 * Given a virtual composite container cart item, find if any of its children need processing.
	 *
	 * @since  3.7.0
	 *
	 * @param  array  $item_values
	 * @return mixed
	 */
	private function components_need_processing( $item_values ) {

		$child_keys        = wc_cp_get_composited_cart_items( $item_values, WC()->cart->cart_contents, true, true );
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
	 * Filters the order item admin class.
	 *
	 * @param  string    $class
	 * @param  array     $item
	 * @param  WC_Order  $order
	 * @return string
	 */
	public function html_order_item_class( $class, $item, $order ) {

		if ( $composite_container_item = wc_cp_get_composited_order_item_container( $item, $order ) ) {

			$class .= ' composited_item';

			$composited_item_ids = wc_cp_get_composited_order_items( $composite_container_item, $order, true );

			if ( end( $composited_item_ids ) === $item->get_id() ) {
				$class .= ' last';
			}

		} else if ( wc_cp_is_composite_container_order_item( $item ) ) {
			$class .= ' composite_item';
		}

		return $class;
	}

	/**
	 * Activates the 'woocommerce_order_item_product' filter below.
	 *
	 * @param  string  $order_id
	 * @return void
	 */
	public function apply_order_item_product_filter( $order_id ) {
		add_filter( 'woocommerce_order_item_product', array( $this, 'get_processing_order_item_product' ), 10, 2 );
	}

	/**
	 * Deactivates the 'woocommerce_order_item_product' filter below.
	 *
	 * @param  string  $order_id
	 * @return void
	 */
	public function remove_order_item_product_filter( $order_id ) {
		remove_filter( 'woocommerce_order_item_product', array( $this, 'get_processing_order_item_product' ), 10, 2 );
	}

	/**
	 * Filters 'woocommerce_order_item_product' to add data used by 'woocommerce_order_item_needs_processing'.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $item
	 * @return WC_Product
	 */
	public function get_processing_order_item_product( $product, $item ) {

		if ( ! empty( $product ) && $product->is_virtual() ) {

			// Process container.
			if ( $child_items = wc_cp_get_composited_order_items( $item ) ) {

				// If no child requires processing and the container is virtual, it should not require processing - @see 'container_item_needs_processing()'.
				if ( $product->is_virtual() && sizeof( $child_items ) > 0 ) {
					if ( 'no' === $item->get_meta( '_components_need_processing', true ) ) {
						$product->composite_needs_processing = 'no';
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

	public function order_item_subtotal( $subtotal, $item, $order ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', 'WC_CP_Display::order_item_subtotal()' );
		return WC_CP()->display->order_item_subtotal( $subtotal, $item, $order );
	}

	public function order_item_count( $count, $type, $order ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', 'WC_CP_Display::order_item_count()' );
		return WC_CP()->display->order_item_count( $count, $type, $order );
	}

	public static function get_composite_children( $item, $order, $return_type = 'item', $strict_mode = false ) {
		_deprecated_function( __METHOD__ . '()', '3.7.0', 'wc_cp_get_composited_order_items()' );
		$return_ids = 'id' === $return_type;
		$deep_mode  = ! $strict_mode;
		return wc_cp_get_composited_order_items( $item, $order, $return_ids, $deep_mode );
	}

	public static function get_composite_parent( $item, $order, $return_type = 'item' ) {
		_deprecated_function( __METHOD__ . '()', '3.7.0', 'wc_cp_get_composited_order_item_container()' );
		$return_id = 'id' === $return_type;
		return wc_cp_get_composited_order_item_container( $item, $order, $return_id );
	}

	public function get_composited_order_item_container( $item, $order ) {
		_deprecated_function( __METHOD__ . '()', '3.5.0', 'wc_cp_get_composited_order_item_container()' );
		return wc_cp_get_composited_order_item_container( $item, $order );
	}
}
