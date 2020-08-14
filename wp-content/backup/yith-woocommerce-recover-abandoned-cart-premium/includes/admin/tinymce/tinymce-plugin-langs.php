<?php
// This file is based on wp-includes/js/tinymce/langs/wp-langs.php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '_WP_Editors' ) ) {
	require ABSPATH . WPINC . '/class-wp-editor.php';
}

function ywrac_tinymce_plugin_translation() {
	$strings = array(
		'firstname'             => __( 'User First Name', 'yith-woocommerce-recover-abandoned-cart' ),
		'lastname'              => __( 'User Last Name', 'yith-woocommerce-recover-abandoned-cart' ),
		'fullname'              => __( 'Full Name', 'yith-woocommerce-recover-abandoned-cart' ),
		'useremail'             => __( 'User Email', 'yith-woocommerce-recover-abandoned-cart' ),
		'cartcontent'           => __( 'Cart Content', 'yith-woocommerce-recover-abandoned-cart' ),
		'cartlink'              => __( 'Cart Link', 'yith-woocommerce-recover-abandoned-cart' ),
		'cartlink-label'        => __( 'Recover Cart', 'yith-woocommerce-recover-abandoned-cart' ),
		'unsubscribelink'       => __( 'Unsubscribe Link', 'yith-woocommerce-recover-abandoned-cart' ),
		'unsubscribelink-label' => __( 'To unsubscribe from this mail click here', 'yith-woocommerce-recover-abandoned-cart' ),
		'coupon'                => __( 'Coupon', 'yith-woocommerce-recover-abandoned-cart' ),
	);

	$locale     = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.tc_button", ' . json_encode( $strings ) . ");\n";

	return $translated;
}

$strings = ywrac_tinymce_plugin_translation();
