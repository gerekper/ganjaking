<?php

// Meta Fields
function porto_block_meta_fields() {

	$fields = array(
		// Layout
		'container'  => array(
			'name'    => 'container',
			'title'   => __( 'Wrap as Container', 'porto-functionality' ),
			'desc'    => '',
			'type'    => 'select',
			'default' => '',
			'options' => array(
				'yes' => __( 'Inner Container', 'porto-functionality' ),
				'fluid'  => __( 'Fluid Container', 'porto-functionality' ),
			),
		),
		'custom_css' => array(
			'name'  => 'custom_css',
			'title' => __( 'Custom CSS', 'porto-functionality' ),
			'type'  => 'textarea',
		),
	);

	if ( current_user_can( 'manage_options' ) ) {
		$fields['custom_js_body'] = array(
			'name'  => 'custom_js_body',
			'title' => __( 'JS Code', 'porto-functionality' ),
			'type'  => 'textarea',
		);
	}

	return $fields;
}

// Show Meta Boxes
add_action( 'add_meta_boxes', 'porto_add_block_meta_boxes' );
function porto_add_block_meta_boxes() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'block' == $screen->id ) {
		add_meta_box( 'block-meta-box', __( 'Block Options', 'porto-functionality' ), 'porto_block_meta_box', 'block', 'normal', 'high' );
	}
}

function porto_block_meta_box() {
	$meta_fields = porto_block_meta_fields();
	porto_show_meta_box( $meta_fields );
}

// Save Meta Values
add_action( 'save_post', 'porto_save_block_meta_values' );
function porto_save_block_meta_values( $post_id ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'post' == $screen->base && ( 'block' == $screen->id || 'product_layout' == $screen->id ) ) {
		porto_save_meta_value( $post_id, porto_block_meta_fields() );
	}
}

// Remove in default custom field meta box
add_filter( 'is_protected_meta', 'porto_block_protected_meta', 10, 3 );
function porto_block_protected_meta( $protected, $meta_key, $meta_type ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $protected;
	}
	$screen = get_current_screen();
	if ( ! $protected && $screen && 'post' == $screen->base && ( 'block' == $screen->id || 'product_layout' == $screen->id ) ) {
		if ( array_key_exists( $meta_key, porto_block_meta_fields() ) ) {
			$protected = true;
		}
	}
	return $protected;
}
