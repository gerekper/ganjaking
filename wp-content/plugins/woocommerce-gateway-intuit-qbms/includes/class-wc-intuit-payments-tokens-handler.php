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
 * @package   WC-Intuit-Payments/Gateway/Payment-Tokens
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

/**
 * Handles the payment tokens.
 *
 * @see Framework\SV_WC_Payment_Gateway_Payment_Tokens_Handler
 *
 * @since 2.3.0
 */
class WC_Intuit_Payments_Tokens_Handler extends Framework\SV_WC_Payment_Gateway_Payment_Tokens_Handler {


	/**
	 * Gets the token editor instance.
	 *
	 * @since 2.3.0
	 *
	 * @return \WC_Intuit_Payments_Admin_Token_Editor
	 */
	public function get_token_editor() {

		return new WC_Intuit_Payments_Admin_Token_Editor( $this->get_gateway() );
	}
}
