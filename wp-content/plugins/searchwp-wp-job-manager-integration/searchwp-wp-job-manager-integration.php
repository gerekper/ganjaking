<?php
/*
Plugin Name: SearchWP WP Job Manager Integration
Plugin URI: https://searchwp.com/
Description: Have SearchWP take over WP Job Manager (and associated WP Job Manager add-ons) searches
Version: 1.5.15
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2014-2019 Jonathan Christopher

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SEARCHWP_WPJOBMANAGER_VERSION' ) ) {
	define( 'SEARCHWP_WPJOBMANAGER_VERSION', '1.5.15' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_WPJobManager_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

/**
 * Set up the updater
 *
 * @return bool|SWP_WPJobManager_Updater
 */
function searchwp_wpjobmanager_update_check() {

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

	if ( ! defined( 'SEARCHWP_WPJOBMANAGER_VERSION' ) ) {
		return false;
	}

	// Retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// Instantiate the updater to prep the environment
	$searchwp_wpjobmanager_updater = new SWP_WPJobManager_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33362,
			'version'   => SEARCHWP_WPJOBMANAGER_VERSION,
			'license'   => $license_key,
			'item_name' => 'WP Job Manager Integration',
			'author'    => 'Jonathan Christopher',
			'url'       => site_url(),
		)
	);

	return $searchwp_wpjobmanager_updater;
}

add_action( 'admin_init', 'searchwp_wpjobmanager_update_check' );

