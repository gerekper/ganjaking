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

use SkyVerge\WooCommerce\Memberships\Helpers\Strings_Helper;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Handle memberships order data in admin screens.
 *
 * This also runs in Subscriptions screens, being subscriptions objects child classes of WC_Order.
 *
 * @since 1.9.0
 */
class WC_Memberships_Admin_Orders {


	/**
	 * Handler constructor.
	 *
	 * @since 1.9.0
	 */
	public function __construct() {

		// list user memberships on individual "edit order" screen
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'render_memberships_order_data' ) );
	}


	/**
	 * Adds User Memberships information to "Edit Order" screen.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Order|\WC_Order_Refund $order the WooCommerce order
	 */
	public function render_memberships_order_data( $order ) {

		if ( $customer_user = $order instanceof \WC_Order || $order instanceof \WC_Order_Refund ? get_user_by( 'id', $order->get_user_id() ) : null ) :

			?>
			<p class="form-field form-field-wide wc-customer-memberships">
				<label for="customer_memberships"><?php esc_html_e( 'Active Memberships:', 'woocommerce-memberships' ); ?></label>
				<?php

				$user_id     = $order->get_user_id();
				$memberships = wc_memberships()->get_user_memberships_instance()->get_user_memberships( $user_id );
				$links       = array();

				if ( ! empty( $memberships ) ) {
					foreach ( $memberships as $membership ) {

						$plan = $membership->get_plan();

						if ( $plan && wc_memberships_is_user_active_member( $user_id, $plan ) ) {

							$links[] = '<a href="' . esc_url( get_edit_post_link( $membership->id ) ) . '">' . esc_html( $plan->name ) . '</a>';
						}
					}
				}

				if ( 0 === count( $links ) ) {
					esc_html_e( 'none', 'woocommerce-memberships' );
				} else {
					echo Strings_Helper::get_human_readable_items_list( $links, 'and' );
				}

				?>
			</p>
			<?php

		endif;
	}


}
