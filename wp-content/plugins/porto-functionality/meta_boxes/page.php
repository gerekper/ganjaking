<?php

// Meta Fields
function porto_page_meta_fields() {

	return array(
		// Share
		'page_share'     => array(
			'name'    => 'page_share',
			'title'   => __( 'Share', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => porto_ct_share_options(),
		),
		// Microdata Rich Snippets
		'page_microdata' => array(
			'name'    => 'page_microdata',
			'title'   => __( 'Microdata Rich Snippets', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => porto_ct_enable_options(),
		),
	);
}

function porto_page_view_meta_fields() {
	$meta_fields = porto_ct_default_view_meta_fields();
	// Layout
	$meta_fields['layout']['default'] = 'fullwidth';
	return $meta_fields;
}

function porto_page_skin_meta_fields() {
	$meta_fields = porto_ct_default_skin_meta_fields();
	return $meta_fields;
}

// Show Meta Boxes
add_action( 'add_meta_boxes', 'porto_add_page_meta_boxes' );
function porto_add_page_meta_boxes() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	global $porto_settings;
	$screen = get_current_screen();
	if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'page' == $screen->id ) {
		add_meta_box( 'page-meta-box', __( 'Page Options', 'porto-functionality' ), 'porto_page_meta_box', 'page', 'normal', 'high' );
		add_meta_box( 'view-meta-box', __( 'View Options', 'porto-functionality' ), 'porto_page_view_meta_box', 'page', 'normal', 'low' );
		if ( $porto_settings['show-content-type-skin'] ) {
			add_meta_box( 'skin-meta-box', __( 'Skin Options', 'porto-functionality' ), 'porto_page_skin_meta_box', 'page', 'normal', 'low' );
		}
	}
}

function porto_page_meta_box() {
	$meta_fields = porto_page_meta_fields();
	porto_show_meta_box( $meta_fields );
}

function porto_page_view_meta_box() {
	$meta_fields = porto_page_view_meta_fields();
	porto_show_meta_box( $meta_fields );
}

function porto_page_skin_meta_box() {
	$meta_fields = porto_page_skin_meta_fields();
	porto_show_meta_box( $meta_fields );
}

// Save Meta Values
add_action( 'save_post', 'porto_save_page_meta_values' );
function porto_save_page_meta_values( $post_id ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'post' == $screen->base && 'page' == $screen->id ) {
		porto_save_meta_value( $post_id, porto_page_meta_fields() );
		porto_save_meta_value( $post_id, porto_page_view_meta_fields() );
		porto_save_meta_value( $post_id, porto_page_skin_meta_fields() );
	}
}

// Remove in default custom field meta box
add_filter( 'is_protected_meta', 'porto_page_protected_meta', 10, 3 );
function porto_page_protected_meta( $protected, $meta_key, $meta_type ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $protected;
	}
	$screen = get_current_screen();
	if ( ! $protected && $screen && 'post' == $screen->base && 'page' == $screen->id ) {
		if ( array_key_exists( $meta_key, porto_page_meta_fields() )
			|| array_key_exists( $meta_key, porto_page_view_meta_fields() )
			|| array_key_exists( $meta_key, porto_page_skin_meta_fields() ) ) {
			$protected = true;
		}
	}
	return $protected;
}
