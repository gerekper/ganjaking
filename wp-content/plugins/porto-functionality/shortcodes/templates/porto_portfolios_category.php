<?php
$output      = $title = $category_layout = $info_view = $thumb_image = $portfolios_counter = $cat_in = $number = $el_class = $hover_image_class = '';
$classes_arr = $cat_ids_arr = array();

extract(
	shortcode_atts(
		array(
			'title'              => '',
			'category_layout'    => 'stripes',
			'info_view'          => '',
			'thumb_image'        => '',
			'portfolios_counter' => 'show',
			'cat_in'             => '',
			'number'             => 5,
			'orderby'            => '',
			'order'              => '',
			'el_class'           => '',
		),
		$atts
	)
);

if ( $title ) {
	$output .= porto_shortcode_widget_title(
		array(
			'title'      => $title,
			'extraclass' => '',
		)
	);
}

$cat_args = array(
	'taxonomy' => 'portfolio_cat',
);
if ( $cat_in ) {
	$cat_args['orderby'] = 'include';
	$cat_ids_arr         = explode( ',', $cat_in );
	$cat_args['include'] = array_map( 'trim', $cat_ids_arr );
} else {
	if ( $orderby ) {
		$cat_args['orderby'] = sanitize_text_field( $orderby );
	}
	if ( $order ) {
		$cat_args['order'] = sanitize_text_field( $order );
	}
}
if ( $number ) {
	$cat_args['number'] = intval( $number );
}

$cats = get_terms( $cat_args );

if ( 'stripes' == $category_layout ) {
	$classes_arr[] = 'zoom' == $thumb_image ? '' : 'thumb-info-' . esc_attr( $thumb_image );
}

switch ( $info_view ) {
	case 'bottom-info':
		$classes_arr[] = 'thumb-info-bottom-info';
		break;
	case 'bottom-info-dark':
		$classes_arr[] = 'thumb-info-bottom-info thumb-info-bottom-info-dark';
		break;
	default:
		$classes_arr[] = 'thumb-info-basic-info';
}

$classes = implode( ' ', $classes_arr );

$el_class = porto_shortcode_extract_class( $el_class );

