<?php
$width_class    = $shortcode_args['extra']['width_class'];
$index_carousel = $shortcode_args['extra']['index_carousel'];
$k              = $shortcode_args['extra']['k'];
$extra_styles   = $shortcode_args['extra']['extra_styles'];

$render = '<div class="sp-posts-single-block index-' . $index_carousel . ' " style="' . $extra_styles . '" data-index="' . $k . '">';

		// Show featured image.
if ( 'true' === $shortcode_args['show_featured_image'] ) {
	$featured_img_url = get_the_post_thumbnail_url( $id, 'full' );



	if ( $featured_img_url ) {
		$render .= '<div class="sp-container">';
		$render .= '<a href="' . esc_attr( $link ) . '" class="sp-posts-image-link sp-inline-block">';
		$render .= get_the_post_thumbnail(
			$id,
			'large',
			array(
				'class'   => 'sp-posts-image',
				'loading' => 'lazy',
				'alt'     => $title,
			)
		);
		$render .= '</a>';
		$render .= '</div>';
	}
}

		// Show title.
if ( 'true' === $shortcode_args['show_title'] ) {
	$render .= '<a href="' . esc_attr( $link ) . '">';
	$render .= '<div class="sp-container sp-posts-text sp-py-2">';
	$render .= '<' . esc_html( $shortcode_args['title_html_tag'] ) . ' class="sp-pt-2 sp-posts-block-title">';
	$render .= $title;
	$render .= '</' . esc_html( $shortcode_args['title_html_tag'] ) . '>';
	$render .= '</div>';
	$render .= '</a>';
}

		// Show meta data.
if ( 'true' === $shortcode_args['show_meta_options'] ) {
	$render .= '<div class="sp-container sp-posts-text sp-posts-block-meta-text">';

	if ( 'true' === $shortcode_args['show_date_modified_meta'] ) {
		$render .= esc_html( $modified_date );
	}

	if ( 'true' === $shortcode_args['show_author_meta'] ) {
		if ( 'true' === $shortcode_args['show_date_modified_meta'] ) {
			$render .= ' ' . esc_html( $shortcode_args['meta_separator'] ) . ' ';
		}
		$render .= esc_html( $author );
	}

	if ( 'true' === $shortcode_args['show_date_meta'] ) {
		if ( 'true' === $shortcode_args['show_author_meta'] || 'true' === $shortcode_args['show_date_modified_meta'] ) {
			$render .= ' ' . esc_html( $shortcode_args['meta_separator'] ) . ' ';
		}
		$render .= esc_html( $date );
	}

	if ( 'true' === $shortcode_args['show_time_meta'] ) {
		if ( 'true' === $shortcode_args['show_author_meta'] || 'true' === $shortcode_args['show_date_modified_meta'] || 'true' === $shortcode_args['show_date_meta'] ) {
			$render .= ' ' . esc_html( $shortcode_args['meta_separator'] ) . ' ';
		}
		$render .= esc_html( $time );
	}

	if ( 'true' === $shortcode_args['show_comment_count_meta'] ) {
		if ( 'true' === $shortcode_args['show_author_meta'] || 'true' === $shortcode_args['show_date_modified_meta'] || 'true' === $shortcode_args['show_date_meta'] || 'true' === $shortcode_args['show_time_meta'] ) {
			$render .= ' ' . esc_html( $shortcode_args['meta_separator'] ) . ' ';
		}

		if ( 1 > $comments_number ) {
			$render .= esc_html( 'No comments' );
		} elseif ( 1 === $comments_number ) {
			$render .= esc_html( $comments_number . ' Comment' );
		} elseif ( 2 <= $comments_number ) {
			$render .= esc_html( $comments_number . 'Comments' );
		}
	}

	$render .= '</div>';
}

		// Show post content.
if ( 'true' === $shortcode_args['show_excerpt'] ) {
	if ( ! empty( $excerpt ) ) {
		$render .= '<div class="sp-container sp-posts-text sp-py-2 sp-posts-block-excerpt">';
		$render .= esc_html( wp_trim_words( $excerpt, (int) $shortcode_args['excerpt_length'], null ) );
		$render .= '</div>';
	} else {
		if ( 0 < strlen( $content ) ) {
			$render .= '<div class="sp-container sp-posts-text sp-py-2 sp-posts-block-excerpt">';
			$render .= esc_html( wp_trim_words( $content, (int) $shortcode_args['excerpt_length'], null ) );
			$render .= '</div>';
		}
	}
}

		// Show read more button.
if ( 'true' === $shortcode_args['show_read_more'] ) {
	$render .= '<div class="sp-container sp-posts-text sp-py-2 sp-posts-block-read-more">';
	$render .= '<a href="' . esc_attr( $link ) . '">' . esc_html( $shortcode_args['read_more_text'] ) . '</a>';
	$render .= '</div>';
}

		$render .= '</div>';

echo $render;
