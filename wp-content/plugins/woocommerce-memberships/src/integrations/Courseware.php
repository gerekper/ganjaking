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

namespace SkyVerge\WooCommerce\Memberships\Integrations;

use SkyVerge\WooCommerce\Memberships\Integrations\Courseware\Admin;
use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Abstract class for Courseware/eLearning integrations.
 *
 * @TODO consider introducing some helper methods to get the current version of supported plugins, if useful {unfulvio 2021-05-19}
 *
 * @since 1.22.0
 */
abstract class Courseware {


	/** @var string membership plan rule meta key for setting an option whether to auto-enroll members of the plan in a given course */
	const COURSE_AUTO_ENROLL_PLAN_RULE_META_KEY = 'course_auto_enroll';


	/** @var string course plugin ID */
	protected $course_plugin_id;

	/** @var string course post type */
	protected $course_post_type;


	/**
	 * Courseware constructor.
	 *
	 * @since 1.22.0
	 */
	public function __construct() {
		$this->add_hooks();
	}


	/**
	 * Adds action and filter hooks.
	 *
	 * @since 1.22.0
	 */
	protected function add_hooks() {

		// load the courseware admin handler
		add_action( 'admin_init', function() {

			require_once( wc_memberships()->get_plugin_path() . '/src/integrations/Courseware/Admin.php' );

			new Admin( $this );
		} );

		add_action( 'wc_memberships_user_membership_status_changed', [ $this, 'handle_user_membership_status_changed' ] );
		add_action( 'wc_memberships_user_membership_saved', [ $this, 'handle_user_membership_saved' ], 10, 2 );
	}


	/**
	 * Gets the course post type.
	 *
	 * @since 1.22.0
	 *
	 * @return string
	 */
	public function get_course_post_type() : string {

		return $this->course_post_type;
	}


	/**
	 * Handles user membership status changes.
	 *
	 * @internal
	 *
	 * @since 1.22.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership the user membership
	 */
	public function handle_user_membership_status_changed( $user_membership ) {

		if ( ! $user_membership ) {
			return;
		}

		$this->maybe_start_courses_associated_with_membership( $user_membership );
	}


	/**
	 * Handles user membership saved.
	 *
	 * @internal
	 *
	 * @since 1.22.0
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan the Membership Plan
	 * @param array $args optional arguments
	 */
	public function handle_user_membership_saved( $membership_plan, $args = [] ) {

		$user_membership_id = isset( $args['user_membership_id'] ) ? absint( $args['user_membership_id'] ) : null;

		if ( ! ( $user_membership = wc_memberships_get_user_membership( $user_membership_id ) ) ) {
			return;
		}

		$this->maybe_start_courses_associated_with_membership( $user_membership );
	}


	/**
	 * Attempts to start courses associated with an active membership.
	 *
	 * @internal
	 *
	 * @since 1.22.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership the user membership
	 */
	protected function maybe_start_courses_associated_with_membership( \WC_Memberships_User_Membership $user_membership ) {

		/** do not use {@see \WC_Memberships_User_Membership::is_active()} here to avoid triggering checks that may expire the membership */
		if ( ! in_array( $user_membership->get_status(), wc_memberships()->get_user_memberships_instance()->get_active_access_membership_statuses(), true ) ) {
			return;
		}

		foreach ( $this->get_user_membership_courses( $user_membership ) as $course ) {

			$this->auto_enroll_course( (int) $course->ID, $user_membership );
		}
	}


	/**
	 * Gets courses which the user's membership plan grants access to.
	 *
	 * @since 1.22.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership
	 * @return \WP_Post[]
	 */
	protected function get_user_membership_courses( \WC_Memberships_User_Membership $user_membership ) : array {

		$membership_plan = $user_membership->get_plan();

		if ( empty( $membership_plan ) ) {
			return [];
		}

		$restricted_courses = $membership_plan->get_restricted_content( 0, [
			'post_type' => $this->get_course_post_type()
		] );

		return $restricted_courses ? $restricted_courses->get_posts() : [];
	}


