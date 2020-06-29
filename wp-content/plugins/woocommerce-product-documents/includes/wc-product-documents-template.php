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
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Template Function Overrides.
 *
 * @since 1.0
 */

if ( ! function_exists( 'woocommerce_product_documents_template' ) ) {

	/**
	 * Renders the product documents element for the given product.
	 *
	 * @since 1.0
	 *
	 * @param mixed $product the product object or identifier
	 * @param string $title optional title to display above the product documents section
	 */
	function woocommerce_product_documents_template( $product, $title = null ) {

		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;

		// product exists?
		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		// product has sections/documents?
		$documents_collection = new \WC_Product_Documents_Collection( $product->get_id() );

		if ( ! $documents_collection->has_sections() ) {
			return;
		}

		// enqueue the required JavaScript
		woocommerce_product_documents_scripts_template();

		// get the default index (if any)
		$active_index = $documents_collection->get_default_section_index();

		if ( false === $active_index ) {
			$active_index = 'false';
		}

		// javascript to activate the accordion element
		ob_start();

		?>
		$( '.woocommerce-product-documents-<?php echo $product->get_id() ?>' ).accordion( {
			heightStyle: "content",              // each panel will only be as tall as its content
			collapsible: true,                   // all panels can be collapsed at once
			active: <?php echo $active_index; ?> // the active panel (if any)
		} );
		<?php

		wc_enqueue_js( ob_get_clean() );

		// load the template
		wc_get_template(
			'single-product/product-documents.php',
			array(
				'title'                => $title,
				'product'              => $product,
				'product_id'           => $product->get_id(),
				'documents_collection' => $documents_collection,
			),
			'',
			wc_product_documents()->get_plugin_path() . '/templates/'
		);
	}

}


if ( ! function_exists( 'woocommerce_product_documents_scripts_template' ) ) {


	/**
	 * Enqueue required scripts/styles:
	 *
	 * + jQuery UI Accordion
	 * + Product Documents frontend styling
	 * + jQuery UI Smoothness CSS
	 *
	 * @since 1.0
	 */
	function woocommerce_product_documents_scripts_template() {
		global $wp_scripts;

		// enqueue the jquery accordion script
		wp_enqueue_script( 'jquery-ui-accordion' );

		// enqueue the frontend styles
		wp_enqueue_style( 'wc-product-documents', wc_product_documents()->get_plugin_url() . '/assets/css/frontend/wc-product-documents.min.css', false, \WC_Product_Documents::VERSION );

		// get jQuery UI version
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

		// enqueue UI CSS
		wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
	}


}
