<?php
/**
 * WooInstant Functions
 *
 * @package WooInstant
 */

defined( 'ABSPATH' ) || exit;

/**
 * Wooinstant Cart Fragments
 */
function wooinstant_cart_fragments( $fragments ) {
	global $woocommerce;

    ob_start();
    wi_cart_count();
    $fragments['span.wi_cart_total'] = ob_get_clean();

    /*ob_start();
    wi_checkout_inner();
    $fragments['div.wi-checkout-inner'] = ob_get_clean();*/

    return $fragments;

}
add_filter( 'woocommerce_add_to_cart_fragments', 'wooinstant_cart_fragments', 10, 1 );

/**
 * Cart Count function
 */
if ( ! function_exists( 'wi_cart_count' ) ) {
	function wi_cart_count() { ?>
		<span class="wi_cart_total">
			<script type='text/javascript'>
			/* <![CDATA[ */
				var wiCartTotal = <?php echo WC()->cart->get_cart_contents_count(); ?>;
			/* ]]> */
			</script>
			<?php if ( WC()->cart->get_cart_contents_count() == 0 ) : ?>
				<style type="text/css" version="1.0">
					.wi-container{
						right: -50% !important;
					}
					.wi-cart-header.hascart {
						left: 0;
					}
					@media (max-width: 767px){
						.wi-container{
							right: -100% !important;
						}
					}
				</style>
			<?php endif; ?>
			<?php echo WC()->cart->get_cart_contents_count(); ?>
		</span> <?php
	}
}

/**
 * Cart Area function
 */
if ( ! function_exists( 'wi_cart_inner' ) ) {
	function wi_cart_inner() {
		include plugin_dir_path( __FILE__ ) . '/templates/cart/cart.php';
	}
}

/**
 * Assign Checkout Template
 */
add_filter( 'page_template', 'wicheckout_page_template' );
function wicheckout_page_template( $page_template ){
    if ( is_page( 'wooinstant-checkout' ) ) {

    	add_filter('show_admin_bar', '__return_false');

        $page_template = dirname( __FILE__ ) . '/templates/wi-checkout.php';
    }
    return $page_template;
}

/**
 * Get ID by page_slug
 */
function get_id_by_slug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
}

/**
 * Create Checkout Page
 */
add_action( 'init', 'wi_create_checkout_page' );
function wi_create_checkout_page(){
    if( get_page_by_title( 'wooinstant-checkout' ) == NULL ) {

        $createWIcheckoutPage = array(
          'post_title'    => 'wooinstant-checkout',
          'post_content'  => "[woocommerce_checkout] <br>Please don't edit this page. This page reserved for WooInstant Checkout Layout",
          'post_status'   => 'publish',
          'post_author'   => 1,
          'post_type'     => 'page',
          'post_name'     => 'wooinstant-checkout'
        );

        // Insert the post into the database
        wp_insert_post( $createWIcheckoutPage );
    }else{
		// Update page for old users
		$updateWIcheckoutPage = array(
		    'ID'           => get_id_by_slug('wooinstant-checkout'),
		    'post_content' => "[woocommerce_checkout] <br>Please don't edit this page. This page reserved for WooInstant Checkout Layout",
		);

		wp_update_post( $updateWIcheckoutPage );
    }
}

add_filter( 'display_post_states', 'wicheckout_add_post_state', 10, 2 );
function wicheckout_add_post_state( $post_states, $post ) {
	if( $post->post_name == 'wooinstant-checkout' ) {
		$post_states[] = 'Wooinstant Checkout Page';
	}
	return $post_states;
}


/**
 * Checkout Area function
 */
if ( ! function_exists( 'wi_checkout_inner' ) ) {
	function wi_checkout_inner() { ?>
		<div class="wi-checkout-inner">
			<button type="button" class="button alt" id="back_to_cart"><?php echo esc_attr('Back to cart','wooinstant'); ?></button>
			<div id="wi-checkout-frame"><span class="wi-loader"><?php wi_svg_icon('spinner'); ?></span><iframe id="wi-iframe" src="" frameborder=0 scrolling=no ></iframe></div>
		</div> <?php
	}
}

/**
 * Add wooinstant-active class to body
 */
function wi_body_classes( $classes ) {
	$classes[] = 'wooinstant-active';
	return $classes;
}
add_filter( 'body_class', 'wi_body_classes' );

/**
 * Calculate Shipping on Update Order Review
 */
