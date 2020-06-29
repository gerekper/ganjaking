<?php
/**
 * Tinymce plugin langs. This file is based on wp-includes/js/tinymce/langs/wp-langs.php
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
}

if ( ! class_exists( '_WP_Editors' ) ) {
	require ABSPATH . WPINC . '/class-wp-editor.php';
}

function ywcwtl_tinymce_plugin_translation() {
	$strings = array(
		'blogname'      => esc_html__( 'The Blogname', 'yith-woocommerce-waiting-list' ),
		'site_title'    => esc_html__( 'The Site Title', 'yith-woocommerce-waiting-list' ),
		'product_title' => esc_html__( 'The Product Name', 'yith-woocommerce-waiting-list' ),
	);

	if ( isset( $_GET['section'] ) && $_GET['section'] == 'yith_wcwtl_mail_instock' ) {
		$strings['product_link'] = esc_html__( 'The Product Link', 'yith-woocommerce-waiting-list' );
	} elseif ( isset( $_GET['section'] ) && $_GET['section'] == 'yith_wcwtl_mail_subscribe' ) {
		$strings['remove_link'] = esc_html__( 'Remove from list link', 'yith-woocommerce-waiting-list' );
	} elseif ( isset( $_GET['section'] ) && $_GET['section'] == 'yith_wcwtl_mail_subscribe_optin' ) {
		$strings['confirm_link'] = esc_html__( 'Confirm subscription link', 'yith-woocommerce-waiting-list' );
	} elseif ( isset( $_GET['section'] ) && $_GET['section'] == 'yith_wcwtl_mail_admin' ) {
		$strings['user_email'] = esc_html__( 'The user email', 'yith-woocommerce-waiting-list' );
	}

	$locale     = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.tc_button", ' . json_encode( $strings ) . ");\n";

	return $translated;
}

$strings = ywcwtl_tinymce_plugin_translation();