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
 * Tab Manager Product Data Panel - Tabs tab
 *
 * Functions for displaying the Tab Manager product data panel Tabs tab
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_4_0 as Framework;


add_action( 'woocommerce_product_write_panel_tabs', 'wc_tab_manager_product_tabs_panel_tab' );

/**
 * Adds the "Tabs" tab to the Product Data postbox in the admin product interface
 * @access public
 */
function wc_tab_manager_product_tabs_panel_tab() {
	echo '<li class="product_tabs_tab"><a href="#woocommerce_product_tabs"><span>' . esc_html__( 'Tabs', 'woocommerce-tab-manager' ) . '</span></a></li>';
}


add_action( 'woocommerce_product_data_panels', 'wc_tab_manager_product_tabs_panel_content' );

/**
 * Adds the "Tabs" tab panel to the Product Data postbox in the product interface.
 */
function wc_tab_manager_product_tabs_panel_content() {
	global $post;

	wc_tab_manager_sortable_product_tabs( get_post_meta( $post->ID, '_product_tabs', true ) );
}


add_action( 'woocommerce_process_product_meta', 'wc_tab_manager_process_product_meta_tabs_tab', 10, 2 );

/**
 * Create/Update/Delete the product tabs
 *
 * @access public
 * @param int $post_id the post identifier
 * @param \WP_Post $post the post object
 */
function wc_tab_manager_process_product_meta_tabs_tab( $post_id, $post ) {

	$new_tabs = wc_tab_manager_process_tabs( $post_id, $post );

	$old_tabs = get_post_meta( $post_id, '_product_tabs', true );

	if ( ! is_array( $old_tabs ) ) {
		$old_tabs = array();
	}

	update_post_meta( $post_id, '_product_tabs', $new_tabs );

	do_action( 'wc_tab_manager_product_tabs_updated', $new_tabs, $old_tabs );

	// Whether the tab layout defined at the product level should be used.
	$override_tab_layout = isset( $_POST['_override_tab_layout'] ) && $_POST['_override_tab_layout'] ? 'yes' : 'no';

	update_post_meta( $post_id, '_override_tab_layout', $override_tab_layout );

	// Update / remove tab content meta.
	$args = array(
		'product_id' => $post_id,
	);

	if ( 'yes' === $override_tab_layout ) {
		$args['action'] = 'update';
	} else {
		$args['action'] = 'remove';
	}

	// Extract product & global tab IDs from tab data array.
	$tab_id_list = array();
	foreach ( $new_tabs as $key => $tab ) {
		if ( 'product' === $tab['type'] || 'global' === $tab['type'] ) {
			$tab_id_list[] = $tab['id'];
		}
	}

	// Only update meta if we have any tabs to process.
	if ( ! empty( $tab_id_list ) ) {
		wc_tab_manager()->get_search_instance()->update_products_for_tabs( $tab_id_list, $args );
	}
}
