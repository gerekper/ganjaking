<?php
/**
 * WooCommerce Chase Paymentech
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Chase Paymentech to newer
 * versions in the future. If you wish to customize WooCommerce Chase Paymentech for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-chase-paymentech/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_3 as Framework;

/**
 * Orbital Gateway API refund response.
 *
 * Parses the response XML received from the Orbital Gateway API after performing
 * a refund.
 *
 * @since 1.9.0
 * @see \WC_Orbital_Gateway_API_Response
 */
class WC_Orbital_Gateway_API_Refund_Response extends WC_Orbital_Gateway_API_Response {


	/**
	 * Determines if the refund was successful.
	 *
	 * @since 1.9.0
	 * @see \WC_Orbital_Gateway_API_Response::transaction_approved()
	 * @return bool
	 */
	public function transaction_approved() {

		return parent::transaction_approved() && '1' === $this->get_approval_status();
	}


	/**
	 * Gets the refund approval status.
	 *
	 * Possible values include:
	 *
	 * `0` - declined
	 * `1` - approved
	 * `2` - message/system error
	 *
	 * @since 1.9.0
	 * @return string
	 */
	public function get_approval_status() {

		return $this->get_element( 'ApprovalStatus' );
	}


	/**
	 * Gets the transaction index.
	 *
	 * Used to identify the unique components of transactions adjusted more than one time.
	 *
	 * @since 1.9.0
	 * @return string
	 */
	public function get_transaction_index() {

		return $this->get_element( 'TxRefIdx' );
	}


}
