<?php
/**
 * REST API Products categories controller customized for Bookenberg.
 *
 * Handles requests to the /products/categories endpoint.
 *
 * @package WooCommerce\Bookings\Rest\Controller
 */

/**
 * REST API Products categories controller class.
 */
class WC_Bookings_REST_Products_Categories_Controller extends WC_REST_Product_Categories_Controller {

	use WC_Bookings_Rest_Permission_Check;

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = WC_Bookings_REST_API::V1_NAMESPACE;

	/**
	 * Get terms associated with a taxonomy.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_items( $request ) {

		// Add filter to only return categories with bookable products.
		add_filter( 'terms_clauses', array( $this, 'add_bookable_product_category_filter' ), 10, 3 );

		$items = parent::get_items( $request );

		// Remove filter as it's only used by the get_terms() call that just happened.
		remove_filter( 'terms_clauses', array( $this, 'add_bookable_product_category_filter' ) );

		return $items;
	}

	/**
	 * Get a single term from a taxonomy.
	 *
	 * Return category data only if it is
	 * assigned to at least 1 booking product.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Request|WP_Error|array
	 */
	public function get_item( $request ) {
		$booking_term = get_term_by( 'id', (int) $request['id'], 'product_cat' );

		$count = 0;
		if ( $booking_term ) {
			$args     = array(
				'type'     => 'booking',
				'category' => array( $booking_term->slug ),
				'return'   => 'ids',
				'limit'    => 1,
			);
			$products = wc_get_products( $args );
			$count    = count( $products );
		}

		// If no booking products are using the term, do not return its data.
		if ( 0 === $count ) {
			return array();
		}

		return parent::get_item( $request );
	}

	/**
	 * Filters the terms query SQL clauses so only categories with bookable products are returned.
	 * Product type is also a term so we have to do many joins.
	 *
	 * @param string[] $pieces     Array of query SQL clauses.
	 * @param string[] $taxonomies An array of taxonomy names.
	 * @param array    $args       An array of term query arguments.
	 *
	 * @return array
	 */
	public function add_bookable_product_category_filter( $pieces, $taxonomies, $args ) {
		global $wpdb;

		$pieces['join']    .= " INNER JOIN {$wpdb->term_relationships} AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id";
		$pieces['join']    .= " INNER JOIN {$wpdb->term_relationships} AS tr2 ON tr2.object_id = tr.object_id";
		$pieces['join']    .= " INNER JOIN {$wpdb->term_taxonomy} AS tt2 ON tt2.term_taxonomy_id = tr2.term_taxonomy_id AND tt2.taxonomy = 'product_type'";
		$pieces['join']    .= " INNER JOIN {$wpdb->terms} AS t2 ON tt2.term_id = t2.term_id";
		$pieces['where']   .= $wpdb->prepare( ' AND t2.name = %s', 'booking' );
		$pieces['join']    .= " INNER JOIN {$wpdb->posts} AS post ON post.ID = tr.object_id AND post.post_status = 'publish'";
		$pieces['distinct'] = 'DISTINCT';

		return $pieces;
	}
}
