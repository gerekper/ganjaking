<?php
/**
 * Credit note object
 *
 * Handles the credit note document.
 *
 * @class   YITH_Credit_Note
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_Credit_Note' ) ) {
	/**
	 * Implements features related to a PDF document
	 *
	 * @class YITH_Credit_Note
	 * @since 1.0.0
	 */
	class YITH_Credit_Note extends YITH_Document {

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
		 * @param int $order_id The order id for which an invoice is creating.
		 *
		 * @since  1.0
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
		 * @since  1.0.0
		 */
		public function is_valid() {
			return $this->order && $this->order instanceof WC_Order_Refund;
		}

		/**
		 * Check if this document has been generated
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function generated() {
			if ( function_exists( 'yith_plugin_fw_is_wc_custom_orders_table_usage_enabled' ) && yith_plugin_fw_is_wc_custom_orders_table_usage_enabled() ) {
				return $this->is_valid() && $this->order->get_meta( '_ywpi_credit_note' );
			} else {
				return $this->is_valid() && get_post_meta( $this->order->get_id(), '_ywpi_credit_note', true );
			}
		}

		/**
		 * Check if an invoice exist for current order and load related data
		 */
		private function init_document() {
			if ( ! $this->is_valid() ) {
				return;
			}

			if ( $this->generated() ) {
				$this->number           = $this->order->get_meta( '_ywpi_credit_note_number' );
				$this->prefix           = ywpi_get_option( 'ywpi_credit_note_prefix' );
				$this->suffix           = ywpi_get_option( 'ywpi_credit_note_suffix' );
				$this->formatted_number = $this->order->get_meta( '_ywpi_credit_note_formatted_number' );

				$this->date        = $this->order->get_meta( '_ywpi_credit_note_date' );
				$this->save_path   = $this->order->get_meta( '_ywpi_credit_note_path' );
				$this->save_folder = $this->order->get_meta( '_ywpi_credit_note_folder' );
			}
		}

		/**
		 * Cancel current document
		 */
		public function reset() {
			$this->order->delete_meta_data( '_ywpi_credit_note' );
			$this->order->delete_meta_data( '_ywpi_credit_note_number' );
			$this->order->delete_meta_data( '_ywpi_credit_note_prefix' );
			$this->order->delete_meta_data( '_ywpi_credit_note_suffix' );
			$this->order->delete_meta_data( '_ywpi_credit_note_formatted_number' );
			$this->order->delete_meta_data( '_ywpi_credit_note_path' );
			$this->order->delete_meta_data( '_ywpi_credit_note_folder' );
			$this->order->delete_meta_data( '_ywpi_credit_note_date' );
			$this->order->delete_meta_data( '_ywpi_xml_credit_note' );
			$this->order->delete_meta_data( '_ywpi_xml_credit_note_path' );

			$this->order->save();
		}

		/**
		 * Retrieve the formatted order date
		 *
		 * @param string $extension The extension of the document.
		 *
		 * @return false|string
		 */
		public function get_formatted_document_date( $extension = 'pdf' ) {
			$date = '';

			if ( $this->order ) {
				/**
				 * APPLY_FILTERS: ywpi_invoice_date_format
				 *
				 * Filter the invoice date format.
				 *
				 * @param string the date format.
				 *
				 * @return string
				 */
				$format = apply_filters( 'ywpi_invoice_date_format', ywpi_get_option( 'ywpi_invoice_date_format' ), $extension );
				$date   = date( $format, strtotime( $this->date ) ); //phpcs:ignore
			}

			return $date;
		}

		/**
		 * Retrieve the formatted document number
		 *
		 * @return mixed|string
		 * @since  1.0.0
		 */
		public function get_formatted_document_number() {
			return $this->formatted_number;
		}

		/**
		 * Save credit note
		 *
		 * @param string $extension The extension of the document.
		 */
		public function save( $extension = 'pdf' ) {
			$this->order->update_meta_data( '_ywpi_credit_note', true );
			$this->order->update_meta_data( '_ywpi_credit_note_prefix', $this->prefix );
			$this->order->update_meta_data( '_ywpi_credit_note_suffix', $this->suffix );
			$this->order->update_meta_data( '_ywpi_credit_note_number', $this->number );
			$this->order->update_meta_data( '_ywpi_credit_note_formatted_number', $this->formatted_number );
			$this->order->update_meta_data( '_ywpi_credit_note_date', $this->date );
			$this->order->update_meta_data( '_ywpi_credit_note_path', $this->save_path );
			$this->order->update_meta_data( '_ywpi_credit_note_folder', $this->save_folder );

			// TODO remove it in the future when the HPOS sync works correctly.
			update_post_meta( $this->order->get_id(), '_ywpi_credit_note', true );
			update_post_meta( $this->order->get_id(), '_ywpi_credit_note_prefix', $this->prefix );
			update_post_meta( $this->order->get_id(), '_ywpi_credit_note_suffix', $this->suffix );
			update_post_meta( $this->order->get_id(), '_ywpi_credit_note_number', $this->number );
			update_post_meta( $this->order->get_id(), '_ywpi_credit_note_formatted_number', $this->formatted_number );
			update_post_meta( $this->order->get_id(), '_ywpi_credit_note_date', $this->date );
			update_post_meta( $this->order->get_id(), '_ywpi_credit_note_path', $this->save_path );
			update_post_meta( $this->order->get_id(), '_ywpi_credit_note_folder', $this->save_folder );

			do_action( 'ywpi_document_save_props', $this, $this->order, $extension );

			$this->order->save();
		}
	}
}
