<?php
/**
 * WooCommerce Intuit QBMS
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
 * @package   WC-Intuit-QBMS/Gateway/Payment-Tokens
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_1 as Framework;

/**
 * Handle the payment tokens.
 *
 * @since 1.9.0
 * @see Framework\SV_WC_Payment_Gateway_Payment_Tokens_Handler
 */
class WC_Intuit_QBMS_Payment_Token_Handler extends Framework\SV_WC_Payment_Gateway_Payment_Tokens_Handler {


	/**
	 * A factory method to build and return an Intuit QBMS payment token object.
	 *
	 * @since 1.9.0
	 * @see Framework\SV_WC_Payment_Gateway_Payment_Tokens_Handler::build_token()
	 *
	 * @param string $token payment token
	 * @param array|Framework\SV_WC_Payment_Gateway_Payment_Token $data payment token data
	 * @return WC_Intuit_QBMS_Payment_Token payment token
	 */
	public function build_token( $token, $data ) {

		return new \WC_Intuit_QBMS_Payment_Token( $token, $data );
	}


	/**
	 * Get the token editor instance.
	 *
	 * @since 1.9.0
	 * @return \WC_Intuit_QBMS_Payment_Token_Editor
	 */
	public function get_token_editor() {

		return new WC_Intuit_QBMS_Payment_Token_Editor( $this->get_gateway() );
	}


	/**
	 * Gets the local attributes to be merged with remote token data.
	 *
	 * Ensures the billing hash doesn't get overwritten.
	 *
	 * @since 2.3.3
	 *
	 * @return array
	 */
	protected function get_merge_attributes() {

		$attributes = parent::get_merge_attributes();

		$attributes[] = 'billing_hash';

		return $attributes;
	}


}
