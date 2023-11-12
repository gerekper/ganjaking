<?php
	function pafe_woocommerce_checkout_shortcode($args, $content) {
		ob_start();
			echo '<div data-pafe-woocommerce-checkout>';
				global $post;
				echo do_shortcode('[woocommerce_checkout]');
			echo '</div>';
		return ob_get_clean();
	}
	add_shortcode( 'pafe_woocommerce_checkout', 'pafe_woocommerce_checkout_shortcode' );