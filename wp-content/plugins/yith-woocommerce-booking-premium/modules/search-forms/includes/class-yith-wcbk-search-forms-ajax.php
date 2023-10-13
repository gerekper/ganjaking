<?php
/**
 * Class YITH_WCBK_Search_Forms_Ajax
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\SearchForms
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Forms_Ajax' ) ) {
	/**
	 * Class YITH_WCBK_Search_Forms_Ajax
	 */
	class YITH_WCBK_Search_Forms_Ajax {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Search_Form_Frontend constructor.
		 */
		protected function __construct() {
			add_action( 'yith_wcbk_frontend_ajax_search_forms_search_booking_products', array( $this, 'ajax_search_booking_products' ) );
			add_action( 'yith_wcbk_frontend_ajax_search_forms_search_booking_products_paged', array( $this, 'ajax_search_booking_products_paged' ) );
		}

		/**
		 * Define Search Form Results const
		 */
		private function set_in_search_const() {
			if ( ! defined( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS' ) ) {
				define( 'YITH_WCBK_IS_IN_AJAX_SEARCH_FORM_RESULTS', true );
			}
		}

		/**
		 * Search booking products
		 */
		public function ajax_search_booking_products() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			yith_wcbk_ajax_start( 'frontend' );

			if ( isset( $_REQUEST['yith-wcbk-booking-search'] ) && 'search-bookings' === $_REQUEST['yith-wcbk-booking-search'] ) {

				/**
				 * DO_ACTION: yith_wcbk_before_set_search_products_query
				 * Hook to perform any action before setting the search product query (when searching bookable products though a Search Form).
				 */
				do_action( 'yith_wcbk_before_set_search_products_query' );

				$this->set_in_search_const();

				$from         = wc_clean( wp_unslash( $_REQUEST['from'] ?? '' ) );
				$to           = wc_clean( wp_unslash( $_REQUEST['to'] ?? '' ) );
				$persons      = wc_clean( wp_unslash( $_REQUEST['persons'] ?? 1 ) );
				$person_types = wc_clean( wp_unslash( $_REQUEST['person_types'] ?? array() ) );
				$services     = wc_clean( wp_unslash( $_REQUEST['services'] ?? array() ) );

				if ( ! ! $person_types && is_array( $person_types ) ) {
					$persons = array_sum( array_values( $person_types ) );
				}

				$product_ids = yith_wcbk_search_booking_products( $_REQUEST );

				if ( ! $product_ids ) {
					ob_start();

					echo wp_kses_post( apply_filters( 'yith_wcbk_search_booking_products_no_bookings_available_text', __( 'No booking available for this search', 'yith-booking-for-woocommerce' ) ) );

					/**
					 * DO_ACTION: yith_wcbk_search_booking_products_no_bookings_available_after
					 * Hook to render something after the "no booking available" message (if no bookable products was found).
					 */
					do_action( 'yith_wcbk_search_booking_products_no_bookings_available_after' );

					wp_send_json_success(
						array(
							'results_html' => ob_get_clean(),
						)
					);
				}

				$current_page = 1;

				$args     = array(
					'post_type'           => 'product',
					'ignore_sticky_posts' => 1,
					'no_found_rows'       => 1,
					'posts_per_page'      => apply_filters( 'yith_wcbk_ajax_search_booking_products_posts_per_page', 12 ),
					'paged'               => $current_page,
					'post__in'            => $product_ids,
					'orderby'             => 'post__in',
					'meta_query'          => WC()->query->get_meta_query(),
				);
				$args     = apply_filters( 'yith_wcbk_ajax_search_booking_products_query_args', $args, $product_ids );
				$products = new WP_Query( $args );

				$booking_request = array(
					'from'             => $from,
					'to'               => $to,
					'persons'          => $persons,
					'person_types'     => $person_types,
					'booking_services' => $services,
				);

				wp_send_json_success(
					array(
						'results_html' => yith_wcbk_get_module_template_html( 'search-forms', 'results/results.php', compact( 'booking_request', 'products', 'product_ids', 'current_page' ), 'booking/search-form/' ),
					)
				);
			}

			// phpcs:enable
		}

		/**
		 * Search booking products paged
		 */
		public function ajax_search_booking_products_paged() {
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			yith_wcbk_ajax_start( 'frontend' );

			if ( ! empty( $_REQUEST['product_ids'] ) && ! empty( $_REQUEST['booking_request'] ) && ! empty( $_REQUEST['page'] ) ) {
				$this->set_in_search_const();

				$product_ids     = wc_clean( wp_unslash( $_REQUEST['product_ids'] ) );
				$booking_request = wc_clean( wp_unslash( $_REQUEST['booking_request'] ) );
				$current_page    = absint( $_REQUEST['page'] );

				$args = array(
					'post_type'           => 'product',
					'ignore_sticky_posts' => 1,
					'no_found_rows'       => 1,
					'posts_per_page'      => apply_filters( 'yith_wcbk_ajax_search_booking_products_posts_per_page', 12 ),
					'paged'               => $current_page,
					'post__in'            => $product_ids,
					'meta_query'          => WC()->query->get_meta_query(),
				);
				$args = apply_filters( 'yith_wcbk_ajax_search_booking_products_query_args', $args, $product_ids );

				$products = new WP_Query( $args );

				wp_send_json_success(
					array(
						'results_html' => yith_wcbk_get_module_template_html( 'search-forms', 'results/results-list.php', compact( 'products', 'booking_request' ), 'booking/search-form/' ),
					)
				);
			}
			// phpcs:enable
		}
	}
}
