<?php

class WoocommerceProductFeedsMeasurementPriceCalculator {

	/**
	 * Attach relevant filters and hooks.
	 *
	 * @return void
	 */
	public function run() {
		add_filter( 'woocommerce_gpf_feed_item_google', [ $this, 'modify_feed_item' ], 10, 2 );
		add_action( 'woocommerce_init', [ $this, 'woocommerce_init' ], 99 );
	}

	/**
	 * Removes the integration previously provided in the measurement price calculator plugin.
	 *
	 * @return void
	 */
	public function woocommerce_init() {
		$compatibility_instance = wc_measurement_price_calculator()->get_compatibility_instance();
		remove_filter(
			'woocommerce_gpf_feed_item',
			[ $compatibility_instance, 'google_product_feed_pricing_rules_price_adjustment' ],
			10
		);
	}

	/**
	 * @param $feed_item
	 * @param $product
	 *
	 * @return mixed
	 */
	public function modify_feed_item( $feed_item, $product ) {

		// Bail if we don't need, or want to generate unit_pricing_measure fields.
		if ( ! empty( $feed_item->additional_elements['unit_pricing_measure'] ) ||
			 ! empty( $feed_item->additional_elements['unit_pricing_base_measure'] ) ||
			 ! apply_filters( 'woocommerce_gpf_mpc_send_unit_pricing', true, $product ) ) {
			return $feed_item;
		}

		$product_settings = new \WC_Price_Calculator_Settings( $product );
		if ( ! \WC_Price_Calculator_Product::pricing_per_unit_enabled( $product ) ) {
			// Do nothing if price-per-unit not being used.
			return $feed_item;
		}

		if ( $product_settings->pricing_rules_enabled() ) {
			// If they had defined a pricing table find the lowest quantity and use the price & UOM/base-UOM from that.
			$this->user_defined_calculator_prices( $feed_item, $product, $product_settings );
		} else {
			// Leave the price as-is, but indicate the UOM & base UOM for the per-unit price.
			$this->quantity_calculator_per_unit( $feed_item, $product, $product_settings );
		}

		return $feed_item;
	}

	/**
	 * Product has a pricing table. Pull out the price/quantities relating to the lowest qty.
	 *
	 * @param $feed_item
	 * @param $product
	 * @param $product_settings
	 *
	 * @return void
	 */
	private function user_defined_calculator_prices( $feed_item, $product, $product_settings ) {
		// Get the rule corresponding to the lower quantity.
		$pricing_rules   = $product_settings->get_pricing_rules();
		$lowest_qty_rule = $this->get_lowest_qty_rule( $pricing_rules );
		$lowest_qty      = $lowest_qty_rule['range_start'];
		if ( $lowest_qty < 1 ) {
			$lowest_qty = 1;
		}

		// Pull the pricing from that.
		$price         = $lowest_qty_rule['price'] * $lowest_qty;
		$regular_price = $lowest_qty_rule['regular_price'] * $lowest_qty;
		$sale_price    = $lowest_qty_rule['sale_price'] ? $lowest_qty_rule['sale_price'] * $lowest_qty : '';
		$this->set_price_property( $feed_item, $product, 'price', $price );
		$this->set_price_property( $feed_item, $product, 'regular_price', $regular_price );
		$this->set_price_property( $feed_item, $product, 'sale_price', $sale_price );

		// Get the WooCommerce pricing UOM.
		$pricing_unit = $product_settings->get_pricing_unit();

		// Map to a supported pricing UOM.
		$feed_pricing_unit = $this->map_pricing_unit( $pricing_unit );

		// Get the quantities in the supported pricing unit.
		$lowest_qty = $this->map_measurement_value( $lowest_qty, $pricing_unit );
		$single_qty = $this->map_measurement_value( 1, $pricing_unit );

		// Set the fields in the feed.
		$feed_item->additional_elements['unit_pricing_measure']      = [ $lowest_qty . $feed_pricing_unit ];
		$feed_item->additional_elements['unit_pricing_base_measure'] = [ $single_qty . $feed_pricing_unit ];
	}

	/**
	 * Quantity calculator with per unit pricing.
	 *
	 * @param $feed_item
	 * @param $product
	 * @param WC_Price_Calculator_Settings $product_settings
	 *
	 * @return void
	 */
	private function quantity_calculator_per_unit( $feed_item, $product, WC_Price_Calculator_Settings $product_settings ) {
		// Check we have a measurement to calculate per-unit pricing.
		$measurement = \WC_Price_Calculator_Product::get_product_measurement( $product, $product_settings );
		if ( ! $measurement ) {
			return;
		}
		$pricing_unit = $product_settings->get_pricing_unit();
		$measurement->set_unit( $pricing_unit );
		$measurement_value = $measurement->get_value();
		if ( ! $measurement_value ) {
			return;
		}

		// Map to a supported pricing UOM.
		$feed_pricing_unit = $this->map_pricing_unit( $pricing_unit );

		// Get the quantities in the supported pricing unit.
		$feed_measurement_value = $this->map_measurement_value( $measurement_value, $pricing_unit );
		$single_qty             = $this->map_measurement_value( 1, $pricing_unit );

		// Add the unit pricing fields to the feed item.
		$feed_item->additional_elements['unit_pricing_measure']      = [ $feed_measurement_value . $feed_pricing_unit ];
		$feed_item->additional_elements['unit_pricing_base_measure'] = [ $single_qty . $feed_pricing_unit ];
	}

