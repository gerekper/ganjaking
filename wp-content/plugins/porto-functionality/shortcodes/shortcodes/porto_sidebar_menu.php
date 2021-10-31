<?php
// Porto Sidebar Menu
if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'porto/porto-sidebar-menu',
		array(
			'attributes'      => array(
				'title'    => array(
					'type' => 'string',
				),
				'nav_menu' => array(
					'type'    => 'string',
					'default' => '',
				),
				'el_class' => array(
					'type' => 'string',
				),
			),
			'editor_script'   => 'porto_blocks',
			'render_callback' => 'porto_shortcode_sidebar_menu',
		)
	);
}

add_action( 'vc_after_init', 'porto_load_sidebar_menu_shortcode' );

function porto_shortcode_sidebar_menu( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_sidebar_menu' ) ) {
		if ( isset( $atts['className'] ) ) {
			$atts['el_class'] = $atts['className'];
		}
		include $template;
	}
	return ob_get_clean();
}

function porto_load_sidebar_menu_shortcode() {

	$custom_class = porto_vc_custom_class();
	$custom_menus = array();
	$menus        = get_terms(
		array(
			'taxonomy'   => 'nav_menu',
			'hide_empty' => false,
		)
	);
	if ( is_array( $menus ) && ! empty( $menus ) ) {
		foreach ( $menus as $single_menu ) {
			if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->term_id ) ) {
				$custom_menus[ $single_menu->name ] = $single_menu->term_id;
			}
		}
	}

	vc_map(
		array(
			'name'         => __( 'Sidebar Menu', 'porto-functionality' ),
			'base'         => 'porto_sidebar_menu',
			'class'        => 'porto_sidebar_menu',
			'icon'         => 'far fa-list-alt',
			'category'     => __( 'Porto', 'porto-functionality' ),
			'description'  => __( 'Add a sidebar menu to the page.', 'porto-functionality' ),
			'is_container' => false,
			'params'       => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Menu', 'porto-functionality' ),
					'param_name'  => 'nav_menu',
					'value'       => $custom_menus,
					/* translators: opening and closing bold tags */
					'description' => empty( $custom_menus ) ? sprintf( esc_html__( 'Custom menus not found. Please visit %1$sAppearance > Menus%2$s page to create new menu.', 'porto-functionality' ), '<b>', '</b>' ) : esc_html__( 'Select menu to display.', 'porto-functionality' ),
					'admin_label' => true,
					'save_always' => true,
				),
				$custom_class,
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_sidebar_menu extends WPBakeryShortCode {
		}
	}
}
