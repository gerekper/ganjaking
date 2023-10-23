<?php
/**
 * Pro forma object
 *
 * Handles the pro forma document.
 *
 * @class   YITH_Pro_Forma
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_Pro_Forma' ) ) {
	/**
	 * Implements features related to a PDF document
	 *
	 * @class YITH_Invoice
	 * @since 1.0.0
	 */
	class YITH_Pro_Forma extends YITH_Document {

		/**
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @param int $order_id the order for which the document is generated.
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
				return $this->is_valid() && $this->order->get_meta( '_ywpi_has_pro_forma' );
			} else {
				return $this->is_valid() && get_post_meta( $this->order->get_id(), '_ywpi_has_pro_forma', true );
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
				$this->save_path   = $this->order->get_meta( '_ywpi_pro_forma_path' );
				$this->save_folder = $this->order->get_meta( '_ywpi_pro_forma_folder' );
			}
		}

		/**
		 *  Cancel reference to pro-forma options for the current order
		 */
		public function reset() {
			$this->order->delete_meta_data( '_ywpi_has_pro_forma' );
			$this->order->delete_meta_data( '_ywpi_pro_forma_path' );

			$this->order->save();
		}

		/**
		 * Set invoice data for current order, picking the invoice number from the related general option
		 */
		public function save() {
			$this->order->update_meta_data( '_ywpi_has_pro_forma', true );
			$this->order->update_meta_data( '_ywpi_pro_forma_path', $this->save_path );
			$this->order->update_meta_data( '_ywpi_pro_forma_folder', $this->save_folder );

			// TODO remove it in the future when the HPOS sync works correctly.
			update_post_meta( $this->order->get_id(), '_ywpi_has_pro_forma', true );
			update_post_meta( $this->order->get_id(), '_ywpi_pro_forma_path', $this->save_path );
			update_post_meta( $this->order->get_id(), '_ywpi_pro_forma_folder', $this->save_folder );

			$this->order->save();
		}
	}
}
