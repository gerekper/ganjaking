<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * BetterDocs Theme Customizer
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


function betterdocs_customize_register( $wp_customize ) {

	// Get default customizer values
	$defaults = betterdocs_get_option_defaults();

	// Load custom controls
	require_once( BETTERDOCS_ADMIN_DIR_PATH . 'customizer/controls.php' );
	require_once( BETTERDOCS_ADMIN_DIR_PATH . 'customizer/sanitize.php' );


	// Docs page Settings

	$wp_customize->add_section( 'betterdocs_doc_page_settings' , array(
		'title'      => __('Docs Page','betterdocs'),
		'priority'   => 100
	) );

	$wp_customize->add_setting( 'betterdocs_docs_layout_select' , array(
		'default'     => $defaults['betterdocs_docs_layout_select'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_select',
	) );

	$wp_customize->add_control(
		new BetterDocs_Radio_Image_Control(
		$wp_customize,
		'betterdocs_docs_layout_select',
		array(
			'type'     => 'betterdocs-radio-image',
			'settings'		=> 'betterdocs_docs_layout_select',
			'section'		=> 'betterdocs_doc_page_settings',
			'label'			=> __( 'Select Category Layout', 'theme-slug' ),
			'priority' => 2,
			'choices'		=> apply_filters('betterdocs_docs_layout_select_choices', array(
				'layout-1' 	=> array(
					'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-1.png',
				),
				'layout-2' 	=> array(
					'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-2.png',
				),
				'layout-3' 	=> array(
					'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-3.png',
					'pro' => true,
					'url' => 'https://betterdocs.co/upgrade',
				),
				'layout-4' 	=> array(
					'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-4.png',
					'pro' => true,
					'url' => 'https://betterdocs.co/upgrade',
				),
			))
		) )
	);

	// Content Area Background Color

	$wp_customize->add_setting( 'betterdocs_doc_page_background_color' , array(
		'default'     => $defaults['betterdocs_doc_page_background_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_background_color',
		array(
			'label'      => __( 'Content Area Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_background_color',
			'priority' => 2,
		) )
	);

	// Content Area background image
	
	$wp_customize->add_setting( 'betterdocs_doc_page_background_image', array(
		'default'       => $defaults['betterdocs_doc_page_background_image'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage'

	) );

	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize, 'betterdocs_doc_page_background_image', array(
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_background_image',
		'label'    => __( 'Backgroud Image', 'betterdocs' ),
		'priority' => 3
	) ) );

	// Background property

	$wp_customize->add_setting( 'betterdocs_doc_page_background_property', array(
		'default'       => $defaults['betterdocs_doc_page_background_property'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_background_property', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_background_property',
		'label'    => __( 'Background Property', 'betterdocs' ),
		'priority' => 4,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_page_background_property',
			'class' => 'betterdocs-select',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_background_size', array(
		'default'       => $defaults['betterdocs_doc_page_background_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_doc_page_background_size', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_background_size',
		'label'    => __( 'Size', 'betterdocs' ),
		'priority' => 5,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_background_property betterdocs-select',
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
	
	$wp_customize->add_setting( 'betterdocs_doc_page_background_repeat', array(
		'default'       => $defaults['betterdocs_doc_page_background_repeat'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_doc_page_background_repeat', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_background_repeat',
		'label'    => __( 'Repeat', 'betterdocs' ),
		'priority' => 6,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_background_property betterdocs-select',
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

	$wp_customize->add_setting( 'betterdocs_doc_page_background_attachment', array(
		'default'       => $defaults['betterdocs_doc_page_background_attachment'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_doc_page_background_attachment', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_background_attachment',
		'label'    => __( 'Attachment', 'betterdocs' ),
		'priority' => 7,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_background_property betterdocs-select',
		),
		'choices'  => array(
			'initial' 	=> __( 'initial', 'betterdocs' ),
			'inherit'   => __( 'inherit', 'betterdocs' ),
			'scroll'   	=> __( 'scroll', 'betterdocs' ),
			'fixed'  	 => __( 'fixed', 'betterdocs' ),
			'local'  	=> __( 'local', 'betterdocs' ),
		)
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_background_position', array(
		'default'       => $defaults['betterdocs_doc_page_background_position'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'esc_html'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_doc_page_background_position', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_background_position',
		'label'    => __( 'Position', 'betterdocs' ),
		'priority' => 8,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_background_property betterdocs-select',
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

	$wp_customize->add_setting( 'betterdocs_doc_page_content_padding', array(
		'default'       => $defaults['betterdocs_doc_page_content_padding'],
		'capability'    => 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_content_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_content_padding',
		'label'    => __( 'Content Area Padding', 'betterdocs' ),
		'priority' => 9,
		'input_attrs' => array(
			'id' => 'betterdocs-doc-page-content-padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_content_padding_top',
		apply_filters('betterdocs_doc_page_content_padding_top', array(
			'default'       => $defaults['betterdocs_doc_page_content_padding_top'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) )
	);

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_content_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_content_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority' => 10,
		'input_attrs' => array(
			'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension',
		),
	) ) );
	
	$wp_customize->add_setting( 'betterdocs_doc_page_content_padding_right', 
		apply_filters('betterdocs_doc_page_content_padding_right', array(
			'default'       => $defaults['betterdocs_doc_page_content_padding_right'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) )
	);

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_content_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_content_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority' => 11,
		'input_attrs' => array(
			'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_content_padding_bottom', 
		apply_filters('betterdocs_doc_page_content_padding_bottom', array(
			'default'       => $defaults['betterdocs_doc_page_content_padding_bottom'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) )
	);

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_content_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_content_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority' => 12,
		'input_attrs' => array(
			'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_content_padding_left', 
		apply_filters('betterdocs_doc_page_content_padding_left', array(
			'default'       => $defaults['betterdocs_doc_page_content_padding_left'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) )
	);

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_content_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_content_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority' => 13,
		'input_attrs' => array(
			'class' => 'betterdocs-doc-page-content-padding betterdocs-dimension',
		),
	) ) );

	// Content Area Width

	$wp_customize->add_setting( 'betterdocs_doc_page_content_width', 
		apply_filters('betterdocs_doc_page_content_width', array(
			'default'       => $defaults['betterdocs_doc_page_content_width'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) )
	);

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_content_width', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_content_width',
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

	$wp_customize->add_setting( 'betterdocs_doc_page_content_max_width', 
		apply_filters('betterdocs_doc_page_content_max_width', array(
			'default'       => $defaults['betterdocs_doc_page_content_max_width'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) )
	);

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_content_max_width', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_content_max_width',
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

	// Category Column Settings

	$wp_customize->add_setting('betterdocs_doc_page_column_settings', array(
		'default'           => $defaults['betterdocs_doc_page_column_settings'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_column_settings', array(
		'label'	      => esc_html__( 'Category Column Settings', 'betterdocs' ),
		'priority' => 16,
		'settings'		=> 'betterdocs_doc_page_column_settings',
		'section'  		=> 'betterdocs_doc_page_settings'
	)));

	// Spacing Between Columns

	$wp_customize->add_setting( 'betterdocs_doc_page_column_space', 
		apply_filters('betterdocs_doc_page_column_space', array(
			'default'       => $defaults['betterdocs_doc_page_column_space'],
			'capability'    => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) )
	);

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_column_space', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_space',
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

	// Column Background Color

	$wp_customize->add_setting( 'betterdocs_doc_page_column_bg_color' , array(
		'default'     => $defaults['betterdocs_doc_page_column_bg_color'],
		'capability'    => 'edit_theme_options',
		'transport'   => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_column_bg_color',
		array(
			'label'      => __( 'Column Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_column_bg_color',
			'priority' => 18,
		) )
	);

	// Column Background Color Layout 2

	$wp_customize->add_setting( 'betterdocs_doc_page_column_bg_color2' , array(
		'default'     => $defaults['betterdocs_doc_page_column_bg_color2'],
		'capability'    => 'edit_theme_options',
		'transport'   => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_column_bg_color2',
		array(
			'label'      => __( 'Column Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_column_bg_color2',
			'priority' => 18
		) )
	);

	// Column Hover Background Color

	$wp_customize->add_setting( 'betterdocs_doc_page_column_hover_bg_color' , array(
		'default'     => $defaults['betterdocs_doc_page_column_hover_bg_color'],
		'capability'    => 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_column_hover_bg_color',
		array(
			'label'      => __( 'Column Hover Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_column_hover_bg_color',
			'priority' => 18
		) )
	);

	// Column Padding

	$wp_customize->add_setting( 'betterdocs_doc_page_column_padding', array(
		'default'       => $defaults['betterdocs_doc_page_column_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_column_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_padding',
		'label'    => __( 'Column Padding', 'betterdocs' ),
		'priority' => 18,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_page_column_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_padding_top', array(
		'default'       => $defaults['betterdocs_doc_page_column_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority' => 19,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_padding_right', array(
		'default'       => $defaults['betterdocs_doc_page_column_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority' => 20,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_padding_bottom', array(
		'default'       => $defaults['betterdocs_doc_page_column_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority' => 21,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_padding_left', array(
		'default'       => $defaults['betterdocs_doc_page_column_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority' => 22,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_padding betterdocs-dimension',
		),
	) ) );

	// Category Icon Size

	$wp_customize->add_setting( 'betterdocs_doc_page_cat_icon_size_layout1', array(
		'default'       => $defaults['betterdocs_doc_page_cat_icon_size_layout1'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_cat_icon_size_layout1', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_cat_icon_size_layout1',
		'label'    => __( 'Icon Size', 'betterdocs' ),
		'priority' => 23,
		'input_attrs' => array(
			'class' => '',
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Category Icon Size

	$wp_customize->add_setting( 'betterdocs_doc_page_cat_icon_size_layout2', array(
		'default'       => $defaults['betterdocs_doc_page_cat_icon_size_layout2'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_cat_icon_size_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_cat_icon_size_layout2',
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

	$wp_customize->add_setting( 'betterdocs_doc_page_column_borderr', array(
		'default'       => $defaults['betterdocs_doc_page_column_borderr'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_column_borderr', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_borderr',
		'label'    => __( 'Column Border Radius', 'betterdocs' ),
		'priority' => 24,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_page_column_borderr',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_borderr_topleft', array(
		'default'       => $defaults['betterdocs_doc_page_column_borderr_topleft'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_borderr_topleft', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_borderr_topleft',
		'label'    => __( 'Top Left', 'betterdocs' ),
		'priority' => 24,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_borderr betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_borderr_topright', array(
		'default'       => $defaults['betterdocs_doc_page_column_borderr_topright'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_borderr_topright', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_borderr_topright',
		'label'    => __( 'Top Right', 'betterdocs' ),
		'priority' => 24,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_borderr betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_borderr_bottomright', array(
		'default'       => $defaults['betterdocs_doc_page_column_borderr_bottomright'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_borderr_bottomright', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_borderr_bottomright',
		'label'    => __( 'Bottom Right', 'betterdocs' ),
		'priority' => 24,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_borderr betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_borderr_bottomleft', array(
		'default'       => $defaults['betterdocs_doc_page_column_borderr_bottomleft'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_borderr_bottomleft', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_borderr_bottomleft',
		'label'    => __( 'Bottom Left', 'betterdocs' ),
		'priority' => 24,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_borderr betterdocs-dimension',
		),
	) ) );

	// Category Title Font Size

	$wp_customize->add_setting( 'betterdocs_doc_page_cat_title_font_size', array(
		'default'       => $defaults['betterdocs_doc_page_cat_title_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_cat_title_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_cat_title_font_size',
		'label'    => __( 'Category Title Font Size', 'betterdocs' ),
		'priority' => 25,
		'input_attrs' => array(
			'class' => '',
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Category Title Color

	$wp_customize->add_setting( 'betterdocs_doc_page_cat_title_color' , array(
		'default'     => $defaults['betterdocs_doc_page_cat_title_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_cat_title_color',
		array(
			'label'      => __( 'Category Title Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_cat_title_color',
			'priority' => 26
		) )
	);
	
	$wp_customize->add_setting( 'betterdocs_doc_page_cat_title_color2' , array(
		'default'     => $defaults['betterdocs_doc_page_cat_title_color2'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_cat_title_color2',
		array(
			'label'      => __( 'Category Title Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_cat_title_color2',
			'priority' => 26
		) )
	);

	$wp_customize->add_setting( 'betterdocs_doc_page_cat_title_hover_color' , array(
		'default'     => $defaults['betterdocs_doc_page_cat_title_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_cat_title_hover_color',
		array(
			'label'      => __( 'Category Title Hover Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_cat_title_hover_color',
			'priority' => 26
		) )
	);

	// Category Title Border Color

	$wp_customize->add_setting( 'betterdocs_doc_page_cat_title_border_color' , array(
		'default'     => $defaults['betterdocs_doc_page_cat_title_border_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_cat_title_border_color',
		array(
			'label'      => __( 'Category Title Border Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_cat_title_border_color',
			'priority' => 27
		) )
	);

	// Category Description

	$wp_customize->add_setting('betterdocs_doc_page_cat_desc', array(
        'default' => $defaults['betterdocs_doc_page_cat_desc'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_checkbox',
    ));

    $wp_customize->add_control(new BetterDocs_Customizer_Toggle_Control(
		$wp_customize, 'betterdocs_doc_page_cat_desc', array(
        'label' => esc_html__('Category Description', 'betterdocs'),
        'section' => 'betterdocs_doc_page_settings',
        'settings' => 'betterdocs_doc_page_cat_desc',
		'type' => 'light', // light, ios, flat
		'priority' => 28
	)));

	// Category Description Color

	$wp_customize->add_setting( 'betterdocs_doc_page_cat_desc_color' , array(
		'default'     => $defaults['betterdocs_doc_page_cat_desc_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_cat_desc_color',
		array(
			'label'      => __( 'Category Description Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_cat_desc_color',
			'priority' => 28
		) )
	);

	// Item Count Title

	$wp_customize->add_setting('betterdocs_item_counter_title', array(
		'default'           => $defaults['betterdocs_item_counter_title'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_item_counter_title', array(
		'label'	    => esc_html__( 'Category Item Counter', 'betterdocs' ),
		'settings'	=> 'betterdocs_item_counter_title',
		'section'  	=> 'betterdocs_doc_page_settings',
		'priority'  => 28
	)));
	
	// Item Count Color 

	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_color' , array(
		'default'     => $defaults['betterdocs_doc_page_item_count_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_item_count_color',
		array(
			'label'      => __( 'Item Count Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_item_count_color',
			'priority' => 28
		) )
	);
	
	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_color_layout2' , array(
		'default'     => $defaults['betterdocs_doc_page_item_count_color_layout2'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_item_count_color_layout2',
		array(
			'label'      => __( 'Item Count Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_item_count_color_layout2',
			'priority' => 29
		) )
	);

	// Item Count Font Size

	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_font_size', array(
		'default'       => $defaults['betterdocs_doc_page_item_count_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_item_count_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_item_count_font_size',
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

	// Item Count Background Color

	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_bg_color' , array(
		'default'     => $defaults['betterdocs_doc_page_item_count_bg_color'],
		'capability'    => 'edit_theme_options',
		'transport'   => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_item_count_bg_color',
		array(
			'label'      => __( 'Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_item_count_bg_color',
			'priority' => 31
		) )
	);

	// Item Count Background Color

	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_inner_bg_color' , array(
		'default'     => $defaults['betterdocs_doc_page_item_count_inner_bg_color'],
		'capability'    => 'edit_theme_options',
		'transport'   => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_item_count_inner_bg_color',
		array(
			'label'      => __( 'Inner Circle Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_item_count_inner_bg_color',
			'priority' => 31
		) )
	);

	// Item Counter Size

	$wp_customize->add_setting( 'betterdocs_doc_page_item_counter_size', array(
		'default'       => $defaults['betterdocs_doc_page_item_counter_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_item_counter_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_item_counter_size',
		'label'    => __( 'Counter Size (Height, Width)', 'betterdocs' ),
		'priority' => 32,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 10,
			'max'    => 100,
			'step'   => 1,
			'suffix'  => 'px', //optional suffix
		),
	) ) );

	// Content Space Between

	$wp_customize->add_setting( 'betterdocs_doc_page_column_content_space', array(
		'default'       => $defaults['betterdocs_doc_page_column_content_space'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_column_content_space', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_content_space',
		'label'    => __( 'Content Space Between', 'betterdocs' ),
		'priority' => 33,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_content_space betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_content_space_image', array(
		'default'       => $defaults['betterdocs_doc_page_column_content_space_image'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_content_space_image', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_content_space_image',
		'label'    => __( 'Image', 'betterdocs' ),
		'priority' => 33,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_content_space betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_content_space_title', array(
		'default'       => $defaults['betterdocs_doc_page_column_content_space_title'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_content_space_title', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_content_space_title',
		'label'    => __( 'Title', 'betterdocs' ),
		'priority' => 33,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_content_space betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_content_space_desc', array(
		'default'       => $defaults['betterdocs_doc_page_column_content_space_desc'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_content_space_desc', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_content_space_desc',
		'label'    => __( 'Description', 'betterdocs' ),
		'priority' => 33,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_content_space betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_column_content_space_counter', array(
		'default'       => $defaults['betterdocs_doc_page_column_content_space_counter'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_column_content_space_counter', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_column_content_space_counter',
		'label'    => __( 'Counter', 'betterdocs' ),
		'priority' => 33,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_column_content_space betterdocs-dimension',
		),
	) ) );

	// Article List

	$wp_customize->add_setting('betterdocs_doc_page_article_list_settings', array(
		'default'           => $defaults['betterdocs_doc_page_article_list_settings'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_settings', array(
		'label'	    => esc_html__( 'Article List', 'betterdocs' ),
		'settings'	=> 'betterdocs_doc_page_article_list_settings',
		'section'  	=> 'betterdocs_doc_page_settings',
		'priority'  => 33
	)));

	// Article List Background Color

	$wp_customize->add_setting( 'betterdocs_doc_page_article_list_bg_color' , array(
		'default'     => $defaults['betterdocs_doc_page_article_list_bg_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_article_list_bg_color',
		array(
			'label'      => __( 'Article List Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_article_list_bg_color',
			'priority' => 34
		) )
	);

	// Article List Color

	$wp_customize->add_setting( 'betterdocs_doc_page_article_list_color' , array(
		'default'     => $defaults['betterdocs_doc_page_article_list_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_article_list_color',
		array(
			'label'      => __( 'Article List Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_article_list_color',
			'priority' 	 => 35
		) )
	);

	// Article List Hover Color

	$wp_customize->add_setting( 'betterdocs_doc_page_article_list_hover_color' , array(
		'default'     => $defaults['betterdocs_doc_page_article_list_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_article_list_hover_color',
		array(
			'label'      => __( 'Article List Hover Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_article_list_hover_color',
			'priority' => 36
		) )
	);

	// Article List Font Size

	$wp_customize->add_setting( 'betterdocs_doc_page_article_list_font_size', array(
		'default'       => $defaults['betterdocs_doc_page_article_list_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_font_size',
		'label'    => __( 'Article List Font Size', 'betterdocs' ),
		'priority' => 37,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix'  => 'px', //optional suffix
		),
	) ) );

	// List Icon Color

	$wp_customize->add_setting( 'betterdocs_doc_page_list_icon_color' , array(
		'default'     => $defaults['betterdocs_doc_page_list_icon_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_list_icon_color',
		array(
			'label'      => __( 'List Icon Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_list_icon_color',
			'priority' => 38
		) )
	);

	// List Icon Font Size

	$wp_customize->add_setting( 'betterdocs_doc_page_list_icon_font_size', array(
		'default'       => $defaults['betterdocs_doc_page_list_icon_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_list_icon_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_list_icon_font_size',
		'label'    => __( 'List Icon Font Size', 'betterdocs' ),
		'priority' => 39,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Article list margin

	$wp_customize->add_setting( 'betterdocs_doc_page_article_list_margin', array(
		'default'       => $defaults['betterdocs_doc_page_article_list_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin',
		'label'    => __( 'Article List Margin', 'betterdocs' ),
		'priority' => 40,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_page_article_list_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_article_list_margin_top', array(
		'default'       => $defaults['betterdocs_doc_page_article_list_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority' => 41,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_article_list_margin_right', array(
		'default'       => $defaults['betterdocs_doc_page_article_list_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority' => 42,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_article_list_margin_bottom', array(
		'default'       => $defaults['betterdocs_doc_page_article_list_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority' => 43,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_article_list_margin_left', array(
		'default'       => $defaults['betterdocs_doc_page_article_list_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension',
		),
	) ) );

	// Article Subcategory Color

	$wp_customize->add_setting( 'betterdocs_doc_page_article_subcategory_color' , array(
		'default'     => $defaults['betterdocs_doc_page_article_subcategory_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_article_subcategory_color',
		array(
			'label'      => __( 'Article Subcategory Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_article_subcategory_color',
			'priority' 	 => 44
		) )
	);

	// Article Subcategory Hover Color

	$wp_customize->add_setting( 'betterdocs_doc_page_article_subcategory_hover_color' , array(
		'default'     => $defaults['betterdocs_doc_page_article_subcategory_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_article_subcategory_hover_color',
		array(
			'label'      => __( 'Article Subcategory Hover Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_article_subcategory_hover_color',
			'priority' => 44
		) )
	);

	// Article Subcategory Font Size

	$wp_customize->add_setting( 'betterdocs_doc_page_article_subcategory_font_size', array(
		'default'       => $defaults['betterdocs_doc_page_article_subcategory_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_article_subcategory_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_subcategory_font_size',
		'label'    => __( 'Article Subcategory Font Size', 'betterdocs' ),
		'priority' => 44,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix'  => 'px', //optional suffix
		),
	) ) );

	// Subcategory Icon Color

	$wp_customize->add_setting( 'betterdocs_doc_page_subcategory_icon_color' , array(
		'default'     => $defaults['betterdocs_doc_page_subcategory_icon_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_subcategory_icon_color',
		array(
			'label'      => __( 'Subcategory Icon Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_subcategory_icon_color',
			'priority' => 44
		) )
	);

	// Subcategory Icon Font Size

	$wp_customize->add_setting( 'betterdocs_doc_page_subcategory_icon_font_size', array(
		'default'       => $defaults['betterdocs_doc_page_subcategory_icon_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_subcategory_icon_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_subcategory_icon_font_size',
		'label'    => __( 'Subcategory Icon Font Size', 'betterdocs' ),
		'priority' => 44,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Explore More Button

	$wp_customize->add_setting('betterdocs_doc_page_explore_btn', array(
		'default'           => $defaults['betterdocs_doc_page_explore_btn'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn', array(
		'label'	      => esc_html__( 'Explore More Button', 'betterdocs' ),
		'settings'		=> 'betterdocs_doc_page_explore_btn',
		'section'  		=> 'betterdocs_doc_page_settings',
		'priority' => 45
	)));

	// Explore More Button Background Color

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_bg_color' , array(
		'default'     => $defaults['betterdocs_doc_page_explore_btn_bg_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_explore_btn_bg_color',
		array(
			'label'      => __( 'Button Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_explore_btn_bg_color',
			'priority' => 46
		) )
	);

	// Explore More Button Color

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_color' , array(
		'default'     => $defaults['betterdocs_doc_page_explore_btn_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_explore_btn_color',
		array(
			'label'      => __( 'Button Text Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_explore_btn_color',
			'priority' => 47
		) )
	);

	// Explore More Button Border Color

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_border_color' , array(
		'default'     => $defaults['betterdocs_doc_page_explore_btn_border_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_explore_btn_border_color',
		array(
			'label'      => __( 'Button Border Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_explore_btn_border_color',
			'priority' => 48
		) )
	);

	// Explore More Button Hover Background Color

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_hover_bg_color' , array(
		'default'     => $defaults['betterdocs_doc_page_explore_btn_hover_bg_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_explore_btn_hover_bg_color',
		array(
			'label'      => __( 'Button Hover Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_explore_btn_hover_bg_color',
			'priority' => 49
		) )
	);

	// Explore More Button Hover Color

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_hover_color' , array(
		'default'     => $defaults['betterdocs_doc_page_explore_btn_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_explore_btn_hover_color',
		array(
			'label'      => __( 'Button Hover Text Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_explore_btn_hover_color',
			'priority' => 50
		) )
	);

	// Explore More Button Hover Border Color

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_hover_border_color' , array(
		'default'     => $defaults['betterdocs_doc_page_explore_btn_hover_border_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_explore_btn_hover_border_color',
		array(
			'label'      => __( 'Button Hover Border Color', 'betterdocs' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_explore_btn_hover_border_color',
			'priority' => 51
		) )
	);

	// Explore More Button Font Size

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_font_size', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_font_size',
		'label'    => __( 'Button Font Size', 'betterdocs' ),
		'priority' => 52,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Explore More Button Padding

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_padding', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_padding',
		'label'    => __( 'Button Padding', 'betterdocs' ),
		'priority' => 53,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_page_explore_btn_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_padding_top', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority' => 54,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_padding_right', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority' => 55,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_padding_bottom', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority' => 56,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_padding_left', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority' => 57,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension',
		),
	) ) );

	// explore more button border radius 

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_borderr', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_borderr'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_borderr', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_borderr',
		'label'    => __( 'Button Border Radius', 'betterdocs' ),
		'priority' => 58,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_page_explore_btn_borderr',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_borderr_topleft', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_borderr_topleft'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_borderr_topleft', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_borderr_topleft',
		'label'    => __( 'Top Left', 'betterdocs' ),
		'priority' => 59,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_explore_btn_borderr betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_borderr_topright', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_borderr_topright'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_borderr_topright', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_borderr_topright',
		'label'    => __( 'Top Right', 'betterdocs' ),
		'priority' => 60,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_explore_btn_borderr betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_borderr_bottomright', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_borderr_bottomright'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_borderr_bottomright', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_borderr_bottomright',
		'label'    => __( 'Bottom Right', 'betterdocs' ),
		'priority' => 61,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_explore_btn_borderr betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_page_explore_btn_borderr_bottomleft', array(
		'default'       => $defaults['betterdocs_doc_page_explore_btn_borderr_bottomleft'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_explore_btn_borderr_bottomleft', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_explore_btn_borderr_bottomleft',
		'label'    => __( 'Bottom Left', 'betterdocs' ),
		'priority' => 62,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_explore_btn_borderr betterdocs-dimension',
		),
	) ) );

  	// Single Doc Settings

	$wp_customize->add_section( 'betterdocs_single_docs_settings' , array(
	'title'      => __('Single Doc','betterdocs'),
	'priority'   => 101
	) );

	$wp_customize->add_setting( 'betterdocs_single_layout_select' , array(
		'default'     => $defaults['betterdocs_single_layout_select'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_select',
	) );

	$wp_customize->add_control(
		new BetterDocs_Radio_Image_Control(
		$wp_customize,
		'betterdocs_single_layout_select',
		array(
			'type'     => 'betterdocs-radio-image',
			'settings'		=> 'betterdocs_single_layout_select',
			'section'		=> 'betterdocs_single_docs_settings',
			'label'			=> __( 'Select Layout', 'theme-slug' ),
			'priority'   => 102,
			'choices'		=> apply_filters('betterdocs_single_layout_select_choices', array(
				'layout-1' 	=> array(
					'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-1.png',
				),
				'layout-2' 	=> array(
					'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-2.png',
					'pro' => true,
					'url' => 'https://betterdocs.co/upgrade',
				),
				'layout-3' 	=> array(
					'image' => BETTERDOCS_ADMIN_URL . 'assets/img/single-layout-3.png',
					'pro' => true,
					'url' => 'https://betterdocs.co/upgrade',
				),
			))
		) )
	);

	// Column Background Color

	$wp_customize->add_setting( 'betterdocs_doc_single_content_area_bg_color' , array(
		'default'     => $defaults['betterdocs_doc_single_content_area_bg_color'],
		'capability'    => 'edit_theme_options',
		'transport'   => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_single_content_area_bg_color',
		array(
			'label'      => __( 'Content Area Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_doc_single_content_area_bg_color',
			'priority'   => 103
		) )
	);

	$wp_customize->add_setting( 'betterdocs_doc_single_content_area_padding', array(
		'default'       => $defaults['betterdocs_doc_single_content_area_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_single_content_area_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_content_area_padding',
		'label'    => __( 'Content Area Padding', 'betterdocs' ),
		'priority'   => 104,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_single_content_area_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_content_area_padding_top', array(
		'default'       => $defaults['betterdocs_doc_single_content_area_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_content_area_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_content_area_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority'   => 105,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_content_area_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_content_area_padding_right', array(
		'default'       => $defaults['betterdocs_doc_single_content_area_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_content_area_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_content_area_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority'   => 106,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_content_area_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_content_area_padding_bottom', array(
		'default'       => $defaults['betterdocs_doc_single_content_area_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_content_area_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_content_area_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority'   => 107,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_content_area_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_content_area_padding_left', array(
		'default'       => $defaults['betterdocs_doc_single_content_area_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_content_area_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_content_area_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority'   => 108,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_content_area_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_post_content_padding', array(
		'default'       => $defaults['betterdocs_doc_single_post_content_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_single_post_content_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_post_content_padding',
		'label'    => __( 'Post Content Padding', 'betterdocs' ),
		'priority'   => 109,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_single_post_content_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_post_content_padding_top', array(
		'default'       => $defaults['betterdocs_doc_single_post_content_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_post_content_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_post_content_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority'   => 110,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_post_content_padding_right', array(
		'default'       => $defaults['betterdocs_doc_single_post_content_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_post_content_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_post_content_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority'   => 111,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_post_content_padding_bottom', array(
		'default'       => $defaults['betterdocs_doc_single_post_content_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_post_content_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_post_content_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority'   => 112,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_post_content_padding_left', array(
		'default'       => $defaults['betterdocs_doc_single_post_content_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_post_content_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_post_content_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority'   => 113,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_2_post_content_padding', array(
		'default'       => $defaults['betterdocs_doc_single_2_post_content_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_single_2_post_content_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_2_post_content_padding',
		'label'    => __( 'Post Content Padding', 'betterdocs' ),
		'priority'   => 114,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_single_2_post_content_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_2_post_content_padding_top', array(
		'default'       => $defaults['betterdocs_doc_single_2_post_content_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_2_post_content_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_2_post_content_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority'   => 115,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_2_post_content_padding  betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_2_post_content_padding_right', array(
		'default'       => $defaults['betterdocs_doc_single_2_post_content_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_2_post_content_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_2_post_content_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority'   => 116,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_2_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_2_post_content_padding_bottom', array(
		'default'       => $defaults['betterdocs_doc_single_2_post_content_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_2_post_content_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_2_post_content_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority'   => 117,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_2_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_2_post_content_padding_left', array(
		'default'       => $defaults['betterdocs_doc_single_2_post_content_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_2_post_content_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_2_post_content_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority'   => 118,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_2_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_3_post_content_padding', array(
		'default'       => $defaults['betterdocs_doc_single_3_post_content_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_single_3_post_content_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_3_post_content_padding',
		'label'    => __( 'Content Area Padding', 'betterdocs' ),
		'priority'   => 119,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_single_3_post_content_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_3_post_content_padding_top', array(
		'default'       => $defaults['betterdocs_doc_single_3_post_content_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_3_post_content_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_3_post_content_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority'   => 120,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_3_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_3_post_content_padding_right', array(
		'default'       => $defaults['betterdocs_doc_single_3_post_content_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_3_post_content_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_3_post_content_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority'   => 121,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_3_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_3_post_content_padding_bottom', array(
		'default'       => $defaults['betterdocs_doc_single_3_post_content_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_3_post_content_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_3_post_content_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority'   => 122,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_3_post_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_3_post_content_padding_left', array(
		'default'       => $defaults['betterdocs_doc_single_3_post_content_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_3_post_content_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_3_post_content_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority'   => 123,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_3_post_content_padding betterdocs-dimension',
		),
	) ) );

	// Post title
	
	$wp_customize->add_setting('betterdocs_single_doc_title', array(
		'default'           => $defaults['betterdocs_single_doc_title'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_single_doc_title', array(
		'label'	      => esc_html__( 'Post Title', 'betterdocs' ),
		'priority'   => 124,
		'settings'		=> 'betterdocs_single_doc_title',
		'section'  		=> 'betterdocs_single_docs_settings'
	)));

	// Breadcrumbs Font Size

	$wp_customize->add_setting( 'betterdocs_single_doc_title_font_size', array(
		'default'       => $defaults['betterdocs_single_doc_title_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_single_doc_title_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_doc_title_font_size',
		'label'    => __( 'Font Size', 'betterdocs' ),
		'priority'   => 125,
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Breadcrumbs color

	$wp_customize->add_setting( 'betterdocs_single_doc_title_color' , array(
		'capability'    => 'edit_theme_options',
	    'default'     => $defaults['betterdocs_single_doc_title_color'],
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_title_color',
		array(
			'label'      => __( 'Color', 'betterdocs' ),
			'priority'   => 126,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_title_color',
		) )
	);

	// Breadcrumbs
	
	$wp_customize->add_setting('betterdocs_single_doc_breadcrumb', array(
		'default'           => $defaults['betterdocs_single_doc_breadcrumb'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_single_doc_breadcrumb', array(
		'label'	      => esc_html__( 'Breadcrumb', 'betterdocs' ),
		'priority'   => 127,
		'settings'		=> 'betterdocs_single_doc_breadcrumb',
		'section'  		=> 'betterdocs_single_docs_settings'
	)));

	// Breadcrumbs Font Size

	$wp_customize->add_setting( 'betterdocs_single_doc_breadcrumbs_font_size', array(
		'default'       => $defaults['betterdocs_single_doc_breadcrumbs_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_single_doc_breadcrumbs_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_doc_breadcrumbs_font_size',
		'label'    => __( 'Font Size', 'betterdocs' ),
		'priority'   => 128,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Breadcrumbs color

	$wp_customize->add_setting( 'betterdocs_single_doc_breadcrumb_color' , array(
		'capability'    => 'edit_theme_options',
	    'default'     => $defaults['betterdocs_single_doc_breadcrumb_color'],
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_breadcrumb_color',
		array(
			'label'      => __( 'Color', 'betterdocs' ),
			'priority'   => 129,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_breadcrumb_color',
		) )
	);
	
	$wp_customize->add_setting( 'betterdocs_single_doc_breadcrumb_hover_color' , array(
		'capability'    => 'edit_theme_options',
		'default'     => $defaults['betterdocs_single_doc_breadcrumb_hover_color'],
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_breadcrumb_hover_color',
		array(
			'label'      => __( 'Hover Color', 'betterdocs' ),
			'priority'   => 129,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_breadcrumb_hover_color',
		) )
	);

	// Breadcrumbs seperator color

	$wp_customize->add_setting( 'betterdocs_single_doc_breadcrumb_speretor_color' , array(
		'capability'    => 'edit_theme_options',
	    'default'     => $defaults['betterdocs_single_doc_breadcrumb_speretor_color'],
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_breadcrumb_speretor_color',
		array(
			'label'      => __( 'Seperator Color', 'betterdocs' ),
			'priority'   => 130,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_breadcrumb_speretor_color',
		) )
	);

	// Breadcrumbs Active Item Color

	$wp_customize->add_setting( 'betterdocs_single_doc_breadcrumb_active_item_color' , array(
		'capability'  => 'edit_theme_options',
	    'default'     => $defaults['betterdocs_single_doc_breadcrumb_active_item_color'],
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_breadcrumb_active_item_color',
		array(
			'label'      => __( 'Active Item Color', 'betterdocs' ),
			'priority'   => 131,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_breadcrumb_active_item_color',
		) )
	);

	// Table of Content (TOC)

	$wp_customize->add_setting('betterdocs_doc_single_toc_title', array(
		'default'           => $defaults['betterdocs_doc_single_toc_title'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_doc_single_toc_title', array(
		'label'	      => esc_html__( 'Table of Contents', 'betterdocs' ),
		'priority'   => 132,
		'settings'		=> 'betterdocs_doc_single_toc_title',
		'section'  		=> 'betterdocs_single_docs_settings'
	)));

	// Sticky Toc Width

	$wp_customize->add_setting( 'betterdocs_sticky_toc_width', array(
		'default'       => $defaults['betterdocs_sticky_toc_width'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sticky_toc_width', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_sticky_toc_width',
		'label'    => __( 'Sticky Toc Width', 'betterdocs' ),
		'priority'   => 133,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 100,
			'max'    => 500,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// TOC z-index

	$wp_customize->add_setting( 'betterdocs_sticky_toc_zindex', array(
		'default'       => $defaults['betterdocs_sticky_toc_zindex'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Number_Control(
		$wp_customize, 'betterdocs_sticky_toc_zindex', array(
		'type'     => 'betterdocs-number',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_sticky_toc_zindex',
		'label'    => __( 'Sticky Toc z-index', 'betterdocs' ),
		'priority'   => 134,
	) ) );

	// Sticky Toc Margin Top

	$wp_customize->add_setting( 'betterdocs_sticky_toc_margin_top', array(
		'default'       => $defaults['betterdocs_sticky_toc_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sticky_toc_margin_top', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_sticky_toc_margin_top',
		'label'    => __( 'Sticky Toc Margin Top', 'betterdocs' ),
		'priority'   => 135,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 500,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// TOC Background Color

	$wp_customize->add_setting( 'betterdocs_toc_bg_color' , array(
		'capability'    => 'edit_theme_options',
	    'default'     => $defaults['betterdocs_toc_bg_color'],
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_toc_bg_color',
		array(
			'label'      => __( 'Background Color', 'betterdocs' ),
			'priority'   => 136,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_toc_bg_color',
		) )
	);

	// TOC Padding

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_padding', array(
		'default'       => $defaults['betterdocs_doc_single_toc_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_single_toc_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_padding',
		'label'    => __( 'Content Area Padding', 'betterdocs' ),
		'priority'   => 137,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_single_toc_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_padding_top', array(
		'default'       => $defaults['betterdocs_doc_single_toc_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_toc_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority'   => 138,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_toc_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_padding_right', array(
		'default'       => $defaults['betterdocs_doc_single_toc_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_toc_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority'   => 139,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_toc_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_padding_bottom', array(
		'default'       => $defaults['betterdocs_doc_single_toc_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_toc_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority'   => 140,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_toc_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_padding_left', array(
		'default'       => $defaults['betterdocs_doc_single_toc_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_toc_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority'   => 141,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_toc_padding betterdocs-dimension',
		),
	) ) );

	// TOC title color

	$wp_customize->add_setting( 'betterdocs_toc_title_color' , array(
		'capability'    => 'edit_theme_options',
	    'default'     => $defaults['betterdocs_toc_title_color'],
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_toc_title_color',
		array(
			'label'      => __( 'Title Color', 'betterdocs' ),
			'priority'   => 142,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_toc_title_color',
		) )
	);

	// Title font size

	$wp_customize->add_setting( 'betterdocs_toc_title_font_size', array(
		'default'       => $defaults['betterdocs_toc_title_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_toc_title_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_toc_title_font_size',
		'label'    => __( 'Title Font Size', 'betterdocs' ),
		'priority'   => 143,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// TOC list item color

	$wp_customize->add_setting( 'betterdocs_toc_list_item_color' , array(
	    'default'     => $defaults['betterdocs_toc_list_item_color'],
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_toc_list_item_color',
		array(
			'label'      => __( 'List Item Color', 'betterdocs' ),
			'priority'   => 144,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_toc_list_item_color',
		) )
	);

	// TOC list item hover color

	$wp_customize->add_setting( 'betterdocs_toc_list_item_hover_color' , array(
		'capability'    => 'edit_theme_options',
		'default'     => $defaults['betterdocs_toc_list_item_hover_color'],
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_toc_list_item_hover_color',
		array(
			'label'      => __( 'List Item Hover Color', 'betterdocs' ),
			'priority'   => 145,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_toc_list_item_hover_color',
		) )
	);

	// TOC active item color

	$wp_customize->add_setting( 'betterdocs_toc_active_item_color' , array(
		'capability'  => 'edit_theme_options',
	    'default'     => $defaults['betterdocs_toc_active_item_color'],
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_toc_active_item_color',
		array(
			'label'      => __( 'Active Item Color', 'betterdocs' ),
			'priority'   => 146,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_toc_active_item_color',
		) )
	);

	// TOC list item font size

	$wp_customize->add_setting( 'betterdocs_toc_list_item_font_size', array(
		'default'       => $defaults['betterdocs_toc_list_item_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_toc_list_item_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_toc_list_item_font_size',
		'label'    => __( 'List Item Font Size', 'betterdocs' ),
		'priority'   => 147,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// TOC List Margin

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_list_margin', array(
		'default'       => $defaults['betterdocs_doc_single_toc_list_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_single_toc_list_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_list_margin',
		'label'    => __( 'TOC List Margin', 'betterdocs' ),
		'priority'   => 148,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_single_toc_list_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_list_margin_top', array(
		'default'       => $defaults['betterdocs_doc_single_toc_list_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_toc_list_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_list_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'priority'   => 149,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_toc_list_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_list_margin_right', array(
		'default'       => $defaults['betterdocs_doc_single_toc_list_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_toc_list_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_list_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'priority'   => 150,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_toc_list_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_list_margin_bottom', array(
		'default'       => $defaults['betterdocs_doc_single_toc_list_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_toc_list_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_list_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'priority'   => 151,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_toc_list_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_doc_single_toc_list_margin_left', array(
		'default'       => $defaults['betterdocs_doc_single_toc_list_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_single_toc_list_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_doc_single_toc_list_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'priority'   => 152,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_single_toc_list_margin betterdocs-dimension',
		),
	) ) );

	
	// TOC list number color

	$wp_customize->add_setting( 'betterdocs_toc_list_number_color' , array(
		'default'     => $defaults['betterdocs_toc_list_number_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_toc_list_number_color',
		array(
			'label'      => __( 'List Number Color', 'betterdocs' ),
			'priority'   => 153,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_toc_list_number_color',
		) )
	);

	// TOC list number font size

	$wp_customize->add_setting( 'betterdocs_toc_list_number_font_size', array(
		'default'       => $defaults['betterdocs_toc_list_number_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_toc_list_number_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_toc_list_number_font_size',
		'label'    => __( 'List Number Font Size', 'betterdocs' ),
		'priority'   => 154,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Entry Content Font Size

	$wp_customize->add_setting( 'betterdocs_toc_margin_bottom', array(
		'default'       => $defaults['betterdocs_toc_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_toc_margin_bottom', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_toc_margin_bottom',
		'label'    => __( 'TOC Margin Bottom', 'betterdocs' ),
		'priority'   => 155,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 500,
			'step'   => 1,
			'suffix' => 'px', // optional suffix
		),
	) ) );

	// Entry Content

	$wp_customize->add_setting('betterdocs_doc_single_entry_content', array(
		'default'           => $defaults['betterdocs_doc_single_entry_content'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_doc_single_entry_content', array(
		'label'	      => esc_html__( 'Entry Content', 'betterdocs' ),
		'priority'   => 156,
		'settings'		=> 'betterdocs_doc_single_entry_content',
		'section'  		=> 'betterdocs_single_docs_settings'
	)));

	// Entry Content Font Size

	$wp_customize->add_setting( 'betterdocs_single_content_font_size', array(
		'default'       => $defaults['betterdocs_single_content_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_single_content_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_content_font_size',
		'label'    => __( 'Font Size', 'betterdocs' ),
		'priority'   => 157,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', // optional suffix
		),
	) ) );

	// Entry Content Font Color

	$wp_customize->add_setting( 'betterdocs_single_content_font_color' , array(
		'default'     => $defaults['betterdocs_single_content_font_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_content_font_color',
		array(
			'label'      => __( 'Font Color', 'betterdocs' ),
			'priority'   => 158,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_content_font_color',
		) )
	);
	
	// Social Share Separator

    $wp_customize->add_setting('betterdocs_social_share_title', array(
        'default' => '',
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(new BetterDocs_Separator_Custom_Control($wp_customize, 'betterdocs_social_share_title', array(
		'label' => __('Social Share', 'betterdocs'),
		'priority'   => 164,
        'settings' => 'betterdocs_social_share_title',
        'section' => 'betterdocs_single_docs_settings',
	)));
	
	// post social share 

    $wp_customize->add_setting('betterdocs_post_social_share', array(
        'default' => $defaults['betterdocs_post_social_share'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_checkbox',
    ));

    $wp_customize->add_control(new BetterDocs_Customizer_Toggle_Control($wp_customize, 'betterdocs_post_social_share', array(
		'label' => esc_html__('Enable Social Sharing?', 'betterdocs'),
		'priority'   => 165,
        'section' => 'betterdocs_single_docs_settings',
        'settings' => 'betterdocs_post_social_share',
        'type' => 'light', // light, ios, flat
    )));

    $wp_customize->add_setting('betterdocs_social_sharing_text', array(
		'default' => $defaults['betterdocs_social_sharing_text'],
		'capability'    => 'edit_theme_options',
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_social_sharing_text',
            array(
				'label' => __('Social Sharing Title', 'betterdocs'),
				'priority'   => 166,
                'section' => 'betterdocs_single_docs_settings',
                'settings' => 'betterdocs_social_sharing_text',
                'type' => 'text',
            )
        )
	);
	
	// Social Share Text Color

	$wp_customize->add_setting( 'betterdocs_post_social_share_text_color' , array(
		'default'     => $defaults['betterdocs_post_social_share_text_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_post_social_share_text_color',
		array(
			'label'      => __( 'Title Text Color', 'betterdocs' ),
			'priority'   => 167,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_post_social_share_text_color',
		) )
	);

    $wp_customize->add_setting('betterdocs_post_social_share_facebook', array(
        'default' => $defaults['betterdocs_post_social_share_facebook'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_checkbox',
    ));

    $wp_customize->add_control( new BetterDocs_Customizer_Toggle_Control( 
		$wp_customize, 'betterdocs_post_social_share_facebook', array(
		'label' => esc_html__('Facebook Sharing', 'betterdocs'),
		'priority'   => 168,
        'section' => 'betterdocs_single_docs_settings',
        'settings' => 'betterdocs_post_social_share_facebook',
        'type' => 'light', // light, ios, flat
	)));

    $wp_customize->add_setting('betterdocs_post_social_share_twitter', array(
        'default' => $defaults['betterdocs_post_social_share_twitter'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_checkbox',
    ));

    $wp_customize->add_control(new BetterDocs_Customizer_Toggle_Control(
		$wp_customize, 'betterdocs_post_social_share_twitter', array(
		'label' => esc_html__('Twitter Sharing', 'betterdocs'),
		'priority'   => 169,
        'section' => 'betterdocs_single_docs_settings',
        'settings' => 'betterdocs_post_social_share_twitter',
        'type' => 'light', // light, ios, flat
	)));

    $wp_customize->add_setting('betterdocs_post_social_share_linkedin', array(
        'default' => $defaults['betterdocs_post_social_share_linkedin'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_checkbox',
    ));

    $wp_customize->add_control(new BetterDocs_Customizer_Toggle_Control($wp_customize, 'betterdocs_post_social_share_linkedin', array(
		'label' => esc_html__('Linkedin Sharing', 'betterdocs'),
		'priority'   => 170,
        'section' => 'betterdocs_single_docs_settings',
        'settings' => 'betterdocs_post_social_share_linkedin',
        'type' => 'light', // light, ios, flat
	)));

    $wp_customize->add_setting('betterdocs_post_social_share_pinterest', array(
        'default' => $defaults['betterdocs_post_social_share_pinterest'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_checkbox',
    ));

    $wp_customize->add_control(new BetterDocs_Customizer_Toggle_Control($wp_customize, 'betterdocs_post_social_share_pinterest', array(
		'label' => esc_html__('Pinterest Sharing', 'betterdocs'),
		'priority'   => 171,
        'section' => 'betterdocs_single_docs_settings',
        'settings' => 'betterdocs_post_social_share_pinterest',
        'type' => 'light', // light, ios, flat
	)));

	// Entry Footer

	$wp_customize->add_setting('betterdocs_doc_single_entry_footer', array(
		'default'           => $defaults['betterdocs_doc_single_entry_footer'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_doc_single_entry_footer', array(
		'label'	      => esc_html__( 'Entry Footer', 'betterdocs' ),
		'priority'   => 172,
		'settings'		=> 'betterdocs_doc_single_entry_footer',
		'section'  		=> 'betterdocs_single_docs_settings'
	)));

	// Feedback icon font size

	$wp_customize->add_setting( 'betterdocs_single_doc_feedback_icon_font_size', array(
		'default'       => $defaults['betterdocs_single_doc_feedback_icon_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_single_doc_feedback_icon_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_doc_feedback_icon_font_size',
		'label'    => __( 'Feedback Icon Size', 'betterdocs' ),
		'priority'   => 173,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_single_doc_feedback_icon', array(
		'default'       => $defaults['betterdocs_single_doc_feedback_icon'],
		'capability'    => 'edit_theme_options'
	) );

	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize, 'betterdocs_single_doc_feedback_icon', array(
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_doc_feedback_icon',
		'label'    => __( 'Feedback Icon', 'betterdocs' ),
		'priority'   => 173,
	) ) );
	
	// Feedback Link Color

	$wp_customize->add_setting( 'betterdocs_single_doc_feedback_link_color' , array(
		'default'     => $defaults['betterdocs_single_doc_feedback_link_color'],
		'capability'  => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_feedback_link_color',
		array(
			'label'      => __( 'Feedback Link Color', 'betterdocs' ),
			'priority'   => 174,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_feedback_link_color',
		) )
	);

	// Feedback Link Hover Color

	$wp_customize->add_setting( 'betterdocs_single_doc_feedback_link_hover_color' , array(
		'default'     => $defaults['betterdocs_single_doc_feedback_link_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_feedback_link_hover_color',
		array(
			'label'      => __( 'Feedback Link Hover Color', 'betterdocs' ),
			'priority'   => 175,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_feedback_link_hover_color',
		) )
	);

	// Feedback Link Font Size

	$wp_customize->add_setting( 'betterdocs_single_doc_feedback_link_font_size', array(
		'default'       => $defaults['betterdocs_single_doc_feedback_link_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_single_doc_feedback_link_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_doc_feedback_link_font_size',
		'label'    => __( 'Feedback Link Font Size', 'betterdocs' ),
		'priority'   => 176,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Navigation Color

	$wp_customize->add_setting( 'betterdocs_single_doc_navigation_color' , array(
		'default'     => $defaults['betterdocs_single_doc_navigation_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_navigation_color',
		array(
			'label'      => __( 'Navigation Color', 'betterdocs' ),
			'priority'   => 177,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_navigation_color',
		) )
	);

	// Navigation Font Size

	$wp_customize->add_setting( 'betterdocs_single_doc_navigation_font_size', array(
		'default'       => $defaults['betterdocs_single_doc_navigation_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_single_doc_navigation_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_doc_navigation_font_size',
		'label'    => __( 'Navigation Font Size', 'betterdocs' ),
		'priority'   => 178,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Navigation Hover Color

	$wp_customize->add_setting( 'betterdocs_single_doc_navigation_hover_color' , array(
		'default'     => $defaults['betterdocs_single_doc_navigation_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_navigation_hover_color',
		array(
			'label'      => __( 'Navigation Hover Color', 'betterdocs' ),
			'priority'   => 179,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_navigation_hover_color',
		) )
	);

	// Navigation Arrow Color

	$wp_customize->add_setting( 'betterdocs_single_doc_navigation_arrow_color' , array(
		'default'     => $defaults['betterdocs_single_doc_navigation_arrow_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_navigation_arrow_color',
		array(
			'label'      => __( 'Navigation Arrow Color', 'betterdocs' ),
			'priority'   => 180,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_navigation_arrow_color',
		) )
	);

	// Navigation Arrow Font Size

	$wp_customize->add_setting( 'betterdocs_single_doc_navigation_arrow_font_size', array(
		'default'       => $defaults['betterdocs_single_doc_navigation_arrow_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_single_doc_navigation_arrow_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_doc_navigation_arrow_font_size',
		'label'    => __( 'Navigation Arrow Font Size', 'betterdocs' ),
		'priority'   => 181,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Last Updated Time Color

	$wp_customize->add_setting( 'betterdocs_single_doc_lu_time_color' , array(
		'default'     => $defaults['betterdocs_single_doc_lu_time_color'],
		'capability'  => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_lu_time_color',
		array(
			'label'      => __( 'Last Updated Time Color', 'betterdocs' ),
			'priority'   => 182,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_lu_time_color',
		) )
	);

	// Last Updated Time Font Size

	$wp_customize->add_setting( 'betterdocs_single_doc_lu_time_font_size', array(
		'default'       => $defaults['betterdocs_single_doc_lu_time_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_single_doc_lu_time_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_doc_lu_time_font_size',
		'label'    => __( 'Last Updated Time Font Size', 'betterdocs' ),
		'priority'   => 183,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Powered By Color

	$wp_customize->add_setting( 'betterdocs_single_doc_powered_by_color' , array(
		'default'     => $defaults['betterdocs_single_doc_powered_by_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_powered_by_color',
		array(
			'label'      => __( 'Powered by Color', 'betterdocs' ),
			'priority'   => 184,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_powered_by_color',
		) )
	);

	// Powered By Font Size

	$wp_customize->add_setting( 'betterdocs_single_doc_powered_by_font_size', array(
		'default'       => $defaults['betterdocs_single_doc_powered_by_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_single_doc_powered_by_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_single_docs_settings',
		'settings' => 'betterdocs_single_doc_powered_by_font_size',
		'label'    => __( 'Powered By Font Size', 'betterdocs' ),
		'priority'   => 185,
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Powered By Link Color

	$wp_customize->add_setting( 'betterdocs_single_doc_powered_by_link_color' , array(
		'default'     => $defaults['betterdocs_single_doc_powered_by_link_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_single_doc_powered_by_link_color',
		array(
			'label'      => __( 'Powered By Link Color', 'betterdocs' ),
			'priority'   => 186,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_single_doc_powered_by_link_color',
		) )
	);

	// Sidebar 

	$wp_customize->add_section( 'betterdocs_sidebar_settings' , array(
		'title'      => __('Sidebar','betterdocs'),
		'priority'   => 300
	) );

	// Sidebar Background Color

	$wp_customize->add_setting( 'betterdocs_sidebar_bg_color' , array(
		'default'     => $defaults['betterdocs_sidebar_bg_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_bg_color',
		array(
			'label'      => __( 'Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_bg_color',
		) )
	);

	// Sidebar Padding

	$wp_customize->add_setting( 'betterdocs_sidebar_padding', array(
		'default'       => $defaults['betterdocs_sidebar_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding',
		'label'    => __( 'Sidebar Padding', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_padding_top', array(
		'default'       => $defaults['betterdocs_sidebar_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_padding_right', array(
		'default'       => $defaults['betterdocs_sidebar_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_padding_bottom', array(
		'default'       => $defaults['betterdocs_sidebar_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_padding_left', array(
		'default'       => $defaults['betterdocs_sidebar_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_padding betterdocs-dimension',
		),
	) ) );

	// Sidebar border radius 

	$wp_customize->add_setting( 'betterdocs_sidebar_borderr', array(
		'default'       => $defaults['betterdocs_sidebar_borderr'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_borderr', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_borderr',
		'label'    => __( 'Sidebar Border Radius', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_borderr',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_borderr_topleft', array(
		'default'       => $defaults['betterdocs_sidebar_borderr_topleft'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_borderr_topleft', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_borderr_topleft',
		'label'    => __( 'Top Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_borderr betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_borderr_topright', array(
		'default'       => $defaults['betterdocs_sidebar_borderr_topright'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_borderr_topright', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_borderr_topright',
		'label'    => __( 'Top Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_borderr betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_borderr_bottomright', array(
		'default'       => $defaults['betterdocs_sidebar_borderr_bottomright'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_borderr_bottomright', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_borderr_bottomright',
		'label'    => __( 'Bottom Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_borderr betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_borderr_bottomleft', array(
		'default'       => $defaults['betterdocs_sidebar_borderr_bottomleft'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_borderr_bottomleft', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_borderr_bottomleft',
		'label'    => __( 'Bottom Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_borderr betterdocs-dimension',
		),
	) ) );

	// Sidebar Title Settings

	$wp_customize->add_setting('betterdocs_sidebar_title', array(
		'default'           => $defaults['betterdocs_sidebar_title'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_title', array(
		'label'	      => esc_html__( 'Sidebar Title', 'betterdocs' ),
		'settings'		=> 'betterdocs_sidebar_title',
		'section'  		=> 'betterdocs_sidebar_settings'
	)));

	// Sidebar Icon Size

	$wp_customize->add_setting( 'betterdocs_sidebar_icon_size', array(
		'default'       => $defaults['betterdocs_sidebar_icon_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_icon_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_icon_size',
		'label'    => __( 'Icon Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Sidebar Title Background Color

	$wp_customize->add_setting( 'betterdocs_sidebar_title_bg_color' , array(
		'default'     => $defaults['betterdocs_sidebar_title_bg_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_title_bg_color',
		array(
			'label'      => __( 'Title Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_title_bg_color',
		) )
	);

	// Sidebar Active Category Border Color

	$wp_customize->add_setting( 'betterdocs_sidebar_active_cat_background_color' , array(
		'default'     => $defaults['betterdocs_sidebar_active_cat_background_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_active_cat_background_color',
		array(
			'label'      => __( 'Active Title Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_active_cat_background_color',
		) )
	);

	// Sidebar Active Category Border Color

	$wp_customize->add_setting( 'betterdocs_sidebar_active_cat_border_color' , array(
		'default'     => $defaults['betterdocs_sidebar_active_cat_border_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_active_cat_border_color',
		array(
			'label'      => __( 'Active Title Border Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_active_cat_border_color',
		) )
	);

	// Sidebar Title Color

	$wp_customize->add_setting( 'betterdocs_sidebar_title_color' , array(
		'default'     => $defaults['betterdocs_sidebar_title_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_title_color',
		array(
			'label'      => __( 'Title Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_title_color',
		) )
	);
	
	$wp_customize->add_setting( 'betterdocs_sidebar_title_hover_color' , array(
		'default'     => $defaults['betterdocs_sidebar_title_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_title_hover_color',
		array(
			'label'      => __( 'Title Hover Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_title_hover_color',
		) )
	);
	
	// Sidebar Active Title Color

	$wp_customize->add_setting( 'betterdocs_sidebar_active_title_color' , array(
		'default'     => $defaults['betterdocs_sidebar_active_title_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_active_title_color',
		array(
			'label'      => __( 'Active Title Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_active_title_color',
		) )
	);


	// Sidebar Title Font Size

	$wp_customize->add_setting( 'betterdocs_sidebar_title_font_size', array(
		'default'       => $defaults['betterdocs_sidebar_title_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_title_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_font_size',
		'label'    => __( 'Title Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Sidebar Title Padding

	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding', array(
		'default'       => $defaults['betterdocs_sidebar_title_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding',
		'label'    => __( 'Title Padding', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_title_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding_top', array(
		'default'       => $defaults['betterdocs_sidebar_title_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding_right', array(
		'default'       => $defaults['betterdocs_sidebar_title_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding_bottom', array(
		'default'       => $defaults['betterdocs_sidebar_title_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding_left', array(
		'default'       => $defaults['betterdocs_sidebar_title_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_padding betterdocs-dimension',
		),
	) ) );

	// Sidebar Title Margin

	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin', array(
		'default'       => $defaults['betterdocs_sidebar_title_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin',
		'label'    => __( 'Title Margin', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_title_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin_top', array(
		'default'       => $defaults['betterdocs_sidebar_title_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin_right', array(
		'default'       => $defaults['betterdocs_sidebar_title_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin_bottom', array(
		'default'       => $defaults['betterdocs_sidebar_title_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin_left', array(
		'default'       => $defaults['betterdocs_sidebar_title_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_margin betterdocs-dimension',
		),
	) ) );

	// Sidebar Title Settings

	$wp_customize->add_setting('betterdocs_sidebar_item_counter_title', array(
		'default'           => $defaults['betterdocs_sidebar_item_counter_title'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_item_counter_title', array(
		'label'	      => esc_html__( 'Sidebar Item Counter', 'betterdocs' ),
		'settings'		=> 'betterdocs_sidebar_item_counter_title',
		'section'  		=> 'betterdocs_sidebar_settings'
	)));

	// Sidebar Item Count Background Color

	$wp_customize->add_setting( 'betterdocs_sidbebar_item_count_bg_color' , array(
		'default'     => $defaults['betterdocs_sidbebar_item_count_bg_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidbebar_item_count_bg_color',
		array(
			'label'      => __( 'Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidbebar_item_count_bg_color',
		) )
	);

	// Item Count Background Color

	$wp_customize->add_setting( 'betterdocs_sidbebar_item_count_inner_bg_color' , array(
		'default'     => $defaults['betterdocs_sidbebar_item_count_inner_bg_color'],
		'capability'    => 'edit_theme_options',
		'transport'   => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidbebar_item_count_inner_bg_color',
		array(
			'label'      => __( 'Inner Circle Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidbebar_item_count_inner_bg_color'
		) )
	);

	// Item Counter Size

	$wp_customize->add_setting( 'betterdocs_sidebar_item_counter_size', array(
		'default'       => $defaults['betterdocs_sidebar_item_counter_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_item_counter_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_item_counter_size',
		'label'    => __( 'Size (Height, Width)', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 10,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Sidebar Item Count Color

	$wp_customize->add_setting( 'betterdocs_sidebar_item_count_color' , array(
		'default'     => $defaults['betterdocs_sidebar_item_count_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_item_count_color',
		array(
			'label'      => __( 'Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_item_count_color',
		) )
	);

	// Sidebar Item Count Font Size

	$wp_customize->add_setting( 'betterdocs_sidebat_item_count_font_size', array(
		'default'       => $defaults['betterdocs_sidebat_item_count_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebat_item_count_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebat_item_count_font_size',
		'label'    => __( 'Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Sidebar Title Settings

	$wp_customize->add_setting('betterdocs_sidebar_content', array(
		'default'           => $defaults['betterdocs_sidebar_content'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_content', array(
		'label'	      => esc_html__( 'Sidebar Content', 'betterdocs' ),
		'settings'		=> 'betterdocs_sidebar_content',
		'section'  		=> 'betterdocs_sidebar_settings'
	)));

	// Sidebar Item List Background Color

	$wp_customize->add_setting( 'betterdocs_sidbebar_item_list_bg_color' , array(
		'default'     => $defaults['betterdocs_sidbebar_item_list_bg_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidbebar_item_list_bg_color',
		array(
			'label'      => __( 'List Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidbebar_item_list_bg_color',
		) )
	);

	// Sidebar List Item Color

	$wp_customize->add_setting( 'betterdocs_sidebar_list_item_color' , array(
		'default'     => $defaults['betterdocs_sidebar_list_item_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_list_item_color',
		array(
			'label'      => __( 'List Item Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_list_item_color',
		) )
	);

	// Sidebar List Item Hover Color

	$wp_customize->add_setting( 'betterdocs_sidebar_list_item_hover_color' , array(
		'default'     => $defaults['betterdocs_sidebar_list_item_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_list_item_hover_color',
		array(
			'label'      => __( 'List Item Hover Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_list_item_hover_color',
		) )
	);

	// Sidebar List Item Font Size

	$wp_customize->add_setting( 'betterdocs_sidebar_list_item_font_size', array(
		'default'       => $defaults['betterdocs_sidebar_list_item_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_list_item_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_list_item_font_size',
		'label'    => __( 'List Item Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Sidebar List Icon Color

	$wp_customize->add_setting( 'betterdocs_sidebar_list_icon_color' , array(
		'default'     => $defaults['betterdocs_sidebar_list_icon_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_list_icon_color',
		array(
			'label'      => __( 'List Icon Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_list_icon_color'
		) )
	);

	// Sidebar List Icon Font Size

	$wp_customize->add_setting( 'betterdocs_sidebar_list_icon_font_size', array(
		'default'       => $defaults['betterdocs_sidebar_list_icon_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_list_icon_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_list_icon_font_size',
		'label'    => __( 'List Icon Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Sidebar Item Margin

	$wp_customize->add_setting( 'betterdocs_sidebar_list_item_margin', array(
		'default'       => $defaults['betterdocs_sidebar_list_item_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_list_item_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_list_item_margin',
		'label'    => __( 'List Item Margin', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_list_item_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_list_item_margin_top', array(
		'default'       => $defaults['betterdocs_sidebar_list_item_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_list_item_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_list_item_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_list_item_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_list_item_margin_right', array(
		'default'       => $defaults['betterdocs_sidebar_list_item_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_list_item_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_list_item_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_list_item_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_list_item_margin_bottom', array(
		'default'       => $defaults['betterdocs_sidebar_list_item_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_list_item_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_list_item_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_list_item_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_sidebar_list_item_margin_left', array(
		'default'       => $defaults['betterdocs_sidebar_list_item_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_list_item_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_list_item_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_list_item_margin betterdocs-dimension',
		),
	) ) );

	// Sidebar Active List Item Color

	$wp_customize->add_setting( 'betterdocs_sidebar_active_list_item_color' , array(
		'default'     => $defaults['betterdocs_sidebar_active_list_item_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_active_list_item_color',
		array(
			'label'      => __( 'Active List Item Color', 'betterdocs' ),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_active_list_item_color',
		) )
	);

	// Archive Page

	$wp_customize->add_section( 'betterdocs_archive_page_settings' , array(
		'title'      => __('Archive Page','betterdocs'),
		'priority'   => 400
	) );

	// Archive Background Color

	$wp_customize->add_setting( 'betterdocs_archive_page_background_color' , array(
		'default'     => $defaults['betterdocs_archive_page_background_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_page_background_color',
		array(
			'label'      => __( 'Page Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_page_background_color'
		) )
	);

	// Archive background image
	
	$wp_customize->add_setting( 'betterdocs_archive_page_background_image', array(
		'default'       => $defaults['betterdocs_archive_page_background_image'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage'

	) );

	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize, 'betterdocs_archive_page_background_image', array(
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_page_background_image',
		'label'    => __( 'Backgroud Image', 'betterdocs' ),
	) ) );

	// Background property

	$wp_customize->add_setting( 'betterdocs_archive_page_background_property', array(
		'default'       => $defaults['betterdocs_archive_page_background_property'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_page_background_property', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_page_background_property',
		'label'    => __( 'Background Property', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_page_background_property',
			'class' => 'betterdocs-select',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_page_background_size', array(
		'default'       => $defaults['betterdocs_archive_page_background_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_archive_page_background_size', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_page_background_size',
		'label'    => __( 'Size', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_page_background_property betterdocs-select',
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
	
	$wp_customize->add_setting( 'betterdocs_archive_page_background_repeat', array(
		'default'       => $defaults['betterdocs_archive_page_background_repeat'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_archive_page_background_repeat', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_page_background_repeat',
		'label'    => __( 'Repeat', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_page_background_property betterdocs-select',
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

	$wp_customize->add_setting( 'betterdocs_archive_page_background_attachment', array(
		'default'       => $defaults['betterdocs_archive_page_background_attachment'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_archive_page_background_attachment', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_page_background_attachment',
		'label'    => __( 'Attachment', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_page_background_property betterdocs-select',
		),
		'choices'  => array(
			'initial' 	=> __( 'initial', 'betterdocs' ),
			'inherit'   => __( 'inherit', 'betterdocs' ),
			'scroll'   	=> __( 'scroll', 'betterdocs' ),
			'fixed'  	 => __( 'fixed', 'betterdocs' ),
			'local'  	=> __( 'local', 'betterdocs' ),
		)
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_page_background_position', array(
		'default'       => $defaults['betterdocs_archive_page_background_position'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'esc_html'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_archive_page_background_position', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_page_background_position',
		'label'    => __( 'Position', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_page_background_property betterdocs-select',
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

	// Archive Content Area

	$wp_customize->add_setting('betterdocs_archive_content_area_settings', array(
		'default'           => $defaults['betterdocs_archive_content_area_settings'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_archive_content_area_settings', array(
		'label'	      => esc_html__( 'Content Area', 'betterdocs' ),
		'settings'		=> 'betterdocs_archive_content_area_settings',
		'section'  		=> 'betterdocs_archive_page_settings'
	)));

	// Archive Content Area Background Color

	$wp_customize->add_setting( 'betterdocs_archive_content_background_color' , array(
		'default'     => $defaults['betterdocs_archive_content_background_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_content_background_color',
		array(
			'label'      => __( 'Content Area Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_content_background_color'
		) )
	);

	// Archive Content Area Margin

	$wp_customize->add_setting( 'betterdocs_archive_content_margin', array(
		'default'       => $defaults['betterdocs_archive_content_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_content_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_margin',
		'label'    => __( 'Content Area Margin', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_content_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_content_margin_top', array(
		'default'       => $defaults['betterdocs_archive_content_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_content_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_content_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_content_margin_right', array(
		'default'       => $defaults['betterdocs_archive_content_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_content_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_content_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_content_margin_bottom', array(
		'default'       => $defaults['betterdocs_archive_content_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_content_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_content_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_content_margin_left', array(
		'default'       => $defaults['betterdocs_archive_content_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_content_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_content_margin betterdocs-dimension',
		),
	) ) );

	// Archive Content Area Padding

	$wp_customize->add_setting( 'betterdocs_archive_content_padding', array(
		'default'       => $defaults['betterdocs_archive_content_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_content_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_padding',
		'label'    => __( 'Content Area Padding', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_content_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_content_padding_top', array(
		'default'       => $defaults['betterdocs_archive_content_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_content_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_content_padding_right', array(
		'default'       => $defaults['betterdocs_archive_content_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_content_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_content_padding_bottom', array(
		'default'       => $defaults['betterdocs_archive_content_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_content_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_content_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_content_padding_left', array(
		'default'       => $defaults['betterdocs_archive_content_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_content_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_content_padding betterdocs-dimension',
		),
	) ) );

	// Archive Content Border Radius

	$wp_customize->add_setting( 'betterdocs_archive_content_border_radius', array(
		'default'       => $defaults['betterdocs_archive_content_border_radius'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_content_border_radius', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_content_border_radius',
		'label'    => __( 'Archive Content Border Radius', 'betterdocs' ),
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Archive Title Color

	$wp_customize->add_setting( 'betterdocs_archive_title_color' , array(
		'default'     => $defaults['betterdocs_archive_title_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_title_color',
		array(
			'label'      => __( 'Title Color', 'betterdocs' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_title_color',
		) )
	);

	// Archive Title Font Size

	$wp_customize->add_setting( 'betterdocs_archive_title_font_size', array(
		'default'       => $defaults['betterdocs_archive_title_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_title_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_title_font_size',
		'label'    => __( 'Title Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Archive Title Margin

	$wp_customize->add_setting( 'betterdocs_archive_title_margin', array(
		'default'       => $defaults['betterdocs_archive_title_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_title_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_title_margin',
		'label'    => __( 'Archive Title Margin', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_title_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_title_margin_top', array(
		'default'       => $defaults['betterdocs_archive_title_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_title_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_title_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_title_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_title_margin_right', array(
		'default'       => $defaults['betterdocs_archive_title_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_title_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_title_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_title_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_title_margin_bottom', array(
		'default'       => $defaults['betterdocs_archive_title_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_title_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_title_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_title_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_title_margin_left', array(
		'default'       => $defaults['betterdocs_archive_title_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_title_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_title_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_title_margin betterdocs-dimension',
		),
	) ) );

	// Archive Description

	$wp_customize->add_setting( 'betterdocs_archive_description_color' , array(
		'default'     => $defaults['betterdocs_archive_description_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_description_color',
		array(
			'label'      => __( 'Description Color', 'betterdocs' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_description_color',
		) )
	);

	// Archive Description Font Size

	$wp_customize->add_setting( 'betterdocs_archive_description_font_size', array(
		'default'       => $defaults['betterdocs_archive_description_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_description_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_font_size',
		'label'    => __( 'Description Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Archive Description Margin

	$wp_customize->add_setting( 'betterdocs_archive_description_margin', array(
		'default'       => $defaults['betterdocs_archive_description_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_description_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin',
		'label'    => __( 'Archive Description Margin', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_description_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_description_margin_top', array(
		'default'       => $defaults['betterdocs_archive_description_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_description_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_description_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_description_margin_right', array(
		'default'       => $defaults['betterdocs_archive_description_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_description_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_description_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_description_margin_bottom', array(
		'default'       => $defaults['betterdocs_archive_description_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_description_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_description_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_description_margin_left', array(
		'default'       => $defaults['betterdocs_archive_description_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_description_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_description_margin betterdocs-dimension',
		),
	) ) );

	// Archive List Icon Color

	$wp_customize->add_setting( 'betterdocs_archive_list_icon_color' , array(
		'default'     => $defaults['betterdocs_archive_list_icon_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_icon_color',
		array(
			'label'      => __( 'List Icon Color', 'betterdocs' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_icon_color',
		) )
	);

	// Archive List Icon Font Size

	$wp_customize->add_setting( 'betterdocs_archive_list_icon_font_size', array(
		'default'       => $defaults['betterdocs_archive_list_icon_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_list_icon_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_icon_font_size',
		'label'    => __( 'List Icon Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Archive List Item Color

	$wp_customize->add_setting( 'betterdocs_archive_list_item_color' , array(
		'default'     => $defaults['betterdocs_archive_list_item_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_item_color',
		array(
			'label'      => __( 'List Item Color', 'betterdocs' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_item_color',
		) )
	);
	
	// Archive List Item Hover Color

	$wp_customize->add_setting( 'betterdocs_archive_list_item_hover_color' , array(
		'default'     => $defaults['betterdocs_archive_list_item_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_item_hover_color',
		array(
			'label'      => __( 'List Item Hover Color', 'betterdocs' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_item_hover_color',
		) )
	);

	// Archive List Item Font Size

	$wp_customize->add_setting( 'betterdocs_archive_list_item_font_size', array(
		'default'       => $defaults['betterdocs_archive_list_item_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_list_item_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_item_font_size',
		'label'    => __( 'List Item Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Archive Article list margin

	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin', array(
		'default'       => $defaults['betterdocs_archive_article_list_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin',
		'label'    => __( 'Article List Margin', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_article_list_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin_top', array(
		'default'       => $defaults['betterdocs_archive_article_list_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin_right', array(
		'default'       => $defaults['betterdocs_archive_article_list_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin_bottom', array(
		'default'       => $defaults['betterdocs_archive_article_list_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin_left', array(
		'default'       => $defaults['betterdocs_archive_article_list_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_margin betterdocs-dimension',
		),
	) ) );

	// Live Search

	$wp_customize->add_section( 'betterdocs_live_search_settings' , array(
		'title'      => __('Live Search','betterdocs'),
		'priority'   => 500
	) );

	$wp_customize->add_setting( 'betterdocs_live_search_heading_switch', array(
		'default'       => $defaults['betterdocs_live_search_heading_switch'],
		'capability'    => 'edit_theme_options',

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Toggle_Control( 
		$wp_customize, 'betterdocs_live_search_heading_switch', array(
		'label' => esc_html__('Search Heading', 'betterdocs'),
        'section' => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_live_search_heading_switch',
        'type' => 'light', // light, ios, flat
	)));

	$wp_customize->add_setting('betterdocs_live_search_heading', array(
		'default' => $defaults['betterdocs_live_search_heading'],
		'capability'    => 'edit_theme_options',
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_live_search_heading',
            array(
				'label' => __('Heading', 'betterdocs'),
                'section' => 'betterdocs_live_search_settings',
                'settings' => 'betterdocs_live_search_heading',
                'type' => 'text',
            )
        )
	);

	// Search Field Font Size

	$wp_customize->add_setting( 'betterdocs_live_search_heading_font_size', array(
		'default'       => $defaults['betterdocs_live_search_heading_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_live_search_heading_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_heading_font_size',
		'label'    => __( 'Heading Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_live_search_heading_font_color' , array(
		'default'     => $defaults['betterdocs_live_search_heading_font_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_live_search_heading_font_color',
		array(
			'label'      => __( 'Heading Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_live_search_heading_font_color',
		) )
	);

	$wp_customize->add_setting( 'betterdocs_search_heading_margin', array(
		'default'       => $defaults['betterdocs_search_heading_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_search_heading_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_heading_margin',
		'label'    => __( 'Heading Margin', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_search_heading_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_heading_margin_top', array(
		'default'       => $defaults['betterdocs_search_heading_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_heading_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_heading_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_heading_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_heading_margin_right', array(
		'default'       => $defaults['betterdocs_search_heading_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_heading_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_heading_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_heading_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_heading_margin_bottom', array(
		'default'       => $defaults['betterdocs_search_heading_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_heading_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_heading_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_heading_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_heading_margin_left', array(
		'default'       => $defaults['betterdocs_search_heading_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_heading_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_heading_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_heading_margin betterdocs-dimension',
		),
	) ) );
	
	$wp_customize->add_setting('betterdocs_live_search_subheading', array(
		'default' => $defaults['betterdocs_live_search_subheading'],
		'capability'    => 'edit_theme_options',
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_live_search_subheading',
            array(
				'label' => __('Sub Heading', 'betterdocs'),
                'section' => 'betterdocs_live_search_settings',
                'settings' => 'betterdocs_live_search_subheading',
                'type' => 'text',
            )
        )
	);

	$wp_customize->add_setting( 'betterdocs_live_search_subheading_font_size', array(
		'default'       => $defaults['betterdocs_live_search_subheading_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_live_search_subheading_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_subheading_font_size',
		'label'    => __( 'Sub Heading Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_live_search_subheading_font_color' , array(
		'default'     => $defaults['betterdocs_live_search_subheading_font_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_live_search_subheading_font_color',
		array(
			'label'      => __( 'Sub Heading Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_live_search_subheading_font_color',
		) )
	);

	$wp_customize->add_setting( 'betterdocs_search_subheading_margin', array(
		'default'       => $defaults['betterdocs_search_subheading_margin'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_search_subheading_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_subheading_margin',
		'label'    => __( 'Sub Heading Margin', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_search_subheading_margin',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_subheading_margin_top', array(
		'default'       => $defaults['betterdocs_search_subheading_margin_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_subheading_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_subheading_margin_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_subheading_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_subheading_margin_right', array(
		'default'       => $defaults['betterdocs_search_subheading_margin_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_subheading_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_subheading_margin_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_subheading_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_subheading_margin_bottom', array(
		'default'       => $defaults['betterdocs_search_subheading_margin_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_subheading_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_subheading_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_subheading_margin betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_subheading_margin_left', array(
		'default'       => $defaults['betterdocs_search_subheading_margin_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_subheading_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_subheading_margin_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_subheading_margin betterdocs-dimension',
		),
	) ) );

	// Live Search Background Color

	$wp_customize->add_setting( 'betterdocs_live_search_background_color' , array(
		'default'     => $defaults['betterdocs_live_search_background_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_live_search_background_color',
		array(
			'label'      => __( 'Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_live_search_background_color'
		) )
	);

    // Live Search background image
	
	$wp_customize->add_setting( 'betterdocs_live_search_background_image', array(
		'default'       => $defaults['betterdocs_live_search_background_image'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage'

	) );

	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize, 'betterdocs_live_search_background_image', array(
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_background_image',
		'label'    => __( 'Backgroud Image', 'betterdocs' ),
	) ) );

	// Background property

	$wp_customize->add_setting( 'betterdocs_live_search_background_property', array(
		'default'       => $defaults['betterdocs_live_search_background_property'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_live_search_background_property', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_background_property',
		'label'    => __( 'Background Property', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_live_search_background_property',
			'class' => 'betterdocs-select',
		),
		
	) ) );

	$wp_customize->add_setting( 'betterdocs_live_search_background_size', array(
		'default'       => $defaults['betterdocs_live_search_background_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_live_search_background_size', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_background_size',
		'label'    => __( 'Size', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_live_search_background_property betterdocs-select',
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
	
	$wp_customize->add_setting( 'betterdocs_live_search_background_repeat', array(
		'default'       => $defaults['betterdocs_live_search_background_repeat'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_live_search_background_repeat', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_background_repeat',
		'label'    => __( 'Repeat', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_live_search_background_property betterdocs-select',
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

	$wp_customize->add_setting( 'betterdocs_live_search_background_attachment', array(
		'default'       => $defaults['betterdocs_live_search_background_attachment'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_select'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_live_search_background_attachment', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_background_attachment',
		'label'    => __( 'Attachment', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_live_search_background_property betterdocs-select',
		),
		'choices'  => array(
			'initial' 	=> __( 'initial', 'betterdocs' ),
			'inherit'   => __( 'inherit', 'betterdocs' ),
			'scroll'   	=> __( 'scroll', 'betterdocs' ),
			'fixed'  	 => __( 'fixed', 'betterdocs' ),
			'local'  	=> __( 'local', 'betterdocs' ),
		)
	) ) );

	$wp_customize->add_setting( 'betterdocs_live_search_background_position', array(
		'default'       => $defaults['betterdocs_live_search_background_position'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'esc_html'

	) );

	$wp_customize->add_control( new BetterDocs_Select_Control(
		$wp_customize, 'betterdocs_live_search_background_position', array(
		'type'     => 'betterdocs-select',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_background_position',
		'label'    => __( 'Position', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_live_search_background_property betterdocs-select',
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

	// Live Search Padding

	$wp_customize->add_setting( 'betterdocs_live_search_padding', array(
		'default'       => $defaults['betterdocs_live_search_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_live_search_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_padding',
		'label'    => __( 'Padding', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_live_search_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_live_search_padding_top', array(
		'default'       => $defaults['betterdocs_live_search_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_live_search_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_live_search_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_live_search_padding_right', array(
		'default'       => $defaults['betterdocs_live_search_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_live_search_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_live_search_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_live_search_padding_bottom', array(
		'default'       => $defaults['betterdocs_live_search_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_live_search_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_live_search_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_live_search_padding_left', array(
		'default'       => $defaults['betterdocs_live_search_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_live_search_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_live_search_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_live_search_padding betterdocs-dimension',
		),
	) ) );

	// Search Field Settings

	$wp_customize->add_setting('betterdocs_search_field_settings', array(
		'default'           => $defaults['betterdocs_search_field_settings'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_search_field_settings', array(
		'label'	      => esc_html__( 'Search Field Settings', 'betterdocs' ),
		'settings'		=> 'betterdocs_search_field_settings',
		'section'  		=> 'betterdocs_live_search_settings'
	)));

	// Search Field Background Color

	$wp_customize->add_setting( 'betterdocs_search_field_background_color' , array(
		'default'     => $defaults['betterdocs_search_field_background_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_field_background_color',
		array(
			'label'      => __( 'Search Field Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_field_background_color'
		) )
	);

	// Search Field Font Size

	$wp_customize->add_setting( 'betterdocs_search_field_font_size', array(
		'default'       => $defaults['betterdocs_search_field_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_search_field_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_field_font_size',
		'label'    => __( 'Search Field Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Search Field Color

	$wp_customize->add_setting( 'betterdocs_search_field_color' , array(
		'default'     => $defaults['betterdocs_search_field_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_field_color',
		array(
			'label'      => __( 'Search Field Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_field_color'
		) )
	);

	// Search Field Padding

	$wp_customize->add_setting( 'betterdocs_search_field_padding', array(
		'default'       => $defaults['betterdocs_search_field_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_search_field_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_field_padding',
		'label'    => __( 'Search Field Padding', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_search_field_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_field_padding_top', array(
		'default'       => $defaults['betterdocs_search_field_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_field_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_field_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_field_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_field_padding_right', array(
		'default'       => $defaults['betterdocs_search_field_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_field_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_field_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_field_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_field_padding_bottom', array(
		'default'       => $defaults['betterdocs_search_field_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_field_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_field_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_field_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_field_padding_left', array(
		'default'       => $defaults['betterdocs_search_field_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_field_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_field_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_field_padding betterdocs-dimension',
		),
	) ) );

	// Search Field Border Radius

	$wp_customize->add_setting( 'betterdocs_search_field_border_radius', array(
		'default'       => $defaults['betterdocs_search_field_border_radius'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_search_field_border_radius', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_field_border_radius',
		'label'    => __( 'Search Field Border Radius', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Search Icon Size

	$wp_customize->add_setting( 'betterdocs_search_icon_size', array(
		'default'       => $defaults['betterdocs_search_icon_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_search_icon_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_icon_size',
		'label'    => __( 'Search Icon Size', 'betterdocs' ),
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Search Icon Color

	$wp_customize->add_setting( 'betterdocs_search_icon_color' , array(
		'default'     => $defaults['betterdocs_search_icon_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_icon_color',
		array(
			'label'      => __( 'Search Icon Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_icon_color'
		) )
	);

	// Search Icon Hover Color

	$wp_customize->add_setting( 'betterdocs_search_icon_hover_color' , array(
		'default'     => $defaults['betterdocs_search_icon_hover_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_icon_hover_color',
		array(
			'label'      => __( 'Search Icon Hover Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_icon_hover_color'
		) )
	);

	// Close Icon Color

	$wp_customize->add_setting( 'betterdocs_search_close_icon_color' , array(
		'default'     => $defaults['betterdocs_search_close_icon_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_close_icon_color',
		array(
			'label'      => __( 'Close Icon Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_close_icon_color'
		) )
	);

	// Close Icon Border Color

	$wp_customize->add_setting( 'betterdocs_search_close_icon_border_color' , array(
		'default'     => $defaults['betterdocs_search_close_icon_border_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_close_icon_border_color',
		array(
			'label'      => __( 'Close Icon Border Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_close_icon_border_color'
		) )
	);

	// Search Result Settings

	$wp_customize->add_setting('betterdocs_search_result_settings', array(
		'default'           => $defaults['betterdocs_search_result_settings'],
		'capability'    => 'edit_theme_options',
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_search_result_settings', array(
		'label'	      => esc_html__( 'Search Result Settings', 'betterdocs' ),
		'settings'		=> 'betterdocs_search_result_settings',
		'section'  		=> 'betterdocs_live_search_settings'
	)));

	// Search Result Box Width

	$wp_customize->add_setting( 'betterdocs_search_result_width', array(
		'default'       => $defaults['betterdocs_search_result_width'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_search_result_width', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_result_width',
		'label'    => __( 'Search Result Box Width', 'betterdocs' ),
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => '%', //optional suffix
		),
	) ) );

	// Search Result Box Width

	$wp_customize->add_setting( 'betterdocs_search_result_max_width', array(
		'default'       => $defaults['betterdocs_search_result_max_width'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_search_result_max_width', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_result_max_width',
		'label'    => __( 'Search Result Box Maximum Width', 'betterdocs' ),
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 1000,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Search Result Background Color

	$wp_customize->add_setting( 'betterdocs_search_result_background_color' , array(
		'default'     => $defaults['betterdocs_search_result_background_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_result_background_color',
		array(
			'label'      => __( 'Search Result Box Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_result_background_color'
		) )
	);

	// Search Result Border Color

	$wp_customize->add_setting( 'betterdocs_search_result_border_color' , array(
		'default'     => $defaults['betterdocs_search_result_border_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_result_border_color',
		array(
			'label'      => __( 'Search Result Box Border Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_result_border_color'
		) )
	);

	// Search Result Font Size

	$wp_customize->add_setting( 'betterdocs_search_result_item_font_size', array(
		'default'       => $defaults['betterdocs_search_result_item_font_size'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_search_result_item_font_size', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_result_item_font_size',
		'label'    => __( 'Search Result Item Font Size', 'betterdocs' ),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Search Result Item Font Color

	$wp_customize->add_setting( 'betterdocs_search_result_item_font_color' , array(
		'default'     => $defaults['betterdocs_search_result_item_font_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_result_item_font_color',
		array(
			'label'      => __( 'Search Result Item Font Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_result_item_font_color'
		) )
	);

	// Search Result Item Padding

	$wp_customize->add_setting( 'betterdocs_search_result_item_padding', array(
		'default'       => $defaults['betterdocs_search_result_item_padding'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_search_result_item_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_result_item_padding',
		'label'    => __( 'Search Result Item Padding', 'betterdocs' ),
		'input_attrs' => array(
			'id' => 'betterdocs_search_result_item_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_result_item_padding_top', array(
		'default'       => $defaults['betterdocs_search_result_item_padding_top'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_result_item_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_result_item_padding_top',
		'label'    => __( 'Top', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_result_item_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_result_item_padding_right', array(
		'default'       => $defaults['betterdocs_search_result_item_padding_right'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_result_item_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_result_item_padding_right',
		'label'    => __( 'Right', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_result_item_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_result_item_padding_bottom', array(
		'default'       => $defaults['betterdocs_search_result_item_padding_bottom'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_result_item_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_result_item_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_result_item_padding betterdocs-dimension',
		),
	) ) );

	$wp_customize->add_setting( 'betterdocs_search_result_item_padding_left', array(
		'default'       => $defaults['betterdocs_search_result_item_padding_left'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_search_result_item_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_live_search_settings',
		'settings' => 'betterdocs_search_result_item_padding_left',
		'label'    => __( 'Left', 'betterdocs' ),
		'input_attrs' => array(
			'class' => 'betterdocs_search_result_item_padding betterdocs-dimension',
		),
	) ) );

	// Search Result Item Border Color

	$wp_customize->add_setting( 'betterdocs_search_result_item_border_color' , array(
		'default'     => $defaults['betterdocs_search_result_item_border_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_result_item_border_color',
		array(
			'label'      => __( 'Search Result Item Border Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_result_item_border_color'
		) )
	);

	// Search Result Item Hover Font Color

	$wp_customize->add_setting( 'betterdocs_search_result_item_hover_font_color' , array(
		'default'     => $defaults['betterdocs_search_result_item_hover_font_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_result_item_hover_font_color',
		array(
			'label'      => __( 'Search Result Item Hover Font Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_result_item_hover_font_color'
		) )
	);

	// Search Result Item Hover Background Color

	$wp_customize->add_setting( 'betterdocs_search_result_item_hover_background_color' , array(
		'default'     => $defaults['betterdocs_search_result_item_hover_background_color'],
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_search_result_item_hover_background_color',
		array(
			'label'      => __( 'Item Hover Background Color', 'betterdocs' ),
			'section'    => 'betterdocs_live_search_settings',
			'settings'   => 'betterdocs_search_result_item_hover_background_color'
		) )
	);

	

	// Create custom panels

	$wp_customize->add_panel( 'betterdocs_customize_options', array(
		'priority' => 30,
		'theme_supports' => '',
		'title' => __( 'BetterDocs', 'betterdocs' ),
		'description' => __( 'Controls the design settings for the theme.', 'betterdocs' ),
	) );

	// Assign sections to panels
	$wp_customize->get_section('betterdocs_doc_page_settings')->panel = 'betterdocs_customize_options';
	$wp_customize->get_section('betterdocs_single_docs_settings')->panel = 'betterdocs_customize_options';
	$wp_customize->get_section('betterdocs_sidebar_settings')->panel = 'betterdocs_customize_options';
	$wp_customize->get_section('betterdocs_archive_page_settings')->panel = 'betterdocs_customize_options';
	$wp_customize->get_section('betterdocs_live_search_settings')->panel = 'betterdocs_customize_options';


}
add_action( 'customize_register', 'betterdocs_customize_register' );

require_once( BETTERDOCS_ADMIN_DIR_PATH . 'customizer/output-css.php' );
