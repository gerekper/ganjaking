<?php
/**
 * Class to manage the PDF template Object
 *
 * @class   YITH_YWPI_PDF_Template
 * @since   4.0.0
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDF_Invoice\PDF_Builder
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_YWPI_Cpt_Object', false ) ) {
	include_once YITH_YWPI_INC_DIR . 'pdf-builder/abstracts/abstract-yith-ywpi-cpt-object.php';
}

if ( ! class_exists( 'YITH_YWPI_PDF_Template' ) ) {
	/**
	 * Class YITH_YWPI_PDF_Template
	 */
	class YITH_YWPI_PDF_Template extends YITH_YWPI_Cpt_Object {

		/**
		 * Array of data
		 *
		 * @var array
		 */
		protected $data = array(
			'name'              => '',
			'default'           => 0,
			'template_parent'   => 'default',
			'custom_background' => '',
			'footer_content'    => '',
		);

		/**
		 * Post type name
		 *
		 * @var string
		 */
		protected $post_type = '';

		/**
		 * Main constructor function
		 *
		 * @param   mixed $obj  Object.
		 */
		public function __construct( $obj ) {
			$this->post_type = YITH_YWPI_PDF_Template_Builder::$pdf_template;

			parent::__construct( $obj );
		}

		/**
		 * Set the name of the template
		 *
		 * @param   string $value  The value to set.
		 */
		public function set_name( $value ) {
			$this->set_prop( 'name', $value );
		}

		/**
		 * Set if the template is the default template
		 *
		 * @param   int $value  The value to set.
		 */
		public function set_default( $value ) {
			$this->set_prop( 'default', $value );
		}

		/**
		 * Set the template parent id
		 *
		 * @param   string $value  The value to set.
		 */
		public function set_template_parent( $value ) {
			$this->set_prop( 'template_parent', $value );
		}

		/**
		 * Return if the template is the default template.
		 *
		 * @return bool
		 */
		public function is_default() {
			return (bool) $this->get_default();
		}

		/**
		 * Return the name of template
		 *
		 * @param   string $context  What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			return $this->get_prop( 'name', $context );
		}

		/**
		 * Return yes the if the template is the default template
		 *
		 * @param   string $context  What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_default( $context = 'view' ) {
			return $this->get_prop( 'default', $context );
		}

		/**
		 * Return the template parent id
		 *
		 * @param   string $context  What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_template_parent( $context = 'view' ) {
			return $this->get_prop( 'template_parent', $context );
		}

		/**
		 * Return the content of the template
		 *
		 * @param YITH_Document|int $document          The document to show or the path of the file to be shown.
		 * @param int               $order_id          Order id.
		 * @param array             $preview_products  Preview products.
		 *
		 * @return string
		 */
		public function get_content( $document, $order_id, $preview_products = array() ) {
			$content = get_the_content( null, false, $this->get_id() );
			$output  = yith_ywpi_template_editor()->render_template(
				$this,
				$content,
				$document,
				$order_id,
				$preview_products
			);

			return $output;
		}

		/**
		 * Return the footer content
		 *
		 * @param   string $context  What the value is for. Valid values are view and edit.
		 *
		 * @return mixed|null
		 */
		public function get_footer_content( $context = 'view' ) {
			return $this->get_prop( 'footer_content', $context );
		}

		/**
		 * Return the footer content
		 *
		 * @param   string $context  What the value is for. Valid values are view and edit.
		 *
		 * @return mixed|null
		 */
		public function get_custom_background( $context = 'view' ) {
			return $this->get_prop( 'custom_background', $context );
		}

		/**
		 * Generate pdf
		 *
		 * @param mixed $document YITH_Document.
		 *
		 * @return false|resource
		 */
		public function generate_pdf( $document ) {
			$mpdf                       = YITH_YWPI_PDF_Template_Builder::get_mpdf( $document );
			$mpdf->shrink_tables_to_fit = 1;

			ob_start();
			$content = $this->get_content( $document, $document->order->get_id() );
			$footer  = $this->get_footer_content();

			wc_get_template(
				'yith-pdf-invoice/pdf-builder/invoice-template.php',
				array(
					'content'  => $content,
					'footer'   => apply_filters( 'yith_ywpi_custom_pdf_template_footer', $footer, $document ),
					'document' => $document,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR
			);

			$html = ob_get_contents();
			ob_end_clean();

			$mpdf->WriteHTML( $html );
			$pdf = $mpdf->Output( 'document', 'S' );

			return $pdf;
		}

		/**
		 * Return the pdf preview
		 *
		 * @param   array $preview_products  Preview products.
		 */
		public function get_preview( $preview_products = array() ) {
			$mpdf = YITH_YWPI_PDF_Template_Builder::get_mpdf();

			$content = $this->get_content( 0, 0, $preview_products );
			$footer  = $this->get_footer_content();

			ob_start();
			wc_get_template(
				'invoice-template.php',
				array(
					'content' => $content,
					'footer'  => $footer,
				),
				'',
				YITH_YWPI_TEMPLATE_DIR . 'yith-pdf-invoice/pdf-builder/'
			);

			$html = ob_get_contents();
			ob_end_clean();

			$mpdf->WriteHTML( $html );

			$pdf       = $mpdf->Output( 'document', 'S' );
			$file_path = $this->get_pdf_preview_file_path();

			$file = fopen( $file_path, 'a' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen

			fwrite( $file, $pdf ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
			fclose( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

			$pdf_url = $this->get_pdf_preview_file_url();

			wp_send_json(
				array( 'pdf' => $pdf_url )
			);
		}

		/**
		 * Get PDF File Path
		 *
		 * @return string
		 */
		public function get_pdf_preview_file_path() {
			if ( ! file_exists( YITH_YWPI_DOCUMENT_SAVE_DIR . 'template_pdf_preview' ) ) {
				wp_mkdir_p( YITH_YWPI_DOCUMENT_SAVE_DIR . 'template_pdf_preview' );
			}

			$file = YITH_YWPI_DOCUMENT_SAVE_DIR . 'template_pdf_preview/' . $this->get_id() . '.pdf';

			// delete the document if exists.
			if ( file_exists( $file ) ) {
				@unlink( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
			}

			return $file;
		}

		/**
		 * Get Pdf File Path
		 *
		 * @return string
		 */
		public function get_pdf_preview_file_url() {
			$url = YITH_YWPI_SAVE_INVOICE_URL . '/template_pdf_preview/' . $this->get_id() . '.pdf';

			return $url;
		}
	}
}

if ( ! function_exists( 'yith_ywpi_get_pdf_template' ) ) {
	/**
	 * Return the pdf template object
	 *
	 * @param mixed  $pdf_template PDF Template.
	 * @param string $lang         Language.
	 *
	 * @return YITH_YWPI_PDF_Template
	 */
	function yith_ywpi_get_pdf_template( $pdf_template, $lang = '' ) { // phpcs:ignore Universal.Files.SeparateFunctionsFromOO
		if ( function_exists( 'wpml_object_id_filter' ) ) {
			global $sitepress;

			if ( ! is_null( $sitepress ) && is_callable( array( $sitepress, 'get_current_language' ) ) ) {
				$lang         = empty( $lang ) ? $sitepress->get_current_language() : $lang;
				$pdf_template = wpml_object_id_filter(
					$pdf_template,
					YITH_YWPI_PDF_Template_Builder::$pdf_template,
					true,
					$lang
				);
			}
		}

		return new YITH_YWPI_PDF_Template( $pdf_template );
	}
}
