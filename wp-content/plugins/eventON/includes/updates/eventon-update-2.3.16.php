<?php
/**
 * Update EVO to 2.3.16
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon/Admin/Updates
 * @version     2.3.16
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $eventon;

// save location and organizer ID on event posts
	$events = new WP_Query(array(
		'post_type'=>'ajde_events',
		'posts_per_page'=>-1
	));
	
	if($events->have_posts()){
		while($events->have_posts()): $events->the_post();
			$event_id = $events->post->ID;

			// location
			$location_terms = wp_get_post_terms($event_id, 'event_location');
			
			// organizer			
			$organizer_terms = wp_get_post_terms($event_id, 'event_organizer');
						
		endwhile;
		wp_reset_postdata();
	}

