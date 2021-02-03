<?php

if ( ! function_exists( 'porto_override_shortcodes' ) ) {
	function porto_override_shortcodes( $item_space, $item_animation ) {
		global $shortcode_tags, $_shortcode_tags;
		$_shortcode_tags = $shortcode_tags;
		$disabled_tags   = array( '' );
		foreach ( $shortcode_tags as $tag => $cb ) {
			if ( in_array( $tag, $disabled_tags ) ) {
				continue;
			}
			$shortcode_tags[ $tag ]              = 'porto_wrap_shortcode_in_div';
			$_shortcode_tags['porto_item_space'] = $item_space;
			$_shortcode_tags['item_animation']   = $item_animation;
		}
	}
}

if ( ! function_exists( 'porto_wrap_shortcode_in_div' ) ) {
	function porto_wrap_shortcode_in_div( $attr, $content, $tag ) {
		global $_shortcode_tags;

		$attrs = $_shortcode_tags['item_animation'] ? ' data-appear-animation="' . esc_attr( $_shortcode_tags['item_animation'] ) . '"' : '';
		return '<div class="porto-item-wrap"' . $attrs . '>' . call_user_func( $_shortcode_tags[ $tag ], $attr, $content, $tag ) . '</div>';
	}
}
if ( ! function_exists( 'porto_restore_shortcodes' ) ) {
	function porto_restore_shortcodes() {
		global $shortcode_tags, $_shortcode_tags;
		// Restore the original callbacks
		if ( isset( $_shortcode_tags ) ) {
			$shortcode_tags = $_shortcode_tags;
		}
	}
}

$slides_on_desk = $slides_on_tabs = $slides_on_mob = $slide_to_scroll = $speed = $infinite_loop = $autoplay = $autoplay_speed = '';
$lazyload       = $arrows = $dots = $dots_icon = $next_icon = $prev_icon = $dots_color = $swipe = $touch_move = '';
$rtl            = $arrow_color = $arrow_size = $arrow_style = $arrow_border_color = $item_space = $el_class = '';
$item_animation = '';

wp_enqueue_style( 'font-awesome' );

extract(
	shortcode_atts(
		array(
			'slides_on_desk'       => '5',
			'slides_on_tabs'       => '3',
			'slides_on_mob'        => '2',
			'slide_to_scroll'      => '',
			'speed'                => '300',
			'infinite_loop'        => 'on',
			'autoplay'             => 'on',
			'autoplay_speed'       => '5000',
			'lazyload'             => '',
			'arrows'               => 'show',
			'dots'                 => 'show',
			'icon_type'            => 'fontawesome',
			'dots_icon_type'       => 'fontawesome',
			'dots_icon'            => 'far fa-circle',
			'next_icon'            => 'fas fa-chevron-right',
			'prev_icon'            => 'fas fa-chevron-left',
			'next_icon_simpleline' => '',
			'next_icon_porto'      => '',
			'prev_icon_simpleline' => '',
			'prev_icon_porto'      => '',
			'dots_icon_simpleline' => '',
			'dots_icon_porto'      => '',
			'dots_color'           => '#333333',
			'arrow_color'          => '#333333',
			'arrow_size'           => '20',
			'arrow_style'          => 'default',
			'arrow_bg_color'       => '',
			'swipe'                => 'true',
			'touch_move'           => 'on',
			'rtl'                  => '',
			'item_space'           => '15',
			'el_class'             => '',
			'item_animation'       => '',
			'animation_type'       => '',
			'adaptive_height'      => '',
			'css_ad_caraousel'     => '',
		),
		$atts
	)
);

wp_enqueue_script( 'jquery-slick' );
wp_enqueue_script( 'porto_shortcodes_ultimate_carousel_loader_js' );

if ( $animation_type ) {
	$item_animation = $animation_type;
}

$uid_escaped = uniqid( rand() );

$settings_escaped = $responsive = $infinite = $dot_display = $custom_dots = $arr_style = $wrap_data = $design_style = '';

