<?php

use Pimple\Container;

class WoocommerceProductFeedsIntegrationManager {

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * WoocommerceProductFeedsIntegrationManager constructor.
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	public function initialise() {
		$this->cost_of_goods_integration();
		$this->multicurrency_integration();
		$this->min_max_quantities_integration();
		$this->product_vendors_integration();
		$this->the_content_protection_integration();
		$this->yoast_woocommerce_seo_integration();
	}

	private function yoast_woocommerce_seo_integration() {
		if ( ! defined( 'WPSEO_WOO_VERSION' ) ) {
			return;
		}
		$this->container['WoocommerceGpfYoastWoocommerceSeo']->run();
	}

	private function the_content_protection_integration() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}
		$this->container['WoocommerceGpfTheContentProtection']->run();
	}

	private function product_vendors_integration() {
		if ( ! defined( 'WC_PRODUCT_VENDORS_VERSION' ) ||
			 version_compare( WC_PRODUCT_VENDORS_VERSION, '2.1.16', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceProductVendors']->run();
	}

	private function min_max_quantities_integration() {
		if ( ! defined( 'WC_MIN_MAX_QUANTITIES' ) ||
			 version_compare( WC_MIN_MAX_QUANTITIES, '2.4.5', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceMinMaxQuantities']->run();
	}

	private function multicurrency_integration() {
		if ( ! defined( 'WOOCOMMERCE_MULTICURRENCY_VERSION' ) ||
			 version_compare( WOOCOMMERCE_MULTICURRENCY_VERSION, '1.9.0', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceGpfMulticurrency']->run();
	}

	private function cost_of_goods_integration() {
		if ( ! class_exists( 'WC_COG_Loader' ) ) {
			return;
		}
		$this->container['WoocommerceCostOfGoods']->run();
	}
}
