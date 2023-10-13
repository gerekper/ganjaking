<?php
/**
 * Class YITH_WCBK_Search_Form_Helper
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Form_Helper' ) ) {
	/**
	 * Search Form Helper
	 *
	 * @deprecated 4.0.0
	 */
	class YITH_WCBK_Search_Form_Helper {
		use YITH_WCBK_Singleton_Trait;

		const RESULT_KEY_IN_BOOKING_DATA = 'bk-sf-res';

		/**
		 * The post type name.
		 *
		 * @var string
		 */
		public $post_type_name = YITH_WCBK_Post_Types::SEARCH_FORM;

		/**
		 * __get function.
		 * Handle backward compatibility.
		 *
		 * @param string $key The key.
		 *
		 * @return mixed
		 */
		public function __get( $key ) {

			if ( 'post_type_name' === $key ) {
				yith_wcbk_doing_it_wrong( __CLASS__ . '::' . $key, 'To get the post type you can use YITH_WCBK_Post_Types::SEARCH_FORM', '4.0.0' );
			}

			return null;
		}

		/**
		 * The constructor.
		 */
		protected function __construct() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Search_Form_Helper::__construct', '4.0.0' );
		}

		/**
		 * Get all search forms by arguments
		 *
		 * @param array $args Arguments.
		 *
		 * @return WP_Post[]|int[]
		 */
		public function get_forms( $args = array() ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Search_Form_Helper::get_forms', '4.0.0', 'yith_wcbk_get_search_forms' );
			$default_args = array(
				'post_type'      => YITH_WCBK_Post_Types::SEARCH_FORM,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
			);

			$args = wp_parse_args( $args, $default_args );

			return get_posts( $args );
		}

		/**
		 * Get forms in array id -> name
		 *
		 * @return array
		 */
		public function get_forms_in_array() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Search_Form_Helper::get_forms_in_array', '4.0.0', 'yith_wcbk_get_search_forms' );
			$form_ids = $this->get_forms();
			$forms    = array();

			if ( $form_ids && is_array( $form_ids ) ) {
				foreach ( $form_ids as $form_id ) {
					$forms[ $form_id ] = get_the_title( $form_id );
				}
			}

			return $forms;
		}

		/**
		 * Get a search form by id
		 *
		 * @param int $form_id The search form ID.
		 *
		 * @return WP_Post|bool
		 * @deprecated 4.0.0 | use yith_wcbk_get_search_form instead.
		 */
		public function get_form( $form_id ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Search_Form_Helper::get_form', '4.0.0', 'yith_wcbk_get_search_form' );
			$form = get_post( $form_id );

			if ( $form && YITH_WCBK_Post_Types::SEARCH_FORM === $form->post_type ) {
				return $form;
			}

			return false;
		}

		/**
		 * Search booking products
		 *
		 * @param array $args Arguments.
		 *
		 * @return array|bool
		 * @since      1.0.8
		 * @deprecated 4.0.0 | use yith_wcbk_search_booking_products instead.
		 */
		public function search_booking_products( $args ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Search_Form_Helper::search_booking_products', '4.0.0', 'yith_wcbk_search_booking_products' );

			return function_exists( 'yith_wcbk_search_booking_products' ) ? yith_wcbk_search_booking_products( $args ) : array();
		}

		/**
		 * Get searched values from query string
		 * used for showing default values in booking form by searched values
		 *
		 * @param string $key The key.
		 *
		 * @return bool|false|string
		 * @deprecated 4.0.0 | use yith_wcbk_get_query_string_param instead.
		 */
		public static function get_searched_value_for_field( $key ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Search_Form_Helper::get_searched_value_for_field', '4.0.0', 'yith_wcbk_get_query_string_param' );

			return yith_wcbk_get_query_string_param( $key );
		}
	}
}
