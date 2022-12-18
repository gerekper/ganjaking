<?php
function porto_check_theme_options() {
	// check default options
	global $porto_settings;
	ob_start();
	include PORTO_ADMIN . '/theme_options/default_options.php';
	$options                = ob_get_clean();
	$porto_default_settings = json_decode( $options, true );

	global $porto_settings_optimize;
	$legacy_mode = true;
	if ( isset( $porto_settings_optimize['legacy_mode'] ) ) {
		$legacy_mode = $porto_settings_optimize['legacy_mode'];
	} else {
		$legacy_mode = true;
	}

	if ( class_exists( 'Porto_Soft_Mode' ) && ! $legacy_mode ) {
		$should_remove = Porto_Soft_Mode::$should_remove;
		foreach ( $should_remove as $value ) {
			if ( isset( $porto_default_settings[ $value ] ) ) {
				unset( $porto_default_settings[ $value ] );
			}
		}
	}
	foreach ( $porto_default_settings as $key => $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key1 => $value1 ) {
				if ( 'google' != $key1 && ( ! isset( $porto_settings[ $key ][ $key1 ] ) || '' === $porto_settings[ $key ][ $key1 ] ) ) {
					if ( ! isset( $porto_settings[ $key ] ) || empty( $porto_settings[ $key ] ) ) {
						$porto_settings[ $key ] = array( $key1 => '' );
					}
					$porto_settings[ $key ][ $key1 ] = $porto_default_settings[ $key ][ $key1 ];
				}
			}
		} else {
			if ( ! isset( $porto_settings[ $key ] ) ) {
				$porto_settings[ $key ] = $porto_default_settings[ $key ];
			}
		}
	}
	return $porto_settings;
}

if ( ! function_exists( 'porto_options_sidebars' ) ) :
	function porto_options_sidebars() {
		return array(
			'wide-left-sidebar',
			'wide-right-sidebar',
			'left-sidebar',
			'right-sidebar',
			'wide-both-sidebar',
			'both-sidebar',
		);
	}
endif;

if ( ! function_exists( 'porto_options_both_sidebars' ) ) :
	function porto_options_both_sidebars() {
		return array(
			'wide-both-sidebar',
			'both-sidebar',
		);
	}
endif;

if ( ! function_exists( 'porto_options_body_wrapper' ) ) :
	function porto_options_body_wrapper() {
		return array(
			'wide'  => array(
				'title' => 'Wide',
				'img'   => PORTO_OPTIONS_URI . '/layouts/body_wide.svg',
			),
			'full'  => array(
				'title' => 'Full',
				'img'   => PORTO_OPTIONS_URI . '/layouts/body_full.svg',
			),
			'boxed' => array(
				'title' => 'Boxed',
				'img'   => PORTO_OPTIONS_URI . '/layouts/body_boxed.svg',
			),
		);
	}
endif;

if ( ! function_exists( 'porto_options_layouts' ) ) :
	function porto_options_layouts() {
		return array(
			'widewidth'          => array(
				'title' => 'Wide Width',
				'img'   => PORTO_OPTIONS_URI . '/layouts/page_wide.svg',
			),
			'wide-left-sidebar'  => array(
				'title' => 'Wide Left Sidebar',
				'img'   => PORTO_OPTIONS_URI . '/layouts/page_wide_left.svg',
			),
			'wide-right-sidebar' => array(
				'title' => 'Wide Right Sidebar',
				'img'   => PORTO_OPTIONS_URI . '/layouts/page_wide_right.svg',
			),
			'wide-both-sidebar'  => array(
				'title' => 'Wide Both Sidebars',
				'img'   => PORTO_OPTIONS_URI . '/layouts/page_wide_both.svg',
			),
			'fullwidth'          => array(
				'title' => 'Without Sidebar',
				'img'   => PORTO_OPTIONS_URI . '/layouts/page_full.svg',
			),
			'left-sidebar'       => array(
				'title' => 'Left Sidebar',
				'img'   => PORTO_OPTIONS_URI . '/layouts/page_full_left.svg',
			),
			'right-sidebar'      => array(
				'title' => 'Right Sidebar',
				'img'   => PORTO_OPTIONS_URI . '/layouts/page_full_right.svg',
			),
			'both-sidebar'       => array(
				'title' => 'Both Sidebars',
				'img'   => PORTO_OPTIONS_URI . '/layouts/page_full_both.svg',
			),
		);
	}
