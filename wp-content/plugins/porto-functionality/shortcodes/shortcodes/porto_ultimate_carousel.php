<?php
// Porto ultimate carousel
add_action( 'vc_after_init', 'porto_load_ultimate_carousel_shortcode' );

function porto_load_ultimate_carousel_shortcode() {

	$animation_type        = porto_vc_animation_type();
	$animation_duration    = porto_vc_animation_duration();
	$animation_delay       = porto_vc_animation_delay();
	$custom_class          = porto_vc_custom_class();
	$custom_class['group'] = 'General';

	vc_map(
		array(
			'name'                    => __( 'Porto Advanced Carousel', 'porto-functionality' ),
			'base'                    => 'porto_ultimate_carousel',
			'icon'                    => 'fas fa-fast-forward',
			'class'                   => 'porto_ultimate_carousel',
			'as_parent'               => array( 'except' => 'porto_ultimate_carousel' ),
			'content_element'         => true,
			'controls'                => 'full',
			'show_settings_on_create' => true,
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Carousel anything.', 'porto-functionality' ),
			'params'                  => array(
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Slides to Scroll', 'porto-functionality' ),
					'param_name' => 'slide_to_scroll',
					'value'      => array(
						'All visible'   => 'all',
						'One at a Time' => 'single',
					),
					'group'      => 'General',
				),
				array(
					'type'             => 'number',
					'class'            => '',
					'edit_field_class' => 'vc_col-sm-4 items_to_show porto_margin_bottom',
					'heading'          => __( 'On Desktop', 'porto-functionality' ),
					'param_name'       => 'slides_on_desk',
					'value'            => '5',
					'min'              => '1',
					'max'              => '25',
					'step'             => '1',
					'group'            => 'General',
				),
				array(
					'type'             => 'number',
					'class'            => '',
					'edit_field_class' => 'vc_col-sm-4 items_to_show porto_margin_bottom',
					'heading'          => __( 'On Tabs', 'porto-functionality' ),
					'param_name'       => 'slides_on_tabs',
					'value'            => '3',
					'min'              => '1',
					'max'              => '25',
					'step'             => '1',
					'group'            => 'General',
				),
				array(
					'type'             => 'number',
					'class'            => '',
					'edit_field_class' => 'vc_col-sm-4 items_to_show porto_margin_bottom',
					'heading'          => __( 'On Mobile', 'porto-functionality' ),
					'param_name'       => 'slides_on_mob',
					'value'            => '2',
					'min'              => '1',
					'max'              => '25',
					'step'             => '1',
					'group'            => 'General',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Infinite loop', 'porto-functionality' ),
					'param_name' => 'infinite_loop',
					'value'      => array(
						'Yes' => 'on',
						'No'  => 'off',
					),
					'group'      => 'General',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Autoplay Slides‏', 'porto-functionality' ),
					'param_name' => 'autoplay',
					'value'      => array(
						'Yes' => 'on',
						'No'  => 'off',
					),
					'group'      => 'General',
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Autoplay Speed', 'porto-functionality' ),
					'param_name' => 'autoplay_speed',
					'value'      => '5000',
					'min'        => '100',
					'max'        => '10000',
					'step'       => '10',
					'suffix'     => 'ms',
					'dependency' => array(
						'element' => 'autoplay',
						'value'   => array( 'on' ),
					),
					'group'      => 'General',
				),
				$custom_class,
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Navigation Arrows', 'porto-functionality' ),
					'description' => __( 'Display next / previous navigation arrows', 'porto-functionality' ),
					'param_name'  => 'arrows',
					'value'       => array(
						'Yes' => 'on',
						'No'  => 'off',
					),
					'group'       => 'Navigation',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Arrow Style', 'porto-functionality' ),
					'param_name' => 'arrow_style',
					'value'      => array(
						'Default'           => 'default',
						'Circle Background' => 'circle-bg',
						'Square Background' => 'square-bg',
						'Circle Border'     => 'circle-border',
						'Square Border'     => 'square-border',
					),
					'dependency' => array(
						'element' => 'arrows',
						'value'   => array( 'on' ),
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'arrow_bg_color',
					'value'      => '',
					'dependency' => array(
						'element' => 'arrow_style',
						'value'   => array( 'circle-bg', 'square-bg' ),
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Arrow Color', 'porto-functionality' ),
					'param_name' => 'arrow_color',
					'value'      => '#333333',
					'dependency' => array(
						'element' => 'arrows',
						'value'   => array( 'on' ),
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Arrow Size', 'porto-functionality' ),
					'param_name' => 'arrow_size',
					'value'      => '20',
					'min'        => '10',
					'max'        => '75',
					'step'       => '1',
					'suffix'     => 'px',
					'dependency' => array(
						'element' => 'arrows',
						'value'   => array( 'on' ),
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon library', 'porto-functionality' ),
					'value'      => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
					),
					'param_name' => 'icon_type',
					'dependency' => array(
						'element' => 'arrows',
						'value'   => array( 'on' ),
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( "Select icon for 'Next Arrow'", 'porto-functionality' ),
					'param_name' => 'next_icon',
					'value'      => 'fas fa-chevron-left',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'fontawesome',
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( "Select icon for 'Previous Arrow'", 'porto-functionality' ),
					'param_name' => 'prev_icon',
					'value'      => 'fas fa-chevron-right',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'fontawesome',
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( "Select icon for 'Next Arrow'", 'porto-functionality' ),
					'param_name' => 'next_icon_simpleline',
					'value'      => '',
					'settings'   => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'simpleline',
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( "Select icon for 'Previous Arrow'", 'porto-functionality' ),
					'param_name' => 'prev_icon_simpleline',
					'value'      => '',
					'settings'   => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'simpleline',
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( "Select icon for 'Next Arrow'", 'porto-functionality' ),
					'param_name' => 'next_icon_porto',
					'value'      => '',
					'settings'   => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'porto',
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( "Select icon for 'Previous Arrow'", 'porto-functionality' ),
					'param_name' => 'prev_icon_porto',
					'value'      => '',
					'settings'   => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'porto',
					),
					'group'      => 'Navigation',
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Dots Navigation', 'porto-functionality' ),
					'description' => __( 'Display dot navigation', 'porto-functionality' ),
					'param_name'  => 'dots',
					'value'       => array(
						'Yes' => 'on',
						'No'  => 'off',
					),
					'group'       => 'Navigation',
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Color of dots', 'porto-functionality' ),
					'param_name' => 'dots_color',
					'value'      => '#333333',
					'dependency' => array(
						'element' => 'dots',
						'value'   => array( 'on' ),
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon library for \'Navigation Dots\'', 'porto-functionality' ),
					'value'      => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
					),
					'param_name' => 'dots_icon_type',
					'dependency' => array(
						'element' => 'dots',
						'value'   => array( 'on' ),
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( "Select icon for 'Navigation Dots'", 'porto-functionality' ),
					'param_name' => 'dots_icon',
					'value'      => 'far fa-circle',
					'dependency' => array(
						'element' => 'dots_icon_type',
						'value'   => 'fontawesome',
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( "Select icon for 'Navigation Dots'", 'porto-functionality' ),
					'param_name' => 'dots_icon_simpleline',
					'settings'   => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'dots_icon_type',
						'value'   => 'simpleline',
					),
					'group'      => 'Navigation',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( "Select icon for 'Navigation Dots'", 'porto-functionality' ),
					'param_name' => 'dots_icon_porto',
					'settings'   => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'dots_icon_type',
						'value'   => 'porto',
					),
					'group'      => 'Navigation',
				),
				$animation_type,
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'RTL Mode', 'porto-functionality' ),
					'param_name' => 'rtl',
					'value'      => array(
						'No'  => '',
						'Yes' => 'on',
					),
					'default'    => '',
					'group'      => 'Advanced',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Adaptive Height', 'porto-functionality' ),
					'param_name' => 'adaptive_height',
					'value'      => array(
						'No'  => '',
						'Yes' => 'on',
					),
					'dependency' => '',
					'group'      => 'Advanced',
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Space between two items', 'porto-functionality' ),
					'param_name' => 'item_space',
					'value'      => '15',
					'min'        => '0',
					'max'        => '1000',
					'step'       => '1',
					'suffix'     => 'px',
					'group'      => 'Advanced',
				),
				array(
					'type'             => 'css_editor',
					'heading'          => __( 'Css', 'porto-functionality' ),
					'param_name'       => 'css_ad_caraousel',
					'group'            => __( 'Design ', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
				),
			),
			'js_view'                 => 'VcColumnView',
		)
	); // vc_map

	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_porto_ultimate_carousel extends WPBakeryShortCodesContainer {
			protected $controls_list = array(
				'add',
				'edit',
				'clone',
				'delete',
			);
		}
	}
}
