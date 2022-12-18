<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $style
 * @var $shape
 * @var $color
 * @var $custom_background
 * @var $custom_text
 * @var $size
 * @var $align
 * @var $link
 * @var $title
 * @var $button_block
 * @var $el_class
 * @var $outline_custom_color
 * @var $outline_custom_hover_background
 * @var $outline_custom_hover_text
 * @var $add_icon
 * @var $i_align
 * @var $i_type
 * @var $i_icon_fontawesome
 * @var $i_icon_openiconic
 * @var $i_icon_typicons
 * @var $i_icon_entypo
 * @var $i_icon_linecons
 * @var $i_icon_pixelicons
 * @var $css_animation
 * @var $css
 * @var $gradient_color_1
 * @var $gradient_color_2
 * @var $gradient_custom_color_1;
 * @var $gradient_custom_color_2;
 * @var $gradient_text_color;
 *
 * Extra Params
 * @var $skin
 * @var $scale
 * @var $contextual
 * @var $label
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Btn
 */
$style                     = $shape = $color = $size = $custom_background = $custom_text = $align = $link = $title = $button_block = $el_class = $outline_custom_color = $outline_custom_hover_background =
$outline_custom_hover_text = $add_icon = $i_align = $i_type = $i_icon_entypo = $i_icon_fontawesome = $i_icon_linecons = $i_icon_pixelicons = $i_icon_typicons = $css = $css_animation = '';
$gradient_color_1          = $gradient_color_2 = $gradient_custom_color_1 = $gradient_custom_color_2 = $gradient_text_color = '';
$custom_onclick            = $custom_onclick_code = '';
$a_href                    = $a_title = $a_target = $a_rel = $btn_arrow = '';
$styles                    = array();
$icon_wrapper              = false;
$icon_html                 = false;
$attributes                = array();

$css_animation      = '';
$animation_type     = '';
$animation_delay    = '';
$animation_duration = '';

$floating_start_pos  = '';
$floating_speed      = '';
$floating_transition = 'yes';
$floating_horizontal = '';
$floating_duration   = '';


// dynamic field
$enable_field_dynamic             = false;
$field_dynamic_source             = '';
$field_dynamic_content            = '';
$field_dynamic_content_meta_field = '';
$field_dynamic_before             = '';
$field_dynamic_after              = '';
$field_dynamic_fallback           = '';

// dynamic link
$enable_link_dynamic            = false;
$link_dynamic_source            = '';
$link_dynamic_content           = '';
$link_dynamic_content_meta_link = '';
$link_dynamic_fallback          = '';
$date_format                    = '';

$colors = array(
	'blue'        => '#5472d2',
	'turquoise'   => '#00c1cf',
	'pink'        => '#fe6c61',
	'violet'      => '#8d6dc4',
	'peacoc'      => '#4cadc9',
	'chino'       => '#cec2ab',
	'mulled-wine' => '#50485b',
	'vista-blue'  => '#75d69c',
	'orange'      => '#f7be68',
	'sky'         => '#5aa1e3',
	'green'       => '#6dab3c',
	'juicy-pink'  => '#f4524d',
	'sandy-brown' => '#f79468',
	'purple'      => '#b97ebb',
	'black'       => '#2a2a2a',
	'grey'        => '#ebebeb',
	'white'       => '#ffffff',
);

$original_atts = $atts;

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

//dynamic text
if ( $enable_field_dynamic ) {
	if ( ( 'meta_field' == $field_dynamic_source ) && ! empty( $field_dynamic_content_meta_field ) ) {
		$title = Porto_Func_Dynamic_Tags_Content::get_instance()->dynamic_get_data( $field_dynamic_source, $field_dynamic_content_meta_field, 'field' );
	}
	if ( ! empty( $field_dynamic_content ) ) {
		if ( ! empty( $date_format ) ) {
			$field_dynamic_content = array(
				'field_dynamic_content' => $field_dynamic_content,
				'date_format'           => $date_format,
			);
		}
		$title = Porto_Func_Dynamic_Tags_Content::get_instance()->dynamic_get_data( $field_dynamic_source, $field_dynamic_content, 'field' );
	}
	if ( empty( $title ) ) {
		$title = $field_dynamic_fallback;
	}

	$title = $field_dynamic_before . $title . $field_dynamic_after;
}