endif;

if ( ! function_exists( 'porto_options_wrapper' ) ) :
	function porto_options_wrapper() {
		return array(
			'wide'  => array(
				'title' => 'Wide',
				'img'   => PORTO_OPTIONS_URI . '/layouts/content_wide.svg',
			),
			'full'  => array(
				'title' => 'Full',
				'img'   => PORTO_OPTIONS_URI . '/layouts/content_full.svg',
			),
			'boxed' => array(
				'title' => 'Boxed',
				'img'   => PORTO_OPTIONS_URI . '/layouts/content_boxed.svg',
			),
		);
	}
endif;

if ( ! function_exists( 'porto_options_banner_wrapper' ) ) :
	function porto_options_banner_wrapper() {
		return array(
			'wide'  => array(
				'title' => 'Wide',
				'img'   => PORTO_OPTIONS_URI . '/layouts/content_wide.svg',
			),
			'boxed' => array(
				'title' => 'Boxed',
				'img'   => PORTO_OPTIONS_URI . '/layouts/content_boxed.svg',
			),
		);
	}
endif;

if ( ! function_exists( 'porto_options_header_types' ) ) :
	function porto_options_header_types() {
		return array(
			'10'   => array(
				'alt'             => 'Header Type 10',
				'title'           => '10',
				'img'             => PORTO_OPTIONS_URI . '/headers/header_10.png',
				'default_options' => array(),
			),
			'11'   => array(
				'alt'   => 'Header Type 11',
				'title' => '11',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_11.png',
			),
			'12'   => array(
				'alt'   => 'Header Type 12',
				'title' => '12',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_12.png',
			),
			'13'   => array(
				'alt'   => 'Header Type 13',
				'title' => '13',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_13.png',
			),
			'14'   => array(
				'alt'   => 'Header Type 14',
				'title' => '14',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_14.png',
			),
			'15'   => array(
				'alt'   => 'Header Type 15',
				'title' => '15',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_15.png',
			),
			'16'   => array(
				'alt'   => 'Header Type 16',
				'title' => '16',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_16.png',
			),
			'17'   => array(
				'alt'   => 'Header Type 17',
				'title' => '17',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_17.png',
			),

			'1'    => array(
				'alt'   => 'Header Type 1',
				'title' => '1',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_01.png',
			),
			'2'    => array(
				'alt'   => 'Header Type 2',
				'title' => '2',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_02.png',
			),
			'3'    => array(
				'alt'   => 'Header Type 3',
				'title' => '3',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_03.jpg',
			),
			'4'    => array(
				'alt'   => 'Header Type 4',
				'title' => '4',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_04.png',
			),
			'5'    => array(
				'alt'   => 'Header Type 5',
				'title' => '5',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_05.jpg',
			),
			'6'    => array(
				'alt'   => 'Header Type 6',
				'title' => '6',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_06.jpg',
			),
			'7'    => array(
				'alt'   => 'Header Type 7',
				'title' => '7',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_07.jpg',
			),
			'8'    => array(
				'alt'   => 'Header Type 8',
				'title' => '8',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_08.png',
			),
			'9'    => array(
				'alt'   => 'Header Type 9',
				'title' => '9',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_09.png',
			),

			'18'   => array(
				'alt'   => 'Header Type 18',
				'title' => '18',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_18.jpg',
			),
			'19'   => array(
				'alt'   => 'Header Type 19',
				'title' => '19',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_19.png',
			),
			'side' => array(
				'alt'   => 'Header Type(Side Navigation)',
				'title' => 'Side',
				'img'   => PORTO_OPTIONS_URI . '/headers/header_side.jpg',
			),
		);
	}
endif;

/**
 * Default theme options for header types
 */
