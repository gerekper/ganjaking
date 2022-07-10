<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_XR_Line_Item_Manager {

	/**
	 * @var WC_XR_Settings
	 */
	private $settings;

	/**
	 * WC_XR_Line_Item_Manager constructor.
	 *
	 * @param WC_XR_Settings $settings
	 */
	public function __construct( WC_XR_Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Build product line items
	 *
	 * @param WC_Order $order
	 *
	 * @return array<WC_XR_Line_Item>
	 */
	public function build_products( $order ) {
		$items = $order->get_items();
		$this->order = $order;

		// The line items
		$line_items = array();

		// Check if there are any order items
		if ( count( $items ) > 0 ) {

			// Get the sales account
			$sales_account = $this->settings->get_option( 'sales_account' );
			// Check we need to send sku's
			$send_inventory = ( ( 'on' === $this->settings->get_option( 'send_inventory' ) ) ? true : false );

			// Add order items as line items
			foreach ( $items as $item ) {

				// Get the product
				$product = $item->get_product();

				// Create Line Item object
				$line_item = new WC_XR_Line_Item( $this->settings );
				$description = self::detexturize( $item['name'] );
				// Variation? Add attribute data to the description
				if ( 'WC_Product_Variation' === get_class( $product )  || 'WC_Product_Subscription_Variation' === get_class( $product ) ) {
					$attributes = array();

					foreach ( $item['item_meta_array'] as $meta_id => $meta ) {
						if ( empty( $meta->value ) || is_serialized( $meta->value ) || '_' === substr( $meta->key, 0, 1 ) ) {
							continue;
						}

						$attributes[] = $meta->key . ": " . $meta->value;

					}

					if ( 0 < count( $attributes ) ) {
						$description .= " (" . implode( ', ', $attributes ) . ")";
					}

				}
				$line_item->set_description( $description );

				// Set account code
				$line_item->set_account_code( $sales_account );

				// Send SKU?
				if ( $send_inventory && $product ) {
					$line_item->set_item_code( $product->get_sku() );
				}

				// Send Discount?
				$item_without_discounts = $order->get_item_subtotal( $item, false, false );

				// Invoice decimal precision.
				$precision     = 'on' === $this->settings->get_option( 'four_decimals' ) ? 4 : 2;
				$item_discount = round( ( $item->get_subtotal() - $item->get_total() ), $precision );
				if ( 0.001 < abs( $item_discount ) ) {
					$line_item->set_discount_amount( $item_discount );
				}

				// Set the Unit Amount
				$line_item->set_unit_amount( $item_without_discounts );

				// Quantity
				$line_item->set_quantity( $item['qty'] );

				// Line Amount
				$line_item->set_line_amount( $item['line_subtotal'] );

				// Tax Amount
				$line_item->set_tax_amount( $item['line_tax'] );

				// Tax Rate
				$item_tax_status   = $product ? $product->get_tax_status() : 'taxable';
				if ( 'taxable' === $item_tax_status ) {
					add_filter( 'woocommerce_get_tax_location', array( $this, 'set_tax_location' ), 10, 2 );
					$rates = WC_Tax::get_rates( $item['tax_class'] );
					remove_filter( 'woocommerce_get_tax_location', array( $this, 'set_tax_location' ) );
					reset( $rates );
					if ( ! empty( $rates ) ) {
						$line_item->set_tax_rate( $rates[ key( $rates ) ] );
					}
				} else {
					$rates['rate'] = 0;
					$line_item->set_tax_rate( $rates );
				}

				$line_item->set_is_digital_good( $product && ! $product->needs_shipping() );

				// Add Line Item to array.
				$line_items[] = apply_filters( 'woocommerce_xero_line_item_product', $line_item, $item, $order );
			}
		}

		return $line_items;
	}

	/**
	 * Replace specific html entities with XML safe substitutes, strip everything else
	 *
	 * @param  string $string
	 *
	 * @return string
	 */
	public static function detexturize( $string ) {
		$string = strip_tags( $string );

		$replacements = array(
			'&#8211;' => '-',  '&ndash;' => '-',
			'&#8212;' => '-',  '&mdash;' => '-',
			'&#8216;' => '\'', '&lsquo;' => '\'',
			'&#8217;' => '\'', '&rsquo;' => '\'',
			'&#8220;' => '"',  '&ldquo;' => '"',
			'&#8221;' => '"',  '&rdquo;' => '"',
		);

		foreach( $replacements as $needle => $replacement ) {
			$string = str_replace( $needle, $replacement, $string );
		}

		$string = preg_replace( '/&#?[a-z0-9]{2,8};/i', '', $string );

		return $string;
	}

	/**
	 * Sets the tax location (without needing a session) so we can calculate
	 * the correct rates for our items.
	 */
	public function set_tax_location( $location, $tax_class ) {
		$shipping_methods = array();
		foreach ( $this->order->get_shipping_methods() as $method ) {
			$shipping_methods[] = $method['method_id'];
		}

		$tax_based_on = get_option( 'woocommerce_tax_based_on' );

		if ( true == apply_filters( 'woocommerce_apply_base_tax_for_local_pickup', true ) && sizeof( array_intersect( $shipping_methods, apply_filters( 'woocommerce_local_pickup_methods', array( 'local_pickup' ) ) ) ) > 0 ) {
			$tax_based_on = 'base';
		}

		$old_wc = version_compare( WC_VERSION, '3.0', '<' );

		/* 
		 * Check if there is a shipping country set(it will be empty for orders with all virtual items).
		 * If it is empty, calculate tax based on the billing address to ensure tax rates are returned.
		 */
		$shipping_country = $old_wc ? $this->order->shipping_country : $this->order->get_shipping_country();

		if ( 'base' === $tax_based_on ) {
			$country  = WC()->countries->get_base_country();
			$state    = WC()->countries->get_base_state();
			$postcode = WC()->countries->get_base_postcode();
			$city     = WC()->countries->get_base_city();
		} elseif ( 'billing' === $tax_based_on || ! $shipping_country ) {
			$country  = $old_wc ? $this->order->billing_country : $this->order->get_billing_country();
			$state    = $old_wc ? $this->order->billing_state : $this->order->get_billing_state();
			$postcode = $old_wc ? $this->order->billing_postcode : $this->order->get_billing_postcode();
			$city     = $old_wc ? $this->order->billing_city : $this->order->get_billing_city();
		} else {
			$country  = $old_wc ? $this->order->shipping_country : $this->order->get_shipping_country();
			$state    = $old_wc ? $this->order->shipping_state : $this->order->get_shipping_state();
			$postcode = $old_wc ? $this->order->shipping_postcode : $this->order->get_shipping_postcode();
			$city     = $old_wc ? $this->order->shipping_city : $this->order->get_shipping_city();
		}

		return array( $country, $state, $postcode, $city );
	}


	/**
	 * Build shipping line item
	 *
	 * @since 1.6.0
	 * @version 1.7.7
	 * @param WC_Order $order
	 * @return WC_XR_Line_Item
	 */
	public function build_shipping( $order ) {
		$old_wc = version_compare( WC_VERSION, '3.0', '<' );

		$order_shipping = $old_wc ? $order->order_shipping : $order->get_shipping_total();
		if ( $order_shipping > 0 ) {

			$shipping_items = $order->get_items( 'shipping' );

			foreach ( $shipping_items as $item ) {
				// Create Line Item object.
				$line_item = new WC_XR_Line_Item( $this->settings );

				// Shipping Description.
				$line_item->set_description( 'Shipping Charge' );

				// Shipping Quantity.
				$line_item->set_quantity( 1 );

				// Shipping account code.
				$line_item->set_account_code( $this->settings->get_option( 'shipping_account' ) );

				// Shipping cost.
				$line_item->set_unit_amount( $item->get_total() );

				// Shipping tax.
				$shipping_taxes     = $item->get_taxes();
				$order_shipping_tax = array_sum( $shipping_taxes['total'] );
				$line_item->set_tax_amount( $order_shipping_tax );
				$tax_rate_id = 0;

				if ( count( $shipping_taxes['total'] ) > 0 ) {
					$tax_rate_id = array_search( max( $shipping_taxes['total'] ), $shipping_taxes['total'] );
				}
				// Now that we have the tax rate ID, look up the rate.
				$tax_rate = floatval( WC_Tax::get_rate_percent( $tax_rate_id ) );

				$line_item->set_tax_rate(
					array(
						'rate'                  => $tax_rate,
						'shipping'              => true,  // Whether or not this tax rate also gets applied to shipping.
						'compound'              => true, // Compound rates are applied on top of other tax rates.
						'is_shipping_line_item' => true, // Make sure WC_XR_Line_Item can encode this special entry properly when needed, e.g. for AU, NZ, GB.
					)
				);

				$line_items[] = $line_item;
			}
			return $line_items;
		}
	}

	/**
	 * Build fee line items.
	 *
	 * @param WC_Order $order
	 * @param WC_XR_Line_Item[] $line_items
	 *
	 * @return <array>WC_XR_Line_Item
	 */
	public function build_fees( $order ) {

		$items      = $order->get_fees();
		$line_items = array();

		if ( ! $items || 0 == count( $items ) ) {
			return $line_items;
		}

		// Add order items as line items.
		foreach ( $items as $fee ) {

			// Create Line Item object.
			$line_item = new WC_XR_Line_Item( $this->settings );

			$line_item->set_description( $fee->get_name() );

			if ( $this->settings->get_option( 'fees_account' ) ) {

				$line_item->set_account_code( $this->settings->get_option( 'fees_account' ) );

			} else {

				$line_item->set_account_code( $this->settings->get_option( 'sales_account' ) );

			}

			// Set the Unit Amount.
			$line_item->set_unit_amount( $fee->get_total() );

			// Set line amount.
			$line_item->set_line_amount( $fee->get_total() );

			// Add Quantity.
			$line_item->set_quantity( 1 );

			// Add Tax Amount.
			$line_item->set_tax_amount( $fee->get_total_tax() );

			// Add Tax Rate.
			$item_tax_status   = $fee->get_tax_status();
			if ( 'taxable' === $item_tax_status ) {
				add_filter( 'woocommerce_get_tax_location', array( $this, 'set_tax_location' ), 10, 2 );
				$rates = WC_Tax::get_rates( $fee->get_tax_class() );
				remove_filter( 'woocommerce_get_tax_location', array( $this, 'set_tax_location' ) );
				reset( $rates );
				if ( ! empty( $rates ) ) {
					$tax_rate = $rates[ key( $rates ) ];
					// Define fee flag.
					$tax_rate['is_fee_line_item'] = true;
					$line_item->set_tax_rate( $tax_rate );
				} else {
					// Fee has no tax rate -- add zero value for later Tax Exempt handle.
					$line_item->set_tax_rate(
						array(
							'rate'             => 0,
							'shipping'         => false, // Whether or not this tax rate also gets applied to shipping.
							'compound'         => true, // Compound rates are applied on top of other tax rates.
							'is_fee_line_item' => true, // Make sure WC_XR_Line_Item can encode this special entry properly when needed, e.g. for AU, NZ, GB.
						)
					);
				}
			}

			// Add Line Item to array.
			$line_items[] = $line_item;
		}

		return $line_items;
	}

	/**
	 * Build a correction line if needed
	 *
	 * @param WC_Order $order
	 * @param WC_XR_Line_Item[] $line_items
	 *
	 * @return WC_XR_Line_Item
	 */
	public function build_correction( $order, $line_items ) {

		// Line Item
		$correction_line = null;

		// The line item total in cents
		$line_total = 0;

		// Invoice precision
		$precision = 'on' === $this->settings->get_option( 'four_decimals' ) ? 4 : 2;

		// Get a sum of the amount and tax of all line items
		if ( count( $line_items ) > 0 ) {

			foreach ( $line_items as $line_item ) {
				$line_val    = round( $line_item->get_unit_amount(), $precision ) * $line_item->get_quantity() - $line_item->get_discount_amount();
				$line_tax    = round( $line_item->get_tax_amount(), $precision );
				$line_total += $line_val + $line_tax ;
			}
		}

		// Line total in cents
		$line_total = round( $line_total, 2 );

		// Order total in cents
		$order_total = round( $order->get_total(), 2 );

		// Check if there's a difference
		if ( $order_total !== $line_total ) {

			// Calculate difference
			$diff = $order_total - $line_total;

			// Get rounding account code
			$account_code = $this->settings->get_option( 'rounding_account' );

			// Check rounding account code
			if ( '' !== $account_code ) {

				// Create correction line item
				$correction_line = new WC_XR_Line_Item( $this->settings );

				// Correction description
				$correction_line->set_description( 'Rounding adjustment' );

				// Correction quantity
				$correction_line->set_quantity( 1 );

				// Correction amount
				$correction_line->set_unit_amount( $diff );

				$correction_line->set_account_code( $account_code );
			} else {

				// There's a rounding difference but no rounding account
				$logger = new WC_XR_Logger( $this->settings );
				$logger->write( "There's a rounding difference but no rounding account set in XERO settings." );
			}
		}

		return $correction_line;
	}

	/**
	 * Build line items
	 *
	 * @param WC_Order $order
	 *
	 * @return array<WC_XR_Line_Item>
	 */
	public function build_line_items( $order ) {

		// Grab all products.
		$products = $this->build_products( $order );

		// Grab all fees.
		$fees     = $this->build_fees( $order );

		// Merge $line_items with products and fees.
		$line_items = array_merge( $products, $fees );

		// Add shipping line item if there's shipping.
		$order_shipping = is_callable( array( $order, 'get_shipping_total' ) ) ? $order->get_shipping_total() : $order->order_shipping;
		if ( $order_shipping > 0 ) {
			$line_items = array_merge( $line_items, $this->build_shipping( $order ) );
		}

		// Build correction.
		$correction = $this->build_correction( $order, $line_items );
		if ( null !== $correction ) {
			$line_items[] = $correction;
		}

		// Return line items.
		return $line_items;
	}

}
