<?php
/**
 * Quick view share buttons.
 *
 * @author  YITH
 * @package YITH WooCommerce Quick View
 * @version 1.0.0
 */
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $product;

$socials    = get_option( 'yith-wcqv-share-socials' );
$fb_appid   = get_option( 'yith-wcqv-facebook-appid' );
$product_id = $product->get_id();
$link       = get_the_permalink( $product_id );
$title      = get_the_title( $product_id );
$attrs      = '';

if ( empty( $socials ) ) {
	return;
}
?>

<div class="yith-quick-view-share">
	<?php
	foreach ( $socials as $social ) {

		if ( 'facebook' === $social && $fb_appid ) {
			$url   = 'https://www.facebook.com/dialog/share?app_id=' . $fb_appid . '&display=popup&href=' . $link;
			$attrs = " onclick=\"javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;\"";
		} elseif ( 'twitter' === $social ) {
			$url   = 'https://twitter.com/share?url=' . $link . '&text=' . $title . '';
			$attrs = " onclick=\"javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=417,width=600');return false;\"";
		} elseif ( 'pinterest' === $social ) {
			$src = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'full' );

			$url   = 'http://pinterest.com/pin/create/button/?url=' . $link . '&media=' . $src[0];
			$attrs = " onclick=\"javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;\"";
		} elseif ( 'mail' === $social ) {
			$subject = rawurlencode( apply_filters( 'yith_wcqv_share_mail_subject', esc_html__( 'May I ask you to see this product, please?', 'yith-woocommerce-quick-view' ) ) );
			$url     = 'mailto:?subject=' . $subject . '&amp;body= ' . $link . '&amp;title=' . $title;
		} else {
			continue;
		}

		$url = apply_filters( 'yith_wcqv_share_' . $social, $url );

		echo '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $social ) . '" target="_blank" ' . esc_html( $attrs ) . ' class="social-' . esc_attr( $social ) . '">' . esc_html( $social ) . '</a>';
	}
	?>
</div>
<?php
