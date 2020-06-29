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

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

/**
 * The payment token editor.
 *
 * @since 1.9.0
 * @see \SV_WC_Payment_Gateway_Payment_Token_Editor
 */
class WC_Intuit_QBMS_Payment_Token_Editor extends Framework\SV_WC_Payment_Gateway_Admin_Payment_Token_Editor {


	/**
	 * Get the editor fields.
	 *
	 * @since 1.9.0
	 * @return array
	 */
	protected function get_fields( $type = '' ) {

		$fields = parent::get_fields( $type );

		// These fields are returned by the API, so they don't need to be editable
		if ( 'credit-card' === $this->get_gateway()->get_payment_type() ) {

			$fields['card_type']['editable'] = false;
			$fields['expiry']['editable']    = false;
		}

		$fields['last_four']['editable'] = false;

		return $fields;
	}


}
