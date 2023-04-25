<?php

use Pimple\Container;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 */
class WoocommerceProductFeedsIntegrationManager {

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Array of integration keys.
	 *
	 * @var array|string[]
	 */
	protected $integrations = [];

	/**
	 * WoocommerceProductFeedsIntegrationManager constructor.
	 *
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container    = $container;
		$this->integrations = [
			'multicurrency',
			'cost_of_goods',
			'min_max_quantities',
			'product_vendors',
			'the_content_protection',
			'yoast_woocommerce_seo',
			'product_brands_for_woocommerce',
			'woocommerce_mix_and_match_products',
			'price_by_country',
			'currency_switcher_for_woocommerce',
			'woocommerce_composite_products',
			'product_bundles',
			'woocommerce_min_max_quantity_step_control_single',
			'woocommerce_multilingual',
			'pw_bulk_edit',
			'advanced_custom_fields',
			'facebook_for_woocommerce',
			'woocommerce_germanized',
			'woocommerce_additional_variation_images',
			'measurement_price_calculator',
		];
	}

	/**
	 * Initialise integrations.
	 */
	public function initialise() {
		foreach ( $this->integrations as $integration ) {
			$callback = $integration . '_integration';
			if ( apply_filters( 'woocommerce_gpf_disable_' . $integration . '_integration', false ) ) {
				continue;
			}
			$this->$callback();
		}
	}

	/**
	 * https://woocommerce.com/products/facebook/
	 *
	 * @return void
	 */
	private function facebook_for_woocommerce_integration() {
		if ( ! class_exists( 'WC_Facebook_Loader' ) ) {
			return;
		}
		$this->container['WoocommerceProductFeedsFacebookForWoocommerce']->run();
	}

	/**
	 * https://woocommerce.com/products/product-brands-for-woocommerce/
	 *
	 * @return void
	 */
	private function product_brands_for_woocommerce_integration() {
		if ( ! defined( 'PRODUCT_BRANDS_FOR_WOOCOMMERCE_VERSION' ) ) {
			return;
		}
		$this->container['WoocommerceGpfProductBrandsForWooCommerce']->run();
	}

	/**
	 * https://yoast.com/wordpress/plugins/yoast-woocommerce-seo/
	 *
	 * @return void
	 */
	private function yoast_woocommerce_seo_integration() {
		if ( ! defined( 'WPSEO_WOO_VERSION' ) ) {
			return;
		}
		$this->container['WoocommerceGpfYoastWoocommerceSeo']->run();
	}

