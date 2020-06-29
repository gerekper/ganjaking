<?php
/**
 * Share template
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.6.3
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>
	<div class="yith-wcaf-share">
		<h4 class="yith-wcaf-share-title"><?php echo esc_html( $share_title ); ?></h4>
		<ul>
			<?php if ( $share_facebook_enabled ) : ?>
				<li style="list-style-type: none; display: inline-block;">
					<a target="_blank" class="icon-yith-facebook-official" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode( $share_link_url ); ?>&p[title]=<?php echo esc_attr( $share_link_title ); ?>&p[summary]=<?php echo esc_attr( $share_summary ); ?>" title="<?php esc_attr_e( 'Facebook', 'yith-woocommerce-affiliates' ); ?>"></a>
				</li>
			<?php endif; ?>

			<?php if ( $share_twitter_enabled ) : ?>
				<li style="list-style-type: none; display: inline-block;">
					<a target="_blank" class="icon-yith-twitter" href="https://twitter.com/share?url=<?php echo urlencode( $share_link_url ); ?>&amp;text=<?php echo esc_attr( $share_twitter_summary ); ?>" title="<?php esc_attr_e( 'Twitter', 'yith-woocommerce-affiliates' ); ?>"></a>
				</li>
			<?php endif; ?>

			<?php if ( $share_pinterest_enabled ) : ?>
				<li style="list-style-type: none; display: inline-block;">
					<a target="_blank" class="icon-yith-pinterest-squared" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode( $share_link_url ); ?>&amp;description=<?php echo esc_attr( $share_summary ); ?>&amp;media=<?php echo esc_attr( $share_image_url ); ?>" title="<?php esc_attr_e( 'Pinterest', 'yith-woocommerce-affiliates' ); ?>" onclick="window.open(this.href); return false;"></a>
				</li>
			<?php endif; ?>

			<?php if ( $share_email_enabled ) : ?>
				<li style="list-style-type: none; display: inline-block;">
					<a class="icon-yith-mail-alt" href="mailto:?subject=<?php echo urlencode( apply_filters( 'yith_wcaf_email_share_subject', $share_link_title ) ); ?>&amp;body=<?php echo esc_attr( apply_filters( 'yith_wcaf_email_share_body', urlencode( $share_link_url ), $share_summary ) ); ?>&amp;title=<?php echo esc_attr( $share_link_title ); ?>" title="<?php esc_attr_e( 'Email', 'yith-woocommerce-affiliates' ); ?>"></a>
				</li>
			<?php endif; ?>

			<?php if ( $share_whatsapp_enabled && wp_is_mobile() ) : ?>
				<li style="list-style-type: none; display: inline-block;">
					<a class="icon-yith-whatsapp" href="whatsapp://send?text=<?php echo ! empty( $share_summary ) ? esc_attr( $share_summary ) : esc_attr_e( 'My Referral URL on ', 'yith-woocommerce-affiliates' ) . esc_attr( get_bloginfo( 'name' ) ) . ' - ' . urlencode( $share_link_url ); ?>" data-action="share/whatsapp/share" target="_blank" title="<?php esc_attr_e( 'WhatsApp', 'yith-woocommerce-affiliates' ); ?>"></a>
				</li>

			<?php endif; ?>
			<?php if ( $share_whatsapp_enabled && ! wp_is_mobile() ) : ?>
				<li style="list-style-type: none; display: inline-block;">
					<a target="_blank" class="icon-yith-whatsapp" href="https://web.whatsapp.com/send?text=<?php echo ! empty( $share_summary ) ? esc_attr( $share_summary ) : esc_attr_e( 'My Referral URL on ', 'yith-woocommerce-affiliates' ) . esc_attr( get_bloginfo( 'name' ) ) . ' - ' . urlencode( $share_link_url ); ?>" data-action="share/whatsapp/share" title="WhatsApp Web"></a>
				</li>
			<?php endif; ?>
		</ul>
	</div>

<?php do_action( 'yith_wcaf_after_share_buttons', $share_link_url, $share_title, $share_link_title ); ?>
