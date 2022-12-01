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
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for Sensei LMS.
 *
 * @since 1.21.0
 */
class Sensei extends Courseware {


	/** @var string course plugin ID */
	protected $course_plugin_id = 'sensei';

	/** @var string course post type */
	protected $course_post_type = 'course';


	/**
	 * Adds action and filter hooks.
	 *
	 * @since 1.22.0
	 */
	protected function add_hooks() {

		parent::add_hooks();

		add_action( 'sensei_user_course_end', [ $this, 'handle_prerequisite_course_completed' ], 10, 2 );

		// add custom content sections for Sensei lessons and courses
		add_filter( 'wc_membership_plan_members_area_sections', [ $this, 'add_sensei_members_area_section' ], 20 );

		if ( ! is_admin() ) {
			// remove Sensei posts from "My Content"
			add_filter( 'wc_memberships_get_restricted_posts_query_args', [ $this, 'adjust_members_area_my_content_query' ], 10, 2 );
		}
	}


	/**
	 * Adjusts the "My content" posts query.
	 *
	 * @internal
	 *
	 * @since 1.21.0
	 *
	 * @param string[] $query_args args for retrieving membership content
	 * @param string $type Type of request: 'content_restriction', 'product_restriction', 'purchasing_discount'
	 * @return string[] updated query args
	 */
	public function adjust_members_area_my_content_query( array $query_args, string $type ) : array {

		// only adjust this if we're looking at "My Content"
		if ( 'content_restriction' === $type ) {

			$frontend = wc_memberships()->get_frontend_instance();

			if ( $frontend && $frontend->get_my_account_instance()->get_members_area_instance()->is_members_area() ) {
				unset( $query_args['post_type']['course'], $query_args['post_type']['lesson'] );
			}
		}

		return $query_args;
	}


	/**
	 * Adds a new content section for Sensei lessons and courses.
	 *
	 * @internal
	 *
	 * @since 1.21.0
	 *
	 * @param string[] $sections the member area sections
	 * @return string[] the updated sections
	 */
	public function add_sensei_members_area_section( array $sections ) : array {

		$new_sections = [];

		// add the new section after "My Content"
		foreach ( $sections as $key => $section ) {

			$new_sections[ $key ] = $section;

			if ( 'my-membership-content'  === $key ) {
				$new_sections['my-membership-sensei'] = __( 'Courses & Lessons', 'woocommerce-memberships' );
			}
		}

		return $new_sections;
	}


	/**
	 * Handle prerequisite course completion.
	 *
	 * @since 1.22.0
	 *
	 * @internal
	 *
	 * @param int $user_id
	 * @param int $course_id
	 */
	public function handle_prerequisite_course_completed( $user_id, $course_id ) {

		$this->maybe_start_dependent_courses( (int) $user_id, (int) $course_id );
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
	protected function is_user_enrolled_in_course( int $user_id, int $course_id ) : bool {

		return is_callable( 'Sensei_Course::is_user_enrolled' ) && \Sensei_Course::is_user_enrolled( $user_id, $course_id );
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
	protected function has_user_completed_course_prerequisites( int $user_id, int $course_id ) : bool {

		if ( ! is_callable( 'Sensei_Course::is_prerequisite_complete' ) ) {
			return false;
		}

		/**
		 * Since Sensei does not provide allow passing a user ID to
		 * {@see \Sensei_Course::is_prerequisite_complete()},
		 * we need to temporarily set the current user to the user we need.
		 * {@see \get_user_by()} is used to ensure a user for the given ID exists.
		 */
		$current_user   = wp_get_current_user();
		$temporary_user = get_user_by( 'id', $user_id );

		// this shouldn't happen, but better safe than sorry
		if ( ! $temporary_user || ! $current_user || ! $current_user->ID ) {
			return false;
		}

		wp_set_current_user( $temporary_user->ID );

		$is_complete = \Sensei_Course::is_prerequisite_complete( $course_id );

		wp_set_current_user( $current_user->ID );

		return $is_complete;
	}


	/**
	 * Enrolls the user in the given course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $user_id
	 * @param int $course_id
	 * @throws \Exception
	 */
	protected function enroll_user_in_course( int $user_id, int $course_id ) {

		if ( ! is_callable( 'Sensei_Course_Enrolment::get_course_instance' ) ) {
			return;
		}

		// provides some loose backwards compatibility as the `enrol` method here was updated more recently
		if ( $course = \Sensei_Course_Enrolment::get_course_instance( $course_id ) ) {
			if ( is_callable( [ $course, 'enrol' ] ) ) {
				$course->enrol( $user_id );
			} else {
				$course->save_enrolment( $user_id, true );
			}
		}
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
			'meta_key'    => '_course_prerequisite',
			'meta_value'  => $course_id,
			'post_status' => 'publish'
		]);

		foreach ( $posts as $post ) {
			$courses[ $post->ID ] = $post;
		}

		return $courses;
	}


	/**
	 * Gets the temporary current user ID.
	 *
	 * @internal
	 * @deprecated since 1.22.4
	 *
	 * @TODO remove this deprecated method by August 2022 or version 2.0.0 {unfulvio 2021-08-2022}
	 *
	 * @since 1.22.0
	 *
	 * @param null $user_id
	 * @return int|null
	 */
	public function use_temporary_current_user_id( $user_id = null ) {

		wc_deprecated_function( __METHOD__, '1.22.4', 'wp_set_current_user()' );

		return $user_id;
	}


}
