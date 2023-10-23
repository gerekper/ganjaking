<?php
/**
 * PDF Template options.
 *
 * @package YITH\PDFInvoice
 * @since   4.0.0
 * @author  YITH <plugins@yithemes.com>
 */

defined( 'ABSPATH' ) || exit;

$options = array(
	'yith_ywpi_pdf_template_templates' => array(
		'id'   => 'yith_ywpi_pdf_template_templates',
		'name' => 'yith_ywpi_pdf_template_templates',
		'type' => 'html',
		'html' => '<div id="yith_ywpi_pdf_templates"></div>',
	),
	'yith_ywpi_pdf_template_title'     => array(
		'label' => esc_html__( 'Template title', 'yith-woocommerce-pdf-invoice' ),
		'type'  => 'text',
		'name'  => 'name',
		'desc'  => esc_html__( 'Enter a title for this template.', 'yith-woocommerce-pdf-invoice' ),
		'std'   => '',
	),
);

return apply_filters( 'yith_ywpi_pdf_template_options', $options );