// dynamic link
$dynamic_link = false;
if ( $enable_link_dynamic ) {
	if ( ( 'meta_field' == $link_dynamic_source ) && ! empty( $link_dynamic_content_meta_link ) ) {
		$link = Porto_Func_Dynamic_Tags_Content::get_instance()->dynamic_get_data( $link_dynamic_source, $link_dynamic_content_meta_link, 'link' );
	}
	if ( ! empty( $link_dynamic_content ) ) {
		$link = Porto_Func_Dynamic_Tags_Content::get_instance()->dynamic_get_data( $link_dynamic_source, $link_dynamic_content, 'link' );
	}
	if ( empty( $link ) ) {
		$link = $link_dynamic_fallback;
	}

	$dynamic_link = true;
}
if ( class_exists( 'WPBMap' ) ) {
	$sc = WPBMap::getShortCode( 'vc_btn' );
	if ( ! empty( $sc['params'] ) && class_exists( 'PortoShortcodesClass' ) && method_exists( 'PortoShortcodesClass', 'get_global_hashcode' ) ) {
		foreach ( $original_atts as $key => $item ) {
			if ( in_array( $key, array( 'btn_icon_size', 'btn_icon_spacing' ) ) ) {
				$original_atts[ $key ] = str_replace( '"', '``', $item );
			}
		}
		$shortcode_class = ' wpb_custom_' . PortoShortcodesClass::get_global_hashcode( $original_atts, 'vc_btn', $sc['params'] );
		if ( empty( $el_cls ) ) {
			$el_cls = $shortcode_class;
		} else {
			$el_cls .= ' ' . $shortcode_class;
		}
		$internal_css = PortoShortcodesClass::generate_wpb_css( 'vc_btn', $original_atts );
	}
}

//parse link
$link = ( '||' === $link ) ? '' : $link;
if ( $dynamic_link ) {
	$link = array(
		'url'    => $link,
		'target' => '',
		'title'  => '',
		'rel'    => '',
	);
} else {
	$link = vc_build_link( $link );
}
$use_link = false;
if ( strlen( $link['url'] ) > 0 ) {
	$use_link = true;
	$a_href   = $link['url'];
	$a_title  = $link['title'];
	$a_target = $link['target'];
	$a_rel    = $link['rel'];
}

$wrapper_classes = array(
	'vc_btn3-container',
	$this->getExtraClass( $el_class ),
	$this->getCSSAnimation( $css_animation ),
	'vc_btn3-' . $align,
);
if ( $contextual || 'custom' !== $skin ) {
	$button_classes = array(
		'vc_btn3',
		'vc_btn3-shape-' . $shape,
	);
} else {
	$button_classes = array(
		'vc_general',
		'vc_btn3',
		'vc_btn3-size-' . $size,
		'vc_btn3-shape-' . $shape,
		'vc_btn3-style-' . $style,
	);
}

$button_html = $title;
if ( ! empty( $hover_text_effect ) && $title ) {
	$button_html = '<span class="btn-text" data-text="' . esc_attr( $title ) . '">' . $button_html . '</span>';

	$button_classes[] = 'btn-hover-text-effect';
	$button_classes[] = $hover_text_effect;
}

if ( '' === trim( $title ) ) {
	$button_classes[] = 'vc_btn3-o-empty';
	$button_html      = '<span class="vc_btn3-placeholder">&nbsp;</span>';
}
if ( 'true' === $button_block && 'inline' !== $align ) {
	$button_classes[] = 'vc_btn3-block';
}

if ( isset( $el_cls ) && $el_cls ) {
	$button_classes[] = trim( $el_cls );
}

