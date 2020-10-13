<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

global $gm_supported_module;
$theme_search_additional = array();
if ( ! empty( $gm_supported_module['search_post_type_additional'] ) && is_array( $gm_supported_module['search_post_type_additional'] ) ) {
	$theme_search_additional = $gm_supported_module['search_post_type_additional'];
}

$default_arr = array( 'default' => '--- ' . esc_html__( 'Same as desktop', 'groovy-menu' ) . ' ---' );
$none_arr    = array( 'none' => '--- ' . esc_html__( 'Hide Groovy menu', 'groovy-menu' ) . ' ---' );
$nav_menus   = $default_arr + $none_arr + GroovyMenuUtils::getNavMenus();

$default_arr              = array( 0 => '--- ' . esc_html__( 'empty', 'groovy-menu' ) . ' ---' );
$gm_menu_block_posts_list = $default_arr + GroovyMenuUtils::getMenuBlockPostsList();


return array(
	'general' => array(
		'title'  => esc_html__( 'General', 'groovy-menu' ),
		'icon'   => 'gm-icon-music-mixer',
		'fields' => array(
			'general_group'                                => array(
				'title'     => esc_html__( 'General settings', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'header'                                       => array(
				'title'       => esc_html__( 'Groovy menu types', 'groovy-menu' ),
				'description' => esc_html__( 'By applying this option you can choose your menu style, elements align and toggle toolbar.', 'groovy-menu' ),
				'type'        => 'header',
				'default'     => '{\"align\":\"left\", \"style\":1, \"toolbar\":\"false\"}',
			),
			'top_lvl_link_align'                           => array(
				'title'     => esc_html__( 'Top level links align', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'left'   => esc_html__( 'Align left', 'groovy-menu' ),
					'center' => esc_html__( 'Align center', 'groovy-menu' ),
					'right'  => esc_html__( 'Align right', 'groovy-menu' ),
				),
				'default'   => 'right',
				'condition' => array(
					array( 'header.style', 'in', array( '1' ) ),
					array( 'header.align', 'in', array( 'left', 'right' ) ),
				)
			),
			'top_lvl_link_center_considering_logo'         => array(
				'title'       => esc_html__( 'Top level links with align center must considering logo width.', 'groovy-menu' ),
				'description' => esc_html__( 'Calculation of the center position for nav including the width of the logo.', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => false,
				'condition'   => array(
					array( 'header.style', 'in', array( '1' ) ),
					array( 'header.align', 'in', array( 'left', 'right' ) ),
					array( 'top_lvl_link_align', '==', 'center' ),
					array( 'logo_type', 'in', array( 'img', 'text' ) ),
				)
			),
			'gap_between_logo_and_links'                   => array(
				'title'     => esc_html__( 'Distance between logo and nav links', 'groovy-menu' ),
				'type'      => 'number',
				'default'   => 40,
				'range'     => array( 0, 1000 ),
				'unit'      => 'px',
				'condition' => array(
					array( 'header.style', 'in', array( '1' ) ),
					array( 'header.align', 'in', array( 'left', 'right' ) ),
					array( 'top_lvl_link_align', '==', 'left' ),
					array( 'logo_type', 'in', array( 'img', 'text' ) ),
				)
			),
			'overlap'                                      => array(
				'title'       => esc_html__( 'Enable Groovy menu to overlap the first block in the page', 'groovy-menu' ),
				'description' => esc_html__( 'This option will make menu overlap the first block in the page, switch it if you create a transparent menu (works with classic and minimalistic menu types or for mobile resolution).', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => false,
			),
			'header_height'                                => array(
				'title'       => esc_html__( 'Menu height', 'groovy-menu' ),
				'description' => esc_html__( 'You can change menu height using this option. (by default:100px).', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => 100,
				'range'       => array( 40, 250 ),
				'unit'        => 'px',
			),
			'header_height_sticky'                         => array(
				'title'       => esc_html__( 'Sticky menu height', 'groovy-menu' ),
				'description' => esc_html__( 'Using this option you can decide how long header should be on sticky state. (by default:50px).', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => 50,
				'range'       => array( 40, 250 ),
				'unit'        => 'px',
				'condition'   => array( 'sticky_header', 'in', array( 'slide-down', 'fixed-sticky' ) ),
			),
			'minimalistic_menu_open_type'                  => array(
				'title'     => esc_html__( 'Minimalistic menu open type', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'offcanvasSlideLeft'       => esc_html__( 'Offcanvas slide from the left', 'groovy-menu' ),
					'offcanvasSlideRight'      => esc_html__( 'Offcanvas slide from the right', 'groovy-menu' ),
					'offcanvasSlideSlide'      => esc_html__( 'Slide menu and container from the left', 'groovy-menu' ),
					'offcanvasSlideSlideRight' => esc_html__( 'Slide menu and container from the right', 'groovy-menu' ),
				),
				'default'   => 'offcanvasSlideRight',
				'condition' => array(
					array( 'header.style', 'in', array( '2' ) )
				)
			),
			'canvas_container_width_type'                  => array(
				'title'   => esc_html__( 'Menu canvas and container width', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'canvas-boxed-container-boxed' => esc_html__( 'Canvas and container boxed', 'groovy-menu' ),
					'canvas-wide-container-boxed'  => esc_html__( 'Canvas wide - container boxed', 'groovy-menu' ),
					'canvas-wide-container-wide'   => esc_html__( 'Canvas and container wide', 'groovy-menu' ),
				),
				'default' => 'canvas-wide-container-boxed',
			),
			'canvas_wide_container_wide_padding'           => array(
				'title'     => esc_html__( 'Canvas and container wide right/left padding', 'groovy-menu' ),
				'condition' => array( 'canvas_container_width_type', '==', 'canvas-wide-container-wide' ),
				'type'      => 'number',
				'default'   => 15,
				'range'     => array( 0, 2000 ),
				'unit'      => 'px',
			),
			'canvas_boxed_container_boxed_width'           => array(
				'title'       => esc_html__( 'Canvas and container boxed width', 'groovy-menu' ),
				'condition'   => array( 'canvas_container_width_type', '==', 'canvas-boxed-container-boxed' ),
				'description' => esc_html__( 'Note that container will be 30px narrower (because of left/right 15px padding) than canvas. (default canvas width is 1200px).', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => 1200,
				'range'       => array( 900, 2000 ),
				'unit'        => 'px',
			),
			'canvas_wide_container_boxed_width'            => array(
				'title'       => esc_html__( 'Canvas wide - container boxed width', 'groovy-menu' ),
				'condition'   => array( 'canvas_container_width_type', '==', 'canvas-wide-container-boxed' ),
				'description' => esc_html__( 'Container width by default is 1200px.', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => 1200,
				'range'       => array( 900, 2000 ),
				'unit'        => 'px',
			),
			'sticky_header_start'                          => array(
				'title'       => esc_html__( 'Sticky menu behavior', 'groovy-menu' ),
				'description' => esc_html__( "By applying this option you can define how you would like the menu to be transformed from normal state to the sticky one. If &quot;Slide Down&quot; is selected, you can choose the offset location where the sticky menu will be revealed while scrolling down.", 'groovy-menu' ),
				'type'        => 'inlineStart',
			),
			'sticky_header'                                => array(
				'title'     => esc_html__( 'Desktop', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'disable-sticky-header' => esc_html__( 'Disable sticky header', 'groovy-menu' ),
					'fixed-sticky'          => esc_html__( 'Fixed Sticky', 'groovy-menu' ),
					'slide-down'            => esc_html__( 'Slide Down', 'groovy-menu' ),
				),
				'default'   => 'disable-sticky-header',
				'condition' => array( 'header.style', 'in', array( '1', '2' ) ),
			),
			'sticky_header_mobile'                         => array(
				'title'   => esc_html__( 'Mobile', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'disable-sticky-header' => esc_html__( 'Disable sticky header', 'groovy-menu' ),
					'fixed-sticky'          => esc_html__( 'Fixed Sticky', 'groovy-menu' ),
					'slide-down'            => esc_html__( 'Slide Down', 'groovy-menu' )
				),
				'default' => 'disable-sticky-header',
			),
			'sticky_header_end'                            => array(
				'type' => 'inlineEnd'
			),
			'sticky_offset_start'                          => array(
				'title'          => esc_html__( 'Sticky "slide down" menu offset', 'groovy-menu' ),
				'description'    => esc_html__( 'Set this option to decide when the sticky state of header will trigger.', 'groovy-menu' ),
				'type'           => 'inlineStart',
				'condition'      => array(
					array( 'sticky_header', '==', 'slide-down' ),
					array( 'sticky_header_mobile', '==', 'slide-down' ),
				),
				'condition_type' => 'OR',
			),
			'sticky_offset'                                => array(
				'title'     => esc_html__( 'Desktop', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					''     => 'Menu height',
					'20%'  => esc_html__( '20% Of Viewport', 'groovy-menu' ),
					'30%'  => esc_html__( '30% Of Viewport', 'groovy-menu' ),
					'40%'  => esc_html__( '40% Of Viewport', 'groovy-menu' ),
					'50%'  => esc_html__( '50% Of Viewport', 'groovy-menu' ),
					'60%'  => esc_html__( '60% Of Viewport', 'groovy-menu' ),
					'70%'  => esc_html__( '70% Of Viewport', 'groovy-menu' ),
					'80%'  => esc_html__( '80% Of Viewport', 'groovy-menu' ),
					'90%'  => esc_html__( '90% Of Viewport', 'groovy-menu' ),
					'100%' => esc_html__( '100% Of Viewport', 'groovy-menu' ),
				),
				'default'   => '',
				'condition' => array(
					array( 'header.style', 'in', array( '1', '2' ) ),
					array( 'sticky_header', '==', 'slide-down' ),
				)
			),
			'sticky_offset_mobile'                         => array(
				'title'     => esc_html__( 'Mobile', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					''     => 'Menu height',
					'20%'  => esc_html__( '20% Of Viewport', 'groovy-menu' ),
					'30%'  => esc_html__( '30% Of Viewport', 'groovy-menu' ),
					'40%'  => esc_html__( '40% Of Viewport', 'groovy-menu' ),
					'50%'  => esc_html__( '50% Of Viewport', 'groovy-menu' ),
					'60%'  => esc_html__( '60% Of Viewport', 'groovy-menu' ),
					'70%'  => esc_html__( '70% Of Viewport', 'groovy-menu' ),
					'80%'  => esc_html__( '80% Of Viewport', 'groovy-menu' ),
					'90%'  => esc_html__( '90% Of Viewport', 'groovy-menu' ),
					'100%' => esc_html__( '100% Of Viewport', 'groovy-menu' ),
				),
				'default'   => '',
				'condition' => array(
					array( 'sticky_header_mobile', 'in', array( 'slide-down' ) )
				)
			),
			'sticky_offset_end'                            => array(
				'type' => 'inlineEnd'
			),
			'sticky_toolbar'                               => array(
				'title'       => esc_html__( 'Sticky toolbar.', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
				'condition'   => array(
					array( 'header.style', 'in', array( '1', '2' ) ),
					array( 'header.toolbar', 'in', array( 'true', '1' ) ),
				)
			),
			'woocommerce_cart_start'                       => array(
				'title' => esc_html__( 'Show WooCommerce minicart', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'woocommerce_cart'                             => array(
				'title'       => esc_html__( 'Toggle switch to show/hide WooCommerce minicart', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
			),
			'woocommerce_cart_end'                         => array(
				'type' => 'inlineEnd'
			),
			'woocommerce_cart_icon_start'                  => array(
				'title'     => esc_html__( 'WooCommerce minicart icon size', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'woocommerce_cart', '==', true ),
			),
			'woocommerce_cart_icon_size_desktop'           => array(
				'title'       => esc_html__( 'Desktop icon size', 'groovy-menu' ),
				'description' => '',
				'type'        => 'number',
				'default'     => 16,
				'range'       => array( 10, 50 ),
				'unit'        => 'px',
			),
			'woocommerce_cart_icon_size_mobile'            => array(
				'title'       => esc_html__( 'Mobile icon size', 'groovy-menu' ),
				'description' => '',
				'type'        => 'number',
				'default'     => 17,
				'range'       => array( 10, 50 ),
				'unit'        => 'px',
			),
			'woocommerce_cart_icon_end'                    => array(
				'title' => esc_html__( 'wooCommerce cart icon size', 'groovy-menu' ),
				'type'  => 'inlineEnd'
			),
			'show_wpml_start'                              => array(
				'title'       => esc_html__( 'Show WPML language switcher', 'groovy-menu' ),
				'description' => esc_html__( 'Note: switcher will be displayed only if WPML plugin is installed and activated.', 'groovy-menu' ),
				'type'        => 'inlineStart',
			),
			'show_wpml'                                    => array(
				'title'       => esc_html__( 'Toggle switch to show/hide WPML language switcher', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
			),
			'show_wpml_end'                                => array(
				'type' => 'inlineEnd'
			),
			'show_wpml_icon_start'                         => array(
				'title'     => esc_html__( 'WPML language switcher icon size', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'show_wpml', '==', true ),
			),
			'show_wpml_icon_size_desktop'                  => array(
				'title'       => esc_html__( 'Desktop icon size', 'groovy-menu' ),
				'description' => '',
				'type'        => 'number',
				'default'     => 18,
				'range'       => array( 10, 50 ),
				'unit'        => 'px',
			),
			'show_wpml_icon_size_mobile'                   => array(
				'title'       => esc_html__( 'Mobile icon size', 'groovy-menu' ),
				'description' => '',
				'type'        => 'number',
				'default'     => 18,
				'range'       => array( 10, 50 ),
				'unit'        => 'px',
			),
			'show_wpml_icon_end'                           => array(
				'type' => 'inlineEnd'
			),
			'caret'                                        => array(
				'title'       => esc_html__( 'Show caret at the top level', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => true,
			),
			'show_divider'                                 => array(
				'title'       => esc_html__( 'Show divider between navigation and search/cart', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
				'condition'   => array(
					array( 'header.style', 'in', array( '1' ) )
				),
			),
			'show_divider_between_menu_links_start'        => array(
				'title'     => esc_html__( 'Show divider between menu links', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'show_divider_between_menu_links'              => array(
				'title'       => esc_html__( 'Show', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
				'condition'   => array(
					array( 'header.style', 'in', array( '1' ) )
				),
			),
			'show_divider_between_menu_links_wide'         => array(
				'title'       => esc_html__( 'Stretch to all menu height', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
				'condition'   => array(
					array( 'header.style', 'in', array( '1' ) ),
					array( 'show_divider_between_menu_links', '==', true ),
				),
			),
			'show_divider_between_menu_links_end'          => array(
				'type'      => 'inlineEnd',
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'show_top_lvl_and_submenu_icons'               => array(
				'title'       => esc_html__( 'Hide icons at top level menu and submenu',
					'groovy-menu' ),
				'type'        => 'checkbox',
				'description' => esc_html__( 'You can switch between displaying or hiding icons added to menu items from &quot;Appearance > menus&quot;.', 'groovy-menu' ),
				'default'     => false,
			),
			'menu_z_index'                                 => array(
				'title'       => esc_html__( 'Menu z-index', 'groovy-menu' ),
				'description' => esc_html__( 'Set the z-index to ensure the menu are higher than other site content.', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => 9999,
				'range'       => array( 1, 2147483600 ),
				'unit'        => '',
			),
			'submenu_group'                                => array(
				'title'     => esc_html__( 'Submenu', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'show_submenu'                                 => array(
				'title'       => esc_html__( 'Show submenu', 'groovy-menu' ),
				'description' => esc_html__( 'Select behavior of opening the submenu.', 'groovy-menu' ),
				'type'        => 'select',
				'options'     => array(
					'hover' => esc_html__( 'on hover', 'groovy-menu' ),
					'click' => esc_html__( 'on click', 'groovy-menu' ),
				),
				'default'     => 'hover',
			),
			'sub_level_width'                              => array(
				'title'       => esc_html__( 'Submenu width', 'groovy-menu' ),
				'description' => esc_html__( 'By applying this option you can set width of submenu excluding mega menu. Note: this option works with default and icon menu types.', 'groovy-menu' ),
				'type'        => 'number',
				'range'       => array( 60, 600 ),
				'default'     => 230,
				'unit'        => 'px',
				'condition'   => array( 'header.style', 'in', array( '1', '2', '3', '4' ) ),
			),
			'hide_dropdown_bg'                             => array(
				'title'       => esc_html__( 'Hide background image at submenu', 'groovy-menu' ),
				'description' => esc_html__( 'You can hide background image at submenu settings in &quot;Appearance > Menus&quot;.', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => false,
			),
			'icon_submenu_border_start'                    => array(
				'title'     => esc_html__( 'Top level link bottom border', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_submenu_border_top_thickness'       => array(
				'title'     => esc_html__( 'thickness', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 20 ),
				'default'   => 1,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_submenu_border_top_style'           => array(
				'title'     => esc_html__( 'style', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
				),
				'default'   => 'dotted',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_submenu_border_top_color'           => array(
				'title'     => esc_html__( 'color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(74,74,76,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_submenu_border_end'                      => array(
				'type'      => 'inlineEnd',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_first_submenu_active_link_color'    => array(
				'title'     => esc_html__( 'Submenu active link color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(255,255,255,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'sidebar_menu_first_submenu_bg_color'          => array(
				'title'     => esc_html__( 'First level submenu background color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(0,0,0,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '3', '5' ) ),
			),
			'sidebar_menu_next_submenu_bg_color'           => array(
				'title'     => esc_html__( 'Next level submenu background color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(0,0,0,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '3', '5' ) ),
			),
			'minimalistic_menu_first_submenu_bg_color'     => array(
				'title'     => esc_html__( 'First level submenu background color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(0,0,0,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '2' ) ),
			),
			'minimalistic_menu_next_submenu_bg_color'      => array(
				'title'     => esc_html__( 'Next level submenu background color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(0,0,0,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '2' ) ),
			),
			'dropdown_hover_style'                         => array(
				'title'     => esc_html__( 'Submenu hover style', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'default'     => esc_html__( 'Default', 'groovy-menu' ),
					'shift-right' => esc_html__( 'Shift right', 'groovy-menu' ),
				),
				'default'   => 'default',
				'condition' => array( 'header.style', 'in', array( '1', '3', '4', '5' ) ),
			),
			'dropdown_appearance_style'                    => array(
				'title'     => esc_html__( 'Submenu appearance style', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'default'              => esc_html__( 'Default', 'groovy-menu' ),
					'fade-in-out'          => esc_html__( 'Fade in out', 'groovy-menu' ),
					'animate-from-bottom'  => esc_html__( 'Animate from bottom', 'groovy-menu' ),
					'animate-with-scaling' => esc_html__( 'Animate with scaling', 'groovy-menu' ),
				),
				'default'   => 'default',
				'condition' => array( 'header.style', 'in', array( '1', '3', '4', '5' ) ),
			),
			'submenu_border_start'                         => array(
				'title' => esc_html__( 'Submenu bottom border', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'submenu_border_style'                         => array(
				'title'   => esc_html__( 'style', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
				),
				'default' => 'dotted',
			),
			'submenu_border_thickness'                     => array(
				'title'   => esc_html__( 'thickness', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 1,
				'unit'    => 'px',
			),
			'submenu_border_color'                         => array(
				'title'       => esc_html__( 'color', 'groovy-menu' ),
				'description' => '',
				'type'        => 'colorpicker',
				'default'     => 'rgba(110, 110, 111, 1)',
				'alpha'       => true,
			),
			'submenu_border_end'                           => array(
				'type' => 'inlineEnd'
			),
			'sub_level_border_top_color_start'             => array(
				'title' => esc_html__( 'Submenu box border top color', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'sub_level_border_top_style'                   => array(
				'title'   => esc_html__( 'style', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
				),
				'default' => 'solid',
			),
			'sub_level_border_top_thickness'               => array(
				'title'   => esc_html__( 'thickness', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 3,
				'unit'    => 'px',
			),
			'sub_level_border_top_color'                   => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#93cb52',
				'alpha'   => true,
			),
			'sub_level_border_top_color_end'               => array(
				'type' => 'inlineEnd'
			),
			'sub_level_border_top_shift'                   => array(
				'title'       => esc_html__( 'Shift down the submenu at the height of the top border thickness', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => true,
				'condition'   => array( 'header.style', 'in', array( '1', '4' ) ),
			),
			'sub_level_text_color'                         => array(
				'title'   => esc_html__( 'Submenu text color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#6e6e6f',
				'alpha'   => true,
			),
			'sub_level_text_color_hover'                   => array(
				'title'   => esc_html__( 'Submenu text hover color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#93cb52',
				'alpha'   => true,
			),
			'sub_level_text_active_color'                  => array(
				'title'   => esc_html__( 'Submenu active link text color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#93cb52',
				'alpha'   => true,
			),
			'sub_level_background_color'                   => array(
				'title'   => esc_html__( 'Submenu background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#202022',
				'alpha'   => true,
			),
			'sub_level_background_color_hover'             => array(
				'title'   => esc_html__( 'Submenu item hover background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => null,
				'alpha'   => true,
			),
			'sub_item_text_start'                          => array(
				'title' => esc_html__( 'Submenu text', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'sub_level_item_text_size'                     => array(
				'title'   => esc_html__( 'Size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 10, 50 ),
				'default' => 11,
				'unit'    => 'px',
			),
			'sub_level_item_text_case'                     => array(
				'title'   => esc_html__( 'Case', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					''           => esc_html__( 'Select value', 'groovy-menu' ),
					'none'       => esc_html__( 'Default', 'groovy-menu' ),
					'uppercase'  => esc_html__( 'Uppercase', 'groovy-menu' ),
					'capitalize' => esc_html__( 'Capitalize', 'groovy-menu' ),
					'lowercase'  => esc_html__( 'Lowercase', 'groovy-menu' ),
				),
				'default' => 'uppercase',
			),
			'sub_level_item_text_weight'                   => array(
				'title'   => esc_html__( 'Font variant', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none' => 'Select value'
				),
				'default' => 'none',
			),
			'sub_level_item_text_subset'                   => array(
				'title'   => esc_html__( 'Subset', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none' => 'Select value'
				),
				'default' => 'none',
			),
			'sub_item_letter_spacing'                      => array(
				'title'   => esc_html__( 'Letter spacing', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'sub_item_text_end'                            => array(
				'type' => 'inlineEnd'
			),
			'sub_dropdown_radius_start'                    => array(
				'title'     => esc_html__( 'Dropdown radius', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'header.style', 'in', array( '1', '4' ) ),
			),
			'sub_dropdown_radius'                          => array(
				'title'       => esc_html__( 'switch', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
				'condition'   => array( 'header.style', 'in', array( '1', '4' ) ),
			),
			'sub_dropdown_radius_1'                        => array(
				'title'     => esc_html__( 'top left', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array(
					array( 'header.style', 'in', array( '1', '4' ) ),
					array( 'sub_dropdown_radius', '==', true ),
				),
			),
			'sub_dropdown_radius_2'                        => array(
				'title'     => esc_html__( 'top right', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array(
					array( 'header.style', 'in', array( '1', '4' ) ),
					array( 'sub_dropdown_radius', '==', true ),
				),
			),
			'sub_dropdown_radius_4'                        => array(
				'title'     => esc_html__( 'bottom left', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array(
					array( 'header.style', 'in', array( '1', '4' ) ),
					array( 'sub_dropdown_radius', '==', true ),
				),
			),
			'sub_dropdown_radius_3'                        => array(
				'title'     => esc_html__( 'bottom right', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array(
					array( 'header.style', 'in', array( '1', '4' ) ),
					array( 'sub_dropdown_radius', '==', true ),
				),
			),
			'sub_dropdown_radius_end'                      => array(
				'type'      => 'inlineEnd',
				'condition' => array( 'header.style', 'in', array( '1', '4' ) ),
			),
			'dropdown_margin'                              => array(
				'title'       => esc_html__( 'Dropdown gap', 'groovy-menu' ),
				'description' => esc_html__( 'Gap between Main menu and Dropdown menu', 'groovy-menu' ),
				'type'        => 'number',
				'range'       => array( 0, 100 ),
				'default'     => 0,
				'unit'        => 'px',
				'condition'   => array( 'header.style', 'in', array( '1' ) ),
			),
			'sub_dropdown_margin'                          => array(
				'title'       => esc_html__( 'Submenu gap', 'groovy-menu' ),
				'description' => esc_html__( 'Gap beside Dropdown menu and Submenu', 'groovy-menu' ),
				'type'        => 'number',
				'range'       => array( 0, 100 ),
				'default'     => 0,
				'unit'        => 'px',
				'condition'   => array( 'header.style', 'in', array( '1' ) ),
			),
			'megamenu_group'                               => array(
				'title'     => esc_html__( 'Mega menu', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'megamenu_title_text_start'                    => array(
				'title'     => esc_html__( 'Mega menu title text', 'groovy-menu' ),
				'condition' => array( 'header.style', 'in', array( '1' ) ),
				'type'      => 'inlineStart',
			),
			'megamenu_title_text_size'                     => array(
				'title'     => esc_html__( 'Size', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 10, 50 ),
				'default'   => 13,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'megamenu_title_text_case'                     => array(
				'title'     => esc_html__( 'Case', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					''           => esc_html__( 'Select value', 'groovy-menu' ),
					'none'       => esc_html__( 'Default', 'groovy-menu' ),
					'uppercase'  => esc_html__( 'Uppercase', 'groovy-menu' ),
					'capitalize' => esc_html__( 'Capitalize', 'groovy-menu' ),
					'lowercase'  => esc_html__( 'Lowercase', 'groovy-menu' ),
				),
				'default'   => 'uppercase',
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'megamenu_title_text_weight'                   => array(
				'title'     => esc_html__( 'Font variant', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'none' => 'Select value'
				),
				'default'   => 'none',
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'megamenu_title_text_subset'                   => array(
				'title'     => esc_html__( 'Subset', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'none' => 'Select value'
				),
				'default'   => 'none',
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'menu_title_letter_spacing'                    => array(
				'title'     => esc_html__( 'Letter spacing', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 5 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'megamenu_title_text_end'                      => array(
				'type' => 'inlineEnd'
			),
			'megamenu_title_as_link'                       => array(
				'title'       => esc_html__( 'Show Mega Menu titles as a regular menu items', 'groovy-menu' ),
				'description' => esc_html__( 'For sub mega menu items.', 'groovy-menu' ) . ' ' . esc_html__( 'Show with links and badges.', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => false,
			),
			'menu_title_color'                             => array(
				'title'     => esc_html__( 'Mega menu title text color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#ffffff',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'mega_menu_canvas_container_width_type'        => array(
				'title'     => esc_html__( 'Mega menu canvas and container width', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'mega-menu-canvas-boxed-container-boxed' => esc_html__( 'Mega menu canvas and container boxed', 'groovy-menu' ),
					'mega-menu-canvas-wide-container-boxed'  => esc_html__( 'Mega menu canvas wide - container boxed', 'groovy-menu' ),
					'mega-menu-canvas-wide-container-wide'   => esc_html__( 'Mega menu canvas and container wide', 'groovy-menu' ),
				),
				'default'   => 'mega-menu-canvas-boxed-container-boxed',
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'mega_menu_canvas_boxed_container_boxed_width' => array(
				'title'       => esc_html__( 'Mega menu canvas and container boxed width', 'groovy-menu' ),
				'condition'   => array(
					array(
						'mega_menu_canvas_container_width_type',
						'==',
						'mega-menu-canvas-boxed-container-boxed',
					),
					array( 'header.style', 'in', array( '1' ) ),
				),
				'description' => esc_html__( 'Note that container will be 30px narrower than canvas. (canvas width by default is 1200px).', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => 1200,
				'range'       => array( 100, 2000 ),
				'unit'        => 'px',
			),
			'mega_menu_canvas_wide_container_boxed_width'  => array(
				'title'       => esc_html__( 'Mega menu canvas wide - container boxed width', 'groovy-menu' ),
				'condition'   => array(
					array(
						'mega_menu_canvas_container_width_type',
						'==',
						'mega-menu-canvas-wide-container-boxed',
					),
					array( 'header.style', 'in', array( '1' ) ),
				),
				'description' => esc_html__( 'Default container width is 1200px.', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => 1200,
				'range'       => array( 900, 2000 ),
				'unit'        => 'px',
			),
			'megamenu_background_color_custom'             => array(
				'title'     => esc_html__( 'Custom Mega menu background color', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => false,
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'megamenu_background_color'                    => array(
				'title'     => esc_html__( 'Mega menu background color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#202022',
				'alpha'     => true,
				'condition' => array(
					array( 'megamenu_background_color_custom', '==', true, ),
					array( 'header.style', 'in', array( '1' ) ),
				),
			),
			'megamenu_column_padding'                      => array(
				'title'     => esc_html__( 'Add Mega menu columns padding', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => true,
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'mega_menu_divider_color'                      => array(
				'title'     => esc_html__( 'Mega menu columns divider color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(255,255,255,0)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'mega_menu_show_links_bottom_border'           => array(
				'title'     => esc_html__( 'Mega menu links bottom border', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => false,
				'condition' => array( 'header.style', 'in', array( '1' ) ),
			),
			'mega_menu_dropdown_margin'                    => array(
				'title'       => esc_html__( 'Mega menu gap', 'groovy-menu' ),
				'description' => esc_html__( 'Gap between Main menu and Mega menu', 'groovy-menu' ),
				'type'        => 'number',
				'range'       => array( 0, 100 ),
				'default'     => 0,
				'unit'        => 'px',
				'condition'   => array( 'header.style', 'in', array( '1' ) ),
			),
			'search_group'                                 => array(
				'title'     => esc_html__( 'Search', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'search_form_start'                            => array(
				'title' => esc_html__( 'Search form type', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'search_form'                                  => array(
				'title'       => esc_html__( 'Select a type of the search form', 'groovy-menu' ),
				'description' => '',
				'type'        => 'select',
				'options'     => array(
					'dropdown-without-ajax' => array(
						'title'     => esc_html__( 'Dropdown', 'groovy-menu' ),
						'condition' => array( 'header.style', 'in', array( '1' ) )
					),
					'fullscreen'            => esc_html__( 'Fullscreen', 'groovy-menu' ),
					'custom'                => esc_html__( 'Custom', 'groovy-menu' ),
					'disable'               => esc_html__( 'Disable', 'groovy-menu' ),
				),
				'default'     => 'fullscreen',
			),
			'search_form_from'                             => array(
				'title'       => esc_html__( 'Filter search result by', 'groovy-menu' ),
				'description' => '',
				'type'        => 'select',
				'options'     => array_merge( array(
					'all' => array(
						'title'     => esc_html__( 'Search in all post types', 'groovy-menu' ),
						'condition' => array( 'search_form', 'in', array( 'fullscreen', 'dropdown-without-ajax' ) )
					),
				), GroovyMenuUtils::getPostTypesForSearch() ),
				'default'     => 'all',
				'condition'   => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen' ) )
				),
			),
			'search_form_custom_wrapper'                   => array(
				'title'       => esc_html__( 'Custom wrapper', 'groovy-menu' ),
				'description' => '',
				'type'        => 'select',
				'options'     => array(
					'fullscreen' => esc_html__( 'Fullscreen', 'groovy-menu' ),
					'dropdown'   => esc_html__( 'Dropdown', 'groovy-menu' ),
				),
				'default'     => 'fullscreen',
				'condition'   => array(
					array( 'search_form', 'in', array( 'custom' ) ),
					array( 'header.style', 'in', array( '1' ) )
				),
			),
			'search_form_end'                              => array(
				'type' => 'inlineEnd'
			),
			'search_form_custom_id'                        => array(
				'title'       => esc_html__( 'Select Menu Block for search wrapper', 'groovy-menu' ),
				'description' => esc_html__( 'The content of the selected menu block will be displayed in the search form', 'groovy-menu' ),
				'type'        => 'select',
				'options'     => $gm_menu_block_posts_list,
				'default'     => 'fullscreen',
				'condition'   => array(
					array( 'search_form', 'in', array( 'custom' ) )
				),
			),
			'search_form_custom_show_default'              => array(
				'title'       => esc_html__( 'Show default search form?', 'groovy-menu' ),
				'description' => esc_html__( 'Adds a standard search form to the block menu', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => true,
				'condition'   => array(
					array( 'search_form', 'in', array( 'custom' ) )
				),
			),
			'search_form_fullscreen_background'            => array(
				'title'       => esc_html__( 'Canvas background color for fullscreen search wrapper', 'groovy-menu' ),
				'description' => esc_html__( 'Also applicable for mobile screen resolutions', 'groovy-menu' ),
				'type'        => 'colorpicker',
				'default'     => 'rgba(0,0,0,0.85)',
				'alpha'       => true,
				'condition'   => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_icon_start'                       => array(
				'title'     => esc_html__( 'Search form icon size', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'search_form', 'in', array( 'fullscreen', 'dropdown-without-ajax', 'custom' ) ),
			),
			'search_form_icon_size_desktop'                => array(
				'title'       => esc_html__( 'Desktop icon size', 'groovy-menu' ),
				'description' => '',
				'type'        => 'number',
				'default'     => 17,
				'range'       => array( 10, 50 ),
				'unit'        => 'px',
			),
			'search_form_icon_size_mobile'                 => array(
				'title'       => esc_html__( 'Mobile icon size', 'groovy-menu' ),
				'description' => '',
				'type'        => 'number',
				'default'     => 17,
				'range'       => array( 10, 50 ),
				'unit'        => 'px',
			),
			'search_form_icon_end'                         => array(
				'type' => 'inlineEnd'
			),
			'search_form_icon_color_start'                 => array(
				'title'     => esc_html__( 'Opened search form', 'groovy-menu' ) . ': ' . esc_html__( 'Search icon color', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) ),
			),
			'search_form_icon_color'                       => array(
				'title'       => esc_html__( 'color', 'groovy-menu' ),
				'description' => '',
				'type'        => 'colorpicker',
				'default'     => '#ffffff',
				'alpha'       => true,
				'condition'   => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_icon_color_hover'                 => array(
				'title'       => esc_html__( 'on hover', 'groovy-menu' ),
				'description' => '',
				'type'        => 'colorpicker',
				'default'     => '#ffffff',
				'alpha'       => true,
				'condition'   => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_icon_color_end'                   => array(
				'type' => 'inlineEnd'
			),
			'search_form_close_icon_color'                 => array(
				'title'       => esc_html__( 'Opened search form', 'groovy-menu' ) . ': ' . esc_html__( 'Fullscreen close icon color', 'groovy-menu' ),
				'description' => '',
				'type'        => 'colorpicker',
				'default'     => '#ffffff',
				'alpha'       => true,
				'condition'   => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_side_border_start'                => array(
				'title'       => esc_html__( 'Opened search form', 'groovy-menu' ) . ': ' . esc_html__( 'Side border', 'groovy-menu' ),
				'description' => '',
				'type'        => 'inlineStart',
				'condition'   => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_side_border_thickness'            => array(
				'title'     => esc_html__( 'thickness', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 20 ),
				'default'   => 4,
				'unit'      => 'px',
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_side_border_style'                => array(
				'title'     => esc_html__( 'style', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
				),
				'default'   => 'solid',
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_side_border_color'                => array(
				'title'     => esc_html__( 'color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#454545',
				'alpha'     => true,
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_side_border_end'                  => array(
				'type'      => 'inlineEnd',
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_input_field_colors_start'         => array(
				'title'       => esc_html__( 'Opened search form', 'groovy-menu' ) . ': ' . esc_html__( 'Input field colors', 'groovy-menu' ),
				'description' => '',
				'type'        => 'inlineStart',
				'condition'   => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_input_field_background'           => array(
				'title'     => esc_html__( 'background', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(255,255,255,0)',
				'alpha'     => true,
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_input_field_color'                => array(
				'title'     => esc_html__( 'text color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#909090',
				'alpha'     => true,
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_input_field_colors_end'           => array(
				'type'      => 'inlineEnd',
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'fullscreen', 'custom' ) )
				),
			),
			'search_form_dropdown_background'              => array(
				'title'       => esc_html__( 'Opened search form', 'groovy-menu' ) . ': ' . esc_html__( 'Dropdown background', 'groovy-menu' ),
				'description' => '',
				'type'        => 'colorpicker',
				'default'     => '#ffffff',
				'alpha'       => true,
				'condition'   => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'custom' ) )
				),
			),
			'search_form_dropdown_btn_start'               => array(
				'title'       => esc_html__( 'Opened search form', 'groovy-menu' ) . ': ' . esc_html__( 'Dropdown search button', 'groovy-menu' ),
				'description' => '',
				'type'        => 'inlineStart',
				'condition'   => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'custom' ) )
				),
			),
			'search_form_dropdown_btn_background'          => array(
				'title'     => esc_html__( 'background', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#393b3f',
				'alpha'     => true,
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'custom' ) )
				),
			),
			'search_form_dropdown_btn_hover'               => array(
				'title'     => esc_html__( 'hover background', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#57ab55',
				'alpha'     => true,
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'custom' ) )
				),
			),
			'search_form_dropdown_btn_end'                 => array(
				'type'      => 'inlineEnd',
				'condition' => array(
					array( 'search_form', 'in', array( 'dropdown-without-ajax', 'custom' ) )
				),
			),
			'logo_group'                                   => array(
				'title'     => esc_html__( 'Logo', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'logo_type'                                    => array(
				'title'   => esc_html__( 'Logo type', 'groovy-menu' ),
				'type'    => 'logoType',
				'default' => 'text',
				'options' => array(
					'img'  => 'image',
					'text' => 'text',
					'no'   => 'no',
				),
			),
			'logo_responsive'                              => array(
				'title'       => esc_html__( 'Enable fit logotype to the sidebar area', 'groovy-menu' ),
				'description' => esc_html__( 'It apply for desktop resolution', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => false,
				'condition'   => array(
					array( 'logo_type', '==', 'img' ),
					array( 'header.style', 'in', array( '4', '5' ) ),
				),
			),
			'logo_margin_start'                            => array(
				'title'     => esc_html__( 'Logo margin', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array(
					array( 'logo_type', 'in', array( 'img', 'text' ) ),
					array( 'header.style', 'in', array( '1', '2', '3', '5' ) ),
				),
			),
			'logo_margin_top'                              => array(
				'title'     => esc_html__( 'Top', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( - 1000, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array(
					array( 'logo_type', 'in', array( 'img', 'text' ) ),
					array( 'header.style', 'in', array( '1', '2', '3', '5' ) ),
				),
			),
			'logo_margin_right'                            => array(
				'title'     => esc_html__( 'Right', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( - 1000, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array(
					array( 'logo_type', 'in', array( 'img', 'text' ) ),
					array( 'header.style', 'in', array( '1', '2', '3', '5' ) ),
				),
			),
			'logo_margin_bottom'                           => array(
				'title'     => esc_html__( 'Bottom', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( - 1000, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array(
					array( 'logo_type', 'in', array( 'img', 'text' ) ),
					array( 'header.style', 'in', array( '1', '2', '3', '5' ) ),
				),
			),
			'logo_margin_left'                             => array(
				'title'     => esc_html__( 'Left', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( - 1000, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array(
					array( 'logo_type', 'in', array( 'img', 'text' ) ),
					array( 'header.style', 'in', array( '1', '2', '3', '5' ) ),
				),
			),
			'logo_margin_end'                              => array(
				'type'      => 'inlineEnd',
				'condition' => array(
					array( 'logo_type', 'in', array( 'img', 'text' ) ),
					array( 'header.style', 'in', array( '1', '2', '3', '5' ) ),
				),
			),
			'logo_height'                                  => array(
				'title'     => esc_html__( 'Logo height', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 10, 350 ),
				'default'   => 46,
				'unit'      => 'px',
				'condition' => array( array( 'logo_type', '==', 'img' ) ),
			),
			'logo_height_sticky'                           => array(
				'title'     => esc_html__( 'Logo height sticky', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 10, 200 ),
				'default'   => 46,
				'unit'      => 'px',
				'condition' => array(
					array( 'logo_type', '==', 'img' ),
					array( 'header.style', 'in', array( '1', '2' ) ),
				),
			),
			'logo_height_mobile'                           => array(
				'title'     => esc_html__( 'Mobile logo height', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 10, 200 ),
				'default'   => 44,
				'unit'      => 'px',
				'condition' => array( array( 'logo_type', '==', 'img' ) ),
			),
			'logo_height_mobile_sticky'                    => array(
				'title'     => esc_html__( 'Mobile logo height sticky', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 10, 200 ),
				'default'   => 44,
				'unit'      => 'px',
				'condition' => array( array( 'logo_type', '==', 'img' ) ),
			),
			'logo_show_alt'                                => array(
				'title'       => esc_html__( 'Show Alternative text for logo', 'groovy-menu' ) . ' (' . esc_html__( 'Alt attribute', 'groovy-menu' ) . ')',
				'type'        => 'checkbox',
				'default'     => false,
				'description' => esc_html__( 'This attribute is editable in the WordPress media library.', 'groovy-menu' ) . ' ' . esc_html__( 'If there is no alternative text, then the attribute will be displayed empty.', 'groovy-menu' ),
				'condition'   => array( array( 'logo_type', '==', 'img' ) ),
			),
			'logo_show_title_as_alt'                       => array(
				'title'       => esc_html__( 'Show Title as Alternative text', 'groovy-menu' ) . ' (' . esc_html__( 'Alt attribute', 'groovy-menu' ) . ')',
				'type'        => 'checkbox',
				'default'     => false,
				'description' => esc_html__( 'This attribute is editable in the WordPress media library.', 'groovy-menu' ),
				'condition'   => array(
					array( 'logo_type', '==', 'img' ),
					array( 'logo_show_alt', '==', true ),
				),
			),
			'use_alt_logo_at_top'                          => array(
				'title'     => esc_html__( 'Switch default logo to alternative', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => false,
				'condition' => array(
					array( 'logo_type', '==', 'img' ),
					array( 'header.style', 'in', array( '1', '2', '3', '5' ) ),
				),
			),
			'use_alt_logo_at_sticky'                       => array(
				'title'     => esc_html__( 'Switch sticky logo to alternative', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => false,
				'condition' => array(
					array( 'logo_type', '==', 'img' ),
					array( 'header.style', 'in', array( '1', '2', '3' ) ),
				),
			),
			'use_alt_logo_at_mobile'                       => array(
				'title'     => esc_html__( 'Switch mobile logo to alternative', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => false,
				'condition' => array( array( 'logo_type', '==', 'img' ) ),
			),
			'use_alt_logo_at_sticky_mobile'                => array(
				'title'     => esc_html__( 'Switch sticky mobile logo to alternative', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => false,
				'condition' => array( array( 'logo_type', '==', 'img' ) ),
			),
			'logo_txt_font'                                => array(
				'title'       => esc_html__( 'Google font family', 'groovy-menu' ),
				'description' => esc_html__( 'Choose preferred Google font family for logo.', 'groovy-menu' ),
				'type'        => 'select',
				'options'     => array(
					'none' => 'Inherit'
				),
				'default'     => 'none',
				'condition'   => array( 'logo_type', '==', 'text' ),
			),
			'logo_txt_start'                               => array(
				'title'     => esc_html__( 'Text logo', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'logo_txt_font_size'                           => array(
				'title'     => esc_html__( 'font size', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 10, 50 ),
				'default'   => 20,
				'unit'      => 'px',
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'logo_txt_weight'                              => array(
				'title'     => esc_html__( 'variant', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'none' => 'Select value'
				),
				'default'   => 'none',
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'logo_txt_subset'                              => array(
				'title'     => esc_html__( 'subset', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'none' => 'Select value'
				),
				'default'   => 'none',
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'logo_txt_color'                               => array(
				'title'     => esc_html__( 'color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(32,32,34,1)',
				'alpha'     => true,
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'logo_txt_color_hover'                         => array(
				'title'     => esc_html__( 'hover color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(32,32,34,1)',
				'alpha'     => true,
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'logo_txt_end'                                 => array(
				'type' => 'inlineEnd'
			),
			'sticky_logo_txt_start'                        => array(
				'title'     => esc_html__( 'Sticky text logo', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'sticky_logo_txt_font_size'                    => array(
				'title'     => esc_html__( 'font size', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 10, 50 ),
				'default'   => 20,
				'unit'      => 'px',
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'sticky_logo_txt_weight'                       => array(
				'title'     => esc_html__( 'variant', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'none' => 'Select value'
				),
				'default'   => 'none',
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'sticky_logo_txt_subset'                       => array(
				'title'     => esc_html__( 'subset', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'none' => 'Select value'
				),
				'default'   => 'none',
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'sticky_logo_txt_color'                        => array(
				'title'     => esc_html__( 'color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(32,32,34,1)',
				'alpha'     => true,
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'sticky_logo_txt_color_hover'                  => array(
				'title'     => esc_html__( 'hover color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(32,32,34,1)',
				'alpha'     => true,
				'condition' => array( 'logo_type', '==', 'text' ),
			),
			'sticky_logo_txt_end'                          => array(
				'type' => 'inlineEnd'
			),
			'scrollbar_group'                              => array(
				'title'     => esc_html__( 'Scrollbar', 'groovy-menu' ) . ' & ' . esc_html__( 'Onepage', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'scrollbar_enable'                             => array(
				'title'       => esc_html__( 'Enable internal submenus scrollbar', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => true,
				'description' => esc_html__( 'Enable scrolling for long lists of submenus and mega menus', 'groovy-menu' ),
			),
			'scrollbar_enable_mobile'                      => array(
				'title'       => esc_html__( 'Enable mobile menu scrollbar', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => true,
				'description' => esc_html__( 'Enable scrolling for long lists of mobile menu & submenus', 'groovy-menu' ),
			),
			'scroll_speed_settings'                        => array(
				'title'       => esc_html__( 'Custom scroll speed options', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => false,
				'description' => esc_html__( 'Used to smoothly scroll the page content to anchor links in a onepage menu', 'groovy-menu' ),
			),
			'scroll_speed_settings_main'                   => array(
				'title'       => esc_html__( 'Scroll Speed', 'groovy-menu' ),
				'description' => esc_html__( 'Amount of time in milliseconds it should take to scroll 1000px', 'groovy-menu' ),
				'type'        => 'number',
				'range'       => array( 100, 5000 ),
				'default'     => 400,
				'unit'        => 'ms',
				'condition'   => array( 'scroll_speed_settings', '==', true ),
			),
			'scroll_speed_settings_min'                    => array(
				'title'       => esc_html__( 'Minimum', 'groovy-menu' ) . ' ' . esc_html__( 'Scroll Speed', 'groovy-menu' ),
				'description' => esc_html__( 'The minimum amount of time the scroll animation should take', 'groovy-menu' ),
				'type'        => 'number',
				'range'       => array( 100, 5000 ),
				'default'     => 250,
				'unit'        => 'ms',
				'condition'   => array( 'scroll_speed_settings', '==', true ),
			),
			'scroll_speed_settings_max'                    => array(
				'title'       => esc_html__( 'Maximum', 'groovy-menu' ) . ' ' . esc_html__( 'Scroll Speed', 'groovy-menu' ),
				'description' => esc_html__( 'The maximum amount of time the scroll animation should take', 'groovy-menu' ),
				'type'        => 'number',
				'range'       => array( 100, 5000 ),
				'default'     => 2000,
				'unit'        => 'ms',
				'condition'   => array( 'scroll_speed_settings', '==', true ),
			),
			'scroll_speed_settings_easing'                 => array(
				'title'       => esc_html__( 'Scroll easing pattern to use', 'groovy-menu' ),
				'description' => esc_html__( 'Linear Moves at the same speed from start point to end point. Ease-In Gradually increases in speed. Ease-In-Out Gradually increases in speed, peaks, and then gradually slows down. Ease-Out Gradually decreases in speed.', 'groovy-menu' ),
				'type'        => 'select',
				'options'     => array(
					'Linear'         => esc_html__( 'Linear', 'groovy-menu' ),
					'easeInQuad'     => esc_html__( 'Ease-In Quad', 'groovy-menu' ),
					'easeInCubic'    => esc_html__( 'Ease-In Cubic', 'groovy-menu' ),
					'easeInQuart'    => esc_html__( 'Ease-In Quart', 'groovy-menu' ),
					'easeInQuint'    => esc_html__( 'Ease-In Quint', 'groovy-menu' ),
					'easeInOutQuad'  => esc_html__( 'Ease-In-Out Quad', 'groovy-menu' ),
					'easeInOutCubic' => esc_html__( 'Ease-In-Out Cubic', 'groovy-menu' ),
					'easeInOutQuart' => esc_html__( 'Ease-In-Out Quart', 'groovy-menu' ),
					'easeInOutQuint' => esc_html__( 'Ease-In-Out Quint', 'groovy-menu' ),
					'easeOutQuad'    => esc_html__( 'Ease-Out Quad', 'groovy-menu' ),
					'easeOutCubic'   => esc_html__( 'Ease-Out Cubic', 'groovy-menu' ),
					'easeOutQuart'   => esc_html__( 'Ease-Out Quart', 'groovy-menu' ),
					'easeOutQuint'   => esc_html__( 'Ease-Out Quint', 'groovy-menu' ),
				),
				'default'     => 'easeInOutQuad',
				'condition'   => array( 'scroll_speed_settings', '==', true ),
			),
			'menu_item_preview_group'                      => array(
				'title'     => esc_html__( 'Menu item preview', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'menu_item_preview_start'                      => array(
				'title' => esc_html__( 'Preview', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'preview_width'                                => array(
				'title'       => esc_html__( 'width', 'groovy-menu' ),
				'description' => '',
				'type'        => 'number',
				'default'     => 330,
				'range'       => array( 50, 500 ),
				'unit'        => 'px',
			),
			'preview_height'                               => array(
				'title'       => esc_html__( 'height', 'groovy-menu' ),
				'description' => '',
				'type'        => 'number',
				'default'     => 230,
				'range'       => array( 50, 500 ),
				'unit'        => 'px',
			),
			'menu_item_preview_end'                        => array(
				'type' => 'inlineEnd'
			),
			'icon_menu_group'                              => array(
				'title'     => esc_html__( 'Icon menu', 'groovy-menu' ),
				'condition' => array( 'header.style', 'in', array( '4' ) ),
				'type'      => 'group',
				'serialize' => false,
			),
			'icon_menu_side_border_start'                  => array(
				'title'       => esc_html__( 'Side border', 'groovy-menu' ),
				'description' => esc_html__( 'Set left/right side border of menu. Side depends on menu align', 'groovy-menu' ),
				'type'        => 'inlineStart',
				'condition'   => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_side_border_thickness'              => array(
				'title'     => esc_html__( 'thickness', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 20 ),
				'default'   => 1,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_side_border__style'                 => array(
				'title'     => esc_html__( 'style', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
				),
				'default'   => 'solid',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_side_border_color'                  => array(
				'title'     => esc_html__( 'color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(218,218,218,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_side_border_end'                    => array(
				'type'      => 'inlineEnd',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_icon_size'                          => array(
				'title'     => esc_html__( 'Top level icon size', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 10, 40 ),
				'default'   => 26,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_top_level_icon_active_bg_color'     => array(
				'title'     => esc_html__( 'Top level icon active & hover background color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(147,203,82,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_top_level_icon_active_color'        => array(
				'title'     => esc_html__( 'Top level icon active & hover color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(255,255,255,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_border_start'                       => array(
				'title'     => esc_html__( 'Top level items bottom border', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_border_top_thickness'               => array(
				'title'     => esc_html__( 'thickness', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 20 ),
				'default'   => 1,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_border_top_style'                   => array(
				'title'     => esc_html__( 'style', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
				),
				'default'   => 'solid',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_border_top_color'                   => array(
				'title'     => esc_html__( 'color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(232,232,232,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_border_end'                         => array(
				'type'      => 'inlineEnd',
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'icon_menu_top_lvl_link_bg_color'              => array(
				'title'     => esc_html__( 'Top level link background color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(32,32,34,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '4' ) ),
			),
			'sidebar_menu_group'                           => array(
				'title'     => esc_html__( 'Sidebar menu', 'groovy-menu' ),
				'condition' => array( 'header.style', 'in', array( '3' ) ),
				'type'      => 'group',
				'serialize' => false,
			),
			'sidebar_menu_side_border_start'               => array(
				'title'     => esc_html__( 'Side border', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'header.style', 'in', array( '3' ) ),
			),
			'sidebar_menu_side_border_thickness'           => array(
				'title'     => esc_html__( 'thickness', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 20 ),
				'default'   => 1,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '3' ) ),
			),
			'sidebar_menu_side_border__style'              => array(
				'title'     => esc_html__( 'style', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
				),
				'default'   => 'solid',
				'condition' => array( 'header.style', 'in', array( '3' ) ),
			),
			'sidebar_menu_side_border_color'               => array(
				'title'     => esc_html__( 'color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(218,218,218,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '3' ) ),
			),
			'sidebar_menu_side_border_end'                 => array(
				'type'      => 'inlineEnd',
				'condition' => array( 'header.style', 'in', array( '3' ) ),
			),
			'sidebar_expanding_menu_group'                 => array(
				'title'     => esc_html__( 'Expanding sidebar', 'groovy-menu' ),
				'condition' => array( 'header.style', 'in', array( '5' ) ),
				'type'      => 'group',
				'serialize' => false,
			),
			'sidebar_expanding_menu_show_side_icon'        => array(
				'title'       => esc_html__( 'Show side icon', 'groovy-menu' ),
				'description' => esc_html__( 'The button that expand the menu by touch or click', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => true,
				'condition'   => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_open_on_hover'         => array(
				'title'       => esc_html__( 'Expand sidebar on hover', 'groovy-menu' ),
				'description' => esc_html__( 'This allows you to enable the effect of expanding the sidebar', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => true,
				'condition'   => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_initial_width'         => array(
				'title'     => esc_html__( 'Initial sidebar width', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 6, 2000 ),
				'default'   => 80,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_expanded_width'        => array(
				'title'     => esc_html__( 'Expanded sidebar width', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 6, 6000 ),
				'default'   => 300,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_submenu_width'         => array(
				'title'     => esc_html__( 'Submenu width', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 6, 6000 ),
				'default'   => 300,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_use_animation'         => array(
				'title'     => esc_html__( 'Use expand animation', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => true,
				'condition' => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_animation_duration'    => array(
				'title'     => esc_html__( 'Expand animation duration', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 10, 5000 ),
				'default'   => 300,
				'unit'      => 'ms',
				'condition' => array(
					array( 'header.style', 'in', array( '5' ) ),
					array( 'sidebar_expanding_menu_use_animation', '==', true ),
				),
			),
			'sidebar_expanding_menu_icon_size'             => array(
				'title'       => esc_html__( 'Icons size', 'groovy-menu' ),
				'description' => esc_html__( 'first menu level', 'groovy-menu' ),
				'type'        => 'number',
				'range'       => array( 1, 128 ),
				'default'     => 32,
				'unit'        => 'px',
				'condition'   => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_border_start'          => array(
				'title'     => esc_html__( 'Side border', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_border_thickness'      => array(
				'title'     => esc_html__( 'thickness', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( 0, 20 ),
				'default'   => 1,
				'unit'      => 'px',
				'condition' => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_border__style'         => array(
				'title'     => esc_html__( 'style', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
				),
				'default'   => 'solid',
				'condition' => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_border_color'          => array(
				'title'     => esc_html__( 'color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(218,218,218,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '5' ) ),
			),
			'sidebar_expanding_menu_border_end'            => array(
				'type'      => 'inlineEnd',
				'condition' => array( 'header.style', 'in', array( '5' ) ),
			),

			'sidebar_expanding_menu_top_border_start'     => array(
				'title' => esc_html__( 'Top level menu bottom border', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'sidebar_expanding_menu_top_border_style'     => array(
				'title'   => esc_html__( 'style', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
				),
				'default' => 'dotted',
			),
			'sidebar_expanding_menu_top_border_thickness' => array(
				'title'   => esc_html__( 'thickness', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 1,
				'unit'    => 'px',
			),
			'sidebar_expanding_menu_top_border_color'     => array(
				'title'       => esc_html__( 'color', 'groovy-menu' ),
				'description' => '',
				'type'        => 'colorpicker',
				'default'     => 'rgba(110, 110, 111, 1)',
				'alpha'       => true,
			),
			'sidebar_expanding_menu_top_border_end'       => array(
				'type' => 'inlineEnd'
			),
			'sidebar_expanding_menu_top_padding'          => array(
				'title'   => esc_html__( 'Top level menu vertical padding', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 200 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'minimalistic_menu_group'                     => array(
				'title'     => esc_html__( 'Minimalistic menu', 'groovy-menu' ),
				'condition' => array( 'header.style', 'in', array( '2' ) ),
				'type'      => 'group',
				'serialize' => false,
			),
			'minimalistic_menu_top_lvl_menu_bg_color'     => array(
				'title'     => esc_html__( 'Top level menu background color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => 'rgba(0,0,0,1)',
				'alpha'     => true,
				'condition' => array( 'header.style', 'in', array( '2' ) ),
			),
			'css_group'                                   => array(
				'title'     => esc_html__( 'Custom CSS', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'css'                                         => array(
				'title'             => esc_html__( 'Custom CSS', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'css',
				'default'           => '',
				'serialize'         => false,
			),
			'compiled_css'                                => array(
				'title'     => '',
				'type'      => 'hiddenInput',
				'default'   => '',
				'serialize' => false,
			),
			'compiled_css_rtl'                            => array(
				'title'   => '',
				'type'    => 'hiddenInput',
				'default' => '',
			),
			'preset_key'                                  => array(
				'title'   => '',
				'type'    => 'hiddenInput',
				'default' => '',
			),
			'js_group'                                    => array(
				'title'     => esc_html__( 'Custom JS', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'js'                                          => array(
				'title'             => esc_html__( 'Custom JS', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'javascript',
				'default'           => '',
				'serialize'         => false,
			),
			'version'                                     => array(
				'title'   => '',
				'type'    => 'hiddenInput',
				'default' => '',
			),
			'version_rtl'                                 => array(
				'title'   => '',
				'type'    => 'hiddenInput',
				'default' => '',
			),
			'custom_code_group'                           => array(
				'title'     => esc_html__( 'Custom code', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'custom_css_class'                            => array(
				'title'   => esc_html__( 'Custom CSS class', 'groovy-menu' ),
				'type'    => 'text',
				'default' => '',
			),
			// --------------------------------------------------------------------------------[ custom_shortcodes ]
			'custom_shortcode_gm__start'                  => array(
				'title' => esc_html__( 'Insert custom shortcode or raw HTML for', 'groovy-memu' ) . ' ' . esc_html__( 'Groovy Menu wrapper', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'action__gm_before_main_header'               => array(
				'title'             => esc_html__( 'Before Main Header', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_after_main_header'                => array(
				'title'             => esc_html__( 'After Main Header', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'custom_shortcode_gm__end'                    => array(
				'type' => 'inlineEnd',
			),
			'custom_shortcode_toolbar__start'             => array(
				'title'     => esc_html__( 'Insert custom shortcode or raw HTML for', 'groovy-memu' ) . ' ' . esc_html__( 'Toolbar', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'header.toolbar', 'in', array( 'true', '1' ) ),
			),
			'action__gm_toolbar_left_first'               => array(
				'title'             => esc_html__( 'Toolbar Left First', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
				'condition'         => array( 'header.toolbar', 'in', array( 'true', '1' ) ),
			),
			'action__gm_toolbar_left_last'                => array(
				'title'             => esc_html__( 'Toolbar Left Last', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
				'condition'         => array( 'header.toolbar', 'in', array( 'true', '1' ) ),
			),
			'action__gm_toolbar_right_first'              => array(
				'title'             => esc_html__( 'Toolbar Right First', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
				'condition'         => array( 'header.toolbar', 'in', array( 'true', '1' ) ),
			),
			'action__gm_toolbar_right_last'               => array(
				'title'             => esc_html__( 'Toolbar Right Last', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
				'condition'         => array( 'header.toolbar', 'in', array( 'true', '1' ) ),
			),
			'custom_shortcode_toolbar__end'               => array(
				'type'      => 'inlineEnd',
				'condition' => array( 'header.toolbar', 'in', array( 'true', '1' ) ),
			),
			'custom_shortcode_logo__start'                => array(
				'title' => esc_html__( 'Insert custom shortcode or raw HTML for', 'groovy-memu' ) . ' ' . esc_html__( 'Logo', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'action__gm_before_logo'                      => array(
				'title'             => esc_html__( 'Logo Before', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_after_logo'                       => array(
				'title'             => esc_html__( 'Logo After', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'custom_shortcode_logo__end'                  => array(
				'type' => 'inlineEnd',
			),
			'custom_shortcode_nav__start'                 => array(
				'title' => esc_html__( 'Insert custom shortcode or raw HTML for', 'groovy-memu' ) . ' ' . esc_html__( 'Main Navigation', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'action__gm_main_menu_nav_first'              => array(
				'title'             => esc_html__( 'Main Menu Navigation First', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_main_menu_nav_last'               => array(
				'title'             => esc_html__( 'Main Menu Navigation Last', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_after_main_menu_nav'              => array(
				'title'             => esc_html__( 'Main Menu After', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'custom_shortcode_nav__end'                   => array(
				'type' => 'inlineEnd',
			),
			'custom_shortcode_buttons__start'             => array(
				'title' => esc_html__( 'Insert custom shortcode or raw HTML for', 'groovy-memu' ) . ' ' . esc_html__( 'Action Buttons', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'action__gm_main_menu_actions_button_first'   => array(
				'title'             => esc_html__( 'Action Buttons First', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_main_menu_actions_button_last'    => array(
				'title'             => esc_html__( 'Action Buttons Last', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'custom_shortcode_buttons__end'               => array(
				'type' => 'inlineEnd',
			),
			'custom_shortcode_mobile__start'              => array(
				'title' => esc_html__( 'Insert custom shortcode or raw HTML for', 'groovy-memu' ) . ' ' . esc_html__( 'Mobile menu', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'action__gm_mobile_main_menu_top'             => array(
				'title'             => esc_html__( 'Mobile Menu Top', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_mobile_main_menu_nav_first'       => array(
				'title'             => esc_html__( 'Mobile Menu Navigation First', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_mobile_main_menu_nav_last'        => array(
				'title'             => esc_html__( 'Mobile Menu Navigation Last', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_mobile_after_main_menu_nav'       => array(
				'title'             => esc_html__( 'Mobile Menu After', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_mobile_before_search_icon'        => array(
				'title'             => esc_html__( 'Mobile Before Search icon', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_mobile_before_minicart'           => array(
				'title'             => esc_html__( 'Mobile Before Woo mini-cart', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'action__gm_mobile_toolbar_end'               => array(
				'title'             => esc_html__( 'Mobile Toolbar End', 'groovy-menu' ),
				'type'              => 'textarea',
				'codemirror_editor' => true,
				'lang_type'         => 'html',
				'small_height'      => true,
				'default'           => '',
				'serialize'         => false,
				'do_action'         => true,
			),
			'custom_shortcode_mobile__end'                => array(
				'type' => 'inlineEnd',
			),
		)
	),
	'styles'  => array(
		'title'  => esc_html__( 'Styles', 'groovy-menu' ),
		'icon'   => 'gm-icon-layers',
		'fields' => array(
			'hover_group'                                      => array(
				'type'      => 'group',
				'title'     => esc_html__( 'Hover styles', 'groovy-menu' ),
				'serialize' => false,
			),
			'hover_style'                                      => array(
				'type'    => 'hoverStyle',
				'title'   => esc_html__( 'Top level hover Style', 'groovy-menu' ),
				'options' => array(
					'1' => '1',
					'2' => array( 'condition' => array( 'header.style', 'in', array( '1' ) ) ),
					'3' => array( 'condition' => array( 'header.style', 'in', array( '1' ) ) ),
					'4' => array( 'condition' => array( 'header.style', 'in', array( '1' ) ) ),
					'5' => array( 'condition' => array( 'header.style', 'in', array( '1' ) ) ),
					'6' => array( 'condition' => array( 'header.style', 'in', array( '1' ) ) ),
					'7' => array( 'condition' => array( 'header.style', 'in', array( '1' ) ) ),
				),
				'default' => '1',
			),
			'background_group'                                 => array(
				'type'      => 'group',
				'title'     => esc_html__( 'Background', 'groovy-menu' ),
				'serialize' => false,
			),
			'background_color'                                 => array(
				'title'   => esc_html__( 'Top level menu background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(255,255,255,1)',
				'alpha'   => true,
			),
			'background_color_change_on_submenu_opened'        => array(
				'title'     => esc_html__( 'Change Top level menu background color when submenu(s) are opened', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => false,
				'condition' => array( 'header.style', 'in', array( '1', '3', '4', '5' ) ),
			),
			'background_color_change'                          => array(
				'title'     => esc_html__( 'Top level menu background color when submenu(s) are opened', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#ffffff',
				'alpha'     => true,
				'condition' => array(
					array( 'header.style', 'in', array( '1', '3', '4', '5' ) ),
					array( 'background_color_change_on_submenu_opened', '==', true ),
				)
			),
			'background_image'                                 => array(
				'title'            => esc_html__( 'Top level menu background Image', 'groovy-menu' ),
				'description'      => '',
				'type'             => 'media',
				'default'          => '',
				'image_size_field' => 'background_size',
			),
			'background_size'                                  => array(
				'title'   => esc_html__( 'Top level menu background image size', 'groovy-menu' ),
				'type'    => 'select',
				'options' => GroovyMenuUtils::get_all_image_sizes_for_select(),
				'default' => 'full',
			),
			'background_start'                                 => array(
				'title' => esc_html__( 'Top level menu background', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'background_repeat'                                => array(
				'title'   => esc_html__( 'repeat', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'no-repeat' => esc_html__( 'no-repeat', 'groovy-menu' ),
					'repeat'    => esc_html__( 'repeat', 'groovy-menu' ),
					'repeat-x'  => esc_html__( 'repeat-x', 'groovy-menu' ),
					'repeat-y'  => esc_html__( 'repeat-y', 'groovy-menu' ),
				),
				'default' => 'no-repeat',
			),
			'background_attachment'                            => array(
				'title'   => esc_html__( 'attachment', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'fixed'  => esc_html__( 'fixed', 'groovy-menu' ),
					'scroll' => esc_html__( 'scroll', 'groovy-menu' ),
				),
				'default' => 'scroll',
			),
			'background_position'                              => array(
				'title'   => esc_html__( 'position', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'center center' => esc_html__( 'center center', 'groovy-menu' ),
					'top left'      => esc_html__( 'top left', 'groovy-menu' ),
					'top center'    => esc_html__( 'top center', 'groovy-menu' ),
					'top right'     => esc_html__( 'top right', 'groovy-menu' ),
					'center left'   => esc_html__( 'center left', 'groovy-menu' ),
					'center right'  => esc_html__( 'center right', 'groovy-menu' ),
					'bottom left'   => esc_html__( 'bottom left', 'groovy-menu' ),
					'bottom center' => esc_html__( 'bottom center', 'groovy-menu' ),
					'bottom right'  => esc_html__( 'bottom right', 'groovy-menu' ),
				),
				'default' => 'center center',
			),
			'cover_background'                                 => array(
				'title'   => esc_html__( 'Cover background', 'groovy-menu' ),
				'type'    => 'checkbox',
				'default' => false,
			),
			'background_end'                                   => array(
				'title' => esc_html__( 'Background', 'groovy-menu' ),
				'type'  => 'inlineEnd',
			),
			'sticky_background_color'                          => array(
				'title'   => esc_html__( 'Top level menu sticky background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(255,255,255,1)',
				'alpha'   => true,
			),
			'sticky_background_color_change_on_submenu_opened' => array(
				'title'     => esc_html__( 'Change Top level menu sticky background color when submenu(s) are opened', 'groovy-menu' ),
				'type'      => 'checkbox',
				'default'   => false,
				'condition' => array( 'header.style', 'in', array( '1', '3', '4', '5' ) ),
			),
			'sticky_background_color_change'                   => array(
				'title'     => esc_html__( 'Top level menu sticky background color when submenu(s) are opened', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#ffffff',
				'alpha'     => true,
				'condition' => array(
					array( 'header.style', 'in', array( '1', '3', '4', '5' ) ),
					array( 'sticky_background_color_change_on_submenu_opened', '==', true ),
				)
			),
			'sticky_bg_image'                                  => array(
				'title'            => esc_html__( 'Top level menu sticky background Image', 'groovy-menu' ),
				'type'             => 'media',
				'default'          => '',
				'image_size_field' => 'sticky_bg_image_size',
			),
			'sticky_bg_image_size'                             => array(
				'title'   => esc_html__( 'Top level menu sticky background image size', 'groovy-menu' ),
				'type'    => 'select',
				'options' => GroovyMenuUtils::get_all_image_sizes_for_select(),
				'default' => 'full',
			),
			'sticky_bg_start'                                  => array(
				'title' => esc_html__( 'Top level menu sticky background', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'sticky_bg_repeat'                                 => array(
				'title'   => esc_html__( 'repeat', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'no-repeat' => esc_html__( 'no-repeat', 'groovy-menu' ),
					'repeat'    => esc_html__( 'repeat', 'groovy-menu' ),
					'repeat-x'  => esc_html__( 'repeat-x', 'groovy-menu' ),
					'repeat-y'  => esc_html__( 'repeat-y', 'groovy-menu' ),
				),
				'default' => 'no-repeat',
			),
			'sticky_bg_attachment'                             => array(
				'title'   => esc_html__( 'attachment', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'fixed'  => esc_html__( 'fixed', 'groovy-menu' ),
					'scroll' => esc_html__( 'scroll', 'groovy-menu' ),
				),
				'default' => 'scroll',
			),
			'sticky_bg_position'                               => array(
				'title'   => esc_html__( 'position', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'center center' => esc_html__( 'center center', 'groovy-menu' ),
					'top left'      => esc_html__( 'top left', 'groovy-menu' ),
					'top center'    => esc_html__( 'top center', 'groovy-menu' ),
					'top right'     => esc_html__( 'top right', 'groovy-menu' ),
					'center left'   => esc_html__( 'center left', 'groovy-menu' ),
					'center right'  => esc_html__( 'center right', 'groovy-menu' ),
					'bottom left'   => esc_html__( 'bottom left', 'groovy-menu' ),
					'bottom center' => esc_html__( 'bottom center', 'groovy-menu' ),
					'bottom right'  => esc_html__( 'bottom right', 'groovy-menu' ),
				),
				'default' => 'center center',
			),
			'sticky_bg_cover'                                  => array(
				'title'   => esc_html__( 'Cover background', 'groovy-menu' ),
				'type'    => 'checkbox',
				'default' => false,
			),
			'sticky_bg_end'                                    => array(
				'type' => 'inlineEnd'
			),
			'group_1'                                          => array(
				'title'     => esc_html__( 'Border', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'header_bottom_border_start'                       => array(
				'title' => esc_html__( 'Menu bottom border', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'bottom_border_thickness'                          => array(
				'title'   => esc_html__( 'thickness', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'bottom_border_color'                              => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true,
			),
			'header_bottom_border_end'                         => array(
				'type' => 'inlineEnd'
			),
			'sticky_header_bottom_border_start'                => array(
				'title' => esc_html__( 'Sticky menu bottom border', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'bottom_border_thickness_sticky'                   => array(
				'title'   => esc_html__( 'thickness', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'bottom_border_color_sticky'                       => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true
			),
			'sticky_header_bottom_border_end'                  => array(
				'type' => 'inlineEnd'
			),
			'group_2'                                          => array(
				'type'      => 'group',
				'title'     => esc_html__( 'Colors', 'groovy-menu' ),
				'serialize' => false,
			),
			'top_level_text_color'                             => array(
				'title'   => esc_html__( 'Top level link text color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'alpha'   => true,
				'default' => '#5a5a5a',
			),
			'top_level_text_color_hover'                       => array(
				'title'   => esc_html__( 'Top level hover and active link color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'alpha'   => true,
				'default' => '#93cb52',
			),
			'top_level_text_color_hover_2'                     => array(
				'title'     => esc_html__( 'Top level hover and active text color (hover style 3, 4 and 6 only)', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#ffffff',
				'alpha'     => true,
				'condition' => array( 'hover_style', 'in', array( '3', '4', '6' ) ),
			),
			'sticky_top_level_text_color'                      => array(
				'title'   => esc_html__( 'Sticky top level text color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'alpha'   => true,
				'default' => '#5a5a5a',
			),
			'sticky_top_level_text_color_hover'                => array(
				'title'   => esc_html__( 'Sticky top level hover and active link color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'alpha'   => true,
				'default' => '#93cb52',
			),
			'sticky_top_level_text_color_hover_2'              => array(
				'title'     => esc_html__( 'Sticky top level hover and active text color (hover style 3, 4 and 6 only)', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#ffffff',
				'alpha'     => true,
				'condition' => array( 'hover_style', 'in', array( '3', '4', '6' ) ),
			),
			'typography_group'                                 => array(
				'type'      => 'group',
				'title'     => esc_html__( 'Typography', 'groovy-menu' ),
				'serialize' => false,
			),
			'google_font'                                      => array(
				'title'       => esc_html__( 'Google font family', 'groovy-menu' ),
				'description' => esc_html__( 'Choose preferred Google font family for menu.', 'groovy-menu' ),
				'type'        => 'select',
				'options'     => array(
					'none' => 'Inherit',
				),
				'default'     => 'none'
			),
			'items_gutter_space'                               => array(
				'type'        => 'number',
				'range'       => array( 0, 100 ),
				'title'       => esc_html__( 'Top level menu items gutter space', 'groovy-menu' ),
				'condition'   => array( 'header.style', 'in', array( '1' ) ),
				'description' => esc_html__( 'This value will be applied as padding to left and right of the item.', 'groovy-menu' ),
				'default'     => 15,
				'unit'        => 'px',
			),
			'item_text_start'                                  => array(
				'title' => esc_html__( 'Top level text', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'item_text_size'                                   => array(
				'title'   => esc_html__( 'Size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 10, 50 ),
				'default' => 14,
				'unit'    => 'px',
			),
			'item_text_case'                                   => array(
				'title'   => esc_html__( 'Case', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none'       => esc_html__( 'Default', 'groovy-menu' ),
					'uppercase'  => esc_html__( 'Uppercase', 'groovy-menu' ),
					'capitalize' => esc_html__( 'Capitalize', 'groovy-menu' ),
					'lowercase'  => esc_html__( 'Lowercase', 'groovy-menu' ),
				),
				'default' => 'uppercase',
			),
			'item_text_weight'                                 => array(
				'title'   => esc_html__( 'Font variant', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none' => 'Select value'
				),
				'default' => 'none',
			),
			'item_text_subset'                                 => array(
				'title'   => esc_html__( 'Subset', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none' => 'Select value'
				),
				'default' => 'none',
			),
			'item_letter_spacing'                              => array(
				'title'   => esc_html__( 'Letter spacing', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'item_text_end'                                    => array(
				'type' => 'inlineEnd'
			),
			'shadow_group'                                     => array(
				'type'      => 'group',
				'title'     => esc_html__( 'Shadow', 'groovy-menu' ),
				'serialize' => false,
			),
			'shadow'                                           => array(
				'title'       => esc_html__( 'Menu shadow', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => true,
			),
			'shadow_dropdown'                                  => array(
				'title'       => esc_html__( 'Submenu shadow', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
			),
			'shadow_sticky'                                    => array(
				'title'       => esc_html__( 'Sticky menu shadow', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => true,
			),
			'toolbar_group'                                    => array(
				'type'      => 'group',
				'title'     => esc_html__( 'Toolbar', 'groovy-menu' ),
				'condition' => array( 'header.toolbar', '==', 'true' ),
				'serialize' => false,
			),
			'hide_toolbar_on_mobile'                           => array(
				'title'   => esc_html__( 'Hide toolbar on mobile devices', 'groovy-menu' ),
				'type'    => 'checkbox',
				'default' => false,
			),
			'toolbar_top__start'                               => array(
				'title' => esc_html__( 'Toolbar top border', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'toolbar_top_thickness'                            => array(
				'title'   => esc_html__( 'thickness', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'toolbar_top_color'                                => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,1)',
				'alpha'   => true,
			),
			'toolbar_top__end'                                 => array(
				'type' => 'inlineEnd'
			),
			'toolbar_bottom__start'                            => array(
				'title' => esc_html__( 'Toolbar bottom border', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'toolbar_bottom_thickness'                         => array(
				'title'   => esc_html__( 'thickness', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'toolbar_bottom_color'                             => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,1)',
				'alpha'   => true,
			),
			'toolbar_bottom__end'                              => array(
				'type' => 'inlineEnd'
			),
			'toolbar_bg_color'                                 => array(
				'title'   => esc_html__( 'Toolbar background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(255,255,255,1)',
				'alpha'   => true,
			),
			'toolbar_additional_info_color'                    => array(
				'title'   => esc_html__( 'Toolbar additional information color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(104,104,104,1)',
				'alpha'   => true,
			),
			'wpml_dropdown_bg_color'                           => array(
				'title'   => esc_html__( 'WPML dropdown background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(255,255,255,1)',
				'alpha'   => true,
			),
			'toolbar_additional_info_font_size'                => array(
				'title'   => esc_html__( 'Toolbar additional information font size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 8, 50 ),
				'default' => 14,
				'unit'    => 'px',
			),
			'hide_toolbar_icon_text_on_mobile'                 => array(
				'title'   => esc_html__( 'Hide social icon link text on mobile devices', 'groovy-menu' ),
				'type'    => 'checkbox',
				'default' => false,
			),
			'toolbar_icon_size'                                => array(
				'title'   => esc_html__( 'Toolbar social icon size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 8, 50 ),
				'default' => 16,
				'unit'    => 'px',
			),
			'toolbar_icon_color'                               => array(
				'title'   => esc_html__( 'Toolbar social icon color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(104,104,104,1)',
				'alpha'   => true,
			),
			'toolbar_icon_hover_color'                         => array(
				'title'   => esc_html__( 'Toolbar social icon hover color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#a5e25e',
				'alpha'   => true,
			),
			'toolbar_icon_switch_border'                       => array(
				'title'       => esc_html__( 'Add left/right border to social icons', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
			),
			'hamburger_group'                                  => array(
				'type'      => 'group',
				'title'     => esc_html__( 'Side icon', 'groovy-menu' ),
				'serialize' => false,
			),
			'hamburger_icon_start'                             => array(
				'title' => esc_html__( 'Side icon', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'hamburger_icon_size'                              => array(
				'title'   => esc_html__( 'size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 10, 50 ),
				'default' => 24,
				'unit'    => 'px',
			),
			'hamburger_icon_padding'                           => array(
				'title'   => esc_html__( 'padding area', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'hamburger_icon_bg_color'                          => array(
				'title'   => esc_html__( 'background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true,
			),
			'hamburger_icon_color'                             => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(104,104,104,1)',
				'alpha'   => true,
			),
			'hamburger_icon_end'                               => array(
				'type' => 'inlineEnd'
			),
			'hamburger_icon_border_start'                      => array(
				'title' => esc_html__( 'Side icon border', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'hamburger_icon_border_width'                      => array(
				'title'   => esc_html__( 'width', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'hamburger_icon_border_color'                      => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true,
			),
			'hamburger_icon_border_end'                        => array(
				'type' => 'inlineEnd'
			),
			'hamburger_icon_sticky_start'                      => array(
				'title' => esc_html__( 'Side icon sticky', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'hamburger_icon_sticky_size'                       => array(
				'title'   => esc_html__( 'size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 10, 50 ),
				'default' => 24,
				'unit'    => 'px',
			),
			'hamburger_icon_sticky_padding'                    => array(
				'title'   => esc_html__( 'padding area', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'hamburger_icon_sticky_bg_color'                   => array(
				'title'   => esc_html__( 'background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true,
			),
			'hamburger_icon_sticky_color'                      => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(104,104,104,1)',
				'alpha'   => true,
			),
			'hamburger_icon_sticky_end'                        => array(
				'type' => 'inlineEnd'
			),
			'hamburger_icon_sticky_border_start'               => array(
				'title' => esc_html__( 'Side icon sticky border', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'hamburger_icon_sticky_border_width'               => array(
				'title'   => esc_html__( 'width', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'hamburger_icon_sticky_border_color'               => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true
			),
			'hamburger_icon_sticky_border_end'                 => array(
				'type' => 'inlineEnd'
			),
			'hamburger_icon_mobile_start'                      => array(
				'title' => esc_html__( 'Side icon mobile', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'hamburger_icon_size_mobile'                       => array(
				'title'   => esc_html__( 'size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 10, 50 ),
				'default' => 24,
				'unit'    => 'px',
			),
			'hamburger_icon_padding_mobile'                    => array(
				'title'   => esc_html__( 'padding area', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'hamburger_icon_bg_color_mobile'                   => array(
				'title'   => esc_html__( 'background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true,
			),
			'hamburger_icon_color_mobile'                      => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(104,104,104,1)',
				'alpha'   => true,
			),
			'hamburger_icon_mobile_end'                        => array(
				'type' => 'inlineEnd'
			),
			'hamburger_icon_mobile_border_start'               => array(
				'title' => esc_html__( 'Side icon mobile border', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'hamburger_icon_mobile_border_width'               => array(
				'title'   => esc_html__( 'width', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'hamburger_icon_mobile_border_color'               => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true,
			),
			'hamburger_icon_mobile_border_end'                 => array(
				'type' => 'inlineEnd'
			),
			'hamburger_icon_mobile_sticky_start'               => array(
				'title' => esc_html__( 'Side icon mobile sticky', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'hamburger_icon_mobile_sticky_size'                => array(
				'title'   => esc_html__( 'size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 10, 50 ),
				'default' => 24,
				'unit'    => 'px',
			),
			'hamburger_icon_mobile_sticky_padding'             => array(
				'title'   => esc_html__( 'padding area', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'hamburger_icon_mobile_sticky_bg_color'            => array(
				'title'   => esc_html__( 'background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true,
			),
			'hamburger_icon_mobile_sticky_color'               => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(104,104,104,1)',
				'alpha'   => true,
			),
			'hamburger_icon_mobile_sticky_end'                 => array(
				'type' => 'inlineEnd'
			),
			'hamburger_icon_mobile_sticky_border_start'        => array(
				'title' => esc_html__( 'Side icon mobile sticky border', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'hamburger_icon_mobile_sticky_border_width'        => array(
				'title'   => esc_html__( 'width', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'hamburger_icon_mobile_sticky_border_color'        => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgba(0,0,0,0)',
				'alpha'   => true,
			),
			'hamburger_icon_mobile_sticky_border_end'          => array(
				'type' => 'inlineEnd'
			),
			'woo_cart_group'                                   => array(
				'type'      => 'group',
				'condition' => array( 'woocommerce_cart', '==', 'true' ),
				'title'     => esc_html__( 'Woo minicart', 'groovy-menu' ),
				'serialize' => false,
			),
			'woo_cart_count_start'                             => array(
				'title' => esc_html__( 'WooCommerce minicart count', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'woo_cart_count_shape'                             => array(
				'title'   => esc_html__( 'Shape', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'drop'   => esc_html__( 'Drop', 'groovy-menu' ),
					'circle' => esc_html__( 'Circle', 'groovy-menu' ),
					'square' => esc_html__( 'Square', 'groovy-menu' )
				),
				'default' => 'drop'
			),
			'woo_cart_count_bg_color'                          => array(
				'title'   => esc_html__( 'Background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#a5e25e',
				'alpha'   => true,
			),
			'woo_cart_count_text_color'                        => array(
				'title'   => esc_html__( 'Text color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#ffffff',
				'alpha'   => true,
			),
			'woo_cart_count_end'                               => array(
				'type' => 'inlineEnd'
			),
			'woo_cart_dropdown_start'                          => array(
				'title' => esc_html__( 'WooCommerce minicart dropdown', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'woo_cart_dropdown_bg_color'                       => array(
				'title'   => esc_html__( 'Background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#202022',
				'alpha'   => true,
			),
			'woo_cart_dropdown_text_color'                     => array(
				'title'   => esc_html__( 'Text color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#ffffff',
				'alpha'   => true,
			),
			'woo_cart_dropdown_end'                            => array(
				'type' => 'inlineEnd'
			),
			'checkout_btn_start'                               => array(
				'title' => esc_html__( 'Checkout button', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'checkout_btn_font_size'                           => array(
				'title'   => esc_html__( 'font size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 10, 50 ),
				'default' => 13,
				'unit'    => 'px',
			),
			'checkout_btn_font_weight'                         => array(
				'title'   => esc_html__( 'font weight', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					''    => 'Select value',
					'100' => esc_html__( '100', 'groovy-menu' ),
					'300' => esc_html__( '300 (Light)', 'groovy-menu' ),
					'400' => esc_html__( '400 (Normal)', 'groovy-menu' ),
					'500' => esc_html__( '500', 'groovy-menu' ),
					'600' => esc_html__( '600', 'groovy-menu' ),
					'700' => esc_html__( '700 (Bold)', 'groovy-menu' ),
					'800' => esc_html__( '800', 'groovy-menu' ),
					'900' => esc_html__( '900', 'groovy-menu' )
				),
				'default' => 700,
			),
			'checkout_btn_text_color'                          => array(
				'title'   => esc_html__( 'text color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#686868',
				'alpha'   => true,
			),
			'checkout_btn_text_color_hover'                    => array(
				'title'   => esc_html__( 'text color on hover', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#686868',
				'alpha'   => true,
			),
			'checkout_btn_bg_color'                            => array(
				'title'   => esc_html__( 'background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#fff',
				'alpha'   => true,
			),
			'checkout_btn_bg_color_hover'                      => array(
				'title'   => esc_html__( 'background color on hover', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#fff',
				'alpha'   => true,
			),
			'checkout_btn_end'                                 => array(
				'type' => 'inlineEnd'
			),
			'checkout_btn_border_start'                        => array(
				'title' => esc_html__( 'Checkout button border', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'checkout_btn_border_style'                        => array(
				'title'   => esc_html__( 'Border style', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none'   => esc_html__( 'None', 'groovy-menu' ),
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
				),
				'default' => 'none',
			),
			'checkout_btn_border_width'                        => array(
				'title'   => esc_html__( 'width', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 1,
				'unit'    => 'px',
			),
			'checkout_btn_border_color'                        => array(
				'title' => esc_html__( 'color', 'groovy-menu' ),
				'type'  => 'colorpicker',
				'alpha' => true,
			),
			'checkout_btn_border_color_hover'                  => array(
				'title' => esc_html__( 'color on hover', 'groovy-menu' ),
				'type'  => 'colorpicker',
				'alpha' => true,
			),
			'checkout_btn_border_end'                          => array(
				'type' => 'inlineEnd'
			),
			'view_cart_btn_start'                              => array(
				'title' => esc_html__( 'View cart button', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'view_cart_btn_font_size'                          => array(
				'title'   => esc_html__( 'font size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 10, 50 ),
				'default' => 13,
				'unit'    => 'px',
			),
			'view_cart_btn_font_weight'                        => array(
				'title'   => esc_html__( 'font weight', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					''    => 'Select value',
					'100' => esc_html__( '100', 'groovy-menu' ),
					'300' => esc_html__( '300 (Light)', 'groovy-menu' ),
					'400' => esc_html__( '400 (Normal)', 'groovy-menu' ),
					'500' => esc_html__( '500', 'groovy-menu' ),
					'600' => esc_html__( '600', 'groovy-menu' ),
					'700' => esc_html__( '700 (Bold)', 'groovy-menu' ),
					'800' => esc_html__( '800', 'groovy-menu' ),
					'900' => esc_html__( '900', 'groovy-menu' ),
				),
				'default' => 700,
			),
			'view_cart_btn_text_color'                         => array(
				'title'   => esc_html__( 'text color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#ffffff',
				'alpha'   => true,
			),
			'view_cart_btn_text_color_hover'                   => array(
				'title'   => esc_html__( 'text color on hover', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#ffffff',
				'alpha'   => true,
			),
			'view_cart_btn_bg_color'                           => array(
				'title'   => esc_html__( 'background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#a5e25e',
				'alpha'   => true,
			),
			'view_cart_btn_bg_color_hover'                     => array(
				'title'   => esc_html__( 'background color on hover', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#a5e25e',
				'alpha'   => true,
			),
			'view_cart_btn_end'                                => array(
				'type' => 'inlineEnd'
			),
			'view_cart_btn_border_start'                       => array(
				'title' => esc_html__( 'View cart button border', 'groovy-menu' ),
				'type'  => 'inlineStart',
			),
			'view_cart_btn_border_style'                       => array(
				'title'   => esc_html__( 'Border style', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none'   => esc_html__( 'None', 'groovy-menu' ),
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
				),
				'default' => 'none',
			),
			'view_cart_btn_border_width'                       => array(
				'title'   => esc_html__( 'width', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 1,
				'unit'    => 'px',
			),
			'view_cart_btn_border_color'                       => array(
				'title' => esc_html__( 'color', 'groovy-menu' ),
				'type'  => 'colorpicker',
				'alpha' => true,
			),
			'view_cart_btn_border_color_hover'                 => array(
				'title' => esc_html__( 'color on hover', 'groovy-menu' ),
				'type'  => 'colorpicker',
				'alpha' => true,
			),
			'view_cart_btn_border_end'                         => array(
				'type' => 'inlineEnd'
			),
		),
	),
	'mobile'  => array(
		'title'  => esc_html__( 'Mobile menu', 'groovy-menu' ),
		'icon'   => 'gm-icon-power-off',
		'fields' => array(
			// ------------------------------------------------------------------------------ Mobile options
			'mobile_group'                           => array(
				'title'     => esc_html__( 'Mobile options', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'mobile_nav_menu'                        => array(
				'title'       => esc_html__( 'Mobile navigation menu', 'groovy-menu' ),
				'description' => esc_html__( 'If for some reason you need to show another menu in the mobile version, then assign it here.', 'groovy-menu' ),
				'type'        => 'select',
				'options'     => $nav_menus,
				'default'     => '',
			),
			'mobile_submenu_style'                   => array(
				'title'   => esc_html__( 'Mobile submenus style', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'default' => esc_html__( 'Standard dropdown list', 'groovy-menu' ),
					'slider'  => esc_html__( 'Slider mobile submenu opening style', 'groovy-menu' ),
				),
				'default' => 'default',
			),
			'mobile_custom_hamburger'                => array(
				'title'       => esc_html__( 'Custom mobile menu open trigger', 'groovy-menu' ),
				'description' => esc_html__( 'Place any custom element with CSS class [gm-custom-hamburger] as open/close trigger for a mobile menu', 'groovy-menu' ) . '. ' . esc_html__( 'For example', 'groovy-menu' ) . ': <code>' . htmlentities( '<div class="gm-custom-hamburger">menu</div>' ) . '</code>' . '. ' . esc_html__( 'This option will disable the output of Groovy Menu hamburger menu', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => false,
			),
			'mobile_independent_css_hamburger'       => array(
				'title'       => esc_html__( 'Show Hamburger icon as animated', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
				'condition'   => array( 'header.style', 'in', array( '1', '3', '4', '5' ) ),
			),
			'mobile_independent_css_hamburger_type'  => array(
				'title'     => esc_html__( 'Hamburger animation style', 'groovy-menu' ),
				'type'      => 'select',
				'options'   => array(
					'hamburger--3dx'         => esc_html__( '3d x', 'groovy-menu' ),
					'hamburger--3dx-r'       => esc_html__( '3d x reverse', 'groovy-menu' ),
					'hamburger--3dy'         => esc_html__( '3d y', 'groovy-menu' ),
					'hamburger--3dy-r'       => esc_html__( '3d y reverse', 'groovy-menu' ),
					'hamburger--3dxy'        => esc_html__( '3d xy', 'groovy-menu' ),
					'hamburger--3dxy-r'      => esc_html__( '3d xy reverse', 'groovy-menu' ),
					'hamburger--arrow'       => esc_html__( 'arrow', 'groovy-menu' ),
					'hamburger--arrow-r'     => esc_html__( 'arrow reverse', 'groovy-menu' ),
					'hamburger--arrowalt'    => esc_html__( 'arrow alt', 'groovy-menu' ),
					'hamburger--arrowalt-r'  => esc_html__( 'arrow alt reverse', 'groovy-menu' ),
					'hamburger--arrowturn'   => esc_html__( 'arrow turn', 'groovy-menu' ),
					'hamburger--arrowturn-r' => esc_html__( 'arrow turn reverse', 'groovy-menu' ),
					'hamburger--boring'      => esc_html__( 'boring', 'groovy-menu' ),
					'hamburger--collapse'    => esc_html__( 'collapse', 'groovy-menu' ),
					'hamburger--collapse-r'  => esc_html__( 'collapse reverse', 'groovy-menu' ),
					'hamburger--elastic'     => esc_html__( 'elastic', 'groovy-menu' ),
					'hamburger--elastic-r'   => esc_html__( 'elastic reverse', 'groovy-menu' ),
					'hamburger--emphatic'    => esc_html__( 'emphatic', 'groovy-menu' ),
					'hamburger--emphatic-r'  => esc_html__( 'emphatic reverse', 'groovy-menu' ),
					'hamburger--minus'       => esc_html__( 'minus', 'groovy-menu' ),
					'hamburger--slider'      => esc_html__( 'slider', 'groovy-menu' ),
					'hamburger--slider-r'    => esc_html__( 'slider reverse', 'groovy-menu' ),
					'hamburger--spin'        => esc_html__( 'spin', 'groovy-menu' ),
					'hamburger--spin-r'      => esc_html__( 'spin reverse', 'groovy-menu' ),
					'hamburger--spring'      => esc_html__( 'spring', 'groovy-menu' ),
					'hamburger--spring-r'    => esc_html__( 'spring reverse', 'groovy-menu' ),
					'hamburger--stand'       => esc_html__( 'stand', 'groovy-menu' ),
					'hamburger--stand-r'     => esc_html__( 'stand reverse', 'groovy-menu' ),
					'hamburger--squeeze'     => esc_html__( 'squeeze', 'groovy-menu' ),
					'hamburger--vortex'      => esc_html__( 'vortex', 'groovy-menu' ),
					'hamburger--vortex-r'    => esc_html__( 'vortex reverse', 'groovy-menu' ),
				),
				'default'   => 'hamburger--squeeze',
				'condition' => array( 'mobile_independent_css_hamburger', '==', true ),
			),
			'mobile_nav_drawer_open_type'            => array(
				'title'   => esc_html__( 'Mobile navigation drawer open type', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'offcanvasSlideLeft'       => esc_html__( 'Offcanvas slide from the left', 'groovy-menu' ),
					'offcanvasSlideRight'      => esc_html__( 'Offcanvas slide from the right', 'groovy-menu' ),
					'offcanvasSlideSlide'      => esc_html__( 'Slide menu and container from the left', 'groovy-menu' ),
					'offcanvasSlideSlideRight' => esc_html__( 'Slide menu and container from the right', 'groovy-menu' ),
				),
				'default' => 'offcanvasSlideRight',
			),
			'mobile_logo_position'                   => array(
				'title'   => esc_html__( 'Logo Align', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'default' => esc_html__( 'Default', 'groovy-menu' ),
					'left'    => esc_html__( 'Align left', 'groovy-menu' ),
					'center'  => esc_html__( 'Align center', 'groovy-menu' ),
					'right'   => esc_html__( 'Align right', 'groovy-menu' ),
				),
				'default' => 'default',
			),
			'mobile_logo_margin_start'               => array(
				'title'     => esc_html__( 'Logo margin', 'groovy-menu' ),
				'type'      => 'inlineStart',
				'condition' => array( 'mobile_logo_position', 'in', array( 'left', 'center', 'right' ) ),
			),
			'mobile_logo_margin_top'                 => array(
				'title'     => esc_html__( 'Top', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( - 1000, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array( 'mobile_logo_position', 'in', array( 'left', 'center', 'right' ) ),
			),
			'mobile_logo_margin_right'               => array(
				'title'     => esc_html__( 'Right', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( - 1000, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array( 'mobile_logo_position', 'in', array( 'left', 'center', 'right' ) ),
			),
			'mobile_logo_margin_bottom'              => array(
				'title'     => esc_html__( 'Bottom', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( - 1000, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array( 'mobile_logo_position', 'in', array( 'left', 'center', 'right' ) ),
			),
			'mobile_logo_margin_left'                => array(
				'title'     => esc_html__( 'Left', 'groovy-menu' ),
				'type'      => 'number',
				'range'     => array( - 1000, 1000 ),
				'default'   => 0,
				'unit'      => 'px',
				'condition' => array( 'mobile_logo_position', 'in', array( 'left', 'center', 'right' ) ),
			),
			'mobile_logo_margin_end'                 => array(
				'type'      => 'inlineEnd',
				'condition' => array( 'mobile_logo_position', 'in', array( 'left', 'center', 'right' ) ),
			),
			'mobile_side_icon_position'              => array(
				'title'   => esc_html__( 'Side icon Align', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'default' => esc_html__( 'Default', 'groovy-menu' ),
					'left'    => esc_html__( 'Align left', 'groovy-menu' ),
					'right'   => esc_html__( 'Align right', 'groovy-menu' ),
				),
				'default' => 'default',
			),
			'mobile_show_woominicart'                => array(
				'title'       => esc_html__( 'Show', 'groovy-menu' ) . ' ' . esc_html__( 'Woo minicart', 'groovy-menu' ),
				'description' => esc_html__( 'You can change the icon color in the [Styles] - [Side Icon] section.', 'groovy-menu' ) . ' ' . esc_html__( 'See [Side icon mobile] and [Side icon mobile sticky]', 'groovy-menu' ),
				'type'        => 'checkbox',
				'default'     => false,
			),

			// ------------------------------------------------------------------------------ Colors
			'mobile_colors_group'                    => array(
				'title'     => esc_html__( 'Colors', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'responsive_navigation_background_color' => array(
				'title'   => esc_html__( 'Mobile navigation drawer background color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#ffffff',
				'alpha'   => true,
			),
			'mobile_slider_title_background_color'   => array(
				'title'       => esc_html__( 'Mobile submenu title background color', 'groovy-menu' ),
				'type'        => 'colorpicker',
				'default'     => '#dedede',
				'alpha'       => true,
				'description' => esc_html__( 'Slider mobile submenu opening style', 'groovy-menu' ),
			),
			'responsive_navigation_text_color'       => array(
				'title'   => esc_html__( 'Mobile navigation drawer text color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#5a5a5a',
				'alpha'   => true,
			),
			'responsive_navigation_hover_text_color' => array(
				'title'   => esc_html__( 'Mobile navigation drawer text hover and active link color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#cccccc',
				'alpha'   => true,
			),
			'mobile_items_border_start'              => array(
				'title'       => esc_html__( 'Menu items bottom border', 'groovy-menu' ),
				'description' => '',
				'type'        => 'inlineStart',
			),
			'mobile_items_border_thickness'          => array(
				'title'   => esc_html__( 'thickness', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 20 ),
				'default' => 1,
				'unit'    => 'px',
			),
			'mobile_items_border_style'              => array(
				'title'   => esc_html__( 'style', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'dotted' => esc_html__( 'Dotted', 'groovy-menu' ),
					'dashed' => esc_html__( 'Dashed', 'groovy-menu' ),
					'solid'  => esc_html__( 'Solid', 'groovy-menu' ),
				),
				'default' => 'dotted',
			),
			'mobile_items_border_color'              => array(
				'title'   => esc_html__( 'color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => 'rgb(90, 90, 90, 1)',
				'alpha'   => true,
			),
			'mobile_items_border_end'                => array(
				'type' => 'inlineEnd',
			),
			'mobile_toolbar_icon_color'              => array(
				'title'   => esc_html__( 'Mobile toolbar icons color', 'groovy-menu' ),
				'type'    => 'colorpicker',
				'default' => '#5a5a5a',
				'alpha'   => true,
			),
			'mobile_caret_custom_color'              => array(
				'title'   => esc_html__( 'Use custom colors for submenu carets', 'groovy-menu' ),
				'type'    => 'checkbox',
				'default' => false,
			),
			'mobile_caret_custom_color_top'          => array(
				'title'     => esc_html__( 'Top level caret color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#5a5a5a',
				'alpha'     => true,
				'condition' => array( 'mobile_caret_custom_color', '==', true ),
			),
			'mobile_caret_custom_color_sub'          => array(
				'title'     => esc_html__( 'Sub level caret color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#5a5a5a',
				'alpha'     => true,
				'condition' => array( 'mobile_caret_custom_color', '==', true ),
			),
			'mobile_caret_custom_color_current'      => array(
				'title'     => esc_html__( 'Current (active) caret color', 'groovy-menu' ),
				'type'      => 'colorpicker',
				'default'   => '#cccccc',
				'alpha'     => true,
				'condition' => array( 'mobile_caret_custom_color', '==', true ),
			),

			// ------------------------------------------------------------------------------ Sizes
			'mobile_sizes_group'                     => array(
				'title'     => esc_html__( 'Sizes', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'mobile_width'                           => array(
				'title'       => esc_html__( 'Mobile version switch', 'groovy-menu' ),
				'description' => esc_html__( 'You can change switch to mobile breakpoint using this option. (default:1023px).', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => '1023',
				'range'       => array( 0, 2000 ),
				'unit'        => 'px',
			),
			'mobile_offcanvas_width_start'           => array(
				'title' => esc_html__( 'Mobile navigation drawer width', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'mobile_offcanvas_width'                 => array(
				'title'   => esc_html__( 'Maximum size', 'groovy-menu' ),
				'type'    => 'number',
				'default' => 250,
				'range'   => array( 150, 1300 ),
				'unit'    => 'px',
			),
			'mobile_offcanvas_width_dynamic'         => array(
				'title'       => esc_html__( 'Dynamic minimum size', 'groovy-menu' ),
				'description' => '',
				'type'        => 'checkbox',
				'default'     => false,
			),
			'mobile_offcanvas_width_end'             => array(
				'type' => 'inlineEnd'
			),
			'mobile_header_height'                   => array(
				'title'   => esc_html__( 'Mobile header height', 'groovy-menu' ),
				'type'    => 'number',
				'default' => 70,
				'range'   => array( 50, 200 ),
				'unit'    => 'px',
			),
			'mobile_header_sticky_height'            => array(
				'title'   => esc_html__( 'Mobile header sticky height', 'groovy-menu' ),
				'type'    => 'number',
				'default' => 70,
				'range'   => array( 50, 200 ),
				'unit'    => 'px',
			),
			'mobile_menu_wrapper_indent'             => array(
				'title'   => esc_html__( 'Mobile menu top gap', 'groovy-menu' ),
				'type'    => 'number',
				'default' => 0,
				'range'   => array( 0, 700 ),
				'unit'    => 'px',
			),
			'mobile_slider_title_height'             => array(
				'title'       => esc_html__( 'Mobile submenu title height', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => 50,
				'range'       => array( 40, 700 ),
				'unit'        => 'px',
				'description' => esc_html__( 'Slider mobile submenu opening style', 'groovy-menu' ),
			),
			'mobile_items_padding_y'                 => array(
				'title'       => esc_html__( 'Mobile navigation menu item vertical padding', 'groovy-menu' ),
				'description' => esc_html__( 'This value will applied as padding to top and bottom of the item', 'groovy-menu' ),
				'type'        => 'number',
				'default'     => 9,
				'range'       => array( 0, 100 ),
				'unit'        => 'px',
			),

			// ------------------------------------------------------------------------------ Typography
			'mobile_group_typography'                => array(
				'title'     => esc_html__( 'Typography', 'groovy-menu' ),
				'type'      => 'group',
				'serialize' => false,
			),
			'mobile_item_text_start'                 => array(
				'title' => esc_html__( 'Top level text', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'mobile_item_text_size'                  => array(
				'title'   => esc_html__( 'Size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 6, 50 ),
				'default' => 11,
				'unit'    => 'px',
			),
			'mobile_item_text_case'                  => array(
				'title'   => esc_html__( 'Case', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none'       => esc_html__( 'Default', 'groovy-menu' ),
					'uppercase'  => esc_html__( 'Uppercase', 'groovy-menu' ),
					'capitalize' => esc_html__( 'Capitalize', 'groovy-menu' ),
					'lowercase'  => esc_html__( 'Lowercase', 'groovy-menu' ),
				),
				'default' => 'uppercase',
			),
			'mobile_item_text_weight'                => array(
				'title'   => esc_html__( 'Font variant', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none' => 'Select value'
				),
				'default' => '700',
			),
			'mobile_item_letter_spacing'             => array(
				'title'   => esc_html__( 'Letter spacing', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'mobile_item_text_end'                   => array(
				'type' => 'inlineEnd'
			),
			'mobile_subitem_text_start'              => array(
				'title' => esc_html__( 'Sublevel text', 'groovy-menu' ),
				'type'  => 'inlineStart'
			),
			'mobile_subitem_text_size'               => array(
				'title'   => esc_html__( 'Size', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 6, 50 ),
				'default' => 11,
				'unit'    => 'px',
			),
			'mobile_subitem_text_case'               => array(
				'title'   => esc_html__( 'Case', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none'       => esc_html__( 'Default', 'groovy-menu' ),
					'uppercase'  => esc_html__( 'Uppercase', 'groovy-menu' ),
					'capitalize' => esc_html__( 'Capitalize', 'groovy-menu' ),
					'lowercase'  => esc_html__( 'Lowercase', 'groovy-menu' ),
				),
				'default' => 'uppercase',
			),
			'mobile_subitem_text_weight'             => array(
				'title'   => esc_html__( 'Font variant', 'groovy-menu' ),
				'type'    => 'select',
				'options' => array(
					'none' => 'Select value'
				),
				'default' => 'none',
			),
			'mobile_subitem_letter_spacing'          => array(
				'title'   => esc_html__( 'Letter spacing', 'groovy-menu' ),
				'type'    => 'number',
				'range'   => array( 0, 5 ),
				'default' => 0,
				'unit'    => 'px',
			),
			'mobile_subitem_text_end'                => array(
				'type' => 'inlineEnd'
			),
		),
	),

);
