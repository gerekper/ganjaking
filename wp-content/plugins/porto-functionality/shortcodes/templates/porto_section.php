<?php
$output = $anchor = $container = $section_text_color = $text_align = $is_section = $section_skin = $section_color_scale = $section_skin_scale = $remove_margin_top = $remove_margin_bottom = $remove_padding_top = $remove_padding_bottom = $remove_border = $show_divider = $divider_pos = $divider_color = $divider_height = $show_divider_icon = $divider_icon_type = $divider_icon_image = $divider_icon = $divider_icon_simpleline = $divider_icon_skin = $divider_icon_color = $divider_icon_bg_color = $divider_icon_border_color = $divider_icon_wrap_border_color = $divider_icon_style = $divider_icon_pos = $divider_icon_size = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'anchor'                         => '',
			'container'                      => false,
			'section_text_color'             => '',
			'text_align'                     => '',
			'is_section'                     => false,
			'section_skin'                   => 'parallax',
			'section_color_scale'            => '',
			'section_skin_scale'             => '',
			'remove_margin_top'              => false,
			'remove_margin_bottom'           => false,
			'remove_padding_top'             => false,
			'remove_padding_bottom'          => false,
			'remove_border'                  => false,
			'show_divider'                   => false,
			'divider_pos'                    => '',
			'divider_color'                  => '',
			'divider_height'                 => '',
			'show_divider_icon'              => false,
			'divider_icon_type'              => 'fontawesome',
			'divider_icon_image'             => '',
			'divider_icon'                   => '',
			'divider_icon_simpleline'        => '',
			'divider_icon_skin'              => 'custom',
			'divider_icon_color'             => '',
			'divider_icon_bg_color'          => '',
			'divider_icon_border_color'      => '',
			'divider_icon_wrap_border_color' => '',
			'divider_icon_style'             => '',
			'divider_icon_pos'               => '',
			'divider_icon_size'              => '',
			'animation_type'                 => '',
			'animation_duration'             => 1000,
			'animation_delay'                => 0,
			'el_class'                       => '',
		),
		$atts
	)
);

$el_class    = porto_shortcode_extract_class( $el_class );
$css_classes = array();

if ( $container ) {
	$el_class .= ' container';
}

$id = '';
if ( $anchor ) {
	$id = ' id="' . $anchor . '"';
}

$divider_output = '';

