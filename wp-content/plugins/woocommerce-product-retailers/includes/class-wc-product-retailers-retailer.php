<?php
/**
 * WooCommerce Product Retailers
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Retailers to newer
 * versions in the future. If you wish to customize WooCommerce Product Retailers for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-retailers/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Product Retailer
 *
 * @since 1.0.0
 */
class WC_Product_Retailers_Retailer {


	/** @var string $name the retailer name */
	private $name = '';

	/** @var string $price the retailer price (defined on a per-product basis) */
	private $price = '';

	/** @var false|string $url the retailer url */
	private $url = false;

	/** @var false|string $icon the retailer icon (image url) */
	private $icon = false;

	/** @var false|string $css_class CSS class(es) for the retailer button */
	private $css_class = false;

	/** @var false|string $styles custom CSS styles for the retailer */
	private $styles = false;

	/** @var \WP_Post $post the post object */
	private $post = null;


	/**
	 * Constructs and initializes a product retailer.
	 *
	 * @since 1.0.0
	 *
	 * @param int|\WP_Post Retailer ID or post object
	 * @throws \Exception if the retailer identified by $id doesn't exist
	 */
	public function __construct( $id ) {

		// load the post object if we don't already have it
		if ( is_object( $id ) ) {
			$post = $id;
		} else {
			$post = get_post( $id );
			if ( ! $post ) {
				throw new \Exception( 'Retailer does not exist' );
			}
		}

		$this->post = $post;
		$this->name = $post->post_title;
		$this->url  = get_post_meta( $post->ID, '_product_retailer_default_url', true );
	}


	/**
	 * Returns true if this retailer is available for display on the frontend.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $is_admin whether this check is from within the admin, where we don't care about the url
	 * @return bool
	 */
	public function is_available( $is_admin = false ) {

		$url  = $this->get_url();
		$name = $this->get_name();

		return ( $is_admin || ! empty( $url ) ) && ! empty( $name ) && 'publish' === $this->post->post_status;
	}


	/**
	 * Returns the retailer id.
	 *
	 * @since 1.0.0
	 *
	 * @return int retailer post id
	 */
	public function get_id() {

		return $this->post->ID;
	}


	/**
	 * Returns the retailer name.
	 *
	 * @since 1.0.0
	 *
	 * @return string the retailer name
	 */
	public function get_name() {

		return $this->name;
	}



	/**
	 * Returns the retailer label.
	 *
	 * @since 1.7.1
	 *
	 * @param string|\WC_Product $product Product object. Optional, used in filter (defaults to empty string)
	 * @param bool $use_button_text Whether to use the product button text, default false uses retailer name
	 * @return string
	 */
	public function get_label( $product = '', $use_button_text = false ) {

		if ( true === $use_button_text && $product instanceof \WC_Product ) {
			$label = \WC_Product_Retailers_Product::get_product_button_text( $product );
		} else {
			$label = $this->get_name();
		}

		$price = wp_kses_post( \WC_Product_Retailers_Product::wc_price( $this->get_price() ) );

		// add the price information to the label, but check if the price is not 0 first
		if ( $price && (float) 0 !== (float) preg_replace( '/[^0-9,.]/', '', $price ) ) {
			// this accounts for localization too
			$label = is_rtl() ? $price . ' - ' . $label : $label . ' - ' . $price;
		}

		/**
		 * Filters the Product Retailer button label.
		 *
		 * @param string $label The label
		 * @param \WC_Product_Retailers_Retailer $retailer Retailer object
		 * @param \WC_Product|string $product Optional. Product object or empty string if unused
		 */
		return apply_filters( 'wc_product_retailers_button_label', $label, $this, $product );
	}


	/**
	 * Returns the retailer class.
	 *
	 * @since 1.7.1
	 *
	 * @return string
	 */
	public function get_class() {

		// turns a string like "My  Retailer's Name " into 'my-retailers-name'
		$class = strtolower( sanitize_html_class( preg_replace('/\s+/', '-', trim( $this->get_name() ) ) ) );

		return "wc-product-retailer-{$class}";
	}


	/**
	 * Returns the price set for retailer (defined at the per-product level).
	 *
	 * @since 1.1.0
	 *
	 * @return string the retailer price
	 */
	 public function get_price() {

	 	return $this->price;
	}


	/**
	 * Sets the retailer price.
	 *
	 * @since 1.1.0
	 *
	 * @param string $price the price to set
	 */
	public function set_price( $price ) {

		$this->price = $price;
	}


	/**
	 * Returns the retailer url.
	 *
	 * @since 1.0.0
	 *
	 * @return string the retailer url
	 */
	public function get_url() {

		// add http:// if missing
		if ( $this->url && null === parse_url( $this->url, PHP_URL_SCHEME ) ) {
			$this->url = 'http://' . $this->url;
		}

		return $this->url;
	}


	/**
	 * Sets the retailer url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url the url to set
	 */
	public function set_url( $url ) {

		$this->url = $url;
	}


	/**
	 * Persists this retailer to the DB.
	 *
	 * @since 1.0.0
	 */
	public function persist() {

		update_post_meta( $this->post->ID, '_product_retailer_default_url',  $this->get_url() );
	}


}
