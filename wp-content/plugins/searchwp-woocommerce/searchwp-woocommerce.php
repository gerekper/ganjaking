<?php
/*
Plugin Name: SearchWP WooCommerce Integration
Plugin URI: https://searchwp.com/extensions/woocommerce-integration/
Description: Integrate SearchWP with WooCommerce searches and Layered Navigation
Version: 1.3.9
Requires PHP: 5.6
Author: SearchWP
Author URI: https://searchwp.com/

Copyright 2014-2020 SearchWP

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

if ( ! defined( 'SEARCHWP_WOOCOMMERCE_VERSION' ) ) {
	define( 'SEARCHWP_WOOCOMMERCE_VERSION', '1.3.9' );
}

/**
 * Implement updater
 *
 * @return bool|SWP_WooCommerce_Updater
 */
function searchwp_woocommerce_update_check() {

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

	if ( ! class_exists( 'SWP_WooCommerce_Updater' ) ) {
		// load our custom updater
		include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
	}

	// SearchWP 4 compat.
	if ( class_exists( '\\SearchWP\\License' ) ) {
		$license_key = \SearchWP\License::get_key();
	} else {
		$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
		$license_key = sanitize_text_field( $license_key );
	}

	// instantiate the updater to prep the environment
	$searchwp_woocommerce_updater = new SWP_WooCommerce_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33339,
			'version'   => SEARCHWP_WOOCOMMERCE_VERSION,
			'license'   => $license_key,
			'item_name' => 'WooCommerce Integration',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_woocommerce_updater;
}

add_action( 'admin_init', 'searchwp_woocommerce_update_check' );

/**
 * Class SearchWP_WooCommerce_Integration
 */
class SearchWP_WooCommerce_Integration {

	private $post_in = array();
	private $woocommerce_query = false;
	private $woocommerce;
	private $results = array();
	private $native_get_vars = array( 's', 'post_type', 'orderby' );
	private $post_type = 'product';
	private $ordering = array();
	private $original_query = '';
	private $filtered_posts = array();
	private $price_min = 0;
	private $price_max = 0;