	/**
	 * Auto-enrolls user in the given course based on their membership.
	 *
	 * @since 1.22.0
	 *
	 * @param int $course_id
	 * @param \WC_Memberships_User_Membership $user_membership
	 */
	protected function auto_enroll_course( int $course_id, \WC_Memberships_User_Membership $user_membership ) {

		/**
		 * Filters whether to auto-enroll a given user in a course.
		 *
		 * Determine if we should automatically enroll users on a specific course
		 * that is part of a user membership and has not started yet.
		 *
		 * By default, users who are not enrolled in the course and have completed the course prerequisites
		 * will be auto-enrolled.
		 *
		 * @since 1.22.0
		 *
		 * @param bool $auto_enroll_course
		 * @param int $user_id the user that will start this course
		 * @param int $course_id the course that will be started
		 * @param \WC_Memberships_User_Membership $user_membership the user membership
		 */
		$auto_enroll_course = (bool) apply_filters(
			"wc_memberships_{$this->course_plugin_id}_auto_enroll_course",
			$this->should_user_auto_enroll_in_course( $user_membership, $course_id ),
			$user_membership->get_user_id(),
			$course_id,
			$user_membership
		);

		if ( $auto_enroll_course ) {
			$this->enroll_user_in_course( $user_membership->get_user_id(), $course_id );
		}
	}


	/**
	 * Checks whether the user should be auto-enrolled in the given course.
	 *
	 * @since 1.22.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership
	 * @param int $course_id
	 * @return bool
	 */
	protected function should_user_auto_enroll_in_course( \WC_Memberships_User_Membership $user_membership, int $course_id ) : bool {

		$applicable_rule = $this->get_applicable_course_restriction_rule_for_user_membership( $user_membership, $course_id );

		return $applicable_rule
			&& $this->does_membership_plan_rule_auto_enroll_in_course( $applicable_rule )
			&& ! $this->is_user_enrolled_in_course( $user_membership->get_user_id(), $course_id )
			&& $this->has_user_completed_course_prerequisites( $user_membership->get_user_id(), $course_id );
	}


	/**
	 * Determines whether a rule allows auto-enrolling a member in the course.
	 *
	 * @since 1.22.0
	 *
	 * @param \WC_Memberships_Membership_Plan_Rule $rule
	 * @return bool
	 */
	public function does_membership_plan_rule_auto_enroll_in_course( \WC_Memberships_Membership_Plan_Rule $rule ) : bool {

		return wc_string_to_bool( $rule->get_meta( self::COURSE_AUTO_ENROLL_PLAN_RULE_META_KEY, 'yes' ) );
	}


	/**
	 * Attempts to auto-enroll user into dependent courses when a prerequisite course is completed.
	 *
	 * @since 1.22.0
	 *
	 * @param int $user_id
	 * @param int $completed_course_id
	 */
	protected function maybe_start_dependent_courses( int $user_id, int $completed_course_id ) {

		foreach ( array_keys( $this->get_dependent_courses( $completed_course_id ) ) as $course_id ) {

			$rule = $this->get_course_content_restriction_rule_for_user( $user_id, $course_id );

			// skip if the course is not restricted in the first place
			if ( ! $rule || ! wc_memberships_is_post_content_restricted( $course_id )) {
				continue;
			}

			if ( $user_membership = wc_memberships_get_user_membership( $user_id, $rule->get_membership_plan_id() ) ) {
				$this->auto_enroll_course( (int) $course_id, $user_membership );
			}
		}
	}


	/**
	 * Gets the most specific course content restriction rule for the given user membership.
	 *
	 * @TODO: Consider supporting filtering rules by plan ID in WC_Memberships_Rules in the future {IT 2021-04-28}
	 *
	 * @since 1.22.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership
	 * @param int $course_id
	 * @return \WC_Memberships_Membership_Plan_Rule|null
	 */
	protected function get_applicable_course_restriction_rule_for_user_membership( \WC_Memberships_User_Membership $user_membership, int $course_id ) {

		$rules = $this->get_course_content_restriction_rules( $course_id );
		$rules = empty( $rules ) ? [] : array_filter( $rules, static function( \WC_Memberships_Membership_Plan_Rule $rule ) use ( $user_membership ) {
			return $rule->get_membership_plan_id() === $user_membership->get_plan_id();
		} );

		$sorted_rules = $this->sort_rules_by_specificity( $rules );

		// get the first rule that applies only
		return $sorted_rules[0] ?? null;
	}


