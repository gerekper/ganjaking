<?php
/**
 * Plugin Name: WooInstant - WooCommerce Instant / One Page Checkout
 * Plugin URI: https://psdtowpservice.com/wooinstant
 * Bitbucket Plugin URI: https://bitbucket.org/devshuvo/wooinstant
 * Description: Are you tired of multi-step checkout process of WooCommerce? WooInstant is your solution. Install WooInstant and your multi-step checkout process will convert to One Page Checkout. Now, all your customer have to do is "Add to Cart", a popup will appear with the cart view. Your customer can then checkout and order from that single window!. No Page Reload whatsoever!
 * Author: BootPeople
 * Text Domain: wooinstant
 * Domain Path: /lang/
 * Author URI: https://psdtowpservice.com
 * Tags: wooinstant,responsive,woocommerce
 * Version: 2.0.18
 * WC tested up to: 4.3.0
 */

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// Define WI_VERSION.
if ( ! defined( 'WI_VERSION' ) ) {
	define( 'WI_VERSION', '2.0.18' );
}

/**
* Including Plugin file for security
* Include_once
*
* @since 1.0.0
*/
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
* Loading Text Domain
*
*/
add_action('plugins_loaded', 'wooinstant_plugin_loaded_action', 10, 2);

function wooinstant_plugin_loaded_action() {
	//Internationalization
	load_plugin_textdomain( 'wooinstant', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );

	//Redux Framework calling
	if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/inc/redux-framework/ReduxCore/framework.php' ) ) {
	    require_once( dirname( __FILE__ ) . '/inc/redux-framework/ReduxCore/framework.php' );
	}

    // Load the plugin options
    if ( !isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/inc/wi-options-init.php' ) ) {
        require_once dirname( __FILE__ ) . '/inc/wi-options-init.php';
    }

}

/**
 *	Enqueue Wooinstant scripts
 *
 */
