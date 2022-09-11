<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Post types
 *
 * Registers post types and taxonomies
 *
 * @class 		EVO_post_types
 * @version		2.2.9
 * @package		Eventon/Classes/events
 * @category	Class
 * @author 		AJDE
 */

class EVO_post_types{

	private static $evOpt='';
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		self::$evOpt = get_option('evcal_options_evcal_1');
	}

	// Register eventon taxonomies.
	public static function register_taxonomies() {
		// Taxonomies
		do_action( 'eventon_register_taxonomy' );		
		
		$evOpt = self::$evOpt;

		$__capabilities = array(
			'manage_terms' 		=> 'manage_eventon_terms',
			'edit_terms' 		=> 'edit_eventon_terms',
			'delete_terms' 		=> 'delete_eventon_terms',
			'assign_terms' 		=> 'assign_eventon_terms',
		);

		$_name = __('Event Location','eventon');
		$_names = __('Event Locations','eventon');
		register_taxonomy( 'event_location', 
			apply_filters( 'eventon_taxonomy_objects_event_location', array('ajde_events') ),
			apply_filters( 'eventon_taxonomy_args_event_location', array(
				'hierarchical' => false, 
				'labels' => array(
                    'name' 				=> sprintf(__( "%s", 'eventon' ), $_name ),
                    'singular_name' 	=> sprintf(__( "%s", 'eventon' ), $_name ),
					'menu_name'			=> _x( $_name, 'Admin menu name', 'eventon' ),
                    'search_items' 		=> sprintf(__( "Search %s", 'eventon' ), $_names ),
                    'all_items' 		=> sprintf(__( "All %s", 'eventon' ), $_names ),
                    'parent_item' 		=> sprintf(__( "Parent %s", 'eventon' ), $_name ),
                    'parent_item_colon' => sprintf(__( "Parent %s:", 'eventon' ), $_name ),
                    'edit_item' 		=> sprintf(__( "Edit %s", 'eventon' ), $_name ),
                    'update_item' 		=> sprintf(__( "Update %s", 'eventon' ), $_name ),
                    'add_new_item' 		=> sprintf(__( "Add New %s", 'eventon' ), $_name ),
                    'new_item_name' 	=> sprintf(__( "New %s", 'eventon' ), $_name ),
                    'back_to_items' 	=> sprintf(__( "Back to %s", 'eventon' ), $_name ),
            	),
				'public' => true,
				'show_ui' => true,
				'query_var' => true,
				'show_in_quick_edit'         => false,
				'meta_box_cb'                => false,
				'capabilities'	=> $__capabilities,
				'rewrite' => apply_filters('evotax_slug_loc', array( 'slug' => 'event-location' ) )
			)) 
		);
		$_name = __('Event Organizer','eventon');
		$_names = __('Event Organizers','eventon');
		register_taxonomy( 'event_organizer', 
			apply_filters( 'eventon_taxonomy_objects_event_organizer', array('ajde_events') ),
			apply_filters( 'eventon_taxonomy_args_event_organizer', array(
				'hierarchical' => false, 
				'labels' => array(
                    'name' 				=> sprintf(__( "%s", 'eventon' ), $_name),
                    'singular_name' 	=> sprintf(__( "%s", 'eventon' ), $_name),
					'menu_name'			=> _x( $_name, 'Admin menu name', 'eventon' ),
                    'search_items' 		=> sprintf(__( "Search %s", 'eventon' ), $_names),
                    'all_items' 		=> sprintf(__( "All %s", 'eventon' ), $_names),
                    'parent_item' 		=> sprintf(__( "Parent %s", 'eventon' ), $_name),
                    'parent_item_colon' => sprintf(__( "Parent %s:", 'eventon' ), $_name),
                    'edit_item' 		=> sprintf(__( "Edit %s", 'eventon' ), $_name),
                    'update_item' 		=> sprintf(__( "Update %s", 'eventon' ), $_name),
                    'add_new_item' 		=> sprintf(__( "Add New %s", 'eventon' ), $_name),
                    'new_item_name' 	=> sprintf(__( "New %s", 'eventon' ), $_name),
                    'back_to_items' 	=> sprintf(__( "Back to %s", 'eventon' ), $_name ),
            	),
				'show_ui' => true,
				'query_var' => true,
				'show_in_quick_edit'         => false,
				'meta_box_cb'                => false,
				'capabilities'			=> $__capabilities,
				'rewrite' => apply_filters('evotax_slug_org', array( 'slug' => 'event-organizer' ) )
			)) 
		);

		// Event type custom taxonomy NAMES
			$event_type_names = evo_get_ettNames($evOpt);

			// for each activated event type category
			for($x=1; $x<=evo_get_ett_count($evOpt); $x++){
				$ab = ($x==1)? '':'_'.$x;
				$ab2 = ($x==1)? '':'-'.$x;
				$evt_name = $event_type_names[$x];

				register_taxonomy( 'event_type'.$ab, 
					apply_filters( 'eventon_taxonomy_objects_event_type'.$ab, array('ajde_events') ),
					apply_filters( 'eventon_taxonomy_args_event_type'.$ab, array(
						'hierarchical' => true, 
						'labels' => array(
			                    'name' 				=> sprintf(__( "%s Categories", 'eventon' ), $evt_name),
			                    'singular_name' 	=> sprintf(__( "%s Category", 'eventon' ), $evt_name),
								'menu_name'			=> _x( $evt_name, 'Admin menu name', 'eventon' ),
			                    'search_items' 		=> sprintf(__( "Search %s Categories", 'eventon' ), $evt_name),
			                    'all_items' 		=> sprintf(__( "All %s Categories", 'eventon' ), $evt_name),
			                    'parent_item' 		=> sprintf(__( "Parent %s Category", 'eventon' ), $evt_name),
			                    'parent_item_colon' => sprintf(__( "Parent %s Category:", 'eventon' ), $evt_name),
			                    'edit_item' 		=> sprintf(__( "Edit %s Category", 'eventon' ), $evt_name),
			                    'update_item' 		=> sprintf(__( "Update %s Category", 'eventon' ), $evt_name),
			                    'add_new_item' 		=> sprintf(__( "Add New %s Category", 'eventon' ), $evt_name),
			                    'new_item_name' 	=> sprintf(__( "New %s Category Name", 'eventon' ), $evt_name)
			            	),
						'show_ui' => true,
						'show_in_rest' => true,
						'query_var' => true,
						'capabilities'			=> $__capabilities,
						'rewrite' => array( 'slug' => 'event-type'.$ab2 ) 
					)) 
				);
			}
	}
	


	/** Register core post types */
	public static function register_post_types() {
		if ( post_type_exists('ajde_events') )
			return;

		do_action( 'eventon_register_post_type' );

		// get updated event slug for evnet posts
		$evOpt = self::$evOpt;
		$event_slug = (!empty($evOpt['evo_event_slug']))? $evOpt['evo_event_slug']: 'events';
		
		$sin_name = (!empty($evOpt['evo_textstr_sin']))? $evOpt['evo_textstr_sin']: __('Event','eventon');
		$plu_name = (!empty($evOpt['evo_textstr_plu']))? $evOpt['evo_textstr_plu']: __('Events','eventon');

		register_post_type('ajde_events', 
			apply_filters( 'eventon_register_post_type_ajde_events',
				array(
					'labels' => array(
						/*'name'                  => __( 'Events', 'eventon' ),
							'singular_name'         => __( 'Event', 'eventon' ),
							'menu_name'             => _x( 'Events', 'Admin menu name', 'eventon' ),
							'add_new'               => __( 'Add Event', 'eventon' ),
							'add_new_item'          => __( 'Add New Event', 'eventon' ),
							'edit'                  => __( 'Edit', 'eventon' ),
							'edit_item'             => __( 'Edit Event', 'eventon' ),
							'new_item'              => __( 'New Event', 'eventon' ),
							'view'                  => __( 'View Event', 'eventon' ),
							'view_item'             => __( 'View Event', 'eventon' ),
							'search_items'          => __( 'Search Events', 'eventon' ),
							'not_found'             => __( 'No Events found', 'eventon' ),
							'not_found_in_trash'    => __( 'No Events found in trash', 'eventon' ),
							'parent'                => __( 'Parent Event', 'eventon' ),
							'featured_image'        => __( 'Event Image', 'eventon' ),
							'set_featured_image'    => __( 'Set event image', 'eventon' ),
							'remove_featured_image' => __( 'Remove event image', 'eventon' ),
							'use_featured_image'    => __( 'Use as event image', 'eventon' ),
							'insert_into_item'      => __( 'Insert into event', 'eventon' ),
							'uploaded_to_this_item' => __( 'Uploaded to this event', 'eventon' ),
							'filter_items_list'     => __( 'Filter Events', 'eventon' ),
							'items_list_navigation' => __( 'Events navigation', 'eventon' ),
							'items_list'            => __( 'Events list', 'eventon' ),
						*/
						'name'                  => $plu_name,
						'singular_name'         => $sin_name,
						'menu_name'             => $plu_name,
						'add_new'               => sprintf(__( 'Add %s','eventon'), $sin_name ),
						'add_new_item'          => sprintf(__( 'Add New %s','eventon'),$sin_name ),
						'edit'                  => __( 'Edit', 'eventon' ),
						'edit_item'             => sprintf(__( 'Edit %s','eventon'),$sin_name ),
						'new_item'              => sprintf(__( 'New %s','eventon'),$sin_name ),
						'view'                  => sprintf(__( 'View %s','eventon'),$sin_name ),
						'view_item'             => sprintf(__( 'View %s','eventon'),$sin_name ),
						'search_items'          => sprintf(__( 'Search %s', 'eventon' ), $plu_name),
						'not_found'             => sprintf(__( 'No %s found', 'eventon' ), $plu_name),
						'not_found_in_trash'    => sprintf(__( 'No %s found in trash', 'eventon' ), $plu_name),
						'parent'                => sprintf(__( 'Parent %s', 'eventon' ), $sin_name),
						'featured_image'        => sprintf(__( '%s Image', 'eventon' ), $sin_name),
						'set_featured_image'    => sprintf(__( 'Set %s image', 'eventon' ), $sin_name),
						'remove_featured_image' => sprintf(__( 'Remove %s image', 'eventon' ), $sin_name),
						'use_featured_image'    => sprintf(__( 'Use as %s image', 'eventon' ), $sin_name),
						'insert_into_item'      => sprintf(__( 'Insert into %s', 'eventon' ), $sin_name),
						'uploaded_to_this_item' => sprintf(__( 'Uploaded to this %s', 'eventon' ), $sin_name),
						'filter_items_list'     => sprintf(__( 'Filter %s','eventon' ), $plu_name),
						'items_list_navigation' => sprintf(__(  '%s navigation', 'eventon' ), $plu_name),
						'items_list'            => sprintf(__(  '%s list', 'eventon' ), $plu_name),
					),
					'description' 			=> __( 'This is where you can add new events to your calendar.', 'eventon' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'eventon',
					'map_meta_cap'			=> true,
					'publicly_queryable' 	=> true,
					'hierarchical' 			=> false,
					'rewrite' 				=> apply_filters('eventon_event_slug', array(
						'slug'=>$event_slug
					)),
					'query_var'		 		=> true,
					'show_in_rest'			=> true,
					'supports' 				=> apply_filters('eventon_event_post_supports', array('title','author', 'editor','custom-fields','thumbnail','page-attributes','comments')),
					//'supports' 			=> array('title','editor','thumbnail','page-attributes'),
					'menu_position' 		=> 15, 
					'has_archive' 			=> true,
					'taxonomies'			=> array('post_tag'),
					'exclude_from_search'	=> apply_filters('evo_cpt_search_visibility',true),
					'template'				=>array(
						array(
							'core/pattern', array(
								'slug' => 'ajde_events/blocks'
							)
						)
					)
				)
			)
		);
	}
}

new EVO_post_types();
