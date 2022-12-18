<?php

global $post;

$post_class   = array();
$post_class[] = 'faq';

if ( ! empty( $post_classes ) ) {
	$post_class[] = trim( $post_classes );
}

$item_cats    = get_the_terms( $post->ID, 'faq_cat' );
if ( $item_cats ) :
	foreach ( $item_cats as $item_cat ) {
		$post_class[] = urldecode( $item_cat->slug );
	}
endif;
?>

<div <?php post_class( $post_class ); ?>>
	<section class="toggle">
		<label><?php the_title(); ?></label>
		<div class="toggle-content">
		<?php
			porto_render_rich_snippets( 'h3' );
			porto_the_content();
			wp_link_pages(
				array(
					'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'porto' ) . '</span>',
					'after'       => '</div>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
					'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'porto' ) . ' </span>%',
					'separator'   => '<span class="screen-reader-text">, </span>',
				)
			);
			?>
		</div>
	</section>
</div>
