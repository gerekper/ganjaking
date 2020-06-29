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
 * Background job handler to add usage_count meta to coupons that were created without a usage_count value.
 *
 * @since 3.8.3
 */
class Background_Fix_Coupons_Usage_Count extends Framework\SV_WP_Background_Job_Handler {


	/**
	 * Background job constructor.
	 *
	 * @since 3.8.3
	 */
	public function __construct() {

		$this->prefix = 'wc_csv_import_suite';
		$this->action = 'background_fix_coupons_usage_count';

		parent::__construct();
	}


	/**
	 * Processes job.
	 *
	 * This job continues to insert coupons meta data until we run out of memory or exceed the time limit.
	 * There is no list of items to loop over.
	 *
	 * @since 3.8.3
	 *
	 * @param object $job
	 * @param int $items_per_batch number of items to process in a single request. Defaults to unlimited.
	 * @return object
	 */
	public function process_job( $job, $items_per_batch = null ) {

		if ( ! isset( $job->total ) ) {
			$job->total = $this->count_remaining_coupons();
		}

		if ( ! isset( $job->progress ) ) {
			$job->progress = 0;
		}

		$remaining_coupons = $job->total;
		$processed_coupons = 0;

		// fix coupon's usage count until memory or time limit is exceeded
		while ( $processed_coupons < $remaining_coupons ) {

			$rows_inserted = $this->fix_coupons_usage_count();

			$processed_coupons += $rows_inserted;
			$job->progress     += $rows_inserted;

			// update job progress
			$job = $this->update_job( $job );

			// memory or time limit reached
			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				break;
			}
		}

		// job complete! :)
		if ( $this->count_remaining_coupons() === 0 ) {

			update_option( 'wc_csv_import_suite_coupons_usage_count_fixed', 'yes' );

			$this->complete_job( $job );
		}

		return $job;
	}


	/**
	 * Counts the number of coupons that don't have usage_count meta data defined.
	 *
	 * @since 3.8.3
	 *
	 * @return bool
	 */
	private function count_remaining_coupons() {
		global $wpdb;

		$sql = "
			SELECT COUNT( posts.ID )
			FROM {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS meta ON ( posts.ID = meta.post_id AND meta.meta_key = 'usage_count' )
			WHERE posts.post_type = 'shop_coupon' AND meta.meta_id IS NULL
		";

		return (int) $wpdb->get_var( $sql );
	}


	/**
	 * Inserts rows into the postmeta table to define usage_count for coupons that don't have a value defined.
	 *
	 * @since 3.8.3
	 *
	 * @return int
	 */
	private function fix_coupons_usage_count() {
		global $wpdb;

		$sql = "
			INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value)
				SELECT posts.ID, 'usage_count', 0
				FROM {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS meta ON ( posts.ID = meta.post_id AND meta.meta_key = 'usage_count' )
				WHERE posts.post_type = 'shop_coupon' AND meta.meta_id IS NULL
				LIMIT 1000
		";

		$rows_inserted = $wpdb->query( $sql );

		if ( false === $rows_inserted ) {

			$message = sprintf( 'There was an error trying to update coupons meta data. %s', $wpdb->last_error );

			wc_csv_import_suite()->log( $message );
		}

		return (int) $rows_inserted;
	}


	/**
	 * No-op
	 *
	 * @since 3.8.3
	 */
	protected function process_item( $item, $job ) {
		// void
	}


}
