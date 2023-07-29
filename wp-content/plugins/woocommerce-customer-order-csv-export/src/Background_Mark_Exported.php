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

namespace SkyVerge\WooCommerce\CSV_Export;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_6 as Framework;

/**
 * Customer/Order CSV Export Background Mark Exported
 *
 * Class that handles marking orders as exported in the background
 *
 * @since 5.0.8
 */
class Background_Mark_Exported extends Framework\SV_WP_Background_Job_Handler {


	/**
	 * Initializes the background export handler.
	 *
	 * @since 5.0.8
	 */
	public function __construct() {

		$this->prefix   = 'wc_customer_order_export';
		$this->action   = 'background_mark_exported';
		$this->data_key = 'object_ids';

		parent::__construct();
	}


	/**
	 * Processes a single item from the job.
	 *
	 * @internal
	 *
	 * @since 5.0.8
	 *
	 * @param mixed $item
	 * @param \stdClass $job related job instance
	 */
	public function process_item( $item, $job ) {

		$method           = $job->method;
		$output_type      = $job->output_type;
		$mark_as_exported = $job->mark_as_exported;
		$add_notes        = $job->add_notes;
		$automation_id    = $job->automation_id;

		wc_customer_order_csv_export()->get_export_handler_instance()->mark_order_as_exported( $item, $method, $output_type, $mark_as_exported, $add_notes, $automation_id );
	}


}
