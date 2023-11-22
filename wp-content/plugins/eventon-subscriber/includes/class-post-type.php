<?php
if ( ! defined( 'ABSPATH' ) )	exit; 

/**
 * Post types
 *
 * Registers post types and taxonomies
 *
 * @class 		evosb_post_types
 * @version		0.1
 * @category	Class
 * @author 		AJDE
 */

class evosb_post_types{

	public function __construct(){
		add_action( 'init', array( __CLASS__, 'register_post_type' ), 5 );
	}

	// create new post type
		public static function register_post_type(){
			$labels = eventon_get_proper_labels('Subscriber','Subscribers');
			register_post_type('evo-subscriber', 
				apply_filters( 'eventon_register_post_type_subscriber',
					array(
						'labels' => $labels,
						'description'	=> 'Subscribers for eventon events',
						'public' 				=> false,
						'show_ui' 				=> true,
						'capability_type' 		=> 'eventon',
						'map_meta_cap'			=> true,
						'publicly_queryable' 	=> false,
						'hierarchical' 			=> false,
						'query_var'		 		=> true,
						'supports' 				=> array('title', 'custom-fields'),					
						'menu_position' 		=> 5, 
						'show_in_menu'			=>'edit.php?post_type=ajde_events',
						'has_archive' 			=> true,
						'exclude_from_search'	=> true,
						'publicly_queryable'	=> false
					)
				)
			);
		}
		
}
new evosb_post_types();