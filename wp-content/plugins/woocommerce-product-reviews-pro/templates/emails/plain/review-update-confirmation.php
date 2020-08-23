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
 * Products Review Pro review update confirmation email (html)
 *
 * @type string $email_heading Email heading as set in admin
 * @type \WP_User $user User being notified
 * @type \WC_Product $product Product being reviewed/commented
 * @type string $site_title The website title
 * @type \WC_Contribution|\WP_Comment $contribution Top level contribution
 *
 * @version 1.8.0
 * @since 1.8.0
 */

echo $email_heading . "\n\n";

/* translators: Placeholders: %s user name */
echo sprintf( __( 'Hello %s,', 'woocommerce-product-reviews-pro' ), $user->display_name ) . "\n\n";

echo "----------\n\n";

/* translators: Placeholders: %1$s <a> html opening tag - %2$s </a> html closing tag - %3$s product title */
echo sprintf( __( 'Update your review for %1$s: %2$s', 'woocommerce-product-reviews-pro' ),
        $product->get_title(),
        wc_product_reviews_pro_get_review_update_confirmation_link( $user, $contribution, $product )
    ) . "\n\n";

echo esc_html__( "Please note that your updated review won't be published unless you click this link.", 'woocommerce-product-reviews-pro' ) . "\n\n";

$new_data = wc_product_reviews_pro_get_review_update_data( $contribution->id );

if ( ! empty( $new_data ) ) {

    echo esc_html__( "For reference, here's your review:", 'woocommerce-product-reviews-pro' ) . "\n\n";

	$url	 = '';
	$new_url = '';

	if ( ! empty( $contribution->attachment_id ) ) {
        $url = wp_get_attachment_url($contribution->attachment_id);
    } elseif ( ! empty( $contribution->attachment_url ) ) {
        $url = $contribution->attachment_url;
    }

	if ( ! empty( $new_data['attachment_id'] ) ) {
        $new_url = wp_get_attachment_url($new_data['attachment_id']);
    } elseif ( ! empty( $new_data['attachment_url'] ) ) {
        $new_url = $new_data['attachment_url'];
    }

	echo "&nbsp;\t" . esc_html__( 'Old Content', 'woocommerce-product-reviews-pro' ) . "\t" . esc_html__( 'New Content', 'woocommerce-product-reviews-pro' ) . "\n";
	echo esc_html__( 'Rating', 'woocommerce-product-reviews-pro' ) . "\t" . ( ! empty( $contribution->rating ) ? $contribution->rating . '/5' : '' ) . "\t" . ( ! empty( $new_data['rating'] ) ? $new_data['rating'] . '/5' : '' ) . "\n";
	echo esc_html__( 'Title', 'woocommerce-product-reviews-pro' ) . "\t" . ( ! empty( $contribution->title ) ? $contribution->title : '' ) . "\t" . ( ! empty( $new_data['review_title'] ) ? $new_data['review_title'] : '' ) . "\n";
	echo esc_html__( 'Content', 'woocommerce-product-reviews-pro' ) . "\t" . $contribution->content . "\t" . $new_data['review_content'] . "\n";
	echo esc_html__( 'Attachment Type', 'woocommerce-product-reviews-pro' ) . "\t" . ( ! empty( $contribution->attachment_type ) ? ucfirst( $contribution->attachment_type ) : '' ) . "\t" . ( ! empty( $new_data['attachment_type'] ) ? ucfirst( $new_data['attachment_type'] ) : '' ) . "\n";
	echo esc_html__( 'Attachment', 'woocommerce-product-reviews-pro' ) . "\t" . esc_url( $url ) . "\t" . esc_url( $new_url ) . "\n\n";
}

echo "----------\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
