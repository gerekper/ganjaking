<?php
global $porto_settings;

$share = porto_get_meta_value( 'post_share' );
if ( $porto_settings['share-enable'] && 'no' !== $share && ( 'yes' === $share || ( 'yes' !== $share && ! empty( $porto_settings['post-share'] ) ) ) ) : ?>
	<?php
		$classes = array( 'post-share' );
	if ( ! isset( $style ) || 'inline' != $style ) {
		$classes[] = 'post-block';
	}
	if ( isset( $porto_settings['post-share-position'] ) && 'advance' === $porto_settings['post-share-position'] ) {
		$classes[] = 'post-share-advance';
	}
	?>
	<div class="<?php echo implode( ' ', $classes ); ?>">
		<?php if ( isset( $style ) && 'inline' == $style ) : ?>
			<span><i class="fas fa-share-alt"></i><?php esc_html_e( 'Share:', 'porto' ); ?></span>
		<?php elseif ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) : ?>
			<h3><?php esc_html_e( 'Share this post', 'porto' ); ?></h3>
		<?php else : ?>
			<h3><i class="fas fa-share"></i><?php esc_html_e( 'Share this post', 'porto' ); ?></h3>
		<?php endif; ?>
		<?php if ( isset( $porto_settings['post-share-position'] ) && 'advance' === $porto_settings['post-share-position'] ) : ?>
			<div class="post-share-advance-bg">
				<?php get_template_part( 'share' ); ?>
				<i class="fas fa-share-alt"></i>
			</div>
		<?php else : ?>
			<?php get_template_part( 'share' ); ?>
		<?php endif; ?>
	</div>
	<?php
endif;
