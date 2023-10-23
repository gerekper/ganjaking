<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName
/**
 * Class that manage the modern template.
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
	 * YITH_YWPI_Modern_Template class
	 */
	class YITH_YWPI_Modern_Template extends YITH_YWPI_Template_Default {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWPI_Modern_Template
		 * @since 2.1.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 2.1.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * The template path where to find the templates.
		 *
		 * @var string
		 */
		private $template_path = 'yith-pdf-invoice/modern_template/';

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @access public
		 */
		private function __construct() {
			/**
			 * Show the document title template
			 */
			add_action( 'yith_ywpi_template_document_header', array( $this, 'show_document_header' ) );

			/**
			 * Show the document details
			 */
			add_action( 'yith_ywpi_template_document_details', array( $this, 'show_document_details' ) );

			/**
			 * Show the document data
			 */
			add_action( 'yith_ywpi_template_document_data', array( $this, 'show_document_data' ) );

			/**
			 * Show the customer details
			 */
			add_action( 'yith_ywpi_template_customer_details', array( $this, 'show_customer_details' ) );

			add_filter( 'yith_ywpi_customer_details_content', array( $this, 'modify_customer_details_content' ), 10, 2 );

			/**
			 * Show the order content
			 */
			add_action( 'yith_ywpi_template_order_content', array( $this, 'show_order_content' ) );

			/**
			 * Show product list (table)
			 */
			add_action( 'yith_ywpi_invoice_template_products_list', array( $this, 'show_invoice_products_list_template' ) );

			/**
			 * Show totals
			 */
			add_action( 'yith_ywpi_invoice_template_totals', array( $this, 'show_totals' ) );

			/**
			 * Show notes
			 */
			add_action( 'yith_ywpi_template_notes', array( $this, 'show_notes' ) );

			add_action( 'yith_ywpi_before_print_document_notes', array( $this, 'show_notes_separator' ) );
		}

		/**
		 * Show the document title
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @since  1.0.0
		 */
		public function show_document_header( $document ) {
			$company_logo_path = 'yes' === ywpi_get_option( 'ywpi_show_company_logo', $document ) ? ywpi_get_option( 'ywpi_company_logo', $document ) : null;

			wc_get_template(
				$this->template_path . 'document-header.php',
				array(
					'document'          => $document,
					'company_logo_path' => apply_filters( 'yith_ywpi_template_company_logo_path', $company_logo_path, $document ),
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
		}

		/**
		 * Show the template with document data details
		 *
		 * @param YITH_Document $document The document object.
		 *
		 * @since  1.0.0
		 */
		public function show_document_data( $document ) {
			wc_get_template(
				$this->template_path . 'document-data.php',
				array(
					'document' => $document,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);
		}

		/**
		 * Print the notes separator.
		 */
		public function show_notes_separator() {
			$html = '<hr class="ywpi_notes_separator">';

			echo wp_kses_post( $html );
		}
	}
}
