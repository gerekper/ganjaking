<?php
/**
 * WooCommerce Measurement Price Calculator
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Pricing calculator inventory handling
 *
 * This class is responsible for managing all aspects of the Pricing Calculator
 * products Inventory Handling.  By default, a pricing calculator product's
 * stock will be managed as would any other, non-customizable product.  Meaning
 * that if you have a stock of '10' for custom-length fabric, each item could
 * be of any length, which probably isn't very realistic.
 *
 * With inventory management enabled, that same stock of '10' would repesent
 * '10 feet' worth of fabric available for purchase.  So customers could order
 * 1 piece at 10 feet long, or perhaps 2 pieces at 5 feet each.
 *
 * The implementation strategy used to achieve this is to work internally with
 * a measurement stock based on the unit (ie 10 feet in our instance) while
 * displaying the item stock on the frontend/order detail area.  So a customer
 * ordering 2 pieces of fabric at 5 feet each, will see a quantity of '2'
 * throughout the frontend, while internally we would convert that to the
 * measurement stock unit of 2 * 5 = 10.
 *
 * Terminology:
 * * measurement stock - refers to stock in terms of a unit, ie 10 feet of stock
 * * product/item stock - refers to the count of items, as normal
 *
 * @since 3.0
 */
class WC_Price_Calculator_Inventory {


	/** @var bool used to keep track of whether a pricing calculator product stock has been updated when added to cart */
	private $pricing_stock_altered = false;


	/**
	 * Construct and initialize the inventory handling class
	 *
	 * @since 3.0
	 */
	public function __construct() {

		// set the measurement stock amount when adding to the cart in WC <= 2.6
		add_filter( 'woocommerce_stock_amount',                    array( $this, 'get_measurement_stock_amount' ), 5, 1 );

		// set the measurement stock in WC 3.0
		add_filter( 'woocommerce_add_to_cart_quantity', array( $this, 'set_measurement_add_to_cart_stock_amount' ), 5, 2 );

		// set the measurement stock amount when updating the cart/checkout in all supported WC versions
		add_filter( 'woocommerce_stock_amount_cart_item',          array( $this, 'get_measurement_stock_amount' ), 10, 2 );

		// set the measurement stock amount when adding to the cart
		add_filter( 'woocommerce_add_cart_item', array( $this, 'set_measurement_stock_amount' ), 1, 1 );

		add_filter( 'woocommerce_get_availability',                array( $this, 'get_availability_measurement' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_quantity',              array( $this, 'get_cart_item_quantity' ), 10, 2 );
		add_filter( 'woocommerce_widget_cart_item_quantity',       array( $this, 'get_widget_cart_item_quantity' ), 10, 3 );
		// note: no filter required for the order items table, as its unit quantity by then
		add_filter( 'woocommerce_checkout_cart_item_quantity',     array( $this, 'get_checkout_item_quantity' ), 10, 2 );
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'after_cart_item_quantity_update' ), 10, 2 );
		add_filter( 'woocommerce_order_item_quantity',             array( $this, 'get_order_item_measurement_quantity' ), 10, 3 );
		add_filter( 'woocommerce_order_get_items',                 array( $this, 'order_again_item_set_quantity' ), 10, 2 );
		add_filter( 'woocommerce_cart_shipping_packages',          array( $this, 'cart_shipping_packages' ) );

		// filter the backordered quantity item meta label to reference measurement unit
		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gt( '3.2' ) ) {
			add_filter( 'woocommerce_backordered_item_meta_name', array( $this, 'get_backordered_item_meta_name' ), 20, 2 );
		} else {
			add_filter( 'woocommerce_backordered_item_meta_name', array( $this, 'get_backordered_item_meta_name' ), 20, 1 );
		}

