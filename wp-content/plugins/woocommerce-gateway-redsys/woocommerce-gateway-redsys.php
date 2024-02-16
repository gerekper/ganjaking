<?php
/**
 * Plugin Name: WooCommerce Servired/RedSys Spain Gateway
 * Plugin URI: https://woo.com/products/redsys-gateway/
 * Description: Extends WooCommerce with RedSys gateway.
 * Version: 24.3.1
 * Author: José Conti
 * Author URI: https://www.joseconti.com/
 * Tested up to: 6.4
 * WC requires at least: 7.4
 * WC tested up to: 8.3
 * Woo: 187871:50392593e834002d8bee386333d1ed3c
 * Text Domain: woocommerce-redsys
 * Domain Path: /languages/
 * Copyright: (C) 2013 - 2024 José Conti
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WooCommerce Redsys Gateway
 * @since 1.0.0
 * @author José Conti.
 * @link https://joseconti.com
 * @link https://redsys.joseconti.com
 * @link https://woo.com/products/redsys-gateway/
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2024 José Conti.
 */

use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! defined( 'REDSYS_VERSION' ) ) {
	define( 'REDSYS_VERSION', '24.3.1' );
}
if ( ! defined( 'REDSYS_LICENSE_SITE_ID' ) ) {
	define( 'REDSYS_LICENSE_SITE_ID', 1 );
}
if ( ! defined( 'REDSYS_FLUSH_VERSION' ) ) {
	define( 'REDSYS_FLUSH_VERSION', 200 );
}

if ( ! defined( 'REDSYS_PLUGIN_URL_P' ) ) {
	define( 'REDSYS_PLUGIN_URL_P', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'REDSYS_PLUGIN_PATH_P' ) ) {
	define( 'REDSYS_PLUGIN_PATH_P', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'REDSYS_PLUGIN_FILE' ) ) {
	define( 'REDSYS_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'REDSYS_ABSPATH' ) ) {
	define( 'REDSYS_ABSPATH', dirname( REDSYS_PLUGIN_FILE ) . '/' );
}

if ( ! defined( 'REDSYS_PLUGIN_BASENAME' ) ) {
	define( 'REDSYS_PLUGIN_BASENAME', plugin_basename( REDSYS_PLUGIN_FILE ) );
}

if ( ! defined( 'REDSYS_POST_UPDATE_URL_P' ) ) {
	define( 'REDSYS_POST_UPDATE_URL_P', 'https://redsys.joseconti.com/2024/02/04/woocommerce-redsys-gateway-24-3-0/' );
}

if ( ! defined( 'REDSYS_ITEM_NANE' ) ) {
	define( 'REDSYS_ITEM_NANE', 'woocommerce-gateway-redsys' );
}
$spaces = wp_spaces_regexp();
$prefix = preg_replace( "/$spaces/", '_', strtolower( REDSYS_ITEM_NANE ) );
if ( ! defined( 'REDSYS_PREFIX' ) ) {
	define( 'REDSYS_PREFIX', $prefix );
}

add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

require_once REDSYS_PLUGIN_PATH_P . 'includes/defines.php';
require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-redsys-push-notifications.php'; // Version 18.0 Add Push Notifications.

/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woo.com/products/redsys-gateway/
 * Copyright: (C) 2013 - 2024 José Conti
 */
function WCPSD2() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-redsys-psd2.php'; // PSD2 class for Redsys.
	return new WC_Gateway_Redsys_PSD2();
}

/**
 * Global functions WCRed
 */
function WCRed() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-redsys-global.php'; // Global class for global functions.
	return new WC_Gateway_Redsys_Global();
}

/**
 * Package: WooCommerce Redsys Gateway
 * Plugin URI: https://woo.com/products/redsys-gateway/
 * Copyright: (C) 2013 - 2024 José Conti
 */
function redsys_deactivate_plugins() {
	include_once REDSYS_PLUGIN_DATA_PATH_P . 'deactivate-plugins.php';
	$plugins = array();
	$plugins = plugins_to_deactivate();
	deactivate_plugins( $plugins, true );
}
add_action( 'admin_init', 'redsys_deactivate_plugins' );

// Site Health.
require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-redsys-site-health.php';
require_once REDSYS_PLUGIN_NOTICE_PATH_P . 'notices.php';
require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-redsys-card-images.php';
require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-redsys-qr-codes.php';
require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-redsys-advanced-setings.php';
require_once REDSYS_PLUGIN_PATH_P . 'bloques-redsys/bloques-redsys.php';


