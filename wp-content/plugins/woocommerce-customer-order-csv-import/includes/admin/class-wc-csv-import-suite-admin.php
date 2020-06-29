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

/**
 * WooCommerce CSV Import Suite Admin
 *
 * @since 3.0.0
 */
class WC_CSV_Import_Suite_Admin {


	/** @var string sub-menu page hook suffix */
	public $page;

	/** @var array tab IDs / titles */
	public $tabs;

	/** @var \SV_WP_Admin_Message_Handler instance */
	public $message_handler;

	/** @var string settings page name */
	protected $settings_page_name;


	/**
	 * Admin class constructor
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		if ( ! is_ajax() ) {
			$this->message_handler = wc_csv_import_suite()->get_message_handler();
		}

		// register importers
		add_action( 'admin_init', array( $this, 'register_importers' ) );

		// add the menu item
		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

		// load styles/scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// Load WC styles / scripts
		add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_styles_scripts' ) );

		// render any admin notices
		add_action( 'admin_notices', array( $this, 'add_admin_notices' ), 10 );

		// process bulk import deletion
		add_action( 'current_screen', array( $this, 'process_import_bulk_actions' ) );

		// filter hidden order item meta in edit order screen
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ) );

		if ( isset( $_GET['import_id'] ) && isset( $_GET['delete_csv_import'] ) ) {
			add_action( 'current_screen', array( $this, 'delete_import' ) );
		}

		// set the settings page name in case "WooCommerce" is translated.
		$this->settings_page_name = Framework\SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'csv_import_suite' );
	}


	/**
	 * Register the customer and order importers
	 *
	 * @since 3.0.0
	 */
	public function register_importers() {

		register_importer( 'woocommerce_customer_csv',
							'WooCommerce Customer (CSV)',
							__( 'Import <strong>customers</strong> to your store via a csv file.', 'woocommerce-csv-import-suite' ),
							array( $this, 'load_importer' ) );

		register_importer( 'woocommerce_order_csv',
							'WooCommerce Order (CSV)',
							__( 'Import <strong>orders</strong> to your store via a csv file.', 'woocommerce-csv-import-suite' ),
							array( $this, 'load_importer' ) );

		register_importer( 'woocommerce_coupon_csv',
							'WooCommerce Coupon (CSV)',
							__( 'Import <strong>coupons</strong> to your store via a csv file.', 'woocommerce-csv-import-suite' ),
							array( $this, 'load_importer' ) );

		// load importers early on action/POST requests, to support
		// `redirect after post` type pattern.
		// This allows processing action/POST requests before any output is sent to the
		// buffer and also using wp_redirect(). This, however, means that all the
		// importers _must_ use the `redirect after post` pattern.
		if ( isset( $_REQUEST['import'] ) && isset( $_REQUEST['action'] ) ) {
			$this->load_importer();
		}
	}


	/**
	 * Add a submenu item to the WooCommerce menu
	 *
	 * @since 3.0.0
	 */
	public function add_menu_link() {

		$menu_title = wc_csv_import_suite()->is_plugin_active( 'woocommerce-product-csv-import-suite.php' ) ? __( 'CSV Order Import Suite', 'woocommerce-csv-import-suite' ) : __( 'CSV Import Suite', 'woocommerce-csv-import-suite' );

		$this->page = add_submenu_page(
			'woocommerce',
			__( 'CSV Import Suite', 'woocommerce-csv-import-suite' ),
			$menu_title,
			'manage_woocommerce',
			wc_csv_import_suite()->get_id(),
			array( $this, 'render_submenu_pages' )
		);

	}


