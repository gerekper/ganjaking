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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit QBMS to newer
 * versions in the future. If you wish to customize WooCommerce Intuit QBMS for your
 * needs please refer to https://docs.woocommerce.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/API
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_4 as Framework;

/**
 * The eCheck payment class.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_API_eCheck_Request extends WC_Intuit_Payments_API_Payment_Request {


	/**
	 * Constructs the class.
	 *
	 * @since 2.0.0
	 * @param \WC_Order $order the order object
	 */
	public function __construct( \WC_Order $order ) {

		$this->set_type( 'echecks' );

		$this->set_tokenized_method_key( 'bankAccountOnFile' );

		parent::__construct( $order );
	}


	/**
	 * Sets the data for an eCheck payment.
	 *
	 * @since 2.0.0
	 */
	public function set_echeck_data() {

		$this->set_payment_data();

		$this->data['paymentMode'] = 'WEB';

		// eChecks use the order amount for test cases
		if ( $test_amount = $this->get_order()->payment->test_case ) {
			$this->data['amount'] = (float) $test_amount;
		}
	}


}
