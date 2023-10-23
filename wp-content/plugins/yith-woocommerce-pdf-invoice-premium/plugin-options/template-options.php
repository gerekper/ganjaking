<?php
/**
 * Template options.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$template_options = array(
	'template' => array(
		'template-options' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'template-content'       => array(
					'title'       => _x( 'Settings', 'subtab name', 'yith-woocommerce-pdf-invoice' ),
					'description' => __( 'Configure the PDF template settings.', 'yith-woocommerce-pdf-invoice' ),
				),
				'template-style'         => array(
					'title'       => __( 'Style', 'yith-woocommerce-pdf-invoice' ),
					'description' => __( 'Configure the PDF template style settings.', 'yith-woocommerce-pdf-invoice' ),
				),
				'template-pdf-templates' => array(
					'title'       => esc_html_x( 'Templates', 'Admin title of tab', 'yith-woocommerce-pdf-invoice' ),
					'description' => __( 'Create and manage the templates that your customers can download as PDFs.', 'yith-woocommerce-pdf-invoice' ),
				),
			),
		),
	),
);

return apply_filters( 'ywpi_template_options', $template_options );
