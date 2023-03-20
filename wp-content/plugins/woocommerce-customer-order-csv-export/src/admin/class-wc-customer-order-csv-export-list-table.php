<?php
/**
 * WooCommerce Customer/Order/Coupon Export
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Customer/Order CSV Export List Table
 *
 * Lists recently exported files
 *
 * @since 4.0.0
 */
class WC_Customer_Order_CSV_Export_List_Table extends WP_List_Table {


	/** @var string specific export type to list */
	private $export_type;

	/** @var string specific method type to list */
	private $method_type;

	/** @var array associative array of translated export status labels */
	private $statuses;


	/**
	 * Constructor - setup list table
	 *
	 * @since 4.0.0
	 * @param array $args
	 * @return \WC_Customer_Order_CSV_Export_List_Table
	 */
	public function __construct( $args = [] ) {

		parent::__construct( [
			'singular' => 'export',
			'plural'   => 'exports',
			'ajax'     => false
		] );

		$this->statuses = [
			'queued'     => esc_html__( 'Queued', 'woocommerce-customer-order-csv-export' ),
			'processing' => esc_html__( 'Processing', 'woocommerce-customer-order-csv-export' ),
			'completed'  => esc_html__( 'Completed', 'woocommerce-customer-order-csv-export' ),
			'failed'     => esc_html__( 'Failed', 'woocommerce-customer-order-csv-export' ),
			'paused'     => esc_html__( 'Paused', 'woocommerce-customer-order-csv-export' )
		];
	}


	/**
	 * Outputs the filter dropdowns and button.
	 *
	 * @since 5.0.0
	 *
	 * @param string $which the tablenav this is for -- 'top' or 'bottom'
	 */
	protected function extra_tablenav( $which ) {

		if ( 'top' === $which ) {

			?>
			<div class="alignleft actions">

				<label class="screen-reader-text" for="filter-by-export-type"><?php esc_html_e( 'Filter by export type', 'woocommerce-customer-order-csv-export' ); ?></label>
				<select id="filter-by-export-type" name="export_type">
					<option value=""><?php esc_html_e( 'Show all types', 'woocommerce-customer-order-csv-export' ); ?></option>

					<?php foreach ( wc_customer_order_csv_export()->get_export_types() as $type => $label ) : ?>

						<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $this->get_export_type() ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>

					<?php endforeach; ?>
				</select>

				<label class="screen-reader-text" for="filter-by-method"><?php esc_html_e( 'Filter by method', 'woocommerce-customer-order-csv-export' ); ?></label>
				<select id="filter-by-method" name="method_type">
					<option value=""><?php esc_html_e( 'Show all transfer methods', 'woocommerce-customer-order-csv-export' ); ?></option>
					<option value="download"><?php esc_html_e( 'Manual', 'woocommerce-customer-order-csv-export' ); ?></option>

					<?php foreach ( wc_customer_order_csv_export()->get_methods_instance()->get_export_method_labels() as $type => $label ) : ?>

						<option value="<?php echo esc_attr( $type ); ?>" <?php selected( $type, $this->get_method_type() ); ?>>
							<?php echo esc_html( $label ); ?>
						</option>

					<?php endforeach; ?>
				</select>

				<?php submit_button( _x( 'Filter', 'button text', 'woocommerce-customer-order-csv-export' ), '', 'filter_action', false ); ?>

			</div>
			<?php
		}
	}


	/**
	 * Set column titles
	 *
	 * @since 4.0.0
	 * @return array
	 */
	public function get_columns() {

		$columns = [
			'cb'              => '<input type="checkbox" />',
			'export_status'   => '<span class="status_head tips" data-tip="' . esc_attr__( 'Export Status', 'woocommerce-customer-order-csv-export' ) . '">' . esc_attr__( 'Export Status', 'woocommerce-customer-order-csv-export' ) . '</span>',
			'transfer_status' => '<span class="transfer_status_head tips" data-tip="' . esc_attr__( 'Transfer Status', 'woocommerce-customer-order-csv-export' ) . '">' . esc_attr__( 'Transfer Status', 'woocommerce-customer-order-csv-export' ) . '</span>',
			'export_type'     => esc_html__( 'Type', 'woocommerce-customer-order-csv-export' ),
			'output_type'     => esc_html__( 'Output', 'woocommerce-customer-order-csv-export' ),
			'invocation'      => esc_html__( 'Export name', 'woocommerce-customer-order-csv-export' ),
			'filename'        => esc_html__( 'File name', 'woocommerce-customer-order-csv-export' ),
			'export_date'     => esc_html__( 'Date', 'woocommerce-customer-order-csv-export' ),
			'file_actions'    => esc_html__( 'Actions', 'woocommerce-customer-order-csv-export' ),
		];

		$auto_exports_enabled = false;
		$automations          = \SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory::get_automations();

		foreach ( $automations as $key => $automation ) {

			if ( 'local' === $automation->get_method_type() ) {
				unset( $automations[ $key ] );
			}
		}

		if ( empty( $automations ) ) {
			unset( $columns['transfer_status'] );
		}

		/**
		 * Filters the columns in the export list table.
		 *
		 * @since 4.4.5
		 *
		 * @param array $columns the export list columns
		 * @param bool $auto_exports_enabled true if automated exports are enabled
		 */
		return apply_filters( 'wc_customer_order_export_admin_export_list_columns', $columns, $auto_exports_enabled );
	}