function wi_action_woocommerce_checkout_update_order_review( $posted_data ) {
    //WC()->cart->calculate_shipping();
}
add_action( 'woocommerce_checkout_update_order_review', 'wi_action_woocommerce_checkout_update_order_review', 10, 1 );

/**
 *	WooInstant Ajax functions
 */
// variable product quick view ajax actions
add_action('wp_ajax_wi_variable_product_quick_view', 'wi_ajax_quickview_variable_products');
add_action('wp_ajax_nopriv_wi_variable_product_quick_view', 'wi_ajax_quickview_variable_products');

// variable product quick view ajax function
function wi_ajax_quickview_variable_products(){
	global $post, $product, $woocommerce;
	check_ajax_referer( 'wi_ajax_nonce', 'security', false );

	add_action( 'wcqv_product_data', 'woocommerce_template_single_add_to_cart');

	$product_id = $_POST['product_id'];
    $wiqv_loop = new WP_Query(
        array(
            'post_type' => 'product',
            'p' => $product_id,
        )
    );

    ob_start();
	if( $wiqv_loop->have_posts() ) :
		while ( $wiqv_loop->have_posts() ) : $wiqv_loop->the_post(); ?>
			<?php wc_get_template( 'single-product/add-to-cart/variation.php' ); ?>
			<script>
	            jQuery.getScript("<?php echo $woocommerce->plugin_url(); ?>/assets/js/frontend/add-to-cart-variation.min.js");
	 	    </script> <?php
			do_action( 'wcqv_product_data' );
	 	endwhile;
	endif;

	echo ob_get_clean();

	wp_die();
}

// single product ajax add to cart actions
add_action('wp_ajax_wi_single_ajax_add_to_cart', 'wi_single_ajax_add_to_cart');
add_action('wp_ajax_nopriv_wi_single_ajax_add_to_cart', 'wi_single_ajax_add_to_cart');

// single product ajax add to cart actions
function wi_single_ajax_add_to_cart() {

    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = absint($_POST['variation_id']);
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $product_status = get_post_status($product_id);

    if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id) && 'publish' === $product_status) {

        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX :: get_refreshed_fragments();
    } else {

        $data = array(
            'error' => true,
            'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));

        echo wp_send_json($data);
    }

    wp_die();
}

/**
 *	Custom CSS function
 */
