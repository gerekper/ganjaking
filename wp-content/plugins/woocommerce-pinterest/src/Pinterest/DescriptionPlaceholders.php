<?php


namespace Premmerce\WooCommercePinterest\Pinterest;

/**
 * Class DescriptionPlaceholders
 *
 * @package Premmerce\WooCommercePinterest\Pinterest
 *
 * This class is responsible for storing pin description placeholders and their actual values
 */
class DescriptionPlaceholders {

	/**
	 * Return placeholders list with descriptions
	 *
	 * @return string[]
	 */
	public function getPlaceholders() {
		$placeholders = array(
			'{price}' => __('Price', 'woocommerce-pinterest'),
			'{title}' => __('Title', 'woocommerce-pinterest'),
			'{link}' => __('Link', 'woocommerce-pinterest'),
			'{description}' => __('Description', 'woocommerce-pinterest'),
			'{excerpt}' => __('Excerpt', 'woocommerce-pinterest'),
			'{site_title}' => __('Site title', 'woocommerce-pinterest'),
		);

		return apply_filters('woocommerce_pinterest_pin_description_placeholders', $placeholders);
	}
}
