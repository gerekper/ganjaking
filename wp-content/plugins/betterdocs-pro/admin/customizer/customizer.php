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
			'title'      => __('Multiple KB','betterdocs-pro'),
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
					'settings' => 'betterdocs_multikb_layout_select',
					'section'  => 'betterdocs_mkb_settings',
					'label'	   => __( 'Select Multiple KB Layout', 'theme-slug' ),
					'priority' => 1,
					'choices'		=> array(
						'layout-1' 	=> array(
							'label' => esc_html__('Grid Layout', 'betterdocs-pro'),
							'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-2.png',
						),
						'layout-2' 	=> array(
							'label' => esc_html__('Box Layout', 'betterdocs-pro'),
							'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-3.png',
						),
                        'layout-3' 	=> array(
							'label' => esc_html__('Card Layout', 'betterdocs-pro'),
							'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-5.png',
						),
                        'layout-4' 	=> array(
							'label' => esc_html__('Tabbed Layout', 'betterdocs-pro'),
							'image' => BETTERDOCS_PRO_ADMIN_URL . 'assets/img/layout-tab-view.png',
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
				'label'      => __( 'Content Area Background Color', 'betterdocs-pro' ),
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
			'label'    => __( 'Background Image', 'betterdocs-pro' ),
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
			'label'    => __( 'Background Property', 'betterdocs-pro' ),
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
			'label'    => __( 'Size', 'betterdocs-pro' ),
			'priority' => 5,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_background_property betterdocs-select',
			),
			'choices'  => array(
				'auto'   	=> __( 'auto', 'betterdocs-pro' ),
				'length'   	=> __( 'length', 'betterdocs-pro' ),
				'cover'   	=> __( 'cover', 'betterdocs-pro' ),
				'contain'   => __( 'contain', 'betterdocs-pro' ),
				'initial'   => __( 'initial', 'betterdocs-pro' ),
				'inherit'   => __( 'inherit', 'betterdocs-pro' )
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
			'label'    => __( 'Repeat', 'betterdocs-pro' ),
			'priority' => 6,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_background_property betterdocs-select',
			),
			'choices'  => array(
				'no-repeat' => __( 'no-repeat', 'betterdocs-pro' ),
				'initial'   => __( 'initial', 'betterdocs-pro' ),
				'inherit'   => __( 'inherit', 'betterdocs-pro' ),
				'repeat'   	=> __( 'repeat', 'betterdocs-pro' ),
				'repeat-x'  => __( 'repeat-x', 'betterdocs-pro' ),
				'repeat-y'  => __( 'repeat-y', 'betterdocs-pro' )
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
			'label'    => __( 'Attachment', 'betterdocs-pro' ),
			'priority' => 7,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_background_property betterdocs-select',
			),
			'choices'  => array(
				'initial' 	=> __( 'initial', 'betterdocs-pro' ),
				'inherit'   => __( 'inherit', 'betterdocs-pro' ),
				'scroll'   	=> __( 'scroll', 'betterdocs-pro' ),
				'fixed'  	 => __( 'fixed', 'betterdocs-pro' ),
				'local'  	=> __( 'local', 'betterdocs-pro' ),
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
			'label'    => __( 'Position', 'betterdocs-pro' ),
			'priority' => 8,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_background_property betterdocs-select',
			),
			'choices'  => array(
				'left top'   	=> __( 'left top', 'betterdocs-pro' ),
				'left center'  => __( 'left center', 'betterdocs-pro' ),
				'left bottom'  => __( 'left bottom', 'betterdocs-pro' ),
				'right top' => __( 'right top', 'betterdocs-pro' ),
				'right center'   => __( 'right center', 'betterdocs-pro' ),
				'right bottom'   => __( 'right bottom', 'betterdocs-pro' ),
				'center top'   => __( 'center top', 'betterdocs-pro' ),
				'center center'   => __( 'center center', 'betterdocs-pro' ),
				'center bottom'   => __( 'center bottom', 'betterdocs-pro' )
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
			'label'    => __( 'Content Area Padding', 'betterdocs-pro' ),
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
			'label'    => __( 'Top', 'betterdocs-pro' ),
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
			'label'    => __( 'Right', 'betterdocs-pro' ),
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
			'label'    => __( 'Bottom', 'betterdocs-pro' ),
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
			'label'    => __( 'Left', 'betterdocs-pro' ),
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
			'label'    => __( 'Content Area Width', 'betterdocs-pro' ),
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
			'label'    => __( 'Content Area Maximum Width', 'betterdocs-pro' ),
			'priority' => 15,
			'input_attrs' => array(
				'class' => 'betterdocs-range-value',
				'min'    => 100,
				'max'    => 1600,
				'step'   => 1,
				'suffix' => 'px', //optional suffix
			),
		) ) );

        /** Tab View List Start **/

        $wp_customize->add_setting('betterdocs_mkb_list_seprator', array(
            'default' 			=> '',
            'sanitize_callback' => 'esc_html',
        ));

        $wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
            $wp_customize, 'betterdocs_mkb_list_seprator', array(
            'label'     => esc_html__('Knowledge Base Tab List', 'betterdocs-pro'),
            'settings'  => 'betterdocs_mkb_list_seprator',
            'section' 	=> 'betterdocs_mkb_settings',
            'priority'  => 17
        )));

        /** MKB Tab List BG Color **/
        $wp_customize->add_setting('betterdocs_mkb_list_bg_color', array(
            'default' 	 		=> $defaults['betterdocs_mkb_list_bg_color'],
            'capability' 		=> 'edit_theme_options',
            'transport'  		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ));

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_list_bg_color',
                array(
                    'label' 	=> __('Tab List Background Color', 'betterdocs-pro'),
                    'section' 	=> 'betterdocs_mkb_settings',
                    'settings' 	=> 'betterdocs_mkb_list_bg_color',
                    'priority' 	=> 17
                ))
        );
        /** MKB Tab List BG Color **/

        /** MKB Tab List BG Hover Color**/
        $wp_customize->add_setting('betterdocs_mkb_list_bg_hover_color', array(
            'default'			=> $defaults['betterdocs_mkb_list_bg_hover_color'],
            'capability' 		=> 'edit_theme_options',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ));

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_list_bg_hover_color',
                array(
                    'label' 	=> __('Tab List Background Hover Color', 'betterdocs-pro'),
                    'section' 	=> 'betterdocs_mkb_settings',
                    'settings' 	=> 'betterdocs_mkb_list_bg_hover_color',
                    'priority' 	=> 17
                ))
        );

        /** MKB Tab List BG Hover Color**/

        /** MKB Tab List Font Color**/

        $wp_customize->add_setting( 'betterdocs_mkb_tab_list_font_color' , array(
            'default'			=> $defaults['betterdocs_mkb_tab_list_font_color'],
            'capability'    	=> 'edit_theme_options',
            'transport'   		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ) );

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_tab_list_font_color',
                array(
                    'label'      => __( 'Tab List Font Color', 'betterdocs-pro' ),
                    'section'    => 'betterdocs_mkb_settings',
                    'settings'   => 'betterdocs_mkb_tab_list_font_color',
                    'priority' 	 => 17
                ) )
        );

        /** MKB Tab List Font Color**/

        /** MKB Tab List Font Color Active**/

        $wp_customize->add_setting( 'betterdocs_mkb_tab_list_font_color_active' , array(
            'default'			=> $defaults['betterdocs_mkb_tab_list_font_color_active'],
            'capability'    	=> 'edit_theme_options',
            'transport'   		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ) );

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_tab_list_font_color_active',
                array(
                    'label'      => __( 'Active Tab List Font Color', 'betterdocs-pro' ),
                    'section'    => 'betterdocs_mkb_settings',
                    'settings'   => 'betterdocs_mkb_tab_list_font_color_active',
                    'priority' 	 => 17
                ) )
        );

        /** MKB Tab List Font Color Active**/

        /** MKB Tab List Active Back Color **/

        $wp_customize->add_setting( 'betterdocs_mkb_tab_list_back_color_active' , array(
            'default'			=> $defaults['betterdocs_mkb_tab_list_back_color_active'],
            'capability'    	=> 'edit_theme_options',
            'transport'   		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ) );

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_tab_list_back_color_active',
                array(
                    'label'      => __( 'Active Tab List Background Color', 'betterdocs-pro' ),
                    'section'    => 'betterdocs_mkb_settings',
                    'settings'   => 'betterdocs_mkb_tab_list_back_color_active',
                    'priority' 	 => 17
                ) )
        );

        /** MKB Tab List Active Back Color **/



        /** MKB Tab List Font Size **/

        $wp_customize->add_setting('betterdocs_mkb_list_font_size', array(
            'default' 	 		=> $defaults['betterdocs_mkb_list_font_size'],
            'capability' 		=> 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
            $wp_customize, 'betterdocs_mkb_list_font_size', array(
            'type' 		=> 'betterdocs-range-value',
            'section' 	=> 'betterdocs_mkb_settings',
            'settings'  => 'betterdocs_mkb_list_font_size',
            'label' 	=> __('Tab List Font Size', 'betterdocs-pro'),
            'priority'  => 17,
            'input_attrs' => array(
                'class'   => '',
                'min'     => 0,
                'max'	  => 50,
                'step' 	  => 1,
                'suffix'  => 'px', //optional suffix
            ),
        )));

        /** MKB Tab List Font Size **/

        /** Tab List Padding **/

        $wp_customize->add_setting( 'betterdocs_mkb_list_column_padding', array(
            'default'       	=> '',
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ) );

        $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
            $wp_customize, 'betterdocs_mkb_list_column_padding', array(
            'type'     => 'betterdocs-title',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_list_column_padding',
            'label'    => __( 'Tab List Padding', 'betterdocs-pro' ),
            'priority' => 17,
            'input_attrs' => array(
                'id'	  => 'betterdocs_mkb_list_column_padding',
                'class'   => 'betterdocs-dimension',
            ),
        ) ) );

        $wp_customize->add_setting( 'betterdocs_mkb_list_column_padding_top', array(
            'default'       	=> $defaults['betterdocs_mkb_list_column_padding_top'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) );

        $wp_customize->add_control( new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_list_column_padding_top', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_list_column_padding_top',
            'label'    => __( 'Top', 'betterdocs-pro' ),
            'priority' => 17,
            'input_attrs' => array(
                'class'   => 'betterdocs_mkb_list_column_padding betterdocs-dimension',
            ),
        ) ) );

        $wp_customize->add_setting( 'betterdocs_mkb_list_column_padding_right', array(
            'default'       	=> $defaults['betterdocs_mkb_list_column_padding_right'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) );

        $wp_customize->add_control( new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_list_column_padding_right', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_list_column_padding_right',
            'label'    => __( 'Right', 'betterdocs-pro' ),
            'priority' => 17,
            'input_attrs' => array(
                'class' => 'betterdocs_mkb_list_column_padding betterdocs-dimension',
            ),
        ) ) );

        $wp_customize->add_setting( 'betterdocs_mkb_list_column_padding_bottom', array(
            'default'       	=> $defaults['betterdocs_mkb_list_column_padding_bottom'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ) );

        $wp_customize->add_control( new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_list_column_padding_bottom', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_list_column_padding_bottom',
            'label'    => __( 'Bottom', 'betterdocs-pro' ),
            'priority' => 17,
            'input_attrs' => array(
                'class'   => 'betterdocs_mkb_list_column_padding betterdocs-dimension',
            ),
        ) ) );

        $wp_customize->add_setting( 'betterdocs_mkb_list_column_padding_left', array(
            'default'       	=> $defaults['betterdocs_mkb_list_column_padding_left'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) );

        $wp_customize->add_control( new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_list_column_padding_left', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_list_column_padding_left',
            'label'    => __( 'Left', 'betterdocs-pro' ),
            'priority' => 17,
            'input_attrs' => array(
                'class' => 'betterdocs_mkb_list_column_padding betterdocs-dimension',
            ),
        ) ) );

        /** Tab List Padding **/

        /** Tab List Margin **/

        $wp_customize->add_setting('betterdocs_mkb_tab_list_margin', array(
            'default' 	 		=> '',
            'capability'		=> 'edit_theme_options',
            'transport'  		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Title_Custom_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_margin', array(
            'type' 	   => 'betterdocs-title',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_margin',
            'label'    => __('Tab List Margin', 'betterdocs-pro'),
            'priority' => 17,
            'input_attrs' => array(
                'id'	=> 'betterdocs_mkb_tab_list_margin',
                'class' => 'betterdocs-dimension',
            ),
        )));

        $wp_customize->add_setting('betterdocs_mkb_tab_list_margin_top', array(
            'default' 			=> $defaults['betterdocs_mkb_tab_list_margin_top'],
            'capability' 		=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ));

        $wp_customize->add_control(new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_margin_top', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_margin_top',
            'label'    => __('Top', 'betterdocs-pro'),
            'priority' => 17,
            'input_attrs' => array(
                'class'   => 'betterdocs_mkb_tab_list_margin betterdocs-dimension',
            ),
        )));

        $wp_customize->add_setting('betterdocs_mkb_tab_list_margin_right', array(
            'default' 	 	    => $defaults['betterdocs_mkb_tab_list_margin_right'],
            'capability' 		=> 'edit_theme_options',
            'transport'         => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_margin_right', array(
            'type' 	   => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_margin_right',
            'label'    => __('Right', 'betterdocs-pro'),
            'priority' => 17,
            'input_attrs' => array(
                'class'   => 'betterdocs_mkb_tab_list_margin betterdocs-dimension',
            ),
        )));

        $wp_customize->add_setting('betterdocs_mkb_tab_list_margin_bottom', array(
            'default'    		=> $defaults['betterdocs_mkb_tab_list_margin_bottom'],
            'capability' 		=> 'edit_theme_options',
            'transport'  		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_margin_bottom', array(
            'type' 	   => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_margin_bottom',
            'label'    => __('Bottom', 'betterdocs-pro'),
            'priority' => 17,
            'input_attrs' => array(
                'class'   => 'betterdocs_mkb_tab_list_margin betterdocs-dimension',
            ),
        )));

        $wp_customize->add_setting('betterdocs_mkb_tab_list_margin_left', array(
            'default' 	 		=> $defaults['betterdocs_mkb_tab_list_margin_left'],
            'capability' 		=> 'edit_theme_options',
            'transport'  		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_margin_left', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_margin_left',
            'label'    => __('Left', 'betterdocs-pro'),
            'priority' => 17,
            'input_attrs' => array(
                'class'   => 'betterdocs_mkb_tab_list_margin betterdocs-dimension',
            ),
        )));

        /** Tab List Margin **/

        /** Tab List Border Radius **/

        $wp_customize->add_setting( 'betterdocs_mkb_tab_list_border', array(
            'default'       	=> '',
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ) );

        $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_border', array(
            'type'     => 'betterdocs-title',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_border',
            'label'    => __( 'Tab List Border Radius', 'betterdocs-pro' ),
            'priority' => 17,
            'input_attrs' => array(
                'id' 	  => 'betterdocs_mkb_tab_list_border',
                'class'   => 'betterdocs-dimension',
            ),
        ) ) );

        $wp_customize->add_setting( 'betterdocs_mkb_tab_list_border_topleft', array(
            'default'       	=> $defaults['betterdocs_mkb_tab_list_border_topleft'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer',
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_tab_list_border betterdocs-dimension',
			),
        ) );

        $wp_customize->add_control( new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_border_topleft', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_border_topleft',
            'label'    => __( 'Top Left', 'betterdocs-pro' ),
            'priority' => 17,
            'input_attrs' => array(
                'class'   => 'betterdocs_mkb_tab_list_border betterdocs-dimension',
            ),
        ) ) );

        $wp_customize->add_setting( 'betterdocs_mkb_tab_list_border_topright', array(
            'default'       	=> $defaults['betterdocs_mkb_tab_list_border_topright'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ) );

        $wp_customize->add_control( new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_border_topright', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_border_topright',
            'label'    => __( 'Top Right', 'betterdocs-pro' ),
            'priority' => 17,
            'input_attrs' => array(
                'class'   => 'betterdocs_mkb_tab_list_border betterdocs-dimension',
            ),
        ) ) );

        $wp_customize->add_setting( 'betterdocs_mkb_tab_list_border_bottomright', array(
            'default'       	=> $defaults['betterdocs_mkb_tab_list_border_bottomright'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ) );

        $wp_customize->add_control( new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_border_bottomright', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_border_bottomright',
            'label'    => __( 'Bottom Right', 'betterdocs-pro' ),
            'priority' => 17,
			'input_attrs' => array(
                'class'   => 'betterdocs_mkb_tab_list_border betterdocs-dimension',
            ),
        ) ) );

        $wp_customize->add_setting( 'betterdocs_mkb_tab_list_border_bottomleft', array(
            'default'       	=> $defaults['betterdocs_mkb_tab_list_border_bottomleft'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ) );

        $wp_customize->add_control( new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_tab_list_border_bottomleft', array(
            'type'     => 'betterdocs-dimension',
            'section'  => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_tab_list_border_bottomleft',
            'label'    => __( 'Bottom Left', 'betterdocs-pro' ),
            'priority' => 17,
            'input_attrs' => array(
                'class'   => 'betterdocs_mkb_tab_list_border betterdocs-dimension',
            ),
        ) ) );

        /** Tab List Border Radius**/

        /** Tab View List End **/

        /** Category Column Settings MKB **/

        $wp_customize->add_setting('betterdocs_mkb_category_column_list_seprator', array(
            'default' 			=> '',
            'sanitize_callback' => 'esc_html',
        ));

        $wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
            $wp_customize, 'betterdocs_mkb_category_column_list_seprator', array(
            'label'     => esc_html__('Category Column Settings', 'betterdocs-pro'),
            'settings'  => 'betterdocs_mkb_category_column_list_seprator',
            'section' 	=> 'betterdocs_mkb_settings',
            'priority'  => 17
        )));

		/** Category Column Settings MKB  **/

        $wp_customize->add_setting( 'betterdocs_mkb_title_tag' , array(
            'default'     => $defaults['betterdocs_mkb_title_tag'],
            'capability'    => 'edit_theme_options',
            'sanitize_callback' => 'betterdocs_sanitize_choices',
        ) );

        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'betterdocs_mkb_title_tag',
                array(
                    'label'      => __( 'Category Title Tag', 'betterdocs-pro' ),
                    'section'    => 'betterdocs_mkb_settings',
                    'settings'   => 'betterdocs_mkb_title_tag',
                    'type'    => 'select',
                    'choices' => array(
                        'h1' => 'h1',
                        'h2' => 'h2',
                        'h3' => 'h3',
                        'h4' => 'h4',
                        'h5' => 'h5',
                        'h6' => 'h6'
                    ),
                    'priority' => 17,
                ) )
        );

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
			'label'    => __( 'Spacing Between Columns', 'betterdocs-pro' ),
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
				'label'      => __( 'Column Background Color', 'betterdocs-pro' ),
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
				'label'      => __( 'Column Background Hover Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_column_hover_bg_color',
				'priority' => 18
			) )
		);

		// Column Padding

		$wp_customize->add_setting( 'betterdocs_mkb_column_padding', array(
			'default'       => $defaults['betterdocs_mkb_column_padding'],
			'capability'    => 'edit_theme_options',
			'transport' 	=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
			$wp_customize, 'betterdocs_mkb_column_padding', array(
			'type'     => 'betterdocs-title',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_padding',
			'label'    => __( 'Column Padding', 'betterdocs-pro' ),
			'priority' => 18,
			'input_attrs' => array(
				'id' => 'betterdocs_mkb_column_padding',
				'class' => 'betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_column_padding_top', array(
			'default'       	=> $defaults['betterdocs_mkb_column_padding_top'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_padding_top', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_padding_top',
			'label'    => __( 'Top', 'betterdocs-pro' ),
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
			'label'    => __( 'Right', 'betterdocs-pro' ),
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
			'label'    => __( 'Bottom', 'betterdocs-pro' ),
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
			'label'    => __( 'Left', 'betterdocs-pro' ),
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
			'label'    => __( 'Icon Size', 'betterdocs-pro' ),
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
			'label'    => __( 'Column Border Radius', 'betterdocs-pro' ),
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
			'label'    => __( 'Top Left', 'betterdocs-pro' ),
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
			'label'    => __( 'Top Right', 'betterdocs-pro' ),
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
			'label'    => __( 'Bottom Right', 'betterdocs-pro' ),
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
			'label'    => __( 'Bottom Left', 'betterdocs-pro' ),
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
			'label'    => __( 'Title Font Size', 'betterdocs-pro' ),
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
				'label'      => __( 'Title Color', 'betterdocs-pro' ),
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
				'label'      => __( 'Title Hover Color', 'betterdocs-pro' ),
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
				'label'      => __( 'Item Count Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_item_count_color',
				'priority' => 29
			) )
		);

		$wp_customize->add_setting( 'betterdocs_mkb_item_count_color_hover' , array(
			'default'     		=> $defaults['betterdocs_mkb_item_count_color_hover'],
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );
	
		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_item_count_color_hover',
			array(
				'label'      => __( 'Item Count Hover Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_item_count_color_hover',
				'priority'   => 29
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
			'label'    => __( 'Font Size', 'betterdocs-pro' ),
			'priority' => 30,
			'input_attrs' => array(
				'class'  => '',
				'min'    => 0,
				'max'    => 50,
				'step'   => 1,
				'suffix'  => 'px', //optional suffix
			),
		) ) );

		// KB Description

		$wp_customize->add_setting('betterdocs_mkb_desc', array(
			'default' => $defaults['betterdocs_mkb_desc'],
			'capability' => 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_checkbox',
		));

		$wp_customize->add_control(new BetterDocs_Customizer_Toggle_Control(
			$wp_customize, 'betterdocs_mkb_desc', array(
			'label' => esc_html__('KB Description', 'betterdocs-pro'),
			'section' => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_desc',
			'type' => 'light', // light, ios, flat
			'priority' => 28
		)));

		// Category Description Color

		$wp_customize->add_setting( 'betterdocs_mkb_desc_color' , array(
			'default'     => $defaults['betterdocs_mkb_desc_color'],
			'capability'    => 'edit_theme_options',
			'transport'   => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_desc_color',
			array(
				'label'      => __( 'KB Description Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_desc_color',
				'priority' => 28
			) )
		);

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
			'label'    => __( 'Content Space Between', 'betterdocs-pro' ),
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
			'label'    => __( 'Icon', 'betterdocs-pro' ),
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
			'label'    => __( 'Title', 'betterdocs-pro' ),
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
			'label'    => __( 'Description', 'betterdocs-pro' ),
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
			'label'    => __( 'Counter', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_column_content_space betterdocs-dimension',
			),
		) ) );

		

		/** Docs List Start**/

		$wp_customize->add_setting('betterdocs_mkb_column_list_heading', array(
			'default'           => '',
			'sanitize_callback' => 'esc_html',
		));
	
		$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
			$wp_customize, 'betterdocs_mkb_column_list_heading', array(
			'label'	    => esc_html__( 'Category Column List', 'betterdocs-pro' ),
			'settings'	=> 'betterdocs_mkb_column_list_heading',
			'section'  	=> 'betterdocs_mkb_settings',
			'priority'  => 33
		)));

		// Docs List Color
	
		$wp_customize->add_setting( 'betterdocs_mkb_column_list_color' , array(
			'default'     		=> $defaults['betterdocs_mkb_column_list_color'],
			'capability'    	=> 'edit_theme_options',
			'transport'   	    => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );
	
		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_column_list_color',
			array(
				'label'      => __( 'Docs List Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_column_list_color',
				'priority' 	 => 33
			) )
		);
	
		// Docs List Hover Color
	
		$wp_customize->add_setting( 'betterdocs_mkb_column_list_hover_color' , array(
			'default'     		=> $defaults['betterdocs_mkb_column_list_hover_color'],
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );
	
		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_column_list_hover_color',
			array(
				'label'      => __( 'Docs List Hover Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_column_list_hover_color',
				'priority'   => 33
			) )
		);
	
		// Docs List Font Size
	
		$wp_customize->add_setting( 'betterdocs_mkb_column_list_font_size', array(
			'default'      	 	=> $defaults['betterdocs_mkb_column_list_font_size'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
	
		) );
	
		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_mkb_column_list_font_size', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_list_font_size',
			'label'    => __( 'Docs List Font Size', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class'  => '',
				'min'    => 0,
				'max'    => 50,
				'step'   => 1,
				'suffix'  => 'px', //optional suffix
			),
		) ) );
	
		// Docs List margin
	
		$wp_customize->add_setting( 'betterdocs_mkb_column_list_margin', array(
			'default'       	=> '',
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
	
		) );
	
		$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
			$wp_customize, 'betterdocs_mkb_column_list_margin', array(
			'type'     => 'betterdocs-title',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_list_margin',
			'label'    => __( 'Docs List Margin', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'id' 	=> 'betterdocs_doc_page_article_list_margin',
				'class' => 'betterdocs-dimension',
			),
		) ) );
	
		$wp_customize->add_setting( 'betterdocs_mkb_column_list_margin_top', array(
			'default'       	=> $defaults['betterdocs_mkb_column_list_margin_top'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) );
	
		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_list_margin_top', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_list_margin_top',
			'label'    => __( 'Top', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension',
			),
		) ) );
	
		$wp_customize->add_setting( 'betterdocs_mkb_column_list_margin_right', array(
			'default'       	=> $defaults['betterdocs_mkb_column_list_margin_right'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
	
		) );
	
		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_column_list_margin_right', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_column_list_margin_right',
			'label'    => __( 'Right', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension',
			),
		) ) );
	
		$wp_customize->add_setting( 'betterdocs_mkb_list_margin_bottom', array(
			'default'       	=> $defaults['betterdocs_mkb_list_margin_bottom'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
	
		) );
	
		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_list_margin_bottom', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_list_margin_bottom',
			'label'    => __( 'Bottom', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class'   => 'betterdocs_doc_page_article_list_margin betterdocs-dimension',
			),
		) ) );
	
		$wp_customize->add_setting( 'betterdocs_mkb_list_margin_left', array(
			'default'       	=> $defaults['betterdocs_mkb_list_margin_left'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
	
		) );
	
		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_list_margin_left', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_list_margin_left',
			'label'    => __( 'Left', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_doc_page_article_list_margin betterdocs-dimension',
			),
		) ) );

		/** Docs List End**/

		/** Explore More Button Start **/

		$wp_customize->add_setting('betterdocs_mkb_tab_list_explore_btn', array(
			'default'           => $defaults['betterdocs_mkb_tab_list_explore_btn'],
			'sanitize_callback' => 'esc_html',
		));

		$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn', array(
			'label'	   => esc_html__( 'Explore More Button', 'betterdocs-pro' ),
			'settings' => 'betterdocs_mkb_tab_list_explore_btn',
			'section'  => 'betterdocs_mkb_settings',
			'priority' => 33
		)));

		// Explore More Button Background Color

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_bg_color' , array(
			'default'     		=> $defaults['betterdocs_mkb_tab_list_explore_btn_bg_color'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_tab_list_explore_btn_bg_color',
			array(
				'label'      => __( 'Button Background Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_tab_list_explore_btn_bg_color',
				'priority'   => 33
			) )
		);

		// Explore More Button Color

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_color' , array(
			'default'     		=> $defaults['betterdocs_mkb_tab_list_explore_btn_color'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_tab_list_explore_btn_color',
			array(
				'label'      => __( 'Button Text Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_tab_list_explore_btn_color',
				'priority'   => 33
			) )
		);

		// Explore More Button Border Color

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_border_color' , array(
			'default'     		=> $defaults['betterdocs_mkb_tab_list_explore_btn_border_color'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_tab_list_explore_btn_border_color',
			array(
				'label'      => __( 'Button Border Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_tab_list_explore_btn_border_color',
				'priority' 	 => 33
			) )
		);

		// Explore More Button Hover Background Color

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_hover_bg_color' , array(
			'default'     		=> $defaults['betterdocs_mkb_tab_list_explore_btn_hover_bg_color'],
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_tab_list_explore_btn_hover_bg_color',
			array(
				'label'      => __( 'Button Background Hover Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_tab_list_explore_btn_hover_bg_color',
				'priority'   => 33
			) )
		);

		// Explore More Button Hover Color

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_hover_color' , array(
			'default'     		=> $defaults['betterdocs_mkb_tab_list_explore_btn_hover_color'],
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_tab_list_explore_btn_hover_color',
			array(
				'label'      => __( 'Button Text Hover Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_tab_list_explore_btn_hover_color',
				'priority'   => 33
			) )
		);

		// Explore More Button Hover Border Color

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_hover_border_color' , array(
			'default'     		=> $defaults['betterdocs_mkb_tab_list_explore_btn_hover_border_color'],
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_mkb_tab_list_explore_btn_hover_border_color',
			array(
				'label'      => __( 'Button Border Hover Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_mkb_settings',
				'settings'   => 'betterdocs_mkb_tab_list_explore_btn_hover_border_color',
				'priority'   => 33
			) )
		);

		// Explore More Button Font Size

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_font_size', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_font_size'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_font_size', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_font_size',
			'label'    => __( 'Button Font Size', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class'  => '',
				'min'    => 0,
				'max'    => 50,
				'step'   => 1,
				'suffix' => 'px', //optional suffix
			),
		) ) );

		// Explore More Button Padding

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_padding'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_padding', array(
			'type'     => 'betterdocs-title',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_padding',
			'label'    => __( 'Button Padding', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'id' 	=> 'betterdocs_doc_page_explore_btn_padding',
				'class' => 'betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding_top', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_padding_top'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_padding_top', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_padding_top',
			'label'    => __( 'Top', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding_right', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_padding_right'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_padding_right', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_padding_right',
			'label'    => __( 'Right', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding_bottom', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_padding_bottom'],
			'capability'    	=> 'edit_theme_options',
			'transport'	 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_padding_bottom', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_padding_bottom',
			'label'    => __( 'Bottom', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_padding_left', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_padding_left'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_padding_left', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_padding_left',
			'label'    => __( 'Left', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_doc_page_explore_btn_padding betterdocs-dimension',
			),
		) ) );

		// explore more button border radius 

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_borderr'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_borderr', array(
			'type'     => 'betterdocs-title',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_borderr',
			'label'    => __( 'Button Border Radius', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'id' 	=> 'betterdocs_mkb_tab_list_explore_btn_borderr',
				'class' => 'betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr_topleft', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_borderr_topleft'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_borderr_topleft', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_borderr_topleft',
			'label'    => __( 'Top Left', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_tab_list_explore_btn_borderr betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr_topright', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_borderr_topright'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_borderr_topright', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_borderr_topright',
			'label'    => __( 'Top Right', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_tab_list_explore_btn_borderr betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomright', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_borderr_bottomright'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomright', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomright',
			'label'    => __( 'Bottom Right', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_tab_list_explore_btn_borderr betterdocs-dimension',
			),
		) ) );

		$wp_customize->add_setting( 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft', array(
			'default'       	=> $defaults['betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft', array(
			'type'     => 'betterdocs-dimension',
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_tab_list_explore_btn_borderr_bottomleft',
			'label'    => __( 'Bottom Left', 'betterdocs-pro' ),
			'priority' => 33,
			'input_attrs' => array(
				'class' => 'betterdocs_mkb_tab_list_explore_btn_borderr betterdocs-dimension',
			),
		) ) );

		/** Explore More Button End **/

        // Popular List

        $wp_customize->add_setting('betterdocs_mkb_popular_list_settings', array(
            'default' => $defaults['betterdocs_mkb_popular_list_settings'],
            'sanitize_callback' => 'esc_html',
        ));

        $wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
            $wp_customize, 'betterdocs_mkb_popular_list_settings', array(
            'label' => esc_html__('Popular Docs', 'betterdocs-pro'),
            'settings' => 'betterdocs_mkb_popular_list_settings',
            'section' => 'betterdocs_mkb_settings',
            'priority' => 34
        )));

		// Popular Docs On/Off(MKB)
		$wp_customize->add_setting('betterdocs_mkb_popular_docs_switch', array(
			'default' 			=> $defaults['betterdocs_mkb_popular_docs_switch'],
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_checkbox',
		));
	
		$wp_customize->add_control(new BetterDocs_Customizer_Toggle_Control(
			$wp_customize, 'betterdocs_mkb_popular_docs_switch', array(
			'label'    => esc_html__('Popular Docs Show', 'betterdocs-pro'),
			'section'  => 'betterdocs_mkb_settings',
			'settings' => 'betterdocs_mkb_popular_docs_switch',
			'type'     => 'light', // light, ios, flat
			'priority' => 34
		)));


        // Docs List Background Color(MKB)
        $wp_customize->add_setting('betterdocs_mkb_popular_list_bg_color', array(
            'default' => $defaults['betterdocs_mkb_popular_list_bg_color'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ));

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_popular_list_bg_color',
                array(
                    'label' => __('Popular Docs Background Color', 'betterdocs-pro'),
                    'section' => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_list_bg_color',
                    'priority' => 34
                ))
        );

        // Docs List Color(MKB)
        $wp_customize->add_setting('betterdocs_mkb_popular_list_color', array(
            'default' => $defaults['betterdocs_mkb_popular_list_color'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ));

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_popular_list_color',
                array(
                    'label' => __('Popular Docs List Color', 'betterdocs-pro'),
                    'section' => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_list_color',
                    'priority' => 35
                ))
        );

        // Docs List Hover Color(MKB)
        $wp_customize->add_setting('betterdocs_mkb_popular_list_hover_color', array(
            'default' => $defaults['betterdocs_mkb_popular_list_hover_color'],
            'capability' => 'edit_theme_options',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ));

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_popular_list_hover_color',
                array(
                    'label' => __('Popular Docs List Hover Color', 'betterdocs-pro'),
                    'section' => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_list_hover_color',
                    'priority' => 36
                ))
        );

        // Docs List Font Size(MKB)
        $wp_customize->add_setting('betterdocs_mkb_popular_list_font_size', array(
            'default' => $defaults['betterdocs_mkb_popular_list_font_size'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
            $wp_customize, 'betterdocs_mkb_popular_list_font_size', array(
            'type' => 'betterdocs-range-value',
            'section' => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_popular_list_font_size',
            'label' => __('Popular Docs List Font Size', 'betterdocs-pro'),
            'priority' => 37,
            'input_attrs' => array(
                'class' => '',
                'min' => 0,
                'max' => 50,
                'step' => 1,
                'suffix' => 'px', //optional suffix
            ),
        )));

		//Popular Title Font Size(MKB)
        $wp_customize->add_setting('betterdocs_mkb_popular_title_font_size', array(
            'default' => $defaults['betterdocs_mkb_popular_title_font_size'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
            $wp_customize, 'betterdocs_mkb_popular_title_font_size', array(
            'type' => 'betterdocs-range-value',
            'section' => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_popular_title_font_size',
            'label' => __('Popular Title Font Size', 'betterdocs-pro'),
            'priority' => 37,
            'input_attrs' => array(
                'class' => '',
                'min' => 0,
                'max' => 50,
                'step' => 1,
                'suffix' => 'px', //optional suffix
            ),
        )));

		// Popular Title Color(MKB)
        $wp_customize->add_setting('betterdocs_mkb_popular_title_color', array(
            'default' 			=> $defaults['betterdocs_mkb_popular_title_color'],
            'capability' 		=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ));

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_popular_title_color',
                array(
                    'label'    => __('Popular Title Color', 'betterdocs-pro'),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_title_color',
                    'priority' => 38
                ))
        );

		// Popular Title Color Hover(MKB)
        $wp_customize->add_setting('betterdocs_mkb_popular_title_color_hover', array(
            'default' 			=> $defaults['betterdocs_mkb_popular_title_color_hover'],
            'capability' 		=> 'edit_theme_options',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ));

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_popular_title_color_hover',
                array(
                    'label'    => __('Popular Title Color Hover', 'betterdocs-pro'),
                    'section'  => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_title_color_hover',
                    'priority' => 38
                ))
        );

        // List Icon Color(MKB)
        $wp_customize->add_setting('betterdocs_mkb_popular_list_icon_color', array(
            'default' => $defaults['betterdocs_mkb_popular_list_icon_color'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_rgba',
        ));

        $wp_customize->add_control(
            new BetterDocs_Customizer_Alpha_Color_Control(
                $wp_customize,
                'betterdocs_mkb_popular_list_icon_color',
                array(
                    'label' => __('Popular List Icon Color', 'betterdocs-pro'),
                    'section' => 'betterdocs_mkb_settings',
                    'settings' => 'betterdocs_mkb_popular_list_icon_color',
                    'priority' => 38
                ))
        );

        // List Icon Font Size(MKB)
        $wp_customize->add_setting('betterdocs_mkb_popular_list_icon_font_size', array(
            'default' => $defaults['betterdocs_mkb_popular_list_icon_font_size'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
            $wp_customize, 'betterdocs_mkb_popular_list_icon_font_size', array(
            'type' => 'betterdocs-range-value',
            'section' => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_popular_list_icon_font_size',
            'label' => __('Popular List Icon Font Size', 'betterdocs-pro'),
            'priority' => 39,
            'input_attrs' => array(
                'class' => '',
                'min' => 0,
                'max' => 50,
                'step' => 1,
                'suffix' => 'px', //optional suffix
            ),
        )));

      // Docs List margin(MKB)
      $wp_customize->add_setting('betterdocs_mkb_popular_list_margin', array(
            'default' => $defaults['betterdocs_mkb_popular_list_margin'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Title_Custom_Control(
            $wp_customize, 'betterdocs_mkb_popular_list_margin', array(
            'type' => 'betterdocs-title',
            'section' => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_popular_list_margin',
            'label' => __('Popular Docs List Margin', 'betterdocs-pro'),
            'priority' => 40,
            'input_attrs' => array(
                'id' => 'betterdocs_mkb_popular_list_margin',
                'class' => 'betterdocs-dimension',
            ),
        )));

        $wp_customize->add_setting('betterdocs_mkb_popular_list_margin_top', array(
            'default' => $defaults['betterdocs_mkb_popular_list_margin_top'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ));

        $wp_customize->add_control(new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_popular_list_margin_top', array(
            'type' => 'betterdocs-dimension',
            'section' => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_popular_list_margin_top',
            'label' => __('Top', 'betterdocs-pro'),
            'priority' => 41,
            'input_attrs' => array(
                'class' => 'betterdocs_mkb_popular_list_margin betterdocs-dimension',
            ),
        )));

        $wp_customize->add_setting('betterdocs_mkb_popular_list_margin_right', array(
            'default' => $defaults['betterdocs_mkb_popular_list_margin_right'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_popular_list_margin_right', array(
            'type' => 'betterdocs-dimension',
            'section' => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_popular_list_margin_right',
            'label' => __('Right', 'betterdocs-pro'),
            'priority' => 42,
            'input_attrs' => array(
                'class' => 'betterdocs_mkb_popular_list_margin betterdocs-dimension',
            ),
        )));

        $wp_customize->add_setting('betterdocs_mkb_popular_list_margin_bottom', array(
            'default' => $defaults['betterdocs_mkb_popular_list_margin_bottom'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_popular_list_margin_bottom', array(
            'type' => 'betterdocs-dimension',
            'section' => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_popular_list_margin_bottom',
            'label' => __('Bottom', 'betterdocs-pro'),
            'priority' => 43,
            'input_attrs' => array(
                'class' => 'betterdocs_mkb_popular_list_margin betterdocs-dimension',
            ),
        )));

        $wp_customize->add_setting('betterdocs_mkb_popular_list_margin_left', array(
            'default' => $defaults['betterdocs_mkb_popular_list_margin_left'],
            'capability' => 'edit_theme_options',
            'transport' => 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'

        ));

        $wp_customize->add_control(new BetterDocs_Dimension_Control(
            $wp_customize, 'betterdocs_mkb_popular_list_margin_left', array(
            'type' => 'betterdocs-dimension',
            'section' => 'betterdocs_mkb_settings',
            'settings' => 'betterdocs_mkb_popular_list_margin_left',
            'label' => __('Left', 'betterdocs-pro'),
            'priority' => 44,
            'input_attrs' => array(
                'class' => 'betterdocs_mkb_popular_list_margin betterdocs-dimension',
            ),
        )));
	}

    //Popular Title Margin (MKB)
    $wp_customize->add_setting('betterdocs_mkb_popular_title_margin', array(
		'default' => $defaults['betterdocs_mkb_popular_title_margin'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_mkb_popular_title_margin', array(
		'type' => 'betterdocs-title',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_title_margin',
		'label' => __('Popular Docs Title Margin', 'betterdocs-pro'),
		'priority' => 39,
		'input_attrs' => array(
			'id' => 'betterdocs_mkb_popular_title_margin',
			'class' => 'betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_mkb_popular_title_margin_top', array(
		'default' => $defaults['betterdocs_mkb_popular_title_margin_top'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_mkb_popular_title_margin_top', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_title_margin_top',
		'label' => __('Top', 'betterdocs-pro'),
		'priority' => 39,
		'input_attrs' => array(
			'class' => 'betterdocs_mkb_popular_title_margin betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_mkb_popular_title_margin_right', array(
		'default' => $defaults['betterdocs_mkb_popular_title_margin_right'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_mkb_popular_title_margin_right', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_title_margin_right',
		'label' => __('Right', 'betterdocs-pro'),
		'priority' => 39,
		'input_attrs' => array(
			'class' => 'betterdocs_mkb_popular_title_margin betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_mkb_popular_title_margin_bottom', array(
		'default' => $defaults['betterdocs_mkb_popular_title_margin_bottom'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_mkb_popular_title_margin_bottom', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_title_margin_bottom',
		'label' => __('Bottom', 'betterdocs-pro'),
		'priority' => 39,
		'input_attrs' => array(
			'class' => 'betterdocs_mkb_popular_title_margin betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_mkb_popular_title_margin_left', array(
		'default' => $defaults['betterdocs_mkb_popular_title_margin_left'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_mkb_popular_title_margin_left', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_title_margin_left',
		'label' => __('Left', 'betterdocs-pro'),
		'priority' => 39,
		'input_attrs' => array(
			'class' => 'betterdocs_mkb_popular_title_margin betterdocs-dimension',
		),
	)));
	
	// Category Image (Doc Page Layout 6)
	$wp_customize->add_setting(
		'betterdocs_doc_list_img_switch_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_img_switch_layout6'],
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Toggle_Control(
		$wp_customize, 'betterdocs_doc_list_img_switch_layout6', 
			array(
				'label' 	=> esc_html__('Category Image', 'betterdocs-pro'),
				'section' 	=> 'betterdocs_doc_page_settings',
				'settings' 	=> 'betterdocs_doc_list_img_switch_layout6',
				'type' 		=> 'light', // light, ios, flat
				'priority' 	=> 17
			)
		)
	);

	// Category Image Width(Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_list_img_width_layout6', array(
		'default'       	=> $defaults['betterdocs_doc_list_img_width_layout6'],
		'transport'   		=> 'postMessage',
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_list_img_width_layout6', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_list_img_width_layout6',
		'label'    => esc_html__('Category Image Width', 'betterdocs-pro'),
		'priority' => 17,
		'input_attrs' => array(
			'class' => 'betterdocs-range-value',
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => '%', //optional suffix
		),
	) ) );

	// Category Title Padding Bottom (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_page_cat_title_padding_bottom_layout6', array(
			'default'       	=> $defaults['betterdocs_doc_page_cat_title_padding_bottom_layout6'],
			'transport'   		=> 'postMessage',
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_cat_title_padding_bottom_layout6', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_cat_title_padding_bottom_layout6',
		'label'    => esc_html__('Category Title Padding Bottom', 'betterdocs-pro'),
		'priority' => 17,
		'input_attrs' => array(
			'class' => 'betterdocs-range-value',
			'min'    => 0,
			'max'    => 500,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );
	
	// Category Title Font Size (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_page_cat_title_font_size_layout6', array(
			'default'       	=> $defaults['betterdocs_doc_page_cat_title_font_size_layout6'],
			'transport'   		=> 'postMessage',
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_cat_title_font_size_layout6', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_cat_title_font_size_layout6',
		'label'    => esc_html__('Category Title Font Size', 'betterdocs-pro'),
		'priority' => 17,
		'input_attrs' => array(
			'class' => 'betterdocs-range-value',
			'min'    => 0,
			'max'    => 500,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );

	// Item Count Font Size (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_font_size_layout6', array(
			'default'       	=> $defaults['betterdocs_doc_page_item_count_font_size_layout6'],
			'transport'   		=> 'postMessage',
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_item_count_font_size_layout6', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_item_count_font_size_layout6',
		'label'    => esc_html__('Item Count Font Size', 'betterdocs-pro'),
		'priority' => 28,
		'input_attrs' => array(
			'class' => 'betterdocs-range-value',
			'min'    => 0,
			'max'    => 500,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
	) ) );


	// Item Count Color (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_doc_page_item_count_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_item_count_color_layout6',
		array(
			'label'      => esc_html__('Item Count Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_item_count_color_layout6',
			'priority' 	 => 28
		) )
	);


	// Item Count Background Color (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_back_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_doc_page_item_count_back_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_item_count_back_color_layout6',
		array(
			'label'      => esc_html__('Item Count Background Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_item_count_back_color_layout6',
			'priority' 	 => 29
		) )
	);

	// Item Count Inner Border Style
	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_border_type_layout6' , array(
        'default'     		=> $defaults['betterdocs_doc_page_item_count_border_type_layout6'],
        'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_doc_page_item_count_border_type_layout6',
            array(
                'label'      => esc_html__('Item Count Border Style', 'betterdocs-pro'),
                'section'    => 'betterdocs_doc_page_settings',
                'settings'   => 'betterdocs_doc_page_item_count_border_type_layout6',
                'type'    => 'select',
                'choices' => array(
					'none'	 => 'none',
                    'solid'  => 'solid',
                    'dashed' => 'dashed',
                    'dotted' => 'dotted',
                    'double' => 'double',
                    'groove' => 'groove',
                    'ridge'  => 'ridge',
					'inset'  => 'inset',
					'outset' => 'outset'
                ),
                'priority' => 30,
            ) )
    );

	// Item Count Border Color (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_page_item_count_border_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_doc_page_item_count_border_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_item_count_border_color_layout6',
		array(
			'label'      => esc_html__('Item Count Border Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_page_item_count_border_color_layout6',
			'priority' 	 => 31
		) )
	);

	// Item Count Border Width (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_width_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_width_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_item_count_border_width_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_page_item_count_border_width_layout6',
		'label' 	  => __('Item Count Border Width', 'betterdocs-pro'),
		'priority'    => 32,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_page_item_count_border_width_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Item Count Border Width Top (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_width_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_width_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_border_width_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_border_width_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 33,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_border_width_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Border Width Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_width_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_width_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_border_width_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_border_width_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 34,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_border_width_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Border Width Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_width_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_width_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_border_width_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_border_width_bottom_layout6',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 35,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_border_width_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Border Width Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_width_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_width_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_border_width_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_border_width_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 36,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_border_width_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Border Radius (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_radius_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_radius_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_item_count_border_radius_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_page_item_count_border_radius_layout6',
		'label' 	  => __('Item Count Border Radius', 'betterdocs-pro'),
		'priority'    => 37,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_page_item_count_border_radius_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Item Count Border Radius Top Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_radius_top_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_radius_top_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_border_radius_top_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_border_radius_top_left_layout6',
				'label' 	  => __('Top Left', 'betterdocs-pro'),
				'priority' 	  => 38,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_border_radius_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Border Radius Top Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_radius_top_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_radius_top_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_border_radius_top_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_border_radius_top_right_layout6',
				'label' 	  => __('Top Right', 'betterdocs-pro'),
				'priority' 	  => 39,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_border_radius_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Border Radius Bottom Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_radius_bottom_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_radius_bottom_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_border_radius_bottom_right_layout6',
				'label' 	  => __('Bottom Right', 'betterdocs-pro'),
				'priority' 	  => 40,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_border_radius_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Border Radius Bottom Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_border_radius_bottom_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_border_radius_bottom_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_border_radius_bottom_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_border_radius_bottom_left_layout6',
				'label' 	  => __('Bottom Left', 'betterdocs-pro'),
				'priority' 	  => 41,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_border_radius_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Margin (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_margin_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_margin_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_item_count_margin_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_page_item_count_margin_layout6',
		'label' 	  => __('Item Count Margin', 'betterdocs-pro'),
		'priority'    => 42,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_page_item_count_margin_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Item Count Margin Top (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_margin_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_margin_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_margin_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_margin_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 43,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);	

	// Item Count Margin Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_margin_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_margin_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_margin_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_margin_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 44,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);	

	// Item Count Margin Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_margin_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_margin_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_margin_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_margin_bottom_layout6',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 45,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);	

	// Item Count Margin Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_margin_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_margin_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_margin_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_margin_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 46,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);	

	// Item Count Padding (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_padding_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_padding_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_item_count_padding_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_page_item_count_padding_layout6',
		'label' 	  => __('Item Count Padding', 'betterdocs-pro'),
		'priority'    => 47,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_page_item_count_padding_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Item Count Padding Top (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_padding_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_padding_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_padding_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_padding_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 48,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Padding Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_padding_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_padding_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_padding_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_padding_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 49,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Padding Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_padding_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_padding_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_padding_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_padding_bottom_layout6',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 50,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Item Count Padding Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_page_item_count_padding_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_page_item_count_padding_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_page_item_count_padding_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_page_item_count_padding_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 51,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_page_item_count_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Seprator (Doc Page Layout 6)
	$wp_customize->add_setting(
		'betterdocs_doc_list_layout6_separator', 
		array(
			'default' 			=> '',
			'sanitize_callback' => 'esc_html'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Separator_Custom_Control(
			$wp_customize, 
			'betterdocs_doc_list_layout6_separator',
			array(
				'label'     => esc_html__('Doc List', 'betterdocs-pro'),
				'settings'  => 'betterdocs_doc_list_layout6_separator',
				'section' 	=> 'betterdocs_doc_page_settings',
				'priority'  => 52
			)
		)
	);

	// Doc List Font Size (Doc Page Layout 6)
	$wp_customize->add_setting(
		'betterdocs_doc_list_font_size_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_font_size_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_font_size_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_font_size_layout6',
				'label'    => __('List Font Size', 'betterdocs-pro'),
				'priority' => 53,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	// Doc List Font Line Height (Doc Page Layout 6)
	$wp_customize->add_setting(
		'betterdocs_doc_list_font_line_height_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_font_line_height_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_font_line_height_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_font_line_height_layout6',
				'label'    => __('List Font Line Height', 'betterdocs-pro'),
				'priority' => 54,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	// Doc List Font Weight (Doc Page Layout 6)
	$wp_customize->add_setting( 
		'betterdocs_doc_list_font_weight_layout6' , 
		array(
			'default'     		=> $defaults['betterdocs_doc_list_font_weight_layout6'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_choices',
    	) 
	);

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_doc_list_font_weight_layout6',
            array(
                'label'      => esc_html__('List Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_doc_page_settings',
                'settings'   => 'betterdocs_doc_list_font_weight_layout6',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 55
        	) 
		)
    );

	// Doc List Description Switch (Doc Page Layout 6)
	$wp_customize->add_setting(
		'betterdocs_doc_list_desc_switch_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_desc_switch_layout6'],
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Toggle_Control(
		$wp_customize, 'betterdocs_doc_list_desc_switch_layout6', 
			array(
				'label' 	=> esc_html__('Category Description', 'betterdocs-pro'),
				'section' 	=> 'betterdocs_doc_page_settings',
				'settings' 	=> 'betterdocs_doc_list_desc_switch_layout6',
				'type' 		=> 'light', // light, ios, flat
				'priority' 	=> 56
			)
		)
	);

	// Doc List Description Color(Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_list_desc_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_doc_list_desc_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_desc_color_layout6',
		array(
			'label'      => esc_html__('Description Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_desc_color_layout6',
			'priority' 	 => 57
		) )
	);

	// Doc List Description Font Size (Doc Page Layout 6)
	$wp_customize->add_setting(
		'betterdocs_doc_list_desc_font_size_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_desc_font_size_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_desc_font_size_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_desc_font_size_layout6',
				'label'    => __('Description Font Size', 'betterdocs-pro'),
				'priority' => 58,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	// Doc List Description Font Weight (Doc Page Layout 6)
	$wp_customize->add_setting( 
		'betterdocs_doc_list_desc_font_weight_layout6' , 
		array(
			'default'     		=> $defaults['betterdocs_doc_list_desc_font_weight_layout6'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_choices',
    	) 
	);

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_doc_list_desc_font_weight_layout6',
            array(
                'label'      => esc_html__('Description Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_doc_page_settings',
                'settings'   => 'betterdocs_doc_list_desc_font_weight_layout6',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 59
        	) 
		)
    );

	// Doc List Description Font Line Height (Doc Page Layout 6)
	$wp_customize->add_setting(
		'betterdocs_doc_list_desc_line_height_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_desc_line_height_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_desc_line_height_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_desc_line_height_layout6',
				'label'    => __('Description Font Line Height', 'betterdocs-pro'),
				'priority' => 60,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	// Doc List Description Margin (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_desc_margin_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_desc_margin_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_list_desc_margin_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_list_desc_margin_layout6',
		'label' 	  => __('Description Margin', 'betterdocs-pro'),
		'priority'    => 61,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_list_desc_margin_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Doc List Description Margin Top (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_desc_margin_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_desc_margin_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_desc_margin_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_desc_margin_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 62,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_desc_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Description Margin Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_desc_margin_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_desc_margin_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_desc_margin_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_desc_margin_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 63,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_desc_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Description Margin Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_desc_margin_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_desc_margin_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_desc_margin_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_desc_margin_bottom_layout6',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 64,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_desc_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Description Margin Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_desc_margin_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_desc_margin_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_desc_margin_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_desc_margin_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 65,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_desc_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Font Color (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_list_font_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_doc_list_font_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_font_color_layout6',
		array(
			'label'      => esc_html__('List Font Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_font_color_layout6',
			'priority' 	 => 66
		) )
	);

	// Doc List Font Color Hover (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_list_font_color_hover_layout6' , array(
		'default'     		=> $defaults['betterdocs_doc_list_font_color_hover_layout6'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_font_color_hover_layout6',
		array(
			'label'      => esc_html__('List Font Color Hover', 'betterdocs-pro'),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_font_color_hover_layout6',
			'priority' 	 => 67
		) )
	);

	// Doc List Background Color Hover (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_list_back_color_hover_layout6' , array(
		'default'     		=> $defaults['betterdocs_doc_list_back_color_hover_layout6'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_back_color_hover_layout6',
		array(
			'label'      => esc_html__('List Background Color Hover', 'betterdocs-pro'),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_back_color_hover_layout6',
			'priority' 	 => 67
		) )
	);

	// Doc List Border Color Hover (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_list_border_color_hover_layout6' , array(
		'default'     		=> $defaults['betterdocs_doc_list_border_color_hover_layout6'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_border_color_hover_layout6',
		array(
			'label'      => esc_html__('List Background Border Color Hover', 'betterdocs-pro'),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_border_color_hover_layout6',
			'priority' 	 => 67
		) )
	);

	// Doc List Margin (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_margin_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_margin_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_list_margin_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_list_margin_layout6',
		'label' 	  => __('List Margin', 'betterdocs-pro'),
		'priority'    => 68,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_list_margin_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Doc List Margin Top (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_margin_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_margin_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_margin_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_margin_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 69,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Margin Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_margin_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_margin_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_margin_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_margin_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 70,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Margin Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_margin_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_margin_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_margin_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_margin_bottom_layout6',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 71,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Margin Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_margin_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_margin_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_margin_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_margin_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 72,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	$wp_customize->add_setting('betterdocs_doc_list_padding_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_padding_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_list_padding_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_list_padding_layout6',
		'label' 	  => __('List Padding', 'betterdocs-pro'),
		'priority'    => 73,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_list_padding_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Doc List Padding Top (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_padding_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_padding_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_padding_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_padding_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 74,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Padding Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_padding_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_padding_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_padding_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_padding_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 75,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Padding Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_padding_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_padding_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_padding_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_padding_bottom_layout6',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 76,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Padding Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_padding_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_padding_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_padding_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_padding_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 77,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Border Style (Doc Page Layout 6)
	$wp_customize->add_setting( 'betterdocs_doc_list_border_style_layout6' , array(
        'default'     		=> $defaults['betterdocs_doc_list_border_style_layout6'],
        'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_doc_list_border_style_layout6',
            array(
                'label'      => esc_html__('List Border Style', 'betterdocs-pro'),
                'section'    => 'betterdocs_doc_page_settings',
                'settings'   => 'betterdocs_doc_list_border_style_layout6',
                'type'    => 'select',
                'choices' => array(
					'none'	 => 'none',
                    'solid'  => 'solid',
                    'dashed' => 'dashed',
                    'dotted' => 'dotted',
                    'double' => 'double',
                    'groove' => 'groove',
                    'ridge'  => 'ridge',
					'inset'  => 'inset',
					'outset' => 'outset'
                ),
                'priority' => 77,
            ) )
    );

	// Doc List Border Width (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_list_border_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_list_border_layout6',
		'label' 	  => __('List Border Width', 'betterdocs-pro'),
		'priority'    => 77,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_list_border_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Doc List Border Width Top (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_border_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_border_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 77,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_border_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Border Width Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_border_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_border_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 77,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_border_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Border Width Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_border_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_border_bottom_layout6',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 77,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_border_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Border Width Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_border_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_border_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 77,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_border_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Border Width Hover (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_hover_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_hover_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_list_border_hover_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_list_border_hover_layout6',
		'label' 	  => __('List Border Width Hover', 'betterdocs-pro'),
		'priority'    => 77,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_list_border_hover_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Doc List Border Width Hover Top (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_hover_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_hover_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_border_hover_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_border_hover_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 77,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_border_hover_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Border Width Hover Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_hover_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_hover_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_border_hover_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_border_hover_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 77,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_border_hover_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Border Width Hover Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_hover_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_hover_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_border_hover_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_border_hover_bottom_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 77,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_border_hover_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Doc List Border Width Hover Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_border_hover_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_border_hover_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_border_hover_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_border_hover_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 77,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_border_hover_layout6 betterdocs-dimension',
				),
			)
		)
	);


	//Doc List Border Color Top(Doc Page Layout 6) 
	$wp_customize->add_setting( 
		'betterdocs_doc_list_border_color_top_layout6' ,
	 	array(
			'default'     		=> $defaults['betterdocs_doc_list_border_color_top_layout6'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_border_color_top_layout6',
		array(
			'label'      => __( 'List Border Color Top', 'betterdocs-pro' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_border_color_top_layout6',
			'priority'   => 79,
		) )
	);

	//Doc List Border Color Right(Doc Page Layout 6) 
	$wp_customize->add_setting( 
		'betterdocs_doc_list_border_color_right_layout6' ,
	 	array(
			'default'     		=> $defaults['betterdocs_doc_list_border_color_right_layout6'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_border_color_right_layout6',
		array(
			'label'      => __( 'List Border Color Right', 'betterdocs-pro' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_border_color_right_layout6',
			'priority'   => 79,
		) )
	);

	//Doc List Border Color Bottom(Doc Page Layout 6) 
	$wp_customize->add_setting( 
		'betterdocs_doc_list_border_color_bottom_layout6' ,
	 	array(
			'default'     		=> $defaults['betterdocs_doc_list_border_color_bottom_layout6'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_border_color_bottom_layout6',
		array(
			'label'      => __( 'List Border Color Bottom', 'betterdocs-pro' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_border_color_bottom_layout6',
			'priority'   => 79,
		) )
	);

	//Doc List Border Color Left(Doc Page Layout 6) 
	$wp_customize->add_setting( 
		'betterdocs_doc_list_border_color_left_layout6' ,
	 	array(
			'default'     		=> $defaults['betterdocs_doc_list_border_color_left_layout6'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_border_color_left_layout6',
		array(
			'label'      => __( 'List Border Color Left', 'betterdocs-pro' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_border_color_left_layout6',
			'priority'   => 79,
		) )
	);

	//Doc List Arrow Height (Doc Page Layout 6) 
	$wp_customize->add_setting(
		'betterdocs_doc_list_arrow_height_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_arrow_height_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_arrow_height_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_arrow_height_layout6',
				'label'    => __('List Arrow Height', 'betterdocs-pro'),
				'priority' => 80,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	//Doc List Arrow Width (Doc Page Layout 6) 
	$wp_customize->add_setting(
		'betterdocs_doc_list_arrow_width_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_arrow_width_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_arrow_width_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_arrow_width_layout6',
				'label'    => __('List Arrow Width', 'betterdocs-pro'),
				'priority' => 81,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	// Doc List Arrow Color (Doc Page Layout 6) 
	$wp_customize->add_setting( 
		'betterdocs_doc_list_arrow_color_layout6' ,
	 	array(
			'default'     		=> $defaults['betterdocs_doc_list_arrow_color_layout6'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_arrow_color_layout6',
		array(
			'label'      => __( 'List Arrow Color', 'betterdocs-pro' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_arrow_color_layout6',
			'priority'   => 82,
		) )
	);

	// Doc List Explore More Button Separator (Doc Page Layout 6)
	$wp_customize->add_setting(
		'betterdocs_doc_list_explore_more_separator', 
		array(
			'default' 			=> '',
			'sanitize_callback' => 'esc_html'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Separator_Custom_Control(
			$wp_customize, 
			'betterdocs_doc_list_explore_more_separator',
			array(
				'label'     => esc_html__('Explore More', 'betterdocs-pro'),
				'settings'  => 'betterdocs_doc_list_explore_more_separator',
				'section' 	=> 'betterdocs_doc_page_settings',
				'priority'  => 83
			)
		)
	);

	//Doc List Explore More Font Size(Doc Page Layout 6) 
	$wp_customize->add_setting(
		'betterdocs_doc_list_explore_more_font_size_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_explore_more_font_size_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_explore_more_font_size_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_explore_more_font_size_layout6',
				'label'    => __('Font Size', 'betterdocs-pro'),
				'priority' => 84,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	//Doc List Explore More Line Height(Doc Page Layout 6)
	$wp_customize->add_setting(
		'betterdocs_doc_list_explore_more_font_line_height_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_explore_more_font_line_height_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_explore_more_font_line_height_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_explore_more_font_line_height_layout6',
				'label'    => __('Line Height', 'betterdocs-pro'),
				'priority' => 85,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	//Doc List Explore More Font Color(Doc Page Layout 6) 
	$wp_customize->add_setting( 
		'betterdocs_doc_list_explore_more_font_color_layout6' ,
	 	array(
			'default'     		=> $defaults['betterdocs_doc_list_explore_more_font_color_layout6'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_explore_more_font_color_layout6',
		array(
			'label'      => __( 'Font Color', 'betterdocs-pro' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_explore_more_font_color_layout6',
			'priority'   => 86,
		) )
	);

	//Doc List Explore More Font Weight(Doc Page Layout 6) 
	$wp_customize->add_setting( 'betterdocs_doc_list_explore_more_font_weight_layout6' , array(
        'default'     		=> $defaults['betterdocs_doc_list_explore_more_font_weight_layout6'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_doc_list_explore_more_font_weight_layout6',
            array(
                'label'      => esc_html__('Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_doc_page_settings',
                'settings'   => 'betterdocs_doc_list_explore_more_font_weight_layout6',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 87
            ) )
    );

	//Doc List Explore More Padding (Doc Page Layout 6) 
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_padding_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_padding_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_list_explore_more_padding_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_list_explore_more_padding_layout6',
		'label' 	  => __('Padding', 'betterdocs-pro'),
		'priority'    => 88,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_list_explore_more_padding_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	//Explore More Top Padding (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_padding_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_padding_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 
			'betterdocs_doc_list_explore_more_padding_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_explore_more_padding_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 89,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_explore_more_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Explore More Padding Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_padding_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_padding_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_explore_more_padding_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_explore_more_padding_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 90,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_explore_more_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Explore More Padding Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_padding_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_padding_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_explore_more_padding_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_explore_more_padding_bottom_layout6',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 91,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_explore_more_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Explore More Padding Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_padding_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_padding_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_explore_more_padding_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_explore_more_padding_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 92,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_explore_more_padding_layout6 betterdocs-dimension',
				),
			)
		)
	);

	//Doc List Explore More Margin (Doc Page Layout 6) 
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_margin_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_margin_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_list_explore_more_margin_layout6', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_doc_page_settings',
		'settings'	  => 'betterdocs_doc_list_explore_more_margin_layout6',
		'label' 	  => __('Margin', 'betterdocs-pro'),
		'priority'    => 93,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_doc_list_explore_more_margin_layout6',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Explore More Margin Top (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_margin_top_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_margin_top_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_explore_more_margin_top_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_explore_more_margin_top_layout6',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 94,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_explore_more_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Explore More Margin Right (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_margin_right_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_margin_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_explore_more_margin_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_explore_more_margin_right_layout6',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 95,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_explore_more_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Explore More Margin Bottom (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_margin_bottom_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_margin_bottom_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_explore_more_margin_bottom_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_explore_more_margin_bottom_layout6',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 96,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_explore_more_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Explore More Margin Left (Doc Page Layout 6)
	$wp_customize->add_setting('betterdocs_doc_list_explore_more_margin_left_layout6', array(
		'default' 			=> $defaults['betterdocs_doc_list_explore_more_margin_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_doc_list_explore_more_margin_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_doc_page_settings',
				'settings' 	  => 'betterdocs_doc_list_explore_more_margin_left_layout6',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 97,
				'input_attrs' => array(
					'class' => 'betterdocs_doc_list_explore_more_margin_layout6 betterdocs-dimension',
				),
			)
		)
	);

	//Explore More Arrow Height(Doc Page Layout 6) 
	$wp_customize->add_setting(
		'betterdocs_doc_list_explore_more_arrow_height_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_explore_more_arrow_height_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_explore_more_arrow_height_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_explore_more_arrow_height_layout6',
				'label'    => __('Arrow Height', 'betterdocs-pro'),
				'priority' => 98,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	//Explore More Arrow Width(Doc Page Layout 6) 
	$wp_customize->add_setting(
		'betterdocs_doc_list_explore_more_arrow_width_layout6', 
		array(
			'default' 			=> $defaults['betterdocs_doc_list_explore_more_arrow_width_layout6'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_doc_list_explore_more_arrow_width_layout6', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_list_explore_more_arrow_width_layout6',
				'label'    => __('Arrow Width', 'betterdocs-pro'),
				'priority' => 99,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	//Explore More Arrow Color(Doc Page Layout 6) 
	$wp_customize->add_setting( 
		'betterdocs_doc_list_explore_more_arrow_color_layout6' ,
	 	array(
			'default'     		=> $defaults['betterdocs_doc_list_explore_more_arrow_color_layout6'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_list_explore_more_arrow_color_layout6',
		array(
			'label'      => __( 'Arrow Color', 'betterdocs-pro' ),
			'section'    => 'betterdocs_doc_page_settings',
			'settings'   => 'betterdocs_doc_list_explore_more_arrow_color_layout6',
			'priority'   => 100,
		) )
	);


	// Doc Sidebar Layout 6(For Single Doc Layout 6)

	// Sidebar Layout 6 Separator(For Single Doc Layout 6)
	$wp_customize->add_setting('betterdocs_sidebar_seperator_layout6', array(
		'default'           => $defaults['betterdocs_sidebar_seperator_layout6'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_seperator_layout6', array(
		'label'	      	=> esc_html__('Sidebar Bohemian Layout', 'betterdocs-pro'),
		'settings'		=> 'betterdocs_sidebar_seperator_layout6',
		'section'  		=> 'betterdocs_sidebar_settings',
		'input_attrs'	=> array(
			'class' => 'bohemian-layout'
		),
		'priority'   	=> 301
	)));

	// Sidebar Background Color Layout 6(For Single Doc Layout 6)

	$wp_customize->add_setting( 'betterdocs_sidebar_bg_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_bg_color_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_bg_color_layout6',
		array(
			'label'      => esc_html__('Background Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_bg_color_layout6',
			'priority'   => 301
		) )
	);

	// Sidebar Active Background Color Layout 6(For Single Doc Layout 6)

	$wp_customize->add_setting( 'betterdocs_sidebar_active_bg_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_active_bg_color_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_active_bg_color_layout6',
		array(
			'label'      => esc_html__('Active Background Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_active_bg_color_layout6',
			'priority'   => 301
		) )
	);

	// Sidebar Active Border Layout 6(For Single Doc Layout 6) 
	$wp_customize->add_setting( 'betterdocs_sidebar_active_bg_border_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_active_bg_border_color_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_active_bg_border_color_layout6',
		array(
			'label'      => esc_html__('Active Background Border Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_active_bg_border_color_layout6',
			'priority'   => 301
		) )
	);

	// Sidebar Padding Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_padding_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_padding_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_padding_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding_layout6',
		'label'    => esc_html__('Sidebar Padding', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_padding_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 302
	) ) );

	// Sidebar Padding Top Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_padding_top_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_padding_top_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_padding_top_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding_top_layout6',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 303
	) ) );

	// Sidebar Padding Right Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_padding_right_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_padding_right_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_padding_right_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding_right_layout6',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 304
	) ) );
	
	// Sidebar Padding Bottom Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_padding_bottom_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_padding_bottom_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_padding_bottom_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding_bottom_layout6',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 305
	) ) );
	

	// Sidebar Padding Left Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_padding_left_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_padding_left_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_padding_left_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_padding_left_layout6',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 306
	) ) );
	
	// Sidebar Margin Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_margin_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_margin_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_margin_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_margin_layout6',
		'label'    => esc_html__('Sidebar Margin', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_margin_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 307
	) ) );

	// Sidebar Margin Top Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_margin_top_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_margin_top_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_margin_top_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_margin_top_layout6',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 308
	) ) );

	// Sidebar Margin Right Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_margin_right_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_margin_right_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_margin_right_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_margin_right_layout6',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 309
	) ) );

	// Sidebar Margin Bottom Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_margin_bottom_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_margin_bottom_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_margin_bottom_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_margin_bottom_layout6',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 310
	) ) );

	// Sidebar Margin Left Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_margin_left_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_margin_left_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_margin_left_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_margin_left_layout6',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 311
	) ) );
	
	// Sidebar Border Radius Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_border_radius_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_border_radius_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_border_radius_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_border_radius_layout6',
		'label'    => esc_html__('Sidebar Border Radius', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_border_radius_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 312
	) ) );

	// Sidebar Border Radius Top Left Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_border_radius_top_left_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_border_radius_top_left_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_border_radius_top_left_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_border_radius_top_left_layout6',
		'label'    => esc_html__('Top Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_border_radius_layout6 betterdocs-dimension',
		),
		'priority'   => 313
	) ) );
	
	// Sidebar Border Radius Top Right Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_border_radius_top_right_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_border_radius_top_right_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_border_radius_top_right_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_border_radius_top_right_layout6',
		'label'    => esc_html__('Top Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_border_radius_layout6 betterdocs-dimension',
		),
		'priority'   => 313
	) ) );
	
	// Sidebar Border Radius Bottom Right Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_border_radius_bottom_right_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_border_radius_bottom_right_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_border_radius_bottom_right_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_border_radius_bottom_right_layout6',
		'label'    => esc_html__('Bottom Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_border_radius_layout6 betterdocs-dimension',
		),
		'priority'   => 314
	) ) );

	// Sidebar Border Radius Bottom Left Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_border_radius_bottom_left_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_border_radius_bottom_left_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_border_radius_bottom_left_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_border_radius_bottom_left_layout6',
		'label'    => esc_html__('Bottom Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_border_radius_layout6 betterdocs-dimension',
		),
		'priority'   => 315
	) ) );

	// Sidebar Title Tag Layout 6(For Single Doc Layout 6)
    $wp_customize->add_setting( 'betterdocs_sidebar_title_tag_layout6' , array(
        'default'     		=> $defaults['betterdocs_sidebar_title_tag_layout6'],
        'capability'    	=> 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_sidebar_title_tag_layout6',
            array(
                'label'      => esc_html__('Category Title Tag', 'betterdocs-pro'),
                'section'    => 'betterdocs_sidebar_settings',
                'settings'   => 'betterdocs_sidebar_title_tag_layout6',
                'type'    	=> 'select',
                'choices' => array(
                    'h1' => 'h1',
                    'h2' => 'h2',
                    'h3' => 'h3',
                    'h4' => 'h4',
                    'h5' => 'h5',
                    'h6' => 'h6'
				),
				'priority'  => 317
            ) )
    );

	// Sidebar Title Background Color Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_bg_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_title_bg_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_title_bg_color_layout6',
		array(
			'label'      => esc_html__('Title Background Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_title_bg_color_layout6',
			'priority'   => 318
		) )
	);

	// Sidebar Active Title Background Color Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_active_title_bg_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_active_title_bg_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_active_title_bg_color_layout6',
		array(
			'label'      => esc_html__('Active Title Background Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_active_title_bg_color_layout6',
			'priority'   => 318
		) )
	);

	// Sidebar Title Color Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_title_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_title_color_layout6',
		array(
			'label'      => esc_html__('Title Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_title_color_layout6',
			'priority'	 => 319
		) )
	);

	// Sidebar Title Hover Color Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_hover_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_title_hover_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_title_hover_color_layout6',
		array(
			'label'      => esc_html__('Title Hover Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_title_hover_color_layout6',
			'priority'	 => 319
		) )
	);

	// Sidebar Title Font Size Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_font_size_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_font_size_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_title_font_size_layout6', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_font_size_layout6',
		'label'    => esc_html__('Title Font Size', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority'	 => 320
	) ) );

	// Sidebar Title Font Line Height Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_font_line_height_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_font_line_height_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_title_font_line_height_layout6', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_font_line_height_layout6',
		'label'    => esc_html__('Title Font Line Height', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority'	 => 321
	) ) );

	// Sidebar Title Font Font Weight Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_font_weight_layout6' , array(
        'default'     		=> $defaults['betterdocs_sidebar_title_font_weight_layout6'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_sidebar_title_font_weight_layout6',
            array(
                'label'      => esc_html__('Title Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_sidebar_settings',
                'settings'   => 'betterdocs_sidebar_title_font_weight_layout6',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 322
            ) )
    );

	// Sidebar Title Padding Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_padding_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding_layout6',
		'label'    => esc_html__('Title Padding', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_title_padding_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 323
	) ) );
	
	// Sidebar Title Padding Top Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding_top_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_padding_top_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding_top_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding_top_layout6',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 324
	) ) );

	// Sidebar Title Padding Right Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding_right_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_padding_right_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding_right_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding_right_layout6',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 325
	) ) );

	// Sidebar Title Padding Bottom Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding_bottom_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_padding_bottom_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding_bottom_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding_bottom_layout6',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 326
	) ) );

	// Sidebar Title Padding Left Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_padding_left_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_padding_left_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_padding_left_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_padding_left_layout6',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 327
	) ) );

	// Sidebar Title Margin Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_margin_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin_layout6',
		'label'    => esc_html__('Title Margin', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_title_margin_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 328
	) ) );

	// Sidebar Title Margin Top Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin_top_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_margin_top_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin_top_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin_top_layout6',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 329
	) ) );

	// Sidebar Title Margin Right Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin_right_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_margin_right_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin_right_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin_right_layout6',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 330
	) ) );

	// Sidebar Title Margin Bottom Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin_bottom_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_margin_bottom_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin_bottom_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin_bottom_layout6',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 331
	) ) );


	// Sidebar Title Margin Left Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_title_margin_left_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_title_margin_left_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_title_margin_left_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_title_margin_left_layout6',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_title_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 332
	) ) );

	// Sidebar Term List Border Type Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_list_border_type_layout6' , array(
        'default'     		=> $defaults['betterdocs_sidebar_term_list_border_type_layout6'],
        'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_sidebar_term_list_border_type_layout6',
            array(
                'label'      => esc_html__('Term List Border Type', 'betterdocs-pro'),
                'section'    => 'betterdocs_sidebar_settings',
                'settings'   => 'betterdocs_sidebar_term_list_border_type_layout6',
                'type'    => 'select',
                'choices' => array(
					'none'	 => 'none',
                    'solid'  => 'solid',
                    'dashed' => 'dashed',
                    'dotted' => 'dotted',
                    'double' => 'double',
                    'groove' => 'groove',
                    'ridge'  => 'ridge',
					'inset'  => 'inset',
					'outset' => 'outset'
                ),
                'priority' => 333,
            ) )
    );

	// Sidebar Term List Border Width Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_border_width_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_border_width_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_term_border_width_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_border_width_layout6',
		'label'    => esc_html__('Term List Border Width', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_term_border_width_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 334
	) ) );

	// Sidebar Term List Border Top Width Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_border_top_width_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_border_top_width_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_border_top_width_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_border_top_width_layout6',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_border_width_layout6 betterdocs-dimension',
		),
		'priority'   => 335
	) ) );

	// Sidebar Term List Border Right Width Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_border_right_width_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_border_right_width_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_border_right_width_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_border_right_width_layout6',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_border_width_layout6 betterdocs-dimension',
		),
		'priority'   => 336
	) ) );

	// Sidebar Term List Border Bottom Width Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_border_bottom_width_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_border_bottom_width_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_border_bottom_width_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_border_bottom_width_layout6',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_border_width_layout6 betterdocs-dimension',
		),
		'priority'   => 337
	) ) );

	// Sidebar Term List Border Left Width Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_border_left_width_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_border_left_width_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_border_left_width_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_border_left_width_layout6',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_border_width_layout6 betterdocs-dimension',
		),
		'priority'   => 338
	) ) );

	// Sidebar Term List Border Width Color (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_border_width_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_term_border_width_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_term_border_width_color_layout6',
		array(
			'label'      => esc_html__('Border Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_term_border_width_color_layout6',
			'priority'	 => 339
		) )
	);

	//Sidebar Term List Item Color Layout 6(For Single Doc Layout 6)

	$wp_customize->add_setting('betterdocs_sidebar_term_list_item_color_layout6', array(
		'default' 	 		=> $defaults['betterdocs_sidebar_term_list_item_color_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport'  		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_sidebar_term_list_item_color_layout6',
			array(
				'label' 	=> __('List Item Color', 'betterdocs-pro'),
				'section' 	=> 'betterdocs_sidebar_settings',
				'settings' 	=> 'betterdocs_sidebar_term_list_item_color_layout6',
				'priority' 	=> 340
			))
	);

	//Sidebar Term List Item Hover Color Layout 6(For Single Doc Layout 6)

	$wp_customize->add_setting('betterdocs_sidebar_term_list_item_hover_color_layout6', array(
		'default' 	 		=> $defaults['betterdocs_sidebar_term_list_item_hover_color_layout6'],
		'capability' 		=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_sidebar_term_list_item_hover_color_layout6',
			array(
				'label' 	=> __('List Item Color Hover', 'betterdocs-pro'),
				'section' 	=> 'betterdocs_sidebar_settings',
				'settings' 	=> 'betterdocs_sidebar_term_list_item_hover_color_layout6',
				'priority' 	=> 340
			))
	);

	//Sidebar Term List Item Font Size Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_list_item_font_size_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_list_item_font_size_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_term_list_item_font_size_layout6', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_list_item_font_size_layout6',
		'label'    => esc_html__('List Item Font Size', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 340
	) ) );

	//Sidebar Term List Item Icon Color Layout 6(For Single Doc Layout 6)

	$wp_customize->add_setting('betterdocs_sidebar_term_list_item_icon_color_layout6', array(
		'default' 	 		=> $defaults['betterdocs_sidebar_term_list_item_icon_color_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_sidebar_term_list_item_icon_color_layout6',
			array(
				'label' 	=> __('List Icon Color', 'betterdocs-pro'),
				'section' 	=> 'betterdocs_sidebar_settings',
				'settings' 	=> 'betterdocs_sidebar_term_list_item_icon_color_layout6',
				'priority' 	=> 340
			))
	);

	//Sidebar Term List Item Icon Size Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_list_item_icon_size_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_list_item_icon_size_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_term_list_item_icon_size_layout6', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_list_item_icon_size_layout6',
		'label'    => esc_html__('List Item Icon Size', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 340
	) ) );

	//Sidebar Term List Item Padding(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_list_item_padding_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_list_item_padding_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_term_list_item_padding_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_list_item_padding_layout6',
		'label'    => esc_html__('List Item Padding', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_term_list_item_padding_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 340
	) ) );

	//Sidebar Term List Item Padding Top(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_list_item_padding_top_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_list_item_padding_top_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_list_item_padding_top_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_list_item_padding_top_layout6',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_list_item_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 340
	) ) );

	//Sidebar Term List Item Padding Right(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_list_item_padding_right_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_list_item_padding_right_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_list_item_padding_right_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_list_item_padding_right_layout6',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_list_item_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 340
	) ) );

	//Sidebar Term List Item Padding Bottom(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_list_item_padding_bottom_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_list_item_padding_bottom_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_list_item_padding_bottom_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_list_item_padding_bottom_layout6',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_list_item_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 340
	) ) );

	//Sidebar Term List Item Padding Left(For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_list_item_padding_left_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_list_item_padding_left_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_list_item_padding_left_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_list_item_padding_left_layout6',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_list_item_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 340
	) ) );

	//Sidebar Term List Active Item Color(For Single Doc Layout 6)
	$wp_customize->add_setting('betterdocs_sidebar_term_list_active_item_color_layout6', array(
		'default' 	 		=> $defaults['betterdocs_sidebar_term_list_active_item_color_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_sidebar_term_list_active_item_color_layout6',
			array(
				'label' 	=> __('Active List Item Color', 'betterdocs-pro'),
				'section' 	=> 'betterdocs_sidebar_settings',
				'settings' 	=> 'betterdocs_sidebar_term_list_active_item_color_layout6',
				'priority' 	=> 340
			))
	);

	// Sidebar Term List Item Counter Border Type
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_border_type_layout6' , array(
        'default'     		=> $defaults['betterdocs_sidebar_term_item_counter_border_type_layout6'],
        'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_sidebar_term_item_counter_border_type_layout6',
            array(
                'label'      => esc_html__('Count Border Style', 'betterdocs-pro'),
                'section'    => 'betterdocs_sidebar_settings',
                'settings'   => 'betterdocs_sidebar_term_item_counter_border_type_layout6',
                'type'    => 'select',
                'choices' => array(
					'none'	 => 'none',
                    'solid'  => 'solid',
                    'dashed' => 'dashed',
                    'dotted' => 'dotted',
                    'double' => 'double',
                    'groove' => 'groove',
                    'ridge'  => 'ridge',
					'inset'  => 'inset',
					'outset' => 'outset'
                ),
                'priority' => 340,
            ) )
    );

	// Sidebar Term List Item Counter Border Width
	$wp_customize->add_setting('betterdocs_sidebar_term_item_counter_border_width_layout6', array(
		'default' 	 		=> $defaults['betterdocs_sidebar_term_item_counter_border_width_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_border_width_layout6', array(
		'type' 		=> 'betterdocs-range-value',
		'section' 	=> 'betterdocs_sidebar_settings',
		'settings'  => 'betterdocs_sidebar_term_item_counter_border_width_layout6',
		'label' 	=> __('Count Border Width', 'betterdocs-pro'),
		'priority'  => 341,
		'input_attrs' => array(
			'class'   => '',
			'min'     => 0,
			'max'	  => 100,
			'step' 	  => 1,
			'suffix'  => 'px', //optional suffix
		),
	)));

	// Sidebar Term List Item Counter Font Size Layout 6(For Single Doc Layout 6)
	$wp_customize->add_setting('betterdocs_sidebar_term_item_counter_font_size_layout6', array(
		'default' 	 		=> $defaults['betterdocs_sidebar_term_item_counter_font_size_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_font_size_layout6', array(
		'type' 		=> 'betterdocs-range-value',
		'section' 	=> 'betterdocs_sidebar_settings',
		'settings'  => 'betterdocs_sidebar_term_item_counter_font_size_layout6',
		'label' 	=> __('Count Font Size', 'betterdocs-pro'),
		'priority'  => 341,
		'input_attrs' => array(
			'class'   => '',
			'min'     => 0,
			'max'	  => 200,
			'step' 	  => 1,
			'suffix'  => 'px', //optional suffix
		),
	)));


	// Sidebar Term List Item Counter Font Weight Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_font_weight_layout6' , array(
        'default'     		=> $defaults['betterdocs_sidebar_term_item_counter_font_weight_layout6'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_sidebar_term_item_counter_font_weight_layout6',
            array(
                'label'      => esc_html__('Count Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_sidebar_settings',
                'settings'   => 'betterdocs_sidebar_term_item_counter_font_weight_layout6',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 342
            ) )
    );

	// Sidebar Term List Item Counter Font Line Height Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting('betterdocs_sidebar_term_item_counter_font_line_height_layout6', array(
		'default' 	 		=> $defaults['betterdocs_sidebar_term_item_counter_font_line_height_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_font_line_height_layout6', array(
		'type' 		=> 'betterdocs-range-value',
		'section' 	=> 'betterdocs_sidebar_settings',
		'settings'  => 'betterdocs_sidebar_term_item_counter_font_line_height_layout6',
		'label' 	=> __('Count Font Line Height', 'betterdocs-pro'),
		'priority'  => 343,
		'input_attrs' => array(
			'class'   => '',
			'min'     => 0,
			'max'	  => 200,
			'step' 	  => 1,
			'suffix'  => 'px', //optional suffix
		),
	)));

	// Sidebar Term List Item Counter Color Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_term_item_counter_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_term_item_counter_color_layout6',
		array(
			'label'      => esc_html__('Count Font Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_term_item_counter_color_layout6',
			'priority' 	 => 344
		) )
	);

	// Sidebar Term List Item Counter Background Color Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_back_color_layout6' , array(
		'default'     		=> $defaults['betterdocs_sidebar_term_item_counter_back_color_layout6'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_sidebar_term_item_counter_back_color_layout6',
		array(
			'label'      => esc_html__('Count Background Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_sidebar_settings',
			'settings'   => 'betterdocs_sidebar_term_item_counter_back_color_layout6',
			'priority' 	 => 345
		) )
	);

	// Sidebar Term List Item Counter Border Radius Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_border_radius_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_border_radius_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_border_radius_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6',
		'label'    => esc_html__('Count Border Radius', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 346
	) ) );

	// Sidebar Term List Item Counter Border Radius Top Left Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting('betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6', array(
		'default' 			=> $defaults['betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_sidebar_settings',
				'settings' 	  => 'betterdocs_sidebar_term_item_counter_border_radius_top_left_layout6',
				'label' 	  => __('Top Left', 'betterdocs-pro'),
				'priority' 	  => 347,
				'input_attrs' => array(
					'class' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Sidebar Term List Item Counter Border Radius Top Right Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting('betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6', array(
		'default' 			=> $defaults['betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_sidebar_settings',
				'settings' 	  => 'betterdocs_sidebar_term_item_counter_border_radius_top_right_layout6',
				'label' 	  => __('Top Right', 'betterdocs-pro'),
				'priority' 	  => 348,
				'input_attrs' => array(
					'class' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Sidebar Term List Item Counter Border Radius Bottom Right Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting('betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6', array(
		'default' 			=> $defaults['betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_sidebar_settings',
				'settings' 	  => 'betterdocs_sidebar_term_item_counter_border_radius_bottom_right_layout6',
				'label' 	  => __('Bottom Right', 'betterdocs-pro'),
				'priority' 	  => 349,
				'input_attrs' => array(
					'class' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Sidebar Term List Item Counter Border Radius Bottom Left Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting('betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6', array(
		'default' 			=> $defaults['betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_sidebar_settings',
				'settings' 	  => 'betterdocs_sidebar_term_item_counter_border_radius_bottom_left_layout6',
				'label' 	  => __('Bottom Right', 'betterdocs-pro'),
				'priority' 	  => 350,
				'input_attrs' => array(
					'class' => 'betterdocs_sidebar_term_item_counter_border_radius_layout6 betterdocs-dimension',
				),
			)
		)
	);

	// Sidebar Term List Item Counter Padding Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_padding_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_padding_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_padding_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_padding_layout6',
		'label'    => esc_html__('Count Padding', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_term_item_counter_padding_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 351
	) ) );

	// Sidebar Term List Item Counter Padding Top Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_padding_top_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_padding_top_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_padding_top_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_padding_top_layout6',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_item_counter_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 352
	) ) );

	// Sidebar Term List Item Counter Padding Right Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_padding_right_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_padding_right_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_padding_right_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_padding_right_layout6',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_item_counter_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 353
	) ) );

	// Sidebar Term List Item Counter Padding Bottom Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_padding_bottom_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_padding_bottom_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_padding_bottom_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_padding_bottom_layout6',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_item_counter_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 354
	) ) );

	// Sidebar Term List Item Counter Padding Left Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_padding_left_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_padding_left_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_padding_left_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_padding_left_layout6',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_item_counter_padding_layout6 betterdocs-dimension',
		),
		'priority'   => 355
	) ) );

	// Sidebar Term List Item Counter Margin Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_margin_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_margin_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_margin_layout6', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_margin_layout6',
		'label'    => esc_html__('Count Margin', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_sidebar_term_item_counter_margin_layout6',
			'class' => 'betterdocs-dimension',
		),
		'priority'   => 356
	) ) );

	// Sidebar Term List Item Counter Margin Top Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_margin_top_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_margin_top_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_margin_top_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_margin_top_layout6',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_item_counter_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 357
	) ) );

	// Sidebar Term List Item Counter Margin Right Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_margin_right_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_margin_right_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_margin_right_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_margin_right_layout6',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_item_counter_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 358
	) ) );

	// Sidebar Term List Item Counter Margin Bottom Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_margin_bottom_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_margin_bottom_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_margin_bottom_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_margin_bottom_layout6',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_item_counter_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 359
	) ) );

	// Sidebar Term List Item Counter Margin Left Layout 6 (For Single Doc Layout 6)
	$wp_customize->add_setting( 'betterdocs_sidebar_term_item_counter_margin_left_layout6', array(
		'default'       	=> $defaults['betterdocs_sidebar_term_item_counter_margin_left_layout6'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_sidebar_term_item_counter_margin_left_layout6', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_sidebar_settings',
		'settings' => 'betterdocs_sidebar_term_item_counter_margin_left_layout6',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_sidebar_term_item_counter_margin_layout6 betterdocs-dimension',
		),
		'priority'   => 360
	) ) );


	// Archive Page Title Tag (Archive Page Layout 2)

    $wp_customize->add_setting( 'betterdocs_archive_title_tag_layout2' , array(
        'default'     		=> $defaults['betterdocs_archive_title_tag_layout2'],
        'capability'    	=> 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_title_tag_layout2',
            array(
                'label'      => esc_html__('Category Title Tag', 'betterdocs-pro'),
                'section'    => 'betterdocs_archive_page_settings',
                'settings'   => 'betterdocs_archive_title_tag_layout2',
                'type'    => 'select',
                'choices' => array(
                    'h1' => 'h1',
                    'h2' => 'h2',
                    'h3' => 'h3',
                    'h4' => 'h4',
                    'h5' => 'h5',
                    'h6' => 'h6'
				),
				'priority' => 401
            ) )
    );

	// Archive Page Category Inner Content Background Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_back_color_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_inner_content_back_color_layout2'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_inner_content_back_color_layout2',
		array(
			'label'      => esc_html__('Inner Content Background Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_inner_content_back_color_layout2',
			'priority' 	 => 402
		) )
	);

	// Archive Page Category Inner Content Image Size(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_size_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_size_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_size_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_size_layout2',
		'label'    => esc_html__('Content Image Size', 'betterdocs-pro'),
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => '%', //optional suffix
		),
		'priority' 	 => 402
	) ) );

	// Archive Page Category Inner Content Image Padding(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_padding_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_padding_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_padding_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_padding_layout2',
		'label'    => esc_html__('Content Image Padding', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' 	=> 'betterdocs_archive_inner_content_image_padding_layout2',
			'class' => 'betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );
	
	// Archive Page Category Inner Content Image Padding Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_padding_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_padding_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'		 	=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_padding_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_padding_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_inner_content_image_padding_layout2 betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );

	// Archive Page Category Inner Content Image Padding Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_padding_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_padding_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'		 	=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_padding_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_padding_right_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_inner_content_image_padding_layout2 betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );

	// Archive Page Category Inner Content Image Padding Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_padding_bottom_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_padding_bottom_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'		 	=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_padding_bottom_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_padding_bottom_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_inner_content_image_padding_layout2 betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );

	// Archive Page Category Inner Content Image Padding Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_padding_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_padding_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'		 	=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_padding_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_padding_left_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_inner_content_image_padding_layout2 betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );
	
	// Archive Page Category Inner Content Image Margin(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_margin_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_margin_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_margin_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_margin_layout2',
		'label'    => esc_html__('Content Image Margin', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' 	=> 'betterdocs_archive_inner_content_image_margin_layout2',
			'class' => 'betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );


	// Archive Page Category Inner Content Image Margin Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_margin_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_margin_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'		 	=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_margin_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_margin_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_inner_content_image_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );

	// Archive Page Category Inner Content Image Margin Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_margin_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_margin_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'		 	=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_margin_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_margin_right_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_inner_content_image_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );

	// Archive Page Category Inner Content Image Margin Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_margin_bottom_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_margin_bottom_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'		 	=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_margin_bottom_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_margin_bottom_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_inner_content_image_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );
	
	// Archive Page Category Inner Content Image Margin Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_inner_content_image_margin_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_inner_content_image_margin_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'		 	=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_inner_content_image_margin_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_inner_content_image_margin_left_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_inner_content_image_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 402
	) ) );

	// Archive Page Title Color (Archive Page Layout 2)

	$wp_customize->add_setting( 'betterdocs_archive_title_color_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_title_color_layout2'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_title_color_layout2',
		array(
			'label'      => esc_html__('Title Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_title_color_layout2',
			'priority' 	 => 402
		) )
	);

	// Archive Page Title Font Size (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_title_font_size_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_title_font_size_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_title_font_size_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_title_font_size_layout2',
		'label'    => esc_html__('Title Font Size', 'betterdocs-pro'),
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 100,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' 	 => 403
	) ) );

	// Archive Page Title Margin (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_title_margin_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_title_margin_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_title_margin_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_title_margin_layout2',
		'label'    => esc_html__('Archive Title Margin', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' 	=> 'betterdocs_archive_title_margin_layout2',
			'class' => 'betterdocs-dimension',
		),
		'priority' 	 => 404
	) ) );

	// Archive Page Title Margin Top (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_title_margin_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_title_margin_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'		 	=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_title_margin_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_title_margin_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_title_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 405
	) ) );


	// Archive Page Title Margin Right (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_title_margin_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_title_margin_right_layout2'],
		'capability'        => 'edit_theme_options',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_title_margin_right_layout2', array(
		'type'     	  => 'betterdocs-dimension',
		'section'  	  => 'betterdocs_archive_page_settings',
		'settings' 	  => 'betterdocs_archive_title_margin_right_layout2',
		'label'    	  => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_title_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 406
	) ) );

	// Archive Page Title Margin Bottom (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_title_margin_bottom_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_title_margin_bottom_layout2'],
		'capability'        => 'edit_theme_options',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_title_margin_bottom_layout2', array(
		'type'     	  => 'betterdocs-dimension',
		'section'  	  => 'betterdocs_archive_page_settings',
		'settings' 	  => 'betterdocs_archive_title_margin_bottom_layout2',
		'label'    	  => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_title_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 407
	) ) );

	// Archive Page Title Margin Left (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_title_margin_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_title_margin_left_layout2'],
		'capability'        => 'edit_theme_options',
		'transport'         => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_title_margin_left_layout2', array(
		'type'     	  => 'betterdocs-dimension',
		'section'  	  => 'betterdocs_archive_page_settings',
		'settings' 	  => 'betterdocs_archive_title_margin_left_layout2',
		'label'    	  => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_title_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 408
	) ) );

	// Archive Page Description Color (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_description_color_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_description_color_layout2'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_description_color_layout2',
		array(
			'label'      => esc_html__('Description Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_description_color_layout2',
			'priority' 	 => 409
		) )
	);

	// Archive Page Description Font Size (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_description_font_size_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_description_font_size_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_description_font_size_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_font_size_layout2',
		'label'    => esc_html__('Description Font Size', 'betterdocs-pro'),
		'input_attrs' => array(
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' 	 => 410
	) ) );

	// Archive Page Description Margin (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_description_margin_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_description_margin_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_description_margin_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin_layout2',
		'label'    => esc_html__('Archive Description Margin', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_description_margin_layout2',
			'class' => 'betterdocs-dimension',
		),
		'priority' 	 => 411
	) ) );

	// Archive Page Description Margin Top (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_description_margin_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_description_margin_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_description_margin_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_description_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 412
	) ) );

	// Archive Page Description Margin Right (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_description_margin_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_description_margin_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_description_margin_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin_right_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_description_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 413
	) ) );

	// Archive Page Description Margin Bottom (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_description_margin_bottom_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_description_margin_bottom_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_description_margin_bottom_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin_bottom_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_description_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 414
	) ) );

	// Archive Page Description Margin Left (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_description_margin_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_description_margin_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_description_margin_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_description_margin_left_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_description_margin_layout2 betterdocs-dimension',
		),
		'priority' 	 => 415
	) ) );


	// Archive Page List Item Color (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_item_color_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_list_item_color_layout2'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_item_color_layout2',
		array(
			'label'      => esc_html__('List Item Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_item_color_layout2',
			'priority' 	 => 416
		) )
	);

	// Archive Page List Item Hover Color (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_item_color_hover_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_list_item_color_hover_layout2'],
		'capability'    	=> 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_item_color_hover_layout2',
		array(
			'label'      => esc_html__('List Item Hover Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_item_color_hover_layout2',
			'priority' 	 => 417
		) )
	);

	// Archive Page List Item Hover Background Color (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_back_color_hover_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_list_back_color_hover_layout2'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_back_color_hover_layout2',
		array(
			'label'      => esc_html__('List Background Color Hover', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_back_color_hover_layout2',
			'priority' 	 => 417
		) )
	);

	// Archive Page List Item Hover Border Color (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_border_color_hover_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_list_border_color_hover_layout2'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_border_color_hover_layout2',
		array(
			'label'      => esc_html__('List Background Border Color Hover', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_border_color_hover_layout2',
			'priority' 	 => 417
		) )
	);

	// Archive Page List Item Hover Border Width (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_border_width_hover_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_list_border_width_hover_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_list_border_width_hover_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_border_width_hover_layout2',
		'label'    => esc_html__('List Item Border Width Hover', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_list_border_width_hover_layout2',
			'class' => 'betterdocs-dimension',
		),
		'priority' => 417
	) ) );

	// Archive Page List Item Hover Border Width Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_border_width_top_hover_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_list_border_width_top_hover_layout2'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_list_border_width_top_hover_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_border_width_top_hover_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_list_border_width_hover_layout2 betterdocs-dimension',
		),
		'priority' => 417
	) ) );

	// Archive Page List Item Hover Border Width Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_border_width_right_hover_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_list_border_width_right_hover_layout2'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_list_border_width_right_hover_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_border_width_right_hover_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_list_border_width_hover_layout2 betterdocs-dimension',
		),
		'priority' => 417
	) ) );

	// Archive Page List Item Hover Border Width Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_border_width_bottom_hover_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_list_border_width_bottom_hover_layout2'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_list_border_width_bottom_hover_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_border_width_bottom_hover_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_list_border_width_hover_layout2 betterdocs-dimension',
		),
		'priority' => 417
	) ) );

	// Archive Page List Item Hover Border Width Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_border_width_left_hover_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_list_border_width_left_hover_layout2'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_list_border_width_left_hover_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_border_width_left_hover_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_list_border_width_hover_layout2 betterdocs-dimension',
		),
		'priority' => 417
	) ) );

	// Archive Page List Item Border Style(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_border_style_layout2' , array(
        'default'     		=> $defaults['betterdocs_archive_list_border_style_layout2'],
        'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_list_border_style_layout2',
            array(
                'label'      => esc_html__('List Border Style', 'betterdocs-pro'),
                'section'    => 'betterdocs_archive_page_settings',
                'settings'   => 'betterdocs_archive_list_border_style_layout2',
                'type'    => 'select',
                'choices' => array(
					'none'	 => 'none',
                    'solid'  => 'solid',
                    'dashed' => 'dashed',
                    'dotted' => 'dotted',
                    'double' => 'double',
                    'groove' => 'groove',
                    'ridge'  => 'ridge',
					'inset'  => 'inset',
					'outset' => 'outset'
                ),
                'priority' => 417,
            ) )
    );

	// Archive Page List Item Border Width(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_list_border_width_layout2', array(
		'default' 			=> $defaults['betterdocs_archive_list_border_width_layout2'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_list_border_width_layout2', array(
		'type' 	   	  => 'betterdocs-title',
		'section'  	  => 'betterdocs_archive_page_settings',
		'settings'	  => 'betterdocs_archive_list_border_width_layout2',
		'label' 	  => __('List Border Width', 'betterdocs-pro'),
		'priority'    => 417,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_archive_list_border_width_layout2',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Archive Page List Item Border Width Top(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_list_border_width_top_layout2', array(
		'default' 			=> $defaults['betterdocs_archive_list_border_width_top_layout2'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_archive_list_border_width_top_layout2', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_archive_page_settings',
				'settings' 	  => 'betterdocs_archive_list_border_width_top_layout2',
				'label' 	  => __('Top', 'betterdocs-pro'),
				'priority' 	  => 417,
				'input_attrs' => array(
					'class' => 'betterdocs_archive_list_border_width_layout2 betterdocs-dimension',
				),
			)
		)
	);

	// Archive Page List Item Border Width Right(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_list_border_width_right_layout2', array(
		'default' 			=> $defaults['betterdocs_archive_list_border_width_right_layout2'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_archive_list_border_width_right_layout2', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_archive_page_settings',
				'settings' 	  => 'betterdocs_archive_list_border_width_right_layout2',
				'label' 	  => __('Right', 'betterdocs-pro'),
				'priority' 	  => 417,
				'input_attrs' => array(
					'class' => 'betterdocs_archive_list_border_width_layout2 betterdocs-dimension',
				),
			)
		)
	);

	// Archive Page List Item Border Width Bottom(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_list_border_width_bottom_layout2', array(
		'default' 			=> $defaults['betterdocs_archive_list_border_width_bottom_layout2'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_archive_list_border_width_bottom_layout2', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_archive_page_settings',
				'settings' 	  => 'betterdocs_archive_list_border_width_bottom_layout2',
				'label' 	  => __('Bottom', 'betterdocs-pro'),
				'priority' 	  => 417,
				'input_attrs' => array(
					'class' => 'betterdocs_archive_list_border_width_layout2 betterdocs-dimension',
				),
			)
		)
	);

	// Archive Page List Item Border Width Left(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_list_border_width_left_layout2', array(
		'default' 			=> $defaults['betterdocs_archive_list_border_width_left_layout2'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(
		new BetterDocs_Dimension_Control(
			$wp_customize, 'betterdocs_archive_list_border_width_left_layout2', 
			array(
				'type' 		  => 'betterdocs-dimension',
				'section' 	  => 'betterdocs_archive_page_settings',
				'settings' 	  => 'betterdocs_archive_list_border_width_left_layout2',
				'label' 	  => __('Left', 'betterdocs-pro'),
				'priority' 	  => 417,
				'input_attrs' => array(
					'class' => 'betterdocs_archive_list_border_width_layout2 betterdocs-dimension',
				),
			)
		)
	);

	// Archive Page List Item Border Color Top(Archive Page Layout 2) 
	$wp_customize->add_setting( 
		'betterdocs_archive_list_border_width_color_top_layout2' ,
	 	array(
			'default'     		=> $defaults['betterdocs_archive_list_border_width_color_top_layout2'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_border_width_color_top_layout2',
		array(
			'label'      => __( 'List Border Color Top', 'betterdocs-pro' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_border_width_color_top_layout2',
			'priority'   => 417,
		) )
	);

	// Archive Page List Item Border Color Right(Archive Page Layout 2) 
	$wp_customize->add_setting( 
		'betterdocs_archive_list_border_width_color_right_layout2' ,
	 	array(
			'default'     		=> $defaults['betterdocs_archive_list_border_width_color_right_layout2'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_border_width_color_right_layout2',
		array(
			'label'      => __( 'List Border Color Right', 'betterdocs-pro' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_border_width_color_right_layout2',
			'priority'   => 417,
		) )
	);

	// Archive Page List Item Border Color Bottom(Archive Page Layout 2) 
	$wp_customize->add_setting( 
		'betterdocs_archive_list_border_width_color_bottom_layout2' ,
	 	array(
			'default'     		=> $defaults['betterdocs_archive_list_border_width_color_bottom_layout2'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_border_width_color_bottom_layout2',
		array(
			'label'      => __( 'List Border Color Bottom', 'betterdocs-pro' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_border_width_color_bottom_layout2',
			'priority'   => 417,
		) )
	);

	// Archive Page List Item Border Color Left(Archive Page Layout 2) 
	$wp_customize->add_setting( 
		'betterdocs_archive_list_border_width_color_left_layout2' ,
	 	array(
			'default'     		=> $defaults['betterdocs_archive_list_border_width_color_left_layout2'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_border_width_color_left_layout2',
		array(
			'label'      => __( 'List Border Color Left', 'betterdocs-pro' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_border_width_color_left_layout2',
			'priority'   => 417,
		) )
	);
	
	// Archive Page List Item Font Size(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_item_font_size_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_list_item_font_size_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_list_item_font_size_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_item_font_size_layout2',
		'label'    => esc_html__('List Item Font Size', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 50,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 418
	) ) );

	// Archive Page Docs List Margin(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_margin_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin_layout2',
		'label'    => esc_html__('Docs List Margin', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_article_list_margin_layout2',
			'class' => 'betterdocs-dimension',
		),
		'priority' => 419
	) ) );

	// Archive Page Docs List Margin Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_margin_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_margin_layout2 betterdocs-dimension',
		),
		'priority' => 420
	) ) );

	// Archive Page Docs List Margin Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_margin_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin_right_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_margin_layout2 betterdocs-dimension',
		),
		'priority' => 421
	) ) );

	// Archive Page Docs List Margin Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin_bottom_layout2', array(
		'default'       => $defaults['betterdocs_archive_article_list_margin_bottom_layout2'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin_bottom_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin_bottom_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_margin_layout2 betterdocs-dimension',
		),
		'priority' => 422
	) ) );

	// Archive Page Docs List Margin Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_margin_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_margin_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_margin_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_margin_left_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_margin_layout2 betterdocs-dimension',
		),
		'priority' => 423
	) ) );

	// Archive Page Doc List Font Weight(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_font_weight_layout2' , array(
        'default'     		=> $defaults['betterdocs_archive_article_list_font_weight_layout2'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_article_list_font_weight_layout2',
            array(
                'label'      => esc_html__('List Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_archive_page_settings',
                'settings'   => 'betterdocs_archive_article_list_font_weight_layout2',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 425
            ) )
    );

	// Archive Page List Item Line Height(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_item_line_height_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_list_item_line_height_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_list_item_line_height_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_item_line_height_layout2',
		'label'    => esc_html__('List Item Line Height', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 426
	) ) );

	// Archive Page List Item Arrow Height(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_item_arrow_height_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_list_item_arrow_height_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_list_item_arrow_height_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_item_arrow_height_layout2',
		'label'    => esc_html__('List Item Arrow Height', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 427
	) ) );


	// Archive Page List Item Arrow Width(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_item_arrow_width_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_list_item_arrow_width_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_list_item_arrow_width_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_list_item_arrow_width_layout2',
		'label'    => esc_html__('List Item Arrow Width', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 428
	) ) );

	// Archive Page List Item Arrow Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_list_item_arrow_color_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_list_item_arrow_color_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_list_item_arrow_color_layout2',
		array(
			'label'      => esc_html__('List Item Arrow Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_list_item_arrow_color_layout2',
			'priority' 	 => 429
		) )
	);

	// Archive Page Docs Arrow Margin(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_arrow_margin_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_arrow_margin_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_article_list_arrow_margin_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_arrow_margin_layout2',
		'label'    => esc_html__('Docs List Arrow Margin', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' => 'betterdocs_archive_article_list_arrow_margin_layout2',
			'class' => 'betterdocs-dimension',
		),
		'priority' => 430
	) ) );

	// Archive Page Docs List Arrow Margin Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_arrow_margin_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_arrow_margin_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_arrow_margin_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_arrow_margin_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_arrow_margin_layout2 betterdocs-dimension',
		),
		'priority' => 431
	) ) );

	// Archive Page Docs List Arrow Margin Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_arrow_margin_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_arrow_margin_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_arrow_margin_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_arrow_margin_right_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_arrow_margin_layout2 betterdocs-dimension',
		),
		'priority' => 432
	) ) );

	// Archive Page Docs List Arrow Margin Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_arrow_margin_bottom_layout2', array(
		'default'       => $defaults['betterdocs_archive_article_list_arrow_margin_bottom_layout2'],
		'capability'    => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_arrow_margin_bottom_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_arrow_margin_bottom_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_arrow_margin_layout2 betterdocs-dimension',
		),
		'priority' => 433
	) ) );

	// Archive Page Docs List Arrow Margin Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_arrow_margin_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_arrow_margin_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_arrow_margin_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_arrow_margin_left_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_arrow_margin_layout2 betterdocs-dimension',
		),
		'priority' => 434
	) ) );	

	// Archive Page Excerpt Font Weight(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_font_weight_layout2' , array(
        'default'     		=> $defaults['betterdocs_archive_article_list_excerpt_font_weight_layout2'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_article_list_excerpt_font_weight_layout2',
            array(
                'label'      => esc_html__('Excerpt Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_archive_page_settings',
                'settings'   => 'betterdocs_archive_article_list_excerpt_font_weight_layout2',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 435
            ) )
    );

	// Archive Page Excerpt Font Size(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_font_size_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_font_size_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_font_size_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_font_size_layout2',
		'label'    => esc_html__('Excerpt Font Size', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 436
	) ) );

	// Archive Page Excerpt Font Line Height(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_font_line_height_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_font_line_height_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_font_line_height_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_font_line_height_layout2',
		'label'    => esc_html__('Excerpt Line Height', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 437
	) ) );

	// Archive Page Excerpt Font Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_font_color_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_article_list_excerpt_font_color_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_article_list_excerpt_font_color_layout2',
		array(
			'label'      => __( 'Excerpt Font Color', 'betterdocs-pro' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_article_list_excerpt_font_color_layout2',
			'priority' => 438,
		) )
	);

	// Archive Page Excerpt Margin(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_margin_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_margin_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_margin_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_margin_layout2',
		'label'    => esc_html__('Excerpt Margin', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' 	=> 'betterdocs_archive_article_list_excerpt_margin_layout2',
			'class' => 'betterdocs-dimension',
		),
		'priority' => 439
	) ) );
	
	// Archive Page Excerpt Margin Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_margin_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_margin_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_margin_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_margin_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_excerpt_margin_layout2 betterdocs-dimension',
		),
		'priority' => 440
	) ) );
	
	// Archive Page Excerpt Margin Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_margin_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_margin_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_margin_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_margin_right_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_excerpt_margin_layout2 betterdocs-dimension',
		),
		'priority' => 441
	) ) );
	
	// Archive Page Excerpt Margin Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_margin_bottom_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_margin_bottom_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_margin_bottom_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_margin_bottom_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_excerpt_margin_layout2 betterdocs-dimension',
		),
		'priority' => 442
	) ) );

	// Archive Page Excerpt Margin Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_margin_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_margin_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_margin_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_margin_left_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_excerpt_margin_layout2 betterdocs-dimension',
		),
		'priority' => 443
	) ) );	

	// Archive Page Excerpt Padding(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_padding_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_padding_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_padding_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_padding_layout2',
		'label'    => esc_html__('Excerpt Padding', 'betterdocs-pro'),
		'input_attrs' => array(
			'id' 	=> 'betterdocs_archive_article_list_excerpt_padding_layout2',
			'class' => 'betterdocs-dimension',
		),
		'priority' => 444
	) ) );

	// Archive Page Excerpt Padding Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_padding_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_padding_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_padding_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_padding_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_excerpt_padding_layout2 betterdocs-dimension',
		),
		'priority' => 445
	) ) );

	// Archive Page Excerpt Padding Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_padding_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_padding_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_padding_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_padding_right_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_excerpt_padding_layout2 betterdocs-dimension',
		),
		'priority' => 446
	) ) );

	// Archive Page Excerpt Padding Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_padding_bottom_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_padding_bottom_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_padding_bottom_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_padding_bottom_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_excerpt_padding_layout2 betterdocs-dimension',
		),
		'priority' => 447
	) ) );

	// Archive Page Excerpt Padding Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_excerpt_padding_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_excerpt_padding_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_excerpt_padding_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_excerpt_padding_left_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_excerpt_padding_layout2 betterdocs-dimension',
		),
		'priority' => 448
	) ) );

	// Archive Page Category Item Count Separator(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_article_list_counter_seperator_layout2', array(
		'default'           => $defaults['betterdocs_archive_article_list_counter_seperator_layout2'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_seperator_layout2', array(
		'label'	      	=> esc_html__('Category Item Count', 'betterdocs-pro'),
		'settings'		=> 'betterdocs_archive_article_list_counter_seperator_layout2',
		'section'  		=> 'betterdocs_archive_page_settings',
		'priority' 		=> 449
	)));

	// Archive Page Category Item Count Font Weight(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_font_weight_layout2' , array(
        'default'     		=> $defaults['betterdocs_archive_article_list_counter_font_weight_layout2'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_article_list_counter_font_weight_layout2',
            array(
                'label'      => esc_html__('Count Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_archive_page_settings',
                'settings'   => 'betterdocs_archive_article_list_counter_font_weight_layout2',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 450
            ) )
    );

	// Archive Page Category Item Count Font Size(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_font_size_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_font_size_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_font_size_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_font_size_layout2',
		'label'    => esc_html__('Count Font Size', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 451
	) ) );

	// Archive Page Category Item Count Font Line Height(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_font_line_height_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_font_line_height_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_font_line_height_layout2', array(
		'type'     => 'betterdocs-range-value',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_font_line_height_layout2',
		'label'    => esc_html__('Count Font Line Height', 'betterdocs-pro'),
		'input_attrs' => array(
			'class'  => '',
			'min'    => 0,
			'max'    => 200,
			'step'   => 1,
			'suffix' => 'px', //optional suffix
		),
		'priority' => 452
	) ) );

	// Archive Page Category Item Count Font Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_font_color_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_article_list_counter_font_color_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_article_list_counter_font_color_layout2',
		array(
			'label'      => __( 'Count Font Color', 'betterdocs-pro' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_article_list_counter_font_color_layout2',
			'priority' => 452,
		) )
	);

	//Archive Page Category Item Count Border Radius(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_border_radius_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_border_radius_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_border_radius_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_border_radius_layout2',
		'label'    => esc_html__('Count Border Radius', 'betterdocs-pro'),
		'priority' => 453,
		'input_attrs' => array(
			'id' => 'betterdocs_archive_article_list_counter_border_radius_layout2',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Border Radius Top Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_border_radius_top_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_border_radius_top_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_border_radius_top_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_border_radius_top_left_layout2',
		'label'    => esc_html__('Top Left', 'betterdocs-pro'),
		'priority' => 454,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_border_radius_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Border Radius Top Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_border_radius_top_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_border_radius_top_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_border_radius_top_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_border_radius_top_right_layout2',
		'label'    => esc_html__('Top Right', 'betterdocs-pro'),
		'priority' => 455,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_border_radius_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Border Radius Bottom Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_border_radius_bottom_right_layout2',
		'label'    => esc_html__('Bottom Right', 'betterdocs-pro'),
		'priority' => 456,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_border_radius_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Border Radius Bottom Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_border_radius_bottom_left_layout2',
		'label'    => esc_html__('Bottom Left', 'betterdocs-pro'),
		'priority' => 457,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_border_radius_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Margin(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_margin_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_margin_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_margin_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_margin_layout2',
		'label'    => esc_html__('Count Margin', 'betterdocs-pro'),
		'priority' => 458,
		'input_attrs' => array(
			'id' => 'betterdocs_archive_article_list_counter_margin_layout2',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Margin Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_margin_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_margin_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_margin_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_margin_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'priority' => 459,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_margin_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Margin Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_margin_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_margin_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_margin_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_margin_right_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'priority' => 460,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_margin_layout2 betterdocs-dimension',
		),
	) ) );
	
	//Archive Page Category Item Count Margin Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_margin_bottom_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_margin_bottom_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_margin_bottom_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_margin_bottom_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'priority' => 461,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_margin_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Margin Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_margin_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_margin_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_margin_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_margin_left_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'priority' => 462,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_margin_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Padding(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_padding_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_padding_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_padding_layout2', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_padding_layout2',
		'label'    => esc_html__('Count Padding', 'betterdocs-pro'),
		'priority' => 463,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_archive_article_list_counter_padding_layout2',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Padding Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_padding_top_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_padding_top_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	
	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_padding_top_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_padding_top_layout2',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'priority' => 464,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_padding_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Padding Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_padding_right_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_padding_right_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	
	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_padding_right_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_padding_right_layout2',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'priority' => 465,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_padding_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Padding Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_padding_bottom_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_padding_bottom_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	
	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_padding_bottom_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_padding_bottom_layout2',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'priority' => 466,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_padding_layout2 betterdocs-dimension',
		),
	) ) );

	//Archive Page Category Item Count Padding Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_padding_left_layout2', array(
		'default'       	=> $defaults['betterdocs_archive_article_list_counter_padding_left_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	
	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_article_list_counter_padding_left_layout2', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_article_list_counter_padding_left_layout2',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'priority' => 467,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_article_list_counter_padding_layout2 betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Item Count Border Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_border_color_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_article_list_counter_border_color_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_article_list_counter_border_color_layout2',
		array(
			'label'      => __( 'Count Border Color', 'betterdocs-pro' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_article_list_counter_border_color_layout2',
			'priority'   => 468,
		) )
	);

	// Archive Page Category Item Count Background Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_article_list_counter_back_color_layout2' , array(
		'default'     		=> $defaults['betterdocs_archive_article_list_counter_back_color_layout2'],
		'capability'    	=> 'edit_theme_options',
		'transport'   		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_article_list_counter_back_color_layout2',
		array(
			'label'      => __( 'Count Background Color', 'betterdocs-pro' ),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_article_list_counter_back_color_layout2',
			'priority'   => 468,
		) )
	);

	// Archive Page Category Other Categories Seperator(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_seperator', array(
		'default'           => $defaults['betterdocs_archive_other_categories_seperator'],
		'sanitize_callback' => 'esc_html',
	));

	$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
		$wp_customize, 'betterdocs_archive_other_categories_seperator', array(
		'label'	      	=> esc_html__('Other Categories', 'betterdocs-pro'),
		'settings'		=> 'betterdocs_archive_other_categories_seperator',
		'section'  		=> 'betterdocs_archive_page_settings',
		'priority' 		=> 469
	)));

	//Archive Page Category Other Categories Title Text(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_heading_text', array(
        'default' => $defaults['betterdocs_archive_other_categories_heading_text'],
        'capability'    => 'edit_theme_options',
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_other_categories_heading_text',
            array(
                'label' => esc_html__('Heading Text', 'betterdocs-pro'),
                'section' => 'betterdocs_archive_page_settings',
                'settings' => 'betterdocs_archive_other_categories_heading_text',
                'type' => 'text',
                'priority' 	 => 470
            )
        )
    );

	//Archive Page Category Other Categories Load More Text(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_load_more_text', array(
        'default' => $defaults['betterdocs_archive_other_categories_load_more_text'],
        'capability'    => 'edit_theme_options',
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_other_categories_load_more_text',
            array(
                'label' => esc_html__('Load More Text', 'betterdocs-pro'),
                'section' => 'betterdocs_archive_page_settings',
                'settings' => 'betterdocs_archive_other_categories_load_more_text',
                'type' => 'text',
                'priority' 	 => 470
            )
        )
    );

	// Archive Page Category Other Categories Title Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_color' , array(
		'default'     		=> $defaults['betterdocs_archive_other_categories_title_color'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_other_categories_title_color',
		array(
			'label'      => esc_html__('Title Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_other_categories_title_color',
			'priority'	 => 470
		) )
	);
	
	// Archive Page Category Other Categories Hover Title Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_hover_color' , array(
		'default'     		=> $defaults['betterdocs_archive_other_categories_title_hover_color'],
		'capability'    	=> 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_other_categories_title_hover_color',
		array(
			'label'      => esc_html__('Title Hover Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_other_categories_title_hover_color',
			'priority'	 => 470
		) )
	);

	// Archive Page Category Other Categories Title Font Style(Archive Page Layout 2)
	$wp_customize->add_setting( 
		'betterdocs_archive_other_categories_title_font_weight' , 
		array(
			'default'     		=> $defaults['betterdocs_archive_other_categories_title_font_weight'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_choices',
    	) 
	);

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_other_categories_title_font_weight',
            array(
                'label'      => esc_html__('Title Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_archive_page_settings',
                'settings'   => 'betterdocs_archive_other_categories_title_font_weight',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 471
        	) 
		)
    );

	// Archive Page Category Other Categories Title Font Size(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_title_font_size', array(
		'default'			=> $defaults['betterdocs_archive_other_categories_title_font_size'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_font_size', array(
		'type' => 'betterdocs-range-value',
		'section' => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_font_size',
		'label' => __('Title Font Size', 'betterdocs-pro'),
		'priority' => 472,
		'input_attrs' => array(
			'class' => '',
			'min' => 0,
			'max' => 100,
			'step' => 1,
			'suffix' => 'px', //optional suffix
		),
	)));

	// Archive Page Category Other Categories Title Line Height(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_title_line_height', array(
		'default'			=> $defaults['betterdocs_archive_other_categories_title_line_height'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_line_height', array(
		'type' => 'betterdocs-range-value',
		'section' => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_line_height',
		'label' => __('Title Line Height', 'betterdocs-pro'),
		'priority' => 473,
		'input_attrs' => array(
			'class' => '',
			'min' => 0,
			'max' => 100,
			'step' => 1,
			'suffix' => 'px', //optional suffix
		),
	)));

	// Archive Page Category Other Categories Image Size(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_image_size', array(
		'default'			=> $defaults['betterdocs_archive_other_categories_image_size'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_other_categories_image_size', array(
		'type' => 'betterdocs-range-value',
		'section' => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_image_size',
		'label' => __('Image Size', 'betterdocs-pro'),
		'priority' => 473,
		'input_attrs' => array(
			'class' => '',
			'min' => 0,
			'max' => 200,
			'step' => 1,
			'suffix' => '%', //optional suffix
		),
	)));

	// Archive Page Category Other Categories Title Padding(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_title_padding', array(
		'default' 			=> $defaults['betterdocs_archive_other_categories_title_padding'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_padding', array(
		'type' => 'betterdocs-title',
		'section' => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_padding',
		'label' => __('Title Padding', 'betterdocs-pro'),
		'priority' => 474,
		'input_attrs' => array(
			'id' => 'betterdocs_archive_other_categories_title_padding',
			'class' => 'betterdocs-dimension',
		),
	)));

	
	// Archive Page Category Other Categories Title Padding Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_padding_top', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_title_padding_top'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_padding_top',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'priority' => 475,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_title_padding betterdocs-dimension',
		)
	) ) );

	// Archive Page Category Other Categories Title Padding Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_padding_right', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_title_padding_right'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_padding_right',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_title_padding betterdocs-dimension',
		),
		'priority' => 476
	) ) );

	// Archive Page Category Other Categories Title Padding Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_padding_bottom', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_title_padding_bottom'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_padding_bottom',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_title_padding betterdocs-dimension',
		),
		'priority' => 477
	) ) );


	// Archive Page Category Other Categories Title Padding Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_padding_left', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_title_padding_left'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_padding_left',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_title_padding betterdocs-dimension',
		),
		'priority' => 478
	) ) );

	// Archive Page Category Other Categories Title Margin(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_title_margin', array(
		'default' 			=> $defaults['betterdocs_archive_other_categories_title_margin'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_margin', array(
		'type' => 'betterdocs-title',
		'section' => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_margin',
		'label' => __('Title Margin', 'betterdocs-pro'),
		'priority' => 479,
		'input_attrs' => array(
			'id' => 'betterdocs_archive_other_categories_title_margin',
			'class' => 'betterdocs-dimension',
		),
	)));

	// Archive Page Category Other Categories Title Margin Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_margin_top', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_title_margin_top'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_margin_top',
		'label'    => esc_html__('Top', 'betterdocs-pro'),
		'priority' => 480,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_title_margin betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Title Margin Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_margin_right', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_title_margin_right'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_margin_right',
		'label'    => esc_html__('Right', 'betterdocs-pro'),
		'priority' => 481,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_title_margin betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Title Margin Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_margin_bottom', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_title_margin_bottom'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_margin_bottom',
		'label'    => esc_html__('Bottom', 'betterdocs-pro'),
		'priority' => 482,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_title_margin betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Title Margin Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_title_margin_left', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_title_margin_left'],
		'capability'    	=> 'edit_theme_options',
		'transport'			=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_title_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_title_margin_left',
		'label'    => esc_html__('Left', 'betterdocs-pro'),
		'priority' => 483,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_title_margin betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_color' , array(
		'default'     		=> $defaults['betterdocs_archive_other_categories_count_color'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_other_categories_count_color',
		array(
			'label'      => esc_html__('Count Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_other_categories_count_color',
			'priority'	 => 484
		) )
	);

	// Archive Page Category Other Categories Count Background Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_back_color' , array(
		'default'     		=> $defaults['betterdocs_archive_other_categories_count_back_color'],
		'capability'    	=> 'edit_theme_options',
	    'transport'   		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_other_categories_count_back_color',
		array(
			'label'      => esc_html__('Count Background Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_other_categories_count_back_color',
			'priority'	 => 485
		) )
	);

	// Archive Page Category Other Categories Count Background Hover Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_back_color_hover' , array(
		'default'     		=> $defaults['betterdocs_archive_other_categories_count_back_color_hover'],
		'capability'    	=> 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_other_categories_count_back_color_hover',
		array(
			'label'      => esc_html__('Count Background Hover Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_other_categories_count_back_color_hover',
			'priority'	 => 486
		) )
	);

	// Archive Page Category Other Categories Count Line Height(Archive Page Layout 2)
	$wp_customize->add_setting(
		'betterdocs_archive_other_categories_count_line_height', 
		array(
			'default' 			=> $defaults['betterdocs_archive_other_categories_count_line_height'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_archive_other_categories_count_line_height', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_archive_page_settings',
				'settings' => 'betterdocs_archive_other_categories_count_line_height',
				'label'    => __('Count Line Height', 'betterdocs-pro'),
				'priority' => 487,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	// Archive Page Category Other Categories Count Font Weight (Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_font_weight' , array(
        'default'     		=> $defaults['betterdocs_archive_other_categories_count_font_weight'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_other_categories_count_font_weight',
            array(
                'label'      => esc_html__('Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_archive_page_settings',
                'settings'   => 'betterdocs_archive_other_categories_count_font_weight',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 488
            ) )
    );

	// Archive Page Category Other Categories Count Font Size(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_count_font_size', array(
		'default' 			=> $defaults['betterdocs_archive_other_categories_count_font_size'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_font_size', array(
		'type' => 'betterdocs-range-value',
		'section' => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_font_size',
		'label' => __('Count Font Size', 'betterdocs-pro'),
		'priority' => 489,
		'input_attrs' => array(
			'class' => '',
			'min' => 0,
			'max' => 50,
			'step' => 1,
			'suffix' => 'px', //optional suffix
		),
	)));

	// Archive Page Category Other Categories Count Border Radius(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_border_radius', array(
        'default'       	=> $defaults['betterdocs_archive_other_categories_count_border_radius'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_archive_other_categories_count_border_radius', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_archive_page_settings',
        'settings' => 'betterdocs_archive_other_categories_count_border_radius',
        'label'    => esc_html__('Count Border Radius', 'betterdocs-pro'),
		'priority' => 490,
        'input_attrs' => array(
            'id' 	=> 'betterdocs_archive_other_categories_count_border_radius',
            'class' => 'betterdocs-dimension',
        )
    ) ) );

	// Archive Page Category Other Categories Count Border Radius Top Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_border_radius_topleft', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_border_radius_topleft'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer',
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_border_radius_topleft', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_border_radius_topleft',
		'label'    => __( 'Top Left', 'betterdocs-pro' ),
		'priority' => 490,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_border_radius betterdocs-dimension',
		)
	) ) );

	// Archive Page Category Other Categories Count Border Radius Top Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_border_radius_topright', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_border_radius_topright'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_border_radius_topright', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_border_radius_topright',
		'label'    => __( 'Top Right', 'betterdocs-pro' ),
		'priority' => 491,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_border_radius betterdocs-dimension',
		)
	) ) );

	// Archive Page Category Other Categories Count Border Radius Bottom Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_border_radius_bottomright', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_border_radius_bottomright'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_border_radius_bottomright', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_border_radius_bottomright',
		'label'    => __( 'Bottom Right', 'betterdocs-pro' ),
		'priority' => 492,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_border_radius betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count Border Radius Bottom Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_border_radius_bottomleft', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_border_radius_bottomleft'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_border_radius_bottomleft', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_border_radius_bottomleft',
		'label'    => __( 'Bottom Right', 'betterdocs-pro' ),
		'priority' => 493,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_border_radius betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count Padding(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_padding', array(
        'default'       	=> $defaults['betterdocs_archive_other_categories_count_padding'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_archive_other_categories_count_padding', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_archive_page_settings',
        'settings' => 'betterdocs_archive_other_categories_count_padding',
        'label'    => esc_html__('Count Padding', 'betterdocs-pro'),
		'priority' => 494,
        'input_attrs' => array(
            'id' 	=> 'betterdocs_archive_other_categories_count_padding',
            'class' => 'betterdocs-dimension',
        )
    ) ) );

	// Archive Page Category Other Categories Count Padding Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_padding_top', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_padding_top'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_padding_top',
		'label'    => __( 'Top', 'betterdocs-pro' ),
		'priority' => 495,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count Padding Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_padding_right', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_padding_right'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_padding_right',
		'label'    => __( 'Right', 'betterdocs-pro' ),
		'priority' => 496,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count Padding Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_padding_bottom', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_padding_bottom'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs-pro' ),
		'priority' => 497,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count Padding Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_padding_left', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_padding_left'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_padding_left',
		'label'    => __( 'Left', 'betterdocs-pro' ),
		'priority' => 498,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count Margin(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_margin', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_margin'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_margin', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_margin',
		'label'    => esc_html__('Count Margin', 'betterdocs-pro'),
		'priority' => 499,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_archive_other_categories_count_margin',
			'class' => 'betterdocs-dimension',
		)
	) ) );

	// Archive Page Category Other Categories Count Margin Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_margin_top', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_margin_top'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_margin_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_margin_top',
		'label'    => __( 'Top', 'betterdocs-pro' ),
		'priority' => 500,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_margin betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count Margin Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_margin_right', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_margin_right'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_margin_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_margin_right',
		'label'    => __( 'Right', 'betterdocs-pro' ),
		'priority' => 501,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_margin betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count Margin Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_margin_bottom', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_margin_bottom'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_margin_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_margin_bottom',
		'label'    => __( 'Bottom', 'betterdocs-pro' ),
		'priority' => 502,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_margin betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Count Margin Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_count_margin_left', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_count_margin_left'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_count_margin_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_count_margin_left',
		'label'    => __( 'Left', 'betterdocs-pro' ),
		'priority' => 503,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_count_margin betterdocs-dimension',
		),
	) ) );
	
	// Archive Page Category Other Categories Description Color(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_description_color' , array(
		'default'     		=> $defaults['betterdocs_archive_other_categories_description_color'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_archive_other_categories_description_color',
		array(
			'label'      => esc_html__('Description Color', 'betterdocs-pro'),
			'section'    => 'betterdocs_archive_page_settings',
			'settings'   => 'betterdocs_archive_other_categories_description_color',
			'priority'	 => 504
		) )
	);

	// Archive Page Category Other Categories Description Font Weight(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_description_font_weight' , array(
        'default'     		=> $defaults['betterdocs_archive_other_categories_description_font_weight'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_other_categories_description_font_weight',
            array(
                'label'      => esc_html__('Description Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_archive_page_settings',
                'settings'   => 'betterdocs_archive_other_categories_description_font_weight',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 505
            ) )
    );

	// Archive Page Category Other Categories Description Font Size(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_description_font_size', array(
		'default' 			=> $defaults['betterdocs_archive_other_categories_description_font_size'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_other_categories_description_font_size', array(
		'type' => 'betterdocs-range-value',
		'section' => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_description_font_size',
		'label' => __('Description Font Size', 'betterdocs-pro'),
		'priority' => 506,
		'input_attrs' => array(
			'class' => '',
			'min' => 0,
			'max' => 50,
			'step' => 1,
			'suffix' => 'px', //optional suffix
		),
	)));

	// Archive Page Category Other Categories Description Line Height(Archive Page Layout 2)
	$wp_customize->add_setting(
		'betterdocs_archive_other_categories_description_line_height', 
		array(
			'default' 			=> $defaults['betterdocs_archive_other_categories_description_line_height'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_archive_other_categories_description_line_height', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_archive_page_settings',
				'settings' => 'betterdocs_archive_other_categories_description_line_height',
				'label'    => __('Description Font Line Height', 'betterdocs-pro'),
				'priority' => 507,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	// Archive Page Category Other Categories Description Padding(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_description_padding', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_description_padding'],
		'capability'    	=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	) );

	$wp_customize->add_control( new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_archive_other_categories_description_padding', array(
		'type'     => 'betterdocs-title',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_description_padding',
		'label'    => __( 'Description Padding', 'betterdocs-pro' ),
		'priority' => 508,
		'input_attrs' => array(
			'id' 	=> 'betterdocs_archive_other_categories_description_padding',
			'class' => 'betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Description Padding Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_description_padding_top', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_description_padding_top'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_description_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_description_padding_top',
		'label'    => __( 'Top', 'betterdocs-pro' ),
		'priority' => 509,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_description_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Description Padding Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_description_padding_right', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_description_padding_right'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_description_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_description_padding_right',
		'label'    => __( 'Right', 'betterdocs-pro' ),
		'priority' => 510,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_description_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Description Padding Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_description_padding_bottom', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_description_padding_bottom'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_description_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_description_padding_bottom',
		'label'    => __( 'Right', 'betterdocs-pro' ),
		'priority' => 511,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_description_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Description Padding Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_description_padding_left', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_description_padding_left'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	) );

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_description_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_description_padding_left',
		'label'    => __( 'Left', 'betterdocs-pro' ),
		'priority' => 512,
		'input_attrs' => array(
			'class'   => 'betterdocs_archive_other_categories_description_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Button Color(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_button_color', array(
		'default' 			=> $defaults['betterdocs_archive_other_categories_button_color'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_archive_other_categories_button_color',
			array(
				'label'    => __('Button Color', 'betterdocs-pro'),
				'section'  => 'betterdocs_archive_page_settings',
				'settings' => 'betterdocs_archive_other_categories_button_color',
				'priority' => 513
			))
	);

	// Archive Page Category Other Categories Button Background Color(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_button_back_color', array(
		'default' 			=> $defaults['betterdocs_archive_other_categories_button_back_color'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_archive_other_categories_button_back_color',
			array(
				'label'    => __('Button Background Color', 'betterdocs-pro'),
				'section'  => 'betterdocs_archive_page_settings',
				'settings' => 'betterdocs_archive_other_categories_button_back_color',
				'priority' => 514
			))
	);

	// Archive Page Category Other Categories Button Background Hover Color(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_button_back_color_hover', array(
		'default' 			=> $defaults['betterdocs_archive_other_categories_button_back_color_hover'],
		'capability' 		=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_archive_other_categories_button_back_color_hover',
			array(
				'label'    => __('Button Background Hover Color', 'betterdocs-pro'),
				'section'  => 'betterdocs_archive_page_settings',
				'settings' => 'betterdocs_archive_other_categories_button_back_color_hover',
				'priority' => 514
			))
	);

	// Archive Page Category Other Categories Button Font Weight(Archive Page Layout 2)
	$wp_customize->add_setting( 
		'betterdocs_archive_other_categories_button_font_weight' , 
		array(
			'default'     		=> $defaults['betterdocs_archive_other_categories_button_font_weight'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_choices',
    	) 
	);

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_archive_other_categories_button_font_weight',
            array(
                'label'      => esc_html__('Button Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_archive_page_settings',
                'settings'   => 'betterdocs_archive_other_categories_button_font_weight',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 515
        	) 
		)
    );

	// Archive Page Category Other Categories Button Font Size(Archive Page Layout 2)
	$wp_customize->add_setting('betterdocs_archive_other_categories_button_font_size', array(
		'default' 	        => $defaults['betterdocs_archive_other_categories_button_font_size'],
		'capability' 	    => 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_archive_other_categories_button_font_size', array(
		'type' => 'betterdocs-range-value',
		'section' => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_button_font_size',
		'label' => __('Button Font Size', 'betterdocs-pro'),
		'priority' => 516,
		'input_attrs' => array(
			'class' => '',
			'min' => 0,
			'max' => 100,
			'step' => 1,
			'suffix' => 'px', //optional suffix
		),
	)));

	// Archive Page Category Other Categories Button Font Line Height(Archive Page Layout 2)
	$wp_customize->add_setting(
		'betterdocs_archive_other_categories_button_font_line_height', 
		array(
			'default' 			=> $defaults['betterdocs_archive_other_categories_button_font_line_height'],
			'capability' 		=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control(
		new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 
			'betterdocs_archive_other_categories_button_font_line_height', 
			array(
				'type'     => 'betterdocs-range-value',
				'section'  => 'betterdocs_archive_page_settings',
				'settings' => 'betterdocs_archive_other_categories_button_font_line_height',
				'label'    => __('List Font Line Height', 'betterdocs-pro'),
				'priority' => 517,
				'input_attrs' => array(
					'class' => '',
					'min' => 0,
					'max' => 500,
					'step' => 1,
					'suffix' => 'px', //optional suffix
				),
			)
		)
	);

	// Archive Page Category Other Categories Button Border Radius(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_border_radius', array(
        'default'       	=> $defaults['betterdocs_archive_other_categories_button_border_radius'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_archive_other_categories_button_border_radius', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_archive_page_settings',
        'settings' => 'betterdocs_archive_other_categories_button_border_radius',
        'label'    => esc_html__('Button Border Radius', 'betterdocs-pro'),
        'input_attrs' => array(
            'id' 	=> 'betterdocs_archive_other_categories_button_border_radius',
            'class' => 'betterdocs-dimension',
        ),
        'priority' 	 => 518
    ) ) );

	// Archive Page Category Other Categories Button Border Radius Top Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_border_radius_top_left', array(
			'default'       	=> $defaults['betterdocs_archive_other_categories_button_border_radius_top_left'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		)
	);

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_button_border_radius_top_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_button_border_radius_top_left',
		'label'    => __( 'Top Left', 'betterdocs-pro' ),
		'priority' => 519,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_button_border_radius betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Button Border Radius Top Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_border_radius_top_right', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_button_border_radius_top_right'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_button_border_radius_top_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_button_border_radius_top_right',
		'label'    => __( 'Top Right', 'betterdocs-pro' ),
		'priority' => 520,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_button_border_radius betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Button Border Radius Bottom Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_border_radius_bottom_right', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_button_border_radius_bottom_right'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_button_border_radius_bottom_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_button_border_radius_bottom_right',
		'label'    => __( 'Bottom Right', 'betterdocs-pro' ),
		'priority' => 521,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_button_border_radius betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Button Border Radius Bottom Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_border_radius_bottom_left', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_button_border_radius_bottom_left'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_button_border_radius_bottom_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_button_border_radius_bottom_left',
		'label'    => __( 'Bottom Left', 'betterdocs-pro' ),
		'priority' => 522,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_button_border_radius betterdocs-dimension',
		),
	) ) );


	// Archive Page Category Other Categories Button Padding(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_padding', array(
        'default'       	=> $defaults['betterdocs_archive_other_categories_button_padding'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_archive_other_categories_button_padding', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_archive_page_settings',
        'settings' => 'betterdocs_archive_other_categories_button_padding',
        'label'    => esc_html__('Button Padding', 'betterdocs-pro'),
        'input_attrs' => array(
            'id' 	=> 'betterdocs_archive_other_categories_button_padding',
            'class' => 'betterdocs-dimension',
        ),
        'priority' 	 => 523
    ) ) );

	// Archive Page Category Other Categories Button Padding Top(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_padding_top', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_button_padding_top'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_button_padding_top', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_button_padding_top',
		'label'    => __( 'Top', 'betterdocs-pro' ),
		'priority' => 524,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_button_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Button Padding Right(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_padding_right', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_button_padding_right'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_button_padding_right', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_button_padding_right',
		'label'    => __( 'Right', 'betterdocs-pro' ),
		'priority' => 525,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_button_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Button Padding Bottom(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_padding_bottom', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_button_padding_bottom'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_button_padding_bottom', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_button_padding_bottom',
		'label'    => __( 'Bottom', 'betterdocs-pro' ),
		'priority' => 526,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_button_padding betterdocs-dimension',
		),
	) ) );

	// Archive Page Category Other Categories Button Padding Left(Archive Page Layout 2)
	$wp_customize->add_setting( 'betterdocs_archive_other_categories_button_padding_left', array(
		'default'       	=> $defaults['betterdocs_archive_other_categories_button_padding_left'],
		'capability'    	=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control( new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_archive_other_categories_button_padding_left', array(
		'type'     => 'betterdocs-dimension',
		'section'  => 'betterdocs_archive_page_settings',
		'settings' => 'betterdocs_archive_other_categories_button_padding_left',
		'label'    => __( 'Left', 'betterdocs-pro' ),
		'priority' => 527,
		'input_attrs' => array(
			'class' => 'betterdocs_archive_other_categories_button_padding betterdocs-dimension',
		),
	) ) );



	// Popular Docs On/Off(Docs)
	$wp_customize->add_setting('betterdocs_docs_page_popular_docs_switch', array(
		'default' 			=> $defaults['betterdocs_docs_page_popular_docs_switch'],
		'capability' 		=> 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_checkbox',
	));

	$wp_customize->add_control(new BetterDocs_Customizer_Toggle_Control(
		$wp_customize, 'betterdocs_docs_page_popular_docs_switch', array(
		'label'    => esc_html__('Popular Docs Show', 'betterdocs-pro'),
		'section'  => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_docs_page_popular_docs_switch',
		'type'     => 'light', // light, ios, flat
		'priority' => 34
	)));

		// Docs List Background Color(Docs)
	$wp_customize->add_setting('betterdocs_doc_page_article_list_bg_color_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_bg_color_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));
	
	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_article_list_bg_color_2',
		array(
			'label' => __('Popular Docs Background Color', 'betterdocs-pro'),
			'section' => 'betterdocs_doc_page_settings',
			'settings' => 'betterdocs_doc_page_article_list_bg_color_2',
			'priority' => 34
			)
		)
	);

	// Docs List Color(Docs)
	$wp_customize->add_setting('betterdocs_doc_page_article_list_color_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_color_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_doc_page_article_list_color_2',
			array(
				'label' => __('Popular Docs List Color', 'betterdocs-pro'),
				'section' => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_page_article_list_color_2',
				'priority' => 35
			))
	);

	// Docs List Hover Color(Docs)
	$wp_customize->add_setting('betterdocs_doc_page_article_list_hover_color_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_hover_color_2'],
		'capability' => 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_doc_page_article_list_hover_color_2',
			array(
				'label' => __('Popular Docs List Hover Color', 'betterdocs-pro'),
				'section' => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_page_article_list_hover_color_2',
				'priority' => 36
			))
	);

	// Docs List Font Size(Docs)
	$wp_customize->add_setting('betterdocs_doc_page_article_list_font_size_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_font_size_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_font_size_2', array(
		'type' => 'betterdocs-range-value',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_font_size_2',
		'label' => __('Popular Docs List Font Size', 'betterdocs-pro'),
		'priority' => 37,
		'input_attrs' => array(
			'class' => '',
			'min' => 0,
			'max' => 50,
			'step' => 1,
			'suffix' => 'px', //optional suffix
		),
	)));

	  //Popular Title Font Size(Docs)
	  $wp_customize->add_setting('betterdocs_doc_page_article_title_font_size_2', array(
		'default' => $defaults['betterdocs_doc_page_article_title_font_size_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_article_title_font_size_2', array(
		'type' => 'betterdocs-range-value',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_title_font_size_2',
		'label' => __('Popular Title Font Size', 'betterdocs-pro'),
		'priority' => 37,
		'input_attrs' => array(
			'class' => '',
			'min' => 0,
			'max' => 50,
			'step' => 1,
			'suffix' => 'px', //optional suffix
		),
	)));

	  // Popular Title Color(Docs)
	  $wp_customize->add_setting('betterdocs_doc_page_article_title_color_2', array(
		'default' 			=> $defaults['betterdocs_doc_page_article_title_color_2'],
		'capability' 		=> 'edit_theme_options',
		'transport' 		=> 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_doc_page_article_title_color_2',
			array(
				'label'    => __('Popular Title Color', 'betterdocs-pro'),
				'section'  => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_page_article_title_color_2',
				'priority' => 38
			))
	);


	// Popular Title Color Hover(Docs)
	$wp_customize->add_setting('betterdocs_doc_page_article_title_color_hover_2', array(
		'default'        => $defaults['betterdocs_doc_page_article_title_color_hover_2'],
		'capability'      => 'edit_theme_options',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));
	
	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_doc_page_article_title_color_hover_2',
		array(
			'label'    => __('Popular Title Hover Color', 'betterdocs-pro'),
			'section'  => 'betterdocs_doc_page_settings',
			'settings' => 'betterdocs_doc_page_article_title_color_hover_2',
			'priority' => 38
		))
	);


	// List Icon Color(Docs)
	$wp_customize->add_setting('betterdocs_doc_page_article_list_icon_color_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_icon_color_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_rgba',
	));

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_doc_page_article_list_icon_color_2',
			array(
				'label' => __('Popular List Icon Color', 'betterdocs-pro'),
				'section' => 'betterdocs_doc_page_settings',
				'settings' => 'betterdocs_doc_page_article_list_icon_color_2',
				'priority' => 38
			))
	);

	  // List Icon Font Size(Docs)
	  $wp_customize->add_setting('betterdocs_doc_page_article_list_icon_font_size_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_icon_font_size_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_icon_font_size_2', array(
		'type' => 'betterdocs-range-value',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_icon_font_size_2',
		'label' => __('Popular List Icon Font Size', 'betterdocs-pro'),
		'priority' => 39,
		'input_attrs' => array(
			'class' => '',
			'min' => 0,
			'max' => 50,
			'step' => 1,
			'suffix' => 'px', //optional suffix
		),
	)));

    //Popular Title Margin(Docs)
    $wp_customize->add_setting('betterdocs_doc_page_popular_title_margin', array(
		'default' => $defaults['betterdocs_doc_page_popular_title_margin'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_popular_title_margin', array(
		'type' => 'betterdocs-title',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_title_margin',
		'label' => __('Popular Docs Title Margin', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_page_popular_title_margin',
			'class' => 'betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_popular_title_margin_top', array(
		'default' => $defaults['betterdocs_doc_page_popular_title_margin_top'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_popular_title_margin_top', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_title_margin_top',
		'label' => __('Top', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_popular_title_margin betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_popular_title_margin_right', array(
		'default' => $defaults['betterdocs_doc_page_popular_title_margin_right'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_popular_title_margin_right', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_title_margin_right',
		'label' => __('Right', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_popular_title_margin betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_popular_title_margin_bottom', array(
		'default' => $defaults['betterdocs_doc_page_popular_title_margin_bottom'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_popular_title_margin_bottom', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_title_margin_bottom',
		'label' => __('Bottom', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_popular_title_margin betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_popular_title_margin_left', array(
		'default' => $defaults['betterdocs_doc_page_popular_title_margin_left'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_popular_title_margin_left', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_title_margin_left',
		'label' => __('Left', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_popular_title_margin betterdocs-dimension',
		),
	)));

    // Docs List Margin(Docs)
    $wp_customize->add_setting('betterdocs_doc_page_article_list_margin_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_margin_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin_2', array(
		'type' => 'betterdocs-title',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin_2',
		'label' => __('Popular Docs List Margin', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'id' => 'betterdocs_doc_page_article_list_margin_2',
			'class' => 'betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_article_list_margin_top_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_margin_top_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin_top_2', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin_top_2',
		'label' => __('Top', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_article_list_margin_2 betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_article_list_margin_right_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_margin_right_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin_right_2', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin_right_2',
		'label' => __('Right', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_article_list_margin_2 betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_article_list_margin_bottom_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_margin_bottom_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin_bottom_2', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin_bottom_2',
		'label' => __('Bottom', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_article_list_margin_2 betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_article_list_margin_left_2', array(
		'default' => $defaults['betterdocs_doc_page_article_list_margin_left_2'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_article_list_margin_left_2', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_article_list_margin_left_2',
		'label' => __('Left', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_doc_page_article_list_margin_2 betterdocs-dimension',
		),
	)));

    // Popular Docs Padding(MKB)
	$wp_customize->add_setting('betterdocs_mkb_popular_docs_padding', array(
		'default' => $defaults['betterdocs_mkb_popular_docs_padding'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_mkb_popular_docs_padding', array(
		'type' => 'betterdocs-title',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_docs_padding',
		'label' => __('Popular Docs Padding', 'betterdocs-pro'),
		'priority' => 45,
		'input_attrs' => array(
			'id' => 'betterdocs_mkb_popular_docs_padding',
			'class' => 'betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_mkb_popular_docs_padding_top', array(
		'default' => $defaults['betterdocs_mkb_popular_docs_padding_top'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_mkb_popular_docs_padding_top', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_docs_padding_top',
		'label' => __('Top', 'betterdocs-pro'),
		'priority' => 46,
		'input_attrs' => array(
			'class' => 'betterdocs_mkb_popular_docs_padding betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_mkb_popular_docs_padding_right', array(
		'default' => $defaults['betterdocs_mkb_popular_docs_padding_right'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_mkb_popular_docs_padding_right', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_docs_padding_right',
		'label' => __('Right', 'betterdocs-pro'),
		'priority' => 47,
		'input_attrs' => array(
			'class' => 'betterdocs_mkb_popular_docs_padding betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_mkb_popular_docs_padding_bottom', array(
		'default' => $defaults['betterdocs_mkb_popular_docs_padding_bottom'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_mkb_popular_docs_padding_bottom', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_docs_padding_bottom',
		'label' => __('Bottom', 'betterdocs-pro'),
		'priority' => 48,
		'input_attrs' => array(
			'class' => 'betterdocs_mkb_popular_docs_padding betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_mkb_popular_docs_padding_left', array(
		'default' => $defaults['betterdocs_mkb_popular_docs_padding_left'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_mkb_popular_docs_padding_left', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_mkb_settings',
		'settings' => 'betterdocs_mkb_popular_docs_padding_left',
		'label' => __('Left', 'betterdocs-pro'),
		'priority' => 49,
		'input_attrs' => array(
			'class' => 'betterdocs_mkb_popular_docs_padding betterdocs-dimension',
		),
	)));

    // Docs List Padding(Docs)
    $wp_customize->add_setting('betterdocs_doc_page_popular_docs_padding', array(
		'default' => $defaults['betterdocs_doc_page_popular_docs_padding'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Title_Custom_Control(
		$wp_customize, 'betterdocs_doc_page_popular_docs_padding', array(
		'type' => 'betterdocs-title',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_docs_padding',
		'label' => __('Popular Docs Padding', 'betterdocs-pro'),
		'priority' => 44, 
		'input_attrs' => array(
			'id' => 'betterdocs_article_popular_docs_padding',
			'class' => 'betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_popular_docs_padding_top', array(
		'default' => $defaults['betterdocs_doc_page_popular_docs_padding_top'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'
	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_popular_docs_padding_top', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_docs_padding_top',
		'label' => __('Top', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_article_popular_docs_padding betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_popular_docs_padding_right', array(
		'default' => $defaults['betterdocs_doc_page_popular_docs_padding_right'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_popular_docs_padding_right', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_docs_padding_right',
		'label' => __('Right', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_article_popular_docs_padding betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_popular_docs_padding_bottom', array(
		'default' => $defaults['betterdocs_doc_page_popular_docs_padding_bottom'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_popular_docs_padding_bottom', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_docs_padding_bottom',
		'label' => __('Bottom', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_article_popular_docs_padding betterdocs-dimension',
		),
	)));

	$wp_customize->add_setting('betterdocs_doc_page_popular_docs_padding_left', array(
		'default' => $defaults['betterdocs_doc_page_popular_docs_padding_left'],
		'capability' => 'edit_theme_options',
		'transport' => 'postMessage',
		'sanitize_callback' => 'betterdocs_sanitize_integer'

	));

	$wp_customize->add_control(new BetterDocs_Dimension_Control(
		$wp_customize, 'betterdocs_doc_page_popular_docs_padding_left', array(
		'type' => 'betterdocs-dimension',
		'section' => 'betterdocs_doc_page_settings',
		'settings' => 'betterdocs_doc_page_popular_docs_padding_left',
		'label' => __('Left', 'betterdocs-pro'),
		'priority' => 44,
		'input_attrs' => array(
			'class' => 'betterdocs_article_popular_docs_padding betterdocs-dimension',
		),
	)));


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

	// Icon Size (This controller works for category layout-5( DocsPage ))

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

	if ( BetterDocs_Multiple_Kb::$enable == 1 ) {
		// Assign sections to panels
		$wp_customize->get_section('betterdocs_mkb_settings')->panel = 'betterdocs_customize_options';	
	}

    // Advance Search
    $live_search = BetterDocs_DB::get_settings('advance_search');
    if ($live_search == 1) {
    // Category Search Toogle
    $wp_customize->add_setting( 'betterdocs_category_search_toggle', array(
        'default'       => $defaults['betterdocs_category_search_toggle'],
        'capability'    => 'edit_theme_options',

    ) );

    $wp_customize->add_control( new BetterDocs_Customizer_Toggle_Control(
        $wp_customize, 'betterdocs_category_search_toggle', array(
        'label' => esc_html__('Enable Category Search', 'betterdocs-pro'),
        'section' => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_category_search_toggle',
        'type' => 'light', // light, ios, flat
        'priority' 	 => 500
    )));

    //Search Button Toggle
    $wp_customize->add_setting( 'betterdocs_search_button_toggle', array(
        'default'       => $defaults['betterdocs_search_button_toggle'],
        'capability'    => 'edit_theme_options',

    ) );

    $wp_customize->add_control( new BetterDocs_Customizer_Toggle_Control(
        $wp_customize, 'betterdocs_search_button_toggle', array(
        'label' => esc_html__('Enable Search Button', 'betterdocs-pro'),
        'section' => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_toggle',
        'type' => 'light', // light, ios, flat
        'priority' 	 => 500
    )));

    //Popular Search Enable/Disable
    $wp_customize->add_setting( 'betterdocs_popular_search_toggle', array(
        'default'       => $defaults['betterdocs_popular_search_toggle'],
        'capability'    => 'edit_theme_options',

    ) );

    $wp_customize->add_control( new BetterDocs_Customizer_Toggle_Control(
        $wp_customize, 'betterdocs_popular_search_toggle', array(
        'label'    => esc_html__('Enable Popular Search', 'betterdocs-pro'),
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_toggle',
        'type'     => 'light', // light, ios, flat
        'priority' 	 => 500
    )));

    //Category Select Section
    $wp_customize->add_setting('betterdocs_category_select_search_section', array(
        'default'           => $defaults['betterdocs_category_select_search_section'],
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
        $wp_customize, 'betterdocs_category_select_search_section', array(
        'label'	        => esc_html__('Category Select Settings', 'betterdocs-pro'),
        'settings'		=> 'betterdocs_category_select_search_section',
        'section'  		=> 'betterdocs_live_search_settings',
        'priority' 	 => 570
    )));

    //Category Select Font Size
    $wp_customize->add_setting( 'betterdocs_category_select_font_size', array(
        'default'       => $defaults['betterdocs_category_select_font_size'],
        'capability'    => 'edit_theme_options',
        'transport' => 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
        $wp_customize, 'betterdocs_category_select_font_size', array(
        'type'     => 'betterdocs-range-value',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_category_select_font_size',
        'label'    => esc_html__('Font Size', 'betterdocs-pro'),
        'input_attrs' => array(
            'class'  => '',
            'min'    => 0,
            'max'    => 200,
            'step'   => 1,
            'suffix' => 'px', //optional suffix
        ),
        'priority' 	 => 572
    ) ) );

    //Category Select Font Weight
    $wp_customize->add_setting( 'betterdocs_category_select_font_weight' , array(
        'default'     		=> $defaults['betterdocs_category_select_font_weight'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_category_select_font_weight',
            array(
                'label'      => esc_html__('Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_category_select_font_weight',
                'type'    => 'select',
                'choices' => array(
                    'normal' => 'Normal',
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 573
            ) )
    );

    //Category Select Text Transform
    $wp_customize->add_setting( 'betterdocs_category_select_text_transform' , array(
        'default'     		=> $defaults['betterdocs_category_select_text_transform'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_category_select_text_transform',
            array(
                'label'      => esc_html__('Font Text Transform', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_category_select_text_transform',
                'type'    	 => 'select',
                'choices' => array(
                    'none' => 'none',
                    'capitalize' => 'capitalize',
                    'uppercase'  => 'uppercase',
                    'lowercase'  => 'lowercase',
                    'initial'    => 'initial',
                    'inherit'    => 'inherit'
                ),
                'priority' 	 => 574
            ) )
    );

    //Category Select Text Color
    $wp_customize->add_setting( 'betterdocs_category_select_text_color' , array(
        'default'           => $defaults['betterdocs_category_select_text_color'],
        'capability'        => 'edit_theme_options',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_rgba',
    ) );

    $wp_customize->add_control(
        new BetterDocs_Customizer_Alpha_Color_Control(
            $wp_customize,
            'betterdocs_category_select_text_color',
            array(
                'label'      => esc_html__('Font Color', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_category_select_text_color',
                'priority' 	 => 575
            ) )
    );

    // Search Button Controls
    $wp_customize->add_setting('betterdocs_search_button_section', array(
        'default'           => $defaults['betterdocs_search_button_section'],
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
        $wp_customize, 'betterdocs_search_button_section', array(
        'label'	      => esc_html__('Search Button Settings', 'betterdocs-pro'),
        'settings'		=> 'betterdocs_search_button_section',
        'section'  		=> 'betterdocs_live_search_settings',
        'priority' 	 => 576
    )));

    //Search Button Font Size
    $wp_customize->add_setting( 'betterdocs_new_search_button_font_size', array(
        'default'       => $defaults['betterdocs_new_search_button_font_size'],
        'capability'    => 'edit_theme_options',
        'transport' => 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
        $wp_customize, 'betterdocs_new_search_button_font_size', array(
        'type'     => 'betterdocs-range-value',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_new_search_button_font_size',
        'label'    => esc_html__('Font Size', 'betterdocs-pro'),
        'input_attrs' => array(
            'class'  => '',
            'min'    => 0,
            'max'    => 200,
            'step'   => 1,
            'suffix' => 'px', //optional suffix
        ),
        'priority' 	 => 578
    ) ) );

    //Search Button Letter Spacing
    $wp_customize->add_setting( 'betterdocs_new_search_button_letter_spacing', array(
        'default'       	=> $defaults['betterdocs_new_search_button_letter_spacing'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
        $wp_customize, 'betterdocs_new_search_button_letter_spacing', array(
        'type'     => 'betterdocs-range-value',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_new_search_button_letter_spacing',
        'label'    => esc_html__('Font Letter Spacing', 'betterdocs-pro'),
        'input_attrs' => array(
            'class'  => '',
            'min'    => 0,
            'max'    => 200,
            'step'   => 1,
            'suffix' => 'px', //optional suffix
        ),
        'priority' 	 => 579
    ) ) );

    //Search Button Font Weight
    $wp_customize->add_setting( 'betterdocs_new_search_button_font_weight' , array(
        'default'     		=> $defaults['betterdocs_new_search_button_font_weight'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_new_search_button_font_weight',
            array(
                'label'      => esc_html__('Font Weight', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_new_search_button_font_weight',
                'type'    => 'select',
                'choices' => array(
                    '100' => '100',
                    '200' => '200',
                    '300' => '300',
                    '400' => '400',
                    '500' => '500',
                    '600' => '600',
                    '700' => '700',
                    '800' => '800',
                    '900' => '900'
                ),
                'priority' 	 => 579
            ) )
    );

    //Search Button Text Transform
    $wp_customize->add_setting( 'betterdocs_new_search_button_text_transform' , array(
        'default'     		=> $defaults['betterdocs_new_search_button_text_transform'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_new_search_button_text_transform',
            array(
                'label'      => esc_html__('Font Text Transform', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_new_search_button_text_transform',
                'type'    	 => 'select',
                'choices' => array(
                    'none' => 'none',
                    'capitalize' => 'capitalize',
                    'uppercase'  => 'uppercase',
                    'lowercase'  => 'lowercase',
                    'initial'    => 'initial',
                    'inherit'    => 'inherit'
                ),
                'priority' 	 => 580
            ) )
    );

    // Search Button Text Color
    $wp_customize->add_setting( 'betterdocs_search_button_text_color' , array(
        'default'     		=> $defaults['betterdocs_search_button_text_color'],
        'capability'    	=> 'edit_theme_options',
        'transport'   		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_rgba',
    ) );

    $wp_customize->add_control(
        new BetterDocs_Customizer_Alpha_Color_Control(
            $wp_customize,
            'betterdocs_search_button_text_color',
            array(
                'label'      => esc_html__('Text Color', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_search_button_text_color',
                'priority' 	 => 582
            ) )
    );

    // Search Button Background Color
    $wp_customize->add_setting( 'betterdocs_search_button_background_color' , array(
        'default'     		=> $defaults['betterdocs_search_button_background_color'],
        'capability'    	=> 'edit_theme_options',
        'transport'   		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_rgba',
    ) );

    $wp_customize->add_control(
        new BetterDocs_Customizer_Alpha_Color_Control(
            $wp_customize,
            'betterdocs_search_button_background_color',
            array(
                'label'      => esc_html__('Background Color', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_search_button_background_color',
                'priority' 	 => 583
            ) )
    );

    // Search Button Background Color Hover
    $wp_customize->add_setting( 'betterdocs_search_button_background_color_hover' , array(
        'default'     		=> $defaults['betterdocs_search_button_background_color_hover'],
        'capability'    	=> 'edit_theme_options',
        'sanitize_callback' => 'betterdocs_sanitize_rgba',
    ) );

    $wp_customize->add_control(
        new BetterDocs_Customizer_Alpha_Color_Control(
            $wp_customize,
            'betterdocs_search_button_background_color_hover',
            array(
                'label'      => esc_html__('Background Hover Color', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_search_button_background_color_hover',
                'priority' 	 => 583
            ) )
    );

    //Search Button Border Radius
    $wp_customize->add_setting( 'betterdocs_search_button_borderr_radius', array(
        'default'       	=> $defaults['betterdocs_search_button_borderr_radius'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_search_button_borderr_radius', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_borderr_radius',
        'label'    => esc_html__('Border Radius', 'betterdocs-pro'),
        'input_attrs' => array(
            'id' 	=> 'betterdocs_search_button_borderr_radius',
            'class' => 'betterdocs-dimension',
        ),
        'priority' 	 => 584
    ) ) );

    $wp_customize->add_setting( 'betterdocs_search_button_borderr_left_top', array(
        'default'       	=> $defaults['betterdocs_search_button_borderr_left_top'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_search_button_borderr_left_top', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_borderr_left_top',
        'label'    => esc_html__('Left Top', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_search_button_borderr_radius betterdocs-dimension',
        ),
        'priority' 	 => 585
    ) ) );

    $wp_customize->add_setting( 'betterdocs_search_button_borderr_right_top', array(
        'default'       	=> $defaults['betterdocs_search_button_borderr_right_top'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_search_button_borderr_right_top', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_borderr_right_top',
        'label'    => esc_html__('Right Top', 'betterdocs-pro'),
        'input_attrs' => array(
            'class'   => 'betterdocs_search_button_borderr_radius betterdocs-dimension',
        ),
        'priority' 	 => 586
    ) ) );

    $wp_customize->add_setting( 'betterdocs_search_button_borderr_left_bottom', array(
        'default'       	=> $defaults['betterdocs_search_button_borderr_left_bottom'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_search_button_borderr_left_bottom', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_borderr_left_bottom',
        'label'    => esc_html__('Left Bottom', 'betterdocs-pro'),
        'input_attrs' => array(
            'class'   => 'betterdocs_search_button_borderr_radius betterdocs-dimension',
        ),
        'priority' 	 => 587
    ) ) );

    $wp_customize->add_setting( 'betterdocs_search_button_borderr_right_bottom', array(
        'default'       	=> $defaults['betterdocs_search_button_borderr_right_bottom'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_search_button_borderr_right_bottom', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_borderr_right_bottom',
        'label'    => esc_html__('Right Bottom', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_search_button_borderr_radius betterdocs-dimension',
        ),
        'priority' 	 => 588
    ) ) );

    //Search Button Padding
    $wp_customize->add_setting( 'betterdocs_search_button_padding', array(
        'default'       	=> $defaults['betterdocs_search_button_padding'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_search_button_padding', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_padding',
        'label'    => esc_html__('Padding', 'betterdocs-pro'),
        'input_attrs' => array(
            'id' 	=> 'betterdocs_search_button_padding',
            'class' => 'betterdocs-dimension',
        ),
        'priority' 	 => 589
    ) ) );


    $wp_customize->add_setting( 'betterdocs_search_button_padding_top',
        apply_filters('betterdocs_search_button_padding_top', array(
            'default'       	=> $defaults['betterdocs_search_button_padding_top'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_search_button_padding_top', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_padding_top',
        'label'    => esc_html__('Top', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_search_button_padding betterdocs-dimension',
        ),
        'priority' 	 => 590
    ) ) );

    $wp_customize->add_setting( 'betterdocs_search_button_padding_right',
        apply_filters('betterdocs_search_button_padding_right', array(
            'default'       	=> $defaults['betterdocs_search_button_padding_right'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_search_button_padding_right', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_padding_right',
        'label'    => esc_html__('Right', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_search_button_padding betterdocs-dimension',
        ),
        'priority' 	 => 591
    ) ) );

    $wp_customize->add_setting( 'betterdocs_search_button_padding_bottom',
        apply_filters('betterdocs_search_button_padding_bottom', array(
            'default'       	=> $defaults['betterdocs_search_button_padding_bottom'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_search_button_padding_bottom', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_padding_bottom',
        'label'    => esc_html__('Bottom', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_search_button_padding betterdocs-dimension',
        ),
        'priority' 	 => 592
    ) ) );

    $wp_customize->add_setting( 'betterdocs_search_button_padding_left', array(
        'default'       	=> $defaults['betterdocs_search_button_padding_left'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_search_button_padding_left', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_search_button_padding_left',
        'label'    => esc_html__('Left', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_search_button_padding betterdocs-dimension',
        ),
        'priority' 	 => 593
    ) ) );

    // Popular Search Settings
    $wp_customize->add_setting('betterdocs_popular_search_section', array(
        'default'           => $defaults['betterdocs_popular_search_section'],
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
        $wp_customize, 'betterdocs_popular_search_section', array(
        'label'	        => esc_html__('Popular Search Settings', 'betterdocs-pro'),
        'settings'		=> 'betterdocs_popular_search_section',
        'section'  		=> 'betterdocs_live_search_settings',
        'priority' 	 => 599
    )));

    //Popular Search Margin
    $wp_customize->add_setting( 'betterdocs_popular_search_margin', array(
        'default'       	=> $defaults['betterdocs_popular_search_margin'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_popular_search_margin', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_margin',
        'label'    => esc_html__('Popular Search Margin', 'betterdocs-pro'),
        'input_attrs' => array(
            'id' => 'betterdocs_popular_search_margin',
            'class' => 'betterdocs-dimension',
        ),
        'priority' 	 => 601
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_margin_top',
        apply_filters('betterdocs_popular_search_margin_top', array(
            'default'       	=> $defaults['betterdocs_popular_search_margin_top'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_margin_top', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_margin_top',
        'label'    => esc_html__('Top', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_margin betterdocs-dimension',
        ),
        'priority' 	 => 602
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_margin_right',
        apply_filters('betterdocs_popular_search_margin_right', array(
            'default'       	=> $defaults['betterdocs_popular_search_margin_right'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_margin_right', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_margin_right',
        'label'    => esc_html__('Right', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_margin betterdocs-dimension',
        ),
        'priority' 	 => 603
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_margin_bottom',
        apply_filters('betterdocs_popular_search_margin_bottom', array(
            'default'       	=> $defaults['betterdocs_popular_search_margin_bottom'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_margin_bottom', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_margin_bottom',
        'label'    => esc_html__('Bottom', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_margin betterdocs-dimension',
        ),
        'priority' 	 => 604
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_margin_left', array(
        'default'       	=> $defaults['betterdocs_popular_search_margin_left'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_margin_left', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_margin_left',
        'label'    => esc_html__('Left', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_margin betterdocs-dimension',
        ),
        'priority' 	 => 605
    ) ) );

    $wp_customize->add_setting('betterdocs_popular_search_text', array(
        'default' => $defaults['betterdocs_popular_search_text'],
        'capability'    => 'edit_theme_options',
        'sanitize_callback' => 'esc_html',
    ));

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_popular_search_text',
            array(
                'label' => esc_html__('Sub Heading', 'betterdocs-pro'),
                'section' => 'betterdocs_live_search_settings',
                'settings' => 'betterdocs_popular_search_text',
                'type' => 'text',
                'priority' 	 => 606
            )
        )
    );

    //Popular Title Text Color
    $wp_customize->add_setting( 'betterdocs_popular_search_title_text_color' , array(
        'default'           => $defaults['betterdocs_popular_search_title_text_color'],
        'capability'        => 'edit_theme_options',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_rgba',
    ) );

    $wp_customize->add_control(
        new BetterDocs_Customizer_Alpha_Color_Control(
            $wp_customize,
            'betterdocs_popular_search_title_text_color',
            array(
                'label'      => esc_html__('Title Color', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_popular_search_title_text_color',
                'priority' 	 => 606
            ) )
    );


    //Popular Title Font Size
    $wp_customize->add_setting( 'betterdocs_popular_search_title_font_size', array(
        'default'       	=> $defaults['betterdocs_popular_search_title_font_size'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
        $wp_customize, 'betterdocs_popular_search_title_font_size', array(
        'type'     => 'betterdocs-range-value',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_title_font_size',
        'label'    => esc_html__('Title Font Size', 'betterdocs-pro'),
        'input_attrs' => array(
            'class'  => '',
            'min'    => 0,
            'max'    => 200,
            'step'   => 1,
            'suffix' => 'px', //optional suffix
        ),
        'priority' 	 => 607
    ) ) );

    //Popular Search Font Size
    $wp_customize->add_setting( 'betterdocs_popular_search_font_size', array(
        'default'       	=> $defaults['betterdocs_popular_search_font_size'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
        $wp_customize, 'betterdocs_popular_search_font_size', array(
        'type'     => 'betterdocs-range-value',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_font_size',
        'label'    => esc_html__('Keyword Font Size', 'betterdocs-pro'),
        'input_attrs' => array(
            'class'  => '',
            'min'    => 0,
            'max'    => 200,
            'step'   => 1,
            'suffix' => 'px', //optional suffix
        ),
        'priority' 	 => 608
    ) ) );

	//Keyword Border Type
	$wp_customize->add_setting( 'betterdocs_popular_search_keyword_border' , array(
        'default'     		=> $defaults['betterdocs_popular_search_keyword_border'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_choices',
    ) );

    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'betterdocs_popular_search_keyword_border',
            array(
                'label'      => esc_html__('Keyword Border Type', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_popular_search_keyword_border',
                'type'    => 'select',
                'choices' => array(
                    'none'   => 'none',
                    'solid'  => 'solid',
                    'double' => 'double',
                    'dotted' => 'dotted',
                    'dashed' => 'dashed',
                    'groove' => 'groove'
                ),
                'priority' 	 => 608
            ) )
    );

	//Keyword Border Color
	$wp_customize->add_setting( 'betterdocs_popular_search_keyword_border_color' , array(
        'default'           => $defaults['betterdocs_popular_search_keyword_border_color'],
        'capability'        => 'edit_theme_options',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_rgba',
    ) );

    $wp_customize->add_control(
        new BetterDocs_Customizer_Alpha_Color_Control(
            $wp_customize,
            'betterdocs_popular_search_keyword_border_color',
            array(
                'label'      => esc_html__('Keyword Border Color', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_popular_search_keyword_border_color',
                'priority' 	 => 608
            ) )
    );

	//Keyword Border Width
    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_border_width', array(
        'default'       	=> $defaults['betterdocs_popular_search_keyword_border_width'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_border_width', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_border_width',
        'label'    => esc_html__('Keyword Border Width', 'betterdocs-pro'),
        'input_attrs' => array(
            'id' => 'betterdocs_popular_search_keyword_border_width',
            'class' => 'betterdocs-dimension',
        ),
        'priority' 	 => 608
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_border_width_top',
        apply_filters('betterdocs_popular_search_keyword_border_width_top', array(
            'default'       	=> $defaults['betterdocs_popular_search_keyword_border_width_top'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_border_width_top', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_border_width_top',
        'label'    => esc_html__('Top', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_keyword_border_width betterdocs-dimension',
        ),
        'priority' 	 => 608
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_border_width_right',
        apply_filters('betterdocs_popular_search_padding_right', array(
            'default'       	=> $defaults['betterdocs_popular_search_keyword_border_width_right'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_border_width_right', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_border_width_right',
        'label'    => esc_html__('Right', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_keyword_border_width betterdocs-dimension',
        ),
        'priority' 	 => 608
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_border_width_bottom',
        apply_filters('betterdocs_popular_search_keyword_border_width_bottom', array(
            'default'       	=> $defaults['betterdocs_popular_search_keyword_border_width_bottom'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_border_width_bottom', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_border_width_bottom',
        'label'    => esc_html__('Bottom', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_keyword_border_width betterdocs-dimension',
        ),
        'priority' 	 => 608
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_border_width_left', array(
        'default'       	=> $defaults['betterdocs_popular_search_keyword_border_width_left'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_border_width_left', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_border_width_left',
        'label'    => esc_html__('Left', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_keyword_border_width betterdocs-dimension',
        ),
        'priority' 	 => 608
    ) ) );

    //Popular Search Background Color
    $wp_customize->add_setting( 'betterdocs_popular_search_background_color' , array(
        'default'           => $defaults['betterdocs_popular_search_background_color'],
        'capability'        => 'edit_theme_options',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_rgba',
    ) );

    $wp_customize->add_control(
        new BetterDocs_Customizer_Alpha_Color_Control(
            $wp_customize,
            'betterdocs_popular_search_background_color',
            array(
                'label'      => esc_html__('Keyword Background Color', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_popular_search_background_color',
                'priority' 	 => 609
            ) )
    );

    //Popular Search Keyword Text Color
    $wp_customize->add_setting( 'betterdocs_popular_keyword_text_color' , array(
        'default'           => $defaults['betterdocs_popular_keyword_text_color'],
        'capability'        => 'edit_theme_options',
        'transport'         => 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_rgba',
    ) );

    $wp_customize->add_control(
        new BetterDocs_Customizer_Alpha_Color_Control(
            $wp_customize,
            'betterdocs_popular_keyword_text_color',
            array(
                'label'      => esc_html__('Keyword Text Color', 'betterdocs-pro'),
                'section'    => 'betterdocs_live_search_settings',
                'settings'   => 'betterdocs_popular_keyword_text_color',
                'priority' 	 => 610
            ) )
    );

	//Keyword Border Radius
    $wp_customize->add_setting( 'betterdocs_popular_keyword_border_radius', array(
        'default'       	=> $defaults['betterdocs_popular_keyword_border_radius'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_popular_keyword_border_radius', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_keyword_border_radius',
        'label'    => esc_html__('Keyword Border Radius', 'betterdocs-pro'),
        'input_attrs' => array(
            'id' 	=> 'betterdocs_popular_keyword_border_radius',
            'class' => 'betterdocs-dimension',
        ),
        'priority' 	 => 610
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_keyword_border_radius_left_top', array(
        'default'       	=> $defaults['betterdocs_popular_keyword_border_radius_left_top'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_keyword_border_radius_left_top', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_keyword_border_radius_left_top',
        'label'    => esc_html__('Left Top', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_keyword_border_radius betterdocs-dimension',
        ),
        'priority' 	 => 610
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_keyword_border_radius_right_top', array(
        'default'       	=> $defaults['betterdocs_popular_keyword_border_radius_right_top'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_keyword_border_radius_right_top', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_keyword_border_radius_right_top',
        'label'    => esc_html__('Right Top', 'betterdocs-pro'),
        'input_attrs' => array(
            'class'   => 'betterdocs_popular_keyword_border_radius betterdocs-dimension',
        ),
        'priority' 	 => 610
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_keyword_border_radius_left_bottom', array(
        'default'       	=> $defaults['betterdocs_popular_keyword_border_radius_left_bottom'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_keyword_border_radius_left_bottom', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_keyword_border_radius_left_bottom',
        'label'    => esc_html__('Left Bottom', 'betterdocs-pro'),
        'input_attrs' => array(
            'class'   => 'betterdocs_popular_keyword_border_radius betterdocs-dimension',
        ),
        'priority' 	 => 610
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_keyword_border_radius_right_bottom', array(
        'default'       	=> $defaults['betterdocs_popular_keyword_border_radius_right_bottom'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_keyword_border_radius_right_bottom', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_keyword_border_radius_right_bottom',
        'label'    => esc_html__('Right Bottom', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_keyword_border_radius betterdocs-dimension',
        ),
        'priority' 	 => 610
    ) ) );

    //Keyword Padding
    $wp_customize->add_setting( 'betterdocs_popular_search_padding', array(
        'default'       	=> $defaults['betterdocs_popular_search_padding'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_popular_search_padding', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_padding',
        'label'    => esc_html__('Keyword Padding', 'betterdocs-pro'),
        'input_attrs' => array(
            'id' => 'betterdocs_popular_search_padding',
            'class' => 'betterdocs-dimension',
        ),
        'priority' 	 => 611
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_padding_top',
        apply_filters('betterdocs_popular_search_padding_top', array(
            'default'       	=> $defaults['betterdocs_popular_search_padding_top'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_padding_top', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_padding_top',
        'label'    => esc_html__('Top', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_padding betterdocs-dimension',
        ),
        'priority' 	 => 612
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_padding_right',
        apply_filters('betterdocs_popular_search_padding_right', array(
            'default'       	=> $defaults['betterdocs_popular_search_padding_right'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_padding_right', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_padding_right',
        'label'    => esc_html__('Right', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_padding betterdocs-dimension',
        ),
        'priority' 	 => 613
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_padding_bottom',
        apply_filters('betterdocs_popular_search_padding_bottom', array(
            'default'       	=> $defaults['betterdocs_popular_search_padding_bottom'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_padding_bottom', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_padding_bottom',
        'label'    => esc_html__('Bottom', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_padding betterdocs-dimension',
        ),
        'priority' 	 => 614
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_padding_left', array(
        'default'       	=> $defaults['betterdocs_popular_search_padding_left'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_padding_left', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_padding_left',
        'label'    => esc_html__('Left', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_padding betterdocs-dimension',
        ),
        'priority' 	 => 615
    ) ) );

    //Keyword Margin
    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_margin', array(
        'default'       	=> $defaults['betterdocs_popular_search_keyword_margin'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'

    ) );

    $wp_customize->add_control( new BetterDocs_Title_Custom_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_margin', array(
        'type'     => 'betterdocs-title',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_margin',
        'label'    => esc_html__('Keyword Margin', 'betterdocs-pro'),
        'input_attrs' => array(
            'id' => 'betterdocs_popular_search_keyword_margin',
            'class' => 'betterdocs-dimension',
        ),
        'priority' 	 => 616
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_margin_top',
        apply_filters('betterdocs_popular_search_keyword_margin_top', array(
            'default'       	=> $defaults['betterdocs_popular_search_keyword_margin_top'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_margin_top', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_margin_top',
        'label'    => esc_html__('Top', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_keyword_margin betterdocs-dimension',
        ),
        'priority' 	 => 617
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_margin_right',
        apply_filters('betterdocs_popular_search_keyword_margin_right', array(
            'default'       	=> $defaults['betterdocs_popular_search_keyword_margin_right'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_margin_right', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_margin_right',
        'label'    => esc_html__('Right', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_keyword_margin betterdocs-dimension',
        ),
        'priority' 	 => 618
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_margin_bottom',
        apply_filters('betterdocs_popular_search_keyword_margin_bottom', array(
            'default'       	=> $defaults['betterdocs_popular_search_keyword_margin_bottom'],
            'capability'    	=> 'edit_theme_options',
            'transport' 		=> 'postMessage',
            'sanitize_callback' => 'betterdocs_sanitize_integer'
        ) )
    );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_margin_bottom', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_margin_bottom',
        'label'    => esc_html__('Bottom', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_keyword_margin betterdocs-dimension',
        ),
        'priority' 	 => 619
    ) ) );

    $wp_customize->add_setting( 'betterdocs_popular_search_keyword_margin_left', array(
        'default'       	=> $defaults['betterdocs_popular_search_keyword_margin_left'],
        'capability'    	=> 'edit_theme_options',
        'transport' 		=> 'postMessage',
        'sanitize_callback' => 'betterdocs_sanitize_integer'
    ) );

    $wp_customize->add_control( new BetterDocs_Dimension_Control(
        $wp_customize, 'betterdocs_popular_search_keyword_margin_left', array(
        'type'     => 'betterdocs-dimension',
        'section'  => 'betterdocs_live_search_settings',
        'settings' => 'betterdocs_popular_search_keyword_margin_left',
        'label'    => esc_html__('Left', 'betterdocs-pro'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_keyword_margin betterdocs-dimension',
        ),
        'priority' 	 => 620
    ) ) );
    }


	if ( BetterDocs_Multiple_Kb::$enable == 1 ) {
		/** FAQ Related Controllers **/

		// FAQ Controls

		$wp_customize->add_setting('betterdocs_faq_section_mkb_seperator', array(
			'default'           => $defaults['betterdocs_faq_section_mkb_seperator'],
			'sanitize_callback' => 'esc_html',
		));

		$wp_customize->add_control(new BetterDocs_Separator_Custom_Control(
			$wp_customize, 'betterdocs_faq_section_mkb_seperator', array(
			'label'	    => esc_html__('Multiple KB FAQ', 'betterdocs-pro'),
			'settings'	=> 'betterdocs_faq_section_mkb_seperator',
			'section'  	=> 'betterdocs_faq_section',
			'priority'  => 601
		)));

		$wp_customize->add_setting( 'betterdocs_faq_switch_mkb', array(
			'default'    => $defaults['betterdocs_faq_switch_mkb'],
			'capability' => 'edit_theme_options',
		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Toggle_Control( 
			$wp_customize, 
			'betterdocs_faq_switch_mkb', array(
			'label' 	 => esc_html__('Enable FAQ', 'betterdocs-pro'),
			'section' 	 => 'betterdocs_faq_section',
			'settings'   => 'betterdocs_faq_switch_mkb',
			'type' 		 => 'light', // light, ios, flat
			'priority' 	 => 601
		)));

		// Select Specific FAQ
		$wp_customize->add_setting( 'betterdocs_select_specific_faq_mkb' , array(
			'default'           => $defaults['betterdocs_select_specific_faq_mkb'],
			'capability'        => 'edit_theme_options',
		) );
		
		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'betterdocs_select_specific_faq_mkb',
				array(
					'label'      => esc_html__('Select FAQ Groups', 'betterdocs-pro'),
					'section'    => 'betterdocs_faq_section',
					'settings'   => 'betterdocs_select_specific_faq_mkb',
					'type'       => 'select',
					'choices'    => BetterDocs_Helper::faq_term_list(),
					'priority' 	 => 602
				) )
		);

		$wp_customize->add_setting( 'betterdocs_select_faq_template_mkb' , array(
			'default'     		=> $defaults['betterdocs_select_faq_template_mkb'],
			'capability'   	 	=> 'edit_theme_options',
			'sanitize_callback' => 'betterdocs_sanitize_select',
		) );

		$wp_customize->add_control(
			new BetterDocs_Radio_Image_Control(
			$wp_customize,
			'betterdocs_select_faq_template_mkb',
			array(
				'type'     		=> 'betterdocs-radio-image',
				'settings'		=> 'betterdocs_select_faq_template_mkb',
				'section'		=> 'betterdocs_faq_section',
				'label'			=> esc_html__('Select FAQ Layout', 'betterdocs-pro'),
				'priority' 		=> 603,
				'choices'		=> array(
					'layout-1' 	=> array(
						'label' => esc_html__('Modern Layout', 'betterdocs-pro'),
						'image' => BETTERDOCS_ADMIN_URL . 'assets/img/faq-layout-1.png',
					),
					'layout-2' 	=> array(
						'label' => esc_html__('Classic Layout', 'betterdocs-pro'),
						'image' => BETTERDOCS_ADMIN_URL . 'assets/img/faq-layout-2.png',
					)
				)
			) )
		);

		$wp_customize->add_setting('betterdocs_faq_title_text_mkb', array(
			'default' 			=> $defaults['betterdocs_faq_title_text_mkb'],
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'esc_html',
		));

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'betterdocs_faq_title_text_mkb',
				array(
					'label' 	=> __('Section Title Text', 'betterdocs-pro'),
					'section' 	=> 'betterdocs_faq_section',
					'priority' 	=> 604,
					'settings' 	=> 'betterdocs_faq_title_text_mkb',
					'type' 		=> 'text',
				)
			)
		);

		/** MKB FAQ CONTROLLERS LAYOUT 1 **/

		// FAQ Section Title Margin

		$wp_customize->add_setting('betterdocs_faq_title_margin_mkb_layout_1', array(
			'default' 			=> $defaults['betterdocs_faq_title_margin_mkb_layout_1'],
			'transport' 		=> 'postMessage',
			'capability'    	=> 'edit_theme_options',
		));

		$wp_customize->add_control(
			new BetterDocs_Multi_Dimension_Control(
				$wp_customize,
				'betterdocs_faq_title_margin_mkb_layout_1',
				array(
					'label' => __('FAQ Section Title Margin (PX)', 'betterdocs-pro'),
					'section' => 'betterdocs_faq_section',
					'settings' => 'betterdocs_faq_title_margin_mkb_layout_1',
					'priority' => 605,
					'input_fields' => array(
						'input1'   	=> __( 'top', 'betterdocs-pro' ),
						'input2'   	=> __( 'right', 'betterdocs-pro' ),
						'input3'   	=> __( 'bottom', 'betterdocs-pro' ),
						'input4'   	=> __( 'left', 'betterdocs-pro' ),
					),
					'defaults' => array(
						'input1'  	=> 0,
						'input2'  	=> 0,
						'input3'  	=> 0,
						'input4'  	=> 0,
					)
				))
		);
		
		// FAQ Section Title Color

		$wp_customize->add_setting( 'betterdocs_faq_title_color_mkb_layout_1' , array(
			'default'       	=> $defaults['betterdocs_faq_title_color_mkb_layout_1'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_title_color_mkb_layout_1',
			array(
				'label'      => __( 'Section Title Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'settings'   => 'betterdocs_faq_title_color_mkb_layout_1',
				'priority' 	 => 606,
			) )
		);

		// FAQ Section Font Size

		$wp_customize->add_setting( 'betterdocs_faq_title_font_size_mkb_layout_1', array(
			'default'       	=> $defaults['betterdocs_faq_title_font_size_mkb_layout_1'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_faq_title_font_size_mkb_layout_1', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_faq_section',
			'settings' => 'betterdocs_faq_title_font_size_mkb_layout_1',
			'label'    => esc_html__('Section Title Font Size', 'betterdocs-pro'),
			'priority' 	 => 607,
			'input_attrs' => array(
				'class'  => '',
				'min'    => 0,
				'max'    => 50,
				'step'   => 1,
				'suffix' => 'px', // optional suffix
			),
		) ) );

		
		// FAQ Category Title Color

		$wp_customize->add_setting( 'betterdocs_faq_category_title_color_mkb_layout_1' , array(
			'default'     		=> $defaults['betterdocs_faq_category_title_color_mkb_layout_1'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_category_title_color_mkb_layout_1',
			array(
				'label'      => __( 'Group Title Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'priority' 	 => 608,
				'settings'   => 'betterdocs_faq_category_title_color_mkb_layout_1',
			) )
		);

		// FAQ Category Title Font Size

		$wp_customize->add_setting( 'betterdocs_faq_category_name_font_size_mkb_layout_1', array(
			'default'       	=> $defaults['betterdocs_faq_category_name_font_size_mkb_layout_1'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_faq_category_name_font_size_mkb_layout_1', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_faq_section',
			'settings' => 'betterdocs_faq_category_name_font_size_mkb_layout_1',
			'label'    => esc_html__('Group Title Font Size', 'betterdocs-pro'),
			'priority' 	 => 609,
			'input_attrs' => array(
				'class'  => '',
				'min'    => 0,
				'max'    => 50,
				'step'   => 1,
				'suffix' => 'px', // optional suffix
			),
		) ) );

		// FAQ Category Title Padding

		$wp_customize->add_setting('betterdocs_faq_category_name_padding_mkb_layout_1', array(
			'default' 			=> $defaults['betterdocs_faq_category_name_padding_mkb_layout_1'],
			'transport' 		=> 'postMessage',
			'capability'    	=> 'edit_theme_options',
		));

		$wp_customize->add_control(
			new BetterDocs_Multi_Dimension_Control(
				$wp_customize,
				'betterdocs_faq_category_name_padding_mkb_layout_1',
				array(
					'label' => __('Group Title Padding (PX)', 'betterdocs-pro'),
					'section' => 'betterdocs_faq_section',
					'settings' => 'betterdocs_faq_category_name_padding_mkb_layout_1',
					'priority' => 610,
					'input_fields' => array(
						'input1'   	=> __( 'top', 'betterdocs-pro' ),
						'input2'   	=> __( 'right', 'betterdocs-pro' ),
						'input3'   	=> __( 'bottom', 'betterdocs-pro' ),
						'input4'   	=> __( 'left', 'betterdocs-pro' ),
					),
					'defaults' => array(
						'input1'  	=> 20,
						'input2'  	=> 20,
						'input3'  	=> 20,
						'input4'  	=> 20,
					)
				))
		);


		// FAQ List Color
		$wp_customize->add_setting( 'betterdocs_faq_list_color_mkb_layout_1' , array(
			'default'     		=> $defaults['betterdocs_faq_list_color_mkb_layout_1'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_list_color_mkb_layout_1',
			array(
				'label'      => __( 'FAQ List Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'settings'   => 'betterdocs_faq_list_color_mkb_layout_1',
				'priority' 	 => 611,
			) )
		);


		// FAQ List Background Color
		$wp_customize->add_setting( 'betterdocs_faq_list_background_color_mkb_layout_1' , array(
			'default'     		=> $defaults['betterdocs_faq_list_background_color_mkb_layout_1'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_list_background_color_mkb_layout_1',
			array(
				'label'      => __( 'FAQ List Background Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'settings'   => 'betterdocs_faq_list_background_color_mkb_layout_1',
				'priority' 	 => 612,
			) )
		);

		// FAQ List Content Background Color
		$wp_customize->add_setting( 'betterdocs_faq_list_content_background_color_mkb_layout_1' , array(
			'default'     		=> $defaults['betterdocs_faq_list_content_background_color_mkb_layout_1'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_list_content_background_color_mkb_layout_1',
			array(
				'label'      => __( 'FAQ List Content Background Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'settings'   => 'betterdocs_faq_list_content_background_color_mkb_layout_1',
				'priority' 	 => 613,
			) )
		);

		// FAQ List Content Color
		$wp_customize->add_setting( 'betterdocs_faq_list_content_color_mkb_layout_1' , array(
			'default'     		=> $defaults['betterdocs_faq_list_content_color_mkb_layout_1'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_list_content_color_mkb_layout_1',
			array(
				'label'      => __( 'FAQ List Content Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'settings'   => 'betterdocs_faq_list_content_color_mkb_layout_1',
				'priority' 	 => 614,
			) )
		);

		//FAQ List Content Font Size
		$wp_customize->add_setting('betterdocs_faq_list_content_font_size_mkb_layout_1', array(
			'default' 	 		=> $defaults['betterdocs_faq_list_content_font_size_mkb_layout_1'],
			'capability' 		=> 'edit_theme_options',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		));

		$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_faq_list_content_font_size_mkb_layout_1', array(
			'type' 		=> 'betterdocs-range-value',
			'section' 	=> 'betterdocs_faq_section',
			'settings'  => 'betterdocs_faq_list_content_font_size_mkb_layout_1',
			'label' 	=> __('FAQ List Content Font Size', 'betterdocs-pro'),
			'priority'  => 615,
			'input_attrs' => array(
				'class'   => '',
				'min'     => 0,
				'max'	  => 50,
				'step' 	  => 1,
				'suffix'  => 'px', //optional suffix
			),
		)));

		// FAQ List Font Size

		$wp_customize->add_setting( 'betterdocs_faq_list_font_size_mkb_layout_1', array(
			'default'       	=> $defaults['betterdocs_faq_list_font_size_mkb_layout_1'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_faq_list_font_size_mkb_layout_1', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_faq_section',
			'settings' => 'betterdocs_faq_list_font_size_mkb_layout_1',
			'label'    => esc_html__('FAQ List Font Size', 'betterdocs-pro'),
			'priority' 	 => 616,
			'input_attrs' => array(
				'class'  => '',
				'min'    => 0,
				'max'    => 50,
				'step'   => 1,
				'suffix' => 'px', // optional suffix
			),
		) ) );

		// FAQ List Padding

		$wp_customize->add_setting('betterdocs_faq_list_padding_mkb_layout_1', array(
			'default' 			=> $defaults['betterdocs_faq_list_padding_mkb_layout_1'],
			'transport' 		=> 'postMessage',
			'capability'    	=> 'edit_theme_options',
		));

		$wp_customize->add_control(
			new BetterDocs_Multi_Dimension_Control(
				$wp_customize,
				'betterdocs_faq_list_padding_mkb_layout_1',
				array(
					'label' => __('FAQ List Padding (PX)', 'betterdocs-pro'),
					'section' => 'betterdocs_faq_section',
					'settings' => 'betterdocs_faq_list_padding_mkb_layout_1',
					'priority' => 617,
					'input_fields' => array(
						'input1'   	=> __( 'top', 'betterdocs-pro' ),
						'input2'   	=> __( 'right', 'betterdocs-pro' ),
						'input3'   	=> __( 'bottom', 'betterdocs-pro' ),
						'input4'   	=> __( 'left', 'betterdocs-pro' ),
					),
					'defaults' => array(
						'input1'  	=> 20,
						'input2'  	=> 20,
						'input3'  	=> 20,
						'input4'  	=> 20,
					)
				))
		);

		/** MKB FAQ CONTROLLERS LAYOUT 2 **/

		// FAQ Category Title Color

		$wp_customize->add_setting( 'betterdocs_faq_category_title_color_mkb_layout_2' , array(
			'default'     		=> $defaults['betterdocs_faq_category_title_color_mkb_layout_2'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_category_title_color_mkb_layout_2',
			array(
				'label'      => __( 'Group Title Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'priority' 	 => 618,
				'settings'   => 'betterdocs_faq_category_title_color_mkb_layout_2',
			) )
		);

		// FAQ Category Title Font Size

		$wp_customize->add_setting( 'betterdocs_faq_category_name_font_size_mkb_layout_2', array(
			'default'       	=> $defaults['betterdocs_faq_category_name_font_size_mkb_layout_2'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_faq_category_name_font_size_mkb_layout_2', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_faq_section',
			'settings' => 'betterdocs_faq_category_name_font_size_mkb_layout_2',
			'label'    => esc_html__('Group Title Font Size', 'betterdocs-pro'),
			'priority' 	 => 618,
			'input_attrs' => array(
				'class'  => '',
				'min'    => 0,
				'max'    => 50,
				'step'   => 1,
				'suffix' => 'px', // optional suffix
			),
		) ) );

		// FAQ Category Title Padding

		$wp_customize->add_setting('betterdocs_faq_category_name_padding_mkb_layout_2', array(
			'default' 			=> $defaults['betterdocs_faq_category_name_padding_mkb_layout_2'],
			'transport' 		=> 'postMessage',
			'capability'    	=> 'edit_theme_options',
		));

		$wp_customize->add_control(
			new BetterDocs_Multi_Dimension_Control(
				$wp_customize,
				'betterdocs_faq_category_name_padding_mkb_layout_2',
				array(
					'label' => __('Group Title Padding (PX)', 'betterdocs-pro'),
					'section' => 'betterdocs_faq_section',
					'settings' => 'betterdocs_faq_category_name_padding_mkb_layout_2',
					'priority' => 618,
					'input_fields' => array(
						'input1'   	=> __( 'top', 'betterdocs-pro' ),
						'input2'   	=> __( 'right', 'betterdocs-pro' ),
						'input3'   	=> __( 'bottom', 'betterdocs-pro' ),
						'input4'   	=> __( 'left', 'betterdocs-pro' ),
					),
					'defaults' => array(
						'input1'  	=> 20,
						'input2'  	=> 20,
						'input3'  	=> 20,
						'input4'  	=> 20,
					)
				))
		);


		// FAQ List Color
		$wp_customize->add_setting( 'betterdocs_faq_list_color_mkb_layout_2' , array(
			'default'     		=> $defaults['betterdocs_faq_list_color_mkb_layout_2'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_list_color_mkb_layout_2',
			array(
				'label'      => __( 'FAQ List Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'settings'   => 'betterdocs_faq_list_color_mkb_layout_2',
				'priority' 	 => 618,
			) )
		);


		// FAQ List Background Color
		$wp_customize->add_setting( 'betterdocs_faq_list_background_color_mkb_layout_2' , array(
			'default'     		=> $defaults['betterdocs_faq_list_background_color_mkb_layout_2'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_list_background_color_mkb_layout_2',
			array(
				'label'      => __( 'FAQ List Background Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'settings'   => 'betterdocs_faq_list_background_color_mkb_layout_2',
				'priority' 	 => 618,
			) )
		);

		// FAQ List Content Background Color
		$wp_customize->add_setting( 'betterdocs_faq_list_content_background_color_mkb_layout_2' , array(
			'default'     		=> $defaults['betterdocs_faq_list_content_background_color_mkb_layout_2'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_list_content_background_color_mkb_layout_2',
			array(
				'label'      => __( 'FAQ List Content Background Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'settings'   => 'betterdocs_faq_list_content_background_color_mkb_layout_2',
				'priority' 	 => 618,
			) )
		);

		// FAQ List Content Color
		$wp_customize->add_setting( 'betterdocs_faq_list_content_color_mkb_layout_2' , array(
			'default'     		=> $defaults['betterdocs_faq_list_content_color_mkb_layout_2'],
			'capability'    	=> 'edit_theme_options',
			'transport'   		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_rgba',
		) );

		$wp_customize->add_control(
			new BetterDocs_Customizer_Alpha_Color_Control(
			$wp_customize,
			'betterdocs_faq_list_content_color_mkb_layout_2',
			array(
				'label'      => __( 'FAQ List Content Color', 'betterdocs-pro' ),
				'section'    => 'betterdocs_faq_section',
				'settings'   => 'betterdocs_faq_list_content_color_mkb_layout_2',
				'priority' 	 => 618,
			) )
		);		
		
		//FAQ List Content Font Size
		$wp_customize->add_setting('betterdocs_faq_list_content_font_size_mkb_layout_2', array(
			'default' 	 		=> $defaults['betterdocs_faq_list_content_font_size_mkb_layout_2'],
			'capability' 		=> 'edit_theme_options',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'
		));

		$wp_customize->add_control(new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_faq_list_content_font_size_mkb_layout_2', array(
			'type' 		=> 'betterdocs-range-value',
			'section' 	=> 'betterdocs_faq_section',
			'settings'  => 'betterdocs_faq_list_content_font_size_mkb_layout_2',
			'label' 	=> __('FAQ List Content Font Size', 'betterdocs-pro'),
			'priority'  => 618,
			'input_attrs' => array(
				'class'   => '',
				'min'     => 0,
				'max'	  => 50,
				'step' 	  => 1,
				'suffix'  => 'px', //optional suffix
			),
		)));

		// FAQ List Font Size

		$wp_customize->add_setting( 'betterdocs_faq_list_font_size_mkb_layout_2', array(
			'default'       	=> $defaults['betterdocs_faq_list_font_size_mkb_layout_2'],
			'capability'    	=> 'edit_theme_options',
			'transport' 		=> 'postMessage',
			'sanitize_callback' => 'betterdocs_sanitize_integer'

		) );

		$wp_customize->add_control( new BetterDocs_Customizer_Range_Value_Control(
			$wp_customize, 'betterdocs_faq_list_font_size_mkb_layout_2', array(
			'type'     => 'betterdocs-range-value',
			'section'  => 'betterdocs_faq_section',
			'settings' => 'betterdocs_faq_list_font_size_mkb_layout_2',
			'label'    => esc_html__('FAQ List Font Size', 'betterdocs-pro'),
			'priority' 	 => 618,
			'input_attrs' => array(
				'class'  => '',
				'min'    => 0,
				'max'    => 50,
				'step'   => 1,
				'suffix' => 'px', // optional suffix
			),
		) ) );

		// FAQ List Padding

		$wp_customize->add_setting('betterdocs_faq_list_padding_mkb_layout_2', array(
			'default' 			=> $defaults['betterdocs_faq_list_padding_mkb_layout_2'],
			'transport' 		=> 'postMessage',
			'capability'    	=> 'edit_theme_options',
		));

		$wp_customize->add_control(
			new BetterDocs_Multi_Dimension_Control(
				$wp_customize,
				'betterdocs_faq_list_padding_mkb_layout_2',
				array(
					'label' => __('FAQ List Padding (PX)', 'betterdocs-pro'),
					'section' => 'betterdocs_faq_section',
					'settings' => 'betterdocs_faq_list_padding_mkb_layout_2',
					'priority' => 618,
					'input_fields' => array(
						'input1'   	=> __( 'top', 'betterdocs-pro' ),
						'input2'   	=> __( 'right', 'betterdocs-pro' ),
						'input3'   	=> __( 'bottom', 'betterdocs-pro' ),
						'input4'   	=> __( 'left', 'betterdocs-pro' ),
					),
					'defaults' => array(
						'input1'  	=> 20,
						'input2'  	=> 20,
						'input3'  	=> 20,
						'input4'  	=> 20,
					)
			)	)
		);
	}



}
add_action( 'customize_register', 'betterdocs_customize_register_pro' );

require_once( BETTERDOCS_PRO_ADMIN_DIR_PATH . 'customizer/output-css.php' );
