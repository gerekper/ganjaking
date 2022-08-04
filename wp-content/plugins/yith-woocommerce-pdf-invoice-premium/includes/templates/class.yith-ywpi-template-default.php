<?php // phpcs:ignore WordPress.NamingConventions.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWPI_Template' ) ) {

	/**
	 * Class that manage the default template.
	 *
	 * @class   YITH_YWPI_Template
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	abstract class YITH_YWPI_Template_Default {

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 */
		private function __construct() {
		}

		/**
		 * Show the template with customer details
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @author YITH
		 * @since  1.0.0
		 */
		public function show_customer_details( $document ) {

			$content  = ywpi_get_customer_details_template( $document );
			$order_id = yit_get_prop( $document->order, 'id' );

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

			$is_receipt = get_post_meta( $document->order->get_id(), '_billing_invoice_type', true );

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
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_invoice_products_list_template( $document ) {

			$is_receipt = get_post_meta( $document->order->get_id(), '_billing_invoice_type', true );

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

			$is_receipt  = get_post_meta( $document->order->get_id(), '_billing_invoice_type', true );
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
			} else {
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

			return apply_filters( 'ywpi_modify_customer_details_content', $full_content, $content, $document );
		}
	}
}