	/**
	 * Include admin scripts
	 *
	 * @since 3.0.0
	 */
	public function load_styles_scripts() {

		$css_loaded = false;

		// always load our CSS on the import list page
		if ( $this->settings_page_name === get_current_screen()->id && isset( $_GET['tab'] ) && 'import_list' === $_GET['tab'] ) {

			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
			wp_enqueue_style( 'wc-csv-import-suite-admin', wc_csv_import_suite()->get_plugin_url() . '/assets/css/admin/wc-csv-import-suite-admin.min.css', array( 'woocommerce_admin_styles' ), \WC_CSV_Import_Suite::VERSION );

			$css_loaded = true;
		}

		// bail out if not on import page
		if ( ! isset( $_GET['import'] ) ) {
			return;
		}

		// Bail out on unsupported importer
		if ( ! wc_csv_import_suite()->get_importers_instance()->get_importer( $_GET['import'] ) ) {
			return;
		}

		// Load flot on progress page only
		if ( isset( $_GET['job_id'] ) && $_GET['job_id'] ) {
			wp_enqueue_script( 'flot' );
			wp_enqueue_script( 'flot-pie' );
		}

		if ( ! $css_loaded ) {
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css' );
			wp_enqueue_style( 'wc-csv-import-suite-admin', wc_csv_import_suite()->get_plugin_url() . '/assets/css/admin/wc-csv-import-suite-admin.min.css', array( 'woocommerce_admin_styles' ), \WC_CSV_Import_Suite::VERSION );
		}

		wp_enqueue_script( 'wc-csv-import-suite-admin', wc_csv_import_suite()->get_plugin_url() . '/assets/js/admin/wc-csv-import-suite-admin.min.js', array( 'jquery', 'jquery-blockui' ), \WC_CSV_Import_Suite::VERSION );

		$background_import        = isset( $_GET['job_id'] ) ? wc_csv_import_suite()->get_background_import_instance()->get_job( $_GET['job_id'] ) : null;
		$unexpected_error_message = esc_html__( 'Something unexpected happened while importing. Your import may or may have not completed.', 'woocommerce-csv-import-suite' );

		if ( $background_import && isset( $background_import->options['debug_mode'] ) && $background_import->options['debug_mode'] ) {
			$unexpected_error_message .= ' ' . __( 'Please check your site error log for possible clues as to what may have happened.', 'woocommerce-csv-import-suite' );
		} else {
			$unexpected_error_message .= ' ' . __( 'Please try again with debug mode enabled to gain insight into what happened.', 'woocommerce-csv-import-suite' );
		}

		wp_localize_script( 'wc-csv-import-suite-admin', 'wc_csv_import_suite', array(
			'preview_nonce'  => wp_create_nonce( 'get-csv-preview' ),
			'progress_nonce' => wp_create_nonce( 'get-import-progress' ),
			'type'           => isset( $_GET['import'] ) ? esc_attr( $_GET['import'] ) : null,
			'i18n' => array(
				'show_details'             => esc_html__( 'View detailed results', 'woocommerce-csv-import-suite' ),
				'hide_details'             => esc_html__( 'Hide detailed results', 'woocommerce-csv-import-suite' ),
				'import_complete'          => esc_html__( 'Import complete.', 'woocommerce-csv-import-suite' ),
				'skipped_or_failed_lines'  => esc_html__( 'Some lines were skipped or failed to import. See below for details.', 'woocommerce-csv-import-suite' ),
				'unexpected_error_message' => $unexpected_error_message,
			),
		) );

	}


	/**
	 * Add settings/export screen ID to the list of pages for WC to load its CSS/JS on
	 *
	 * @since 3.2.0
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_styles_scripts( $screen_ids ) {

		$screen_ids[] = $this->settings_page_name;
		return $screen_ids;
	}


	/**
	 * Add import finished notices for the current user
	 *
	 * @since 3.1.0
	 */
	public function add_admin_notices() {

		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return;
		}

		$import_notices   = get_user_meta( $user_id, '_wc_csv_import_suite_notices', true );
		$is_import_screen = isset( $_GET['import'] ) && wc_csv_import_suite()->get_importers_instance()->get_importer( $_GET['import'] );