if ( ! function_exists( 'porto_header_types_default_options' ) ) :
	function porto_header_types_default_options() {
		return array(
			/* shop headers */
			'1, 4, 5, 6, 9'              => array(
				'search-layout' => 'advanced',
				'minicart-type' => 'minicart-arrow-alt',
				'menu-type'     => '',
			),
			'3, 7, 8, 18'                => array(
				'search-layout' => 'large',
				'minicart-type' => 'minicart-arrow-alt',
				'menu-type'     => '',
			),
			'2'                          => array(
				'search-layout' => 'simple',
				'minicart-type' => 'minicart-arrow-alt',
				'menu-type'     => '',
			),
			'19'                         => array(
				'search-layout' => 'advanced',
				'minicart-type' => 'simple',
				'menu-type'     => '',
			),
			'side'                       => array(
				'search-layout' => 'advanced',
				'minicart-type' => 'minicart-inline',
				'menu-type'     => '',
			),

			/* classic headers */
			'10, 11, 12, 13, 14, 15, 16' => array(
				'search-layout' => 'simple',
				'minicart-type' => 'simple',
				'menu-type'     => '',
			),
			'17'                         => array(
				'search-layout' => 'advanced',
				'minicart-type' => 'minicart-inline',
				'menu-type'     => 'menu-flat menu-flat-border',
			),
		);
	}
endif;

/**
 * Header Builder Presets
 */
