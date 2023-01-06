<?php

namespace WCML\Rest;

use WPML\FP\Fns;

class Generic {

	/**
	 * Prevent WPML redirection when using the default language as a parameter in the url
	 */
	public static function preventDefaultLangUrlRedirect() {
		$exp = explode( '?', $_SERVER['REQUEST_URI'] );
		if ( ! empty( $exp[1] ) ) {
			parse_str( $exp[1], $vars );
			if ( isset( $vars['lang'] ) && $vars['lang'] === wpml_get_default_language() ) {
				unset( $vars['lang'] );
				$_SERVER['REQUEST_URI'] = $exp[0] . '?' . http_build_query( $vars );
			}
		}
	}

	/**
	 * @param \WP_Query $wp_query
	 */
	public static function autoAdjustIncludedIds( \WP_Query $wp_query ) {
		$lang    = $wp_query->get( 'lang' );
		$include = $wp_query->get( 'post__in' );
		if ( empty( $lang ) && ! empty( $include ) ) {
			$filtered_include = [];
			foreach ( $include as $id ) {
				$filtered_include[] = wpml_object_id_filter( $id, get_post_type( $id ), true );
			}
			$wp_query->set( 'post__in', $filtered_include );
		}
	}

	/**
	 * We need an unfiltered 'home_url' so that the REST signature matches.
	 *
	 * Note that WPML already does this, but fails to recognize a REST
	 * request when we get a 'relative' home_url.
	 */
	public static function removeHomeUrlFilterOnRestAuthentication() {
		$returnTrue = Fns::always( true );

		add_filter( 'determine_current_user', Fns::tap( function() use ( $returnTrue ) {
			add_filter( 'wpml_skip_convert_url_string', $returnTrue );
		} ), 10 );

		add_filter( 'determine_current_user', Fns::tap( function() use ( $returnTrue ) {
			remove_filter( 'wpml_skip_convert_url_string', $returnTrue );
		} ), 20 );
	}

}
