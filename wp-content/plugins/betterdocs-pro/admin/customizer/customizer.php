<?php 
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * BetterDocs Pro Feature for Customizer
 *
 * @package BetterDocs
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */

/**
 * Check for WP_Customizer_Control existence before adding custom control because WP_Customize_Control
 * is loaded on customizer page only
 *
 * @see _wp_customize_include()
 */

function betterdocs_customize_register_pro( $wp_customize ) {
	$defaults = betterdocs_get_option_defaults_pro();

	// Load custom controls
	require_once( BETTERDOCS_ADMIN_DIR_PATH . 'customizer/controls.php' );
	require_once( BETTERDOCS_ADMIN_DIR_PATH . 'customizer/sanitize.php' );

	if ( BetterDocs_Multiple_Kb::$enable == 1 ) {
		
		$wp_customize->add_section( 'betterdocs_mkb_settings' , array(
			'title'      => __('Multiple KB','betterdocs'),
			'priority'   => 99
		) );

		// Multiple KB layout select 

		$wp_customize->add_setting( 'betterdocs_multikb_layout_select' , array(
			'default'     => $defaults['betterdocs_multikb_layout_select'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_select',
		) );

		$wp_customize->add_control(
			new BetterDocs_Radio_Image_Control (
			$wp_customize,
			'betterdocs_multikb_layout_select',
				array(
					'type'     => 'betterdocs-radio-image',
					'settings'		=> 'betterdocs_multikb_layout_select',
					'section'		=> 'betterdocs_mkb_settings',
					'label'			=> __( 'Select Multiple KB Layout', 'theme-slug' ),
					'priority' => 1,
					'choices'		=> array(
						'layout-1' 	=> array(
							'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-2.png',
						),
						'layout-2' 	=> array(
							'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-3.png',
						),
						
					)
				)
			)
		);

		// Content Area Background Color

		$wp_customize->add_setting( 'betterdocs_mkb_background_color' , array(
			'default'     => $defaults['betterdocs_mkb_background_color'],
			'capability'    => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_background_color',
			array(
				'label'      => __( 'Content Area Background Color', 'betterdocs' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_background_color',
				'priority' => 2,
			) )
		);

		// Content Area background image
		
		$wp_customize->add_setting( 'betterdocs_mkb_background_image', array(
			'default'       => $defaults['betterdocs_mkb_background_image'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage'

		) );

		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize, 'betterdocs_mkb_background_image', array(
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_background_image',
			'label'    => __( 'Background Image', 'betterdocs' ),
			'priority' => 3
		) ) );

		// Background property

		$wp_customize->add_setting( 'betterdocs_mkb_background_property', array(
			'default'       => $defaults['betterdocs_mkb_background_property'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_select'

		) );

		$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
			$wp_customize, 'betterdocs_mkb_background_property', array(
			'type'     => 'betterdocs-title',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_background_property',
			'label'    => __( 'Background Property', 'betterdocs' ),
			'priority' => 4,
			'input_attrs' => array(
				'id' => 'betterdocs_mkb_background_property',
				'class' => 'betterdocs-select',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_background_size', array(
			'default'       => $defaults['betterdocs_mkb_background_size'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_select'

		) );

		$wp_customize->add_control( new BetterDocs_Select_Control(
			$wp_customize, 'betterdocs_mkb_background_size', array(
			'type'     => 'betterdocs-select',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_background_size',
			'label'    => __( 'Size', 'betterdocs' ),
			'priority' => 5,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_background_property betterdocs-select',
			),
			'choices'  => array(
				'auto'   	=> __( 'auto', 'betterdocs' ),
				'length'   	=> __( 'length', 'betterdocs' ),
				'cover'   	=> __( 'cover', 'betterdocs' ),
				'contain'   => __( 'contain', 'betterdocs' ),
				'initial'   => __( 'initial', 'betterdocs' ),
				'inherit'   => __( 'inherit', 'betterdocs' )
			)
		) ) );
		
		$wp_customize->add_setting( 'betterdocs_mkb_background_repeat', array(
			'default'       => $defaults['betterdocs_mkb_background_repeat'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_select'

		) );

		$wp_customize->add_control( new BetterDocs_Select_Control(
			$wp_customize, 'betterdocs_mkb_background_repeat', array(
			'type'     => 'betterdocs-select',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_background_repeat',
			'label'    => __( 'Repeat', 'betterdocs' ),
			'priority' => 6,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_background_property betterdocs-select',
			),
			'choices'  => array(
				'no-repeat' => __( 'no-repeat', 'betterdocs' ),
				'initial'   => __( 'initial', 'betterdocs' ),
				'inherit'   => __( 'inherit', 'betterdocs' ),
				'repeat'   	=> __( 'repeat', 'betterdocs' ),
				'repeat-x'  => __( 'repeat-x', 'betterdocs' ),
				'repeat-y'  => __( 'repeat-y', 'betterdocs' )
			)
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_background_attachment', array(
			'default'       => $defaults['betterdocs_mkb_background_attachment'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_select'

		) );

		$wp_customize->add_control( new BetterDocs_Select_Control(
			$wp_customize, 'betterdocs_mkb_background_attachment', array(
			'type'     => 'betterdocs-select',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_background_attachment',
			'label'    => __( 'Attachment', 'betterdocs' ),
			'priority' => 7,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_background_property betterdocs-select',
			),
			'choices'  => array(
				'initial' 	=> __( 'initial', 'betterdocs' ),
				'inherit'   => __( 'inherit', 'betterdocs' ),
				'scroll'   	=> __( 'scroll', 'betterdocs' ),
				'fixed'  	 => __( 'fixed', 'betterdocs' ),
				'local'  	=> __( 'local', 'betterdocs' ),
			)
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_background_position', array(
			'default'       => $defaults['betterdocs_mkb_background_position'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'esc_html'

		) );

		$wp_customize->add_control( new BetterDocs_Select_Control(
			$wp_customize, 'betterdocs_mkb_background_position', array(
			'type'     => 'betterdocs-select',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_background_position',
			'label'    => __( 'Position', 'betterdocs' ),
			'priority' => 8,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_background_property betterdocs-select',
			),
			'choices'  => array(
				'left top'   	=> __( 'left top', 'betterdocs' ),
				'left center'  => __( 'left center', 'betterdocs' ),
				'left bottom'  => __( 'left bottom', 'betterdocs' ),
				'right top' => __( 'right top', 'betterdocs' ),
				'right center'   => __( 'right center', 'betterdocs' ),
				'right bottom'   => __( 'right bottom', 'betterdocs' ),
				'center top'   => __( 'center top', 'betterdocs' ),
				'center center'   => __( 'center center', 'betterdocs' ),
				'center bottom'   => __( 'center bottom', 'betterdocs' )
			)
		) ) );

		// Content Area Padding

		$wp_customize->add_setting( 'betterdocs_mkb_content_padding', array(
			'default'       => $defaults['betterdocs_mkb_content_padding'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
			$wp_customize, 'betterdocs_mkb_content_padding', array(
			'type'     => 'betterdocs-title',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_content_padding',
			'label'    => __( 'Content Area Padding', 'betterdocs' ),
			'priority' => 9,
			'input_attrs' => array(
				'id' => 'betterdocs-doc-page-content-padding',
				'class' => 'betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_content_padding_top',
			apply_filters('betterdocs_mkb_content_padding_top', array(
				'default'       => $defaults['betterdocs_mkb_content_padding_top'],
				'capability'    => 'edit_theme_options',
				'sanitize_callback' => 'betterdocs_sanitize_integer'
			) )
		);

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_content_padding_top', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_content_padding_top',
			'label'    => __( 'Top', 'betterdocs' ),
			'priority' => 10,
			'input_attrs' => array(
				'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension',
			),
		) ) );
		
		$wp_customize->add_setting( 'betterdocs_mkb_content_padding_right', 
			apply_filters('betterdocs_mkb_content_padding_right', array(
				'default'       => $defaults['betterdocs_mkb_content_padding_right'],
				'capability'    => 'edit_theme_options',
				'sanitize_callback' => 'betterdocs_sanitize_integer'
			) )
		);

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_content_padding_right', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_content_padding_right',
			'label'    => __( 'Right', 'betterdocs' ),
			'priority' => 11,
			'input_attrs' => array(
				'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_content_padding_bottom', 
			apply_filters('betterdocs_mkb_content_padding_bottom', array(
				'default'       => $defaults['betterdocs_mkb_content_padding_bottom'],
				'capability'    => 'edit_theme_options',
				'sanitize_callback' => 'betterdocs_sanitize_integer'
			) )
		);

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_content_padding_bottom', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_content_padding_bottom',
			'label'    => __( 'Bottom', 'betterdocs' ),
			'priority' => 12,
			'input_attrs' => array(
				'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_content_padding_left', 
			apply_filters('betterdocs_mkb_content_padding_left', array(
				'default'       => $defaults['betterdocs_mkb_content_padding_left'],
				'capability'    => 'edit_theme_options',
				'sanitize_callback' => 'betterdocs_sanitize_integer'
			) )
		);

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_content_padding_left', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_content_padding_left',
			'label'    => __( 'Left', 'betterdocs' ),
			'priority' => 13,
			'input_attrs' => array(
				'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension',
			),
		) ) );

		// Content Area Width

		$wp_customize->add_setting( 'betterdocs_mkb_content_width', 
			apply_filters('betterdocs_mkb_content_width', array(
				'default'       => $defaults['betterdocs_mkb_content_width'],
				'capability'    => 'edit_theme_options',
				'sanitize_callback' => 'betterdocs_sanitize_integer'

			) )
		);

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_mkb_content_width', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_content_width',
			'label'    => __( 'Content Area Width', 'betterdocs' ),
			'priority' => 14,
			'input_attrs' => array(
				'class' => 'betterdocs-range-value',
				'min'    => 0,
				'max'    => 100,
				'step'   => 1,
				'suffix' => '%', //optional suffix
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_content_max_width', 
			apply_filters('betterdocs_mkb_content_max_width', array(
				'default'       => $defaults['betterdocs_mkb_content_max_width'],
				'capability'    => 'edit_theme_options',
				'sanitize_callback' => 'betterdocs_sanitize_integer'
			) )
		);

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_mkb_content_max_width', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_content_max_width',
			'label'    => __( 'Content Area Maximum Width', 'betterdocs' ),
			'priority' => 15,
			'input_attrs' => array(
				'class' => 'betterdocs-range-value',
				'min'    => 100,
				'max'    => 1600,
				'step'   => 1,
				'suffix' => 'px', //optional suffix
			),
		) ) );


		// Spacing Between Columns

		$wp_customize->add_setting( 'betterdocs_mkb_column_space', 
			apply_filters('betterdocs_mkb_column_space', array(
				'default'       => $defaults['betterdocs_mkb_column_space'],
				'capability'    => 'edit_theme_options',
				'sanitize_callback' => 'betterdocs_sanitize_integer'
			) )
		);

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_mkb_column_space', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_space',
			'label'    => __( 'Spacing Between Columns', 'betterdocs' ),
			'priority' => 17,
			'input_attrs' => array(
				'class' => 'betterdocs-range-value',
				'min'    => 0,
				'max'    => 100,
				'step'   => 1,
				'suffix' => 'px', //optional suffix
			),
		) ) );

		// Column Background Color Layout 2

		$wp_customize->add_setting( 'betterdocs_mkb_column_bg_color2' , array(
			'default'     => $defaults['betterdocs_mkb_column_bg_color2'],
			'capability'    => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_column_bg_color2',
			array(
				'label'      => __( 'Column Background Color', 'betterdocs' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_column_bg_color2',
				'priority' => 18
			) )
		);

		// Column Hover Background Color

		$wp_customize->add_setting( 'betterdocs_mkb_column_hover_bg_color' , array(
			'default'     => $defaults['betterdocs_mkb_column_hover_bg_color'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_column_hover_bg_color',
			array(
				'label'      => __( 'Column Hover Background Color', 'betterdocs' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_column_hover_bg_color',
				'priority' => 18
			) )
		);

		// Column Padding

		$wp_customize->add_setting( 'betterdocs_mkb_column_padding', array(
			'default'       => $defaults['betterdocs_mkb_column_padding'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
			$wp_customize, 'betterdocs_mkb_column_padding', array(
			'type'     => 'betterdocs-title',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_padding',
			'label'    => __( 'Column Padding', 'betterdocs' ),
			'priority' => 18,
			'input_attrs' => array(
				'id' => 'betterdocs_mkb_column_padding',
				'class' => 'betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_padding_top', array(
			'default'       => $defaults['betterdocs_mkb_column_padding_top'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_padding_top', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_padding_top',
			'label'    => __( 'Top', 'betterdocs' ),
			'priority' => 19,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_padding betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_padding_right', array(
			'default'       => $defaults['betterdocs_mkb_column_padding_right'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_padding_right', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_padding_right',
			'label'    => __( 'Right', 'betterdocs' ),
			'priority' => 20,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_padding betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_padding_bottom', array(
			'default'       => $defaults['betterdocs_mkb_column_padding_bottom'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_padding_bottom', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_padding_bottom',
			'label'    => __( 'Bottom', 'betterdocs' ),
			'priority' => 21,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_padding betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_padding_left', array(
			'default'       => $defaults['betterdocs_mkb_column_padding_left'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_padding_left', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_padding_left',
			'label'    => __( 'Left', 'betterdocs' ),
			'priority' => 22,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_padding betterdocs-dimension',
			),
		) ) );

		// Icon Size

		$wp_customize->add_setting( 'betterdocs_mkb_cat_icon_size', array(
			'default'       => $defaults['betterdocs_mkb_cat_icon_size'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_mkb_cat_icon_size', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_cat_icon_size',
			'label'    => __( 'Icon Size', 'betterdocs' ),
			'priority' => 24,
			'input_attrs' => array(
				'class' => '',
				'min'    => 0,
				'max'    => 200,
				'step'   => 1,
				'suffix' => 'px', //optional suffix
			),
		) ) );

		// column border radius 

		$wp_customize->add_setting( 'betterdocs_mkb_column_borderr', array(
			'default'       => $defaults['betterdocs_mkb_column_borderr'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
			$wp_customize, 'betterdocs_mkb_column_borderr', array(
			'type'     => 'betterdocs-title',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_borderr',
			'label'    => __( 'Column Border Radius', 'betterdocs' ),
			'priority' => 24,
			'input_attrs' => array(
				'id' => 'betterdocs_mkb_column_borderr',
				'class' => 'betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_borderr_topleft', array(
			'default'       => $defaults['betterdocs_mkb_column_borderr_topleft'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_borderr_topleft', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_borderr_topleft',
			'label'    => __( 'Top Left', 'betterdocs' ),
			'priority' => 24,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_borderr betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_borderr_topright', array(
			'default'       => $defaults['betterdocs_mkb_column_borderr_topright'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_borderr_topright', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_borderr_topright',
			'label'    => __( 'Top Right', 'betterdocs' ),
			'priority' => 24,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_borderr betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_borderr_bottomright', array(
			'default'       => $defaults['betterdocs_mkb_column_borderr_bottomright'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_borderr_bottomright', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_borderr_bottomright',
			'label'    => __( 'Bottom Right', 'betterdocs' ),
			'priority' => 24,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_borderr betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_borderr_bottomleft', array(
			'default'       => $defaults['betterdocs_mkb_column_borderr_bottomleft'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_borderr_bottomleft', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_borderr_bottomleft',
			'label'    => __( 'Bottom Left', 'betterdocs' ),
			'priority' => 24,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_borderr betterdocs-dimension',
			),
		) ) );

		// Title Font Size

		$wp_customize->add_setting( 'betterdocs_mkb_cat_title_font_size', array(
			'default'       => $defaults['betterdocs_mkb_cat_title_font_size'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_mkb_cat_title_font_size', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_cat_title_font_size',
			'label'    => __( 'Title Font Size', 'betterdocs' ),
			'priority' => 25,
			'input_attrs' => array(
				'class' => '',
				'min'    => 0,
				'max'    => 100,
				'step'   => 1,
				'suffix' => 'px', //optional suffix
			),
		) ) );
		
		$wp_customize->add_setting( 'betterdocs_mkb_cat_title_color' , array(
			'default'     => $defaults['betterdocs_mkb_cat_title_color'],
			'capability'    => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_cat_title_color',
			array(
				'label'      => __( 'Title Color', 'betterdocs' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_cat_title_color',
				'priority' => 26
			) )
		);

		$wp_customize->add_setting( 'betterdocs_mkb_cat_title_hover_color' , array(
			'default'     => $defaults['betterdocs_mkb_cat_title_hover_color'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_cat_title_hover_color',
			array(
				'label'      => __( 'Title Hover Color', 'betterdocs' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_cat_title_hover_color',
				'priority' => 26
			) )
		);

		$wp_customize->add_setting( 'betterdocs_mkb_item_count_color' , array(
			'default'     => $defaults['betterdocs_mkb_item_count_color'],
			'capability'    => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );
	
		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_item_count_color',
			array(
				'label'      => __( 'Item Count Color', 'betterdocs' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_item_count_color',
				'priority' => 29
			) )
		);

		// Item Count Font Size

		$wp_customize->add_setting( 'betterdocs_mkb_item_count_font_size', array(
			'default'       => $defaults['betterdocs_mkb_item_count_font_size'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_mkb_item_count_font_size', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_item_count_font_size',
			'label'    => __( 'Font Size', 'betterdocs' ),
			'priority' => 30,
			'input_attrs' => array(
				'class'  => '',
				'min'    => 0,
				'max'    => 50,
				'step'   => 1,
				'suffix'  => 'px', //optional suffix
			),
		) ) );

		// Content Space Between

		$wp_customize->add_setting( 'betterdocs_mkb_column_content_space', array(
			'default'       => $defaults['betterdocs_mkb_column_content_space'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
			$wp_customize, 'betterdocs_mkb_column_content_space', array(
			'type'     => 'betterdocs-title',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_content_space',
			'label'    => __( 'Content Space Between', 'betterdocs' ),
			'priority' => 33,
			'input_attrs' => array(
				'id' => 'betterdocs_mkb_column_content_space',
				'class' => 'betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_content_space_image', array(
			'default'       => $defaults['betterdocs_mkb_column_content_space_image'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_content_space_image', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_content_space_image',
			'label'    => __( 'Icon', 'betterdocs' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_content_space betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_content_space_title', array(
			'default'       => $defaults['betterdocs_mkb_column_content_space_title'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_content_space_title', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_content_space_title',
			'label'    => __( 'Title', 'betterdocs' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_content_space betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_content_space_desc', array(
			'default'       => $defaults['betterdocs_mkb_column_content_space_desc'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_content_space_desc', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_content_space_desc',
			'label'    => __( 'Description', 'betterdocs' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_content_space betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_content_space_counter', array(
			'default'       => $defaults['betterdocs_mkb_column_content_space_counter'],
			'capability'    => 'edit_theme_options',
			'transport' => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_content_space_counter', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_content_space_counter',
			'label'    => __( 'Counter', 'betterdocs' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_content_space betterdocs-dimension',
			),
		) ) );

	}

	// Title Font Size

	$wp_customize->add_setting( 'betterdocs_doc_page_content_overlap', array(
		'default'       => $defaults['betterdocs_doc_page_content_overlap'],
		'capability'    => 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_content_overlap', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_content_overlap',
		'label'    => __( 'Content Overlap', 'betterdocs-pro' ),
		'priority' => 16,
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 500,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Icon Size

	$wp_customize->add_setting( 'betterdocs_doc_page_cat_icon_size_l_3_4', array(
		'default'       => $defaults['betterdocs_doc_page_cat_icon_size_l_3_4'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_cat_icon_size_l_3_4', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_cat_icon_size_l_3_4',
		'label'    => __( 'Top Box Icon Size', 'betterdocs-pro' ),
		'priority' => 24,
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Title Font Size

	$wp_customize->add_setting( 'betterdocs_doc_page_cat_title_font_size2', array(
		'default'       => $defaults['betterdocs_doc_page_cat_title_font_size2'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_cat_title_font_size2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_cat_title_font_size2',
		'label'    => __( 'Docs List Title Font Size', 'betterdocs-pro' ),
		'priority' => 34,
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Reactions Separator

    $wp_customize->add_setting('betterdocs_reactions_title', array(
        'default' => '',
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_reactions_title', array(
		'label' => __('Reactions', 'betterdocs-pro'),
		'priority'   => 159,
        'settings' => 'betterdocs_reactions_title',
        'section' => 'betterdocs_single_docs_settings',
	)));

	// Post Reactions

	$wp_customize->add_setting('betterdocs_post_reactions', array(
        'default' => $defaults['betterdocs_post_reactions'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_checkbox',
    ));

    $wp_customize->add_control(new BetterDocs_Customizer_Toggle_Control(
		$wp_customize, 'betterdocs_post_reactions', array(
		'label' => esc_html__('Enable Reactions?', 'betterdocs-pro'),
		'priority'   => 160,
        'section' => 'betterdocs_single_docs_settings',
        'settings' => 'betterdocs_post_reactions',
        'type' => 'light', // light, ios, flat
    )));

    $wp_customize->add_setting('betterdocs_post_reactions_text', array(
		'default' => $defaults['betterdocs_post_reactions_text'],
		'capability'    => 'edit_theme_options',
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_post_reactions_text',
            array(
				'label' => __('Reactions Title', 'betterdocs-pro'),
				'priority'   => 161,
                'section' => 'betterdocs_single_docs_settings',
                'settings' => 'betterdocs_post_reactions_text',
                'type' => 'text',
            )
        )
	);
	
	// Reactions Text Color

	$wp_customize->add_setting( 'betterdocs_post_reactions_text_color' , array(
		'default'     => $defaults['betterdocs_post_reactions_text_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_post_reactions_text_color',
		array(
			'label'      => __( 'Reactions Text Color', 'betterdocs-pro' ),
			'priority'   => 162,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_post_reactions_text_color',
		) )
	);

	// Reactions Icon Color

	$wp_customize->add_setting( 'betterdocs_post_reactions_icon_color' , array(
		'default'     => $defaults['betterdocs_post_reactions_icon_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_post_reactions_icon_color',
		array(
			'label'      => __( 'Reactions Icon Color', 'betterdocs-pro' ),
			'priority'   => 163,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_post_reactions_icon_color',
		) )
	);

	if ( BetterDocs_Multiple_Kb::$enable == 1 ) {
		// Assign sections to panels
		$wp_customize->get_section('betterdocs_mkb_settings')->panel = 'betterdocs_customize_options';	
	}

}
add_action( 'customize_register', 'betterdocs_customize_register_pro' );

require_once( BETTERDOCS_PRO_ADMIN_DIR_PATH . 'customizer/output-css.php' );
