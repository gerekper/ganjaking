<?php
	$settings = shortcode_atts(
		array(
			'title'               => __( 'Click here', 'porto-functionality' ),
			'size'                => 'md',
			'skin'                => 'primary',
			'layout'              => '',
			'is_block'            => false,
			'link_source'         => '',
			'dynamic_content'     => '',
			'link'                => '',
			'shape'               => '',
			'icon_pos'            => 'left',
			'icon_cls'            => '',
			'hover_effect'        => '',
			'hover_text_effect'   => '',
			'spacing'             => '',
			'align'               => '',
			'show_arrow'          => '',
			'floating_start_pos'  => '',
			'floating_speed'      => '',
			'floating_transition' => 'yes',
			'floating_horizontal' => '',
			'floating_duration'   => '',
			'className'           => '',
			'animation_type'      => '',
			'animation_duration'  => 1000,
			'animation_delay'     => 0,
			'el_class'   	      => '',
		),
		$atts
	);
	
	$icon_cls = is_array( $settings['icon_cls'] ) ? $settings['icon_cls']['value'] : $settings['icon_cls'];
	$tag      = 'a';

	if ( $settings['title'] || $icon_cls ) {
		if ( 'dynamic' == $settings['link_source'] && $settings['dynamic_content'] && $settings['dynamic_content']['source'] ) {
			$field_name = '';
			if ( 'post' == $settings['dynamic_content']['source'] ) {
				if ( isset( $settings['dynamic_content']['post_info'] ) ) {
					$field_name = $settings['dynamic_content']['post_info'];
				}
			} else {
				if ( isset( $settings['dynamic_content'][ $settings['dynamic_content']['source'] ] ) ) {
					$field_name = $settings['dynamic_content'][ $settings['dynamic_content']['source'] ];
				}
			}
			if ( $field_name ) {
				$settings['link'] = apply_filters( 'porto_dynamic_tags_content', '', null, $settings['dynamic_content']['source'], $field_name );
			}
			if ( ( '#' == $settings['link'] || empty( $settings['link'] ) ) && ! empty( $settings['dynamic_content']['fallback'] ) ) {
				$settings['link'] = $settings['dynamic_content']['fallback'];
			}
		}

		$btn_classes = 'btn btn-' . $settings['size'];
		if ( 'custom' != $settings['skin'] ) {
			$btn_classes .= ' btn-' . $settings['skin'];
		}
		if ( $settings['layout'] ) {
			$btn_classes .= ' btn-' . $settings['layout'];
		}
		if ( 'yes' == $settings['is_block'] ) {
			$btn_classes .= ' btn-block';
		}
		if ( 'round' == $settings['shape'] ) {
			$btn_classes .= ' btn-full-rounded';
		}

		if( !empty( $settings['el_class'] ) ) {
			$btn_classes .= ' ' . $settings['el_class'];
		}
		
		$btn_icon_html_escaped = '';
		if ( ! empty( $icon_cls ) ) {
			$btn_icon_html_escaped = '<i class="' . esc_attr( trim( $icon_cls ) ) . '"></i>';
			$btn_classes          .= ' btn-icon';
			if ( 'right' == $settings['icon_pos'] ) {
				$btn_classes .= ' btn-icon-right';
			}

			if ( empty( $settings['title'] ) ) {
				$btn_classes .= ' btn-icon-only';
			}
		}

		if ( ! empty( $settings['hover_effect'] ) ) {
			$btn_classes .= ' ' . trim( $settings['hover_effect'] );
		}

		if ( ! empty( $settings['hover_text_effect'] ) && ! empty( $settings['title'] ) ) {
			if ( empty( $title_attrs_escaped ) || empty( trim( $title_attrs_escaped ) ) ) {
				$title_attrs_escaped = ' class="btn-text" data-text="' . esc_attr( $settings['title'] ) . '"';
			} else {
				$title_attrs_escaped  = str_replace( 'class="', 'class="btn-text ', $title_attrs_escaped );
				$title_attrs_escaped .= ' data-text="' . esc_attr( $settings['title'] ) . '"';
			}

			$btn_classes .= ' btn-hover-text-effect';
			$btn_classes .= ' ' . $settings['hover_text_effect'];
		}
		
		if ( ! empty( $settings['spacing'] ) || ( isset( $settings['spacing'] ) && '0' == $settings['spacing'] ) ) {
			if ( ! is_array( $settings['spacing'] ) ) {
				$btn_classes .= ' porto-btn-sp-' . sanitize_title( $settings['spacing'] );
			}
		}

		if ( $settings['className'] ) {
			$btn_classes .= ' ' . trim( $settings['className'] );
		}

		$url = is_array( $settings['link'] ) ? $settings['link']['url'] : $settings['link'];
		if ( $settings['align'] ) {
			echo '<div class="porto-button text-' . esc_attr( $settings['align'] ) . '">';
		}

		$attrs = '';
		if ( ! empty( $settings['animation_type'] ) ) {
			$attrs .= ' data-appear-animation="' . esc_attr( $settings['animation_type'] ) . '"';
			if ( ! empty( $settings['animation_delay'] ) ) {
				$attrs .= ' data-appear-animation-delay="' . absint( $settings['animation_delay'] ) . '"';
			}
			if ( ! empty( $settings['animation_duration'] ) && 1000 !== (int) $settings['animation_duration'] ) {
				$attrs .= ' data-appear-animation-duration="' . absint( $settings['animation_duration'] ) . '"';
			}
		}
		echo '<' . $tag . ' aria-label="button" class="' . esc_attr( apply_filters( 'porto_elements_wrap_css_class', $btn_classes, $atts, 'button' ) ) . '" href="' . esc_url( $url ) . '"' . ( isset( $settings['link']['is_external'] ) && $settings['link']['is_external'] ? ' target="_blank"' : '' ) . porto_shortcode_add_floating_options( $settings ) . $attrs . '>';
		if ( 'left' == $settings['icon_pos'] ) {
			echo porto_filter_output( $btn_icon_html_escaped );
		}
			echo '<span' . ( isset( $title_attrs_escaped ) ? wp_kses_post( $title_attrs_escaped ) : '' ) . '>' . porto_strip_script_tags( $settings['title'] ) . '</span>';
		if ( $settings['show_arrow'] ) {
			echo '<span class="dir-arrow hlb" data-appear-animation-delay="800" data-appear-animation="rotateInUpLeft"></span>';
		}
		if ( 'right' == $settings['icon_pos'] ) {
			echo porto_filter_output( $btn_icon_html_escaped );
		}
		echo '</' . $tag . '>';
		if ( $settings['align'] ) {
			echo '</div>';
		}
	}