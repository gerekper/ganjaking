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
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_12 as Framework;

/**
 * The payment method request class.
 *
 * This handles CRUD operations for stored payment methods. Handling for both
 * credit cards and echecks are pretty much the same, so no need for an
 * overriding class for each like we do with transactions.
 *
 * @since 2.0.0
 */
class WC_Intuit_Payments_API_Payment_Method_Request extends WC_Intuit_Payments_API_Request {


	/** @var string the customer ID */
	protected $customer_id;

	/** @var string the type, either 'cards' or 'bank-accounts' */
	protected $type;


	/**
	 * Constructs the class.
	 *
	 * @since 2.0.0
	 */
	public function __construct( $customer_id, $type ) {

		$this->method = 'GET';

		$this->customer_id = $customer_id;
		$this->type        = 'credit_card' === $type ? 'cards' : 'bank-accounts';

		$this->path = '/customers/' . $this->get_customer_id() . '/' . $this->get_type();
	}


	/**
	 * Sets the data for storing a payment token.
	 *
	 * @since 2.0.0
	 * @param string $token the token to store
	 */
	public function set_create_method_data( $token ) {

		$this->path .= '/createFromToken';

		$this->method = 'POST';

		$this->data['value'] = $token;
	}


	/**
	 * Sets the data for deleting a payment method.
	 *
	 * @since 2.0.0
	 * @param string $id the payment method ID to delete
	 */
	public function set_delete_method_data( $id ) {

		$this->path .= '/' . $id;

		$this->method = 'DELETE';
	}


	/**
	 * Gets the customer ID for this request.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_customer_id() {

		return $this->customer_id;
	}


	/**
	 * Gets the payment type.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function get_type() {

		return $this->type;
	}


}
