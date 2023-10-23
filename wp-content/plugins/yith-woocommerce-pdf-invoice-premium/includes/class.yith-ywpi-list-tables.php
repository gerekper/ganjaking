<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase, WordPress.Files.FileName.InvalidClassFileName

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_YWPI_List_Tables' ) ) {
	/**
	 * Manage Invoice and Credit Notes List Tables
	 *
	 * @class   YITH_YWPI_List_Tables
	 * @package YITH\PDF_Invoice\Classes
	 * @since   2.1.0
	 * @author  YITH <plugins@yithemes.com>
	 */
	class YITH_YWPI_List_Tables {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWPI_List_Tables
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  2.1.0
		 * @access public
		 */
		public function __construct() {
			add_action( 'yith_wcpi_invoice_list_table', array( $this, 'invoices_list_table' ) );

			add_action( 'yith_wcpi_credit_notes_list_table', array( $this, 'credit_notes_list_table' ) );

			add_action( 'init', array( $this, 'manage_actions_list_table' ) );

			add_action( 'init', array( $this, 'process_bulk_actions' ) );
			add_action( 'admin_init', array( $this, 'export_csv' ) );
			add_action( 'admin_init', array( $this, 'download_all' ) );
		}

		/**
		 * Show the list of invoices created.
		 */
		public function invoices_list_table() {
			$path = YITH_YWPI_DIR . 'views/panel/invoices-list-tab.php';

			if ( file_exists( $path ) ) {
				include $path;
			}
		}

		/**
		 * Show the list of credit notes created.
		 */
		public function credit_notes_list_table() {
			$path = YITH_YWPI_DIR . 'views/panel/credit-notes-list-tab.php';

			if ( file_exists( $path ) ) {
				include $path;
			}
		}

		/**
		 * Manage the list table actions
		 */
		public function manage_actions_list_table() {
			if ( isset( $_REQUEST['page'] ) && 'yith_woocommerce_pdf_invoice_panel' === $_REQUEST['page'] && isset( $_REQUEST['action'] ) && isset( $_REQUEST['type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				YITH_WooCommerce_Pdf_Invoice_Premium::get_instance()->manage_document_action();
			}
		}

		/**
		 * Process bulk actions from Invoice and Credit Notes WP List Table
		 */
		public function process_bulk_actions() {
			if ( isset( $_REQUEST['filter_action'] ) || isset( $_REQUEST['export_action'] ) || isset( $_REQUEST['download_all'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$current_action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$current_action = ( '-1' === strval( $current_action ) && isset( $_REQUEST['action2'] ) ) ? sanitize_key( wp_unslash( $_REQUEST['action2'] ) ) : $current_action; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$document_type = ( isset( $_REQUEST['sub_tab'] ) && 'documents_type-credit-notes' === sanitize_text_field( wp_unslash( $_REQUEST['sub_tab'] ) ) ) ? 'credit-note' : 'invoice'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( ! empty( $_REQUEST['yith_ywpi_checkbox_ids'] ) && ! empty( $current_action ) && ! empty( $document_type ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$order_ids = array_map( 'intval', $_REQUEST['yith_ywpi_checkbox_ids'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$redirect  = esc_url_raw( remove_query_arg( array( 'action', 'action2', 'yith_ywpi_checkbox_ids' ) ) );

				$instance = YITH_WooCommerce_Pdf_Invoice_Premium::get_instance();

				switch ( $current_action ) {
					case 'download':
						$instance->download_files_as_zip( $order_ids, $document_type ? $document_type : '', 'pdf' );
						break;

					case 'download_xml':
						$instance->download_files_as_zip( $order_ids, $document_type ? $document_type : '', 'xml' );
						break;

					case 'delete':
						foreach ( $order_ids as $order_id ) {
							$instance->reset_document( $order_id, $document_type ? $document_type : '' );
						}
						break;

					case 'regenerate':
						foreach ( $order_ids as $order_id ) {
							$instance->regenerate_document( $order_id, $document_type ? $document_type : '', 'pdf' );
						}
						break;
				}

				wp_safe_redirect( $redirect );
				die();
			}
		}

		/**
		 * Export CSV action for invoices and credit notes
		 */
		public function export_csv() {
			global $wpdb;

			$available_tabs = array( 'invoice', 'credit-note', 'documents_type' );

			if ( ! isset( $_REQUEST['page'] ) || 'yith_woocommerce_pdf_invoice_panel' !== $_REQUEST['page'] || ! isset( $_REQUEST['tab'] ) || ( ! in_array( $_REQUEST['tab'], $available_tabs, true ) ) || ! isset( $_REQUEST['export_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$document_type = ( isset( $_REQUEST, $_REQUEST['sub_tab'] ) && 'documents_type-credit-notes' === sanitize_text_field( wp_unslash( $_REQUEST['sub_tab'] ) ) ) ? 'credit-note' : 'invoice'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			/**
			 * APPLY_FILTERS: yith_ywpi_export_list_columns
			 *
			 * Filter the columns to export in the CSV.
			 *
			 * @param array the columns.
			 *
			 * @return array
			 */
			$headings = apply_filters(
				'yith_ywpi_export_list_columns',
				array(
					'date'            => _x( 'Date', '[admin] Invoices table column header', 'yith-woocommerce-pdf-invoice' ),
					'document_number' => 'credit-note' === $document_type ? _x( 'Credit Note No.', '[admin] Credit notes table column header', 'yith-woocommerce-pdf-invoice' ) : _x( 'Invoice No.', '[admin] Invoices table column header', 'yith-woocommerce-pdf-invoice' ),
					'order'           => _x( 'Order', '[admin] Invoices table column header', 'yith-woocommerce-pdf-invoice' ),
					'customer'        => _x( 'Customer', '[admin] Invoices table column header', 'yith-woocommerce-pdf-invoice' ),
					'subtotal'        => _x( 'Subtotal', '[admin] Invoices table column header', 'yith-woocommerce-pdf-invoice' ),
					'tax'             => _x( 'Tax', '[admin] Invoices table column header', 'yith-woocommerce-pdf-invoice' ),
					'total'           => _x( 'Total', '[admin] Invoices table column header', 'yith-woocommerce-pdf-invoice' ),
				)
			);

			$query_args = array(
				'metas'    => '',
				'order_by' => '',
			);
			$per_page   = apply_filters( 'yith_ywpi_documents_list_per_page', 15 );

			$current_page = isset( $_REQUEST['paged'] ) ? max( 1, absint( $_REQUEST['paged'] ) ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$search_input = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$offset = ( $current_page - 1 ) * $per_page;

			$meta_key                  = ( 'credit-note' === $document_type ) ? '_ywpi_credit_note' : '_ywpi_invoiced';
			$post_type                 = ( 'credit-note' === $document_type ) ? 'shop_order_refund' : 'shop_order';
			$meta_key_date             = ( 'credit-note' === $document_type ) ? '_ywpi_credit_note_date' : '_ywpi_invoice_date';
			$meta_key_formatted_number = ( 'credit-note' === $document_type ) ? '_ywpi_credit_note_formatted_number' : '_ywpi_invoice_formatted_number';
			$order_id_field            = ( 'credit-note' === $document_type ) ? 'post_parent' : 'ID';
			$document_meta_key_number  = 'credit-note' === $document_type ? '_ywpi_credit_note_number' : '_ywpi_invoice_number';

			$from = isset( $_REQUEST['_from'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_from'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$to   = isset( $_REQUEST['_to'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_to'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$date_format = apply_filters( 'ywpi_date_format_for_datepickers_converted', 'yy-m-d' );

			if ( ! empty( $from ) ) {
				$from_date = DateTime::createFromFormat( $date_format, $from );
				$from_date = $from_date->format( 'Y-m-d' );
			}

			if ( ! empty( $to ) ) {
				$to_date = DateTime::createFromFormat( $date_format, $to );
				$to_date = $to_date->format( 'Y-m-d' );
			}

			if ( $from && $to ) {
				$query_args['metas'] = "AND om2.meta_key = '{$meta_key_date}' AND om2.meta_value BETWEEN '{$from_date}' AND '{$to_date}' + INTERVAL 1 DAY";
			} elseif ( $from && empty( $to ) ) {
				$query_args['metas'] = "AND om2.meta_key = '{$meta_key_date}' AND om2.meta_value >= '{$from_date}'";
			} elseif ( empty( $from ) && $to ) {
				$query_args['metas'] = "AND om2.meta_key = '{$meta_key_date}' AND om2.meta_value < '{$to_date}'";
			}

			/*
			 * Order by
			 */
			$query_args['order_by'] = "AND om4.meta_key = '{$document_meta_key_number}' ORDER BY om4.meta_value ASC";

			if ( -1 !== $per_page ) {
				$query_args['limit'] = "LIMIT {$per_page} OFFSET {$offset}";
			}

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$orders = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare(
					"SELECT DISTINCT ID
			    	FROM {$wpdb->prefix}posts AS o
			    	LEFT JOIN {$wpdb->prefix}postmeta AS om
			    	ON o.ID = om.post_id
			    	LEFT JOIN {$wpdb->prefix}postmeta AS om2
			    	ON o.ID = om2.post_id
			    	LEFT JOIN {$wpdb->prefix}postmeta AS om3
			    	ON o.ID = om3.post_id
			    	LEFT JOIN {$wpdb->prefix}postmeta AS om4
			    	ON o.ID = om4.post_id
			    	WHERE o.post_type = %s
			    	AND om.meta_key = %s
			    	AND ( om3.meta_key = %s AND om3.meta_value LIKE %s OR %s LIKE %s )
			    	{$query_args['metas']}
			    	{$query_args['order_by']}
					",
					$post_type,
					$meta_key,
					$meta_key_formatted_number,
					'%' . $wpdb->esc_like( $search_input ) . '%',
					"o.$order_id_field",
					'%' . $wpdb->esc_like( $search_input ) . '%',
				)
			);
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

			if ( ! empty( $orders ) ) {
				$sitename  = sanitize_key( get_bloginfo( 'name' ) );
				$sitename .= ( ! empty( $sitename ) ) ? '-' : '';
				$filename  = $sitename . '-' . $document_type . '-' . gmdate( 'Y-m-d' ) . '.csv';

				header( 'Content-Description: File Transfer' );
				header( 'Content-Disposition: attachment; filename=' . $filename );
				header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );

				$df = fopen( 'php://output', 'w' );

				fputcsv( $df, $headings );

				foreach ( $orders as $the_order ) {
					if ( is_numeric( $the_order ) ) {
						$the_order = wc_get_order( $the_order );
					}

					$values = array();

					foreach ( $headings as $key => $heading ) {
						$document = ( 'credit-note' === $document_type ) ? ywpi_get_credit_note( $the_order->get_id() ) : ywpi_get_invoice( $the_order->get_id() );

						if ( $the_order instanceof \Automattic\WooCommerce\Admin\Overrides\OrderRefund || $the_order instanceof WC_Order_Refund ) {
							$order = wc_get_order( $the_order->get_parent_id() );
						} else {
							$order = $the_order;
						}

						switch ( $key ) {
							case 'date':
								$document_date = $document->get_formatted_document_date();
								$output        = $document_date;
								break;

							case 'document_number':
								$document_number = $document->get_formatted_document_number();
								$output          = $document_number;
								break;

							case 'order':
								$output = ( $order ) ? $order->get_id() : '';
								break;

							case 'customer':
								$customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
								$billing_vat   = $order->get_meta( '_billing_vat_number' );
								$billing_ssn   = $order->get_meta( '_billing_vat_ssn' );

								$billing_country = $order->get_billing_country();
								$countries       = WC()->countries->get_countries();

								if ( isset( $countries[ $billing_country ] ) ) {
									$billing_country = $countries[ $billing_country ];
								}

								$output = $customer_name . ' - ' . $order->get_billing_address_1() . ' - ' . $order->get_billing_postcode() . ' - ' . $order->get_billing_city() . ' - ' . $billing_country;
								$output = ( $billing_vat ) ? $output . ' - ' . apply_filters( 'yith_ywpi_vat_field_text', esc_html__( 'VAT', 'yith-woocommerce-pdf-invoice' ) ) . ': ' . $billing_vat : $output;
								$output = ( $billing_ssn ) ? $output . ' - ' . apply_filters( 'yith_ywpi_ssn_field_text', esc_html__( 'SSN', 'yith-woocommerce-pdf-invoice' ) ) . ': ' . $billing_ssn : $output;
								break;

							case 'subtotal':
								$output = ( 'credit-note' === $document_type ) ? $the_order->get_subtotal() : $order->get_subtotal();
								break;

							case 'tax':
								$output = ( 'credit-note' === $document_type ) ? $the_order->get_total_tax() : $order->get_total_tax();
								break;

							case 'total':
								$output = ( 'credit-note' === $document_type ) ? $the_order->get_total() : $order->get_total();
								break;

							default:
								/**
								 * APPLY_FILTERS: yith_ywpi_default_list_output_column
								 *
								 * Filter the default column output when exporting in CSV.
								 *
								 * @param string the default output.
								 * @param int $key the key.
								 * @param object $order the order object.
								 *
								 * @return string
								 */
								$output = apply_filters( 'yith_ywpi_default_list_output_column', '', $key, $order );
						}
						/**
						 * Filter to modify the output of the columns.
						 *
						 * @since 3.7.0
						 *
						 * @param array  $output contains the columns data.
						 * @param string $key    contains the columns name.
						 * @param object $order  contains the order.
						 */
						$values[] = apply_filters( 'yith_ywpi_export_output_column', $output, $key, $order );
					}

					fputcsv( $df, $values );
				}

				fclose( $df ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
				die();
			}
		}

		/**
		 * Download all the documents ( Invoices or credit notes ).
		 */
		public function download_all() {
			global $wpdb;

			$available_tabs = array( 'invoice', 'credit-note', 'documents_type' );

			if ( ! isset( $_REQUEST['page'] ) || YITH_YWPI_Plugin_FW_Loader::get_instance()->get_panel_page() !== $_REQUEST['page'] || ! isset( $_REQUEST['tab'] ) || ( ! in_array( $_REQUEST['tab'], $available_tabs, true ) ) || ! isset( $_REQUEST['download_all'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return;
			}

			$document_type = ( isset( $_REQUEST, $_REQUEST['sub_tab'] ) && 'documents_type-credit-notes' === sanitize_text_field( wp_unslash( $_REQUEST['sub_tab'] ) ) ) ? 'credit-note' : 'invoice'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$zip_name      = ( 'credit-note' === $document_type ) ? 'credit_notes' : 'invoices';
			$zip_name      = $zip_name . '_' . uniqid( wp_rand(), true ) . '.zip';

			$meta_key  = ( 'credit-note' === $document_type ) ? '_ywpi_credit_note' : '_ywpi_invoiced';
			$post_type = ( 'credit-note' === $document_type ) ? 'shop_order_refund' : 'shop_order';

			$orders = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->prepare(
					"SELECT DISTINCT ID
			    	FROM {$wpdb->prefix}posts AS o
			    	LEFT JOIN {$wpdb->prefix}postmeta AS om
			    	ON o.ID = om.post_id
			   	 	LEFT JOIN {$wpdb->prefix}postmeta AS om2
			    	ON o.ID = om2.post_id
			    	LEFT JOIN {$wpdb->prefix}postmeta AS om3
			    	ON o.ID = om3.post_id
			    	WHERE o.post_type = %s
			    	AND om.meta_key = %s
					",
					$post_type,
					$meta_key
				)
			);

			if ( ! empty( $orders ) ) {
				$zip = new ZipArchive();

				$zip->open( $zip_name, ZipArchive::CREATE );

				foreach ( $orders as $order ) {
					if ( is_numeric( $order ) ) {
						$order = wc_get_order( $order );
					}

					/**
					 * APPLY_FILTERS: yith_ywpi_download_all_files_as_zip_condition_per_order
					 *
					 * Filter the condition to download all files in a zip.
					 *
					 * @param bool false to include the order invoice in the zip, true to not.
					 * @param object $order the order object.
					 *
					 * @return bool
					 */
					if ( apply_filters( 'yith_ywpi_download_all_files_as_zip_condition_per_order', false, $order ) ) {
						continue;
					}

					$document  = ( 'credit-note' === $document_type ) ? ywpi_get_credit_note( $order->get_id() ) : ywpi_get_invoice( $order->get_id() );
					$file_path = $document->get_full_path();
					$filename  = basename( $file_path );

					$zip->addFile( $file_path, $filename );
				}

				$zip->close();

				header( 'Content-Type: application/zip' );
				header( 'Content-disposition: attachment; filename=' . $zip_name );
				header( 'Content-Length: ' . filesize( $zip_name ) );
				header( 'Pragma: no-cache' );
				header( 'Expires: 0' );

				readfile( $zip_name ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile

				// remove the file after download it.
				wp_delete_file( $zip_name );

				exit();
			}
		}
	}
}