if ( ! function_exists( 'porto_header_builder_presets' ) ) :
	function porto_header_builder_presets() {
		return array(
			'preset_1'      => array(
				'img'      => 'header_preset1.jpg',
				'title'    => 'Header Preset 1',
				'elements' => array(
					'top_left'   => '[{"contact":""}]',
					'top_right'  => '[{"html":"<ul class=\"nav nav-top\">\n    <li class=\"nav-item\">\n        <a href=\"mailto:mail@domain.com\"><i class=\"far fa-envelope text-color-primary\"></i>mail@domain.com</a>\n    </li>\n    <li class=\"nav-item\">\n        <a href=\"tel:123-456-7890\"><i class=\"fab fa-whatsapp text-color-primary\"></i>123-456-7890</a>\n    </li>\n</ul>"}]',
					'main_left'  => '[{"logo":""}]',
					'main_right' => '[{"main-menu":""},{"social":""},{"divider":""},{"search-form":""},{"mini-cart":""},{"menu-icon":""}]',
				),
				'options'  => array(
					'menu-type'                    => '',
					'mainmenu-toplevel-padding1'   => array(
						'padding-top'    => 40,
						'padding-bottom' => 40,
						'padding-left'   => 16,
						'padding-right'  => 16,
					),
					'mainmenu-toplevel-padding2'   => array(
						'padding-top'    => 30,
						'padding-bottom' => 30,
						'padding-left'   => 14,
						'padding-right'  => 14,
					),
					'mainmenu-toplevel-padding3'   => array(
						'padding-top'    => 25,
						'padding-bottom' => 25,
					),
					'header-main-padding'          => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
					),
					'header-main-padding-mobile'   => array(
						'padding-top'    => 15,
						'padding-bottom' => 15,
					),
					'mainmenu-wrap-padding-sticky' => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
						'padding-left'   => 0,
						'padding-right'  => 0,
					),
				),
			),
			'preset_2'      => array(
				'img'      => 'header_preset2.jpg',
				'title'    => 'Header Preset 2',
				'elements' => array(
					'top_left'   => '[{"contact":""}]',
					'top_right'  => '[{"html":"<ul class=\"nav nav-top\">\n    <li class=\"nav-item\">\n        <a href=\"mailto:mail@domain.com\"><i class=\"far fa-envelope text-color-primary\"></i>mail@domain.com</a>\n    </li>\n    <li class=\"nav-item\">\n        <a href=\"tel:123-456-7890\"><i class=\"fab fa-whatsapp text-color-primary\"></i>123-456-7890</a>\n    </li>\n</ul>"}]',
					'main_left'  => '[{"logo":""}]',
					'main_right' => '[{"main-menu":""},{"social":""},{"menu-icon":""}]',

				),
				'options'  => array(
					'menu-type'                    => 'menu-hover-line menu-hover-underline',
					'mainmenu-toplevel-padding1'   => array(
						'padding-top'    => 40,
						'padding-bottom' => 40,
						'padding-left'   => 14,
						'padding-right'  => 14,
					),
					'mainmenu-toplevel-padding2'   => array(
						'padding-top'    => 30,
						'padding-bottom' => 30,
						'padding-left'   => 12,
						'padding-right'  => 12,
					),
					'mainmenu-toplevel-padding3'   => array(
						'padding-top'    => 25,
						'padding-bottom' => 25,
					),
					'header-main-padding'          => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
					),
					'header-main-padding-mobile'   => array(
						'padding-top'    => 15,
						'padding-bottom' => 15,
					),
					'mainmenu-wrap-padding-sticky' => array(
						'padding-top'    => '',
						'padding-bottom' => '',
						'padding-left'   => '',
						'padding-right'  => '',
					),
				),
			),
			'preset_3'      => array(
				'img'      => 'header_preset3.jpg',
				'title'    => 'Header Preset 3',
				'elements' => array(
					'main_left'  => '[{"logo":""}]',
					'main_right' => '[{"main-menu":""},{"divider":""},{"search-form":""},{"mini-cart":""},{"menu-icon":""}]',
				),
				'options'  => array(
					'menu-type'                    => '',
					'mainmenu-toplevel-padding1'   => array(
						'padding-top'    => 40,
						'padding-bottom' => 40,
						'padding-left'   => 16,
						'padding-right'  => 16,
					),
					'mainmenu-toplevel-padding2'   => array(
						'padding-top'    => 30,
						'padding-bottom' => 30,
						'padding-left'   => 14,
						'padding-right'  => 14,
					),
					'mainmenu-toplevel-padding3'   => array(
						'padding-top'    => 25,
						'padding-bottom' => 25,
					),
					'header-main-padding'          => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
					),
					'header-main-padding-mobile'   => array(
						'padding-top'    => 15,
						'padding-bottom' => 15,
					),
					'mainmenu-wrap-padding-sticky' => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
						'padding-left'   => 0,
						'padding-right'  => 0,
					),
				),
			),
			'preset_4'      => array(
				'img'        => 'header_preset4.jpg',
				'title'      => 'Header Preset 4',
				'elements'   => array(
					'main_left'  => '[{"logo":""}]',
					'main_right' => '[{"main-menu":""},{"divider":""},{"social":""},{"menu-icon":""}]',
				),
				'custom_css' => '@media (max-width: 991px) { #header .separator { display: none; } }',
				'options'    => array(
					'menu-type'                    => 'menu-hover-line',
					'mainmenu-toplevel-padding1'   => array(
						'padding-top'    => 40,
						'padding-bottom' => 40,
						'padding-left'   => 14,
						'padding-right'  => 14,
					),
					'mainmenu-toplevel-padding2'   => array(
						'padding-top'    => 30,
						'padding-bottom' => 30,
						'padding-left'   => 12,
						'padding-right'  => 12,
					),
					'mainmenu-toplevel-padding3'   => array(
						'padding-top'    => 25,
						'padding-bottom' => 25,
					),
					'header-main-padding'          => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
					),
					'header-main-padding-mobile'   => array(
						'padding-top'    => 15,
						'padding-bottom' => 15,
					),
					'mainmenu-wrap-padding-sticky' => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
						'padding-left'   => 0,
						'padding-right'  => 0,
					),
				),
			),
			'preset_5'      => array(
				'img'        => 'header_preset5.jpg',
				'title'      => 'Header Preset 5',
				'elements'   => array(
					'top_left'   => '[{"contact":""},{"language-switcher":""},{"html":""}]',
					'top_right'  => '[{"social":""}]',
					'main_left'  => '[{"logo":""}]',
					'main_right' => '[{"main-menu":""},{"divider":""},{"search-form":""},{"mini-cart":""},{"menu-icon":""}]',
				),
				'custom_css' => '@media (max-width: 991px) { #header .separator { display: none; } }',
				'options'    => array(
					'menu-type'                    => 'menu-hover-line',
					'mainmenu-toplevel-padding1'   => array(
						'padding-top'    => 40,
						'padding-bottom' => 40,
						'padding-left'   => 16,
						'padding-right'  => 16,
					),
					'mainmenu-toplevel-padding2'   => array(
						'padding-top'    => 30,
						'padding-bottom' => 30,
						'padding-left'   => 14,
						'padding-right'  => 14,
					),
					'mainmenu-toplevel-padding3'   => array(
						'padding-top'    => 25,
						'padding-bottom' => 25,
					),
					'header-main-padding'          => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
					),
					'header-main-padding-mobile'   => array(
						'padding-top'    => 15,
						'padding-bottom' => 15,
					),
					'mainmenu-wrap-padding-sticky' => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
						'padding-left'   => 0,
						'padding-right'  => 0,
					),
				),
			),
			'preset_6'      => array(
				'img'      => 'header_preset6.jpg',
				'title'    => 'Header Preset 6',
				'elements' => array(
					'top_left'   => '[{"contact":""}]',
					'top_right'  => '[{"nav-top":""},{"divider":""},{"language-switcher":""}]',
					'main_left'  => '[{"logo":""}]',
					'main_right' => '[{"main-menu":""},{"divider":""},{"menu-icon":""},{"search-form":""},{"mini-cart":""}]',
				),
				'options'  => array(
					'menu-type'                    => '',
					'mainmenu-toplevel-padding1'   => array(
						'padding-top'    => 40,
						'padding-bottom' => 40,
						'padding-left'   => 16,
						'padding-right'  => 16,
					),
					'mainmenu-toplevel-padding2'   => array(
						'padding-top'    => 30,
						'padding-bottom' => 30,
						'padding-left'   => 14,
						'padding-right'  => 14,
					),
					'mainmenu-toplevel-padding3'   => array(
						'padding-top'    => 25,
						'padding-bottom' => 25,
					),
					'header-main-padding'          => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
					),
					'header-main-padding-mobile'   => array(
						'padding-top'    => 15,
						'padding-bottom' => 15,
					),
					'mainmenu-wrap-padding-sticky' => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
						'padding-left'   => 0,
						'padding-right'  => 0,
					),
				),
			),
			'preset_7'      => array(
				'img'        => 'header_preset7.jpg',
				'title'      => 'Header Preset 7',
				'elements'   => array(
					'top_left'           => '[{"contact":""}]',
					'top_right'          => '[{"social":""}]',
					'main_left'          => '[{"html":"<div class=\"feature-box feature-box-style-2 align-items-center\">\n\t<div class=\"feature-box-icon font-size-sm\">\n\t\t<i class=\"far fa-clock\"></i>\n\t</div>\n\t<div class=\"feature-box-info ps-2\">\n\t\t<p class=\"pb-0 font-weight-semibold font-size-sm mb-0\">MON - FRI: 10:00 - 18:00<br>SAT - SUN: 10:00 - 14:00</p>\n\t</div>\n</div>"}]',
					'main_center'        => '[{"logo":""}]',
					'main_right'         => '[{"html":"<div class=\"feature-box reverse-allres feature-box-style-2 align-items-center\">\n\t<div class=\"feature-box-icon font-size-sm\">\n\t\t<i class=\"fab fa-whatsapp\"></i>\n\t</div>\n\t<div class=\"feature-box-info pe-2 mt-1\">\n\t\t<p class=\"mb-0 font-weight-semibold font-size-sm\">(123) 456-7890<br>(123) 456-7891</p>\n\t</div>\n</div>"}]',
					'bottom_center'      => '[{"main-menu":""}]',
					'mobile_top_left'    => '[{"contact":""}]',
					'mobile_top_right'   => '[{"social":""}]',
					'mobile_main_center' => '[{"logo":""}]',
					'mobile_main_right'  => '[{"html":"<div class=\"feature-box reverse-allres feature-box-style-2 align-items-center\">\n\t<div class=\"feature-box-icon font-size-sm\">\n\t\t<i class=\"fab fa-whatsapp\"></i>\n\t</div>\n\t<div class=\"feature-box-info pe-2 mt-1\">\n\t\t<p class=\"mb-0 font-weight-semibold font-size-sm\">(123) 456-7890<br>(123) 456-7891</p>\n\t</div>\n</div>"},{"menu-icon":""}]',
				),
				'custom_css' => '#header .feature-box p { line-height: 1.5; }@media (min-width: 992px) { .header-bottom { border-top: 1px solid rgba(0, 0, 0, .08); } }',
				'options'    => array(
					'menu-type'                    => 'menu-hover-line',
					'mainmenu-toplevel-hbg-color'  => 'transparent',
					'mainmenu-toplevel-padding1'   => array(
						'padding-top'    => 20,
						'padding-bottom' => 20,
						'padding-left'   => 24,
						'padding-right'  => 24,
					),
					'mainmenu-toplevel-padding2'   => array(
						'padding-top'    => 15,
						'padding-bottom' => 15,
						'padding-left'   => 20,
						'padding-right'  => 20,
					),
					'mainmenu-toplevel-padding3'   => array(
						'padding-top'    => '',
						'padding-bottom' => '',
					),
					'header-main-padding'          => array(
						'padding-top'    => '',
						'padding-bottom' => '',
					),
					'header-main-padding-mobile'   => array(
						'padding-top'    => 15,
						'padding-bottom' => 15,
					),
					'mainmenu-wrap-padding-sticky' => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
						'padding-left'   => 0,
						'padding-right'  => 0,
					),
				),
			),
			'preset_8'      => array(
				'img'      => 'header_preset8.jpg',
				'title'    => 'Header Preset 8',
				'elements' => array(
					'main_left'   => '[{"logo":""}]',
					'main_center' => '[{"main-menu":""}]',
					'main_right'  => '[{"social":""},{"menu-icon":""}]',
				),
				'options'  => array(
					'menu-type'                    => '',
					'mainmenu-toplevel-padding1'   => array(
						'padding-top'    => 40,
						'padding-bottom' => 40,
						'padding-left'   => 16,
						'padding-right'  => 16,
					),
					'mainmenu-toplevel-padding2'   => array(
						'padding-top'    => 30,
						'padding-bottom' => 30,
						'padding-left'   => 14,
						'padding-right'  => 14,
					),
					'mainmenu-toplevel-padding3'   => array(
						'padding-top'    => 25,
						'padding-bottom' => 25,
					),
					'header-main-padding'          => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
					),
					'header-main-padding-mobile'   => array(
						'padding-top'    => 15,
						'padding-bottom' => 15,
					),
					'mainmenu-wrap-padding-sticky' => array(
						'padding-top'    => 0,
						'padding-bottom' => 0,
						'padding-left'   => 0,
						'padding-right'  => 0,
					),
				),
			),
			'preset_side_1' => array(
				'img'                     => 'header_preset_side1.jpg',
				'title'                   => 'Side Header Preset 1',
				'elements'                => array(
					'top_center'    => '[{"logo":""}]',
					'main_center'   => '[{"main-menu":""}]',
					'bottom_center' => '[{"social":""}]',
				),
				'type'                    => 'side',
				'side_header_toggle'      => 'side',
				'side_header_toggle_logo' => '//sw-themes.com/porto_dummy/wp-content/uploads/2018/12/logo-symbol-light.png',
				'side_header_toggle_desc' => '<strong class="side-header-narrow-bar-content-vertical">Porto Wordpress</strong>',
				'side_header_width'       => '320',
				'options'                 => array(
					'side-menu-type'                => 'slide',
					'side-social-bg-color'          => '#ffffff',
					'side-social-color'             => '#333333',
					'menu-text-transform'           => 'uppercase',
					'mainmenu-toplevel-hbg-color'   => 'transparent',
					'mainmenu-popup-bg-color'       => '#ffffff',
					'mainmenu-popup-heading-color'  => '#333333',
					'mainmenu-popup-text-color'     => array(
						'regular' => '#777777',
						'hover'   => '#777777',
					),
					'mainmenu-popup-text-hbg-color' => '#f7f7f7',
				),
			),
			'preset_side_2' => array(
				'img'               => 'header_preset_side2.png',
				'title'             => 'Side Header Preset 2',
				'elements'          => array(
					'top_center'        => '[{"logo":""}]',
					'main_center'       => '[{"main-menu":""}]',
					'bottom_center'     => '[{"social":""}]',
					'mobile_main_left'  => '[{"logo":""}]',
					'mobile_main_right' => '[{"social":""},{"menu-icon":""}]',
				),
				'type'              => 'side',
				'side_header_width' => '256',
				'options'           => array(
					'side-menu-type'                => 'accordion',
					'side-social-bg-color'          => '#ffffff',
					'side-social-color'             => '#333333',
					'menu-text-transform'           => 'uppercase',
					'mainmenu-toplevel-hbg-color'   => 'transparent',
					'mainmenu-popup-bg-color'       => '#ffffff',
					'mainmenu-popup-heading-color'  => '#333333',
					'mainmenu-popup-text-color'     => array(
						'regular' => '#777777',
						'hover'   => '#777777',
					),
					'mainmenu-popup-text-hbg-color' => '#f4f4f4',
				),
			),
			'preset_side_3' => array(
				'img'                     => 'header_preset_side3.png',
				'title'                   => 'Side Header Preset 3',
				'elements'                => array(
					'top_center'    => '[{"logo":""}]',
					'main_center'   => '[{"main-menu":""}]',
					'bottom_center' => '[{"social":""}]',
				),
				'type'                    => 'side',
				'side_header_toggle'      => 'top',
				'side_header_toggle_logo' => '//sw-themes.com/porto_dummy/wp-content/uploads/2018/12/logo-corporate-13.png',
				'side_header_width'       => '320',
				'options'                 => array(
					'side-menu-type'               => 'slide',
					'side-social-bg-color'         => '#ffffff',
					'side-social-color'            => '#333333',
					'menu-text-transform'          => 'uppercase',
					'mainmenu-toplevel-hbg-color'  => 'transparent',
					'mainmenu-popup-bg-color'      => '#ffffff',
					'mainmenu-popup-heading-color' => '#777777',
					'mainmenu-popup-text-color'    => array(
						'regular' => '#777777',
						'hover'   => '#777777',
					),
				),
			),
		);
	}
