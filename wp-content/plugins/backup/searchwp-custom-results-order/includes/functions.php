<?php

/**
 * Retrieves the buoy key used for a specific query/engine.
 *
 * @since 1.0
 *
 * @param string $query The search query.
 * @param string $engine The search engine.
 *
 * @return string The full buoy key.
 */
function searchwp_cro_get_buoy_key( $query = '', $engine = 'default' ) {
	if ( empty( $query ) ) {
		$query = get_search_query( $query );
	}

	$query = strtolower( $query );

	return 'searchwp_cro_' . md5( $query . $engine );
}

/**
 * Retrieves promoted posts (IDs) for a specific query/engine.
 *
 * @since 1.0
 *
 * @param string $query The search query.
 * @param string $engine The search engine.
 *
 * @return array Post IDs of promoted posts.
 */
function searchwp_cro_get_promoted( $query = '', $engine = 'default' ) {
	if ( empty( $query ) ) {
		$query = get_search_query( $query );
	}

	return get_posts( array(
		'post_type'   => 'any',
		'post_status' => 'any',
		'nopaging'    => true,
		'fields'      => 'ids',
		'order'       => 'DESC',
		'orderby'     => 'meta_value_num',
		'meta_query'  => array(
			array(
				'key'     => searchwp_cro_get_buoy_key( $query, $engine ),
				'compare' => 'EXISTS',
			),
		),
	) );
}

/**
 * Retrieves CRO settings.
 *
 * @since 1.0
 *
 * @return bool|array The existing settings.
 */
function searchwp_cro_get_settings() {
	return get_option( 'searchwp_cro_settings' );
}

/**
 * Determine whether a single post is promoted for a specific query/engine.
 *
 * @since 1.1
 *
 * @param int    $post_id The post ID.
 * @param string $query The search query.
 * @param string $engine The search engine.
 *
 * @return boolean|int Whether the post is promoted, buoy value (int) if promoted.
 */
function searchwp_cro_is_promoted( $post_id, $query = '', $engine = 'default' ) {
	if ( empty( $query ) ) {
		$query = get_search_query( $query );
	}

	$meta_key = searchwp_cro_get_buoy_key( $query, $engine );
	$promoted = get_post_meta( absint( $post_id ), $meta_key, true );

	return ! empty( $promoted ) ? absint( $promoted ) : false;
}
