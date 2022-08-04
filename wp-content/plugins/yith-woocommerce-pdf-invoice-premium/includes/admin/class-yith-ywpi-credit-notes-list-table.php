<?php // phpcs:ignore WordPress.NamingConventions
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH\PDF_Invoice\Classes
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_WCPI_Credit_Notes_Table' ) ) {

	/**
	 * YITH_WCPI_Credit_Notes_Table Class
	 *
	 * @class YITH_WCPI_Credit_Notes_Table
	 * @author  YITH
	 * @package YITH\PDF_Invoice\Classes
	 */
	class YITH_WCPI_Credit_Notes_Table extends YITH_WCPI_Documents_List_Table {

		/**
		 * Construct
		 *
		 * @param  array $args The array of arguments.
		 * @author YITH
		 * @since  2.1
		 */
		public function __construct( $args = array() ) {
			parent::__construct();
		}

		/**
		 * Return the columns for the table
		 *
		 * @return array
		 * @since  2.1.0
		 * @author YITH
		 */
		public function get_columns() {
			$columns = parent::get_columns();

			$columns['document_number'] = _x( 'Credit Note No.', '[admin] Invoices table column header', 'yith-woocommerce-pdf-invoice' );

			return apply_filters( 'yith_ywpi_credit_notes_list_columns', $columns );
		}

		/**
		 * Return list of available bulk actions
		 *
		 * @return array Available bulk action
		 * @since 2.1.0
		 */
		public function get_bulk_actions() {
			$actions = array(
				'download'     => __( 'Download PDF', 'yith-woocommerce-pdf-invoice' ),
				'download_xml' => __( 'Download XML', 'yith-woocommerce-pdf-invoice' ),
				'delete'       => __( 'Delete', 'yith-woocommerce-pdf-invoice' ),
				'regenerate'   => __( 'Regenerate', 'yith-woocommerce-pdf-invoice' ),
			);
			if ( 'yes' !== ywpi_get_option( 'ywpi_enable_credit_notes' ) ) {
				// Remove regenerate action if credit notes is disabled.
				unset( $actions['regenerate'] );
			}
			if ( 'yes' !== ywpi_get_option( 'ywpi_electronic_invoice_enable' ) ) {
				unset( $actions['download_xml'] );
			}

			return $actions;
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
				$credit_note = ywpi_get_credit_note( yit_get_prop( $order, 'id' ) );
				switch ( $column_name ) {

					case 'date':
						$invoice_date = $credit_note->get_formatted_document_date();

						$output = $invoice_date;
						break;
					case 'document_number':
						$invoice_number = $credit_note->get_formatted_document_number();

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
									$is_xml_generated = yit_get_prop( $order, '_ywpi_xml_credit_note', true );
									$xml_action       = $is_xml_generated ? 'download' : 'create';

									$url_download   = YITH_PDF_Invoice()->get_action_url( 'download', 'credit-note', yit_get_prop( $order, 'id' ) );
									$url_xml        = YITH_PDF_Invoice()->get_action_url( $xml_action, 'credit-note', yit_get_prop( $order, 'id' ), 'xml' );
									$url_delete     = YITH_PDF_Invoice()->get_action_url( 'reset', 'credit-note', yit_get_prop( $order, 'id' ) );
									$url_regenerate = YITH_PDF_Invoice()->get_action_url( 'regenerate', 'credit-note', yit_get_prop( $order, 'id' ) );
									$url_send       = YITH_PDF_Invoice()->get_action_url( 'send_customer', 'credit-note', yit_get_prop( $order, 'id' ) );

									$actions = array(
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
											'regenerate'   => array(
												'name' => __( 'Regenerate', 'yith-woocommerce-pdf-invoice' ),
												'url'  => $url_regenerate,
												'confirm_data' => array(
													'title'   => __( 'Confirm regenerate', 'yith-woocommerce-pdf-invoice' ),
													'message' => __( 'Do you want to regenerate this credit note?', 'yith-woocommerce-pdf-invoice' ),
												),
											),
											'send_customer' => array(
												'name' => __( 'Send to customer', 'yith-woocommerce-pdf-invoice' ),
												'url'  => $url_send,
												'confirm_data' => array(
													'title'          => __( 'Send to customer', 'yith-woocommerce-pdf-invoice' ),
													'message'        => __( 'Do you want to send this credit note to the customer?', 'yith-woocommerce-pdf-invoice' ),
													'cancel-button'  => __( 'No', 'yith-woocommerce-pdf-invoice' ),
													'confirm-button' => __( 'Yes, send it', 'yith-woocommerce-pdf-invoice' ),
												),
											),
											'delete'       => array(
												'name' => __( 'Delete', 'yith-woocommerce-pdf-invoice' ),
												'url'  => $url_delete,
												'confirm_data' => array(
													'title'               => __( 'Confirm delete', 'yith-woocommerce-pdf-invoice' ),
													'message'             => __( 'Are you sure to delete this credit note?', 'yith-woocommerce-pdf-invoice' ),
													'confirm-button'      => 'Delete',
													'confirm-button-type' => 'delete',
												),
											),
										),
									);
									if ( 'yes' !== ywpi_get_option( 'ywpi_enable_credit_notes' ) ) {
										// Remove regenerate action if credit notes is disabled.
										unset( $actions['menu']['regenerate'] );
									}
									if ( 'yes' !== ywpi_get_option( 'ywpi_electronic_invoice_enable' ) ) {
										unset( $actions['menu']['download_xml'] );
									}
									yith_plugin_fw_get_component(
										$actions
									);
								} else {
									$url = YITH_PDF_Invoice()->get_action_url( $action_id, 'credit-note', yit_get_prop( $order, 'id' ), '', true );
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
				echo wp_kses_post( apply_filters( 'yith_ywpi_list_output_column_default', $output, $column_name, $order ) );
			}

		}

		/**
		 * Prepare items for table
		 *
		 * @return void
		 * @since 2.1.0
		 */
		public function prepare_items() {
			//phpcs:disable WordPress.Security.NonceVerification.Recommended

			global $wpdb;

			$per_page              = apply_filters( 'yith_ywpi_documents_list_per_page', 15 );
			$query_args            = array(
				'metas'    => '',
				'limit'    => '',
				'order_by' => '',
			);
			$columns               = $this->get_columns();
			$hidden                = array();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->_column_headers = array( $columns, $hidden );

			$current_page = $this->get_pagenum();
			$search_input = isset( $_REQUEST['s'] ) ? wp_unslash( $_REQUEST['s'] ) : ''; //phpcs:ignore

			$offset = ( $current_page - 1 ) * $per_page;

			/*
			 * Datepickers filter.
			 */

			$from      = isset( $_REQUEST['_from'] ) ? $_REQUEST['_from'] : false; //phpcs:ignore
			$to        = isset( $_REQUEST['_to'] ) ? $_REQUEST['_to'] : false; //phpcs:ignore
			$from_date = '';
			$to_date   = '';

			$date_format = apply_filters( 'ywpi_date_format_for_datepickers', 'd M, y' );

			if ( ! empty( $from ) ) {
				$from_date = DateTime::createFromFormat( $date_format, $from );
				$from_date = $from_date->format( 'Y-m-d' );
			}
			if ( ! empty( $to ) ) {
				$to_date = DateTime::createFromFormat( $date_format, $to );
				$to_date = $to_date->format( 'Y-m-d' );
			}

			if ( $from_date && $to_date ) {
				$query_args['metas'] = "AND om2.meta_key = '_ywpi_credit_note_date' AND om2.meta_value BETWEEN '{$from_date}' AND '{$to_date}' + INTERVAL 1 DAY";
			} elseif ( $from_date && empty( $to_date ) ) {
				$query_args['metas'] = "AND om2.meta_key = '_ywpi_credit_note_date' AND om2.meta_value >= '{$from_date}'";
			} elseif ( empty( $from_date ) && $to_date ) {
				$query_args['metas'] = "AND om2.meta_key = '_ywpi_credit_note_date' AND om2.meta_value < '{$to_date}'";
			}

			/*
			 * Order by
			 */
			$query_args['order_by'] = "AND om3.meta_key = '_ywpi_credit_note_number' ORDER BY om3.meta_value ASC";

			if ( -1 !== $per_page ) {
				$query_args['limit'] = " LIMIT {$per_page} OFFSET {$offset}";
			}

			// phpcs:disable
			$this->items = $wpdb->get_col(
				"
			    SELECT DISTINCT ID
			    FROM {$wpdb->prefix}posts AS o
			    LEFT JOIN {$wpdb->prefix}postmeta AS om
			    ON o.ID = om.post_id
			    LEFT JOIN {$wpdb->prefix}postmeta AS om2
			    ON o.ID = om2.post_id
			    LEFT JOIN {$wpdb->prefix}postmeta AS om3
				ON o.ID = om3.post_id
			    WHERE o.post_type = 'shop_order_refund'
			    AND om.meta_key = '_ywpi_credit_note'
			    AND ( om3.meta_key = '_ywpi_credit_note_formatted_number' AND om3.meta_value LIKE '%{$search_input}%' OR o.post_parent LIKE '%{$search_input}%' )
			    {$query_args['metas']}
			    {$query_args['order_by']}
			    {$query_args['limit']}
			"
			);

			$total_items =
				$wpdb->get_col(
					"
				    SELECT COUNT( DISTINCT ID )
				    FROM {$wpdb->prefix}posts AS o
				    LEFT JOIN {$wpdb->prefix}postmeta AS om
				    ON o.ID = om.post_id
				    LEFT JOIN {$wpdb->prefix}postmeta AS om2
				    ON o.ID = om2.post_id
				    LEFT JOIN {$wpdb->prefix}postmeta AS om3
				    ON o.ID = om3.post_id
				    WHERE o.post_type = 'shop_order_refund'
				    AND om.meta_key = '_ywpi_credit_note'
				    AND om3.meta_key = '_ywpi_credit_note_formatted_number' AND ( om3.meta_value LIKE '%{$search_input}%' OR o.post_parent LIKE '%{$search_input}%' )
				    {$query_args['metas']}
				"
			);
			// phpcs:enable

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
