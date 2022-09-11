<?php
/**
 * WooCommerce Product Documents
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Documents to newer
 * versions in the future. If you wish to customize WooCommerce Product Documents for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-documents/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Product Documents Shortcode.
 *
 * Renders a list of product documents.
 *
 * @since 1.2.0
 */
class WC_Product_Documents_List_Shortcode {


	/**
	 * Gets the shortcode content.
	 *
	 * @since 1.2.0
	 *
	 * @param array $atts associative array of shortcode parameters
	 * @return string shortcode content
	 */
	public static function get( $atts ) {

		return \WC_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}


	/**
	 * Render a set of documents for a given product.
	 *
	 * All shortcode parameters are optional:
	 *
	 * + orderby - set the sorting order for found documents (e.g. "title")
	 * + order - set ascending ("ASC") or descending order ("DESC")
	 *
	 * Example:
	 *
	 * [woocommerce_product_documents_list orderby="title" order="ASC"]
	 *
	 * @param array $atts associative array of shortcode parameters
	 */
	public static function output( $atts ) {

		$atts = shortcode_atts( array(
			'orderby'   => 'title',
			'order'     => 'ASC'
		), $atts );

		$query_args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $atts['orderby'],
			'order'               => $atts['order'],
			'posts_per_page'      => -1,
		);

		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'operator' => 'NOT IN',
				'terms'    => array(
					'exclude-from-search',
					'exclude-from-catalog',
					'outofstock',
				),
			),
		);

		$query_args = apply_filters( 'wc_product_documents_list_shortcode_query_args', $query_args );
		$products   = get_posts( $query_args );

		foreach ( $products as $post ) {

			if ( $product = wc_get_product( $post ) ) {

				$product_documents = $product->get_meta( '_wc_product_documents' );

				if ( ! empty( $product_documents ) ) {

					// Get the product
					$product = wc_get_product( $post->ID );

					// Get the title
					$title = get_the_title( $post->ID );

					// Render any product documents
					woocommerce_product_documents_template( $product, $title );
				}
			}
		}
	}


}
