<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Documents options (Invoices and Credit notes multi tab).
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$invoice_array = array(
	'documents_type' => array(
		'documents-type-options' => array(
			'type'       => 'multi_tab',
			'nav-layout' => 'horizontal',
			'sub-tabs'   => array(
				'documents_type-invoices'     => array(
					'title'       => __( 'Invoices', 'yith-woocommerce-pdf-invoice' ),
					'description' => __( 'A table with all the invoices generated in your shop.', 'yith-woocommerce-pdf-invoice' ),
				),
				'documents_type-credit-notes' => array(
					'title'       => __( 'Credit Notes', 'yith-woocommerce-pdf-invoice' ),
					'description' => __( 'A table with all the credit notes generated in your shop.', 'yith-woocommerce-pdf-invoice' ),
				),
			),
		),
	),
);

return apply_filters( 'ywpi_documents_type_options', $invoice_array );
