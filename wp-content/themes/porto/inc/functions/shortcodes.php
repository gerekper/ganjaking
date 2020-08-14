<?php

add_filter( 'the_excerpt', 'do_shortcode' );

// Replace rows and columns classes
function porto_custom_css_classes_for_elements( $class_string, $tag ) {

	if ( 'vc_row' == $tag || 'vc_row_inner' == $tag ) {
		if ( strpos( $class_string, 'porto-inner-container' ) !== false ) {
			$class_string = str_replace( 'vc_row-fluid', '', $class_string );
			//$class_string = str_replace( ' porto-inner-container', '', $class_string );
		} else {
			$class_string = str_replace( 'vc_row-fluid', 'row', $class_string );
		}
	}
	if ( ! ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) && ( 'vc_column' == $tag || 'vc_column_inner' == $tag ) ) {
		//$class_string = str_replace( 'vc_column_container', '', $class_string );
		if ( preg_match_all( '/vc_col-(\w{2})-(\d{1,2})(\/5|)($| )/', $class_string, $matches ) ) {
			$class_string = str_replace( array( 'vc_col-lg-offset-', 'vc_col-md-offset-', 'vc_col-sm-offset-', 'vc_col-xs-offset-' ), array( 'offset-xl-', 'offset-lg-', 'offset-md-', 'offset-' ), $class_string );
			if ( isset( $matches[1] ) && isset( $matches[2] ) ) {
				$size_array = array(
					'-lg' => '-xl',
					'-md' => '-lg',
					'-sm' => '-md',
					'-xs' => '',
				);
				foreach ( $matches[1] as $index => $size ) {
					$cols = $matches[2][ $index ];
					if ( ! empty( $matches[3][ $index ] ) ) {
						$class_string = str_replace( 'vc_col-' . $size . '-' . $cols . $matches[3][ $index ], 'col' . $size_array[ '-' . $size ] . '-' . $cols . str_replace( '/', '-', $matches[3][ $index ] ), $class_string );
					} else {
						$class_string = str_replace( 'vc_col-' . $size . '-' . $cols, 'col' . $size_array[ '-' . $size ] . '-' . $cols, $class_string );
					}
				}
			}
		}
		/*if ( preg_match( '/col-(\w{2})-(\d{1})\/5/', $class_string ) ) {
			$class_string = str_replace( 'vc_column_container', 'vc_column_container porto-column', $class_string );
			$class_string = preg_replace( '/ col-(\w{2})-12/', '', $class_string );
		}*/
		if ( preg_match( '/vc_hidden-(\w{2})/', $class_string ) ) {
			$class_string = str_replace( array( 'vc_hidden-lg', 'vc_hidden-md', 'vc_hidden-sm', 'vc_hidden-xs' ), array( 'd-xl-none', 'd-lg-none d-xl-block', 'd-md-none d-lg-block', 'd-none d-md-block' ), $class_string );
			$screens      = array( '', 'md', 'lg', 'xl' );
			for ( $i = 0; $i <= 3; $i++ ) {
				if ( 0 == $i ) {
					$screen = ' d';
				} else {
					$screen = ' d-' . $screens[ $i ];
				}
				if ( strpos( $class_string, $screen . '-block' ) !== false && strpos( $class_string, $screen . '-none' ) !== false ) {
					$class_string = str_replace( $screen . '-block', '', $class_string );
				}
			}
			for ( $i = 3; $i >= 1; $i-- ) {
				if ( 1 == $i ) {
					$screen = ' d';
				} else {
					$screen = ' d-' . $screens[ $i - 1 ];
				}
				$screen_bigger = ' d-' . $screens[ $i ];
				if ( strpos( $class_string, $screen . '-none' ) !== false && strpos( $class_string, $screen_bigger . '-none' ) !== false ) {
					$class_string = str_replace( $screen_bigger . '-none', '', $class_string );
				}
			}
		}
	}

	return $class_string;
}
add_filter( 'vc_shortcodes_css_class', 'porto_custom_css_classes_for_elements', 10, 2 );

