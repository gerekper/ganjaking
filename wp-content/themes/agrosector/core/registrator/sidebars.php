<?php
if ( ! function_exists( 'gt3_sidebar_generator' ) ) {
	function gt3_sidebar_generator() {
		$sidebars = gt3_option( 'sidebars' );
		if ( ! empty( $sidebars ) ) {
			foreach ( $sidebars as $sidebar ) {
				register_sidebar( array(
					'name'          => esc_attr( $sidebar ),
					'description'   => esc_html__( 'Add the widgets appearance for Custom Sidebar. Drag the widget from the available list on the left, configure widgets options and click Save button. Select the sidebar on the posts or pages in just few clicks.', 'agrosector' ),
					'id'            => "sidebar_" . esc_attr( strtolower( preg_replace( '/[^A-Za-z0-9\-]/', '', str_replace( ' ', '-', $sidebar ) ) ) ),
					'before_widget' => '<div id="%1$s" class="widget gt3_widget open %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h3 class="widget-title">',
					'after_title'   => '</h3>',
				) );
			}
		}
	}

	add_action( 'widgets_init', 'gt3_sidebar_generator' );
}

if ( ! function_exists( 'gt3_register_sidebar' ) ) {
	function gt3_register_sidebar() {
		$footer_1 = array(
			'name'          => esc_html__( 'Column Footer 1', 'agrosector' ),
			'id'            => 'footer_column_1',
			'description'   => esc_html__( 'Display and style the footer area with multiple widgets. Simply drag the widgets from the left, make the adjustments to the widget according the needs. Preview the front end.', 'agrosector' ),
			'before_widget' => '<div id="%1$s" class="widget gt3_widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		);
		register_sidebar( $footer_1 );

		$footer_2 = array(
			'name'          => esc_html__( 'Column Footer 2', 'agrosector' ),
			'id'            => 'footer_column_2',
			'description'   => esc_html__( 'Display and style the footer area with multiple widgets. Simply drag the widgets from the left, make the adjustments to the widget according the needs. Preview the front end.', 'agrosector' ),
			'before_widget' => '<div id="%1$s" class="widget gt3_widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		);
		register_sidebar( $footer_2 );

		$footer_3 = array(
			'name'          => esc_html__( 'Column Footer 3', 'agrosector' ),
			'id'            => 'footer_column_3',
			'description'   => esc_html__( 'Display and style the footer area with multiple widgets. Simply drag the widgets from the left, make the adjustments to the widget according the needs. Preview the front end.', 'agrosector' ),
			'before_widget' => '<div id="%1$s" class="widget gt3_widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		);
		register_sidebar( $footer_3 );

		$footer_4 = array(
			'name'          => esc_html__( 'Column Footer 4', 'agrosector' ),
			'id'            => 'footer_column_4',
			'description'   => esc_html__( 'Display and style the footer area with multiple widgets. Simply drag the widgets from the left, make the adjustments to the widget according the needs. Preview the front end.', 'agrosector' ),
			'before_widget' => '<div id="%1$s" class="widget gt3_widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		);
		register_sidebar( $footer_4 );

		$footer_5 = array(
			'name'          => esc_html__( 'Column Footer 5', 'agrosector' ),
			'id'            => 'footer_column_5',
			'description'   => esc_html__( 'Display and style the footer area with multiple widgets. Simply drag the widgets from the left, make the adjustments to the widget according the needs. Preview the front end.', 'agrosector' ),
			'before_widget' => '<div id="%1$s" class="widget gt3_widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		);
		register_sidebar( $footer_5 );
	}

	add_action( 'widgets_init', 'gt3_register_sidebar' );
}
