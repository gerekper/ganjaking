<?php namespace Premmerce\WooCommercePinterest\Frontend;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\PinterestPlugin;
use WC_Product;

class SaveButton {


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
	 * PinIt constructor.
	 *
	 * @param FileManager $fileManager
	 * @param PinterestIntegration $integration
	 */
	public function __construct( FileManager $fileManager, PinterestIntegration $integration) {
		$this->fileManager = $fileManager;
		$this->integration = $integration;
	}

	/**
	 * Set hooks and filters for this class
	 */
	public function init() {
		add_action('wp_enqueue_scripts', array($this, 'enqueueAssets'));

		if ($this->showOnAllPages()) {
			// Output button on loop product
			add_action('woocommerce_before_shop_loop_item', array($this, 'renderPinItButton'), 9);
		}

		if ($this->showOnProduct()) {
			// Output button on product page
			add_action('wp_footer', array($this, 'renderPinItProductButton'));
		}
	}

	/**
	 * Register assets for this class
	 */
	public function enqueueAssets() {
		$prefix  = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
		$version = PinterestPlugin::$version;

		wp_enqueue_script(
			'woocommerce_pinit',
			$this->fileManager->locateAsset('frontend/pinit/pinit' . $prefix . '.js'),
			array('wc-single-product', 'jquery'),
			$version,
			false //todo: check if it can be enqueued in footer
		);

		wp_enqueue_style('woocommerce_pinit',
			$this->fileManager->locateAsset('/frontend/pinit/pinit' . $prefix . '.css'),
			array(),
			$version
		);

		wp_enqueue_script(
			'pinterest_pinit',
			'//assets.pinterest.com/js/pinit.js',
			array(),
			$version,
			true
		);

		if (is_product()) {
			wp_localize_script('woocommerce_pinit', 'wooPinterestConfig', array(
				'productUrl' => get_the_permalink()
			));
		}
	}

	/**
	 * Output pin button for woocommerce-product-gallery
	 */
	public function renderPinItProductButton() {

		if (is_product()) {
			?>

			<div class="product-gallery-pin-btn">
				<?php $this->renderPinItButton(); ?>
			</div>

			<?php
		}
	}

	/**
	 * Render pin button
	 */
	public function renderPinItButton() {
		global $product;

		if ($product instanceof WC_Product) {

			// Check if product has main image
			if (!$product->get_image_id()) {
				return;
			}

			$full_size       = apply_filters(
				'woocommerce_gallery_full_size',
				apply_filters('woocommerce_product_thumbnails_large_size', 'full')
			);
			$imageSrc        = wp_get_attachment_image_src($product->get_image_id(), $full_size);
			$pinUrl          = get_the_permalink();
			$singleProductId = get_queried_object_id() === $product->get_id() ? 'id="data-product-pin-button"' : '';

			printf("<a href='https://www.pinterest.com/pin/create/button/'
                 data-pin-media='%s'
                 data-pin-url='%s'
 				 %s
                 ></a>", esc_attr($imageSrc[0]), esc_url($pinUrl), esc_html($singleProductId));
		}
	}

	/**
	 * Is button enabled at current page
	 *
	 * @return bool
	 */
	public function enabled() {
		return $this->showOnAllPages() || $this->showOnProduct();
	}

	/**
	 * Show pin it button on product page
	 *
	 * @return bool
	 */
	protected function showOnProduct() {
		return 'yes' === $this->integration->get_option('save_button_product');
	}

	/**
	 * Show pin it button on archive page
	 *
	 * @return bool
	 */
	protected function showOnAllPages() {
		return 'yes' === $this->integration->get_option('save_button_all');
	}
}
