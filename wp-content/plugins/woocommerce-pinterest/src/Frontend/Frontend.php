<?php namespace Premmerce\WooCommercePinterest\Frontend;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\PinterestPluginUtils;
use WC_Product;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Analytics;

/**
 * Class Frontend
 * Responsible for handling frontend requests
 *
 * @package Premmerce\Pinterest\Frontend
 */
class Frontend {
	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $integration;

	/**
	 * SaveButton instance
	 *
	 * @var SaveButton
	 */
	private $saveButton;

	/**
	 * Analytics instance
	 *
	 * @var Analytics
	 */
	private $analytics;

	/**
	 * Frontend constructor.
	 *
	 * @param FileManager $fileManager
	 * @param PinterestIntegration $integration
	 * @param SaveButton $saveButton
	 * @param Analytics $analytics
	 *
	 * @todo: move meta data generation to new class
	 */
	public function __construct( FileManager $fileManager, PinterestIntegration $integration, SaveButton $saveButton, Analytics $analytics  ) {

		$this->fileManager = $fileManager;
		$this->integration = $integration;
		$this->saveButton  = $saveButton;
		$this->analytics   = $analytics;

		$this->init();
	}

	/**
	 * Register hooks
	 */
	public function init() {

		if ($this->saveButton->enabled()) {
			$this->saveButton->init();
		}

		if ($this->analytics->enabled()) {
			$this->analytics->init();
		}

		add_action('wp_head', array($this, 'renderRichPinMeta'));
		add_action('wp_head', array($this, 'renderVerificationCode'));

		if ($this->needOverrideYoastType()) {
			add_action('wpseo_opengraph_type', function ( $type) {
				if (is_product()) {
					return 'og:product';
				}

				return $type;
			});
		}
	}


	/**
	 * Show domain verification meta - p:domain_verify
	 */
	public function renderVerificationCode() {
		$code = $this->integration->get_option( 'verification_code' );

		if ( $code ) {
			print( '<!--WooCommerce Pinterest Verification Code-->' . PHP_EOL );
			$this->renderMeta( 'p:domain_verify', $code, true );
			print( '<!--/WooCommerce Pinterest Verification Code-->' . PHP_EOL );
		}
	}

	/**
	 * Add Pinterest og meta tags
	 */
	public function renderRichPinMeta() {
		if ( 'yes' === $this->integration->get_option( 'enable_richpins' ) && is_product() ) {

			$data = $this->getPinterestOgData( wc_get_product() );

			print( '<!--WooCommerce Pinterest Product Rich Pins-->' . PHP_EOL );
			foreach ( $data as $key => $value ) {
				if ( $value ) {
					$this->renderMeta( $key, $value );
				}
			}
			print( '<!--/WooCommerce Pinterest Product Rich Pins-->' . PHP_EOL );
		}
	}

	/**
	 * Get Pinterest OpenGraph data
	 *
	 * @param WC_Product $product
	 * @return mixed|void
	 */
	public function getPinterestOgData( WC_Product $product ) {

		$product_cost = number_format( floatval( $product->get_price() ), 2 );
		$brand        = null;

		if ( taxonomy_exists( 'product_brand' ) ) {
			$terms = wp_get_post_terms( $product->get_id(), 'product_brand', array( 'fields' => 'names' ) );

			if ( is_array( $terms ) && ! empty( $terms[0] ) ) {
				$brand = $terms[0];
			}
		}

		$data['og:brand']               = $brand;
		$data['og:url']                 = get_permalink( $product->get_id() );
		$data['og:title']               = $product->get_title();
		$data['og:site_name']           = get_bloginfo( 'site_title' );
		$data['og:description']         = wp_strip_all_tags( $product->get_description() );
		$data['product:price:amount']   = esc_attr( $product_cost );
		$data['product:price:currency'] = esc_attr( get_woocommerce_currency() );
		$data['og:availability']        = $this->formatStock( $product );
		$data['og:type']                = 'product';

		if ( PinterestPluginUtils::isYoastActive() && $this->isEnabledYoastCompatibility() ) {

			// Yoast already adds this fields
			unset( $data['og:description'] );
			unset( $data['og:site_name'] );
			unset( $data['og:title'] );
			unset( $data['og:url'] );

			if ( PinterestPluginUtils::isYoastWooCommerceActive() ) {

				// Yoast for WooCommerce already adds this fields
				unset( $data['product:price:amount'] );
				unset( $data['product:price:currency'] );
				unset( $data['og:availability'] );
				unset( $data['og:type'] );
			}
		}

		if ( $this->needOverrideYoastType() ) {
			unset( $data['og:type'] );
		}

		if ( $product->is_on_sale() && $product->is_type( 'simple' ) ) {
			$regular_price                    = number_format( floatval( $product->get_regular_price() ), 2 );
			$data['og:price:standard_amount'] = esc_attr( $regular_price );
		}

		return apply_filters( 'woocommerce_pinterest_og_data', $data );
	}


	/**
	 * Format stock status
	 *
	 * @param WC_Product $product
	 *
	 * @return mixed|string
	 */
	protected function formatStock( WC_Product $product ) {
		$stock_status = get_post_meta( $product->get_id(), '_stock_status', true );

		switch ( $stock_status ) {
			case 'instock':
				$stock_status = 'in stock';
				break;
			case 'onbackorder':
				$stock_status = 'backorder';
				break;
			case 'outofstock':
				$stock_status = $product->backorders_allowed() ? 'backorder' : 'out of stock';
				break;
			default:
				$stock_status = 'out of stock';
		}

		return $stock_status;
	}

	/**
	 * Print meta tag
	 *
	 * @param string $key
	 * @param string $value
	 */
	protected function renderMeta( $key, $value, $verifyDomainMeta = false ) {
		$attrName = $verifyDomainMeta ? 'name' : 'property';
		printf( '<meta %s="%s" content="%s"/>%s', esc_attr( $attrName ), esc_attr( $key ), esc_attr( $value ),
			PHP_EOL );
	}

	/**
	 * Check if need to improve yoast og markup
	 *
	 * @return bool
	 */
	protected function needOverrideYoastType() {
		$yoastHasNoProductMarkUp = PinterestPluginUtils::isYoastActive() && ! PinterestPluginUtils::isYoastWooCommerceActive();

		return $this->isEnabledYoastCompatibility() && $yoastHasNoProductMarkUp;
	}

	/**
	 * Check if enabled yoast compatibility
	 *
	 * @return bool
	 */
	protected function isEnabledYoastCompatibility() {
		return $this->integration->get_option( 'enable_yoast_compatibility', 'no' ) === 'yes';
	}
}