if ( ! class_exists( 'WooRedsysAPI' ) ) {
	require_once REDSYS_PLUGIN_API_REDSYS_PATH . 'apiRedsys7.php';
	define( 'REDSYS_API_LOADED', 'yes' );
}

if ( ! class_exists( 'WooRedsysAPIWS' ) ) {
	require_once REDSYS_PLUGIN_API_REDSYS_PATH . 'apiRedsysWs7.php';
	define( 'REDSYS_API_LOADED_WS', 'yes' );
}

require_once REDSYS_PLUGIN_API_REDSYS_PATH . 'initRedsysApi.php';

if ( defined( 'REDSYS_WOOCOMMERCE_VERSION' ) ) {
	return;
}

add_action( 'plugins_loaded', 'woocommerce_gateway_redsys_premium_init', 12 );

/**
 * Add Query Vars
 *
 * @param array $vars Query vars.
 */
function redsys_add_query_vars( $vars ) {
	$vars[] = 'add-redsys-method';
	return $vars;
}
add_filter( 'query_vars', 'redsys_add_query_vars' );

/**
 * Add Endpoint
 */
function redsys_add_endpoint() {
	global $wp_rewrite;

	add_rewrite_endpoint( 'add-redsys-method', EP_ALL );

	if ( WCRed()->has_to_flush() ) {
		$wp_rewrite->flush_rules();
	}
}
add_action( 'init', 'redsys_add_endpoint', 0 );
add_action( 'parse_request', array( 'WC_Gateway_Redsys', 'redsys_handle_requests' ) );

/**
 * Query Vars Pay
 *
 * @param array $vars Query vars.
 */
function redsys_add_query_vars_pay( $vars ) {
	$vars[] = 'redsys-add-card';
	return $vars;
}
add_filter( 'query_vars', 'redsys_add_query_vars_pay' );

/**
 * Add Endpoint Pay
 */
function redsys_add_endpoint_pay() {
	global $wp_rewrite;

	add_rewrite_endpoint( 'redsys-add-card', EP_ALL );

	if ( WCRed()->has_to_flush() ) {
		$wp_rewrite->flush_rules();
	}
}
add_action( 'init', 'redsys_add_endpoint_pay', 0 );

/**
 * Custom Template Pay
 *
 * @param string $template Template.
 */
function redsys_custom_template_pay( $template ) {
	global $wp_query;

	if ( isset( $wp_query->query_vars['redsys-add-card'] ) ) {
		$template = REDSYS_PLUGIN_PATH_P . 'includes/redsys-add-card.php';
	}
	return $template;
}
add_filter( 'template_include', 'redsys_custom_template_pay' );

/**
 * WooCommerce Redsys Gateway Init
 */
