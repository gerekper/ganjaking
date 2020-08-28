<?php
/*
Plugin Name: SearchWP WPML Integration
Plugin URI: https://searchwp.com/
Description: Integrate SearchWP with WPML
Version: 1.6.6
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

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SEARCHWP_WPML_VERSION' ) ) {
	define( 'SEARCHWP_WPML_VERSION', '1.6.6' );
}

/**
 * Instantiate the updater
 */
if ( ! class_exists( 'SWP_WPML_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}


/**
 * Set up the SearchWP WPML Updater
 *
 * @return bool|SWP_WPML_Updater
 */
function searchwp_wpml_update_check() {

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

	// Instantiate the updater to prep the environment
	$searchwp_wpml_updater = new SWP_WPML_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33645,
			'version'   => SEARCHWP_WPML_VERSION,
			'license'   => $license_key,
			'item_name' => 'WPML Integration',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_wpml_updater;
}

add_action( 'admin_init', 'searchwp_wpml_update_check' );

/**
 * Class SearchWP_WPML
 */
class SearchWP_WPML {

	/**
	 * SearchWP_WPML constructor.
	 */
	function __construct() {
		add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), array( $this, 'plugin_row' ), 11 );

		add_filter( 'searchwp_query_join',       array( $this, 'join_wpml' ), 10, 2 );
		add_filter( 'searchwp_query_conditions', array( $this, 'force_current_language' ), 10, 3 );
		add_filter( 'searchwp\query\mods',       array( $this, 'add_mods' ), 10, 2 );

		add_filter( 'searchwp_indexer_taxonomy_terms',                array( $this, 'handle_multilingual_taxonomy' ), 10, 3 );
		add_filter( 'searchwp\source\post\attributes\taxonomy\terms', array( $this, 'handle_multilingual_taxonomy_compat' ), 10, 2 );

		add_filter( 'searchwp_pre_set_post',  array( $this, 'set_post_locale' ) );
		add_filter( 'searchwp\entry\update_data\before', array( $this, 'set_post_locale' ) );

		// Prevent interference with the indexer.
		add_action( 'searchwp_indexer_running', array( $this, 'remove_all_unwanted_filters' ) );
		add_action( 'searchwp\indexer\batch',   array( $this, 'remove_all_unwanted_filters' ) );

		// Compat.
		add_filter( 'searchwp_index_chunk_size',   array( $this, 'chunk_size' ), 9999 );
		add_filter( 'searchwp\indexer\batch_size', array( $this, 'chunk_size' ), 9999 );
	}

	function chunk_size( $size ) {
		if ( is_plugin_active( 'acfml/wpml-acf.php' ) ) {
			$size = 1;
		}

		return $size;
	}

	/**
	 * Prevent WPML from filtering the indexer query for unindexed posts
	 */
	function remove_all_unwanted_filters() {
		$applicable = apply_filters( 'searchwp_wpml_aggressive', false );
		if ( $applicable ) {
			remove_all_filters( 'posts_join' );
			remove_all_filters( 'posts_where' );
			remove_all_filters( 'pre_get_posts' );
		}
	}

	/**
	 * Generates the SQL to JOIN to the WPML tables
	 *
	 * @param $sql
	 * @param $postType
	 *
	 * @return string
	 */
	function join_wpml( $sql, $postType ) {
		global $wpdb, $sitepress;

		if ( ! empty( $sitepress ) && method_exists( $sitepress, 'get_current_language' ) && method_exists( $sitepress, 'get_default_language' ) && post_type_exists( $postType ) ) {
			$prefix = $wpdb->prefix;

			$sql .= " LEFT JOIN {$prefix}icl_translations t ON {$prefix}posts.ID = t.element_id ";
			$sql .= " AND t.element_type LIKE %s LEFT JOIN {$prefix}icl_languages l ON t.language_code=l.code AND l.active=1 ";

			$sql = $wpdb->prepare( $sql, 'post_' . $postType );
		}

		return $sql;
	}

	function add_mods( $mods, $query ) {
		global $wpdb;

		// Add a Mod to JOIN to the wp_posts table.
		$mod = new \SearchWP\Mod();
		$mod->raw_join_sql( function( $runtime ) use ( $wpdb ) {
			return "LEFT JOIN {$wpdb->posts} swpwpml ON swpwpml.ID = {$runtime->get_foreign_alias()}.id";
		} );

		$mod->raw_join_sql("
			LEFT JOIN {$wpdb->prefix}icl_translations swpwpmlicl
			ON (
				swpwpmlicl.element_id = swpwpml.ID
				AND swpwpmlicl.element_type = CONCAT('post_', swpwpml.post_type)
			)
		");

		$mods[] = $mod;

		// We need to loop through all post types and add a Mod for each.
		foreach ( $query->get_engine()->get_sources() as $source ) {
			$flag = 'post' . SEARCHWP_SEPARATOR;

			if ( 0 !== strpos( $source->get_name(), $flag ) ) {
				continue;
			}

			$post_type = substr( $source->get_name(), strlen( $flag ) );

			$mod = new \SearchWP\Mod( $flag );
			$mod->raw_where_sql( function( $runtime_mod, $params ) use ( $post_type, $wpdb ) {
				$where_sql = $this->force_current_language( '1=1', $post_type, null );
				$where_sql = str_replace( 't.', 'swpwpmlicl.', $where_sql );
				$where_sql = str_replace( $wpdb->posts . '.', "swpwpml.", $where_sql );

				return "({$runtime_mod->get_foreign_alias()}.source != 'post"
				. SEARCHWP_SEPARATOR . $post_type . "' OR ({$runtime_mod->get_foreign_alias()}.source = 'post"
						. SEARCHWP_SEPARATOR . $post_type . "' AND {$where_sql}))";
			} );

			$mods[] = $mod;
		}

		return $mods;
	}

	/**
	 * Limit results to the current language as defined by WPML
	 *
	 * @param $sql
	 *
	 * @return string
	 */
	function force_current_language( $sql, $post_type, $engine ) {
		global $wpdb, $sitepress;

		if ( ! empty( $sitepress ) && method_exists( $sitepress, 'get_current_language' ) && method_exists( $sitepress, 'get_default_language' ) ) {
			$current_language = $sitepress->get_current_language();
			$default_language = $sitepress->get_default_language();

			if ( $current_language == $default_language ) {
				$sql .= " AND ( ( {$wpdb->posts}.post_type != 'attachment' AND ( t.language_code = %s OR t.language_code IS NULL ) ) OR ( {$wpdb->posts}.post_type = 'attachment' AND t.language_code = %s ) ) ";
				$sql = $wpdb->prepare( $sql, $current_language, $current_language );

				return $sql;
			} else {
				// WPML supports per-post-type fallbacks (last seen in WPML > Settings > Post Types Translations)
				// "Translatable - use translation if available or fallback to default language"
				// We can support this.
				if ( method_exists( $sitepress, 'get_setting' ) ) {
					$wpml_custom_posts_sync_option = $sitepress->get_setting( 'custom_posts_sync_option', array() );
					if (
						is_array( $wpml_custom_posts_sync_option )
						&& array_key_exists( $post_type, $wpml_custom_posts_sync_option )
						&& 2 == $wpml_custom_posts_sync_option[ $post_type ] // TODO: Can this be better? Need to find out if there's a map we can use to better check this value.
					) {
						// This post type should revert to the default language as per this WPML setting.
						$sql .= " AND ( ( {$wpdb->posts}.post_type = %s AND ( t.language_code = %s OR t.language_code = %s OR t.language_code IS NULL ) ) ) ";
						$sql = $wpdb->prepare( $sql, $post_type, $default_language, $current_language );

						return $sql;
					}
				}

				$sql .= " AND ( t.language_code = %s ) ";
				$sql = $wpdb->prepare( $sql, $current_language );

				return $sql;
			}
		}

		return $sql;
	}

	function set_post_locale( $the_post ) {
		global $sitepress;

		// Make sure we can get the default language
		if ( empty( $sitepress ) || ! method_exists( $sitepress, 'switch_lang' ) ) {
			return $the_post;
		}

		if ( $the_post instanceof WP_Post ) {
			$post_id = $the_post->ID;
		} else {
			// SearchWP 4.0 compat.
			$post_id = $the_post->get_id();
		}

		$post_lang = apply_filters( 'wpml_post_language_details', NULL, $post_id ) ;

		$sitepress->switch_lang( $post_lang['language_code'] );

		return $the_post;
	}

	/**
	 * Ensure the translated taxonomy terms are indexed for the current post
	 *
	 * @param $terms
	 * @param $taxonomy
	 * @param $post_being_indexed
	 *
	 * @since 1.4.0
	 *
	 * @return mixed
	 */
	function handle_multilingual_taxonomy( $terms, $taxonomy, $post_being_indexed ) {
		global $sitepress;

		// Make sure we can get the default language
		if ( empty( $sitepress ) || ! method_exists( $sitepress, 'switch_lang' ) ) {
			return $terms;
		}

		$post_lang = apply_filters( 'wpml_post_language_details', NULL, $post_being_indexed->ID ) ;

		// Retrieve the translated taxonomy terms
		$sitepress->switch_lang( $post_lang['language_code'] );
		$terms = wp_get_object_terms( array( $post_being_indexed->ID ), $taxonomy );

		return $terms;
	}

	function handle_multilingual_taxonomy_compat( $terms, $args ) {
		global $sitepress;

		// Make sure we can get the default language
		if ( empty( $sitepress ) || ! method_exists( $sitepress, 'switch_lang' ) ) {
			return $terms;
		}

		$post_lang = apply_filters( 'wpml_post_language_details', NULL, $args['post_id'] ) ;

		// Retrieve the translated taxonomy terms.
		$sitepress->switch_lang( $post_lang['language_code'] );
		$terms = wp_get_object_terms( array( $args['post_id'] ), $args['taxonomy'] );

		return $terms;
	}

	/**
	 * Output notice if there are version incompatibilities
	 */
	function plugin_row() {
		if ( ! class_exists( 'SearchWP' ) ) { ?>
			<tr class="plugin-update-tr searchwp">
				<td colspan="3" class="plugin-update">
					<div class="update-message">
						<?php esc_html_e( 'SearchWP must be active to use this Extension', 'searchwpwpml' ); ?>
					</div>
				</td>
			</tr>
		<?php }
		else {
			if ( function_exists( 'SWP' ) ) {
				$searchwp = SearchWP::instance();
				if ( version_compare( $searchwp->version, '3.0.0', '<' ) ) { ?>
					<tr class="plugin-update-tr searchwp">
						<td colspan="3" class="plugin-update">
							<div class="update-message">
								<?php esc_html_e( 'SearchWP WPML Integration requires SearchWP 3.0.0 or greater', 'searchwpwpml' ); ?>
							</div>
						</td>
					</tr>
				<?php }
			}
		}
	}

}

new SearchWP_WPML();
