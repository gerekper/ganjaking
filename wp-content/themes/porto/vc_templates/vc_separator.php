<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $el_width
 * @var $style
 * @var $color
 * @var $border_width
 * @var $accent_color
 * @var $el_class
 * @var $align
 * @var string $css
 *
 * Extra Params
 * @var $type
 * @var $gap
 * @var $pattern
 * @var $pattern_repeat
 * @var $pattern_position
 * @var $pattern_height
 * @var $show_icon
 * @var $icon_type
 * @var $icon_image
 * @var $icon
 * @var $icon_simpleline
 * @var $icon_skin
 * @var $icon_style
 * @var $icon_size
 * @var $icon_pos
 * @var $icon_color
 * @var $icon_bg_color
 * @var $icon_border_color
 * @var $icon_wrap_border_color
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Separator
 */
$css  = $css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

if ( isset( $color ) && '' != $color ) {
	switch ( $color ) {
		case 'sky':
			$color = '#5aa1e3';
			break;
		case 'juicy_pink':
			$color = '#f4524d';
			break;
		case 'peacoc':
			$color = '#4cadc9';
			break;
		case 'chino':
			$color = '#cec2ab';
			break;
		case 'mulled_wine':
			$color = '#50485b';
			break;
		case 'vista_blue':
			$color = '#75d69c';
			break;
		case 'sandy_brown':
			$color = '#f79468';
			break;
	}
}

echo '<div class="porto-separator ' . esc_attr( $gap ) . ' ' . $this->getExtraClass( $el_class ) . '">';
$class_to_filter  = '';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getCSSAnimation( $css_animation );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

global $porto_settings;
$default_color = porto_is_dark_skin() ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.15)';

if ( ! $accent_color ) {
	$accent_color = $default_color;
}

$css_class .= ' ' . $align;
if ( 'custom' == $color || ! $color ) {
	$color = $accent_color;
}
if ( ! $align ) {
	$align = 'align_center';
}

switch ( $icon_type ) {
	case 'simpleline':
		$icon_class = $icon_simpleline;
		break;
	case 'image':
		$icon_class = 'icon-image';
		break;
	default:
		$icon_class = $icon;
}

if ( ! $show_icon ) {
	$icon_class = '';
}

if ( $icon_class ) {
	if ( 'custom' != $icon_skin ) {
		$css_class .= ' divider-' . $icon_skin;
	}
	if ( $icon_style ) {
		$css_class .= ' divider-' . $icon_style;
	}
	if ( $icon_size ) {
		$css_class .= ' divider-icon-' . $icon_size;
	}
	if ( $icon_pos ) {
		$css_class .= ' divider-' . $icon_pos;
	}
}

if ( $type ) {
	$style = 'solid';
}

if ( $style ) {
	if ( 'solid' == $style ) {
		$css_class .= ( $icon_class ? ' divider-' : ' ' ) . $style;
	} else {
		$css_class .= ' ' . $style;
	}
}

$inline_style = '';
$custom_css   = '';
$rand         = 'separator' . rand();
$f_rand       = false;

if ( ! $style && ( $color != $default_color || 'align_center' != $align ) ) {
	$inline_style .= 'background-image: -webkit-linear-gradient(left' . ( ( 'align_center' == $align || 'align_right' == $align ) ? ', transparent' : '' ) . ', ' . esc_attr( $color ) .
	( ( 'align_center' == $align || 'align_left' == $align ) ? ', transparent' : '' ) .
	'); background-image: linear-gradient(to right' . ( ( 'align_center' == $align || 'align_right' == $align ) ? ', transparent' : '' ) . ', ' . esc_attr( $color ) .
	( ( 'align_center' == $align || 'align_left' == $align ) ? ', transparent' : '' ) . ');';
} elseif ( 'solid' == $style && $color != $default_color ) {
	$inline_style .= 'background-color:' . esc_attr( $color ) . ';';
} elseif ( 'dashed' == $style && $color != $default_color ) {
	if ( ! $f_rand ) {
		$css_class .= ' ' . $rand;
		$f_rand     = true;
	}
	$custom_css .= '.' . $rand . ':after {border-color:' . esc_html( $color ) . ' !important;}';
} elseif ( 'pattern' == $style ) {
	if ( $pattern ) {
		$pattern_url = wp_get_attachment_url( $pattern );
		if ( ! $f_rand ) {
			$css_class .= ' ' . $rand;
			$f_rand     = true;
		}

		$custom_css .= '.' . $rand . ':after {background-image:url(' . esc_url( $pattern_url ) . ') !important;';
		if ( $pattern_repeat ) {
			$custom_css .= 'background-repeat:' . esc_html( $pattern_repeat ) . ' !important;';
		}

		if ( $pattern_position ) {
			$custom_css .= 'background-position:' . esc_html( $pattern_position ) . ' !important;';
		}

		if ( 15 != $pattern_height ) {
			$custom_css .= 'height:' . (int) $pattern_height . 'px !important;';
			$custom_css .= 'margin-top:-' . ( (int) $pattern_height / 2 ) . 'px !important;';
		}

		$custom_css .= '}';

		if ( 15 != $pattern_height ) {
			$custom_css .= '.' . $rand . ' {height:' . (int) $pattern_height . 'px !important;}';
		}
	}
}

