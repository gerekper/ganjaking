<?php
/**
 * Stock Manager
 *
 * @package  WooCommerce Mix and Match Products/Stock
 * @since    1.0.5
 * @version  2.5.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Mix_and_Match_Stock_Manager Class.
 *
 * Used to create and store a product_id / variation_id representation of a product collection based on the included items' inventory requirements.
 */
class WC_Mix_and_Match_Stock_Manager {

	/**
	 * The collection of items in the container.
	 *
	 * @var array
	 */
	private $items;

	/**
	 * Total quantity of items in the container.
	 *
	 * @var int
	 */
	private $total_qty;

	/**
	 * The Mix and Match Product Object.
	 *
	 * @var obj WC_Product
	 */
	public $product;

	public function __construct( \WC_Product $product ) {

		$this->items     = array();
		$this->total_qty = 0;
		$this->product   = $product;
	}

	/**
	 * Add a product to the collection.
	 *
	 * @param WC_MNM_Child_Item $child_item
	 * @param int               $quantity
	 * @param false             $deprecated - formerly quantity.
	 */
	public function add_item( $child_item, $quantity = 1, $deprecated = false ) {

		if ( is_int( $child_item ) ) {
			wc_deprecated_argument( '$product_id', '2.4.0', 'WC_Mix_and_Match_Stock_Manager::add_item() should be called with a WC_MNM_Child_Item object as the first argument. Warning! This will break in 3.0.0.' );
		}

		$this->items[] = new WC_Mix_and_Match_Stock_Manager_Item( $child_item, $quantity, $deprecated );

		// update the total of items in the container.
		if ( is_int( $deprecated ) ) {
			wc_deprecated_argument( '$quantity', '2.4.0', 'WC_Mix_and_Match_Stock_Manager::add_item() expects quantity as the 2nd argument. Warning! This will break in 3.0.0.' );
			// update the total of items in the container.
			$this->total_qty += $deprecated;
		} else {
			$this->total_qty += $quantity;
		}
	}

	/**
	 * Return the items of this collection.
	 *
	 * @return WC_Mix_and_Match_Stock_Manager_Product[]
	 */
	public function get_items() {

		if ( ! empty( $this->items ) ) {
			return $this->items;
		}

		return array();
	}

	/**
	 * Return the items of this collection.
	 *
	 * @return array
	 */
	public function get_total_quantity() {
		return $this->total_qty;
	}

	/**
	 * Merge another collection with this one.
	 *
	 * @param WC_Mix_and_Match_Stock_Manager  $stock
	 * @return bool | Whether successfully added
	 */
	public function add_stock( $stock ) {

		if ( ! is_object( $stock ) ) {
			return false;
		}

		$items_to_add = $stock->get_items();

		if ( ! empty( $items_to_add ) ) {
			foreach ( $items_to_add as $item ) {
				$this->items[] = $item;

				// Update the total of items in the container.
				$this->total_qty += $item->quantity;
			}
			return true;
		}

		return false;
	}

	/**
	 * Return the stock requirements of the items in this collection.
	 * To validate stock accurately, this method is used to add quantities and build a list of product/variation ids to check.
	 * Note that in some cases, stock for a variation might be managed by the parent - this is tracked by the managed_by_id property in WC_Mix_and_Match_Stock_Manager_Item.
	 *
	 * @return array { int $managed_by_id {
	 *      @type WC_Product $product  The product object controlling this item's stock
	 *      @type int        $quantity The selected quantity for this item.
	 *  }
	 * }
	 */
	public function get_managed_items() {

		$managed_items = array();

		if ( ! empty( $this->items ) ) {

			foreach ( $this->items as $purchased_item ) {

				$managed_by_id = $purchased_item->get_managed_by_id();

				if ( isset( $managed_items[ $managed_by_id ] ) ) {

					$managed_items[ $managed_by_id ]['quantity'] += $purchased_item->get_quantity();

				} else {

					// Store the managed product.
					$managed_items[ $managed_by_id ]['product'] = $purchased_item->get_managed_product();

					// Store initial quantity.
					$managed_items[ $managed_by_id ]['quantity'] = $purchased_item->get_quantity();

					$purchased_product = $purchased_item->get_managed_product();

					// Legacy array keys. Deprecated in 2.4.0. Will be removed in 3.0.0.
					if ( $purchased_product && $purchased_product->get_parent_id() && $purchased_product->get_id() === $managed_by_id ) {
						$managed_items[ $managed_by_id ]['is_variation'] = true;
						$managed_items[ $managed_by_id ]['product_id']   = $purchased_product->get_parent_id();
					} else {
						$managed_items[ $managed_by_id ]['is_variation'] = false;
					}
				}
			}
		}

		return $managed_items;
	}


