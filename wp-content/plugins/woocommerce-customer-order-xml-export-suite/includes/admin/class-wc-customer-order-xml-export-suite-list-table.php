<?php
/**
 * WooCommerce Customer/Order XML Export Suite
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order XML Export Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order XML Export Suite for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-customer-order-xml-export-suite/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2019, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Customer/Order XML Export Suite List Table
 *
 * Lists recently exported files
 *
 * @since 2.0.0
 */
 class WC_Customer_Order_XML_Export_Suite_List_Table extends WP_List_Table {


	/** @var array associative array of translated export status labels */
	private $statuses;


	/**
	 * Constructor - setup list table
	 *
	 * @since 2.0.0
	 * @param array $args
	 * @return \WC_Customer_Order_XML_Export_Suite_List_Table
	 */
	public function __construct( $args = array() ) {

		parent::__construct( array(
			'singular' => 'export',
			'plural'   => 'exports',
			'ajax'     => false,
		) );

		$this->statuses = array(
			'queued'     => __( 'Queued', 'woocommerce-customer-order-xml-export-suite' ),
			'processing' => __( 'Processing', 'woocommerce-customer-order-xml-export-suite' ),
			'completed'  => __( 'Completed', 'woocommerce-customer-order-xml-export-suite' ),
			'failed'     => __( 'Failed', 'woocommerce-customer-order-xml-export-suite' ),
			'paused'     => __( 'Paused', 'woocommerce-customer-order-xml-export-suite' ),
		);
	}


	/**
	 * Set column titles
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_columns() {

		$columns = array(
			'cb'              => '<input type="checkbox" />',
			'export_status'   => '<span class="status_head tips" data-tip="' . esc_attr__( 'Export Status', 'woocommerce-customer-order-xml-export-suite' ) . '">' . esc_attr__( 'Export Status', 'woocommerce-customer-order-xml-export-suite' ) . '</span>',
			'transfer_status' => '<span class="transfer_status_head tips" data-tip="' . esc_attr__( 'Transfer Status', 'woocommerce-customer-order-xml-export-suite' ) . '">' . esc_attr__( 'Transfer Status', 'woocommerce-customer-order-xml-export-suite' ) . '</span>',
			'export_type'     => esc_html__( 'Type', 'woocommerce-customer-order-xml-export-suite' ),
			'invocation'      => esc_html__( 'Invocation', 'woocommerce-customer-order-xml-export-suite' ),
			'filename'        => esc_html__( 'File name', 'woocommerce-customer-order-xml-export-suite' ),
			'export_date'     => esc_html__( 'Date', 'woocommerce-customer-order-xml-export-suite' ),
			'file_actions'    => esc_html__( 'Actions', 'woocommerce-customer-order-xml-export-suite' ),
		);

		$auto_exports_enabled = false;

		foreach ( array( 'orders', 'customers', 'coupons' ) as $export_type ) {

			if ( $auto_export_method = wc_customer_order_xml_export_suite()->get_methods_instance()->get_auto_export_method( $export_type ) ) {

				if ( 'local' !== $auto_export_method ) {
					$auto_exports_enabled = true;
					break;
				}
			}
		}

		// hide transfer status column if no auto exports have been configured
		if ( ! $auto_exports_enabled ) {
			unset( $columns['transfer_status'] );
		}

		return $columns;
	}


	/**
	 * Gets column content.
	 *
	 * @since 2.0.0
	 *
	 * @param object $export_job the export job object
	 * @param string $column_name the column name
	 * @return string the column content
	 */
	public function column_default( $export_job, $column_name ) {

		$export = wc_customer_order_xml_export_suite_get_export( $export_job );

		if ( ! $export ) {
			return '';
		}

		switch ( $column_name ) {

			case 'export_status':

				$status = 'processing' === $export->get_status() && wc_customer_order_xml_export_suite()->is_batch_processing_enabled() ? 'paused' : $export->get_status();
				$label = $this->statuses[ $status ];

				return sprintf( '<mark class="%1$s tips" data-tip="%2$s">%3$s</mark>', sanitize_key( $status ), $label, $label );

			break;

			case 'transfer_status':

				if ( ! $export->get_transfer_status() ) {

					return __( 'N/A', 'woocommerce-customer-order-xml-export-suite' );

				} else {

					$label = $this->statuses[ $export->get_transfer_status() ];
					return sprintf( '<mark class="%1$s tips" data-tip="%2$s">%3$s</mark>', sanitize_key( $export->get_transfer_status() ), esc_attr( $label ), esc_html( $label ) );
				}

			break;

			case 'invocation':

				return 'auto' === $export->get_invocation() ? esc_html__( 'Auto', 'woocommerce-customer-order-xml-export-suite' ) : esc_html__( 'Manual', 'woocommerce-customer-order-xml-export-suite' );

			break;

			case 'filename':

				return esc_html( $export->get_filename() );
			break;

			case 'export_type':

				if ( 'orders' === $export->get_type() ) {

					return esc_html__( 'Orders', 'woocommerce-customer-order-xml-export-suite' );

				} elseif ( 'customers' === $export->get_type() ) {

					return esc_html__( 'Customers', 'woocommerce-customer-order-xml-export-suite' );

				} elseif ( 'coupons' === $export->get_type() ) {

					return esc_html__( 'Coupons', 'woocommerce-customer-order-xml-export-suite' );
				}
			break;

			case 'export_date':
				return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $export->get_created_at() ) );
			break;
		}
	}


	/**
	 * Outputs actions column content for the given export.
	 *
	 * @since 2.0.0
	 *
	 * @param object $export export job object
	 */
	public function column_file_actions( $export ) {

		$export = wc_customer_order_xml_export_suite_get_export( $export );

		if ( ! $export ) {
			return;
		}

		?><p>
			<?php
				$actions = array();

				if ( 'completed' === $export->get_status() ) {

					$download_url = wp_nonce_url( admin_url(), 'download-export' );

					$download_url = add_query_arg( array(
						'download_exported_xml_file' => 1,
						'export_id'                  => $export->get_id(),
					), $download_url );

					$actions['download'] = array(
						'url'    => $download_url,
						'name'   => esc_html__( 'Download', 'woocommerce-customer-order-xml-export-suite' ),
						'action' => 'download'
					);

					if ( $auto_export_method = wc_customer_order_xml_export_suite()->get_methods_instance()->get_auto_export_method( $export->get_type() ) ) {

						if ( 'local' !== $auto_export_method ) {

							$label = wc_customer_order_xml_export_suite()->get_methods_instance()->get_export_method_label( $auto_export_method );

							$transfer_url = wp_nonce_url( admin_url(), 'transfer-export' );
							$transfer_url = add_query_arg( array(
								'transfer_xml_export' => 1,
								'export_id'           => $export->get_id(),
							), $transfer_url );

							$actions['transfer'] = array(
								'url'    => $transfer_url,
								/* translators: Placeholders: %s - via [method], full example: Send via Email */
								'name'   => sprintf( esc_html__( 'Send %s', 'woocommerce-customer-order-xml-export-suite' ), $label ),
								'action' => 'email' === $auto_export_method ? 'email' : 'transfer',
							);
						}

					}

					$view_url = wp_nonce_url( admin_url(), 'view-export' );

					$view_url = add_query_arg( array(
						'view_exported_xml_file' => 1,
						'export_id'              => $export->get_id(),
					), $view_url );

					$actions['view'] = array(
						'url'    => $view_url,
						'name'   => esc_html__( 'View', 'woocommerce-customer-order-xml-export-suite' ),
						'action' => 'view',
						'target' => '_blank',
					);

				} elseif ( 'processing' === $export->get_status() && wc_customer_order_xml_export_suite()->is_batch_processing_enabled() ) {

					$actions['resume'] = array(
						'name'   => __( 'Resume', 'woocommerce-customer-order-xml-export-suite' ),
						'action' => 'resume',
						'url'    => '#',
					);
				}

				$delete_url = wp_nonce_url( admin_url(), 'delete-export' );
				$delete_url = add_query_arg( array(
					'delete_xml_export' => 1,
					'export_id'         => $export->get_id(),
				), $delete_url );

				$done = in_array( $export->get_status(), array( 'completed', 'failed' ), true );

				$actions['delete'] = array(
					'url'    => $delete_url,
					'name'   => $done ? esc_html__( 'Delete', 'woocommerce-customer-order-xml-export-suite' ) : esc_html__( 'Cancel', 'woocommerce-customer-order-xml-export-suite' ),
					'action' => $done ? 'delete' : 'cancel',
				);

				/**
				 * Allow actors to change the available actions for an export in Exports List
				 *
				 * @since 2.0.0
				 * @param array $actions
				 * @param stdClass $export
				 */
				$actions = apply_filters( 'wc_customer_order_xml_export_suite_admin_export_actions', $actions, $export );

				foreach ( $actions as $action ) {
					printf( '<a class="button tips %1$s" href="%2$s" data-tip="%3$s" data-export-id="%4$s">%5$s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $export->get_id() ), esc_attr( $action['name'] ) );
				}
			?>
		</p><?php

	}


	/**
	 * Handles the checkbox column output.
	 *
	 * @since 2.0.0
	 *
	 * @param object $export export job object
	 */
	public function column_cb( $export ) {

		$export = wc_customer_order_xml_export_suite_get_export( $export );

		if ( ! $export ) {
			return;
		}

		if ( current_user_can( 'manage_woocommerce_xml_exports' ) ) : ?>
			<label
					class="screen-reader-text"
					for="cb-select-<?php echo sanitize_html_class( $export->get_id() ); ?>"
			><?php esc_html_e( 'Select export' ); ?></label>
			<input
					id="cb-select-<?php echo sanitize_html_class( $export->get_id() ); ?>"
					type="checkbox"
					name="export[]"
					value="<?php echo esc_attr( $export->get_id() ); ?>"
			/>
			<div class="locked-indicator"></div>
		<?php endif;
	}


	/**
	 * Prepare exported files for display
	 *
	 * @since 2.0.0
	 */
	public function prepare_items() {

		// set column headers manually, see https://codex.wordpress.org/Class_Reference/WP_List_Table#Extended_Properties
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = wc_customer_order_xml_export_suite()->get_export_handler_instance()->get_exports();
	}


	/**
	 * The HTML to display when there are no exported files
	 *
	 * @see WP_List_Table::no_items()
	 * @since 2.0.0
	 */
	public function no_items() {
		?>
		<p><?php esc_html_e( 'Exported files will appear here. Files are stored for 14 days after the export.', 'woocommerce-customer-order-xml-export-suite' ); ?></p>
		<?php
	}


	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_bulk_actions() {

		return array(
			'delete' => esc_html__( 'Delete', 'woocommerce-customer-order-xml-export-suite' ),
		);
	}


}
