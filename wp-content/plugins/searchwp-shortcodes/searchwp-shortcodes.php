<?php
/*
Plugin Name: SearchWP Shortcodes
Plugin URI: https://searchwp.com/extensions/shortcodes/
Description: Provides Shortcodes that generate both search forms and results pages for SearchWP search engines
Version: 1.7.0
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

if ( ! defined( 'SEARCHWP_SHORTCODES_VERSION' ) ) {
	define( 'SEARCHWP_SHORTCODES_VERSION', '1.7.0' );
}

/**
 * instantiate the updater
 */
if ( ! class_exists( 'SWP_Shortcodes_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/vendor/updater.php' );
}

// set up the updater
function searchwp_shortcodes_update_check(){

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
	$searchwp_shortcodes_updater = new SWP_Shortcodes_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 33253,
			'version'   => SEARCHWP_SHORTCODES_VERSION,
			'license'   => $license_key,
			'item_name' => 'Shortcodes',
			'author'    => 'SearchWP',
			'url'       => site_url(),
		)
	);

	return $searchwp_shortcodes_updater;
}

add_action( 'admin_init', 'searchwp_shortcodes_update_check' );

class SearchWP_Shortcodes {

	public $query   = '';
	public $page    = 1;
	public $results = array();
	public $engine  = 'default';
	public $max_num_pages = 0;

	public function __construct() {
		$this->page     = isset( $_REQUEST['swppg'] ) ? absint( $_REQUEST['swppg'] ) : 1;

		add_shortcode( 'searchwp_search_form', array( $this, 'search_form_output' ) );
		add_shortcode( 'searchwp_search_results_pagination', array( $this, 'search_results_pagination_output' ) );
		add_shortcode( 'searchwp_search_results_paginate_links', array( $this, 'search_results_paginate_links' ) );
		add_shortcode( 'searchwp_search_results_none', array( $this, 'search_results_none_output' ) );
		add_shortcode( 'searchwp_search_results', array( $this, 'search_results_output' ) );
		add_shortcode( 'searchwp_search_result_link', array( $this, 'search_result_link_output' ) );
		add_shortcode( 'searchwp_search_result_excerpt', array( $this, 'search_result_excerpt_output' ) );
		add_shortcode( 'searchwp_search_result_excerpt_global', array( $this, 'search_result_excerpt_global_output' ) );
		add_shortcode( 'searchwp_search_result_excerpt_document', array( $this, 'search_result_excerpt_document_output' ) );
		add_shortcode( 'searchwp_total_results', array( $this, 'total_results_output' ) );
	}

	public function prevent_logging() {
		return true;
	}

	public function total_results_output( $attributes ) {
		$args = shortcode_atts( array(
			'engine'         => 'default',
			'var'            => 'swpquery',
		), $attributes );

		$this->maybe_set_search_query( $args['var'] );

		if ( ! class_exists( 'SearchWP' ) || empty( $this->query ) ) {
			return '';
		}

		add_filter( 'searchwp_log_search', array( $this, 'prevent_logging' ), 31 );
		add_filter( 'searchwp\statistics\log', array( $this, 'prevent_logging' ), 31 );

		// Allow developers to filter the engine at runtime.
		$engine = (string) apply_filters( 'searchwp_shortcodes_engine', (string) $args['engine'] );

		// Perform the search.
		if ( function_exists( 'SWP' ) ) {
			$this->results = SWP()->search( sanitize_text_field( $engine ), $this->query, $this->page );
			remove_filter( 'searchwp_log_search', array( $this, 'prevent_logging' ), 31 );
			return SWP()->foundPosts;
		} else {
			$results = new \SWP_Query( [
				's'      => $this->query,
				'engine' => sanitize_text_field( $engine ),
				'page'   => $this->page,
			] );

			remove_filter( 'searchwp\statistics\log', array( $this, 'prevent_logging' ), 31 );

			return $results->found_posts;
		}
	}

	public function maybe_set_search_query( $var = 'swpquery' ) {
		if ( empty( $this->query ) ) {
			$var         = sanitize_text_field( $var );
			$this->query = isset( $_REQUEST[ $var ] ) ? sanitize_text_field( html_entity_decode ( stripslashes( $_REQUEST[ $var ] ) ) ) : '';
		}
	}

