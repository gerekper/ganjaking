<?php
/**
 * WC_CP_Cart class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    2.2.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Composite products cart API and hooks.
 *
 * @class    WC_CP_Cart
 * @version  8.0.3
 */

class WC_CP_Cart {

	/**
	 * Globally accessible validation context for 'validate_composite_configuration'.
	 * Possible values: 'add-to-cart'|'add-to-order'|'cart'.
	 *
	 * @var string|null
	 */
	protected $validation_context = null;

	/**
	 * The single instance of the class.
	 * @var WC_CP_Cart
	 *
	 * @since 3.7.0
	 */
	protected static $_instance = null;

	/**
	 * Main WC_CP_Cart instance.
	 *
	 * Ensures only one instance of WC_CP_Cart is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_CP_Cart
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

	/*
	 * Setup hooks.
	 */
	public function __construct() {

		// Validate composite configuration on adding-to-cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 10, 6 );

		// Validate cart quantity updates.
		add_filter( 'woocommerce_update_cart_validation', array( $this, 'update_cart_validation' ), 10, 4 );

		// Validate composite configuration in cart.
		add_action( 'woocommerce_check_cart_items', array( $this, 'check_cart_items' ), 15 );

		// Add cart item data to validate.
		add_filter( 'woocommerce_cart_item_data_to_validate', array( $this, 'cart_item_data_to_validate' ), 10, 2 );

		// Add composite configuration data to all composited items.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

		// Add composited items to the cart.
		add_action( 'woocommerce_add_to_cart', array( $this, 'add_items_to_cart' ), 5, 6 );

		// Modify cart item data for composite products on first add.
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item_filter' ), 11, 2 );

		// Load composite data from session.
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 11, 2 );

		// Refresh composite configuration fields.
		add_filter( 'woocommerce_composite_container_cart_item', array( $this, 'update_composite_container_cart_item_configuration' ), 10, 2 );
		add_filter( 'woocommerce_composited_cart_item', array( $this, 'update_composited_cart_item_configuration' ), 10, 2 );

