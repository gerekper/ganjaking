<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_YWPI_Template' ) ) {

	/**
	 *
	 * @class   YITH_YWPI_Template
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_YWPI_Template {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null ( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

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
			/**
			 * Show the document title template
			 */
			add_action ( 'yith_ywpi_template_document_header',
				array(
					$this,
					'show_document_title'
				) );

			/**
			 * Show the company data template
			 */
			add_action ( 'yith_ywpi_template_company_data',
				array(
					$this,
					'show_company_data'
				) );

			/**
			 * Show the document data
			 */
			add_action ( 'yith_ywpi_template_document_data',
				array(
					$this,
					'show_document_data'
				) );

			/**
			 * Show the order content
			 */
			add_action ( 'yith_ywpi_template_order_content',
				array(
					$this,
					'show_order_content'
				) );

			/**
			 * Show notes
			 */
			add_action ( 'yith_ywpi_template_notes',
				array(
					$this,
					'show_notes'
				) );

			/**
			 * Show the footer
			 */
			add_action ( 'yith_ywpi_template_footer',
				array(
					$this,
					'show_footer'
				) );

			/**
			 * Show the customer details
			 */
			add_action ( 'yith_ywpi_template_customer_details',
				array(
					$this,
					'show_customer_details'
				) );

			/**
			 * Show the document details
			 */
			add_action ( 'yith_ywpi_template_document_details',
				array(
					$this,
					'show_document_details'
				) );

			add_action ( 'yith_ywpi_invoice_template_totals', array(
				$this,
				'show_totals',
			) );

			add_action ( 'yith_ywpi_invoice_template_products_list', array(
				$this,
				'show_invoice_products_list_template',
			) );

			/**
			 * Add CSS to the <head> tag
			 */
			add_action ( 'yith_ywpi_template_head', array(
				$this,
				'add_document_stylesheet'
			) );

			add_filter ( 'yith_ywpi_add_body_class', array(
				$this,
				'add_body_class'
			) );

			/**
			 * Add a Notice on top of document generated in Preview Mode
			 */
			add_filter ( 'yith_ywpi_document_template_title',
				array(
					$this,
					'print_notice_preview_mode'
				), 20 );
		}


		/**
		 * Add an header to the documents generated that explain that the current document is intended only as a preview
		 *
		 * @param string $text
		 *
		 * @return string
		 */
		public function print_notice_preview_mode( $text ) {
			if ( ! YITH_PDF_Invoice ()->preview_mode ) {
				return $text;
			}

			return sprintf ( "<span class=\"document-type\">%s</span><br>%s",
				esc_html__( 'THIS IS ONLY A PREVIEW DOCUMENT', 'yith-woocommerce-pdf-invoice' ),
				$text );
		}

		public function add_body_class( $class ) {
			$class .= ' ' . get_option ( 'ywpi_pdf_module', 'template1' );

			return trim ( $class );
		}

		/**
		 * Show the document title
		 *
		 * @param YITH_Document $document
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_document_title( $document ) {
			$document_title = YITH_PDF_Invoice ()->get_document_title ( $document );

			wc_get_template ( 'yith-pdf-invoice/document-title.php',
				array(
					'document'       => $document,
					'document_title' => apply_filters ( 'yith_ywpi_document_template_title', $document_title, $document )
				),
				'',
				YITH_YWPI_TEMPLATE_DIR );
		}

		/**
		 * Show company data on documents
		 *
		 * @param YITH_Document $document
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_company_data( $document ) {

			$company_name      = 'yes' === ywpi_get_option ( 'ywpi_show_company_name', $document ) ? ywpi_get_option ( 'ywpi_company_name', $document ) : null;
			$company_details   = 'yes' === ywpi_get_option ( 'ywpi_show_company_details', $document ) ? nl2br ( ywpi_get_option ( 'ywpi_company_details', $document ) ) : null;
			$company_logo_path = 'yes' === ywpi_get_option ( 'ywpi_show_company_logo', $document ) ? ywpi_get_option ( 'ywpi_company_logo', $document ) : null;

			wc_get_template ( 'yith-pdf-invoice/company-details.php',
				array(
					'document'          => $document,
					'company_name'      => $company_name,
					'company_details'   => apply_filters ( 'yith_ywpi_template_company_details', $company_details, $document ),
					'company_logo_path' => apply_filters ( 'yith_ywpi_template_company_logo_path', $company_logo_path, $document ),

				),
				'',
				YITH_YWPI_TEMPLATE_DIR );
		}

		/**
		 * Show the template with customer details
		 *
		 * @param YITH_Document $document
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_customer_details( $document ) {

			$order_id = yit_get_prop ( $document->order, 'id' );
			$html_allowed_tag = apply_filters('yith_ywpi_allowed_tag',array( "br" => array() ));


			if ( ywpi_document_behave_as_invoice ( $document ) ) {
				if ( $document instanceof YITH_Credit_Note ) {
					//  Use the parent order to extract customer information
					$current_order_id = yit_get_prop ( $document->order, 'id' );
					$order_id         = get_post_field ( 'post_parent', $current_order_id );
				}

				$content = wp_kses ( YITH_PDF_Invoice ()->get_customer_billing_details ( $order_id ), $html_allowed_tag );
			} else {
				$content = wp_kses ( YITH_PDF_Invoice ()->get_customer_shipping_details ( $order_id ), $html_allowed_tag );
			}


			wc_get_template ( 'yith-pdf-invoice/customer-details.php',
				array(
					'document' 	=> $document,
					'content'  	=> $content,
					'order_id'	=> $order_id
				),
				'',
				YITH_YWPI_TEMPLATE_DIR );
		}

		public function show_document_details( $document ) {
			if ( $document instanceof YITH_Shipping ) {
				wc_get_template ( 'yith-pdf-invoice/document-data-packing-slip.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );

				return;
			}

			$is_receipt = get_post_meta( $document->order->get_id(), '_billing_invoice_type' , true );

			if ( $document instanceof YITH_Invoice && $is_receipt != 'receipt' ) {
				wc_get_template ( 'yith-pdf-invoice/document-data-invoice.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );

				return;
			}

			if ( $document instanceof YITH_Invoice && $is_receipt == 'receipt' ) {
				wc_get_template ( 'yith-pdf-invoice/document-data-receipt.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );

				return;
			}

			if ( $document instanceof YITH_Pro_Forma ) {
				wc_get_template ( 'yith-pdf-invoice/document-data-proforma.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );

				return;
			}


			if ( $document instanceof YITH_Credit_Note ) {
				wc_get_template ( 'yith-pdf-invoice/document-data-credit-note.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );

				return;
			}
		}

		/**
		 * Show the template with customer details
		 *
		 * @param YITH_Document $document
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_document_data( $document ) {


			wc_get_template ( 'yith-pdf-invoice/document-data.php',
				array(
					'document' => $document,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR );
		}

		public function show_order_content( $document ) {
			wc_get_template ( 'yith-pdf-invoice/order-content.php',
				array(
					'document' => $document,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR );
		}

		/**
		 * Show the document notes
		 *
		 * @param YITH_Document $document
		 */
		public function show_notes( $document ) {
            wc_get_template ( 'yith-pdf-invoice/document-notes.php',
				array(
					'document' => $document,

                ),
				'',
				YITH_YWPI_TEMPLATE_DIR );
		}

		/**
		 * Show the template of invoice totals
		 *
		 * @param YITH_Document $document
		 */
		public function show_totals( $document ) {

			if ( $document instanceof YITH_Credit_Note ) {
				//  There aren't any order totals section  to be shown on Credit note
				return;
			}

			$is_receipt = get_post_meta( $document->order->get_id(), '_billing_invoice_type' , true );

			if ( $document instanceof YITH_Invoice && $is_receipt == 'receipt' ){
				wc_get_template ( 'yith-pdf-invoice/receipt-totals.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );
			}
			else{
				wc_get_template ( 'yith-pdf-invoice/invoice-totals.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );
			}

		}

		/**
		 * Show product list for current order on invoice template
		 *
		 * @param YITH_Document $document the document to be build
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_invoice_products_list_template( $document ) {

			$is_receipt = get_post_meta( $document->order->get_id(), '_billing_invoice_type' , true );

			if ( $document instanceof YITH_Credit_Note ) {

			    $show_products = get_option( 'ywpi_credit_note_template_style_checkbox' );

			    if ( $show_products == 'yes' ){
                    wc_get_template ( 'yith-pdf-invoice/credit-note-details-with-products.php',
                        array(
                            'document' => $document,
                        ),
                        '',
                        YITH_YWPI_TEMPLATE_DIR );
                }
                else{
                    wc_get_template ( 'yith-pdf-invoice/credit-note-details.php',
                        array(
                            'document' => $document,
                        ),
                        '',
                        YITH_YWPI_TEMPLATE_DIR );
                }


			}
			elseif ( $document instanceof YITH_Invoice && $is_receipt == 'receipt'){
				wc_get_template ( 'yith-pdf-invoice/receipt-details.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );
			}
			else {
				wc_get_template ( 'yith-pdf-invoice/invoice-details.php',
					array(
						'document' => $document,
					),
					'',
					YITH_YWPI_TEMPLATE_DIR );
			}
		}

		/**
		 * Show the footer
		 *
		 * @param YITH_Document $document the document to be build
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_footer( $document ) {
			wc_get_template ( 'yith-pdf-invoice/document-footer.php',
				array(
					'document' => $document,
                ),
				'',
				YITH_YWPI_TEMPLATE_DIR );
		}

		/**
		 * Append the stylesheet to the document being created
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_document_stylesheet() {

			return;
			if ( 'template1' === get_option ( 'ywpi_pdf_module', 'template1' ) ) {
				ob_start ();

				wc_get_template ( 'yith-pdf-invoice/invoice-style.css',
					null,
					'',
					YITH_YWPI_TEMPLATE_DIR );

				$content = ob_get_contents ();
				ob_end_clean ();

				if ( $content ) {
					?>
					<style type="text/css">
						<?php echo $content; ?>
					</style>
					<?php
				}
			}
		}
	}
}

YITH_YWPI_Template::get_instance ();
