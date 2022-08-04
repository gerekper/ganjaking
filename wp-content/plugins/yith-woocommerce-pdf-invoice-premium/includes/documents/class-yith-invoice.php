<?php
/**
 * Invoice object
 *
 * Handles the invoice document.
 *
 * @class   YITH_Invoice
 * @author  YITH
 * @package YITH\PDFInvoice\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_Invoice' ) ) {

	/**
	 * Implements features related to a PDF document
	 *
	 * @class   YITH_Invoice
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_Invoice extends YITH_Document {

		/**
		 * The date of the document.
		 *
		 * @var string date of creation for the current invoice
		 */
		public $date;

		/**
		 * The number of the document.
		 *
		 * @var string the document number
		 */
		public $number;

		/**
		 * The prefix of the document.
		 *
		 * @var string the document prefix
		 */
		public $prefix;

		/**
		 * The suffix of the document.
		 *
		 * @var string the document suffix
		 */
		public $suffix;

		/**
		 * The formatted number of the document.
		 *
		 * @var string the document formatted number
		 */
		public $formatted_number;

		/**
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param int $order_id The order for which an invoice is creating.
		 *
		 * @since  1.0
		 * @author YITH
		 * @access public
		 */
		public function __construct( $order_id = 0 ) {

			/**
			 * Call base class constructor
			 */
			parent::__construct( $order_id );

			/**
			 *  Fill invoice information from a previous invoice is exists or from general plugin options plus order related data
			 * */
			$this->init_document();
		}

		/**
		 * Check if the document is associated to a valid order
		 *
		 * @return bool
		 * @author YITH
		 * @since  1.0.0
		 */
		public function is_valid() {

			return $this->order && $this->order instanceof WC_Order;
		}

		/**
		 * Check if this document has been generated
		 *
		 * @return bool
		 * @author YITH
		 * @since  1.0.0
		 */
		public function generated() {
			return $this->is_valid() && yit_get_prop( $this->order, '_ywpi_invoiced', true );
		}

		/**
		 * Check if an invoice exist for current order and load related data
		 */
		private function init_document() {

			if ( ! $this->is_valid() ) {
				return;
			}

			if ( $this->generated() ) {
				$this->number           = yit_get_prop( $this->order, '_ywpi_invoice_number', true );
				$this->prefix           = ywpi_get_option( 'ywpi_invoice_prefix' );
				$this->suffix           = ywpi_get_option( 'ywpi_invoice_suffix' );
				$this->formatted_number = yit_get_prop( $this->order, '_ywpi_invoice_formatted_number', true );
				$this->date             = yit_get_prop( $this->order, '_ywpi_invoice_date', true );
				$this->save_path        = yit_get_prop( $this->order, '_ywpi_invoice_path', true );
				$this->save_path_xml    = yit_get_prop( $this->order, '_ywpi_xml_path', true );
				$this->save_folder      = yit_get_prop( $this->order, '_ywpi_invoice_folder', true );
			}
		}

		/**
		 * Cancel current document
		 */
		public function reset() {

			yit_delete_prop( $this->order, '_ywpi_invoiced' );
			yit_delete_prop( $this->order, '_ywpi_invoice_number' );
			yit_delete_prop( $this->order, '_ywpi_invoice_prefix' );
			yit_delete_prop( $this->order, '_ywpi_invoice_suffix' );
			yit_delete_prop( $this->order, '_ywpi_invoice_formatted_number' );
			yit_delete_prop( $this->order, '_ywpi_invoice_path' );
			yit_delete_prop( $this->order, '_ywpi_invoice_folder' );
			yit_delete_prop( $this->order, '_ywpi_invoice_date' );
			yit_delete_prop( $this->order, '_ywpi_has_xml' );
			yit_delete_prop( $this->order, '_ywpi_xml_path' );
			yit_delete_prop( $this->order, '_ywpi_xml_folder' );
		}

		/**
		 * Retrieve the formatted order date
		 *
		 * @param string $extension Document extension.
		 * @param string $date_format Date format.
		 */
		public function get_formatted_document_date( $extension = 'pdf', $date_format = '' ) {

			$date = '';
			if ( $this->order ) {

				$format = apply_filters( 'ywpi_invoice_date_format', ywpi_get_option( 'ywpi_invoice_date_format' ), $extension );

				$order_id = yit_get_prop( $this->order, 'id' );

				if ( empty( $date_format ) ) {
					$date_to_show = ywpi_get_option( 'ywpi_date_to_show_in_invoice' );
				} else {
					$date_to_show = $date_format;
				}

				$create_invoice_date = yit_get_prop( $this->order, '_ywpi_invoice_date' ) != '' ? date( $format, strtotime( yit_get_prop( $this->order, '_ywpi_invoice_date' ) ) ) : date( $format, time() ); //phpcs:ignore

				switch ( $date_to_show ) {

					case 'new':
						$post            = get_post( $this->order->get_id() );
						$date_to_convert = date_i18n( $format, strtotime( $post->post_date ) );
						break;

					case 'completed':
						$date_to_convert = get_post_meta( $order_id, '_completed_date', true ) ? date( $format, strtotime( get_post_meta( $order_id, '_completed_date', true ) ) ) : $create_invoice_date; //phpcs:ignore
						break;

					case 'invoice_creation':
						$date_to_convert = $create_invoice_date;
						break;

					default:
						$date_to_convert = date( $format, strtotime( $this->order->get_date_created() ) ); //phpcs:ignore
						break;

				}

				$date = apply_filters( 'ywpi_invoice_date_format_document', $date_to_convert, $format, $this->order );           }

			return $date;
		}

		/**
		 * Retrieve the formatted document number
		 *
		 * @return mixed|string|void
		 * @author YITH
		 * @since  1.0.0
		 */
		public function get_formatted_document_number() {
			return $this->formatted_number;

		}

		/**
		 * Save invoice
		 *
		 * @param string $extension The extension of the document.
		 */
		public function save( $extension = 'pdf' ) {
			yit_save_prop(
				$this->order,
				apply_filters(
					'ywpi_document_props',
					array(
						'_ywpi_invoiced'                 => true,
						'_ywpi_invoice_prefix'           => $this->prefix,
						'_ywpi_invoice_suffix'           => $this->suffix,
						'_ywpi_invoice_number'           => $this->number,
						'_ywpi_invoice_formatted_number' => $this->formatted_number,
						'_ywpi_invoice_date'             => $this->date,
						'_ywpi_invoice_path'             => $this->save_path,
						'_ywpi_xml_path'                 => $this->save_path_xml,
						'_ywpi_invoice_folder'           => $this->save_folder,
					),
					$this,
					$this->order,
					$extension
				)
			);
		}
	}
}
