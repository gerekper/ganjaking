<?php
global $post;
if ( ! isset( $post->ID ) ) {
	return;
}
if ( empty( $socials ) ) {
	return;
}

echo "<div class='share-container'>";

foreach ( $socials as $social => $values ) {
	$social_icon = $social;
	if ( $values['url'] == '' ) {
		$title     = urlencode( get_the_title() );
		$permalink = urlencode( get_permalink() );
		$excerpt   = get_the_excerpt();
	} else {
		$title     = get_bloginfo( 'name' );
		$permalink = urldecode( $values['url'] );
		$excerpt   = get_bloginfo( 'description' );
	}


	if ( $social == 'facebook' ) {
		$url = apply_filters( 'ypop_share_facebook', 'https://www.facebook.com/sharer.php?u=' . $permalink . '&t=' . $title . '' );

	} elseif ( $social == 'twitter' ) {
		$url = apply_filters( 'ypop_share_twitter', 'https://twitter.com/share?url=' . $permalink . '&amp;text=' . $title . '' );

	} elseif ( $social == 'google' ) {
		$social_icon = 'google-plus';// fix after social.php use awesome icons
		$url         = apply_filters( 'ypop_share_google', 'https://plus.google.com/share?url=' . $permalink . '&amp;title=' . $title . '' );
		$attrs       = " onclick=\"javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;\"";

	} elseif ( $social == 'pinterest' ) {
		$src   = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
		$url   = apply_filters( 'ypop_share_pinterest', 'http://pinterest.com/pin/create/button/?url=' . $permalink . '&amp;media=' . $src[0] . '&amp;description=' . strip_tags( $excerpt ) );
		$attrs = ' onclick="window.open(this.href); return false;';
	} elseif ( $social == 'linkedin' ) {
		$url = 'http://www.linkedin.com/shareArticle?mini=true&url=' . $permalink . '&title=' . $title . '&summary=' . $excerpt;
	}

	?>
	<?php if ( yith_plugin_fw_is_true( $icon ) ) : ?>

		<a href="<?php echo esc_url( $url ); ?>" class="link_socials" title="<?php echo esc_attr( $social ); ?>" target="_blank">
			<span class="icon-circle">
					<i class="fa fa-<?php echo esc_attr( $social_icon ); ?>"></i>
			</span>
		</a>

	<?php else : ?>
		<div class="socials-text">
			<a href="<?php echo esc_url( $url ); ?>" class="link-<?php echo esc_attr( $social ); ?>" target="_blank">
				<?php echo wp_kses_post( $social ); ?>
			</a>
		</div>

	<?php endif ?>

	<?php

}
 echo '</div>';
