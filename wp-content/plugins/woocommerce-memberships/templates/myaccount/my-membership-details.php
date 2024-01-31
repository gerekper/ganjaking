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
 * Displays information on the current memberships viewed in Members Area.
 *
 * @type \WC_Memberships_User_Membership $customer_membership the current user membership being displayed
 * @type array $membership_details associative array of settings data
 *
 * @version 1.13.0
 * @since 1.9.0
 */

if ( ! empty( $membership_details ) && is_array( $membership_details ) ) :

	?>
	<table class="shop_table shop_table_responsive my_account_orders my_account_memberships my_membership_settings">
		<thead>
			<tr>
				<th colspan="2"><?php esc_html_e( 'Membership Details', 'woocommerce-memberships' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $membership_details as $setting_id => $data ) : ?>
				<tr class="<?php echo sanitize_html_class( $data['class'] ); ?>">
					<td><?php echo esc_html( $data['label'] ); ?></td>
					<td><?php echo $data['content']; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php

endif;

