<?php

function porto_ct_default_view_meta_fields() {

	global $porto_settings;

	$theme_layouts   = porto_ct_layouts();
	$sidebar_options = porto_ct_sidebars();
	$banner_pos      = porto_ct_banner_pos();
	$banner_type     = porto_ct_banner_type();
	$header_view     = porto_ct_header_view();
	$footer_view     = porto_ct_footer_view();
	$master_sliders  = porto_ct_master_sliders();
	$rev_sliders     = porto_ct_rev_sliders();

	// Get menus
	$menus        = wp_get_nav_menus( array( 'orderby' => 'name' ) );
	$menu_options = array();
	if ( ! empty( $menus ) ) {
		foreach ( $menus as $menu ) {
			$menu_options[ $menu->term_id ] = $menu->name;
		}
	}

	if ( function_exists( 'porto_options_breadcrumbs_types' ) ) {
		$breadcrumb_types = porto_options_breadcrumbs_types();
		foreach ( $breadcrumb_types as $key => $b ) {
			$breadcrumb_types[ $key ] = $b['alt'];
		}
	} else {
		$breadcrumb_types = array();
	}

	$fields = array(
		// Loading Overlay
		'loading_overlay'      => array(
			'name'    => 'loading_overlay',
			'title'   => __( 'Loading Overlay', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => porto_ct_show_options(),
		),
		// Breadcrumbs
		'breadcrumbs'          => array(
			'name'  => 'breadcrumbs',
			'title' => __( 'Breadcrumbs', 'porto-functionality' ),
			'desc'  => __( 'Do not Show', 'porto-functionality' ),
			'type'  => 'checkbox',
		),
		// Breadcrumb Type
		'breadcrumbs_type'     => array(
			'name'     => 'breadcrumbs_type',
			'title'    => __( 'Breadcrumbs Type', 'porto-functionality' ),
			'type'     => 'select',
			'required' => array(
				'name'  => 'breadcrumbs',
				'value' => '',
			),
			'std'      => '',
			'options'  => $breadcrumb_types,
		),
		// Page Title
		'page_title'           => array(
			'name'  => 'page_title',
			'title' => __( 'Page Title', 'porto-functionality' ),
			'desc'  => __( 'Do not Show', 'porto-functionality' ),
			'type'  => 'checkbox',
		),
		// Page Sub Title
		'page_sub_title'       => array(
			'name'     => 'page_sub_title',
			'title'    => __( 'Page Sub Title', 'porto-functionality' ),
			'type'     => 'text',
			'required' => array(
				'name'  => 'page_title',
				'value' => '',
			),
		),
		// Header
		'header'               => array(
			'name'  => 'header',
			'title' => __( 'Header', 'porto-functionality' ),
			'desc'  => __( 'Do not Show', 'porto-functionality' ),
			'type'  => 'checkbox',
		),
		// Sticky Header
		'sticky_header'        => array(
			'name'     => 'sticky_header',
			'title'    => __( 'Sticky Header', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => '',
			'required' => array(
				'name'  => 'header',
				'value' => '',
			),
			'options'  => porto_ct_show_options(),
		),
		// Header View
		'header_view'          => array(
			'name'     => 'header_view',
			'title'    => __( 'Header View', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => $porto_settings['header-view'],
			'required' => array(
				'name'  => 'header',
				'value' => '',
			),
			'options'  => $header_view,
		),
		// Footer
		'footer'               => array(
			'name'  => 'footer',
			'title' => __( 'Footer', 'porto-functionality' ),
			'desc'  => __( 'Do not Show', 'porto-functionality' ),
			'type'  => 'checkbox',
		),
		// Footer View
		'footer_view'          => array(
			'name'     => 'footer_view',
			'title'    => __( 'Footer View', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => '',
			'required' => array(
				'name'  => 'footer',
				'value' => '',
			),
			'options'  => $footer_view,
		),
		// Main Menu
		'main_menu'            => array(
			'name'    => 'main_menu',
			'title'   => __( 'Main Menu', 'porto-functionality' ),
			'type'    => 'select',
			'default' => '',
			'options' => $menu_options,
		),
		// Secondary Menu
		'secondary_menu'       => array(
			'name'    => 'secondary_menu',
			'title'   => __( 'Secondary Menu', 'porto-functionality' ),
			'type'    => 'select',
			'default' => '',
			'options' => $menu_options,
		),
		// Sidebar Menu
		'sidebar_menu'         => array(
			'name'    => 'sidebar_menu',
			'title'   => __( 'Sidebar Menu', 'porto-functionality' ),
			'type'    => 'select',
			'default' => '',
			'options' => $menu_options,
		),
		// Layout, Sidebar
		'default'              => array(
			'name'  => 'default',
			'title' => __( 'Layout & Sidebar', 'porto-functionality' ),
			'desc'  => __( 'Use selected layout and sidebar options.', 'porto-functionality' ),
			'type'  => 'checkbox',
		),
		// Layout
		'layout'               => array(
			'name'     => 'layout',
			'title'    => __( 'Layout', 'porto-functionality' ),
			'type'     => 'select',
			'default'  => 'right-sidebar',
			'required' => array(
				'name'  => 'default',
				'value' => 'default',
			),
			'options'  => $theme_layouts,
		),
		// Sidebar
		'sidebar'              => array(
			'name'     => 'sidebar',
			'title'    => __( 'Sidebar', 'porto-functionality' ),
			'desc'     => __( '<strong>Note</strong>: You can create the sidebar under <strong>Appearance > Sidebars</strong>', 'porto-functionality' ),
			'type'     => 'select',
			'default'  => '',
			'required' => array(
				'name'  => 'default',
				'value' => 'default',
			),
			'options'  => $sidebar_options,
		),
		// Sidebar
		'sidebar2'             => array(
			'name'     => 'sidebar2',
			'title'    => __( 'Sidebar 2', 'porto-functionality' ),
			'desc'     => __( '<strong>Note</strong>: You can create the sidebar under <strong>Appearance > Sidebars</strong>', 'porto-functionality' ),
			'type'     => 'select',
			'default'  => '',
			'required' => array(
				'name'  => 'layout',
				'value' => 'wide-both-sidebar,both-sidebar',
			),
			'options'  => $sidebar_options,
		),
		// Sticky Sidebar
		'sticky_sidebar'       => array(
			'name'    => 'sticky_sidebar',
			'title'   => __( 'Sticky Sidebar', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => porto_ct_enable_options(),
		),
		// Mobile Sidebar
		'mobile_sidebar'       => array(
			'name'    => 'mobile_sidebar',
			'title'   => 'Show Mobile Sidebar',
			'desc'    => __( 'Show Sidebar in Navigation on mobile', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => porto_ct_enable_options(),
		),
		// Banner Position
		'banner_pos'           => array(
			'name'    => 'banner_pos',
			'title'   => __( 'Banner Position', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => $banner_pos,
		),
		// Banner Type
		'banner_type'          => array(
			'name'    => 'banner_type',
			'title'   => __( 'Banner Type', 'porto-functionality' ),
			'type'    => 'select',
			'options' => $banner_type,
		),
		// Revolution Slider
		'rev_slider'           => array(
			'name'     => 'rev_slider',
			'title'    => __( 'Revolution Slider', 'porto-functionality' ),
			'desc'     => __( 'Please select <strong>Banner Type</strong> to <strong>Revolution Slider</strong> and select a slider.', 'porto-functionality' ),
			'type'     => 'select',
			'required' => array(
				'name'  => 'banner_type',
				'value' => 'rev_slider',
			),
			'options'  => $rev_sliders,
		),
		// Master Slider
		'master_slider'        => array(
			'name'     => 'master_slider',
			'title'    => __( 'Master Slider', 'porto-functionality' ),
			'desc'     => __( 'Please select <strong>Banner Type</strong> to <strong>Master Slider</strong> and select a slider.', 'porto-functionality' ),
			'type'     => 'select',
			'required' => array(
				'name'  => 'banner_type',
				'value' => 'master_slider',
			),
			'options'  => $master_sliders,
		),
		// Banner
		'banner_block'         => array(
			'name'     => 'banner_block',
			'title'    => __( 'Banner Block', 'porto-functionality' ),
			'desc'     => __( 'Please select <strong>Banner Type</strong> to <strong>Banner Block</strong> and input a block slug name. You can create a block in <strong>Porto -> Templates Builder -> Add New</strong>.', 'porto-functionality' ),
			'type'     => 'text',
			'required' => array(
				'name'  => 'banner_type',
				'value' => 'banner_block',
			),
		),
		// Content Top
		'content_top'          => array(
			'name'  => 'content_top',
			'title' => __( 'Content Top', 'porto-functionality' ),
			'desc'  => __( 'Please input comma separated block slug names.', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Content Inner Top
		'content_inner_top'    => array(
			'name'  => 'content_inner_top',
			'title' => __( 'Content Inner Top', 'porto-functionality' ),
			'desc'  => __( 'Please input comma separated block slug names.', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Content Inner Bottom
		'content_inner_bottom' => array(
			'name'  => 'content_inner_bottom',
			'title' => __( 'Content Inner Bottom', 'porto-functionality' ),
			'desc'  => __( 'Please input comma separated block slug names.', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Content Bottom
		'content_bottom'       => array(
			'name'  => 'content_bottom',
			'title' => __( 'Content Bottom', 'porto-functionality' ),
			'desc'  => __( 'Please input comma separated block slug names.', 'porto-functionality' ),
			'type'  => 'text',
		),
	);
	if ( function_exists( 'porto_header_type_is_preset' ) && porto_header_type_is_preset() && '19' != porto_get_header_type() ) {
		unset( $fields['secondary_menu'] );
	}
	return apply_filters( 'porto_view_meta_fields', $fields );
}

function porto_ct_default_skin_meta_fields( $tax_meta_fields = false ) {

	$bg_repeat     = porto_ct_bg_repeat();
	$bg_size       = porto_ct_bg_size();
	$bg_attachment = porto_ct_bg_attachment();
	$bg_position   = porto_ct_bg_position();

	if ( ! $tax_meta_fields ) {
		$tabs = array(
			'body'           => array( 'body', __( 'Body', 'porto-functionality' ) ),
			'header'         => array( 'header', __( 'Header', 'porto-functionality' ) ),
			'sticky_header'  => array( 'sticky_header', __( 'Sticky Header', 'porto-functionality' ) ),
			'breadcrumbs'    => array( 'breadcrumbs', __( 'Breadcrumbs', 'porto-functionality' ) ),
			'page'           => array( 'page', __( 'Page Content', 'porto-functionality' ) ),
			'content_bottom' => array( 'content_bottom', __( 'Content Bottom Widgets Area', 'porto-functionality' ) ),
			'footer_top'     => array( 'footer_top', __( 'Footer Top Widget Area', 'porto-functionality' ) ),
			'footer'         => array( 'footer', __( 'Footer', 'porto-functionality' ) ),
			'footer_main'    => array( 'footer_main', __( 'Footer Widgets Area', 'porto-functionality' ) ),
			'footer_bottom'  => array( 'footer_bottom', __( 'Footer Bottom Widget Area', 'porto-functionality' ) ),
		);
	} else {
		$tabs = array(
			'body'           => array( 'body', __( 'Body Background', 'porto-functionality' ) ),
			'header'         => array( 'header', __( 'Header Background', 'porto-functionality' ) ),
			'sticky_header'  => array( 'sticky_header', __( 'Sticky Header Background', 'porto-functionality' ) ),
			'breadcrumbs'    => array( 'breadcrumbs', __( 'Breadcrumbs Background', 'porto-functionality' ) ),
			'page'           => array( 'page', __( 'Page Content Background', 'porto-functionality' ) ),
			'content_bottom' => array( 'content_bottom', __( 'Content Bottom Widgets Area Background', 'porto-functionality' ) ),
			'footer_top'     => array( 'footer_top', __( 'Footer Top Widget Area Background', 'porto-functionality' ) ),
			'footer'         => array( 'footer', __( 'Footer Background', 'porto-functionality' ) ),
			'footer_main'    => array( 'footer_main', __( 'Footer Widgets Area Background', 'porto-functionality' ) ),
			'footer_bottom'  => array( 'footer_bottom', __( 'Footer Bottom Widget Area Background', 'porto-functionality' ) ),
		);
	}

	$return = array();

	foreach ( $tabs as $key => $value ) {
		$return[ $key . '_bg_color' ]      = array(
			'name'  => $key . '_bg_color',
			'title' => __( 'Background Color', 'porto-functionality' ),
			'type'  => 'color',
			'tab'   => $value,
		);
		$return[ $key . '_bg_image' ]      = array(
			'name'  => $key . '_bg_image',
			'title' => __( 'Background Image', 'porto-functionality' ),
			'type'  => 'upload',
			'tab'   => $value,
		);
		$return[ $key . '_bg_repeat' ]     = array(
			'name'    => $key . '_bg_repeat',
			'title'   => __( 'Background Repeat', 'porto-functionality' ),
			'type'    => 'select',
			'options' => $bg_repeat,
			'tab'     => $value,
		);
		$return[ $key . '_bg_size' ]       = array(
			'name'    => $key . '_bg_size',
			'title'   => __( 'Background Size', 'porto-functionality' ),
			'type'    => 'select',
			'options' => $bg_size,
			'tab'     => $value,
		);
		$return[ $key . '_bg_attachment' ] = array(
			'name'    => $key . '_bg_attachment',
			'title'   => __( 'Background Attachment', 'porto-functionality' ),
			'type'    => 'select',
			'options' => $bg_attachment,
			'tab'     => $value,
		);
		$return[ $key . '_bg_position' ]   = array(
			'name'    => $key . '_bg_position',
			'title'   => __( 'Background Position', 'porto-functionality' ),
			'type'    => 'select',
			'options' => $bg_position,
			'tab'     => $value,
		);
	}

	// Custom CSS
	$return = array_insert_before(
		'body_bg_color',
		$return,
		'custom_css',
		array(
			'name'  => 'custom_css',
			'title' => __( 'Custom CSS', 'porto-functionality' ),
			'type'  => 'textarea',
		)
	);

	if ( current_user_can( 'manage_options' ) ) {
		// JS Code before </head>
		$return = array_insert_before(
			'body_bg_color',
			$return,
			'custom_js_head',
			array(
				'name'  => 'custom_js_head',
				'title' => __( 'JS Code before &lt;/head&gt;', 'porto-functionality' ),
				'type'  => 'textarea',
			)
		);

		// JS Code before </body>
		$return = array_insert_before(
			'body_bg_color',
			$return,
			'custom_js_body',
			array(
				'name'  => 'custom_js_body',
				'title' => __( 'JS Code before &lt;/body&gt;', 'porto-functionality' ),
				'type'  => 'textarea',
			)
		);
	}

	return apply_filters( 'porto_skin_meta_fields', $return );
}
