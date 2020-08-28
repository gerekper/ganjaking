<?php
/**
 * Plugin Name: WooSlider - WooCommerce Products Slideshow
 * Plugin URI: https://woocommerce.com/products/wooslider-products-slideshow/
 * Description: Add slideshows of your WooCommerce products to WooSlider.
 * Version: 1.0.21
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Woo: 82250:fb2387de8d3a8501dab2329290f9d22e
 * WC tested up to: 4.2
 * Tested up to: 5.5
 * License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @package woocommerce-wooslider-products-slideshow
 */

/*
	Copyright 2019 WooCommerce
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Plugin init hook.
add_action( 'plugins_loaded', 'wooslider_products_slideshow_init' );

/**
 * Initialize plugin.
 */
function wooslider_products_slideshow_init() {

	// Make sure both WooSlider and WooCommerce are active.
	if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WooSlider' ) ) {
		add_action( 'admin_notices', 'wooslider_products_slideshow_woocommerce_deactivated' );
		return;
	}

	require_once __DIR__ . '/classes/class-wooslider-wc-products.php';
	WooSlider_WC_Products::get_instance();
}

/**
 * WooCommerce Deactivated Notice.
 */
function wooslider_products_slideshow_woocommerce_deactivated() {
	/* translators: %1$s: WooCommerce link, %2$s: WooSlider link. */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooSlider - WooCommerce Products Slideshow requires %1$s & %2$s to be installed and active.', 'wooslider-products-slideshow' ), '<a href="https://woocommerce.com" target="_blank">WooCommerce</a>', '<a href="https://woocommerce.com/products/wooslider/" target="_blank">WooSlider</a>' ) . '</p></div>';
}
