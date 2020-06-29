<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YITH_Document' ) ) {

	/**
	 * Implements features related to a PDF document
	 *
	 * @class   YITH_Document
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	abstract class YITH_Document {

		/**
		 * @var WC_Order the order associated to this document
		 */
		public $order = null;

		/**
		 * @var string path to store the document
		 */
		public $save_path;

        /**
         * @var string path to store the document XML
         */
        public $save_path_xml;

		/**
		 * @var string folder path for the current PDF document
		 */
		public $save_folder;

		/**
		 * Create a new document for a specific order
		 *
		 * @param int $order_id
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 */
		public function __construct( $order_id ) {

			if ( ! $order_id ) {
				return;
			}

			/**
			 * Get the WooCommerce order for this order id
			 */
			$this->order = wc_get_order( $order_id );
		}

		/**
		 * Check if the document is associated to a valid order
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function is_valid() {

			return false;
		}

		/**
		 * Check if this document has been generated
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function generated() {
			return false;
		}

		/**
		 * Retrieve if a file for this document exists
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function file_exists() {
			return file_exists( $this->get_full_path() );
		}

		/**
		 * Get full path to the current document
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_full_path( $extension = 'pdf', $order_id = null ) {

            $this->save_path = apply_filters( 'ywpi_document_save_path', $this->save_path, $extension, $order_id, $this );

			return YITH_YWPI_DOCUMENT_SAVE_DIR . $this->save_folder . '/' . $this->save_path;
		}

		public function save() {
			//Do nothing
		}

		/**
		 * Retrieve the formatted order date
		 * @return string
		 */
		public function get_formatted_order_date() {
			$date = '';
			if ( $this->order ) {
				$format = apply_filters('ywpi_invoice_date_format',ywpi_get_option ( 'ywpi_invoice_date_format' ));
				$order_date = yit_get_prop( $this->order, 'order_date' );

				$date = date( $format, strtotime( $order_date ) );
			}

			return $date;
		}

		/**
		 * Retrieve the document number. Overload from extended class to assign a value.
		 * @return string
		 */
		public function get_formatted_document_number() {
			return '';
		}

		/**
		 * Retrieve the document data, if not set it will be equal to the formatted order date
		 * @return string
		 */
		public function get_formatted_document_date() {
			return $this->get_formatted_order_date();
		}
	}
}