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
 * WC CSV Import Suite Import Exception class
 *
 * @since 3.0.0
 */
class WC_CSV_Import_Suite_Import_Exception extends Framework\SV_WC_Plugin_Exception {


	/** @var string sanitized error code */
	protected $error_code;


	/**
	 * Setup exception, requires 3 params:
	 *
	 * error code - machine-readable, e.g. `wc_csv_import_suite_invalid_product_id`
	 * error message - friendly message, e.g. 'Product ID is invalid'
	 * line import status - 'skipped' or 'failed'
	 *
	 * @since 3.0.0
	 * @param string $error_code
	 * @param string $error_message user-friendly translated error message
	 */
	public function __construct( $error_code, $error_message ) {
		$this->error_code = $error_code;
		parent::__construct( $error_message );
	}

	/**
	 * Returns the error code
	 *
	 * @since 3.0.0
	 * @return string
	 */
	public function getErrorCode() {
		return $this->error_code;
	}


}
