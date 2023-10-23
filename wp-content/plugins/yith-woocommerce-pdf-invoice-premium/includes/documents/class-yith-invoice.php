<?php
/**
 * Invoice object
 *
 * Handles the invoice document.
 *
 * @class   YITH_Invoice
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_Invoice' ) ) {
	/**
	 * Implements features related to a PDF document
	 *
	 * @class YITH_Invoice
	 * @since 1.0.0
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
			return $this->order && $this->order instanceof WC_Order;
		}

		/**
		 * Check if this document has been generated
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function generated() {
			if ( function_exists( 'yith_plugin_fw_is_wc_custom_orders_table_usage_enabled' ) && yith_plugin_fw_is_wc_custom_orders_table_usage_enabled() ) {
				return $this->is_valid() && $this->order->get_meta( '_ywpi_invoiced' );
			} else {
				return $this->is_valid() && get_post_meta( $this->order->get_id(), '_ywpi_invoiced', true );
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
				$this->number           = $this->order->get_meta( '_ywpi_invoice_number' );
				$this->prefix           = ywpi_get_option( 'ywpi_invoice_prefix' );
				$this->suffix           = ywpi_get_option( 'ywpi_invoice_suffix' );
				$this->formatted_number = $this->order->get_meta( '_ywpi_invoice_formatted_number' );
				$this->date             = $this->order->get_meta( '_ywpi_invoice_date' );
				$this->save_path        = $this->order->get_meta( '_ywpi_invoice_path' );
				$this->save_path_xml    = $this->order->get_meta( '_ywpi_xml_path' );
				$this->save_folder      = $this->order->get_meta( '_ywpi_invoice_folder' );
			}
		}

		/**
		 * Cancel current document
		 */
		public function reset() {
			$this->order->delete_meta_data( '_ywpi_invoiced' );
			$this->order->delete_meta_data( '_ywpi_invoice_number' );
			$this->order->delete_meta_data( '_ywpi_invoice_prefix' );
			$this->order->delete_meta_data( '_ywpi_invoice_suffix' );
			$this->order->delete_meta_data( '_ywpi_invoice_formatted_number' );
			$this->order->delete_meta_data( '_ywpi_invoice_path' );
			$this->order->delete_meta_data( '_ywpi_invoice_folder' );
			$this->order->delete_meta_data( '_ywpi_invoice_date' );
			$this->order->delete_meta_data( '_ywpi_has_xml' );
			$this->order->delete_meta_data( '_ywpi_xml_path' );
			$this->order->delete_meta_data( '_ywpi_xml_folder' );

			$this->order->save();
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

				if ( empty( $date_format ) ) {
					$date_to_show = ywpi_get_option( 'ywpi_date_to_show_in_invoice' );
				} else {
					$date_to_show = $date_format;
				}

				$create_invoice_date = $this->order->get_meta( '_ywpi_invoice_date' ) !== '' ? gmdate( $format, strtotime( $this->order->get_meta( '_ywpi_invoice_date' ) ) ) : gmdate( $format, time() );

				switch ( $date_to_show ) {
					case 'new':
						$post            = get_post( $this->order->get_id() );
						$date_to_convert = date_i18n( $format, strtotime( $post->post_date ) );
						break;

					case 'completed':
						$date_to_convert = $this->order->get_meta( '_completed_date' ) ? gmdate( $format, strtotime( $this->order->get_meta( '_completed_date' ) ) ) : $create_invoice_date;
						break;

					case 'invoice_creation':
						$date_to_convert = $create_invoice_date;
						break;

					default:
						$date_to_convert = gmdate( $format, strtotime( $this->order->get_date_created() ) );
				}

				/**
				 * APPLY_FILTERS: ywpi_invoice_date_format_document
				 *
				 * Filter the invoice document formatted date.
				 *
				 * @param string $date_to_convert the date converted.
				 * @param string $format the date format.
				 * @param object $order the order object.
				 *
				 * @return string
				 */
				$date = apply_filters( 'ywpi_invoice_date_format_document', $date_to_convert, $format, $this->order );
			}

			return $date;
		}

		/**
		 * Retrieve the formatted document number
		 *
		 * @return mixed|string|void
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
			if ( 'pdf' === $extension ) {
				$this->order->update_meta_data( '_ywpi_invoiced', true );
				$this->order->update_meta_data( '_ywpi_invoice_prefix', $this->prefix );
				$this->order->update_meta_data( '_ywpi_invoice_suffix', $this->suffix );
				$this->order->update_meta_data( '_ywpi_invoice_number', $this->number );
				$this->order->update_meta_data( '_ywpi_invoice_formatted_number', $this->formatted_number );
				$this->order->update_meta_data( '_ywpi_invoice_date', $this->date );
				$this->order->update_meta_data( '_ywpi_invoice_path', $this->save_path );
				$this->order->update_meta_data( '_ywpi_invoice_folder', $this->save_folder );

				// TODO remove it in the future when the HPOS sync works correctly.
				update_post_meta( $this->order->get_id(), '_ywpi_invoiced', true );
				update_post_meta( $this->order->get_id(), '_ywpi_invoice_prefix', $this->prefix );
				update_post_meta( $this->order->get_id(), '_ywpi_invoice_suffix', $this->suffix );
				update_post_meta( $this->order->get_id(), '_ywpi_invoice_number', $this->number );
				update_post_meta( $this->order->get_id(), '_ywpi_invoice_formatted_number', $this->formatted_number );
				update_post_meta( $this->order->get_id(), '_ywpi_invoice_date', $this->date );
				update_post_meta( $this->order->get_id(), '_ywpi_invoice_path', $this->save_path );
				update_post_meta( $this->order->get_id(), '_ywpi_invoice_folder', $this->save_folder );
			}

			do_action( 'ywpi_document_save_props', $this, $this->order, $extension );

			$this->order->save();
		}
	}
}
