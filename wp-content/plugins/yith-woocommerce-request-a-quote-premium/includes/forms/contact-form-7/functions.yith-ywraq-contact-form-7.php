<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements helper functions for YITH WooCommerce Request A Quote for the integration with Contact Form 7 form
 *
 * @package YITH
 * @since   2.0.0
 * @author  YITH
 */

if ( !function_exists( 'yith_ywraq_email_custom_tags' ) ) {
	/**
	 * @param $text
	 * @param $tag
	 * @param $html
	 *
	 * @return string
	 */
	function yith_ywraq_email_custom_tags( $text, $tag, $html ) {

		if ( $tag == 'yith-request-a-quote-list' ) {
			$text =  yith_ywraq_get_email_template( $html );
		}

		return $text;
	}
}

if ( ! function_exists( 'yith_ywraq_wpcf7_get_contact_forms' ) ) {
	/**
	 * Get list of forms by Contact Form 7 plugin
	 *
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 * @return  array
	 */
	function yith_ywraq_wpcf7_get_contact_forms() {
		if ( ! function_exists( 'wpcf7_contact_form' ) ) {
			return array( '' => __( 'Plugin not activated or not installed', 'yith-woocommerce-request-a-quote' ) );
		}

		$posts = WPCF7_ContactForm::find();

		$array = array();
		foreach ( $posts as $post ) {
			$array[ $post->id() ] = $post->title();
		}

		if ( empty( $array ) ) {
			return array( '' => __( 'No contact form found', 'yith-woocommerce-request-a-quote' ) );
		}

		return $array;
	}
}

if ( ! function_exists( 'ywraq_get_current_contact_form_7' ) ) {
	/**
	 * Get current contact form selected by Contact Form 7 plugin
	 *
	 * @since   1.5.6
	 * @author  Emanuela Castorina
	 * @return mixed|void
	 */
	function ywraq_get_current_contact_form_7() {
		$cform7_id = '';
		global $sitepress;

		if ( get_option( 'ywraq_inquiry_form_type' ) == 'contact-form-7' ) {
			if ( function_exists( 'icl_get_languages' ) && !is_null($sitepress) ) {
				$current_language = $sitepress->get_current_language();
				$cform7_id        = get_option( 'ywraq_inquiry_contact_form_7_id_' . $current_language );
			} else {
				$cform7_id = get_option( 'ywraq_inquiry_contact_form_7_id' );
			}
		}

		return apply_filters( 'ywraq_inquiry_contact_form_7_id', $cform7_id );
	}
}

if ( ! function_exists( 'ywraq_cf7_get_fields_excluded' ) ) {
	/**
	 * Return the list of excluded fields
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_cf7_get_fields_excluded() {
		return apply_filters( 'ywraq_other_fields_exclusion_list', array(
			'_wpcf7',
			'_wpcf7_version',
			'_wpcf7_locale',
			'_wpcf7_unit_tag',
			'_wpcf7_is_ajax_call',
			'_wpcf7_container_post',
			'_wpnonce',
			'your-name',
			'lang',
			'your-email',
			'your-subject',
			'your-message',
			'action',
			'ywraq_order_action',
			'billing-address',
			'billing-phone',
			'billing-vat',
			'billing-postcode',
			'g-recaptcha-response',
		) );
	}
}

if ( ! function_exists( 'ywraq_cf7_supported_woocommerce_fields' ) ) {
	/**
	 * Return the list of supported WooCommerce fields
	 * @return array
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_cf7_supported_woocommerce_fields() {
		$fields = array_map( 'ywraq_string_change', (array) ywraq_get_connect_fields()  );
		return apply_filters( 'ywraq_cf7_supported_woocommerce_fields', $fields );
	}
}

if ( ! function_exists( 'ywraq_string_change' ) ) {
	/**
	 * Callback called by ywraq_cf7_supported_woocommerce_fields
	 * @param $string
	 *
	 * @return mixed
	 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
	 */
	function ywraq_string_change( $string ) {
		return str_replace( '_', '-', $string );
	}
}

