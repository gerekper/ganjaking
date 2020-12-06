<?php
/**
 * WooCommerce Intuit Payments
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit Payments to newer
 * versions in the future. If you wish to customize WooCommerce Intuit Payments for your
 * needs please refer to http://docs.woothemes.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Intuit\Handlers;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_1 as Framework;

/**
 * Payment form handler.
 *
 * @since 2.7.4
 */
class Payment_Form extends Framework\SV_WC_Payment_Gateway_Payment_Form {


	/**
	 * Gets the JS handler class name.
	 *
	 * @since 2.7.4
	 */
	protected function get_js_handler_class_name() {

		return 'WC_Intuit_Payments_Payment_Form_Handler';
	}


}
