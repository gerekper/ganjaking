<?php
/*
Plugin Name: SearchWP DirectoryPress Integration
Description: Integrate DirectoryPress' Advanced Search features with SearchWP
Version: 1.7.0
Author: SearchWP
Author URI: https://searchwp.com/
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SEARCHWP_DIRECTORYPRESS_VERSION' ) ) {
	define( 'SEARCHWP_DIRECTORYPRESS_VERSION', '1.7.0' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_DirectoryPress_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_directorypress_update_check(){

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	// environment check
	if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_STORE_URL' ) ) {
		return false;
	}

	// SearchWP 4 compat.
	if ( class_exists( '\\SearchWP\\License' ) ) {
		$license_key = \SearchWP\License::get_key();
	} else {
		$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
		$license_key = sanitize_text_field( $license_key );
	}

	// instantiate the updater to prep the environment
	$searchwp_directorypress_updater = new SWP_DirectoryPress_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 31026,
			'version'   => SEARCHWP_DIRECTORYPRESS_VERSION,
			'license'   => $license_key,
			'item_name' => 'DirectoryPress Integration',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_directorypress_updater;
}

add_action( 'admin_init', 'searchwp_directorypress_update_check' );

class SearchWP_DirectoryPress {

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		// solve the issue of 'empty' searches that are intended to limit to a category or Advanced Search limiter
		// if there is no search string tell SearchWP to skip searching and let WordPress take care of it
		if ( isset( $_GET['s'] ) ) {
			$mysearchquery = trim( esc_attr( urldecode( $_GET['s'] ) ) );
			if ( empty( $mysearchquery ) && $mysearchquery != '+' ) {
				add_filter( 'searchwp_short_circuit', '__return_true' );
				add_filter( 'searchwp\native\short_circuit', '__return_true' );
			} else {
				// we need to limit the results pool based on values chosen from the dropdown(s)
				add_filter( 'searchwp_include', array( $this, 'dp_advanced_search_limits' ), 10, 3 );
				add_filter( 'searchwp\query\mods', array( $this, 'add_mods' ), 10, 2 );
			}
		}
	}

	function add_mods( $mods, $query ) {
		// We need to loop through all post types and add a Mod for each.
		foreach ( $query->get_engine()->get_sources() as $source ) {
			$flag = 'post' . SEARCHWP_SEPARATOR;

			if ( 0 !== strpos( $source->get_name(), $flag ) ) {
				continue;
			}

			$mod = new \SearchWP\Mod( $source );
			$mod->raw_where_sql( function( $runtime_mod, $args ) {
				$ids = $this->dp_advanced_search_limits( array(), null, null );
				$ids = array_map( 'absint', (array) $ids );

				return empty( $ids ) ? '' : "{$runtime_mod->get_foreign_alias()}.id IN (" . implode( ',', $ids ) . ')';
			} );

			$mods[] = $mod;
		}

		return $mods;
	}

	// conditionally limit the search results pool to posts that meet the appropriate criteria
	function dp_advanced_search_limits( $ids, $engine, $terms ) {

		// all of the advanced search limiters are based on taxonomies, which is great, let's see which ones are populated
		$listing_category       = isset( $_GET['cat1'] )                 ? intval( $_GET['cat1'] ) : false;
		$listing_industry       = isset( $_GET['tx_industrycategory'] )  ? sanitize_text_field( $_GET['tx_industrycategory'] ) : false;
		$listing_product_type   = isset( $_GET['tx_producttype'] )       ? sanitize_text_field( $_GET['tx_producttype'] ) : false;
		$listing_supplier_type  = isset( $_GET['tx_suppliertype'] )      ? sanitize_text_field( $_GET['tx_suppliertype'] ) : false;
		$listing_country        = isset( $_GET['map-country1--4'] )      ? sanitize_text_field( urldecode( $_GET['map-country1--4'] ) ) : false;    // TODO: is this GET var a coincidence from a customer support request?
		$listing_zip            = isset( $_GET['zipcode'] )              ? sanitize_text_field( $_GET['zipcode'] ) : false;

		// check to see if any user-defined filtration is going on
		if ( ! empty( $listing_category ) || ! empty( $listing_industry ) || ! empty( $listing_product_type ) || ! empty( $listing_supplier_type ) || ! empty( $listing_zip ) ) {

			// we need to limit the results
			$args = array(
				'post_type'         => 'listing_type',
				'nopaging'          => true,
				'fields'            => 'ids',
				'suppress_filters'  => true,
			);

			// if limiting by zip we need to pre-fetch (code borrowed from class_white_label_themes.php query_where() function
			if ( ! empty( $listing_zip ) ) {

				$zip_args = $args;
				$zip_listings = array();

				// we want to limit by zip code so we're going to pre-fetch these IDs and then use them to limit via post__in
				$saved_searches = get_option( 'wlt_saved_zipcodes' );
				$range = 0; // range in KM

				if ( isset( $_GET['radius'] ) && is_numeric( $_GET['radius'] ) && 0 != $_GET['radius'] ) {
					$range = absint( $_GET['radius'] );
				}
				if ( $range > 0 ){

					if ( isset( $saved_searches[ $_GET['zipcode'] ] ) && 1 < strlen( $saved_searches[ $_GET['zipcode'] ] ['log'] ) && 1 < strlen( $saved_searches[ $_GET['zipcode'] ]['lat'] ) ) {
						$longitude = sanitize_text_field( $saved_searches[ $_GET['zipcode'] ]['log'] );
						$latitude = sanitize_text_field( $saved_searches[ $_GET['zipcode'] ]['lat'] );
					} else {
						$geocode = wp_remote_get( esc_attr( 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode( $_GET['zipcode'] ) . '&sensor=false' ) );
						$output = json_decode( $geocode['body'] );
						if ( isset( $output->error_message ) && current_user_can( 'manage_options' ) ) {
							$GLOBALS['error_message'] = $output->error_message;
						} else {
							$longitude = sanitize_text_field( $output->results[0]->geometry->location->lng );
							$latitude = sanitize_text_field( $output->results[0]->geometry->location->lat );
							$saved_searches[ $_GET['zipcode'] ] = array( 'log' => $longitude, 'lat' => $latitude );
							update_option( 'wlt_saved_zipcodes', $saved_searches );
						}
					}
					/*** validate ***/
					if ( isset( $longitude ) && isset( $latitude ) && is_numeric( $longitude ) && is_numeric( $latitude ) ) {
						// Find Max - Min Lat / Long for Radius and zero point and query
						$lat_range = $range / 69.172;
						$lon_range = abs( $range / ( cos( $latitude ) * 69.172 ) );
						$min_lat = (float) number_format( $latitude - $lat_range, '4', '.', '' );
						$max_lat = (float) number_format( $latitude + $lat_range, '4', '.', '' );
						$min_lon = (float) number_format( $longitude - $lon_range, '4', '.', '' );
						$max_lon = (float) number_format( $longitude + $lon_range, '4', '.', '' );

						// set up our zip args
						// we need to fire two separate queries here, one for both lat AND long and another for OR exact zip match
						$zip_args['meta_query'] = array(
							'relation' => 'AND',
							array(
								'key' => 'map-lat',
								'value' => array( (float) $min_lat, (float) $max_lat ),
								'compare' => 'BETWEEN',
							),
							array(
								'key' => 'map-log',
								'value' => array( (float) $max_lon, (float) $min_lon ),
								'compare' => 'BETWEEN',
							),
						);
						$zip_listings_lat_long_range = get_posts( $zip_args );

						$zip_args['meta_query'] = array(
							array(
								'key' => 'map-zip',
								'value' => sanitize_text_field( strip_tags( $listing_zip ) ),
							),
						);
						$zip_listings_lat_long_exact = get_posts( $zip_args );

						// merge the two queries to find our true matches
						$zip_listings = array_merge( $zip_listings_lat_long_range, $zip_listings_lat_long_exact );

					}// end if

				} else { // SAME ZIP ONLY
					$zip_args['meta_query'] = array(
						array(
							'key' => 'map-zip',
							'value' => sanitize_text_field( strip_tags( $listing_zip ) ),
						),
					);
					$zip_listings = get_posts( $zip_args );
				}

				// force a specific results pool
				if ( isset( $zip_listings ) && is_array( $zip_listings ) ) {
					if ( empty( $zip_listings ) ) {
						// force it to really be empty
						$zip_listings = array( 0 );
					}
					$args['post__in'] = array_map( 'absint', $zip_listings );
				}
			}

			if ( ! empty( $listing_country ) ) {
				$args['meta_query'] = array(
					array(
						'key'   => 'map-country',
						'value' => sanitize_text_field( $listing_country ),
					),
				);
			}

			if ( ! empty( $listing_category ) || ! empty( $listing_industry ) || ! empty( $listing_product_type ) || ! empty( $listing_supplier_type ) ) {

				$args['tax_query'] = array( 'relation' => 'AND' );

				// create our proper tax_query
				if ( ! empty( $listing_category ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => 'listing',
						'field'    => 'id',
						'terms'    => absint( $listing_category ),
					);
				}

				if ( ! empty( $listing_industry ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => 'tx_industrycategory',
						'field'    => 'slug',
						'terms'    => sanitize_text_field( $listing_industry ),
					);
				}

				if ( ! empty( $listing_product_type ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => 'tx_producttype',
						'field'    => 'slug',
						'terms'    => sanitize_text_field( $listing_product_type ),
					);
				}

				if ( ! empty( $listing_supplier_type ) ) {
					$args['tax_query'][] = array(
						'taxonomy' => 'tx_suppliertype',
						'field'    => 'slug',
						'terms'    => sanitize_text_field( $listing_supplier_type ),
					);
				}
			}

			// override the IDs with our properly limited IDs
			$ids = get_posts( $args );

			// if zero IDs were found, then force it all the way through
			if ( empty( $ids ) ) {
				$ids = array( 0 );
			}
		}

		return $ids;
	}

}

new SearchWP_DirectoryPress();
