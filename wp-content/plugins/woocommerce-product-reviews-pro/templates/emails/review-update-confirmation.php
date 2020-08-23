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
 * @type \WC_Product_Reviews_Pro_Emails_Review_Update_Confirmation $email email instance
 * @type \WP_User $user User being notified
 * @type \WC_Product $product Product being reviewed/commented
 * @type string $site_title The website title
 * @type \WC_Contribution|\WP_Comment $contribution Top level contribution
 *
 * @version 1.13.0
 * @since 1.8.0
 */

do_action( 'woocommerce_email_header', $email_heading, $email );

?>

<p>
	<?php
	/* translators: Placeholders: %s user name */
	printf( __( 'Hello %s,', 'woocommerce-product-reviews-pro' ), $user->display_name );
	?>
</p>

<p>
	<?php /* translators: Placeholders: %1$s <a> html opening tag - %2$s </a> html closing tag - %3$s product title */
	printf( __( 'Please %1$sclick this confirmation link%2$s to update your review for %3$s.', 'woocommerce-product-reviews-pro' ),
		'<a href="' . esc_url( wc_product_reviews_pro_get_review_update_confirmation_link( $user, $contribution, $product ) ) . '" target="_blank">',
		'</a>',
		$product->get_title()
	); ?>
</p>

<p>
	<?php esc_html_e( "Please note that your updated review won't be published unless you click this link.", 'woocommerce-product-reviews-pro' ); ?>
</p>

<?php

$new_data = wc_product_reviews_pro_get_review_update_data( $contribution->id );

if ( ! empty( $new_data ) ) :

	?>
	<p>
		<?php esc_html_e( "For reference, here's your review:", 'woocommerce-product-reviews-pro' ); ?>
	</p>

	<table id="review" cellspacing="0" cellpadding="6" style="width: 100%; vertical-align: top; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border-color: #e4e4e4;" border="1">
		<thead>
			<tr>
				<th style="vertical-align: top;">&nbsp;</th>
				<th style="vertical-align: top;"><?php esc_html_e( 'Old Content', 'woocommerce-product-reviews-pro' ); ?></th>
				<th style="vertical-align: top;"><?php esc_html_e( 'New Content', 'woocommerce-product-reviews-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="vertical-align: top;"><strong><?php esc_html_e( 'Rating', 'woocommerce-product-reviews-pro' ); ?></strong>:</td>
				<td style="vertical-align: top;"><?php echo ( ! empty( $contribution->rating ) ? $contribution->rating . '/5' : '' ); ?></td>
				<td style="vertical-align: top;"><?php echo ( ! empty( $new_data['rating'] ) ? $new_data['rating'] . '/5' : '' ); ?></td>
			</tr>
			<tr>
				<td style="vertical-align: top;"><strong><?php esc_html_e( 'Title', 'woocommerce-product-reviews-pro' ); ?></strong>:</td>
				<td style="vertical-align: top;"><?php echo ( ! empty( $contribution->title ) ? $contribution->title : '' ); ?></td>
				<td style="vertical-align: top;"><?php echo ( ! empty( $new_data['review_title'] ) ? $new_data['review_title'] : '' ); ?></td>
			</tr>
			<tr>
				<td style="vertical-align: top;"><strong><?php esc_html_e( 'Content', 'woocommerce-product-reviews-pro' ); ?></strong>:</td>
				<td style="vertical-align: top;"><?php echo $contribution->content; ?></td>
				<td style="vertical-align: top;"><?php echo $new_data['review_content']; ?></td>
			</tr>
			<tr>
				<td style="vertical-align: top;"><strong><?php esc_html_e( 'Attachment Type', 'woocommerce-product-reviews-pro' ); ?></strong>:</td>
				<td style="vertical-align: top;"><?php echo ( ! empty( $contribution->attachment_type ) ? ucfirst( $contribution->attachment_type ) : '' ); ?></td>
				<td style="vertical-align: top;"><?php echo ( ! empty( $new_data['attachment_type'] ) ? ucfirst( $new_data['attachment_type'] ) : '' ); ?></td>
			</tr>
			<tr>
				<td style="vertical-align: top;"><strong><?php esc_html_e( 'Attachment', 'woocommerce-product-reviews-pro' ); ?></strong>:</td>
				<td style="vertical-align: top;">
					<?php
					$url = '';

					if ( ! empty( $contribution->attachment_id ) ) {
						$url = wp_get_attachment_url($contribution->attachment_id);
					} elseif ( ! empty( $contribution->attachment_url ) ) {
						$url = $contribution->attachment_url;
					}
					?>

					<?php if ( ! empty( $url ) ) : ?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_url( $url ); ?></a>
					<?php endif; ?>
				</td>
				<td style="vertical-align: top;">
					<?php
					$new_url = '';

					if ( ! empty( $new_data['attachment_id'] ) ) {
						$new_url = wp_get_attachment_url($new_data['attachment_id']);
					} elseif ( ! empty( $new_data['attachment_url'] ) ) {
						$new_url = $new_data['attachment_url'];
					}
					?>

					<?php if ( ! empty( $new_url ) ) : ?>
						<a href="<?php echo esc_url( $new_url ); ?>" target="_blank"><?php echo esc_url( $new_url ); ?></a>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>

<?php endif; ?>

<?php

do_action( 'woocommerce_email_footer', $email );