	public function search_form_output( $attributes ) {
		$args = shortcode_atts( array(
			'target'      => '',
			'engine'      => 'default',
			'var'         => 'swpquery',
			'button_text' => __( 'Search' ),
			'placeholder' => __( 'Search' ),
		), $attributes );

		$this->maybe_set_search_query( $args['var'] );

		// Allow developers to filter the engine at runtime.
		$engine = (string) apply_filters( 'searchwp_shortcodes_engine', (string) $args['engine'] );

		ob_start(); ?>
		<?php do_action( 'searchwp_shortcodes_before_wrapper' ); ?>
		<div class="searchwp-search-form searchwp-supplemental-search-form">
			<?php do_action( 'searchwp_shortcodes_before_form' ); ?>
			<form role="search" method="get" id="searchform" class="searchform" action="<?php echo esc_url( $args['target'] ); ?>">
				<div>
					<?php do_action( 'searchwp_shortcodes_before_label' ); ?>
					<label class="screen-reader-text" for="swpquery"><?php esc_html_e( 'Search for:' ); ?></label>
					<?php do_action( 'searchwp_shortcodes_after_label' ); ?>
					<?php do_action( 'searchwp_shortcodes_before_input' ); ?>
					<input
						type="text"
						placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
						value="<?php echo esc_attr( $this->query ); ?>"
						name="<?php echo esc_attr( $args['var'] ); ?>"
						id="<?php echo esc_attr( $args['var'] ); ?>">
					<?php do_action( 'searchwp_shortcodes_after_input' ); ?>
					<input type="hidden" name="engine" value="<?php echo esc_attr( $engine ); ?>" />
					<?php do_action( 'searchwp_shortcodes_before_button' ); ?>
					<input type="submit" id="searchsubmit" value="<?php echo esc_attr( $args['button_text'] ); ?>">
					<?php do_action( 'searchwp_shortcodes_after_button' ); ?>
				</div>
			</form>
			<?php do_action( 'searchwp_shortcodes_after_form' ); ?>
		</div>
		<?php
		do_action( 'searchwp_shortcodes_after_wrapper' );

		return ob_get_clean();
	}

	public function search_results_output( $attributes, $content = null ) {
		global $post, $searchwp_shortcodes_posts_per_page;

		$args = shortcode_atts( array(
			'engine'         => 'default',
			'posts_per_page' => 10,
			'var'            => 'swpquery',
		), $attributes );

		$searchwp_shortcodes_posts_per_page = absint( $args['posts_per_page'] );

		$this->maybe_set_search_query( $args['var'] );

		if ( empty( $this->query ) ) {
			return;
		}

		// Allow developers to filter the engine at runtime.
		$engine = (string) apply_filters( 'searchwp_shortcodes_engine', (string) $args['engine'] );

		if ( class_exists( 'SearchWP' ) ) {
			$engine = sanitize_text_field( $engine );

			// Set up custom posts per page.
			if ( ! function_exists( 'searchwp_shortcodes_posts_per_page' ) ) {
				function searchwp_shortcodes_posts_per_page() {
					global $searchwp_shortcodes_posts_per_page;

					return absint( $searchwp_shortcodes_posts_per_page );
				}
			}
			add_filter( 'searchwp_posts_per_page', 'searchwp_shortcodes_posts_per_page', 99 );

			// Perform the search.
			if ( function_exists( 'SWP' ) ) {
				$this->results = SWP()->search( $engine, $this->query, $this->page );
			} else {
				$results = new \SWP_Query( [
					's'      => $this->query,
					'engine' => $engine,
					'page'   => $this->page,
				] );

				$this->results = $results->posts;
				$this->max_num_pages = $results->max_num_pages;
			}
		}

		ob_start();
		if ( ! empty( $this->query ) && ! empty( $this->results ) ) {
			foreach ( $this->results as $post ) {
				setup_postdata( $post );
				echo do_shortcode( $content );
			}
			wp_reset_postdata();
		}
		return ob_get_clean();
	}

	public function search_result_link_output( $atts ) {
		global $post;

		$args = shortcode_atts( array(
			'direct' => 'true',
		), $atts );

		$direct = 'true' !== strtolower( (string) $args['direct'] ) ? false : true;

		if ( $direct && isset( $post->post_type ) && 'attachment' === $post->post_type ) {
			$permalink = wp_get_attachment_url( $post->ID );
		} else {
			$permalink = get_permalink();
		}

		ob_start();
		echo '<a href="' . esc_url( $permalink ) . '">' . wp_kses_post( get_the_title() ) . '</a>';

		return ob_get_clean();
	}

	public function search_result_excerpt_output() {
		ob_start();
		the_excerpt();
		return ob_get_clean();
	}

	public function search_result_excerpt_global_output() {
		global $post;

		ob_start();
		if ( function_exists( 'searchwp_term_highlight_the_excerpt_global' ) ) {
			searchwp_term_highlight_the_excerpt_global( $post->ID, null, $this->query );
		} else if ( class_exists( 'SearchWP\Entry' ) ) {
			$entry  = new \SearchWP\Entry( 'post' . SEARCHWP_SEPARATOR . $post->post_type, $post->ID );
			$source = $entry->get_source();

			if ( method_exists( $source, 'get_global_excerpt' ) ) {
				return $source->get_global_excerpt( $entry, $this->query );
			} else {
				the_excerpt();
			}
		} else {
			the_excerpt();
		}
		return ob_get_clean();
	}

