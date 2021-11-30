<?php
/**
 * Plugin Name: WooCommerce Advanced Ajax Layered Navigation
 * Version: 1.7.0
 * Plugin URI: https://woocommerce.com/products/ajax-layered-navigation/
 * Description: Ajaxifies the standard WooCommerce Layered Nav and adds additional output types like color swatches, sizes, checkboxes, etc
 * Author URI: https://woocommerce.com
 * Author: WooCommerce
 * Tested up to: 5.8
 * WC tested up to: 5.4
 * Woo: 18675:8a0ed1b64e6a889a9f084db0ed5ece6c
 * Text Domain: woocommerce-ajax-layered-nav
 *
 * Copyright: © 2021 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-ajax-layered-nav
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_ajax_layered_nav_init' );

// Subscribe to automated translations.
add_action( 'woocommerce_translations_updates_for_woocommerce-ajax-layered-nav', '__return_true' );

/**
 * Initialize plugin.
 */
function wc_ajax_layered_nav_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_ajax_layered_nav_woocommerce_deactivated' );
		return;
	}

	define( 'WC_AJAX_LAYERED_NAV_VERSION', '1.7.0' ); // WRCS: DEFINED_VERSION.

	load_plugin_textdomain( 'woocommerce-ajax-layered-nav', false, plugin_basename( __DIR__ ) . '/languages' );

	add_filter( 'woocommerce_ajax_layered_nav_term_link', 'wc_ajax_layered_nav_maybe_preserve_brand_filter' );
	add_action( 'widgets_init', 'wc_ajax_layered_nav_register_widgets', 15 );

	// Queue Scripts and Stylesheets.
	add_action( 'wp_enqueue_scripts', 'wc_ajax_layered_nav_scripts' );
	add_action( 'admin_enqueue_scripts', 'wc_ajax_layered_nav_admin_scripts' );

	/**
	 * Pagination Wrapper
	 *
	 * Makes for easy locating on pagination after ajax callback - makes sure pagination carries through.
	 */
	add_action( 'woocommerce_pagination', 'wc_ajax_layered_nav_pagination_before', 1 );
	add_action( 'woocommerce_pagination', 'wc_ajax_layered_nav_pagination_after', 15 );

	/**
	 * Contents Wrapper
	 *
	 * Helps us know what elements to update with new content.
	 */
	add_action( 'woocommerce_before_shop_loop', 'wc_ajax_layered_nav_before_products_div', 0 );
	add_action( 'woocommerce_after_shop_loop', 'wc_ajax_layered_nav_after_products_div', 999 );
	add_action( 'wp_ajax_ajax_layered_nav_set_type', 'wc_ajax_layered_nav_ajax_set_type' );
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_ajax_layered_nav_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Advanced Ajax Layered Navigation requires %s to be installed and active.', 'woocommerce-ajax-layered-nav' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}


/**
 * If the WooCommerce Brands extension's layered navigation filter is in use, make sure we preserve this.
 *
 * @param string $link URL Link containing the filters as query arguments.
 * @return string
 */
function wc_ajax_layered_nav_maybe_preserve_brand_filter( $link ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['filter_product_brand'] ) ) {
		$link = add_query_arg( 'filter_product_brand', intval( wp_unslash( $_GET['filter_product_brand'] ) ), $link );
	}
	if ( ! empty( $_GET['filtering'] ) ) {
		$link = add_query_arg( 'filtering', '1', $link );
	}
	// phpcs:enable WordPress.Security.NonceVerification.Recommended
	return $link;
}

/**
 * Register widgets to use for filtering.
 */
function wc_ajax_layered_nav_register_widgets() {
	include_once 'widgets/class-sod-widget-ajax-layered-nav.php';
	include_once 'widgets/class-sod-widget-ajax-layered-nav-filters.php';
	include_once 'widgets/class-sod-widget-ajax-layered-nav-clear.php';

	register_widget( 'SOD_Widget_Ajax_Layered_Nav' );
	register_widget( 'SOD_Widget_Ajax_Layered_Nav_Filters' );
	register_widget( 'SOD_Widget_Ajax_Layered_Nav_Clear' );
}

/**
 * Enqueue scripts.
 */
