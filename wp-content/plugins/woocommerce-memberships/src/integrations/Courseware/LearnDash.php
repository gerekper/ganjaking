<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Integrations\Courseware;

use SkyVerge\WooCommerce\Memberships\Integrations\Courseware;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for LearnDash plugin.
 *
 * @since 1.12.3
 */
class LearnDash extends Courseware {


	/** @var string course plugin ID */
	protected $course_plugin_id = 'learndash';

	/** @var string course post type */
	protected $course_post_type = 'sfwd-courses';


	/**
	 * Adds hooks.
	 *
	 * @since 1.22.0
	 */
	protected function add_hooks() {

		parent::add_hooks();

		add_filter( 'learndash_content', [ $this, 'learndash_restricted_content' ], 1, 2 );
		add_filter( 'get_post_metadata', [ $this, 'get_post_metadata' ], 10, 4 );

		add_action( 'learndash_course_completed', [ $this, 'handle_prerequisite_course_completed' ] );
	}


	/**
	 * Forces LearnDash to query posts when retrieving course steps (lessons).
	 *
	 * @internal
	 *
	 * @see get_metadata()
	 *
	 * @since 1.13.2
	 *
	 * @param null|array|string $value the value get_metadata() should return - a single metadata value, or an array of values
	 * @param int $post_id post ID
	 * @param string $meta_key meta key
	 * @param bool $single whether to return only the first value of the specified $meta_key
	 * @return array|null|string the metadata value
	 */
	public function get_post_metadata( $value, $post_id, $meta_key, $single ) {

		return 'ld_course_steps_dirty' === $meta_key ?: $value;
	}


	/**
	 * Filters the LearnDash course content for members when restricted.
	 *
	 * Helps us hide the course overview and status, too.
	 *
	 * @since 1.12.3
	 *
	 * @param string $content the learner content
	 * @param \WP_Post $post the post object
	 * @return string updated content
	 */
	public function learndash_restricted_content( $content, $post ) {

		$user_id = get_current_user_id();
		$post_id = $post instanceof \WP_Post ? $post->ID : get_the_ID(); // this filter runs in the loop

		// bail if we don't have a post ID or the post is public
		if ( ! $post_id || ! wc_memberships_is_post_content_restricted( $post_id ) ) {
			return $content;
		}

		if ( ! wc_memberships_user_can( $user_id, 'view', array( 'post' => $post_id ) ) ) {

			$message_type = 'restricted';

			if ( ! current_user_can( 'wc_memberships_view_restricted_post_content', $post_id ) ) {
				$message_type = 'restricted';
			} elseif ( ! current_user_can( 'wc_memberships_view_delayed_post_content', $post_id ) ) {
				$message_type = 'delayed';
			}

			$message_code = "content_{$message_type}";
			$args         = [
				'access_time' => wc_memberships()->get_capabilities_instance()->get_user_access_start_time_for_post( $user_id, $post_id ),
				'code'        => $message_code,
				'post'        => $post,
				'post_id'     => $post_id,
				'use_excerpt' => false,
			];

			$content = \WC_Memberships_User_Messages::get_message_html( $message_code, $args );
		}

		return $content;
	}


	/**
	 * Handles prerequisite course completion.
	 *
	 * @since 1.22.0
	 *
	 * @internal
	 *
	 * @param array $course_data
	 */
	public function handle_prerequisite_course_completed( $course_data = [] ) {

		if ( ! is_array( $course_data ) || ! isset( $course_data['user'], $course_data['course'], $course_data['user']->ID, $course_data['course']->ID ) ) {
			return;
		}

		$this->maybe_start_dependent_courses( (int) $course_data['user']->ID, (int) $course_data['course']->ID );
	}


	/**
	 * Checks if the user is already enrolled in the given course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $user_id
	 * @param int $course_id
	 * @return bool
	 */
	protected function is_user_enrolled_in_course( int $user_id, int $course_id ): bool {

		// note: this will always return true for "open" courses
		return function_exists( 'sfwd_lms_has_access' ) && sfwd_lms_has_access( $course_id, $user_id );
	}


	/**
	 * Checks if the user has completed all prerequisites for the given course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $user_id
	 * @param int $course_id
	 * @return bool
	 */
	protected function has_user_completed_course_prerequisites( int $user_id, int $course_id ): bool {

		return function_exists( 'learndash_is_course_prerequities_completed' ) && learndash_is_course_prerequities_completed( $course_id, $user_id );
	}


	/**
	 * Enrolls the user in the given course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $user_id
	 * @param int $course_id
	 */
	protected function enroll_user_in_course( int $user_id, int $course_id ) {

		if ( ! function_exists( 'ld_update_course_access' ) ) {
			return;
		}

		ld_update_course_access( $user_id, $course_id );
	}


	/**
	 * Gets the dependent courses for a prerequisite course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $course_id
	 * @return array associative array of post IDs and course posts
	 */
	protected function get_dependent_courses( int $course_id ) : array {

		$courses = [];
		$posts = get_posts( [
			'post_type'   => $this->get_course_post_type(),
			'post_status' => 'publish',
			'post__in'    => $this->get_dependent_course_ids( $course_id ),
		] );

		foreach ( $posts as $post ) {
			$courses[ $post->ID ] = $post;
		}

		return $courses;
	}


	/**
	 * Gets IDs of all courses that have the given course as a prerequisite.
	 *
	 * LearnDash stores most course settings in a serialized array inside a single post meta record.
	 * Querying serialized data directly in SQL is a fragile matter, so instead we'll get all post meta with the
	 * `_sfwd-courses` meta key, find post IDs that have prerequisites enabled & the completed course as prerequisite.
	 *
	 * @since 1.22.0
	 *
	 * @param int $course_id
	 * @return int[] array of course IDs
	 */
	private function get_dependent_course_ids( int $course_id ) : array {
		global $wpdb;

		$dependent_course_ids = [];

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s",
				'_' . $this->get_course_post_type()
			)
		);

		foreach ( $results as $result ) {

			$meta = maybe_unserialize( $result->meta_value );

			if ( ! is_array( $meta ) ) {
				continue;
			}

			if ( isset( $meta['sfwd-courses_course_prerequisite_enabled'] ) && 'on' !== $meta['sfwd-courses_course_prerequisite_enabled'] ) {
				continue;
			}

			if ( ! isset( $meta['sfwd-courses_course_prerequisite'] ) || ! is_array( $meta['sfwd-courses_course_prerequisite'] ) ) {
				continue;
			}

			if ( ! in_array( $course_id, $meta['sfwd-courses_course_prerequisite'], false ) ) {
				continue;
			}

			$dependent_course_ids[] = (int) $result->post_id;
		}

		return $dependent_course_ids;
	}


}