		if ( is_admin() || is_ajax() ) {
			add_filter( 'woocommerce_reduce_order_stock_quantity',  array( $this, 'admin_manage_order_stock' ), 10, 2 );
			add_filter( 'woocommerce_restore_order_stock_quantity', array( $this, 'admin_manage_order_stock' ), 10, 2 );
		}
	}


	/**
	 * This returns the stock amount in pricing units for pricing calculator
	 * products with inventory enabled.  This filter is called from a number of
	 * places, but the only times that we're interested in are:
	 *
	 * * when a product is added to the cart $_REQUEST['add-to-cart']
	 * * when the cart is updated or we transition to the checkout page
	 * * when an order is "ordered again"
	 *
	 * The purpose of this is to convert the item quantity (ie 2 pieces of
	 * fabric) to the measurement quantity (ie 2 pieces of fabric at 3 ft each
	 * equals 6 ft of fabric)
	 *
	 * @since 3.0.0
	 *
	 * @param int|float $quantity the item quantity
	 * @param string $cart_item_key the cart item key, available when the cart
	 *        is being updated or we're moving to the checkout pages
	 * @return int|float the calculated measurement quantity
	 */
	public function get_measurement_stock_amount( $quantity, $cart_item_key = null ) {

		if ( $cart_item_key ) {

			// This is called when updating the cart/transitioning to checkout,
			// so we already have the measurement needed in pricing/stock units.
			$cart     = WC()->cart->get_cart();
			$product  = $cart[ $cart_item_key ]['data'];
			$settings = new \WC_Price_Calculator_Settings( $product );

			$measurement_needed      = isset( $cart[ $cart_item_key ]['pricing_item_meta_data']['_measurement_needed'] )      ? $cart[ $cart_item_key ]['pricing_item_meta_data']['_measurement_needed']      : null;
			$measurement_needed_unit = isset( $cart[ $cart_item_key ]['pricing_item_meta_data']['_measurement_needed_unit'] ) ? $cart[ $cart_item_key ]['pricing_item_meta_data']['_measurement_needed_unit'] : null;

			if ( \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) ) {

				// quantity * measurement needed in pricing units
				$quantity *= \WC_Price_Calculator_Measurement::convert( $measurement_needed, $measurement_needed_unit, $settings->get_pricing_unit() );
			}
		}

		return $quantity;
	}


	/**
	 * Set the measurement stock amount in pricing units for pricing calculator products with inventory enabled.
	 *
	 * The purpose of this is to convert the item quantity (ie 2 pieces of fabric)
	 * to the measurement quantity (ie 2 pieces of fabric at 3 ft each equals 6 ft of fabric).
	 *
	 * This is designed for WC 3.0+ and can replace the get_measurement_stock_amount()
	 * method above when WC 3.0+ is required.
	 *
	 * @since 3.11.4
	 *
	 * @param string[] $cart_item cart item data
	 * @return string[] updated cart item data
	 */
	public function set_measurement_stock_amount( $cart_item ) {

		if ( $cart_item['data'] instanceof \WC_Product && \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $cart_item['data'] ) ) {

			$settings = new \WC_Price_Calculator_Settings( $cart_item['data'] );

			// Get the needed unit, but default for backwards compatibility.
			$measurement_needed_unit  = isset( $_REQUEST['_measurement_needed_unit'] ) ? $_REQUEST['_measurement_needed_unit'] : $settings->get_pricing_unit();
			$measurement_needed_value = isset( $_REQUEST['_measurement_needed'] ) ? $_REQUEST['_measurement_needed'] : null;
			if ( isset( $cart_item['pricing_item_meta_data'], $cart_item['pricing_item_meta_data']['_measurement_needed'] ) && $measurement_needed_value !== $cart_item['pricing_item_meta_data']['_measurement_needed'] ) {
				$measurement_needed_value = $cart_item['pricing_item_meta_data']['_measurement_needed'];
			}

			// measurement instance
			$measurement_needed = new \WC_Price_Calculator_Measurement( $measurement_needed_unit, $measurement_needed_value );

			// quantity * measurement needed in pricing units
			$cart_item['quantity'] = $_REQUEST['quantity'] * $measurement_needed->get_value( $settings->get_pricing_unit() );
		}

		return $cart_item;
	}


	/**
	 * Set the quantity being added to the cart in WooCommerce 3.0+ when inventory is enabled.
	 *
	 * This needs to run in addition to set_measurement_stock_amount() because if quantity drops below 1, WooCommerce
	 *  will throw an exception due to stock amount not being enough, and the incoming requested quantity being "1".
	 * This filters quantity before the check so the requested quantity represents the measurement input.
	 *
	 * @since 3.12.0
	 *
	 * @param float $quantity the quantity being added to the cart
	 * @param int $product_id the product ID being added to the cart
	 * @return float the updated quantity
	 */
	public function set_measurement_add_to_cart_stock_amount( $quantity, $product_id ) {

		$product = wc_get_product( $product_id );

		if ( $product instanceof \WC_Product && \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) ) {

			$settings = new \WC_Price_Calculator_Settings( $product );

			// Get the needed unit, but default for backwards compatibility.
			$measurement_needed_unit = isset( $_REQUEST['_measurement_needed_unit'] ) ? $_REQUEST['_measurement_needed_unit'] : $settings->get_pricing_unit();
			$measurement_needed      = new \WC_Price_Calculator_Measurement( $measurement_needed_unit, isset( $_REQUEST['_measurement_needed'] ) ? $_REQUEST['_measurement_needed'] : null );

			// quantity * measurement needed in pricing units
			$quantity = $_REQUEST['quantity'] * $measurement_needed->get_value( $settings->get_pricing_unit() );
		}

		return $quantity;
	}


	/**
	 * Gets the checkout item quantity html, modifying for pricing calculator
	 * products with inventory enabled.  We replace the measurement quantity
	 * (ie 10 feet) with the item unit quantity (ie 2 pieces of fabric at 5
	 * feet each)
	 *
	 * @since 3.0
	 * @param string $item_quantity_html the checkout item quantity html
	 * @param array $values the cart item data
	 * @return string the checkout item name html
	 */
	public function get_checkout_item_quantity( $item_quantity_html, $values ) {

		$_product = $values['data'];

		if ( \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $_product ) && isset( $values['pricing_item_meta_data']['_quantity'] ) && $values['pricing_item_meta_data']['_quantity'] ) {
			// replace the item measurement quantity (10 feet) with the item unit quantity (2 pieces of fabric)
			$item_quantity_html = '<strong class="product-quantity">&times; ' . $values['pricing_item_meta_data']['_quantity'] . '</strong>';
		}

		return $item_quantity_html;
	}


	/**
	 * Filter the shipping packages, modifying the quantity for pricing
	 * calculator products with inventory enabled.  We replace the measurement
	 * quantity (ie 10 feet) with the item unit quantity (ie 2 pieces of fabric
	 * at 5 feet each).  This is done so that shipping methods can operate on
	 * the quantity of products (ie 2 pieces of fabric) with a weight that
	 * corresponds to the item unit quantity.
	 *
	 * @since 3.2
	 * @param array $packages shipping packages
	 * @return array shipping packages
	 */
	public function cart_shipping_packages( $packages ) {

		foreach ( array_keys( $packages ) as $index ) {

			foreach ( $packages[ $index ]['contents'] as $package_id => $values ) {

				$_product = $values['data'];

				if ( isset( $values['pricing_item_meta_data']['_quantity'] ) && $values['pricing_item_meta_data']['_quantity'] && \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $_product ) ) {
					// replace the item measurement quantity (10 feet) with the item unit quantity (2 pieces of fabric)
					// That way the quantity and weight are correct for shipping methods to calculate rates
					$packages[ $index ]['contents'][ $package_id ]['quantity'] = $values['pricing_item_meta_data']['_quantity'];
				}

			}

		}

		return $packages;
	}


	/**
	 * Returns the availability of the product, including the unit if this is a
	 * pricing calculator product with inventory enabled.  Ie, instead of '9 in
	 * stock' this might return '9 ft. in stock'
	 *
	 * @since 3.0
	 * @param array $return array with keys 'availability' and 'class'
	 * @param \WC_Product $product product object
	 * @return array
	 */
	public function get_availability_measurement( $return, $product ) {

		$class = $return['class'];

		if ( \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) && $product->managing_stock() ) {

			$settings = new \WC_Price_Calculator_Settings( $product );

			if ( $product->is_in_stock() ) {

				$total_stock = $this->get_total_stock( $product );

				if ( $total_stock > 0 ) {

					$format_option = get_option( 'woocommerce_stock_format' );

					switch ( $format_option ) {
						case 'no_amount' :
							return $return;  // nothing to be done
						break;
						case 'low_amount' :
							$low_amount = get_option( 'woocommerce_notify_low_stock_amount' );

							$format = ( $total_stock <= $low_amount ) ? __( 'Only %s %s left in stock', 'woocommerce-measurement-price-calculator' ) : __( 'In stock', 'woocommerce-measurement-price-calculator' );
						break;
						default :
							$format = __( '%s %s in stock', 'woocommerce-measurement-price-calculator' );
						break;
					}

					$availability = sprintf( $format, $total_stock, $settings->get_pricing_unit() );

					if ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
						$availability .= ' ' . __( '(backorders allowed)', 'woocommerce-measurement-price-calculator' );
					}

					$return = array( 'availability' => $availability, 'class' => $class );
				}
			}
		}

		return $return;
	}


	/**
	 * Get a product total stock amount.
	 *
	 * @since 3.11.0
	 * @param \WC_Product|\WC_Product_Variable $product A product.
	 * @return float|int
	 */
	private function get_total_stock( $product ) {

		$children = $product->get_children();

		if ( count( $children ) > 0 ) {

			$total_stock = max( 0, $product->get_stock_quantity() );

			foreach ( $children as $child_id ) {

				$child = wc_get_product( $child_id );

				if ( $child && 'yes' === $child->get_meta( '_manage_stock' ) ) {

					$stock        = $child->get_meta( '_stock' );
					$total_stock += max( 0, wc_stock_amount( $stock ) );
				}
			}

		} else {

			$total_stock = $product->get_stock_quantity();
		}

		return wc_stock_amount( $total_stock );
	}


	/**
	 * Gets the item quantity HTML snippet to display in the cart, modifying if
	 * the product uses the pricing calculator with inventory management enabled.
	 * Replaces the measurement quantity (ie 10 feet) with the item unit
	 * quantity (ie 2 pieces of fabric at 5 feet each)
	 *
	 * @since 3.0
	 * @param string $quantity_html the cart item quantity html snippet
	 * @param string $cart_item_key the cart item key
	 * @return string the cart item quantity html snippet
	 */
	public function get_cart_item_quantity( $quantity_html, $cart_item_key ) {

		$cart = WC()->cart->get_cart();

		if ( ! isset( $cart[ $cart_item_key ]['data'] ) ) {
			return $quantity_html;
		}

		/** @var \WC_Product $_product */
		$_product = $cart[ $cart_item_key ]['data'];

		if ( $_product->is_sold_individually() ) {
			return $quantity_html;
		}

		if ( \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $_product ) && isset( $cart[ $cart_item_key ]['pricing_item_meta_data']['_quantity'] ) && $cart[ $cart_item_key ]['pricing_item_meta_data']['_quantity'] ) {

			$quantity_html = woocommerce_quantity_input( array(
				'input_name'  => "cart[{$cart_item_key}][qty]",
				'input_value' => $cart[ $cart_item_key ]['pricing_item_meta_data']['_quantity'],
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(), $_product ),
			), $_product, false );
		}

		return $quantity_html;
	}


	/**
	 * Gets the item quantity HTML snippet to display in the mini-cart,
	 * modifying for pricing calculator products with inventory enabled.
	 * Replaces the measurement quantity (ie 10 feet) with the item unit
	 * quantity (ie 2 pieces of fabric at 5 feet each)
	 *
	 * @since 3.0
	 * @param string $quantity_html the mini-cart item quantity html snippet
	 * @param array $cart_item the cart item identified by $cart_item_key
	 * @param string $cart_item_key the mini-cart item key
	 * @return string the mini-cart item quantity html snippet
	 */
	public function get_widget_cart_item_quantity( $quantity_html, $cart_item, $cart_item_key ) {

		$cart = WC()->cart->get_cart();

		$_product = $cart_item['data'];

		if ( \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $_product ) && isset( $cart_item['pricing_item_meta_data']['_quantity'] ) && $cart_item['pricing_item_meta_data']['_quantity'] ) {

			$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );

			$quantity_html = '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['pricing_item_meta_data']['_quantity'], $product_price ) . '</span>';
		}

		return $quantity_html;
	}


	/**
	 * Invoked after a cart item's quantity is updated, and if the item in
	 * question is for a pricing calculator product with inventory enabled, and
	 * it's being added to the cart or the cart is being updated or proceeding
	 * to checkout, we keep track of the unit quantity which is displayed to the
	 * customer.  The unit quantity would be '2' pieces of 3 foot fabric, for
	 * instance.
	 *
	 * @since 3.0
	 * @param string $cart_item_key the cart item key
	 * @param numeric $quantity the item quantity
	 */
	public function after_cart_item_quantity_update( $cart_item_key, $quantity ) {

		$cart_items = WC()->cart->get_cart();
		if ( isset( $cart_items[ $cart_item_key ] ) && $cart_items[ $cart_item_key ] ) {

			// we want the product, not the variation
			$product = wc_get_product( $cart_items[ $cart_item_key ]['product_id'] );

			if ( \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) ) {
				// save the actual item quantity (ie *2* pieces of fabric at 3 feet each)

				if ( isset( $_REQUEST['quantity'] ) ) {

					// add-to-cart actions
					WC()->cart->cart_contents[ $cart_item_key ]['pricing_item_meta_data']['_quantity'] += $_REQUEST['quantity'];

					// in WC 3.0+, adding a 2nd quantity to the cart of a product with the same measurements (e.g. clicking add to cart twice)
					// uses WC_Cart::set_quantity() and skips the woocommerce_add_cart_item filter which prevents us from changing the cart quantity to reflect
					// the total measured amount (e.g. 2 pieces of fabric at 3 feet each is a total quantity of 6, when priced on a per-foot basis).
					// This resets the cart quantity to the total measured amount. When we can require WC 3.1+, we can make use of the
					// woocommerce_add_to_cart_quantity filter added in https://github.com/woocommerce/woocommerce/commit/494fa0974c955796a3168892499bf370aa9ce8f2
					// to set the correct quantity prior to WC_Cart::set_quantity() being called. {MR 2017-06-21}
					$settings = new \WC_Price_Calculator_Settings( $product );

					// Get the needed unit, but default for backwards compatibility.
					$measurement_needed_unit = isset( $_REQUEST['_measurement_needed_unit'] ) ? $_REQUEST['_measurement_needed_unit'] : $settings->get_pricing_unit();
					$measurement_needed      = new \WC_Price_Calculator_Measurement( $measurement_needed_unit, isset( $_REQUEST['_measurement_needed'] ) ? $_REQUEST['_measurement_needed'] : null );

					// quantity * measurement needed in pricing units
					WC()->cart->cart_contents[ $cart_item_key ]['quantity'] = ( WC()->cart->cart_contents[ $cart_item_key ]['pricing_item_meta_data']['_quantity'] * $measurement_needed->get_value( $settings->get_pricing_unit() ) );

				} elseif ( ! empty( $_POST['update_cart'] ) || ! empty( $_POST['proceed'] ) ) {

					// update cart/proceed to checkout
					$cart_totals = isset( $_POST['cart'] ) ? $_POST['cart'] : '';

					if ( isset( $cart_totals[ $cart_item_key ]['qty'] ) && $cart_totals[ $cart_item_key ]['qty'] ) {
						WC()->cart->cart_contents[ $cart_item_key ]['pricing_item_meta_data']['_quantity'] = preg_replace( "/[^0-9\.]/", "", $cart_totals[ $cart_item_key ]['qty'] );
					}
				}
			}
		}
	}


	/**
	 * Gets the measurement stock quantity for the given item if its a pricing
	 * calculator item with inventory enabled, for purposes of reducing the
	 * product stock.  Ie, if $quantity is 2 and the item is 3 ft fabric, the
	 * measurement stock returned would be 6
	 *
	 * @since 3.0
	 * @param int|float $quantity the cart item quantity
	 * @param \WC_Order $order the order object
	 * @param array $item the order item
	 * @return int|float
	 */
	public function get_order_item_measurement_quantity( $quantity, $order, $item ) {

		// always need the actual parent product, not the useless variation product
		$product = wc_get_product( $item['product_id'] );

		if (    isset( $item['item_meta']['_measurement_data'][0] )
		     && $item['item_meta']['_measurement_data'][0]
		     && \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) ) {

			$measurement_data = maybe_unserialize( $item['item_meta']['_measurement_data'][0] );
			$settings         = new \WC_Price_Calculator_Settings( $product );

			// get the measurement quantity (ie item quantity is '2' pieces of fabric at 3 ft each, so the measurement quantity is '6'
			$quantity *= \WC_Price_Calculator_Measurement::convert( $measurement_data['_measurement_needed'], $measurement_data['_measurement_needed_unit'], $settings->get_pricing_unit() );
		}

		return $quantity;
	}


	/**
	 * Modifies the 'Backordered' order item meta name to include the units for backordered pricing calculator products with inventory enabled
	 *
	 * For example: "Backordered (ft.): 12.4"
	 *
	 * @internal
	 *
	 * @since 3.0
	 *
	 * @param string $backordered_text the backordered text
	 * @param null|\WC_Order_Item_Product $item the backordered item (available in callback from WC 3.2 onwards)
	 * @return string the backordered text, including units if available
	 */
	public function get_backordered_item_meta_name( $backordered_text, $item = null ) {

		// TODO the $item argument will be added from WC 3.2 onwards, remove hook BC when 3.2 is the minimum required version {FN 2017-07-26}
		if ( empty( $item ) ) {

			$cart_contents = WC()->cart->get_cart();
			$regular_items = 0;
			$pricing_units = array();

			// in WC < 3.2 we have no context where the 'Backordered' label is printed so we can make some assumption based on cart contents
			foreach ( $cart_contents as $key => $cart_item ) {
				if ( \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $cart_item['data'] ) ) {
					$settings        = new \WC_Price_Calculator_Settings( $cart_item['data'] );
					$pricing_units[] = $settings->get_pricing_unit();
				} else {
					$regular_items++;
				}
			}

			if ( 0 === $regular_items ) {

				// all cart items are MPC items
				if ( count( array_count_values( $pricing_units ) ) > 1 ) {
					// there are at least two different pricing units
					$backordered_text = __( 'Backordered measure', 'woocommerce-measurement-price-calculator' );
				} else {
					// there is only one pricing unit being used across one or more MPC items
					$backordered_text .= sprintf( ' (%s)', current( $pricing_units ) );
				}

			} elseif ( count( $pricing_units ) > 0 ) {

				// there are both regular items and one or more MPC items
				$backordered_text = __( 'Backordered quantity or measure', 'woocommerce-measurement-price-calculator' );
			}

		} elseif ( $item instanceof \WC_Order_Item_Product && \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $item->get_product() ) ) {

			$settings = new \WC_Price_Calculator_Settings( $item->get_product() );

			// in WC 3.2+ we have context to output the pricing unit for the current backordered item
			$backordered_text .= sprintf( ' (%s)', $settings->get_pricing_unit() );
		}

		return $backordered_text;
	}


	/**
	 * Filter to set the measurement quantity for pricing calculator type
	 * products with inventory enabled so that they can be ordered again.
	 * The item quantity is changed to the measurement quantity, such that if
	 * the item quantity is 2 and the item is 3 ft of fabric, the measurement
	 * quantity will be 6
	 *
	 * @since 3.0
	 * @param array $items array of item arrays
	 * @param WC_Order $order the original order
	 * @return array the item
	 */
	public function order_again_item_set_quantity( $items, $order ) {

		if ( isset( $_GET['order_again'] ) ) {
			foreach ( $items as &$item ) {

				// skip non-product line items like tax, etc
				if ( 'line_item' !== $item['type'] ) {
					continue;
				}

				$product = wc_get_product( $item['product_id'] );

				if (    isset( $item['item_meta']['_measurement_data'][0] )
				     && $item['item_meta']['_measurement_data'][0]
				     && \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) ) {

					$measurement_data  = maybe_unserialize( $item['item_meta']['_measurement_data'][0] );
					$settings          = new \WC_Price_Calculator_Settings( $product );
					$total_measurement = new \WC_Price_Calculator_Measurement( $measurement_data['_measurement_needed_unit'], $measurement_data['_measurement_needed'] );

					// save the item quantity for order_again_cart_item_data()
					$item['item_meta']['_quantity'][0] = $item['qty'];

					// save the unit quantity (ie item quantity is '2' pieces of fabric at 3 ft each, so the unit quantity is '6'
					$item['qty'] *= $total_measurement->get_value( $settings->get_pricing_unit() );
				}
			}
		}

		return $items;
	}


	/** Admin methods ******************************************************/


	/**
	 * Manage the order stock (whether restore or reduce) from the order admin
	 * returning the true product stock change if this is for a pricing calculator
	 * product/item with inventory enabled.  Ie 2 pieces of cloth at 3 ft each
	 * we'd want to return 6
	 *
	 * @since 3.0
	 * @param int|float $quantity the new quantity
	 * @param string $item_id the order item identifier
	 * @return int|float $quantity the measurement quantity
	 */
	public function admin_manage_order_stock( $quantity, $item_id ) {

		$order_id    = absint( $_POST['order_id'] );
		$order       = wc_get_order( $order_id );
		$order_items = $order->get_items();
		$product     = wc_get_product( $order_items[ $item_id ]['product_id'] );

		if (    isset( $order_items[ $item_id ]['measurement_data'] )
		     && \WC_Price_Calculator_Product::pricing_calculator_inventory_enabled( $product ) ) {

			$settings         = new \WC_Price_Calculator_Settings( $product );
			$measurement_data = maybe_unserialize( $order_items[ $item_id ]['measurement_data'] );
			$total_amount     = new \WC_Price_Calculator_Measurement( $measurement_data['_measurement_needed_unit'], $measurement_data['_measurement_needed'] );

			// this is a pricing calculator product so we want to return the
			//  quantity in terms of units, ie 2 pieces of cloth at 3 ft each = 6
			$quantity *= $total_amount->get_value( $settings->get_pricing_unit() );
		}

		return $quantity;
	}


}