	/**
	 * SearchWP_WooCommerce_Integration constructor.
	 */
	function __construct() {
		// Always exclude hidden Products.
		add_filter( 'searchwp_exclude', array( $this, 'exclude_hidden_products' ) ); // SearchWP 3.x compat.

		// Maybe exclude out of stock products.
		add_filter( 'searchwp\source\attribute\label', array( $this, 'comments_label' ), 10, 2 );
		add_filter( 'searchwp_exclude', array( $this, 'maybe_exclude_out_of_stock_products' ) ); // SearchWP 3.x compat.
		add_filter( 'searchwp_supports_label_product_comments', array( $this, 'comments_label' ) ); // SearchWP 3.x compat.

		add_filter( 'woocommerce_json_search_found_products', array( $this, 'json_search_products' ) );

		$query = isset( $_GET['s'] ) ? esc_attr( $_GET['s'] ) : '';
		$this->original_query = $query;

		add_filter( 'searchwp\native\short_circuit', array( $this, 'maybe_cancel_native_searchwp' ), 999, 2 );
		add_filter( 'searchwp\native\force', array( $this, 'maybe_force_admin_search' ) );
		add_filter( 'searchwp\query\mods', array( $this, 'implement_mods' ) );

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Customize the label used for Comments in the SearchWP engine configuration
	 */
	function comments_label( $label, $args = null ) {
		$custom = __( 'Reviews', 'searchwp_woocommerce' );

		if ( is_null( $args ) ) {
			// SearchWP 3.x.
			return $custom;
		} else {
			if ( 'post.product' === $args['source'] && 'comments' === $args['attribute'] ) {
				return $custom;
			} else {
				return $label;
			}
		}
	}

	/**
	 * Initializer
	 */
	function init() {
		global $wp_query;

		$forced = apply_filters( 'searchwp_woocommerce_forced', false );
		if ( ( empty( $_GET['post_type'] ) || 'product' !== $_GET['post_type'] ) && empty( $forced ) ) {
			return;
		}

		// Short circuit if we're in the admin but admin searches are not enabled in SearchWP
		// e.g. Because WooCommerce does support SKU searches out of the box
		$in_admin = apply_filters( 'searchwp_in_admin', false ); // SearchWP 3.x compat.
		$in_admin = apply_filters( 'searchwp\native\admin\short_circuit', $in_admin, $wp_query ); // $query questionable.
		if ( empty( $in_admin ) && is_admin() ) {
			return;
		}

		$searchwp_core_short_circuit = apply_filters( 'searchwp_short_circuit', false ); // SearchWP 3.x compat.
		$searchwp_core_short_circuit = apply_filters( 'searchwp\native\short_circuit', $searchwp_core_short_circuit, $wp_query );
		$short_circuit = apply_filters( 'searchwp_woocommerce_short_circuit', $searchwp_core_short_circuit );

		if ( ( ! empty( $short_circuit ) || empty( $_GET['s'] ) ) && empty( $forced ) ) {
			return;
		}

		// WooCommerce hooks
		add_action( 'loop_shop_post_in', array( $this, 'post_in' ), 9999 );
		add_action( 'woocommerce_product_query', array( $this, 'product_query' ), 10, 2 );
		add_filter( 'the_posts', array( $this, 'the_posts' ), 15, 2 ); // Woo uses priority 11
		add_filter( 'woocommerce_get_filtered_term_product_counts_query', array( $this, 'get_filtered_term_product_counts_query' ) );
		add_filter( 'woocommerce_product_query_meta_query', array( $this, 'woocommerce_product_query_meta_query' ) );
		add_filter( 'woocommerce_price_filter_widget_min_amount', array( $this, 'get_price_min_amount' ), 999 );
		add_filter( 'woocommerce_price_filter_widget_max_amount', array( $this, 'get_price_max_amount' ), 999 );

		// WordPress hooks
		$this->get_woocommerce_ordering();

		add_action( 'wp', array( $this, 'hijack_query_vars' ), 1 );
		add_action( 'wp', array( $this, 'replace_original_search_query' ), 3 );

		// Backwards compatibility for SearchWP 3.x.
		if ( $this->is_legacy_searchwp() ) {
			$this->init_legacy();

			return;
		}
	}

	/**
	 * Force an admin search when applicable.
	 *
	 * @param mixed $args
	 * @return bool
	 */
	function maybe_force_admin_search( $args ) {
		if ( ! is_admin() || ! $this->is_woocommerce_search() ) {
			return false;
		}

		// If this is an admin search and there is an admin engine with Products, force it to happen.
		// We have to do this because $query->is_search() is false at runtime.
		$admin_engine = \SearchWP\Settings::get_admin_engine();

		if ( empty( $admin_engine ) ) {
			return $args;
		}

		$engine_model = new \SearchWP\Engine( $admin_engine );
		$sources      = $engine_model->get_sources();

		if ( ! array_key_exists( 'post' . SEARCHWP_SEPARATOR . 'product', $sources ) ) {
			return $args;
		}

		add_filter( 'searchwp\native\args', array( $this, 'set_admin_search_args' ) );

		return true;
	}

	function set_admin_search_args( $args ) {
		remove_filter( 'searchwp\native\args', array( $this, 'set_admin_search_args' ) );

		if ( array_key_exists( 'product_search', $args ) && $args['product_search'] ) {
			$args['post__in'] = [];
		}

		return $args;
	}

	function maybe_cancel_native_searchwp( $cancel, $query ) {
		return is_admin() ? false : $this->is_woocommerce_search();
	}

	/**
	 * Returns whether this is a legacy version of SearchWP.
	 *
	 * @since 1.3
	 * @return bool
	 */
	function is_legacy_searchwp() {
		return ! ( defined( 'SEARCHWP_VERSION' ) && version_compare( SEARCHWP_VERSION, '3.99.0', '>=' ) );
	}

	function implement_mods( $mods ) {
		global $wpdb;

		$mod = new \SearchWP\Mod();
		$mod->raw_join_sql( function( $runtime ) use ( $wpdb ) {
			return "LEFT JOIN {$wpdb->posts} AS swpwcposts ON swpwcposts.ID = {$runtime->get_foreign_alias()}.id";
		} );
		$main_join_sql = $this->query_main_join( '', null );
		$main_join_sql = str_replace( "{$wpdb->posts}.", 'swpwcposts.', $main_join_sql );
		$mod->raw_join_sql( $main_join_sql );

		$column_as = $this->searchwp_query_inject();
		if ( ! empty( $column_as ) ) {
			$mod->column_as(
				str_ireplace( 'AS average_rating', '', $column_as ),
				'average_rating'
			);
		}

		$legacy_orderbys = $this->query_orderby( null );
		$legacy_orderbys = array_map( function( $orderby ) use ( $wpdb ) {
			return str_replace( "{$wpdb->posts}.", 'swpwcposts.', $orderby );
		}, $legacy_orderbys );
		foreach ( $legacy_orderbys as $legacy_orderby ) {
			$mod->order_by( $legacy_orderby['column'], $legacy_orderby['direction'], 5 );
		}

		$where = $this->searchwp_query_where();

		if ( $where ) {
			$mod->raw_where_sql( ' 1=1 ' . $this->searchwp_query_where() );
		}

		$mods[] = $mod;

		// Exclude hidden products?
		$excluded = $this->exclude_hidden_products( array() );
		if ( ! empty( $excluded ) ) {
			$source = \SearchWP\Utils::get_post_type_source_name( 'product' );
			$mod = new \SearchWP\Mod( $source );
			$mod->set_where( [ [
				'column'  => 'id',
				'value'   => $excluded,
				'compare' => 'NOT IN',
				'type'    => 'NUMERIC',
			] ] );

			$mods[] = $mod;
		}

		// Hide out of stock items?
		$out_of_stock = $this->maybe_exclude_out_of_stock_products( array() );
		if ( ! empty( $out_of_stock ) ) {
			$source = \SearchWP\Utils::get_post_type_source_name( 'product' );
			$mod = new \SearchWP\Mod( $source );
			$mod->set_where( [ [
				'column'  => 'id',
				'value'   => $out_of_stock,
				'compare' => 'NOT IN',
				'type'    => 'NUMERIC',
			] ] );

			$mods[] = $mod;
		}

		return $mods;
	}

	/**
	 * Initializer for SearchWP 3.x
	 */
	function init_legacy() {
		if ( ! function_exists( 'searchwp_get_option' ) ) {
			return;
		}

		// SearchWP hooks
		add_filter( 'searchwp_engine_settings_default', array( $this, 'limit_engine_to_products' ) );
		add_filter( 'searchwp_query_main_join', array( $this, 'query_main_join' ), 10, 2 );
		add_filter( 'searchwp_query_orderby', array( $this, 'query_orderby' ) );
		add_filter( 'searchwp_query_select_inject', array( $this, 'searchwp_query_inject' ) );
		add_filter( 'searchwp_where', array( $this, 'searchwp_query_where' ) );

		// Support highlighting.
		$existing_settings = searchwp_get_option( 'advanced' );
		if (
			is_array( $existing_settings )
			&& array_key_exists( 'highlight_terms', $existing_settings )
			&& ! empty( $existing_settings['highlight_terms']
			&& class_exists( 'SearchWPHighlighter' ) )
		) {
			add_filter( 'the_title', array( $this, 'maybe_highlight' ) );
			add_filter( 'get_the_excerpt', array( $this, 'maybe_highlight' ) );
		}
	}

	function maybe_highlight( $content ) {
		if (
			! $this->is_woocommerce_search()
			|| ! did_action( 'loop_start' )
			|| did_action( 'loop_end' )
		) {
			return $content;
		}

		$highlighter = new SearchWPHighlighter();

		return $highlighter->apply_highlight( $content, get_search_query() );
	}

	function json_search_products( $core_wc_products ) {
		if ( ! class_exists( 'SWP_Query' ) ) {
			return $core_wc_products;
		}

		$proceed = apply_filters( 'searchwp_woocommerce_hijack_json_search', false );
		if ( empty( $proceed ) ) {
			return $core_wc_products;
		}

		$args = array(
			's'                 => $_REQUEST['term'],
			'post_type'         => array( 'product', 'product_variation' ),
			'engine'            => 'default',
			'page'              => 1,
			'fields'            => 'ids',
			'posts_per_page'    => -1
		);

		$results = new SWP_Query( apply_filters( 'searchwp_woocommerce_json_search_products_args', $args ) );

		if ( empty( $results->posts ) ) {
			return $core_wc_products;
		}

		// @see WC_AJAX@json_search_products()
		$product_objects = array_filter( array_map( 'wc_get_product', $results->posts ), 'wc_products_array_filter_readable' );
		$products = array();

		foreach ( $product_objects as $product_object ) {
			$formatted_name = $product_object->get_formatted_name();
			$managing_stock = $product_object->managing_stock();

			if ( $managing_stock && ! empty( $_GET['display_stock'] ) ) {
				$formatted_name .= ' &ndash; ' . wc_format_stock_for_display( $product_object );
			}

			$products[ $product_object->get_id() ] = rawurldecode( $formatted_name );
		}

		return $products;
	}

	/**
	 * Retrieve the minimum price of a SearchWP-found results set
	 *
	 * @since 1.1.21
	 *
	 * @param float $amount The original minimum price.
	 *
	 * @return float        The actual minimum price of SearchWP-retrieved results
	 */
	function get_price_min_amount( $amount ) {
		global $wpdb;

		if ( empty( $this->results ) ) {
			return 0;
		}

		// This query is forked from WC_Widget_Price_Filter->get_filtered_price() and modified to
		// retrieve the proper min/max prices of the results set. This is inaccurate by default
		// because the Widget logic depends on appending Woo's native search SQL to the
		// query that runs here, but it allows us to override the native results for display
		$sql  = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";
		$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id ";
		$sql .= " 	WHERE price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
			AND price_meta.meta_value > '' ";
		$sql .= " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', $this->results ) ) . ")";

		$result = $wpdb->get_row( $sql );

		$this->price_min = floatval( $result->min_price );
		$this->price_max = floatval( $result->max_price );

		return $this->price_min;
	}

