<?php
$output = $type = $video_url = $image_url = $image_id = $merge_items = $el_class = '';
extract(
	shortcode_atts(
		array(
			'type'        => '',
			'video_url'   => '',
			'image_url'   => '',
			'image_id'    => '',
			'image_size'  => '',
			'merge_items' => 1,
			'el_class'    => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$merge_items = (int) $merge_items;
$merge       = '';
if ( 1 !== $merge_items ) {
	$merge = ' data-merge="' . esc_attr( $merge_items ) . '"';
}

if ( 'lazyload' === $type ) {
	$alt_text = '';
	if ( ! $image_url && $image_id ) {
		$image_url = wp_get_attachment_image_src( $image_id, $image_size ? $image_size : 'full' );
		$alt_text  = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		if ( isset( $image_url[0] ) ) {
			$image_size = $image_url[1] . 'x' . $image_url[2];
			$image_url  = $image_url[0];
		} else {
			$image_url = '';
		}
	}

	$image_url = str_replace( array( 'http:', 'https:' ), '', $image_url );
	if ( $image_size ) {
		$placeholder = porto_generate_placeholder( $image_size );
	}
	if ( $image_url ) {
		$output .= '<img class="owl-lazy ' . esc_attr( $el_class ) . '" src="' . esc_url( $placeholder[0] ) . '"' . ( (int) $placeholder[1] > 1 ? ' width="' . esc_attr( $placeholder[1] ) . '"' : '' ) . ( (int) $placeholder[2] > 1 ? ' height="' . esc_attr( $placeholder[2] ) . '"' : '' ) . ' data-src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $alt_text ) . '"' . $merge . '>';
	}
} elseif ( 'video' == $type ) {
	if ( $video_url ) {
		$output .= '<div class="item-video ' . esc_attr( $el_class ) . '"' . $merge . '><a class="owl-video" href="' . esc_url( $video_url ) . '"></a></div>';
	}
} else {
	$output .= '<div class="' . esc_attr( $el_class ) . '"' . $merge . '>';
	$output .= do_shortcode( $content );
	$output .= '</div>';
}

echo porto_filter_output( $output );
