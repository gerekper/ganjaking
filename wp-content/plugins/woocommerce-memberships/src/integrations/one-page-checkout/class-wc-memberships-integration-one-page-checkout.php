<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for WooCommerce One Page Checkout.
 *
 * @since 1.10.6
 */
class WC_Memberships_Integration_One_Page_Checkout {


	/**
	 * Hooks One Page Checkout to disable guest checkout when buying a product that grants access.
	 *
	 * @see \PP_One_Page_Checkout::init()
	 *
	 * @since 1.10.6
	 */
	public function __construct() {

		// filters at priority 11 will run right after One Page Checkout ones (priority 10)
		add_filter( 'woocommerce_get_script_data', [ $this, 'maybe_force_registration' ], 11, 2 );
	}


	/**
	 * Filters One Page Checkout options to force guest user registration if checkout contains a product that grants access.
	 *
	 * @see \PP_One_Page_Checkout::filter_woocommerce_script_paramaters()
	 *
	 * @internal
	 *
	 * @since 1.10.6
	 *
	 * @param array $params WooCommerce script parameters
	 * @param string $handle current screen (WC 3.3+)
	 * @return array
	 */
	public function maybe_force_registration( $params, $handle = '' ) {

		if ( $frontend = wc_memberships()->get_frontend_instance() ) {

			$filter = current_filter();

			if (      in_array( $filter, array( 'woocommerce_params', 'wc_checkout_params' ), true )
			     || ( in_array( $handle, array( 'woocommerce', 'wc-checkout' ), true ) && 'woocommerce_get_script_data' === $filter ) ) {

				$checkout = $frontend->get_checkout_instance();

				if (    $checkout
				     && \PP_One_Page_Checkout::is_any_form_of_opc_page()
				     && $checkout->force_registration() ) {

					$params['wcopc_option_guest_checkout'] = 'no';
					$params['option_guest_checkout']       = 'no';
				}
			}
		}

		return $params;
	}


}
