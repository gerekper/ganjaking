<?php

global $porto_settings;

if ( ( is_singular( 'portfolio' ) && isset( $porto_settings['portfolio-metas'] ) && in_array( 'quote', $porto_settings['portfolio-metas'] ) ) || ( ! is_singular( 'portfolio' ) && isset( $porto_settings['portfolio-show-testimonial'] ) && $porto_settings['portfolio-show-testimonial'] ) ) :
	global $post;
	$portfolio_author_quote = get_post_meta( $post->ID, 'portfolio_author_quote', true );
	if ( $portfolio_author_quote ) :
		$portfolio_author_name  = get_post_meta( $post->ID, 'portfolio_author_name', true );
		$portfolio_author_image = get_post_meta( $post->ID, 'portfolio_author_image', true );
		$portfolio_author_role  = get_post_meta( $post->ID, 'portfolio_author_role', true );
		?>
		<div class="testimonial testimonial-style-4">
			<blockquote>
				<p><?php echo wp_kses_post( $portfolio_author_quote ); ?></p>
			</blockquote>
			<div class="testimonial-arrow-down"></div>
			<div class="testimonial-author">
				<?php if ( $portfolio_author_image ) : ?>
					<div class="testimonial-author-thumbnail">
						<img alt="author" class="img-responsive img-circle" src="<?php echo esc_url( $portfolio_author_image ); ?>">
					</div>
				<?php endif; ?>
				<p><strong><?php echo esc_html( $portfolio_author_name ); ?></strong><span><?php echo esc_html( $portfolio_author_role ); ?></span></p>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>
