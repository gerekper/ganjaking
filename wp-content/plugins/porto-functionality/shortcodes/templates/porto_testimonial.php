<?php
$output = $name = $name_color = $role = $company = $role_company_color = $author_url = $photo_url = $photo_id = $quote = $quote_color = $view = $remove_border = $color = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'name'               => '',
			'name_color'         => '',
			'role'               => '',
			'company'            => '',
			'role_company_color' => '',
			'author_url'         => '',
			'photo_url'          => '',
			'photo_id'           => '',
			'quote'              => '',
			'quote_color'        => '',
			'view'               => '',
			'style'              => '',
			'remove_border'      => '',
			'remove_bg'          => '',
			'alt_font'           => '',
			'skin'               => 'custom',
			'color'              => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);
$el_class  = porto_shortcode_extract_class( $el_class );
$img_attrs = '';
if ( ! $photo_url && $photo_id ) {
	$image = wp_get_attachment_image_src( $photo_id, 'thumbnail' );
	if ( $image ) {
		$photo_url  = $image[0];
		$img_attrs .= ' width="' . absint( $image[1] ) . '" height="' . absint( $image[2] ) . '"';
	}
}
$porto_url = str_replace( array( 'http:', 'https:' ), '', $photo_url );
$output    = '<div class="porto-testimonial wpb_content_element ' . ( $animation_type ? 'appear-animation ' : '' ) . $el_class . '"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= '>';
if ( 'transparent' == $view ) {
	$output .= '<div class="testimonial' . ( $style ? ' ' . $style : '' ) . ' testimonial-with-quotes' . ( 'white' == $color ? ' testimonial-light' : '' ) . ( $remove_border ? ' testimonial-no-borders' : '' ) . '">';
	if ( $photo_url ) {
		$output .= '<img class="img-responsive img-circle" src="' . esc_url( $porto_url ) . '" alt="' . esc_attr( $name ) . '"' . $img_attrs . '>';
	}
	$output .= '<blockquote class="testimonial-carousel' . ( $color ? ' ' . esc_attr( $color ) : '' ) . '"' . ( $quote_color ? ' style="color:' . esc_attr( $quote_color ) . '"' : '' ) . '>';
	$output .= '<p>' . do_shortcode( $content ? $content : $quote ) . '</p>';
	$output .= '</blockquote>';
	$output .= '<div class="testimonial-author"><p>';
	if ( $author_url ) {
		$output .= '<a href="' . esc_url( $author_url ) . '">';
	}
	$output .= '<strong' . ( $name_color ? ' style="color:' . esc_attr( $name_color ) . '"' : '' ) . '>' . esc_html( $name ) . '</strong>';
	if ( $author_url ) {
		$output .= '</a>';
	}
	$output .= '<span' . ( $role_company_color ? ' style="color:' . esc_attr( $role_company_color ) . '"' : '' ) . '>' . wp_kses_post( $role ) . ( ( $role && $company ) ? ' - ' : '' ) . esc_html( $company ) . '</span>';
	$output .= '</p></div></div>';
} elseif ( 'simple' == $view ) {
	$output .= '<div class="testimonial testimonial-style-6 testimonial-with-quotes' . ( 'white' == $color ? ' testimonial-light' : '' ) . '"><blockquote' . ( $quote_color ? ' style="color:' . esc_attr( $quote_color ) . '"' : '' ) . '><p>' . do_shortcode( $content ? $content : $quote ) . '</p></blockquote><div class="testimonial-author"><p>';
	if ( $author_url ) {
		$output .= '<a href="' . esc_url( $author_url ) . '">';
	}
	$output .= '<strong' . ( $name_color ? ' style="color:' . esc_attr( $name_color ) . '"' : '' ) . '>' . esc_html( $name ) . '</strong>';
	if ( $author_url ) {
		$output .= '</a>';
	}
	$output .= '<span' . ( $role_company_color ? ' style="color:' . esc_attr( $role_company_color ) . '"' : '' ) . '>' . wp_kses_post( $role ) . ( ( $role && $company ) ? ' - ' : '' ) . esc_html( $company ) . '</span></p>';
	$output .= '</div></div>';
} elseif ( 'advance' == $view ) {
		$output           .= '<div class="row m-b-md p-b-md">';
			$content_class = 'col-lg-12';
	if ( $photo_url ) {
		$output       .= '<div class="col-8 col-md-4 col-lg-2 p-t-lg">';
			$output   .= '<img src="' . esc_url( $porto_url ) . '" alt="' . esc_attr( $name ) . '" class="img-responsive custom-rounded-image"' . $img_attrs . '>';
		$output       .= '</div>';
		$content_class = 'col-lg-10';
	}
			$output             .= '<div class="col-12 col-md-12 ' . $content_class . '">';
				$output         .= '<div class="testimonial testimonial-advance testimonial-with-quotes' . ( 'white' == $color ? ' testimonial-light' : '' ) . ( $remove_border ? ' testimonial-no-borders' : '' ) . ' m-b-none">';
					$output     .= '<blockquote class="p-b-sm"' . ( $quote_color ? ' style="color:' . esc_attr( $quote_color ) . '"' : '' ) . '>';
						$output .= '<p>' . do_shortcode( $content ? $content : $quote ) . '</p>';
					$output     .= '</blockquote>';
					$output     .= '<div class="testimonial-author pull-left">';
						$output .= '<p>';
	if ( $author_url ) {
		$output .= '<a href="' . esc_url( $author_url ) . '">';
	}
								$output .= '<strong' . ( $name_color ? ' style="color:' . esc_attr( $name_color ) . '"' : '' ) . '>' . esc_html( $name ) . '</strong><span' . ( $role_company_color ? ' style="color:' . esc_attr( $role_company_color ) . '"' : '' ) . '>' . wp_kses_post( $role ) . ( ( $role && $company ) ? ' - ' : '' ) . esc_html( $company ) . '</span>';
	if ( $author_url ) {
		$output .= '</a>';
	}
						$output .= '</p>';
					$output     .= '</div>';
				$output         .= '</div>';
			$output             .= '</div>';
		$output                 .= '</div>';

} else {
	$output .= '<div class="testimonial' . ( ! $style && 'custom' != $skin ? ' testimonial-' . $skin : '' ) . ( $style ? ' ' . $style : '' ) . ( $remove_border ? ' testimonial-no-borders' : '' ) . ( $remove_bg ? ' testimonial-transparent-background' : '' ) . ( $alt_font ? ' testimonial-alternarive-font' : '' ) . '">';
	if ( 'default2' !== $view ) {
		$output .= '<blockquote' . ( $quote_color ? ' style="color:' . esc_attr( $quote_color ) . '"' : '' ) . '>';
		$output .= '<p>' . do_shortcode( $content ? $content : $quote ) . '</p>';
		$output .= '</blockquote>';
		if ( ! $remove_bg ) {
			$output .= '<div class="testimonial-arrow-down"></div>';
		}
	}
	$output .= '<div class="testimonial-author clearfix">';
	if ( $photo_url ) {
		switch ( $style ) {
			case 'testimonial-style-2':
			case 'testimonial-style-5':
			case 'testimonial-style-6':
				$output .= '<img class="img-responsive img-circle" src="' . esc_url( $photo_url ) . '" alt="' . esc_attr( $name ) . '"' . $img_attrs . '>';
				break;
			case 'testimonial-style-3':
			case 'testimonial-style-4':
				$output .= '<div class="testimonial-author-thumbnail"><img class="img-responsive img-circle" src="' . esc_url( $photo_url ) . '" alt="' . esc_attr( $name ) . '"' . $img_attrs . '></div>';
				break;
			default:
				$output .= '<div class="testimonial-author-thumbnail"><img src="' . esc_url( $photo_url ) . '" alt="' . esc_attr( $name ) . '" class="img-circle"' . $img_attrs . '></div>';
				break;
		}
	}
	$output .= '<p>';
	if ( $author_url ) {
		$output .= '<a href="' . esc_url( $author_url ) . '">';
	}
	$output .= '<strong' . ( $name_color ? ' style="color:' . esc_attr( $name_color ) . '"' : '' ) . '>' . esc_html( $name ) . '</strong>';
	if ( $author_url ) {
		$output .= '</a>';
	}
	$output .= '<span' . ( $role_company_color ? ' style="color:' . esc_attr( $role_company_color ) . '"' : '' ) . '>' . wp_kses_post( $role ) . ( ( $role && $company ) ? ' - ' : '' ) . esc_html( $company ) . '</span></p>';
	$output .= '</div>';

	if ( 'default2' === $view ) {
		if ( ! $remove_bg ) {
			$output .= '<div class="testimonial-arrow-down reversed"></div>';
		}
		$output .= '<blockquote' . ( $quote_color ? ' style="color:' . esc_attr( $quote_color ) . '"' : '' ) . '>';
		$output .= '<p>' . do_shortcode( $content ? $content : $quote ) . '</p>';
		$output .= '</blockquote>';
	}

	$output .= '</div>';
}
$output .= '</div>';
echo porto_filter_output( $output );
