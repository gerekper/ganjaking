<?php
/**
 * Shortcodes Extended class
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Shortcode_Extended' ) ) {
	/**
	 * YITH WCWL Shortcodes Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Shortcode_Extended {

		/**
		 * Constructor
		 *
		 * @since 2.0.0
		 */
		public function __construct() {
			// Filters applied to add params to wishlist views.
			add_filter( 'yith_wcwl_wishlist_params', array( 'YITH_WCWL_Shortcode_Extended', 'wishlist_view' ), 5, 6 );
		}

		/**
		 * Filters template params, to add view-specific variables
		 *
		 * @param array  $additional_params Array of params to filter.
		 * @param string $action            Action from query string.
		 * @param array  $action_params     Array of query-string params.
		 * @param string $pagination        Whether or not pagination is enabled for template (not always required; value showuld be "yes" or "no").
		 * @param string $per_page          Number of elements per page (required only if $pagination == 'yes'; should be a numeric string).
		 * @param array  $atts              Original attributes passed via shortcode.
		 *
		 * @return array Filtered array of params
		 * @since 2.0.0
		 */
		public static function wishlist_view( $additional_params, $action, $action_params, $pagination, $per_page, $atts ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
			/* === VIEW TEMPLATE === */
			if ( ! empty( $additional_params['template_part'] ) && 'view' === $additional_params['template_part'] ) {
				$wishlist        = isset( $additional_params['wishlist'] ) ? $additional_params['wishlist'] : false;
				$no_interactions = isset( $additional_params['no_interactions'] ) ? $additional_params['no_interactions'] : false;

				$show_quantity = 'yes' === get_option( 'yith_wcwl_quantity_show' );
				$show_update   = $wishlist && $wishlist->current_user_can( 'update_wishlist' ) && ! $no_interactions && ( $show_quantity );

				$additional_params = array_merge(
					$additional_params,
					array(
						'show_quantity' => $show_quantity,
						/**
						 * APPLY_FILTERS: yith_wcwl_show_wishlist_update_button
						 *
						 * Filter whether to show the update button in the wishlist.
						 *
						 * @param bool               $show_update Whether to show update button or not
						 * @param YITH_WCWL_Wishlist $wishlist    Wishlist object
						 *
						 * @return bool
						 */
						'show_update'   => apply_filters( 'yith_wcwl_show_wishlist_update_button', $show_update, $wishlist ),
					)
				);
			}

			return $additional_params;
		}
	}
}

return new YITH_WCWL_Shortcode_Extended();
