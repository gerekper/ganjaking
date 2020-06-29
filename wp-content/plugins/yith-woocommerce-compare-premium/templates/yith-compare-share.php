<?php
/**
 * Compare share template
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Compare
 * @version 1.0.0
 */
?>

<div id="yith-woocompare-share">
	<h3 class="yith-woocompare-share-title"><?php echo esc_html( $share_title ); ?></h3>
	<ul>
		<?php foreach ( $socials as $social ) : ?>
			<li>
				<?php
				switch ( $social ) {
					case 'facebook':
						if ( $facebook_appid ) {
							echo '<a target="_blank" class="facebook" href="https://www.facebook.com/dialog/feed?app_id=' . esc_html( $facebook_appid ) . '&display=popup&name=' . esc_html( $share_link_title ) . '&description=' . esc_html( $share_summary ) . '&picture=' . esc_html( $facebook_image ) . '&link=' . esc_html( $share_link_url ) . '&redirect_uri=' . esc_html( home_url() ) . '" title="' . esc_html__( 'Facebook', 'yith-woocommerce-compare' ) . '">facebook</a>';
						}
						break;
					case 'twitter':
						echo '<a target="_blank" class="twitter" href="https://twitter.com/share?url=' . esc_html( $share_link_url ) . '&amp;text=' . esc_html( $share_summary ) . '" title="' . esc_html__( 'Twitter', 'yith-woocommerce-compare' ) . '">twitter</a>';
						break;
					case 'pinterest':
						echo '<a target="_blank" class="pinterest" href="http://pinterest.com/pin/create/button/?url=' . esc_html( $share_link_url ) . '&amp;description=' . esc_html( $share_summary ) . '" title="' . esc_html__( 'Pinterest', 'yith-woocommerce-compare' ) . '" onclick="window.open(this.href); return false;">pinterest</a>';
						break;
					case 'mail':
						echo '<a class="email" href="mailto:?subject=I wanted you to see this site&amp;body=' . esc_html( $share_link_url ) . '&amp;title=' . esc_html( $share_link_title ) . '" title="' . esc_html__( 'Email', 'yith-woocommerce-compare' ) . '">email</a>';
						break;
				}
				?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>