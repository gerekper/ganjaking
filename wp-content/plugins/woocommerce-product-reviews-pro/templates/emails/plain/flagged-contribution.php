<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Products Review Pro flagged contribution email (plain text).
 *
 * @type string $email_heading Email heading as set in admin
 * @type \WC_Product_Reviews_Pro_Contribution_Flag $flag flag being set by a customer for the $contribution
 * @type \WC_Contribution $contribution the contribution being flagged
 * @type \WC_Product_Reviews_Pro_Contribution_Type $contribution_type the contribution type handler
 * @type \WC_Product $product product being reviewed/commented
 * @type string $site_title the website title
 *
 * @version 1.10.0
 * @since 1.10.0
 */

echo $email_heading . "\n\n";

/* translators: Placeholders: %1$s - formatted name of the user setting the flag, %2$s - contribution type name, %3$s - product name */
printf( esc_html__( 'Hello, a %1$s flagged a %2$s on %3$s as inappropriate and thinks it should be moderated.', 'woocommerce-product-reviews-pro' ) . "\n\n", $flag->get_user_display_name( false ), strtolower( $contribution_type->get_title() ), $product->get_title() );

if ( $flag->has_reason() ) {
	/* translators: Placeholder: %s - reason given for flagging a contribution as inappropriate */
	printf( esc_html__( 'Reason given: "%s"', 'woocommerce-product-reviews-pro' ) . "\n\n", esc_html( $flag->get_reason() ) );
}

/* translators: Placeholders: %1$s - the contribution type name, %2$s - URL to the contribution's admin edit screen */
printf( esc_html__( 'You can dismiss this flag or moderate the related %1$s: %2$s', 'woocommerce-product-reviews-pro' ) . "\n\n", strtolower( $contribution_type->get_title() ), esc_url( admin_url( 'comment.php?action=editcomment&amp;c=' . $contribution->get_id() ) ) );

