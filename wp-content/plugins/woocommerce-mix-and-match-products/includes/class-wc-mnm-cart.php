<?php
/**
 * Cart Functions and Filters
 *
 * @author   Kathy Darling
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Cart
 * @since    1.0.0
 * @version  1.9.12
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_Cart Class.
 *
 * Functions and filters for adding Mix and Match type products to cart.
 */
class WC_Mix_and_Match_Cart {

	/**
	 * The single instance of the class.
	 * @var WC_Mix_and_Match_Cart
	 *
	 * @since 1.9.2
	 */
	protected static $_instance = null;

	/**
	 * Main class instance. Ensures only one instance of class is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_Mix_and_Match_Cart
	 * @since  1.9.2
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * __construct function.
	 *
	 * @return object
	 */
	public function __construct() {

		// Validate mnm add-to-cart.
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 10, 6 );

		// validate mnm cart update.
		add_filter( 'woocommerce_update_cart_validation', array( $this, 'update_cart_validation' ), 10, 4 );

		// Validate container configuration in cart.
		add_action( 'woocommerce_check_cart_items', array( $this, 'check_cart_items' ), 15 );

		// Add mnm configuration data to all mnm items.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 2 );

		// Add mnm items to the cart.
		add_action( 'woocommerce_add_to_cart', array( $this, 'add_mnm_items_to_cart' ), 10, 6 );

		// Modify price and shipping details for child items.
		add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item_filter' ), 10, 2 );

		// Preserve data in cart.
		add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_data_from_session' ), 10, 3 );