	/**
	 * Return the maximum price of a SearchWP-found results set (which was determined when finding the minimum)
	 *
	 * @since 1.1.21
	 *
	 * @param float $amount The original maximum price.
	 *
	 * @return float        The actual maximum price of SearchWP-retrieved results
	 */
	function get_price_max_amount( $amount ) {
		return $this->price_max;
	}

	/**
	 * We need to customize WooCommerce's visibility meta query because we're doing our own
	 * @param $meta_query
	 *
	 * @return mixed
	 */
	function woocommerce_product_query_meta_query( $meta_query ) {

		$proceed = apply_filters( 'searchwp_woocommerce_consider_visibility', true );

		if ( empty( $proceed ) ) {
			return $meta_query;
		}

		if ( isset( $meta_query['visibility'] ) && $this->is_woocommerce_search() ) {
			unset( $meta_query['visibility'] );
		}

		return $meta_query;
	}

	/**
	 * Even if it's not a WooCommerce search, we should exclude hidden WooCommerce product IDS
	 *
	 * @since 1.1.3
	 *
	 * @param $ids
	 *
	 * @return array
	 */
	function exclude_hidden_products( $ids ) {

		$proceed = apply_filters( 'searchwp_woocommerce_consider_visibility', true );

		if ( empty( $proceed ) ) {
			return $ids;
		}

		$args = array(
			'post_type'  => 'product',
			'nopaging'   => true,
			'fields'     => 'ids'
		);

		// WooCommerce 3.0 switched to powering visibility with ataxonomy
		if ( function_exists( 'WC' ) && ! empty( WC()->version ) && version_compare( WC()->version, '3.0.0', '>=' ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => 'exclude-from-search'
				)
			);
		} else {
			// Before WooCommerce 3.0 metadata was used
			$args['meta_query'] = array(
				array(
					'key'     => '_visibility',
					'value'   => array( 'hidden', 'catalog' ),
					'compare' => 'IN',
				),
			);
		}

