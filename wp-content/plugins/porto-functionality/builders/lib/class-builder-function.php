<?php


/**
 * Suggester for autocomplete by post type builder id
 *
 * @since 2.3.0
 */
if ( ! function_exists( 'builder_id_callback' ) ) {
	function builder_id_callback( $query ) {
		$query_args = array(
			'post_type'      => PortoBuilders::BUILDER_SLUG,
			'post_status'    => 'publish',
			'posts_per_page' => 15,
			'tax_query'      => array(
				array(
					'taxonomy' => PortoBuilders::BUILDER_TAXONOMY_SLUG,
					'field'    => 'name',
					'terms'    => array( 'type' ),
				),
			),
		);
		if ( $query ) {
			if ( is_numeric( $query ) ) {
				$query_args['p'] = (int) $query;
			} else {
				$query_args['s'] = sanitize_text_field( $query );
			}
		}
		$templates = new WP_Query( $query_args );
		$results   = array();
		if ( $templates->have_posts() ) {
			$templates = $templates->get_posts();
			foreach ( $templates as $t ) {
				$results[] = array(
					'label' => str_replace( array( '&amp;', '&#039;' ), array( '&', '\'' ), esc_html( $t->post_title ) ),
					'value' => (int) $t->ID,
				);
			}
		}
		return $results;
	}
}

/**
 * Find post type builder by id
 *
 * @since 2.3.0
 */
if ( ! function_exists( 'builder_id_render' ) ) {
	function builder_id_render( $query ) {
		$result = $query;
		$query  = trim( $query['value'] );
		if ( $query ) {
			$template = get_post( (int) $query );
			if ( $template ) {
				$result = array(
					'label' => str_replace( array( '&amp;', '&#039;' ), array( '&', '\'' ), esc_html( $template->post_title ) ),
					'value' => (int) $template->ID,
				);
			}
		}
		return $result;
	}
}
