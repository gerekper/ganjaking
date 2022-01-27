<?php
if ( ! class_exists( 'GT3_Core_Elementor' ) || ! class_exists( 'Redux' ) ) {
	return;
}

$theme    = wp_get_theme();
$opt_name = 'agrosector';

$args = array(
	'opt_name'             => $opt_name,
	'display_name'         => $theme->get( 'Name' ),
	'display_version'      => $theme->get( 'Version' ),
	'menu_type'            => 'menu',
	'allow_sub_menu'       => true,
	'menu_title'           => esc_html__( 'Theme Options', 'agrosector' ),
	'page_title'           => esc_html__( 'Theme Options', 'agrosector' ),
	'google_api_key'       => '',
	'google_update_weekly' => false,
	'async_typography'     => true,
	'admin_bar'            => true,
	'admin_bar_icon'       => 'dashicons-admin-generic',
	'admin_bar_priority'   => 50,
	'global_variable'      => '',
	'dev_mode'             => false,
	'update_notice'        => true,
	'customizer'           => false,
	'page_priority'        => null,
	'page_parent'          => 'themes.php',
	'page_permissions'     => 'manage_options',
	'menu_icon'            => 'dashicons-admin-generic',
	'last_tab'             => '',
	'page_icon'            => 'icon-themes',
	'page_slug'            => '',
	'save_defaults'        => true,
	'default_show'         => false,
	'default_mark'         => '',
	'show_import_export'   => true,
	'transient_time'       => 60 * MINUTE_IN_SECONDS,
	'output'               => true,
	'output_tag'           => true,
	'database'             => '',
	'use_cdn'              => true,
);


Redux::setArgs( $opt_name, $args );

// -> START Basic Fields
Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'General', 'agrosector' ),
	'id'               => 'general',
	'customizer_width' => '400px',
	'icon'             => 'el el-home',
	'fields'           => array(
		array(
			'id'      => 'responsive',
			'type'    => 'switch',
			'title'   => esc_html__( 'Responsive', 'agrosector' ),
			'default' => true,
		),
		array(
			'id'      => 'page_comments',
			'type'    => 'switch',
			'title'   => esc_html__( 'Page Comments', 'agrosector' ),
			'default' => true,
		),
		array(
			'id'      => 'back_to_top',
			'type'    => 'switch',
			'title'   => esc_html__( 'Back to Top', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'    => 'team_slug',
			'type'  => 'text',
			'title' => esc_html__( 'Team Slug', 'agrosector' ),
		),
		array(
			'id'    => 'portfolio_slug',
			'type'  => 'text',
			'title' => esc_html__( 'Portfolio Slug', 'agrosector' ),
		),
		array(
			'id'    => 'project_slug',
			'type'  => 'text',
			'title' => esc_html__( 'Project Slug', 'agrosector' ),
		),
		array(
			'id'    => 'page_404_bg',
			'type'  => 'media',
			'title' => esc_html__( 'Page 404 Background Image', 'agrosector' ),
		),
		array(
			'id'      => 'add_default_typography_sapcing',
			'type'    => 'switch',
			'title'   => esc_html__( 'Add Default Typography Spacings', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'      => 'disable_right_click',
			'type'    => 'switch',
			'title'   => esc_html__( 'Disable right click', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'       => 'disable_right_click_text',
			'type'     => 'text',
			'title'    => esc_html__( 'Right click alert text', 'agrosector' ),
			'default'  => esc_html__( 'The right click is disabled. Your content is protected. You can configure this option in the theme.', 'agrosector' ),
			'required' => array( 'disable_right_click', '=', '1' ),
		),
		array(
			'id'       => 'custom_js',
			'type'     => 'ace_editor',
			'title'    => esc_html__( 'Custom JS', 'agrosector' ),
			'subtitle' => esc_html__( 'Paste your JS code here.', 'agrosector' ),
			'mode'     => 'javascript',
			'theme'    => 'chrome',
			'default'  => "jQuery(document).ready(function(){\n\n});"
		),
		array(
			'id'       => 'header_custom_js',
			'type'     => 'ace_editor',
			'title'    => esc_html__( 'Custom JS', 'agrosector' ),
			'subtitle' => esc_html__( 'Code to be added inside HEAD tag', 'agrosector' ),
			'mode'     => 'html',
			'theme'    => 'chrome',
			'default'  => "<script type='text/javascript'>\njQuery(document).ready(function(){\n\n});\n</script>"
		),
	),
) );

Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Preloader', 'agrosector' ),
	'id'               => 'preloader-option',
	'customizer_width' => '400px',
	'icon'             => 'el-icon-graph',
	'fields'           => array(
		array(
			'id'      => 'preloader',
			'type'    => 'switch',
			'title'   => esc_html__( 'Preloader', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'       => 'preloader_type',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Preloader type', 'agrosector' ),
			'options'  => array(
				'linear' => esc_html__( 'Linear', 'agrosector' ),
				'circle' => esc_html__( 'Circle', 'agrosector' ),
				'theme'  => esc_html__( 'Theme', 'agrosector' ),
			),
			'default'  => 'circle',
			'required' => array( 'preloader', '=', '1' ),
		),
		array(
			'id'          => 'preloader_background',
			'type'        => 'color',
			'title'       => esc_html__( 'Preloader Background', 'agrosector' ),
			'subtitle'    => esc_html__( 'Set Preloader Background', 'agrosector' ),
			'default'     => '#ffffff',
			'transparent' => false,
			'required'    => array( 'preloader', '=', '1' ),
		),
		array(
			'id'          => 'preloader_item_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Preloader Stroke Background Color', 'agrosector' ),
			'subtitle'    => esc_html__( 'Set Preloader Stroke Background Color', 'agrosector' ),
			'default'     => '#98a1a9',
			'transparent' => false,
			'required'    => array( 'preloader', '=', '1' ),
		),
		array(
			'id'          => 'preloader_item_color2',
			'type'        => 'color',
			'title'       => esc_html__( 'Preloader Stroke Foreground Color', 'agrosector' ),
			'subtitle'    => esc_html__( 'Set Preloader Stroke Foreground Color', 'agrosector' ),
			'default'     => '#e94e76',
			'transparent' => false,
			'required'    => array( 'preloader', '=', '1' ),
		),
		array(
			'id'          => 'preloader_item_width',
			'type'        => 'dimensions',
			'title'       => esc_html__( 'Preloader Circle Width', 'agrosector' ),
			'subtitle'    => esc_html__( 'Set Preloader Circle Width in px (Diameter)', 'agrosector' ),
			'units'       => false,
			'height'      => false,
			'default'     => array(
				'width' => '140',
			),
			'transparent' => false,
			'required'    => array(
				array( 'preloader', '=', '1' ),
				array( 'preloader_type', '=', array( 'circle', 'theme' ) )
			),
		),
		array(
			'id'          => 'preloader_item_stroke',
			'type'        => 'dimensions',
			'title'       => esc_html__( 'Preloader Circle Stroke Width', 'agrosector' ),
			'subtitle'    => esc_html__( 'Set Preloader Circle Stroke Width in px', 'agrosector' ),
			'units'       => false,
			'height'      => false,
			'default'     => array(
				'width' => '3'
			),
			'transparent' => false,
			'required'    => array(
				array( 'preloader', '=', '1' ),
				array( 'preloader_type', '=', array( 'circle', 'theme' ) )
			),
		),
		array(
			'id'       => 'preloader_item_logo',
			'type'     => 'media',
			'title'    => esc_html__( 'Preloader Logo', 'agrosector' ),
			'required' => array( 'preloader', '=', '1' ),
		),
		array(
			'id'          => 'preloader_item_logo_width',
			'type'        => 'dimensions',
			'title'       => esc_html__( 'Preloader Logo Width', 'agrosector' ),
			'subtitle'    => esc_html__( 'Set Preloader Logo Width', 'agrosector' ),
			'units'       => array( 'px', '%' ),
			'height'      => false,
			'default'     => array(
				'width' => '45',
				'units' => 'px',
			),
			'transparent' => false,
			'required'    => array(
				array( 'preloader', '=', '1' ),
				array( 'preloader_type', '=', array( 'circle', 'theme' ) )
			),
		),
		array(
			'id'       => 'preloader_full',
			'type'     => 'switch',
			'title'    => esc_html__( 'Preloader Fullscreen', 'agrosector' ),
			'default'  => true,
			'required' => array( 'preloader', '=', '1' ),
		),
	)
) );


// HEADER
if ( function_exists( 'gt3_header_presets' ) ) {
	$presets         = gt3_header_presets();
	$header_preset_1 = $presets['header_preset_1'];
}

function gt3_getMenuList() {
	$menus     = wp_get_nav_menus();
	$menu_list = array();

	foreach ( $menus as $menu => $menu_obj ) {
		$menu_list[ $menu_obj->slug ] = $menu_obj->name;
	}

	return $menu_list;
}


$def_header_option = array(
	'all_item'              => array(
		'title'   => 'All Item',
		'layout'  => 'all',
		'content' => array(
			'search'         => array(
				'title'        => 'Search',
				'has_settings' => false,
			),
			'login'          => array(
				'title'        => 'Login',
				'has_settings' => false,
			),
			'cart'           => array(
				'title'        => 'Cart',
				'has_settings' => false,
			),
			'burger_sidebar' => array(
				'title'        => 'Burger Sidebar',
				'has_settings' => true,
			),
			'text1'          => array(
				'title'        => 'Text/HTML 1',
				'has_settings' => true,
			),
			'text2'          => array(
				'title'        => 'Text/HTML 2',
				'has_settings' => true,
			),

			'text3' => array(
				'title'        => 'Text/HTML 3',
				'has_settings' => true,
			),
			'text4' => array(
				'title'        => 'Text/HTML 4',
				'has_settings' => true,
			),

			'text5'        => array(
				'title'        => 'Text/HTML 5',
				'has_settings' => true,
			),
			'text6'        => array(
				'title'        => 'Text/HTML 6',
				'has_settings' => true,
			),
			'delimiter1'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter2'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter3'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter4'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter5'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter6'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'empty_space1' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space2' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space3' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space4' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space5' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
		),
	),
	'top_left'              => array(
		'title'        => 'Top Left',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'content'      => array(),
	),
	'top_center'            => array(
		'title'        => 'Top Center',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'content'      => array(),
	),
	'top_right'             => array(
		'title'        => 'Top Right',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'content'      => array(),
	),
	'middle_left'           => array(
		'title'        => 'Middle Left',
		'has_settings' => true,
		'layout'       => 'one-thirds clear-item',
		'content'      => array(
			'logo' => array(
				'title'        => 'Logo',
				'has_settings' => true,
			),
		),
	),
	'middle_center'         => array(
		'title'        => 'Middle Center',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'content'      => array(),
	),
	'middle_right'          => array(
		'title'        => 'Middle Right',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'content'      => array(
			'menu' => array(
				'title'        => 'Menu',
				'has_settings' => true,
			),
		),
	),
	'bottom_left'           => array(
		'title'        => 'Bottom Left',
		'has_settings' => true,
		'layout'       => 'one-thirds clear-item',
		'content'      => array(),
	),
	'bottom_center'         => array(
		'title'        => 'Bottom Center',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'content'      => array(),
	),
	'bottom_right'          => array(
		'title'        => 'Bottom Right',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'content'      => array(),
	),

	/// tablet
	'all_item__tablet'      => array(
		'title'       => 'All Item',
		'layout'      => 'all',
		'extra_class' => 'tablet',
		'content'     => array(
			'logo'           => array(
				'title'        => 'Logo',
				'has_settings' => true,
			),
			'menu'           => array(
				'title'        => 'Menu',
				'has_settings' => true,
			),
			'search'         => array(
				'title'        => 'Search',
				'has_settings' => false,
			),
			'login'          => array(
				'title'        => 'Login',
				'has_settings' => false,
			),
			'cart'           => array(
				'title'        => 'Cart',
				'has_settings' => false,
			),
			'burger_sidebar' => array(
				'title'        => 'Burger Sidebar',
				'has_settings' => true,
			),
			'text1'          => array(
				'title'        => 'Text/HTML 1',
				'has_settings' => true,
			),
			'text2'          => array(
				'title'        => 'Text/HTML 2',
				'has_settings' => true,
			),

			'text3' => array(
				'title'        => 'Text/HTML 3',
				'has_settings' => true,
			),
			'text4' => array(
				'title'        => 'Text/HTML 4',
				'has_settings' => true,
			),

			'text5'        => array(
				'title'        => 'Text/HTML 5',
				'has_settings' => true,
			),
			'text6'        => array(
				'title'        => 'Text/HTML 6',
				'has_settings' => true,
			),
			'delimiter1'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter2'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter3'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter4'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter5'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter6'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'empty_space1' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space2' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space3' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space4' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space5' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
		),
	),
	'top_left__tablet'      => array(
		'title'        => 'Top Left',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'tablet',
		'content'      => array(),
	),
	'top_center__tablet'    => array(
		'title'        => 'Top Center',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'tablet',
		'content'      => array(),
	),
	'top_right__tablet'     => array(
		'title'        => 'Top Right',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'tablet',
		'content'      => array(),
	),
	'middle_left__tablet'   => array(
		'title'        => 'Middle Left',
		'has_settings' => true,
		'layout'       => 'one-thirds clear-item',
		'extra_class'  => 'tablet',
		'content'      => array(),
	),
	'middle_center__tablet' => array(
		'title'        => 'Middle Center',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'tablet',
		'content'      => array(),
	),
	'middle_right__tablet'  => array(
		'title'        => 'Middle Right',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'tablet',
		'content'      => array(),
	),
	'bottom_left__tablet'   => array(
		'title'        => 'Bottom Left',
		'has_settings' => true,
		'layout'       => 'one-thirds clear-item',
		'extra_class'  => 'tablet',
		'content'      => array(),
	),
	'bottom_center__tablet' => array(
		'title'        => 'Bottom Center',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'tablet',
		'content'      => array(),
	),
	'bottom_right__tablet'  => array(
		'title'        => 'Bottom Right',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'tablet',
		'content'      => array(),
	),


	/// mobile
	'all_item__mobile'      => array(
		'title'       => 'All Item',
		'layout'      => 'all',
		'extra_class' => 'mobile',
		'content'     => array(
			'logo'           => array(
				'title'        => 'Logo',
				'has_settings' => true,
			),
			'menu'           => array(
				'title'        => 'Menu',
				'has_settings' => true,
			),
			'search'         => array(
				'title'        => 'Search',
				'has_settings' => false,
			),
			'login'          => array(
				'title'        => 'Login',
				'has_settings' => false,
			),
			'cart'           => array(
				'title'        => 'Cart',
				'has_settings' => false,
			),
			'burger_sidebar' => array(
				'title'        => 'Burger Sidebar',
				'has_settings' => true,
			),
			'text1'          => array(
				'title'        => 'Text/HTML 1',
				'has_settings' => true,
			),
			'text2'          => array(
				'title'        => 'Text/HTML 2',
				'has_settings' => true,
			),

			'text3' => array(
				'title'        => 'Text/HTML 3',
				'has_settings' => true,
			),
			'text4' => array(
				'title'        => 'Text/HTML 4',
				'has_settings' => true,
			),

			'text5'        => array(
				'title'        => 'Text/HTML 5',
				'has_settings' => true,
			),
			'text6'        => array(
				'title'        => 'Text/HTML 6',
				'has_settings' => true,
			),
			'delimiter1'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter2'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter3'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter4'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter5'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'delimiter6'   => array(
				'title'        => '|',
				'has_settings' => true,
			),
			'empty_space1' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space2' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space3' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space4' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
			'empty_space5' => array(
				'title'        => '&#8592;&#8594;',
				'has_settings' => false,
			),
		),
	),
	'top_left__mobile'      => array(
		'title'        => 'Top Left',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'mobile',
		'content'      => array(),
	),
	'top_center__mobile'    => array(
		'title'        => 'Top Center',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'mobile',
		'content'      => array(),
	),
	'top_right__mobile'     => array(
		'title'        => 'Top Right',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'mobile',
		'content'      => array(),
	),
	'middle_left__mobile'   => array(
		'title'        => 'Middle Left',
		'has_settings' => true,
		'layout'       => 'one-thirds clear-item',
		'extra_class'  => 'mobile',
		'content'      => array(),
	),
	'middle_center__mobile' => array(
		'title'        => 'Middle Center',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'mobile',
		'content'      => array(),
	),
	'middle_right__mobile'  => array(
		'title'        => 'Middle Right',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'mobile',
		'content'      => array(),
	),
	'bottom_left__mobile'   => array(
		'title'        => 'Bottom Left',
		'has_settings' => true,
		'layout'       => 'one-thirds clear-item',
		'extra_class'  => 'mobile',
		'content'      => array(),
	),
	'bottom_center__mobile' => array(
		'title'        => 'Bottom Center',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'mobile',
		'content'      => array(),
	),
	'bottom_right__mobile'  => array(
		'title'        => 'Bottom Right',
		'has_settings' => true,
		'layout'       => 'one-thirds',
		'extra_class'  => 'mobile',
		'content'      => array(),
	),
);

