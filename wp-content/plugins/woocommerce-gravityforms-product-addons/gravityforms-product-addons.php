<?php
/**
 * Plugin Name: WooCommerce Gravity Forms Product Add-Ons
 * Plugin URI: http://woothemes.com/products/gravity-forms-add-ons/
 * Description: Allows you to use Gravity Forms on individual WooCommerce products. Requires the Gravity Forms plugin to work.
 * Version: 3.5.4
 * Author: Element Stark
 * Author URI: https://www.elementstark.com/
 * Developer: Lucas Stark
 * Developer URI: http://www.elementstark.com/
 * Requires at least: 3.1
 * Tested up to: 6.4
 * Text Domain: wc_gf_addons

 * Copyright: © 2009-2024 Element Stark.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html

 * WC requires at least: 7.0
 * WC tested up to: 8.5
 * Woo: 18633:a6ac0ab1a1536e3a357ccf24c0650ed0
 *
 * @package WooCommerce Gravity Forms Product Add-Ons
 **/

/**
 * Required functions
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
	require_once 'woo-includes/woo-functions.php';
}

if ( is_woocommerce_active() ) {

	// Declare support for features.
	add_action(
		'before_woocommerce_init',
		function () {
			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}
	);


	load_plugin_textdomain( 'wc_gf_addons', null, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	add_action( 'init', 'wc_gravityforms_product_addons_load_textdomain', 0 );

	function wc_gravityforms_product_addons_load_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wc_gf_addons' );
		load_textdomain( 'wc_gf_addons', WP_LANG_DIR . '/woocommerce/woocommerce-gravityforms-product-addons-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc_gf_addons', false, plugin_basename( __DIR__ ) . '/i18n/languages' );
	}

	include 'compatibility.php';

	add_action( 'plugins_loaded', 'wc_gravityforms_product_addons_plugins_loaded' );

	function wc_gravityforms_product_addons_plugins_loaded() {
		if ( wc_gravityforms_is_plugin_active( 'gravityforms/gravityforms.php' ) || wc_gravityforms_is_plugin_active_for_network( 'gravityforms/gravityforms.php' ) ) {
			require_once 'gravityforms-product-addons-main.php';
		} else {
			add_action( 'admin_notices', 'wc_gravityforms_admin_install_notices' );
		}
	}

	function wc_gravityforms_is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || wc_gravityforms_is_plugin_active_for_network( $plugin );
	}

	function wc_gravityforms_is_plugin_active_for_network( $plugin ) {
		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}

		return false;
	}

	function wc_gfpa_get_plugin_url() {
		return plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) );
	}

	function wc_gravityforms_admin_install_notices() {
		?>
		<div id="message" class="updated woocommerce-error wc-connect">
			<div class="squeezer">
				<h4><?php _e( '<strong>Gravity Forms Not Found</strong> &#8211; The Gravity Forms Plugin is required to build and manage the forms for your products.', 'wc_gf_addons' ); ?></h4>
				<p class="submit">
					<a href="https://www.gravityforms.com/"
						class="button-primary"><?php _e( 'Get Gravity Forms', 'wc_gf_addons' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}

}
