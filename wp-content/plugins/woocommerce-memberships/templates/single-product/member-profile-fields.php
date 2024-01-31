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
 * Single Product Member Profile Fields.
 *
 * @var \WC_Memberships_Membership_Plan[] $membership_plans array of membership plans (with IDs for keys)
 * @var SkyVerge\WooCommerce\Memberships\Profile_Fields\Profile_Field[] $profile_fields array of profile field objects
 *
 * @version 1.19.0
 * @since 1.19.0
 */

/**
 * Triggers before displaying form fields and their inputs on a product page.
 *
 * @since 1.19.0
 */
do_action( 'wc_memberships_before_product_profile_fields' );

?>
<div class="wc-memberships-profile-fields-wrapper">

	<?php foreach ( $profile_fields as $profile_field ) : ?>

		<?php wc_memberships_profile_field_form_field( $profile_field ); ?>

	<?php endforeach; ?>

	<input
		type="hidden"
		id="wc-memberships-member-profile-fields-membership-plans"
		name="member_profile_fields_membership_plans"
		value="<?php echo implode( ',', array_keys( $membership_plans ) ); ?>"
	/>

</div>
<?php

/**
 * Triggers after displaying form fields and their inputs on a product page.
 *
 * @since 1.19.0
 */
do_action( 'wc_memberships_after_product_profile_fields' );
