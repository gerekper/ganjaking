<?php
/**
 * WooCommerce Tab Manager
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://docs.woocommerce.com/document/tab-manager/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * WooCommerce Tab Manager Template Functions
 *
 * Functions used in the template files to output content - in most cases
 * hooked in via the template actions. All functions are pluggable.
 */

defined( 'ABSPATH' ) or exit;


if ( ! function_exists( 'woocommerce_tab_manager_tab_content' ) ) {

	/**
	 * Renders the product/global tab content.
	 *
	 * Templates are loaded in the following order:
	 *
	 * 1. theme / woocommerce / single-product / tabs / content-{tab-name-slug}.php
	 * 2. theme / woocommerce / single-product / tabs / content.php
	 * 3. woocommerce-tab-manager / templates / single-product / tabs / content.php
	 *
	 * $tab structure:
	 * Array(
	 *   'title'    => (string) Tab title,
	 *   'priority' => (string) Tab priority,
	 *   'callback' => (mixed) callback function,
	 *   'id'       => (int) tab post identifier,
	 * )
	 *
	 * @since 1.0.5
	 *
	 * @global \WC_Tab_Manager wc_tab_manager()
	 * @global \WC_Product $product
	 *
	 * @param string $key tab key, this is the sanitized tab title with possibly a numerical suffix to avoid key clashes
	 * @param null|array $tab tab data
	 */
	function woocommerce_tab_manager_tab_content( $key, $tab ) {
		global $product;

		if ( $product && ( $tab = wc_tab_manager()->get_product_tab( $product->get_id(), $tab['id'], true ) ) ) {

			// first look for a template specific for this tab
			$template_name = "single-product/tabs/content-{$tab['name']}.php";
			$located       = locate_template( [
				trailingslashit( WC()->template_path() ) . $template_name,
				$template_name
			] );

			// if not found, fallback to the general template
			if ( ! $located ) {
				$template_name = 'single-product/tabs/content.php';
			}

			wc_tab_manager()->load_template( $template_name, [ 'tab' => $tab ] );
		}
	}

}
