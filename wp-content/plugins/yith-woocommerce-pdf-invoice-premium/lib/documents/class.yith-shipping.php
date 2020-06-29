<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YITH_Shipping' ) ) {
	
	/**
	 * Implements features related to a PDF document
	 *
	 * @class   YITH_Shipping
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 */
	class YITH_Shipping extends YITH_Document {
		
		/**
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param int $order_id the order for which the document is generated
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
			
			return $this->order && $this->order instanceof WC_Order;
		}
		
		/**
		 * Check if this document has been generated
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function generated() {
			return $this->is_valid () && yit_get_prop ( $this->order, '_ywpi_has_packing_slip', true );
		}
		
		/*
		 * Check if a document exist for current order and load related data
		 */
		private function init_document() {
			if ( ! $this->is_valid () ) {
				return;
			}
			
			if ( $this->generated () ) {
				$this->save_path   = yit_get_prop ( $this->order, '_ywpi_packing_slip_path', true );
				$this->save_folder = yit_get_prop ( $this->order, '_ywpi_packing_slip_folder', true );
			}
		}
		
		/**
		 *  Cancel packing slip document for the current order
		 */
		public function reset() {
			yit_delete_prop ( $this->order, '_ywpi_has_packing_slip' );
			yit_delete_prop ( $this->order, '_ywpi_packing_slip_path' );
		}
		
		/**
		 * Set invoice data for current order, picking the invoice number from the related general option
		 */
		public function save() {
			
			yit_save_prop ( $this->order,
				array(
					'_ywpi_has_packing_slip'    => true,
					'_ywpi_packing_slip_path'   => $this->save_path,
					'_ywpi_packing_slip_folder' => $this->save_folder,
				) );
		}
	}
}