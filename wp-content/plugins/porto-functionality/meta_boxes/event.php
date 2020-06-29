<?php
// Meta Fields
function porto_event_meta_fields() {
	// Slideshow Types
	$slideshow_types = porto_ct_slideshow_types();
	return array(
		// Visit Site Link
		'event_link'         => array(
			'name'  => 'event_link',
			'title' => __( 'Event Link', 'porto-functionality' ),
			'desc'  => __( 'External Link for the Event which adds a <strong>Live Preview</strong> button with the link. Leave blank for event URL.', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Event Start Date
		'event_start_date'   => array(
			'name'  => 'event_start_date',
			'title' => __( 'Event Start Date', 'porto-functionality' ),
			'type'  => 'text',
			'desc'  => __( 'Date format should be: <strong>yyyy/mm/dd</strong>', 'porto-functionality' ),
		),
		// Event End Date
		'event_end_date'     => array(
			'name'  => 'event_end_date',
			'title' => __( 'Event End Date', 'porto-functionality' ),
			'type'  => 'text',
			'desc'  => __( 'Date format should be: <strong>yyyy/mm/dd</strong>', 'porto-functionality' ),
		),
		// Event Start Time
		'event_start_time'   => array(
			'name'  => 'event_start_time',
			'title' => __( 'Event Start Time', 'porto-functionality' ),
			'type'  => 'text',
			'desc'  => __( 'Time should be in 12 hours format: <strong>12:00 AM/PM</strong>', 'porto-functionality' ),
		),
		// Event End Time
		'event_end_time'     => array(
			'name'  => 'event_end_time',
			'title' => __( 'Event End Time', 'porto-functionality' ),
			'type'  => 'text',
			'desc'  => __( 'Time should be in 12 hours format: <strong>12:00 AM/PM</strong>', 'porto-functionality' ),
		),
		// Event Location
		'event_location'     => array(
			'name'  => 'event_location',
			'title' => __( 'Event Location', 'porto-functionality' ),
			'type'  => 'text',
		),
		// Event Time Counter
		'event_time_counter' => array(
			'name'    => 'event_time_counter',
			'title'   => __( 'Event Time Counter', 'porto-functionality' ),
			'type'    => 'radio',
			'default' => '',
			'options' => array(
				''     => __( 'Default', 'porto-functionality' ),
				'show' => __( 'Show', 'porto-functionality' ),
				'hide' => __( 'Hide', 'porto-functionality' ),
			),
		),
	);
}
function porto_event_view_meta_fields() {
	$meta_fields = porto_ct_default_view_meta_fields();
	// Layout
	$meta_fields['layout']['default'] = 'fullwidth';
	return $meta_fields;
}
function porto_event_skin_meta_fields() {
	$meta_fields = porto_ct_default_skin_meta_fields();
	return $meta_fields;
}
// Show Meta Boxes
add_action( 'add_meta_boxes', 'porto_add_event_meta_boxes' );
function porto_add_event_meta_boxes() {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	global $porto_settings;
	$screen = get_current_screen();
	if ( function_exists( 'add_meta_box' ) && $screen && 'post' == $screen->base && 'event' == $screen->id ) {
		add_meta_box( 'event-meta-box', __( 'Event Options', 'porto-functionality' ), 'porto_event_meta_box', 'event', 'normal', 'high' );
		add_meta_box( 'view-meta-box', __( 'View Options', 'porto-functionality' ), 'porto_event_view_meta_box', 'event', 'normal', 'low' );
		if ( $porto_settings['show-content-type-skin'] ) {
			add_meta_box( 'skin-meta-box', __( 'Skin Options', 'porto-functionality' ), 'porto_event_skin_meta_box', 'event', 'normal', 'low' );
		}
	}
}
function porto_event_meta_box() {
	$meta_fields = porto_event_meta_fields();
	porto_show_meta_box( $meta_fields );
}
function porto_event_view_meta_box() {
	$meta_fields = porto_event_view_meta_fields();
	porto_show_meta_box( $meta_fields );
}
function porto_event_skin_meta_box() {
	$meta_fields = porto_event_skin_meta_fields();
	porto_show_meta_box( $meta_fields );
}
// Save Meta Values
add_action( 'save_post', 'porto_save_event_meta_values' );
function porto_save_event_meta_values( $post_id ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'post' == $screen->base && 'event' == $screen->id ) {
		porto_save_meta_value( $post_id, porto_event_meta_fields() );
		porto_save_meta_value( $post_id, porto_event_view_meta_fields() );
		porto_save_meta_value( $post_id, porto_event_skin_meta_fields() );
	}
}
// Remove in default custom field meta box
add_filter( 'is_protected_meta', 'porto_event_protected_meta', 10, 3 );
function porto_event_protected_meta( $protected, $meta_key, $meta_type ) {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $protected;
	}
	$screen = get_current_screen();
	if ( ! $protected && $screen && 'post' == $screen->base && 'event' == $screen->id ) {
		if ( array_key_exists( $meta_key, porto_event_meta_fields() )
			|| array_key_exists( $meta_key, porto_event_view_meta_fields() )
			|| array_key_exists( $meta_key, porto_event_skin_meta_fields() ) ) {
			$protected = true;
		}
	}
	return $protected;
}
////////////////////////////////////////////////////////////////////////
// Taxonomy Meta Fields
function porto_event_cat_meta_fields() {
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
	// Event Options
	$meta_fields = array_insert_before(
		'loading_overlay',
		$meta_fields,
		'event_options',
		array(
			'name'  => 'event_options',
			'title' => __( 'Archive Options', 'porto-functionality' ),
			'desc'  => __( 'Change default theme options.', 'porto-functionality' ),
			'type'  => 'checkbox',
		)
	);
	// Infinite Scroll
	$meta_fields = array_insert_after(
		'event_options',
		$meta_fields,
		'event_infinite',
		array(
			'name'     => 'event_infinite',
			'title'    => __( 'Infinite Scroll', 'porto-functionality' ),
			'desc'     => __( 'Disable infinite scroll.', 'porto-functionality' ),
			'type'     => 'checkbox',
			'required' => array(
				'name'  => 'event_options',
				'value' => 'event_options',
			),
		)
	);
	// Layout
	$meta_fields = array_insert_after(
		'event_infinite',
		$meta_fields,
		'event_layout',
		array(
			'name'     => 'event_layout',
			'title'    => __( 'Event Layout', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => 'grid',
			'options'  => porto_ct_event_archive_layouts(),
			'required' => array(
				'name'  => 'event_options',
				'value' => 'event_options',
			),
		)
	);
	// Grid Columns
	$meta_fields = array_insert_after(
		'event_layout',
		$meta_fields,
		'event_grid_columns',
		array(
			'name'     => 'event_grid_columns',
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
				'name'  => 'event_options',
				'value' => 'event_options',
			),
		)
	);
	// Grid View
	$meta_fields = array_insert_after(
		'event_grid_columns',
		$meta_fields,
		'event_grid_view',
		array(
			'name'     => 'event_grid_view',
			'title'    => __( 'View Type in Grid, Masonry Layout', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => 'default',
			'options'  => array(
				'default'  => __( 'Default', 'porto-functionality' ),
				'full'     => __( 'No Margin', 'porto-functionality' ),
				'outimage' => __( 'Out of Image', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'event_options',
				'value' => 'event_options',
			),
		)
	);
	// Info View Type
	$meta_fields = array_insert_after(
		'event_grid_view',
		$meta_fields,
		'event_archive_thumb',
		array(
			'name'     => 'event_archive_thumb',
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
				'name'  => 'event_options',
				'value' => 'event_options',
			),
		)
	);
	// Image Overlay Background
	$meta_fields = array_insert_after(
		'event_archive_thumb',
		$meta_fields,
		'event_archive_thumb_bg',
		array(
			'name'     => 'event_archive_thumb_bg',
			'title'    => __( 'Image Overlay Background', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => 'darken',
			'options'  => array(
				'darken'          => __( 'Darken', 'porto-functionality' ),
				'lighten'         => __( 'Lighten', 'porto-functionality' ),
				'hide-wrapper-bg' => __( 'Transparent', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'event_options',
				'value' => 'event_options',
			),
		)
	);
	// Image Hover Effect
	$meta_fields = array_insert_after(
		'event_archive_thumb_bg',
		$meta_fields,
		'event_archive_thumb_image',
		array(
			'name'     => 'event_archive_thumb_image',
			'title'    => __( 'Hover Image Effect', 'porto-functionality' ),
			'type'     => 'radio',
			'default'  => 'zoom',
			'options'  => array(
				'zoom'    => __( 'Zoom', 'porto-functionality' ),
				'no-zoom' => __( 'No Zoom', 'porto-functionality' ),
			),
			'required' => array(
				'name'  => 'event_options',
				'value' => 'event_options',
			),
		)
	);
	if ( isset( $porto_settings['show-category-skin'] ) && $porto_settings['show-category-skin'] ) {
		$meta_fields = array_merge( $meta_fields, porto_ct_default_skin_meta_fields( true ) );
	}
	return $meta_fields;
}
$taxonomy             = 'event_cat';
$table_name           = $wpdb->prefix . $taxonomy . 'meta';
$variable_name        = $taxonomy . 'meta';
$wpdb->$variable_name = $table_name;
// Add Meta Fields when edit taxonomy
add_action( 'event_cat_edit_form_fields', 'porto_edit_event_cat_meta_fields', 100, 2 );
function porto_edit_event_cat_meta_fields( $tag, $taxonomy ) {
	if ( 'event_cat' !== $taxonomy ) {
		return;
	}
	porto_edit_tax_meta_fields( $tag, $taxonomy, porto_event_cat_meta_fields() );
}
// Save Meta Values
add_action( 'edit_term', 'porto_save_event_cat_meta_values', 100, 3 );
function porto_save_event_cat_meta_values( $term_id, $tt_id, $taxonomy ) {
	if ( 'event_cat' !== $taxonomy ) {
		return;
	}
	porto_create_tax_meta_table( $taxonomy );
	return porto_save_tax_meta_values( $term_id, $taxonomy, porto_event_cat_meta_fields() );
}
// Delete Meta Values
add_action( 'delete_term', 'porto_delete_event_cat_meta_values', 10, 5 );
function porto_delete_event_cat_meta_values( $term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {
	if ( 'event_cat' !== $taxonomy ) {
		return;
	}
	return porto_delete_tax_meta_values( $term_id, $taxonomy, porto_event_cat_meta_fields() );
}
