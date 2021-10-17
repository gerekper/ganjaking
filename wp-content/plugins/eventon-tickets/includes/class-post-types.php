<?php
if ( ! defined( 'ABSPATH' ) ) 	exit; // Exit if accessed directly

/**
 * Post types
 *
 * Registers post types and taxonomies
 *
 * @class 		evotx_post_types
 * @version		0.1
 * @package		event-tickets/includes
 * @category	class
 * @author 		AJDE
 */

class evotx_post_types{
	public function __construct() {
		$this->register_tix_post_type();
	}
	// create new post type
		function register_tix_post_type(){

			$labels = eventon_get_proper_labels('Event Ticket','Event Tickets');
			register_post_type('evo-tix', 
				apply_filters( 'eventon_register_post_type_tix',
					array(
						'labels' => $labels,
						'public' 				=> false,
						'show_ui' 				=> true,
						'capability_type' 		=> 'eventon',
						'capabilities'			=>array(
							'create_posts'=> 'do_not_allow'
						),
						'map_meta_cap'			=> true,
						'exclude_from_search'	=> true,
						'publicly_queryable' 	=> false,
						'hierarchical' 			=> false,
						'rewrite' 				=> false,
						'query_var'		 		=> true,
						'supports' 				=> array('title','custom-fields'),				
						'menu_position' 		=> 5, 
						'show_in_menu'			=>'edit.php?post_type=ajde_events',
						'has_archive' 			=> true
					)
				)
			);
		}
	
}
new evotx_post_types();