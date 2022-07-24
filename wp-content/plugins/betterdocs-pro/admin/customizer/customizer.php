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
                        'layout-3' 	=> array(
							'image' => BETTERDOCS_ADMIN_URL . 'assets/img/docs-layout-5.png',
						),
                        'layout-4' 	=> array(
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
			'label'      => __( 'Reactions Icon Background Color', 'betterdocs-pro' ),
			'priority'   => 163,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_post_reactions_icon_color',
		) )
	);
	
	$wp_customize->add_setting( 'betterdocs_post_reactions_icon_svg_color' , array(
		'default'     => $defaults['betterdocs_post_reactions_icon_svg_color'],
		'capability'    => 'edit_theme_options',
	    'transport'   => 'postMessage',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_post_reactions_icon_svg_color',
		array(
			'label'      => __( 'Reactions Icon Color', 'betterdocs-pro' ),
			'priority'   => 163,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_post_reactions_icon_svg_color',
		) )
	);
	
	$wp_customize->add_setting( 'betterdocs_post_reactions_icon_hover_bg_color' , array(
		'default'     => $defaults['betterdocs_post_reactions_icon_hover_bg_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_post_reactions_icon_hover_bg_color',
		array(
			'label'      => __( 'Reactions Icon Hover Background Color', 'betterdocs-pro' ),
			'priority'   => 163,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_post_reactions_icon_hover_bg_color',
		) )
	);
	
	$wp_customize->add_setting( 'betterdocs_post_reactions_icon_hover_svg_color' , array(
		'default'     => $defaults['betterdocs_post_reactions_icon_hover_svg_color'],
		'capability'    => 'edit_theme_options',
	    'sanitize_callback' => 'betterdocs_sanitize_rgba',
	) );

	$wp_customize->add_control(
		new BetterDocs_Customizer_Alpha_Color_Control(
		$wp_customize,
		'betterdocs_post_reactions_icon_hover_svg_color',
		array(
			'label'      => __( 'Reactions Icon Hover Color', 'betterdocs-pro' ),
			'priority'   => 163,
			'section'    => 'betterdocs_single_docs_settings',
			'settings'   => 'betterdocs_post_reactions_icon_hover_svg_color',
		) )
	);

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
        'label' => esc_html__('Enable Search Button', 'betterdocs'),
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
        'label'    => esc_html__('Enable Popular Search', 'betterdocs'),
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
        'label'	        => esc_html__('Category Select Settings', 'betterdocs'),
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
        'label'    => esc_html__('Font Size', 'betterdocs'),
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
                'label'      => esc_html__('Font Weight', 'betterdocs'),
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
                'label'      => esc_html__('Font Text Transform', 'betterdocs'),
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
                'label'      => esc_html__('Font Color', 'betterdocs'),
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
        'label'	      => esc_html__('Search Button Settings', 'betterdocs'),
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
        'label'    => esc_html__('Font Size', 'betterdocs'),
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
        'label'    => esc_html__('Font Letter Spacing', 'betterdocs'),
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
                'label'      => esc_html__('Font Weight', 'betterdocs'),
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
                'label'      => esc_html__('Font Text Transform', 'betterdocs'),
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
                'label'      => esc_html__('Text Color', 'betterdocs'),
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
                'label'      => esc_html__('Background Color', 'betterdocs'),
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
                'label'      => esc_html__('Background Hover Color', 'betterdocs'),
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
        'label'    => esc_html__('Border Radius', 'betterdocs'),
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
        'label'    => esc_html__('Left Top', 'betterdocs'),
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
        'label'    => esc_html__('Right Top', 'betterdocs'),
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
        'label'    => esc_html__('Left Bottom', 'betterdocs'),
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
        'label'    => esc_html__('Right Bottom', 'betterdocs'),
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
        'label'    => esc_html__('Padding', 'betterdocs'),
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
        'label'    => esc_html__('Top', 'betterdocs'),
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
        'label'    => esc_html__('Right', 'betterdocs'),
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
        'label'    => esc_html__('Bottom', 'betterdocs'),
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
        'label'    => esc_html__('Left', 'betterdocs'),
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
        'label'	        => esc_html__('Popular Search Settings', 'betterdocs'),
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
        'label'    => esc_html__('Popular Search Margin', 'betterdocs'),
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
        'label'    => esc_html__('Top', 'betterdocs'),
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
        'label'    => esc_html__('Right', 'betterdocs'),
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
        'label'    => esc_html__('Bottom', 'betterdocs'),
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
        'label'    => esc_html__('Left', 'betterdocs'),
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
                'label' => esc_html__('Sub Heading', 'betterdocs'),
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
                'label'      => esc_html__('Title Color', 'betterdocs'),
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
        'label'    => esc_html__('Title Font Size', 'betterdocs'),
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
        'label'    => esc_html__('Keyword Font Size', 'betterdocs'),
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
                'label'      => esc_html__('Keyword Border Type', 'betterdocs'),
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
                'label'      => esc_html__('Keyword Border Color', 'betterdocs'),
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
        'label'    => esc_html__('Keyword Border Width', 'betterdocs'),
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
        'label'    => esc_html__('Top', 'betterdocs'),
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
        'label'    => esc_html__('Right', 'betterdocs'),
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
        'label'    => esc_html__('Bottom', 'betterdocs'),
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
        'label'    => esc_html__('Left', 'betterdocs'),
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
                'label'      => esc_html__('Keyword Background Color', 'betterdocs'),
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
                'label'      => esc_html__('Keyword Text Color', 'betterdocs'),
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
        'label'    => esc_html__('Keyword Border Radius', 'betterdocs'),
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
        'label'    => esc_html__('Left Top', 'betterdocs'),
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
        'label'    => esc_html__('Right Top', 'betterdocs'),
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
        'label'    => esc_html__('Left Bottom', 'betterdocs'),
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
        'label'    => esc_html__('Right Bottom', 'betterdocs'),
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
        'label'    => esc_html__('Keyword Padding', 'betterdocs'),
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
        'label'    => esc_html__('Top', 'betterdocs'),
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
        'label'    => esc_html__('Right', 'betterdocs'),
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
        'label'    => esc_html__('Bottom', 'betterdocs'),
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
        'label'    => esc_html__('Left', 'betterdocs'),
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
        'label'    => esc_html__('Keyword Margin', 'betterdocs'),
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
        'label'    => esc_html__('Top', 'betterdocs'),
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
        'label'    => esc_html__('Right', 'betterdocs'),
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
        'label'    => esc_html__('Bottom', 'betterdocs'),
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
        'label'    => esc_html__('Left', 'betterdocs'),
        'input_attrs' => array(
            'class' => 'betterdocs_popular_search_keyword_margin betterdocs-dimension',
        ),
        'priority' 	 => 620
    ) ) );
    }

}
add_action( 'customize_register', 'betterdocs_customize_register_pro' );

require_once( BETTERDOCS_PRO_ADMIN_DIR_PATH . 'customizer/output-css.php' );