	/**
	 * Product quantities already in cart.
	 *
	 * @since  1.4.0
	 *
	 * @return array
	 */
	private function get_quantities_in_cart() {

		$quantities_in_cart = WC()->cart->get_cart_item_quantities();

		// If we are updating a container in-cart, subtract the child item cart quantites that belong to the container_id being updated, since it's going to be removed later on.
		if ( isset( $_POST['update-container'] ) ) {

			$updating_cart_key = wc_clean(wp_unslash( $_POST['update-container'] ) );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {

				$container_cart_item = WC()->cart->cart_contents[ $updating_cart_key ];
				$mnm_cart_items      = wc_mnm_get_child_cart_items( $container_cart_item );

				if ( isset( $quantities_in_cart[ $container_cart_item['product_id'] ] ) ) {
					$quantities_in_cart[ $container_cart_item['product_id'] ] -= $container_cart_item['quantity'];
					// Unset if 0.
					if ( 0 === absint( $quantities_in_cart[ $container_cart_item['product_id'] ] ) ) {
						unset( $quantities_in_cart[ $container_cart_item['product_id'] ] );
					}
				}

				if ( ! empty( $mnm_cart_items ) ) {
					foreach ( $mnm_cart_items as $item_key => $item ) {

						$mnm_product_id = $item['data']->is_type( 'variation' ) && true === $item['data']->managing_stock() ? $item['variation_id'] : $item['product_id'];

						if ( isset( $quantities_in_cart[ $mnm_product_id ] ) ) {
							$quantities_in_cart[ $mnm_product_id ] -= $item['quantity'];
							// Unset if 0.
							if ( 0 === absint( $quantities_in_cart[ $mnm_product_id ] ) ) {
								unset( $quantities_in_cart[ $mnm_product_id ] );
							}
						}
					}
				}
			}
		}

		return $quantities_in_cart;
	}

