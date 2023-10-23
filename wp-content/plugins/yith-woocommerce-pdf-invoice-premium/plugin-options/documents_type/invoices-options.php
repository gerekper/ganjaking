<?php
/**
 * Invoices custom tab.
 *
 * @package YITH\PDFInvoice
 * @since   2.1.0
 * @author  YITH <plugins@yithemes.com>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWPI_PREMIUM' ) ) {
	exit;
}

return array(
	'documents_type-invoices' => array(
		'list-table' => array(
			'type'   => 'custom_tab',
			'action' => 'yith_wcpi_invoice_list_table',
		),
	),
);
