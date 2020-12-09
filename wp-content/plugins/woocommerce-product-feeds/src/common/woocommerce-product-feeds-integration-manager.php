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

	/**
	 * Initialise integrations.
	 */
	public function initialise() {
		$this->cost_of_goods_integration();
		$this->multicurrency_integration();
		$this->min_max_quantities_integration();
		$this->product_vendors_integration();
		$this->the_content_protection_integration();
		$this->yoast_woocommerce_seo_integration();
		$this->product_brands_for_woocommerce_integration();
		$this->woocommerce_mix_and_match_products_integration();
		$this->price_by_country_integration();
		$this->currency_switcher_for_woocommerce_integration();
		$this->woocommerce_composite_products_integration();
		$this->product_bundles_integration();
		$this->woocommerce_min_max_quantity_step_control_single_integration();
		$this->woocommerce_multilingual_integration();
	}

	private function product_brands_for_woocommerce_integration() {
		if ( ! defined( 'PRODUCT_BRANDS_FOR_WOOCOMMERCE_VERSION' ) ) {
			return;
		}
		$this->container['WoocommerceGpfProductBrandsForWooCommerce']->run();
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

	private function price_by_country_integration() {
		if ( ! class_exists( 'CBP_Country_Based_Price' ) ) {
			return;
		}
		$this->container['WoocommerceGpfPriceByCountry']->run();
	}

	private function cost_of_goods_integration() {
		if ( ! class_exists( 'WC_COG_Loader' ) ) {
			return;
		}
		$this->container['WoocommerceCostOfGoods']->run();
	}

	private function woocommerce_mix_and_match_products_integration() {
		if ( ! class_exists( 'WC_Mix_and_Match' ) ) {
			return;
		}
		$mnm_instance = WC_Mix_and_Match::instance();
		if ( empty( $mnm_instance->version ) ||
			 version_compare( $mnm_instance->version, '1.10.2', '<' ) ) {
			return;
		}
		$this->container['WoocommerceGpfWoocommerceMixAndMatchProducts']->run();
	}

	private function currency_switcher_for_woocommerce_integration() {
		if ( ! defined( 'WCCS_VERSION' ) ||
			 version_compare( WCCS_VERSION, '1.2.2', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceGpfCurrencySwitcherForWooCommerce']->run();
	}

	private function woocommerce_composite_products_integration() {
		if ( ! class_exists( 'WC_Composite_Products' ) ||
			 ! is_callable( 'WC_Composite_Products::instance' ) ) {
			return;
		}
		$wc_cp = WC_Composite_Products::instance();
		if ( empty( $wc_cp->version ) || version_compare( $wc_cp->version, '7.0.0', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceGpfWoocommerceCompositeProducts']->run();
	}

	private function product_bundles_integration() {
		if ( ! class_exists( 'WC_Bundles' ) ||
			 ! is_callable( 'WC_Bundles::instance' ) ) {
			return;
		}
		$wc_pb = WC_Bundles::instance();
		if ( empty( $wc_pb->version ) || version_compare( $wc_pb->version, '6.2.4', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceGpfWoocommerceProductBundles']->run();
	}

	private function woocommerce_min_max_quantity_step_control_single_integration() {
		if ( ! defined( 'WC_MMQ_S_PLUGIN_BASE_FILE' ) ) {
			return;
		}
		$this->container['WoocommerceGpfWoocommerceMinMaxQuantityStepControlSingle']->run();
	}

	private function woocommerce_multilingual_integration() {
		if ( ! defined( 'WCML_VERSION' ) ) {
			return;
		}
		$this->container['WoocommerceGpfWoocommerceMultilingual']->run();
	}
}
