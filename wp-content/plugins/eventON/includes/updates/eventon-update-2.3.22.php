<?php
/**
 * Update EVO to 2.3.22
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon/Admin/Updates
 * @version     2.3.22
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $eventon;

// All locations auto generate lat long and save them for faster google map drawing
	$locations = get_terms('event_location', array('hide_empty'=>false) );

	if(count($locations)>0){
		foreach ( $locations as $term ) {

	    	$t_id = $term->term_id;
	    	$term_meta = get_option( "taxonomy_$t_id" );

	    	if(empty($term_meta['location_address'])) continue;

	    	$address = stripslashes(str_replace('"', "'", (esc_attr( $term_meta['location_address'] )) )) ;
	    	$latlon = eventon_get_latlon_from_address($address);

	    	// if lat lon generated save those values
	    	$term_meta['location_lon'] = (!empty($term_meta['location_lon']))? $term_meta['location_lon']:
				(!empty($latlon['lng'])? floatval($latlon['lng']): null);

			// longitude
			$term_meta['location_lat'] = (!empty($term_meta['location_lat'] ))? $term_meta['location_lat'] :
				(!empty($latlon['lat'])? floatval($latlon['lat']): null);

			// update the new values to location taxonomy
			update_option("taxonomy_".$t_id, $term_meta);

	    }
	}