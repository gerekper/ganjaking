<?php

// Meta Fields
function porto_portfolio_meta_fields() {

	// Slideshow Types
	$slideshow_types = porto_ct_slideshow_types();

	return array(
		// Archive Image
		'portfolio_archive_image' => array(
			'name'  => 'portfolio_archive_image',
			'title' => __( 'Change Featured Image', 'porto-functionality' ),
			'desc'  => __( 'Change featured image on Archives, Carousel, etc.', 'porto-functionality' ),
			'type'  => 'attach',
		),
		// Slideshow Type
		'slideshow_type'          => array(
			'name'    => 'slideshow_type',
			'title'   => __( 'Slideshow Type', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => 'images',
			'options' => $slideshow_types,
		),
		// Slider Type
		'slider_type'             => array(
			'name'     => 'slider_type',
			'title'    => __( 'Slider Type', 'porto-functionality' ),
			'type'     => 'radio',
			'desc'     => __( 'Use in slider portfolio layout.', 'porto-functionality' ),
			'default'  => '',
			'options'  => array(
				''               => __( 'Default', 'porto-functionality' ),
				'without-thumbs' => __( 'Without Thumbs', 'porto-functionality' ),
				'with-thumbs'    => __( 'With Thumbs', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'slideshow_type',
				'value' => 'images',
			),
		),
		// Slider Thumbs Count
		'slider_thumbs_count'     => array(
			'name'     => 'slider_thumbs_count',
			'title'    => __( 'Slider Thumbs Count', 'porto-functionality' ),
			'type'     => 'text',
			'desc'     => __( 'Use in slider portfolio layout.', 'porto-functionality' ),
			'default'  => '4',
			'required' => array(
				'name'  => 'slideshow_type',
				'value' => 'images',
			),
		),
		// Video & Audio Embed Code
		'video_code'              => array(
			'name'     => 'video_code',
			'title'    => __( 'Video & Audio Embed Code or Content', 'porto-functionality' ),
			'desc'     => __( 'Paste the iframe code of the Flash (YouTube or Vimeo etc) or Input the shortcodes. Only necessary when the portfolio type is Video & Audio.', 'porto-functionality' ),
			'type'     => 'textarea',
			'required' => array(
				'name'  => 'slideshow_type',
				'value' => 'video',
			),
		),
		// More Information
		'portfolio_info'          => array(
			'name'  => 'portfolio_info',
			'title' => __( 'More Information', 'porto-functionality' ),
			'type'  => 'editor',
		),
		// Visit Site Link
		'portfolio_link'          => array(
			'name'  => 'portfolio_link',
			'title' => __( 'Portfolio Link', 'porto-functionality' ),
			'desc'  => __( 'External Link for the Portfolio which adds a <strong>Live Preview</strong> button with the link. Leave blank for portfolio URL.', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Location
		'portfolio_location'      => array(
			'name'  => 'portfolio_location',
			'title' => __( 'Location', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Client Name
		'portfolio_client'        => array(
			'name'  => 'portfolio_client',
			'title' => __( 'Client Name', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Client URL
		'portfolio_client_link'   => array(
			'name'  => 'portfolio_client_link',
			'title' => __( 'Client URL(Link)', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Author Quote
		'portfolio_author_quote'  => array(
			'name'  => 'portfolio_author_quote',
			'title' => __( 'Author Quote', 'porto-functionality' ),
			'type'  => 'textarea',
		),
		// Author Name
		'portfolio_author_name'   => array(
			'name'  => 'portfolio_author_name',
			'title' => __( 'Author Name', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Author Image
		'portfolio_author_image'  => array(
			'name'  => 'portfolio_author_image',
			'title' => __( 'Author Image', 'porto-functionality' ),
			'type'  => 'upload',
		),
		// Author Role
		'portfolio_author_role'   => array(
			'name'  => 'portfolio_author_role',
			'title' => __( 'Author Role', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Layout
		'portfolio_layout'        => array(
			'name'    => 'portfolio_layout',
			'title'   => __( 'Portfolio Layout', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => 'default',
			'options' => array_merge(
				array(
					'default' => __( 'Default', 'porto-functionality' ),
				),
				porto_ct_portfolio_single_layouts()
			),
		),
		// Share
		'portfolio_share'         => array(
			'name'    => 'portfolio_share',
			'title'   => __( 'Share', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => porto_ct_share_options(),
		),
		// Like Count
		'like_count'              => array(
			'name'    => 'like_count',
			'title'   => __( 'Like Count', 'porto-functionality' ),
			'type'    => 'text',
			'default' => __( '0', 'porto-functionality' ),
		),
	);
}

function porto_portfolio_view_meta_fields() {
	$meta_fields = porto_ct_default_view_meta_fields();
	// Layout
	$meta_fields['layout']['default'] = 'fullwidth';
	return $meta_fields;
}

function porto_portfolio_skin_meta_fields() {
	$meta_fields = porto_ct_default_skin_meta_fields();
	return $meta_fields;
}

// Show Meta Boxes
add_action( 'add_meta_boxes', 'porto_add_portfolio_meta_boxes' );
function porto_add_portfolio_meta_boxes() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	global $porto_settings;
	$screen = get_current_screen();
	if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'portfolio' == $screen->id ) {
		add_meta_box( 'portfolio-meta-box', __( 'Portfolio Options', 'porto-functionality' ), 'porto_portfolio_meta_box', 'portfolio', 'normal', 'high' );
		add_meta_box( 'view-meta-box', __( 'View Options', 'porto-functionality' ), 'porto_portfolio_view_meta_box', 'portfolio', 'normal', 'low' );
		if ( $porto_settings['show-content-type-skin'] ) {
			add_meta_box( 'skin-meta-box', __( 'Skin Options', 'porto-functionality' ), 'porto_portfolio_skin_meta_box', 'portfolio', 'normal', 'low' );
		}
	}
}

function porto_portfolio_meta_box() {
	$meta_fields = porto_portfolio_meta_fields();
	porto_show_meta_box( $meta_fields );
}

function porto_portfolio_view_meta_box() {
	$meta_fields = porto_portfolio_view_meta_fields();
	porto_show_meta_box( $meta_fields );
}

function porto_portfolio_skin_meta_box() {
	$meta_fields = porto_portfolio_skin_meta_fields();
	porto_show_meta_box( $meta_fields );
}

// Save Meta Values
add_action( 'save_post', 'porto_save_portfolio_meta_values' );
function porto_save_portfolio_meta_values( $post_id ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'post' == $screen->base && 'portfolio' == $screen->id ) {
		porto_save_meta_value( $post_id, porto_portfolio_meta_fields() );
		porto_save_meta_value( $post_id, porto_portfolio_view_meta_fields() );
		porto_save_meta_value( $post_id, porto_portfolio_skin_meta_fields() );
	}
}

// Remove in default custom field meta box
add_filter( 'is_protected_meta', 'porto_portfolio_protected_meta', 10, 3 );
function porto_portfolio_protected_meta( $protected, $meta_key, $meta_type ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $protected;
	}
	$screen = get_current_screen();
	if ( ! $protected && $screen && 'post' == $screen->base && 'portfolio' == $screen->id ) {
		if ( array_key_exists( $meta_key, porto_portfolio_meta_fields() )
			|| array_key_exists( $meta_key, porto_portfolio_view_meta_fields() )
			|| array_key_exists( $meta_key, porto_portfolio_skin_meta_fields() ) ) {
			$protected = true;
		}
	}
	return $protected;
}

////////////////////////////////////////////////////////////////////////

// Taxonomy Meta Fields
function porto_portfolio_cat_meta_fields() {
	global $porto_settings;

	$meta_fields = porto_ct_default_view_meta_fields();
	// Category Image
	$meta_fields = array_insert_before(
		'loading_overlay',
		$meta_fields,
		'category_image',
		array(
			'name'  => 'category_image',
			'title' => __( 'Category Image', 'porto-functionality' ),
			'type'  => 'upload',
		)
	);
	// Portfolio Options
	$meta_fields = array_insert_before(
		'loading_overlay',
		$meta_fields,
		'portfolio_options',
		array(
			'name'  => 'portfolio_options',
			'title' => __( 'Archive Options', 'porto-functionality' ),
			'desc'  => __( 'Change default theme options.', 'porto-functionality' ),
			'type'  => 'checkbox',
		)
	);

	// Infinite Scroll
	$meta_fields = array_insert_after(
		'portfolio_options',
		$meta_fields,
		'portfolio_infinite',
		array(
			'name'     => 'portfolio_infinite',
			'title'    => __( 'Infinite Scroll', 'porto-functionality' ),
			'desc'     => __( 'Disable infinite scroll.', 'porto-functionality' ),
			'type'     => 'checkbox',
			'required' => array(
				'name'  => 'portfolio_options',
				'value' => 'portfolio_options',
			),
		)
	);

	// Layout
	$meta_fields = array_insert_after(
		'portfolio_infinite',
		$meta_fields,
		'portfolio_layout',
		array(
			'name'     => 'portfolio_layout',
			'title'    => __( 'Portfolio Layout', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => 'grid',
			'options'  => porto_ct_portfolio_archive_layouts(),
			'required' => array(
				'name'  => 'portfolio_options',
				'value' => 'portfolio_options',
			),
		)
	);
	// Grid Columns
	$meta_fields = array_insert_after(
		'portfolio_layout',
		$meta_fields,
		'portfolio_grid_columns',
		array(
			'name'     => 'portfolio_grid_columns',
			'title'    => __( 'Columns in Grid, Masonry Layout', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => '4',
			'options'  => array(
				'1' => __( '1 Column', 'porto-functionality' ),
				'2' => __( '2 Columns', 'porto-functionality' ),
				'3' => __( '3 Columns', 'porto-functionality' ),
				'4' => __( '4 Columns', 'porto-functionality' ),
				'5' => __( '5 Columns', 'porto-functionality' ),
				'6' => __( '6 Columns', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'portfolio_options',
				'value' => 'portfolio_options',
			),
		)
	);
	// Grid View
	$meta_fields = array_insert_after(
		'portfolio_grid_columns',
		$meta_fields,
		'portfolio_grid_view',
		array(
			'name'     => 'portfolio_grid_view',
			'title'    => __( 'View Type in Grid, Masonry Layout', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => 'default',
			'options'  => array(
				'default'  => __( 'Default', 'porto-functionality' ),
				'full'     => __( 'No Margin', 'porto-functionality' ),
				'outimage' => __( 'Out of Image', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'portfolio_options',
				'value' => 'portfolio_options',
			),
		)
	);
	// Info View Type
	$meta_fields = array_insert_after(
		'portfolio_grid_view',
		$meta_fields,
		'portfolio_archive_thumb',
		array(
			'name'     => 'portfolio_archive_thumb',
			'title'    => __( 'Info View Type in Grid, Masonry, Timeline Layout', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => 'left-info',
			'options'  => array(
				'left-info'        => __( 'Left Info', 'porto-functionality' ),
				'centered-info'    => __( 'Centered Info', 'porto-functionality' ),
				'bottom-info'      => __( 'Bottom Info', 'porto-functionality' ),
				'bottom-info-dark' => __( 'Bottom Info Dark', 'porto-functionality' ),
				'hide-info-hover'  => __( 'Hide Info Hover', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'portfolio_options',
				'value' => 'portfolio_options',
			),
		)
	);
	// Image Overlay Background
	$meta_fields = array_insert_after(
		'portfolio_archive_thumb',
		$meta_fields,
		'portfolio_archive_thumb_bg',
		array(
			'name'     => 'portfolio_archive_thumb_bg',
			'title'    => __( 'Image Overlay Background', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => 'darken',
			'options'  => array(
				'darken'          => __( 'Darken', 'porto-functionality' ),
				'lighten'         => __( 'Lighten', 'porto-functionality' ),
				'hide-wrapper-bg' => __( 'Transparent', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'portfolio_options',
				'value' => 'portfolio_options',
			),
		)
	);
	// Image Hover Effect
	$meta_fields = array_insert_after(
		'portfolio_archive_thumb_bg',
		$meta_fields,
		'portfolio_archive_thumb_image',
		array(
			'name'     => 'portfolio_archive_thumb_image',
			'title'    => __( 'Hover Image Effect', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => 'zoom',
			'options'  => array(
				'zoom'    => __( 'Zoom', 'porto-functionality' ),
				'no-zoom' => __( 'No Zoom', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'portfolio_options',
				'value' => 'portfolio_options',
			),
		)
	);

	if ( isset( $porto_settings['show-category-skin'] ) && $porto_settings['show-category-skin'] ) {
		$meta_fields = array_merge( $meta_fields, porto_ct_default_skin_meta_fields( true ) );
	}

	return $meta_fields;
}

$taxonomy             = 'portfolio_cat';
$table_name           = $wpdb->prefix . $taxonomy . 'meta';
$variable_name        = $taxonomy . 'meta';
$wpdb->$variable_name = $table_name;

// Add Meta Fields when edit taxonomy
add_action( 'portfolio_cat_edit_form_fields', 'porto_edit_portfolio_cat_meta_fields', 100, 2 );
function porto_edit_portfolio_cat_meta_fields( $tag, $taxonomy ) {
	if ( 'portfolio_cat' !== $taxonomy ) {
		return;
	}
	porto_edit_tax_meta_fields( $tag, $taxonomy, porto_portfolio_cat_meta_fields() );
}

// Save Meta Values
add_action( 'edit_term', 'porto_save_portfolio_cat_meta_values', 100, 3 );
function porto_save_portfolio_cat_meta_values( $term_id, $tt_id, $taxonomy ) {
	if ( 'portfolio_cat' !== $taxonomy ) {
		return;
	}
	porto_create_tax_meta_table( $taxonomy );
	return porto_save_tax_meta_values( $term_id, $taxonomy, porto_portfolio_cat_meta_fields() );
}

// Delete Meta Values
add_action( 'delete_term', 'porto_delete_portfolio_cat_meta_values', 10, 5 );
function porto_delete_portfolio_cat_meta_values( $term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
	if ( 'portfolio_cat' !== $taxonomy ) {
		return;
	}
	return porto_delete_tax_meta_values( $term_id, $taxonomy, porto_portfolio_cat_meta_fields() );
}