function wooinstant_enqueue_scripts(){
	global $wiopt;

	if ( $wiopt['wi-active'] != true ) {
	    return;
	}

	wp_enqueue_style('wooinstant-stylesheet', plugin_dir_url( __FILE__ ) . 'assets/css/styles.css','',WI_VERSION );

	if ( $wiopt['wi-drawer-direction'] == '1' ) {
    	wp_enqueue_script( 'drawer-left-right', plugin_dir_url( __FILE__ ) . 'assets/js/drawer-left-right.js', array('jquery'), WI_VERSION, true );
	} elseif( $wiopt['wi-drawer-direction'] == '2' ) {
		wp_enqueue_script( 'drawer-left-right', plugin_dir_url( __FILE__ ) . 'assets/js/drawer-right-left.js', array('jquery'), WI_VERSION, true );
	} elseif( $wiopt['wi-drawer-direction'] == '3' ) {
		wp_enqueue_script( 'drawer-left-right', plugin_dir_url( __FILE__ ) . 'assets/js/drawer-right-left.js', array('jquery'), WI_VERSION, true );
	} else {
		wp_enqueue_script( 'drawer-right-left', plugin_dir_url( __FILE__ ) . 'assets/js/drawer-right-left.js', array('jquery'), WI_VERSION, true );
	}

	wp_enqueue_script( 'iframeResizer', plugin_dir_url( __FILE__ ) . 'assets/js/iframeResizer.min.js', array('jquery'), '', true );

	if( is_page('wooinstant-checkout') ){
		wp_enqueue_script( 'iframeResizer-contentWindow', plugin_dir_url( __FILE__ ) . 'assets/js/iframeResizer.contentWindow.min.js', array('jquery'), '', true );
	}

    wp_enqueue_script( 'wi-ajax-script', plugin_dir_url( __FILE__ ) . 'assets/js/wi-ajax-script.js', array('jquery'), WI_VERSION, true );

	if( $wiopt['wi-disable-quickview'] != 1 ){
		wp_enqueue_script('wi-ajax-quick-view.js', plugin_dir_url(__FILE__) . 'assets/js/wi-ajax-quick-view.js', array('jquery'), WI_VERSION, true);
	}

	wp_localize_script( 'wi-ajax-script', 'wi_ajax_params',
		array(
	        'wi_ajax_nonce' => wp_create_nonce( 'wi_ajax_nonce' ),
	        'wi_ajax_url' => admin_url( 'admin-ajax.php' )
	    )
    );

	/**
	 * Handle WC frontend scripts
	 *
	 * @package WooCommerce/Classes
	 * @version 2.3.0
	 * http://woocommerce.wp-a2z.org/oik_api/wc_frontend_scriptsget_script_data/
	 */
	//remove_action('wp_head', array($GLOBALS['woocommerce'], 'generator'));

	//first check that woo exists to prevent fatal errors
	if( function_exists('is_woocommerce') ) {
		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? : '.min';
		$lightbox_en          = 'yes' === get_option( 'woocommerce_enable_lightbox' );
		$ajax_cart_en         = 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' );
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'js/frontend/';

		global $wp_scripts;
    	$wp_scripts->registered[ 'wc-checkout' ]->src = plugin_dir_url( __FILE__ ) . 'assets/js/checkout.js';

		wp_enqueue_script( 'wc-cart', $frontend_script_path . 'cart' . $suffix . '.js', array( 'jquery', 'wc-country-select', 'wc-address-i18n' ) );

	    wp_localize_script('wc-cart', 'wc_cart_params', apply_filters('wc_cart_params', array(
			'ajax_url' => WC()->ajax_url() ,
			'wc_ajax_url' => WC_AJAX::get_endpoint(' %%endpoint%%') ,
			'ajax_loader_url' => apply_filters('woocommerce_ajax_loader_url', $assets_path . 'images / ajax - loader@2x . gif') ,
			'update_shipping_method_nonce' => wp_create_nonce('update-shipping-method') ,
		)));

		wp_enqueue_script( 'wc-checkout', $frontend_script_path . 'checkout' . $suffix . '.js', array( 'jquery', 'wc-address-i18n' ) );

	    wp_localize_script('wc-checkout', 'wc_checkout_params', apply_filters('wc_checkout_params', array(
			'ajax_url'                  => WC()->ajax_url(),
			'wc_ajax_url'               => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'update_order_review_nonce' => wp_create_nonce( 'update-order-review' ),
			'apply_coupon_nonce'        => wp_create_nonce( 'apply-coupon' ),
			'remove_coupon_nonce'       => wp_create_nonce( 'remove-coupon' ),
			'option_guest_checkout'     => get_option( 'woocommerce_enable_guest_checkout' ),
			'checkout_url'              => WC_AJAX::get_endpoint( 'checkout' ),
			'is_checkout'               => is_checkout() && empty( $wp->query_vars['order-pay'] ) && ! isset( $wp->query_vars['order-received'] ) ? 1 : 0,
			'debug_mode'                => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'i18n_checkout_error'       => esc_attr__( 'Error processing checkout. Please try again.', 'woocommerce' ),
		)));

		wp_enqueue_script( 'wc-add-to-cart-variation', $frontend_script_path . 'add-to-cart-variation' . $suffix . '.js', array( 'jquery', 'wp-util', 'jquery-blockui' ) );

	    wp_localize_script('wc-add-to-cart-variation', 'wc_add_to_cart_variation_params', apply_filters('wc_add_to_cart_variation_params', array(
			'wc_ajax_url'                      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
			'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
			'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
			'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' ),
		)));

		wp_enqueue_style('select2');
		wp_enqueue_script('select2');
		wp_enqueue_script( 'wc-country-select' );

	}
}
add_filter( 'wp_enqueue_scripts', 'wooinstant_enqueue_scripts', 200 ,2 ); // Giving high priority

/**
 *	Wooinstant Menu Layout
 */
require_once( dirname( __FILE__ ) . '/inc/wooinstant-layout.php' );

/**
 *	Wooinstant Functions
 */
require_once( dirname( __FILE__ ) . '/inc/wooinstant-functions.php' );

/**
 *	Wooinstant Updates
 */