add_action( 'vc_after_init', 'porto_load_shortcodes' );
function porto_load_shortcodes() {

	if ( function_exists( 'vc_map' ) ) {
		global $porto_settings;
		$dark = porto_is_dark_skin();

		$section_group      = __( 'Porto Options', 'porto' );
		$sticky_group       = __( 'Sticky Options', 'porto' );
		$animation_group    = __( 'Animation', 'porto' );
		$animation_type     = array(
			'type'       => 'porto_theme_animation_type',
			'heading'    => __( 'Animation Type', 'porto' ),
			'param_name' => 'animation_type',
			'group'      => $animation_group,
		);
		$animation_duration = array(
			'type'        => 'textfield',
			'heading'     => __( 'Animation Duration', 'porto' ),
			'param_name'  => 'animation_duration',
			'description' => __( 'numerical value (unit: milliseconds)', 'porto' ),
			'value'       => '1000',
			'group'       => $animation_group,
		);
		$animation_delay    = array(
			'type'        => 'textfield',
			'heading'     => __( 'Animation Delay', 'porto' ),
			'param_name'  => 'animation_delay',
			'description' => __( 'numerical value (unit: milliseconds)', 'porto' ),
			'value'       => '0',
			'group'       => $animation_group,
		);

		$floating_start_pos  = array(
			'type'       => 'dropdown',
			'heading'    => __( 'Floating Start Pos', 'porto' ),
			'param_name' => 'floating_start_pos',
			'value'      => array(
				__( 'Disabled', 'porto' ) => '',
				__( 'None', 'porto' )     => 'none',
				__( 'Top', 'porto' )      => 'top',
				__( 'Bottom', 'porto' )   => 'bottom',
			),
			'dependency' => array(
				'element' => 'animation_type',
				'value'   => array( '' ),
			),
			'group'      => $animation_group,
		);
		$floating_speed      = array(
			'type'        => 'textfield',
			'heading'     => __( 'Floating Speed', 'porto' ),
			'param_name'  => 'floating_speed',
			'description' => __( 'numerical value (from 0.0 to 10.0)', 'porto' ),
			'value'       => '',
			'dependency'  => array(
				'element' => 'animation_type',
				'value'   => array( '' ),
			),
			'group'       => $animation_group,
		);
		$floating_transition = array(
			'type'       => 'checkbox',
			'heading'    => __( 'Floating Transition', 'porto' ),
			'param_name' => 'floating_transition',
			'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
			'std'        => 'yes',
			'dependency' => array(
				'element' => 'animation_type',
				'value'   => array( '' ),
			),
			'group'      => $animation_group,
		);
		$floating_horizontal = array(
			'type'       => 'checkbox',
			'heading'    => __( 'Floating Horizontal', 'porto' ),
			'param_name' => 'floating_horizontal',
			'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
			'dependency' => array(
				'element' => 'animation_type',
				'value'   => array( '' ),
			),
			'group'      => $animation_group,
		);
		$floating_duration   = array(
			'type'        => 'textfield',
			'heading'     => __( 'Transition Duration', 'porto' ),
			'param_name'  => 'floating_duration',
			'description' => __( 'numerical value (unit: milliseconds)', 'porto' ),
			'dependency'  => array(
				'element' => 'animation_type',
				'value'   => array( '' ),
			),
			'group'       => $animation_group,
		);

		/* ---------------------------- */
		/* Customize Row
		/* ---------------------------- */
		vc_add_param(
			'vc_row',
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Video link', 'js_composer' ),
				'param_name'  => 'video_bg_url',
				'value'       => '',
				'description' => __( 'Add YouTube link or mp4 video link.', 'js_composer' ),
				'dependency'  => array(
					'element'   => 'video_bg',
					'not_empty' => true,
				),
			)
		);

		vc_add_param(
			'vc_row',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Wrap as Container', 'porto' ),
				'param_name'  => 'wrap_container',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'No Space between Columns?', 'porto' ),
				'param_name'  => 'no_padding',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Section & Parallax Text Color', 'porto' ),
				'param_name' => 'section_text_color',
				'value'      => porto_vc_commons( 'section_text_color' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Text Align', 'porto' ),
				'param_name' => 'text_align',
				'value'      => porto_vc_commons( 'align' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Is Section?', 'porto' ),
				'param_name'  => 'is_section',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Section Skin Color', 'porto' ),
				'param_name' => 'section_skin',
				'value'      => porto_vc_commons( 'section_skin' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Section Default Color Scale', 'porto' ),
				'param_name' => 'section_color_scale',
				'value'      => porto_vc_commons( 'section_color_scale' ),
				'dependency' => array(
					'element' => 'section_skin',
					'value'   => array( 'default' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Section Color Scale', 'porto' ),
				'param_name' => 'section_skin_scale',
				'dependency' => array(
					'element' => 'section_skin',
					'value'   => array( 'primary', 'secondary', 'tertiary', 'quaternary', 'dark', 'light' ),
				),
				'group'      => $section_group,
				'value'      => array(
					__( 'Default', 'porto' ) => '',
					__( 'Scale 2', 'porto' ) => 'scale-2',
				),
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Margin Top', 'porto' ),
				'param_name' => 'remove_margin_top',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Margin Bottom', 'porto' ),
				'param_name' => 'remove_margin_bottom',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Padding Top', 'porto' ),
				'param_name' => 'remove_padding_top',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Padding Bottom', 'porto' ),
				'param_name' => 'remove_padding_bottom',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Border', 'porto' ),
				'param_name' => 'remove_border',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Divider', 'porto' ),
				'param_name' => 'show_divider',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'dependency' => array(
					'element'   => 'is_section',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Divider Position', 'porto' ),
				'param_name' => 'divider_pos',
				'value'      => array(
					__( 'Top', 'porto' )    => '',
					__( 'Bottom', 'porto' ) => 'bottom',
				),
				'dependency' => array(
					'element'   => 'show_divider',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Divider Color', 'porto' ),
				'param_name' => 'divider_color',
				'dependency' => array(
					'element'   => 'show_divider',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Divider Height', 'porto' ),
				'param_name' => 'divider_height',
				'dependency' => array(
					'element'   => 'show_divider',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Divider Icon', 'porto' ),
				'param_name' => 'show_divider_icon',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'dependency' => array(
					'element'   => 'show_divider',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon library', 'js_composer' ),
				'value'      => array(
					__( 'Font Awesome', 'porto' )      => 'fontawesome',
					__( 'Simple Line Icon', 'porto' )  => 'simpleline',
					__( 'Custom Image Icon', 'porto' ) => 'image',
				),
				'param_name' => 'divider_icon_type',
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Select Icon', 'porto' ),
				'dependency' => array(
					'element' => 'divider_icon_type',
					'value'   => 'image',
				),
				'param_name' => 'divider_icon_image',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'divider_icon',
				'dependency' => array(
					'element' => 'divider_icon_type',
					'value'   => 'fontawesome',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'divider_icon_simpleline',
				'value'      => '',
				'settings'   => array(
					'type'         => 'simpleline',
					'iconsPerPage' => 4000,
				),
				'dependency' => array(
					'element' => 'divider_icon_type',
					'value'   => 'simpleline',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Skin Color', 'porto' ),
				'param_name' => 'divider_icon_skin',
				'std'        => 'custom',
				'value'      => porto_vc_commons( 'colors' ),
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Color', 'porto' ),
				'param_name' => 'divider_icon_color',
				'dependency' => array(
					'element' => 'divider_icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Background Color', 'porto' ),
				'param_name' => 'divider_icon_bg_color',
				'dependency' => array(
					'element' => 'divider_icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Border Color', 'porto' ),
				'param_name' => 'divider_icon_border_color',
				'dependency' => array(
					'element' => 'divider_icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Wrap Border Color', 'porto' ),
				'param_name' => 'divider_icon_wrap_border_color',
				'dependency' => array(
					'element' => 'divider_icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Style', 'porto' ),
				'param_name' => 'divider_icon_style',
				'value'      => porto_vc_commons( 'separator_icon_style' ),
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Position', 'porto' ),
				'param_name' => 'divider_icon_pos',
				'value'      => porto_vc_commons( 'separator_icon_pos' ),
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Size', 'porto' ),
				'param_name' => 'divider_icon_size',
				'value'      => porto_vc_commons( 'separator_icon_size' ),
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Enable Sticky Options?', 'porto' ),
				'param_name'  => 'is_sticky',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $sticky_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Container Selector', 'porto' ),
				'param_name' => 'sticky_container_selector',
				'value'      => '.main-content',
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Min Width (unit: px)', 'porto' ),
				'param_name' => 'sticky_min_width',
				''           => __( 'Wll be disabled if window width is smaller than min width', 'porto' ),
				'value'      => 767,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Top (unit: px)', 'porto' ),
				'param_name' => 'sticky_top',
				''           => __( 'Top position when active', 'porto' ),
				'value'      => 110,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Bottom (unit: px)', 'porto' ),
				'param_name' => 'sticky_bottom',
				''           => __( 'Bottom position when active', 'porto' ),
				'value'      => 0,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_row',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Active Class', 'porto' ),
				'param_name' => 'sticky_active_class',
				'value'      => 'sticky-active',
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param( 'vc_row', $animation_type );
		vc_add_param( 'vc_row', $animation_duration );
		vc_add_param( 'vc_row', $animation_delay );

		vc_add_param(
			'vc_row_inner',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Enable Sticky Options?', 'porto' ),
				'param_name'  => 'is_sticky',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $sticky_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_row_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Container Selector', 'porto' ),
				'param_name' => 'sticky_container_selector',
				'value'      => '.vc_row',
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_row_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Min Width (unit: px)', 'porto' ),
				'param_name' => 'sticky_min_width',
				''           => __( 'Wll be disabled if window width is smaller than min width', 'porto' ),
				'value'      => 767,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_row_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Top (unit: px)', 'porto' ),
				'param_name' => 'sticky_top',
				''           => __( 'Top position when active', 'porto' ),
				'value'      => 110,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_row_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Bottom (unit: px)', 'porto' ),
				'param_name' => 'sticky_bottom',
				''           => __( 'Bottom position when active', 'porto' ),
				'value'      => 0,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_row_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Active Class', 'porto' ),
				'param_name' => 'sticky_active_class',
				'value'      => 'sticky-active',
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param( 'vc_row_inner', $animation_type );
		vc_add_param( 'vc_row_inner', $animation_duration );
		vc_add_param( 'vc_row_inner', $animation_delay );

		/* ---------------------------- */
		/* Customize Column
		/* ---------------------------- */
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Section & Parallax Text Color', 'porto' ),
				'param_name' => 'section_text_color',
				'value'      => porto_vc_commons( 'section_text_color' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Text Align', 'porto' ),
				'param_name' => 'text_align',
				'value'      => porto_vc_commons( 'align' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Is Section?', 'porto' ),
				'param_name'  => 'is_section',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Section Skin Color', 'porto' ),
				'param_name' => 'section_skin',
				'value'      => porto_vc_commons( 'section_skin' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Section Default Color Scale', 'porto' ),
				'param_name' => 'section_color_scale',
				'value'      => porto_vc_commons( 'section_color_scale' ),
				'dependency' => array(
					'element' => 'section_skin',
					'value'   => array( 'default' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Section Color Scale', 'porto' ),
				'param_name' => 'section_skin_scale',
				'dependency' => array(
					'element' => 'section_skin',
					'value'   => array( 'primary', 'secondary', 'tertiary', 'quaternary', 'dark', 'light' ),
				),
				'group'      => $section_group,
				'value'      => array(
					__( 'Default', 'porto' ) => '',
					__( 'Scale 2', 'porto' ) => 'scale-2',
				),
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Margin Top', 'porto' ),
				'param_name' => 'remove_margin_top',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Margin Bottom', 'porto' ),
				'param_name' => 'remove_margin_bottom',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Padding Top', 'porto' ),
				'param_name' => 'remove_padding_top',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Padding Bottom', 'porto' ),
				'param_name' => 'remove_padding_bottom',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Remove Border', 'porto' ),
				'param_name' => 'remove_border',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Divider', 'porto' ),
				'param_name' => 'show_divider',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'dependency' => array(
					'element'   => 'is_section',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Divider Position', 'porto' ),
				'param_name' => 'divider_pos',
				'value'      => array(
					__( 'Top', 'porto' )    => '',
					__( 'Bottom', 'porto' ) => 'bottom',
				),
				'dependency' => array(
					'element'   => 'show_divider',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Divider Color', 'porto' ),
				'param_name' => 'divider_color',
				'dependency' => array(
					'element'   => 'show_divider',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Divider Height', 'porto' ),
				'param_name' => 'divider_height',
				'dependency' => array(
					'element'   => 'show_divider',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Divider Icon', 'porto' ),
				'param_name' => 'show_divider_icon',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'dependency' => array(
					'element'   => 'show_divider',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon library', 'js_composer' ),
				'value'      => array(
					__( 'Font Awesome', 'porto' )      => 'fontawesome',
					__( 'Simple Line Icon', 'porto' )  => 'simpleline',
					__( 'Custom Image Icon', 'porto' ) => 'image',
				),
				'param_name' => 'divider_icon_type',
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Select Icon', 'porto' ),
				'dependency' => array(
					'element' => 'divider_icon_type',
					'value'   => 'image',
				),
				'param_name' => 'divider_icon_image',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'divider_icon',
				'dependency' => array(
					'element' => 'divider_icon_type',
					'value'   => 'fontawesome',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'divider_icon_simpleline',
				'value'      => '',
				'settings'   => array(
					'type'         => 'simpleline',
					'iconsPerPage' => 4000,
				),
				'dependency' => array(
					'element' => 'divider_icon_type',
					'value'   => 'simpleline',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Skin Color', 'porto' ),
				'param_name' => 'divider_icon_skin',
				'std'        => 'custom',
				'value'      => porto_vc_commons( 'colors' ),
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Color', 'porto' ),
				'param_name' => 'divider_icon_color',
				'dependency' => array(
					'element' => 'divider_icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Background Color', 'porto' ),
				'param_name' => 'divider_icon_bg_color',
				'dependency' => array(
					'element' => 'divider_icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Border Color', 'porto' ),
				'param_name' => 'divider_icon_border_color',
				'dependency' => array(
					'element' => 'divider_icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Wrap Border Color', 'porto' ),
				'param_name' => 'divider_icon_wrap_border_color',
				'dependency' => array(
					'element' => 'divider_icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Style', 'porto' ),
				'param_name' => 'divider_icon_style',
				'value'      => porto_vc_commons( 'separator_icon_style' ),
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Position', 'porto' ),
				'param_name' => 'divider_icon_pos',
				'value'      => porto_vc_commons( 'separator_icon_pos' ),
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Size', 'porto' ),
				'param_name' => 'divider_icon_size',
				'value'      => porto_vc_commons( 'separator_icon_size' ),
				'dependency' => array(
					'element'   => 'show_divider_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Enable Sticky Options?', 'porto' ),
				'param_name'  => 'is_sticky',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $sticky_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Container Selector', 'porto' ),
				'param_name' => 'sticky_container_selector',
				'value'      => '.vc_row',
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Min Width (unit: px)', 'porto' ),
				'param_name' => 'sticky_min_width',
				''           => __( 'Wll be disabled if window width is smaller than min width', 'porto' ),
				'value'      => 767,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Top (unit: px)', 'porto' ),
				'param_name' => 'sticky_top',
				''           => __( 'Top position when active', 'porto' ),
				'value'      => 110,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Bottom (unit: px)', 'porto' ),
				'param_name' => 'sticky_bottom',
				''           => __( 'Bottom position when active', 'porto' ),
				'value'      => 0,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Active Class', 'porto' ),
				'param_name' => 'sticky_active_class',
				'value'      => 'sticky-active',
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param( 'vc_column', $animation_type );
		vc_add_param( 'vc_column', $animation_duration );
		vc_add_param( 'vc_column', $animation_delay );

		vc_add_param(
			'vc_column_inner',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Enable Sticky Options?', 'porto' ),
				'param_name'  => 'is_sticky',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $sticky_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_column_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Container Selector', 'porto' ),
				'param_name' => 'sticky_container_selector',
				'value'      => '.vc_row',
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_column_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Min Width (unit: px)', 'porto' ),
				'param_name' => 'sticky_min_width',
				''           => __( 'Wll be disabled if window width is smaller than min width', 'porto' ),
				'value'      => 767,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_column_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Top (unit: px)', 'porto' ),
				'param_name' => 'sticky_top',
				''           => __( 'Top position when active', 'porto' ),
				'value'      => 110,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_column_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Bottom (unit: px)', 'porto' ),
				'param_name' => 'sticky_bottom',
				''           => __( 'Bottom position when active', 'porto' ),
				'value'      => 0,
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param(
			'vc_column_inner',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Active Class', 'porto' ),
				'param_name' => 'sticky_active_class',
				'value'      => 'sticky-active',
				'dependency' => array(
					'element'   => 'is_sticky',
					'not_empty' => true,
				),
				'group'      => $sticky_group,
			)
		);
		vc_add_param( 'vc_column_inner', $animation_type );
		vc_add_param( 'vc_column_inner', $animation_duration );
		vc_add_param( 'vc_column_inner', $animation_delay );

		/* ---------------------------- */
		/* Customize Custom Heading
		/* ---------------------------- */
		vc_add_param(
			'vc_custom_heading',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Text Transform', 'porto' ),
				'param_name' => 'text_transform',
				'std'        => '',
				'value'      => array(
					__( 'None', 'porto' )       => '',
					__( 'Uppercase', 'porto' )  => 'text-uppercase',
					__( 'Lowercase', 'porto' )  => 'text-lowercase',
					__( 'Capitalize', 'porto' ) => 'text-capitalize',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_custom_heading',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Font Weight', 'porto' ),
				'param_name' => 'font_weight',
				'std'        => '',
				'value'      => array(
					__( 'Default', 'porto' ) => '',
					'100'                    => '100',
					'200'                    => '200',
					'300'                    => '300',
					'400'                    => '400',
					'500'                    => '500',
					'600'                    => '600',
					'700'                    => '700',
					'800'                    => '800',
					'900'                    => '900',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_custom_heading',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Skin Color', 'porto' ),
				'param_name' => 'skin',
				'std'        => 'custom',
				'value'      => porto_vc_commons( 'colors' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_custom_heading',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Letter Spacing', 'porto' ),
				'param_name' => 'letter_spacing',
				'std'        => '',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_custom_heading',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Border', 'porto' ),
				'param_name' => 'show_border',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_custom_heading',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Border Skin Color', 'porto' ),
				'param_name' => 'border_skin',
				'std'        => 'custom',
				'value'      => porto_vc_commons( 'colors' ),
				'dependency' => array(
					'element'   => 'show_border',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_custom_heading',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Border Color', 'porto' ),
				'param_name' => 'border_color',
				'dependency' => array(
					'element' => 'border_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_custom_heading',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Border Type', 'porto' ),
				'param_name' => 'border_type',
				'value'      => porto_vc_commons( 'heading_border_type' ),
				'dependency' => array(
					'element'   => 'show_border',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_custom_heading',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Border Size', 'porto' ),
				'param_name' => 'border_size',
				'value'      => porto_vc_commons( 'heading_border_size' ),
				'dependency' => array(
					'element'   => 'show_border',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_remove_param( 'vc_custom_heading', 'css_animation' );
		vc_add_param( 'vc_custom_heading', $animation_type );
		vc_add_param( 'vc_custom_heading', $animation_duration );
		vc_add_param( 'vc_custom_heading', $animation_delay );

		vc_add_param( 'vc_custom_heading', $floating_start_pos );
		vc_add_param( 'vc_custom_heading', $floating_speed );
		vc_add_param( 'vc_custom_heading', $floating_transition );
		vc_add_param( 'vc_custom_heading', $floating_horizontal );
		vc_add_param( 'vc_custom_heading', $floating_duration );

		/* ---------------------------- */
		/* Customize Tabs, Tab
		/* ---------------------------- */
		vc_remove_param( 'vc_tabs', 'interval' );
		vc_add_param(
			'vc_tabs',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Position', 'porto' ),
				'param_name'  => 'position',
				'value'       => porto_vc_commons( 'tabs' ),
				'description' => __( 'Select the position of the tab header.', 'porto' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_tabs',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Skin Color', 'porto' ),
				'param_name'  => 'skin',
				'std'         => 'custom',
				'value'       => porto_vc_commons( 'colors' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_tabs',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Color', 'porto' ),
				'param_name' => 'color',
				'dependency' => array(
					'element' => 'skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tabs',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Type', 'porto' ),
				'param_name'  => 'type',
				'value'       => porto_vc_commons( 'tabs_type' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_tabs',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Icon Style', 'porto' ),
				'param_name'  => 'icon_style',
				'value'       => porto_vc_commons( 'tabs_icon_style' ),
				'admin_label' => true,
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'tabs-simple' ),
				),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_tabs',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Icon Effect', 'porto' ),
				'param_name'  => 'icon_effect',
				'value'       => porto_vc_commons( 'tabs_icon_effect' ),
				'group'       => $section_group,
				'admin_label' => true,
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'tabs-simple' ),
				),
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Icon', 'porto' ),
				'param_name' => 'show_icon',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon library', 'js_composer' ),
				'value'      => array(
					__( 'Font Awesome', 'porto' )      => 'fontawesome',
					__( 'Simple Line Icon', 'porto' )  => 'simpleline',
					__( 'Custom Image Icon', 'porto' ) => 'image',
				),
				'param_name' => 'icon_type',
				'dependency' => array(
					'element'   => 'show_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Select Icon', 'porto' ),
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'image',
				),
				'param_name' => 'icon_image',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'icon',
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'fontawesome',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'icon_simpleline',
				'value'      => '',
				'settings'   => array(
					'type'         => 'simpleline',
					'iconsPerPage' => 4000,
				),
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'simpleline',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'label',
				'heading'    => __( 'Please configure the following options when Tabs Type is Simple.', 'porto' ),
				'param_name' => 'label',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Skin Color', 'porto' ),
				'param_name' => 'icon_skin',
				'std'        => 'custom',
				'value'      => porto_vc_commons( 'colors' ),
				'dependency' => array(
					'element'   => 'show_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Color', 'porto' ),
				'param_name' => 'icon_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Background Color', 'porto' ),
				'param_name' => 'icon_bg_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Border Color', 'porto' ),
				'param_name' => 'icon_border_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Wrap Border Color', 'porto' ),
				'param_name' => 'icon_wrap_border_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Box Shadow Color', 'porto' ),
				'param_name' => 'icon_shadow_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Hover Color', 'porto' ),
				'param_name' => 'icon_hcolor',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Hover Background Color', 'porto' ),
				'param_name' => 'icon_hbg_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Hover Border Color', 'porto' ),
				'param_name' => 'icon_hborder_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Wrap Hover Border Color', 'porto' ),
				'param_name' => 'icon_wrap_hborder_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_tab',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Hover Box Shadow Color', 'porto' ),
				'param_name' => 'icon_hshadow_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);

		/* ---------------------------- */
		/* Customize Tour
		/* ---------------------------- */
		vc_remove_param( 'vc_tour', 'interval' );
		vc_add_param(
			'vc_tour',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Position', 'porto' ),
				'param_name'  => 'position',
				'value'       => porto_vc_commons( 'tour' ),
				'description' => __( 'Select the position of the tab header.', 'porto' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_tour',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Skin Color', 'porto' ),
				'param_name'  => 'skin',
				'std'         => 'custom',
				'value'       => porto_vc_commons( 'colors' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);
		vc_add_param(
			'vc_tour',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Color', 'porto' ),
				'param_name' => 'color',
				'group'      => $section_group,
				'dependency' => array(
					'element' => 'skin',
					'value'   => array( 'custom' ),
				),
			)
		);
		vc_add_param(
			'vc_tour',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Type', 'porto' ),
				'param_name'  => 'type',
				'value'       => porto_vc_commons( 'tour_type' ),
				'group'       => $section_group,
				'admin_label' => true,
			)
		);

		/* ---------------------------- */
		/* Customize Separator
		/* ---------------------------- */
		vc_remove_param( 'vc_separator', 'style' );
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Type', 'porto' ),
				'param_name' => 'type',
				'value'      => porto_vc_commons( 'separator_type' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Style', 'porto' ),
				'param_name' => 'style',
				'value'      => porto_vc_commons( 'separator_style' ),
				'dependency' => array(
					'element' => 'type',
					'value'   => array( '' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Pattern', 'porto' ),
				'param_name' => 'pattern',
				'dependency' => array(
					'element' => 'style',
					'value'   => array( 'pattern' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Pattern Repeat', 'porto' ),
				'param_name' => 'pattern_repeat',
				'value'      => porto_vc_commons( 'separator_repeat' ),
				'dependency' => array(
					'element' => 'style',
					'value'   => array( 'pattern' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Pattern Position', 'porto' ),
				'param_name' => 'pattern_position',
				'value'      => porto_vc_commons( 'separator_position' ),
				'dependency' => array(
					'element' => 'style',
					'value'   => array( 'pattern' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Pattern Height (unit: px)', 'porto' ),
				'param_name' => 'pattern_height',
				'dependency' => array(
					'element' => 'style',
					'value'   => array( 'pattern' ),
				),
				'value'      => '15',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Icon', 'porto' ),
				'param_name' => 'show_icon',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon library', 'js_composer' ),
				'value'      => array(
					__( 'Font Awesome', 'porto' )      => 'fontawesome',
					__( 'Simple Line Icon', 'porto' )  => 'simpleline',
					__( 'Custom Image Icon', 'porto' ) => 'image',
				),
				'param_name' => 'icon_type',
				'dependency' => array(
					'element'   => 'show_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Select Icon', 'porto' ),
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'image',
				),
				'param_name' => 'icon_image',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'icon',
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'fontawesome',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'icon_simpleline',
				'value'      => '',
				'settings'   => array(
					'type'         => 'simpleline',
					'iconsPerPage' => 4000,
				),
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'simpleline',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Skin Color', 'porto' ),
				'param_name' => 'icon_skin',
				'std'        => 'custom',
				'value'      => porto_vc_commons( 'colors' ),
				'dependency' => array(
					'element'   => 'show_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Color', 'porto' ),
				'param_name' => 'icon_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Background Color', 'porto' ),
				'param_name' => 'icon_bg_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Border Color', 'porto' ),
				'param_name' => 'icon_border_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Wrap Border Color', 'porto' ),
				'param_name' => 'icon_wrap_border_color',
				'dependency' => array(
					'element' => 'icon_skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Style', 'porto' ),
				'param_name' => 'icon_style',
				'value'      => porto_vc_commons( 'separator_icon_style' ),
				'dependency' => array(
					'element'   => 'show_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Position', 'porto' ),
				'param_name' => 'icon_pos',
				'value'      => porto_vc_commons( 'separator_icon_pos' ),
				'dependency' => array(
					'element'   => 'show_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon Size', 'porto' ),
				'param_name' => 'icon_size',
				'value'      => porto_vc_commons( 'separator_icon_size' ),
				'dependency' => array(
					'element'   => 'show_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Gap Size', 'porto' ),
				'param_name' => 'gap',
				'value'      => porto_vc_commons( 'separator' ),
				'group'      => $section_group,
			)
		);

		/* ---------------------------- */
		/* Customize Text Separator
		/* ---------------------------- */
		vc_remove_param( 'vc_text_separator', 'style' );
		vc_add_param(
			'vc_text_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Style', 'porto' ),
				'param_name' => 'style',
				'value'      => porto_vc_commons( 'separator_style' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_text_separator',
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Pattern', 'porto' ),
				'param_name' => 'pattern',
				'dependency' => array(
					'element' => 'style',
					'value'   => array( 'pattern' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_text_separator',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Element Tag', 'porto' ),
				'param_name' => 'element',
				'std'        => 'h4',
				'value'      => porto_vc_commons( 'separator_elements' ),
				'group'      => $section_group,
			)
		);

		/* ---------------------------- */
		/* Customize Accordion, Accordion Tab
		/* ---------------------------- */
		vc_remove_param( 'vc_accordion', 'disable_keyboard' );
		vc_add_param(
			'vc_accordion',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Open only a section?', 'porto' ),
				'param_name' => 'use_accordion',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Type', 'porto' ),
				'param_name' => 'type',
				'value'      => porto_vc_commons( 'accordion' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Size', 'porto' ),
				'param_name' => 'size',
				'value'      => porto_vc_commons( 'accordion_size' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Skin Color', 'porto' ),
				'param_name'  => 'skin',
				'std'         => 'custom',
				'value'       => porto_vc_commons( 'colors' ),
				'admin_label' => true,
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Heading Color', 'porto' ),
				'param_name' => 'heading_color',
				'dependency' => array(
					'element' => 'skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Heading Background Color', 'porto' ),
				'param_name' => 'heading_bg_color',
				'dependency' => array(
					'element' => 'skin',
					'value'   => array( 'custom' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion_tab',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Icon', 'porto' ),
				'param_name' => 'show_icon',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion_tab',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon library', 'js_composer' ),
				'value'      => array(
					__( 'Font Awesome', 'porto' )      => 'fontawesome',
					__( 'Simple Line Icon', 'porto' )  => 'simpleline',
					__( 'Custom Image Icon', 'porto' ) => 'image',
				),
				'param_name' => 'icon_type',
				'dependency' => array(
					'element'   => 'show_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion_tab',
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Select Icon', 'porto' ),
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'image',
				),
				'param_name' => 'icon_image',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion_tab',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'icon',
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'fontawesome',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_accordion_tab',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'icon_simpleline',
				'value'      => '',
				'settings'   => array(
					'type'         => 'simpleline',
					'iconsPerPage' => 4000,
				),
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'simpleline',
				),
				'group'      => $section_group,
			)
		);

		/* ---------------------------- */
		/* Customize Toggle
		/* ---------------------------- */
		vc_remove_param( 'vc_toggle', 'style' );
		vc_remove_param( 'vc_toggle', 'color' );
		vc_remove_param( 'vc_toggle', 'size' );
		vc_add_param(
			'vc_toggle',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Icon', 'porto' ),
				'param_name' => 'show_icon',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_toggle',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Icon library', 'js_composer' ),
				'value'      => array(
					__( 'Font Awesome', 'porto' )      => 'fontawesome',
					__( 'Simple Line Icon', 'porto' )  => 'simpleline',
					__( 'Custom Image Icon', 'porto' ) => 'image',
				),
				'param_name' => 'icon_type',
				'dependency' => array(
					'element'   => 'show_icon',
					'not_empty' => true,
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_toggle',
			array(
				'type'       => 'attach_image',
				'heading'    => __( 'Select Icon', 'porto' ),
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'image',
				),
				'param_name' => 'icon_image',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_toggle',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'icon',
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'fontawesome',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_toggle',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select Icon', 'porto' ),
				'param_name' => 'icon_simpleline',
				'value'      => '',
				'settings'   => array(
					'type'         => 'simpleline',
					'iconsPerPage' => 4000,
				),
				'dependency' => array(
					'element' => 'icon_type',
					'value'   => 'simpleline',
				),
				'group'      => $section_group,
			)
		);

		/* ---------------------------- */
		/* Customize Buttons
		/* ---------------------------- */
		vc_add_param(
			'vc_button',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Disable', 'porto' ),
				'param_name' => 'disabled',
				'value'      => array( __( 'Disable button.', 'porto' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_button',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show as Label', 'porto' ),
				'param_name' => 'label',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Skin Color', 'porto' ),
				'param_name' => 'skin',
				'std'        => 'custom',
				'value'      => array_merge(
					porto_vc_commons( 'colors' ),
					array(
						__( 'Default', 'porto' ) => 'default',
					)
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Color Scale', 'porto' ),
				'param_name' => 'scale',
				'std'        => '',
				'value'      => array(
					__( 'Default', 'porto' ) => '',
					__( 'Scale 2', 'porto' ) => 'scale-2',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Contextual Classes', 'porto' ),
				'param_name' => 'contextual',
				'value'      => porto_vc_commons( 'contextual' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Font Size', 'porto' ),
				'param_name' => 'btn_fs',
				'value'      => '',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Font Weight', 'porto' ),
				'param_name' => 'btn_fw',
				'std'        => '',
				'value'      => array(
					__( 'Default', 'porto' ) => '',
					'100'                    => '100',
					'200'                    => '200',
					'300'                    => '300',
					'400'                    => '400',
					'500'                    => '500',
					'600'                    => '600',
					'700'                    => '700',
					'800'                    => '800',
					'900'                    => '900',
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Letter Spacing', 'porto' ),
				'param_name' => 'btn_ls',
				'value'      => '',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Button Left / Right Padding', 'porto-functionality' ),
				'param_name' => 'btn_px',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Button Top / Bottom Padding', 'porto-functionality' ),
				'param_name' => 'btn_py',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Extra Class', 'porto' ),
				'param_name' => 'el_cls',
				'value'      => '',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show as Label', 'porto' ),
				'param_name' => 'label',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show pointer arrow', 'porto' ),
				'param_name' => 'show_arrow',
				'value'      => array( __( 'Yes', 'porto' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Is Arrow Button?', 'porto' ),
				'param_name' => 'btn_arrow',
				'value'      => array( __( 'Yes', 'porto' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		$param = WPBMap::getParam( 'vc_btn', 'size' );
		$param['value'][ __( 'Extra Large', 'porto' ) ] = 'xl';
		vc_update_shortcode_param( 'vc_btn', $param );

		$param                                      = WPBMap::getParam( 'vc_btn', 'shape' );
		$param['value'][ __( 'Default', 'porto' ) ] = 'default';
		$param['std']                               = 'default';
		vc_update_shortcode_param( 'vc_btn', $param );

		$param        = WPBMap::getParam( 'vc_btn', 'style' );
		$param['std'] = 'classic';
		vc_update_shortcode_param( 'vc_btn', $param );

		vc_remove_param( 'vc_btn', 'css_animation' );
		vc_add_param( 'vc_btn', $animation_type );
		vc_add_param( 'vc_btn', $animation_duration );
		vc_add_param( 'vc_btn', $animation_delay );

		vc_add_param( 'vc_btn', $floating_start_pos );
		vc_add_param( 'vc_btn', $floating_speed );
		vc_add_param( 'vc_btn', $floating_transition );
		vc_add_param( 'vc_btn', $floating_horizontal );
		vc_add_param( 'vc_btn', $floating_duration );

		/* ---------------------------- */
		/* Add Single Image Parameters
		/* ---------------------------- */
		vc_add_param(
			'vc_single_image',
			array(
				'type'       => 'label',
				'heading'    => __( 'Please select "On click action" as "Link to Large Image" in "Design Section" before configure.', 'porto' ),
				'param_name' => 'label',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_single_image',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'LightBox', 'porto' ),
				'param_name'  => 'lightbox',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'description' => __( 'Check it if you want to link to the lightbox with the large image.', 'porto' ),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_single_image',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Show as Image Gallery', 'porto' ),
				'param_name'  => 'image_gallery',
				'description' => __( 'Show all the images inside of same row.', 'porto' ),
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_single_image',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Container Class', 'porto' ),
				'param_name' => 'container_class',
				'dependency' => array(
					'element'   => 'image_gallery',
					'not_empty' => true,
				),
				'value'      => 'vc_row',
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_single_image',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Zoom Icon', 'porto' ),
				'param_name' => 'zoom_icon',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_single_image',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Hover Effect', 'porto' ),
				'param_name' => 'hover_effect',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_remove_param( 'vc_single_image', 'css_animation' );
		vc_add_param( 'vc_single_image', $animation_type );
		vc_add_param( 'vc_single_image', $animation_duration );
		vc_add_param( 'vc_single_image', $animation_delay );

		vc_add_param( 'vc_single_image', $floating_start_pos );
		vc_add_param( 'vc_single_image', $floating_speed );
		vc_add_param( 'vc_single_image', $floating_transition );
		vc_add_param( 'vc_single_image', $floating_horizontal );
		vc_add_param( 'vc_single_image', $floating_duration );

		/* ---------------------------- */
		/* Customize Progress Bar
		/* ---------------------------- */
		vc_add_param(
			'vc_progress_bar',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Contextual Classes', 'porto' ),
				'param_name'  => 'contextual',
				'value'       => porto_vc_commons( 'contextual' ),
				'admin_label' => true,
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_progress_bar',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Enable Animation', 'porto' ),
				'param_name' => 'animation',
				'std'        => 'yes',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_progress_bar',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Show Percentage as Tooltip', 'porto' ),
				'param_name' => 'tooltip',
				'std'        => 'yes',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_progress_bar',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Border Radius', 'porto' ),
				'param_name' => 'border_radius',
				'value'      => porto_vc_commons( 'progress_border_radius' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_progress_bar',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'Size', 'porto' ),
				'param_name' => 'size',
				'value'      => porto_vc_commons( 'progress_size' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_progress_bar',
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Min Width', 'porto' ),
				'description' => 'ex: 2em or 30px, etc',
				'param_name'  => 'min_width',
				'group'       => $section_group,
			)
		);

		/* ---------------------------- */
		/* Customize Pie Chart
		/* ---------------------------- */
		vc_remove_param( 'vc_pie', 'color' );

		// Used in 'Button', 'Call __( 'Blue', 'js_composer' )to Action', 'Pie chart' blocks
		$colors_arr = array(
			__( 'Grey', 'js_composer' )      => 'wpb_button',
			__( 'Blue', 'js_composer' )      => 'btn-primary',
			__( 'Turquoise', 'js_composer' ) => 'btn-info',
			__( 'Green', 'js_composer' )     => 'btn-success',
			__( 'Orange', 'js_composer' )    => 'btn-warning',
			__( 'Red', 'js_composer' )       => 'btn-danger',
			__( 'Black', 'js_composer' )     => 'btn-inverse',
		);

		vc_add_param(
			'vc_pie',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Type', 'porto' ),
				'param_name'  => 'type',
				'std'         => 'custom',
				'value'       => array(
					__( 'Porto Circular Bar', 'porto' ) => 'custom',
					__( 'VC Pie Chart', 'porto' )       => 'default',
				),
				'description' => __( 'Select pie chart type.', 'porto' ),
				'admin_label' => true,
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'               => 'dropdown',
				'heading'            => __( 'Bar color', 'porto' ),
				'param_name'         => 'color',
				'value'              => $colors_arr, //$pie_colors,
				'description'        => __( 'Select pie chart color.', 'js_composer' ),
				'dependency'         => array(
					'element' => 'type',
					'value'   => array( 'default' ),
				),
				'param_holder_class' => 'vc_colored-dropdown',
				'group'              => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'View Type', 'porto' ),
				'param_name' => 'view',
				'dependency' => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'value'      => porto_vc_commons( 'circular_view_type' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Select FontAwesome Icon', 'porto' ),
				'param_name' => 'icon',
				'dependency' => array(
					'element' => 'view',
					'value'   => array( 'only-icon' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'       => 'colorpicker',
				'heading'    => __( 'Icon Color', 'porto' ),
				'param_name' => 'icon_color',
				'dependency' => array(
					'element' => 'view',
					'value'   => array( 'only-icon' ),
				),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'       => 'dropdown',
				'heading'    => __( 'View Size', 'porto' ),
				'param_name' => 'view_size',
				'dependency' => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'value'      => porto_vc_commons( 'circular_view_size' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Bar Size', 'porto' ),
				'param_name'  => 'size',
				'std'         => 175,
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'description' => __( 'Enter the size of the chart in px.', 'porto' ),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'        => 'colorpicker',
				'heading'     => __( 'Track Color', 'porto' ),
				'param_name'  => 'trackcolor',
				'std'         => $dark ? '#2e353e' : '#eeeeee',
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'description' => __( 'Choose the color of the track. Please clear this if you want to use the default color.', 'porto' ),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'        => 'colorpicker',
				'heading'     => __( 'Bar color', 'porto' ),
				'param_name'  => 'barcolor',
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'description' => __( 'Select pie chart color. Please clear this if you want to use the default color.', 'porto' ),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'        => 'colorpicker',
				'heading'     => __( 'Scale color', 'porto' ),
				'param_name'  => 'scalecolor',
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'description' => __( 'Choose the color of the scale. Please clear this if you want to hide the scale.', 'porto' ),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Animation Speed', 'porto' ),
				'param_name'  => 'speed',
				'std'         => 2500,
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'description' => __( 'Enter the animation speed in milliseconds.', 'porto' ),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'        => 'textfield',
				'heading'     => __( 'Line Width', 'porto' ),
				'param_name'  => 'line',
				'std'         => 14,
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'description' => __( 'Enter the width of the line bar in px.', 'porto' ),
				'group'       => $section_group,
			)
		);
		vc_add_param(
			'vc_pie',
			array(
				'type'        => 'dropdown',
				'heading'     => __( 'Line Cap', 'porto' ),
				'param_name'  => 'linecap',
				'std'         => 'round',
				'value'       => array(
					__( 'Round', 'porto' )  => 'round',
					__( 'Square', 'porto' ) => 'square',
				),
				'dependency'  => array(
					'element' => 'type',
					'value'   => array( 'custom' ),
				),
				'description' => __( 'Choose how the ending of the bar line looks like.', 'porto' ),
				'group'       => $section_group,
			)
		);

		// remove unwanted shortcodes
		/*vc_remove_element('vc_posts_grid');
		vc_remove_element('vc_carousel');
		vc_remove_element('vc_message');
		vc_remove_element('vc_hoverbox');

		vc_remove_element('vc_gmaps');
		vc_remove_element('vc_posts_slider');
		vc_remove_element('vc_zigzag');
		vc_remove_element('vc_round_chart');
		vc_remove_element('vc_line_chart');*/

	}
}

if ( ! function_exists( 'porto_vc_commons' ) ) {
	function porto_vc_commons( $asset = '' ) {
		switch ( $asset ) {
			case 'accordion':
				return Porto_VcSharedLibrary::getAccordionType();
			case 'accordion_size':
				return Porto_VcSharedLibrary::getAccordionSize();
			case 'align':
				return Porto_VcSharedLibrary::getTextAlign();
			case 'tabs':
				return Porto_VcSharedLibrary::getTabsPositions();
			case 'tabs_type':
				return Porto_VcSharedLibrary::getTabsType();
			case 'tabs_icon_style':
				return Porto_VcSharedLibrary::getTabsIconStyle();
			case 'tabs_icon_effect':
				return Porto_VcSharedLibrary::getTabsIconEffect();
			case 'tour':
				return Porto_VcSharedLibrary::getTourPositions();
			case 'tour_type':
				return Porto_VcSharedLibrary::getTourType();
			case 'separator':
				return Porto_VcSharedLibrary::getSeparator();
			case 'separator_type':
				return Porto_VcSharedLibrary::getSeparatorType();
			case 'separator_style':
				return Porto_VcSharedLibrary::getSeparatorStyle();
			case 'separator_repeat':
				return Porto_VcSharedLibrary::getSeparatorRepeat();
			case 'separator_position':
				return Porto_VcSharedLibrary::getSeparatorPosition();
			case 'separator_icon_style':
				return Porto_VcSharedLibrary::getSeparatorIconStyle();
			case 'separator_icon_size':
				return Porto_VcSharedLibrary::getSeparatorIconSize();
			case 'separator_icon_pos':
				return Porto_VcSharedLibrary::getSeparatorIconPosition();
			case 'separator_elements':
				return Porto_VcSharedLibrary::getSeparatorElements();
			case 'colors':
				return Porto_VcSharedLibrary::getColors();
			case 'contextual':
				return Porto_VcSharedLibrary::getContextual();
			case 'progress_border_radius':
				return Porto_VcSharedLibrary::getProgressBorderRadius();
			case 'progress_size':
				return Porto_VcSharedLibrary::getProgressSize();
			case 'circular_view_type':
				return Porto_VcSharedLibrary::getCircularViewType();
			case 'circular_view_size':
				return Porto_VcSharedLibrary::getCircularViewSize();
			case 'section_skin':
				return Porto_VcSharedLibrary::getSectionSkin();
			case 'section_color_scale':
				return Porto_VcSharedLibrary::getSectionColorScale();
			case 'section_text_color':
				return Porto_VcSharedLibrary::getSectionTextColor();
			case 'heading_border_type':
				return Porto_VcSharedLibrary::getHeadingBorderType();
			case 'heading_border_size':
				return Porto_VcSharedLibrary::getHeadingBorderSize();
			default:
				return array();
		}
	}
}

if ( ! class_exists( 'Porto_VcSharedLibrary' ) ) {

	class Porto_VcSharedLibrary {

		public static function getTextAlign() {
			return array(
				__( 'None', 'porto' )    => '',
				__( 'Left', 'porto' )    => 'left',
				__( 'Right', 'porto' )   => 'right',
				__( 'Center', 'porto' )  => 'center',
				__( 'Justify', 'porto' ) => 'justify',
			);
		}

		public static function getTabsPositions() {
			return array(
				__( 'Top left', 'porto' )       => '',
				__( 'Top right', 'porto' )      => 'top-right',
				__( 'Bottom left', 'porto' )    => 'bottom-left',
				__( 'Bottom right', 'porto' )   => 'bottom-right',
				__( 'Top justify', 'porto' )    => 'top-justify',
				__( 'Bottom justify', 'porto' ) => 'bottom-justify',
				__( 'Top center', 'porto' )     => 'top-center',
				__( 'Bottom center', 'porto' )  => 'bottom-center',
			);
		}

		public static function getTabsType() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Simple', 'porto' )  => 'tabs-simple',
			);
		}

		public static function getTabsIconStyle() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Style 1', 'porto' ) => 'featured-boxes-style-1',
				__( 'Style 2', 'porto' ) => 'featured-boxes-style-2',
				__( 'Style 3', 'porto' ) => 'featured-boxes-style-3',
				__( 'Style 4', 'porto' ) => 'featured-boxes-style-4',
				__( 'Style 5', 'porto' ) => 'featured-boxes-style-5',
				__( 'Style 6', 'porto' ) => 'featured-boxes-style-6',
				__( 'Style 7', 'porto' ) => 'featured-boxes-style-7',
				__( 'Style 8', 'porto' ) => 'featured-boxes-style-8',
			);
		}

		public static function getTabsIconEffect() {
			return array(
				__( 'Default', 'porto' )  => '',
				__( 'Effect 1', 'porto' ) => 'featured-box-effect-1',
				__( 'Effect 2', 'porto' ) => 'featured-box-effect-2',
				__( 'Effect 3', 'porto' ) => 'featured-box-effect-3',
				__( 'Effect 4', 'porto' ) => 'featured-box-effect-4',
				__( 'Effect 5', 'porto' ) => 'featured-box-effect-5',
				__( 'Effect 6', 'porto' ) => 'featured-box-effect-6',
				__( 'Effect 7', 'porto' ) => 'featured-box-effect-7',
			);
		}

		public static function getTourPositions() {
			return array(
				__( 'Left', 'porto' )  => 'vertical-left',
				__( 'Right', 'porto' ) => 'vertical-right',
			);
		}

		public static function getTourType() {
			return array(
				__( 'Default', 'porto' )    => '',
				__( 'Navigation', 'porto' ) => 'tabs-navigation',
			);
		}

		public static function getSeparator() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Short', 'porto' )  => 'short',
				__( 'Tall', 'porto' )   => 'tall',
				__( 'Taller', 'porto' ) => 'taller',
			);
		}

		public static function getSeparatorType() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Small', 'porto' )  => 'small',
			);
		}

		public static function getSeparatorStyle() {
			return array(
				__( 'Gradient', 'porto' ) => '',
				__( 'Solid', 'porto' )    => 'solid',
				__( 'Dashed', 'porto' )   => 'dashed',
				__( 'Pattern', 'porto' )  => 'pattern',
			);
		}

		public static function getSeparatorRepeat() {
			return array(
				__( 'Repeat', 'porto' )    => '',
				__( 'No Repeat', 'porto' ) => 'no-repeat',
			);
		}

		public static function getSeparatorPosition() {
			return array(
				__( 'Left Top', 'porto' )      => '',
				__( 'Left Center', 'porto' )   => 'left center',
				__( 'Left Bottom', 'porto' )   => 'left bottom',
				__( 'Center Top', 'porto' )    => 'center top',
				__( 'Center Center', 'porto' ) => 'center center',
				__( 'Center Bottom', 'porto' ) => 'center bottom',
				__( 'Right Top', 'porto' )     => 'right top',
				__( 'Right Center', 'porto' )  => 'right center',
				__( 'Right Bottom', 'porto' )  => 'right bottom',
			);
		}

		public static function getSeparatorIconStyle() {
			return array(
				__( 'Style 1', 'porto' ) => '',
				__( 'Style 2', 'porto' ) => 'style-2',
				__( 'Style 3', 'porto' ) => 'style-3',
				__( 'Style 4', 'porto' ) => 'style-4',
			);
		}

		public static function getSeparatorIconSize() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Small', 'porto' )  => 'sm',
				__( 'Large', 'porto' )  => 'lg',
			);
		}

		public static function getSeparatorIconPosition() {
			return array(
				__( 'Center', 'porto' ) => '',
				__( 'Left', 'porto' )   => 'left',
				__( 'Right', 'porto' )  => 'right',
			);
		}

		public static function getSeparatorElements() {
			return array(
				__( 'h1', 'porto' )  => 'h1',
				__( 'h2', 'porto' )  => 'h2',
				__( 'h3', 'porto' )  => 'h3',
				__( 'h4', 'porto' )  => 'h4',
				__( 'h5', 'porto' )  => 'h5',
				__( 'h6', 'porto' )  => 'h6',
				__( 'p', 'porto' )   => 'p',
				__( 'div', 'porto' ) => 'div',
			);
		}

		public static function getAccordionType() {
			return array(
				__( 'Default', 'porto' )                   => 'panel-default',
				__( 'Modern', 'porto' )                    => 'panel-modern',
				__( 'Modern Without Background', 'porto' ) => 'panel-modern without-bg',
				__( 'Without Background', 'porto' )        => 'without-bg',
				__( 'Without Borders and Background', 'porto' ) => 'without-bg without-borders',
				__( 'Custom', 'porto' )                    => 'custom',
			);
		}

		public static function getAccordionSize() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Small', 'porto' )   => 'panel-group-sm',
				__( 'Large', 'porto' )   => 'panel-group-lg',
			);
		}

		public static function getColors() {
			return array(
				''                          => 'custom',
				__( 'Primary', 'porto' )    => 'primary',
				__( 'Secondary', 'porto' )  => 'secondary',
				__( 'Tertiary', 'porto' )   => 'tertiary',
				__( 'Quaternary', 'porto' ) => 'quaternary',
				__( 'Dark', 'porto' )       => 'dark',
				__( 'Light', 'porto' )      => 'light',
			);
		}

		public static function getContextual() {
			return array(
				__( 'None', 'porto' )    => '',
				__( 'Success', 'porto' ) => 'success',
				__( 'Info', 'porto' )    => 'info',
				__( 'Warning', 'porto' ) => 'warning',
				__( 'Danger', 'porto' )  => 'danger',
			);
		}

		public static function getProgressBorderRadius() {
			return array(
				__( 'Default', 'porto' )               => '',
				__( 'No Border Radius', 'porto' )      => 'no-border-radius',
				__( 'Rounded Border Radius', 'porto' ) => 'border-radius',
			);
		}

		public static function getProgressSize() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Small', 'porto' )  => 'sm',
				__( 'Large', 'porto' )  => 'lg',
			);
		}

		public static function getCircularViewType() {
			return array(
				__( 'Show Title and Value', 'porto' ) => '',
				__( 'Show Only Icon', 'porto' )       => 'only-icon',
				__( 'Show Only Title', 'porto' )      => 'single-line',
			);
		}

		public static function getCircularViewSize() {
			return array(
				__( 'Normal', 'porto' ) => '',
				__( 'Small', 'porto' )  => 'sm',
				__( 'Large', 'porto' )  => 'lg',
			);
		}

		public static function getSectionSkin() {
			return array(
				__( 'Default', 'porto' )     => 'default',
				__( 'Transparent', 'porto' ) => 'parallax',
				__( 'Primary', 'porto' )     => 'primary',
				__( 'Secondary', 'porto' )   => 'secondary',
				__( 'Tertiary', 'porto' )    => 'tertiary',
				__( 'Quaternary', 'porto' )  => 'quaternary',
				__( 'Dark', 'porto' )        => 'dark',
				__( 'Light', 'porto' )       => 'light',
			);
		}

		public static function getSectionColorScale() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Scale 1', 'porto' ) => 'scale-1',
				__( 'Scale 2', 'porto' ) => 'scale-2',
				__( 'Scale 3', 'porto' ) => 'scale-3',
				__( 'Scale 4', 'porto' ) => 'scale-4',
				__( 'Scale 5', 'porto' ) => 'scale-5',
				__( 'Scale 6', 'porto' ) => 'scale-6',
				__( 'Scale 7', 'porto' ) => 'scale-7',
				__( 'Scale 8', 'porto' ) => 'scale-8',
				__( 'Scale 9', 'porto' ) => 'scale-9',
			);
		}

		public static function getSectionTextColor() {
			return array(
				__( 'Default', 'porto' ) => '',
				__( 'Dark', 'porto' )    => 'dark',
				__( 'Light', 'porto' )   => 'light',
			);
		}

		public static function getHeadingBorderType() {
			return array(
				__( 'Bottom Border', 'porto' )         => 'bottom-border',
				__( 'Bottom Double Border', 'porto' )  => 'bottom-double-border',
				__( 'Middle Border', 'porto' )         => 'middle-border',
				__( 'Middle Border Reverse', 'porto' ) => 'middle-border-reverse',
				__( 'Middle Border Center', 'porto' )  => 'middle-border-center',
			);
		}

		public static function getHeadingBorderSize() {
			return array(
				__( 'Normal', 'porto' )      => '',
				__( 'Extra Small', 'porto' ) => 'xs',
				__( 'Small', 'porto' )       => 'sm',
				__( 'Large', 'porto' )       => 'lg',
				__( 'Extra Large', 'porto' ) => 'xl',
			);
		}
	}
}

add_action( 'vc_after_init', 'porto_vc_enable_deprecated_shortcodes' );

function porto_vc_enable_deprecated_shortcodes() {
	if ( class_exists( 'WPBMap' ) ) {
		$category = __( 'Porto', 'porto' );
		$desc     = __( ' with porto style', 'porto' );
		WPBMap::modify( 'vc_tabs', 'deprecated', false );
		WPBMap::modify( 'vc_tabs', 'category', $category );
		WPBMap::modify( 'vc_tabs', 'name', __( 'Porto Tabs', 'porto' ) );
		WPBMap::modify( 'vc_tab', 'name', __( 'Porto Tab', 'porto' ) );
		WPBMap::modify( 'vc_tabs', 'description', __( 'Tabbed content', 'js_composer' ) . $desc );
		WPBMap::modify( 'vc_tour', 'deprecated', false );
		WPBMap::modify( 'vc_tour', 'category', $category );
		WPBMap::modify( 'vc_tour', 'name', __( 'Porto Tour', 'porto' ) );
		WPBMap::modify( 'vc_tour', 'description', __( 'Vertical tabbed content', 'js_composer' ) . $desc );
		WPBMap::modify( 'vc_accordion', 'deprecated', false );
		WPBMap::modify( 'vc_accordion', 'category', $category );
		WPBMap::modify( 'vc_accordion', 'name', __( 'Porto Accordion', 'porto' ) );
		WPBMap::modify( 'vc_accordion', 'description', __( 'Collapsible content panels', 'js_composer' ) . $desc );
		WPBMap::modify( 'vc_accordion_tab', 'name', __( 'Accordion Section', 'porto' ) );

		/*$all_shortcodes = WPBMap::getAllShortCodes();
		foreach( $all_shortcodes as $key => $s ) {
			echo '\''.$key .'\' => \'' . $s['name'] . '\',<br />';
		}*/
	}
}

add_filter( 'vc_add_element_box_buttons', 'porto_vc_remove_deprecated_css_class', 10, 1 );
function porto_vc_remove_deprecated_css_class( $output ) {
	$porto_elements = array( 'vc_tabs', 'vc_tour', 'vc_accordion' );
	foreach ( $porto_elements as $e ) {
		preg_match_all( '/<li data-element="' . $e . '"([^class]*)class="([^"]*)vc_element-deprecated([^"]*)"([^>]*)>/', $output, $matches );
		if ( isset( $matches[0] ) && ! empty( $matches[0] ) ) {
			$str    = str_replace( ' vc_element-deprecated', '', $matches[0] );
			$output = str_replace( $matches[0], $str, $output );
		}
	}
	return $output;
}

if ( ! function_exists( 'porto_image_resize' ) ) :
	function porto_image_resize( $attach_id = null, $thumb_size ) {
		if ( ! $attach_id ) {
			return false;
		}

		if ( is_string( $thumb_size ) ) {
			preg_match_all( '/\d+/', $thumb_size, $thumb_matches );
			if ( isset( $thumb_matches[0] ) ) {
				$thumb_size = array();
				if ( count( $thumb_matches[0] ) > 1 ) {
					$thumb_size[] = $thumb_matches[0][0]; // width
					$thumb_size[] = $thumb_matches[0][1]; // height
				} elseif ( count( $thumb_matches[0] ) > 0 && count( $thumb_matches[0] ) < 2 ) {
					$thumb_size[] = $thumb_matches[0][0]; // width
					$thumb_size[] = $thumb_matches[0][0]; // height
				} else {
					$thumb_size = false;
				}
			}
		}

		$width  = $thumb_size[0];
		$height = $thumb_size[1];
		$crop   = true;

		$image_src = array();

		$image_src        = wp_get_attachment_image_src( $attach_id, 'full' );
		$actual_file_path = get_attached_file( $attach_id );
		// this is not an attachment, let's use the image url

		if ( ! empty( $actual_file_path ) ) {
			$file_info = pathinfo( $actual_file_path );
			$extension = '.' . $file_info['extension'];

			// the image path without the extension
			$no_ext_path = $file_info['dirname'] . '/' . $file_info['filename'];

			$cropped_img_path = $no_ext_path . '-' . $width . 'x' . $height . $extension;

			// checking if the file size is larger than the target size
			// if it is smaller or the same size, stop right here and return
			if ( $image_src[1] > $width || $image_src[2] > $height ) {

				// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
				if ( file_exists( $cropped_img_path ) ) {
					$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
					$vt_image        = array(
						$cropped_img_url,
						$width,
						$height,
					);

					return $vt_image;
				}

				// no cache files - let's finally resize it
				$img_editor = wp_get_image_editor( $actual_file_path );

				if ( is_wp_error( $img_editor ) || is_wp_error( $img_editor->resize( $width, $height, $crop ) ) ) {
					return array(
						'',
						'',
						'',
					);
				}

				$new_img_path = $img_editor->generate_filename();

				if ( is_wp_error( $img_editor->save( $new_img_path ) ) ) {
					return array(
						'',
						'',
						'',
					);
				}
				if ( ! is_string( $new_img_path ) ) {
					return array(
						'',
						'',
						'',
					);
				}

				$new_img_size = getimagesize( $new_img_path );
				$new_img      = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

				// resized output
				$vt_image = array(
					$new_img,
					$new_img_size[0],
					$new_img_size[1],
				);

				return $vt_image;
			}

			// default output - without resizing
			$vt_image = array(
				$image_src[0],
				$image_src[1],
				$image_src[2],
			);

			return $vt_image;
		}
		return false;
	}
endif;

if ( is_admin() ) :
	add_action( 'vc_after_init', 'porto_update_vc_shortcodes_settings', 20 );

	if ( ! function_exists( 'porto_update_vc_shortcodes_settings' ) ) :

		/**
		 * Update WPBakery elements' icon settings
		 */
		function porto_update_vc_shortcodes_settings() {
			global $pagenow;
			if ( ( vc_user_access()->part( 'backend_editor' )->can()->get() && ( 'post-new.php' == $pagenow || 'post.php' == $pagenow ) ) || vc_is_inline() ) {
				add_filter(
					'vc_add_element_box_buttons',
					function( $output ) {
						return preg_replace( '/(<i class="vc_general vc_element-icon[^>]*><\/i>)/', '$1$1', $output );
					},
					20
				);

				// WordPress elements
				vc_map_update( 'vc_wp_search', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_meta', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_recentcomments', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_calendar', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_pages', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_tagcloud', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_custommenu', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_text', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_posts', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_categories', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_archives', 'icon', 'fab fa-wordpress' );
				vc_map_update( 'vc_wp_rss', 'icon', 'fab fa-wordpress' );

				// WPBakery elements
				vc_map_update( 'vc_row', 'icon', 'fas fa-align-justify' );
				vc_map_update( 'vc_custom_heading', 'icon', 'fas fa-heading' );
				vc_map_update( 'vc_message', 'icon', 'fas fa-exclamation-triangle' );
				vc_map_update( 'contact-form-7', 'icon', 'far fa-envelope' );
				vc_map_update( 'vc_column_text', 'icon', 'fas fa-font' );
				vc_map_update( 'vc_gutenberg', 'icon', 'fab fa-google' );


				// WPBakery elements updated in Porto
				vc_map_update( 'vc_tabs', 'icon', 'fas fa-columns' );
				vc_map_update( 'vc_accordion', 'icon', 'fas fa-bars' );
				vc_map_update( 'vc_tour', 'icon', 'fas fa-indent' );
				vc_map_update( 'vc_btn', 'icon', 'fas fa-minus' );
				vc_map_update( 'vc_separator', 'icon', 'fas fa-align-center' );
				vc_map_update( 'vc_progress_bar', 'icon', 'far fa-chart-bar' );
				vc_map_update( 'vc_pie', 'icon', 'fas fa-chart-pie' );
			}
		}
	endif;
endif;