if( !function_exists( 'wi_custom_css' ) ){
	function wi_custom_css(){

		global $wiopt;
		$output = '';

		if( $wiopt["wi-zindex"] != '999' ) :
			$output .= '
			.wi-container{
				z-index: '.$wiopt["wi-zindex"].';
			}
			.select2-container{
				z-index: '.$wiopt["wi-zindex"].';
			}
			';
		endif;

		if( $wiopt["wi-container-bg"] != '#f5f5f5' ) :
			$output .= '
			.wi-container{
				background-color: '.$wiopt["wi-container-bg"].';
			}
			';
		endif;

		if( $wiopt["wi-quickview-bg"] != '#f5f5f5' ) :
			$output .= '
			.wi-quick-view{
				background-color: '.$wiopt["wi-quickview-bg"].';
			}
			';
		endif;

		if( $wiopt["wi-header-bg"] != '#f5f5f5' ) :
			$output .= '
			.wi-cart-header{
				background-color: '.$wiopt["wi-header-bg"].';
			}
			';
		endif;

		if( $wiopt["wi-header-text-color"] != '#272727' ) :
			$output .= '
			.wi-cart-header,
			.wi-cart-header:not([href]):not([tabindex]){ /*for bootstrap override*/
				color: '.$wiopt["wi-header-text-color"].';
			}
			';
		endif;
		if( $wiopt["wi-header-text-hovcolor"] != '' ) :
			$output .= '
			.wi-cart-header:hover,
			.wi-cart-header:not([href]):not([tabindex]):hover,
			.wi-cart-header:not([href]):not([tabindex]):focus{
				color: '.$wiopt["wi-header-text-hovcolor"].';
			}
			';
		endif;

		if( $wiopt["wi-drawer-direction"] == '1' ) :
			$output .= '
			.wi-container.drawer-left {
			    left: -50%;
			    right: unset;
			}
			.drawer-left .wi-cart-header{
			    right: 0;
			    left: unset;
			    border-top-left-radius: 0;
			    border-bottom-left-radius: 0;
			    border-top-right-radius: 4px;
			    border-bottom-right-radius: 4px;
			}

			.drawer-left .wi-cart-header.hascart{
			    z-index: 999;
			    right: -70px;
			}

			@media(max-width: 767px){
			    .wi-container.drawer-left{
			        width: 100%;
			        right: unset;
			        left: -100%;
			    }
			    .drawer-left .wi-cart-header.hascart.open {
			        right: 0;
			        z-index: 1;
			    }
			}

			';
		endif;

		$output .= $wiopt["wi-custom-css"]; //Custom css

		wp_add_inline_style( 'wooinstant-stylesheet', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'wi_custom_css', 200 );


if( !function_exists( 'wi_custom_js' ) ){
	function wi_custom_js(){

		global $wiopt;

		$wiCheckoutUrl = esc_url( home_url('/wooinstant-checkout/') );

		$output = "
		(function($) {
    		'use strict';
    		jQuery(document).ready(function() { ";

    			if( $wiopt['wi-active-window'] == '1' ) :

			    	$output .= "$(document).on('click', '#wi-toggler, .added_to_cart', function() {

			    	    $('#wi-cart-area .checkout-button').click();

			    	});";

    			endif;

    			$output .= "$(document).on('wi_checkout_refresh', function() {

    				$('#wi-checkout-frame').addClass('iframe-loading');

            		$('#wi-checkout-frame').find('iframe').attr('src','".$wiCheckoutUrl."');

            		//$('#wi-checkout-frame').load('$wiCheckoutUrl');

			    });";

    			$output .= "
			        $(document).on('click', '#wi-toggler', function(e) {
			            var src = $('#wi-iframe').attr('src');

			            if ( !src ) {
							jQuery(document).trigger( 'wi_checkout_refresh' );
			            }
			        });
    			";


				if( $wiopt['wi-disable-ajax-add-cart'] != 1 ) :
				    $output .= "
			        // Single Product Ajax Cart
			        $(document).on('click', '.single_add_to_cart_button:not(.disabled)', function (e) {
			            e.preventDefault();

			            var thisbutton = $(this),
			                cart_form = thisbutton.closest('form.cart'),
			                id = thisbutton.val(),
			                product_qty = cart_form.find('input[name=quantity]').val() || 1,
			                product_id = cart_form.find('input[name=product_id]').val() || id,
			                variation_id = cart_form.find('input[name=variation_id]').val() || 0;

			            var data = {
			                action: 'wi_single_ajax_add_to_cart',
			                product_id: product_id,
			                product_sku: '',
			                quantity: product_qty,
			                variation_id: variation_id,
			            };

			            $(document.body).trigger('adding_to_cart', [thisbutton, data]);

			            $.ajax({
			                type: 'post',
			                url: wi_ajax_params.wi_ajax_url,
			                data: data,
			                beforeSend: function (response) {
			                    thisbutton.removeClass('added').addClass('loading');
			                },
			                complete: function (response) {
			                    thisbutton.addClass('added').removeClass('loading');
			                },
			                success: function (response) {
			                    if (response.error & response.product_url) {
			                        window.location = response.product_url;
			                        return;
			                    } else {
			                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, thisbutton]);
			                    }
			                },
			            });

			            return false;
			        });
				    ";
	            endif;

		$output .= "
			});
		})(jQuery);" ;


		wp_add_inline_script( 'wi-ajax-script', $output );
	}
}
add_action( 'wp_enqueue_scripts', 'wi_custom_js', 200 );

/**
 * SVG Icons function
 *
 * @return  string
 */
