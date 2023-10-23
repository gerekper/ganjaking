<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName
/**
 * Class that manage the default template.
 *
 * @package YITH\PDF_Invoice\Classes
 * @since   2.1.0
 * @author  YITH <plugins@yithemes.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWPI_Template' ) ) {
	/**
	 * YITH_YWPI_Template_Default class
	 */
	abstract class YITH_YWPI_Template_Default {

		/**
		 * Show the template with customer details
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @since  1.0.0
		 */
		public function show_customer_details( $document ) {
			$content  = ywpi_get_customer_details_template( $document );
			$order_id = $document->order->get_id();

			wc_get_template(
				'yith-pdf-invoice/customer-details.php',
				array(
					'document' => $document,
					'content'  => $content,
					'order_id' => $order_id,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
		}

		/**
		 * Show the document details.
		 *
		 * @param  mixed $document The document object.
		 * @return void
		 */
		public function show_document_details( $document ) {
			if ( $document instanceof YITH_Shipping ) {
				wc_get_template(
					'yith-pdf-invoice/document-data-packing-slip.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);

				return;
			}

			$is_receipt = $document->order->get_meta( '_billing_invoice_type' );

			if ( $document instanceof YITH_Invoice && 'receipt' !== strval( $is_receipt ) ) {
				wc_get_template(
					'yith-pdf-invoice/document-data-invoice.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);

				return;
			}

			if ( $document instanceof YITH_Invoice && 'receipt' === strval( $is_receipt ) ) {
				wc_get_template(
					'yith-pdf-invoice/document-data-receipt.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);

				return;
			}

			if ( $document instanceof YITH_Pro_Forma ) {
				wc_get_template(
					'yith-pdf-invoice/document-data-proforma.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);

				return;
			}

			if ( $document instanceof YITH_Credit_Note ) {
				wc_get_template(
					'yith-pdf-invoice/document-data-credit-note.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);

				return;
			}
		}

		/**
		 * Show the order content.
		 *
		 * @param  mixed $document The document object.
		 * @return void
		 */
		public function show_order_content( $document ) {
			wc_get_template(
				'yith-pdf-invoice/order-content.php',
				array(
					'document' => $document,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
		}

		/**
		 * Show product list for current order on invoice template
		 *
		 * @param YITH_Document $document the document to be build.
		 *
		 * @since  1.0.0
		 */
		public function show_invoice_products_list_template( $document ) {
			$is_receipt = $document->order->get_meta( '_billing_invoice_type' );

			if ( $document instanceof YITH_Credit_Note ) {
				wc_get_template(
					'yith-pdf-invoice/credit-note-details-with-products.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);
			} elseif ( $document instanceof YITH_Invoice && 'receipt' === $is_receipt ) {
				wc_get_template(
					'yith-pdf-invoice/receipt-details.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);
			} else {
				wc_get_template(
					'yith-pdf-invoice/invoice-details.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);
			}
		}

		/**
		 * Show the template of invoice totals
		 *
		 * @param YITH_Document $document The document object.
		 */
		public function show_totals( $document ) {
			$is_receipt = $document->order->get_meta( '_billing_invoice_type' );

			/**
			 * APPLY_FILTERS: ywpi_show_totals_in_documents
			 *
			 * Filter the condition to show the totals in the documents.
			 *
			 * @param bool True to display the totals, false to not. Default: true.
			 * @param object $document the document object.
			 *
			 * @return bool
			 */
			$show_totals = apply_filters( 'ywpi_show_totals_in_documents', true, $document );

			if ( $document instanceof YITH_Invoice && 'receipt' === strval( $is_receipt ) ) {
				if ( $show_totals ) {
					wc_get_template(
						'yith-pdf-invoice/receipt-totals.php',
						array(
							'document' => $document,
						),
						'',
						YITH_YWPI_TEMPLATE_DIR
					);
				}
			} elseif ( $document instanceof YITH_Credit_Note ) {
				if ( $show_totals ) {
					wc_get_template(
						'yith-pdf-invoice/credit-note-totals.php',
						array(
							'document' => $document,
						),
						'',
						YITH_YWPI_TEMPLATE_DIR
					);
				}
			} elseif ( $show_totals ) {
				wc_get_template(
					'yith-pdf-invoice/invoice-totals.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR
				);
			}
		}

		/**
		 * Show the document notes
		 *
		 * @param YITH_Document $document The document object.
		 */
		public function show_notes( $document ) {
			wc_get_template(
				'yith-pdf-invoice/document-notes.php',
				array(
					'document' => $document,

				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
		}

		/**
		 * Modify the customer details content to add 'Customer info:' as title.
		 *
		 * @param string        $content The content of customer details.
		 * @param YITH_Document $document The document object.
		 *
		 * @return mixed
		 */
		public function modify_customer_details_content( $content, $document ) {
			$full_content = '';

			if ( ! empty( $content ) ) {
				if ( $document instanceof YITH_Shipping ) {
					$full_content = '<div class="customer-details-title">' . esc_html__( 'Ship to:', 'yith-woocommerce-pdf-invoice' ) . '</div>' . $content;
				} else {
					$full_content = '<div class="customer-details-title">' . esc_html__( 'Customer info:', 'yith-woocommerce-pdf-invoice' ) . '</div>' . $content;
				}
			}

			/**
			 * APPLY_FILTERS: ywpi_modify_customer_details_content
			 *
			 * Filter the customer details content.
			 *
			 * @param string $full_content The formatted content of the customer details.
			 * @param string $content The content of the customer details.
			 * @param object $document the document object.
			 *
			 * @return string
			 */
			return apply_filters( 'ywpi_modify_customer_details_content', $full_content, $content, $document );
		}
	}
}
