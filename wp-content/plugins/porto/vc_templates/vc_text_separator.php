<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $title_align
 * @var $el_width
 * @var $style
 * @var $title
 * @var $align
 * @var $color
 * @var $accent_color
 * @var $el_class
 * @var $layout
 * @var $css
 * @var $border_width
 * @var $add_icon
 * Icons:
 * @var $i_type
 * @var $i_icon_fontawesome
 * @var $i_icon_openiconic
 * @var $i_icon_typicons
 * @var $i_icon_entypo
 * @var $i_icon_linecons
 * @var $i_color
 * @var $i_custom_color
 * @var $i_background_style
 * @var $i_background_color
 * @var $i_custom_background_color
 * @var $i_size
 * @var $i_css_animation
 *
 * Extra Params
 * @var $pattern
 * @var $element
 *
 * Shortcode class
 * @var $this WPBakeryShortcode_Vc_Text_Separator
 */

$css  = $title_align = $el_width = $style = $title = $align = $color = $accent_color = $el_class = $layout = $border_width = $add_icon = $i_type = $i_icon_fontawesome = $i_icon_openiconic = $i_icon_typicons = $i_icon_entypo = $i_icon_linecons = $i_color = $i_custom_color = $i_background_style = $i_background_color = $i_custom_background_color = $i_size = $i_css_animation = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class = 'vc_separator vc_text_separator';

$class .= ( '' !== $title_align ) ? ' vc_' . $title_align : '';
$class .= ( '' !== $el_width ) ? ' vc_sep_width_' . $el_width : ' vc_sep_width_100';
$class .= ( '' !== $style ) ? ' vc_sep_' . $style : '';
$class .= ( '' !== $align ) ? ' vc_sep_pos_' . $align : '';

$class .= ( 'separator_no_text' == $layout ) ? ' vc_separator_no_text' : '';

$default_color = porto_is_dark_skin() ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.15)';

if ( ! $accent_color ) {
	$accent_color = $default_color;
}

if ( 'custom' == $color || ! $color ) {
	$color = $accent_color;
}

$rand   = 'vc_sep_line' . rand();
$f_rand = false;

$line_class = '';
if ( $style ) {
	$line_class .= ' ' . $style;
}

$custom_css = '';

$inline_css_1 = '';
$inline_css_2 = '';
if ( ! $style && $color != $default_color ) {
	$inline_css_1 .= ( $color ) ? 'background-image: -webkit-linear-gradient(left, transparent, ' . esc_attr( $color ) . ');
	 background-image: linear-gradient(to right, transparent, ' . esc_attr( $color ) . ');' : '';
	$inline_css_2 .= ( $color ) ? 'background-image: -webkit-linear-gradient(left, ' . esc_attr( $color ) . ', transparent);
	 background-image: linear-gradient(to right, ' . esc_attr( $color ) . ', transparent);' : '';
} elseif ( 'solid' == $style && $color != $default_color ) {
	$inline_css_1 .= ( $color ) ? 'background-color: ' . esc_attr( $color ) . ';' : '';
	$inline_css_2 .= ( $color ) ? 'background-color: ' . esc_attr( $color ) . ';' : '';
} elseif ( 'dashed' == $style && $color != $default_color ) {
	if ( ! $f_rand ) {
		$line_class .= ' ' . $rand;
		$f_rand      = true;
	}
	$custom_css .= '.' . $rand . ':after {border-color:' . esc_html( $color ) . ' !important;}';
} elseif ( 'pattern' == $style ) {
	if ( $pattern ) {
		$pattern_url = wp_get_attachment_url( $pattern );
		if ( ! $f_rand ) {
			$line_class .= ' ' . $rand;
			$f_rand      = true;
		}
		$custom_css .= '.' . $rand . ':after {background-image:url(' . esc_url( $pattern_url ) . ') !important;}';
	}
}

if ( $border_width ) {
	if ( 'dashed' == $style ) {
		if ( ! $f_rand ) {
			$line_class .= ' ' . $rand;
			$f_rand      = true;
		}
		$custom_css .= '.' . $rand . ':after {border-width:' . esc_html( $border_width ) . 'px !important; margin-top:-' . esc_html( $border_width ) . 'px !important;}';
	} else {
		$inline_css_1 .= 'height:' . esc_attr( $border_width ) . 'px;';
		$inline_css_2 .= 'height:' . esc_attr( $border_width ) . 'px;';
	}
}

if ( $inline_css_1 ) {
	$inline_css_1 = ' style="' . $inline_css_1 . '"';
}
if ( $inline_css_2 ) {
	$inline_css_2 = ' style="' . $inline_css_2 . '"';
}

$class_to_filter  = $class;
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

if ( $custom_css ) {
	porto_filter_inline_css( '<style>' . $custom_css . '</style>' );
}

$icon = '';
if ( 'true' === $add_icon ) {
	vc_icon_element_fonts_enqueue( $i_type );
	$icon = $this->getVcIcon( $atts );
}

?>
<div class="<?php echo esc_attr( trim( $css_class ) ); ?>">
	<span class="vc_sep_holder vc_sep_holder_l"><span<?php echo porto_filter_output( $inline_css_1 ); ?> class="vc_sep_line<?php echo esc_attr( $line_class ); ?>"></span></span>
	<?php
	if ( $icon ) :
		?>
		<?php echo porto_filter_output( $icon ); ?><?php endif ?>
	<?php
	if ( $title ) :
		?>
		<<?php echo ! $element ? 'h4' : esc_html( $element ); ?>><?php echo wp_kses_post( $title ); ?></<?php echo ! $element ? 'h4' : esc_html( $element ); ?>><?php endif ?>
	<span class="vc_sep_holder vc_sep_holder_r"><span<?php echo porto_filter_output( $inline_css_2 ); ?> class="vc_sep_line<?php echo esc_attr( $line_class ); ?>"></span></span>
</div>