function woocommerce_gateway_redsys_premium_init() {

	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-redsys-scheduled-actions.php';

	load_plugin_textdomain( 'woocommerce-redsys', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	/**
	 * Add Select2 to users Test Field
	 */
	function redsys_add_select2() {

		$screen = get_current_screen();

		if ( 'woocommerce_page_wc-settings' === $screen->id ) {
			wp_register_script( 'redsys-select2', REDSYS_PLUGIN_URL_P . 'assets/js/test-users-min.js', array( 'jquery', 'select2' ), REDSYS_VERSION, true );
			wp_enqueue_script( 'redsys-select2' );
		}
		if ( 'woocommerce_page_paygold-page' === $screen->id ) {
			wp_register_script( 'redsys-select2b', REDSYS_PLUGIN_URL_P . 'assets/js/pay-gold-search-user.js', array( 'jquery', 'select2' ), REDSYS_VERSION, true );
			wp_enqueue_script( 'redsys-select2b' );
		}
		if ( 'woocommerce_page_wc-settings' === $screen->id ) {
			wp_register_script( 'redsys-select2c', REDSYS_PLUGIN_URL_P . 'assets/js/shipping-search.js', array( 'jquery', 'select2' ), REDSYS_VERSION, true );
			wp_enqueue_script( 'redsys-select2c' );
		}
	}
	/**
	 * Get users for Test Field
	 */
	function redsys_get_users_settings_ajax_callback() {

		if ( ! isset( $_GET['q'] ) ) {
			wp_die();
		}
		$search = sanitize_text_field( wp_unslash( $_GET['q'] ) );
		$args   = array(
			'search'         => "*{$search}*",
			'fields'         => 'all',
			'search_columns' => array( 'user_login', 'user_email', 'user_nicename' ),
		);
		// The User Query.
		$user_query = new WP_User_Query( $args );
		$users      = $user_query->get_results();

		if ( ! empty( $users ) ) {
			$return = array();
			foreach ( $users as $user ) {
				$user_info = get_userdata( $user->ID );
				$return[]  = array( $user_info->ID, $user_info->user_email );
			}
			echo wp_json_encode( $return );
			wp_die();
		} else {
			wp_die();
		}
	}
	/**
	 * Get shipping methods for Test Field
	 */
	function redsys_search_shipping_methods_callback() {
		$term = isset( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : '';

		// Obtener instancias de métodos de envío.
		$shipping_methods = WC_Shipping::instance()->get_shipping_methods();

		// Preparar un array para los resultados.
		$results = array();

		// Iterar sobre los métodos de envío y filtrarlos.
		foreach ( $shipping_methods as $id => $shipping_method ) {
			if ( isset( $shipping_method->enabled ) && $shipping_method->enabled === 'yes' ) {
				// Verificar si el término de búsqueda coincide con el título del método de envío.
				if ( stripos( $shipping_method->method_title, $term ) !== false ) {
					$results[] = array(
						'id'   => $id,
						'text' => $shipping_method->method_title,
					);
				}
			}
		}
		wp_send_json( $results );
	}
	add_action( 'admin_enqueue_scripts', 'redsys_add_select2' );
	add_action( 'wp_ajax_redsys_search_shipping_methods', 'redsys_search_shipping_methods_callback' );
	add_action( 'wp_ajax_redsys_get_users_settings_search_users', 'redsys_get_users_settings_ajax_callback' );
	add_action( 'wp_ajax_nopriv_redsys_get_users_settings_search_users_show_gateway', 'redsys_get_users_settings_ajax_callback' );
	add_action( 'wp_ajax_redsys_get_users_settings_search_users_show_gateway', 'redsys_get_users_settings_ajax_callback' );
	add_action( 'wp_ajax_nopriv_verificar_estado_pago', array( 'WC_Gateway_Bizum_Checkout_Redsys', 'verificar_estado_pago_ajax' ) );
	add_action( 'wp_ajax_verificar_estado_pago', array( 'WC_Gateway_Bizum_Checkout_Redsys', 'verificar_estado_pago_ajax' ) );

	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-redsys.php';

	/**
	 * Add the Gateway to WooCommerce
	 *
	 * @param array $methods WooCommerce payment methods.
	 */
	function woocommerce_add_gateway_redsys_gateway( $methods ) {
		$methods[] = 'WC_Gateway_redsys';
		return $methods;
	}
	add_filter( 'woocommerce_payment_gateways', 'woocommerce_add_gateway_redsys_gateway' );

	// inlude metaboxes.
	require_once REDSYS_PLUGIN_METABOXES_PATH . 'metaboxes.php';

	/**
	 * Redirect users to Checkout after add to cart
	 *
	 * @param string $checkout_url URl checkout.
	 */
	function redsys_add_to_cart_redirect( $checkout_url ) {

		if ( ! is_checkout() && ! is_wc_endpoint_url() ) {

			$redirect = WCRed()->get_redsys_option( 'checkoutredirect', 'redsys' );

			if ( 'yes' === $redirect ) {
				$checkout_url = wc_get_checkout_url();
			}
		}
		return $checkout_url;
	}
	add_filter( 'woocommerce_add_to_cart_redirect', 'redsys_add_to_cart_redirect' );

	require_once REDSYS_PLUGIN_STATUS_PATH . 'status.php';

	/**
	 * Make Preauthorized Order editable
	 *
	 * @param bol $editable Order Editable (true/false).
	 * @param obj $order Order object.
	 */
	function redsys_preauthorized_is_editable( $editable, $order ) {

		if ( 'redsys-pre' === $order->get_status() ) {
			$editable = true;
		}
		return $editable;
	}
	add_filter( 'wc_order_is_editable', 'redsys_preauthorized_is_editable', 10, 2 );

	/**
	 * Add button to confirm preauthorization
	 *
	 * @param obj $order Order object.
	 */
	function redsys_add_buttom_preauthorization_ok( $order ) {
		if ( 'redsys-pre' === $order->get_status() ) {
			echo '<button type="button" class="button redsys-confirm-preauthorization">' . esc_html__( 'Confirm Preauthorization', 'woocommerce-redsys' ) . '</button>';
		} else {
			return;
		}
	}
	add_action( 'woocommerce_order_item_add_action_buttons', 'redsys_add_buttom_preauthorization_ok' );

	/**
	 * Add button to charge deposits.
	 *
	 * @param obj $order Order object.
	 */
	function redsys_add_buttom_charge_deposits( $order ) {
		if ( 'partial-payment' === $order->get_status() ) {
			$amount = 0;

			foreach ( $order->get_items() as $item ) {
				if ( ! empty( $item['is_deposit'] ) ) {
					$deposit_full_amount_ex_vat = '';
					$deposit_full_amount        = '';
					$deposit_full_amount_ex_vat = (float) $item['_deposit_full_amount_ex_tax'];
					$deposit_full_amount        = (float) $item['_deposit_full_amount'];

					if ( ! empty( $deposit_full_amount ) ) {
						$amount = $deposit_full_amount + $amount;
					} else {
						$amount = $deposit_full_amount_ex_vat + $amount;
					}
				}
			}
			$total     = $order->get_total();
			$remainder = $amount - $total;

			echo '<button type="button" class="button redsys-charge-full-deposit">' . esc_html__( 'Collect the remainder With Redsys: ', 'woocommerce-redsys' ) . esc_html( $remainder ) . '</button>';
		} else {
			return;
		}
	}
	add_action( 'woocommerce_order_item_add_action_buttons', 'redsys_add_buttom_charge_deposits' );

	/**
	 * Redsys CSS
	 */
	function redsys_css() {
		global $post_type;

		$current_screen = get_current_screen();

		if ( 'shop_order' === $post_type || 'woocommerce_page_wc-settings' === $current_screen->id || 'woocommerce_page_wc-orders' === $current_screen->id ) {
			wp_register_style( 'redsys-css', plugins_url( 'assets/css/redsys-css.css', __FILE__ ), array(), REDSYS_VERSION );
			wp_enqueue_style( 'redsys-css' );
		}

	}
	add_action( 'admin_enqueue_scripts', 'redsys_css' );

	/**
	 * Redsys Front CSS
	 */
	function redsys_add_front_css() {

		if ( is_wc_endpoint_url( 'add-payment-method' ) ) {
			wp_enqueue_style( 'redsys-style-front', REDSYS_PLUGIN_URL_P . 'assets/css/redsys-add-payment-method.css', array(), REDSYS_VERSION );
		}
	}
	add_action( 'wp_enqueue_scripts', 'redsys_add_front_css' );

	/**
	 * Redsys capture Order ID
	 */
	function redsys_capture_order_id() {
		if ( ! is_admin() && is_checkout() ) {
			global $post;
			
			// Verifica si el contenido del post no contiene el shortcode de checkout
			if ( ! has_shortcode( $post->post_content, 'woocommerce_checkout' ) ) {
				wp_enqueue_script( 'redsys_order_id_react', REDSYS_PLUGIN_URL_P . 'assets/js/capture-order-id.js', array('jquery'), null, true );
				wp_localize_script( 'redsys_order_id_react', 'redsysAjax', array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'redsys_ajax_order_id_nonce' )
				));
			}
		}
	}
	add_action('wp_enqueue_scripts', 'redsys_capture_order_id');
	/**
	 *  Handle Redsys capture Order ID
	 */
	function handle_redsys_capture_order_id() {
		check_ajax_referer('redsys_ajax_order_id_nonce', 'nonce');
	
		$order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
		// Repite para cada campo enviado
	
		// Aquí puedes guardar los datos como meta del pedido
		if( ! empty( $order_id )) {
			update_post_meta( $order_id, '_navegador_base64', $_POST['navegadorBase64'] );
			// Repite para cada dato que quieras guardar
		}
	
		wp_send_json_success('Datos guardados');
	}
	add_action('wp_ajax_redsys_check_order_id', 'handle_redsys_capture_order_id');
	add_action('wp_ajax_nopriv_redsys_check_order_id', 'handle_redsys_capture_order_id');

	/**
	 * Redsys preauthorized JS
	 */
	function redsys_preauthorized_js() {
		global $post;

		$screen = get_current_screen();

		if ( is_admin() && ( 'shop_order' === $screen->id || 'woocommerce_page_wc-orders' === $screen->id ) ) {

			if ( isset( $_GET['id'] ) ) {
				$order_id = sanitize_text_field( wp_unslash( $_GET['id'] ) );
			}

			wp_enqueue_script( 'redsysajax-script', plugins_url( '/assets/js/preauthorizations-min.js', __FILE__ ), array( 'jquery', 'stupidtable', 'jquery-tiptip' ), REDSYS_VERSION, true );
			$done = true;
			if ( isset( $post->ID ) ) {
				$post_id = $post->ID;
			} elseif ( isset( $order_id ) ) {
				$post_id = $order_id;
			} else {
				$done = false;
			}
			if ( $done ) {
				$params = array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'postid'   => $post_id,
				);
				wp_localize_script( 'redsysajax-script', 'redsys_preauthorizations', $params );
			}
		}
	}
	add_action( 'admin_enqueue_scripts', 'redsys_preauthorized_js' );

	/**
	 * Redsys charge deposit JS
	 */
	function redsys_charge_deposit_js() {
		global $post;

		$screen = get_current_screen();

		if ( is_admin() && 'shop_order' === $screen->id ) {
			wp_enqueue_script( 'redsysajax-script-2', plugins_url( '/assets/js/woo-deposits-charge-min.js', __FILE__ ), array( 'jquery', 'stupidtable', 'jquery-tiptip' ), REDSYS_VERSION, true );

			if ( isset( $post->ID ) ) {
				$post_id = $post->ID;
			} else {
				$post_id = '';
			}
			$params = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'postid'   => $post_id,
			);
			wp_localize_script( 'redsysajax-script-2', 'redsys_charge_depo', $params );
		}
	}
	add_action( 'admin_enqueue_scripts', 'redsys_charge_deposit_js' );

	// Adding all Redsys Gateways.

	$private_product     = WCRed()->get_redsys_option( 'privateproduct', 'redsys' );
	$sent_email_template = WCRed()->get_redsys_option( 'sentemailscustomers', 'redsys' );
	$thankyoucheck       = WCRed()->get_redsys_option( 'sendemailthankyou', 'redsys' );
	$thankyourecipe      = WCRed()->get_redsys_option( 'showthankyourecipe', 'redsys' );

	// Adding Private Products.
	if ( 'yes' === $private_product ) {
		require_once REDSYS_PLUGIN_PATH_P . 'includes/private-products.php';
	}

	// Adding emails Templates.
	if ( 'yes' === $sent_email_template ) {
		require_once REDSYS_PLUGIN_PATH_P . 'includes/emails/class-redsys-wc-email.php';
	}

	// Adding Thank you Check.
	if ( 'yes' === $thankyoucheck ) {
		require_once REDSYS_PLUGIN_PATH_P . 'includes/thank-you-checks.php';
	}

	// Adding Thank you Recipe.
	if ( 'yes' === $thankyourecipe ) {
		require_once REDSYS_PLUGIN_PATH_P . 'includes/thank-you-receipe.php';
	}

	// Adding Plugin List Links.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-plugin-list-links-redsys-premium.php'; // Version 16.1. Add Links to plugin list.

	// Adding Dashboard Widget.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-redsys-wp-dashboard.php'; // Version 16.1. WordPress Dashboard.

	// Adding all Redsys Gateways.

	// Adding Bizum.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-bizum-redsys.php'; // Bizum Version 6.0.

	// Adding MasterPass.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-masterpass-redsys.php'; // MasterPass Version 7.0.

	// Adding Redsys Bank Transfer.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-redsys-bank-transfer.php'; // Bank Transfer Version 9.0.

	// Adding InSIte.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-insite-redsys.php'; // Insite version 10.0. (version 15 refactoring).

	// Adding Direct Debit stand alone.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-direct-debit-redsys.php'; // Insite version 11.0.

	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-bizum-checkout-redsys.php'; // Bizum Checkout Version 21.0.

	// Adding Tokens in admin user profile.

	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-redsys-profile.php'; // Version 14.0.

	// Adding Pay Gold.

	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-paygold-redsys.php'; // Paygold Version 16.0.

	require_once REDSYS_PLUGIN_PATH_P . 'includes/banner-live.php';

	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-rest-redsys.php'; // Version 17.1.0.

	// Adding Google Pay redirection.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-googlepay-redirection-redsys.php'; // Google Pay version 21.2.0.

	// Adding Google Pay Checkout.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-googlepay-checkout.php'; // Google Pay version 22.0.0.

	// Adding Apple Pay Checkout.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-wc-gateway-apple-pay-checkout.php'; // Apple Pay version 23.0.0.

	// Pay with One Click.
	require_once REDSYS_PLUGIN_CLASS_PATH_P . 'class-redsys-pay-one-clic.php'; // Pay with one click 24.0.0.

	/**
	 * Add Paygold page.
	 */
	if ( WCRed()->is_gateway_enabled( 'paygold' ) ) {
		include_once REDSYS_PLUGIN_PATH_P . 'includes/paygold-page.php';
		add_action( 'admin_menu', 'paygold_menu' );
	}
	/**
	 * Add Paygold menu.
	 */
	function paygold_menu() {
		global $paygold_page;

		$paygold_page = add_submenu_page(
			'woocommerce',
			esc_html__( 'Pay Gold Tools', 'woocommerce-redsys' ),
			esc_html__( 'Pay Gold Tools', 'woocommerce-redsys' ),
			'manage_options',
			'paygold-page',
			'paygold_page'
		);
	}
	/**
	 * Paygold Ajax Callback.
	 */
	function redsys_paygond_ajax_callback() {

		if ( is_admin() ) {

			if ( ! isset( $_GET['q'] ) ) {
				wp_die();
			}

			$search = sanitize_text_field( wp_unslash( $_GET['q'] ) );
			$args   = array(
				'search'         => "*{$search}*",
				'fields'         => 'all',
				'search_columns' => array( 'user_login', 'user_email', 'user_nicename' ),
			);

			// The User Query.
			$user_query = new WP_User_Query( $args );
			$users      = $user_query->get_results();
			if ( ! empty( $users ) ) {
				$return = array();
				foreach ( $users as $user ) {
					$user_info = get_userdata( $user->ID );
					$return[]  = array( $user_info->ID, $user_info->user_email );
				}
				echo wp_json_encode( $return );
				die;
			} else {
				die;
			}
		}
	}
	add_action( 'wp_ajax_woo_search_users_paygold', 'redsys_paygond_ajax_callback' );

	/**
	 * Paygold CSS.
	 */
	function redsys_paygold_css() {
		wp_register_style( 'redsys_css_slect2', REDSYS_PLUGIN_URL_P . 'assets/css/select2.css', false, REDSYS_VERSION );
		wp_enqueue_style( 'redsys_css_slect2' );
	}
	add_action( 'admin_enqueue_scripts', 'redsys_paygold_css' );

	/**
	 * Add Ajax Actions.
	 */
	function redsys_add_ajax_actions() {
		if ( ! is_checkout() && ! is_wc_endpoint_url() ) {

			// Ajax Preautorizaciones.
			add_action( 'wp_ajax_redsys_preauth_action', array( 'WC_Gateway_Redsys', 'redsys_preauthorized_js_callback' ) );
			// Ajax carga deposits.
			add_action( 'wp_ajax_redsys_charge_depo_action', array( 'WC_Gateway_redsys', 'redsys_charge_depo_js_callback' ) );
		}

		add_action( 'wp_ajax_check_token_insite_from_action', array( 'WC_Gateway_InSite_Redsys', 'check_token_insite_from_action' ) );
		add_action( 'wp_ajax_nopriv_check_token_insite_from_action', array( 'WC_Gateway_InSite_Redsys', 'check_token_insite_from_action' ) );
		// Conservar.
		add_action( 'wp_ajax_check_token_insite_from_action_checkout', array( 'WC_Gateway_InSite_Redsys', 'check_token_insite_from_action_checkout' ) );
		add_action( 'wp_ajax_nopriv_check_token_insite_from_action_checkout', array( 'WC_Gateway_InSite_Redsys', 'check_token_insite_from_action_checkout' ) );

		// Add Ajax Apple Pay.
		add_action( 'wp_ajax_validate_merchant', array( 'WC_Gateway_Apple_Pay_Checkout', 'handle_ajax_request_applepay' ) );
		add_action( 'wp_ajax_nopriv_validate_merchant', array( 'WC_Gateway_Apple_Pay_Checkout', 'handle_ajax_request_applepay' ) );
		add_action( 'wp_ajax_check_payment_status', array( 'WC_Gateway_Apple_Pay_Checkout', 'check_payment_status' ) );
		add_action( 'wp_ajax_nopriv_check_payment_status', array( 'WC_Gateway_Apple_Pay_Checkout', 'check_payment_status' ) );
	}
	add_action( 'admin_init', 'redsys_add_ajax_actions' );

	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
		add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( 'WC_Gateway_Redsys', 'redsys_add_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( 'WC_Gateway_Redsys', 'redsys_bulk_actions_handler' ), 10, 3 );
	} else {
		add_filter( 'bulk_actions-edit-shop_order', array( 'WC_Gateway_Redsys', 'redsys_add_bulk_actions' ) );
		add_filter( 'handle_bulk_actions-edit-shop_order', array( 'WC_Gateway_Redsys', 'redsys_bulk_actions_handler' ), 10, 3 );
	}

	// Needed for allow "pay" 0€.
	add_filter( 'woocommerce_cart_needs_payment', array( 'WC_Gateway_Redsys_Global', 'cart_needs_payment' ), 10, 2 );
	add_filter( 'woocommerce_order_needs_payment', array( 'WC_Gateway_Redsys_Global', 'order_needs_payment' ), 10, 3 );
	// Add Pay Gold Bulk Actions.
	add_filter( 'bulk_actions-users', array( 'WC_Gateway_Paygold_Redsys', 'add_bulk_actions' ) );
	add_filter( 'handle_bulk_actions-users', array( 'WC_Gateway_Paygold_Redsys', 'paygold_bulk_actions_handler' ), 10, 3 );

	/**
	 * Add dns-prefetch to head.
	 */
	function redsys_woo_add_head_text() {
		echo '<!-- Added by WooCommerce Redsys Gateway v.' . esc_html( REDSYS_VERSION ) . ' - https://woo.com/products/redsys-gateway/ -->';
		if ( WCRed()->is_gateway_enabled( 'insite' ) ) {
			echo '<link rel="dns-prefetch" href="https://sis.redsys.es:443">';
			echo '<link rel="dns-prefetch" href="https://sis-t.redsys.es:25443">';
			echo '<link rel="dns-prefetch" href="https://sis-i.redsys.es:25443">';
		}
		if ( WCRed()->is_gateway_enabled( 'googlepayredsys' ) ) {
			echo '<link rel="dns-prefetch" href="https://pay.google.com">';
		}
		if ( WCRed()->is_gateway_enabled( 'applepayredsys' ) ) {
			echo '<link rel="dns-prefetch" href="https://applepay.cdn-apple.com">';
		}
		echo '<meta name="generator" content=" WooCommerce Redsys Gateway v.' . esc_html( REDSYS_VERSION ) . '">';
		echo '<!-- This site is powered by WooCommerce Redsys Gateway v.' . esc_html( REDSYS_VERSION ) . ' - https://woo.com/products/redsys-gateway/ -->';
	}
	add_action( 'wp_head', 'redsys_woo_add_head_text' );
	add_action( 'parse_request', array( 'WC_Redsys_Profile', 'redsys_handle_requests_add_method' ) );

	// Customization of the checkout buttons.
	include_once REDSYS_PLUGIN_PATH_P . 'loader/checkout-buttons.php';
	include_once REDSYS_PLUGIN_PATH_P . 'loader/one-clic-button.php';
	include_once REDSYS_PLUGIN_PATH_P . 'loader/refresh-checkout.php';
	/*
	require_once REDSYS_PLUGIN_PATH_P . 'classes/class-wc-gateway-redsys-license.php';

	new WC_Gateway_Redsys_License(
		'https://redsys.joseconti.com/',
		__FILE__,
		array(
			'version'    => REDSYS_VERSION,
			'item_name'  => REDSYS_ITEM_NANE,
			'menu_slug'  => REDSYS_ITEM_NANE . '-license',
			'menu_title' => 'REDSYS License',
			'license'    => '', // current license key.
			'prefix'     => REDSYS_PREFIX,
		)
	);
	*/

}

/**
 * Add support for WooCommerce Blocks / Payments.
 */

require_once REDSYS_PLUGIN_PATH_P . 'includes/load-checkout-blocks.php';

// WooCommerce Redsys Gateway License.
