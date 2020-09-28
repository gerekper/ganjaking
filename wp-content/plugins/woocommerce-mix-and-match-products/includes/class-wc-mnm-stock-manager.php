<?php
/**
 * Stock Manager
 *
 * @author   SomewhereWarm
 * @category Classes
 * @package  WooCommerce Mix and Match Products/Stock
 * @since    1.0.5
 * @version  1.7.0
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
	 * @var str
	 */
	private $items;

	/**
	 * Total quantity of items in the container.
	 * @var str
	 */
	private $total_qty;
	
	/**
	 * The Mix and Match Product Object.
	 * @var obj WC_Product
	 */
	public $product;

	public function __construct( $product ) {

		$this->items  = array();
		$this->total_qty = 0;
		$this->product = $product;
	}

	/**
	 * Add a product to the collection.
	 *
	 * @param int          $product_id
	 * @param false|int    $variation_id
	 * @param int      	   $quantity
	 */
	public function add_item( $product_id, $variation_id = false, $quantity = 1 ) {

		$this->items[] = new WC_Mix_and_Match_Stock_Manager_Item( $product_id, $variation_id, $quantity );

		// update the total of items in the container
		$this->total_qty += $quantity;
	}

	/**
	 * Return the items of this collection.
	 *
	 * @return array
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
	 * @return array
	 */
	public function get_managed_items() {

		$managed_items = array();

		if ( ! empty( $this->items ) ) {

			foreach ( $this->items as $purchased_item ) {

				$managed_by_id = $purchased_item->managed_by_id;

				if ( isset( $managed_items[ $managed_by_id ] ) ) {

					$managed_items[ $managed_by_id ][ 'quantity' ] += $purchased_item->quantity;

				} else {

					$managed_items[ $managed_by_id ][ 'quantity' ] = $purchased_item->quantity;

					if ( $purchased_item->variation_id && $purchased_item->variation_id == $managed_by_id ) {
						$managed_items[ $managed_by_id ][ 'is_variation' ] = true;
						$managed_items[ $managed_by_id ][ 'product_id' ]   = $purchased_item->product_id;
					} else {
						$managed_items[ $managed_by_id ][ 'is_variation' ] = false;
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

		// If we are updating a container in-cart, subtract the child item cart quantites that belong to the bundle being updated, since it's going to be removed later on.
		if ( isset( $_POST[ 'update-container' ] ) ) {

			$updating_cart_key = wc_clean( $_POST[ 'update-container' ] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {

				$container_cart_item   = WC()->cart->cart_contents[ $updating_cart_key ];
				$mnm_cart_items = wc_mnm_get_child_cart_items( $container_cart_item );

				if ( isset( $quantities_in_cart[ $container_cart_item[ 'product_id' ] ] ) ) {
					$quantities_in_cart[ $container_cart_item[ 'product_id' ] ] -= $container_cart_item[ 'quantity' ];
					// Unset if 0.
					if ( 0 === absint( $quantities_in_cart[ $container_cart_item[ 'product_id' ] ] ) ) {
						unset( $quantities_in_cart[ $container_cart_item[ 'product_id' ] ] );
					}
				}

				if ( ! empty( $mnm_cart_items ) ) {
					foreach ( $mnm_cart_items as $item_key => $item ) {

						$mnm_product_id = $item[ 'data' ]->is_type( 'variation' ) && true === $item[ 'data' ]->managing_stock() ? $item[ 'variation_id' ] : $item[ 'product_id' ];

						if ( isset( $quantities_in_cart[ $mnm_product_id ] ) ) {
							$quantities_in_cart[ $mnm_product_id ] -= $item[ 'quantity' ];
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
			// Throw a warning... updating cart
			$args = array( 'context' => 'cart' );
		}

		$context         = isset( $args[ 'context' ] ) ? $args[ 'context' ] : 'add-to-cart';
		$throw_exception = isset( $args[ 'throw_exception' ] ) && $args[ 'throw_exception' ];

		$managed_items = $this->get_managed_items();

		if ( empty( $managed_items ) ) {
			return true;
		}

		if ( ! isset( $this->product ) || ! is_object( $this->product ) ) {
			if ( WP_DEBUG ) {
				trigger_error( 'WC_Mix_and_Match_Stock_Manager class instantiated with invalid constructor arguments.' );
			}
			return false;
		}

		$container_id    = $this->product->get_id();
		$container_title = $this->product->get_title();

		foreach ( $managed_items as $managed_item_id => $managed_item ) {

			try {

				$quantity = $managed_item[ 'quantity' ];

				// Get the product.
				$managed_product       = wc_get_product( $managed_item_id );

				if ( ! $managed_product ) {
					continue;
				}

				$managed_product_title = $managed_product->get_title();

				if ( $managed_product->is_type( 'variation' ) && $managed_product->managing_stock() ) {
					$managed_product_title .= ' &ndash; ' . wc_get_formatted_variation( $managed_product, true );
				}

				// Check if product is_sold_individually.
				if ( $managed_product->is_sold_individually() && $quantity > 1 ) {
					// translators: %s product title.
					$reason = sprintf( __( 'Only 1 &quot;%s&quot; may be purchased.', 'woocommerce-mix-and-match-products' ), $managed_product_title );

					if ( 'add-to-cart' === $context ) {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart as configued. %2$s', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
						
					} else {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased as configured. %2$s', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
					}

					throw new Exception( $notice );
				}

				// In-stock check: a product may be marked as "Out of stock", but has_enough_stock() may still return a number.
				// We also need to take into account the 'woocommerce_notify_no_stock_amount' setting.
				if ( ! $managed_product->is_in_stock() ) {
					// translators: %s product title.				
					$reason = sprintf( __( '&quot;%s&quot; is out of stock.', 'woocommerce-mix-and-match-products' ), $managed_product_title );

					if ( 'add-to-cart' === $context ) {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart as configued. %2$s', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
					} else {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased as configured. %2$s', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
					} 

					throw new Exception( $notice );

				// Not enough stock for this configuration.
				} elseif ( ! $managed_product->has_enough_stock( $quantity ) ) {
					// translators: %1$s product title. %2$s quantity in stock.
					$reason = sprintf( __( 'There is not enough stock of &quot;%1$s&quot; (%2$s remaining).', 'woocommerce-mix-and-match-products' ), $managed_product_title, $managed_product->get_stock_quantity() );

					if ( 'add-to-cart' === $context ) {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart as configured. %2$s', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
					} else {
						// translators: %1$s product title. %2$s Error reason.
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased as configured. %2$s', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
					}

					throw new Exception( $notice );

				} 

				// Stock check - this time accounting for whats already in-cart.
				if ( $managed_product->managing_stock() ) {

					// Get quantities of items already in cart: returns array of IDs => quantity.
					$cart_quantities = $quantities_in_cart = $this->get_quantities_in_cart();

					if ( isset( $cart_quantities[ $managed_item_id ] ) && ! $managed_product->has_enough_stock( $cart_quantities[ $managed_item_id ] + $quantity ) ) {

						// translators: %1$s product title. %2$s quantity in stock. %3$s quantity in cart.
						$reason = sprintf( __( 'There is not enough stock of &quot;%1$s&quot; (%2$s in stock, %3$s in your cart).', 'woocommerce-mix-and-match-products' ), $managed_product_title, $managed_product->get_stock_quantity(), $cart_quantities[ $managed_item_id ] );

						if ( 'add-to-cart' === $context ) {
							// translators: %1$s product title. %2$s Error reason.
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to the cart as configured. %2$s', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
						} else {
							// translators: %1$s product title. %2$s Error reason.
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased as configured. %2$s', 'woocommerce-mix-and-match-products' ), $container_title, $reason );
						}

						$notice = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View Cart', 'woocommerce-mix-and-match-products' ), $notice );

						throw new Exception( $notice );

					}
				}

			} catch ( Exception $e ) {

				$error = $e->getMessage();

				if ( $throw_exception ) {
					throw new Exception( $error );
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
 * @version  1.0.5
 * @since    1.0.5
 */
class WC_Mix_and_Match_Stock_Manager_Item {

	/**
	 * Product ID.
	 * 
	 * @var int
	 */
	public $product_id;
	
	/**
	 * Varitation ID.
	 * 
	 * @var int
	 */
	public $variation_id;
	
	/**
	 * Quantity of Item in Container.
	 * 
	 * @var int
	 */
	public $quantity;

	/**
	 * The variation or product ID that manages the stock for this item.
	 * 
	 * @var int
	 */	
	public $managed_by_id;

	/**
	 * __construct function.
	 * 
	 * @param int $product_id
	 * @param int $variation_id
	 * @param int $quantity
	 */
	
	public function __construct( $product_id, $variation_id = false, $quantity = 1 ) {

		$this->product_id   = $product_id;
		$this->variation_id = $variation_id;
		$this->quantity     = $quantity;

		if ( $variation_id ) {

			$variation_stock = get_post_meta( $variation_id, '_stock', true );

			// If stock is managed at variation level.
			if ( isset( $variation_stock ) && $variation_stock !== '' ) {
				$this->managed_by_id = $variation_id;
				// Otherwise stock is managed by the parent.
			} else {
				$this->managed_by_id = $product_id;
			}

		} else {
			$this->managed_by_id = $product_id;
		}
	}
}