if ( $border_width ) {
	if ( 'dashed' == $style ) {
		if ( ! $f_rand ) {
			$css_class .= ' ' . $rand;
			$f_rand     = true;
		}

		$custom_css .= '.' . $rand . ':after {border-width:' . esc_attr( $border_width ) . 'px !important;margin-top:-' . esc_attr( $border_width ) . 'px !important;}';
	} else {
		$inline_style .= 'height:' . esc_attr( $border_width ) . 'px;';
	}
}
if ( ( $icon_class || 'small' != $type ) && $el_width ) {
	$inline_style .= 'width:' . esc_attr( $el_width ) . '%;';
}

if ( $inline_style ) {
	$inline_style = ' style="' . $inline_style . '"';
}

if ( $custom_css ) {
	porto_filter_inline_css( '<style>' . $custom_css . '</style>' );
}

if ( $icon_class ) {
	$divider_class = 'divider' . rand();
	if ( 'custom' == $icon_skin && ( $icon_color || $icon_bg_color || $icon_border_color || $icon_wrap_border_color ) ) :
		$css_class .= ' ' . $divider_class;
		ob_start();
		?>
		<style>
		<?php
		if ( $icon_color || $icon_bg_color || $icon_border_color ) :
			?>
			.<?php echo esc_html( $divider_class ); ?> i {
				<?php
				if ( $icon_color ) :

					?>
				color: <?php echo esc_html( $icon_color ); ?> !important;
					<?php
endif;
				if ( $icon_bg_color ) :

					?>
				background-color: <?php echo esc_html( $icon_bg_color ); ?> !important;
					<?php
endif;
				if ( $icon_border_color ) :

					?>
				border-color: <?php echo esc_html( $icon_border_color ); ?> !important;
					<?php
endif;
				?>
			}
			<?php
			endif;

		if ( $icon_wrap_border_color ) :
			?>
			.<?php echo esc_html( $divider_class ); ?> i:after {
				<?php
				if ( $icon_wrap_border_color ) :

					?>
				border-color: <?php echo esc_html( $icon_wrap_border_color ); ?> !important;
					<?php
endif;
				?>
			}
			<?php
			endif;
		?>
		</style>
		<?php
		porto_filter_inline_css( ob_get_clean() );
	endif;

	echo '<div class="divider ' . esc_attr( $css_class ) . '"' . $inline_style . '>';

	if ( $icon_class ) {
		echo '<i class="' . esc_attr( $icon_class ) . '">';
		if ( 'icon-image' == $icon_class && $icon_image ) {
			$icon_image = preg_replace( '/[^\d]/', '', $icon_image );
			$image_url  = wp_get_attachment_url( $icon_image );
			$alt_text   = get_post_meta( $icon_image, '_wp_attachment_image_alt', true );
			$image_url  = str_replace( array( 'http:', 'https:' ), '', $image_url );
			if ( $image_url ) {
				echo '<img alt="' . esc_attr( $alt_text ) . '" src="' . esc_url( $image_url ) . '">';
			}
		}

		echo '</i>';
	}
	echo '</div>';
} else {
	if ( 'small' == $type ) {
		echo '<div class="divider divider-small ' . esc_attr( $css_class ) . ' ' . ( $align ? ( 'align_left' == $align ? '' : str_replace( 'align_', 'divider-small-', $align ) ) : 'divider-small-center' ) . '">' . '<hr ' . $inline_style . '>' . '</div>';
	} else {
		echo '<hr class="separator-line ' . esc_attr( $css_class ) . '"' . $inline_style . '>';
	}
}

echo '</div>';