if ( $is_section ) {
	$css_classes[] .= ' section';
	if ( $section_skin ) {
		$css_classes[] .= 'section-' . $section_skin;
		if ( $section_skin_scale ) {
			$css_classes[] .= 'section-' . $section_skin . '-' . $section_skin_scale;
		}
	}
	if ( 'default' == $section_skin && $section_color_scale ) {
		$css_classes[] .= 'section-default-' . $section_color_scale;
	}
	if ( $section_text_color ) {
		$css_classes[] .= 'section-text-' . $section_text_color;
	}

	if ( $remove_margin_top ) {
		$css_classes[] .= 'm-t-none';
	}

	if ( $remove_margin_bottom ) {
		$css_classes[] .= 'm-b-none';
	}

	if ( $remove_padding_top ) {
		$css_classes[] .= 'p-t-none';
	}

	if ( $remove_padding_bottom ) {
		$css_classes[] .= 'p-b-none';
	}

	if ( $remove_border ) {
		$css_classes[] .= 'section-no-borders';
	}

	if ( $show_divider ) {
		if ( 'bottom' === $divider_pos ) {
			$css_classes[] .= 'section-with-divider-footer';
		} else {
			$css_classes[] .= 'section-with-divider';
		}

		$divider_classes = array( 'section-divider', 'divider', 'divider-solid' );
		if ( 'custom' != $divider_icon_skin ) {
			$divider_classes[] = 'divider-' . $divider_icon_skin;
		}
		if ( $divider_icon_style ) {
			$divider_classes[] = 'divider-' . $divider_icon_style;
		}
		if ( $divider_icon_size ) {
			$divider_classes[] = 'divider-icon-' . $divider_icon_size;
		}
		if ( $divider_icon_pos ) {
			$divider_classes[] = 'divider-' . $divider_icon_pos;
		}

		$divider_inline_style = '';
		if ( $divider_color ) {
			$divider_inline_style .= 'background-color:' . $divider_color . ';';
		}
		if ( $divider_height ) {
			$divider_inline_style .= 'height:' . (int) $divider_height . 'px;';
		}
		if ( $remove_border ) {
			if ( 'bottom' === $divider_pos ) {
				$divider_inline_style .= 'margin-bottom: -51px;';
			} else {
				$divider_inline_style .= 'margin-top: -51px;';
			}
		}

		if ( $divider_inline_style ) {
			$divider_inline_style = ' style="' . esc_attr( $divider_inline_style ) . '"';
		}

		switch ( $divider_icon_type ) {
			case 'simpleline':
				$divider_icon_class = $divider_icon_simpleline;
				break;
			case 'image':
				$divider_icon_class = 'icon-image';
				break;
			default:
				$divider_icon_class = $divider_icon;
		}

		$divider_class_escaped = 'divider' . rand();
		if ( $show_divider_icon && $divider_icon_class && 'custom' == $divider_icon_skin && ( $divider_icon_color || $divider_icon_bg_color || $divider_icon_border_color || $divider_icon_wrap_border_color ) ) :
			$divider_classes[] = $divider_class_escaped;
			?>
			<style>
			<?php
			if ( $divider_icon_color || $divider_icon_bg_color || $divider_icon_border_color ) :
				?>
				.<?php echo $divider_class_escaped; ?> i {
					<?php
					if ( $divider_icon_color ) :

						?>
					color: <?php echo esc_html( $divider_icon_color ); ?> !important;
						<?php
endif;
					if ( $divider_icon_bg_color ) :

						?>
				background-color: <?php echo esc_html( $divider_icon_bg_color ); ?> !important;
						<?php
endif;
					if ( $divider_icon_border_color ) :

						?>
				border-color: <?php echo esc_html( $divider_icon_border_color ); ?> !important;
						<?php
endif;
					?>
				}
				<?php
				endif;
			if ( $divider_icon_wrap_border_color ) :
				?>
				.<?php echo $divider_class_escaped; ?> i:after {
					<?php
					if ( $divider_icon_wrap_border_color ) :

						?>
					border-color: <?php echo esc_html( $divider_icon_wrap_border_color ); ?> !important;
						<?php
endif;
					?>
				}
				<?php
				endif;
			?>
				</style>
			<?php
		endif;

		$divider_output = '<div class="' . implode( ' ', $divider_classes ) . '"' . $divider_inline_style . '>';
		if ( $show_divider_icon && $divider_icon_class ) {
			$divider_output .= '<i class="' . $divider_icon_class . '">';
			if ( 'icon-image' == $divider_icon_class && $divider_icon_image ) {
				$divider_icon_image = preg_replace( '/[^\d]/', '', $divider_icon_image );
				$divider_image_url  = wp_get_attachment_url( $divider_icon_image );
				$divider_image_url  = str_replace( array( 'http:', 'https:' ), '', $divider_image_url );
				if ( $divider_image_url ) {
					$alt_text        = get_post_meta( $divider_icon_image, '_wp_attachment_image_alt', true );
					$divider_output .= '<img alt="' . esc_attr( $alt_text ) . '" src="' . esc_url( $divider_image_url ) . '">';
				}
			}
			$divider_output .= '</i>';
		}
		$divider_output .= '</div>';
	}
}

if ( $text_align ) {
	$css_classes[] .= 'text-' . $text_align;
}

$output = '<div' . $id . ' class="porto-section">';

$css_classes[] = $el_class;

$output .= '<section class="' . esc_attr( implode( ' ', $css_classes ) ) . '"';
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

if ( $show_divider && ! $divider_pos ) {
	$output .= $divider_output;
}

$output .= do_shortcode( $content );

if ( $show_divider && 'bottom' === $divider_pos ) {
	$output .= $divider_output;
}

$output .= '</section>';

$output .= '</div>';

echo porto_filter_output( $output );