switch ( $icon_type ) {
	case 'simpleline':
		$next_icon = $next_icon_simpleline;
		$prev_icon = $prev_icon_simpleline;
		break;
	case 'porto':
		$next_icon = $next_icon_porto;
		$prev_icon = $prev_icon_porto;
		break;
}
switch ( $dots_icon_type ) {
	case 'simpleline':
		$dots_icon = $dots_icon_simpleline;
		break;
	case 'porto':
		$dots_icon = $dots_icon_porto;
		break;
}

if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
	$desing_style = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_ad_caraousel, ' ' ), 'porto_ultimate_carousel', $atts );
	$desing_style = esc_attr( $desing_style );
}
if ( 'single' == $slide_to_scroll ) {
	$slide_to_scroll = 1;
} else {
	$slide_to_scroll = $slides_on_desk;
}

$arr_style .= 'color:' . $arrow_color . '; font-size:' . $arrow_size . 'px;';
if ( 'circle-bg' == $arrow_style || 'square-bg' == $arrow_style ) {
	$arr_style .= 'background:' . esc_attr( $arrow_bg_color ) . ';';
}

if ( 'off' !== $dots ) {
	$settings_escaped .= 'dots: true,';
} else {
	$settings_escaped .= 'dots: false,';
}
if ( 'off' !== $autoplay ) {
	$settings_escaped .= 'autoplay: true,';
}
if ( '' !== $autoplay_speed ) {
	$settings_escaped .= 'autoplaySpeed: ' . $autoplay_speed . ',';
}
if ( '' !== $speed ) {
	$settings_escaped .= 'speed: ' . $speed . ',';
}
if ( 'off' === $infinite_loop ) {
	$settings_escaped .= 'infinite: false,';
} else {
	$settings_escaped .= 'infinite: true,';
}
if ( 'off' !== $lazyload && '' !== $lazyload ) {
	$settings_escaped .= 'lazyLoad: true,';
}

if ( is_rtl() ) {
	if ( 'off' !== $arrows ) {
		$settings_escaped .= 'arrows: true,';
		$settings_escaped .= 'nextArrow: \'<button type="button" role="button" aria-label="Next" style="' . esc_attr( $arr_style ) . '" class="slick-next ' . esc_attr( $arrow_style ) . '"><i class="' . esc_attr( $prev_icon ) . '"></i></button>\',';
		$settings_escaped .= 'prevArrow: \'<button type="button" role="button" aria-label="Previous" style="' . esc_attr( $arr_style ) . '" class="slick-prev ' . esc_attr( $arrow_style ) . '"><i class="' . esc_attr( $next_icon ) . '"></i></button>\',';
	} else {
		$settings_escaped .= 'arrows: false,';
	}
} else {
	if ( 'off' !== $arrows ) {
		$settings_escaped .= 'arrows: true,';
		$settings_escaped .= 'nextArrow: \'<button type="button" role="button" aria-label="Next" style="' . esc_attr( $arr_style ) . '" class="slick-next ' . esc_attr( $arrow_style ) . '"><i class="' . esc_attr( $next_icon ) . '"></i></button>\',';
		$settings_escaped .= 'prevArrow: \'<button type="button" role="button" aria-label="Previous" style="' . esc_attr( $arr_style ) . '" class="slick-prev ' . esc_attr( $arrow_style ) . '"><i class="' . esc_attr( $prev_icon ) . '"></i></button>\',';
	} else {
		$settings_escaped .= 'arrows: false,';
	}
}


if ( '' !== $slide_to_scroll ) {
	$settings_escaped .= 'slidesToScroll:' . esc_js( $slide_to_scroll ) . ',';
}
if ( '' !== $slides_on_desk ) {
	$settings_escaped .= 'slidesToShow:' . esc_js( $slides_on_desk ) . ',';
}
if ( '' == $slides_on_mob ) {
	$slides_on_mob = $slides_on_desk;
}
if ( '' == $slides_on_tabs ) {
	$slides_on_tabs = $slides_on_desk;
}

	$settings_escaped .= 'swipe: true,';
	$settings_escaped .= 'draggable: true,';

