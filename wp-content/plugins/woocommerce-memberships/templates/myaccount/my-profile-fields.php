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
 * @copyright Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Renders the My Profile section in the My Account page to list customer profile fields.
 *
 * @var SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field[] $profile_fields array of profile field objects
 * @var string $security nonce necessary to update profile fields
 *
 * @version 1.19.0
 * @since 1.19.0
 */

?>
<div class="my-membership-section">
	<form method="post">
		<table class="shop_table shop_table_responsive my_account_orders my_account_memberships my_profile">
			<thead>
				<tr>
					<th colspan="2"><?php esc_html_e( 'My Profile', 'woocommerce-memberships' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $profile_fields as $profile_field ) : ?>
					<tr>
						<td>
							<?php echo esc_html( $profile_field->get_definition()->get_name() ); ?>
							<?php echo $profile_field->is_required() ? '<abbr class="required" title="' . esc_attr_x( 'required', 'Required input field', 'woocommerce-memberships'  ) . '">*</abbr>' : '<span class="optional">(' . esc_html_x( 'optional', 'Optional input field', 'woocommerce-memberships' ) . ')</span>'; ?>
						</td>
						<td>
							<?php wc_memberships_profile_field_form_field( $profile_field, [ 'label' => false ] ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="security" value="<?php echo esc_attr( $security ); ?>" />
		<input class="button button-primary" type="submit" name="update_profile_fields" value="<?php esc_html_e( 'Save', 'woocommerce-memberships' ); ?>" />
	</form>
</div>