if ( 'true' === $add_icon ) {
	$button_classes[] = 'vc_btn3-icon-' . $i_align;
	vc_icon_element_fonts_enqueue( $i_type );

	if ( ! empty( $hover_effect ) ) {
		$button_classes[] = $hover_effect;
	}

	if ( isset( ${'i_icon_' . $i_type} ) ) {
		if ( 'pixelicons' === $i_type ) {
			$icon_wrapper = true;
		}
		$icon_class = ${'i_icon_' . $i_type};
	} else {
		$icon_class = 'fas fa-adjust';
	}

	if ( $icon_wrapper ) {
		$icon_html = '<i class="vc_btn3-icon"><span class="vc_btn3-icon-inner ' . esc_attr( $icon_class ) . '"></span></i>';
	} else {
		$icon_html = '<i class="vc_btn3-icon ' . esc_attr( $icon_class ) . '"></i>';
	}

	if ( 'left' === $i_align ) {
		$button_html = $icon_html . ' ' . $button_html;
	} else {
		$button_html .= ' ' . $icon_html;
	}
}

if ( ! ( $contextual || 'custom' !== $skin ) ) {
	if ( 'custom' === $style ) {
		if ( $custom_background ) {
			$styles[] = vc_get_css_color( 'background-color', $custom_background );
		}

		if ( $custom_text ) {
			$styles[] = vc_get_css_color( 'color', $custom_text );
		}

		if ( ! $custom_background && ! $custom_text ) {
			$button_classes[] = 'vc_btn3-color-grey';
		}
	} elseif ( 'outline-custom' === $style ) {
		if ( $outline_custom_color ) {
			$styles[]     = vc_get_css_color( 'border-color', $outline_custom_color );
			$styles[]     = vc_get_css_color( 'color', $outline_custom_color );
			$attributes[] = 'onmouseleave="this.style.borderColor=\'' . esc_js( $outline_custom_color ) . '\'; this.style.backgroundColor=\'transparent\'; this.style.color=\'' . esc_js( $outline_custom_color ) . '\'"';
		} else {
			$attributes[] = 'onmouseleave="this.style.borderColor=\'\'; this.style.backgroundColor=\'transparent\'; this.style.color=\'\'"';
		}

		$onmouseenter = array();
		if ( $outline_custom_hover_background ) {
			$onmouseenter[] = 'this.style.borderColor=\'' . esc_js( $outline_custom_hover_background ) . '\';';
			$onmouseenter[] = 'this.style.backgroundColor=\'' . esc_js( $outline_custom_hover_background ) . '\';';
		}
		if ( $outline_custom_hover_text ) {
			$onmouseenter[] = 'this.style.color=\'' . esc_js( $outline_custom_hover_text ) . '\';';
		}
		if ( $onmouseenter ) {
			$attributes[] = 'onmouseenter="' . esc_js( implode( ' ', $onmouseenter ) ) . '"';
		}

		if ( ! $outline_custom_color && ! $outline_custom_hover_background && ! $outline_custom_hover_text ) {
			$button_classes[] = 'vc_btn3-color-inverse';

			foreach ( $button_classes as $k => $v ) {
				if ( 'vc_btn3-style-outline-custom' === $v ) {
					unset( $button_classes[ $k ] );
					break;
				}
			}
			$button_classes[] = 'vc_btn3-style-outline';
		}
	} elseif ( 'gradient' === $style || 'gradient-custom' === $style ) {

		$gradient_color_1 = $colors[ $gradient_color_1 ];
		$gradient_color_2 = $colors[ $gradient_color_2 ];

		$button_text_color = '#fff';
		if ( 'gradient-custom' === $style ) {
			$gradient_color_1  = $gradient_custom_color_1;
			$gradient_color_2  = $gradient_custom_color_2;
			$button_text_color = $gradient_text_color;
		}

		$gradient_css   = array();
		$gradient_css[] = 'color: ' . $button_text_color;
		$gradient_css[] = 'border: none';
		$gradient_css[] = 'background-color: ' . $gradient_color_1;
		$gradient_css[] = 'background-image: -webkit-linear-gradient(left, ' . $gradient_color_1 . ' 0%, ' . $gradient_color_2 . ' 50%,' . $gradient_color_1 . ' 100%)';
		$gradient_css[] = 'background-image: linear-gradient(to right, ' . $gradient_color_1 . ' 0%, ' . $gradient_color_2 . ' 50%,' . $gradient_color_1 . ' 100%)';
		$gradient_css[] = 'transition: all .2s ease-in-out';
		$gradient_css[] = 'background-size: 200% 100%';

		// hover css
		$gradient_css_hover   = array();
		$gradient_css_hover[] = 'color: ' . $button_text_color;
		$gradient_css_hover[] = 'background-color: ' . $gradient_color_2;
		$gradient_css_hover[] = 'border: none';
		$gradient_css_hover[] = 'background-position: 100% 0';

		$uid = uniqid();
		ob_start();
		echo '<style>.vc_btn3-style-' . esc_html( $style ) . '.vc_btn-gradient-btn-' . $uid . ':hover,.vc_btn3-style-' . esc_html( $style ) . '.vc_btn-gradient-btn-' . $uid . ':focus{' . esc_html(
			implode(
				';',
				$gradient_css_hover
			)
		) . ';' . '} ';
		echo '.vc_btn3-style-' . esc_html( $style ) . '.vc_btn-gradient-btn-' . $uid . '{' . esc_html(
			implode(
				';',
				$gradient_css
			)
		) . ';' . '}</style>';
		porto_filter_inline_css( ob_get_clean() );
		$button_classes[] = 'vc_btn-gradient-btn-' . $uid;
		$attributes[]     = 'data-vc-gradient-1="' . esc_attr( $gradient_color_1 ) . '"';
		$attributes[]     = 'data-vc-gradient-2="' . esc_attr( $gradient_color_2 ) . '"';
	} else {
		$button_classes[] = 'vc_btn3-color-' . $color;
	}
}

