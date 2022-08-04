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
	class YITH_YWPI_Template extends YITH_YWPI_Template_Default {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWPI_Template
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
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
			add_action(
				'yith_ywpi_template_document_header',
				array(
					$this,
					'show_document_title',
				)
			);

			/**
			 * Show the company data template
			 */
			add_action(
				'yith_ywpi_template_company_data',
				array(
					$this,
					'show_company_data',
				)
			);

			/**
			 * Show the document data
			 */
			add_action(
				'yith_ywpi_template_document_data',
				array(
					$this,
					'show_document_data',
				)
			);

			/**
			 * Show the order content
			 */
			add_action(
				'yith_ywpi_template_order_content',
				array(
					$this,
					'show_order_content',
				)
			);

			/**
			 * Show notes
			 */
			add_action(
				'yith_ywpi_template_notes',
				array(
					$this,
					'show_notes',
				)
			);

			/**
			 * Show the footer
			 */
			add_action(
				'yith_ywpi_template_footer',
				array(
					$this,
					'show_footer',
				)
			);

			/**
			 * Show the customer details
			 */
			add_action(
				'yith_ywpi_template_customer_details',
				array(
					$this,
					'show_customer_details',
				)
			);

			add_filter(
				'yith_ywpi_customer_details_content',
				array(
					$this,
					'modify_customer_details_content',
				),
				10,
				2
			);

			/**
			 * Show the document details
			 */
			add_action(
				'yith_ywpi_template_document_details',
				array(
					$this,
					'show_document_details',
				)
			);

			add_action(
				'yith_ywpi_invoice_template_totals',
				array(
					$this,
					'show_totals',
				)
			);

			add_action(
				'yith_ywpi_invoice_template_products_list',
				array(
					$this,
					'show_invoice_products_list_template',
				)
			);

			/** Add shipping  */

		}

		/**
		 * Show the document title
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @author YITH
		 * @since  1.0.0
		 */
		public function show_document_title( $document ) {
			$document_title = YITH_PDF_Invoice()->get_document_title( $document );

			wc_get_template(
				'yith-pdf-invoice/document-title.php',
				array(
					'document'       => $document,
					'document_title' => apply_filters( 'yith_ywpi_document_template_title', $document_title, $document ),
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
		}

		/**
		 * Show company data on documents
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @author YITH
		 * @since  1.0.0
		 */
		public function show_company_data( $document ) {

			$company_name      = 'yes' === ywpi_get_option( 'ywpi_show_company_name', $document, 'yes' ) ? ywpi_get_option( 'ywpi_company_name', $document, 'yes' ) : null;
			$company_details   = 'yes' === ywpi_get_option( 'ywpi_show_company_details', $document, 'yes' ) ? nl2br( ywpi_get_option( 'ywpi_company_details', $document, 'yes' ) ) : null;
			$company_logo_path = 'yes' === ywpi_get_option( 'ywpi_show_company_logo', $document, 'yes' ) ? ywpi_get_option( 'ywpi_company_logo', $document, 'yes' ) : null;

			if ( apply_filters( 'ywpi_show_company_data_custom_condition', false, $document ) ) {
				return;
			}

			wc_get_template(
				'yith-pdf-invoice/company-details.php',
				array(
					'document'          => $document,
					'company_name'      => $company_name,
					'company_details'   => apply_filters( 'yith_ywpi_template_company_details', $company_details, $document ),
					'company_logo_path' => apply_filters( 'yith_ywpi_template_company_logo_path', $company_logo_path, $document ),

				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
		}

		/**
		 * Show the template with customer details
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @author YITH
		 * @since  1.0.0
		 */
		public function show_document_data( $document ) {

			wc_get_template(
				'yith-pdf-invoice/document-data.php',
				array(
					'document' => $document,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
		}

		/**
		 * Show the footer
		 *
		 * @param YITH_Document $document the document to be build.
		 *
		 * @author YITH
		 * @since  1.0.0
		 */
		public function show_footer( $document ) {
			wc_get_template(
				'yith-pdf-invoice/document-footer.php',
				array(
					'document' => $document,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
		}
	}
}