switch ( $category_layout ) {

	case 'stripes':
		$items_arr = array(
			'items' => 4,
			'lg'    => 3,
			'md'    => 2,
			'sm'    => 1,
			'xs'    => 1,
		);

		if ( 1 == $number || 1 == count( $cat_ids_arr ) ) {
			$items_arr = array(
				'items' => 1,
				'lg'    => 1,
				'md'    => 1,
				'sm'    => 1,
				'xs'    => 1,
			);
		}
		if ( 2 == $number || 2 == count( $cat_ids_arr ) ) {
			$items_arr = array(
				'items' => 2,
				'lg'    => 2,
				'md'    => 2,
				'sm'    => 1,
				'xs'    => 1,
			);
		}
		if ( 3 == $number || 3 == count( $cat_ids_arr ) ) {
			$items_arr = array(
				'items' => 3,
				'lg'    => 3,
				'md'    => 2,
				'sm'    => 1,
				'xs'    => 1,
			);
		}

		$carousel_options = array_merge(
			$items_arr,
			array(
				'loop' => false,
				'dots' => false,
				'nav'  => true,
			)
		);

		$output     .= '<div class="portfolio-' . esc_attr( $category_layout ) . ' ' . esc_attr( $el_class ) . '">';
			$output .= '<div class="porto-carousel owl-carousel owl-theme nav-center custom-carousel-arrows-style m-none" data-plugin-options=\'' . json_encode( $carousel_options ) . '\'>';

		foreach ( $cats as $cat ) {

			$cat_id      = $cat->term_id;
			$cat_title   = $cat->name;
			$cat_img_id  = porto_get_image_id( esc_url( get_metadata( 'portfolio_cat', $cat_id, 'category_image', true ) ) );
			$cat_img_arr = wp_get_attachment_image_src( $cat_img_id, 'portfolio-cat-stripes' );
			$cat_img_url = $cat_img_arr[0];
			$term        = get_term( $cat_id, 'portfolio_cat' );
			$term_count  = $term->count;

			$output             .= '<div>';
				$output         .= '<div class="portfolio-item">';
					$output     .= '<a href="' . esc_url( get_term_link( $cat_id ) ) . '" class="text-decoration-none">';
						$output .= '<span class="thumb-info ' . $classes . '"><span class="thumb-info-wrapper m-none">';

			if ( $cat_img_url ) {
				$output .= '<span class="background-image" style="background-image: url(' . esc_url( $cat_img_url ) . ')"></span>';
			}

			if ( ! $info_view ) { // Basic

				$output     .= '<span class="thumb-info-title text-capitalize alternative-font font-weight-light">';
					$output .= $cat_title;
				$output     .= '</span>';

				if ( 'show' == $portfolios_counter ) {
					$output         .= '<span class="thumb-info-icons position-style-1 text-color-light">';
						$output     .= '<span class="thumb-info-icon pictures background-color-primary">';
							$output .= $term_count;
							$output .= '<i class="far fa-image"></i>';
						$output     .= '</span>';
					$output         .= '</span>';
				}

				$output .= '<span class="thumb-info-plus"></span>';

			} else {

				$output     .= '<span class="thumb-info-title">';
					$output .= '<span class="thumb-info-inner">' . $cat_title . '</span>';
				if ( 'show' == $portfolios_counter ) {
					/* translators: %s: Portfolio count */
					$output .= '<span class="thumb-info-type">' . sprintf( _n( '%d Portfolio', '%d Portfolios', $term_count, 'porto-functionality' ), number_format_i18n( $term_count ) ) . '</span>';
				}
					$output .= '</span>';
			}



						$output     .= '</span></span>';
							$output .= '</a>';
							$output .= '</div>';
							$output .= '</div>';
		}
			$output .= '</div>';
		$output     .= '</div>';

		break;

	case 'parallax':
			wp_enqueue_script( 'skrollr' );
			$parallax_options = array( 'speed' => 1.5 );

			$output .= '<div class="' . esc_attr( $el_class ) . '">';

		foreach ( $cats as $cat ) {

			$cat_id      = $cat->term_id;
			$cat_title   = $cat->name;
			$cat_img_id  = porto_get_image_id( esc_url( get_metadata( 'portfolio_cat', $cat_id, 'category_image', true ) ) );
			$cat_img_arr = wp_get_attachment_image_src( $cat_img_id, 'portfolio-cat-parallax' );
			$cat_img_url = $cat_img_arr[0];
			$term        = get_term( $cat_id, 'portfolio_cat' );
			$term_count  = $term->count;


			$output .= '<a href="' . get_term_link( $cat_id ) . '" class="text-decoration-none">';

				$output     .= '<section class="portfolio-parallax parallax thumb-info section section-text-light section-parallax m-none ' . $classes . '" data-plugin-parallax data-plugin-options="' . esc_attr( json_encode( $parallax_options ) ) . '" data-image-src="' . $cat_img_url . '">';
					$output .= '<div class="container-fluid">';

			if ( ! $info_view ) { // Basic

				$output .= '<h2>' . $cat_title . '</h2>';

				if ( 'show' == $portfolios_counter ) {
					$output         .= '<span class="thumb-info-icons position-style-3 text-color-light">';
						$output     .= '<span class="thumb-info-icon pictures background-color-primary">';
							$output .= $term_count;
							$output .= '<i class="far fa-image"></i>';
						$output     .= '</span>';
					$output         .= '</span>';
				}

				$output .= '<span class="thumb-info-plus"></span>';

			} else {

				$output     .= '<span class="thumb-info-title">';
					$output .= '<span class="thumb-info-inner">' . $cat_title . '</span>';
				if ( 'show' == $portfolios_counter ) {
					/* translators: %s: Portfolio count */
					$output .= '<span class="thumb-info-type">' . sprintf( _n( '%d Portfolio', '%d Portfolios', $term_count, 'porto-functionality' ), number_format_i18n( $term_count ) ) . '</span>';
				}
					$output .= '</span>';

			}

					$output .= '</div>';
				$output     .= '</section>';

				$output .= '</a>';

		}

			$output .= '</div>';

		break;
	default:
		$output .= '<ul class="portfolio-cat-list list list-unstyled' . ( $el_class ? ' ' . esc_attr( trim( $el_class ) ) : '' ) . '">';

		$term_id = false;
		if ( is_archive() ) {
			$term = get_queried_object();
			if ( $term && isset( $term->term_id ) ) {
				$term_id = $term->term_id;
			}
		}
		foreach ( $cats as $cat ) {
			$output .= '<li class="portfolio-item' . ( $term_id && $term_id === $cat->term_id ? ' active' : '' ) . '">';
			$output .= '<a href="' . esc_url( get_term_link( $cat->term_id ) ) . '"><h5 class="portfolio-item-title">' . esc_html( $cat->name ) . '</h5></a>';
			$output .= '</li>';
		}
		$output .= '</ul>';

}

echo porto_filter_output( $output );