if ( $label ) {
	$button_classes[] = 'vc_label';
}

if ( $animation_type ) {
	$attributes[] = ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$attributes[] = ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$attributes[] = ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
} elseif ( $floating_start_pos && $floating_speed ) {
	$floating_options = array(
		'startPos' => $floating_start_pos,
		'speed'    => $floating_speed,
	);
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
	$attributes[] = 'data-plugin-float-element';
	$attributes[] = 'data-plugin-options="' . esc_attr( json_encode( $floating_options ) ) . '"';
}

if ( isset( $show_arrow ) && $show_arrow ) {
	$wrapper_classes[] = 'show-arrow';
}

$class_to_filter  = implode( ' ', array_filter( $wrapper_classes ) );
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' );
$css_class        = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

if ( $button_classes ) {
	if ( $contextual || 'custom' !== $skin ) {
		if ( $label ) {
			$button_classes[] = 'label';
			$button_classes[] = 'label-' . $size;
		} else {
			$button_classes[] = 'btn';
			switch ( $style ) {
				case 'outline':
					$button_classes[] = 'btn-borders';
					break;
				case '3d':
					$button_classes[] = 'btn-3d';
					break;
				case 'modern':
					$button_classes[] = 'btn-modern';
					break;
				case 'flat':
					$button_classes[] = 'btn-flat';
			}
			$button_classes[] = 'btn-' . $size;
		}
		if ( $contextual ) {
			if ( $label ) {
				$button_classes[] = 'bg-' . $contextual;
				$button_classes[] = 'border-' . $contextual;
			} else {
				$button_classes[] = 'btn-' . $contextual;
			}
		} elseif ( 'custom' !== $skin ) {
			if ( $label ) {
				$button_classes[] = 'label-' . $skin;
			} else {
				$button_classes[] = 'btn-' . $skin;
			}
		}
	} elseif ( ! $contextual && ! $label && 'custom' == $skin ) {
		$button_classes[] = 'btn';
	}
	if ( $btn_arrow ) {
		$button_classes[] = 'btn-arrow';
		$button_html     .= '<span class="icon-wrapper"><i class="fas fa-chevron-right"></i></span>';
	}
	$button_classes = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $button_classes ) ), $this->settings['base'], $atts );
	$attributes[]   = 'class="' . esc_attr( trim( $button_classes ) ) . '"';

	if ( 'custom' === $style ) {
		if ( $custom_background ) {
			$styles[] = vc_get_css_color( 'background-color', $custom_background );
		}

		if ( $custom_text ) {
			$styles[] = vc_get_css_color( 'color', $custom_text );
		}
	}
}

