<?php
/**
 * @package Polylang-WC
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Automattic\WooCommerce\Utilities\OrderUtil;

/**
 * Class that declares the compatibility with custom order tables for WooCommerce (HPOS).
 *
 * @see https://developer.woocommerce.com/2022/09/29/high-performance-order-storage-backward-compatibility-and-synchronization/
 * @see https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book
 *
 * @since 1.9
 */
class PLLWC_HPOS_Feature {

	/**
	 * Cache.
	 *
	 * @var bool[]
	 *
	 * @phpstan-var array<non-falsy-string, bool>
	 */
	private $cache = array();

	/**
	 * Tells if PLLWC can use the WC's custom order table feature.
	 *
	 * @since 1.9
	 *
	 * @return bool
	 */
	public function feature_exists() {
		if ( isset( $this->cache[ __FUNCTION__ ] ) ) {
			return $this->cache[ __FUNCTION__ ];
		}

		// Require WC 7.1+.
		if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			$this->cache[ __FUNCTION__ ] = false;
		} else {
			$features = FeaturesUtil::get_features( true );
			$this->cache[ __FUNCTION__ ] = ! empty( $features['custom_order_tables'] );
		}

		return $this->cache[ __FUNCTION__ ];
	}

	/**
	 * Tells if the custom order table feature is enabled.
	 * Must not be used before {@see PLLWC_HPOS_Feature::feature_exists()}.
	 * Note: `Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()` is introduced in WC 6.9.
	 *
	 * @since 1.9
	 *
	 * @return bool
	 */
	public function is_feature_enabled() {
		if ( isset( $this->cache[ __FUNCTION__ ] ) ) {
			return $this->cache[ __FUNCTION__ ];
		}

		if ( ! $this->feature_exists() ) {
			$this->cache[ __FUNCTION__ ] = false;
			return $this->cache[ __FUNCTION__ ];
		}

		// Check for the whole feature.
		if ( ! FeaturesUtil::feature_is_enabled( 'custom_order_tables' ) ) {
			$this->cache[ __FUNCTION__ ] = false;
			return $this->cache[ __FUNCTION__ ];
		}

		// Check that the custom order table is the authoritative data source.
		$this->cache[ __FUNCTION__ ] = OrderUtil::custom_orders_table_usage_is_enabled();
		return $this->cache[ __FUNCTION__ ];
	}

	/**
	 * Launches the hook that declares this plugin compatible with WC's custom order table feature.
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function declare_compatibility_with_feature() {
		if ( $this->feature_exists() ) {
			add_action( 'before_woocommerce_init', array( $this, 'declare_compatibility_with_feature_hook' ) );
		}
	}

	/**
	 * Declares this plugin compatible with WC's custom order table feature.
	 *
	 * @since 1.9
	 *
	 * @return void
	 */
	public function declare_compatibility_with_feature_hook() {
		// Can only be used in the hook `before_woocommerce_init`.
		FeaturesUtil::declare_compatibility( 'custom_order_tables', PLLWC_FILE, true );
	}
}
