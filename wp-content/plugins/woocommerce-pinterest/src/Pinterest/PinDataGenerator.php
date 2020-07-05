<?php namespace Premmerce\WooCommercePinterest\Pinterest;

use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Pinterest\Api\PinterestApiException;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsController;
use Premmerce\WooCommercePinterest\ServiceContainer;
use WC_Product;
use WC_Product_Variable;

/**
 * Class PinDataGenerator
 * Responsible for generating data for Pinterest API requests
 *
 * @package Premmerce\WooCommercePinterest\Pinterest
 */

class PinDataGenerator {

	/**
	 * PinterestTagsController instance
	 *
	 * @var PinterestTagsController
	 */
	private $pinterestTagsController;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $pinterestIntegration;


	/**
	 * PinDataGenerator constructor.
	 *
	 * @param PinterestTagsController $pinterestTagsController
	 * @param PinterestIntegration $pinterestIntegration
	 */
	public function __construct( PinterestTagsController $pinterestTagsController, PinterestIntegration $pinterestIntegration) {
		$this->pinterestTagsController = $pinterestTagsController;
		$this->pinterestIntegration    = $pinterestIntegration;
	}

	/**
	 * Generate pin data
	 *
	 * @param array $pin
	 *
	 * @return array
	 * @throws PinterestApiException | PinterestException
	 */
	public function generateData( $pin ) {

		$product    = wc_get_product( $pin['post_id'] );
		$attachment = wp_get_attachment_image_url( $pin['attachment_id'] );

		$settings = get_option('woocommerce_pinterest_settings');
		$board    = isset($pin['board']) ? $pin['board'] : '';


		if ( ! $board ) {
			throw new PinterestApiException( __( 'Board not specified', 'woocommerce-pinterest' ) );
		}

		if ( ! $product instanceof WC_Product ) {
			throw new PinterestApiException( __( 'Product not found', 'woocommerce-pinterest' ) );
		}

		if ( $product->get_status() !== 'publish' ) {
			throw new PinterestApiException( __( 'Product not published', 'woocommerce-pinterest' ) );
		}

		if ( ! $attachment ) {
			throw new PinterestApiException( __( 'Attachment not found', 'woocommerce-pinterest' ) );
		}

		$size = isset($settings['pinterest_image_size_type']) ? $settings['pinterest_image_size_type'] : 'full';

		$data = array(
			'board'     => $pin['board'],
			'note'      => $this->generateNote( $pin, $product ),
			'link'      => get_permalink( $pin['post_id'] ),
			'image_url' => wp_get_attachment_image_url( $pin['attachment_id'], $size ),
		);

		$data = apply_filters( 'woocommerce_pinterest_pin_data', $data, $pin );

		return $data;
	}

	/**
	 * Generate pin note string
	 *
	 * @param array $pin
	 *
	 * @param WC_Product $product
	 *
	 * @return string|null
	 *
	 * @throws PinterestException
	 */
	public function generateNote( $pin, $product ) {
		$placeholders = $this->getPlaceholders( $product, $pin['attachment_id'] );

		$product = $this->getActualProduct($product, $pin['attachment_id']);
		
		$template = $this->getPinDescriptionTemplate($product);

		$template = apply_filters( 'woocommerce_pinterest_description_template', $template, $pin );

		$placeholders = apply_filters( 'woocommerce_pinterest_description_placeholders', $placeholders, $pin );

		$note = strtr( $template, $placeholders );

		$id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();

		$tags  = $this->pinterestTagsController->getTagsForProduct($id);
		$tags  = apply_filters('woocommerce_pinterest_description_tags', $tags, $pin);
		$note .= ' ' . $this->formatTagNamesToString($tags);

		return apply_filters('woocommerce_pinterest_description', trim($note), $pin, $product);
	}

	/**
	 * Format array of term names to the string looks like "#tag1, #tag2, #tag3"
	 *
	 * @param string[] $tagsNames
	 *
	 * @return string
	 */
	private function formatTagNamesToString( array $tagsNames) {

		if ($tagsNames) {
			$tagsString = '#' . implode(', #', $tagsNames);
		}

		return isset($tagsString) ? $tagsString : '';
	}

	/**
	 * Get Pin description template depend on product and global pin description
	 *
	 * @param WC_Product $product
	 *
	 * @return string|void
	 */
	protected function getPinDescriptionTemplate( WC_Product $product) {
		$container  = ServiceContainer::getInstance();
		$pinService = $container->getPinService();

		if ($product->is_type('variation') && ! $pinService->isVariationHasPinDescription($product->get_id())) {
			$productId = $product->get_parent_id();
		} else {
			$productId = $product->get_id();
		}

		$settings       = get_option('woocommerce_pinterest_settings');
		$globalTemplate = isset($settings['pin_description']) ? $settings['pin_description'] : '';

		$pinDescription = $pinService->getProductPinDescriptionTemplate($productId);

		return $pinDescription ? $pinDescription : $globalTemplate;
	}

	/**
	 * Set placeholders for pin description
	 *
	 * @param WC_Product $product
	 * @param int $attachmentId
	 *
	 * @return array
	 */
	public function getPlaceholders( WC_Product $product, $attachmentId) {
		$formattedPrice = wp_strip_all_tags(wc_price($product->get_price()));
		if ($product instanceof WC_Product_Variable) {
			$formattedPrice = wp_strip_all_tags($product->get_price_html());
		}


		$placeholders['{excerpt}']    = wp_strip_all_tags($product->get_short_description());
		$placeholders['{site_title}'] = get_bloginfo('name');


		$product = $this->getActualProduct($product, $attachmentId);

		$placeholders['{link}']        = get_permalink($product->get_id());
		$placeholders['{price}']       = $formattedPrice;
		$placeholders['{title}']       = $product->get_name();
		$placeholders['{description}'] = wp_strip_all_tags($product->get_description());

		return $placeholders;
	}

	/**
	 * Get product tied to attachment.
	 *
	 * @param WC_Product $product
	 * @param int $attachmentId
	 * @return WC_Product
	 */
	protected function getActualProduct( WC_Product $product, $attachmentId) {
		if ($product instanceof WC_Product_Variable) {

			foreach ($product->get_children() as $variationId) {
				$image     = get_post_thumbnail_id($variationId);
				$variation = wc_get_product($variationId);
				if ($image === $attachmentId && $variation) {
					return $variation;
				}
			}
		}

		return $product;
	}
}
