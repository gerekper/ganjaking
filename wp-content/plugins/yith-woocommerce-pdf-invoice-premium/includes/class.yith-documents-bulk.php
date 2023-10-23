<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName
/**
 * Documents bulk class.
 *
 * Handles the bulk actions of the documents.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDFInvoice\Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Documents_Bulk' ) ) {
	/**
	 * Implements features of YITH_Documents_Bulk
	 *
	 * @class   YITH_Documents_Bulk
	 * @package YITH\PDFInvoice\Classes
	 * @since   1.0.0
	 */
	class YITH_Documents_Bulk {

		/**
		 * Single instance of the class
		 *
		 * @var object
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * YITH_Documents_Bulk constructor.
		 */
		public function __construct() {
			if ( function_exists( 'yith_plugin_fw_is_wc_custom_orders_table_usage_enabled' ) && yith_plugin_fw_is_wc_custom_orders_table_usage_enabled() ) {
				add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'yith_ywpi_documents_bulk_actions' ) );
				add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'yith_ywpi_documents_bulk_actions_handler' ), 10, 3 );
			} else {
				add_filter( 'bulk_actions-edit-shop_order', array( $this, 'yith_ywpi_documents_bulk_actions' ) );
				add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'yith_ywpi_documents_bulk_actions_handler' ), 10, 3 );
			}
		}

		/**
		 * Add documents bulk actions.
		 *
		 * @param array $bulk_actions Array with the bulk actions.
		 *
		 * @return mixed
		 */
		public function yith_ywpi_documents_bulk_actions( $bulk_actions ) {
			$bulk_actions['generate_invoices']       = esc_html__( 'Generate Invoices', 'yith-woocommerce-pdf-invoice' );
			$bulk_actions['generate_packing_slip']   = esc_html__( 'Generate Packing Slip', 'yith-woocommerce-pdf-invoice' );
			$bulk_actions['regenerate_invoices']     = esc_html__( 'Regenerate Invoices', 'yith-woocommerce-pdf-invoice' );
			$bulk_actions['regenerate_packing_slip'] = esc_html__( 'Regenerate Packing Slip', 'yith-woocommerce-pdf-invoice' );

			return apply_filters( 'yith_ywpi_order_list_bulk_actions', $bulk_actions );
		}

		/**
		 * Handle bulk actions.
		 *
		 * @param string $redirect_to URL to redirect to.
		 * @param string $doaction    Action name.
		 * @param array  $post_ids    List of ids.
		 *
		 * @return string
		 */
		public function yith_ywpi_documents_bulk_actions_handler( $redirect_to, $doaction, $post_ids ) {
			if ( 'generate_invoices' === $doaction ) {
				if ( count( $post_ids ) > 0 ) {
					foreach ( $post_ids as $post_id ) {
						$this->yith_ywpi_documents_bulk_generate_invoice( $post_id );
					}
				}
			}

			if ( 'generate_packing_slip' === $doaction ) {
				if ( count( $post_ids ) > 0 ) {
					foreach ( $post_ids as $post_id ) {
						$this->yith_ywpi_documents_bulk_generate_packing_slip( $post_id );
					}
				}
			}

			if ( 'regenerate_invoices' === $doaction ) {
				if ( count( $post_ids ) > 0 ) {
					foreach ( $post_ids as $post_id ) {
						$this->yith_ywpi_documents_bulk_regenerate_invoice( $post_id );
					}
				}
			}

			if ( 'regenerate_packing_slip' === $doaction ) {
				if ( count( $post_ids ) > 0 ) {
					foreach ( $post_ids as $post_id ) {
						$this->yith_ywpi_documents_bulk_regenerate_packing_slip( $post_id );
					}
				}
			}

			return $redirect_to;
		}

		/**
		 * Bulk generate invoices.
		 *
		 * @param int $post_id The post id.
		 */
		public function yith_ywpi_documents_bulk_generate_invoice( $post_id ) {
			$object = new YITH_WooCommerce_Pdf_Invoice();
			$object->create_document( $post_id, 'invoice' );

			// Create XML file on 'Generate invoices' bulk action.
			/**
			 * DO_ACTION: ywpi_create_document
			 *
			 * Trigger the create document on bulk actions.
			 *
			 * @param object $object the invoice object.
			 * @param int $post_id the post ID.
			 * @param string the document type
			 */
			do_action( 'ywpi_create_document', $object, $post_id, 'invoice', 'xml' );
		}

		/**
		 * Bulk generate packing slips.
		 *
		 * @param int $post_id The post id.
		 */
		public function yith_ywpi_documents_bulk_generate_packing_slip( $post_id ) {
			$object = new YITH_WooCommerce_Pdf_Invoice();
			$object->create_document( $post_id, 'packing-slip' );
		}

		/**
		 * Bulk regenerate invoices.
		 *
		 * @param int $post_id The post id.
		 */
		public function yith_ywpi_documents_bulk_regenerate_invoice( $post_id ) {
			$object = new YITH_WooCommerce_Pdf_Invoice();
			$object->regenerate_document( $post_id, 'invoice', 'pdf' );
		}

		/**
		 * Bulk regenerate packing slips.
		 *
		 * @param int $post_id The post id.
		 */
		public function yith_ywpi_documents_bulk_regenerate_packing_slip( $post_id ) {
			$object = new YITH_WooCommerce_Pdf_Invoice();
			$object->regenerate_document( $post_id, 'packing-slip', 'pdf' );
		}
	}
}
