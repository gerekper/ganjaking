<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Represents an abstract WooCommerce product availability presenter.
 */
abstract class WPSEO_WooCommerce_Abstract_Product_Availability_Presenter extends WPSEO_WooCommerce_Abstract_Product_Presenter {

	/**
	 * Whether the product is on backorder.
	 *
	 * @var bool
	 */
	protected $is_on_backorder;

	/**
	 * Whether the product is in stock.
	 *
	 * @var bool
	 */
	protected $is_in_stock;

	/**
	 * WPSEO_WooCommerce_Abstract_Product_Availability_Presenter constructor.
	 *
	 * @param \WC_Product $product         The product.
	 * @param bool        $is_on_backorder Whether the product is on backorder.
	 * @param bool        $is_in_stock     Whether the product is in stock.
	 */
	public function __construct( WC_Product $product, $is_on_backorder, $is_in_stock = false ) {
		parent::__construct( $product );

		$this->is_on_backorder = $is_on_backorder;
		$this->is_in_stock     = $is_in_stock;
	}
}
