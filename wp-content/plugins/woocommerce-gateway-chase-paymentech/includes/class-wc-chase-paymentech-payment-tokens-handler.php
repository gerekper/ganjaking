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

use SkyVerge\WooCommerce\PluginFramework\v5_10_1 as Framework;

/**
 * Handle the payment token functionality.
 *
 * @since 1.6.0
 */
class WC_Chase_Paymentech_Payment_Tokens_Handler extends Framework\SV_WC_Payment_Gateway_Payment_Tokens_Handler {


	/**
	 * Returns true if the customer has selected to tokenize their payment
	 * method
	 *
	 * @since 1.6.0
	 * @return boolean true if the customer has selected to tokenize their payment method
	 */
	public function should_tokenize() {

		return 'yes' === Framework\SV_WC_Helper::get_posted_value( 'should_tokenize' );
	}


	/**
	 * Returns the tokenized payment method selected, if there is one.
	 *
	 * @since 1.13.0
	 *
	 * @return int|null returns the tokenized payment method selected
	 */
	public function tokenized_payment_method_selected() {

		$selected = Framework\SV_WC_Helper::get_posted_value( 'tokenized_payment_method_selected' );

		return '' === $selected ? null : (int) $selected;
	}


}
