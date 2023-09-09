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
 * @copyright Copyright (c) 2013-2023, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_11_7 as Framework;

/**
 * Orbital Gateway API void response.
 *
 * Parses the response XML received from the Orbital Gateway API after performing
 * a void.
 *
 * @since 1.9.0
 * @see \WC_Orbital_Gateway_API_Response
 */
class WC_Orbital_Gateway_API_Void_Response extends WC_Orbital_Gateway_API_Response {


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
