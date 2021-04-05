<?php
/**
 * Storefront Powerpack template functions.
 *
 * @package Storefront_Powerpack
 */

if ( ! function_exists( 'sp_checkout_form_wrapper_div' ) ) {
	/**
	 * Used to wrap the checkout form in a div and include navigation links
	 *
	 * @return void
	 */
	function sp_checkout_form_wrapper_div() {
		echo '<div class="checkout-slides">'; ?>
			<ul class="sp-checkout-control-nav">
				<li><a href="#"><?php esc_html_e( 'Your Details', 'storefront-powerpack' ); ?></a></li>
				<li><a href="#"><?php esc_html_e( 'Your Order', 'storefront-powerpack' ); ?></a></li>
			</ul>
			<?php
	}
}

if ( ! function_exists( 'sp_close_div' ) ) {
	/**
	 * Close a div
	 *
	 * @return void
	 */
	function sp_close_div() {
		echo '</div>';
	}
}

if ( ! function_exists( 'sp_checkout_form_wrapper' ) ) {
	/**
	 * Used to wrap the checkout form in a ul
	 *
	 * @return void
	 */
	function sp_checkout_form_wrapper() {
		echo '<ul class="sp-two-step-checkout">';
	}
}

if ( ! function_exists( 'sp_close_ul' ) ) {
	/**
	 * Close the ul that wraps the checkout form
	 *
	 * @return void
	 */
	function sp_close_ul() {
		echo '</ul>';
	}
}

if ( ! function_exists( 'sp_address_wrapper' ) ) {
	/**
	 * Used to wrap the address fields on the ckecout in an li
	 *
	 * @return void
	 */
	function sp_address_wrapper() {
		echo '<li class="sp-addresses">';
	}
}

if ( ! function_exists( 'sp_close_li' ) ) {
	/**
	 * Close an li
	 *
	 * @return void
	 */
	function sp_close_li() {
		echo '</li>';
	}
}

if ( ! function_exists( 'sp_order_review_wrap' ) ) {
	/**
	 * Used to wrap the order review in an li
	 *
	 * @return void
	 */
	function sp_order_review_wrap() {
		echo '<li class="order-review">';
		echo '<h3 id="order_review_heading">' . esc_html__( 'Your order', 'storefront-powerpack' ) . '</h3>';
	}
}

if ( ! function_exists( 'sp_fire_flexslider' ) ) {
	/**
	 * Fire FlexSlider
	 *
	 * @return void
	 */
	function sp_fire_flexslider() {
		?>
		<script>
		jQuery( window ).load(function() {
			jQuery( '.checkout-slides' ).flexslider({
				selector:       '.sp-two-step-checkout > li',
				slideshow:      false,
				prevText:       '<?php esc_html_e( 'Back to my details', 'storefront-powerpack' ); ?>',
				nextText:       '<?php esc_html_e( 'Proceed to payment', 'storefront-powerpack' ); ?>',
				animationLoop:  false,
				manualControls: '.sp-checkout-control-nav li a',
				keyboard:       false,
			});

			jQuery( '.flex-direction-nav a' ).removeAttr( 'href' ).addClass( 'button' );

			jQuery( '.flex-direction-nav a' ).click(function() {
				jQuery( 'html, body' ).animate( {
					scrollTop: jQuery( 'form.checkout' ).offset().top
				}, 400 );
			});

			jQuery( '.flex-direction-nav a' ).on( 'touchstart', function() {
				jQuery( 'body' ).animate( {
					scrollTop: jQuery( 'form.checkout' ).offset().top
				}, 400 );
			});
		});
		</script>
		<?php
	}
}