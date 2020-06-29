<?php
/**
 * WooCommerce Yoast SEO plugin file.
 *
 * @package WPSEO/WooCommerce
 */

/**
 * Represents the product's retailer item ID.
 */
class WPSEO_WooCommerce_Product_Retailer_Item_ID_Presenter extends WPSEO_WooCommerce_Abstract_Product_Presenter {

	/**
	 * The tag format including placeholders.
	 *
	 * @var string
	 */
	protected $tag_format = '<meta property="product:retailer_item_id" content="%s" />';

	/**
	 * Gets the raw value of a presentation.
	 *
	 * @return string The raw value.
	 */
	public function get() {
		return (string) $this->product->get_sku();
	}
}