if ( ! function_exists('wi_get_svg_icon') ) {
	function wi_get_svg_icon( $icon = null ){

		if ( ! $icon ) {
			return;
		}

		switch ( $icon ) {
				case 'shopping_cart':
					$output ='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" width="18px" height="18px"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/><path d="M0 0h24v24H0z" fill="none"/></svg>';
							break;

				case 'shopping_basket':
					$output ='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="black" width="18px" height="18px"><path d="M0 0h24v24H0z" fill="none"/><path d="M17.21 9l-4.38-6.56c-.19-.28-.51-.42-.83-.42-.32 0-.64.14-.83.43L6.79 9H2c-.55 0-1 .45-1 1 0 .09.01.18.04.27l2.54 9.27c.23.84 1 1.46 1.92 1.46h13c.92 0 1.69-.62 1.93-1.46l2.54-9.27L23 10c0-.55-.45-1-1-1h-4.79zM9 9l3-4.4L15 9H9zm3 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>';
							break;

				case 'spinner':
					$output ='<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="456.817px" height="456.817px" viewBox="0 0 456.817 456.817" style="enable-background:new 0 0 456.817 456.817;" xml:space="preserve"><g><g><path d="M109.641,324.332c-11.423,0-21.13,3.997-29.125,11.991c-7.992,8.001-11.991,17.706-11.991,29.129c0,11.424,3.996,21.129,11.991,29.13c7.998,7.994,17.705,11.991,29.125,11.991c11.231,0,20.889-3.997,28.98-11.991c8.088-7.991,12.132-17.706,12.132-29.13c0-11.423-4.043-21.121-12.132-29.129C130.529,328.336,120.872,324.332,109.641,324.332z"/><path d="M100.505,237.542c0-12.562-4.471-23.313-13.418-32.267c-8.946-8.946-19.702-13.418-32.264-13.418c-12.563,0-23.317,4.473-32.264,13.418c-8.945,8.947-13.417,19.701-13.417,32.267c0,12.56,4.471,23.309,13.417,32.258c8.947,8.949,19.701,13.422,32.264,13.422c12.562,0,23.318-4.473,32.264-13.422C96.034,260.857,100.505,250.102,100.505,237.542z"/><path d="M365.454,132.48c6.276,0,11.662-2.24,16.129-6.711c4.473-4.475,6.714-9.854,6.714-16.134c0-6.283-2.241-11.658-6.714-16.13c-4.47-4.475-9.853-6.711-16.129-6.711c-6.283,0-11.663,2.24-16.136,6.711c-4.47,4.473-6.707,9.847-6.707,16.13s2.237,11.659,6.707,16.134C353.791,130.244,359.171,132.48,365.454,132.48z"/><path d="M109.644,59.388c-13.897,0-25.745,4.902-35.548,14.703c-9.804,9.801-14.703,21.65-14.703,35.544c0,13.899,4.899,25.743,14.703,35.548c9.806,9.804,21.654,14.705,35.548,14.705s25.743-4.904,35.544-14.705c9.801-9.805,14.703-21.652,14.703-35.548c0-13.894-4.902-25.743-14.703-35.544C135.387,64.29,123.538,59.388,109.644,59.388z"/><path d="M439.684,218.125c-5.328-5.33-11.799-7.992-19.41-7.992c-7.618,0-14.089,2.662-19.417,7.992c-5.325,5.33-7.987,11.803-7.987,19.421c0,7.61,2.662,14.092,7.987,19.41c5.331,5.332,11.799,7.994,19.417,7.994c7.611,0,14.086-2.662,19.41-7.994c5.332-5.324,7.991-11.8,7.991-19.41C447.675,229.932,445.02,223.458,439.684,218.125z"/><path d="M365.454,333.473c-8.761,0-16.279,3.138-22.562,9.421c-6.276,6.276-9.418,13.798-9.418,22.559c0,8.754,3.142,16.276,9.418,22.56c6.283,6.282,13.802,9.417,22.562,9.417c8.754,0,16.272-3.141,22.555-9.417c6.283-6.283,9.422-13.802,9.422-22.56c0-8.761-3.139-16.275-9.422-22.559C381.727,336.61,374.208,333.473,365.454,333.473z"/><path d="M237.547,383.717c-10.088,0-18.702,3.576-25.844,10.715c-7.135,7.139-10.705,15.748-10.705,25.837s3.566,18.699,10.705,25.837c7.142,7.139,15.752,10.712,25.844,10.712c10.089,0,18.699-3.573,25.838-10.712c7.139-7.138,10.708-15.748,10.708-25.837s-3.569-18.698-10.708-25.837S247.636,383.717,237.547,383.717z"/><path d="M237.547,0c-15.225,0-28.174,5.327-38.834,15.986c-10.657,10.66-15.986,23.606-15.986,38.832c0,15.227,5.327,28.167,15.986,38.828c10.66,10.657,23.606,15.987,38.834,15.987c15.232,0,28.172-5.327,38.828-15.987c10.656-10.656,15.985-23.601,15.985-38.828c0-15.225-5.329-28.168-15.985-38.832C265.719,5.33,252.779,0,237.547,0z"/></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>';
							break;

				default:
				$output = '';
				break;
		}

		return $output;
	}
}

/**
 * SVG Icon display
 *
 * @return  void
 */
if ( ! function_exists('wi_svg_icon') ) {
	function wi_svg_icon( $icon = null ){
		echo wi_get_svg_icon( $icon );
	}
}