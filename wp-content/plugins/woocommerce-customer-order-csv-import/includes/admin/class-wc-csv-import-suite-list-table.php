<?php
/**
 * WooCommerce Customer/Order/Coupon CSV Import Suite
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order/Coupon CSV Import Suite to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order/Coupon CSV Import Suite for your
 * needs please refer to http://docs.woocommerce.com/document/customer-order-csv-import-suite/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2020, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Customer/Coupon/Order CSV Import List Table
 *
 * Lists recently imported files
 *
 * @since 3.2.0
 */
class WC_CSV_Import_Suite_List_Table extends \WP_List_Table {


	/** @var array associative array of translated import status labels */
	private $statuses;


	/**
	 * Constructor - setup list table
	 *
	 * @since 3.2.0
	 * @param array $args
	 */
	public function __construct( $args = array() ) {

		parent::__construct( array(
			'singular' => 'import',
			'plural'   => 'imports',
			'ajax'     => false
		) );

		$this->statuses = array(
			'queued'     => esc_html__( 'Queued', 'woocommerce-csv-import-suite' ),
			'processing' => esc_html__( 'Processing', 'woocommerce-csv-import-suite' ),
			'completed'  => esc_html__( 'Completed', 'woocommerce-csv-import-suite' ),
			'failed'     => esc_html__( 'Failed', 'woocommerce-csv-import-suite' ),
		);
	}


	/**
	 * Set column titles
	 *
	 * @since 3.2.0
	 * @return array
	 */
	public function get_columns() {

		$columns = array(
			'cb'              => '<input type="checkbox" />',
			'import_status'   => '<span class="status_head tips" data-tip="' . esc_attr__( 'Import Status', 'woocommerce-csv-import-suite' ) . '">' . esc_attr__( 'Import Status', 'woocommerce-csv-import-suite' ) . '</span>',
			'import_type'     => esc_html__( 'Type', 'woocommerce-csv-import-suite' ),
			'run_type'        => esc_html__( 'Run Type', 'woocommerce-csv-import-suite' ),
			'filename'        => esc_html__( 'File name', 'woocommerce-csv-import-suite' ),
			'import_date'     => esc_html__( 'Start Date', 'woocommerce-csv-import-suite' ),
			'file_actions'    => esc_html__( 'Actions', 'woocommerce-csv-import-suite' ),
		);

		return $columns;
	}


	/**
	 * Get column content
	 *
	 * @since 3.2.0
	 * @param stdClass $import
	 * @param string $column_name
	 * @return string column content
	 */
	public function column_default( $import, $column_name ) {

		switch ( $column_name ) {

			case 'import_status':

				$label = $this->statuses[ $import->status ];
				return sprintf( '<mark class="%1$s tips" data-tip="%2$s">%3$s</mark>', sanitize_key( $import->status ), $label, $label );

			break;

			case 'import_type':

				if ( 'woocommerce_order_csv' === $import->type ) {

					return esc_html__( 'Orders', 'woocommerce-csv-import-suite' );

				} elseif ( 'woocommerce_customer_csv' === $import->type ) {

					return esc_html__( 'Customers', 'woocommerce-csv-import-suite' );

				} elseif ( 'woocommerce_coupon_csv' === $import->type ) {

					return esc_html__( 'Coupons', 'woocommerce-csv-import-suite' );
				}

			break;

			case 'run_type':

				return $import->options['dry_run'] ? esc_html__( 'Dry', 'woocommerce-csv-import-suite' ) : esc_html__( 'Live', 'woocommerce-csv-import-suite' );

			break;

			case 'filename':

				$filename = basename( $import->file_path );

				// strip the .txt ending from the filename, which is added by WP on upload
				$filename = pathinfo( $filename, PATHINFO_FILENAME );

				return $filename;

			break;

			case 'import_date':
				return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $import->created_at ) );
			break;
		}
	}


	/**
	 * Output actions column content for the given import
	 *
	 * @since 3.2.0
	 * @param stdClass $import
	 */
	public function column_file_actions( $import ) {

		?><p>
			<?php
				$actions = array();

				// No point in viewing jobs with no progress
				if ( ! in_array( $import->status, array( 'failed', 'queued' ), true ) ) {

					$view_url = add_query_arg( array(
						'import' => $import->type,
						'job_id' => urlencode( $import->id ),
					), admin_url() );

					$actions['view'] = array(
						'url'    => $view_url,
						'name'   => esc_html__( 'View', 'woocommerce-csv-import-suite' ),
						'action' => 'view',
					);
				}

				$delete_url = wp_nonce_url( admin_url(), 'delete-import' );
				$delete_url = add_query_arg( array(
					'delete_csv_import' => 1,
					'import_id'         => urlencode( $import->id ),
				), $delete_url );

				$done = in_array( $import->status, array( 'completed', 'failed' ), true );

				$actions['delete'] = array(
					'url'    => $delete_url,
					'name'   => $done ? esc_html__( 'Delete', 'woocommerce-csv-import-suite' ) : esc_html__( 'Stop', 'woocommerce-csv-import-suite' ),
					'action' => $done ? 'delete' : 'stop',
				);

				/**
				 * Allow actors to change the available actions for an import in Imports List
				 *
				 * @since 3.2.0
				 * @param array $actions
				 * @param stdClass $import
				 */
				$actions = apply_filters( 'wc_csv_import_suite_import_list_actions', $actions, $import );

				foreach ( $actions as $action ) {
					printf( '<a class="button tips %1$s" href="%2$s" data-tip="%3$s">%4$s</a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_attr( $action['name'] ) );
				}
			?>
		</p><?php

	}


	 /**
	  * Handles the checkbox column output.
	  *
	  * @since 3.2.0
	  * @param stdClass $import
	  */
	 public function column_cb( $import ) {

		 if ( current_user_can( 'manage_woocommerce' ) ) : ?>

			 <label class="screen-reader-text" for="cb-select-<?php echo sanitize_html_class( $import->id ); ?>"><?php esc_html_e( 'Select import', 'woocommerce-csv-import-suite' ); ?></label>
			 <input id="cb-select-<?php echo sanitize_html_class( $import->id ); ?>" type="checkbox" name="import[]" value="<?php echo esc_attr( $import->id ); ?>" />
			 <div class="locked-indicator"></div>

		 <?php endif;
	 }


	/**
	 * Prepare imported files for display
	 *
	 * @since 3.2.0
	 */
	public function prepare_items() {

		// set column headers manually, see https://codex.wordpress.org/Class_Reference/WP_List_Table#Extended_Properties
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->items = wc_csv_import_suite()->get_background_import_instance()->get_jobs();
	}


	/**
	 * The HTML to display when there are no imported files
	 *
	 * @see \WP_List_Table::no_items()
	 * @since 3.2.0
	 */
	public function no_items() {
		?>
		<p><?php esc_html_e( 'Imported files will appear here. Files are stored for 14 days after the import.', 'woocommerce-csv-import-suite' ); ?></p>
		<?php
	}


	 /**
	  * Get an associative array ( option_name => option_title ) with the list
	  * of bulk actions available on this table.
	  *
	  * @since 3.2.0
	  * @return array
	  */
	 protected function get_bulk_actions() {

		 return array(
			 'delete' => esc_html__( 'Delete', 'woocommerce-csv-import-suite' ),
		 );
	 }


}