if ( ! class_exists( 'SearchWP_WP_Job_Manager_Integration' ) ) {

	/**
	 * Class SearchWP_WP_Job_Manager_Integration
	 */
	class SearchWP_WP_Job_Manager_Integration {

		private $query                  = '';
		private $search_engine_name     = 'default';
		private $override_post_types    = false;
		private $posts_per_page         = 10;
		private $offset                 = false;
		private $page                   = false;

		/**
		 * SearchWP_WP_Job_Manager_Integration constructor.
		 */
		function __construct() {
			add_filter( 'init', array( $this, 'init' ) );
		}

		/**
		 * Initializer
		 */
		function init() {

			// allow developer the opportunity to enforce SearchWP engine-configured post types
			$this->override_post_types = apply_filters( 'searchwp_wpjmi_job_post_type_override', false );

			// the gist here is that we're going to hijack WP Job Manager's search arguments,
			// remove the search_keywords, and provide our own post__in powered by SearchWP

			if ( apply_filters( 'searchwp_wpjmi_hijack_job_listing_search', true ) ) {

				// Disable WPJMs internal meta/taxonomy JOINs
				add_filter( 'job_listing_search_post_meta', '__return_false' );
				add_filter( 'job_listing_search_conditions', '__return_empty_array' );

				// Grab our args
				add_filter( 'job_manager_get_listings_args', array( $this, 'hijack_search_settings' ), 999 );
			}

			if ( apply_filters( 'searchwp_wpjmi_hijack_resume_search', true ) ) {
				add_filter( 'get_resumes_query_args', array( $this, 'do_searchwp_search' ), 999 );

				// The resume search doesn't let us hijack the search terms so we'll do it manually
				$this->hijack_search_settings( $_REQUEST );
			}

			if ( apply_filters( 'searchwp_wpjmi_hijack_alerts_search', true ) ) {
				add_filter( 'job_manager_alerts_get_job_listings_args', array( $this, 'hijack_search_settings' ), 999 );
			}

			// To prevent running multiple searches this is a catch-all for WP Job Manager and add-ons
			if (
				apply_filters( 'searchwp_wpjmi_hijack_job_listing_search', true )
				&& apply_filters( 'searchwp_wpjmi_hijack_alerts_search', true )
			) {
				add_filter( 'get_job_listings_query_args', array( $this, 'do_searchwp_search' ), 999 );
			}

			add_action( 'save_post', array( $this, 'index_wpjm_post' ) );
		}

		public function index_wpjm_post() {
			if (
				( isset( $_REQUEST['resume_manager_form'] ) && 'submit-resume' === $_REQUEST['resume_manager_form'] )
				||
				( isset( $_REQUEST['job_manager_form'] ) && 'submit-job' === $_REQUEST['job_manager_form'] )
				&& isset( $_REQUEST['job_id'] ) && ! empty( $_REQUEST['job_id'] )
				) {
				SWP()->trigger_index();
			}
		}

		/**
		 * Fired when the search takes place, we need to extract the search term(s) here
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		function hijack_search_settings( $args ) {

			// This is because of a poor design decision in init().
			if ( ! apply_filters( 'searchwp_wpjmi_hijack_job_listing_search', true ) ) {
				return $args;
			}

			if ( doing_filter( 'job_manager_get_listings_args' ) ) {
				$this->search_engine_name = apply_filters( 'searchwp_wpjmi_job_engine', $this->search_engine_name );
			}

			if ( doing_filter( 'job_manager_alerts_get_job_listings_args' ) ) {
				$this->search_engine_name = apply_filters( 'searchwp_wpjmi_alerts_engine', $this->search_engine_name );
			}

			if ( ! isset( $args['search_keywords'] ) ) {
				return $args;
			}

			$this->query = sanitize_text_field( $args['search_keywords'] );

			if ( isset( $args['posts_per_page'] ) ) {
				$this->posts_per_page = intval( $args['posts_per_page'] );
			} elseif ( isset( $args['per_page'] ) ) {
				// resume calls it per_page
				$this->posts_per_page = intval( $args['per_page'] );
			}

			if ( isset( $args['offset'] ) ) {
				$this->offset = absint( $args['offset'] );
			} elseif ( isset( $args['page'] ) ) {
				// resume uses page instead of offset
				$this->page = absint( $args['page'] );
			}

			return $args;
		}

		/**
		 * Hijack the search by forcing a post__in and matching orderby
		 *
		 * @param $query_args
		 *
		 * @return mixed
		 */
		function do_searchwp_search( $query_args ) {

			// limit the pool WP Job Manager works from by providing our own post__in
			if ( ! empty( $this->query ) && class_exists( 'SearchWP' ) ) {

				// Jobs and Alerts engine filters are run in hijack_search_settings()
				if ( doing_filter( 'get_resumes_query_args' ) ) {
					$this->search_engine_name = apply_filters( 'searchwp_wpjmi_resume_engine', $this->search_engine_name );
				}

				// we'll do our own keyword search (added in 1.21.4)
				remove_filter( 'posts_clauses', 'get_job_listings_keyword_search' );
				remove_filter( 'posts_clauses', 'get_resumes_keyword_search' );

				// instantiate SearchWP
				$engine = SearchWP::instance();

				// prevent pagination
				$query_args['posts_per_page'] = $this->posts_per_page;
				add_filter( 'searchwp_posts_per_page', array( $this, 'posts_per_page' ) );

				// we only want post IDs
				add_filter( 'searchwp_load_posts', '__return_false' );

				// Perform the search
				if ( $this->page ) {
					// this is a resume search
					$results = $engine->search( $this->search_engine_name, $this->query, $this->page );
				} else {
					$results = $engine->search( $this->search_engine_name, $this->query );
				}

				if ( ! empty( $results ) ) {
					// Bubble Featured to the top
					$featured_jobs = get_posts( array(
						'nopaging'   => true,
						'post_type'  => 'job_listing',
						'fields'     => 'ids',
						'post__in'   => $results,
						'orderby'    => 'post__in',
						'meta_query' => array(
							array(
								'key'   => '_featured',
								'value' => 1
							)
						),
					) );

					if ( ! empty( $featured_jobs ) && apply_filters( 'searchwp_wpjmi_bubble_featured', true ) ) {
						$unfeatured_jobs = array_diff( $results, $featured_jobs );
						$results = array_merge( $featured_jobs, $unfeatured_jobs );
					}
				}

				// there is a chance this hook is being used elsewhere so before we blatantly
				// override post__in let's make sure we intersect it

				// make sure if it's defined it's an array
				if ( array_key_exists( 'post__in', $query_args ) && ! empty( $query_args['post__in'] ) ) {

					// make sure it's an array
					$source = $query_args['post__in'];

					if ( is_string( $source ) ) {
						$source = explode( ',', $source );
					}

					$source = array_map( 'trim', $source );
					$source = array_map( 'absint', $source );
					$source = array_unique( $source );

					$query_args['post__in'] = $source;
				}

				// check to see if it's already being limited
				if ( isset( $query_args['post__in'] ) && is_array( $query_args['post__in'] ) && count( $query_args['post__in'] ) ) {
					$query_args['post__in'] = array_intersect( $query_args['post__in'], $results );
					// we may have (correctly) just zeroed out the results set
					// the radius limiter sets post__in to an array of results within that radius
					// but the keyword match may net zero results here, which is accurate
					if ( empty( $query_args['post__in'] ) ) {
						$query_args['post__in'] = array( 0 );
					}
				} else {
					// post__in wasn't set so let's just set it
					$query_args['post__in'] = $results;

					// if it was empty, we want to be sure it's empty
					if ( empty( $query_args['post__in'] ) ) {
						// no results, so force that
						$query_args['post__in'] = array( 0 );
					}
				}

				// sort the results by SearchWP relevance
				if ( ! empty( $query_args['post__in'] ) ) {
					$query_args['orderby'] = 'post__in';
				}

				if ( 0 === count( $results ) ) {
					// a search was submitted, but no results were found
					// so we need to force that by limiting post__in to
					// an impossible results set
					$query_args['post__in'] = array( 0 );
				}

				// if the developer really wants to, they can override the WP Job Manager restricted post_type
				if ( $this->override_post_types ) {
					// SearchWP's engine configuration will restrict this
					$query_args['post_type'] = 'any';
					$query_args['post_status'] = 'any';
				}

				// Brute force no search
				unset( $query_args['s'] );

				$query_args = apply_filters( 'searchwp_wpjmi_query_args', $query_args );
			} // End if().

			return $query_args;
		}

		/**
		 * Disable pagination
		 *
		 * @return int
		 */
		function posts_per_page() {
			return -1;
		}

	}
}

new SearchWP_WP_Job_Manager_Integration();