		if ( ! empty( $import_notices ) ) {

			foreach ( $import_notices as $import_id ) {

				$message_id                      = "wc_csv_import_suite_finished_{$import_id}";
				$is_current_import_progress_page = isset( $_GET['job_id'] ) && $_GET['job_id'] === $import_id;

				if ( $is_current_import_progress_page || wc_csv_import_suite()->get_admin_notice_handler()->is_notice_dismissed( $message_id, $user_id ) ) {

					// dismiss any completed/finished notices about the current import
					wc_csv_import_suite()->remove_import_finished_notice( $import_id, $user_id );

				} elseif ( ! $is_import_screen ) {

					// only display notices when not on any import screen, as to reduce confusion & clutter
					list( $message, $notice_class ) = $this->get_import_finished_message( $import_id );

					// if no message was returned, dismiss the message - this can happen when the import was manually deleted from the database
					if ( ! $message ) {
						wc_csv_import_suite()->remove_import_finished_notice( $import_id, $user_id );
						continue;
					}

					wc_csv_import_suite()->get_admin_notice_handler()->add_admin_notice( $message, $message_id, array( 'always_show_on_settings' => false, 'notice_class' => $notice_class ) );
				}
			}
		}
	}


	/**
	 * Get import finished message
	 *
	 * @since 3.1.0
	 * @param string $import_id
	 * @return string|array
	 */
	private function get_import_finished_message( $import_id ) {

		$import = wc_csv_import_suite()->get_background_import_instance()->get_job( $import_id );

		if ( ! $import ) {
			return '';
		}

		$filename            = basename( $import->file_path );
		$import_progress_url = admin_url( 'admin.php?import=' . $import->type . '&job_id=' . urlencode( $import->id ) );

		if ( 'completed' === $import->status ) {

			if ( $import->options['dry_run'] ) {
				/* translators: Placeholders: %s - file name */
				$message = sprintf( __( 'Dry run of file %s is complete!', 'woocommerce-csv-import-suite' ), $filename );
			} else {
				/* translators: Placeholders: %s - file name */
				$message = sprintf( __( 'Importing file %s is complete!', 'woocommerce-csv-import-suite' ), $filename );
			}

			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			$message  .= ' ' . sprintf( __( 'You can see the import results in the %1$simport progress page%2$s', 'woocommerce-csv-import-suite' ), '<a href="' . $import_progress_url . '">', '</a>' );

			if ( $import->options['dry_run'] ) {
				$message .= __( ', then run your live import', 'woocommerce-csv-import-suite' );
			}

			// finally, full stop this bad boy
			$message .= '.';

			$notice_class = 'updated';

		} elseif ( 'failed' === $import->status ) {

			if ( $import->options['dry_run'] ) {
				/* translators: Placeholders: %s - file name */
				$message = sprintf( __( 'Dry run of file %s failed.', 'woocommerce-csv-import-suite' ), $filename );
			} else {
				/* translators: Placeholders: %s - file name */
				$message = sprintf( __( 'Importing file %s failed.', 'woocommerce-csv-import-suite' ), $filename );
			}

			/* translators: Placeholders: %1$s - opening <a> tag, %2$s - closing </a> tag */
			$message .= ' ' . sprintf( __( 'Additional details may be found in the CSV Import %1$slogs%2$s.', 'woocommerce-csv-import-suite' ), '<a href="' . admin_url( 'admin.php?page=wc-status&tab=logs' ) . '">', '</a>' );

			$notice_class = 'error';

		}

		if ( ! isset( $message ) || ! $message ) {
			return '';
		}

		return array( $message, $notice_class );
	}


	/**
	 * Render the sub-menu page for 'CSV Import Suite'
	 *
	 * @since 3.2.0
	 */
	public function render_submenu_pages() {
		global $current_tab;

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$this->tabs = array(
			'import'         => __( 'Import', 'woocommerce-csv-import-suite' ),
			'import_list'    => __( 'Import List', 'woocommerce-csv-import-suite' ),
		);

		$current_tab = empty( $_GET[ 'tab' ] ) ? 'import' : sanitize_title( $_GET[ 'tab' ] );

		?>
		<div class="wrap woocommerce">
			<form method="post" id="mainform" action="" enctype="multipart/form-data">
				<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
					<?php
					foreach ( $this->tabs as $tab_id => $tab_title ) :
						$class = ( $tab_id === $current_tab ) ? array( 'nav-tab', 'nav-tab-active' ) : array( 'nav-tab' );
						$url   = add_query_arg( 'tab', $tab_id, admin_url( 'admin.php?page=csv_import_suite' ) );
						printf( '<a href="%1$s" class="%2$s">%3$s</a>', esc_url( $url ), implode( ' ', array_map( 'sanitize_html_class', $class ) ), esc_html( $tab_title ) );
					endforeach;
					?> </h2> <?php

				$this->message_handler->show_messages();

				if ( 'import_list' === $current_tab ) {
					$this->render_import_list_page();
				} else {
					$this->render_import_screen();
				}
				?> </form>
		</div> <?php
	}


	/**
	 * Render the admin page which includes links to the documentation,
	 * sample import files, and buttons to perform the imports
	 *
	 * @since 3.0.0
	 */
	public function render_import_screen() {

		$import_progress_url = $this->get_progress_url_for_current_import();

		include( 'views/html-import-screen.php' );
	}


	/**
	 * Show import list page
	 *
	 * @since 3.2.0
	 */
	private function render_import_list_page() {

		// permissions check
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// instantiate extended list table
		$import_list_table = $this->get_import_list_table();

		// prepare and display the list table
		$import_list_table->prepare_items();
		$import_list_table->display();
	}


	/**
	 * Get an instance of WC_CSV_Import_Suite_List_Table
	 *
	 * @since 3.2.0
	 * @return \WC_CSV_Import_Suite_List_Table
	 */
	private function get_import_list_table() {
		return wc_csv_import_suite()->load_class( '/includes/admin/class-wc-csv-import-suite-list-table.php', 'WC_CSV_Import_Suite_List_Table' );
	}


	/**
	 * Process imported files bulk actions
	 *
	 * Note this is hooked into `current_screen` as WC 2.1+ interferes with sending
	 * headers() from a sub-menu page, and `admin_init` is too early to detect current
	 * screen.
	 *
	 * @since 3.2.0
	 */
	public function process_import_bulk_actions() {

		if ( $this->settings_page_name !== get_current_screen()->id ) {
			return;
		}

		$import_list_table = $this->get_import_list_table();
		$action            = $import_list_table->current_action();

		if ( ! $action ) {
			return;
		}

		check_admin_referer( 'bulk-imports' );

		$sendback = wp_get_referer();

		if ( ! $sendback ) {
			$sendback = admin_url( 'admin.php?page=csv_import_suite&tab=import_list' );
		}

		$pagenum  = $import_list_table->get_pagenum();

		if ( $pagenum > 1 ) {
			$sendback = add_query_arg( 'paged', $pagenum, $sendback );
		}

		if ( 'delete' === $action ) {
			if ( empty( $_POST['import'] ) ) {
				return;
			}

			$import_ids        = (array) $_POST['import'];
			$background_import = wc_csv_import_suite()->get_background_import_instance();

			foreach ( $import_ids as $import_id ) {
				$background_import->delete_job( $import_id );
			}

			$num_deleted = count( $import_ids );

			$this->message_handler->add_message( sprintf( _n( '%d import job deleted.',  '%d import jobs deleted.', 'woocommerce-csv-import-suite', $num_deleted ), $num_deleted ) );

			wp_redirect( $sendback );

			exit();
		}
	}


	/**
	 * Delete an imported file
	 *
	 * @since 3.2.0
	 */
	public function delete_import() {

		check_admin_referer( 'delete-import' );

		$sendback = wp_get_referer();

		if ( ! $sendback ) {
			$sendback = admin_url( 'admin.php?page=csv_import_suite&tab=import_list' );
		}

		$import_id = wc_clean( urldecode( $_GET['import_id'] ) );

		$background_import = wc_csv_import_suite()->get_background_import_instance();
		$import            = $background_import->get_job( $import_id );

		if ( ! $import ) {
			wp_safe_redirect( $sendback );
		}

		switch( $import->status ) {

			case 'queued':
				$message = __( 'Import removed from queue.', 'woocommerce-csv-import-suite' );
			break;

			case 'processing':

				if ( $import->options['dry_run'] ) {
					/* translators: Placeholders: %s - filename */
					$message = sprintf( __( 'Dry run for %s import stopped and deleted.', 'woocommerce-csv-import-suite' ), pathinfo( basename( $import->file_path ), PATHINFO_FILENAME ) );
				} else {
					$message = __( 'Import stopped and deleted. Data already updated or inserted by the import has not been removed.', 'woocommerce-csv-import-suite' );
				}

			break;

			default:

				if ( $import->options['dry_run'] ) {
					$message = __( 'Information for this dry run has been deleted.', 'woocommerce-csv-import-suite' );
				} else {
					$message = __( 'Import job deleted. Data imported or updated by this job has not been removed.', 'woocommerce-csv-import-suite' );
				}

			break;
		}

		// be sure we properly remove messages before deleting
		$background_import->handle_job_delete( $import );
		$background_import->delete_job( $import_id );

		$this->message_handler->add_message( $message );

		wp_redirect( $sendback );

		exit();
	}


	/**
	 * Load an importer and start processing the import queue
	 *
	 * @since 3.0.0
	 */
	public function load_importer() {

		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			return;
		}

		$type     = isset( $_REQUEST['import'] ) ? esc_attr( $_REQUEST['import'] ) : null;
		$importer = wc_csv_import_suite()->get_importers_instance()->get_importer( $type );

		if ( $importer ) {
			$importer->dispatch();
		}
	}


	/**
	 * Hide _original_order_item_id meta on edit order screen
	 *
	 * @since 3.0.0
	 * @param array $hidden
	 * @return array
	 */
	public function hidden_order_itemmeta( $hidden ) {

		$hidden[] = '_original_order_item_id';

		return $hidden;
	}


	/**
	 * If there are jobs in process, return the URL for the progress screen.
	 *
	 * @since 3.1.1
	 * @return string progress screen URL if jobs are processing / queued
	 */
	public function get_progress_url_for_current_import() {

		// get any jobs that haven't completed or failed yet
		$args = array(
			'status' => array( 'queued', 'processing' ),
		);

		$imports      = wc_csv_import_suite()->get_background_import_instance()->get_jobs( $args );
		$progress_url = '';

		if ( empty( $imports ) ) {
			return $progress_url;
		}

		// we don't break the foreach here since we want to prefer processing jobs if they exist,
		// so we check all jobs, but set the progress URL for the first queued job in case none are processing
		foreach ( $imports as $import ) {

			// direct users to the processing jobs if available
			if ( 'processing' === $import->status ) {
				return admin_url( 'admin.php?import=' . $import->type . '&job_id=' . urlencode( $import->id ) . '&block_new_import=yes' );
			}

			// otherwise we'll use the progress screen for the first queued import instead
			elseif ( empty( $progress_url ) ) {
				$progress_url = admin_url( 'admin.php?import=' . $import->type . '&job_id=' . urlencode( $import->id ) . '&block_new_import=yes' );
			}

		}

		return $progress_url;
	}


}
