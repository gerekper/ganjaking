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
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Renders the tab sections on My Account page for a customer membership.
 *
 * @version 1.12.0
 * @since 1.9.0
 */

$members_area          = wc_memberships()->get_frontend_instance()->get_my_account_instance()->get_members_area_instance();
$customer_membership   = $members_area->get_members_area_user_membership();
$membership_plan       = $customer_membership ? $customer_membership->get_plan() : null;
$members_area_sections = $membership_plan ? $members_area->get_members_area_navigation_items( $membership_plan ) : null;

if ( ! empty( $members_area_sections ) && is_array( $members_area_sections ) ) :

	// reinstates WooCommerce core action
	do_action( 'woocommerce_before_account_navigation' );

	/**
	 * Fires before the members area navigation.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Memberships_User_Membership $customer_membership the user membership displayed
	 */
	do_action( 'wc_memberships_members_area_before_my_membership_navigation', $customer_membership );

	ob_start();

	?>
	<nav class="woocommerce-MyAccount-navigation wc-memberships-members-area-navigation">
		<ul>
			<?php foreach ( $members_area_sections as $section_id => $section_data ) : ?>
				<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $section_id ) . ' ' . $section_data['class'] ); ?>">
					<a href="<?php echo esc_url( $section_data['url'] ); ?>"><?php echo esc_html( $section_data['label'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php

	/**
	 * Filters the Members Area navigation HTML.
	 *
	 * @since 1.9.5
	 *
	 * @param string $navigation HTML
	 * @param array $members_area_sections the members area sections being output
	 */
	echo apply_filters( 'wc_memberships_members_area_my_membership_navigation', ob_get_clean(), $members_area_sections );

	/**
	 * Fires after the members area navigation.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Memberships_User_Membership $customer_membership the user membership displayed
	 */
	do_action( 'wc_memberships_members_area_after_my_membership_navigation', $customer_membership );

	// reinstates WooCommerce core action
	do_action( 'woocommerce_after_account_navigation' );

endif;
