<?php
/**
 * Template PDF Templates options.
 *
 * @package YITH\PDFInvoice
 * @since   4.0.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPI_PREMIUM' ) ) {
	exit;
} // Exit if accessed directly.

return array(
	'template-pdf-templates' => array(
		'template-pdf-template_list_table' => array(
			'type'          => 'post_type',
			'post_type'     => YITH_YWPI_PDF_Template_Builder::$pdf_template,
			'wp-list-style' => 'boxed',
		),
	),
);
