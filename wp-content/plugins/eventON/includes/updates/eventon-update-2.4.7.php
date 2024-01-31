<?php
/**
 * Update EVO to 2.4.7
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon/Admin/Updates
 * @version     2.4.7
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $eventon;

// save location & organizer term meta into one meta field
	
	$options = get_option( "evo_tax_meta");
	foreach(array(
		'event_location','event_organizer'
	) as $tax){
		$terms = get_terms($tax, array(
			'hide_empty' => false
		));
		$debug = '';
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
			foreach($terms as $term){
				$termid = $term->term_id;

				if(empty($termid)) continue;

				$termmeta = get_option( "taxonomy_".$termid);
				
				if(!empty($termmeta)){
					evo_save_term_metas($tax,$termid, $termmeta,'' );
					delete_option('taxonomy_'.$term->term_id);
				}
			}
		}
	}

// change locations saved as evcal_location_name into taxonomy based location 
// with location meta values saved into evo_tax_meta
	$events = new WP_Query(array(
			'post_type'=>'ajde_events',
			'posts_per_page'=>-1
		));

		if($events->have_posts()){
			//echo 'have events';
			while($events->have_posts()): $events->the_post();
				$event_id = $events->post->ID;

				// location
				$location_terms = wp_get_post_terms($event_id, 'event_location');

				// if an event already have location saved with taxonomy method go to next one
				if ( $location_terms && ! is_wp_error( $location_terms ) ) continue;
				

				$pmv_location = get_post_meta($event_id, 'evcal_location_name',true);
				if($pmv_location ){

					$term = term_exists( $pmv_location, 'event_location' );
					if($term !== 0 && $term !== null){
						$taxtermID = (int)$term['term_id'];
						wp_set_object_terms( $event_id, $taxtermID, 'event_location' );						
					}else{
					
						$trans = array(" "=>'-', ","=>'');
						$term_slug= strtr($pmv_location, $trans);

						// create wp term
						$new_term_ = wp_insert_term( $pmv_location, 'event_location' , array('slug'=>$term_slug) );

						if(!is_wp_error($new_term_)){
							$taxtermID = (int)$new_term_['term_id'];

							$epmv = get_post_custom($event_id);

							$term_meta = array();

							if(isset($epmv['evcal_location']))
								$latlon = eventon_get_latlon_from_address($epmv['evcal_location']);

							// longitude
							$term_meta['location_lon'] = (!empty($epmv['evcal_lon']))?$epmv['evcal_lon']:
								(!empty($latlon['lng'])? floatval($latlon['lng']): null);

							// latitude
							$term_meta['location_lat'] = (!empty($epmv['evcal_lat']))?$epmv['evcal_lat']:
								(!empty($latlon['lat'])? floatval($latlon['lat']): null);

							$term_meta['location_address' ] = (isset($epmv[ 'evcal_location' ]))?$epmv[ 'evcal_location' ]:null;

							if(sizeof($term_meta)>0)
								evo_save_term_metas('event_location', $taxtermID, $term_meta);

							wp_set_object_terms( $event_id, $taxtermID, 'event_location' , false);
						}
					}
					
					delete_post_meta($event_id, 'evcal_location_name');
				}
				
			endwhile;
			wp_reset_postdata();
		}