<?php
/**
 * Cart Functions and Filters
 *
 * @package  WooCommerce Mix and Match Products/Cart
 * @since    1.0.0
 * @version  2.2.0
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
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 3 );

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

		// Filter cart weight.
		add_filter( 'woocommerce_cart_contents_weight', array( $this, 'cart_contents_weight' ) );

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
	 * @param  string   $container_cart_key
	 * @param  int      $product_id
	 * @param  int      $quantity
	 * @param  int      $variation_id
	 * @param  array    $variation
	 * @param  array    $cart_item_data
	 */
	function add_mnm_items_to_cart( $container_cart_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

		if ( wc_mnm_is_container_cart_item( $cart_item_data ) ) {

			$mnm_cart_item_data = array(
				'mnm_container' => $container_cart_key,
				'mnm_child_id'  => $variation_id > 0 ? $variation_id : $product_id
			);

			// Now add all items - yay!
			foreach ( $cart_item_data['mnm_config'] as $item_id => $mnm_item_data ) {

				$mnm_product_id     = $mnm_item_data['product_id'];
				$mnm_variation_id   = isset( $mnm_item_data['variation_id'] ) ? $mnm_item_data['variation_id'] : 0;
				$mnm_variations     = isset( $mnm_item_data['variation'] ) ? $mnm_item_data['variation'] : array();

				$item_quantity      = $mnm_item_data['quantity'];
				$mnm_quantity       = $item_quantity * $quantity;

				// Add the child item ID.
				$mnm_cart_item_data[ 'child_item_id' ] = isset( $mnm_item_data[ 'child_item_id' ] ) ? $mnm_item_data[ 'child_item_id' ] : 0;

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
				$mnm_cart_item_data = (array) apply_filters( 'wc_mnm_child_cart_item_data', $mnm_cart_item_data, $cart_item_data, $item_id, $product_id );

				/**
				 * Before child item is added to cart.
				 *
				 * @param int $mnm_product_id The child item product ID.
				 * @param int $mnm_quantity The quantity of the child item in the container.
				 * @param int $mnm_variation_id The child item variation ID.
				 * @param array $mnm_variations Attributes of specific variation being added to cart.
				 * @param array $mnm_cart_item_data Child item product data.
				 */
				do_action( 'wc_mnm_before_mnm_add_to_cart', $mnm_product_id, $mnm_quantity, $mnm_variation_id, $mnm_variations, $mnm_cart_item_data );

				// Add to cart.
				$mnm_item_cart_key = $this->mnm_add_to_cart( $product_id, $mnm_product_id, $mnm_quantity, $mnm_variation_id, $mnm_variations, $mnm_cart_item_data );

				// Push child key to parent mnm_contents.
				if ( $mnm_item_cart_key && ! in_array( $mnm_item_cart_key, wc()->cart->cart_contents[ $container_cart_key ]['mnm_contents'] ) ) {
					wc()->cart->cart_contents[ $container_cart_key ]['mnm_contents'][] = $mnm_item_cart_key;
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
				do_action( 'wc_mnm_after_mnm_add_to_cart', $mnm_product_id, $mnm_quantity, $mnm_variation_id, $mnm_variations, $mnm_cart_item_data );

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
	public function mnm_add_to_cart( $container_id, $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data = array() ) {

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
			WC()->cart->cart_contents[ $cart_item_key ] = apply_filters(
				'woocommerce_add_cart_item',
				array_merge(
					$cart_item_data,
					array(
						'key'          => $cart_item_key,
						'product_id'   => absint( $product_id ),
						'variation_id' => absint( $variation_id ),
						'variation'    => $variation,
						'quantity'     => $quantity,
						'data'         => $product_data,
					)
				),
				$cart_item_key
			);

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
		do_action( 'wc_mnm_add_to_cart', $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data, $container_id );

		return $cart_item_key;
	}


	/**
	 * Build container configuration array from posted data. 
	 *
	 * @param  mixed  $product
	 * @param  array $config example: array(
	 * 		134 => 2 // product|variation_id => quantity.
	 * )
	 * @return array example:
	 *
	 *    $config = array(
	 *        134 => array(                             // ID of child item.
	 *            'child_item_id'     => 134,           // ID of child item.
	 *            'product_id'        => 15,            // ID of child product.
	 *            'quantity'          => 2,             // Qty of child product, will fall back to min.
	 *            'variation_id'      => 43             // ID of chosen variation, if applicable.
	 *            'variation'         => array( 'color' => 'blue' ) // Attributes of chosen variation.
	 *        )
	 *    );
	 */
	public function get_posted_container_configuration( $product, $config = array() ) {

		$posted_config = array();

		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}

		if ( is_object( $product ) && wc_mnm_is_product_container_type( $product ) ) {

			$product_id = $product->get_id();

			if ( $product->has_child_items() ) {

				$child_items = $product->get_child_items();

				/**
				* Choose between $_POST or $_GET for grabbing data.
				* We will not rely on $_REQUEST because checkbox names may not exist in $_POST but they may well exist in $_GET, for instance when editing a container from the cart.
				*/

				$posted_data = $_POST;

				if ( empty( $_POST['add-to-cart'] ) && ! empty( $_GET['add-to-cart'] ) ) {
					$posted_data = $_GET;
				}


				foreach ( $child_items as $child_item_id => $child_item ) {

					$child_product    = $child_item->get_product();
					$child_product_id = $child_product->get_id();

					// Check that a product has been selected.
					if ( ! empty( $config ) ) {
						$child_item_quantity = ! empty ( $config[ $child_product_id ] ) ? intval( $config[ $child_product_id ] ) : 0;						
					} else {
						$posted_field_name = ! empty( $config ) ? $child_product_id : $child_item->get_input_name( false );
						$child_item_quantity = ! empty( $posted_data[ $posted_field_name ] ) && ! empty( $posted_data[ $posted_field_name ][ $child_product_id ] ) ? intval( $posted_data[ $posted_field_name ][ $child_product_id ] ) : 0;
					}

					if ( $child_item_quantity <= 0 ) {
						continue;
					}

					// Build the configuration array.
					$posted_config[ $child_product_id ] = array();

					$parent_id = $child_product->get_parent_id();

					$posted_config[ $child_product_id ]['child_item_id'] = $child_item->get_child_item_id();
					$posted_config[ $child_product_id ]['mnm_child_id']  = $child_product_id;
					$posted_config[ $child_product_id ]['product_id']    = $parent_id > 0 ? $parent_id : $child_product->get_id();
					$posted_config[ $child_product_id ]['variation_id']  = $parent_id > 0 ? $child_product->get_id() : 0;
					$posted_config[ $child_product_id ]['quantity']      = $child_item_quantity;
					$posted_config[ $child_product_id ]['variation']     = $parent_id > 0 ? $child_product->get_variation_attributes() : array();

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
		return (array) apply_filters( 'wc_mnm_get_posted_container_configuration', $posted_config, $product );
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
	public function rebuild_posted_container_form_data( $configuration = array(), $container = null ) {
		$form_data = array();

		if ( ! empty( $configuration ) ) {
			foreach ( $configuration as $mnm_item_id => $item_config ) {
				$form_data[ $mnm_item_id ] = isset( $item_config['quantity'] ) ? intval( $item_config['quantity'] ) : 0;
			}
		}

		// Return the array as mnm_quantity = array() if $container is passed.
		if ( wc_mnm_is_product_container_type( $container ) ) {
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
		return (array) apply_filters( 'wc_mnm_get_posted_container_form_data', $form_data, $configuration, $container );

	}


	/**
	 * Validates that all MnM items chosen can be added-to-cart before actually starting to add items.
	 *
	 * @param  bool $passed_validation
	 * @param  int  $product_id
	 * @param  int  $quantity
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
			if ( isset( $cart_item_data['is_order_again_mnm_item'] ) ) {
				return false;
			}
		}

		$the_id = $variation_id ? $variation_id : $product_id;

		$product_type = WC_Product_Factory::get_product_type( $the_id );

		if ( wc_mnm_is_product_container_type( $product_type ) ) {

			$container = wc_get_product( $the_id );

			if ( false === $this->validate_container_add_to_cart( $container, $quantity, $cart_item_data ) ) {
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
		 * 'wc_mnm_before_container_validation' filter.
		 *
		 * Early chance to stop/bypass any further validation.
		 *
		 * @param  boolean            $true
		 * @param  WC_Product_Mix_and_Match  $container
		 */
		if ( apply_filters( 'wc_mnm_before_container_validation', true, $container ) ) {

			$configuration = isset( $cart_item_data['mnm_config'] ) ? $cart_item_data['mnm_config'] : $this->get_posted_container_configuration( $container );

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
	 * @param  bool     $passed_validation
	 * @param  int      $cart_item_key
	 * @param  array    $values
	 * @param  int      $quantity
	 * @return bool
	 */
	public function update_cart_validation( $passed_validation, $cart_item_key, $values, $product_quantity ) {

		$product = $values['data'];

		if ( ! $product ) {
			return false;
		}

		// Don't check child items individually, will be checked by parent container.
		if ( wc_mnm_maybe_is_child_cart_item( $values ) ) {
			return $passed_validation;
		}

		if ( wc_mnm_is_product_container_type( $product ) && wc_mnm_is_container_cart_item( $values ) ) {

			$existing_quantity   = $values['quantity'];
			$additional_quantity = $product_quantity - $existing_quantity;

			$passed_validation = $this->validate_container_configuration( $product, $additional_quantity, $values['mnm_config'], 'cart' );

		}

		return $passed_validation;
	}


	/**
	 * Check container cart item configurations on cart load.
	 */
	public function check_cart_items() {

		foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {

			if ( wc_mnm_is_container_cart_item( $cart_item ) ) {
				$this->validate_container_in_cart( $cart_item );
			}
		}
	}


	/**
	 * Check container cart item configuration and children.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $cart_item
	 */
	public function validate_container_in_cart( $cart_item ) {

		$configuration = isset( $cart_item['mnm_config'] ) ? $cart_item['mnm_config'] : $this->get_posted_container_configuration( $cart_item['data'] );

		// Re-validate, though unlikely to have changed config.
		$this->validate_container_configuration( $cart_item['data'], $cart_item['quantity'], $configuration, array( 'context' => 'cart' ) );

		// Check child cart items are actually still in cart.
		if ( count( $configuration ) !== count( wc_mnm_get_child_cart_items( $cart_item, wc()->cart->cart_contents, true ) ) ) {
			$notice = sprintf( esc_html_x( 'Sorry, the configuration for "%s" is no longer valid. Please edit your cart and try again.', '[Frontend]', 'woocommerce-mix-and-match-products' ), $cart_item['data']->get_name() );
			wc_add_notice( $notice, 'error' );
		}

	}



	/**
	 * Validates add to cart for MNM containers.
	 * Basically ensures that stock for all child products exists before attempting to add them to cart.
	 *
	 * @throws Exception
	 *
	 * @since  1.4.0
	 *
	 * @param  mixed   $container int|WC_Product_Mix_and_Match
	 * @param  int     $container_quantity
	 * @param  array   $configuration
	 * @see            get_posted_container_configuration() for array details.
	 * @param  array|string  $args
	 * @return boolean
	 */
	public function validate_container_configuration( $container, $container_quantity, $configuration, $args = array() ) {

		$defaults = array(
			'context'         => is_string( $args ) ? $args : '', // Back in the day, args was a string and was used to pass context. Possible values: 'add-to-cart'|'add-to-order'|'cart'
			'throw_exception' => WC_MNM_Core_Compatibility::is_api_request() // Do not add a notice in Rest/Store API requests, unless otherwise instructed.
		);

		$args = wp_parse_args( $args, $defaults );

		$is_valid = true;

		// Count the total child items.
		$total_items_in_container = 0;

		if ( is_numeric( $container ) ) {
			$container = wc_get_product( $container );
		}

		if ( is_object( $container ) && wc_mnm_is_product_container_type( $container ) ) {

			try {

				/**
				 * 'wc_mnm_container_validation_context' filter.
				 *
				 * @since  1.9.0
				 *
				 * @param  string                   $context
				 * @param  WC_Product_Mix_and_Match $container
				 */
				$context = '' === $args[ 'context' ] ? apply_filters( 'wc_mnm_container_validation_context', 'add-to-cart', $container ) : $args[ 'context' ];

				$container_id    = $container->get_id();
				$container_title = $container->get_title();

				// If a stock-managed product / variation exists in the container multiple times, its stock will be checked only once for the sum of all child quantities.
				// The stock manager class keeps a record of stock-managed product / variation ids.
				$mnm_stock = new WC_Mix_and_Match_Stock_Manager( $container );

				if ( $container->has_child_items() ) {

					// Grab child items.
					$child_items = $container->get_child_items();

					// Loop through the items.
					foreach ( $child_items as $child_item_id => $child_item ) {

						$child_product    = $child_item->get_product();
						$child_product_id = $child_product->get_id();

						// Check that a product has been selected.
						if ( isset( $configuration[ $child_product_id ] ) && $configuration[ $child_product_id ] !== '' ) {
							$item_quantity = $configuration[ $child_product_id ]['quantity'];
							// If the ID isn't in the posted data something is rotten in Denmark.
						} else {
							continue;
						}

						// Total quantity in single container.
						$total_items_in_container += $item_quantity;

						// Total quantity of items in all containers: for stock purposes.
						$quantity = $item_quantity * $container_quantity;

						// Product is_purchasable - only for per item pricing.
						if ( $container->is_priced_per_product() && ! $child_product->is_purchasable() ) {
							// translators: %s is the product title.
							$notice = sprintf( _x( 'The configuration you have selected cannot be added to the cart since &quot;%s&quot; cannot be purchased.', '[Frontend]', 'woocommerce-mix-and-match-products' ), $child_product->get_title() );
							throw new Exception( $notice );
						}

						// Check individual min/max quantities.
						$min_quantity  = $child_item->get_quantity( 'min' );
						$max_quantity  = $child_item->get_quantity( 'max' );
						$step_quantity = $child_item->get_quantity( 'step' );

						if ( $max_quantity && $item_quantity > $max_quantity ) {
							// translators: %1ds is the maximum quantity of the child product. %2$s is the product title.
							$notice = sprintf( _x( 'The configuration you have selected cannot be added to the cart since you cannot select more than %1$d of &quot;%2$s&quot;.', '[Frontend]', 'woocommerce-mix-and-match-products' ), $max_quantity, $child_product->get_title() );
							throw new Exception( $notice );
						} elseif ( $min_quantity && $item_quantity < $min_quantity ) {
							// translators: %1$d is the minimum quantity of the child product. %2$s is the product title.
							$notice = sprintf( _x( 'The configuration you have selected cannot be added to the cart since you must select at least %1$d of &quot;%2$s&quot;.', '[Frontend]', 'woocommerce-mix-and-match-products' ), $min_quantity, $child_product->get_title() );
							throw new Exception( $notice );
						} elseif ( $step_quantity > 1 && $item_quantity % $step_quantity ) {
							// translators: %1$s is the product title. %2$d is the step quantity of the child product.
							$notice = sprintf( _x( 'The configuration you have selected cannot be added to the cart since you must select &quot;%1$s&quot; in quantities of %2$d.', '[Frontend]', 'woocommerce-mix-and-match-products' ), $child_product->get_title(), $step_quantity );
							throw new Exception( $notice );
						}

						// Stock management.
						$mnm_stock->add_item( $child_item->get_product_id(), $child_item->get_variation_id(), $quantity );

						/**
						 * Individual item validation.
						 *
						 * @param bool $is_valid
						 * @param obj $container WC_Product_Mix_and_Match of parent container.
						 * @param obj $child_item WC_MNM_Child_Item of child item.
						 * @param int $item_quantity Quantity of child item.
						 * @param int $container_quantity Quantity of parent container.
						 */
						$is_valid = apply_filters( 'wc_mnm_child_item_' . str_replace( '-', '_', $context ) . '_validation', true, $container, $child_item, $item_quantity, $container_quantity );

						if ( has_filter( 'woocommerce_mnm_item_add_to_cart_validation' ) ) {

							wc_deprecated_function( 'woocommerce_mnm_item_add_to_cart_validation', '2.0.0', 'wc_mnm_child_item_add_to_cart_validation, nb: 3rd param will be WC_MNM_Child_Item object.' );
							/**
							 * Individual item validation.
							 *
							 * @deprecated - 2.0.0
							 *
							 * @param bool $is_valid
							 * @param obj $container WC_Product_Mix_and_Match of parent container.
							 * @param obj $child_product WC_Product of child item.
							 * @param int $item_quantity Quantity of child item.
							 * @param int $container_quantity Quantity of parent container.
							 */
							$is_valid = apply_filters( 'woocommerce_mnm_item_add_to_cart_validation', $is_valid, $container, $child_product, $item_quantity, $container_quantity );
						}

						if ( ! $is_valid ) {
							break;
						}
					} // End foreach.
				}

				// The number of items allowed to be in the container.
				$min_container_size = $container->get_min_container_size();
				$max_container_size = $container->get_max_container_size();

				// Validate the max number of items in the container.
				if ( $is_valid && $max_container_size > 0 && $total_items_in_container > $max_container_size ) {
					$is_valid = false;
					// translators: %1$d is the maximum container quantity. %2$s is the container product title.
					$notice = sprintf( _n( 'You have selected too many items. Please choose %1$d item for &quot;%2$s&quot;.', 'You have selected too many items. Please choose %1$d items for &quot;%2$s&quot;.', $max_container_size, 'woocommerce-mix-and-match-products' ), $max_container_size, $container->get_title() );
				}

				// Validate the min number of items in the container.
				if ( $is_valid && $min_container_size > 0 && $total_items_in_container < $min_container_size ) {
					$is_valid = false;
					// translators: %1$d is the minimum container quantity. %2$s is the container product title.
					$notice = sprintf( _n( 'You have selected too few items. Please choose %1$d item for &quot;%2$s&quot;.', 'You have selected too few items. Please choose %1$d items for &quot;%2$s&quot;.', $min_container_size, 'woocommerce-mix-and-match-products' ), $min_container_size, $container->get_title() );
				}

				// Check stock for stock-managed child items when adding to cart. If out of stock, don't proceed.
				if ( $is_valid && 'add-to-cart' === $context ) {
					$is_valid = $mnm_stock->validate_stock(
						array(
						'context'         => $context,
						'throw_exception' => true
						)
					);
				}

				/**
				 * Perform additional validation checks at container level.
				 *
				 * @param  boolean                  $result
				 * @param  WC_Product_Mix_and_Match $container
				 * @param  WC_MNM_Stock_Manager     $mnm_stock
				 * @param  array                    $configuration
				 * @since  2.0.0
				 */
				$is_valid = apply_filters( 'wc_mnm_' . str_replace( '-', '_', $context ) . '_container_validation', $is_valid, $container, $mnm_stock, $configuration );

				if ( has_filter( 'woocommerce_mnm_add_to_cart_validation' ) ) {

					wc_deprecated_function( 'woocommerce_mnm_add_to_cart_validation', '2.0.0', 'wc_mnm_child_item_add_to_cart_validation, nb: parameters have switched order.' );

					/**
					 * Validate the container.
					 *
					 * @deprecated 1.9.0
					 *
					 * @param bool $is_valid
					 * @param obj WC_Mix_and_Match_Stock_Manager $mnm_stock
					 * @param obj WC_Product_Mix_and_Match $container
					 */
					$is_valid = apply_filters( 'woocommerce_mnm_add_to_cart_validation', $is_valid, $mnm_stock, $container );

				}

				/**
				 * Late Exception for containers allows for alternative validations.
				 * Example "by weight" or "by price" where the quantity validations shouldn't throw a notice.
				 */
				if ( ! $is_valid ) {
					throw new Exception( $notice );
				}

			} catch ( Exception $e ) {

				/**
				 * Change the quantity error message.
				 *
				 * @param str $error_message
				 * @param obj WC_Mix_and_Match_Stock_Manager $cart_item_data
				 * @param obj WC_Product_Mix_and_Match $container
				 */
				$notice = apply_filters( 'wc_mnm_container_quantity_error_message', $e->getMessage(), $mnm_stock, $container );

				if ( $args[ 'throw_exception' ] ) {

					throw new Exception( $notice );

				} else {

					if ( $notice ) {
						wc_add_notice( $notice, 'error' );
					}

					$is_valid = false;

				}

			}

			return $is_valid;

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
		return _x( 'Cart updated.', '[Frontend]', 'woocommerce-mix-and-match-products' );
	}


	/**
	 * Adds configuration-specific cart-item data.
	 *
	 * @param  array  $cart_item_data
	 * @param  int    $product_id
	 * @param int $variation_id Child item's variation ID.
	 * @return array
	 */
	public function add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {

		$the_id = $variation_id ? $variation_id : $product_id;

		// Get product type.
		$product_type = WC_Product_Factory::get_product_type( $the_id );

		// Support prefixes on the quantity input name.
		$quantity_field = wc_mnm_get_child_input_name( $the_id );

		if ( wc_mnm_is_product_container_type( $product_type ) ) {

			// Updating container in cart?
			if ( isset( $_POST['update-container'] ) ) {

				$updating_cart_key = wc_clean( $_POST['update-container'] );

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
			if ( ! isset( $cart_item_data['mnm_config'] ) ) {

				$config = array();

				$configuration = $this->get_posted_container_configuration( $the_id );

				foreach ( $configuration as $child_item_id => $child_item_configuration ) {

					/**
					 * 'wc_mnm_child_item_cart_item_identifier' filter.
					 *
					 * Filters the config data array - use this to add any container-specific data that should result in unique container item ids being produced when the input data changes, such as add-ons data.
					 *
					 * @param  array  $posted_item_config
					 * @param  int    $child_item_id
					 * @param  mixed  $the_id
					 */
					$configuration[ $child_item_id ] = apply_filters( 'wc_mnm_child_item_cart_item_identifier', $child_item_configuration, $child_item_id, $the_id );
				}

				// Add the array to the container item's data.
				$cart_item_data['mnm_config'] = $configuration;

			}

			// Add an empty contents array to the item's data.
			if ( ! isset( $cart_item_data['mnm_contents'] ) ) {
				$cart_item_data['mnm_contents'] = array();
			}
		}

		return $cart_item_data;

	}


	/**
	 * Modifies mnm cart item virtual status and price depending on pricing and shipping options.
	 *
	 * @param  array                     $cart_item
	 * @param  WC_Product_Mix_and_Match  $container
	 * @return array
	 */
	private function set_mnm_cart_item( $cart_item, $container ) {

		$child_item = $container->get_child_item_by_product_id( $cart_item[ 'variation_id' ] ? $cart_item[ 'variation_id' ] : $cart_item[ 'product_id' ] );

		if ( ! $child_item ) {
			return $cart_item;
		}

		$discount_method = WC_MNM_Product_Prices::get_discount_method();

		$cart_item['data']->mnm_child_item = $child_item;

		if ( 'props' === $discount_method ) {

			// If container is static-priced, the child products have 0 price.
			if ( ! $container->is_priced_per_product() ) {

				$cart_item['data']->set_price( 0 );
				$cart_item['data']->set_regular_price( 0 );
				$cart_item['data']->set_sale_price( '' );

				// If the container has a dynamic price, potentially discount item.
			} elseif ( $child_item && $child_item->has_discount() ) {
				$cart_item['data']->set_price( $child_item->get_raw_price( $cart_item[ 'data' ], 'cart' ) );
				$cart_item['data']->set_sale_price( $child_item->get_raw_price( $cart_item[ 'data' ], 'cart' ) );
			}

		}

		// If is not shipped individually, mark it as virtual and save weight to be optionally added to the container.
		if ( $cart_item['data']->needs_shipping() ) {

			$item_id = $cart_item['variation_id'] > 0 ? $cart_item['variation_id'] : $cart_item['product_id'];

			/**
			 * Is child item shipped individually or as part of container.
			 *
			 * @param bool $per_product_shipping
			 * @param obj WC_Product  $cart_item['data']
			 * @param  int $item_id Product or Variation ID of child item.
			 * @param obj WC_Product_Mix_and_Match $container Product object of parent container.
			 */
			if ( ! apply_filters( 'wc_mnm_child_item_shipped_individually', ! $container->is_packed_together(), $cart_item['data'], $item_id, $container ) ) {

				/**
				 * Does the child item add weight?
				 *
				 * @param bool $has_weight
				 * @param obj WC_Product  $cart_item['data']
				 * @param  int $item_id Product or Variation ID of child item.
				 * @param obj WC_Product_Mix_and_Match $container Product object of parent container.
				 */
				if ( apply_filters( 'wc_mnm_child_item_has_cumulative_weight', $container->is_weight_cumulative(), $cart_item['data'], $item_id, $container ) ) {
					$cart_item['data']->bundled_weight = $cart_item['data']->get_weight( 'edit' );
				}

				$cart_item['data']->bundled_value = 'props' === $discount_method ? $cart_item[ 'data' ]->get_price( 'edit' ) : $child_item->get_raw_price( $cart_item[ 'data' ] );
				$cart_item['data']->set_virtual( true );
				$cart_item['data']->set_weight( '' );
			}
		}

		/**
		 * Allow the child item to be modified by other plugins.
		 *
		 * @param array $cart_item
		 * @param obj WC_Product_Mix_and_Match $container Product object of parent container.
		 */
		return apply_filters( 'wc_mnm_child_cart_item', $cart_item, $container );
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
		$container = $cart_item['data'];

		/**
		 * Allow MNM container cart item data to be modified
		 *
		 * @param array $cart_item
		 * @param obj WC_Product_Mix_and_Match $container Product object of parent container.
		 */
		return apply_filters( 'wc_mnm_container_cart_item', $cart_item, $container );
	}


	/**
	 * Modifies MNM cart item data.
	 * Important for the first calculation of totals only.
	 *
	 * @param  array    $cart_item
	 * @param  string   $cart_item_key
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

				$parent    = $cart_contents[ $container_cart_key ]['data'];
				$cart_item = $this->set_mnm_cart_item( $cart_item, $parent );

			}
		}

		return $cart_item;
	}


	/**
	 * Load all MnM-related session data.
	 *
	 * @param  array    $cart_item
	 * @param  array    $cart_session_item
	 * @param  string   $key
	 */
	public function get_cart_data_from_session( $cart_item, $cart_session_item, $key ) {

		// Parent container config.
		if ( isset( $cart_session_item['mnm_config'] ) ) {
			$cart_item['mnm_config'] = $cart_session_item['mnm_config'];
		}

		// Cart keys of items in parent container.
		if ( wc_mnm_is_container_cart_item( $cart_session_item ) ) {

			if ( wc_mnm_is_product_container_type( $cart_item['data'] ) ) {

				if ( ! isset( $cart_item['mnm_contents'] ) ) {
					$cart_item['mnm_contents'] = $cart_session_item['mnm_contents'];
				}

				$cart_item = $this->set_mnm_container_cart_item( $cart_item );

			} else {

				if ( isset( $cart_item['mnm_contents'] ) ) {
					unset( $cart_item['mnm_contents'] );
				}
			}
		}

		// Child items. - @todo - store entire config on child items.
		if ( wc_mnm_maybe_is_child_cart_item( $cart_session_item ) ) {

			$container_cart_key = $cart_session_item['mnm_container'];
			$cart_contents      = WC()->cart->cart_contents;

			$cart_item_container = wc_mnm_get_cart_item_container( $cart_session_item );

			if ( $cart_item_container ) {

				$container = $cart_item_container['data'];

				if ( wc_mnm_is_product_container_type( $container ) ) {
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

				$child_product_id = $cart_item[ 'variation_id' ] ? $cart_item[ 'variation_id' ] : $cart_item[ 'product_id' ];

				if ( ! $container_item || ! is_array( $container_item['mnm_contents'] ) || ! in_array( $cart_item_key, $container_item['mnm_contents'] ) ) {
					unset( WC()->cart->cart_contents[ $cart_item_key ] );
				} elseif ( wc_mnm_is_product_container_type( $container_item['data'] ) && ! $container_item['data']->is_allowed_child_product( $child_product_id ) ) {
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

			$mnm_contents = ! empty( $mnm_container['mnm_contents'] ) ? $mnm_container['mnm_contents'] : '';

			if ( ! empty( $mnm_contents ) ) {

				$container_quantity = ( $quantity == 0 || $quantity < 0 ) ? 0 : $mnm_container['quantity'];

				// Change the quantity of all MnM items that belong to the same config.
				foreach ( $mnm_contents as $mnm_child_key ) {

					if ( ! isset( WC()->cart->cart_contents[ $mnm_child_key ] ) ) {
						continue;
					}

					$mnm_item = WC()->cart->cart_contents[ $mnm_child_key ];

					if ( $mnm_item['data']->is_sold_individually() && $quantity > 0 ) {

						WC()->cart->set_quantity( $mnm_child_key, 1 );

					} else {

						// Get quantity per container from parent container config.
						$mnm_id = ! empty( $mnm_item['variation_id'] ) ? $mnm_item['variation_id'] : $mnm_item['product_id'];

						$child_qty_per_container = isset( $mnm_container['mnm_config'][ $mnm_id ]['quantity'] ) ? $mnm_container['mnm_config'][ $mnm_id ]['quantity'] : 0;

						WC()->cart->set_quantity( $mnm_child_key, $child_qty_per_container * $container_quantity );
					}
				}
			}
		}
	}

	/**
	 * Filters the reported cart weights.
	 * Counts cumulative weight of containers.
	 *
	 * @since 2.0.0
	 *
	 * @param  float  $weight
	 * @return float
	 */
	public function cart_contents_weight( $weight ) {

		$cart_items = WC()->cart->get_cart();

		foreach ( $cart_items as $key => $cart_item ) {

			if ( wc_mnm_is_container_cart_item( $cart_item ) && $cart_item['data']->is_weight_cumulative() ) {

				foreach ( wc_mnm_get_child_cart_items( $cart_item, $cart_items, true ) as $child_item_key ) {

					$child_cart_item_data = $cart_items[ $child_item_key ];
					$child_product        = $child_cart_item_data['data'];
					$child_product_qty    = $child_cart_item_data['quantity'];
					$child_product_weight = isset( $child_product->bundled_weight ) ? $child_product->bundled_weight : 0.0;

					// Add weight for the child items.
					if ( $child_product_weight) {
						$weight += $child_product_weight * $child_product_qty;
					}
				}

			}
		}

		return $weight;
	}

	/**
	 * Remove child cart items with parent.
	 *
	 * @param  string  $cart_item_key
	 * @param  obj WC_Cart $cart
	 */
	public function cart_item_removed( $cart_item_key, $cart ) {

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
	public function cart_item_restored( $cart_item_key, $cart ) {

		if ( wc_mnm_is_container_cart_item( $cart->cart_contents[ $cart_item_key ] ) ) {

			$child_item_cart_keys = $cart->cart_contents[ $cart_item_key ]['mnm_contents'];

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

				if ( ! empty( $package['contents'] ) ) {
					foreach ( $package['contents'] as $cart_item_key => $cart_item_data ) {

						if ( wc_mnm_is_container_cart_item( $cart_item_data ) ) {

							$container     = unserialize( serialize( $cart_item_data['data'] ) );
							$container_qty = $cart_item_data['quantity'];

							/*
							 * Container needs shipping: Sum the prices of any children that are not shipped individually into the parent and, optionally, add their weight to the parent weight.
							 */

							if ( $container->needs_shipping() ) {

								// Aggregate weights.
								$cumulative_weight = 0.0;

								// Aggregate prices.
								$cumulative_value = 0.0;

								$container_totals = array(
									'line_subtotal'     => $cart_item_data['line_subtotal'],
									'line_total'        => $cart_item_data['line_total'],
									'line_subtotal_tax' => $cart_item_data['line_subtotal_tax'],
									'line_tax'          => $cart_item_data['line_tax'],
									'line_tax_data'     => $cart_item_data['line_tax_data']
								);

								foreach ( wc_mnm_get_child_cart_items( $cart_item_data, WC()->cart->cart_contents, true ) as $child_item_key ) {

									$child_cart_item_data = WC()->cart->cart_contents[ $child_item_key ];
									$child_product        = $child_cart_item_data['data'];
									$child_product_qty    = $child_cart_item_data['quantity'];
									$child_product_value  = isset( $child_product->bundled_value ) ? $child_product->bundled_value: 0.0;
									$child_product_weight = isset( $child_product->bundled_weight ) ? $child_product->bundled_weight : 0.0;

									// Aggregate price of physically packaged child item - already converted to virtual.
									if ( $child_product_value ) {

										$cumulative_value += $child_product_value * $child_product_qty;

										$container_totals['line_subtotal']     += $child_cart_item_data['line_subtotal'];
										$container_totals['line_total']        += $child_cart_item_data['line_total'];
										$container_totals['line_subtotal_tax'] += $child_cart_item_data['line_subtotal_tax'];
										$container_totals['line_tax']          += $child_cart_item_data['line_tax'];

										$packages[ $package_key ]['contents_cost'] += $child_cart_item_data['line_total'];

										$child_item_line_tax_data = $child_cart_item_data['line_tax_data'];

										$container_totals['line_tax_data']['total']    = array_merge( $container_totals['line_tax_data']['total'], $child_item_line_tax_data['total'] );
										$container_totals['line_tax_data']['subtotal'] = array_merge( $container_totals['line_tax_data']['subtotal'], $child_item_line_tax_data['subtotal'] );
									}

									// Aggregate weight of physically packaged child item - already converted to virtual.
									if ( $child_product_weight ) {
										$cumulative_weight += $child_product_weight * $child_product_qty;
									}
								}

								if ( $cumulative_value > 0 ) {
									$container_price = $container->get_price( 'edit' );
									$container->set_price( (double) $container_price + $cumulative_value / $container_qty );
								}

								if ( $cumulative_weight > 0 ) {
									$container_weight = $container->get_weight( 'edit' );
									$container->set_weight( (double) $container_weight + $cumulative_weight / $container_qty );
								}

								$packages[ $package_key ]['contents'][ $cart_item_key ]         = array_merge( $cart_item_data, $container_totals );
								$packages[ $package_key ]['contents'][ $cart_item_key ]['data'] = $container;
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

	/**
	 * Do not show mix and matched items in cart widget.
	 *
	 * @deprecated 2.0.0
	 *
	 *
	 * @param  bool     $show
	 * @param  array    $cart_item
	 * @param  string   $cart_item_key
	 * @return bool
	 */
	public function cart_widget_filter( $show, $cart_item, $cart_item_key ) {
		wc_deprecated_function( 'WC_Mix_and_Match_Cart::cart_widget_filter()', '2.0.0', 'WC_Mix_and_Match_Display::cart_widget_filter()' );
		return WC_Mix_and_Match()->display->cart_widget_filter( $show, $cart_item, $cart_item_key );
	}


	/**
	 * Filters the reported number of cart items.
	 *
	 * @deprecated 2.0.0
	 *
	 * Counts only MnM containers.
	 *
	 * @param  int  $count
	 * @return int
	 */
	public function cart_contents_count( $count ) {
		wc_deprecated_function( 'WC_Mix_and_Match_Cart::cart_contents_count()', '2.0.0', 'WC_Mix_and_Match_Display::cart_contents_count()' );
		return WC_Mix_and_Match()->display->cart_contents_count( $count );
	}

	/**
	 * MnM items can't be removed individually from the cart.
	 * This filter doesn't pass the $cart_item array for some reason.
	 *
	 * @deprecated 2.0.0
	 *
	 *
	 * @param  string  $link
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_remove_link( $link, $cart_item_key ) {
		wc_deprecated_function( 'WC_Mix_and_Match_Cart::cart_item_remove_link()', '2.0.0', 'WC_Mix_and_Match_Display::cart_item_remove_link()' );
		return WC_Mix_and_Match()->display->cart_item_remove_link( $link, $cart_item_key );
	}

	/**
	 * Modifies the cart.php formatted quantity for items in the container.
	 *
	 * @deprecated 2.0.0
	 *
	 *
	 * @param  string  $quantity
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item
	 * @return string
	 */
	public function cart_item_quantity( $quantity, $cart_item_key, $cart_item ) {
		wc_deprecated_function( 'WC_Mix_and_Match_Cart::cart_item_quantity()', '2.0.0', 'WC_Mix_and_Match_Display::cart_item_quantity()' );
		return WC_Mix_and_Match()->display->cart_item_quantity( $quantity, $cart_item_key, $cart_item );
	}


	/**
	 * Modifies the cart.php formatted html prices visibility for items in the container.
	 *
	 * @deprecated 2.0.0
	 *
	 *
	 * @param  string  $price
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_price( $price, $cart_item, $cart_item_key ) {
		wc_deprecated_function( 'WC_Mix_and_Match_Cart::cart_item_quantity()', '2.0.0', 'WC_Mix_and_Match_Display::cart_item_quantity()' );
		return WC_Mix_and_Match()->display->cart_item_price( $price, $cart_item, $cart_item_key );
	}


	/**
	 * Modifies the cart.php template formatted subtotal appearance.
	 *
	 * @deprecated 2.0.0
	 *
	 *
	 * @param  string  $subtotal
	 * @param  array   $cart_item
	 * @param  string  $cart_item_key
	 * @return string
	 */
	public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
		wc_deprecated_function( 'WC_Mix_and_Match_Cart::cart_item_subtotal()', '2.0.0', 'WC_Mix_and_Match_Display::cart_item_subtotal()' );
		return WC_Mix_and_Match()->display->cart_item_subtotal( $subtotal, $cart_item, $cart_item_key );
	}

	/**
	 * Outputs a formatted subtotal ( @see cart_item_subtotal() ).
	 *
	 * @deprecated 2.0.0
	 *
	 * @static
	 * @param  obj     $product   The WC_Product.
	 * @param  string  $subtotal  Formatted subtotal.
	 * @return string             Modified formatted subtotal.
	 */
	public static function format_product_subtotal( $product, $subtotal ) {
		wc_deprecated_function( 'WC_Mix_and_Match_Cart::format_product_subtotal()', '2.0.0', 'WC_Mix_and_Match_Display::format_product_subtotal()' );
		return WC_Mix_and_Match()->display->format_product_subtotal( $product, $subtotal );
	}

} //End class.
