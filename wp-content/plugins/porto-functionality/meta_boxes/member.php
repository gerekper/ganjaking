<?php

// Meta Fields
function porto_member_meta_fields() {

	// Slideshow Types
	$slideshow_types = porto_ct_slideshow_types();

	return array(
		// Fist name
		'member_firstname'  => array(
			'name'  => 'member_firstname',
			'title' => __( 'First Name', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Last name
		'member_lastname'   => array(
			'name'  => 'member_lastname',
			'title' => __( 'Last Name', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Member Role
		'member_role'       => array(
			'name'  => 'member_role',
			'title' => __( 'Role', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Overview
		'member_overview'   => array(
			'name'  => 'member_overview',
			'title' => __( 'Overview', 'porto-functionality' ),
			'type'  => 'editor',
		),
		// Portfolio IDs
		'member_portfolios' => array(
			'name'  => 'member_portfolios',
			'title' => __( 'Portfolio IDs', 'porto-functionality' ),
			'desc'  => __( 'Comma separated list of portfolio ids.', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Product IDs
		'member_products'   => array(
			'name'  => 'member_products',
			'title' => __( 'Product IDs', 'porto-functionality' ),
			'desc'  => __( 'Comma separated list of product ids.', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Post IDs
		'member_posts'      => array(
			'name'  => 'member_posts',
			'title' => __( 'Post IDs', 'porto-functionality' ),
			'desc'  => __( 'Comma separated list of post ids.', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Slideshow Type
		'slideshow_type'    => array(
			'name'    => 'slideshow_type',
			'title'   => __( 'Slideshow Type', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => 'images',
			'options' => $slideshow_types,
		),
		// Video & Audio Embed Code
		'video_code'        => array(
			'name'     => 'video_code',
			'title'    => __( 'Video & Audio Embed Code or Content', 'porto-functionality' ),
			'desc'     => __( 'Paste the iframe code of the Flash (YouTube or Vimeo etc) or Input the shortcodes. Only necessary when the member type is Video & Audio.', 'porto-functionality' ),
			'type'     => 'textarea',
			'required' => array(
				'name'  => 'slideshow_type',
				'value' => 'video',
			),
		),
		// Visit Site Link
		'member_link'       => array(
			'name'  => 'member_link',
			'title' => __( 'Member Link', 'porto-functionality' ),
			'desc'  => __( 'External Link for the Member which adds a visit site button with the link. Leave blank for post URL.', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Email
		'member_email'      => array(
			'name'  => 'member_email',
			'title' => __( 'Email', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Phone
		'member_phone'      => array(
			'name'  => 'member_phone',
			'title' => __( 'Phone', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Facebook
		'member_facebook'   => array(
			'name'  => 'member_facebook',
			'title' => __( 'Facebook', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Twitter
		'member_twitter'    => array(
			'name'  => 'member_twitter',
			'title' => __( 'Twitter', 'porto-functionality' ),
			'type'  => 'text',
		),
		// LinkedIn
		'member_linkedin'   => array(
			'name'  => 'member_linkedin',
			'title' => __( 'LinkedIn', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Google +
		'member_googleplus' => array(
			'name'  => 'member_googleplus',
			'title' => __( 'Google +', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Pinterest
		'member_pinterest'  => array(
			'name'  => 'member_pinterest',
			'title' => __( 'Pinterest', 'porto-functionality' ),
			'type'  => 'text',
		),
		// VK
		'member_vk'         => array(
			'name'  => 'member_vk',
			'title' => __( 'VK', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Xing
		'member_xing'       => array(
			'name'  => 'member_xing',
			'title' => __( 'Xing', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Tumblr
		'member_tumblr'     => array(
			'name'  => 'member_tumblr',
			'title' => __( 'Tumblr', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Reddit
		'member_reddit'     => array(
			'name'  => 'member_reddit',
			'title' => __( 'Reddit', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Vimeo
		'member_vimeo'      => array(
			'name'  => 'member_vimeo',
			'title' => __( 'Vimeo', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Instagram
		'member_instagram'  => array(
			'name'  => 'member_instagram',
			'title' => __( 'Instagram', 'porto-functionality' ),
			'type'  => 'text',
		),
		// WhatsApp
		'member_whatsapp'   => array(
			'name'  => 'member_whatsapp',
			'title' => __( 'WhatsApp', 'porto-functionality' ),
			'type'  => 'text',
		),
	);
}

function porto_member_view_meta_fields() {
	$meta_fields = porto_ct_default_view_meta_fields();
	// Layout
	$meta_fields['layout']['default'] = 'fullwidth';
	return $meta_fields;
}

function porto_member_skin_meta_fields() {
	$meta_fields = porto_ct_default_skin_meta_fields();
	return $meta_fields;
}

// Show Meta Boxes
add_action( 'add_meta_boxes', 'porto_add_member_meta_boxes' );
function porto_add_member_meta_boxes() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	global $porto_settings;
	$screen = get_current_screen();
	if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'member' == $screen->id ) {
		add_meta_box( 'member-meta-box', __( 'Member Options', 'porto-functionality' ), 'porto_member_meta_box', 'member', 'normal', 'high' );
		add_meta_box( 'view-meta-box', __( 'View Options', 'porto-functionality' ), 'porto_member_view_meta_box', 'member', 'normal', 'low' );
		if ( $porto_settings['show-content-type-skin'] ) {
			add_meta_box( 'skin-meta-box', __( 'Skin Options', 'porto-functionality' ), 'porto_member_skin_meta_box', 'member', 'normal', 'low' );
		}
	}
}

function porto_member_meta_box() {
	$meta_fields = porto_member_meta_fields();
	porto_show_meta_box( $meta_fields );
}

function porto_member_view_meta_box() {
	$meta_fields = porto_member_view_meta_fields();
	porto_show_meta_box( $meta_fields );
}

function porto_member_skin_meta_box() {
	$meta_fields = porto_member_skin_meta_fields();
	porto_show_meta_box( $meta_fields );
}

// Save Meta Values
add_action( 'save_post', 'porto_save_member_meta_values' );
function porto_save_member_meta_values( $post_id ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'post' == $screen->base && 'member' == $screen->id ) {
		porto_save_meta_value( $post_id, porto_member_meta_fields() );
		porto_save_meta_value( $post_id, porto_member_view_meta_fields() );
		porto_save_meta_value( $post_id, porto_member_skin_meta_fields() );
	}
}

// Remove in default custom field meta box
add_filter( 'is_protected_meta', 'porto_member_protected_meta', 10, 3 );
function porto_member_protected_meta( $protected, $meta_key, $meta_type ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $protected;
	}
	$screen = get_current_screen();
	if ( ! $protected && $screen && 'post' == $screen->base && 'member' == $screen->id ) {
		if ( array_key_exists( $meta_key, porto_member_meta_fields() )
			|| array_key_exists( $meta_key, porto_member_view_meta_fields() )
			|| array_key_exists( $meta_key, porto_member_skin_meta_fields() ) ) {
			$protected = true;
		}
	}
	return $protected;
}

////////////////////////////////////////////////////////////////////////

// Taxonomy Meta Fields
function porto_member_cat_meta_fields() {
	global $porto_settings;

	$meta_fields = porto_ct_default_view_meta_fields();

	// Member Options
	$meta_fields = array_insert_before(
		'loading_overlay',
		$meta_fields,
		'member_options',
		array(
			'name'  => 'member_options',
			'title' => __( 'Archive Options', 'porto-functionality' ),
			'desc'  => __( 'Change default theme options.', 'porto-functionality' ),
			'type'  => 'checkbox',
		)
	);

	// Infinite Scroll
	$meta_fields = array_insert_after(
		'loading_overlay',
		$meta_fields,
		'member_infinite',
		array(
			'name'     => 'member_infinite',
			'title'    => __( 'Infinite Scroll', 'porto-functionality' ),
			'desc'     => __( 'Disable infinite scroll.', 'porto-functionality' ),
			'type'     => 'checkbox',
			'required' => array(
				'name'  => 'member_options',
				'value' => 'member_options',
			),
		)
	);

	if ( isset( $porto_settings['show-category-skin'] ) && $porto_settings['show-category-skin'] ) {
		$meta_fields = array_merge( $meta_fields, porto_ct_default_skin_meta_fields( true ) );
	}

	return $meta_fields;
}

$taxonomy             = 'member_cat';
$table_name           = $wpdb->prefix . $taxonomy . 'meta';
$variable_name        = $taxonomy . 'meta';
$wpdb->$variable_name = $table_name;

// Add Meta Fields when edit taxonomy
add_action( 'member_cat_edit_form_fields', 'porto_edit_member_cat_meta_fields', 100, 2 );
function porto_edit_member_cat_meta_fields( $tag, $taxonomy ) {
	if ( 'member_cat' !== $taxonomy ) {
		return;
	}
	porto_edit_tax_meta_fields( $tag, $taxonomy, porto_member_cat_meta_fields() );
}

// Save Meta Values
add_action( 'edit_term', 'porto_save_member_cat_meta_values', 100, 3 );
function porto_save_member_cat_meta_values( $term_id, $tt_id, $taxonomy ) {
	if ( 'member_cat' !== $taxonomy ) {
		return;
	}
	porto_create_tax_meta_table( $taxonomy );
	return porto_save_tax_meta_values( $term_id, $taxonomy, porto_member_cat_meta_fields() );
}

// Delete Meta Values
add_action( 'delete_term', 'porto_delete_member_cat_meta_values', 10, 5 );
function porto_delete_member_cat_meta_values( $term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
	if ( 'member_cat' !== $taxonomy ) {
		return;
	}
	return porto_delete_tax_meta_values( $term_id, $taxonomy, porto_member_cat_meta_fields() );
}
