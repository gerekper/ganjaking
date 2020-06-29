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

namespace SkyVerge\WooCommerce\CSV_Import_Suite;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Plugin lifecycle handler.
 *
 * @since 3.6.0
 *
 * @method \WC_CSV_Import_Suite get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 3.6.0
	 *
	 * @param \WC_CSV_Import_Suite $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'3.4.0',
			'3.8.3',
		];
	}


	/**
	 * Create files and directories.
	 *
	 * @since 3.6.0
	 */
	private function create_files() {

		// install files and folders for exported files and prevent hotlinking
		$upload_dir      = wp_upload_dir();
		$download_method = get_option( 'woocommerce_file_download_method', 'force' );

		$files = [
			[
				'base'    => $upload_dir['basedir'] . '/csv_imports',
				'file'    => 'index.html',
				'content' => ''
			],
		];

		if ( 'redirect' !== $download_method ) {
			$files[] = [
				'base'    => $upload_dir['basedir'] . '/csv_imports',
				'file'    => '.htaccess',
				'content' => 'deny from all'
			];
		}

		foreach ( $files as $file ) {

			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {

				$file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' );

				if ( $file_handle ) {

					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}


	/**
	 * Performs installation tasks.
	 *
	 * @since 3.6.0
	 */
	protected function install() {

		// set up csv imports folder
		$this->create_files();
	}


	/**
	 * Runs version upgrade tasks.
	 *
	 * @since 3.6.0
	 *
	 * @param string $installed_version
	 */
	protected function upgrade( $installed_version ) {

		// forces logging enabled so we can record upgrade messages
		add_filter( 'wc_csv_import_suite_logging_enabled', [ $this, 'enable_logging' ], 999 );

		parent::upgrade( $installed_version );

		// restores the normal logging behavior
		remove_filter( 'wc_csv_import_suite_logging_enabled', [ $this, 'enable_logging'], 999 );
	}


	/**
	 * Forces enable logging (callback method).
	 *
	 * @internal
	 *
	 * @since 3.6.0
	 *
	 * @return true
	 */
	public function enable_logging() {

		return true;
	}


	/**
	 * Updates plugin to v3.4.0
	 *
	 * @since 3.6.0
	 */
	protected function upgrade_to_3_4_0() {

		// set up csv imports folder
		$this->create_files();
	}


	/**
	 * Updates plugin to v3.8.3
	 *
	 * @since 3.8.3
	 */
	protected function upgrade_to_3_8_3() {

		if ( $handler = $this->get_plugin()->get_background_fix_coupons_usage_count_instance() ) {

			// create_job() expects an non-empty array of attributes
			$handler->create_job( [ 'created_at' => current_time( 'mysql' ) ] );
			$handler->dispatch();
		}
	}


}
