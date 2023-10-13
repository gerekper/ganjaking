<?php
/**
 * Functions
 *
 * @package YITH\Booking\Modules\Costs
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_extra_cost_ids' ) ) {
	/**
	 * Get extra costs.
	 *
	 * @param array $args The arguments.
	 *
	 * @return int[]
	 */
	function yith_wcbk_get_extra_cost_ids( array $args = array() ) {
		$defaults = array(
			'items_per_page' => 10,
			'post_status'    => 'publish',
			'order_by'       => 'title',
			'order'          => 'ASC',
		);

		$args       = wp_parse_args( $args, $defaults );
		$query_args = $args;

		/**
		 * DO_ACTION: yith_wcbk_before_get_extra_costs
		 * Hook to perform any action before retrieving Extra Costs.
		 *
		 * @param array $args The arguments passed to the get_posts.
		 */
		do_action( 'yith_wcbk_before_get_extra_costs', $args );

		$key_mapping = array(
			'status'         => 'post_status',
			'page'           => 'paged',
			'exclude'        => 'post__not_in',
			'include'        => 'post__in',
			'items_per_page' => 'posts_per_page',
			'order_by'       => 'orderby',
		);

		foreach ( $key_mapping as $key => $wp_key ) {
			if ( isset( $query_args[ $key ] ) ) {
				$query_args[ $wp_key ] = $query_args[ $key ];
				unset( $query_args[ $key ] );
			}
		}

		$query_args['post_type'] = YITH_WCBK_Post_Types::EXTRA_COST;
		$query_args['fields']    = 'ids';

		$posts = get_posts( $query_args );

		/**
		 * DO_ACTION: yith_wcbk_after_get_extra_costs
		 * Hook to perform any action after retrieving Extra Costs.
		 *
		 * @param array $args The arguments passed to the get_posts.
		 */
		do_action( 'yith_wcbk_after_get_extra_costs', $args );

		return $posts;
	}
}

if ( ! function_exists( 'yith_wcbk_product_extra_cost' ) ) {
	/**
	 * Retrieve an extra cost.
	 *
	 * @param array $args Arguments.
	 *
	 * @return YITH_WCBK_Product_Extra_Cost|YITH_WCBK_Product_Extra_Cost_Custom
	 */
	function yith_wcbk_product_extra_cost( $args ) {
		$extra_cost = $args instanceof YITH_WCBK_Product_Extra_Cost ? $args : new YITH_WCBK_Product_Extra_Cost( $args );

		if ( $extra_cost->is_custom() && ! $extra_cost instanceof YITH_WCBK_Product_Extra_Cost_Custom ) {
			$extra_cost = new YITH_WCBK_Product_Extra_Cost_Custom( $extra_cost->get_data() );
		}

		return $extra_cost;
	}
}

if ( ! function_exists( 'yith_wcbk_product_extra_costs_array_reduce' ) ) {
	/**
	 * Extra costs reduce.
	 *
	 * @param array                        $result     The result array.
	 * @param YITH_WCBK_Product_Extra_Cost $extra_cost The extra cost.
	 *
	 * @return array
	 */
	function yith_wcbk_product_extra_costs_array_reduce( $result, $extra_cost ) {
		if ( $extra_cost->is_valid() ) {
			$result[ $extra_cost->get_identifier() ] = $extra_cost;
		}

		return $result;
	}
}
