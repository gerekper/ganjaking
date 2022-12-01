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
 * Checkout handler.
 *
 * Mainly handles forcing account creation or login when purchasing a product that grants access to a membership.*
 *
 * Inspired from the similar checkout code in WC Subscriptions, thanks Prospress :)
 *
 * @since 1.0.0
 */
class WC_Memberships_Checkout {


	/**
	 * Checkout handler constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// users must be able to register on checkout
		add_filter( 'woocommerce_checkout_registration_enabled',  [ $this, 'maybe_enable_registration'  ], 9999 );
		add_filter( 'woocommerce_checkout_registration_required', [ $this, 'maybe_require_registration' ], 9999 );

		// mark checkout registration fields as required
		add_filter( 'woocommerce_checkout_fields', [ $this, 'maybe_require_registration_fields' ], 9999 );

		// remove guest checkout param from WC checkout JS
		add_filter( 'woocommerce_get_script_data', [ $this, 'remove_guest_checkout_js_param' ] );

		// force registration during checkout process
		add_action( 'woocommerce_before_checkout_process', [ $this, 'maybe_force_registration_during_checkout' ], 9999 );
	}


	/**
	 * Enables user registration at checkout, if the shopping cart contains access granting products.
	 *
	 * @internal callback:
	 * @see \WC_Checkout::is_registration_enabled()
	 *
	 * @since 1.0.0
	 *
	 * @param bool $enable_registration whether to enable registration at checkout
	 * @return bool
	 */
	public function maybe_enable_registration( $enable_registration ) {

		return $this->force_registration() ? true : $enable_registration;
	}


	/**
	 * Requires user registration at checkout, if the shopping cart contains access granting products.
	 *
	 * @internal callback:
	 * @see \WC_Checkout::is_registration_required()
	 *
	 * @since 1.20.0
	 *
	 * @param bool $require_registration whether to require registration at checkout
	 * @return bool
	 */
	public function maybe_require_registration( $require_registration ) {

		return $this->force_registration() ? true : $require_registration;
	}


	/**
	 * Restores the original checkout registration settings after checkout has loaded
	 *
	 * TODO remove this method by version 2.0.0 or April 2022, whichever comes first {FN 2020-12-24}
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @deprecated 1.20.0
	 */
	public function restore_registration_settings() {

		wc_deprecated_function( __METHOD__, '1.20.0' );
	}


	/**
	 * Marks account fields as required.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields associative array
	 * @return array
	 */
	public function maybe_require_registration_fields( $fields = [] ) {

		if ( is_array( $fields ) && $this->force_registration() ) {

			foreach ( [ 'account_username', 'account_password', 'account_password-2' ] as $field ) {

				if ( isset( $fields['account'][ $field ] ) ) {

					$fields['account'][ $field ]['required'] = true;
				}
			}
		}

		return $fields;
	}


	/**
	 * Removes the guest checkout param from WC checkout JS so the registration form isn't hidden.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $params checkout JS params
	 * @return array
	 */
	public function remove_guest_checkout_js_param( $params = [] ) {

		if (    isset( $params['option_guest_checkout'] )
		     && 'yes' === $params['option_guest_checkout']
		     && $this->force_registration() ) {

			$params['option_guest_checkout'] = 'no';
		}

		return $params;
	}


	/**
	 * Forces registration during the checkout process.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function maybe_force_registration_during_checkout() {

		if ( $this->force_registration() ) {
			$_POST['createaccount'] = 1;
		}
	}


	/**
	 * Checks if registration should be forced.
	 *
	 * This will happen if all of the following are true:
	 *
	 * 1. user is not logged in
	 * 2. an item in the cart contains a product that grants access to a membership
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function force_registration() {

		if ( is_user_logged_in() || ! WC()->cart || WC()->cart->is_empty() ) {
			return false;
		}

		// get membership plans
		$membership_plans = wc_memberships()->get_plans_instance()->get_membership_plans();

		// bail out if there are no membership plans
		if ( empty( $membership_plans ) ) {
			return false;
		}

		$force = false;

		// loop over all available membership plans
		foreach ( $membership_plans as $plan ) {

			// skip if no products grant access to this plan
			if ( ! $plan->has_products() ) {
				continue;
			}

			// array to store products that grant access to this plan
			$access_granting_product_ids = array();

			// loop over items to see if any of them grant access to any memberships
			foreach ( WC()->cart->get_cart() as $key => $item ) {

				// product grants access to this membership
				if ( $plan->has_product( $item['product_id'] ) ) {
					$access_granting_product_ids[] = $item['product_id'];
				}

				// variation access
				if ( isset( $item['variation_id'] ) && $item['variation_id'] && $plan->has_product( $item['variation_id'] ) ) {
					$access_granting_product_ids[] = $item['variation_id'];
				}

			}

			// no products grant access, skip further processing
			if ( empty( $access_granting_product_ids ) ) {
				continue;
			}

			/* this filter is documented in /src/class-wc-memberships-user-memberships.php */
			$product_id = apply_filters( 'wc_memberships_access_granting_purchased_product_id', $access_granting_product_ids[0], $access_granting_product_ids, $plan );

			// sanity check: make sure the selected product ID in fact does grant access
			if ( ! $plan->has_product( $product_id ) ) {
				continue;
			}

			$force = true;
			break;
		}

		/**
		 * Filters whether registration should be forced at checkout ot not.
		 *
		 * This hook is mainly provided to allow add-ons and custom code to force registration when Memberships itself normally wouldn't.
		 * It is not advisable to disable forced registration if there are membership granting products in cart, as memberships cannot be granted to guests.
		 *
		 * @since 1.9.4
		 *
		 * @param bool $force whether to force checkout registration or not
		 * @param \WC_Memberships_Membership_Plan[] $membership_plans an array of all the available membership plans, provided for context
		 */
		return (bool) apply_filters( 'wc_memberships_force_checkout_registration', $force, $membership_plans );
	}


}
