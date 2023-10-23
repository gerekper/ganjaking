<?php
/**
 * General options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$general_options = array(
	'settings' => array(
		'settings-options' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'settings-general'           => array(
					'title'       => __( 'General Options', 'yith-woocommerce-pdf-invoice' ),
					'description' => __( 'Configure the plugin\'s general options.', 'yith-woocommerce-pdf-invoice' ),
				),
				'settings-documents'         => array(
					'title'       => __( 'Documents Format', 'yith-woocommerce-pdf-invoice' ),
					'description' => __( 'Configure the documents\' formatting settings.', 'yith-woocommerce-pdf-invoice' ),
				),
				'settings-documents_storage' => array(
					'title'       => __( 'Documents Storage', 'yith-woocommerce-pdf-invoice' ),
					'description' => __( 'Configure the documents\' storage settings.', 'yith-woocommerce-pdf-invoice' ),
				),
			),
		),
	),
);

return $general_options;
