<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_Credit_Note' ) ) {
	
	/**
	 * Implements features related to a PDF document
	 *
	 * @class   YITH_Credit_Note
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_Credit_Note extends YITH_Document {
		
		/**
		 * @var string date of creation for the current invoice
		 */
		public $date;
		
		/**
		 * @var string the document number
		 */
		public $number;
		
		/**
		 * @var string the document prefix
		 */
		public $prefix;
		
		/**
		 * @var string the document suffix
		 */
		public $suffix;
		
		/**
		 * @var string the document formatted number
		 */
		public $formatted_number;
		
		/**
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param int $order_id int the order for which an invoice is creating
		 *
		 * @since  1.0
		 * @author Lorenzo giuffrida
		 * @access public
		 */
		public function __construct( $order_id = 0 ) {
			
			/**
			 * Call base class constructor
			 */
			parent::__construct ( $order_id );
			
			/**
			 *  Fill invoice information from a previous invoice is exists or from general plugin options plus order related data
			 * */
			$this->init_document ();
		}
		
		/**
		 * Check if the document is associated to a valid order
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function is_valid() {
			
			return $this->order && $this->order instanceof WC_Order_Refund;
		}
		
		/**
		 * Check if this document has been generated
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function generated() {
			return $this->is_valid () && yit_get_prop ( $this->order, '_ywpi_credit_note', true );
		}
		
		/*
		 * Check if an invoice exist for current order and load related data
		 */
		private function init_document() {
			
			if ( ! $this->is_valid () ) {
				return;
			}
			
			if ( $this->generated () ) {
				
				$this->number           = yit_get_prop ( $this->order, '_ywpi_credit_note_number', true );
				$this->prefix           = yit_get_prop ( $this->order, '_ywpi_credit_note_prefix', true );
				$this->suffix           = yit_get_prop ( $this->order, '_ywpi_credit_note_suffix', true );
				$this->formatted_number = yit_get_prop ( $this->order, '_ywpi_credit_note_formatted_number', true );
				
				$this->date        = yit_get_prop ( $this->order, '_ywpi_credit_note_date', true );
				$this->save_path   = yit_get_prop ( $this->order, '_ywpi_credit_note_path', true );
				$this->save_folder = yit_get_prop ( $this->order, '_ywpi_credit_note_folder', true );
			}
		}
		
		/**
		 * Cancel current document
		 */
		public function reset() {
			
			yit_delete_prop ( $this->order, '_ywpi_credit_note' );
			yit_delete_prop ( $this->order, '_ywpi_credit_note_number' );
			yit_delete_prop ( $this->order, '_ywpi_credit_note_prefix' );
			yit_delete_prop ( $this->order, '_ywpi_credit_note_suffix' );
			yit_delete_prop ( $this->order, '_ywpi_credit_note_formatted_number' );
			
			yit_delete_prop ( $this->order, '_ywpi_credit_note_path' );
			yit_delete_prop ( $this->order, '_ywpi_credit_note_folder' );
			yit_delete_prop ( $this->order, '_ywpi_credit_note_date' );

            yit_delete_prop ( $this->order, '_ywpi_xml_credit_note' );
            yit_delete_prop ( $this->order, '_ywpi_xml_credit_note_path' );
		}
		
		/**
		 * Retrieve the formatted order date
		 *
		 */
		public function get_formatted_document_date( $extension='pdf' ) {
			$date = '';
			if ( $this->order ) {
				$format = apply_filters('ywpi_invoice_date_format',ywpi_get_option ( 'ywpi_invoice_date_format' ), $extension);
				$date   = date ( $format, strtotime ( $this->date ) );
			}
			
			return $date;
		}
		
		/**
		 * Retrieve the formatted document number
		 *
		 * @return mixed|string|void
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_formatted_document_number() {

			
			// if a document number is set, retrieve it
			$formatted_invoice_number = yit_get_prop ( $this->order, '_ywpi_credit_note_formatted_number', true );
			
			if ( $formatted_invoice_number ) {
				return $formatted_invoice_number;
			}
			
			$formatted_invoice_number = ywpi_get_option_with_placeholder ( 'ywpi_credit_note_number_format', '[number]' );
			
			$formatted_invoice_number = str_replace (
				array( '[prefix]', '[suffix]', '[number]' ),
				array( $this->prefix, $this->suffix, $this->number ),
				$formatted_invoice_number );
			
			return $formatted_invoice_number;
		}
		
		/**
		 * Save credit note
		 */
		public function save( $extension = 'pdf' ) {

            yit_save_prop ( $this->order,
                apply_filters( 'ywpi_document_props',
                    array(
                        '_ywpi_credit_note'                  => true,
                        '_ywpi_credit_note_prefix'           => $this->prefix,
                        '_ywpi_credit_note_suffix'           => $this->suffix,
                        '_ywpi_credit_note_number'           => $this->number,
                        '_ywpi_credit_note_formatted_number' => $this->formatted_number,
                        '_ywpi_credit_note_date'             => $this->date,
                        '_ywpi_credit_note_path'             => $this->save_path,
                        '_ywpi_credit_note_folder'           => $this->save_folder,
                    ),
                    $this,
                    $this->order,
                    $extension
                )
            );

		}
	}
}