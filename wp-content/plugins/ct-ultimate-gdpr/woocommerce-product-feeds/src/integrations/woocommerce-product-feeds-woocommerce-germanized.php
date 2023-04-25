<?php

class WoocommerceProductFeedsWoocommerceGermanized {

	/**
	 * @var Mapping from germanized's list of UOMs to Google's accepted UOMs.
	 */
	const UOM_MAP = [
		'lbs' => 'lb',
	];

	/**
	 * CCheck versions and run the integration if suitable.
	 */
	public function run() {
		// Check version is 3.7.0 or higher.
		$instance = WooCommerce_Germanized::instance();
		if ( empty( $instance->version ) || version_compare( '3.7.0', $instance->version, '>=' ) ) {
			return;
		}
		add_filter( 'woocommerce_gpf_custom_field_list', array( $this, 'register_fields' ) );
	}

	/**
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function register_fields( $fields ) {
		$fields['disabled:wcgermanized'] = __(
			'-- Fields from "WooCommerce Germanized" --',
			'woocommerce_gpf'
		);

		$fields['meta:_ts_gtin'] = _x(
			'GTIN field',
			'Name of field from WooCommerce Germanized extension',
			'woocommerce_gpf'
		);

		$fields['meta:_ts_mpn'] = _x(
			'MPN field',
			'Name of field from WooCommerce Germanized extension',
			'woocommerce_gpf'
		);

		$fields['method:WoocommerceProductFeedsWoocommerceGermanized::get_product_units'] = _x(
			'Product units',
			'Name of field from WooCommerce Germanized extension',
			'woocommerce_gpf'
		);

		$fields['method:WoocommerceProductFeedsWoocommerceGermanized::get_unit_price_units'] = _x(
			'Unit price units',
			'Name of field from WooCommerce Germanized extension',
			'woocommerce_gpf'
		);

		return $fields;
	}

	/**
	 * @param WC_Product $wc_product
	 *
	 * @return string
	 */
	public static function get_product_units( \WC_Product $wc_product ) {
		$uom          = self::get_uom_for_product( $wc_product );
		$unit_product = $wc_product->get_meta( '_unit_product', true );
		if ( empty( $unit_product ) || empty( $uom ) ) {
			return '';
		}

		return $unit_product . $uom;
	}

	/**
	 * @param WC_Product $wc_product
	 *
	 * @return string
	 */
	public static function get_unit_price_units( \WC_Product $wc_product ) {
		$uom              = self::get_uom_for_product( $wc_product );
		$unit_price_units = $wc_product->get_meta( '_unit_base', true );
		if ( empty( $unit_price_units ) || empty( $uom ) ) {
			return '';
		}

		return $unit_price_units . $uom;
	}

	/**
	 * @param WC_Product $wc_product
	 *
	 * @return string
	 */
	public static function get_uom_for_product( \WC_Product $wc_product ) {
		$specific_uom = (string) $wc_product->get_meta( '_unit', true );
		if ( ! empty( $specific_uom ) ) {
			return isset( self::UOM_MAP[ $specific_uom ] ) ?
				self::UOM_MAP[ $specific_uom ] :
				$specific_uom;
		}
		$parent_id = $wc_product->get_parent_id();
		if ( empty( $parent_id ) ) {
			return '';
		}
		$parent_product = wc_get_product( $parent_id );
		$parent_uom     = (string) $parent_product->get_meta( '_unit', true );

		return isset( self::UOM_MAP[ $parent_uom ] ) ?
			self::UOM_MAP[ $parent_uom ] :
			$parent_uom;
	}
}
