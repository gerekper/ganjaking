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
		/* Customize Section
		/* ---------------------------- */
		vc_add_params(
			'vc_section',
			array(
				array(
					'type'       => 'checkbox',
					'heading'    => esc_html__( 'Wrap as container', 'porto' ),
					'param_name' => 'is_container',
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
				'heading'     => __( 'Wrap as Container', 'porto' ),
				'param_name'  => 'wrap_container',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'admin_label' => true,
			)
		);
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
				'heading'     => __( 'Is Half Container?', 'porto' ),
				'param_name'  => 'is_half',
				'value'       => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'       => $section_group,
				'admin_label' => true,
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
				'type'        => 'textfield',
				'heading'     => __( 'Custom CSS Class for Half container', 'porto' ),
				'param_name'  => 'half_css',
				'group'       => $section_group,
				'dependency'  => array(
					'element'   => 'is_half',
					'not_empty' => true,
				),
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
				'heading'    => __( 'Enable typewriter effect', 'porto' ),
				'param_name' => 'enable_typewriter',
				'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				'group'      => $section_group,
			)
		);
		vc_add_param(
			'vc_custom_heading',
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
			)
		);
		vc_add_param(
			'vc_custom_heading',
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
			)
		);
		vc_add_param(
			'vc_custom_heading',
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
				'type'       => 'porto_number',
				'heading'    => __( 'Icon Size', 'porto-functionality' ),
				'param_name' => 'btn_icon_size',
				'units'      => array( 'px', 'rem', 'em' ),
				'group'      => $section_group,
				'selectors'  => array(
					'{{WRAPPER}}.btn .vc_btn3-icon' => 'font-size: {{VALUE}}{{UNIT}};',
				),
			)
		);
		$left  = is_rtl() ? 'right' : 'left';
		$right = is_rtl() ? 'left' : 'right';
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'porto_number',
				'heading'    => __( 'Spacing between Icon and Text', 'porto-functionality' ),
				'param_name' => 'btn_icon_spacing',
				'units'      => array( 'px', 'rem', 'em' ),
				'group'      => $section_group,
				'selectors'  => array(
					'{{WRAPPER}}.vc_btn3-icon-right:not(.vc_btn3-o-empty) .vc_btn3-icon' => 'padding-' . $left . ': {{VALUE}}{{UNIT}};',
					'{{WRAPPER}}.vc_btn3-icon-left:not(.vc_btn3-o-empty) .vc_btn3-icon' => 'padding-' . $right . ': {{VALUE}}{{UNIT}};',
				),
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

		$param = WPBMap::getParam( 'vc_btn', 'i_type' );
		$param['value'][ __( 'Porto Icon', 'porto' ) ] = 'porto';
		vc_update_shortcode_param( 'vc_btn', $param );
		vc_add_param(
			'vc_btn',
			array(
				'type'       => 'iconpicker',
				'heading'    => __( 'Icon', 'porto-functionality' ),
				'param_name' => 'i_icon_porto',
				'settings'   => array(
					'type'         => 'porto',
					'iconsPerPage' => 4000,
				),
				'dependency' => array(
					'element' => 'i_type',
					'value'   => 'porto',
				),
			)
		);
		vc_add_param(
			'vc_btn',
			array(
				'type'        => 'dropdown',
				'class'       => '',
				'heading'     => __( 'Select Hover Effect type', 'porto-functionality' ),
				'param_name'  => 'hover_effect',
				'value'       => array(
					__( 'No Effect', 'porto-functionality' ) => '',
					__( 'Icon Zoom', 'porto-functionality' ) => 'hover-icon-zoom',
					__( 'Icon Slide Up', 'porto-functionality' ) => 'hover-icon-up',
					__( 'Icon Slide Left', 'porto-functionality' ) => 'hover-icon-left',
					__( 'Icon Slide Right', 'porto-functionality' ) => 'hover-icon-right',
				),
				'dependency'  => array(
					'element' => 'add_icon',
					'value'   => 'true',
				),
				'description' => __( 'Select the type of effct you want on hover', 'porto-functionality' ),
			)
		);
		$update_params = array( 'el_id', 'el_class', 'custom_onclick', 'custom_onclick_code' );
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