function wc_ajax_layered_nav_scripts() {
	global $is_IE;

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_register_style( 'advanced_nav_css', plugins_url( 'assets/css/advanced_nav.css', __FILE__ ), array(), WC_AJAX_LAYERED_NAV_VERSION );

	if ( $is_IE ) {
		wp_register_script( 'html5', plugins_url( 'assets/js/html5' . $suffix . '.js', __FILE__ ), array(), WC_AJAX_LAYERED_NAV_VERSION, true );
		wp_enqueue_script( 'html5' );
	}

	// No need for this on the Single Product page.
	if ( ! is_product() && ( is_shop() || is_product_taxonomy() ) ) {
		wp_enqueue_script( 'pageloader', plugins_url( 'assets/js/ajax_layered_nav' . $suffix . '.js', __FILE__ ), array( 'jquery' ), WC_AJAX_LAYERED_NAV_VERSION, true );
		wp_enqueue_style( 'advanced_nav_css' );
	}

	$html_containers = array(
		'#products',
		'.products',
		'#pagination-wrapper',
		'.woocommerce-pagination',
		'.woo-pagination',
		'.pagination',
		'.widget_layered_nav',
		'.widget_layered_nav_filters',
		'.woocommerce-ordering',
		'.sod-inf-nav-next',
		'.woocommerce-result-count',
		'.woocommerce-info',
		'.widget_ajax_layered_nav_clear',
	);

	$clickables = array(
		'.widget_layered_nav a',
		'.widget_layered_nav input[type="checkbox"]',
		'.widget_ajax_layered_nav_filters a',
		'.widget_layered_nav_clear a',
	);

	$selects = array(
		'.widget_layered_nav select.dropdown',
	);

	$no_products        = apply_filters( 'sod_ajax_layered_no_products', '.woocommerce-info' );
	$html_containers    = apply_filters( 'sod_ajax_layered_nav_containers', $html_containers );
	$clickables         = apply_filters( 'sod_ajax_layered_nav_clickables', $clickables );
	$order_by_form      = apply_filters( 'sod_ajax_layered_nav_orderby', '.woocommerce-ordering' );
	$products_container = apply_filters( 'sod_ajax_layered_nav_product_container', '#products' );
	$inf_scroll_nav     = apply_filters( 'sod_ajax_layered_nav_inf_scroll_nav', '.sod-inf-nav-next' );
	$redirect           = apply_filters( 'woocommerce_redirect_single_search_result', false ) ? '1' : '0';
	$scroll             = apply_filters( 'sod_ajax_layered_nav_scrolltop', true ) ? '1' : '0';
	$offset             = apply_filters( 'sod_ajax_layered_nav_offset', '150' );
	$args               = array(
		'loading_img'          => esc_url( apply_filters( 'woocommerce_ajax_layered_nav_loading_img_url', plugins_url( 'assets/images/loading.gif', __FILE__ ) ) ),
		'superstore_img'       => esc_url( apply_filters( 'woocommerce_ajax_layered_nav_superstore_img_url', plugins_url( 'assets/images/ajax-loader.gif', __FILE__ ) ) ),
		'nextSelector'         => apply_filters( 'sod_aln_inf_scroll_next', '.pagination a.next' ),
		'navSelector'          => apply_filters( 'sod_aln_inf_scroll_nav', '.pagination' ),
		'itemSelector'         => apply_filters( 'sod_aln_inf_scroll_item', '#main .product' ),
		'contentSelector'      => apply_filters( 'sod_aln_inf_scroll_content', '#main ul.products' ),
		'loading_text'         => apply_filters( 'woocommerce_ajax_layered_nav_loading_text', esc_attr__( 'Loading', 'woocommerce-ajax-layered-nav' ) ),
		'containers'           => $html_containers,
		'triggers'             => $clickables,
		'selects'              => $selects,
		'orderby'              => $order_by_form,
		'product_container'    => $products_container,
		'inf_scroll_nav'       => $inf_scroll_nav,
		'search_page_redirect' => $redirect,
		'scrolltop'            => $scroll,
		'offset'               => $offset,
		'no_products'          => $no_products,
		'i18n_error_message'   => esc_attr__( 'Error getting products. Try again.', 'woocommerce-ajax-layered-nav' ),
	);

	wp_localize_script( 'pageloader', 'ajax_layered_nav', $args );
}

/**
 * Enqueue scripts.
 */
function wc_ajax_layered_nav_admin_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_style( 'colorpicker_css', plugins_url( 'assets/css/colorpicker.css', __FILE__ ), array(), WC_AJAX_LAYERED_NAV_VERSION );
	wp_enqueue_style( 'advanced_nav_css', plugins_url( 'assets/css/advanced_nav.css', __FILE__ ), array(), WC_AJAX_LAYERED_NAV_VERSION );

	wp_enqueue_script( 'advanced_nav_admin', plugins_url( 'assets/js/ajax_layered_nav_admin' . $suffix . '.js', __FILE__ ), array( 'jquery' ), WC_AJAX_LAYERED_NAV_VERSION, false );
	wp_enqueue_script( 'advanced_colorpicker', plugins_url( 'assets/js/colorpicker' . $suffix . '.js', __FILE__ ), array( 'jquery' ), WC_AJAX_LAYERED_NAV_VERSION, false );

	wp_localize_script(
		'advanced_nav_admin',
		'ajax_layered_nav',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'ajax_layered_nav_nonce' ),
		)
	);
}

