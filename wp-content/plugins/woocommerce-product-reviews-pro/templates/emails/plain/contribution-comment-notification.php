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
 * Products Review Pro new comment notification email (plaintext)
 *
 * @type string $email_heading Email heading as set in admin
 * @type \WP_User $user User being notified
 * @type \WC_Product $product Product being reviewed/commented
 * @type string $site_title The website title
 * @type \WC_Contribution|\WP_Comment $contribution Top level contribution
 * @type \WC_Contribution|\WP_Comment $reply Comment to above contribution
 *
 * @version 1.7.0
 * @since 1.3.0
 */

echo $email_heading . "\n\n";

$contribution_type = wc_product_reviews_pro_get_contribution_type( $contribution->type );

/* translators: Placeholders: %1$s user name - %2$s product title - %3$s contribution type - %4$s site title */
echo sprintf( __( 'Hello %1$s, a new comment has been posted to a %2$s %3$s you are watching on %4$s.', 'woocommerce-product-reviews-pro' ),
	$user->display_name,
	$product->get_title(),
	strtolower( $contribution_type->get_title() ),
	$site_title
) . "\n\n";

echo "----------\n\n";

/* translators: Placeholders: %s - url to go to a product comment */
echo sprintf( __( 'Comment link: %s' ),
	get_comment_link( $reply->id )
) . "\n\n";

/* translators: Placeholders: %1$s url to unsubscribe from email notifications - %2$s contribution type */
echo sprintf( __( 'Stop notifications for this %1$s: %2$s', 'woocommerce-product-reviews-pro' ),
	strtolower( $contribution_type->get_title() ),
	wc_product_reviews_pro_get_comment_notification_unsubscribe_link( $user, $contribution, $product )
) . "\n\n";

echo "----------\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
