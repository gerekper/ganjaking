<?php
/**
 * WooCommerce Measurement Price Calculator
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Measurement Price Calculator to newer
 * versions in the future. If you wish to customize WooCommerce Measurement Price Calculator for your
 * needs please refer to http://docs.woocommerce.com/document/measurement-price-calculator/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Measurement Price Calculator Product Loop View Class
 *
 * @since 3.0
 */
class WC_Price_Calculator_Product_Loop {


	/**
	 * Construct and initialize the class
	 *
	 * @since 3.0
	 */
	public function __construct() {

		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'loop_add_to_cart_link' ), 10, 2 );

	}


	/** Frontend methods ******************************************************/


	/**
	 * Modify the 'add to cart' url for pricing calculator products to simply link to
	 * the product page, just like a variable product.  This is because the
	 * customer must supply whatever product measurements they require.
	 *
	 * @since 3.3
	 * @param string $tag the 'add to cart' button tag html
	 * @param \WC_Product $product the product
	 * @return string the Add to Cart tag
	 */
	public function loop_add_to_cart_link( $tag, $product ) {

		// Otherwise, for simple type products, the page javascript would take over and try to do an ajax add-to-cart, when really we need the customer to visit the product page to supply whatever input fields they require.
		if ( \WC_Price_Calculator_Product::pricing_calculator_enabled( $product ) && $product->is_in_stock() ) {

			/**
			 * Filters the product loop URL if product is in stock and pricing calculator is enabled.
			 *
			 * @since 3.13.6
			 *
			 * @param string $product_url product URL
			 * @param \WC_Product $product current product
			 */
			$product_url = (string) apply_filters( 'wc_measurement_price_calculator_product_loop_url', get_permalink( $product->get_id() ), $product );

			$tag = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button add_to_cart_button product_type_%s">%s</a>',
				esc_url( $product_url ),
				esc_attr( $product->get_id() ),
				esc_attr( $product->get_sku() ),
				'variable',
				__( 'Select options', 'woocommerce-measurement-price-calculator' )
			);
		}

		return $tag;
	}


}