if ( isset( $btn_fs ) && $btn_fs ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $btn_fs ) );
	if ( ! $unit ) {
		$btn_fs .= 'px';
	}
	$styles[] = 'font-size:' . $btn_fs . ';';
}
if ( isset( $btn_fw ) && $btn_fw ) {
	$styles[] = 'font-weight:' . $btn_fw . ';';
}
if ( isset( $btn_ls ) && $btn_ls ) {
	$unit = trim( preg_replace( '/[0-9.-]/', '', $btn_ls ) );
	if ( ! $unit ) {
		$btn_ls .= 'px';
	}
	$styles[] = 'letter-spacing:' . $btn_ls . ';';
}
if ( ( isset( $btn_px ) && $btn_px ) && ( isset( $btn_py ) && $btn_py ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $btn_px ) );
	if ( ! $unit ) {
		$btn_px .= 'px';
	}
	$unit = trim( preg_replace( '/[0-9.]/', '', $btn_py ) );
	if ( ! $unit ) {
		$btn_py .= 'px';
	}
	$styles[] = 'padding:' . $btn_py . ' ' . $btn_px . ';';
} elseif ( isset( $btn_px ) && $btn_px ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $btn_px ) );
	if ( ! $unit ) {
		$btn_px .= 'px';
	}
	$styles[] = 'padding-left:' . $btn_px . ';';
	$styles[] = 'padding-right:' . $btn_px . ';';
} elseif ( isset( $btn_py ) && $btn_py ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $btn_py ) );
	if ( ! $unit ) {
		$btn_py .= 'px';
	}
	$styles[] = 'padding-top:' . $btn_py . ';';
	$styles[] = 'padding-bottom:' . $btn_py . ';';
}

if ( ! empty( $styles ) ) {
	$attributes[] = 'style="' . esc_attr( implode( ' ', $styles ) ) . '"';
}

if ( $use_link ) {
	$attributes[] = 'href="' . trim( $a_href ) . '"';
	$attributes[] = 'title="' . esc_attr( trim( $a_title ) ) . '"';
	if ( ! empty( $a_target ) ) {
		$attributes[] = 'target="' . esc_attr( trim( $a_target ) ) . '"';
	}
	if ( ! empty( $a_rel ) ) {
		$attributes[] = 'rel="' . esc_attr( trim( $a_rel ) ) . '"';
	}
}

if ( ! empty( $custom_onclick ) && $custom_onclick_code ) {
	$attributes[] = 'onclick="' . esc_attr( $custom_onclick_code ) . '"';
}

$attributes         = implode( ' ', $attributes );
$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';}
?>
<div class="<?php echo trim( esc_attr( $css_class ) ); ?>" <?php echo implode( ' ', $wrapper_attributes ); ?>>
	<?php
	if ( ! empty( $internal_css ) ) {
		// only wpbakery frontend editor
		echo '<style>' . wp_strip_all_tags( $internal_css ) . '</style>';
	}
	if ( $use_link ) {
		echo '<a ' . $attributes . '>' . $button_html . '</a>';
	} else {
		echo '<button ' . $attributes . '>' . $button_html . '</button>';
	}
	?>
	<?php if ( isset( $show_arrow ) && $show_arrow ) : ?>
		<span class="dir-arrow hlb" data-appear-animation-delay="800" data-appear-animation="rotateInUpLeft"></span>
	<?php endif; ?>
</div>
