<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $source
 * @var $text
 * @var $link
 * @var $google_fonts
 * @var $font_container
 * @var $el_class
 * @var $css
 * @var $font_container_data - returned from $this->getAttributes
 * @var $google_fonts_data - returned from $this->getAttributes
 *
 * Extra Params
 * @var $text_transform
 * @var $skin
 * @var $show_border
 * @var $border_skin
 * @var $border_color
 * @var $border_type
 * @var $border_size
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Custom_heading
 */

$source = $link = $google_fonts = $font_container = $el_id = $el_class = $css = $font_container_data = $google_fonts_data = array();

$text               = '';
$css_animation      = '';
$animation_type     = '';
$animation_delay    = '';
$animation_duration = '';

$floating_start_pos  = '';
$floating_speed      = '';
$floating_transition = 'yes';
$floating_horizontal = '';
$floating_duration   = '';
// This is needed to extract $font_container_data and $google_fonts_data
extract( $this->getAttributes( $atts ) );

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

extract( $this->getStyles( $el_class . $this->getCSSAnimation( $css_animation ), $css, $google_fonts_data, $font_container_data, $atts ) );

$skin        = $border_skin = 'custom';
$show_border = $border_color = $border_type = $border_size = '';
extract(
	shortcode_atts(
		array(
			'skin'         => 'custom',
			'show_border'  => false,
			'border_skin'  => 'custom',
			'border_color' => '',
			'border_type'  => 'bottom-border',
			'border_size'  => '',
		),
		$atts
	)
);

$settings = get_option( 'wpb_js_google_fonts_subsets' );
if ( is_array( $settings ) && ! empty( $settings ) ) {
	$subsets = '&subset=' . implode( ',', $settings );
} else {
	$subsets = '';
}

if ( ( ! isset( $atts['use_theme_fonts'] ) || 'yes' !== $atts['use_theme_fonts'] ) && isset( $google_fonts_data['values']['font_family'] ) && $google_fonts_data['values']['font_family'] ) {
	wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $google_fonts_data['values']['font_family'] . $subsets );
}
$text_align_left  = 'text-align: left';
$text_align_right = 'text-align: right';

if ( in_array( $text_align_left, $styles ) ) {
	$css_class .= ' align-left';
	$key        = array_search( $text_align_left, $styles );
	unset( $styles[ $key ] );
} elseif ( in_array( $text_align_right, $styles ) ) {
	$css_class .= ' align-right';
	$key        = array_search( $text_align_right, $styles );
	unset( $styles[ $key ] );
}

if ( isset( $font_weight ) && $font_weight ) {
	if ( empty( $styles ) ) {
		$styles = array();
	}
	$styles[] = 'font-weight:' . ( (int) $font_weight );
}

if ( isset( $letter_spacing ) && '' != $letter_spacing ) {
	if ( empty( $styles ) ) {
		$styles = array();
	}
	$styles[] = 'letter-spacing:' . $letter_spacing;
}

if ( ! empty( $styles ) ) {
	$new_styles = array();
	foreach ( $styles as $inline_style ) {
		$inline_style_arr = explode( ':', $inline_style );
		if ( count( $inline_style_arr ) === 2 && '' != trim( $inline_style_arr[1] ) ) {
			$new_styles[] = $inline_style;
		}
	}
	if ( ! empty( $new_styles ) ) {
		$style = 'style="' . esc_attr( implode( ';', $new_styles ) ) . '"';
	}
} else {
	$style = '';
}

if ( $el_id ) {
	$style .= ' id="' . esc_attr( $el_id ) . '"';
}
if ( $animation_type ) {
	$style .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$style .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$style .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
} elseif ( $floating_start_pos && $floating_speed ) {
	$floating_options = array( 'startPos' => $floating_start_pos, 'speed' => $floating_speed );
	if ( $floating_transition ) {
		$floating_options['transition'] = true;
	} else {
		$floating_options['transition'] = false;
	}
	if ( $floating_horizontal ) {
		$floating_options['horizontal'] = true;
	} else {
		$floating_options['horizontal'] = false;
	}
	if ( $floating_duration ) {
		$floating_options['transitionDuration'] = absint( $floating_duration );
	}
	$style .= ' data-plugin-float-element data-plugin-options="' . esc_attr( json_encode( $floating_options ) ) . '"';
}

if ( 'post_title' === $source ) {
	$text = get_the_title( get_the_ID() );
}

if ( ! empty( $link ) ) {
	$link = vc_build_link( $link );
	$text = '<a href="' . esc_url( $link['url'] ) . '"' . ( $link['target'] ? ' target="' . esc_attr( $link['target'] ) . '"' : '' ) . ( $link['title'] ? ' title="' . esc_attr( $link['title'] ) . '"' : '' ) . '>' . wp_specialchars_decode( $text ) . '</a>';
}

$output = '';
if ( apply_filters( 'vc_custom_heading_template_use_wrapper', false ) || $show_border ) {
	if ( $show_border ) {
		$wrap_class = 'heading' . rand();
		$css_class .= ' heading heading-border';
		if ( 'custom' !== $border_skin ) {
			$css_class .= ' heading-' . $border_skin;
		}
		if ( 'middle-border-reverse' === $border_type || 'middle-border-center' === $border_type ) {
			$css_class .= ' heading-middle-border';
		}
		if ( $border_type ) {
			$css_class .= ' heading-' . $border_type;
		}
		if ( $border_size ) {
			$css_class .= ' heading-border-' . $border_size;
		}
		if ( $border_color ) {
			$css_class .= ' ' . $wrap_class;
			?>
			<style>
				<?php
				if ( 'bottom-border' === $border_type || 'bottom-double-border' === $border_type ) :
					?>
					.<?php echo esc_html( $wrap_class ); ?> <?php echo esc_html( $font_container_data['values']['tag'] ); ?> { border-bottom-color: <?php echo esc_html( $border_color ); ?> !important }<?php endif; ?>
				<?php
				if ( ! ( 'bottom-border' === $border_type || 'bottom-double-border' === $border_type ) ) :
					?>
					.<?php echo esc_html( $wrap_class ); ?> .heading-tag:before, .<?php echo esc_html( $wrap_class ); ?> .heading-tag:after { border-top-color: <?php echo esc_html( $border_color ); ?> !important }<?php endif; ?>
			</style>
			<?php
		}
	}
	$output       .= '<div class="' . esc_attr( $css_class ) . '">';
	$heading_class = 'heading-tag';
	if ( 'custom' !== $skin ) {
		$heading_class .= ' heading-' . $skin;
	}
	if ( $text_transform ) {
		$heading_class .= ' ' . $text_transform;
	}
	$output .= '<' . esc_html( $font_container_data['values']['tag'] ) . ' ' . trim( $style ) . ( $heading_class ? ' class="' . esc_attr( $heading_class ) . '"' : '' ) . '>';
	$output .= force_balance_tags( wp_specialchars_decode( $text ) );
	$output .= '</' . $font_container_data['values']['tag'] . '>';
	$output .= '</div>';
} else {
	if ( $text_transform ) {
		$css_class .= ' ' . $text_transform;
	}
	if ( 'custom' !== $skin ) {
		$css_class .= ' heading-' . $skin;
	}
	$output .= '<' . $font_container_data['values']['tag'] . ' ' . $style . ' class="' . esc_attr( $css_class ) . '">';
	$output .= force_balance_tags( wp_specialchars_decode( $text ) );
	$output .= '</' . esc_html( $font_container_data['values']['tag'] ) . '>';
}

echo porto_filter_output( $output );
