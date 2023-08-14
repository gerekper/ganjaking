<?php

/**
 * SearchWP REST API search handler.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

/**
 * Class Rest is responsible for taking over REST searches.
 *
 * @since 4.0
 */
class Rest extends \WP_REST_Post_Search_Handler {

	/**
	 * Searches the object type content for a given search request.
	 *
	 * @since 5.0.0
	 *
	 * @param WP_REST_Request $request Full REST request.
	 * @return array Associative array containing an `WP_REST_Search_Handler::RESULT_IDS` containing
	 *               an array of found IDs and `WP_REST_Search_Handler::RESULT_TOTAL` containing the
	 *               total count for the matching search results.
	 */
	public function search_items( \WP_REST_Request $request ) {
		$args = [
			'engine' => apply_filters( 'searchwp\rest\engine', 'default', [ 'request' => $request ] ),
			'fields' => 'ids',
		];

		if ( ! empty( $request['search'] ) ) {
			$args['s'] = $request['search'];
		}

		if ( ! empty( $request['page'] ) ) {
			$args['page'] = $request['page'];
		}

		if ( ! empty( $request['per_page'] ) ) {
			$args['posts_per_page'] = $request['per_page'];
		}

		if ( ! empty( $request['subtype'] ) ) {
			$args['post_type'] = $request['subtype'];

			if ( in_array( 'any', $args['post_type'], true ) ) {
				$args['post_type'] = 'any';
			}
		}

		$args  = apply_filters( 'searchwp\rest\args', $args, [ 'request' => $request ] );
		$query = empty( $args['s'] ) || empty( $args['engine'] ) ? new \WP_Query( $args ) : new \SWP_Query( $args );
		$found = $query->posts;
		$total = $query->found_posts;

		return [
			self::RESULT_IDS   => $found,
			self::RESULT_TOTAL => $total,
		];
	}
}
