<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

use Yoast\WP\SEO\Presenters\Abstract_Indexable_Tag_Presenter;

/**
 * Represents an abstract WooCommerce product presenter.
 */
abstract class WPSEO_WooCommerce_Abstract_Product_Presenter extends Abstract_Indexable_Tag_Presenter {

	/**
	 * The product to present for.
	 *
	 * @var \WC_Product
	 */
	protected $product;

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = self::META_PROPERTY_CONTENT;

	/**
	 * WPSEO_WooCommerce_Abstract_Product_Presenter constructor.
	 *
	 * @param \WC_Product $product The product.
	 */
	public function __construct( WC_Product $product ) {
		$this->product = $product;
	}
}