require_once( dirname( __FILE__ ) . '/inc/update-checker.php' );

/**
 *	Plugin activation hook
 *
 */
function wooinstant_activation_redirect( $plugin ) {
	if( $plugin == plugin_basename( __FILE__ ) ) {
	    // redirect option page after installed
	    wp_redirect( admin_url( 'admin.php?page=_woinstant' ) );
	    exit;
	}
}
add_action( 'activated_plugin', 'wooinstant_activation_redirect' );

/**
 *	Plugin Deactivate Confirmation
 *
 */
if( !function_exists('wooinstant_plugin_admin_footer_scripts') ){
	function wooinstant_plugin_admin_footer_scripts(){
		ob_start(); ?>
		<script type="text/javascript">
			jQuery( function($) {
				jQuery(document).ready( function(){
					//plugin deactive
				    jQuery(document).on('click', '[data-plugin="wooinstant/wooinstant.php"] .deactivate>a', function(e) {
				        e.preventDefault()
				        var urlRedirect = jQuery(this).attr('href');
				        if ( confirm( 'Are you sure ?\n( You will lose your saved options )' ) ) {
				            window.location.href = urlRedirect;
				        } else {
				            //what else ?
				        }
				    });

				    //Notice Dismiss
				    $('.wi-notice a,.wi-notice button').on('click',function(e){
				    	//e.preventDefault();
				    	//var data = $(this).data('value');

			            $.post(ajaxurl, {
			                action: "dismissnotice",
			                dismiss: 1,
			            }, function (data) {
			            });

			            $('.wi-notice').fadeOut();
				    })

				});
			});
		</script> <?php
		echo ob_get_clean();
	}
}
add_action( 'admin_footer', 'wooinstant_plugin_admin_footer_scripts' );

/**
 * Notice if WooCommerce is inactive
 */
function wooinstant_admin_notice_warn() {
	if ( !class_exists( 'WooCommerce' ) ) { ?>
	    <div class="notice notice-warning is-dismissible">
	        <p>
	        	<strong><?php esc_attr_e( 'Wooinstant requires WooCommerce to be activated ', 'wooinstant' ); ?> <a href="<?php echo esc_url( admin_url('/plugin-install.php?s=WooCommerce&tab=search&type=term') ); ?>">Install Now</a></strong>
	        </p>
	    </div> <?php
    }
    if( get_option( "dismiss-notice") != 1 ){ ?>
		<div class="wi-notice notice notice-info is-dismissible">
			<style>
				.wi-notice{
					clear: both;
					overflow: hidden;
				}
				.wi-notice .dashicons{
					height: unset;
					width: unset;
					font-size: 12px;
					vertical-align: middle;
				}
				.wi-notice a{
					margin-left: 10px;
					text-decoration: none;
					cursor: pointer;
				}
			</style>
		    <p style="float: left;">If you like <strong>Wooinstant</strong> please leave a review</p>
		    <p style="float: right;">
		    	<a data-value="1" href="https://codecanyon.net/downloads" target="_blank">Rate Us <span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></a>
		    	<a data-value="1" target="">Maybe later</a>
		    	<a data-value="1" target="">Already Rated <span class="dashicons dashicons-smiley"></span></a>
		    </p>
		</div> <?php
	}
}
add_action( 'admin_notices', 'wooinstant_admin_notice_warn' );

function wi_notice_ajax_function(){
	if ( isset( $_POST['dismiss'] ) && $_POST['dismiss'] == 1 ) {
		update_option( "dismiss-notice", 1 );
	}
	wp_die( 'done' );
}
add_action('wp_ajax_dismissnotice','wi_notice_ajax_function');

/**
 * Add plugin action links.
 *
 * @since 1.0.0
 * @version 4.0.0
 */
function wi_plugin_action_links( $links ) {
	$plugin_links = array(
		'<a href="admin.php?page=_woinstant">' . esc_html__( 'Settings', 'wooinstant' ) . '</a>',
	);
	return array_merge( $plugin_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wi_plugin_action_links' );