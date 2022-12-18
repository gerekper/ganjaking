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
if ( ! function_exists( 'porto_add_event_meta_boxes' ) ) {
	/**
	 * @todo 2.3.0 Legacy Mode
	 */
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