	/**
	 * Various plugins.
	 *
	 * @return void
	 */
	private function the_content_protection_integration() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) &&
			 ! defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			return;
		}

		$this->container['WoocommerceGpfTheContentProtection']->run();
	}

	/**
	 * https://woocommerce.com/products/product-vendors/
	 *
	 * @return void
	 */
	private function product_vendors_integration() {
		if ( ! defined( 'WC_PRODUCT_VENDORS_VERSION' ) ||
			 version_compare( WC_PRODUCT_VENDORS_VERSION, '2.1.16', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceProductVendors']->run();
	}

	/**
	 * https://woocommerce.com/products/minmax-quantities
	 *
	 * @return void
	 */
	private function min_max_quantities_integration() {
		if ( ! defined( 'WC_MIN_MAX_QUANTITIES' ) ||
			 version_compare( WC_MIN_MAX_QUANTITIES, '2.4.5', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceMinMaxQuantities']->run();
	}

	/**
	 * https://woocommerce.com/products/multi-currency/
	 *
	 * @return void
	 */
	private function multicurrency_integration() {
		if ( ! defined( 'WOOCOMMERCE_MULTICURRENCY_VERSION' ) ||
			 version_compare( WOOCOMMERCE_MULTICURRENCY_VERSION, '1.9.0', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceGpfMulticurrency']->run();
	}

	/**
	 * https://woocommerce.com/products/price-by-country/
	 *
	 * @return void
	 */
	private function price_by_country_integration() {
		if ( ! class_exists( 'CBP_Country_Based_Price' ) ) {
			return;
		}
		$this->container['WoocommerceGpfPriceByCountry']->run();
	}

	/**
	 * https://woocommerce.com/products/woocommerce-cost-of-goods/
	 *
	 * @return void
	 */
	private function cost_of_goods_integration() {
		if ( ! class_exists( 'WC_COG_Loader' ) ) {
			return;
		}
		$this->container['WoocommerceCostOfGoods']->run();
	}

	/**
	 * https://woocommerce.com/products/woocommerce-mix-and-match-products/
	 *
	 * @return void
	 */
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

	/**
	 * https://woocommerce.com/products/currency-switcher-for-woocommerce/
	 *
	 * @return void
	 */
	private function currency_switcher_for_woocommerce_integration() {
		$wccs = $GLOBALS['WCCS'] ?? new stdClass();
		if ( ! defined( 'WCCS_VERSION' ) ||
			 version_compare( WCCS_VERSION, '1.2.2', 'lt' ) ||
			 ! is_callable( [ $wccs, 'wccs_get_currencies' ] ) ) {
			return;
		}
		$this->container['WoocommerceGpfCurrencySwitcherForWooCommerce']->run();
	}

	/**
	 * https://woocommerce.com/products/composite-products/
	 *
	 * @return void
	 */
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

	/**
	 * https://woocommerce.com/products/product-bundles/
	 *
	 * @return void
	 */
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

	/**
	 * https://codecanyon.net/item/woocommerce-min-max-quantity-step-control/22962198
	 *
	 * @return void
	 */
	private function woocommerce_min_max_quantity_step_control_single_integration() {
		if ( ! defined( 'WC_MMQ_S_PLUGIN_BASE_FILE' ) ) {
			return;
		}
		$this->container['WoocommerceGpfWoocommerceMinMaxQuantityStepControlSingle']->run();
	}

	/**
	 * https://wordpress.org/plugins/woocommerce-multilingual/
	 *
	 * @return void
	 */
	private function woocommerce_multilingual_integration() {
		if ( ! defined( 'WCML_VERSION' ) ) {
			return;
		}
		$this->container['WoocommerceGpfWoocommerceMultilingual']->run();
	}

	/**
	 * https://en-gb.wordpress.org/plugins/pw-bulk-edit/
	 *
	 * @return void
	 */
	private function pw_bulk_edit_integration() {
		if ( ! defined( 'PWBE_VERSION' ) ) {
			return;
		}
		$this->container['WoocommerceGpfPwBulkEdit']->run();
	}

	/**
	 * https://wordpress.org/plugins/advanced-custom-fields/
	 *
	 * @return void
	 */
	private function advanced_custom_fields_integration() {
		if ( ! class_exists( 'ACF' ) ) {
			return;
		}
		$this->container['WoocommerceProductFeedsAdvancedCustomFields']->run();
	}

	/**
	 * https://wordpress.org/plugins/woocommerce-germanized/
	 *
	 * @return void
	 */
	private function woocommerce_germanized_integration() {
		if ( ! class_exists( 'WooCommerce_Germanized' ) ) {
			return;
		}
		$this->container['WoocommerceProductFeedsWoocommerceGermanized']->run();
	}

	/**
	 * https://woocommerce.com/products/woocommerce-additional-variation-images/
	 *
	 * @return void
	 */
	private function woocommerce_additional_variation_images_integration() {
		if ( ! defined( 'WC_ADDITIONAL_VARIATION_IMAGES_VERSION' ) ||
			 version_compare( WC_ADDITIONAL_VARIATION_IMAGES_VERSION, '1.9.0', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceProductFeedsWoocommerceAdditionalVariationImages']->run();
	}

	/**
	 * https://woocommerce.com/products/measurement-price-calculator/
	 *
	 * @return void
	 */
	private function measurement_price_calculator_integration() {
		if ( ! class_exists( 'WC_Measurement_Price_Calculator_Loader' ) ||
			 version_compare( WC_Measurement_Price_Calculator::VERSION, '3.20.1', 'lt' ) ) {
			return;
		}
		$this->container['WoocommerceProductFeedsMeasurementPriceCalculator']->run();
	}
}
