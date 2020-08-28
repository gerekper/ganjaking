<?php
/*
Plugin Name: SearchWP Term Archive Priority
Plugin URI: https://searchwp.com/
Description: Bubbles term archive pages to the top of search results for supplemental search engines
Version: 1.2.0
Requires PHP: 5.6
Author: SearchWP
Author URI: https://searchwp.com/

Copyright 2013-2020 SearchWP

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

global $searchwp_term_priority;

if ( ! defined( 'SEARCHWP_TERM_PRIORITY_VERSION' ) ) {
	define( 'SEARCHWP_TERM_PRIORITY_VERSION', '1.2.0' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_Term_Priority_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}


/**
 * @return bool|SWP_Term_Priority_Updater
 */
function searchwp_term_priority_update_check() {

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
	$searchwp_term_priority_updater = new SWP_Term_Priority_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33679,
			'version'   => SEARCHWP_TERM_PRIORITY_VERSION,
			'license'   => $license_key,
			'item_name' => 'Term Archive Priority',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_term_priority_updater;
}

add_action( 'admin_init', 'searchwp_term_priority_update_check' );

include_once 'class.SearchWPTermResult.php';

/**
 * Class SearchWPTermArchivePriority
 */
class SearchWPTermArchivePriority {

	public $applicable = false;
	public $applicableTerms = array();
	public $offset = 0;
	public $found_terms = 0;

	private $taxonomies = array();