	/**
	 * Sets the inc/ex tax variants of a price on the feed_item object.
	 *
	 * @param $feed_item
	 * @param $product
	 * @param $property
	 * @param $price
	 *
	 * @return void
	 */
	private function set_price_property( $feed_item, $product, $property, $price ) {
		$ex_tax_property  = $property . '_ex_tax';
		$inc_tax_property = $property . '_inc_tax';
		$price_data       = [
			'qty'   => 1,
			'price' => $price,
		];

		if ( is_null( $price ) || '' === $price ) {
			$feed_item->{$ex_tax_property}  = '';
			$feed_item->{$inc_tax_property} = '';

			return;
		}

		$feed_item->{$ex_tax_property}  = wc_get_price_excluding_tax( $product, $price_data );
		$feed_item->{$inc_tax_property} = wc_get_price_including_tax( $product, $price_data );
	}

	/**
	 * Get the pricing rule that contains the lowest purchasable quantity.
	 *
	 * @param $pricing_rules
	 *
	 * @return mixed|null
	 */
	private function get_lowest_qty_rule( $pricing_rules ) {
		$matched_rule = null;
		$matched_qty  = PHP_INT_MAX;
		foreach ( $pricing_rules as $rule ) {
			if ( $rule['range_start'] < $matched_qty ) {
				$matched_qty  = $rule['range_start'];
				$matched_rule = $rule;
			}
		}

		return $matched_rule;
	}

	/**
	 * Map units of measures from MPC to the values Google expect.
	 *
	 * @param $uom
	 *
	 * @return mixed|string
	 */
	private function map_pricing_unit( $uom ) {
		switch ( $uom ) {
			// Area UOMs
			case 'ha':
			case 'sq km':
			case 'sq cm':
			case 'sq mm':
			case 'acs':
			case 'sq. mi.':
			case 'sq. yd.':
				// These are supported via conversion - see map_measurement_value()
				$uom = 'sqm';
				break;
			case 'sq m':
				$uom = 'sqm';
				break;
			case 'sq. ft.':
				$uom = 'sqft';
				break;
			// Length UOMs
			case 'km':
			case 'mi':
				// These are supported via conversion - see map_measurement_value()
				$uom = 'm';
				break;
			case 'mm':
				// This is supported via conversion - see map_measurement_value()
				$uom = 'cm';
				break;
			// Volume UOMs
			case 'cu m':
				$uom = 'cbm';
				break;
			case 'cup':
				// This is supported via conversion - see map_measurement_value()
				$uom = 'floz';
				break;
			case 'fl. oz.':
				$uom = 'floz';
				break;
			case 'cu. yd.':
			case 'cu. ft.':
			case 'cu. in.':
				// These are supported via conversion - see map_measurement_value()
				$uom = 'cbm';
				break;
			// Weight UOMs
			case 't':
			case 'tn':
				// These are supported via conversion - see map_measurement_value()
				$uom = 'kg';
				break;
			case 'lbs':
				$uom = 'lb';
				break;
		}

		return $uom;
	}

	/**
	 * Map a value in the given unit of measure to the relevant value in the Google-supported UOM.
	 *
	 * @param $value
	 * @param $source_uom
	 *
	 * @return float|int|mixed
	 */
	private function map_measurement_value( $value, $source_uom ) {
		switch ( $source_uom ) {
			case 'ha':
				return $value * 10000;
				break;
			case 'sq km':
				return $value * 1000000;
				break;
			case 'sq cm':
				return $value * 0.0001;
				break;
			case 'sq mm':
				return $value * 0.000001;
				break;
			case 'acs':
				return $value * 4046.86;
				break;
			case 'sq. mi.':
				return $value * 2589988;
				break;
			case 'sq. yd.':
				return $value * 0.836127;
				break;
			case 'km':
				return $value * 1000;
				break;
			case 'mi':
				return $value * 1609.34;
				break;
			case 'mm':
				return $value * 0.1;
				break;
			case 'cup':
				return $value * 8;
				break;
			case 'cu. yd.':
				return $value * 0.764555;
				break;
			case 'cu. ft.':
				return $value * 0.0283168;
				break;
			case 'cu. in.':
				return $value * 0.0000163871;
				break;
			case 't':
				return $value * 1000;
				break;
			case 'tn':
				return $value * 907.1847;
				break;
		}

		return $value;
	}
}
