<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Customer Contributions in My Account page
 *
 * @since 1.6.0
 */
class WC_Product_Reviews_Pro_My_Account_Contributions {


	/**
	 * Hook in tabs
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		// change WooCommerce My Account page title for the new endpoint
		add_filter( 'the_title', array( $this, 'my_account_contributions_tab_title' ) );

		// insert the new item in My Account tabbed area
		add_filter( 'woocommerce_account_menu_items',             array( $this, 'add_my_account_contributions_tab_item' ) );
		add_action( 'woocommerce_account_contributions_endpoint', array( $this, 'render_my_account_contributions_endpoint_content' ), 15 );
	}


	/**
	 * Set Contributions endpoint title
	 *
	 * @since 1.6.0
	 * @param string $title
	 * @return string
	 */
	public function my_account_contributions_tab_title( $title ) {
		global $wp_query;

		$is_endpoint = isset( $wp_query->query_vars['contributions'] );

		if ( $is_endpoint && is_main_query() && in_the_loop() && is_account_page() ) {

			/* translators: Placeholders: %s contribution type name (e.g. 'My Reviews' or 'My Contributions') */
			$title = sprintf( __( 'My %s', 'woocommerce-product-reviews-pro' ), ucwords( wc_product_reviews_pro_get_enabled_types_name() ) );

			// this prevents to filter all product names in contributions list
			remove_filter( 'the_title', array( $this, 'my_account_contributions_tab_title' ) );
		}

		return $title;
	}


	/**
	 * Insert the new endpoint into the My Account menu
	 *
	 * @since 1.6.0
	 * @param array $items
	 * @return array
	 */
	public function add_my_account_contributions_tab_item( $items ) {

		$enabled_contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();

		if ( ! empty( $enabled_contribution_types ) ) {

			end( $items );

			// grab the last tab, usually logout
			// but we can't assume
			$last_tab_key   = key( $items );
			$last_tab_label = current( $items );

			array_pop( $items );

			// insert Contributions endpoint before the last item
			$items['contributions'] = ucwords( esc_attr( wc_product_reviews_pro_get_enabled_types_name() ) );

			// put back the last item
			$items[ $last_tab_key ] = $last_tab_label;
		}

		return $items;
	}


	/**
	 * Contributions endpoint HTML content
	 *
	 * @since 1.6.0
	 */
	public function render_my_account_contributions_endpoint_content() {

		// output the table contribution list
		wc_product_reviews_pro_contribution_list_table();
	}


}


