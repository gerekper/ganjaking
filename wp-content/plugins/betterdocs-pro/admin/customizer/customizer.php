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

	// Category Title Font Size

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

	// Category Icon Size

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
		'label'    => __( 'Category Icon Size', 'betterdocs-pro' ),
		'priority' => 24,
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Category Title Font Size

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
		'label'    => __( 'Article List Title Font Size', 'betterdocs-pro' ),
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

}
add_action( 'customize_register', 'betterdocs_customize_register_pro' );

require_once( BETTERDOCS_PRO_ADMIN_DIR_PATH . 'customizer/output-css.php' );
