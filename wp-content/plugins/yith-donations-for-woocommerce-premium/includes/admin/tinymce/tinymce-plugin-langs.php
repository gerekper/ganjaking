<?php
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( '_WP_Editors' ) )
	require( ABSPATH . WPINC . '/class-wp-editor.php' );

function ywcds_tinymce_plugin_translation() {
	$strings = array(
		'multi_amount_label'      => __( 'Donation pre-set amounts', 'yith-donations-for-woocommerce' ) ,
		'multi_amount_placeholder' => __( 'Enter values separate by |', 'yith-donations-for-woocommerce' ),
		'multi_amount_style'       =>  __( 'Style', 'yith-donations-for-woocommece' ),
		'multi_amount_options' => array(
				array(
					'text' => __( 'Radio Button', 'yith-donations-for-woocommerce' ),
					'value' => 'radio'
				),
				array(
					'text' => __( 'Label', 'yith-donations-for-woocommerce' ),
					'value' => 'label'
				)
		),
		'show_donation_reference'       => __( 'Show an extra field in the donation form','yith-donations-for-woocommerce' ),
		'extra_desc_label'      => __( 'Extra field label', 'yith-donations-for-woocommerce' ),

	);

	$locale = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $locale . '.ywcds_shortcode", ' . json_encode( $strings ) . ");\n";


	return $translated;
}

$strings = ywcds_tinymce_plugin_translation();