if ( 'on' == $touch_move ) {
	$settings_escaped .= 'touchMove: true,';
} else {
	$settings_escaped .= 'touchMove: false,';
}

if ( 'off' !== $rtl && '' !== $rtl ) {
	$settings_escaped .= 'rtl: true,';
	$wrap_data         = 'dir="rtl"';
}

$site_rtl = 'false';
if ( is_rtl() ) {
	$site_rtl = 'true';
}


if ( is_rtl() ) {
	$settings_escaped .= 'rtl: true,';
}

$settings_escaped .= 'pauseOnHover: true,';


if ( 'on' === $adaptive_height ) {
	$settings_escaped .= 'adaptiveHeight: true,';
}

$settings_escaped .= 'responsive: [
				{
				  breakpoint: 1025,
				  settings: {
					slidesToShow: ' . esc_js( $slides_on_desk ) . ',
					slidesToScroll: ' . esc_js( $slide_to_scroll ) . ', ' . esc_js( $infinite ) . ' ' . esc_js( $dot_display ) . '
				  }
				},
				{
				  breakpoint: 769,
				  settings: {
					slidesToShow: ' . esc_js( $slides_on_tabs ) . ',
					slidesToScroll: ' . esc_js( $slides_on_tabs ) . '
				  }
				},
				{
				  breakpoint: 481,
				  settings: {
					slidesToShow: ' . esc_js( $slides_on_mob ) . ',
					slidesToScroll: ' . esc_js( $slides_on_mob ) . '
				  }
				}
			],';
$settings_escaped .= 'pauseOnDotsHover: true,';

if ( 'off' !== $dots_icon && '' !== $dots_icon ) {
	if ( 'off' !== $dots_color && '' !== $dots_color ) {
		$custom_dots = 'style="color:' . esc_attr( $dots_color ) . ';"';
	}
	$settings_escaped .= 'customPaging: function(slider, i) {
	   return \'<i type="button" ' . $custom_dots . ' class="' . esc_attr( $dots_icon ) . '" data-role="none"></i>\';
	},';
}

ob_start();
$uniqid = uniqid( rand() );

echo '<div id="porto-carousel-' . esc_attr( $uniqid ) . '" class="porto-carousel-wrapper ' . esc_attr( $desing_style ) . ' ' . esc_attr( $el_class ) . '" data-gutter="' . esc_attr( $item_space ) . '" data-rtl="' . esc_attr( $site_rtl ) . '" >';
echo '<div class="porto-ultimate-carousel porto-carousel-' . $uid_escaped . ' " ' . $wrap_data . '>';
porto_override_shortcodes( $item_space, $item_animation );
echo do_shortcode( $content );
porto_restore_shortcodes();
echo '</div>';
echo '</div>';
?>
<script>
	jQuery(document).ready(function ($) {
		if ($.fn.slick) {
			$('.porto-carousel-<?php echo $uid_escaped; ?>').slick({<?php echo $settings_escaped; ?>});
		} else {
			var c = document.createElement("script");
			c.src = "<?php echo wp_scripts()->registered['jquery-slick']->src; ?>";
			if (!$('script[src="' + c.src + '"]').length) {
				document.getElementsByTagName("body")[0].appendChild(c);
			}
			c = document.createElement("script");
			c.src = "<?php echo wp_scripts()->registered['porto_shortcodes_ultimate_carousel_loader_js']->src; ?>";
			if (!$('script[src="' + c.src + '"]').length) {
				document.getElementsByTagName("body")[0].appendChild(c);
			}
			setTimeout(function() {
				if ($.fn.slick) { $('.porto-carousel-<?php echo $uid_escaped; ?>').slick({<?php echo $settings_escaped; ?>}); }
			}, 300);
		}
	});
</script>
<?php
echo ob_get_clean();
