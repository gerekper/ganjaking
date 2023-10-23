<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Class to manage the invoices table
 *
 * @package YITH\PDF_Invoice\Classes
 * @author  YITH <plugins@yithemes.com>
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WCPI_Invoices_List_Table' ) ) {
	/**
	 * YITH_WCPI_Invoices_List_Table Class
	 *
	 * @class YITH_WCPI_Invoices_List_Table
	 */
	class YITH_WCPI_Invoices_List_Table extends YITH_WCPI_Documents_List_Table {

		/**
		 * Construct
		 *
		 * @param  array $args The array of arguments.
		 * @since  2.1
		 */
		public function __construct( $args = array() ) {
			parent::__construct();
		}

		/**
		 * Return the default column for the table.
		 *
		 * @param object $order The order object.
		 * @param string $column_name The column name.
		 */
		public function column_default( $order, $column_name ) {
			$output = parent::column_default( $order, $column_name );

			if ( is_numeric( $order ) ) {
				$order = wc_get_order( $order );
			}

			if ( ! empty( $order ) ) {
				switch ( $column_name ) {
					case 'date':
						$invoice      = ywpi_get_invoice( $order->get_id() );
						$invoice_date = $invoice->get_formatted_document_date( 'pdf', 'invoice_creation' );

						$output = $invoice_date;
						break;

					case 'document_number':
						$invoice        = ywpi_get_invoice( $order->get_id() );
						$invoice_number = $invoice->get_formatted_document_number();

						$output = $invoice_number;
						break;

					case 'subtotal':
						$output = wc_price( $order->get_subtotal() );
						break;

					case 'tax':
						$output = wc_price( $order->get_total_tax() );
						break;

					case 'total':
						$output = wc_price( $order->get_total() );
						break;

					case 'actions':
						$available_actions = array(
							'preview'   => array(
								'icon'  => 'eye',
								'title' => __( 'View', 'yith-woocommerce-pdf-invoice' ),
							),
							'more-menu' => array(
								'icon'  => 'more',
								'title' => __( 'Further actions', 'yith-woocommerce-pdf-invoice' ),
							),
						);

						?>
						<div class="yith-ywpi-actions-container">
							<?php
							foreach ( $available_actions as $action_id => $action ) {
								if ( 'more-menu' === $action_id ) {
									$is_xml_generated = $order->get_meta( '_ywpi_has_xml' );
									$xml_action       = $is_xml_generated ? 'download' : 'create';

									$url_download   = YITH_PDF_Invoice()->get_action_url( 'download', 'invoice', $order->get_id(), 'pdf', false, 'table' );
									$url_xml        = YITH_PDF_Invoice()->get_action_url( $xml_action, 'invoice', $order->get_id(), 'xml', false, 'table' );
									$url_delete     = YITH_PDF_Invoice()->get_action_url( 'reset', 'invoice', $order->get_id(), 'pdf', false, 'table' );
									$url_regenerate = YITH_PDF_Invoice()->get_action_url( 'regenerate', 'invoice', $order->get_id(), 'pdf', false, 'table' );
									$url_send       = YITH_PDF_Invoice()->get_action_url( 'send_customer', 'invoice', $order->get_id(), 'pdf', false, 'table' );

									$actions = apply_filters(
										'yith_ywpi_invoice_list_table_actions',
										array(
											'type'   => 'action-button',
											'title'  => $action['title'],
											'action' => $action_id,
											'icon'   => $action['icon'],
											'menu'   => array(
												'download_pdf' => array(
													'name' => __( 'Download PDF', 'yith-woocommerce-pdf-invoice' ),
													'url'  => $url_download,
												),
												'download_xml' => array(
													'name' => $is_xml_generated ? __( 'Download XML', 'yith-woocommerce-pdf-invoice' ) : __( 'Create XML', 'yith-woocommerce-pdf-invoice' ),
													'url'  => $url_xml,
												),
												'regenerate' => array(
													'name' => __( 'Regenerate', 'yith-woocommerce-pdf-invoice' ),
													'url'  => $url_regenerate,
													'confirm_data' => array(
														'title'   => __( 'Confirm regenerate', 'yith-woocommerce-pdf-invoice' ),
														'message' => __( 'Do you want to regenerate this invoice?', 'yith-woocommerce-pdf-invoice' ),
													),
												),
												'send_customer' => array(
													'name' => __( 'Send to customer', 'yith-woocommerce-pdf-invoice' ),
													'url'  => $url_send,
													'confirm_data' => array(
														'title'          => __( 'Send to customer', 'yith-woocommerce-pdf-invoice' ),
														'message'        => __( 'Do you want to send this invoice to the customer?', 'yith-woocommerce-pdf-invoice' ),
														'cancel-button'  => __( 'No', 'yith-woocommerce-pdf-invoice' ),
														'confirm-button' => __( 'Yes, send it', 'yith-woocommerce-pdf-invoice' ),
													),
												),
												'delete' => array(
													'name' => __( 'Delete', 'yith-woocommerce-pdf-invoice' ),
													'url'  => $url_delete,
													'confirm_data' => array(
														'title'               => __( 'Confirm delete', 'yith-woocommerce-pdf-invoice' ),
														'message'             => __( 'Are you sure you want to delete this invoice?', 'yith-woocommerce-pdf-invoice' ),
														'cancel-button'       => __( 'No', 'yith-woocommerce-pdf-invoice' ),
														'confirm-button'      => __( 'Yes, delete', 'yith-woocommerce-pdf-invoice' ),
														'confirm-button-type' => 'delete',
													),
												),
											),
										)
									);

									if ( 'yes' !== ywpi_get_option( 'ywpi_electronic_invoice_enable' ) ) {
										unset( $actions['menu']['download_xml'] );
									}

									yith_plugin_fw_get_component( $actions );
								} else {
									$url = YITH_PDF_Invoice()->get_action_url( $action_id, 'invoice', $order->get_id(), '', true );

									yith_plugin_fw_get_component(
										array(
											'type'   => 'action-button',
											'title'  => $action['title'],
											'class'  => 'ywpi_preview_action',
											'action' => $action_id,
											'icon'   => $action['icon'],
											'url'    => $url,
										)
									);
								}
							}
							?>
						</div>
						<?php
				}

				echo wp_kses_post( $output );
			}
		}

		/**
		 * Prepare items for table
		 *
		 * @return void
		 * @since 2.1.0
		 */
		public function prepare_items() {
			global $wpdb;

			$per_page              = apply_filters( 'yith_ywpi_documents_list_per_page', 15 );
			$query_args            = array(
				'metas' => '',
				'limit' => '',
			);
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden );

			$current_page = $this->get_pagenum();
			$search_input = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$offset = ( $current_page - 1 ) * $per_page;

			// Datepickers filter.
			$from      = isset( $_REQUEST['_from'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_from'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$to        = isset( $_REQUEST['_to'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_to'] ) ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$from_date = '';
			$to_date   = '';

			$date_format = apply_filters( 'ywpi_date_format_for_datepickers_converted', 'yy-m-d' );

			if ( ! empty( $from ) ) {
				$from_date = DateTime::createFromFormat( $date_format, $from );
				$from_date = $from_date->format( 'Y-m-d' );
			}

			if ( ! empty( $to ) ) {
				$to_date = DateTime::createFromFormat( $date_format, $to );
				$to_date = $to_date->format( 'Y-m-d' );
			}

			if ( $from_date && $to_date ) {
				$query_args['metas'] = "AND om2.meta_key = '_ywpi_invoice_date' AND om2.meta_value BETWEEN '{$from_date}' AND '{$to_date}' + INTERVAL 1 DAY";
			} elseif ( $from_date && empty( $to_date ) ) {
				$query_args['metas'] = "AND om2.meta_key = '_ywpi_invoice_date' AND om2.meta_value >= '{$from_date}'";
			} elseif ( empty( $from_date ) && $to_date ) {
				$query_args['metas'] = "AND om2.meta_key = '_ywpi_invoice_date' AND om2.meta_value < '{$to_date}'";
			}

			$order_by = apply_filters( 'yith_ywpi_invoices_table_order', 'ASC' );

			/*
			 * Order by
			 */
			$query_args['order_by'] = "AND om3.meta_key = '_ywpi_invoice_number' ORDER BY om3.meta_value {$order_by}";

			if ( -1 !== $per_page ) {
				$query_args['limit'] = "LIMIT {$per_page} OFFSET {$offset}";
			}

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			if ( function_exists( 'yith_plugin_fw_is_wc_custom_orders_table_usage_enabled' ) && yith_plugin_fw_is_wc_custom_orders_table_usage_enabled() ) {
				$this->items = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					"SELECT DISTINCT o.id
				    FROM {$wpdb->prefix}wc_orders AS o
				    LEFT JOIN {$wpdb->prefix}wc_orders_meta AS om on o.id = om.order_id
				    LEFT JOIN {$wpdb->prefix}wc_orders_meta AS om2 on o.id = om2.order_id
				    LEFT JOIN {$wpdb->prefix}wc_orders_meta AS om3 on o.id = om3.order_id
				    WHERE o.type = 'shop_order'
					AND om.meta_key = '_ywpi_invoiced'
				    AND ( om3.meta_key = '_ywpi_invoice_formatted_number' AND om3.meta_value LIKE '%{$search_input}%' OR o.id LIKE '%{$search_input}%' )
				    {$query_args['metas']}
				    {$query_args['order_by']}
				    {$query_args['limit']}"
				);

				$total_items = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					"SELECT COUNT( DISTINCT o.id )
				    FROM {$wpdb->prefix}wc_orders AS o
			    	LEFT JOIN {$wpdb->prefix}wc_orders_meta AS om on o.id = om.order_id
			    	LEFT JOIN {$wpdb->prefix}wc_orders_meta AS om2 on o.id = om2.order_id
			    	LEFT JOIN {$wpdb->prefix}wc_orders_meta AS om3 on o.id = om3.order_id
			    	WHERE o.type = 'shop_order'
				    AND om.meta_key = '_ywpi_invoiced'
				    AND om3.meta_key = '_ywpi_invoice_formatted_number' AND ( om3.meta_value LIKE '%{$search_input}%' OR o.ID LIKE '%{$search_input}%' )
				    {$query_args['metas']}"
				);
			} else {
				$this->items = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					"SELECT DISTINCT ID
				    FROM {$wpdb->prefix}posts AS o
				    LEFT JOIN {$wpdb->prefix}postmeta AS om
				    ON o.ID = om.post_id
				    LEFT JOIN {$wpdb->prefix}postmeta AS om2
				    ON o.ID = om2.post_id
				    LEFT JOIN {$wpdb->prefix}postmeta AS om3
				    ON o.ID = om3.post_id
				    WHERE o.post_type = 'shop_order'
				    AND om.meta_key = '_ywpi_invoiced'
				    AND ( om3.meta_key = '_ywpi_invoice_formatted_number' AND om3.meta_value LIKE '%{$search_input}%' OR o.ID LIKE '%{$search_input}%' )
				    {$query_args['metas']}
				    {$query_args['order_by']}
				    {$query_args['limit']}"
				);

				$total_items = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					"SELECT COUNT( DISTINCT ID )
				    FROM {$wpdb->prefix}posts AS o
				    LEFT JOIN {$wpdb->prefix}postmeta AS om
				    ON o.ID = om.post_id
				    LEFT JOIN {$wpdb->prefix}postmeta AS om2
				    ON o.ID = om2.post_id
				    LEFT JOIN {$wpdb->prefix}postmeta AS om3
				    ON o.ID = om3.post_id
				    WHERE o.post_type = 'shop_order'
				    AND om.meta_key = '_ywpi_invoiced'
				    AND om3.meta_key = '_ywpi_invoice_formatted_number' AND ( om3.meta_value LIKE '%{$search_input}%' OR o.ID LIKE '%{$search_input}%' )
				    {$query_args['metas']}"
				);
			}
			// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

			$this->set_pagination_args(
				array(
					'total_items' => $total_items[0],
					'per_page'    => $per_page,
					'total_pages' => ceil( $total_items[0] / $per_page ),
				)
			);
		}
	}
}
