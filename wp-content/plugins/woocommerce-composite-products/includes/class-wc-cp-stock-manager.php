<?php
/**
 * WC_CP_Stock_Manager and WC_CP_Stock_Manager_Item classes
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.0.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Used to create and store a product_id / variation_id representation of a product collection based on the included items' inventory requirements.
 *
 * @class    WC_CP_Stock_Manager
 * @version  4.0.0
 */
class WC_CP_Stock_Manager {

	private $items;
	public $product;

	/**
	 * Constructor.
	 *
	 * @param  WC_Product_Composite  $product
	 */
	public function __construct( $product ) {
		$this->items   = array();
		$this->product = $product;
	}

	/**
	 * Add a product to the collection.
	 *
	 * @param  WC_Product|int                  $product
	 * @param  false|WC_Product_Variation|int  $variation
	 * @param  integer                         $quantity
	 */
	public function add_item( $product, $variation = false, $quantity = 1 ) {
		$this->items[] = new WC_CP_Stock_Manager_Item( $product, $variation, $quantity );
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
	 * Merge another collection with this one.
	 *
	 * @param WC_CP_Stock_Manager  $stock
	 */
	public function add_stock( $stock ) {

		if ( ! is_object( $stock ) ) {
			return false;
		}

		$items_to_add = $stock->get_items();

		if ( ! empty( $items_to_add ) ) {
			foreach ( $items_to_add as $item ) {
				$this->items[] = $item;
			}
			return true;
		}

		return false;
	}

	/**
	 * Return the stock requirements of the items in this collection.
	 * To validate stock accurately, this method is used to add quantities and build a list of product/variation ids to check.
	 * Note that in some cases, stock for a variation might be managed by the parent - this is tracked by the managed_by_id property in WC_CP_Stock_Manager_Item.
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
	 * @since  3.14.0
	 *
	 * @return array
	 */
	private function get_quantities_in_cart() {

		$quantities_in_cart = WC()->cart->get_cart_item_quantities();

		// If we are updating a composite in-cart, subtract the composited item cart quantites that belong to the composite being updated, since it's going to be removed later on.
		if ( isset( $_POST[ 'update-composite' ] ) ) {

			$updating_cart_key = wc_clean( $_POST[ 'update-composite' ] );

			if ( isset( WC()->cart->cart_contents[ $updating_cart_key ] ) ) {

				$parent_cart_item = WC()->cart->cart_contents[ $updating_cart_key ];
				$child_cart_items = wc_cp_get_composited_cart_items( $parent_cart_item );

				if ( isset( $quantities_in_cart[ $parent_cart_item[ 'product_id' ] ] ) ) {
					$quantities_in_cart[ $parent_cart_item[ 'product_id' ] ] -= $parent_cart_item[ 'quantity' ];
					// Unset if 0.
					if ( 0 === absint( $quantities_in_cart[ $parent_cart_item[ 'product_id' ] ] ) ) {
						unset( $quantities_in_cart[ $parent_cart_item[ 'product_id' ] ] );
					}
				}

				if ( ! empty( $child_cart_items ) ) {
					foreach ( $child_cart_items as $item_key => $item ) {

						$child_product_id = $item[ 'data' ]->is_type( 'variation' ) && true === $item[ 'data' ]->managing_stock() ? $item[ 'variation_id' ] : $item[ 'product_id' ];

						if ( isset( $quantities_in_cart[ $child_product_id ] ) ) {
							$quantities_in_cart[ $child_product_id ] -= $item[ 'quantity' ];
							// Unset if 0.
							if ( 0 === absint( $quantities_in_cart[ $child_product_id ] ) ) {
								unset( $quantities_in_cart[ $child_product_id ] );
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

		$context         = isset( $args[ 'context' ] ) ? $args[ 'context' ] : 'add-to-cart';
		$throw_exception = isset( $args[ 'throw_exception' ] ) && $args[ 'throw_exception' ];

		$managed_items = $this->get_managed_items();

		if ( empty( $managed_items ) ) {
			return true;
		}

		$composite_id    = $this->product->get_id();
		$composite_title = $this->product->get_title();

		// Stock Validation.
		foreach ( $managed_items as $managed_item_id => $managed_item ) {

			try {

				$quantity = $managed_item[ 'quantity' ];

				// Get the product.
				$product_data = wc_get_product( $managed_item_id );

				if ( ! $product_data ) {
					continue;
				}

				if ( ! $quantity ) {
					continue;
				}

				$product_title = $product_data->get_title();

				// Sold individually?
				if ( $product_data->is_sold_individually() && $quantity > 1 ) {

					$reason = sprintf( __( 'Only 1 &quot;%s&quot; may be purchased.', 'woocommerce-composite-products' ), $product_title );

					if ( 'add-to-cart' === $context ) {
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
					} else {
						$notice = $reason;
					}

					throw new Exception( $notice );
				}

				// Purchasable?
				if ( false === $product_data->is_purchasable() ) {

					$reason = sprintf( __( '&quot;%s&quot; cannot be purchased.', 'woocommerce-composite-products' ), $product_title );

					if ( 'add-to-cart' === $context ) {
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
					} else {
						$notice = $reason;
					}

					throw new Exception( $notice );
				}

				if ( 'variation' === $product_data->get_type() ) {
					$product_title = WC_CP_Helpers::format_product_title( $product_title, '', wc_get_formatted_variation( $product_data, true, false ), false );
				}

				// Stock check - only check if we're managing stock and backorders are not allowed.
				if ( ! $product_data->is_in_stock() ) {

					$reason = sprintf( __( '&quot;%s&quot; is out of stock.', 'woocommerce-composite-products' ), $product_title );

					if ( 'add-to-cart' === $context ) {
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
					} elseif ( 'cart' === $context ) {
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
					} else {
						$notice = $reason;
					}

					throw new Exception( $notice );

				} elseif ( ! $product_data->has_enough_stock( $quantity ) ) {

					$reason = sprintf( __( 'There is not enough stock of &quot;%1$s&quot; (%2$s remaining).', 'woocommerce-composite-products' ), $product_title, $product_data->get_stock_quantity() );

					if ( 'add-to-cart' === $context ) {
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
					} elseif ( 'cart' === $context ) {
						$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
					} else {
						$notice = $reason;
					}

					throw new Exception( $notice );
				}

				// Stock check, possibly accounting for what's in cart.
				if ( $product_data->managing_stock() ) {

					$quantities_in_cart = $this->get_quantities_in_cart();

					if ( isset( $quantities_in_cart[ $managed_item_id ] ) && ! $product_data->has_enough_stock( $quantities_in_cart[ $managed_item_id ] + $quantity ) ) {

						$reason = sprintf( __( 'There is not enough stock of &quot;%1$s&quot; (%2$s in stock, %3$s in your cart).', 'woocommerce-composite-products' ), $product_title, $product_data->get_stock_quantity(), $quantities_in_cart[ $managed_item_id ] );

						if ( 'add-to-cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be added to your cart. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} elseif ( 'cart' === $context ) {
							$notice = sprintf( __( '&quot;%1$s&quot; cannot be purchased. %2$s', 'woocommerce-composite-products' ), $composite_title, $reason );
						} else {
							$notice = $reason;
						}

						$error = sprintf( '<a href="%s" class="button wc-forward">%s</a> %s', wc_get_cart_url(), __( 'View Cart', 'woocommerce' ), $notice );

						throw new Exception( $error );
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
 * Class to represent stock-managed items.
 *
 * Maps a product/variation in the collection to the item managing stock for it.
 * These 2 will differ only if stock for a variation is managed by its parent.
 *
 * @class    WC_CP_Stock_Manager_Item
 * @version  3.8.0
 * @since    3.3.1
 */
class WC_CP_Stock_Manager_Item {

	public $product_id;
	public $variation_id;
	public $quantity;

	public $managed_by_id;

	public function __construct( $product, $variation = false, $quantity = 1 ) {

		$this->product_id   = is_object( $product ) ? $product->get_id() : $product;
		$this->variation_id = is_object( $variation ) ? $variation->get_id() : $variation;
		$this->quantity     = $quantity;
		$this->quantity     = $quantity;

		if ( $this->variation_id && ( $variation = is_object( $variation ) ? $variation : wc_get_product( $variation ) ) ) {
			$this->managed_by_id = $variation->get_stock_managed_by_id();
		} else {
			$this->managed_by_id = $this->product_id;
		}
	}
}
