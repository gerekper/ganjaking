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
 * Helper for gateway response codes & messages.
 *
 * @since 1.9.0
 */
class WC_Chase_Paymentech_Response_Message_Helper extends Framework\SV_WC_Payment_Gateway_API_Response_Message_Helper {


	/** @var array the known response codes and their cooresponding message IDs */
	protected $known_codes = array(
		'05'  => 'card_declined',
		'17'  => 'decline',
		'43'  => 'card_declined',
		'14'  => 'card_number_invalid',
		'54'  => 'card_expiry_invalid',
	);


	/**
	 * Gets the appropriate message IDs for the given codes.
	 *
	 * @since 1.9.0
	 * @param array $codes the error codes
	 * @return array
	 */
	public function get_message_ids( $codes ) {

		$message_ids = array();

		if ( ! empty( $codes ) ) {
			$message_ids = array_intersect_key( $this->known_codes, array_flip( $codes ) );
		}

		return array_values( $message_ids );
	}


}
