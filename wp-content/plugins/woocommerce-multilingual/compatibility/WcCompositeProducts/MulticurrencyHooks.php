<?php

namespace WCML\Compatibility\WcCompositeProducts;

use WCML_Compatibility_Helper;

class MulticurrencyHooks implements \IWPML_Action {

	const PRICE_FILTERS_PRIORITY_AFTER_COMPOSITE = 99;

	/** @var \woocommerce_wpml $woocommerce_wpml */
	private $woocommerce_wpml;

	public function __construct( \woocommerce_wpml $woocommerce_wpml ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

	public function add_hooks() {
		if ( is_admin() ) {
			add_action( 'wcml_after_save_custom_prices', [ $this, 'update_composite_custom_prices' ], 10, 4 );
		} else {
			add_filter( 'get_post_metadata', [ $this, 'filter_composite_product_cost' ], 10, 3 );
			$this->add_price_rounding_filters();
		}
	}

	/**
	 * @param int|string       $productId
	 * @param string|float|int $productPrice
	 * @param array            $customPrices
	 * @param string           $code
	 *
	 * @return void
	 */
	public function update_composite_custom_prices( $productId, $productPrice, $customPrices, $code ){
		if( WCML_Compatibility_Helper::get_product_type( $productId ) === 'composite' ) {
			update_post_meta( $productId, '_bto_base_regular_price' . '_' . $code, $customPrices['_regular_price' ] );
			update_post_meta( $productId, '_bto_base_sale_price' . '_' . $code, $customPrices['_sale_price' ] );
			update_post_meta( $productId, '_bto_base_price' . '_' . $code, $productPrice );
		}
	}

	/**
	 * @param mixed  $value
	 * @param int    $objectId
	 * @param string $metaKey
	 *
	 * @return mixed
	 */
	public function filter_composite_product_cost( $value, $objectId, $metaKey ) {
		if ( in_array( $metaKey, [
			'_bto_base_regular_price',
			'_bto_base_sale_price',
			'_bto_base_price'
		] ) ) {
			$original_id = $this->woocommerce_wpml->products->get_original_product_id( $objectId );

			$cost_status = get_post_meta( $original_id, '_wcml_custom_prices_status', true );

			$currency = $this->woocommerce_wpml->multi_currency->get_client_currency();

			if ( $currency === wcml_get_woocommerce_currency_option() ) {
				return $value;
			}

			$cost = get_post_meta( $original_id, $metaKey . '_' . $currency, true );

			if ( $cost_status && ! empty( $cost ) ) {
				return $cost;
			} else {
				remove_filter( 'get_post_metadata', [ $this, 'filter_composite_product_cost' ], 10 );

				$cost = get_post_meta( $original_id, $metaKey, true );

				add_filter( 'get_post_metadata', [ $this, 'filter_composite_product_cost' ], 10, 4 );

				if ( $cost ){
					return $this->woocommerce_wpml->multi_currency->prices->convert_price_amount( $cost, $currency );
				}
			}
		}

		return $value;
	}

	public function add_price_rounding_filters() {
		$filters = [
			'woocommerce_product_get_price',
			'woocommerce_product_get_sale_price',
			'woocommerce_product_get_regular_price',
			'woocommerce_product_variation_get_price',
			'woocommerce_product_variation_get_sale_price',
			'woocommerce_product_variation_get_regular_price'
		];

		foreach( $filters as $filter ){
			add_filter( $filter, [ $this, 'apply_rounding_rules' ], self::PRICE_FILTERS_PRIORITY_AFTER_COMPOSITE );
		}
	}

	/**
	 * @param int|float|string $price
	 *
	 * @return int|float|string
	 */
	public function apply_rounding_rules( $price ) {
		if ( $price && is_composite_product() ) {
			$current_currency = $this->woocommerce_wpml->multi_currency->get_client_currency();

			if ( $current_currency !== wcml_get_woocommerce_currency_option() ) {
				$price = $this->woocommerce_wpml->multi_currency->prices->apply_rounding_rules( $price, $current_currency );
			}
		}

		return $price;
	}
}