	/**
	 * SearchWPTermArchivePriority constructor.
	 */
	function __construct() {
		add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), array( $this, 'plugin_row' ), 11 );
		add_action( 'init', array( $this, 'get_taxonomies' ) );

		add_action( 'searchwp_before_query_index', array( $this, 'before_query' ), 10, 1 );

		add_filter( 'searchwp_results', array( $this, 'maybe_inject_archives' ), 10, 2 );
		// add_filter( 'searchwp_query_limit_start', array( $this, 'apply_offset_start' ), 10, 2 );
		// add_filter( 'searchwp_query_limit_total', array( $this, 'apply_offset_total' ), 10, 2 );

		// Compatibility with Live Ajax Search.
		add_action( 'searchwp_live_search_alter_results', array( $this, 'alter_live_search_results' ) );

		add_action( 'searchwp\query\before', array( $this, 'setup' ) );
		add_filter( 'searchwp\query\results', array( $this, 'inject_term_archives' ), 10, 2 );
	}

	function setup( $query ) {
		$params = $query->get_args();
		$params['engine'] = is_string( $params['engine'] ) ? $params['engine'] : $params['engine']->get_name();

		// Determine whether we're applicable.
		if (
			isset( $params['engine'] )
			&& 'default' === $params['engine']
			&& ! $this->maybe_live_search_support( $params['engine'] )
		) {
			return;
		}

		$terms      = $query->get_tokens();
		$applicable = apply_filters( 'searchwp_term_archive_enabled', true, $params['engine'], $terms );

		if ( ! $applicable || ! is_array( $terms ) || empty( $terms ) ) {
			return;
		}

		$this->applicable = true;
	}

	function inject_term_archives( $results, $query ) {
		if ( ! $this->applicable ) {
			return $results;
		}

		$params = $query->get_args();
		$params['engine'] = is_string( $params['engine'] ) ? $params['engine'] : $params['engine']->get_name();
		$params['terms'] = $query->get_tokens();

		$term_args  = apply_filters( 'searchwp_term_archive_term_args', array(), array(
			'engine' => $params['engine'],
		) );

		// Determine which Taxonomies are in play (e.g. added to the Engine).
		$applicable_taxonomies = array();
		foreach ( $query->get_engine()->get_sources() as $source ) {
			$flag = 'post' . SEARCHWP_SEPARATOR;

			if ( 0 !== strpos( $source->get_name(), $flag ) ) {
				continue;
			}

			// We have an applicable Source, see if it has any Taxonomies.
			$attributes = $source->get_attributes();
			$taxonomies = array();

			if ( array_key_exists( 'taxonomy', $attributes ) ) {
				$taxonomies_settings = $attributes['taxonomy'];

				foreach ( $taxonomies_settings->get_settings() as $taxonomy => $weight ) {
					// Make sure this taxonomy was not skipped.
					if (
						array_key_exists( 'taxonomy', $term_args )
						&& ! empty( $term_args['taxonomy'] )
					) {
						$term_args['taxonomy'] = (array) $term_args['taxonomy'];
						if ( in_array( $taxonomy, $term_args['taxonomy'] ) ) {
							continue;
						}
					}

					if ( ! array_key_exists( $weight, $taxonomies ) ) {
						$taxonomies[ $weight ] = array();
					}

					if ( ! array_key_exists( $taxonomy, $taxonomies[ $weight ] ) ) {
						$taxonomies[ $weight ][ $taxonomy ] = $this->get_terms_for_taxonomy( $taxonomy, $query );
					}
				}
			}

			if ( ! empty( $taxonomies ) ) {
				$applicable_taxonomies = $applicable_taxonomies + $taxonomies;
			}
		}

		if ( empty( $applicable_taxonomies ) ) {
			return;
		}
		$this->applicableTerms = $applicable_taxonomies;

		$this->resolve_duplicates();

		return $this->maybe_inject_archives( $results, $params );
	}

	function get_terms_for_taxonomy( $taxonomy, $query ) {
		$tokens = $query->get_tokens();
		$params = $query->get_args();
		$params['engine'] = is_string( $params['engine'] ) ? $params['engine'] : $params['engine']->get_name();

		$core_partial_matching_enabled = \SearchWP\Settings::get_single( 'partial_matches', 'boolean' );

		$terms = array();

		if ( $core_partial_matching_enabled || apply_filters( 'searchwp_tax_term_like_logic', false ) ) {
			// Find partial term matches for all tokens.
			foreach ( $tokens as $token ) {
				$args = array(
					'name__like' => $token,
					'hide_empty' => false,
				);

				$args = apply_filters( 'searchwp_term_archive_term_args', $args, array(
					'engine' => $params['engine'],
				) );

				$tax_terms = get_terms( $taxonomy, $args );

				if ( empty( $tax_terms ) ) {
					continue;
				}

				foreach( $tax_terms as $tax_term ) {
					$terms[ $tax_term->term_id ] = $tax_term->slug;
				}
			}
		} else {
			foreach ( $tokens as $token ) {
				// Single term match?
				$term_id = term_exists( $token, $taxonomy );
				if ( ! empty( $term_id) ) {
					$tax_term = get_term( $term_id['term_id'], $taxonomy );
					$terms[ $tax_term->term_id ] = $tax_term->slug;
				}
				elseif ( $term_id = term_exists( implode( ' ', $tokens ), $taxonomy ) ) {
					$tax_term = get_term( $term_id['term_id'], $taxonomy );
					$terms[ $tax_term->term_id ] = $tax_term->slug;
				} elseif ( apply_filters( 'searchwp_tax_term_or_logic', false ) ) {
					$args = array(
						'search'     => $token,
						'hide_empty' => false,
					);

					$args = apply_filters( 'searchwp_term_archive_term_args', $args, array(
						'engine' => $params['engine'],
					) );
					$tax_terms = get_terms( $taxonomy, $args );

					foreach( $tax_terms as $tax_term ) {
						$terms[ $tax_term->term_id ] = $tax_term->slug;
					}
				}
			}
		}

		return $terms;
	}

	/**
	 * Callback to re-inject term archive links into The Loop once Live Ajax Search has found results.
	 * This is necessary because Live Ajax Search fires query_posts() with specific args, but those args
	 * do not facilitate retaining the work Term Archive Priority has done to inject term archives in place.
	 *
	 * @since 1.1.8
	 *
	 * @param array $args The arguments used for Live Ajax Search's results.
	 *
	 * @return void
	 */
	function alter_live_search_results( $args ) {
		global $wp_query;

		if ( empty( $args['post__in'] ) ) {
			return;
		}

		foreach ( $args['post__in'] as $maybe_term_archive ) {
			if ( ! $maybe_term_archive instanceof SearchWPTermResult ) {
				continue;
			}

			array_unshift( $wp_query->posts, $maybe_term_archive );
		}
	}

	/**
	 * Retrieve a list of registered taxonomies
	 */
	function get_taxonomies() {
		$this->taxonomies = get_taxonomies( '', 'names' );
	}


	/**
	 * Internal active plugin check since is_plugin_active() might not be available yet
	 *
	 * @param $plugin
	 *
	 * @return bool
	 */
	function is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
	}

	/**
	 * Callback for plugin row display
	 */
	function plugin_row() {

		if ( ! function_exists( 'SWP' ) ) {
			return;
		}

		$searchwp = SearchWP::instance();
		if ( version_compare( $searchwp->version, '1.0.8', '<' ) ) { ?>
			<tr class="plugin-update-tr searchwp">
				<td colspan="3" class="plugin-update">
					<div class="update-message">
						<?php esc_html_e( 'SearchWP Term Archive Priority requires SearchWP 1.0.8 or greater', 'searchwp' ); ?>
					</div>
				</td>
			</tr>
		<?php }
	}

	function maybe_live_search_support( $engine = 'default' ) {
		return defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['action'] ) && 'searchwp_live_search' === $_REQUEST['action'] && apply_filters( 'searchwp_term_archive_live_search', false, $engine );
	}

	/**
	 * Find taxonomy terms that match search query
	 *
	 * @param $params
	 */
	function before_query( $params ) {

		if ( isset( $params['engine'] ) && 'default' === $params['engine'] && ! $this->maybe_live_search_support( $params['engine'] ) ) {
			$this->applicable = false;
			return;
		}

		// we need to determine whether any terms match any tax terms
		$terms = isset( $params['terms'] ) ? $params['terms'] : array();

		// allow filtration based on engine and/or term(s)
		$applicable = apply_filters( 'searchwp_term_archive_enabled', true, $params['engine'], $terms );

		// allow 'skipping' of Taxonomies, but base it on these filtered term args
		$term_args = apply_filters( 'searchwp_term_archive_term_args', array(), array(
			'engine' => $params['engine'],
		) );

		if ( $applicable && is_array( $terms ) ) {
			foreach ( $terms as $term ) {

				// check each post type to make sure it's enabled
				if ( isset( $params['settings']['engines'][ $params['engine'] ] ) ) {
					foreach ( $params['settings']['engines'][ $params['engine'] ] as $post_type ) {

						// make sure the post type is enabled and there are taxonomies to consider
						if ( isset( $post_type['enabled'] ) && $post_type['enabled'] && isset( $post_type['weights']['tax'] ) && is_array( $post_type['weights']['tax'] ) && ! empty( $post_type['weights']['tax'] ) ) {
							foreach ( $post_type['weights']['tax'] as $taxonomy => $weight ) {

								// allow 'skipping' of Taxonomies, but base it on these filtered term args
								if ( is_array( $term_args ) && ! empty( $term_args['taxonomy'] ) ) {

									// for consistency make sure it's an array
									if ( ! is_array( $term_args['taxonomy'] ) ) {
										$term_args['taxonomy'] = array( $term_args['taxonomy'] );
									}

									if ( ! in_array( $taxonomy, $term_args['taxonomy'] ) ) {
										continue;
									}
								}

								if ( ! isset( $this->applicableTerms[ $weight ] ) || ! is_array( $this->applicableTerms[ $weight ] ) ) {
									$this->applicableTerms[ $weight ] = array();
								}

								if ( ! isset( $this->applicableTerms[ $weight ][ $taxonomy ] ) || ! is_array( $this->applicableTerms[ $weight ][ $taxonomy ] ) ) {
									$this->applicableTerms[ $weight ][ $taxonomy ] = array();
								}

								// if it has a positive weight, it counts
								if ( intval( $weight ) > 0 ) {

									$existing_swp_advanced_settings = searchwp_get_option( 'advanced' );

									$core_partial_matching_enabled = is_array( $existing_swp_advanced_settings ) && array_key_exists( 'partial_matches', $existing_swp_advanced_settings ) && ! empty( $existing_swp_advanced_settings['partial_matches'] ) ? true : false;

									// if LIKE Terms (or Fuzzy Matches) is active (or dev wants to force LIKE logic)
									if (
										$core_partial_matching_enabled // Added in SearchWP 3.0.
										|| $this->is_plugin_active( 'searchwp-like/searchwp-like.php' )
										|| $this->is_plugin_active( 'searchwp-fuzzy/searchwp-fuzzy.php' )
										|| apply_filters( 'searchwp_tax_term_like_logic', false )
									) {
										$args = array(
											'name__like'  => $term,
											'hide_empty'  => false,
										);

										$args = apply_filters( 'searchwp_term_archive_term_args', $args, array(
											'engine' => $params['engine'],
										) );

										// process our arguments
										$taxTerms = get_terms( $taxonomy, $args );

										foreach ( $taxTerms as $taxTerm ) {
											// make sure there's no dupe
											if (
												! is_array( $this->applicableTerms[ $weight ][ $taxonomy ] )
												||
												(
													is_array( $this->applicableTerms[ $weight ][ $taxonomy ] )
													&&
													! in_array( $taxTerm->slug, $this->applicableTerms[ $weight ][ $taxonomy ], true )
												)
											) {
												$this->set_found_term( $weight, $taxonomy, $taxTerm->slug );
											}
										}
									} else {
										// first check to see if there is a single term match
										$term_id = term_exists( $term, $taxonomy );
										if ( ! empty( $term_id) ) {
											$termObj = get_term( $term_id['term_id'], $taxonomy );
											// make sure there's no dupe
											if (
												! is_array( $this->applicableTerms[ $weight ][ $taxonomy ] )
												||
												(
													is_array( $this->applicableTerms[ $weight ][ $taxonomy ] )
													&&
													! in_array( $termObj->slug, $this->applicableTerms[ $weight ][ $taxonomy ], true )
												)
											) {
												$this->set_found_term( $weight, $taxonomy, $termObj->slug );
											}
										}
										elseif ( $term_id = term_exists( implode( ' ', $terms ), $taxonomy ) ) {
											$termObj = get_term( $term_id['term_id'], $taxonomy );
											// make sure there's no dupe
											if (
												! is_array( $this->applicableTerms[ $weight ][ $taxonomy ] )
												||
												(
													is_array( $this->applicableTerms[ $weight ][ $taxonomy ] )
													&&
													! in_array( $termObj->slug, $this->applicableTerms[ $weight ][ $taxonomy ], true )
												)
											) {
												$this->set_found_term( $weight, $taxonomy, $termObj->slug );
											}
										} elseif ( apply_filters( 'searchwp_tax_term_or_logic', false ) ) {
											$args = array(
												'search'      => $term,
												'hide_empty'  => false,
											);

											$args = apply_filters( 'searchwp_term_archive_term_args', $args, array(
												'engine' => $params['engine'],
											) );
											$taxTerms = get_terms( $taxonomy, $args );

											foreach ( $taxTerms as $taxTerm ) {
												// make sure there's no dupe
												if (
													! is_array( $this->applicableTerms[ $weight ][ $taxonomy ] )
													||
													(
														is_array( $this->applicableTerms[ $weight ][ $taxonomy ] )
														&&
														! in_array( $taxTerm->slug, $this->applicableTerms[ $weight ][ $taxonomy ], true )
													)
												) {
													$this->set_found_term( $weight, $taxonomy, $taxTerm->slug );
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}

			$this->resolve_duplicates();
		}
	}

	/**
	 * @param $xweight
	 * @param $xtaxonomy
	 * @param $xslug
	 */
	function set_found_term( $xweight, $xtaxonomy, $xslug ) {
		$this->applicable = true;
		$this->applicableTerms[ $xweight ][ $xtaxonomy ][] = $xslug;
		$this->offset++;
	}

	function resolve_duplicates() {
		if ( empty( $this->applicableTerms ) ) {
			return;
		}

		// if a taxonomy is used across post types, there will be duplicates, so they need to be merged
		$used_taxonomies = array();
		foreach ( $this->applicableTerms as $weight => $taxonomies ) {
			if ( empty( $weight ) ) {
				continue;
			}

			foreach ( $taxonomies as $taxonomy => $terms ) {
				if ( ! array_key_exists( $taxonomy, $used_taxonomies ) ) {
					$used_taxonomies[ $taxonomy ] = $weight;
				} else {
					// this is a taxonomy that was used across post types, so the weights need to be merged and a new key set up
					$new_weight = $used_taxonomies[ $taxonomy ] + $weight;

					if ( ! isset( $this->applicableTerms[ $new_weight ] ) || ! is_array( $this->applicableTerms[ $new_weight ] ) ) {
						$this->applicableTerms[ $new_weight ] = array();
					}

					if ( ! isset( $this->applicableTerms[ $new_weight ][ $taxonomy ] ) || ! is_array( $this->applicableTerms[ $new_weight ][ $taxonomy ] ) ) {
						$this->applicableTerms[ $new_weight ][ $taxonomy ] = array();
					}

					// create the new, updated key
					$this->applicableTerms[ $new_weight ][ $taxonomy ] = array_merge( $this->applicableTerms[ $weight ][ $taxonomy ], $terms );
					$this->applicableTerms[ $new_weight ][ $taxonomy ] = array_unique( $this->applicableTerms[ $new_weight ][ $taxonomy ] );

					// remove the old key and the most recent since it's no longer applicable (we have the new weight keyed)
					unset( $this->applicableTerms[ $weight ][ $taxonomy ] );
					unset( $this->applicableTerms[ $used_taxonomies[ $taxonomy ] ][ $taxonomy ] );

					// if this leaves that weight key empty, remove it entirely
					if ( empty( $this->applicableTerms[ $weight ] ) ) {
						unset( $this->applicableTerms[ $weight ] );
					}
					if ( empty( $this->applicableTerms[ $used_taxonomies[ $taxonomy ] ] ) ) {
						unset( $this->applicableTerms[ $used_taxonomies[ $taxonomy ] ] );
					}

					$used_taxonomies[ $taxonomy ] = $new_weight;
				}
			}
		}

		foreach ( $this->applicableTerms as $weight => $taxonomies ) {
			foreach ( $taxonomies as $taxonomy => $terms ) {
				if ( 0 == count( $terms ) ) {
					unset( $this->applicableTerms[ $weight ][ $taxonomy ] );
				} else {
					$this->found_terms += count( $this->applicableTerms[ $weight ][ $taxonomy ] );
				}
			}

			if ( empty( $this->applicableTerms[ $weight ] ) ) {
				unset( $this->applicableTerms[ $weight ] );
			}
		}
	}

	/**
	 * Insert found term objects into results
	 *
	 * @param $results
	 * @param $params
	 *
	 * @return mixed
	 */
	function maybe_inject_archives( $results, $params ) {

		// allow filtration based on engine and/or term(s)
		$terms = isset( $params['terms'] ) ? $params['terms'] : array();
		$applicable = apply_filters( 'searchwp_term_archive_enabled', true, $params['engine'], $terms );

		// we don't want to inject anything if we're paging and the found archives
		// no longer apply, so we need to determine the proper offset and if necessary
		// we need to splice the array properly as well
		if ( absint( $params['page'] ) > 1 ) {
			return $results;
		}

		ksort( $this->applicableTerms );

		// This terrible conditional checks to see whether we're applicable
		// It checks to make sure that we're *not* using the default engine
		// (because that's a headache with hijacking native WP search)
		// OR we're using Live Ajax Search with the default engine
		// (which is okay because we're using our own output template)
		if ( ( $applicable && isset( $params['engine'] ) && 'default' !== $params['engine'] && $this->applicable ) || ( $this->maybe_live_search_support( $params['engine'] ) ) ) {
			foreach ( $this->applicableTerms as $weight => $taxonomies ) {
				foreach ( $taxonomies as $taxonomy => $terms ) {
					if ( is_array( $terms ) && ! empty( $terms ) ) {
						foreach ( $terms as $term ) {
							array_unshift( $results, new SearchWPTermResult( $term, $taxonomy ) );
						}
					}
				}
			}
		}

		return $results;
	}

	/**
	 * Determine offset start for found terms
	 *
	 * @param $existing
	 * @param $page
	 *
	 * @return int
	 */
	function apply_offset_start( $existing, $page ) {

		$page = absint( $page );

		if ( $this->applicable && 1 === $page ) {
			// first page, we don't want to mess with the start
			$offset = 0;
		} elseif ( $this->applicable && $page > 1 ) {
			// not the first page, which means the SearchWP start needs to be
			// offset by our results which were output on the previous page(s)
			$offset = $existing - $this->offset;
		} else {
			// not applicable, so don't manipulate
			$offset = $existing;
		}

		return $offset;
	}

	/**
	 * Determine offset total for found terms
	 *
	 * @param $existing
	 * @param $page
	 *
	 * @return mixed
	 */
	function apply_offset_total( $existing, $page ) {

		$page = absint( $page );

		if ( $this->applicable && 1 === $page ) {
			// first page, so the total must be offset by how many terms we found
			$offset = $existing - $this->offset;
		} elseif ( $this->applicable && $page > 1 ) {
			// not the first page, so we don't need to limit the total
			// TODO: take into consideration how many posts per page
			$offset = $existing;
		} else {
			// not applicable, so don't manipulate
			$offset = $existing;
		}
		return $offset;
	}

}

new SearchWPTermArchivePriority();
