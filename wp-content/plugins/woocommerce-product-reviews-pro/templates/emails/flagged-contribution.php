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
 * Products Review Pro flagged contribution email (HTML).
 *
 * @type string $email_heading Email heading as set in admin
 * @type \WC_Product_Reviews_Pro_Emails_Flagged_Contribution $email email instance
 * @type \WC_Product_Reviews_Pro_Contribution_Flag $flag flag being set by a customer for the $contribution
 * @type \WC_Contribution $contribution the contribution being flagged
 * @type \WC_Product_Reviews_Pro_Contribution_Type $contribution_type the contribution type handler
 * @type \WC_Product $product product being reviewed/commented
 * @type string $site_title the website title
 *
 * @version 1.13.0
 * @since 1.10.0
 */

do_action( 'woocommerce_email_header', $email_heading, $email );

?>

<p>
	<?php /* translators: Placeholders: %1$s - formatted name of the user setting the flag, %2$s - linked contribution type name with link to contribution, %3$s - linked product name */
	printf( __( 'Hello, a %1$s flagged a %2$s on %3$s as inappropriate and thinks it should be moderated.', 'woocommerce-product-reviews-pro' ), $flag->get_user_display_name(), '<a href="' . esc_url( $contribution->get_permalink() ) . '">' . strtolower( esc_html( $contribution_type->get_title() ) ) . '</a>', '<a href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $product->get_title() ) . '</a>' ); ?>
</p>

<?php if ( $flag->has_reason() ) : ?>

	<p>
		<?php /* translators: Placeholder: %s - reason given for flagging a contribution as inappropriate */
		printf( __( 'Reason given: %s', 'woocommerce-product-reviews-pro' ), '<em>"' . esc_html( $flag->get_reason() ) . '"</em>' ); ?>
	</p>

<?php endif; ?>

<p>
	<?php /* translators: Placeholders: %1$s - opening <a> link tag, %2$s - contribution type name, %3$s - closing </a> link tag */
	printf( __( 'You can %1$sdismiss this flag or moderate the related %2$s%3$s.', 'woocommerce-product-reviews-pro' ), '<a href="' . esc_url( admin_url( 'comment.php?action=editcomment&amp;c=' . $contribution->get_id() ) ) . '">', strtolower( esc_html( $contribution_type->get_title( ) ) ), '</a>' ); ?>
</p>

<br>
<?php

do_action( 'woocommerce_email_footer', $email );