		// Ensure no orphans are in the cart at this point.
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ) );

		// Sync quantities of packed items with container quantity.
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'update_quantity_in_cart' ), 10, 2 );
		
		// Ignore 'woocommerce_before_cart_item_quantity_zero' action under WC 3.7+.
		if ( ! WC_MNM_Core_Compatibility::is_wc_version_gte( '3.7' ) ) {
			add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'update_quantity_in_cart' ) );
		}

		// Filter cart widget items.
		add_filter( 'woocommerce_widget_cart_item_visible', array( $this, 'cart_widget_filter' ), 10, 3 );

		// Filter cart item count.
		add_filter( 'woocommerce_cart_contents_count', array( $this, 'cart_contents_count' ) );

		// Control modification of packed items' quantity.
		add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'cart_item_remove_link' ), 10, 2 );

		// Change packed item quantity output.
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 3 );

		// Hide packed item price.
		add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );

		// Remove/restore children cart items when parent is removed/restored.
		add_action( 'woocommerce_cart_item_removed', array( $this, 'cart_item_removed' ), 10, 2 );
		add_action( 'woocommerce_cart_item_restored', array( $this, 'cart_item_restored' ), 10, 2 );

		// Shipping fix - ensure that non-virtual containers/children, which are shipped, have a valid price that can be used for insurance calculations.
		// Additionally, allow child item weights to be added to the container weight.
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'cart_shipping_packages' ), 1, 5 );
	}


	/**
	 * Session data loaded?
	 *
	 * @since  1.4.0
	 *
	 * @param  string  $context
	 * @return string
	 */
	public function is_cart_session_loaded() {
		return did_action( 'woocommerce_cart_loaded_from_session' );
	}


	/**
	 * Adds mnm contents to the cart.
	 *
	 * @param  string 	$item_cart_key
	 * @param  int 		$product_id
	 * @param  int 		$quantity
	 * @param  int 		$variation_id
	 * @param  array 	$variation
	 * @param  array 	$cart_item_data
	 */
	function add_mnm_items_to_cart( $item_cart_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

		if ( wc_mnm_is_container_cart_item( $cart_item_data ) ) {

			$mnm_cart_item_data = array(
				'mnm_container' => $item_cart_key,
				'mnm_child_id'  => $variation_id > 0 ? $variation_id : $product_id
			);

			// Now add all items - yay!
			foreach ( $cart_item_data[ 'mnm_config' ] as $item_id => $mnm_item_data ) {

				$mnm_product_id     = $mnm_item_data[ 'product_id' ];
				$mnm_variation_id   = isset( $mnm_item_data[ 'variation_id' ] ) ? $mnm_item_data[ 'variation_id' ] : 0;
				$mnm_variations     = isset( $mnm_item_data[ 'variation' ] ) ? $mnm_item_data[ 'variation' ] : array();

				$item_quantity      = $mnm_item_data[ 'quantity' ];
				$mnm_quantity       = $item_quantity * $quantity;

				/**
				 * Allow filtering child cart item data.
				 *
				 * Example: If the parent cart item data array already contains extension-specific configuration info.
				 *
				 * @param array $mnm_cart_item_data Configuration of each Mix and Match child item.
				 * @param array $cart_item_data Container product data.
				 * @param int $item_id The child item product or variation ID.
				 * @param int $product_id The Mix and Match container product ID.
				 */
				$mnm_cart_item_data = (array) apply_filters( 'woocommerce_mnm_child_cart_item_data', $mnm_cart_item_data, $cart_item_data, $item_id, $product_id );

				/**
				 * Before child item is added to cart.
				 *
				 * @param int $mnm_product_id The child item product ID.
				 * @param int $mnm_quantity The quantity of the child item in the container.
				 * @param int $mnm_variation_id The child item variation ID.
				 * @param array $mnm_variations Attributes of specific variation being added to cart.
				 * @param array $mnm_cart_item_data Child item product data.
				 */
				do_action( 'woocommerce_mnm_before_mnm_add_to_cart', $mnm_product_id, $mnm_quantity, $mnm_variation_id, $mnm_variations, $mnm_cart_item_data );

				// Add to cart.
				$mnm_item_cart_key = $this->mnm_add_to_cart( $product_id, $mnm_product_id, $mnm_quantity, $mnm_variation_id, $mnm_variations, $mnm_cart_item_data );

				if ( $mnm_item_cart_key ) {

					if ( ! isset( WC()->cart->cart_contents[ $item_cart_key ][ 'mnm_contents' ] ) ) {

						WC()->cart->cart_contents[ $item_cart_key ][ 'mnm_contents' ] = array();

					} elseif ( ! in_array( $mnm_item_cart_key, WC()->cart->cart_contents[ $item_cart_key ][ 'mnm_contents' ] ) ) {

						WC()->cart->cart_contents[ $item_cart_key ][ 'mnm_contents' ][] = $mnm_item_cart_key;
					}
				}

				/**
				 * After child item is added to cart.
				 *
				 * @param int $mnm_product_id The child item product ID.
				 * @param int $mnm_quantity The quantity of the child item in the container.
				 * @param int $mnm_variation_id The child item variation ID.
				 * @param array $mnm_variations Attributes of specific variation being added to cart.
				 * @param array $mnm_cart_item_data Child item product data.
				 */
				do_action( 'woocommerce_mnm_after_mnm_add_to_cart', $mnm_product_id, $mnm_quantity, $mnm_variation_id, $mnm_variations, $mnm_cart_item_data );

			}

		}

	}


	/**
	 * Add a mnm child to the cart. Must be done without updating session data, recalculating totals or calling 'woocommerce_add_to_cart' recursively.
	 * For the recursion issue, see: https://core.trac.wordpress.org/ticket/17817.
	 *
	 * @param int          $container_id
	 * @param int          $product_id
	 * @param string       $quantity
	 * @param int          $variation_id
	 * @param array        $variation
	 * @param array        $cart_item_data
	 * @return string|false
	 */
	public function mnm_add_to_cart( $container_id, $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data ) {

		/**
		 * Load cart item data for child items.
		 *
		 * @param array $cart_item_data Child item's cart data.
		 * @param int $product_id Child item's product ID.
		 * @param int $variation_id Child item's variation ID.
		 * @param int $quantity Child item's quantity.
		 */
		$cart_item_data = ( array ) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity );

		// Generate a ID based on product ID, variation ID, variation data, and other cart item data.
		$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );

		// See if this product and its options is already in the cart.
		$cart_item_key = WC()->cart->find_product_in_cart( $cart_id );

		// Get the product.
		$product_data = wc_get_product( $variation_id ? $variation_id : $product_id );

		// If cart_item_key is set, the item is already in the cart and its quantity will be handled by update_quantity_in_cart().
		if ( ! $cart_item_key ) {

			$cart_item_key = $cart_id;

			/**
			 * Add item after merging with $cart_item_data
			 *
			 * Allow plugins and add_cart_item_filter() to modify cart item.
			 *
			 * @param array $cart_item_data Child item's cart data.
			 * @param str $cart_item_key Key in the WooCommerce cart array.
			 */
			WC()->cart->cart_contents[ $cart_item_key ] = apply_filters( 'woocommerce_add_cart_item', array_merge( $cart_item_data, array(
				'product_id'   => absint( $product_id ),
				'variation_id' => absint( $variation_id ),
				'variation'    => $variation,
				'quantity'     => $quantity,
				'data'         => $product_data
			) ), $cart_item_key );

		}

		/**
		 * Add child items to cart.
		 *
		 * Use this hook for compatibility instead of the 'woocommerce_add_to_cart' action hook to work around the recursion issue (solved in WP 4.7).
		 * When the recursion issue is solved, we can simply replace calls to 'mnm_add_to_cart()' with direct calls to 'WC_Cart::add_to_cart()' and delete this function.
		 *
		 * @param str $cart_item_key
		 * @param int $product_id
		 * @param int $quantity
		 * @param int $variation_id
		 * @param array $cart_item_data
		 * @param int $container_id
		 */
		do_action( 'woocommerce_mnm_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data, $container_id );

		return $cart_item_key;
	}


	/**
	 * Build container configuration array from posted data. Array example:
	 *
	 *    $config = array(
	 *        134 => array(                             // ID of child item.
	 *            'mnm_child_id'      => 134,           // ID of child item.
	 *            'product_id'        => 15,            // ID of child product.
	 *            'quantity'          => 2,             // Qty of child product, will fall back to min.
	 *            'variation_id'      => 43             // ID of chosen variation, if applicable.
	 *            'variation'		  => array( 'color' => 'blue' ) // Attributes of chosen variation.
	 *        )
	 *    );
	 *
	 * @param  mixed  $product
	 * @return array
	 */
	public function get_posted_container_configuration( $product ) {

		$posted_config = array();

		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( is_object( $product ) && $product->is_type( 'mix-and-match' ) ) {

			$product_id      = $product->get_id();
			$child_items	 = $product->get_children();

			if ( ! empty( $child_items ) ) {

				/*
				 * Choose between $_POST or $_GET for grabbing data.
				 * We will not rely on $_REQUEST because checkbox names may not exist in $_POST but they may well exist in $_GET, for instance when editing a container from the cart.
				 */

				$posted_data = $_POST;

				if ( empty( $_POST[ 'add-to-cart' ] ) && ! empty( $_GET[ 'add-to-cart' ] ) ) {
					$posted_data = $_GET;
				}

				$posted_field_name = wc_mnm_get_child_input_name( $product_id );

				if( isset( $posted_data[ $posted_field_name ] ) ) {

					foreach ( $child_items as $child_id => $child_product ) {

						$child_item_quantity = intval( $posted_data[ $posted_field_name ][ $child_id ] );

						// Check that a product has been selected.
						if ( isset( $posted_data[ $posted_field_name ][ $child_id ] ) && $posted_data[ $posted_field_name ][ $child_id ] !== '' && $child_item_quantity > 0 ) {
							$posted_config[ $child_id ] = array();

							$parent_id = $child_product->get_parent_id();

							$posted_config[ $child_id ][ 'mnm_child_id' ] = $child_id;
							$posted_config[ $child_id ][ 'product_id' ]   = $parent_id > 0 ? $parent_id : $child_product->get_id();
							$posted_config[ $child_id ][ 'variation_id' ] = $parent_id > 0 ? $child_product->get_id() : 0;
							$posted_config[ $child_id ][ 'quantity' ]     = $child_item_quantity;
							$posted_config[ $child_id ][ 'variation' ]    = $parent_id > 0 ? $child_product->get_variation_attributes() : array();
						} else {
							continue;
						}
					}
				}
			}
		}

		/**
		 * Filter the posted configuration to support alternative templates.
		 *
		 * @since  1.9.0
		 * @param array $posted_config
		 * @param WC_Mix_and_Match_Product $product 
		 * @return array
		 */
		return (array) apply_filters( 'woocommerce_mnm_get_posted_container_configuration', $posted_config, $product );
	}


	/**
	 * Rebuilds posted form data associated with a container configuration.
	 *
	 * @since  1.4.0
	 *
	 * @param  array $configuration
	 * @param  obj   $container WC_Mix_and_Match_Product // Added in 1.7.0.
	 * @return array  
	 *    $form_data = array(
	 *         'mnm_quantity' => array( array( $ID => $quantity ) )
	 *    );
	 */
	public function rebuild_posted_container_form_data( $configuration, $container = null ) {
		$form_data = array();

		foreach ( $configuration as $mnm_item_id => $item_config ) {
			$form_data[$mnm_item_id] = isset( $item_config['quantity'] ) ? intval( $item_config['quantity'] ) : 0;
		}

		// Return the array as mnm_quantity = array() if $container is passed.
		if ( $container instanceof WC_Product_Mix_and_Match ) {
			$input_key = wc_mnm_get_child_input_name( $container->get_id() );
			$form_data = array( $input_key => $form_data );
		}

		/**
		 * Filter the rebuilt configuration to support alternative templates.
		 *
		 * @since  1.9.0
		 * @param array $form_data
		 * @param array $configuration
		 * @param WC_Mix_and_Match_Product $container 
		 * @return array
		 */
		return (array) apply_filters( 'woocommerce_mnm_get_posted_container_form_data', $form_data, $configuration, $container );

	}


	/**
	 * Validates that all MnM items chosen can be added-to-cart before actually starting to add items.
	 *
	 * @param  bool $passed_validation
	 * @param  int 	$product_id
	 * @param  int 	$quantity
	 * @param  int  $variation_id
	 * @param array $variation - selected attribues
	 * @param array $cart_item_data - data from session
	 * @return bool
	 */
	public function add_to_cart_validation( $passed_validation, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {

		if ( ! $passed_validation ) {
			return false;
		}

		/*
		 * Prevent child items from getting validated when re-ordering after cart session data has been loaded:
		 * They will be added by the container item on 'woocommerce_add_to_cart'.
		 */
		if ( $this->is_cart_session_loaded() ) {
			if ( isset( $cart_item_data[ 'is_order_again_mnm_item' ] ) ) {
				return false;
			}
		}

		$product_type = WC_Product_Factory::get_product_type( $product_id );

		if ( 'mix-and-match' === $product_type ) {

			$container = wc_get_product( $product_id );

			if ( is_a( $container, 'WC_Product_Mix_and_Match' ) && false === $this->validate_container_add_to_cart( $container, $quantity, $cart_item_data ) ) {
				$passed_validation = false;
			}

		}

		return $passed_validation;
	}


	/**
	 * Validates add-to-cart for MNM containers.
	 * Basically ensures that stock for all child products exists before attempting to add them to cart.
	 *
	 * @since  1.4.0
	 *
	 * @param  WC_Product_Mix_and_Match  $container
	 * @param  int                $quantity
	 * @param  array              $cart_item_data
	 * @return boolean
	 */
	public function validate_container_add_to_cart( $container, $quantity, $cart_item_data ) {

		$is_valid = true;

		/**
		 * 'woocommerce_mnm_before_container_validation' filter.
		 *
		 * Early chance to stop/bypass any further validation.
		 *
		 * @param  boolean            $true
		 * @param  WC_Product_Mix_and_Match  $container
		 */
		if ( apply_filters( 'woocommerce_mnm_before_container_validation', true, $container ) ) {

			$configuration = isset( $cart_item_data[ 'mnm_config' ] ) ? $cart_item_data[ 'mnm_config' ] : $this->get_posted_container_configuration( $container );

			if ( ! $this->validate_container_configuration( $container, $quantity, $configuration ) ) {
				$is_valid = false;
			}

		} else {
			$is_valid = false;
		}

		return $is_valid;
	}

	/**
	 * Validates that all MnM items can be updated before updating the container.
	 *
	 * @param  bool 	$passed_validation
	 * @param  int 		$cart_item_key
	 * @param  array 	$values
	 * @param  int 		$quantity
	 * @return bool
	 */
	public function update_cart_validation( $passed_validation, $cart_item_key, $values, $product_quantity ) {

		$product = $values[ 'data' ];

		if ( ! $product ) {
			return false;
		}

		// Don't check child items individually, will be checked by parent container.
		if ( wc_mnm_maybe_is_child_cart_item( $values ) ) {
			return $passed_validation;
		}

		if ( $product->is_type( 'mix-and-match' ) && wc_mnm_is_container_cart_item( $values ) ) {

			$existing_quantity   = $values[ 'quantity' ];
			$additional_quantity = $product_quantity - $existing_quantity;

			$passed_validation = $this->validate_container_configuration( $product, $additional_quantity, $values[ 'mnm_config' ], 'cart' );

		}

		return $passed_validation;
	}


	/**
	 * Check container cart item configurations on cart load.
	 */
	public function check_cart_items() {

		foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

				$configuration = isset( $cart_item[ 'mnm_config' ] ) ? $cart_item[ 'mnm_config' ] : $this->get_posted_container_configuration( $cart_item[ 'data' ] );

				$this->validate_container_configuration( $cart_item[ 'data' ], $cart_item[ 'quantity' ], $configuration, 'cart' );
			}
		}
	}


	/**
	 * Validates add to cart for MNM containers.
	 * Basically ensures that stock for all child products exists before attempting to add them to cart.
	 *
	 * @since  1.4.0
	 *
	 * @param  mixed   $container int|WC_Product_Mix_and_Match
	 * @param  int     $container_quantity
	 * @param  array   $configuration 
	 * @see            get_posted_container_configuration() for array details.
	 * @param  string  $context - Possible values: 'add-to-cart'|'add-to-order'|'cart'
	 * @return boolean
	 */
	public function validate_container_configuration( $container, $container_quantity, $configuration, $context = '' ) {

		$is_configuration_valid = true;

		// Count the total child items.
		$total_items_in_container = 0;

		if ( is_numeric( $container ) ) {
			$container = wc_get_product( $container );
		}

		if ( is_object( $container ) && $container->is_type( 'mix-and-match' ) ) {

			try {

				if ( '' === $context ) {

					/**
					 * 'woocommerce_mnm_container_validation_context' filter.
					 *
					 * @since  1.9.0
					 *
					 * @param  string                   $context
					 * @param  WC_Product_Mix_and_Match $container
					 */
					$context = apply_filters( 'woocommerce_mnm_container_validation_context', 'add-to-cart', $container );
				}

				$container_id    = $container->get_id();
				$container_title = $container->get_title();

				// If a stock-managed product / variation exists in the container multiple times, its stock will be checked only once for the sum of all child quantities.
				// The stock manager class keeps a record of stock-managed product / variation ids.
				$mnm_stock = new WC_Mix_and_Match_Stock_Manager( $container );

				// Grab child items.
				$mnm_items = $container->get_children();

				if ( sizeof( $mnm_items ) ) {

					// Loop through the items.
					foreach ( $mnm_items as $id => $mnm_item ) {

						// Check that a product has been selected.
						if ( isset( $configuration[ $id ] ) && $configuration[ $id ] !== '' ) {
							$item_quantity = $configuration[ $id ][ 'quantity' ];
							// If the ID isn't in the posted data something is rotten in Denmark.
						} else {
							continue;
						}

						// Total quantity in single container.
						$total_items_in_container += $item_quantity;

						// Total quantity of items in all containers: for stock purposes.
						$quantity = $item_quantity * $container_quantity;

						// Product is_purchasable - only for per item pricing.
						if ( $container->is_priced_per_product() && ! $mnm_item->is_purchasable() ) {
							// translators: %s is the product title.
							$notice = sprintf( __( 'The configuration you have selected cannot be added to the cart since &quot;%s&quot; cannot be purchased.', 'woocommerce-mix-and-match-products' ), $mnm_item->get_title() );
							throw new Exception( $notice );
						}

						// Check individual min/max quantities
						$min_quantity  = $container->get_child_quantity( 'min', $id );
						$max_quantity  = $container->get_child_quantity( 'max', $id );
						$step_quantity = $container->get_child_quantity( 'step', $id );

						if ( $max_quantity && $item_quantity > $max_quantity ) {
							// translators: %s is the product title. %d is the maximum quantity of the child product.
							$notice = sprintf( __( 'The configuration you have selected cannot be added to the cart since you cannot select more than %1$d of &quot;%2$s&quot;.', 'woocommerce-mix-and-match-products' ), $max_quantity, $mnm_item->get_title() );
							throw new Exception( $notice );
						} elseif( $min_quantity && $item_quantity < $min_quantity ) {
							// translators: %s is the product title. %d is the minimum quantity of the child product.
							$notice = sprintf( __( 'The configuration you have selected cannot be added to the cart since you must select at least %1$d of &quot;%2$s&quot;.', 'woocommerce-mix-and-match-products' ), $min_quantity, $mnm_item->get_title() );
							throw new Exception( $notice );
						} elseif ( $step_quantity > 1 && $item_quantity % $step_quantity ) {
							// translators: %s is the product title. %d is the step quantity of the child product.
							$notice = sprintf( __( 'The configuration you have selected cannot be added to the cart since you must select &quot;%1$s&quot; in quantities of %2$d.', 'woocommerce-mix-and-match-products' ), $mnm_item->get_title(), $step_quantity );
							throw new Exception( $notice );
						}

						// Stock management.
						if ( $mnm_item->is_type( 'variation' ) ) {
							$mnm_stock->add_item( $mnm_item->get_parent_id(), $id, $quantity );
						} else {
							$mnm_stock->add_item( $id, false, $quantity );
						}

						/**
						 * Individual item validation.
						 *
						 * @param bool $is_valid
						 * @param obj $container WC_Product_Mix_and_Match of parent container.
						 * @param obj $mnm_item WC_Product of child item.
						 * @param int $item_quantity Quantity of child item.
						 * @param int $container_quantity Quantity of parent container.
						 */
						if ( ! apply_filters( 'woocommerce_mnm_item_add_to_cart_validation', true, $container, $mnm_item, $item_quantity, $container_quantity ) ) {
							$is_configuration_valid = false;
							break;
						}

					} // End foreach.

				}

				if ( $is_configuration_valid ) {

					// The number of items allowed to be in the container.
					$min_container_size = $container->get_min_container_size();
					$max_container_size = $container->get_max_container_size();

					// Validate the max number of items in the container.
					if ( $max_container_size > 0 && $total_items_in_container > $max_container_size ) {
						// translators: %1$d is the maximum container quantity. %2$s is the container product title.
						$notice = sprintf( _n( 'You have selected too many items. Please choose %1$d item for &quot;%2$s&quot;.', 'You have selected too many items. Please choose %1$d items for &quot;%2$s&quot;.', $max_container_size, 'woocommerce-mix-and-match-products' ), $max_container_size, $container->get_title() );
						throw new Exception( $notice );
					}

					// Validate the min number of items in the container.
					if ( $min_container_size > 0 && $total_items_in_container < $min_container_size ) {
						// translators: %1$d is the minimum container quantity. %2$s is the container product title.
						$notice = sprintf( _n( 'You have selected too few items. Please choose %1$d item for &quot;%2$s&quot;.', 'You have selected too few items. Please choose %1$d items for &quot;%2$s&quot;.', $min_container_size, 'woocommerce-mix-and-match-products' ), $min_container_size, $container->get_title() );
						throw new Exception( $notice );
					}

					// Check stock for stock-managed bundled items when adding to cart. If out of stock, don't proceed.
					if ( 'add-to-cart' === $context ) {
						$is_configuration_valid = $mnm_stock->validate_stock( array(
							'context'         => $context,
							'throw_exception' => true
						) );
					}

					/**
					 * Perform additional validation checks at container level.
					 *
					 * @param  boolean                  $result
					 * @param  WC_Product_Mix_and_Match $container
					 * @param  WC_MNM_Stock_Manager     $mnm_stock
					 * @param  array                    $configuration
					 * @since  1.9.0
					 */
					$is_configuration_valid = apply_filters( 'woocommerce_mnm_' . str_replace( '-', '_', $context ) . '_container_validation', $is_configuration_valid, $container, $mnm_stock, $configuration );

					/**
					 * Validate the container.
					 *
					 * @deprecated 1.9.0
					 *
					 * @param bool $is_valid
					 * @param obj WC_Mix_and_Match_Stock_Manager $mnm_stock
					 * @param obj WC_Product_Mix_and_Match $container
					 */
					$is_configuration_valid = apply_filters( 'woocommerce_mnm_add_to_cart_validation', $is_configuration_valid, $mnm_stock, $container );


				}

			} catch ( Exception $e ) {

				/**
				 * Change the quantity error message.
				 *
				 * @param str $error_message
				 * @param obj WC_Mix_and_Match_Stock_Manager $cart_item_data
				 * @param obj WC_Product_Mix_and_Match $container
				 */
				$notice = apply_filters( 'woocommerce_mnm_container_quantity_error_message', $e->getMessage(), $mnm_stock, $container );

				if ( $notice ) {
					wc_add_notice( $notice, 'error' );
				}

				$is_configuration_valid = false;

			}

			return $is_configuration_valid;

		}

	}


	/**
	 * Redirect to the cart when editing a container "in-cart".
	 *
	 * @since   1.4.0
	 * @param  string  $url
	 * @return string
	 */
	public function edit_in_cart_redirect( $url ) {
		return wc_get_cart_url();
	}


	/**
	 * Filter the displayed notice after redirecting to the cart when editing a container "in-cart".
	 *
	 * @since   1.4.0
	 * @param  string  $url
	 * @return string
	 */
	public function edit_in_cart_redirect_message( $message ) {
		return __( 'Cart updated.', 'woocommerce-mix-and-match-products' );
	}


	/**
	 * Adds configuration-specific cart-item data.
	 *
	 * @param  array  $cart_item_data
	 * @param  int 	  $product_id
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $product_id ) {

		// Get product type.
		$product_type = WC_Product_Factory::get_product_type( $product_id );

		// Support prefixes on the quantity input name.
		$quantity_field = wc_mnm_get_child_input_name( $product_id );

		if ( 'mix-and-match' === $product_type ) {

			// Updating container in cart?
			if ( isset( $_POST[ 'update-container' ] ) ) {

				$updating_cart_key = wc_clean( $_POST[ 'update-container' ] );

				if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {

					// Remove.
					WC()->cart->remove_cart_item( $updating_cart_key );

					// Redirect to cart.
					add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'edit_in_cart_redirect' ) );

					// Edit notice.
					add_filter( 'wc_add_to_cart_message_html', array( $this, 'edit_in_cart_redirect_message' ) );
				}
			}

			// Create a unique array with the mnm configuration.
			if( ! isset( $cart_item_data[ 'mnm_config' ] ) ) {

				$config = array();

				$configuration = $this->get_posted_container_configuration( $product_id );

				foreach ( $configuration as $child_item_id => $child_item_configuration ) {

					/**
					 * 'woocommerce_mnm_child_item_cart_item_identifier' filter.
					 *
					 * Filters the config data array - use this to add any container-specific data that should result in unique container item ids being produced when the input data changes, such as add-ons data.
					 *
					 * @param  array  $posted_item_config
					 * @param  int    $child_item_id
					 * @param  mixed  $product_id
					 */
					$configuration[ $child_item_id ] = apply_filters( 'woocommerce_mnm_child_item_cart_item_identifier', $child_item_configuration, $child_item_id, $product_id );
				}
		
				// Add the array to the container item's data.
				$cart_item_data[ 'mnm_config' ] = $configuration;

			}

			// Add an empty contents array to the item's data.
			if( ! isset( $cart_item_data[ 'mnm_contents' ] ) ) {
				$cart_item_data[ 'mnm_contents' ] = array();
			}

		}

		return $cart_item_data;

	}


	/**
	 * Modifies mnm cart item virtual status and price depending on pricing and shipping options.
	 *
	 * @param  array                     $cart_item
	 * @param  WC_Product_Mix_and_Match  $parent
	 * @return array
	 */
	private function set_mnm_cart_item( $cart_item, $parent ) {

		// If the container has a dynamic price, potentially discount item.
		if ( $parent->is_priced_per_product() ) {
			$parent->maybe_apply_discount_to_child( $cart_item['data'] );
			// If the container has a static price, set item's price to zero.
		} else {
			$cart_item[ 'data' ]->set_price( 0 );
			$cart_item[ 'data' ]->set_regular_price( 0 );
			$cart_item[ 'data' ]->set_sale_price( '' );
		}

		// If is not shipped individually, mark it as virtual and save weight to be optionally added to the container.
		if ( $cart_item[ 'data' ]->needs_shipping() ) {

			$item_id = $cart_item[ 'variation_id' ] > 0 ? $cart_item[ 'variation_id' ] : $cart_item[ 'product_id' ];

			/**
			 * Is child item shipped individually or as part of container.
			 *
			 * @param bool $per_product_shipping
			 * @param obj WC_Product  $cart_item['data']
			 * @param  int $item_id Product or Variation ID of child item.
			 * @param obj WC_Product_Mix_and_Match $parent Product object of parent container.
			 */
			if ( false === apply_filters( 'woocommerce_mnm_item_shipped_individually', $parent->is_shipped_per_product(), $cart_item[ 'data' ], $item_id, $parent ) ) {

				/**
				 * Does the child item have weight?
				 *
				 * @param bool $has_weight
				 * @param obj WC_Product  $cart_item['data']
				 * @param  int $item_id Product or Variation ID of child item.
				 * @param obj WC_Product_Mix_and_Match $parent Product object of parent container.
				 */
				if ( apply_filters( 'woocommerce_mnm_item_has_bundled_weight', false, $cart_item[ 'data' ], $item_id, $parent ) ) {
					$cart_item[ 'data' ]->bundled_weight = $cart_item[ 'data' ]->get_weight( 'edit' );
				}

				$cart_item[ 'data' ]->bundled_value = $cart_item[ 'data' ]->get_price( 'edit' );

				$cart_item[ 'data' ]->set_virtual( 'yes' );
				$cart_item[ 'data' ]->set_weight( '' );
			}
		}

		/**
		 * Allow the child item to be modified by other plugins.
		 *
		 * @param array $cart_item
		 * @param obj WC_Product_Mix_and_Match $parent Product object of parent container.
		 */
		return apply_filters( 'woocommerce_mnm_cart_item', $cart_item, $parent );
	}


	/**
	 * Modifies MNM cart item data. Container price is equal to the base price in Per-Item Pricing mode.
	 *
	 * @since  1.4.0
	 *
	 * @param array $cart_item
	 * @return array $cart_item
	 */
	private function set_mnm_container_cart_item( $cart_item ) {
		$container = $cart_item[ 'data' ];
		/**
		 * Allow MNM container cart item data to be modified
		 *
		 * @param array $cart_item
		 * @param obj WC_Product_Mix_and_Match $container Product object of parent container.
		 */
		return apply_filters( 'woocommerce_mnm_container_cart_item', $cart_item, $container );
	}


	/**
	 * Modifies MNM cart item data.
	 * Important for the first calculation of totals only.
	 *
	 * @param  array 	$cart_item
	 * @param  string 	$cart_item_key
	 * @return array
	 */
	public function add_cart_item_filter( $cart_item, $cart_item_key ) {

		$cart_contents = WC()->cart->get_cart();

		// If item is mnm container.
		if ( wc_mnm_is_container_cart_item( $cart_item ) ) {
			$cart_item = $this->set_mnm_container_cart_item( $cart_item );
		}

		// If part of mnm container.
		if ( $container_cart_key = wc_mnm_get_cart_item_container( $cart_item, $cart_contents, true ) ) {

			if ( WC()->cart->find_product_in_cart( $container_cart_key ) ) {

				$parent    = $cart_contents[ $container_cart_key ][ 'data' ];
				$cart_item = $this->set_mnm_cart_item( $cart_item, $parent );

				// Add item key to parent items.
				array_push( WC()->cart->cart_contents[ $container_cart_key ][ 'mnm_contents' ], $cart_item_key );
			}
		}

		return $cart_item;
	}


	/**
	 * Load all MnM-related session data.
	 *
	 * @param  array 	$cart_item
	 * @param  array 	$cart_session_item
	 * @param  string 	$key
	 */
	public function get_cart_data_from_session( $cart_item, $cart_session_item, $key ) {

		// Parent container config.
		if ( isset( $cart_session_item[ 'mnm_config' ] ) ) {
			$cart_item[ 'mnm_config' ] = $cart_session_item[ 'mnm_config' ];
		}

		// Cart keys of items in parent container.
		if ( wc_mnm_is_container_cart_item( $cart_session_item ) ) {

			if ( $cart_item[ 'data' ]->is_type( 'mix-and-match' ) ) {

				if ( ! isset( $cart_item[ 'mnm_contents' ] ) ) {
					$cart_item[ 'mnm_contents' ] = $cart_session_item[ 'mnm_contents' ];
				}

				$cart_item = $this->set_mnm_container_cart_item( $cart_item );

			} else {

				if ( isset( $cart_item[ 'mnm_contents' ] ) ) {
					unset( $cart_item[ 'mnm_contents' ] );
				}
			}

		}

		// Child items.
		if ( wc_mnm_maybe_is_child_cart_item( $cart_session_item ) ) {

			$container_cart_key = $cart_session_item[ 'mnm_container' ];
			$cart_contents      = WC()->cart->cart_contents;

			$cart_item_container = wc_mnm_get_cart_item_container( $cart_session_item );

			if ( $cart_item_container ) {

				$container = $cart_item_container[ 'data' ];

				if ( $container->is_type( 'mix-and-match' ) ) {
					$cart_item = $this->set_mnm_cart_item( $cart_item, $container );
				}

			}

		}

		return $cart_item;
	}

	/**
	 * Ensure any child cart items have a valid parent. If not, silently remove them.
	 *
	 * @since  1.9.4
	 *
	 * @param  WC_Cart  $cart
	 * @return void
	 */
	public function cart_loaded_from_session( $cart ) {

		if ( empty( $cart->cart_contents ) ) {
			return;
		}

		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) ) {

				// Remove orphaned child items from the cart.
				$container_item = wc_mnm_get_cart_item_container( $cart_item );

				$child_id = $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'];

				if ( ! $container_item || ! is_array( $container_item['mnm_contents'] ) || ! in_array( $cart_item_key, $container_item['mnm_contents'] ) ) {
					unset( WC()->cart->cart_contents[ $cart_item_key ] );
				} elseif ( $container_item[ 'data' ]->is_type( 'mix-and-match' ) && ! array_key_exists( $child_id, $container_item[ 'data' ]->get_children() ) ) {
					unset( WC()->cart->cart_contents[ $cart_item_key ] );
				}

			}
		}
	}

	/**
	 * Keeps MNM item quantities in sync with container item.
	 *
	 * @param  string  $cart_item_key
	 * @param  integer $quantity
	 */
	public function update_quantity_in_cart( $cart_item_key, $quantity = 0 ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

			$mnm_container = WC()->cart->cart_contents[ $cart_item_key ];

			$mnm_contents = ! empty( $mnm_container[ 'mnm_contents' ] ) ? $mnm_container[ 'mnm_contents' ] : '';

			if ( ! empty( $mnm_contents ) ) {

				$container_quantity = ( $quantity == 0 || $quantity < 0 ) ? 0 : $mnm_container[ 'quantity' ];

				// Change the quantity of all MnM items that belong to the same config.
				foreach ( $mnm_contents as $mnm_child_key ) {

					if( ! isset( WC()->cart->cart_contents[ $mnm_child_key ] ) ) {
						continue;
					}

					$mnm_item = WC()->cart->cart_contents[ $mnm_child_key ];

					if ( $mnm_item[ 'data' ]->is_sold_individually() && $quantity > 0 ) {

						WC()->cart->set_quantity( $mnm_child_key, 1 );

					} else {

						// Get quantity per container from parent container config.
						$mnm_id = ! empty( $mnm_item[ 'variation_id' ] ) ? $mnm_item[ 'variation_id' ] : $mnm_item[ 'product_id' ];

						$child_qty_per_container = isset( $mnm_container[ 'mnm_config' ][ $mnm_id ][ 'quantity' ] ) ? $mnm_container[ 'mnm_config' ][ $mnm_id ][ 'quantity' ] : 0;

						WC()->cart->set_quantity( $mnm_child_key, $child_qty_per_container * $container_quantity  );
					}
				}
			}
		}
	}


	/**
	 * Do not show mix and matched items in cart widget.
	 *
	 * @param  bool 	$show
	 * @param  array 	$cart_item
	 * @param  string 	$cart_item_key
	 * @return bool
	 */
	public function cart_widget_filter( $show, $cart_item, $cart_item_key ) {

		if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) ) {
			$show = false;
		}

		return $show;
	}


	/**
	 * Filters the reported number of cart items.
	 * Counts only MnM containers.
	 *
	 * @param  int 	$count
	 * @return int
	 */
	public function cart_contents_count( $count ) {

		$cart_items = WC()->cart->get_cart();
		$subtract 	= 0;

		foreach ( $cart_items as $key => $cart_item ) {

			if ( wc_mnm_maybe_is_child_cart_item( $cart_item ) ) {
				$subtract += $cart_item[ 'quantity' ];
			}
		}

		return $count - $subtract;
	}


	/**
	 * MnM items can't be removed individually from the cart.
	 * This filter doesn't pass the $cart_item array for some reason.
	 *
	 * @param  string  $link
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_remove_link( $link, $cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $cart_item_key ][ 'mnm_container' ] ) && ! empty( WC()->cart->cart_contents[ $cart_item_key ][ 'mnm_container' ] ) ) {
			$link = '';
		}

		return $link;
	}


	/**
	 * Modifies the cart.php formatted quantity for items in the container.
	 *
	 * @param  string  $quantity
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item
	 * @return string
	 */
	public function cart_item_quantity( $quantity, $cart_item_key, $cart_item ) {

		if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {
			$quantity = $cart_item[ 'quantity' ];
		}

		return $quantity;
	}


	/**
	 * Modifies the cart.php formatted html prices visibility for items in the container.
	 *
	 * @param  string  $price
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_price( $price, $cart_item, $cart_item_key ) {

		// Child items.
		if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

			if( $container_cart_item[ 'data' ]->is_priced_per_product() ) {
				$price = '<span class="bundled_' . ( WC_Mix_and_Match()->display->is_cart_widget() ? 'mini_cart' : 'table' ) . '_item_price">' . $price . '</span>';
			} else {
				$price = '&nbsp;';
			}

			// Parent container.
		} else if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

			if ( $cart_item[ 'data' ]->is_priced_per_product() ) {

				$mnm_items_price     = 0;
				$mnm_container_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax ( $cart_item[ 'data' ] ) : wc_get_price_including_tax( $cart_item[ 'data' ] );

				foreach ( wc_mnm_get_child_cart_items( $cart_item ) as $mnm_item_key => $mnm_item ) {

					$child_item_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $mnm_item[ 'data' ], array( 'qty'   => $mnm_item[ 'quantity' ] ) ) : wc_get_price_including_tax( $mnm_item[ 'data' ], array( 'qty'   => $mnm_item[ 'quantity' ] ) );
					$mnm_items_price    += (double) $child_item_price;

				}

				$aggregate_price = $mnm_container_price + $mnm_items_price / $cart_item[ 'quantity' ];
				$price = wc_price( $aggregate_price );
			}
		}

		return $price;
	}


	/**
	 * Modifies the cart.php template formatted subtotal appearance.
	 *
	 * @param  string  $subtotal
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {

		// Child items.
		if ( $container_cart_item = wc_mnm_get_cart_item_container( $cart_item ) ) {

			if( $container_cart_item[ 'data' ]->is_priced_per_product() ) {
				// translators: %s is subtotal price.
				$subtotal = '<span class="bundled_' . ( WC_Mix_and_Match()->display->is_cart_widget() ? 'mini_cart' : 'table' ) . '_item_price">' . sprintf( __( 'Subtotal: %s', 'woocommerce-mix-and-match-products' ), $subtotal ) . '</span>';
			} else {
				$subtotal = '&nbsp;';
			}

			// Parent container.
		} else if ( wc_mnm_is_container_cart_item( $cart_item ) ) {

			if ( $cart_item[ 'data' ]->is_priced_per_product() ) {

				$mnm_items_price     = 0;
				$mnm_container_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $cart_item[ 'data' ], array( 'qty' => $cart_item[ 'quantity' ] ) ) : wc_get_price_including_tax( $cart_item[ 'data' ], array( 'qty' => $cart_item[ 'quantity' ] ) );

				foreach ( wc_mnm_get_child_cart_items( $cart_item ) as $mnm_item_key => $mnm_item ) {

					$child_item_price = get_option( 'woocommerce_tax_display_cart' ) == 'excl' ? wc_get_price_excluding_tax( $mnm_item[ 'data' ], array( 'qty' => $mnm_item[ 'quantity' ] ) ) : wc_get_price_including_tax( $mnm_item[ 'data' ], array( 'qty' => $mnm_item[ 'quantity' ] ) );
					$mnm_items_price    += (double) $child_item_price;

				}

				$aggregate_subtotal = (double) $mnm_container_price + $mnm_items_price;

				$subtotal = $this->format_product_subtotal( $cart_item[ 'data' ], $aggregate_subtotal );
			}
		}

		return $subtotal;
	}

	/**
	 * Outputs a formatted subtotal ( @see cart_item_subtotal() ).
	 * @static
	 * @param  obj     $product   The WC_Product.
	 * @param  string  $subtotal  Formatted subtotal.
	 * @return string             Modified formatted subtotal.
	 */
	public static function format_product_subtotal( $product, $subtotal ) {

		$cart = WC()->cart;
		$taxable = $product->is_taxable();
		$product_subtotal = wc_price( $subtotal );

		// Taxable.
		if ( $taxable ) {

			$tax_subtotal = WC_MNM_Core_Compatibility::is_wc_version_gte( '3.2' ) ? $cart->get_subtotal_tax() : $cart->tax_total;
			
			$cart_display_prices_including_tax = WC_MNM_Core_Compatibility::is_wc_version_gte( '3.3' ) ? $cart->display_prices_including_tax() : $cart->tax_display_cart === 'incl';

			if ( $cart_display_prices_including_tax ) {
				if ( ! wc_prices_include_tax() && $tax_subtotal > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
				}

			} else {
				if ( wc_prices_include_tax() && $tax_subtotal > 0 ) {
					$product_subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
				}
			}

		}

		return $product_subtotal;
	}


	/**
	 * Remove child cart items with parent.
	 *
	 * @param  string  $cart_item_key
	 * @param  obj WC_Cart $cart
	 */
	function cart_item_removed( $cart_item_key, $cart ) {

		if ( wc_mnm_is_container_cart_item( $cart->removed_cart_contents[ $cart_item_key ] ) ) {

			$child_item_cart_keys = wc_mnm_get_child_cart_items( $cart->removed_cart_contents[ $cart_item_key ], $cart->cart_contents, true );

			foreach ( $child_item_cart_keys as $child_item_cart_key ) {

				$remove = $cart->cart_contents[ $child_item_cart_key ];
				$cart->removed_cart_contents[ $child_item_cart_key ] = $remove;
				
				/**
				 * Remove item from cart.
				 * WC core action.
				 *
				 * @param str $mnm_cart_item_key
				 * @param obj WC_Cart $cart
				 */
				do_action( 'woocommerce_cart_item_removed', $child_item_cart_key, $cart );

				unset( $cart->cart_contents[ $child_item_cart_key ] );
			
			}
		}
	}


	/**
	 * Restore child cart items with parent.
	 *
	 * @param  string  $cart_item_key
	 * @param  WC_Cart $cart
	 */
	function cart_item_restored( $cart_item_key, $cart ) {

		if ( wc_mnm_is_container_cart_item( $cart->cart_contents[ $cart_item_key ] ) ) {

			$child_item_cart_keys = $cart->cart_contents[ $cart_item_key ][ 'mnm_contents' ];

			foreach ( $child_item_cart_keys as $child_item_cart_key ) {

				$remove = $cart->removed_cart_contents[ $child_item_cart_key ];
				$cart->cart_contents[ $child_item_cart_key ] = $remove;	

				/**
				 * Restore item tor cart.
				 * WC core action.
				 *
				 * @param str $mnm_cart_item_key
				 * @param obj WC_Cart $cart
				 */
				do_action( 'woocommerce_cart_item_restored', $child_item_cart_key, $cart );
				
				unset( $cart->removed_cart_contents[ $child_item_cart_key ] );
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

						if ( wc_mnm_is_container_cart_item( $cart_item_data ) ) {

							$container     = unserialize( serialize( $cart_item_data[ 'data' ] ) );
							$container_qty = $cart_item_data[ 'quantity' ];

							/*
							 * Container needs shipping: Sum the prices of any children that are not shipped individually into the parent and, optionally, add their weight to the parent weight.
							 */

							if ( $container->needs_shipping() ) {

								// Aggregate weights.
								$aggregate_weight = 0.0;

								// Aggregate prices.
								$aggregate_value = 0.0;

								$container_totals = array(
									'line_subtotal'     => $cart_item_data[ 'line_subtotal' ],
									'line_total'        => $cart_item_data[ 'line_total' ],
									'line_subtotal_tax' => $cart_item_data[ 'line_subtotal_tax' ],
									'line_tax'          => $cart_item_data[ 'line_tax' ],
									'line_tax_data'     => $cart_item_data[ 'line_tax_data' ]
								);

								foreach ( wc_mnm_get_child_cart_items( $cart_item_data, WC()->cart->cart_contents, true ) as $child_item_key ) {

									$child_cart_item_data = WC()->cart->cart_contents[ $child_item_key ];
									$child_product      = $child_cart_item_data[ 'data' ];
									$child_product_qty  = $child_cart_item_data[ 'quantity' ];

									// Aggregate price for the entire container.
									if ( isset( $child_product->bundled_value ) && $child_product->bundled_value ) {

										$aggregate_value += $child_product->bundled_value * $child_product_qty;

										$container_totals[ 'line_subtotal' ]     += $child_cart_item_data[ 'line_subtotal' ];
										$container_totals[ 'line_total' ]        += $child_cart_item_data[ 'line_total' ];
										$container_totals[ 'line_subtotal_tax' ] += $child_cart_item_data[ 'line_subtotal_tax' ];
										$container_totals[ 'line_tax' ]          += $child_cart_item_data[ 'line_tax' ];

										$packages[ $package_key ][ 'contents_cost' ] += $child_cart_item_data[ 'line_total' ];

										$child_item_line_tax_data = $child_cart_item_data[ 'line_tax_data' ];

										$container_totals[ 'line_tax_data' ][ 'total' ]    = array_merge( $container_totals[ 'line_tax_data' ][ 'total' ], $child_item_line_tax_data[ 'total' ] );
										$container_totals[ 'line_tax_data' ][ 'subtotal' ] = array_merge( $container_totals[ 'line_tax_data' ][ 'subtotal' ], $child_item_line_tax_data[ 'subtotal' ] );
									}

									// Aggregate weight for the entire container.
									if ( isset( $child_product->bundled_weight ) && $child_product->bundled_weight ) {
										$aggregate_weight += $child_product->bundled_weight * $child_product_qty;
									}
								}

								if ( $aggregate_value > 0 ) {
									$container_price = $container->get_price( 'edit' );
									$container->set_price( (double) $container_price + $aggregate_value / $container_qty );
								}

								if ( $aggregate_weight > 0 ) {
									$container_weight = $container->get_weight( 'edit' );
									$container->set_weight( (double) $container_weight + $aggregate_weight / $container_qty );
								}

								$packages[ $package_key ][ 'contents' ][ $cart_item_key ]           = array_merge( $cart_item_data, $container_totals );
								$packages[ $package_key ][ 'contents' ][ $cart_item_key ][ 'data' ] = $container;
							}
						}
					}
				}
			}
		}

		return $packages;
	}


	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Reinitialize cart item data for re-ordering purchased orders.
	 *
	 * @deprecated 1.4.0
	 *
	 * @param  mixed     $cart_item
	 * @param  mixed     $order_item
	 * @param  WC_Order  $order
	 * @return mixed
	 */
	public function order_again( $cart_item, $order_item, $order ) {
		wc_deprecated_function( 'WC_Mix_and_Match_Cart::order_again()', '1.4.0', ' WC_MNM_Order_Again::order_again_cart_item_data' );
		return WC_MNM_Order_Again::order_again_cart_item_data( $cart_item, $order_item, $order );
	}


	/**
	 * Find the parent of a child item in a cart.
	 * @deprecated 1.4.0
	 *
	 * @param  array  $item
	 * @return array
	 */
	public function get_bundled_cart_item_container_key( $item ) {
		wc_deprecated_function( 'WC_Mix_and_Match_Cart::get_bundled_cart_item_container_key()', '1.4.0', 'wc_mnm_get_cart_item_container' );
		return wc_mnm_get_cart_item_container( $item, false, true );
	}

} //End class.