/**
 * Pagination wrapper start.
 */
function ajax_layered_nav_pagination_before() {
	echo '<nav id="pagination-wrapper">';
}

/**
 * Pagination wrapper end.
 */
function ajax_layered_nav_pagination_after() {
	echo '</nav>';
}

/**
 * Products wrapper start.
 */
function wc_ajax_layered_nav_before_products_div() {
	echo '<section id="products">';
}

/**
 * Products wrapper end.
 */
function wc_ajax_layered_nav_after_products_div() {
	echo '</section>';
}

/**
 * Ajax Handler function to set admin widget options
 *
 * Returns options table
 **/
function wc_ajax_layered_nav_ajax_set_type() {
	try {
		if ( ! isset( $_POST['ajax_layered_nav_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ajax_layered_nav_nonce'] ), 'ajax_layered_nav_nonce' ) ) {
			die( 'Busted!' );
		}

		$args = array( 'hide_empty' => '0' );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$attribute_values = ! empty( $_POST['attr_name'] ) ? get_terms( wc_attribute_taxonomy_name( wp_unslash( $_POST['attr_name'] ) ), $args ) : array();
		$raw_id           = isset( $_POST['id'] ) ? wc_clean( wp_unslash( $_POST['id'] ) ) : '';
		$id_parts         = explode( '-', $raw_id );
		$number           = array_pop( $id_parts );
		$id               = implode( '-', $id_parts );

		$type = isset( $_POST['type'] ) ? wc_clean( wp_unslash( $_POST['type'] ) ) : '';
		$html = '';

		switch ( $type ) {
			case 'list':
			case 'dropdown':
			case 'checkbox':
			case 'slider':
				break;
			// Return new color picker table.
			case 'colorpicker':
				$html .= '<table class="color">
							<thead>
								<tr>
									<td>' . esc_html__( 'Name', 'woocommerce-ajax-layered-nav' ) . '</td>
									<td>' . esc_html__( 'Color Code', 'woocommerce-ajax-layered-nav' ) . '</td>
								</tr>
							</thead>
							<tbody>';
				foreach ( $attribute_values as $attribute ) {
					$html .= '<tr>
								<td class="labels"><label for="widget-' . esc_attr( $id ) . '[' . esc_attr( $number ) . '][colors][' . esc_attr( $attribute->term_id ) . ']">' . esc_html( $attribute->name ) . '</label></td>
								<td class="inputs"><input class="color_input" type="input" name="widget-' . esc_attr( $id ) . '[' . esc_attr( $number ) . '][colors][' . esc_attr( $attribute->term_id ) . ']" id="widget-' . esc_attr( $id ) . '[' . esc_attr( $number ) . '][colors][' . esc_attr( $attribute->term_id ) . ']" size="10" maxlength="7"/>
								<div class="colorSelector"><div></div></div></td>
							</tr>';
				}
				$html .= '</tbody>
						</table>';
				break;
			// Return new color picker table of sizes.
			case 'sizeselector':
				$html .= '<table class="sizes">
							<thead>
								<tr>
									<td>' . esc_html__( 'Name', 'woocommerce-ajax-layered-nav' ) . '</td>
									<td>' . esc_html__( 'Label', 'woocommerce-ajax-layered-nav' ) . '</td>
									<td></td>
								</tr>
							</thead>
							<tbody>';
				foreach ( $attribute_values as $attribute ) {
					$html .= '<tr>
								<td class="labels"><label for="widget-' . esc_attr( $id ) . '[' . esc_attr( $number ) . '][labels][' . esc_attr( $attribute->term_id ) . ']">' . esc_html( $attribute->name ) . '</label></td>
								<td class="inputs"><input type="input" name="widget-' . esc_attr( $id ) . '[' . esc_attr( $number ) . '][labels][' . esc_attr( $attribute->term_id ) . '] id="widget-' . esc_attr( $id ) . '[' . esc_attr( $number ) . '][labels][' . esc_attr( $attribute->term_id ) . ']" size="10" maxlength="7"/></td>
								<td></td>
							</tr>';
				}
				$html .= '</tbody>
						</table>';
				break;
		}
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} catch ( Exception $e ) {
		exit;
	}
	exit;
}
