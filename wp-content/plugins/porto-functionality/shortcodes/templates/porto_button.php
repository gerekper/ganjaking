<?php
	$settings = shortcode_atts(
		array(
			'title'               => '',
			'size'                => 'md',
			'skin'                => 'primary',
			'layout'              => '',
			'is_block'            => false,
			'link'                => '',
			'shape'               => '',
			'icon_pos'            => 'left',
			'icon_cls'            => '',
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
		),
		$atts
	);

	if ( $settings['title'] ) {
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

		$btn_icon_html_escaped = '';
		$icon_cls              = is_array( $settings['icon_cls'] ) ? $settings['icon_cls']['value'] : $settings['icon_cls'];
		if ( ! empty( $icon_cls ) ) {
			$btn_icon_html_escaped = '<i class="' . esc_attr( trim( $icon_cls ) ) . '"></i>';
			$btn_classes          .= ' btn-icon';
			if ( 'right' == $settings['icon_pos'] ) {
				$btn_classes .= ' btn-icon-right';
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

		echo '<a class="' . esc_attr( $btn_classes ) . '" href="' . esc_url( $url ) . '"' . ( isset( $settings['link']['is_external'] ) && $settings['link']['is_external'] ? ' target="_blank"' : '' ) . porto_shortcode_add_floating_options( $settings ) . $attrs . '>';
		if ( 'left' == $settings['icon_pos'] ) {
			echo porto_filter_output( $btn_icon_html_escaped );
		}
			echo '<span' . ( isset( $title_attrs_escaped ) ? wp_kses_post( $title_attrs_escaped ) : '' ) . '>' . esc_html( $settings['title'] ) . '</span>';
		if ( $settings['show_arrow'] ) {
			echo '<span class="dir-arrow hlb" data-appear-animation-delay="800" data-appear-animation="rotateInUpLeft"></span>';
		}
		if ( 'right' == $settings['icon_pos'] ) {
			echo porto_filter_output( $btn_icon_html_escaped );
		}
		echo '</a>';
		if ( $settings['align'] ) {
			echo '</div>';
		}
	}
