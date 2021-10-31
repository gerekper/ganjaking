<?php
$output = $link = $image_url = $image_id = $fixed = $fixed_pos = $time = $noborders = $boxshadow = $height = $tip_label = $tip_skin = $el_class = '';
extract(
	shortcode_atts(
		array(
			'link'      => '',
			'image_url' => '',
			'image_id'  => '',
			'fixed'     => false,
			'fixed_pos' => '',
			'time'      => '',
			'height'    => '232px',
			'noborders' => false,
			'boxshadow' => false,
			'tip_label' => '',
			'tip_skin'  => 'custom',
			'el_class'  => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( ! $image_url && $image_id ) {
	$image_url = wp_get_attachment_url( $image_id );
}

$image_url = str_replace( array( 'http:', 'https:' ), '', $image_url );

if ( $image_url ) {
	wp_enqueue_script( 'lazyload' );

	$output = '<div class="porto-preview-image ' . esc_attr( $el_class ) . '">';

	//parse link
	$link     = ( '||' === $link ) ? '' : $link;
	$link     = vc_build_link( $link );
	$use_link = false;
	if ( strlen( $link['url'] ) > 0 ) {
		$use_link = true;
		$a_href   = $link['url'];
		$a_title  = $link['title'];
		$a_target = strlen( $link['target'] ) > 0 ? $link['target'] : '_self';
	}

	$attributes = array();
	if ( $use_link ) {
		$attributes[] = 'href="' . esc_url( trim( $a_href ) ) . '"';
		$attributes[] = 'title="' . esc_attr( trim( $a_title ) ) . '"';
		$attributes[] = 'target="' . esc_attr( trim( $a_target ) ) . '"';
	}

	$attributes = implode( ' ', $attributes );

	if ( $use_link ) {
		$output .= '<a ' . $attributes . '>';
	}

	$style = '';
	if ( $height && '232px' != $height ) {
		$style = ' style="height: ' . esc_attr( $height ) . '"';
	}

	$output .= '<span class="thumb-info thumb-info-preview' .
		( $fixed ? ' thumb-info-preview-fixed' . ( $fixed_pos ? ' thumb-info-preview-fixed-' . $fixed_pos : '' ) : '' ) .
		( $time ? ' thumb-info-preview-' . $time : '' ) .
		( $noborders ? ' thumb-info-no-borders' : '' ) .
		( $boxshadow ? ' thumb-info-box-shadow' : '' ) .
		'">' . ( $tip_label ? '<span class="thumb-info-ribbon' . ( ( 'custom' != $tip_skin ) ? ' thumb-info-ribbon-' . $tip_skin : '' ) . '"><span>' . $tip_label . '</span></span>' : '' ) . '<span class="thumb-info-wrapper">
		<span data-src="' . esc_url( $image_url ) . '" data-image="' . esc_url( $image_url ) . '" class="lazy thumb-info-image"' . $style . ' data-plugin-lazyload data-plugin-options="{\'appearEffect\': \'animated fadeIn\'}"></span><i class="fas fa-spinner fa-spin fa-fw"></i></span></span>';

	if ( $use_link ) {
		$output .= '</a>';
	}

	$output .= '</div>';
}

echo porto_filter_output( $output );