	/**
	 * Validate that all managed items in the collection are in stock.
	 *
	 * @throws Exception
	 *
	 * @param  array  $args
	 * @return boolean
	 */
	public function validate_stock( $args = array() ) {

		if ( is_bool( $args ) && $args ) {
			// Throw a warning... updating cart.
			$args = array( 'context' => 'cart' );
		}

		$context         = isset( $args['context'] ) ? $args['context'] : 'add-to-cart';
		$throw_exception = isset( $args['throw_exception'] ) && $args['throw_exception'];

		$managed_items = $this->get_managed_items();

		if ( empty( $managed_items ) ) {
			return true;
		}

		if ( ! isset( $this->product ) || ! is_object( $this->product ) ) {
			wc_doing_it_wrong( 'WC_Mix_and_Match_Stock_Manager __constructor()', 'WC_Mix_and_Match_Stock_Manager class instantiated with invalid constructor arguments.', '2.4.0' );
			return false;
		}

		$container_id    = $this->product->get_id();
		$container_title = $this->product->get_title();

		// Get quantities of items already in cart: returns array of IDs => quantity.
		$quantities_in_cart = $this->get_quantities_in_cart();

		foreach ( $managed_items as $managed_item_id => $managed_item ) {

			try {

				// Get the product.
				$managed_product = $managed_item['product'];

				if ( ! $managed_product ) {
					continue;
				}

				// Get the quantity.
				$quantity = $managed_item['quantity'];

				$managed_product_title = $managed_product->get_title();

				if ( $managed_product->is_type( 'variation' ) && $managed_product->managing_stock() ) {
					$managed_product_title .= ' &ndash; ' . wc_get_formatted_variation( $managed_product, true );
				}

				// Check if product is_sold_individually. @TODO - HOW DOES SOLD INDIVIDUALLY WORK WHEN MANAGED AT PARENT LEVEL vs MANAGED AT VARIATION LEVEL???
				if ( $managed_product->is_sold_individually() ) {
					
					if ( $quantity > 1 ) {
						// translators: %s product title.
						$reason = sprintf( _x( 'Only 1 &quot;%s&quot; may be purchased.', '[Frontend]', 'woocommerce-mix-and-match-products' ), $managed_product_title );

						if ( 'add-to-cart' === $context ) {
							// translators: %1$s product title. %2$s Error reason.
							$notice = sprintf( _x( '&quot;%1$s&quot; cannot be added to your cart as configued. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );

						} else {
							// translators: %1$s product title. %2$s Error reason.
							$notice = sprintf( _x( '&quot;%1$s&quot; cannot be purchased as configured. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
						}

						throw new Exception( $notice );

					} elseif ( isset( $quantities_in_cart[ $managed_item_id ] ) ) {
						
						// translators: %1$s product title.
						$reason = sprintf( _x( 'You cannot add another &quot;%s&quot; to the cart.', '[Frontend]', 'woocommerce-mix-and-match-products' ), $managed_product_title );

						if ( 'add-to-cart' === $context ) {
							// translators: %1$s product title. %2$s Error reason.
							$notice = sprintf( _x( '&quot;%1$s&quot; cannot be added to the cart as configured. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
						} else {
							// translators: %1$s product title. %2$s Error reason.
							$notice = sprintf( _x( '&quot;%1$s&quot; cannot be purchased as configured. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
						}

						throw new Exception( $notice );

					}
				}

				// In-stock check: a product may be marked as "Out of stock", but has_enough_stock() may still return a number.
				// We also need to take into account the 'woocommerce_notify_no_stock_amount' setting.
				if ( ! $managed_product->is_in_stock() ) {
					// translators: %s product title.
					$reason = sprintf( _x( '&quot;%s&quot; is out of stock.', '[Frontend]', 'woocommerce-mix-and-match-products' ), $managed_product_title );

					if ( 'add-to-cart' === $context ) {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( _x( '&quot;%1$s&quot; cannot be added to your cart as configued. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
					} else {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( _x( '&quot;%1$s&quot; cannot be purchased as configured. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
					}

					throw new Exception( $notice );

					// Not enough stock for this configuration.
				} elseif ( ! $managed_product->has_enough_stock( $quantity ) ) {
					// translators: %1$s product title. %2$s quantity in stock.
					$reason = sprintf( _x( 'There is not enough stock of &quot;%1$s&quot; (%2$s remaining).', '[Frontend]', 'woocommerce-mix-and-match-products' ), $managed_product_title, $managed_product->get_stock_quantity() );

					if ( 'add-to-cart' === $context ) {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( _x( '&quot;%1$s&quot; cannot be added to your cart as configured. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
					} else {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( _x( '&quot;%1$s&quot; cannot be purchased as configured. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
					}

					throw new Exception( $notice );

				}

				// Stock check - this time accounting for whats already in-cart.
				if ( $managed_product->managing_stock() ) {

					if ( isset( $quantities_in_cart[ $managed_item_id ] ) && ! $managed_product->has_enough_stock( $quantities_in_cart[ $managed_item_id ] + $quantity ) ) {

						// translators: %1$s product title. %2$s quantity in stock. %3$s quantity in cart.
						$reason = sprintf( _x( 'There is not enough stock of &quot;%1$s&quot; (%2$s in stock, %3$s in your cart).', '[Frontend]', 'woocommerce-mix-and-match-products' ), $managed_product_title, $managed_product->get_stock_quantity(), $quantities_in_cart[ $managed_item_id ] );

						if ( 'add-to-cart' === $context ) {
							// translators: %1$s product title. %2$s Error reason.
							$notice = sprintf( _x( '&quot;%1$s&quot; cannot be added to the cart as configured. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
						} else {
							// translators: %1$s product title. %2$s Error reason.
							$notice = sprintf( _x( '&quot;%1$s&quot; cannot be purchased as configured. %2$s', '[Frontend]', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
						}

						$notice = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), _x( 'View Cart', '[Frontend]', 'woocommerce-mix-and-match-products' ), $notice );

						throw new Exception( $notice );

					}
				}
			} catch ( Exception $e ) {

				$error = $e->getMessage();

				if ( $throw_exception ) {
					throw new Exception( wp_kses_post( $error ) );
				} else {
					return false;
				}
			}
		}

		return true;
	}
}


/**
 * Maps a product/variation in the collection to the item managing stock for it.
 * These 2 will differ only if stock for a variation is managed by its parent.
 *
 * @class    WC_Mix_and_Match_Stock_Manager_Item
 *
 * @since    1.0.5
 * @version  2.4.0
 */
class WC_Mix_and_Match_Stock_Manager_Item {

	/**
	 * The variation|product ID that manages the stock for this item.
	 *
	 * @var int
	 */
	private $managed_by_id;

	/**
	 * Quantity of Item in Container.
	 *
	 * @var int
	 */
	private $quantity = 0;

	/**
	 * Child item object.
	 *
	 * @var WC_MNM_Child_Item
	 */
	private $child_item;

	/**
	 * Managed product object.
	 *
	 * @var WC_Product
	 */
	private $managed_product = null;

	/**
	 * Magic getter for old props.
	 *
	 * @var int
	 */
	public function __get( $prop ) {

		wc_doing_it_wrong( $prop, 'WC_Mix_and_Match_Stock_Manager_Item properties should not be accessed directly. Warning! This will break in 3.0.0.', '2.4.0' );

		$get_fn = 'get_' . $prop;

		if ( is_callable( array( $this, $get_fn ) ) ) {
			return $this->$get_fn();
		}

		switch ( $prop ) {
			case 'product_id':
				$product = $this->get_product();
				if ( $product ) {
					$value = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
				} else {
					$value = 0;
				}
				break;
			case 'variation_id';
				$product = $this->get_product();
				if ( $product ) {
					$value = $product->get_parent_id() ? $product->get_id() : 0;
				} else {
					$value = 0;
				}
				break;
			default:
				$value = '';
		}
	}

	/**
	 * __construct function.
	 *
	 * @param WC_MNM_Child_Item $child_item
	 * @param int $quantity
	 * @param int $deprecated
	 */

	public function __construct( $child_item, $quantity = 1, $deprecated = false ) {

		if ( $child_item instanceof WC_MNM_Child_Item ) {

			$this->child_item = $child_item;
			$this->quantity   = $quantity;

		} elseif ( is_int( $child_item ) ) {
			wc_deprecated_argument( '$product_id', '2.4.0', 'WC_Mix_and_Match_Stock_Manager_Item should be instantiated with a WC_MNM_Child_Item object. Warning! This will break in 3.0.0.' );
			$this->quantity = $deprecated;

			// Not entirely certain this is going to work without the container_id.
			$this->child_item = new WC_MNM_Child_Item(
				array(
					'product_id'   => $child_item,
					'variation_id' => $quantity,
				)
			);

			// Get the managed by ID from post meta in the absence of the product object.
			$variation_manage_stock = get_post_meta( $this->variation_id, '_manage_stock', true );

			$this->get_managed_by_id = 'parent' === $variation_manage_stock ? $this->product_id : $this->variation_id;
		}

		if ( $deprecated ) {
			wc_deprecated_argument( 'quantity', '2.4.0', 'Quantity is now the 2nd parameter in the WC_Mix_and_Match_Stock_Manager_Item constructor. Warning! This will break in 3.0.0.' );
			$this->quantity = $deprecated;
		}

		if ( $this->child_item instanceof WC_MNM_Child_Item && $this->child_item->get_product() instanceof WC_Product ) {
			$this->managed_by_id = $child_item->get_product()->get_stock_managed_by_id();
		} else {
			wc_doing_it_wrong( 'WC_Mix_and_Match_Stock_Manager_Item __construct()', 'WC_Mix_and_Match_Stock_Manager_Item constructed with a WC_MNM_Child_Item that had no valid product.', '2.4.0' );
		}
	}

	/**
	 * Get the stock managed by ID.
	 *
	 * @return int
	 */
	public function get_managed_by_id() {
		return $this->managed_by_id;
	}

	/**
	 * Get the configured quantity.
	 *
	 * @return int
	 */
	public function get_quantity() {
		return $this->quantity;
	}

	/**
	 * Get the child item object for this item.
	 *
	 * @return WC_MNM_Child_Item
	 */
	public function get_child_item() {
		return $this->child_item;
	}

	/**
	 * Get the product object for this item.
	 *
	 * @return WC_Product}false
	 */
	public function get_product() {
		return $this->get_child_item() && $this->get_child_item()->get_product() ? $this->get_child_item()->get_product() : false;
	}

	/**
	 * Get the stock-managed product object for this item.
	 * Mostly it's the same, except when a variation is managed at the parent-product level.
	 *
	 * @return WC_Product|false
	 */
	public function get_managed_product() {

		if ( is_null( $this->managed_product ) ) {
			$this->managed_product = $this->get_child_item()->get_product();

			if ( $this->managed_product && $this->managed_product->get_id() !== $this->get_managed_by_id() ) {
				$this->managed_product = wc_get_product( $this->get_managed_by_id() );
			}
		}

		return $this->managed_product;
	}
}