	/**
	 * Gets the most specific course content restriction rule for the given user based on their active memberships.
	 *
	 * @since 1.22.0
	 *
	 * @param int $user_id the user ID
	 * @param int $course_id the course ID
	 * @return \WC_Memberships_Membership_Plan_Rule|null
	 */
	protected function get_course_content_restriction_rule_for_user( int $course_id, int $user_id ) {

		$rules = $this->get_course_content_restriction_rules( $course_id );
		$rules = array_filter( $rules, static function ( \WC_Memberships_Membership_Plan_Rule $rule ) use ( $user_id ) {
			return wc_memberships_is_user_active_member( $user_id, $rule->get_membership_plan_id() );
		} );

		return $this->sort_rules_by_specificity( $rules )[0];
	}


	/**
	 * Gets all content restriction rules for the given course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $course_id
	 * @return array
	 */
	protected function get_course_content_restriction_rules( int $course_id ) : array {

		return wc_memberships()->get_rules_instance()->get_post_content_restriction_rules( $course_id ) ?: [];
	}


	/**
	 * Sorts content restriction rules by specificity.
	 *
	 * Following is a list of sorting rules, starting with highest priority/specificity:
	 * - Rule content type is `post_type`, applies to a single post
	 * - Rule content type is `post_type`, applies to multiple posts (rules with more posts are less specific)
	 * - Rule content type is `taxonomy`, applies to a single term
	 * - Rule content type is `taxonomy`, applies to multiple terms (rules with more terms are less specific)
	 * - Rule content type is `taxonomy`, applies to the whole taxonomy
	 * - Rule content type is `post_type`, applies to the whole post_type
	 *
	 * @NOTE Consider moving this to the \WC_Memberships_Rules class, in case it may benefit other use-cases {IT 2021-04-30}
	 *
	 * @since 1.22.0
	 *
	 * @param array $rules the rules
	 * @return array
	 */
	private function sort_rules_by_specificity( array $rules ) : array {

		usort( $rules, static function( \WC_Memberships_Membership_Plan_Rule $a, \WC_Memberships_Membership_Plan_Rule $b ) {

			if ( $b->is_content_type( 'post_type' ) ) {

				// rule B is the broadest rule type - always push it down
				if ( ! $b->has_objects() ) {
					return -1;
				}

				// rule B is a post_type rule with object IDs - possibly the most specific rule
				if ( $a->is_content_type( 'post_type' ) ) {

					// the rule with least amount of objects is considered more specific
					return $a->has_objects()
						? count( $a->get_object_ids() ) <=> count( $b->get_object_ids() )
						: 1; // rule A is a broad post_type rule, push rule B up
				}

				// rule B is more specific than any remaining possible combination, push it up
				return 1;
			}

			if ( $b->is_content_type( 'taxonomy' ) ) {

				// if rule A is a post_type rule, we'll push B down if A has objects, vice-versa otherwise.
				if ( $a->is_content_type( 'post_type' ) ) {
					return $a->has_objects() ? -1 : 1;
				}

				if ( $b->has_objects() && $a->has_objects() ) {
					return count( $a->get_object_ids() ) <=> count( $b->get_object_ids() );
				}

				if ( $b->has_objects() && ! $a->has_objects() ) {
					return 1;
				}

				if ( ! $b->has_objects() && $a->has_objects() ) {
					return -1;
				}
			}

			// in all other cases, rules are considered equal
			return 0;
		} );

		return $rules;
	}


	/**
	 * Gets the dependent courses for a prerequisite course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $course_id
	 * @return array associative array of post IDs and course posts
	 */
	abstract protected function get_dependent_courses( int $course_id ) : array;


	/**
	 * Checks if the user is already enrolled in the given course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $user_id
	 * @param int $course_id
	 * @return bool
	 */
	abstract protected function is_user_enrolled_in_course( int $user_id, int $course_id ) : bool;


	/**
	 * Checks if the user has completed all prerequisites for the given course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $user_id
	 * @param int $course_id
	 * @return bool
	 */
	abstract protected function has_user_completed_course_prerequisites( int $user_id, int $course_id ) : bool;


	/**
	 * Enrolls the user in the given course.
	 *
	 * @since 1.22.0
	 *
	 * @param int $user_id
	 * @param int $course_id
	 */
	abstract protected function enroll_user_in_course( int $user_id, int $course_id );


}
