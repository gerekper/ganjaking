<?php

$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . ':{
	wcopc:{
		one_page_checkout: "' . esc_js( __( 'One Page Checkout', 'wcopc' ) ) . '",
		one_page_checkout_shortcode: "' . esc_js( apply_filters( 'woocommerce_one_page_checkout_shortcode_tag', 'woocommerce_one_page_checkout' ) ) . '"
	}
}})';