endif;

if ( ! function_exists( 'porto_options_footer_types' ) ) :
	function porto_options_footer_types() {
		return array(
			'1' => array(
				'alt' => 'Footer Type 1',
				'img' => PORTO_OPTIONS_URI . '/footers/footer_01.jpg',
			),
			'2' => array(
				'alt' => 'Footer Type 2',
				'img' => PORTO_OPTIONS_URI . '/footers/footer_02.jpg',
			),
			'3' => array(
				'alt' => 'Footer Type 3',
				'img' => PORTO_OPTIONS_URI . '/footers/footer_03.jpg',
			),
		);
	}
endif;

if ( ! function_exists( 'porto_options_breadcrumbs_types' ) ) :
	function porto_options_breadcrumbs_types() {
		return array(
			'1' => array(
				'alt' => 'Breadcrumbs Type 1',
				'img' => PORTO_OPTIONS_URI . '/breadcrumbs/breadcrumbs_01.jpg',
			),
			'2' => array(
				'alt' => 'Breadcrumbs Type 2',
				'img' => PORTO_OPTIONS_URI . '/breadcrumbs/breadcrumbs_02.jpg',
			),
			'3' => array(
				'alt' => 'Breadcrumbs Type 3',
				'img' => PORTO_OPTIONS_URI . '/breadcrumbs/breadcrumbs_03.jpg',
			),
			'4' => array(
				'alt' => 'Breadcrumbs Type 4',
				'img' => PORTO_OPTIONS_URI . '/breadcrumbs/breadcrumbs_04.jpg',
			),
			'5' => array(
				'alt' => 'Breadcrumbs Type 5',
				'img' => PORTO_OPTIONS_URI . '/breadcrumbs/breadcrumbs_05.jpg',
			),
			'6' => array(
				'alt' => 'Breadcrumbs Type 6',
				'img' => PORTO_OPTIONS_URI . '/breadcrumbs/breadcrumbs_06.jpg',
			),
			'7' => array(
				'alt' => 'Breadcrumbs Type 7',
				'img' => PORTO_OPTIONS_URI . '/breadcrumbs/breadcrumbs_07.jpg',
			),
		);
	}
endif;

if ( ! function_exists( 'porto_options_footer_columns' ) ) :
	function porto_options_footer_columns() {
		return array(
			'1'  => __( '1 column - 1/12', 'porto' ),
			'2'  => __( '2 columns - 1/6', 'porto' ),
			'3'  => __( '3 columns - 1/4', 'porto' ),
			'4'  => __( '4 columns - 1/3', 'porto' ),
			'5'  => __( '5 columns - 5/12', 'porto' ),
			'6'  => __( '6 columns - 1/2', 'porto' ),
			'7'  => __( '7 columns - 7/12', 'porto' ),
			'8'  => __( '8 columns - 2/3', 'porto' ),
			'9'  => __( '9 columns - 3/4', 'porto' ),
			'10' => __( '10 columns - 5/6', 'porto' ),
			'11' => __( '11 columns - 11/12)', 'porto' ),
			'12' => __( '12 columns - 1/1', 'porto' ),
		);
	}
endif;
