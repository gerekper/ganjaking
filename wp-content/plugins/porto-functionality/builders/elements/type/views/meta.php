<?php

extract( // @codingStandardsIgnoreLine
	shortcode_atts(
		array(
			'field'       => 'author',
			'date_format' => '',
			'icon_cls'    => '',
			'icon_pos'    => '',
			'el_class'    => '',
			'className'   => '',
		),
		$atts
	)
);

$wrap_cls = 'porto-tb-meta tb-meta-' . $field;
if ( $el_class && wp_is_json_request() ) {
	$wrap_cls .= ' ' . trim( $el_class );
}
if ( $className ) {
	$wrap_cls .= ' ' . trim( $className );
}

$icon_html = '';
if ( $icon_cls ) {
	$icon_html .= '<i class="porto-tb-icon ' . esc_attr( $icon_cls ) . '"></i>';
	$wrap_cls  .= ' porto-tb-icon-' . ( $icon_pos ? $icon_pos : 'left' );
}

echo '<span class="' . esc_attr( apply_filters( 'porto_elements_wrap_css_class', $wrap_cls, $atts, 'porto-tb/porto-meta' ) ) . '">';

if ( $icon_html && ! $icon_pos ) {
	echo porto_filter_output( $icon_html );
}

if ( ! empty( $GLOBALS['post'] ) ) {
	switch ( $field ) {
		case 'author':
			the_author_posts_link();
			break;
		case 'published_date':
			if ( ! $date_format ) {
				$date_format = get_option( 'date_format' );
			}
			echo get_the_time( $date_format );
			break;
		case 'modified_date':
			if ( ! $date_format ) {
				$date_format = get_option( 'date_format' );
			}
			echo get_the_modified_date( $date_format );
			break;
		case 'comments':
			comments_popup_link( esc_html__( 'No Comments', 'porto' ), esc_html__( '1 Comment', 'porto' ), esc_html__( '% Comments', 'porto' ) );
			break;
		case 'comments_number':
			echo (int) get_comments_number();
			break;
		case 'sku':
			global $product;
			if ( is_object( $product ) && '' !== $product->get_sku() ) {
				echo esc_html( $product->get_sku() );
			}
			break;
		default:
			$post_type = get_post_type();
			if ( 'post_tag' == $field ) {
				if ( 'post' == $post_type ) {
					$tags = get_the_tag_list( '', ', ', '' );
					if ( $tags && ! is_wp_error( $tags ) ) {
						echo porto_filter_output( $tags );
					}
				}
			} else {
				$terms = get_the_term_list( get_the_ID(), $field, '', ', ', '' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					echo porto_filter_output( $terms );
				}
			}
	}
}

if ( $icon_html && $icon_pos ) {
	echo porto_filter_output( $icon_html );
}

echo '</span>';