		// Ensure no orphans are in the cart at this point.
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 11 );

		// Sync quantities of children with parent.
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'update_quantity_in_cart' ), 1, 2 );

		// Ignore 'woocommerce_before_cart_item_quantity_zero' action under WC 3.7+.
		if ( ! WC_CP_Core_Compatibility::is_wc_version_gte( '3.7' ) ) {
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'update_quantity_in_cart' ) );
		}

		// Remove/restore composited items when the parent gets removed/restored.
		add_action( 'woocommerce_remove_cart_item', array( $this, 'cart_item_remove' ), 10, 2 );
		add_action( 'woocommerce_restore_cart_item', array( $this, 'cart_item_restore' ), 10, 2 );

		// Shipping fix - ensure that non-virtual containers/children, which are shipped, have a valid price that can be used for insurance calculations.
		// Additionally, composited item weights may have to be added in the container.
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'cart_shipping_packages' ), 6 );

		// "Sold Individually" context support under WC 3.5+.
		if ( WC_CP_Core_Compatibility::is_wc_version_gte( '3.5' ) ) {
			add_filter( 'woocommerce_add_to_cart_sold_individually_found_in_cart', array( $this, 'sold_individually_found_in_cart' ), 10, 4 );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| API Methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Session data loaded?
	 *
	 * @since  3.14.6
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function is_cart_session_loaded() {
		return did_action( 'woocommerce_cart_loaded_from_session' );
	}

	/**
	 * Gets the current validation context.
	 *
	 * @since  3.13.5
	 *
	 * @return string|null
	 */
	public function get_validation_context() {
		return $this->validation_context;
	}

	/**
	 * Adds a composite to the cart. Relies on specifying a composite configuration array with all necessary data - @see 'get_posted_composite_configuration()' for details.
	 *
	 * @param  mixed  $product_id      ID of the composite to add to the cart.
	 * @param  mixed  $quantity        Quantity of the composite.
	 * @param  array  $configuration   Composite configuration - @see 'get_posted_composite_configuration()'.
	 * @param  array  $cart_item_data  Custom cart item data to pass to 'WC_Cart::add_to_cart()'.
	 * @return string|WP_Error
	 */
	public function add_composite_to_cart( $product_id, $quantity, $configuration = array(), $cart_item_data = array() ) {

		$composite     = wc_get_product( $product_id );
		$added_to_cart = false;

		if ( $composite ) {

			if ( $this->validate_composite_configuration( $composite, $quantity, $configuration ) ) {
				$added_to_cart = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), array_merge( $cart_item_data, array( 'composite_data' => $configuration, 'composite_children' => array() ) ) );
			} else {

				// No other way to collect notices reliably, including notices from 3rd party extensions.
				$notices = wc_get_notices( 'error' );
				$message = __( 'The submitted composite configuration could not be added to the cart.', 'woocommerce-composite-products' );

				$added_to_cart = new WP_Error( 'woocommerce_composite_configuration', $message, array( 'notices' => $notices ) );
			}

		} else {
			$message       = __( 'A composite with this ID does not exist.', 'woocommerce-composite-products' );
			$added_to_cart = new WP_Error( 'woocommerce_composite_invalid', $message );
		}

		return $added_to_cart;
	}

	/**
	 * Parses a composite configuration array to ensure that all mandatory cart item data fields are present.
	 * Can also be used to get an array with the minimum required data to fill in before calling 'add_composite_to_cart'.
	 *
	 * @param  WC_Product_Composite  $composite      Composite product whose configuration is being parsed or generated.
	 * @param  array                 $configuration  Initial configuration array to parse. Leave empty to get a minimum array that you can fill with data - @see 'get_posted_composite_configuration()'.
	 * @param  boolean               $strict_mode    Set true to initialize component selection IDs to an empty string if undefined in the source array.
	 * @return array
	 */
	public function parse_composite_configuration( $composite, $configuration = array(), $strict_mode = false ) {

		$components           = $composite->get_components();
		$parsed_configuration = array();

		foreach ( $components as $component_id => $component ) {

			$component_configuration = isset( $configuration[ $component_id ] ) ? $configuration[ $component_id ] : array();

			$defaults = array(
				'product_id' => $strict_mode ? '' : $component->get_default_option(),
				'quantity'   => $component->get_quantity( 'min' )
			);

			$parsed_configuration[ $component_id ] = wp_parse_args( $component_configuration, $defaults );

			$parsed_configuration[ $component_id ][ 'quantity_min' ] = $component->get_quantity( 'min' );
			$parsed_configuration[ $component_id ][ 'quantity_max' ] = $component->get_quantity( 'max' );
			$parsed_configuration[ $component_id ][ 'discount' ]     = $component->get_discount();
			$parsed_configuration[ $component_id ][ 'optional' ]     = $component->is_optional() ? 'yes' : 'no';
			$parsed_configuration[ $component_id ][ 'static' ]       = $component->is_static() ? 'yes' : 'no';
			$parsed_configuration[ $component_id ][ 'title' ]        = $component->get_title();
			$parsed_configuration[ $component_id ][ 'composite_id' ] = $composite->get_id();

			if ( $parsed_configuration[ $component_id ][ 'product_id' ] > 0 ) {

				$product_id = $parsed_configuration[ $component_id ][ 'product_id' ];
				// Store the product type.
				$parsed_configuration[ $component_id ][ 'type' ] = WC_Product_Factory::get_product_type( $product_id );
			}
		}

		return $parsed_configuration;
	}

	/**
	 * Build composite configuration array from posted data. Array example:
	 *
	 *    $config = array(
	 *        134567890 => array(                       // ID of component.
	 *            'product_id'        => 15,            // ID of selected option.
	 *            'quantity'          => 2,             // Qty of selected product, will fall back to component min.
	 *            'discount'          => 50.0,          // Component discount, defaults to the defined value.
	 *            'attributes'        => array(         // Array of selected variation attribute names, sanitized.
	 *                'attribute_color' => 'black',
	 *                'attribute_size'  => 'medium'
	 *             ),
	 *            'variation_id'      => 43             // ID of chosen variation, if applicable.
	 *        )
	 *    );
	 *
	 * @param  mixed  $composite
	 * @return array
	 */
	public function get_posted_composite_configuration( $composite ) {

		$posted_config = array();

		if ( is_numeric( $composite ) ) {
			$composite = wc_get_product( $composite );
		}

		if ( is_object( $composite ) && $composite->is_type( 'composite' ) ) {

			/*
			 * Choose between $_POST or $_GET for grabbing data.
			 * We will not rely on $_REQUEST because a field name may not exist in $_POST but may well exist in $_GET, for instance when editing a composite from the cart.
			 */

			$posted_data = $_POST;

			if ( empty( $_POST[ 'add-to-cart' ] ) && ! empty( $_GET[ 'add-to-cart' ] ) ) {
				$posted_data = $_GET;
			}

			// Get components.
			$components = $composite->get_components();

			foreach ( $components as $component_id => $component ) {

				$composited_product_id                = isset( $posted_data[ 'wccp_component_selection' ] ) && ! empty( $posted_data[ 'wccp_component_selection' ][ $component_id ] ) ? absint( $posted_data[ 'wccp_component_selection' ][ $component_id ] ) : '';
				$composited_product_quantity          = isset( $posted_data[ 'wccp_component_quantity' ], $posted_data[ 'wccp_component_quantity' ][ $component_id ] ) ? absint( $posted_data[ 'wccp_component_quantity' ][ $component_id ] ) : $component->get_quantity( 'min' );
				$composited_product_sold_individually = false;

				if ( ! $composited_product_id && $component->is_static() ) {
					if ( ! empty( $posted_data[ 'action' ] ) && $posted_data[ 'action' ] === 'woocommerce_add_order_item' ) {
						$composited_product_id = $component->get_default_option();
					}
				}

				$composited_product_id = isset( $posted_data[ 'wccp_component_selection_nil' ], $posted_data[ 'wccp_component_selection_nil' ][ $component_id ] ) ? '' : $composited_product_id;

				if ( $composited_product_id ) {

					$component_option = $component->get_option( $composited_product_id );

					if ( ! $component_option ) {
						continue;
					}

					$composited_product                   = $component_option->get_product();
					$composited_product_type              = $composited_product->get_type();
					$composited_product_sold_individually = $composited_product->is_sold_individually();

					if ( $composited_product_sold_individually && $composited_product_quantity > 1 ) {
						$composited_product_quantity = 1;
					}
				}

				$posted_config[ $component_id ] = array();

				$posted_config[ $component_id ][ 'product_id' ] = $composited_product_id;
				$posted_config[ $component_id ][ 'quantity' ]   = $composited_product_quantity;

				// Continue when selected product is 'None'.
				if ( ! $composited_product_id ) {
					continue;
				}

				if ( 'variable' === $composited_product_type ) {

					$attributes  = $composited_product->get_attributes();
					$variation   = empty( $posted_data[ 'wccp_variation_id' ][ $component_id ] ) ? '' : absint( wp_unslash( $posted_data[ 'wccp_variation_id' ][ $component_id ] ) );
					$attr_config = array();

					foreach ( $attributes as $attribute ) {

						if ( ! $attribute->get_variation() ) {
							continue;
						}

						$taxonomy = wc_variation_attribute_name( $attribute->get_name() );

						if ( isset( $posted_data[ 'wccp_' . $taxonomy ][ $component_id ] ) && '' !== $posted_data[ 'wccp_' . $taxonomy ][ $component_id ] ) {

							// Get value from post data.
							if ( $attribute->is_taxonomy() ) {
								$value = sanitize_title( stripslashes( $posted_data[ 'wccp_' . $taxonomy ][ $component_id ] ) );
							} else {
								$value = html_entity_decode( wc_clean( stripslashes( $posted_data[ 'wccp_' . $taxonomy ][ $component_id ] ) ), ENT_QUOTES, get_bloginfo( 'charset' ) );
							}

							$attr_config[ $taxonomy ] = $value;
						}
					}

					$posted_config[ $component_id ][ 'attributes' ] = $attr_config;

					// Store posted variation ID, or search for it.
					if ( $variation ) {

						$posted_config[ $component_id ][ 'variation_id' ] = $variation;

					} else {

						$variations = $composited_product->get_children();

						if ( sizeof( $variations ) > 1 ) {

							$data_store = WC_Data_Store::load( 'product' );

							if ( $variation = $data_store->find_matching_product_variation( $composited_product, $posted_config[ $component_id ][ 'attributes' ] ) ) {
								$posted_config[ $component_id ][ 'variation_id' ] = $variation;
							}

						} else {

							$posted_config[ $component_id ][ 'variation_id' ] = current( $variations );
						}
					}
				}
			}

			/**
			 * 'woocommerce_posted_composite_configuration' filter.
			 *
			 * @since  3.14.0
			 *
			 * @param  array                 $configuration
			 * @param  WC_Product_Composite  $composite
			 */
			$posted_config = apply_filters( 'woocommerce_posted_composite_configuration', $this->parse_composite_configuration( $composite, $posted_config, true ), $composite );
		}

		return $posted_config;
	}

	/**
	 * Rebuilds posted form data associated with a composite configuration.
	 *
	 * @since  3.14.0
	 *
	 * @param  WC_Product_Composite  $composite
	 * @param  array                 $configuration
	 * @return boolean
	 */
	public function rebuild_posted_composite_form_data( $configuration ) {

		$form_data = array();

		if ( ! empty( $configuration ) ) {
			foreach ( $configuration as $component_id => $component_config_data ) {

				if ( isset( $component_config_data[ 'product_id' ] ) ) {
					$form_data[ 'wccp_component_selection' ][ $component_id ] = $component_config_data[ 'product_id' ];
				}

				if ( isset( $component_config_data[ 'quantity' ] ) ) {
					$form_data[ 'wccp_component_quantity' ][ $component_id ] = $component_config_data[ 'quantity' ];
				}

				if ( isset( $component_config_data[ 'variation_id' ] ) ) {
					$form_data[ 'wccp_variation_id' ][ $component_id ] = $component_config_data[ 'variation_id' ];
				}

				if ( isset( $component_config_data[ 'attributes' ] ) && is_array( $component_config_data[ 'attributes' ] ) ) {
					foreach ( $component_config_data[ 'attributes' ] as $tax => $val ) {
						$form_data[ 'wccp_' . $tax ][ $component_id ] = $val;
					}
				}
			}
		}

		/**
		 * 'woocommerce_rebuild_posted_composite_form_data' filter.
		 *
		 * @since  3.14.0
		 *
		 * @param  array  $form_data
		 * @param  array  $configuration
		 */
		return apply_filters( 'woocommerce_rebuild_posted_composite_form_data', $form_data, $configuration );
	}

	/**
	 * Validates the components in a composite configuration.
	 *
	 * @param  mixed   $product
	 * @param  int     $composite_quantity
	 * @param  array   $configuration
	 * @param  string  $context
	 * @return boolean
	 */
	public function validate_composite_configuration( $composite, $composite_quantity, $configuration, $context = '' ) {

		$is_configuration_valid = true;

		if ( is_numeric( $composite ) ) {
			$composite = wc_get_product( $composite );
		}

		if ( is_object( $composite ) && $composite->is_type( 'composite' ) ) {

			try {

				if ( '' === $context ) {

					/**
					 * 'woocommerce_composite_validation_context' filter.
					 *
					 * @since  3.13.5
					 *
					 * @param  string                $context
					 * @param  WC_Product_Composite  $product
					 */
					$context = apply_filters( 'woocommerce_composite_validation_context', 'add-to-cart', $composite );
				}

				$this->validation_context = $context;

				$composite_id    = $composite->get_id();
				$composite_title = $composite->get_title();
				$components      = $composite->get_components();
				$validation_data = array();

				// If a stock-managed product / variation exists in the bundle multiple times, its stock will be checked only once for the sum of all bundled quantities.
				// The WC_CP_Stock_Manager class does exactly that.
				$composited_stock = new WC_CP_Stock_Manager( $composite );

				foreach ( $components as $component_id => $component ) {

					$component_title = $component->get_title();

					$validation_data[ $component_id ] = array();

					/*
					 * Store product selection and quantity data for validation later.
					 */
					$composited_product_id = ! empty( $configuration[ $component_id ][ 'product_id' ] ) ? strval( absint( $configuration[ $component_id ][ 'product_id' ] ) ) : '0';

					$validation_data[ $component_id ][ 'product_id' ] = $composited_product_id;
					$validation_data[ $component_id ][ 'optional' ]   = $component->is_optional() ? 'yes' : 'no';
					$validation_data[ $component_id ][ 'title' ]      = $component_title;

					if ( '0' === $composited_product_id ) {
						continue;
					}

					// Prevent people from fucking around - only valid component options can be added to the cart.
					if ( ! in_array( $composited_product_id, $component->get_options() ) ) {

						if ( 'add-to-cart' === $context ) {
							$reason = sprintf( __( 'Please choose a valid &quot;%s&quot; option&hellip;', 'woocommerce-composite-products' ), $component_title );
						} else {
							$reason = sprintf( __( 'The chosen &quot;%s&quot; option is unavailable.', 'woocommerce-composite-products' ), $component_title );
						}

						if ( 'add-to-cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} elseif ( 'cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} else {
							$notice = $reason;
						}

						throw new Exception( $notice );
					}

					// Store quantity min/max data for later use.
					$item_quantity_min = $component->get_quantity( 'min' );
					$item_quantity_max = $component->get_quantity( 'max' );

					$validation_data[ $component_id ][ 'quantity_min' ] = $item_quantity_min;
					$validation_data[ $component_id ][ 'quantity_max' ] = $item_quantity_max;

					// Store quantity for validation.
					$item_quantity = isset( $configuration[ $component_id ][ 'quantity' ] ) ? absint( $configuration[ $component_id ][ 'quantity' ] ) : $item_quantity_min;
					$quantity      = $item_quantity * $composite_quantity;

					if ( ! $component_option = $composite->get_component_option( $component_id, $composited_product_id ) ) {

						if ( 'add-to-cart' === $context ) {
							$reason = sprintf( __( 'Please choose another &quot;%s&quot; option&hellip;', 'woocommerce-composite-products' ), $component_title );
						} else {
							$reason = sprintf( __( 'The chosen &quot;%s&quot; option is unavailable.', 'woocommerce-composite-products' ), $component_title );
						}

						if ( 'add-to-cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} elseif ( 'cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} else {
							$notice = $reason;
						}

						throw new Exception( $notice );
					}

					$composited_product      = $component_option->get_product();
					$composited_product_type = $composited_product->get_type();
					$item_sold_individually  = $composited_product->is_sold_individually();

					if ( $item_sold_individually && $quantity > 1 ) {
						$quantity = 1;
					}

					// Save data for validation.
					$validation_data[ $component_id ][ 'quantity' ]          = $item_quantity;
					$validation_data[ $component_id ][ 'sold_individually' ] = $item_sold_individually ? 'yes' : 'no';

					if ( $quantity === 0 ) {
						continue;
					}

					/*
					 * Validate attributes.
					 */

					if ( 'variable' === $composited_product_type ) {

						$composited_variation_id = isset( $configuration[ $component_id ][ 'variation_id' ] ) ? $configuration[ $component_id ][ 'variation_id' ] : '';
						$composited_variation    = $composited_variation_id ? wc_get_product( $composited_variation_id ) : false;

						if ( $composited_variation ) {
							// Add item for stock validation.
							$composited_stock->add_item( $composited_product_id, $composited_variation, $quantity );
							// Save variation ID for validation.
							$validation_data[ $component_id ][ 'variation_id' ] = $composited_variation_id;
						}

						// Verify all attributes for the variable product were set.
						$attributes         = $composited_product->get_attributes();
						$variation_data     = array();
						$missing_attributes = array();
						$all_set            = true;

						if ( $composited_variation ) {

							$variation_data = wc_get_product_variation_attributes( $composited_variation_id );

							foreach ( $attributes as $attribute ) {

								if ( ! $attribute->get_variation() ) {
									continue;
								}

								$attribute_name = $attribute->get_name();
								$taxonomy       = wc_variation_attribute_name( $attribute_name );

								if ( isset( $configuration[ $component_id ][ 'attributes' ][ $taxonomy ] ) ) {

									// Get valid value from variation.
									$valid_value = isset( $variation_data[ $taxonomy ] ) ? $variation_data[ $taxonomy ] : '';

									// Allow if valid.
									if ( '' === $valid_value || $valid_value === $configuration[ $component_id ][ 'attributes' ][ $taxonomy ] ) {
										continue;
									}

									$missing_attributes[] = '&quot;' . wc_attribute_label( $attribute_name ) . '&quot;';

								} else {
									$missing_attributes[] = '&quot;' . wc_attribute_label( $attribute_name ) . '&quot;';
								}

								$all_set = false;
							}

						} else {
							$all_set = false;
						}

						if ( ! $all_set ) {

							if ( $missing_attributes ) {
								$reason = sprintf( _n( '%1$s is a required &quot;%2$s&quot; field.', '%1$s are required &quot;%2$s&quot; fields.', sizeof( $missing_attributes ), 'woocommerce-composite-products' ), wc_format_list_of_items( $missing_attributes ), $component_title );
							} else {
								if ( 'add-to-cart' === $context ) {
									$reason = sprintf( __( 'Please choose &quot;%s&quot; options&hellip;', 'woocommerce-composite-products' ), $component_title );
								} else {
									$reason = sprintf( __( '&quot;%s&quot; is missing some required options.', 'woocommerce-composite-products' ), $component_title );
								}
							}

							if ( 'add-to-cart' === $context ) {
								$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
							} elseif ( 'cart' === $context ) {
								$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
							} else {
								$notice = $reason;
							}

							throw new Exception( $notice );
						}

					} else {
						// Add item for validation.
						$composited_stock->add_item( $composited_product_id, false, $quantity );
					}

					/**
					 * Filter to allow composited products to add extra items to the stock manager.
					 *
					 * @param  mixed   $stock
					 * @param  string  $composite_id
					 * @param  string  $component_id
					 * @param  string  $composited_product_id
					 * @param  int     $quantity
					 */
					$composited_stock->add_stock( apply_filters( 'woocommerce_composite_component_associated_stock', '', $composite_id, $component_id, $composited_product_id, $quantity ) );
				}

				/*
				 * Stock Validation.
				 */

				if ( 'add-to-cart' === $context ) {
					$is_configuration_valid = $composited_stock->validate_stock( array(
						'context'         => $context,
						'throw_exception' => true
					) );
				}

				/*
				 * Selections and Quantities Validation.
				 */

				$composite_configuration = array();

				foreach ( $validation_data as $component_id => $component_validation_data ) {
					$composite_configuration[ $component_id ] = array(
						'product_id'   => absint( $component_validation_data[ 'product_id' ] ),
						'variation_id' => isset( $component_validation_data[ 'variation_id' ] ) ? absint( $component_validation_data[ 'variation_id' ] ) : 0
					);
				}

				// Validate selections.
				$matching_scenarios = $composite->scenarios()->find_matching( $composite_configuration );

				if ( is_wp_error( $matching_scenarios ) ) {

					$error_code = $matching_scenarios->get_error_code();

					if ( in_array( $error_code, array( 'woocommerce_composite_configuration_selection_required', 'woocommerce_composite_configuration_selection_invalid' ) ) ) {

						$error_data = $matching_scenarios->get_error_data( $error_code );

						if ( ! empty( $error_data[ 'component_id' ] ) ) {

							if ( 'woocommerce_composite_configuration_selection_required' === $error_code ) {

								if ( 'add-to-cart' === $context ) {
									$reason = sprintf( __( 'Please choose a &quot;%s&quot; option.', 'woocommerce-composite-products' ), $validation_data[ $error_data[ 'component_id' ] ][ 'title' ] );
								} else {
									$reason = sprintf( __( 'A &quot;%s&quot; selection is required.', 'woocommerce-composite-products' ), $validation_data[ $error_data[ 'component_id' ] ][ 'title' ] );
								}

							} elseif ( 'woocommerce_composite_configuration_selection_invalid' === $error_code ) {

								if ( 'add-to-cart' === $context ) {
									$reason = sprintf( __( 'Please choose a different &quot;%s&quot; option.', 'woocommerce-composite-products' ), $validation_data[ $error_data[ 'component_id' ] ][ 'title' ] );
								} else {
									$reason = sprintf( __( 'The chosen &quot;%s&quot; option is unavailable.', 'woocommerce-composite-products' ), $validation_data[ $error_data[ 'component_id' ] ][ 'title' ] );
								}
							}

							if ( 'add-to-cart' === $context ) {
								$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
							} elseif ( 'cart' === $context ) {
								$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
							} else {
								$notice = $reason;
							}

							throw new Exception( $notice );
						}

					} elseif ( 'woocommerce_composite_configuration_invalid' === $error_code ) {

						if ( 'cart' === $context ) {
							$notice = sprintf( __( 'The selected &quot;%1$s&quot; options cannot be purchased together.', 'woocommerce-composite-products' ), $composite_title );
						} else {
							$notice = __( 'The selected options cannot be purchased together. Please choose a different configuration and try again.', 'woocommerce-composite-products' );
						}

						throw new Exception( $notice );
					}
				}

				// Validate Quantities.
				foreach ( $validation_data as $component_id => $component_validation_data ) {

					// No need to validate the quantity of an empty selection if we have gotten this far.
					if ( '0' === $component_validation_data[ 'product_id' ] ) {
						continue;
					}

					$qty = $component_validation_data[ 'quantity' ];

					// Allow 3rd parties to modify the min/max qty settings of a component conditionally through scenarios.

					/**
					 * 'woocommerce_composite_component_validation_quantity_min' filter.
					 *
					 * @param  int     $qty_min
					 * @param  string  $component_id
					 * @param  array   $config_data
					 * @param  array   $matching_scenarios
					 * @param  array   $scenario_data
					 * @param  string  $composite_id
					 */
					$qty_min = absint( apply_filters( 'woocommerce_composite_component_validation_quantity_min', $component_validation_data[ 'quantity_min' ], $component_id, $component_validation_data, $matching_scenarios, $composite ) );

					/**
					 * 'woocommerce_composite_component_validation_quantity_max' filter.
					 *
					 * @param  int     $qty_min
					 * @param  string  $component_id
					 * @param  array   $config_data
					 * @param  array   $matching_scenarios
					 * @param  array   $scenario_data
					 * @param  string  $composite_id
					 */
					$qty_max = absint( apply_filters( 'woocommerce_composite_component_validation_quantity_max', $component_validation_data[ 'quantity_max' ], $component_id, $component_validation_data, $matching_scenarios, $composite ) );

					$sold_individually = $component_validation_data[ 'sold_individually' ];

					if ( $qty < $qty_min && 'yes' !== $sold_individually ) {

						$reason = sprintf( __( 'The quantity of &quot;%1$s&quot; cannot be lower than %2$d.', 'woocommerce-composite-products' ), $component_validation_data[ 'title' ], $qty_min );

						if ( 'add-to-cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} elseif ( 'cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} else {
							$notice = $reason;
						}

						throw new Exception( $notice );

					} elseif ( $qty_max && $qty > $qty_max ) {

						$reason = sprintf( __( 'The quantity of &quot;%1$s&quot; cannot be higher than %2$d.', 'woocommerce-composite-products' ), $component_validation_data[ 'title' ], $qty_max );

						if ( 'add-to-cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} elseif ( 'cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} else {
							$notice = $reason;
						}

						throw new Exception( $notice );
					}
				}

				/*
				 * Custom Validation.
				 */

				foreach ( $validation_data as $component_id => $component_validation_data ) {

					/**
					 * Custom component validation.
					 *
					 * @param  WC_CP_Component  $component
					 * @param  array            $component_validation_data
					 * @param  int              $composite_quantity
					 * @param  array            $configuration
					 * @param  string           $context
					 */
					do_action( 'woocommerce_composite_component_validation_' . str_replace( '-', '_', $context ), $components[ $component_id ], $component_validation_data, $composite_quantity, $configuration, $context );
				}

				// Apply deprecated filter.

				$validation_filter_name = 'woocommerce_' . str_replace( '-', '_', $context ) . '_composite_validation';

				if ( has_filter( $validation_filter_name ) ) {

					_deprecated_function( 'The "' . $validation_filter_name . '" filter', '3.14.0' );

					/**
					 * Filter composite configuration validation result.
					 *
					 * @param  boolean              $result
					 * @param  string               $composite_id
					 * @param  WC_CP_Stock_Manager  $composited_stock
					 * @param  array                $composite_configuration
					 */
					$is_configuration_valid = apply_filters( 'woocommerce_' . str_replace( '-', '_', $context ) . '_composite_validation', $is_configuration_valid, $composite_id, $composited_stock, $configuration );
				}

			} catch ( Exception $e ) {

				if ( ! WC_CP_Core_Compatibility::is_rest_api_request() ) {

					$notice = $e->getMessage();

					if ( $notice ) {
						wc_add_notice( $notice, 'error' );
					}
				}

				$is_configuration_valid = false;
			}
		}

		$this->validation_context = null;

		return $is_configuration_valid;
	}

	/**
	 * Analyzes child items to characterize a composite.
	 *
	 * @since  3.14.0
	 *
	 * @param  array   $cart_item
	 * @param  string  $key
	 * @return bool
	 */
	public function container_cart_item_contains( $cart_item, $key ) {

		$child_items = wc_cp_get_composited_cart_items( $cart_item, WC()->cart->cart_contents, false, true );
		$contains    = false;

		foreach ( $child_items as $child_item_key => $child_item ) {
			if ( 'sold_individually' === $key ) {
				if ( $child_item[ 'data' ]->is_sold_individually() ) {
					$contains = true;
					break;
				}
			}
		}

		return $contains;
	}

	/**
	 * Modifies composited cart item virtual status and price depending on composite pricing and shipping strategies.
	 *
	 * @param  array                 $cart_item
	 * @param  WC_Product_Composite  $composite
	 * @return array
	 */
	private function set_composited_cart_item( $cart_item, $composite ) {

		$component_id = $cart_item[ 'composite_item' ];

		// Pricing.
		$cart_item = $this->set_composited_cart_item_price( $cart_item, $component_id, $composite );

		// Shipping.
		if ( $cart_item[ 'data' ]->needs_shipping() ) {

			$component_option = $composite->get_component_option( $component_id, $cart_item[ 'product_id' ] );

			if ( $component_option && false === $component_option->is_shipped_individually() ) {

				if ( $component_option->is_weight_aggregated( $cart_item[ 'data' ] ) ) {

					$cart_item_weight = $cart_item[ 'data' ]->get_weight( 'edit' );

					if ( $cart_item[ 'data' ]->is_type( 'variation' ) && '' === $cart_item_weight ) {

						$parent_data      = $cart_item[ 'data' ]->get_parent_data();
						$cart_item_weight = $parent_data[ 'weight' ];
					}

					$cart_item[ 'data' ]->composited_weight = $cart_item_weight;
				}

				$cart_item[ 'data' ]->composited_value = 'props' === WC_CP_Products::get_composited_cart_item_discount_method() ? $cart_item[ 'data' ]->get_price( 'edit' ) : $component_option->get_raw_price( $cart_item[ 'data' ], 'cart' );

				$cart_item[ 'data' ]->set_virtual( 'yes' );
				$cart_item[ 'data' ]->set_weight( '' );
			}
		}

		/**
		 * Last chance to filter the component cart item.
		 *
		 * @param  array                 $cart_item
		 * @param  WC_Product_Composite  $composite
		 */
		return apply_filters( 'woocommerce_composited_cart_item', $cart_item, $composite );
	}

	/**
	 * Get composited products prices with discounts.
	 *
	 * @param  int                   $product_id
	 * @param  mixed                 $variation_id
	 * @param  string                $component_id
	 * @param  WC_Product_Composite  $composite
	 * @return double
	 */
	private function set_composited_cart_item_price( $cart_item, $component_id, $composite ) {

		$product_id       = $cart_item[ 'product_id' ];
		$component_option = $composite->get_component_option( $component_id, $product_id );

		if ( ! $component_option ) {
			return $cart_item;
		}

		$discount_method = WC_CP_Products::get_composited_cart_item_discount_method();

		if ( 'filters' === $discount_method ) {

			$cart_item[ 'data' ]->composited_cart_item = $component_option;

		} elseif ( 'props' === $discount_method ) {

			$cart_item[ 'data' ]->set_price( $component_option->get_raw_price( $cart_item[ 'data' ], 'cart' ) );

			if ( false === $component_option->is_priced_individually() && false === $cart_item[ 'data' ]->get_price( 'edit' ) > 0 ) {

				$cart_item[ 'data' ]->set_regular_price( 0 );
				$cart_item[ 'data' ]->set_sale_price( '' );

			} else {

				$discount           = $component_option->get_discount();
				$composited_product = $component_option->get_product();
				$on_sale            = ! empty( $discount ) || $composited_product->is_on_sale();

				if ( $on_sale ) {
					$cart_item[ 'data' ]->set_sale_price( $component_option->get_price( 'edit' ) );
				}
			}
		}

		return $cart_item;
	}

	/**
	 * Set container price equal to the base price.
	 *
	 * @param  array  $cart_item
	 * @return array
	 */
	private function set_composite_container_cart_item( $cart_item ) {

		$composite = $cart_item[ 'data' ];
		$composite->set_object_context( 'cart' );
		/**
		 * Last chance to filter the container cart item.
		 *
		 * @param  array                 $cart_item
		 * @param  WC_Product_Composite  $composite
		 */
		return apply_filters( 'woocommerce_composite_container_cart_item', $cart_item, $composite );
	}

	/**
	 * Refresh parent item configuration fields that might be out-of-date.
	 *
	 * @param  array                 $cart_item
	 * @param  WC_Product_Composite  $composite
	 * @return array
	 */
	public function update_composite_container_cart_item_configuration( $cart_item, $composite ) {

		if ( isset( $cart_item[ 'composite_data' ] ) ) {
			$cart_item[ 'composite_data' ] = $this->parse_composite_configuration( $composite, $cart_item[ 'composite_data' ], true );
		}

		return $cart_item;
	}

	/**
	 * Refresh child item configuration fields that might be out-of-date.
	 *
	 * @param  array                 $cart_item
	 * @param  WC_Product_Composite  $composite
	 * @return array
	 */
	public function update_composited_cart_item_configuration( $cart_item, $composite ) {

		if ( $composite_container_item = wc_cp_get_composited_cart_item_container( $cart_item ) ) {
			$cart_item[ 'composite_data' ] = $composite_container_item[ 'composite_data' ];
		}

		return $cart_item;
	}

	/**
	 * Add a composited product to the cart. Must be done without updating session data, recalculating totals or calling 'woocommerce_add_to_cart' recursively.
	 * For the recursion issue, see: https://core.trac.wordpress.org/ticket/17817.
	 *
	 * @param  int     $composite_id
	 * @param  mixed   $product
	 * @param  string  $quantity
	 * @param  int     $variation_id
	 * @param  array   $variation
	 * @param  array   $cart_item_data
	 * @return bool
	 */
	private function composited_add_to_cart( $composite_id, $product, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data = array() ) {

		if ( $quantity <= 0 ) {
			return false;
		}

		// Get the product / ID.
		if ( is_a( $product, 'WC_Product' ) ) {

			$product_id   = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
			$variation_id = $product->is_type( 'variation' ) ? $product->get_id() : $variation_id;
			$product_data = $product->is_type( 'variation' ) ? $product : wc_get_product( $variation_id ? $variation_id : $product_id );

		} else {

			$product_id   = absint( $product );
			$product_data = wc_get_product( $product_id );

			if ( $product_data->is_type( 'variation' ) ) {
				$product_id   = $product_data->get_parent_id();
				$variation_id = $product_data->get_id();
			} else {
				$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
			}
		}

		if ( ! $product_data ) {
			return false;
		}

		// Load cart item data when adding to cart. WC core filter.
		$cart_item_data = ( array ) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity );

		// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
		$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

		// See if this product and its options is already in the cart.
		$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

		// If cart_item_key is set, the item is already in the cart and its quantity will be handled by update_quantity_in_cart.
		if ( ! $cart_item_key ) {

			$cart_item_key = $cart_id;

			// Add item after merging with $cart_item_data - allow plugins and 'add_cart_item_filter' to modify cart item. WC core filter.
			WC()->cart->cart_contents[ $cart_item_key ] = apply_filters( 'woocommerce_add_cart_item', array_merge( $cart_item_data, array(
				'key'          => $cart_item_key,
				'product_id'   => absint( $product_id ),
				'variation_id' => absint( $variation_id ),
				'variation'    => $variation,
				'quantity'     => $quantity,
				'data'         => $product_data
			) ), $cart_item_key );
		}

		/**
		 * Action 'woocommerce_composited_add_to_cart'.
		 *
		 * @param  string  $cart_item_key
		 * @param  string  $product_id
		 * @param  string  $quantity
		 * @param  string  $variation_id
		 * @param  array   $variation
		 * @param  array   $cart_item_data
		 * @param  string  $composite_id
		 */
		do_action( 'woocommerce_composited_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data, $composite_id );

		return $cart_item_key;
	}

	/*
	|--------------------------------------------------------------------------
	| Filter Hooks.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Redirect to the cart when updating a composite cart item.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public function update_composite_cart_redirect( $url ) {
		return wc_get_cart_url();
	}

	/**
	 * Filter the displayed notice after redirecting to the cart when updating a composite cart item.
	 *
	 * @param  string  $url
	 * @return string
	 */
	public function update_composite_cart_redirect_message( $message ) {
		return __( 'Cart updated.', 'woocommerce' );
	}

	/**
	 * Check composite cart item configurations on cart load.
	 */
	public function check_cart_items() {
		foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( wc_cp_is_composite_container_cart_item( $cart_item ) ) {

				$configuration = isset( $cart_item[ 'composite_data' ] ) ? $cart_item[ 'composite_data' ] : $this->get_posted_composite_configuration( $cart_item[ 'data' ] );

				$this->validate_composite_configuration( $cart_item[ 'data' ], $cart_item[ 'quantity' ], $configuration, 'cart' );
			}
		}
	}

	/**
	 * Add composite cart item data to validate.
	 *
	 * @since  6.1.2
	 *
	 * @param  array       $data
	 * @param  WC_Product  $product
	 * @return array
	 */
	public function cart_item_data_to_validate( $data, $product ) {

		if ( $product->is_type( 'composite' ) ) {
			$data[ 'cp_discount_method' ]  = WC_CP_Products::get_composited_cart_item_discount_method();
			$data[ 'cp_composite_type' ]   = $product->is_virtual() ? 'unassembled' : 'assembled';
			$data[ 'cp_aggregate_weight' ] = $product->get_aggregate_weight();
		}

		return $data;
	}

	/**
	 * Validates that all composited items chosen can be added-to-cart before actually starting to add items.
	 *
	 * @param  bool  $add
	 * @param  int   $product_id
	 * @param  int   $quantity
	 * @return bool
	 */
	public function add_to_cart_validation( $add, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		if ( ! $add ) {
			return false;
		}

		/*
		 * Prevent components from getting validated when re-ordering after cart session data has been loaded:
		 * They will be added by the container item on 'woocommerce_add_to_cart'.
		 */
		if ( $this->is_cart_session_loaded() ) {
			// Prevent composited items from getting validated - they will be added by the container item.
			if ( isset( $cart_item_data[ 'is_order_again_composited' ] ) ) {
				return false;
			}
		}

		// Get product type.
		$product_type = WC_Product_Factory::get_product_type( $product_id );

		if ( 'composite' === $product_type ) {

			// Get product.
			$composite = wc_get_product( $product_id );

			if ( ! is_object( $composite ) || ! is_a( $composite, 'WC_Product_Composite' ) ) {
				return false;
			}

			$configuration = isset( $cart_item_data[ 'composite_data' ] ) ? $cart_item_data[ 'composite_data' ] : $this->get_posted_composite_configuration( $composite );

			if ( ! $this->validate_composite_configuration( $composite, $quantity, $configuration ) ) {
				return false;
			}

			foreach ( $configuration as $component_id => $component_configuration ) {

				/**
				 * Filter configuration validation result.
				 *
				 * @param  boolean               $result
				 * @param  string                $product_id
				 * @param  string                $component_id
				 * @param  string                $composited_product_id
				 * @param  int                   $composite_quantity
				 * @param  array                 $cart_item_data
				 * @param  WC_Product_Composite  $composite
				 */
				if ( false === apply_filters( 'woocommerce_composite_component_add_to_cart_validation', true, $product_id, $component_id, $component_configuration[ 'product_id' ], $quantity, $cart_item_data, $composite, $component_configuration ) ) {
					return false;
				}
			}
		}

		return $add;
	}

	/**
	 * Adds configuration-specific cart-item data.
	 *
	 * @param  array  $cart_item_data
	 * @param  int    $product_id
	 * @return void
	 */
	public function add_cart_item_data( $cart_item_data, $product_id ) {

		// Get product type.
		$product_type = WC_Product_Factory::get_product_type( $product_id );

		if ( 'composite' === $product_type ) {

			$updating_composite_in_cart = false;

			// Updating composite in cart?
			if ( isset( $_POST[ 'update-composite' ] ) ) {

				$updating_cart_key = wc_clean( $_POST[ 'update-composite' ] );

				if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {

					$updating_composite_in_cart = true;

					// Remove.
					WC()->cart->remove_cart_item( $updating_cart_key );

					// Redirect to cart.
					add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'update_composite_cart_redirect' ) );

					// Edit notice.
					add_filter( 'wc_add_to_cart_message_html', array( $this, 'update_composite_cart_redirect_message' ) );
				}
			}

			// Use posted data to create a unique array with the composite configuration, if needed.
			if ( ! isset( $cart_item_data[ 'composite_data' ] ) ) {

				$configuration = $this->get_posted_composite_configuration( $product_id );

				foreach ( $configuration as $component_id => $component_configuration ) {
					/**
					 * Filter component configuration identifier. Use this hook to add configuration data for 3rd party input fields.
					 * Any custom data added here can be copied into the child cart item data array using the 'woocommerce_composited_cart_item_data' filter.
					 *
					 * @param   array   $component_configuration
					 * @param   string  $component_id
					 * @param   mixed   $product_id
					 */
					$configuration[ $component_id ] = apply_filters( 'woocommerce_composite_component_cart_item_identifier', $component_configuration, $component_id, $product_id );
				}

				$cart_item_data[ 'composite_data' ] = $configuration;

				// Check "Sold Individually" option context.
				if ( false === WC_CP_Core_Compatibility::is_wc_version_gte( '3.5' ) && false === $updating_composite_in_cart && ( $composite = wc_get_product( $product_id ) ) && $composite->is_sold_individually() ) {
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						if ( $product_id === $cart_item[ 'product_id' ] && 'product' === $composite->get_sold_individually_context() ) {
							throw new Exception( sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View Cart', 'woocommerce' ), sprintf( __( 'You cannot add another &quot;%s&quot; to your cart.', 'woocommerce-composite-products' ), $composite->get_title() ) ) );
						} elseif ( wc_cp_is_composite_container_cart_item( $cart_item ) && $configuration === $cart_item[ 'composite_data' ] ) {
							throw new Exception( sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View Cart', 'woocommerce' ), sprintf( __( 'You have already added an identical &quot;%s&quot; to your cart. You cannot add another one.', 'woocommerce-composite-products' ), $composite->get_title() ) ) );
						}
					}
				}
			}

			// Prepare additional data for later use.
			if ( ! isset( $cart_item_data[ 'composite_children' ] ) ) {
				$cart_item_data[ 'composite_children' ] = array();
			}
		}

		return $cart_item_data;
	}

	/**
	 * Adds composited items to the cart.
	 *
	 * @param  string  $composite_cart_key
	 * @param  int     $composite_id
	 * @param  int     $composite_quantity
	 * @param  int     $variation_id
	 * @param  array   $variation
	 * @param  array   $cart_item_data
	 * @return void
	 */
	public function add_items_to_cart( $composite_cart_key, $composite_id, $composite_quantity, $variation_id, $variation, $cart_item_data ) {

		if ( ! $this->is_cart_session_loaded() ) {
			return;
		}

		// Runs when adding container item - adds composited items.
		if ( wc_cp_is_composite_container_cart_item( $cart_item_data ) ) {

			// Only attempt to add composited items if they don't already exist.
			foreach ( WC()->cart->cart_contents as $cart_key => $cart_value ) {
				if ( isset( $cart_value[ 'composite_data' ] ) && isset( $cart_value[ 'composite_parent' ] ) && $composite_cart_key == $cart_value[ 'composite_parent' ] ) {
					return;
				}
			}

			// Results in a unique cart ID hash, so that composited and non-composited versions of the same product will be added separately to the cart.
			$composited_cart_data = array( 'composite_parent' => $composite_cart_key, 'composite_data' => $cart_item_data[ 'composite_data' ] );

			// Now add all items - yay!
			foreach ( $cart_item_data[ 'composite_data' ] as $component_id => $component_configuration ) {

				$composited_item_cart_data = $composited_cart_data;

				$composited_item_cart_data[ 'composite_item' ] = $component_id;

				$composited_product_id = $component_configuration[ 'product_id' ];
				$variation_id          = '';
				$variations            = array();

				if ( '' === $composited_product_id ) {
					continue;
				}

				// Get product.
				$composited_product = wc_get_product( $composited_product_id );

				if ( ! $composited_product ) {
					continue;
				}

				$item_quantity = $component_configuration[ 'quantity' ];
				$quantity      = $composited_product->is_sold_individually() ? 1 : $item_quantity * $composite_quantity;

				if ( $quantity === 0 ) {
					continue;
				}

				if ( $composited_product->is_type( 'variable' ) ) {

					$variation_id = ( int ) $component_configuration[ 'variation_id' ];
					$variations   = $component_configuration[ 'attributes' ];

				} elseif ( $composited_product->is_type( 'bundle' ) ) {

					$composited_item_cart_data[ 'stamp' ]         = $component_configuration[ 'stamp' ];
					$composited_item_cart_data[ 'bundled_items' ] = array();
				}

				/**
				 * Filter to allow loading child cart item data from the parent cart item data array.
				 *
				 * @param  array  $component_cart_item_data
				 * @param  array  $composite_cart_item_data
				 */
				$composited_item_cart_data = apply_filters( 'woocommerce_composited_cart_item_data', $composited_item_cart_data, $cart_item_data );

				/**
				 * Action 'woocommerce_composited_product_before_add_to_cart'.
				 *
				 * @param  string  $composited_product_id
				 * @param  string  $quantity
				 * @param  string  $variation_id
				 * @param  array   $variations
				 * @param  array   $composited_item_cart_data
				 *
				 * @hooked WC_CP_Addons_Compatibility::before_composited_add_to_cart()
				 */
				do_action( 'woocommerce_composited_product_before_add_to_cart', $composited_product_id, $quantity, $variation_id, $variations, $composited_item_cart_data );

				// Add to cart.
				$composited_item_cart_key = $this->composited_add_to_cart( $composite_id, $composited_product, $quantity, $variation_id, $variations, $composited_item_cart_data );

				if ( $composited_item_cart_key && ! in_array( $composited_item_cart_key, WC()->cart->cart_contents[ $composite_cart_key ][ 'composite_children' ] ) ) {
					WC()->cart->cart_contents[ $composite_cart_key ][ 'composite_children' ][] = $composited_item_cart_key;
				}

				/**
				 * Action 'woocommerce_composited_product_after_add_to_cart'.
				 *
				 * @param  string  $composited_product_id
				 * @param  string  $quantity
				 * @param  string  $variation_id
				 * @param  array   $variations
				 * @param  array   $composited_item_cart_data
				 *
				 * @hooked WC_CP_Addons_Compatibility::after_composited_add_to_cart()
				 */
				do_action( 'woocommerce_composited_product_after_add_to_cart', $composited_product_id, $quantity, $variation_id, $variations, $composited_item_cart_data );
			}
		}
	}

	/**
	 * Modifies cart item data - important for the first calculation of totals only.
	 *
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return array
	 */
	public function add_cart_item_filter( $cart_item, $cart_item_key ) {

		$cart_contents = WC()->cart->cart_contents;

		if ( wc_cp_is_composite_container_cart_item( $cart_item ) ) {

			$cart_item = $this->set_composite_container_cart_item( $cart_item );

		} elseif ( $composite_container_item = wc_cp_get_composited_cart_item_container( $cart_item ) ) {

			$composite = $composite_container_item[ 'data' ];
			$cart_item = $this->set_composited_cart_item( $cart_item, $composite );
		}

		return $cart_item;
	}

	/**
	 * Load all composite-related session data.
	 *
	 * @param  array  $cart_item
	 * @param  array  $item_session_values
	 * @return void
	 */
	public function get_cart_item_from_session( $cart_item, $item_session_values ) {

		if ( ! isset( $cart_item[ 'composite_data' ] ) && isset( $item_session_values[ 'composite_data' ] ) ) {
			$cart_item[ 'composite_data' ] = $item_session_values[ 'composite_data' ];
		}

		if ( wc_cp_is_composite_container_cart_item( $item_session_values ) ) {

			if ( $cart_item[ 'data' ]->is_type( 'composite' ) ) {

				if ( ! isset( $cart_item[ 'composite_children' ] ) ) {
					$cart_item[ 'composite_children' ] = $item_session_values[ 'composite_children' ];
				}

				$cart_item = $this->set_composite_container_cart_item( $cart_item );

			} else {

				if ( isset( $cart_item[ 'composite_children' ] ) ) {
					unset( $cart_item[ 'composite_children' ] );
				}
			}
		}

		if ( wc_cp_maybe_is_composited_cart_item( $item_session_values ) ) {

			if ( ! isset( $cart_item[ 'composite_parent' ] ) ) {
				$cart_item[ 'composite_parent' ] = $item_session_values[ 'composite_parent' ];
			}

			if ( ! isset( $cart_item[ 'composite_item' ] ) ) {
				$cart_item[ 'composite_item' ] = $item_session_values[ 'composite_item' ];
			}

			if ( $composite_container_item = wc_cp_get_composited_cart_item_container( $item_session_values ) ) {

				$composite = $composite_container_item[ 'data' ];

				if ( $composite->is_type( 'composite' ) && $composite->has_component( $cart_item[ 'composite_item' ] ) ) {
					$cart_item = $this->set_composited_cart_item( $cart_item, $composite );
				}
			}
		}

		return $cart_item;
	}

	/**
	 * Ensure any cart items marked as composited have a valid parent. If not, silently remove them.
	 *
	 * @param  WC_Cart  $cart
	 * @return void
	 */
	public function cart_loaded_from_session( $cart ) {

		$cart_contents = $cart->cart_contents;

		if ( ! empty( $cart_contents ) ) {

			foreach ( $cart_contents as $cart_item_key => $cart_item ) {

				if ( wc_cp_maybe_is_composited_cart_item( $cart_item ) ) {

					$container_item = wc_cp_get_composited_cart_item_container( $cart_item );

					if ( ! $container_item || ! isset( $container_item[ 'composite_children' ] ) || ! is_array( $container_item[ 'composite_children' ] ) || ! in_array( $cart_item_key, $container_item[ 'composite_children' ] ) ) {
						unset( WC()->cart->cart_contents[ $cart_item_key ] );
					} elseif ( isset( $cart_item[ 'composite_item' ] ) && $container_item[ 'data' ]->is_type( 'composite' ) && ! $container_item[ 'data' ]->has_component( $cart_item[ 'composite_item' ] ) ) {
						unset( WC()->cart->cart_contents[ $cart_item_key ] );
					}

				// Is from order-again without the parent item?
				} elseif ( isset( $cart_item[ 'composite_item' ] ) && ! isset( $cart_item[ 'composite_parent' ] ) ) {
					unset( WC()->cart->cart_contents[ $cart_item_key ] );
				}
			}
		}
	}

	/**
	 * Keeps composited items' quantities in sync with container item.
	 *
	 * @param  string  $cart_item_key
	 * @param  int     $quantity
	 * @return void
	 */
	public function update_quantity_in_cart( $cart_item_key, $quantity = 0 ) {

		if ( ! empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

			if ( $quantity == 0 || $quantity < 0 ) {
				$quantity = 0;
			} else {
				$quantity = WC()->cart->cart_contents[ $cart_item_key ][ 'quantity' ];
			}

			$composite_children = wc_cp_get_composited_cart_items( WC()->cart->cart_contents[ $cart_item_key ] );

			if ( ! empty( $composite_children ) ) {

				// Change the quantity of all composited items that belong to the same config.
				foreach ( $composite_children as $child_key => $child_item ) {

					$child_item = WC()->cart->cart_contents[ $child_key ];

					if ( $child_item[ 'data' ]->is_sold_individually() && $quantity > 0 ) {

						WC()->cart->set_quantity( $child_key, 1, false );

					} else {

						$child_item_id  = $child_item[ 'composite_item' ];
						$child_quantity = $child_item[ 'composite_data' ][ $child_item_id ][ 'quantity' ];

						WC()->cart->set_quantity( $child_key, $child_quantity * $quantity, false );
					}
				}
			}
		}
	}

	/**
	 * Validates in-cart component quantity changes.
	 *
	 * @param  bool    $passed
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item
	 * @param  int     $quantity
	 * @return bool
	 */
	public function update_cart_validation( $passed, $cart_item_key, $cart_item, $quantity ) {

		if ( $parent = wc_cp_get_composited_cart_item_container( $cart_item ) ) {

			$component_id    = $cart_item[ 'composite_item' ];
			$parent_key      = $cart_item[ 'composite_parent' ];
			$parent_quantity = $parent[ 'quantity' ];
			$min_quantity    = $parent_quantity * $cart_item[ 'composite_data' ][ $component_id ][ 'quantity_min' ];
			$max_quantity    = $cart_item[ 'composite_data' ][ $component_id ][ 'quantity_max' ] ? $parent_quantity * $cart_item[ 'composite_data' ][ $component_id ][ 'quantity_max' ] : '';

			if ( $quantity < $min_quantity ) {

				wc_add_notice( sprintf( __( 'The quantity of &quot;%s&quot; cannot be lower than %d.', 'woocommerce-composite-products' ), $cart_item[ 'data' ]->get_title(), $min_quantity ), 'error' );
				return false;

			} elseif ( $max_quantity && $quantity > $max_quantity ) {

				wc_add_notice( sprintf( __( 'The quantity of &quot;%s&quot; cannot be higher than %d.', 'woocommerce-composite-products' ), $cart_item[ 'data' ]->get_title(), $max_quantity ), 'error' );
				return false;

			} elseif ( $quantity % $parent_quantity != 0 ) {

				wc_add_notice( sprintf( __( 'The quantity of &quot;%s&quot; must be entered in multiples of %d.', 'woocommerce-composite-products' ), $cart_item[ 'data' ]->get_title(), $parent_quantity ), 'error' );
				return false;

			} else {

				// Update new component quantity in container/children composite_data array.
				// Note: updating the composite_data array will have no effect on the generated parent cart_id at this point.

				WC()->cart->cart_contents[ $parent_key ][ 'composite_data' ][ $component_id ][ 'quantity' ] = $quantity / $parent_quantity;

				foreach ( wc_cp_get_composited_cart_items( $parent, WC()->cart->cart_contents, true ) as $composite_child_key ) {
					WC()->cart->cart_contents[ $composite_child_key ][ 'composite_data' ][ $component_id ][ 'quantity' ] = $quantity / $parent_quantity;
				}
			}
		}

		return $passed;
	}

	/**
	 * Remove child cart items with parent.
	 *
	 * @param  string   $cart_item_key
	 * @param  WC_Cart  $cart
	 * @return void
	 */
	public function cart_item_remove( $cart_item_key, $cart ) {

		if ( wc_cp_is_composite_container_cart_item( $cart->removed_cart_contents[ $cart_item_key ] ) ) {

			$bundled_item_cart_keys = wc_cp_get_composited_cart_items( $cart->removed_cart_contents[ $cart_item_key ], $cart->cart_contents, true );

			foreach ( $bundled_item_cart_keys as $bundled_item_cart_key ) {

				$remove = $cart->cart_contents[ $bundled_item_cart_key ];
				$cart->removed_cart_contents[ $bundled_item_cart_key ] = $remove;

				/** WC core action. */
				do_action( 'woocommerce_remove_cart_item', $bundled_item_cart_key, $cart );

				unset( $cart->cart_contents[ $bundled_item_cart_key ] );
			}
		}
	}

	/**
	 * Restore child cart items with parent.
	 *
	 * @param  string   $cart_item_key
	 * @param  WC_Cart  $cart
	 * @return void
	 */
	public function cart_item_restore( $cart_item_key, $cart ) {

		if ( wc_cp_is_composite_container_cart_item( $cart->cart_contents[ $cart_item_key ] ) ) {

			$bundled_item_cart_keys = wc_cp_get_composited_cart_items( $cart->cart_contents[ $cart_item_key ], $cart->removed_cart_contents, true );

			foreach ( $bundled_item_cart_keys as $bundled_item_cart_key ) {

				$remove = $cart->removed_cart_contents[ $bundled_item_cart_key ];
				$cart->cart_contents[ $bundled_item_cart_key ] = $remove;

				/** WC core action. */
				do_action( 'woocommerce_restore_cart_item', $bundled_item_cart_key, $cart );

				unset( $cart->removed_cart_contents[ $bundled_item_cart_key ] );
			}
		}
	}

	/**
	 * Shipping fix - add the value of any children that are not shipped individually to the container value and, optionally, add their weight to the container weight, as well.
	 *
	 * @param  array  $packages
	 * @return array
	 */
	public function cart_shipping_packages( $packages ) {

		if ( ! empty( $packages ) ) {

			foreach ( $packages as $package_key => $package ) {

				if ( ! empty( $package[ 'contents' ] ) ) {
					foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

						if ( wc_cp_is_composite_container_cart_item( $cart_item_data ) ) {

							$composite     = WC_CP_Helpers::get_product_preserving_meta( $cart_item_data[ 'data' ] );
							$composite_qty = $cart_item_data[ 'quantity' ];

							/*
							 * Container needs shipping: Aggregate the prices of any children that are physically packaged in their parent and, optionally, aggregate their weights into the parent, as well.
							 */

							if ( $composite->needs_shipping() ) {

								$bundled_weight = 0.0;
								$bundled_value  = 0.0;

								$composite_totals = array(
									'line_subtotal'     => $cart_item_data[ 'line_subtotal' ],
									'line_total'        => $cart_item_data[ 'line_total' ],
									'line_subtotal_tax' => $cart_item_data[ 'line_subtotal_tax' ],
									'line_tax'          => $cart_item_data[ 'line_tax' ],
									'line_tax_data'     => $cart_item_data[ 'line_tax_data' ]
								);

								foreach ( wc_cp_get_composited_cart_items( $cart_item_data, WC()->cart->cart_contents, true ) as $child_item_key ) {

									/**
									 * 'woocommerce_composited_package_item' filter.
									 *
									 * @param  array   $child_item
									 * @param  string  $child_item_key
									 * @param  string  $parent_item_key
									 */
									$child_cart_item_data      = apply_filters( 'woocommerce_composited_package_item', WC()->cart->cart_contents[ $child_item_key ], $child_item_key, $cart_item_key );
									$composited_product        = $child_cart_item_data[ 'data' ];
									$composited_product_qty    = $child_cart_item_data[ 'quantity' ];
									$composited_product_value  = isset( $composited_product->composited_value ) ? $composited_product->composited_value : 0.0;
									$composited_product_weight = isset( $composited_product->composited_weight ) ? $composited_product->composited_weight : 0.0;

									// Aggregate price of physically packaged child item - already converted to virtual.

									if ( $composited_product_value ) {

										$bundled_value += $composited_product_value * $composited_product_qty;

										$composite_totals[ 'line_subtotal' ]     += $child_cart_item_data[ 'line_subtotal' ];
										$composite_totals[ 'line_total' ]        += $child_cart_item_data[ 'line_total' ];
										$composite_totals[ 'line_subtotal_tax' ] += $child_cart_item_data[ 'line_subtotal_tax' ];
										$composite_totals[ 'line_tax' ]          += $child_cart_item_data[ 'line_tax' ];

										$packages[ $package_key ][ 'contents_cost' ] += $child_cart_item_data[ 'line_total' ];

										$child_item_line_tax_data = $child_cart_item_data[ 'line_tax_data' ];

										$composite_totals[ 'line_tax_data' ][ 'total' ]    = array_merge( $composite_totals[ 'line_tax_data' ][ 'total' ], $child_item_line_tax_data[ 'total' ] );
										$composite_totals[ 'line_tax_data' ][ 'subtotal' ] = array_merge( $composite_totals[ 'line_tax_data' ][ 'subtotal' ], $child_item_line_tax_data[ 'subtotal' ] );
									}

									// Aggregate weight of physically packaged child item - already converted to virtual.

									if ( $composited_product_weight ) {
										$bundled_weight += $composited_product_weight * $composited_product_qty;
									}
								}

								if ( $bundled_value > 0 ) {
									$composite_price = $composite->get_price( 'edit' );
									$composite->set_price( (double) $composite_price + $bundled_value / $composite_qty );
								}

								$packages[ $package_key ][ 'contents' ][ $cart_item_key ] = array_merge( $cart_item_data, $composite_totals );

								if ( $bundled_weight > 0 ) {
									$composite_weight = $composite->get_weight( 'edit' );
									$composite->set_weight( (double) $composite_weight + $bundled_weight / $composite_qty );
								}

								$packages[ $package_key ][ 'contents' ][ $cart_item_key ][ 'data' ] = $composite;
							}
						}
					}
				}
			}
		}

		return $packages;
	}

	/**
	 * "Sold Individually" context support under WC 3.5+.
	 *
	 * @since  3.14.6
	 *
	 * @param  bool    $found
	 * @param  int     $product_id
	 * @param  int     $variation_id
	 * @param  array   $cart_item
	 * @return bool
	 */
	public function sold_individually_found_in_cart( $found, $product_id, $variation_id, $cart_item ) {

		$updating_composite_in_cart = false;

		// Updating composite in cart?
		if ( isset( $_POST[ 'update-composite' ] ) ) {
			$updating_cart_key          = wc_clean( $_POST[ 'update-composite' ] );
			$updating_composite_in_cart = isset( WC()->cart->cart_contents[ $updating_cart_key ] );
		}

		if ( $updating_composite_in_cart ) {
			return $found;
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			return $found;
		}

		if ( ! $product->is_type( 'composite' ) ) {
			return $found;
		}

		if ( ! $product->is_sold_individually() ) {
			return $found;
		}

		// Check "Sold Individually" option context.
		foreach ( WC()->cart->get_cart() as $search_cart_item ) {
			if ( $product_id === $search_cart_item[ 'product_id' ] && 'product' === $product->get_sold_individually_context() ) {
				$found = true;
			} elseif ( wc_cp_is_composite_container_cart_item( $search_cart_item ) && isset( $cart_item[ 'composite_data' ] ) && $cart_item[ 'composite_data' ] === $search_cart_item[ 'composite_data' ] ) {
				throw new Exception( sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View Cart', 'woocommerce' ), sprintf( __( 'You have already added an identical &quot;%s&quot; to your cart. You cannot add another one.', 'woocommerce-composite-products' ), $product->get_title() ) ) );
			}
		}

		return $found;
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	public function order_again( $cart_item_data, $order_item, $order ) {
		_deprecated_function( __METHOD__ . '()', '3.14.6', 'WC_CP_Order_Again::order_again_cart_item_data()' );
		return WC_CP_Order_Again::order_again_cart_item_data( $cart_item_data, $order_item, $order );
	}
	public function coupon_validity( $valid, $product, $coupon, $item ) {
		_deprecated_function( __METHOD__ . '()', '3.14.0', 'WC_CP_Coupon::coupon_validity()' );
		return WC_CP_Coupon::coupon_validity( $valid, $product, $coupon, $item );
	}
	public function format_product_subtotal( $product, $subtotal ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', 'WC_CP_Display::format_subtotal()' );
		return WC_CP()->display->format_subtotal( $product, $subtotal );
	}
	public function cart_item_price( $price, $values, $cart_item_key ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', 'WC_CP_Display::cart_item_price()' );
		return WC_CP()->display->cart_item_price( $price, $values, $cart_item_key );
	}
	public function item_subtotal( $subtotal, $values, $cart_item_key ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', 'WC_CP_Display::cart_item_subtotal()' );
		return WC_CP()->display->cart_item_subtotal( $subtotal, $values, $cart_item_key );
	}
	public function cart_item_quantity( $quantity, $cart_item_key ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', 'WC_CP_Display::cart_item_quantity()' );
		return WC_CP()->display->cart_item_quantity( $quantity, $cart_item_key );
	}
	public function cart_item_remove_link( $link, $cart_item_key ) {
		_deprecated_function( __METHOD__ . '()', '3.12.0', 'WC_CP_Display::cart_item_remove_link()' );
		return WC_CP()->display->cart_item_remove_link( $link, $cart_item_key );
	}
}
