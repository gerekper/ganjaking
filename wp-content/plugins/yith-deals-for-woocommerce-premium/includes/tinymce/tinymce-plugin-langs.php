<?php
// This file is based on wp-includes/js/tinymce/langs/wp-langs.php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '_WP_Editors' ) ) {
	require( ABSPATH . WPINC . '/class-wp-editor.php' );
}

function ywcdls_tinymce_plugin_translation() {
	$strings = array(
		'accept_offer'             => esc_html__( 'Accept Offer button', 'yith-deals-for-woocommerce' ),
		'decline_offer'              => esc_html__( 'Decline Offer button', 'yith-deals-for-woocommerce' ),
	);

	$locale     = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.yith_wcdls_button", ' . json_encode( $strings ) . ");\n";

	return $translated;
}

$strings = ywcdls_tinymce_plugin_translation();