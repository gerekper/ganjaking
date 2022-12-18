<?php

/**
 * @see   porto/inc/soft-mode/setup.php
 * @since 2.3.0 Removed meta fields because of soft mode. Instead add meta fields options in theme about legacy mode.
 */
function porto_ct_default_view_meta_fields() {

	$theme_layouts   = porto_ct_layouts();
	$sidebar_options = porto_ct_sidebars();

	$fields = array(
		// Page Title
		'page_title'     => array(
			'name'  => 'page_title',
			'title' => __( 'Page Title', 'porto-functionality' ),
			'desc'  => __( 'Do not Show', 'porto-functionality' ),
			'type'  => 'checkbox',
		),
		// Page Sub Title
		'page_sub_title' => array(
			'name'     => 'page_sub_title',
			'title'    => __( 'Page Sub Title', 'porto-functionality' ),
			'type'     => 'text',
			'required' => array(
				'name'  => 'page_title',
				'value' => '',
			),
		),
		// Layout, Sidebar
		'default'        => array(
			'name'  => 'default',
			'title' => __( 'Layout & Sidebar', 'porto-functionality' ),
			'desc'  => __( 'Use selected layout and sidebar options.', 'porto-functionality' ),
			'type'  => 'checkbox',
		),
		// Layout
		'layout'         => array(
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
		'sidebar'        => array(
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
		'sidebar2'       => array(
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
		'sticky_sidebar' => array(
			'name'    => 'sticky_sidebar',
			'title'   => __( 'Sticky Sidebar', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => porto_ct_enable_options(),
		),
		// Mobile Sidebar
		'mobile_sidebar' => array(
			'name'    => 'mobile_sidebar',
			'title'   => 'Show Mobile Sidebar',
			'desc'    => __( 'Show Sidebar in Navigation on mobile', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => porto_ct_enable_options(),
		),
	);

	return apply_filters( 'porto_view_meta_fields', $fields );
}

/**
 * @see   porto/inc/soft-mode/setup.php
 * @since 2.3.0 Removed meta fields because of soft mode. Instead add meta fields options in theme about legacy mode.
 */
function porto_ct_default_skin_meta_fields( $tax_meta_fields = false ) {

	$fields = array(
		'custom_css' => array(
			'name'  => 'custom_css',
			'title' => __( 'Custom CSS', 'porto-functionality' ),
			'type'  => 'textarea',
		),
	);

	if ( current_user_can( 'manage_options' ) ) {
		// JS Code before </head>
		$fields['custom_js_head'] = array(
			'name'  => 'custom_js_head',
			'title' => __( 'JS Code before &lt;/head&gt;', 'porto-functionality' ),
			'type'  => 'textarea',
		);
		// JS Code before </body>
		$fields['custom_js_body'] = array(
			'name'  => 'custom_js_body',
			'title' => __( 'JS Code before &lt;/body&gt;', 'porto-functionality' ),
			'type'  => 'textarea',
		);
	}

	return apply_filters( 'porto_skin_meta_fields', $fields, $tax_meta_fields );
}