	/**
	 * Gets column content.
	 *
	 * @since 4.0.0
	 *
	 * @param object $export_job the export job object
	 * @param string $column_name the column name
	 * @return string the column content
	 */
	public function column_default( $export_job, $column_name ) {

		$export = wc_customer_order_csv_export_get_export( $export_job );

		if ( ! $export ) {
			return '';
		}

		switch ( $column_name ) {

			case 'export_status':

				$status = 'processing' === $export->get_status() && $export->is_batch_enabled() ? 'paused' : $export->get_status();

				$label = $this->statuses[ $status ];

				return sprintf( '<mark class="%1$s tips" data-tip="%2$s">%3$s</mark>', sanitize_key( $status ), $label, $label );

			break;

			case 'transfer_status':

				if ( ! $export->get_transfer_status() ) {

					return __( 'N/A', 'woocommerce-customer-order-csv-export' );

				} else {

					$label = $this->statuses[ $export->get_transfer_status() ];
					return sprintf( '<mark class="%1$s tips" data-tip="%2$s">%3$s</mark>', sanitize_key( $export->get_transfer_status() ), $label, $label );
				}

			break;

			case 'invocation':

				if ( 'auto' === $export->get_invocation() ) {

					if ( $export->get_automation_id() && $automation = \SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory::get_automation( $export->get_automation_id() ) ) {

						$url   = \SkyVerge\WooCommerce\CSV_Export\Admin\Automations::get_automation_edit_url( $automation->get_id() );
						$value = $url ? '<a href="' . esc_url( $url ) . '">' . esc_html( $automation->get_name() ) . '</a>' : $automation->get_name();

					} else {

						$value = __( 'Auto', 'woocommerce-customer-order-csv-export' );
					}

				} else {

					$value = esc_html__( 'Manual', 'woocommerce-customer-order-csv-export' );
				}

				return $value;

			break;

			case 'filename':

				return esc_html( $export->get_filename() );

			break;

			case 'export_type':

				if ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_ORDERS === $export->get_type() ) {

					return esc_html__( 'Orders', 'woocommerce-customer-order-csv-export' );

				} elseif ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_CUSTOMERS === $export->get_type() ) {

					return esc_html__( 'Customers', 'woocommerce-customer-order-csv-export' );

				} elseif ( WC_Customer_Order_CSV_Export::EXPORT_TYPE_COUPONS === $export->get_type() ) {

					return esc_html__( 'Coupons', 'woocommerce-customer-order-csv-export' );
				}
			break;

			case 'output_type':

				$output_types = wc_customer_order_csv_export()->get_output_types();

				return esc_html( ! empty( $output_types[ $export->get_output_type() ] ) ? $output_types[ $export->get_output_type() ] : $export->get_output_type() );

			break;