	public function search_result_excerpt_document_output() {
		global $post;

		ob_start();
		if ( function_exists( 'searchwp_term_highlight_the_excerpt_global' ) ) {
			searchwp_term_highlight_the_excerpt_global( $post->ID, 'searchwp_content', $this->query );
		} else if ( class_exists( 'SearchWP\Utils' ) ) {
			$meta_value = get_post_meta( $post->ID, 'searchwp_content', true );
			if ( ! empty( $meta_value ) && SearchWP\Utils::string_has_substring_from_string( $meta_value, $this->query ) ) {
				return SearchWP\Utils::trim_string_around_substring( $meta_value, $this->query );
			} else {
				the_excerpt();
			}
		} else {
			the_excerpt();
		}
		return ob_get_clean();
	}

	public function search_results_none_output( $atts, $content = null ) {

		if ( isset( $atts ) ) {
			$atts = null;
		}

		ob_start();
		if ( ! empty( $this->query ) && empty( $this->results ) && ! empty( $content ) ) {
			echo wp_kses_post( $content );
		}
		return ob_get_clean();
	}

	public function search_results_paginate_links( $atts ) {
		global $searchwp;

		// defaults based on https://codex.wordpress.org/Function_Reference/paginate_links
		$atts = shortcode_atts( array(
			'base'               => '%_%',
			'format'             => '?swppg=%#%',
			'total'              => $searchwp->maxNumPages,
			'current'            => $this->page,
			'show_all'           => false,
			'end_size'           => 1,
			'mid_size'           => 2,
			'prev_next'          => true,
			'prev_text'          => __( '« Previous' ),
			'next_text'          => __( 'Next »' ),
			'type'               => 'plain',
			'add_args'           => false,
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => '',
			'engine'             => 'default',
			'var'                => 'swpquery',
			'big'                => 999999999,
		), $atts );

		$atts['engine'] = (string) apply_filters( 'searchwp_shortcodes_engine', (string) $atts['engine'] );
		$atts['var']    = esc_attr( $atts['var'] );

		$atts = apply_filters( 'searchwp_shortcodes_paginate_links', $atts );

		ob_start();

		if ( $searchwp->maxNumPages > 1 ) :
			?>
			<div class="searchwp-paginate-links">
				<?php echo wp_kses_post( paginate_links( $atts ) ); ?>
			</div>
			<?php

		endif;

		return ob_get_clean();
	}

	public function search_results_pagination_output( $atts, $content ) {
		global $searchwp;

		if ( isset( $content ) ) {
			$content = '';
		}

		$args = shortcode_atts( array(
			'engine'    => 'default',
			'direction' => 'prev',
			'link_text' => __( 'More' ),
			'var'       => 'swpquery',
		), $atts );

		// Allow developers to filter the engine at runtime.
		$engine = (string) apply_filters( 'searchwp_shortcodes_engine', (string) $args['engine'] );

		$this->maybe_set_search_query( $args['var'] );

		if ( empty( $this->query ) ) {
			return;
		}

		if ( 'next' !== strtolower( $args['direction'] ) ) {
			$args['direction'] = 'prev';
		}

		$max_num_pages = isset( $searchwp->maxNumPages ) ? $searchwp->maxNumPages : $this->max_num_pages;

		$prev_page = $this->page > 1 ? $this->page - 1 : false;
		$next_page = $this->page < $max_num_pages ? $this->page + 1 : false;

		ob_start();
		?>
		<?php if ( $max_num_pages > 1 ) : ?>
			<?php if ( 'prev' === strtolower( $args['direction'] ) ) : ?>
				<?php if ( $prev_page ) : ?>
					<div class="nav-previous">
						<?php
						$link = get_permalink() . '?' . esc_attr( $args['var'] ) . '=' . rawurlencode( $this->query ) . '&swppg=' . absint( $prev_page ) . '&engine=' . esc_attr( $engine );
						$link = apply_filters( 'searchwp_shortcodes_pagination_prev', $link );
						?>
						<a href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $args['link_text'] ); ?></a>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php if ( $next_page ) : ?>
					<div class="nav-next">
						<?php
						$link = get_permalink() . '?' . esc_attr( $args['var'] ) . '=' . rawurlencode( $this->query ) . '&swppg=' . absint( $next_page ) . '&engine=' . esc_attr( $engine );
						$link = apply_filters( 'searchwp_shortcodes_pagination_next', $link );
						?>
						<a href="<?php echo esc_url( $link ); ?>"><?php echo wp_kses_post( $args['link_text'] ); ?></a>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php
		return ob_get_clean();
	}

}

new SearchWP_Shortcodes();
