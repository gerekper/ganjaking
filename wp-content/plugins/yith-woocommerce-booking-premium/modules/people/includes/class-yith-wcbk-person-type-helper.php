<?php
/**
 * Class YITH_WCBK_Person_Type_Helper
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Person_Type_Helper' ) ) {
	/**
	 * Class YITH_WCBK_Person_Type_Helper
	 * helper class for Person Types
	 */
	class YITH_WCBK_Person_Type_Helper {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Post type.
		 *
		 * @var string
		 */
		public $post_type_name;

		/**
		 * YITH_WCBK_Person_Type_Helper constructor.
		 */
		protected function __construct() {
			$this->post_type_name = YITH_WCBK_Post_Types::PERSON_TYPE;
		}

		/**
		 * Get all person types by arguments
		 *
		 * @param array $args Arguments.
		 *
		 * @return WP_Post[]|int[]
		 */
		public function get_person_types( $args = array() ) {
			$do_actions = ! isset( $args['suppress_filters'] ) || true === $args['suppress_filters'];
			if ( $do_actions ) {
				/**
				 * DO_ACTION: yith_wcbk_before_get_person_types
				 * Hook to perform any action before retrieving Person Types.
				 *
				 * @param array $args The arguments passed to the get_posts.
				 */
				do_action( 'yith_wcbk_before_get_person_types', $args );
			}

			$default_args = array(
				'post_type'        => $this->post_type_name,
				'post_status'      => 'publish',
				'posts_per_page'   => - 1,
				'suppress_filters' => false,
			);

			$args  = wp_parse_args( $args, $default_args );
			$posts = get_posts( $args );

			if ( $do_actions ) {
				/**
				 * DO_ACTION: yith_wcbk_after_get_person_types
				 * Hook to perform any action after retrieving Person Types.
				 *
				 * @param array $args The arguments passed to the get_posts.
				 */
				do_action( 'yith_wcbk_after_get_person_types', $args );
			}

			return $posts;
		}

		/**
		 * Get all person type ids by arguments
		 *
		 * @param array $args Arguments.
		 *
		 * @return int[]
		 */
		public function get_person_type_ids( $args = array() ) {
			$default_args = array(
				'fields' => 'ids',
			);

			$args = wp_parse_args( $args, $default_args );

			return $this->get_person_types( $args );
		}


		/**
		 * Get all person types in array id => name
		 *
		 * @return array
		 */
		public function get_person_types_array() {
			$ids          = $this->get_person_type_ids();
			$person_types = array();

			if ( ! ! $ids && is_array( $ids ) ) {
				foreach ( $ids as $id ) {
					$person_types[ $id ] = get_the_title( $id );
				}
			}

			return $person_types;
		}

		/**
		 * Get person type title.
		 *
		 * @param int $person_type_id Person type ID.
		 *
		 * @return string
		 */
		public function get_person_type_title( $person_type_id ) {
			return apply_filters( 'yith_wcbk_get_person_type_title', get_the_title( $person_type_id ), $person_type_id );
		}
	}
}