			case 'export_date':
				return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $export->get_created_at() ) );
			break;

			default:

				/**
				 * Allow actors adding custom columns to include their own column data.
				 *
				 * @since 4.4.5
				 *
				 * @param string $content the column content
				 * @param string $column_name the column name
				 * @param \stdClass $export the export job
				 */
				return apply_filters( 'wc_customer_order_export_admin_export_list_custom_column', '', $column_name, $export );
		}
	}


	/**
	 * Outputs actions column content for the given export.
	 *
	 * @since 4.0.0
	 *
	 * @param object $export export job object
	 */
	public function column_file_actions( $export ) {

		$export = wc_customer_order_csv_export_get_export( $export );

		if ( ! $export ) {
			return;
		}

		?><p>
			<?php
				$actions = [];

				if ( 'completed' === $export->get_status() ) {

					$download_url = wp_nonce_url( admin_url(), 'download-export' );

					$download_url = add_query_arg( [
						'download_exported_file' => 1,
						'export_id'              => $export->get_id(),
					], $download_url );

					$actions['download'] = [
						'url'    => $download_url,
						'name'   => esc_html__( 'Download', 'woocommerce-customer-order-csv-export' ),
						'action' => 'download'
					];

					if ( $export->get_automation_id() && $automation = \SkyVerge\WooCommerce\CSV_Export\Automations\Automation_Factory::get_automation( $export->get_automation_id() ) ) {

						$method = $automation->get_method_type();

						if ( 'local' !== $method ) {

							$label = wc_customer_order_csv_export()->get_methods_instance()->get_export_method_label( $method );

							$transfer_url = wp_nonce_url( admin_url(), 'transfer-export' );
							$transfer_url = add_query_arg( [
								'transfer_export' => 1,
								'export_id'       => $export->get_id(),
							], $transfer_url );

							$actions['transfer'] = [
								'url'    => $transfer_url,
								/* translators: Placeholders: %s - via [method], full example: Send via Email */
								'name'   => sprintf( esc_html__( 'Send %s', 'woocommerce-customer-order-csv-export' ), $label ),
								'action' => 'email' === $method ? 'email' : 'transfer',
							];
						}
					}

				} elseif ( 'processing' === $export->get_status() && $export->is_batch_enabled() ) {

					$actions['resume'] = [
						'name'   => __( 'Resume', 'woocommerce-customer-order-csv-export' ),
						'action' => 'resume',
						'url'    => '#',
					];
				}

				$delete_url = wp_nonce_url( admin_url(), 'delete-export' );
				$delete_url = add_query_arg( [
					'delete_csv_export' => 1,
					'export_id'         => $export->get_id(),
				], $delete_url );

				$done = in_array( $export->get_status(), [ 'completed', 'failed' ], true );

				$actions['delete'] = [
					'url'    => $delete_url,
					'name'   => $done ? esc_html__( 'Delete', 'woocommerce-customer-order-csv-export' ) : esc_html__( 'Cancel', 'woocommerce-customer-order-csv-export' ),
					'action' => $done ? 'delete' : 'cancel',
				];

				/**
				 * Allow actors to change the available actions for an export in Exports List
				 *
				 * @since 4.0.0
				 * @param array $actions
				 * @param stdClass $export
				 */
				$actions = apply_filters( 'wc_customer_order_export_admin_export_actions', $actions, $export );

				foreach ( $actions as $action ) {
					printf( '<a class="button tips %1$s" href="%2$s" data-tip="%3$s" data-export-id="%4$s">%5$s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $export->get_id() ), esc_attr( $action['name'] ) );
				}
			?>
		</p><?php

	}


	/**
	 * Handles the checkbox column output.
	 *
	 * @since 4.0.0
	 *
	 * @param object $export export job object
	 */
	public function column_cb( $export ) {

		$export = wc_customer_order_csv_export_get_export( $export );

		if ( ! $export ) {
			return;
		}

		if ( current_user_can( 'manage_woocommerce_csv_exports' ) ) : ?>
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
	 * @since 4.0.0
	 */
	public function prepare_items() {

		$this->_column_headers = [
			$this->get_columns(),
			[],
			$this->get_sortable_columns()
		];

		$exports = wc_customer_order_csv_export()->get_export_handler_instance()->get_exports();

		if ( ! empty( $exports ) ) {

			foreach ( $exports as $key => $export ) {

				$export = wc_customer_order_csv_export_get_export( $export );

				if ( ! $export ) {
					unset( $exports[ $key ] );
				}

				if ( $this->get_export_type() && $export->get_type() !== $this->get_export_type() ) {
					unset( $exports[ $key ] );
					continue;
				}

				if ( $this->get_method_type() && $export->get_transfer_method() !== $this->get_method_type() ) {
					unset( $exports[ $key ] );
					continue;
				}
			}

		} else {

			$exports = [];
		}

		$this->set_pagination_args( [
			'total_items' => count( $exports ),
			'per_page'    => $this->get_items_per_page( 'wc_customer_order_export_admin_exports_per_page' ),
		] );

		if ( $page_number = $this->get_pagenum() ) {

			$per_page = $this->get_pagination_arg( 'per_page' );

			$exports = array_splice( $exports, $per_page * ( $page_number - 1 ), $per_page );
		}

		$this->items = $exports;
	}


	/**
	 * The HTML to display when there are no exported files
	 *
	 * @see WP_List_Table::no_items()
	 * @since 4.0.0
	 */
	public function no_items() {
		?>
		<p><?php esc_html_e( 'Exported files will appear here. Files are stored for 14 days after the export.', 'woocommerce-shipwire' ); ?></p>
		<?php
	}


	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @since 4.0.0
	 * @return array
	 */
	protected function get_bulk_actions() {

		return [
			'delete' => esc_html__( 'Delete', 'woocommerce-customer-order-csv-export' ),
		];
	}


	/**
	 * Gets the specific export type to list.
	 *
	 * @since 5.0.0
	 *
	 * @return string|null
	 */
	private function get_export_type() {

		if ( null === $this->export_type && ! empty( $_POST['export_type'] ) ) {

			$export_types = wc_customer_order_csv_export()->get_export_types();

			if ( isset( $export_types[ $_POST['export_type'] ] ) ) {
				$this->export_type = $_POST['export_type'];
			}
		}

		return $this->export_type;
	}


	/**
	 * Gets the specific method type to list.
	 *
	 * @since 5.0.0
	 *
	 * @return string|null
	 */
	private function get_method_type() {

		if ( null === $this->method_type && ! empty( $_POST['method_type'] ) ) {

			$method_types   = wc_customer_order_csv_export()->get_methods_instance()->get_export_method_labels();
			$method_types['download'] = 'Manual';

			if ( isset( $method_types[ $_POST['method_type'] ] ) ) {
				$this->method_type = $_POST['method_type'];
			}
		}

		return $this->method_type;
	}

}
