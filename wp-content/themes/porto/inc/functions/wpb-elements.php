<?php

/*
 * Update WPB default elements
 */

// Replace rows and columns classes
function porto_custom_css_classes_for_elements( $class_string, $tag ) {

	if ( 'vc_row' == $tag || 'vc_row_inner' == $tag ) {
		if ( strpos( $class_string, 'porto-inner-container' ) !== false ) {
			$class_string = str_replace( 'vc_row-fluid', '', $class_string );
			if ( 'vc_row_inner' == $tag ) {
				$class_string = str_replace( ' porto-inner-container', '', $class_string );
			}
		} else {
			$class_string = str_replace( 'vc_row-fluid', 'row', $class_string );
		}
	}
	if ( ! ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) && ( 'vc_column' == $tag || 'vc_column_inner' == $tag ) ) {
		//$class_string = str_replace( 'vc_column_container', '', $class_string );
		if ( preg_match_all( '/vc_col-(\w{2})-(\d{1,2})(\/5|)($| )/', $class_string, $matches ) ) {
			$class_string = str_replace( array( 'vc_col-xl-offset-', 'vc_col-lg-offset-', 'vc_col-md-offset-', 'vc_col-sm-offset-', 'vc_col-xs-offset-' ), array( 'offset-xxl-', 'offset-xl-', 'offset-lg-', 'offset-md-', 'offset-' ), $class_string );
			if ( isset( $matches[1] ) && isset( $matches[2] ) ) {
				$size_array = array(
					'-xl' => '-xxl',
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
			$class_string = str_replace( array( 'vc_hidden-xl', 'vc_hidden-lg', 'vc_hidden-md', 'vc_hidden-sm', 'vc_hidden-xs' ), array( 'd-xxl-none', 'd-xl-none d-xxl-block', 'd-lg-none d-xl-block', 'd-md-none d-lg-block', 'd-none d-md-block' ), $class_string );
			$screens      = array( '', 'md', 'lg', 'xl', 'xxl' );
			for ( $i = 0; $i <= 4; $i++ ) {
				if ( 0 == $i ) {
					$screen = ' d';
				} else {
					$screen = ' d-' . $screens[ $i ];
				}
				if ( strpos( $class_string, $screen . '-block' ) !== false && strpos( $class_string, $screen . '-none' ) !== false ) {
					$class_string = str_replace( $screen . '-block', '', $class_string );
				}
			}
			for ( $i = 4; $i >= 1; $i-- ) {
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
		$porto_cur_version = get_option( 'porto_version', '1.0' );
		$dark              = porto_is_dark_skin();

		$section_group      = __( 'Porto Options', 'porto' );
		$addon_group        = __( 'Porto Addons', 'porto' );
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

		$left  = is_rtl() ? 'right' : 'left';
		$right = is_rtl() ? 'left' : 'right';

		/* ---------------------------- */
		/* Customize Section
		/* ---------------------------- */
		vc_add_params(
			'vc_section',
			array(
				array(
					'type'        => 'checkbox',
					'heading'     => esc_html__( 'Wrap as container', 'porto' ),
					'param_name'  => 'is_container',
					'qa_selector' => '>.vc_section',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Divider Type', 'porto' ),
					'description' => esc_html__( 'Select the type of the shape divider', 'porto' ),
					'param_name'  => 'top_divider_type',
					'value'       => function_exists( 'porto_sh_commons' ) ? porto_sh_commons( 'divider_type' ) : array(),
					'std'         => 'none',
					'admin_label' => true,
					'group'       => esc_html__( 'Top Shape Divider', 'porto' ),
				),
				array(
					'type'        => 'textarea_raw_html',
					'heading'     => esc_html__( 'Custom Shape Divider', 'porto' ),
					'param_name'  => 'top_divider_custom',
					// @codingStandardsIgnoreLine
					'value' => base64_encode( '' ),
					'description' => esc_html__( 'Please writer your svg code.', 'porto' ),
					'dependency'  => array(
						'element' => 'top_divider_type',
						'value'   => 'custom',
					),
					'group'       => esc_html__( 'Top Shape Divider', 'porto' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => esc_html__( 'Divider Color', 'porto' ),
					'param_name'  => 'top_divider_color',
					'description' => esc_html__( 'Select fill color of svg.', 'porto' ),
					'dependency'  => array(
						'element'            => 'top_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'       => esc_html__( 'Top Shape Divider', 'porto' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Height', 'porto' ),
					'description' => esc_html__( 'Please input height of shape divider.', 'porto' ),
					'param_name'  => 'top_divider_height',
					'dependency'  => array(
						'element'            => 'top_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'       => esc_html__( 'Top Shape Divider', 'porto' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => esc_html__( 'Flip', 'porto' ),
					'param_name' => 'top_divider_flip',
					'dependency' => array(
						'element'            => 'top_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'      => esc_html__( 'Top Shape Divider', 'porto' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => esc_html__( 'Invert', 'porto' ),
					'param_name' => 'top_divider_invert',
					'dependency' => array(
						'element'            => 'top_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'      => esc_html__( 'Top Shape Divider', 'porto' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name', 'js_composer' ),
					'param_name'  => 'top_divider_class',
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
					'dependency'  => array(
						'element'            => 'top_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'       => esc_html__( 'Top Shape Divider', 'porto' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Divider Type', 'porto' ),
					'description' => esc_html__( 'Select the type of the shape divider', 'porto' ),
					'param_name'  => 'bottom_divider_type',
					'value'       => function_exists( 'porto_sh_commons' ) ? porto_sh_commons( 'divider_type' ) : array(),
					'std'         => 'none',
					'admin_label' => true,
					'group'       => esc_html__( 'Bottom Shape Divider', 'porto' ),
				),
				array(
					'type'        => 'textarea_raw_html',
					'heading'     => esc_html__( 'Custom Shape Divider', 'porto' ),
					'param_name'  => 'bottom_divider_custom',
					// @codingStandardsIgnoreLine
					'value' => base64_encode( '' ),
					'description' => esc_html__( 'Please writer your svg code.', 'porto' ),
					'dependency'  => array(
						'element' => 'bottom_divider_type',
						'value'   => 'custom',
					),
					'group'       => esc_html__( 'Bottom Shape Divider', 'porto' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => esc_html__( 'Divider Color', 'porto' ),
					'param_name'  => 'bottom_divider_color',
					'description' => esc_html__( 'Select fill color of svg.', 'porto' ),
					'dependency'  => array(
						'element'            => 'bottom_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'       => esc_html__( 'Bottom Shape Divider', 'porto' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Height', 'porto' ),
					'description' => esc_html__( 'Please input height of shape divider.', 'porto' ),
					'param_name'  => 'bottom_divider_height',
					'dependency'  => array(
						'element'            => 'bottom_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'       => esc_html__( 'Bottom Shape Divider', 'porto' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => esc_html__( 'Flip', 'porto' ),
					'param_name' => 'bottom_divider_flip',
					'dependency' => array(
						'element'            => 'bottom_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'      => esc_html__( 'Bottom Shape Divider', 'porto' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => esc_html__( 'Invert', 'porto' ),
					'param_name' => 'bottom_divider_invert',
					'dependency' => array(
						'element'            => 'bottom_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'      => esc_html__( 'Bottom Shape Divider', 'porto' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name', 'porto' ),
					'param_name'  => 'bottom_divider_class',
					'description' => esc_html__( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'porto' ),
					'dependency'  => array(
						'element'            => 'bottom_divider_type',
						'value_not_equal_to' => 'none',
					),
					'group'       => esc_html__( 'Bottom Shape Divider', 'porto' ),
				),
			)
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
				'description' => __( 'Add YouTube link or mp4 video link.', 'porto' ),
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
				'qa_selector' => '>.vc_row',
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
		$post_id = false;
		if ( is_admin() ) {
			if ( isset( $_REQUEST['post_id'] ) ) {
				$post_id = (int) $_REQUEST['post_id'];
			} elseif ( isset( $_REQUEST['post'] ) ) {
				$post_id = (int) $_REQUEST['post'];
			}
		}
		if ( ( $post_id && 'header' == get_post_meta( $post_id, 'porto_builder_type', true ) ) || ! $post_id ) {
			vc_add_param(
				'vc_row',
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Is Main Header?', 'porto' ),
					'description' => __( 'This section will be displayed in sticky header.', 'porto' ),
					'param_name'  => 'is_main_header',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'       => $section_group,
					'admin_label' => true,
				)
			);
		}
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

		// add sticky options
		vc_add_params(
			'vc_row',
			array(
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'desc_row_addon',
					'text'       => sprintf( esc_html__( 'Read this %1$sarticle%2$s to find out more about Porto Addons.', 'porto' ), '<a target="_blank" href="https://www.portotheme.com/wordpress/porto/documentation/wpbakery-porto-addons/">', '</a>' ),
					'group'      => $addon_group,
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Sticky Options?', 'porto' ),
					'param_name'  => 'is_sticky',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'       => $addon_group,
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Container Selector', 'porto' ),
					'param_name' => 'sticky_container_selector',
					'value'      => '.main-content',
					'dependency' => array(
						'element'   => 'is_sticky',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
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
					'group'      => $addon_group,
				),
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
					'group'      => $addon_group,
				),
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
					'group'      => $addon_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Active Class', 'porto' ),
					'param_name' => 'sticky_active_class',
					'value'      => 'sticky-active',
					'dependency' => array(
						'element'   => 'is_sticky',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
			)
		);

		// add animation options
		vc_add_params(
			'vc_row',
			array(
				$animation_type,
				$animation_duration,
				$animation_delay,
			)
		);

		// add scroll parallax effect
		vc_add_params(
			'vc_row',
			array(
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Scroll Parallax?', 'porto' ),
					'description' => __( 'Section\'s width changes when scrolling page.', 'porto' ),
					'param_name'  => 'scroll_parallax',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'       => $addon_group,
				),
				array(
					'type'       => 'porto_button_group',
					'heading'    => __( 'CSS Unit', 'porto' ),
					'param_name' => 'scroll_unit',
					'std'        => 'vw',
					'value'      => array(
						'vw' => array(
							'title' => 'vw',
						),
						'%'  => array(
							'title' => '%',
						),
					),
					'dependency' => array(
						'element'   => 'scroll_parallax',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Start Width', 'porto' ),
					'param_name' => 'scroll_parallax_width',
					'std'        => 40,
					'min'        => 10,
					'max'        => 90,
					'step'       => 1,
					'dependency' => array(
						'element'   => 'scroll_parallax',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
			)
		);

		// add particles effect
		vc_add_params(
			'vc_row',
			array(
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Particles Effect?', 'porto' ),
					'param_name' => 'particles_effect',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'      => $addon_group,
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Particles Image', 'porto' ),
					'param_name' => 'particles_img',
					'dependency' => array(
						'element'   => 'particles_effect',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Particles Hover Effect', 'porto' ),
					'param_name' => 'particles_hover_effect',
					'value'      => array(
						__( 'None', 'porto' )    => '',
						__( 'Grab', 'porto' )    => 'grab',
						__( 'Bubble', 'porto' )  => 'bubble',
						__( 'Repulse', 'porto' ) => 'repulse',
					),
					'std'        => '',
					'dependency' => array(
						'element'   => 'particles_img',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Particles Click Effect', 'porto' ),
					'param_name' => 'particles_click_effect',
					'value'      => array(
						__( 'None', 'porto' )    => '',
						__( 'Grab', 'porto' )    => 'grab',
						__( 'Bubble', 'porto' )  => 'bubble',
						__( 'Repulse', 'porto' ) => 'repulse',
						__( 'Push', 'porto' )    => 'push',
						__( 'Remove', 'porto' )  => 'remove',
					),
					'std'        => '',
					'dependency' => array(
						'element'   => 'particles_img',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
			)
		);

		// add mouse hover split effect
		vc_add_params(
			'vc_row',
			array(
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Mouse Hover Split?', 'porto' ),
					'param_name'  => 'hover_split',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'description' => __( 'For Hover Split, the row should have two split slide columns.', 'porto' ),
					'group'       => $addon_group,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Min Height', 'porto' ),
					'description' => __( 'Control the min height of split slide. Default is 300px.', 'porto' ),
					'param_name'  => 'split_mh',
					'dependency'  => array(
						'element'   => 'hover_split',
						'not_empty' => true,
					),
					'group'       => $addon_group,
				),
			)
		);

		// add Scroll Effect In Viewport
		vc_add_params(
			'vc_row',
			array(
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'desc_scroll_inviewport',
					'text'       => __( 'Please don\'t use the background option in the "Design Options" tab.', 'porto' ),
					'group'      => $addon_group,
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Scroll Effect In Viewport?', 'porto' ),
					'param_name'  => 'scroll_inviewport',
					'description' => esc_html__( 'Section\'s background color changes when scrolling page.', 'porto' ),
					'group'       => $addon_group,
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Inside Background Color', 'porto' ),
					'param_name'  => 'scroll_bg',
					'description' => esc_html__( 'Actual Background Color in the viewport.', 'porto' ),
					'dependency'  => array(
						'element'   => 'scroll_inviewport',
						'not_empty' => true,
					),
					'group'       => $addon_group,
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Outside Background Color', 'porto' ),
					'param_name'  => 'scroll_bg_inout',
					'description' => esc_html__( 'Background Color for entering or exit.', 'porto' ),
					'dependency'  => array(
						'element'   => 'scroll_inviewport',
						'not_empty' => true,
					),
					'group'       => $addon_group,
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Top Offset(px)', 'porto' ),
					'param_name'  => 'scroll_top_mode',
					'description' => esc_html__( 'Background Color for entering or exit.', 'porto' ),
					'dependency'  => array(
						'element'   => 'scroll_inviewport',
						'not_empty' => true,
					),
					'group'       => $addon_group,
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Bottom Offset(px)', 'porto' ),
					'param_name' => 'scroll_bottom_mode',
					'dependency' => array(
						'element'   => 'scroll_inviewport',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
			)
		);
		/* ---------------------------- */
		/* Customize Inner Row
		/* ---------------------------- */

		// add sticky options
		vc_add_params(
			'vc_row_inner',
			array(
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Wrap as Container', 'porto' ),
					'param_name'  => 'wrap_container',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Sticky Options?', 'porto' ),
					'param_name'  => 'is_sticky',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'       => $addon_group,
					'admin_label' => true,
					'qa_selector' => '>.vc_row',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Container Selector', 'porto' ),
					'param_name' => 'sticky_container_selector',
					'value'      => '.vc_row',
					'dependency' => array(
						'element'   => 'is_sticky',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
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
					'group'      => $addon_group,
				),
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
					'group'      => $addon_group,
				),
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
					'group'      => $addon_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Active Class', 'porto' ),
					'param_name' => 'sticky_active_class',
					'value'      => 'sticky-active',
					'dependency' => array(
						'element'   => 'is_sticky',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
			)
		);

		// add animation options
		vc_add_params(
			'vc_row_inner',
			array(
				$animation_type,
				$animation_duration,
				$animation_delay,
			)
		);

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
				'heading'     => __( 'Is Half Container?', 'porto' ),
				'param_name'  => 'is_half',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $section_group,
				'admin_label' => true,
				'qa_selector' => '>.vc_column_container',
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'checkbox',
				'heading'    => __( 'Is Right Aligned?', 'porto' ),
				'param_name' => 'is_half_right',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
				'dependency' => array(
					'element'   => 'is_half',
					'not_empty' => true,
				),
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'textfield',
				'heading'    => __( 'Custom CSS Class for Half container', 'porto' ),
				'param_name' => 'half_css',
				'group'      => $section_group,
				'dependency' => array(
					'element'   => 'is_half',
					'not_empty' => true,
				),
			)
		);
		vc_add_param(
			'vc_column',
			array(
				'type'       => 'porto_param_heading',
				'param_name' => 'desc_column_addon',
				'text'       => sprintf( esc_html__( 'Read this %1$sarticle%2$s to find out more about Porto Addons.', 'porto' ), '<a target="_blank" href="https://www.portotheme.com/wordpress/porto/documentation/porto-addons-for-wpbakery-column/">', '</a>' ),
				'group'      => $addon_group,
			),
		);
		vc_add_param(
			'vc_column',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Enable Sticky Options?', 'porto' ),
				'param_name'  => 'is_sticky',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $addon_group,
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
				'group'      => $addon_group,
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
				'group'      => $addon_group,
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
				'group'      => $addon_group,
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
				'group'      => $addon_group,
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
				'group'      => $addon_group,
			)
		);

		// add mouse hover split effect
		vc_add_params(
			'vc_column',
			array(
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Hover Split Layer', 'porto' ),
					'param_name'  => 'split_layer',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'description' => __( 'The Hover Split option of the parent row should be selected.', 'porto' ),
					'group'       => $addon_group,
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
				$floating_start_pos,
				$floating_speed,
				$floating_transition,
				$floating_horizontal,
				$floating_duration,
			)
		);

		vc_add_param(
			'vc_column_inner',
			array(
				'type'        => 'checkbox',
				'heading'     => __( 'Enable Sticky Options?', 'porto' ),
				'param_name'  => 'is_sticky',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $addon_group,
				'admin_label' => true,
				'qa_selector' => '.vc_column_container',
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
				'group'      => $addon_group,
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
				'group'      => $addon_group,
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
				'group'      => $addon_group,
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
				'group'      => $addon_group,
			)
		);
		vc_add_params(
			'vc_column_inner',
			array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Active Class', 'porto' ),
					'param_name' => 'sticky_active_class',
					'value'      => 'sticky-active',
					'dependency' => array(
						'element'   => 'is_sticky',
						'not_empty' => true,
					),
					'group'      => $addon_group,
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
			)
		);

		/* ---------------------------- */
		/* Customize Custom Heading
		/* ---------------------------- */
		if ( version_compare( $porto_cur_version, '6.3.0', '>=' ) ) {
			vc_remove_param( 'vc_custom_heading', 'source' );
			$param               = WPBMap::getParam( 'vc_custom_heading', 'text' );
			$param['dependency'] = array(
				'element'  => 'enable_field_dynamic',
				'is_empty' => true,
			);
			$param['weight']     = 2;
			vc_update_shortcode_param( 'vc_custom_heading', $param );
			porto_dynamic_vc_param( 'vc_custom_heading', 'field', 3 );
			porto_dynamic_vc_param( 'vc_custom_heading', 'link', 1 );
			$param               = WPBMap::getParam( 'vc_custom_heading', 'link' );
			$param['dependency'] = array(
				'element'  => 'enable_link_dynamic',
				'is_empty' => true,
			);
			vc_update_shortcode_param( 'vc_custom_heading', $param );
		}
		vc_add_params(
			'vc_custom_heading',
			array(
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
				),
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
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Skin Color', 'porto' ),
					'param_name' => 'skin',
					'std'        => 'custom',
					'value'      => porto_vc_commons( 'colors' ),
					'group'      => $section_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Letter Spacing', 'porto' ),
					'param_name' => 'letter_spacing',
					'std'        => '',
					'group'      => $section_group,
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Typewriter Effect', 'porto' ),
					'param_name'  => 'enable_typewriter',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'       => $section_group,
					'qa_selector' => '.vc_custom_heading',
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Effect By Words', 'porto' ),
					'description' => __( 'Animate the words one by one.', 'porto' ),
					'param_name'  => 'enable_typeword',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'dependency'  => array(
						'element'   => 'enable_typewriter',
						'not_empty' => true,
					),
					'group'       => $section_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Animation Name e.g: typeWriter, fadeIn and so on.', 'porto' ),
					'param_name' => 'typewriter_animation',
					'value'      => 'fadeIn',
					'dependency' => array(
						'element'   => 'enable_typewriter',
						'not_empty' => true,
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Start Delay(ms)', 'porto' ),
					'param_name' => 'typewriter_delay',
					'value'      => '',
					'dependency' => array(
						'element'   => 'enable_typewriter',
						'not_empty' => true,
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Animation Speed(ms)', 'porto' ),
					'param_name' => 'typewriter_speed',
					'std'        => '50',
					'dependency' => array(
						'element'   => 'enable_typewriter',
						'not_empty' => true,
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Please input min width that can work. (px)', 'porto' ),
					'param_name' => 'typewriter_width',
					'value'      => '',
					'dependency' => array(
						'element'   => 'enable_typewriter',
						'not_empty' => true,
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Border', 'porto' ),
					'param_name' => 'show_border',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'      => $section_group,
				),
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
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto' ),
					'param_name' => 'border_color',
					'dependency' => array(
						'element' => 'border_skin',
						'value'   => array( 'custom' ),
					),
					'group'      => $section_group,
				),
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
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Border Size', 'porto' ),
					'param_name' => 'border_size',
					'value'      => array_merge(
						porto_vc_commons( 'heading_border_size' ),
						array(
							esc_html__( 'Custom', 'porto' ) => 'custom',
						)
					),
					'dependency' => array(
						'element'   => 'show_border',
						'not_empty' => true,
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Border Size (px)', 'porto' ),
					'param_name' => 'border_size_px',
					'min'        => 1,
					'max'        => 30,
					'step'       => 1,
					'selectors'  => array(
						'{{WRAPPER}} .heading-tag:before' => 'border-top-width: {{VALUE}}px;',
						'{{WRAPPER}} .heading-tag:after'  => 'border-top-width: {{VALUE}}px;',
						'{{WRAPPER}}.heading-bottom-border .heading-tag, {{WRAPPER}}.heading-bottom-double-border .heading-tag' => 'border-bottom-width: {{VALUE}}px;',
					),
					'dependency' => array(
						'element' => 'border_size',
						'value'   => array( 'custom' ),
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Spacing Between Text & Border', 'porto' ),
					'param_name' => 'border_spacing',
					'units'      => array( 'px', 'em', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}} .heading-tag:before' => 'margin-' . $right . ': {{VALUE}}{{UNIT}};',
						'{{WRAPPER}} .heading-tag:after'  => 'margin-' . $left . ': {{VALUE}}{{UNIT}};',
						'{{WRAPPER}}.heading-bottom-border .heading-tag, {{WRAPPER}}.heading-bottom-double-border .heading-tag' => 'padding-bottom: {{VALUE}}{{UNIT}};',
					),
					'dependency' => array(
						'element'   => 'show_border',
						'not_empty' => true,
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Floating Image', 'porto' ),
					'param_name' => 'floating_img',
					'group'      => __( 'Animation', 'porto' ),
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Floating Offset', 'porto' ),
					'param_name'  => 'floating_offset',
					'description' => __( 'Control the offset from the cursor.', 'porto' ),
					'dependency'  => array(
						'element'   => 'floating_img',
						'not_empty' => true,
					),
					'group'       => __( 'Animation', 'porto' ),
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
				$floating_start_pos,
				$floating_speed,
				$floating_transition,
				$floating_horizontal,
				$floating_duration,
			)
		);
		vc_remove_param( 'vc_custom_heading', 'css_animation' );

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
		/* ---------------------------- */
		/* Customize Button
		/* ---------------------------- */
		if ( version_compare( $porto_cur_version, '6.3.0', '>=' ) ) {
			// Dynamic Field
			porto_dynamic_vc_param( 'vc_btn', 'field', 3 );
			$param               = WPBMap::getParam( 'vc_btn', 'title' );
			$param['dependency'] = array(
				'element'  => 'enable_field_dynamic',
				'is_empty' => true,
			);
			$param['weight']     = 2;
			vc_update_shortcode_param( 'vc_btn', $param );
			// Dynamic Link
			porto_dynamic_vc_param( 'vc_btn', 'link', 1 );
			$param               = WPBMap::getParam( 'vc_btn', 'link' );
			$param['dependency'] = array(
				'element'  => 'enable_link_dynamic',
				'is_empty' => true,
			);
			vc_update_shortcode_param( 'vc_btn', $param );
		}

		vc_add_params(
			'vc_btn',
			array(
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
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Contextual Classes', 'porto' ),
					'param_name' => 'contextual',
					'value'      => porto_vc_commons( 'contextual' ),
					'group'      => $section_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Font Size', 'porto' ),
					'param_name' => 'btn_fs',
					'value'      => '',
					'group'      => $section_group,
				),
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
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Letter Spacing', 'porto' ),
					'param_name' => 'btn_ls',
					'value'      => '',
					'group'      => $section_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Button Left / Right Padding', 'porto' ),
					'param_name' => 'btn_px',
					'group'      => $section_group,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Button Top / Bottom Padding', 'porto' ),
					'param_name' => 'btn_py',
					'group'      => $section_group,
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Icon Size', 'porto' ),
					'param_name' => 'btn_icon_size',
					'units'      => array( 'px', 'rem', 'em' ),
					'group'      => $section_group,
					'selectors'  => array(
						'{{WRAPPER}}.btn .vc_btn3-icon' => 'font-size: {{VALUE}}{{UNIT}};',
					),
					'dependency' => array(
						'element' => 'add_icon',
						'value'   => 'true',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Spacing between Icon and Text', 'porto' ),
					'param_name' => 'btn_icon_spacing',
					'units'      => array( 'px', 'rem', 'em' ),
					'group'      => $section_group,
					'selectors'  => array(
						'{{WRAPPER}}.btn.vc_btn3-icon-right:not(.vc_btn3-o-empty) .vc_btn3-icon' => 'padding-' . $left . ': {{VALUE}}{{UNIT}};',
						'{{WRAPPER}}.btn.vc_btn3-icon-left:not(.vc_btn3-o-empty) .vc_btn3-icon' => 'padding-' . $right . ': {{VALUE}}{{UNIT}};',
					),
					'dependency' => array(
						'element' => 'add_icon',
						'value'   => 'true',
					),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Show as Label', 'porto' ),
					'description' => __( 'Show button as general link.', 'porto' ),
					'param_name'  => 'label',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'       => $section_group,
					'dependency'  => array(
						'element'  => 'hover_text_effect',
						'is_empty' => true,
					),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Show Pointer Arrow', 'porto' ),
					'description' => __( 'Turn on to show pointer animation arrow.', 'porto' ),
					'param_name'  => 'show_arrow',
					'value'       => array( __( 'Yes', 'porto' ) => 'yes' ),
					'group'       => $section_group,
					'dependency'  => array(
						'element'  => 'hover_text_effect',
						'is_empty' => true,
					),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Is Arrow Button?', 'porto' ),
					'description' => __( 'Show arrow button instead of icon.', 'porto' ),
					'param_name'  => 'btn_arrow',
					'value'       => array( __( 'Yes', 'porto' ) => 'yes' ),
					'group'       => $section_group,
					'dependency'  => array(
						'element'            => 'add_icon',
						'value_not_equal_to' => 'true',
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Extra Class', 'porto' ),
					'description' => __( 'This class is appended to the button tag, not to its wrapper.', 'porto' ),
					'param_name'  => 'el_cls',
					'value'       => '',
					'group'       => $section_group,
				),
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
		vc_add_params( 'vc_btn', array( $animation_type, $animation_duration, $animation_delay, $floating_start_pos, $floating_speed, $floating_transition, $floating_horizontal, $floating_duration ) );

		$param = WPBMap::getParam( 'vc_btn', 'i_type' );
		$param['value'][ __( 'Porto Icon', 'porto' ) ] = 'porto';
		vc_update_shortcode_param( 'vc_btn', $param );
		vc_add_params(
			'vc_btn',
			array(
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Icon', 'porto' ),
					'param_name' => 'i_icon_porto',
					'settings'   => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'i_type',
						'value'   => 'porto',
					),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Select Hover Icon Effect', 'porto' ),
					'param_name'  => 'hover_effect',
					'value'       => array(
						__( 'No Effect', 'porto' )        => '',
						__( 'Icon Dash', 'porto' )        => 'hover-icon-dash',
						__( 'Icon Zoom', 'porto' )        => 'hover-icon-zoom',
						__( 'Icon Slide Up', 'porto' )    => 'hover-icon-up',
						__( 'Icon Slide Left', 'porto' )  => 'hover-icon-left',
						__( 'Icon Slide Right', 'porto' ) => 'hover-icon-right',
						__( 'Icon Slide Right & Left', 'porto' ) => 'hover-icon-pulse-left-right',
						__( 'Icon Slide Infinite', 'porto' ) => 'hover-icon-pulse-infnite',
					),
					'dependency'  => array(
						'element' => 'add_icon',
						'value'   => 'true',
					),
					'description' => __( 'Select the type of effct you want on hover', 'porto' ),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Select Hover Text Effect', 'porto' ),
					'param_name'  => 'hover_text_effect',
					'value'       => array(
						__( 'No Effect', 'porto' )    => '',
						__( 'Switch Left', 'porto' )  => 'hover-text-switch-left',
						__( 'Switch Up', 'porto' )    => 'hover-text-switch-up',
						__( 'Marquee Left', 'porto' ) => 'hover-text-marquee-left',
						__( 'Marquee Up', 'porto' )   => 'hover-text-marquee-up',
						__( 'Marquee Down', 'porto' ) => 'hover-text-marquee-down',
					),
					'description' => __( 'Select the type of effct you want on hover', 'porto' ),
				),
			)
		);

		$update_params = array( 'custom_onclick', 'custom_onclick_code', 'el_id', 'el_class' );
		foreach ( $update_params as $p_name ) {
			$param = WPBMap::getParam( 'vc_btn', $p_name );
			if ( ! empty( $param ) && isset( $param['param_name'] ) ) {
				vc_remove_param( 'vc_btn', $p_name );
				vc_add_param( 'vc_btn', $param );
			}
		}

		/* ---------------------------- */
		/* Add Single Image Parameters
		/* ---------------------------- */
		if ( version_compare( $porto_cur_version, '6.3.0', '>=' ) ) {
			// Dynamic Image
			//Title
			$param           = WPBMap::getParam( 'vc_single_image', 'title' );
			$param['weight'] = 3;
			vc_update_shortcode_param( 'vc_single_image', $param );
			// Image source
			$param           = WPBMap::getParam( 'vc_single_image', 'source' );
			$param['weight'] = 3;
			vc_update_shortcode_param( 'vc_single_image', $param );

			// Dynamic Switcher
			porto_dynamic_vc_param( 'vc_single_image', 'image', 2, 'source', 'media_library' );

			$param               = WPBMap::getParam( 'vc_single_image', 'image' );
			$param['dependency'] = array(
				'element'  => 'enable_image_dynamic',
				'is_empty' => true,
			);
			vc_update_shortcode_param( 'vc_single_image', $param );
		}
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
		vc_add_params(
			'vc_single_image',
			array(
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Hover Effect', 'porto' ),
					'param_name' => 'hover_effect',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'      => $section_group,
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
				$floating_start_pos,
				$floating_speed,
				$floating_transition,
				$floating_horizontal,
				$floating_duration,
			)
		);
		vc_remove_param( 'vc_single_image', 'css_animation' );

		/* ---------------------------- */
		/* Customize Progress Bar
		/* ---------------------------- */
		vc_add_params(
			'vc_progress_bar',
			array(
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'pb_style',
					'text'       => esc_html__( 'Progress Bars', 'porto' ),
					'group'      => $section_group,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Contextual Classes', 'porto' ),
					'param_name'  => 'contextual',
					'value'       => porto_vc_commons( 'contextual' ),
					'admin_label' => true,
					'group'       => $section_group,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Enable Animation', 'porto' ),
					'param_name' => 'animation',
					'std'        => 'yes',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'      => $section_group,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Border Radius', 'porto' ),
					'param_name' => 'border_radius',
					'value'      => array_merge(
						porto_vc_commons( 'progress_border_radius' ),
						array(
							__( 'Custom', 'porto' ) => 'custom',
						)
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Bar Border Radius', 'porto' ),
					'param_name' => 'bar_br',
					'selectors'  => array(
						'{{WRAPPER}} .vc_single_bar.progress' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
						'{{WRAPPER}} .vc_single_bar.progress .progress-bar' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'dependency' => array(
						'element' => 'border_radius',
						'value'   => array( 'custom' ),
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Size', 'porto' ),
					'param_name' => 'size',
					'value'      => array_merge(
						porto_vc_commons( 'progress_size' ),
						array(
							__( 'Custom', 'porto' ) => 'custom',
						)
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Bar height', 'porto' ),
					'param_name' => 'bar_h',
					'units'      => array( 'px', 'em', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}} .vc_single_bar.progress' => 'height: {{VALUE}}{{UNIT}};',
					),
					'dependency' => array(
						'element' => 'size',
						'value'   => array( 'custom' ),
					),
					'group'      => $section_group,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Start Width of Progress Bar', 'porto' ),
					'description' => 'ex: 2em or 30px, etc',
					'param_name'  => 'min_width',
					'group'       => $section_group,
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Spacing Between', 'porto' ),
					'param_name' => 'spacing',
					'units'      => array( 'px', 'em', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}} .vc_single_bar.progress' => 'margin-bottom: {{VALUE}}{{UNIT}};',
					),
					'group'      => $section_group,
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'pb_title_style',
					'text'       => esc_html__( 'Title', 'porto' ),
					'group'      => $section_group,
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Title Typography', 'porto' ),
					'param_name' => 'title_tg',
					'selectors'  => array(
						'{{WRAPPER}} .progress-label',
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Title Color', 'porto' ),
					'param_name' => 'title_clr',
					'selectors'  => array(
						'{{WRAPPER}} .progress-label' => 'color: {{VALUE}};',
					),
					'group'      => $section_group,
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'pb_percent_style',
					'text'       => esc_html__( 'Percent Text', 'porto' ),
					'group'      => $section_group,
					'dependency' => array(
						'element'   => 'units',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Percentage as Tooltip', 'porto' ),
					'param_name' => 'tooltip',
					'std'        => 'yes',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
					'group'      => $section_group,
					'dependency' => array(
						'element'   => 'units',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'porto_button_group',
					'heading'    => __( 'Percent Alignment', 'porto' ),
					'param_name' => 'percent_align',
					'value'      => array(
						'flex-start' => array(
							'title' => esc_html__( 'Left', 'porto' ),
							'icon'  => 'fas fa-align-left',
							'label' => esc_html__( 'Left', 'porto' ),
						),
						''           => array(
							'title' => esc_html__( 'Center', 'porto' ),
							'icon'  => 'fas fa-align-center',
							'label' => esc_html__( 'Center', 'porto' ),
						),
						'flex-end'   => array(
							'title' => esc_html__( 'Right', 'porto' ),
							'icon'  => 'fas fa-align-right',
							'label' => esc_html__( 'Right', 'porto' ),
						),
					),
					'std'        => '',
					'dependency' => array(
						'element'  => 'tooltip',
						'is_empty' => true,
					),
					'selectors'  => array(
						'{{WRAPPER}} .progress-bar' => 'justify-content: {{VALUE}};',
					),
					'group'      => $section_group,
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Percent Padding', 'porto' ),
					'param_name' => 'percent_pd',
					'selectors'  => array(
						'{{WRAPPER}} .progress-bar-tooltip, {{WRAPPER}} .vc_label_units' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => $section_group,
					'dependency' => array(
						'element'   => 'units',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Percent Typography', 'porto' ),
					'param_name' => 'percent_tg',
					'selectors'  => array(
						'{{WRAPPER}} .vc_label_units',
					),
					'group'      => $section_group,
					'dependency' => array(
						'element'   => 'units',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Percent Color', 'porto' ),
					'param_name' => 'percent_clr',
					'selectors'  => array(
						'{{WRAPPER}} .vc_label_units' => 'color: {{VALUE}};',
					),
					'group'      => $section_group,
					'dependency' => array(
						'element'   => 'units',
						'not_empty' => true,
					),
				),
			)
		);

		// move Design Options tab to the end
		$css_param = WPBMap::getParam( 'vc_progress_bar', 'css' );
		vc_remove_param( 'vc_progress_bar', 'css' );
		vc_add_param( 'vc_progress_bar', $css_param );

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

		vc_add_params(
			'vc_pie',
			array(
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Type', 'porto' ),
					'param_name'  => 'type',
					'std'         => 'custom',
					'value'       => array(
						__( 'Porto Circular Bar', 'porto' ) => 'custom',
						__( 'VC Pie Chart', 'porto' ) => 'default',
					),
					'description' => __( 'Select pie chart type.', 'porto' ),
					'admin_label' => true,
					'group'       => $section_group,
				),
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
				),
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
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Select FontAwesome Icon', 'porto' ),
					'param_name' => 'icon',
					'value'      => 'fas fa-star',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'only-icon' ),
					),
					'group'      => $section_group,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'View Size', 'porto' ),
					'description' => __( 'Instead of this, you would better use options in style tab.', 'porto' ),
					'param_name'  => 'view_size',
					'dependency'  => array(
						'element'            => 'view',
						'value_not_equal_to' => 'only-icon',
					),
					'value'       => porto_vc_commons( 'circular_view_size' ),
					'group'       => $section_group,
				),
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
				),
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
				),
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
				),
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
				),
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
				),
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
				),
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
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'heading_title',
					'text'       => __( 'Title Style', 'porto' ),
					'dependency' => array(
						'element'            => 'view',
						'value_not_equal_to' => 'only-icon',
					),
					'group'      => __( 'Style', 'porto' ),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Title Typography', 'porto' ),
					'description' => __( 'Controls the typography of the title.', 'porto' ),
					'param_name'  => 'title_porto_typography',
					'selectors'   => array(
						'{{WRAPPER}} strong',
					),
					'group'       => __( 'Style', 'porto' ),
					'dependency'  => array(
						'element'            => 'view',
						'value_not_equal_to' => 'only-icon',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Title Color', 'porto' ),
					'param_name' => 'title_color',
					'selectors'  => array(
						'{{WRAPPER}} strong' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto' ),
					'dependency' => array(
						'element'            => 'view',
						'value_not_equal_to' => 'only-icon',
					),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Top Position', 'porto' ),
					'description' => __( 'Controls the top position of title.', 'porto' ),
					'param_name'  => 'title_pos',
					'units'       => array( '%', 'px' ),
					'group'       => __( 'Style', 'porto' ),
					'selectors'   => array(
						'{{WRAPPER}}.circular-bar strong' => 'top: {{VALUE}}{{UNIT}};',
					),
					'dependency'  => array(
						'element'            => 'view',
						'value_not_equal_to' => 'only-icon',
					),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'heading_value',
					'text'       => __( 'Value Style', 'porto' ),
					'dependency' => array(
						'element'  => 'view',
						'is_empty' => true,
					),
					'group'      => __( 'Style', 'porto' ),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Value Typography', 'porto' ),
					'description' => __( 'Controls the typography of the value.', 'porto' ),
					'param_name'  => 'value_porto_typography',
					'selectors'   => array(
						'{{WRAPPER}} label',
					),
					'group'       => __( 'Style', 'porto' ),
					'dependency'  => array(
						'element'  => 'view',
						'is_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Value Color', 'porto' ),
					'param_name' => 'value_color',
					'selectors'  => array(
						'{{WRAPPER}} label' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto' ),
					'dependency' => array(
						'element'  => 'view',
						'is_empty' => true,
					),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Value Position', 'porto' ),
					'description' => __( 'Controls the top position of value.', 'porto' ),
					'param_name'  => 'value_pos',
					'units'       => array( '%', 'px' ),
					'group'       => __( 'Style', 'porto' ),
					'selectors'   => array(
						'{{WRAPPER}} label' => 'top: {{VALUE}}{{UNIT}};',
					),
					'dependency'  => array(
						'element'  => 'view',
						'is_empty' => true,
					),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'heading_icon',
					'text'       => __( 'Icon Style', 'porto' ),
					'dependency' => array(
						'element' => 'view',
						'value'   => 'only-icon',
					),
					'group'      => __( 'Style', 'porto' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Icon Size', 'porto' ),
					'description' => __( 'Controls the size of the icon.', 'porto' ),
					'param_name'  => 'icon_size',
					'units'       => array( 'px' ),
					'group'       => __( 'Style', 'porto' ),
					'selectors'   => array(
						'{{WRAPPER}}.only-icon i' => 'font-size: {{VALUE}}{{UNIT}};',
					),
					'dependency'  => array(
						'element' => 'view',
						'value'   => 'only-icon',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Color', 'porto' ),
					'param_name' => 'icon_color',
					'selectors'  => array(
						'{{WRAPPER}} i' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto' ),
					'dependency' => array(
						'element' => 'view',
						'value'   => 'only-icon',
					),
				),
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
function porto_dynamic_vc_param( $widget, $dynamic_type, $weight, $dependency = '', $flag = '' ) {
	if ( ! class_exists( 'Porto_Wpb_Dynamic_Tags' ) ) {
		return;
	}
	$params = Porto_Wpb_Dynamic_Tags::get_instance()->dynamic_wpb_tags( $dynamic_type );
	foreach ( $params as $key => $value ) {
		$value['weight'] = $weight;
		if ( 0 == $key && 'image' == $dynamic_type ) {
			$value['dependency'] = array(
				'element' => $dependency,
				'value'   => array( $flag ),
			);
		}
		vc_add_param( $widget, $value );
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

/**
 * Update column offset template path to add xxl settings (> 1400px) to column
 *
 * @since 6.3.0
 */
add_filter( 'vc_path_filter', 'porto_vc_path_filter' );
if ( ! function_exists( 'porto_vc_path_filter' ) ) :
	function porto_vc_path_filter( $path ) {
		if ( false !== strpos( $path, 'params/column_offset/template.tpl.php' ) ) {
			$path = PORTO_DIR . '/vc_templates/params/column_offset/template.tpl.php';
		}
		return $path;
	}
endif;
