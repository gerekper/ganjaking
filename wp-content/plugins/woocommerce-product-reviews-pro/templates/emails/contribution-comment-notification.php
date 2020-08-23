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
 * Products Review Pro new comment notification email (html)
 *
 * @type string $email_heading Email heading as set in admin
 * @type \WC_Product_Reviews_Pro_Emails_New_Comment $email email instance
 * @type \WP_User $user User being notified
 * @type \WC_Product $product Product being reviewed/commented
 * @type string $site_title The website title
 * @type \WC_Contribution|\WP_Comment $contribution Top level contribution
 * @type \WC_Contribution|\WP_Comment $reply Comment to above contribution
 *
 * @version 1.13.0
 * @since 1.3.0
 */

do_action( 'woocommerce_email_header', $email_heading, $email );

?>

<p>
	<?php $contribution_type = wc_product_reviews_pro_get_contribution_type( $contribution->type );

	/* translators: Placeholders: %1$s user name - %2$s product title - %3$s contribution type - %4$s site title */
	printf( __( 'Hello %1$s, a new comment has been posted to a %2$s %3$s you are watching on %4$s.', 'woocommerce-product-reviews-pro' ),
		$user->display_name,
		$product->get_title(),
		strtolower( $contribution_type->get_title() ),
		$site_title
	); ?>
</p>

<p>
	<?php
	printf( '<strong><a href="%s" target="_blank">' . esc_html__( 'View new comment.', 'woocommerce-product-reviews-pro' ) .
		'</a></strong>', get_comment_link( $reply->id )
	); ?>
</p>

<p>
	<?php /* translators: Placeholders: %1$s contribution type - %2$s <a> html opening tag - %3$s </a> html closing tag */
	printf( __( 'If you no longer wish to receive notifications for new replies to this %1$s, please %2$sfollow this link%3$s.', 'woocommerce-product-reviews-pro' ),
		strtolower( $contribution_type->get_title() ),
		'<a href="' . wc_product_reviews_pro_get_comment_notification_unsubscribe_link( $user, $contribution, $product ) . '" target="_blank">',
		'</a>'
	); ?>
</p>
<?php

do_action( 'woocommerce_email_footer', $email );
