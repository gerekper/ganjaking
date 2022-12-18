<?php
	global $porto_settings;
?>

<?php if ( ! empty( $porto_settings['post-author'] ) ) : ?>
	<div class="post-block post-author clearfix">
		<?php if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) : ?>
			<h3><?php esc_html_e( 'Author', 'porto' ); ?></h3>
		<?php else : ?>
			<h3><i class="far fa-user"></i><?php esc_html_e( 'Author', 'porto' ); ?></h3>
		<?php endif; ?>
		<div class="img-thumbnail">
			<?php echo get_avatar( get_the_author_meta( 'email' ), '80' ); ?>
		</div>
		<p><strong class="name"><?php the_author_posts_link(); ?></strong></p>
		<p class="author-content"><?php the_author_meta( 'description' ); ?></p>
	</div>
<?php endif; ?>