$options = array(
	array(
		'id'               => 'gt3_header_builder_id',
		'type'             => 'gt3_header_builder',
		'full_width'       => true,
		'presets'          => 'default',
		'reload_on_change' => true,
		'options'          => array(
			'all_item'         => array(
				'title'   => 'All Item',
				'layout'  => 'all',
				'content' => array(
					'search'         => array(
						'title'        => 'Search',
						'has_settings' => false,
					),
					'login'          => array(
						'title'        => 'Login',
						'has_settings' => false,
					),
					'cart'           => array(
						'title'        => 'Cart',
						'has_settings' => false,
					),
					'burger_sidebar' => array(
						'title'        => 'Burger Sidebar',
						'has_settings' => true,
					),
					'text1'          => array(
						'title'        => 'Text/HTML 1',
						'has_settings' => true,
					),
					'text2'          => array(
						'title'        => 'Text/HTML 2',
						'has_settings' => true,
					),
					'text3'          => array(
						'title'        => 'Text/HTML 3',
						'has_settings' => true,
					),
					'text4'          => array(
						'title'        => 'Text/HTML 4',
						'has_settings' => true,
					),
					'text5'          => array(
						'title'        => 'Text/HTML 5',
						'has_settings' => true,
					),
					'text6'          => array(
						'title'        => 'Text/HTML 6',
						'has_settings' => true,
					),
					'delimiter1'     => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter2'     => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter3'     => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter4'     => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter5'     => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter6'     => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'empty_space1'   => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space2'   => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space3'   => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space4'   => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space5'   => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
				),
			),
			'top_left'         => array(
				'title'        => 'Top Left',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'top_center'       => array(
				'title'        => 'Top Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'top_right'        => array(
				'title'        => 'Top Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'middle_left'      => array(
				'title'        => 'Middle Left',
				'has_settings' => true,
				'layout'       => 'one-thirds clear-item',
				'content'      => array(
					'logo' => array(
						'title'        => 'Logo',
						'has_settings' => true,
					),
				),
			),
			'middle_center'    => array(
				'title'        => 'Middle Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'middle_right'     => array(
				'title'        => 'Middle Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(
					'menu' => array(
						'title'        => 'Menu',
						'has_settings' => true,
					),
				),
			),
			'bottom_left'      => array(
				'title'        => 'Bottom Left',
				'has_settings' => true,
				'layout'       => 'one-thirds clear-item',
				'content'      => array(),
			),
			'bottom_center'    => array(
				'title'        => 'Bottom Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'bottom_right'     => array(
				'title'        => 'Bottom Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),

			/// tablet
			'all_item__tablet' => array(
				'title'       => 'All Item',
				'layout'      => 'all',
				'extra_class' => 'tablet',
				'content'     => array(
					'logo'           => array(
						'title'        => 'Logo',
						'has_settings' => true,
					),
					'menu'           => array(
						'title'        => 'Menu',
						'has_settings' => true,
					),
					'search'         => array(
						'title'        => 'Search',
						'has_settings' => false,
					),
					'login'          => array(
						'title'        => 'Login',
						'has_settings' => false,
					),
					'cart'           => array(
						'title'        => 'Cart',
						'has_settings' => false,
					),
					'burger_sidebar' => array(
						'title'        => 'Burger Sidebar',
						'has_settings' => true,
					),
					'text1'          => array(
						'title'        => 'Text/HTML 1',
						'has_settings' => true,
					),
					'text2'          => array(
						'title'        => 'Text/HTML 2',
						'has_settings' => true,
					),

					'text3' => array(
						'title'        => 'Text/HTML 3',
						'has_settings' => true,
					),
					'text4' => array(
						'title'        => 'Text/HTML 4',
						'has_settings' => true,
					),

					'text5'        => array(
						'title'        => 'Text/HTML 5',
						'has_settings' => true,
					),
					'text6'        => array(
						'title'        => 'Text/HTML 6',
						'has_settings' => true,
					),
					'delimiter1'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter2'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter3'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter4'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter5'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter6'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'empty_space1' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space2' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space3' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space4' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space5' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
				),
			),


			'top_left__tablet'      => array(
				'title'        => 'Top Left',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'tablet',
				'content'      => array(),
			),
			'top_center__tablet'    => array(
				'title'        => 'Top Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'tablet',
				'content'      => array(),
			),
			'top_right__tablet'     => array(
				'title'        => 'Top Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'tablet',
				'content'      => array(),
			),
			'middle_left__tablet'   => array(
				'title'        => 'Middle Left',
				'has_settings' => true,
				'layout'       => 'one-thirds clear-item',
				'extra_class'  => 'tablet',
				'content'      => array(),
			),
			'middle_center__tablet' => array(
				'title'        => 'Middle Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'tablet',
				'content'      => array(),
			),
			'middle_right__tablet'  => array(
				'title'        => 'Middle Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'tablet',
				'content'      => array(),
			),
			'bottom_left__tablet'   => array(
				'title'        => 'Bottom Left',
				'has_settings' => true,
				'layout'       => 'one-thirds clear-item',
				'extra_class'  => 'tablet',
				'content'      => array(),
			),
			'bottom_center__tablet' => array(
				'title'        => 'Bottom Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'tablet',
				'content'      => array(),
			),
			'bottom_right__tablet'  => array(
				'title'        => 'Bottom Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'tablet',
				'content'      => array(),
			),

			/// mobile
			'all_item__mobile'      => array(
				'title'       => 'All Item',
				'layout'      => 'all',
				'extra_class' => 'mobile',
				'content'     => array(
					'logo'           => array(
						'title'        => 'Logo',
						'has_settings' => true,
					),
					'menu'           => array(
						'title'        => 'Menu',
						'has_settings' => true,
					),
					'search'         => array(
						'title'        => 'Search',
						'has_settings' => false,
					),
					'login'          => array(
						'title'        => 'Login',
						'has_settings' => false,
					),
					'cart'           => array(
						'title'        => 'Cart',
						'has_settings' => false,
					),
					'burger_sidebar' => array(
						'title'        => 'Burger Sidebar',
						'has_settings' => true,
					),
					'text1'          => array(
						'title'        => 'Text/HTML 1',
						'has_settings' => true,
					),
					'text2'          => array(
						'title'        => 'Text/HTML 2',
						'has_settings' => true,
					),

					'text3' => array(
						'title'        => 'Text/HTML 3',
						'has_settings' => true,
					),
					'text4' => array(
						'title'        => 'Text/HTML 4',
						'has_settings' => true,
					),

					'text5'        => array(
						'title'        => 'Text/HTML 5',
						'has_settings' => true,
					),
					'text6'        => array(
						'title'        => 'Text/HTML 6',
						'has_settings' => true,
					),
					'delimiter1'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter2'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter3'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter4'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter5'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter6'   => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'empty_space1' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space2' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space3' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space4' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
					'empty_space5' => array(
						'title'        => '&#8592;&#8594;',
						'has_settings' => false,
					),
				),
			),
			'top_left__mobile'      => array(
				'title'        => 'Top Left',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'mobile',
				'content'      => array(),
			),
			'top_center__mobile'    => array(
				'title'        => 'Top Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'mobile',
				'content'      => array(),
			),
			'top_right__mobile'     => array(
				'title'        => 'Top Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'mobile',
				'content'      => array(),
			),
			'middle_left__mobile'   => array(
				'title'        => 'Middle Left',
				'has_settings' => true,
				'layout'       => 'one-thirds clear-item',
				'extra_class'  => 'mobile',
				'content'      => array(),
			),
			'middle_center__mobile' => array(
				'title'        => 'Middle Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'mobile',
				'content'      => array(),
			),
			'middle_right__mobile'  => array(
				'title'        => 'Middle Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'mobile',
				'content'      => array(),
			),
			'bottom_left__mobile'   => array(
				'title'        => 'Bottom Left',
				'has_settings' => true,
				'layout'       => 'one-thirds clear-item',
				'extra_class'  => 'mobile',
				'content'      => array(),
			),
			'bottom_center__mobile' => array(
				'title'        => 'Bottom Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'mobile',
				'content'      => array(),
			),
			'bottom_right__mobile'  => array(
				'title'        => 'Bottom Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'extra_class'  => 'mobile',
				'content'      => array(),
			),
		),
		'default'          => array(
			'all_item'      => array(
				'title'   => 'All Item',
				'layout'  => 'all',
				'content' => array(
					'search'         => array(
						'title'        => 'Search',
						'has_settings' => false,
					),
					'login'          => array(
						'title'        => 'Login',
						'has_settings' => false,
					),
					'cart'           => array(
						'title'        => 'Cart',
						'has_settings' => false,
					),
					'burger_sidebar' => array(
						'title'        => 'Burger Sidebar',
						'has_settings' => true,
					),
					'text1'          => array(
						'title'        => 'Text/HTML 1',
						'has_settings' => true,
					),
					'text2'          => array(
						'title'        => 'Text/HTML 2',
						'has_settings' => true,
					),

					'text3' => array(
						'title'        => 'Text/HTML 3',
						'has_settings' => true,
					),
					'text4' => array(
						'title'        => 'Text/HTML 4',
						'has_settings' => true,
					),

					'text5'      => array(
						'title'        => 'Text/HTML 5',
						'has_settings' => true,
					),
					'text6'      => array(
						'title'        => 'Text/HTML 6',
						'has_settings' => true,
					),
					'delimiter1' => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter2' => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter3' => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter4' => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter5' => array(
						'title'        => '|',
						'has_settings' => true,
					),
					'delimiter6' => array(
						'title'        => '|',
						'has_settings' => true,
					),
				),
			),
			'top_left'      => array(
				'title'        => 'Top Left',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'top_center'    => array(
				'title'        => 'Top Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'top_right'     => array(
				'title'        => 'Top Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'middle_left'   => array(
				'title'        => 'Middle Left',
				'has_settings' => true,
				'layout'       => 'one-thirds clear-item',
				'content'      => array(
					'logo' => array(
						'title'        => 'Logo',
						'has_settings' => true,
					),
				),
			),
			'middle_center' => array(
				'title'        => 'Middle Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'middle_right'  => array(
				'title'        => 'Middle Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(
					'menu' => array(
						'title'        => 'Menu',
						'has_settings' => true,
					),
				),
			),
			'bottom_left'   => array(
				'title'        => 'Bottom Left',
				'has_settings' => true,
				'layout'       => 'one-thirds clear-item',
				'content'      => array(),
			),
			'bottom_center' => array(
				'title'        => 'Bottom Center',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
			'bottom_right'  => array(
				'title'        => 'Bottom Right',
				'has_settings' => true,
				'layout'       => 'one-thirds',
				'content'      => array(),
			),
		),
		'default'          => $def_header_option,
	),

	//HEADER TEMPLATES
	// MAIN HEADER SETTINGS
	array(
		'id'           => 'header_templates-start',
		'type'         => 'gt3_section',
		'title'        => esc_html__( 'Header Templates', 'agrosector' ),
		'indent'       => false,
		'section_role' => 'start'
	),

	//HEADER TEMPLATES
	array(
		'id'         => 'gt3_header_builder_presets',
		'type'       => 'gt3_presets',
		'presets'    => true,
		'full_width' => true,
		'title'      => esc_html__( 'Gt3 Preset', 'agrosector' ),
		'subtitle'   => esc_html__( 'This allows you to set default header layout.', 'agrosector' ),
		'default'    => array(
			'0' => array(
				'title'  => esc_html__( 'Default', 'agrosector' ),
				'preset' => json_encode( array( 'gt3_header_builder_id' => $def_header_option ) )
			),
		),
		'templates'  => array(
			'1' => array(
				'alt'     => 'Header 1',
				'img'     => get_template_directory_uri() . '/core/admin/img/header_1.jpg',
				'presets' => $header_preset_1
			),
		),
		'options'    => array(),
	),
	array(
		'id'           => 'header_templates-end',
		'type'         => 'gt3_section',
		'indent'       => false,
		'section_role' => 'end'
	),

	//NO ITEM SETTINGS
	array(
		'id'           => 'no_item-start',
		'type'         => 'gt3_section',
		'title'        => esc_html__( 'Header Settings', 'agrosector' ),
		'indent'       => false,
		'section_role' => 'start'
	),

	array(
		'id'    => 'no_item_message',
		'type'  => 'info',
		'style' => 'warning',
		'title' => esc_html__( 'Warning!', 'agrosector' ),
		'icon'  => 'el-icon-info-sign',
		'desc'  => esc_html__( 'To modify the settings, please add any item to the header section. It can not be empty.', 'agrosector' )
	),
	array(
		'id'           => 'no_item-end',
		'type'         => 'gt3_section',
		'indent'       => false,
		'section_role' => 'end'
	),


	//LOGO SETTINGS
	array(
		'id'           => 'logo-start',
		'type'         => 'gt3_section',
		'title'        => esc_html__( 'Logo Settings', 'agrosector' ),
		'indent'       => false,
		'section_role' => 'start'
	),
	array(
		'id'    => 'header_logo',
		'type'  => 'media',
		'title' => esc_html__( 'Header Logo', 'agrosector' ),
	),
	array(
		'id'      => 'logo_height_custom',
		'type'    => 'switch',
		'title'   => esc_html__( 'Enable Logo Height', 'agrosector' ),
		'default' => true,
	),
	array(
		'id'             => 'logo_height',
		'type'           => 'dimensions',
		'units'          => false,
		'units_extended' => false,
		'title'          => esc_html__( 'Set Logo Height', 'agrosector' ),
		'height'         => true,
		'width'          => false,
		'default'        => array(
			'height' => 46,
		),
		'required'       => array( 'logo_height_custom', '=', '1' ),
	),
	array(
		'id'       => 'logo_max_height',
		'type'     => 'switch',
		'title'    => esc_html__( 'Don\'t limit maximum height', 'agrosector' ),
		'default'  => false,
		'required' => array( 'logo_height_custom', '=', '1' ),
	),
	array(
		'id'             => 'sticky_logo_height',
		'type'           => 'dimensions',
		'units'          => false,
		'units_extended' => false,
		'title'          => __( 'Set Sticky Logo Height', 'agrosector' ),
		'height'         => true,
		'width'          => false,
		'default'        => array(
			'height' => '',
		),
		'required'       => array(
			array( 'logo_height_custom', '=', '1' ),
			array( 'logo_max_height', '=', '1' ),
		),
	),
	array(
		'id'    => 'logo_sticky',
		'type'  => 'media',
		'title' => __( 'Sticky Logo', 'agrosector' ),
	),

	array(
		'id'    => 'logo_tablet',
		'type'  => 'media',
		'title' => __( 'Tablet Logo', 'agrosector' ),
	),
	array(
		'id'             => 'logo_teblet_width',
		'type'           => 'dimensions',
		'units'          => false,
		'units_extended' => false,
		'title'          => __( 'Set Logo Width on Tablet', 'agrosector' ),
		'height'         => false,
		'width'          => true,
	),

	array(
		'id'    => 'logo_mobile',
		'type'  => 'media',
		'title' => __( 'Mobile Logo', 'agrosector' ),
	),
	array(
		'id'             => 'logo_mobile_width',
		'type'           => 'dimensions',
		'units'          => false,
		'units_extended' => false,
		'title'          => __( 'Set Logo Width on Mobile', 'agrosector' ),
		'height'         => false,
		'width'          => true,
	),


	array(
		'id'           => 'logo-end',
		'type'         => 'gt3_section',
		'indent'       => false,
		'section_role' => 'end'
	),

	// MENU
	array(
		'id'           => 'menu-start',
		'type'         => 'gt3_section',
		'title'        => __( 'Menu Settings', 'agrosector' ),
		'indent'       => false,
		'section_role' => 'start'
	),
	array(
		'id'      => 'menu_select',
		'type'    => 'select',
		'title'   => esc_html__( 'Select Menu', 'agrosector' ),
		'options' => gt3_getMenuList(),
		'default' => '',
	),
	array(
		'id'      => 'menu_active_top_line',
		'type'    => 'switch',
		'title'   => esc_html__( 'Enable Active Menu Item Marker', 'agrosector' ),
		'default' => false,
	),
	array(
		'id'       => 'sub_menu_background',
		'type'     => 'color_rgba',
		'title'    => __( 'Sub Menu Background', 'agrosector' ),
		'subtitle' => __( 'Set the background color for sub menu', 'agrosector' ),
		'default'  => array(
			'color' => '#ffffff',
			'alpha' => '1',
			'rgba'  => 'rgba(255,255,255,1)'
		),
		'mode'     => 'background',
	),
	array(
		'id'          => 'sub_menu_color',
		'type'        => 'color',
		'title'       => __( 'Sub Menu Text Color', 'agrosector' ),
		'subtitle'    => __( 'Set the header text color for menu', 'agrosector' ),
		'default'     => '#858585',
		'transparent' => false,
	),
	array(
		'id'          => 'sub_menu_color_hover',
		'type'        => 'color',
		'title'       => __( 'Sub Menu Text Color on Hover and Current', 'agrosector' ),
		'subtitle'    => __( 'Set the header text color for menu on hover and Current menu', 'agrosector' ),
		'default'     => '#b2b74a',
		'transparent' => false,
	),
	array(
		'id'           => 'menu-end',
		'type'         => 'gt3_section',
		'indent'       => true,
		'section_role' => 'end'

	),

	// BURGER SIDEBAR
	array(
		'id'           => 'burger_sidebar-start',
		'type'         => 'gt3_section',
		'title'        => __( 'Burger Sidebar Settings', 'agrosector' ),
		'indent'       => false,
		'section_role' => 'start'
	),
	array(
		'id'    => 'burger_sidebar_select',
		'type'  => 'select',
		'title' => esc_html__( 'Select Sidebar', 'agrosector' ),
		'data'  => 'sidebars',
	),
	array(
		'id'           => 'burger_sidebar-end',
		'type'         => 'gt3_section',
		'indent'       => false,
		'section_role' => 'end'
	),

);


$responsive_sections = array( '', '__tablet', '__mobile' );

$sections = array(
	'top_left'      => esc_html__( 'Top Left Settings', 'agrosector' ),
	'top_center'    => esc_html__( 'Top Center Settings', 'agrosector' ),
	'top_right'     => esc_html__( 'Top Right Settings', 'agrosector' ),
	'middle_left'   => esc_html__( 'Middle Left Settings', 'agrosector' ),
	'middle_center' => esc_html__( 'Middle Center Settings', 'agrosector' ),
	'middle_right'  => esc_html__( 'Middle Right Settings', 'agrosector' ),
	'bottom_left'   => esc_html__( 'Bottom Left Settings', 'agrosector' ),
	'bottom_center' => esc_html__( 'Bottom Center Settings', 'agrosector' ),
	'bottom_right'  => esc_html__( 'Bottom Right Settings', 'agrosector' ),
);

$responsive_tabs = array(
	'desktop' => esc_html__( 'Desktop', 'agrosector' ),
	'tablet'  => esc_html__( 'Tablet', 'agrosector' ),
	'mobile'  => esc_html__( 'Mobile', 'agrosector' ),
);

$header_global_settings = array();
foreach ( $responsive_tabs as $responsive_tab => $responsive_tab_translate ) {
	array_push( $header_global_settings,
		array(
			'id'           => $responsive_tab . '_header_settings-start',
			'type'         => 'gt3_section',
			'title'        => $responsive_tab_translate . esc_html__( ' Settings', 'agrosector' ),
			'indent'       => false,
			'section_role' => 'start'
		)
	);

	if ( $responsive_tab == 'desktop' ) {
		array_push( $header_global_settings,
			array(
				'id'       => 'header_full_width',
				'type'     => 'switch',
				'title'    => esc_html__( 'Full Width Header', 'agrosector' ),
				'subtitle' => esc_html__( 'Set header content in full width layout', 'agrosector' ),
				'default'  => false,
			),
			array(
				'id'      => 'header_on_bg',
				'type'    => 'switch',
				'title'   => esc_html__( 'Header Above Content', 'agrosector' ),
				'default' => false,
			),
			array(
				'id'      => 'header_sticky',
				'type'    => 'switch',
				'title'   => esc_html__( 'Sticky Header', 'agrosector' ),
				'default' => false,
			)
		);
	} else {
		array_push( $header_global_settings,
			array(
				'id'      => $responsive_tab . '_header_on_bg',
				'type'    => 'switch',
				'title'   => esc_html__( 'Header Above Content', 'agrosector' ),
				'default' => false,
			),
			array(
				'id'       => $responsive_tab . '_header_sticky',
				'type'     => 'switch',
				'title'    => esc_html__( 'Sticky Header', 'agrosector' ),
				'default'  => false,
				'required' => array( 'header_sticky', '=', '1' ),
			)
		);
	}

	if ( $responsive_tab == 'desktop' ) {
		array_push( $header_global_settings,
			array(
				'id'       => 'header_sticky_appearance_style',
				'type'     => 'select',
				'title'    => esc_html__( 'Sticky Appearance Style', 'agrosector' ),
				'options'  => array(
					'classic'    => esc_html__( 'Classic', 'agrosector' ),
					'scroll_top' => esc_html__( 'Appearance only on scroll top', 'agrosector' ),
				),
				'required' => array( 'header_sticky', '=', '1' ),
				'default'  => 'classic'
			),
			array(
				'id'       => 'header_sticky_appearance_from_top',
				'type'     => 'select',
				'title'    => esc_html__( 'Sticky Header Appearance From Top of Page', 'agrosector' ),
				'options'  => array(
					'auto'   => esc_html__( 'Auto', 'agrosector' ),
					'custom' => esc_html__( 'Custom', 'agrosector' ),
				),
				'required' => array( 'header_sticky', '=', '1' ),
				'default'  => 'auto'
			),
			array(
				'id'             => 'header_sticky_appearance_number',
				'type'           => 'dimensions',
				'units'          => false,
				'units_extended' => false,
				'title'          => __( 'Set the distance from the top of the page', 'agrosector' ),
				'height'         => true,
				'width'          => false,
				'default'        => array(
					'height' => 300,
				),
				'required'       => array( 'header_sticky_appearance_from_top', '=', 'custom' ),
			),
			array(
				'id'       => 'header_sticky_shadow',
				'type'     => 'switch',
				'title'    => esc_html__( 'Sticky Header Bottom Shadow', 'agrosector' ),
				'default'  => true,
				'required' => array( 'header_sticky', '=', '1' ),
			)
		);
	}

	array_push( $header_global_settings,
		array(
			'id'           => $responsive_tab . '_header_settings-end',
			'type'         => 'gt3_section',
			'indent'       => false,
			'section_role' => 'end'
		)
	);
}


// add align options to each section
$aligns = array();
foreach ( $responsive_sections as $responsive_section ) {
	foreach ( $sections as $section => $section_translate ) {
		$default = explode( "_", $section );
		array_push( $aligns,
			array(
				'id'           => $section . $responsive_section . '-start',
				'type'         => 'gt3_section',
				'title'        => $section_translate,
				'indent'       => false,
				'section_role' => 'start'
			),
			array(
				'id'      => $section . $responsive_section . '-align',
				'type'    => 'select',
				'title'   => esc_html__( 'Item Align', 'agrosector' ),
				'options' => array(
					'left'   => esc_html__( 'Left', 'agrosector' ),
					'center' => esc_html__( 'Center', 'agrosector' ),
					'right'  => esc_html__( 'Right', 'agrosector' ),
				),
				'default' => ! empty( $default[1] ) ? $default[1] : 'left',
			),
			array(
				'id'           => $section . $responsive_section . '-end',
				'type'         => 'gt3_section',
				'indent'       => false,
				'section_role' => 'end'
			)
		);
	}
}


$side_opt = array();
$sides    = array(
	'top'    => esc_html__( 'Top Header Settings', 'agrosector' ),
	'middle' => esc_html__( 'Middle Header Settings', 'agrosector' ),
	'bottom' => esc_html__( 'Bottom Header Settings', 'agrosector' ),
);
foreach ( $responsive_sections as $responsive_section ) {
	foreach ( $sides as $side => $section_translate ) {

		$background_color = $background_color2 = $border_color = array();

		$color       = '';
		$color_hover = '';
		$height      = '';

		if ( empty( $responsive_section ) ) {
			$background_color  = array(
				'color' => '#ffffff',
				'alpha' => '1',
				'rgba'  => 'rgba(255,255,255,1)'
			);
			$background_color2 = array(
				'color' => '#ffffff',
				'alpha' => '0',
				'rgba'  => 'rgba(255,255,255,0)'
			);
			$color             = '#232325';
			$color_hover       = '#232325';
			$height            = '100';
			$border_color      = array(
				'color' => '#F3F4F4',
				'alpha' => '1',
				'rgba'  => 'rgba(243,244,244,1)'
			);

		}

		array_push( $side_opt,
			//TOP SIDE
			array(
				'id'           => 'side_' . $side . $responsive_section . '-start',
				'type'         => 'gt3_section',
				'title'        => $section_translate,
				'indent'       => false,
				'section_role' => 'start'
			)
		);

		if ( ! empty( $responsive_section ) ) {
			array_push( $side_opt,
				//TOP SIDE
				array(
					'id'      => 'side_' . $side . $responsive_section . '_custom',
					'type'    => 'switch',
					'title'   => esc_html__( 'Customize ', 'agrosector' ) . $section_translate,
					'default' => false,
				),
				array(
					'id'       => 'side_' . $side . $responsive_section . '_styling-start',
					'type'     => 'section',
					'title'    => esc_html__( 'Customize ', 'agrosector' ) . $section_translate,
					'indent'   => true,
					'required' => array( 'side_' . $side . $responsive_section . '_custom', '=', '1' ),
				)
			);
		}

		array_push( $side_opt,
			array(
				'id'       => 'side_' . $side . $responsive_section . '_background',
				'type'     => 'color_rgba',
				'title'    => esc_html__( 'Background', 'agrosector' ),
				'subtitle' => esc_html__( 'Set background color', 'agrosector' ),
				'default'  => $background_color,
				'mode'     => 'background',
			),
			array(
				'id'       => 'side_' . $side . $responsive_section . '_background2',
				'type'     => 'color_rgba',
				'title'    => __( 'Inner Background', 'agrosector' ),
				'subtitle' => __( 'Set background color', 'agrosector' ),
				'default'  => $background_color2,
				'mode'     => 'background',
				'required' => array( 'header_full_width', '!=', '1' ),
			),
			array(
				'id'          => 'side_' . $side . $responsive_section . '_color',
				'type'        => 'color',
				'title'       => esc_html__( 'Text Color', 'agrosector' ),
				'subtitle'    => esc_html__( 'Set text color', 'agrosector' ),
				'default'     => $color,
				'transparent' => false,
			),
			array(
				'id'          => 'side_' . $side . $responsive_section . '_color_hover',
				'type'        => 'color',
				'title'       => esc_html__( 'Link Text Color on Hover', 'agrosector' ),
				'subtitle'    => esc_html__( 'Set text color', 'agrosector' ),
				'default'     => $color_hover,
				'transparent' => false,
			),
			array(
				'id'             => 'side_' . $side . $responsive_section . '_height',
				'type'           => 'dimensions',
				'units'          => false,
				'units_extended' => false,
				'title'          => esc_html__( 'Height', 'agrosector' ),
				'height'         => true,
				'width'          => false,
				'default'        => array(
					'height' => $height,
				)
			),
			array(
				'id'       => 'side_' . $side . $responsive_section . '_spacing',
				'type'     => 'spacing',
				// An array of CSS selectors to apply this font style to
				'mode'     => 'padding',
				'units'    => 'px',
				'all'      => false,
				'bottom'   => false,
				'top'      => false,
				'left'     => true,
				'right'    => true,
				'title'    => esc_html__( 'Padding (px)', 'agrosector' ),
				'subtitle' => esc_html__( 'Set empty padding-left/-right to default 20px', 'agrosector' ),
				'default'  => array(
					'padding-left'  => '',
					'padding-right' => '',

				)
			),

			array(
				'id'      => 'side_' . $side . $responsive_section . '_border',
				'type'    => 'switch',
				'title'   => esc_html__( 'Set Bottom Border', 'agrosector' ),
				'default' => false,
			),
			array(
				'id'       => 'side_' . $side . $responsive_section . '_border_color',
				'type'     => 'color_rgba',
				'title'    => esc_html__( 'Border Color', 'agrosector' ),
				'subtitle' => esc_html__( 'Set border color', 'agrosector' ),
				'default'  => $border_color,
				'mode'     => 'background',
				'required' => array( 'side_' . $side . $responsive_section . '_border', '=', '1' ),
			),
			array(
				'id'      => 'side_' . $side . $responsive_section . '_border_radius',
				'type'    => 'switch',
				'title'   => esc_html__( 'Set Border Radius', 'agrosector' ),
				'default' => false,
			)
		);

		if ( empty( $responsive_section ) ) {
			array_push( $side_opt,
				array(
					'id'       => 'side_' . $side . $responsive_section . '_sticky',
					'type'     => 'switch',
					'title'    => esc_html__( 'Show Section in Sticky Header?', 'agrosector' ),
					'default'  => true,
					'required' => array( 'header_sticky', '=', '1' ),
				),
				array(
					'id'       => 'side_' . $side . $responsive_section . '_background_sticky',
					'type'     => 'color_rgba',
					'title'    => esc_html__( 'Sticky Header Background', 'agrosector' ),
					'subtitle' => esc_html__( 'Set background color', 'agrosector' ),
					'default'  => array(
						'color' => '#ffffff',
						'alpha' => '1',
						'rgba'  => 'rgba(255,255,255,1)'
					),
					'mode'     => 'background',
					'required' => array( 'side_' . $side . $responsive_section . '_sticky', '=', '1' ),
				),
				array(
					'id'          => 'side_' . $side . $responsive_section . '_color_sticky',
					'type'        => 'color',
					'title'       => esc_html__( 'Sticky Header Text Color', 'agrosector' ),
					'subtitle'    => esc_html__( 'Set text color', 'agrosector' ),
					'default'     => '#222328',
					'transparent' => false,
					'required'    => array( 'side_' . $side . $responsive_section . '_sticky', '=', '1' ),
				),
				array(
					'id'          => 'side_' . $side . $responsive_section . '_color_hover_sticky',
					'type'        => 'color',
					'title'       => esc_html__( 'Sticky Header Link Color on Hover', 'agrosector' ),
					'subtitle'    => esc_html__( 'Set text color', 'agrosector' ),
					'default'     => $color_hover,
					'transparent' => false,
					'required'    => array( 'side_' . $side . $responsive_section . '_sticky', '=', '1' ),
				),
				array(
					'id'             => 'side_' . $side . $responsive_section . '_height_sticky',
					'type'           => 'dimensions',
					'units'          => false,
					'units_extended' => false,
					'title'          => esc_html__( 'Sticky Header Height', 'agrosector' ),
					'height'         => true,
					'width'          => false,
					'default'        => array(
						'height' => 58,
					),
					'required'       => array( 'side_' . $side . $responsive_section . '_sticky', '=', '1' ),
				),
				array(
					'id'       => 'side_' . $side . $responsive_section . '_spacing_sticky',
					'type'     => 'spacing',
					// An array of CSS selectors to apply this font style to
					'mode'     => 'padding',
					'units'    => 'px',
					'all'      => false,
					'bottom'   => false,
					'top'      => false,
					'left'     => true,
					'right'    => true,
					'title'    => esc_html__( 'Sticky Header Padding (px)', 'agrosector' ),
					'subtitle' => esc_html__( 'Set empty padding-left/-right to default 20px', 'agrosector' ),
					'default'  => array(
						'padding-left'  => '',
						'padding-right' => '',
					),
					'required' => array( 'side_' . $side . $responsive_section . '_sticky', '=', '1' ),
				)
			);
		} else {
			$header_sticky_prefix = str_replace( '__', '', $responsive_section );
			array_push( $side_opt,
				array(
					'id'       => 'side_' . $side . $responsive_section . '_sticky',
					'type'     => 'switch',
					'title'    => esc_html__( 'Show Section in Sticky Header?', 'agrosector' ),
					'default'  => true,
					'required' => array( 'header_sticky', '=', '1' ),
				)
			);
		}

		if ( ! empty( $responsive_section ) ) {
			array_push( $side_opt,
				array(
					'id'       => 'side_' . $side . $responsive_section . '_styling-end',
					'type'     => 'section',
					'indent'   => false,
					'required' => array( 'side_' . $side . $responsive_section . '_custom', '=', '1' ),
				)
			);
		}

		array_push( $side_opt,
			array(
				'id'           => 'side_' . $side . $responsive_section . '-end',
				'type'         => 'gt3_section',
				'indent'       => false,
				'section_role' => 'end'
			)
		);

	}
}


// text editor
$text_editor_count = 6;
$text_opt          = array();
for ( $i = 1; $i <= $text_editor_count; $i ++ ) {
	array_push( $text_opt,
		array(
			'id'           => 'text' . $i . '-start',
			'type'         => 'gt3_section',
			'title'        => esc_html__( 'Text / HTML', 'agrosector' ) . ' ' . $i . ' ' . esc_html__( 'Settings', 'agrosector' ),
			'indent'       => false,
			'section_role' => 'start'
		),
		array(
			'id'      => 'text' . $i . '_editor',
			'type'    => 'editor',
			'title'   => esc_html__( 'Text Editor', 'agrosector' ),
			'default' => '',
			'args'    => array(
				'wpautop'       => false,
				'media_buttons' => false,
				'textarea_rows' => 8,
				'teeny'         => false,
				'quicktags'     => true,
			),
		),
		array(
			'id'           => 'text' . $i . '-end',
			'type'         => 'gt3_section',
			'indent'       => false,
			'section_role' => 'end'
		)
	);
};


// delimiter
$delimiter_count = 6;
$delimiter_opt   = array();
for ( $i = 1; $i <= $delimiter_count; $i ++ ) {
	array_push( $delimiter_opt,
		// Delimiters
		array(
			'id'           => 'delimiter' . $i . '-start',
			'type'         => 'gt3_section',
			'title'        => esc_html__( 'Delimiter', 'agrosector' ) . ' ' . $i . ' ' . esc_html__( 'Settings', 'agrosector' ),
			'indent'       => false,
			'section_role' => 'start'
		),
		array(
			'id'      => 'delimiter' . $i . '_height',
			'type'    => 'dimensions',
			'units'   => array( 'em', 'px', '%' ),
			'title'   => esc_html__( 'Delimiter Height', 'agrosector' ),
			'height'  => true,
			'width'   => false,
			'output'  => array( 'height' => '.gt3_delimiter' . $i . '' ),
			'default' => array(
				'height' => 1,
				'units'  => 'em',
			)
		),
		array(
			'id'           => 'delimiter' . $i . '-end',
			'type'         => 'gt3_section',
			'indent'       => false,
			'section_role' => 'end'
		)
	);
};

$options = array_merge( $options, $aligns, $text_opt, $delimiter_opt, $side_opt, $header_global_settings );

Redux::setSection( $opt_name, array(
	'id'     => 'gt3_header_builder_section',
	'title'  => __( 'GT3 Header Builder', 'agrosector' ),
	'icon'   => 'el el-screen',
	'fields' => $options
) );
// END HEADER

Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Page Title', 'agrosector' ),
	'id'               => 'page_title',
	'icon'             => 'el-icon-screen',
	'customizer_width' => '450px',
	'fields'           => array(
		array(
			'id'      => 'page_title_conditional',
			'type'    => 'switch',
			'title'   => esc_html__( 'Show Page Title', 'agrosector' ),
			'default' => true,
		),
		array(
			'id'       => 'blog_title_conditional',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show Blog Post Title', 'agrosector' ),
			'default'  => true,
			'required' => array( 'page_title_conditional', '=', '1' ),
		),
		array(
			'id'       => 'blog_title_prev_next',
			'class'    => 'gt3_child',
			'type'     => 'switch',
			'title'    => ' ',
			'subtitle' => esc_html__( 'Show Prev/Next Links', 'agrosector' ),
			'default'  => false,
			'required' => array(
				array( 'page_title_conditional', '=', '1' ),
				array( 'blog_title_conditional', '=', '1' ),
			),
		),
		array(
			'id'       => 'team_title_conditional',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show Team Post Title', 'agrosector' ),
			'default'  => false,
			'required' => array( 'page_title_conditional', '=', '1' ),
		),
		array(
			'id'       => 'portfolio_title_conditional',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show Portfolio Post Title', 'agrosector' ),
			'default'  => true,
			'required' => array( 'page_title_conditional', '=', '1' ),
		),
		array(
			'id'       => 'portfolio_title_prev_next',
			'class'    => 'gt3_child',
			'type'     => 'switch',
			'title'    => ' ',
			'subtitle' => esc_html__( 'Show Prev/Next Links', 'agrosector' ),
			'default'  => false,
			'required' => array(
				array( 'page_title_conditional', '=', '1' ),
				array( 'portfolio_title_conditional', '=', '1' ),
			),
		),
		array(
			'id'       => 'project_title_conditional',
			'type'     => 'switch',
			'title'    => esc_html__( 'Show Project Post Title', 'agrosector' ),
			'default'  => true,
			'required' => array( 'page_title_conditional', '=', '1' ),
		),
		array(
			'id'       => 'project_title_prev_next',
			'class'    => 'gt3_child',
			'type'     => 'switch',
			'title'    => ' ',
			'subtitle' => esc_html__( 'Show Prev/Next Links', 'agrosector' ),
			'default'  => false,
			'required' => array(
				array( 'page_title_conditional', '=', '1' ),
				array( 'project_title_conditional', '=', '1' ),
			),
		),
		array(
			'id'       => 'page_title-start',
			'type'     => 'section',
			'title'    => esc_html__( 'Page Title Settings', 'agrosector' ),
			'indent'   => true,
			'required' => array( 'page_title_conditional', '=', '1' ),
		),
		array(
			'id'      => 'page_title_breadcrumbs_conditional',
			'type'    => 'switch',
			'title'   => esc_html__( 'Show Breadcrumbs', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'      => 'page_title_vert_align',
			'type'    => 'select',
			'title'   => esc_html__( 'Vertical Align', 'agrosector' ),
			'options' => array(
				'top'    => esc_html__( 'Top', 'agrosector' ),
				'middle' => esc_html__( 'Middle', 'agrosector' ),
				'bottom' => esc_html__( 'Bottom', 'agrosector' )
			),
			'default' => 'middle'
		),
		array(
			'id'      => 'page_title_horiz_align',
			'type'    => 'select',
			'title'   => esc_html__( 'Page Title Text Align?', 'agrosector' ),
			'options' => array(
				'left'   => esc_html__( 'Left', 'agrosector' ),
				'center' => esc_html__( 'Center', 'agrosector' ),
				'right'  => esc_html__( 'Right', 'agrosector' )
			),
			'default' => 'left'
		),
		array(
			'id'          => 'page_title_font_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Page Title Font Color', 'agrosector' ),
			'default'     => '#413c38',
			'transparent' => false
		),
		array(
			'id'          => 'page_title_bg_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Page Title Background Color', 'agrosector' ),
			'default'     => '#ffffff',
			'transparent' => false
		),
		array(
			'id'    => 'page_title_bg_image',
			'type'  => 'media',
			'title' => esc_html__( 'Page Title Background Image', 'agrosector' ),
		),
		array(
			'id'               => 'page_title_bg_image',
			'type'             => 'background',
			'background-color' => false,
			'preview_media'    => true,
			'preview'          => false,
			'title'            => esc_html__( 'Page Title Background Image', 'agrosector' ),
			'default'          => array(
				'background-repeat'     => 'no-repeat',
				'background-size'       => 'cover',
				'background-attachment' => 'scroll',
				'background-position'   => 'center center',
				'background-color'      => '#e0e2e3',
			)
		),
		array(
			'id'             => 'page_title_height',
			'type'           => 'dimensions',
			'units'          => false,
			'units_extended' => false,
			'title'          => esc_html__( 'Page Title Height', 'agrosector' ),
			'height'         => true,
			'width'          => false,
			'default'        => array(
				'height' => 345,
			)
		),
		array(
			'id'      => 'page_title_top_border',
			'type'    => 'switch',
			'title'   => esc_html__( 'Page Title Top Border', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'       => 'page_title_top_border_color',
			'type'     => 'color_rgba',
			'title'    => esc_html__( 'Page Title Top Border Color', 'agrosector' ),
			'default'  => array(
				'color' => '#f3f4f4',
				'alpha' => '1',
				'rgba'  => 'rgba(243,244,244,1)'
			),
			'mode'     => 'background',
			'required' => array(
				array( 'page_title_top_border', '=', '1' ),
			),
		),
		array(
			'id'      => 'page_title_bottom_border',
			'type'    => 'switch',
			'title'   => esc_html__( 'Page Title Bottom Border', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'       => 'page_title_bottom_border_color',
			'type'     => 'color_rgba',
			'title'    => esc_html__( 'Page Title Bottom Border Color', 'agrosector' ),
			'default'  => array(
				'color' => '#eff0ed',
				'alpha' => '1',
				'rgba'  => 'rgba(239,240,237,1)'
			),
			'mode'     => 'background',
			'required' => array(
				array( 'page_title_bottom_border', '=', '1' ),
			),
		),
		array(
			'id'      => 'page_title_bottom_margin',
			'type'    => 'spacing',
			// An array of CSS selectors to apply this font style to
			'mode'    => 'margin',
			'all'     => false,
			'bottom'  => true,
			'top'     => false,
			'left'    => false,
			'right'   => false,
			'title'   => esc_html__( 'Page Title Bottom Margin', 'agrosector' ),
			'default' => array(
				'margin-bottom' => '90',
			)
		),

		array(
			'id'      => 'page_title_svg_line',
			'type'    => 'select',
			'title'   => esc_html__( 'Page Title SVG Line', 'agrosector' ),
			'options' => array(
				'svg_none'        => esc_html__( 'None', 'agrosector' ),
				'svg_line_top'    => esc_html__( 'SVG Line Top', 'agrosector' ),
				'svg_line_bottom' => esc_html__( 'SVG Line Bottom', 'agrosector' ),
				'svg_line_both'   => esc_html__( 'SVG Line Top and Bottom', 'agrosector' )
			),
			'default' => 'svg_line_both'
		),
		array(
			'id'       => 'page_title_svg_line_top_color',
			'type'     => 'color_rgba',
			'class'    => 'gt3_child',
			'title'    => ' ',
			'subtitle' => esc_html__( 'SVG Top Color', 'agrosector' ),
			'default'  => array(
				'color' => '#ffffff',
				'alpha' => '1',
				'rgba'  => 'rgba(255,255,255,1)'
			),
			'mode'     => 'background',
			'required' => array(
				array( 'page_title_svg_line', '=', array( 'svg_line_top', 'svg_line_both' ) ),
			),
		),
		array(
			'id'       => 'page_title_svg_line_bottom_color',
			'type'     => 'color_rgba',
			'class'    => 'gt3_child',
			'title'    => ' ',
			'subtitle' => esc_html__( 'SVG Bottom Color', 'agrosector' ),
			'default'  => array(
				'color' => '#ffffff',
				'alpha' => '1',
				'rgba'  => 'rgba(255,255,255,1)'
			),
			'mode'     => 'background',
			'required' => array(
				array( 'page_title_svg_line', '=', array( 'svg_line_bottom', 'svg_line_both' ) ),
			),
		),
		array(
			'id'       => 'page_title-end',
			'type'     => 'section',
			'indent'   => false,
			'required' => array( 'page_title_conditional', '=', '1' ),
		),
		array(
			'id'       => 'page_title-end',
			'type'     => 'section',
			'indent'   => false,
			'required' => array( 'page_title_conditional', '=', '1' ),
		),

	)
) );

// -> START Footer Options
Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Footer', 'agrosector' ),
	'id'               => 'footer-option',
	'customizer_width' => '400px',
	'icon'             => 'el-icon-screen',
	'fields'           => array(
		array(
			'id'      => 'footer_full_width',
			'type'    => 'select',
			'title'   => esc_html__( 'Full Width Footer', 'agrosector' ),
			'options' => array(
				'default'        => esc_html__( 'Default', 'agrosector' ),
				'stretch_footer'  => esc_html__( 'Stretch Footer', 'agrosector' ),
				'stretch_content' => esc_html__( 'Stretch Footer and Content', 'agrosector' ),
			),
			'default' => 'stretch_footer',
		),
		array(
			'id'          => 'footer_bg_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Footer Background Color', 'agrosector' ),
			'default'     => '#262b2b',
			'transparent' => false
		),
		array(
			'id'          => 'footer_text_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Footer Text color', 'agrosector' ),
			'default'     => '#d1d1d1',
			'transparent' => false
		),
		array(
			'id'          => 'footer_heading_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Footer Heading color', 'agrosector' ),
			'default'     => '#ffffff',
			'transparent' => false
		),
		array(
			'id'               => 'footer_bg_image',
			'type'             => 'background',
			'background-color' => false,
			'preview_media'    => true,
			'preview'          => false,
			'title'            => esc_html__( 'Footer Background Image', 'agrosector' ),
			'default'          => array(
				'background-repeat'     => 'repeat',
				'background-size'       => 'cover',
				'background-attachment' => 'scroll',
				'background-position'   => 'center center',
				'background-color'      => '#323336',
			)
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Footer Content', 'agrosector' ),
	'id'               => 'footer_content',
	'subsection'       => true,
	'customizer_width' => '450px',
	'fields'           => array(
		array(
			'id'      => 'footer_switch',
			'type'    => 'switch',
			'title'   => esc_html__( 'Show Footer', 'agrosector' ),
			'default' => true,
		),
		array(
			'id'       => 'footer-start',
			'type'     => 'section',
			'title'    => esc_html__( 'Footer Settings', 'agrosector' ),
			'indent'   => true,
			'required' => array( 'footer_switch', '=', '1' ),
		),
		array(
			'id'      => 'footer_column',
			'type'    => 'select',
			'title'   => esc_html__( 'Footer Column', 'agrosector' ),
			'options' => array(
				'1'   => '1',
				'2'   => '2',
				'3'   => '3',
				'4'   => '4',
				'4.5' => esc_html__( '4 modified', 'agrosector' ),
				'5'   => '5'
			),
			'default' => '4'
		),
		array(
			'id'       => 'footer_column2',
			'type'     => 'select',
			'title'    => esc_html__( 'Footer Column Layout', 'agrosector' ),
			'options'  => array(
				'6-6' => '50% / 50%',
				'3-9' => '25% / 75%',
				'9-3' => '25% / 75%',
				'4-8' => '33% / 66%',
				'8-3' => '66% / 33%',
			),
			'default'  => '6-6',
			'required' => array( 'footer_column', '=', '2' ),
		),
		array(
			'id'       => 'footer_column3',
			'type'     => 'select',
			'title'    => esc_html__( 'Footer Column Layout', 'agrosector' ),
			'options'  => array(
				'4-4-4' => '33% / 33% / 33%',
				'3-3-6' => '25% / 25% / 50%',
				'3-6-3' => '25% / 50% / 25%',
				'6-3-3' => '50% / 25% / 25%',
			),
			'default'  => '4-4-4',
			'required' => array( 'footer_column', '=', '3' ),
		),
		array(
			'id'       => 'footer_column5',
			'type'     => 'select',
			'title'    => esc_html__( 'Footer Column Layout', 'agrosector' ),
			'options'  => array(
				'2-3-2-2-3' => '16% / 25% / 16% / 16% / 25%',
				'3-2-2-2-3' => '25% / 16% / 16% / 16% / 25%',
				'3-2-3-2-2' => '25% / 16% / 26% / 16% / 16%',
				'3-2-3-3-2' => '25% / 16% / 16% / 25% / 16%',
			),
			'default'  => '2-3-2-2-3',
			'required' => array( 'footer_column', '=', '5' ),
		),
		array(
			'id'      => 'footer_align',
			'type'    => 'select',
			'title'   => esc_html__( 'Footer Title Text Align', 'agrosector' ),
			'options' => array(
				'left'   => esc_html__( 'Left', 'agrosector' ),
				'center' => esc_html__( 'Center', 'agrosector' ),
				'right'  => esc_html__( 'Right', 'agrosector' ),
			),
			'default' => 'left'
		),
		array(
			'id'      => 'footer_spacing',
			'type'    => 'spacing',
			// An array of CSS selectors to apply this font style to
			'mode'    => 'padding',
			'all'     => false,
			'title'   => esc_html__( 'Footer Padding (px)', 'agrosector' ),
			'default' => array(
				'padding-top'    => '70',
				'padding-right'  => '0',
				'padding-bottom' => '66',
				'padding-left'   => '0'
			)
		),
		array(
			'id'       => 'footer-end',
			'type'     => 'section',
			'indent'   => false,
			'required' => array( 'footer_switch', '=', '1' ),
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Copyright', 'agrosector' ),
	'id'               => 'copyright',
	'subsection'       => true,
	'customizer_width' => '450px',
	'fields'           => array(
		array(
			'id'      => 'copyright_switch',
			'type'    => 'switch',
			'title'   => esc_html__( 'Show Copyright', 'agrosector' ),
			'default' => true,
		),
		array(
			'id'       => 'copyright_editor',
			'type'     => 'editor',
			'title'    => esc_html__( 'Copyright Editor', 'agrosector' ),
			'default'  => '',
			'args'     => array(
				'wpautop'       => false,
				'media_buttons' => false,
				'textarea_rows' => 15,
				'teeny'         => false,
				'quicktags'     => true,
			),
			'required' => array( 'copyright_switch', '=', '1' ),
		),
		array(
			'id'       => 'copyright_align',
			'type'     => 'select',
			'title'    => esc_html__( 'Copyright Title Text Align', 'agrosector' ),
			'options'  => array(
				'left'   => esc_html__( 'Left', 'agrosector' ),
				'center' => esc_html__( 'Center', 'agrosector' ),
				'right'  => esc_html__( 'Right', 'agrosector' ),
			),
			'default'  => 'center',
			'required' => array( 'copyright_switch', '=', '1' ),
		),
		array(
			'id'       => 'copyright_spacing',
			'type'     => 'spacing',
			'mode'     => 'padding',
			'all'      => false,
			'title'    => esc_html__( 'Copyright Padding (px)', 'agrosector' ),
			'default'  => array(
				'padding-top'    => '8',
				'padding-right'  => '0',
				'padding-bottom' => '8',
				'padding-left'   => '0'
			),
			'required' => array( 'copyright_switch', '=', '1' ),
		),
		array(
			'id'          => 'copyright_bg_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Copyright Background Color', 'agrosector' ),
			'default'     => 'transparent',
			'transparent' => false,
			'required'    => array( 'copyright_switch', '=', '1' ),
		),
		array(
			'id'          => 'copyright_text_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Copyright Text Color', 'agrosector' ),
			'default'     => '#d1d1d1',
			'transparent' => false,
			'required'    => array( 'copyright_switch', '=', '1' ),
		),
		array(
			'id'       => 'copyright_top_border',
			'type'     => 'switch',
			'title'    => esc_html__( 'Set Copyright Top Border', 'agrosector' ),
			'default'  => false,
			'required' => array( 'copyright_switch', '=', '1' ),
		),
		array(
			'id'       => 'copyright_top_border_color',
			'type'     => 'color_rgba',
			'title'    => esc_html__( 'Copyright Border Color', 'agrosector' ),
			'default'  => array(
				'color' => '#858585',
				'alpha' => '.2',
				'rgba'  => 'rgba(133, 133, 133, 0.2)'
			),
			'mode'     => 'background',
			'required' => array(
				array( 'copyright_top_border', '=', '1' ),
				array( 'copyright_switch', '=', '1' )
			),
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Prefooter Area', 'agrosector' ),
	'id'               => 'pre_footer',
	'subsection'       => true,
	'customizer_width' => '450px',
	'fields'           => array(
		array(
			'id'      => 'pre_footer_switch',
			'type'    => 'switch',
			'title'   => esc_html__( 'Show Prefooter Area', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'       => 'pre_footer_editor',
			'type'     => 'editor',
			'title'    => esc_html__( 'Prefooter Editor', 'agrosector' ),
			'default'  => '',
			'args'     => array(
				'wpautop'       => false,
				'media_buttons' => false,
				'textarea_rows' => 2,
				'teeny'         => false,
				'quicktags'     => true,
			),
			'required' => array( 'pre_footer_switch', '=', '1' ),
		),
		array(
			'id'       => 'pre_footer_align',
			'type'     => 'select',
			'title'    => esc_html__( 'Prefooter Title Text Align', 'agrosector' ),
			'options'  => array(
				'left'   => esc_html__( 'Left', 'agrosector' ),
				'center' => esc_html__( 'Center', 'agrosector' ),
				'right'  => esc_html__( 'Right', 'agrosector' ),
			),
			'default'  => 'left',
			'required' => array( 'pre_footer_switch', '=', '1' ),
		),
		array(
			'id'       => 'pre_footer_spacing',
			'type'     => 'spacing',
			'mode'     => 'padding',
			'all'      => false,
			'title'    => esc_html__( 'Prefooter Area Padding (px)', 'agrosector' ),
			'default'  => array(
				'padding-top'    => '20',
				'padding-right'  => '0',
				'padding-bottom' => '20',
				'padding-left'   => '0'
			),
			'required' => array( 'pre_footer_switch', '=', '1' ),
		),
		array(
			'id'       => 'pre_footer_bottom_border',
			'type'     => 'switch',
			'title'    => esc_html__( 'Set Prefooter Border', 'agrosector' ),
			'default'  => false,
			'required' => array( 'pre_footer_switch', '=', '1' ),
		),
		array(
			'id'       => 'pre_footer_bottom_border_color',
			'type'     => 'color_rgba',
			'title'    => esc_html__( 'Prefooter Border Color', 'agrosector' ),
			'default'  => array(
				'color' => '#e0e1dc',
				'alpha' => '1',
				'rgba'  => 'rgba(224,225,220,1)'
			),
			'mode'     => 'background',
			'required' => array(
				array( 'pre_footer_bottom_border', '=', '1' ),
				array( 'pre_footer_switch', '=', '1' )
			),
		),
	)
) );

// -> START Blog Options
Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Blog', 'agrosector' ),
	'id'               => 'blog-option',
	'customizer_width' => '400px',
	'icon'             => 'el-icon-th-list',
	'fields'           => array(
		array(
			'id'      => 'related_posts',
			'type'    => 'switch',
			'title'   => esc_html__( 'Related Posts', 'agrosector' ),
			'default' => true,
		),
		array(
			'id'      => 'author_box',
			'type'    => 'switch',
			'title'   => esc_html__( 'Author Box on Single Post', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'      => 'post_comments',
			'type'    => 'switch',
			'title'   => esc_html__( 'Post Comments', 'agrosector' ),
			'default' => true,
		),
		array(
			'id'      => 'post_pingbacks',
			'type'    => 'switch',
			'title'   => esc_html__( 'Trackbacks and Pingbacks', 'agrosector' ),
			'default' => true,
		),
		array(
			'id'      => 'blog_post_likes',
			'type'    => 'switch',
			'title'   => esc_html__( 'Likes on Posts', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'      => 'blog_post_share',
			'type'    => 'switch',
			'title'   => esc_html__( 'Share on Posts', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'      => 'blog_post_listing_content',
			'type'    => 'switch',
			'title'   => esc_html__( 'Cut Off Text in Blog Listing', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'      => 'blog_post_fimage_animation',
			'type'    => 'switch',
			'title'   => esc_html__( 'Add animation to Featured Image in Blog Listing', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'      => 'bottom_prev_next',
			'type'    => 'switch',
			'title'   => esc_html__( 'Show Prev/Next Links on bottom', 'agrosector' ),
			'default' => false,
		),
	)
) );

// -> START Gallery Options
if ( class_exists( '\ElementorModal\Widgets\GT3_Elementor_Gallery' ) ) {
	Redux::setSection( $opt_name, array(
		'title'            => esc_html__( 'Gallery', 'agrosector' ),
		'id'               => 'gallery-option',
		'customizer_width' => '400px',
		'icon'             => 'el el-picture',
		'fields'           => array(
			array(
				'id'      => 'gallery_type',
				'type'    => 'select',
				'title'   => esc_html__( 'Select default gallery type', 'agrosector' ),
				'options' => array(
					'grid'         => esc_html__( 'Grid Gallery', 'agrosector' ),
					'packery'      => esc_html__( 'Packery Gallery', 'agrosector' ),
					'fs_slider'    => esc_html__( 'FullScreen Slider', 'agrosector' ),
					'shift_slider' => esc_html__( 'Shift Slider', 'agrosector' ),
					'masonry'      => esc_html__( 'Masonry Gallery', 'agrosector' ),
					'kenburn'      => esc_html__( 'Kenburns', 'agrosector' ),
					'ribbon'       => esc_html__( 'Ribbon Slider', 'agrosector' ),
					'flow'         => esc_html__( 'Flow Slider', 'agrosector' ),
				),
				'default' => 'grid'
			),
			// Grid
			array(
				'id'       => 'grid_grid_type',
				'type'     => 'select',
				'title'    => esc_html__( 'Grid Type', 'agrosector' ),
				'options'  => array(
					'vertical'  => esc_html__( 'Vertical Align', 'agrosector' ),
					'square'    => esc_html__( 'Square', 'agrosector' ),
					'rectangle' => esc_html__( 'Rectangle', 'agrosector' ),
				),
				'default'  => 'vertical',
				'required' => array( 'gallery_type', '=', 'grid' ),
			),
			array(
				'id'       => 'grid_cols',
				'type'     => 'select',
				'title'    => esc_html__( 'Cols', 'agrosector' ),
				'options'  => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'default'  => '4',
				'required' => array( 'gallery_type', '=', 'grid' ),
			),
			array(
				'id'       => 'grid_grid_gap',
				'type'     => 'select',
				'title'    => esc_html__( 'Grid Gap', 'agrosector' ),
				'options'  => array(
					'0'    => '0',
					'1px'  => '1px',
					'2px'  => '2px',
					'3px'  => '3px',
					'4px'  => '4px',
					'5px'  => '5px',
					'10px' => '10px',
					'15px' => '15px',
					'20px' => '20px',
					'25px' => '25px',
					'30px' => '30px',
					'35px' => '35px',

					'2%'    => '2%',
					'4.95%' => '5%',
					'8%'    => '8%',
					'10%'   => '10%',
					'12%'   => '12%',
					'15%'   => '15%',
				),
				'default'  => '30px',
				'required' => array( 'gallery_type', '=', 'grid' ),
			),
			array(
				'id'       => 'grid_hover',
				'type'     => 'select',
				'title'    => esc_html__( 'Hover Effect', 'agrosector' ),
				'options'  => array(
					'type1' => esc_html__( 'Type 1', 'agrosector' ),
					'type2' => esc_html__( 'Type 2', 'agrosector' ),
					'type3' => esc_html__( 'Type 3', 'agrosector' ),
					'type4' => esc_html__( 'Type 4', 'agrosector' ),
					'type5' => esc_html__( 'Type 5', 'agrosector' ),
				),
				'default'  => 'type2',
				'required' => array( 'gallery_type', '=', 'grid' ),
			),

			array(
				'id'       => 'grid_lightbox',
				'type'     => 'switch',
				'title'    => esc_html__( 'Lightbox', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'grid' ),
			),
			array(
				'id'       => 'grid_show_title',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show Title', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'grid' ),
			),
			array(
				'id'            => 'grid_post_per_load',
				'type'          => 'slider',
				'title'         => esc_html__( 'Post Per Load', 'agrosector' ),
				'default'       => 12,
				'min'           => 1,
				'step'          => 1,
				'max'           => 100,
				'display_value' => 'text',
				'required'      => array( 'gallery_type', '=', 'grid' ),
			),
			array(
				'id'       => 'grid_show_view_all',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show "See More" Button', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'grid' ),
			),
			array(
				'id'            => 'grid_load_items',
				'type'          => 'slider',
				'title'         => esc_html__( 'See Items', 'agrosector' ),
				'default'       => 4,
				'min'           => 1,
				'step'          => 1,
				'max'           => 100,
				'display_value' => 'text',
				'required'      => array(
					array( 'gallery_type', '=', 'grid' ),
					array( 'grid_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'grid_button_type',
				'type'     => 'select',
				'title'    => esc_html__( 'Button Type', 'agrosector' ),
				'options'  => array(
					'none'    => esc_html__( 'None', 'agrosector' ),
					'default' => esc_html__( 'Default', 'agrosector' ),
				),
				'default'  => 'default',
				'required' => array(
					array( 'gallery_type', '=', 'grid' ),
					array( 'grid_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'grid_button_border',
				'type'     => 'switch',
				'title'    => esc_html__( 'Button Border', 'agrosector' ),
				'default'  => true,
				'required' => array(
					array( 'gallery_type', '=', 'grid' ),
					array( 'grid_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'grid_button_title',
				'type'     => 'text',
				'title'    => esc_html__( 'Button Title', 'agrosector' ),
				'default'  => esc_html__( 'Load More', 'agrosector' ),
				'required' => array(
					array( 'gallery_type', '=', 'grid' ),
					array( 'grid_show_view_all', '=', '1' ),
				),
			),


			// Packery
			array(
				'id'       => 'packery_type',
				'type'     => 'image_select',
				'title'    => esc_html__( 'Type', 'agrosector' ),
				'options'  => array(
					'1' => array(
						'alt' => esc_html__( 'Type 1', 'agrosector' ),
						'img' => esc_url( \ElementorModal\Widgets\GT3_Elementor_Gallery::$IMAGE_URL . 'type1.png' )
					),
					'2' => array(
						'alt' => esc_html__( 'Type 2', 'agrosector' ),
						'img' => esc_url( \ElementorModal\Widgets\GT3_Elementor_Gallery::$IMAGE_URL . 'type2.png' )
					),
					'3' => array(
						'alt' => esc_html__( 'Type 3', 'agrosector' ),
						'img' => esc_url( \ElementorModal\Widgets\GT3_Elementor_Gallery::$IMAGE_URL . 'type3.png' )
					),
					'4' => array(
						'alt' => esc_html__( 'Type 4', 'agrosector' ),
						'img' => esc_url( \ElementorModal\Widgets\GT3_Elementor_Gallery::$IMAGE_URL . 'type4.png' )
					),
				),
				'default'  => '2',
				'required' => array( 'gallery_type', '=', 'packery' ),
			),
			array(
				'id'       => 'packery_grid_gap',
				'type'     => 'select',
				'title'    => esc_html__( 'Packery Gap', 'agrosector' ),
				'options'  => array(
					'0'    => '0',
					'1px'  => '1px',
					'2px'  => '2px',
					'3px'  => '3px',
					'4px'  => '4px',
					'5px'  => '5px',
					'10px' => '10px',
					'15px' => '15px',
					'20px' => '20px',
					'25px' => '25px',
					'30px' => '30px',
					'35px' => '35px',

					'2%'    => '2%',
					'4.95%' => '5%',
					'8%'    => '8%',
					'10%'   => '10%',
					'12%'   => '12%',
					'15%'   => '15%',
				),
				'default'  => '30px',
				'required' => array( 'gallery_type', '=', 'packery' ),
			),
			array(
				'id'       => 'packery_hover',
				'type'     => 'select',
				'title'    => esc_html__( 'Hover Effect', 'agrosector' ),
				'options'  => array(
					'type1' => esc_html__( 'Type 1', 'agrosector' ),
					'type2' => esc_html__( 'Type 2', 'agrosector' ),
					'type3' => esc_html__( 'Type 3', 'agrosector' ),
					'type4' => esc_html__( 'Type 4', 'agrosector' ),
					'type5' => esc_html__( 'Type 5', 'agrosector' ),
				),
				'default'  => 'type2',
				'required' => array( 'gallery_type', '=', 'packery' ),
			),
			array(
				'id'       => 'packery_lightbox',
				'type'     => 'switch',
				'title'    => esc_html__( 'Lightbox', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'packery' ),
			),
			array(
				'id'       => 'packery_show_title',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show Title', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'packery' ),
			),
			array(
				'id'            => 'packery_post_per_load',
				'type'          => 'slider',
				'title'         => esc_html__( 'Post Per Load', 'agrosector' ),
				'default'       => 12,
				'min'           => 1,
				'step'          => 1,
				'max'           => 100,
				'display_value' => 'text',
				'required'      => array( 'gallery_type', '=', 'packery' ),
			),
			array(
				'id'       => 'packery_show_view_all',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show "See More" Button', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'packery' ),
			),
			array(
				'id'            => 'packery_load_items',
				'type'          => 'slider',
				'title'         => esc_html__( 'See Items', 'agrosector' ),
				'default'       => 4,
				'min'           => 1,
				'step'          => 1,
				'max'           => 100,
				'display_value' => 'text',
				'required'      => array(
					array( 'gallery_type', '=', 'packery' ),
					array( 'packery_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'packery_button_type',
				'type'     => 'select',
				'title'    => esc_html__( 'Button Type', 'agrosector' ),
				'options'  => array(
					'none'    => esc_html__( 'None', 'agrosector' ),
					'default' => esc_html__( 'Default', 'agrosector' ),
				),
				'default'  => 'default',
				'required' => array(
					array( 'gallery_type', '=', 'packery' ),
					array( 'packery_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'packery_button_border',
				'type'     => 'switch',
				'title'    => esc_html__( 'Button Border', 'agrosector' ),
				'default'  => true,
				'required' => array(
					array( 'gallery_type', '=', 'packery' ),
					array( 'packery_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'packery_button_border',
				'type'     => 'switch',
				'title'    => esc_html__( 'Button Border', 'agrosector' ),
				'default'  => true,
				'required' => array(
					array( 'gallery_type', '=', 'packery' ),
					array( 'packery_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'packery_button_title',
				'type'     => 'text',
				'title'    => esc_html__( 'Button Title', 'agrosector' ),
				'default'  => esc_html__( 'Load More', 'agrosector' ),
				'required' => array(
					array( 'gallery_type', '=', 'packery' ),
					array( 'packery_show_view_all', '=', '1' ),
				),
			),
			// FS Slider
			array(
				'id'       => 'fs_controls',
				'type'     => 'switch',
				'title'    => esc_html__( 'Controls', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'fs_slider' ),
			),
			array(
				'id'       => 'fs_autoplay',
				'type'     => 'switch',
				'title'    => esc_html__( 'Autoplay', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'fs_slider' ),
			),
			array(
				'id'       => 'fs_thumbs',
				'type'     => 'switch',
				'title'    => esc_html__( 'Thumbnails', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'fs_slider' ),
			),
			array(
				'id'       => 'fs_interval',
				'type'     => 'text',
				'validate' => 'numeric',
				'title'    => esc_html__( 'Slide Duration', 'agrosector' ),
				'default'  => '6000',
				'required' => array( 'gallery_type', '=', 'fs_slider' ),
			),
			array(
				'id'       => 'fs_transition_time',
				'type'     => 'text',
				'validate' => 'numeric',
				'title'    => esc_html__( 'Transition Interval', 'agrosector' ),
				'default'  => '1000',
				'required' => array( 'gallery_type', '=', 'fs_slider' ),
			),
			array(
				'id'          => 'fs_panel_color',
				'type'        => 'color',
				'title'       => esc_html__( 'Panel Color', 'agrosector' ),
				'transparent' => false,
				'default'     => '#fff',
				'validate'    => 'color',
				'required'    => array( 'gallery_type', '=', 'fs_slider' ),
			),
			array(
				'id'       => 'fs_anim_style',
				'type'     => 'select',
				'title'    => esc_html__( 'Anim style', 'agrosector' ),
				'options'  => array(
					'fade'      => esc_html__( 'Fade', 'agrosector' ),
					'slip'      => esc_html__( 'Slip', 'agrosector' ),
					'slip_up'   => esc_html__( 'Slip Up', 'agrosector' ),
					'slip_down' => esc_html__( 'Slip Down', 'agrosector' ),
				),
				'default'  => 'fade',
				'required' => array( 'gallery_type', '=', 'fs_slider' ),
			),
			array(
				'id'       => 'fs_fit_style',
				'type'     => 'select',
				'title'    => esc_html__( 'Fit Style', 'agrosector' ),
				'options'  => array(
					'no_fit'     => __( 'Cover Slide', 'agrosector' ),
					'fit_always' => __( 'Fit Always', 'agrosector' ),
					'fit_width'  => __( 'Fit Horizontal', 'agrosector' ),
					'fit_height' => __( 'Fit Vertical', 'agrosector' ),
				),
				'default'  => 'no_fit',
				'required' => array( 'gallery_type', '=', 'fs_slider' ),
			),
			array(
				'id'          => 'fs_module_height',
				'type'        => 'text',
				'title'       => esc_html__( 'Module Height', 'agrosector' ),
				'description' => esc_html__( 'Set module height in px (pixels). Enter \'100%\' for full height mode', 'agrosector' ),
				'default'     => '100%',
				'required'    => array( 'gallery_type', '=', 'fs_slider' ),
			),

			// Shift
			array(
				'id'          => 'shift_controls',
				'type'        => 'switch',
				'title'       => esc_html__( 'Show Control Buttons', 'agrosector' ),
				'description' => esc_html__( 'Turn ON or OFF control buttons', 'agrosector' ),
				'default'     => true,
				'required'    => array( 'gallery_type', '=', 'shift_slider' ),
			),
			array(
				'id'          => 'shift_infinity_scroll',
				'type'        => 'switch',
				'title'       => esc_html__( 'Infinite Scroll', 'agrosector' ),
				'default'     => true,
				'required'    => array( 'gallery_type', '=', 'shift_slider' ),
				'description' => esc_html__( 'Turn ON or OFF infinite  scrolling. Autoplay works only when infinite scroll is ON', 'agrosector' ),
			),
			array(
				'id'          => 'shift_autoplay',
				'type'        => 'switch',
				'title'       => esc_html__( 'Autoplay', 'agrosector' ),
				'description' => esc_html__( 'Turn ON or OFF slider autoplay', 'agrosector' ),
				'default'     => true,
				'required'    => array(
					array( 'gallery_type', '=', 'shift_slider' ),
					array( 'shift_infinity_scroll', '=', '1' ),
				),
			),
			array(
				'id'          => 'shift_interval',
				'type'        => 'text',
				'validate'    => 'numeric',
				'title'       => esc_html__( 'Slide Duration', 'agrosector' ),
				'description' => esc_html__( 'Set the timing of single slides in milliseconds', 'agrosector' ),
				'default'     => '6000',
				'required'    => array(
					array( 'gallery_type', '=', 'shift_slider' ),
					array( 'shift_infinity_scroll', '=', '1' ),
					array( 'shift_autoplay', '=', '1' ),
				),
			),
			array(
				'id'          => 'shift_transition_time',
				'type'        => 'text',
				'validate'    => 'numeric',
				'title'       => esc_html__( 'Transition Interval', 'agrosector' ),
				'description' => esc_html__( 'Set transition animation time', 'agrosector' ),
				'default'     => '600',
				'required'    => array( 'gallery_type', '=', 'shift_slider' ),
			),
			array(
				'id'       => 'shift_descr_type',
				'type'     => 'select',
				'title'    => esc_html__( 'Show Title', 'agrosector' ),
				'options'  => array(
					'always'   => esc_html__( 'Always Show', 'agrosector' ),
					'hide'     => esc_html__( 'Always Hide', 'agrosector' ),
					'on_hover' => esc_html__( 'Show on Hover', 'agrosector' ),
					'expanded' => esc_html__( 'Show when slide is expanded', 'agrosector' ),
				),
				'default'  => 'on_hover',
				'required' => array( 'gallery_type', '=', 'shift_slider' ),
			),
			array(
				'id'          => 'shift_expandeble',
				'type'        => 'switch',
				'title'       => esc_html__( 'Expandable slides', 'agrosector' ),
				'description' => esc_html__( 'Turn ON or OFF expandable slides', 'agrosector' ),
				'required'    => array( 'gallery_type', '=', 'shift_slider' ),
			),
			array(
				'id'          => 'shift_hover_effect',
				'type'        => 'switch',
				'title'       => esc_html__( 'Hover Effect', 'agrosector' ),
				'default'     => true,
				'required'    => array( 'gallery_type', '=', 'shift_slider' ),
				'description' => esc_html__( 'Turn ON or OFF hover effect', 'agrosector' ),
			),
			array(
				'id'          => 'shift_module_height',
				'type'        => 'text',
				'title'       => esc_html__( 'Module Height', 'agrosector' ),
				'description' => esc_html__( 'Set module height in px (pixels). Enter \'100%\' for full height mode', 'agrosector' ),
				'default'     => '100%',
				'required'    => array( 'gallery_type', '=', 'shift_slider' ),
			),
			// Masonry
			array(
				'id'       => 'masonry_cols',
				'type'     => 'select',
				'title'    => esc_html__( 'Cols', 'agrosector' ),
				'options'  => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'default'  => '4',
				'required' => array( 'gallery_type', '=', 'masonry' ),
			),
			array(
				'id'       => 'masonry_grid_gap',
				'type'     => 'select',
				'title'    => esc_html__( 'Grid Gap', 'agrosector' ),
				'options'  => array(
					'0'    => '0',
					'1px'  => '1px',
					'2px'  => '2px',
					'3px'  => '3px',
					'4px'  => '4px',
					'5px'  => '5px',
					'10px' => '10px',
					'15px' => '15px',
					'20px' => '20px',
					'25px' => '25px',
					'30px' => '30px',
					'35px' => '35px',

					'2%'    => '2%',
					'4.95%' => '5%',
					'8%'    => '8%',
					'10%'   => '10%',
					'12%'   => '12%',
					'15%'   => '15%',
				),
				'default'  => '30px',
				'required' => array( 'gallery_type', '=', 'masonry' ),
			),
			array(
				'id'       => 'masonry_hover',
				'type'     => 'select',
				'title'    => esc_html__( 'Hover Effect', 'agrosector' ),
				'options'  => array(
					'type1' => esc_html__( 'Type 1', 'agrosector' ),
					'type2' => esc_html__( 'Type 2', 'agrosector' ),
					'type3' => esc_html__( 'Type 3', 'agrosector' ),
					'type4' => esc_html__( 'Type 4', 'agrosector' ),
					'type5' => esc_html__( 'Type 5', 'agrosector' ),
				),
				'default'  => 'type2',
				'required' => array( 'gallery_type', '=', 'masonry' ),
			),

			array(
				'id'       => 'masonry_lightbox',
				'type'     => 'switch',
				'title'    => esc_html__( 'Lightbox', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'masonry' ),
			),
			array(
				'id'       => 'masonry_show_title',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show Title', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'masonry' ),
			),
			array(
				'id'            => 'masonry_post_per_load',
				'type'          => 'slider',
				'title'         => esc_html__( 'Post Per Load', 'agrosector' ),
				'default'       => 12,
				'min'           => 1,
				'step'          => 1,
				'max'           => 100,
				'display_value' => 'text',
				'required'      => array( 'gallery_type', '=', 'masonry' ),
			),
			array(
				'id'       => 'masonry_show_view_all',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show "See More" Button', 'agrosector' ),
				'default'  => true,
				'required' => array( 'gallery_type', '=', 'masonry' ),
			),
			array(
				'id'            => 'masonry_load_items',
				'type'          => 'slider',
				'title'         => esc_html__( 'See Items', 'agrosector' ),
				'default'       => 4,
				'min'           => 1,
				'step'          => 1,
				'max'           => 100,
				'display_value' => 'text',
				'required'      => array(
					array( 'gallery_type', '=', 'masonry' ),
					array( 'masonry_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'masonry_button_type',
				'type'     => 'select',
				'title'    => esc_html__( 'Button Type', 'agrosector' ),
				'options'  => array(
					'none'    => esc_html__( 'None', 'agrosector' ),
					'default' => esc_html__( 'Default', 'agrosector' ),
				),
				'default'  => 'default',
				'required' => array(
					array( 'gallery_type', '=', 'masonry' ),
					array( 'masonry_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'masonry_button_border',
				'type'     => 'switch',
				'title'    => esc_html__( 'Button Border', 'agrosector' ),
				'default'  => true,
				'required' => array(
					array( 'gallery_type', '=', 'masonry' ),
					array( 'masonry_show_view_all', '=', '1' ),
				),
			),
			array(
				'id'       => 'masonry_button_title',
				'type'     => 'text',
				'title'    => esc_html__( 'Button Title', 'agrosector' ),
				'default'  => esc_html__( 'Load More', 'agrosector' ),
				'required' => array(
					array( 'gallery_type', '=', 'masonry' ),
					array( 'masonry_show_view_all', '=', '1' ),
				),
			),
			// Kenburns
			array(
				'id'          => 'kenburn_interval',
				'type'        => 'text',
				'validate'    => 'numeric',
				'title'       => esc_html__( 'Slide Duration', 'agrosector' ),
				'description' => esc_html__( 'Set the timing of single slides in milliseconds', 'agrosector' ),
				'default'     => '6000',
				'required'    => array(
					array( 'gallery_type', '=', 'kenburn' ),
				),
			),
			array(
				'id'          => 'kenburn_transition_time',
				'type'        => 'text',
				'validate'    => 'numeric',
				'title'       => esc_html__( 'Transition Interval', 'agrosector' ),
				'description' => esc_html__( 'Set transition animation time', 'agrosector' ),
				'default'     => '600',
				'required'    => array( 'gallery_type', '=', 'kenburn' ),
			),
			array(
				'id'          => 'kenburn_overlay_state',
				'type'        => 'switch',
				'title'       => esc_html__( 'Overlay', 'agrosector' ),
				'description' => esc_html__( 'Turn ON or OFF slides color overlay.', 'agrosector' ),
				'required'    => array( 'gallery_type', '=', 'kenburn' ),
			),
			array(
				'id'          => 'kenburn_overlay_bg',
				'type'        => 'color',
				'title'       => esc_html__( 'Panel Color', 'agrosector' ),
				'transparent' => false,
				'default'     => '#fff',
				'validate'    => 'color',
				'required'    => array(
					array( 'gallery_type', '=', 'kenburn' ),
					array( 'kenburn_overlay_state', '=', '1' ),
				),
			),
			array(
				'id'          => 'kenburn_module_height',
				'type'        => 'text',
				'title'       => esc_html__( 'Module Height', 'agrosector' ),
				'description' => esc_html__( 'Set module height in px (pixels). Enter \'100%\' for full height mode', 'agrosector' ),
				'default'     => '100%',
				'required'    => array( 'gallery_type', '=', 'kenburn' ),
			),
			// Ribbon
			array(
				'id'          => 'ribbon_show_title',
				'type'        => 'switch',
				'title'       => esc_html__( 'Show Title', 'agrosector' ),
				'description' => esc_html__( 'Turn ON or OFF titles and captions', 'agrosector' ),
				'default'     => true,
				'required'    => array( 'gallery_type', '=', 'ribbon' ),
			),
			array(
				'id'          => 'ribbon_descr',
				'type'        => 'switch',
				'title'       => esc_html__( 'Show Caption', 'agrosector' ),
				'description' => esc_html__( 'Turn ON or OFF captions', 'agrosector' ),
				'default'     => false,
				'required'    => array( 'gallery_type', '=', 'ribbon' ),
			),

			array(
				'id'            => 'ribbon_items_padding',
				'type'          => 'slider',
				'title'         => esc_html__( 'Paddings around the images', 'agrosector' ),
				'description'   => esc_html__( 'Please use this option to add paddings around the images. Recommended size in pixels 0-50. (Ex.: 15px)', 'agrosector' ),
				'default'       => 0,
				'min'           => 0,
				'step'          => 1,
				'max'           => 100,
				'display_value' => 'text',
				'required'      => array( 'gallery_type', '=', 'ribbon' ),
			),
			array(
				'id'       => 'ribbon_controls',
				'type'     => 'switch',
				'title'    => esc_html__( 'Controls', 'agrosector' ),
				'default'  => false,
				'required' => array( 'gallery_type', '=', 'ribbon' ),
			),
			array(
				'id'       => 'ribbon_autoplay',
				'type'     => 'switch',
				'title'    => esc_html__( 'Autoplay', 'agrosector' ),
				'default'  => false,
				'required' => array( 'gallery_type', '=', 'ribbon' ),
			),
			array(
				'id'          => 'ribbon_interval',
				'type'        => 'text',
				'validate'    => 'numeric',
				'title'       => esc_html__( 'Slide Duration', 'agrosector' ),
				'description' => esc_html__( 'Set the timing of single slides in milliseconds', 'agrosector' ),
				'default'     => '6000',
				'required'    => array(
					array( 'gallery_type', '=', 'ribbon' ),
					array( 'ribbon_autoplay', '=', '1' ),
				),
			),
			array(
				'id'          => 'ribbon_transition_time',
				'type'        => 'text',
				'validate'    => 'numeric',
				'title'       => esc_html__( 'Transition Interval', 'agrosector' ),
				'description' => esc_html__( 'Set transition animation time', 'agrosector' ),
				'default'     => '600',
				'required'    => array(
					array( 'gallery_type', '=', 'ribbon' ),
					array( 'ribbon_autoplay', '=', '1' ),
				),
			),
			array(
				'id'          => 'ribbon_module_height',
				'type'        => 'text',
				'title'       => esc_html__( 'Module Height', 'agrosector' ),
				'description' => esc_html__( 'Set module height in px (pixels). Enter \'100%\' for full height mode', 'agrosector' ),
				'default'     => '100%',
				'required'    => array( 'gallery_type', '=', 'ribbon' ),
			),
			// Flow
			array(
				'id'            => 'flow_img_width',
				'type'          => 'slider',
				'title'         => esc_html__( 'Image Width in px (pixels)', 'agrosector' ),
				'default'       => 1168,
				'min'           => 640,
				'step'          => 2,
				'max'           => 2560,
				'display_value' => 'text',
				'required'      => array( 'gallery_type', '=', 'flow' ),
			),
			array(
				'id'            => 'flow_img_height',
				'type'          => 'slider',
				'title'         => esc_html__( 'Image Height in px (pixels)', 'agrosector' ),
				'default'       => 820,
				'min'           => 480,
				'step'          => 2,
				'max'           => 1600,
				'display_value' => 'text',
				'required'      => array( 'gallery_type', '=', 'flow' ),
			),
			array(
				'id'          => 'flow_title_state',
				'type'        => 'switch',
				'title'       => esc_html__( 'Show Title', 'agrosector' ),
				'description' => esc_html__( 'Turn ON or OFF titles on slide', 'agrosector' ),
				'default'     => false,
				'required'    => array( 'gallery_type', '=', 'flow' ),
			),
			array(
				'id'       => 'flow_autoplay',
				'type'     => 'switch',
				'title'    => esc_html__( 'Autoplay', 'agrosector' ),
				'default'  => false,
				'required' => array( 'gallery_type', '=', 'flow' ),
			),
			array(
				'id'          => 'flow_interval',
				'type'        => 'text',
				'validate'    => 'numeric',
				'title'       => esc_html__( 'Slide Duration', 'agrosector' ),
				'description' => esc_html__( 'Set the timing of single slides in milliseconds', 'agrosector' ),
				'default'     => '6000',
				'required'    => array(
					array( 'gallery_type', '=', 'flow' ),
					array( 'flow_autoplay', '=', '1' ),
				),
			),
			array(
				'id'          => 'flow_transition_time',
				'type'        => 'text',
				'validate'    => 'numeric',
				'title'       => esc_html__( 'Transition Interval', 'agrosector' ),
				'description' => esc_html__( 'Set transition animation time', 'agrosector' ),
				'default'     => '600',
				'required'    => array(
					array( 'gallery_type', '=', 'flow' ),
					array( 'flow_autoplay', '=', '1' ),
				),
			),
			array(
				'id'          => 'flow_module_height',
				'type'        => 'text',
				'title'       => esc_html__( 'Module Height', 'agrosector' ),
				'description' => esc_html__( 'Set module height in px (pixels). Enter \'100%\' for full height mode', 'agrosector' ),
				'default'     => '100%',
				'required'    => array( 'gallery_type', '=', 'flow' ),
			),
		)
	) );
}

// -> START Layout Options
Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Sidebars', 'agrosector' ),
	'id'               => 'layout_options',
	'customizer_width' => '400px',
	'icon'             => 'el el-website',
	'fields'           => array(
		array(
			'id'      => 'page_sidebar_layout',
			'type'    => 'image_select',
			'title'   => esc_html__( 'Page Sidebar Layout', 'agrosector' ),
			'options' => array(
				'none'  => array(
					'alt' => 'None',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/1col.png'
				),
				'left'  => array(
					'alt' => 'Left',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cl.png'
				),
				'right' => array(
					'alt' => 'Right',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cr.png'
				)
			),
			'default' => 'right'
		),
		array(
			'id'       => 'page_sidebar_def',
			'type'     => 'select',
			'title'    => esc_html__( 'Page Sidebar', 'agrosector' ),
			'data'     => 'sidebars',
			'required' => array( 'page_sidebar_layout', '!=', 'none' ),
		),
		array(
			'id'      => 'blog_single_sidebar_layout',
			'type'    => 'image_select',
			'title'   => esc_html__( 'Blog Single Sidebar Layout', 'agrosector' ),
			'options' => array(
				'none'  => array(
					'alt' => 'None',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/1col.png'
				),
				'left'  => array(
					'alt' => 'Left',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cl.png'
				),
				'right' => array(
					'alt' => 'Right',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cr.png'
				)
			),
			'default' => 'none'
		),
		array(
			'id'       => 'blog_single_sidebar_def',
			'type'     => 'select',
			'title'    => esc_html__( 'Blog Single Sidebar', 'agrosector' ),
			'data'     => 'sidebars',
			'required' => array( 'blog_single_sidebar_layout', '!=', 'none' ),
		),
		array(
			'id'      => 'portfolio_single_sidebar_layout',
			'type'    => 'image_select',
			'title'   => esc_html__( 'Portfolio Single Sidebar Layout', 'agrosector' ),
			'options' => array(
				'none'  => array(
					'alt' => 'None',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/1col.png'
				),
				'left'  => array(
					'alt' => 'Left',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cl.png'
				),
				'right' => array(
					'alt' => 'Right',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cr.png'
				)
			),
			'default' => 'none'
		),
		array(
			'id'       => 'portfolio_single_sidebar_def',
			'type'     => 'select',
			'title'    => esc_html__( 'Portfolio Single Sidebar', 'agrosector' ),
			'data'     => 'sidebars',
			'required' => array( 'portfolio_single_sidebar_layout', '!=', 'none' ),
		),
		array(
			'id'      => 'project_single_sidebar_layout',
			'type'    => 'image_select',
			'title'   => esc_html__( 'Project Single Sidebar Layout', 'agrosector' ),
			'options' => array(
				'none'  => array(
					'alt' => 'None',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/1col.png'
				),
				'left'  => array(
					'alt' => 'Left',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cl.png'
				),
				'right' => array(
					'alt' => 'Right',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cr.png'
				)
			),
			'default' => 'none'
		),
		array(
			'id'       => 'project_single_sidebar_def',
			'type'     => 'select',
			'title'    => esc_html__( 'Project Single Sidebar', 'agrosector' ),
			'data'     => 'sidebars',
			'required' => array( 'project_single_sidebar_layout', '!=', 'none' ),
		),
		array(
			'id'      => 'team_single_sidebar_layout',
			'type'    => 'image_select',
			'title'   => esc_html__( 'Team Single Sidebar Layout', 'agrosector' ),
			'options' => array(
				'none'  => array(
					'alt' => 'None',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/1col.png'
				),
				'left'  => array(
					'alt' => 'Left',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cl.png'
				),
				'right' => array(
					'alt' => 'Right',
					'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cr.png'
				)
			),
			'default' => 'none'
		),
		array(
			'id'       => 'team_single_sidebar_def',
			'type'     => 'select',
			'title'    => esc_html__( 'Team Single Sidebar', 'agrosector' ),
			'data'     => 'sidebars',
			'required' => array( 'team_single_sidebar_layout', '!=', 'none' ),
		),
	)
) );

Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Sidebar Generator', 'agrosector' ),
	'id'               => 'sidebars_generator_section',
	'subsection'       => true,
	'customizer_width' => '450px',
	'fields'           => array(
		array(
			'id'       => 'sidebars',
			'type'     => 'multi_text',
			'validate' => 'no_html',
			'add_text' => esc_html__( 'Add Sidebar', 'agrosector' ),
			'title'    => esc_html__( 'Sidebar Generator', 'agrosector' ),
			'default'  => array(
				"Main Sidebar",
				"Menu Sidebar",
				"Shop Sidebar",
				"Footer Contacts White",
				"Footer Info White",
				"Cases"
			),
		),
	)
) );


// -> START Styling Options
Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Color Options', 'agrosector' ),
	'id'               => 'color_options',
	'customizer_width' => '400px',
	'icon'             => 'el-icon-brush'
) );

Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Colors', 'agrosector' ),
	'id'               => 'color_options_color',
	'subsection'       => true,
	'customizer_width' => '450px',
	'fields'           => array(
		array(
			'id'          => 'theme-custom-color',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color', 'agrosector' ),
			'transparent' => false,
			'default'     => '#b2b74a',
			'validate'    => 'color',
		),
		array(
			'id'          => 'body-background-color',
			'type'        => 'color',
			'title'       => esc_html__( 'Body Background Color', 'agrosector' ),
			'transparent' => false,
			'default'     => '#ffffff',
			'validate'    => 'color',
		),
	)
) );


Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Typography', 'agrosector' ),
	'id'               => 'typography_options',
	'customizer_width' => '400px',
	'icon'             => 'el-icon-font',
	'fields'           => array(
		array(
			'id'             => 'menu-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'Menu Font', 'agrosector' ),
			'google'         => true,
			'font-style'     => true,
			'color'          => false,
			'line-height'    => true,
			'font-size'      => true,
			'font-backup'    => false,
			'text-align'     => false,
			'letter-spacing' => true,
			'text-transform' => true,
			'default'        => array(
				'font-family'    => 'Roboto',
				'google'         => true,
				'font-size'      => '18px',
				'line-height'    => '24px',
				'font-weight'    => '400',
				'letter-spacing' => '',
				'text-transform' => '',
			),
		),

		array(
			'id'             => 'main-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'Main Font', 'agrosector' ),
			'google'         => true,
			'font-backup'    => false,
			'font-size'      => true,
			'line-height'    => true,
			'color'          => true,
			'word-spacing'   => false,
			'letter-spacing' => false,
			'text-align'     => false,
			'all_styles'     => true,
			'default'        => array(
				'font-size'   => '18px',
				'line-height' => '30px',
				'color'       => '#858585',
				'google'      => true,
				'font-family' => 'Roboto',
				'font-weight' => '400',
			),
		),
		array(
			'id'             => 'header-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'Headers Font', 'agrosector' ),
			'google'         => true,
			'font-backup'    => false,
			'font-size'      => false,
			'line-height'    => false,
			'color'          => true,
			'word-spacing'   => false,
			'letter-spacing' => false,
			'text-align'     => false,
			'text-transform' => false,
			'all_styles'     => true,
			'default'        => array(
				'color'       => '#413c38',
				'google'      => true,
				'font-family' => 'BenchNine',
				'font-weight' => '700',
			),
		),
		array(
			'id'             => 'h1-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'H1', 'agrosector' ),
			'google'         => true,
			'font-backup'    => false,
			'font-size'      => true,
			'line-height'    => true,
			'color'          => false,
			'word-spacing'   => false,
			'letter-spacing' => false,
			'text-align'     => false,
			'text-transform' => false,
			'default'        => array(
				'font-size'   => '36px',
				'line-height' => '50px',
				'google'      => true,
				'font-family' => 'BenchNine',
				'font-weight' => '700',
			),
		),
		array(
			'id'             => 'h2-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'H2', 'agrosector' ),
			'google'         => true,
			'font-backup'    => false,
			'font-size'      => true,
			'line-height'    => true,
			'color'          => false,
			'word-spacing'   => false,
			'letter-spacing' => false,
			'text-align'     => false,
			'text-transform' => false,
			'default'        => array(
				'font-size'   => '30px',
				'line-height' => '42px',
				'google'      => true,
				'font-family' => 'BenchNine',
				'font-weight' => '700',
			),
		),
		array(
			'id'             => 'h3-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'H3', 'agrosector' ),
			'google'         => true,
			'font-backup'    => false,
			'font-size'      => true,
			'line-height'    => true,
			'color'          => false,
			'word-spacing'   => false,
			'letter-spacing' => false,
			'text-align'     => false,
			'text-transform' => false,
			'default'        => array(
				'font-size'   => '24px',
				'line-height' => '34px',
				'google'      => true,
				'font-family' => 'BenchNine',
				'font-weight' => '700',
			),
		),
		array(
			'id'             => 'h4-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'H4', 'agrosector' ),
			'google'         => true,
			'font-backup'    => false,
			'font-size'      => true,
			'line-height'    => true,
			'color'          => false,
			'word-spacing'   => false,
			'letter-spacing' => false,
			'text-align'     => false,
			'text-transform' => false,
			'default'        => array(
				'font-size'   => '20px',
				'line-height' => '30px',
				'google'      => true,
				'font-family' => 'BenchNine',
				'font-weight' => '700',
			),
		),
		array(
			'id'             => 'h5-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'H5', 'agrosector' ),
			'google'         => true,
			'font-backup'    => false,
			'font-size'      => true,
			'line-height'    => true,
			'color'          => false,
			'word-spacing'   => false,
			'letter-spacing' => false,
			'text-align'     => false,
			'text-transform' => false,
			'default'        => array(
				'font-size'   => '18px',
				'line-height' => '27px',
				'google'      => true,
				'font-family' => 'BenchNine',
				'font-weight' => '700',
			),
		),
		array(
			'id'             => 'h6-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'H6', 'agrosector' ),
			'google'         => true,
			'font-backup'    => false,
			'font-size'      => true,
			'line-height'    => true,
			'color'          => false,
			'word-spacing'   => false,
			'letter-spacing' => false,
			'text-align'     => false,
			'text-transform' => false,
			'default'        => array(
				'font-size'   => '16px',
				'line-height' => '25px',
				'google'      => true,
				'font-family' => 'BenchNine',
				'font-weight' => '700',
			),
		),
		array(
			'type' => 'custom_font',
			'id' => 'custom_font',
			'validate'=>'font_load',
			'title' => esc_html__('Font-face list:', 'agrosector'),
			'subtitle' => esc_html__('Upload .zip archive with font-face files.', 'agrosector').'<br>(<a target="_blank" href="http://www.fontsquirrel.com/tools/webfont-generator">'.esc_html__('Create your font-face package','agrosector').'</a>)',
			'desc' => '<span style="color:#F09191">'.esc_html__('Note','agrosector').':</span> '.esc_html__('You have to download the font-face.zip archive.','agrosector').' <br>'.__('Pay your attention, that the archive has to contain the font-face files itself, and not the subfolders','agrosector').'<br> ('.esc_html__('E.g.: font-face.zip/your-font-face.ttf, font-face.zip/your-font-face.eot, font-face.zip/your-font-face.woff etc','qudos').' ).<br> '.esc_html__('They\'ll be extracted and assigned automatically.', 'agrosector').' ).<br> '.esc_html__('Please check the instruction how to create', 'agrosector').' '.'.',
			'placeholder' => array (
				'title' => esc_html__('This is a title', 'agrosector'),
				'description' => esc_html__('Description Here', 'agrosector'),
				'url' => esc_html__('Give us a link!', 'agrosector'),
			),
		),

	)
) );

Redux::setSection( $opt_name, array(
	'title'            => esc_html__( 'Google Map', 'agrosector' ),
	'id'               => 'google_map',
	'customizer_width' => '400px',
	'icon'             => 'el el-map-marker',
	'fields'           => array(
		array(
			'id'      => 'map_prefooter_default',
			'type'    => 'switch',
			'title'   => esc_html__( 'Enable Map Output in the Prefooter Area?', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'      => 'google_map_api_key',
			'type'    => 'text',
			'title'   => esc_html__( 'Google Map API Key', 'agrosector' ),
			'desc'    => esc_html__( 'Create own API key', 'agrosector' )
			             . ' <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#--api" target="_blank">'
			             . esc_html__( 'here', 'agrosector' )
			             . '</a>',
			'default' => '',
		),
		array(
			'id'      => 'google_map_latitude',
			'type'    => 'text',
			'title'   => esc_html__( 'Map Latitude Coordinate', 'agrosector' ),
			'default' => '-37.8172507',
		),
		array(
			'id'      => 'google_map_longitude',
			'type'    => 'text',
			'title'   => esc_html__( 'Map Longitude Coordinate', 'agrosector' ),
			'default' => '144.9535833',
		),
		array(
			'id'      => 'zoom_map',
			'type'    => 'select',
			'title'   => esc_html__( 'Default Zoom Map', 'agrosector' ),
			'desc'    => esc_html__( 'Select the number of zoom map.', 'agrosector' ),
			'options' => array(
				'10' => esc_html__( '10', 'agrosector' ),
				'11' => esc_html__( '11', 'agrosector' ),
				'12' => esc_html__( '12', 'agrosector' ),
				'13' => esc_html__( '13', 'agrosector' ),
				'14' => esc_html__( '14', 'agrosector' ),
				'15' => esc_html__( '15', 'agrosector' ),
				'16' => esc_html__( '16', 'agrosector' ),
				'17' => esc_html__( '17', 'agrosector' ),
				'18' => esc_html__( '18', 'agrosector' ),
			),
			'default' => '14'
		),
		array(
			'id'      => 'map_marker_info',
			'type'    => 'switch',
			'title'   => esc_html__( 'Map Marker Info', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'       => 'custom_map_marker',
			'type'     => 'text',
			'title'    => esc_html__( 'Custom Map Marker URl', 'agrosector' ),
			'default'  => '',
			'subtitle' => esc_html__( 'Visible only on mobile or if "Map Marker Info" option is off.', 'agrosector' ),
		),
		array(
			'id'       => 'map_marker_info_street_number',
			'type'     => 'text',
			'title'    => esc_html__( 'Street Number', 'agrosector' ),
			'default'  => '',
			'required' => array( 'map_marker_info', '=', '1' ),
		),
		array(
			'id'       => 'map_marker_info_street',
			'type'     => 'text',
			'title'    => esc_html__( 'Street', 'agrosector' ),
			'default'  => '',
			'required' => array( 'map_marker_info', '=', '1' ),
		),
		array(
			'id'           => 'map_marker_info_descr',
			'type'         => 'textarea',
			'title'        => esc_html__( 'Short Description', 'agrosector' ),
			'default'      => '',
			'required'     => array( 'map_marker_info', '=', '1' ),
			'allowed_html' => array(
				'a'      => array(
					'href'  => array(),
					'title' => array()
				),
				'br'     => array(),
				'em'     => array(),
				'strong' => array()
			),
			'description'  => esc_html__( 'The optimal number of characters is 35', 'agrosector' ),
		),
		array(
			'id'          => 'map_marker_info_background',
			'type'        => 'color',
			'title'       => esc_html__( 'Map Marker Info Background', 'agrosector' ),
			'subtitle'    => esc_html__( 'Set Map Marker Info Background', 'agrosector' ),
			'default'     => '#f9f9f9',
			'transparent' => false,
			'required'    => array( 'map_marker_info', '=', '1' ),
		),
		array(
			'id'             => 'map-marker-font',
			'type'           => 'typography',
			'title'          => esc_html__( 'Map Marker Font', 'agrosector' ),
			'google'         => true,
			'font-backup'    => false,
			'font-size'      => false,
			'line-height'    => false,
			'color'          => false,
			'word-spacing'   => false,
			'letter-spacing' => false,
			'text-align'     => false,
			'default'        => array(
				'google'      => true,
			),
		),
		array(
			'id'          => 'map_marker_info_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Map Marker Description Text Color', 'agrosector' ),
			'subtitle'    => esc_html__( 'Set Map Marker Description Text Color', 'agrosector' ),
			'default'     => '#949494',
			'transparent' => false,
			'required'    => array( 'map_marker_info', '=', '1' ),
		),
		array(
			'id'      => 'custom_map_style',
			'type'    => 'switch',
			'title'   => esc_html__( 'Custom Map Style', 'agrosector' ),
			'default' => false,
		),
		array(
			'id'       => 'custom_map_code',
			'type'     => 'ace_editor',
			'title'    => esc_html__( 'JavaScript Style Array', 'agrosector' ),
			'desc'     => esc_html__( 'To change the style of the map, you must insert the JavaScript Style Array code from ', 'agrosector' ) . ' <a href="https://snazzymaps.com/" target="_blank">' . esc_html__( 'Snazzy Maps', 'agrosector' )
			              . '</a>',
			'mode'     => 'javascript',
			'theme'    => 'chrome',
			'default'  => "",
			'required' => array( 'custom_map_style', '=', '1' ),
		),
	),
) );


if ( class_exists( 'WooCommerce' ) ) {
	// -> START Layout Options
	Redux::setSection( $opt_name, array(
		'title'            => esc_html__( 'Shop', 'agrosector' ),
		'id'               => 'woocommerce_layout_options',
		'customizer_width' => '400px',
		'icon'             => 'el el-shopping-cart',
		'fields'           => array()
	) );
	Redux::setSection( $opt_name, array(
		'title'            => esc_html__( 'Products Page', 'agrosector' ),
		'id'               => 'products_page_settings',
		'subsection'       => true,
		'customizer_width' => '450px',
		'fields'           => array(
			array(
				'id'       => 'shop_cat_title_conditional',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show Title for Shop Category', 'agrosector' ),
				'default'  => true,
				'required' => array( 'page_title_conditional', '=', '1' ),
			),
			array(
				'id'      => 'products_layout',
				'type'    => 'select',
				'title'   => esc_html__( 'Products Layout', 'agrosector' ),
				'options' => array(
					'container'  => esc_html__( 'Container', 'agrosector' ),
					'full_width' => esc_html__( 'Full Width', 'agrosector' ),
				),
				'default' => 'container'
			),
			array(
				'id'      => 'products_sidebar_layout',
				'type'    => 'image_select',
				'title'   => esc_html__( 'Products Page Sidebar Layout', 'agrosector' ),
				'options' => array(
					'none'  => array(
						'alt' => 'None',
						'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/1col.png'
					),
					'left'  => array(
						'alt' => 'Left',
						'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cl.png'
					),
					'right' => array(
						'alt' => 'Right',
						'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cr.png'
					)
				),
				'default' => 'none'
			),
			array(
				'id'       => 'products_sidebar_def',
				'type'     => 'select',
				'title'    => esc_html__( 'Products Page Sidebar', 'agrosector' ),
				'data'     => 'sidebars',
				'required' => array( 'products_sidebar_layout', '!=', 'none' ),
			),
			array(
				'id'    => 'products_per_page_frontend',
				'type'  => 'switch',
				'title' => esc_html__( 'Show dropdown on the frontend to change Number of products displayed per page', 'agrosector' ),
			),
			array(
				'id'    => 'products_sorting_frontend',
				'type'  => 'switch',
				'title' => esc_html__( 'Show dropdown on the frontend to change Sorting of products displayed per page', 'agrosector' ),
			),
			array(
				'id'      => 'products_infinite_scroll',
				'type'    => 'select',
				'title'   => esc_html__( 'Infinite Scroll', 'agrosector' ),
				'desc'    => esc_html__( 'Select Infinite Scroll options', 'agrosector' ),
				'options' => array(
					'none'     => esc_html__( 'None', 'agrosector' ),
					'view_all' => esc_html__( 'Activate after clicking on "View All"', 'agrosector' ),
					'always'   => esc_html__( 'Always', 'agrosector' ),
				),
				'default' => 'none',
			),
			array(
				'id'      => 'woocommerce_pagination',
				'type'    => 'select',
				'title'   => esc_html__( 'Pagination', 'agrosector' ),
				'desc'    => esc_html__( 'Select the position of pagination.', 'agrosector' ),
				'options' => array(
					'top'        => esc_html__( 'Top', 'agrosector' ),
					'bottom'     => esc_html__( 'Bottom', 'agrosector' ),
					'top_bottom' => esc_html__( 'Top and Bottom', 'agrosector' ),
					'off'        => esc_html__( 'Off', 'agrosector' ),
				),
				'default' => 'top_bottom',
				'required' => array( 'products_infinite_scroll', '!=', 'always' ),
			),
			array(
				'id'      => 'woocommerce_grid_list',
				'type'    => 'select',
				'title'   => esc_html__( 'Grid/List Option', 'agrosector' ),
				'desc'    => esc_html__( 'Display products in grid or list view by default', 'agrosector' ),
				'options' => array(
					'grid' => esc_html__( 'Grid', 'agrosector' ),
					'list' => esc_html__( 'List', 'agrosector' ),
					'off'  => esc_html__( 'Off', 'agrosector' ),
				),
				'default' => 'off',
			),
			array(
				'id'     => 'section-label_color-start',
				'type'   => 'section',
				'title'  => esc_html__( '"Sale", "Hot" and "New" labels color', 'agrosector' ),
				'indent' => true,
			),
			array(
				'id'       => 'label_color_sale',
				'type'     => 'color_rgba',
				'title'    => esc_html__( 'Color for "Sale" label', 'agrosector' ),
				'subtitle' => esc_html__( 'Select the Background Color for "Sale" label.', 'agrosector' ),
				'default'  => array(
					'color' => '#e63764',
					'alpha' => '1',
					'rgba'  => 'rgba(230,55,100,1)'
				),
			),
			array(
				'id'       => 'label_color_hot',
				'type'     => 'color_rgba',
				'title'    => esc_html__( 'Color for "Hot" label', 'agrosector' ),
				'subtitle' => esc_html__( 'Select the Background Color for "Hot" label.', 'agrosector' ),
				'default'  => array(
					'color' => '#71d080',
					'alpha' => '1',
					'rgba'  => 'rgba(113,208,128,1)'
				),
			),
			array(
				'id'       => 'label_color_new',
				'type'     => 'color_rgba',
				'title'    => esc_html__( 'Color for "New" label', 'agrosector' ),
				'subtitle' => esc_html__( 'Select the Background Color for "New" label.', 'agrosector' ),
				'default'  => array(
					'color' => '#6ad1e4',
					'alpha' => '1',
					'rgba'  => 'rgba(106,209,228,1)'
				),
			),
			array(
				'id'     => 'section-label_color-end',
				'type'   => 'section',
				'indent' => false,
			),
		)
	) );
	Redux::setSection( $opt_name, array(
		'title'            => esc_html__( 'Single Product Page', 'agrosector' ),
		'id'               => 'product_page_settings',
		'subsection'       => true,
		'customizer_width' => '450px',
		'fields'           => array(
			array(
				'id'      => 'product_layout',
				'type'    => 'select',
				'title'   => esc_html__( 'Thumbnails Layout', 'agrosector' ),
				'options' => array(
					'horizontal'     => esc_html__( 'Thumbnails Bottom', 'agrosector' ),
					'vertical'       => esc_html__( 'Thumbnails Left', 'agrosector' ),
					'thumb_grid'     => esc_html__( 'Thumbnails Grid', 'agrosector' ),
					'thumb_vertical' => esc_html__( 'Thumbnails Vertical Grid', 'agrosector' ),
				),
				'default' => 'horizontal'
			),
			array(
				'id'      => 'activate_carousel_thumb',
				'type'    => 'switch',
				'title'   => esc_html__( 'Activate Carousel for Vertical Thumbnail', 'agrosector' ),
				'default' => false,
			),
			array(
				'id'       => 'product_title_conditional',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show Product Page Title', 'agrosector' ),
				'default'  => false,
				'required' => array( 'page_title_conditional', '=', '1' ),
			),
			array(
				'id'      => 'product_container',
				'type'    => 'select',
				'title'   => esc_html__( 'Product Page Layout', 'agrosector' ),
				'options' => array(
					'container'  => esc_html__( 'Container', 'agrosector' ),
					'full_width' => esc_html__( 'Full Width', 'agrosector' ),
				),
				'default' => 'container'
			),
			array(
				'id'       => 'sticky_thumb',
				'type'     => 'switch',
				'title'    => esc_html__( 'Sticky Thumbnails', 'agrosector' ),
				'default'  => false,
				'required' => array( 'product_layout', '!=', 'thumb_vertical' ),
			),
			array(
				'id'      => 'product_sidebar_layout',
				'type'    => 'image_select',
				'title'   => esc_html__( 'Single Product Page Sidebar Layout', 'agrosector' ),
				'options' => array(
					'none'  => array(
						'alt' => 'None',
						'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/1col.png'
					),
					'left'  => array(
						'alt' => 'Left',
						'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cl.png'
					),
					'right' => array(
						'alt' => 'Right',
						'img' => esc_url( ReduxFramework::$_url ) . 'assets/img/2cr.png'
					)
				),
				'default' => 'none'
			),
			array(
				'id'       => 'product_sidebar_def',
				'type'     => 'select',
				'title'    => esc_html__( 'Single Product Page Sidebar', 'agrosector' ),
				'data'     => 'sidebars',
				'required' => array( 'product_sidebar_layout', '!=', 'none' ),
			),
			array(
				'id'       => 'shop_title_conditional',
				'type'     => 'switch',
				'title'    => esc_html__( 'Show Single Product Title Area (Category name)', 'agrosector' ),
				'default'  => true,
				'required' => array( 'page_title_conditional', '=', '1' ),
			),
			array(
				'id'      => 'shop_size_guide',
				'type'    => 'switch',
				'title'   => esc_html__( 'Show Size Guide', 'agrosector' ),
				'default' => false,
			),
			array(
				'id'       => 'size_guide',
				'type'     => 'media',
				'title'    => esc_html__( 'Size guide Popup Image', 'agrosector' ),
				'required' => array( 'shop_size_guide', '=', true ),
			),
			array(
				'id'      => 'next_prev_product',
				'type'    => 'switch',
				'title'   => esc_html__( 'Show Next and Previous products', 'agrosector' ),
				'default' => true,
			),
		)
	) );
}