		$hidden = get_posts( $args );

		if ( ! empty( $hidden ) ) {
			$ids = array_merge( $ids, $hidden );
		}

		return $ids;
	}

	/**
	 * If out of stock options should be hidden from search, exclude them from search
	 *
	 * @since 1.1.8
	 *
	 * @param $ids
	 *
	 * @return array
	 */
	function maybe_exclude_out_of_stock_products( $ids ) {

		if ( 'yes' !== get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			return $ids;
		}

		$args = array(
			'post_type'  => 'product',
			'nopaging'   => true,
			'fields'     => 'ids',
			'tax_query' => array( array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'outofstock',
				'operator' => 'IN',
			) ),
			// 'meta_query' => array(
			// 	array(
			// 		'key'     => '_stock_status',
			// 		'value'   => 'instock',
			// 		'compare' => '!=',
			// 	),
			// ),
		);

		$out_of_stock = get_posts( $args );

		if ( ! empty( $out_of_stock ) ) {
			$ids = array_merge( $ids, $out_of_stock );
		}

		return $ids;
	}

	/**
	 * Since we're simply replacing WooCommerce search field results, we want to limit even
	 * the default search engine settings to only include Products
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	function limit_engine_to_products( $settings ) {
		if ( $this->woocommerce_query ) {
			foreach ( $settings as $engine_post_type => $options ) {
				if ( $this->post_type !== $engine_post_type ) {
					$settings[ $engine_post_type ]['enabled'] = false;
				}
			}
		}

		return $settings;
	}

	/**
	 * @param $include
	 *
	 * @return array
	 */
	function include_filtered_posts( $include ) {
		$include = array_merge( (array) $include, $this->filtered_posts );

		return array_unique( $include );
	}

	/**
	 * Piggyback WooCommerce's Layered Navigation and inject SearchWP results where applicable
	 *
	 * @param $filtered_posts
	 *
	 * @return array
	 */
	function post_in( $filtered_posts ) {
		global $wp_query;

		if ( ! class_exists( 'SWP_Query' ) ) {
			return $filtered_posts;
		}

		// WooCommerce 2.6 introduced tax/meta query piggybacking that's much better
		if ( function_exists( 'WC' ) && function_exists( 'SWP' ) && ! empty( WC()->version ) && version_compare( WC()->version, '2.6.0', '<' ) ) {
			return $this->legacy_post_in( $filtered_posts );
		}

		$search_query = urldecode( get_search_query() );

		if ( $this->is_woocommerce_search()
			// && ! isset( $_GET['orderby'] )
			&& $search_query === $this->original_query ) {

			if ( ! empty( $this->results ) ) {
				return $this->results;
			}

			$searchwp_engine = 'default';
			$swppg = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

			// force SearchWP to only consider the filtered posts
			if ( ! empty( $filtered_posts ) ) {
				$this->filtered_posts = $filtered_posts;
				add_filter( 'searchwp\post__in', array( $this, 'include_filtered_posts' ) );
				add_filter( 'searchwp_include',  array( $this, 'include_filtered_posts' ) ); // SearchWP 3.x compat.
			}

			do_action( 'searchwp_woocommerce_before_search', $this );

			if ( ! apply_filters( 'searchwp_woocommerce_log_searches', true ) ) {
				add_filter( 'searchwp\statistics\log', '__return_false' );
				add_filter( 'searchwp_log_search', '__return_false' ); // SearchWP 3.x compat.
			}

			$wc_query = new WC_Query();

			$args = array(
				's'              => $this->original_query,
				'engine'         => $searchwp_engine,
				'page'           => $swppg,
				'fields'         => 'ids',
				'posts_per_page' => -1,
				'tax_query'      => $wc_query->get_tax_query(),
				'meta_query'     => $wc_query->get_meta_query(),
			);

			// WooCommerce 3.0 has additional params for get_tax_query() and get_meta_query()
			if ( function_exists( 'WC' ) && ! empty( WC()->version ) && version_compare( WC()->version, '3.0.0', '>=' ) ) {
				$args['tax_query']  = $wc_query->get_tax_query( array(), true );
				$args['meta_query'] = $wc_query->get_meta_query( array(), true );
			}

			$args = apply_filters( 'searchwp_woocommerce_query_args', $args );

			$results = new SWP_Query( $args );

			$this->results = $results->posts;

			// Force 'no results' if the results are empty
			if ( empty( $this->results ) ) {
				$this->results = array( 0 );
			}


			// Once our search has run we don't want to interfere any any subsequent queries
			add_filter( 'searchwp\native\short_circuit', '__return_true' );
			add_filter( 'searchwp_force_wp_query', '__return_true' ); // SearchWP 3.x compat.
			add_filter( 'searchwp_short_circuit', '__return_true' ); // SearchWP 3.x compat.

			return $this->results;
		} elseif( ! empty( $this->results ) ) {
			return $this->results;
		} // End if().

		return (array) $filtered_posts;
	}

	/**
	 * Legacy post retrieval for WooCommerce <2.6
	 *
	 * @param $filtered_posts
	 *
	 * @return array
	 */
	function legacy_post_in( $filtered_posts ) {
		global $wp_query;

		if ( $this->is_woocommerce_search()
		     && function_exists( 'SWP' )
		     && ! isset( $_GET['orderby'] )
		     && $query = get_search_query() ) {

			$searchwp_engine = 'default';
			$searchwp = SWP();
			$swppg = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

			add_filter( 'searchwp_load_posts', '__return_false' );
			add_filter( 'searchwp_posts_per_page', array( $this, 'set_pagination' ) );

			// force SearchWP to only consider the filtered posts
			if ( ! empty( $filtered_posts ) ) {
				$this->filtered_posts = $filtered_posts;
				add_filter( 'searchwp_include', array( $this, 'include_filtered_posts' ) );
			}

			// don't log this search, it's redundant
			add_filter( 'searchwp_log_search', '__return_false' );
			$this->results = $searchwp->search( $searchwp_engine, $query, $swppg );
			remove_filter( 'searchwp_log_search', '__return_false' );

			remove_filter( 'searchwp_load_posts', '__return_false' );
			remove_filter( 'searchwp_posts_per_page', array( $this, 'set_pagination' ) );

			$filtered_posts = array_intersect( $this->results, (array) $filtered_posts );
			$filtered_posts = array_unique( $filtered_posts );

			// also set our WooCommerce Instance IDs
			WC()->query->unfiltered_product_ids = $this->results;
		}

		return (array) $filtered_posts;
	}

	/**
	 * Callback for the_posts so we can tell WC about our filtered IDs for Layered Nav Widgets
	 *
	 * @since 1.1.4
	 *
	 * @param $posts
	 * @param bool $query
	 *
	 * @return mixed
	 */
	public function the_posts( $posts, $query = false ) {
		WC()->query->unfiltered_product_ids = $this->results;
		WC()->query->filtered_product_ids = $this->results;
		WC()->query->layered_nav_product_ids = $this->results;

		return $posts;
	}

	/**
	 * WooCommerce stores products in view as a transient based on $wp_query but that falls apart
	 * with search terms that rely on SearchWP, WP_Query's s param returns nothing, and that gets used by WC
	 */
	function hijack_query_vars() {
		global $wp_query;

		if ( $this->is_woocommerce_search()
			&& ( function_exists( 'SWP' ) || defined( 'SEARCHWP_VERSION' ) )
			&& ! isset( $_GET['orderby'] )
			&& $this->original_query ) {

			$wp_query->set( 'post__in', array() );
			$wp_query->set( 's', '' );

			if ( isset( $wp_query->query['s'] ) ) {
				unset( $wp_query->query['s'] );
			}
		}
	}

	/**
	 * Put back the search query once we've hijacked it to get around WooCommerce's products in view storage
	 */
	function replace_original_search_query() {
		global $wp_query;

		if ( ! empty( $this->original_query ) ) {
			$wp_query->set( 's', $this->original_query );
		}
	}

	/**
	 * Determines whether Layered Navigation is active right now
	 *
	 * @return bool
	 */
	function is_layered_navigation_active() {
		$active = false;

		if ( is_active_widget( false, false, 'woocommerce_layered_nav', true ) && ! is_admin() ) {
			if ( is_array( $_GET ) ) {
				foreach ( $_GET as $get_key => $get_var ) {
					// our 'flag' will be a GET variable present that isn't the basic search results page
					// as identified by the native WordPress search trigger 's' and Woo's 'post_type'
					if ( ! in_array( $get_key, apply_filters( 'searchwp_woocommerce_native_get_vars', $this->native_get_vars ) ) ) {
						$active = true;
						break;
					}
				}
			}
		}

		return $active;
	}

	/**
	 * Determine whether a WooCommerce search is taking place
	 * @return bool
	 */
	function is_woocommerce_search() {

		$woocommerce_search = apply_filters( 'searchwp_woocommerce_forced', false );

		if (
			( is_search()
			  || (
				  is_archive()
				  && isset( $_GET['s'] )
				  && ! empty( $_GET['s'] )
			  )
			)
			&& isset( $_GET['post_type'] )
			&& 'product' == $_GET['post_type']
		) {
			$woocommerce_search = true;
		}

		return $woocommerce_search;
	}

	/**
	 * Utilize WooCommerce's WC_Query object to retrieve information about any ordering that's going on
	 */
	function get_woocommerce_ordering() {
		if ( ! $this->is_woocommerce_search() ) {
			return;
		}

		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		if ( ! isset( WC()->query ) && ! is_object( WC()->query ) ) {
			return;
		}

		if ( ! method_exists( WC()->query, 'get_catalog_ordering_args' ) ) {
			return;
		}

		$this->ordering = WC()->query->get_catalog_ordering_args();
		$this->ordering['wc_orderby'] = isset( $_GET['orderby'] ) ? $_GET['orderby'] : '';
	}


	/**
	 * Set our environment variables once a WooCommerce query is in progress
	 *
	 * @param $q
	 * @param $woocommerce
	 */
	function product_query( $q, $woocommerce ) {
		global $wp_query;

		$this->woocommerce_query = $q;
		$this->woocommerce = $woocommerce;

		if ( $this->is_woocommerce_search() ) {
			$q->set( 's', '' );
		}

		// if SearchWP found search results we want the order of results to be returned by SearchWP weight in descending order
		if ( $this->is_woocommerce_search() && apply_filters( 'searchwp_woocommerce_force_weight_sort', true ) ) {
			$wp_query->set( 'order', 'DESC' );
			$wp_query->set( 'orderby', 'post__in' );

			// if it's not the main Search page, it's the WooCommerce Shop page
			if ( ! is_search() && wc_get_page_id( 'shop' ) == get_queried_object_id() ) {
				$wp_query->set( 's', '' );
			}
		}
	}

	/**
	 * WooCommerce Layered Nav Widgets fire a query to get term counts on each load, when SearchWP is in play
	 * these counts can be incorrect when searching for taxonomy terms that match the Layered Nav filters
	 * so we need to hijack this query entirely, run our own, and generate new SQL for WooCommerce to fire
	 *
	 * @param $query
	 *
	 * @return mixed
	 */
	function get_filtered_term_product_counts_query( $query ) {
		global $wpdb;

		if ( empty( $this->results ) ) {
			return $query;
		}

		// If we've found results and there are supposed to be zero results, we need to force that here
		if ( 1 === count( $this->results ) && isset( $this->results[0] ) && 0 === $this->results[0] ) {
			$query['where'] .= " AND 1 = 0";
		} else {
			// Modify the WHERE clause to also include SearchWP-provided results
			$query['where'] .= " AND {$wpdb->posts}.ID IN (" . implode( ',', array_map( 'absint', $this->results ) ) . ")";
		}

		return $query;
	}

	/**
	 * Depending on the sorting taking place we may need a custom JOIN in the main SearchWP query
	 *
	 * @param $sql
	 * @param $engine
	 *
	 * @return string
	 */
	function query_main_join( $sql, $engine ) {
		global $wpdb;

		if ( isset( $engine ) ) {
			$engine = null;
		}

		// if WooCommerce is sorting results we need to tell SearchWP to return them in that order
		if ( $this->is_woocommerce_search() ) {

			if ( ! isset( $this->ordering['wc_orderby'] ) ) {
				$this->get_woocommerce_ordering();
			}

			// depending on the sorting we need to do different things
			if ( isset( $this->ordering['wc_orderby'] ) ) {
				switch ( $this->ordering['wc_orderby'] ) {
					case 'price':
					case 'price-desc':
					case 'popularity':
						$meta_key = 'price' === $this->ordering['wc_orderby'] ? '_price' : 'total_sales';
						$sql = $sql . $wpdb->prepare( " LEFT JOIN {$wpdb->postmeta} AS swpwc ON {$wpdb->posts}.ID = swpwc.post_id AND swpwc.meta_key = %s", $meta_key );
						break;
					case 'rating':
						$sql = $sql . " LEFT OUTER JOIN {$wpdb->comments} swpwpcom ON({$wpdb->posts}.ID = swpwpcom.comment_post_ID) LEFT JOIN {$wpdb->commentmeta} swpwpcommeta ON(swpwpcom.comment_ID = swpwpcommeta.comment_id) ";
						break;
				}
			}

			// for visibility we always need to join postmeta
			if ( function_exists( 'WC' ) && ! empty( WC()->version ) && version_compare( WC()->version, '2.6.0', '<' ) ) { // Moved to a taxonomy
				$sql = $sql . " INNER JOIN {$wpdb->postmeta} as woovisibility ON {$wpdb->posts}.ID = woovisibility.post_id ";
			}

		}

		return $sql;
	}

	/**
	 * Handle the varous sorting capabilities offered by WooCommerce by makikng sure SearchWP respects them since
	 * we are always ordering by post__in based on SearchWP's retrieved results
	 *
	 * @param $orderby
	 *
	 * @return string
	 */
	function query_orderby( $orderby ) {
		global $wpdb;

		$array_return = [];

		if ( $this->is_woocommerce_search() && ! empty( $this->ordering ) ) {

			if ( ! isset( $this->ordering['wc_orderby'] ) ) {
				$this->get_woocommerce_ordering();
			}

			// depending on the sorting we need to do different things
			if ( isset( $this->ordering['wc_orderby'] ) ) {
				$order = isset( $this->ordering['order'] ) ? $this->ordering['order'] : 'ASC';
				switch ( $this->ordering['wc_orderby'] ) {
					case 'price':
					case 'price-desc':
					case 'popularity':
						$order = in_array( $this->ordering['wc_orderby'], array( 'popularity', 'price-desc' ) )
							? 'DESC' : $order;

						$array_return[] = [
							'column'    => 'swpwc.meta_value+0',
							'direction' => $order,
						];

						// SeachWP 3.x compat.
						$orderby = "ORDER BY swpwc.meta_value+0 {$order}, " . str_replace( 'ORDER BY', '', $orderby );
						break;
					/* case 'price-desc':
						$orderby = "ORDER BY {$wpdb->postmeta}.meta_value+0 DESC, " . str_replace( 'ORDER BY', '', $orderby );
						break; */
					case 'date':
						$array_return[] = [
							'column'    => "{$wpdb->posts}.post_date",
							'direction' => 'DESC',
						];

						// SeachWP 3.x compat.
						$orderby = 'ORDER BY post_date DESC';
						break;
					case 'rating':
						$array_return[] = [
							'column'    => 'average_rating',
							'direction' => 'DESC',
						];
						$array_return[] = [
							'column'    => "{$wpdb->posts}.post_date",
							'direction' => 'DESC',
						];

						// SeachWP 3.x compat.
						$orderby = "ORDER BY average_rating DESC, {$wpdb->posts}.post_date DESC";
						break;
					case 'name':
						$array_return[] = [
							'column'    => 'post_title',
							'direction' => 'ASC',
						];

						// SeachWP 3.x compat.
						$orderby = 'ORDER BY post_title ASC';
						break;
				}
			}
		}

		return $this->is_legacy_searchwp() ? $orderby : $array_return;
	}

	/**
	 * Callback for SearchWP's main query that facilitates integrating WooCommerce ratings
	 *
	 * @return string
	 */
	function searchwp_query_inject() {
		global $wpdb;

		$sql = '';

		if ( ! isset( $this->ordering['wc_orderby'] ) ) {
			$this->get_woocommerce_ordering();
		}

		if ( $this->is_woocommerce_search() && ! empty( $this->ordering ) ) {
			// ratings need moar SQL
			if ( 'rating' === $this->ordering['wc_orderby'] ) {
				$sql = " AVG( swpwpcommeta.meta_value ) as average_rating ";
			}
		}

		return $sql;
	}

	/**
	 * Callback for SearchWP's main query that facilitates sorting by WooCommerce rating
	 *
	 * @return string
	 */
	function searchwp_query_where() {
		global $wpdb;

		$sql = '';

		if ( ! isset( $this->ordering['wc_orderby'] ) ) {
			$this->get_woocommerce_ordering();
		}

		if ( $this->is_woocommerce_search() && ! empty( $this->ordering ) ) {
			// ratings need moar SQL
			if ( 'rating' === $this->ordering['wc_orderby'] ) {
				$sql = " AND ( swpwpcommeta.meta_key = 'rating' OR swpwpcommeta.meta_key IS null ) ";
			}
		}

		// Pre WooCommerce 3.0: use meta to limit visibility
		if ( function_exists( 'WC' ) && ! empty( WC()->version ) && version_compare( WC()->version, '3.0.0', '<' ) ) {
			if ( $this->is_woocommerce_search() ) {
				// visibility
				if ( apply_filters( 'searchwp_woocommerce_consider_visibility', true ) ) {
					if ( apply_filters( 'searchwp_woocommerce_consider_visibility_variations', true ) ) {
						$sql .= " AND ( ( {$wpdb->prefix}posts.post_type = 'product_variation' OR ( woovisibility.meta_key = '_visibility' AND CAST( woovisibility.meta_value AS CHAR ) IN ( 'visible', 'search' ) ) ) ) ";
					} else {
						$sql .= " AND ( ( woovisibility.meta_key = '_visibility' AND CAST( woovisibility.meta_value AS CHAR ) IN ( 'visible', 'search' ) ) ) ";
					}
				}
			}
		}

		return $sql;
	}

	/**
	 * Callback to set SearchWP pagination
	 *
	 * @return int
	 */
	function set_pagination() {
		global $wp_query;

		return (int) $wp_query->get( 'posts_per_page' );
	}
}

// kickoff
new SearchWP_WooCommerce_Integration();
