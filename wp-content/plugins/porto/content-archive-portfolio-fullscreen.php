<?php
global $porto_settings, $porto_layout, $post;

$post_class = array( 'portfolio', 'portfolio-fullscreen' );
$item_cats  = get_the_terms( $post->ID, 'portfolio_cat' );
if ( $item_cats ) {
	foreach ( $item_cats as $item_cat ) {
		$post_class[] = urldecode( $item_cat->slug );
	}
}

$sub_title  = porto_portfolio_sub_title( $post );
$attachment = porto_get_attachment( get_post_thumbnail_id(), 'full' );

if ( has_post_thumbnail() ) :

	?>
	<article <?php post_class( $post_class ); ?> id="porto_portfolio_<?php echo (int) $post->ID; ?>">
		<?php porto_render_rich_snippets(); ?>
		<div class="portfolio-item" style="background-image: url(<?php echo esc_url( $attachment['src'] ); ?>);">

			<div class="portfolio-meta"<?php echo isset( $content_animation ) && $content_animation ? ' data-appear-animation="' . esc_attr( $content_animation ) . '" data-appear-animation-delay="200" data-plugin-options="' . esc_attr( json_encode( array( 'accY' => -10 ) ) ) . '"' : ''; ?>>
			<?php if ( $sub_title ) : ?>
				<div class="portfolio-cat"<?php echo isset( $content_animation ) && $content_animation ? ' data-appear-animation="' . esc_attr( $content_animation ) . '" data-appear-animation-delay="500" data-plugin-options="' . esc_attr( json_encode( array( 'accY' => -10 ) ) ) . '"' : ''; ?>><?php echo wp_kses_post( $sub_title ); ?></div>
			<?php endif ?>

				<h3 class="portfolio-title"<?php echo isset( $content_animation ) && $content_animation ? ' data-appear-animation="' . esc_attr( $content_animation ) . '" data-appear-animation-delay="300" data-plugin-options="' . esc_attr( json_encode( array( 'accY' => -10 ) ) ) . '"' : ''; ?>><?php the_title(); ?></h3>

				<div class="portfolio-action"<?php echo isset( $content_animation ) && $content_animation ? ' data-appear-animation="' . esc_attr( $content_animation ) . '" data-appear-animation-delay="700" data-plugin-options="' . esc_attr( json_encode( array( 'accY' => -10 ) ) ) . '"' : ''; ?>>
					<a class="btn-view-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View More', 'porto' ); ?> <i class="fas fa-arrow-right"></i></a>
				</div>

			</div>

			<?php do_action( 'porto_portfolio_after_content' ); ?>
		</div>
	</article>
	<?php
endif;
