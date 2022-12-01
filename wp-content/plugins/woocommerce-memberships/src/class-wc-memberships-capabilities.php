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

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership Capabilities Handler.
 *
 * This class handles all capability-related functionality.
 * It also provides start access times for member-restricted content.
 *
 * @since 1.0.0
 */
class WC_Memberships_Capabilities {


	/** @var array memoized cache helper for user post access start time results */
	private $user_access_start_time = array();


	/**
	 * Adds new capabilities.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// adjust user capabilities
		add_filter( 'user_has_cap', array( $this, 'user_has_cap' ), 9, 3 );
	}


	/**
	 * Checks if a user has a certain capability.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $all_caps all capabilities
	 * @param array $caps capabilities
	 * @param array $args capability arguments
	 * @return array all capabilities
	 */
	public function user_has_cap( $all_caps, $caps, $args ) {
		global $pagenow, $typenow;

		if ( ! empty( $caps ) ) {
			foreach ( $caps as $cap ) {

				switch ( $cap ) {

					case 'wc_memberships_access_all_restricted_content':

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

					break;

					case 'wc_memberships_view_restricted_post_content' :

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id = (int) $args[1];
						$post_id = (int) $args[2];

						if ( wc_memberships()->get_restrictions_instance()->is_post_public( $post_id ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$rules            = wc_memberships()->get_rules_instance()->get_post_content_restriction_rules( $post_id );
						$all_caps[ $cap ] = $this->user_has_content_access_from_rules( $user_id, $rules, $post_id );

					break;

					case 'wc_memberships_view_restricted_product' :

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id    = (int) $args[1];
						$product_id = (int) $args[2];

						if ( wc_memberships()->get_restrictions_instance()->is_product_public( $product_id ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$rules            = wc_memberships()->get_rules_instance()->get_product_restriction_rules( $product_id );
						$all_caps[ $cap ] = $this->user_has_product_view_access_from_rules( $user_id, $rules, $product_id );

					break;

					case 'wc_memberships_purchase_restricted_product' :

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id = (int) $args[1];
						$post_id = (int) $args[2];

						if ( wc_memberships()->get_restrictions_instance()->is_product_public( $post_id ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$rules            = wc_memberships()->get_rules_instance()->get_product_restriction_rules( $post_id );
						$all_caps[ $cap ] = $this->user_has_product_purchase_access_from_rules( $user_id, $rules );

					break;

					case 'wc_memberships_view_restricted_product_taxonomy_term':

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id          = (int) $args[1];
						$taxonomy         = $args[2];
						$term_id          = (int) $args[3];
						$rules            = wc_memberships()->get_rules_instance()->get_taxonomy_term_product_restriction_rules( $taxonomy, $term_id );
						$all_caps[ $cap ] = $this->user_has_content_access_from_rules( $user_id, $rules, $term_id );

					break;

					case 'wc_memberships_view_delayed_product_taxonomy_term';

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id     = (int) $args[1];
						$taxonomy    = $args[2];
						$term_id     = (int) $args[3];
						$has_access  = false;
						$access_time = $this->get_user_access_start_time_for_taxonomy_term( $user_id, $taxonomy, $term_id );

						if ( $access_time && current_time( 'timestamp', true ) >= $access_time ) {
							$has_access = true;
						}

						$all_caps[ $cap ] = $has_access;

					break;

					case 'wc_memberships_view_restricted_taxonomy_term' :

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id          = (int) $args[1];
						$taxonomy         = $args[2];
						$term_id          = (int) $args[3];
						$rules            = wc_memberships()->get_rules_instance()->get_taxonomy_term_content_restriction_rules( $taxonomy, $term_id );
						$all_caps[ $cap ] = $this->user_has_content_access_from_rules( $user_id, $rules, $term_id );

					break;

					case 'wc_memberships_view_restricted_taxonomy' :

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id          = (int) $args[1];
						$taxonomy         = (int) $args[2];
						$rules            = wc_memberships()->get_rules_instance()->get_taxonomy_content_restriction_rules( $taxonomy );

						$all_caps[ $cap ] = $this->user_has_content_access_from_rules( $user_id, $rules );

					break;

					case 'wc_memberships_view_restricted_post_type' :

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id   = (int) $args[1];
						$post_type = $args[2];

						if ( in_array( $post_type, array( 'product', 'product_variation' ), true ) ) {

							// get all product restriction rules
							$rules = wc_memberships()->get_rules_instance()->get_rules( array(
								'rule_type'         => 'product_restriction',
								'content_type'      => 'post_type',
								'content_type_name' => 'product',
							) );

							$all_caps[ $cap ] = $this->user_has_product_view_access_from_rules( $user_id, $rules );

						} else {

							$rules            = wc_memberships()->get_rules_instance()->get_post_type_content_restriction_rules( $post_type );
							$all_caps[ $cap ] = $this->user_has_content_access_from_rules( $user_id, $rules );
						}

					break;

					case 'wc_memberships_view_delayed_post_type';

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id     = (int) $args[1];
						$post_type   = $args[2];
						$has_access  = false;
						$access_time = $this->get_user_access_start_time_for_post_type( $user_id, $post_type );

						if ( $access_time && current_time( 'timestamp', true ) >= $access_time ) {
							$has_access = true;
						}

						$all_caps[ $cap ] = $has_access;

						break;

					case 'wc_memberships_view_delayed_taxonomy';

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id     = (int) $args[1];
						$taxonomy    = $args[2];
						$has_access  = false;
						$access_time = $this->get_user_access_start_time_for_taxonomy( $user_id, $taxonomy );

						if ( $access_time && current_time( 'timestamp', true ) >= $access_time ) {
							$has_access = true;
						}

						$all_caps[ $cap ] = $has_access;
						break;

					case 'wc_memberships_view_delayed_taxonomy_term';

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id     = (int) $args[1];
						$taxonomy    = $args[2];
						$term        = is_numeric( $args[3] ) ? (int) $args[3] : $args[3];
						$has_access  = false;
						$access_time = $this->get_user_access_start_time_for_taxonomy_term( $user_id, $taxonomy, $term );

						if ( $access_time && current_time( 'timestamp', true ) >= $access_time ) {
							$has_access = true;
						}

						$all_caps[ $cap ] = $has_access;
						break;

					case 'wc_memberships_view_delayed_post_content' :
					case 'wc_memberships_view_delayed_product' :

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id    = (int) $args[1];
						$post_id    = (int) $args[2];
						$has_access = false;

						if ( wc_memberships()->get_restrictions_instance()->is_post_public( $post_id ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$access_time = $this->get_user_access_start_time_for_post( $user_id, $post_id, 'view' );

						if ( $access_time && current_time( 'timestamp', true ) >= $access_time ) {
							$has_access = true;
						}

						$all_caps[ $cap ] = $has_access;

					break;

					case 'wc_memberships_purchase_delayed_product' :

						if ( $this->can_manage_woocommerce( $all_caps ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$user_id    = (int) $args[1];
						$post_id    = (int) $args[2];
						$has_access = false;

						if ( wc_memberships()->get_restrictions_instance()->is_product_public( $post_id ) ) {
							$all_caps[ $cap ] = true;
							break;
						}

						$access_time = $this->get_user_access_start_time_for_post( $user_id, $post_id, 'purchase' );

						if ( $access_time && current_time( 'timestamp', true ) >= $access_time ) {
							$has_access = true;
						}

						$all_caps[ $cap ] = $has_access;

					break;

					// Editing a rule depends on the rule's content type and related capabilities
					case 'wc_memberships_edit_rule' :

						$rule_id  = $args[2];
						$can_edit = false;
						$rule     = wc_memberships()->get_rules_instance()->get_rule( $rule_id );

						if ( $rule ) {

							switch ( $rule->get_content_type() ) {

								case 'post_type':

									$post_type = get_post_type_object( $rule->get_content_type_name() );

									if ( ! $post_type ) {
										/* @see \WP_User::has_cap() handling */
										return array();
									}

									$can_edit = current_user_can( $post_type->cap->edit_posts ) && current_user_can( $post_type->cap->edit_others_posts );

								break;

								case 'taxonomy':

									$taxonomy = get_taxonomy( $rule->get_content_type_name() );

									if ( ! $taxonomy ) {
										/* @see \WP_User::has_cap() handling */
										return array();
									}

									$can_edit = current_user_can( $taxonomy->cap->manage_terms ) && current_user_can( $taxonomy->cap->edit_terms );

								break;
							}
						}

						$all_caps[ $cap ] = $can_edit;

					break;

					case 'wc_memberships_cancel_membership' :
					case 'wc_memberships_renew_membership' :

						$user_id            = (int) $args[1];
						$user_membership_id = (int) $args[2];
						$user_membership    = wc_memberships_get_user_membership( $user_membership_id );

						// complimentary memberships cannot be cancelled or renewed by the user
						$all_caps[ $cap ] = $user_membership && $user_membership->get_user_id() === $user_id && ! $user_membership->has_status( 'complimentary' );

					break;

					// prevent deleting membership plans with active memberships
					case 'delete_published_membership_plan' :
					case 'delete_published_membership_plans' :

						// this workaround (*hack*, *cough*) allows displaying the trash/delete link on membership plans list table even if the plan has active members
						if ( 'edit.php' === $pagenow && 'wc_membership_plan' === $typenow && empty( $_POST ) && is_admin() ) {
							break;
						}

						$plan = wc_memberships_get_membership_plan( $args[2] );

						if ( $plan && $plan->has_active_memberships() ) {
							$all_caps[ $cap ] = false;
						}

					break;

				}
			}
		}

		return $all_caps;
	}


	/**
	 * Check if the passed in caps contain a positive 'manage_woocommerce' capability.
	 *
	 * @since 1.0.0
	 *
	 * @param array $caps
	 * @return bool
	 */
	private function can_manage_woocommerce( $caps ) {
		return isset( $caps['manage_woocommerce'] ) && $caps['manage_woocommerce'];
	}


	/**
	 * Checks if a user has content access from rules.
	 *
	 * @since 1.9.0
	 *
	 * @param int $user_id WP_User ID
	 * @param \WC_Memberships_Membership_Plan_Rule[] $rules array of rules to search access from
	 * @param int $object_id Optional object ID to check access for (defaults to null)
	 * @return bool returns true if there are no rules at all (users always have access)
	 */
	private function user_has_content_access_from_rules( $user_id, array $rules, $object_id = null ) {

		$has_access = true;

		if ( ! empty( $rules ) ) {

			$has_access = false;

			foreach ( $rules as $rule ) {

				// If no object ID is provided, then we are looking at rules that apply to whole post types or taxonomies.
				// In this case, rules that apply to specific objects should be skipped.
				if ( empty( $object_id ) && $rule->has_objects() ) {
					continue;
				}

				if ( wc_memberships_is_user_active_or_delayed_member( $user_id, $rule->get_membership_plan_id() ) ) {
					$has_access = true;
					break;
				}
			}
		}

		return $has_access;
	}


	/**
	 * Checks if a user has product view access from rules
	 *
	 * Returns true if there are no rules
	 *
	 * @since 1.9.0
	 *
	 * @param int $user_id WP_User ID
	 * @param \WC_Memberships_Membership_Plan_Rule[] $rules array of rules to search access from
	 * @param int $object_id optional object ID to check access for (default null, check for rules that apply to whole content types)
	 * @return bool
	 */
	private function user_has_product_view_access_from_rules( $user_id, array $rules, $object_id = null ) {

		$has_access = true;

		if ( ! empty( $rules ) ) {

			// determine if viewing is restricted at all
			foreach ( $rules as $rule ) {

				// If no object ID is provided, then we are looking at rules that apply to whole post types or taxonomies.
				// In this case, rules that apply to specific objects should be skipped.
				if ( ! $object_id && $rule->has_objects() ) {
					continue;
				}

				if ( 'view' === $rule->get_access_type() ) {
					$has_access = false;
					break;
				}
			}

			// determine if a logged in user has access from view or purchase rules
			if ( $user_id && ! $has_access ) {

				foreach ( $rules as $rule ) {

					// If no object ID is provided, then we are looking at rules that apply to whole post types or taxonomies.
					// In this case, rules that apply to specific objects should be skipped.
					if ( ! $object_id && $rule->has_objects() ) {
						continue;
					}

					if (    in_array( $rule->get_access_type(), array( 'view', 'purchase' ), true )
					     && wc_memberships_is_user_active_or_delayed_member( $user_id, $rule->get_membership_plan_id() ) ) {

						$has_access = true;
						break;
					}
				}
			}
		}

		return $has_access;
	}


	/**
	 * Checks if a user has product purchase access from rules.
	 *
	 * @since 1.9.0
	 *
	 * @param int $user_id user ID to check rules for
	 * @param \WC_Memberships_Membership_Plan_Rule[] $rules array of rules to search access from
	 * @return bool returns true if there are no rules at all
	 */
	private function user_has_product_purchase_access_from_rules( $user_id, array $rules ) {

		$has_access = true;

		if ( ! empty( $rules ) ) {

			// determine if purchasing is restricted at all
			foreach ( $rules as $rule ) {
				if ( 'purchase' === $rule->get_access_type() ) {
					$has_access = false;
					break;
				}
			}

			// determine if user has access from view or purchase rules
			if ( ! $has_access ) {
				foreach ( $rules as $rule ) {
					if ( 'purchase' === $rule->get_access_type() && wc_memberships_is_user_active_or_delayed_member( $user_id, $rule->get_membership_plan_id() ) ) {
						$has_access = true;
						break;
					}
				}
			}
		}

		return $has_access;
	}


	/**
	 * Returns the user access date for a post.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the user (member) ID
	 * @param int $post_id the post ID
	 * @param string $access_type optional (applies only to products), defaults to "view"
	 * @return int|null timestamp of start time or null if no access
	 */
	public function get_user_access_start_time_for_post( $user_id, $post_id, $access_type = 'view' ) {

		$post_type = get_post_type( $post_id );

		if ( 'product_variation' === $post_type ) {
			$post_type = 'product';
		}

		$rule_type = 'product' === $post_type ? 'product_restriction' : 'content_restriction';

		return $this->get_user_access_start_time( array(
			'rule_type'         => $rule_type,
			'user_id'           => $user_id,
			'content_type'      => 'post_type',
			'content_type_name' => $post_type,
			'object_id'         => $post_id,
			'access_type'       => $access_type,
		) );
	}


	/**
	 * Returns the user access date for a post type.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the user (member) ID
	 * @param string $post_type the post type
	 * @param string $access_type optional (Applies only to products and variations.), defaults to "view"
	 * @return int|null timestamp of start time or null if no access
	 */
	public function get_user_access_start_time_for_post_type( $user_id, $post_type, $access_type = 'view' ) {

		if ( 'product_variation' === $post_type ) {
			$post_type = 'product';
		}

		$rule_type = 'product' === $post_type ? 'product_restriction' : 'content_restriction';

		return $this->get_user_access_start_time( array(
			'rule_type'         => $rule_type,
			'user_id'           => $user_id,
			'content_type'      => 'post_type',
			'content_type_name' => $post_type,
			'access_type'       => $access_type,
		) );
	}


	/**
	 * Returns the user access date for a taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the user (member) ID
	 * @param string $taxonomy the taxonomy name
	 * @param string $access_type optional (applies only to product taxonomies), defaults to "view"
	 * @return int|null timestamp of start time or null if no access
	 */
	public function get_user_access_start_time_for_taxonomy( $user_id, $taxonomy, $access_type = 'view' ) {
		return $this->get_user_access_start_time( array(
			'user_id'           => $user_id,
			'content_type'      => 'taxonomy',
			'content_type_name' => $taxonomy,
			'access_type'       => $access_type,
		) );
	}


	/**
	 * Returns the user access date for a taxonomy term.
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id the user (member) ID
	 * @param string $taxonomy the taxonomy name
	 * @param string|int $term the term slug or ID
	 * @param string $access_type optional (applies only to product taxonomy terms), defaults to "view"
	 * @return int|null timestamp of start time or null if no access
	 */
	public function get_user_access_start_time_for_taxonomy_term( $user_id, $taxonomy, $term, $access_type = 'view' ) {
		return $this->get_user_access_start_time( array(
			'user_id'           => $user_id,
			'content_type'      => 'taxonomy',
			'content_type_name' => $taxonomy,
			'object_id'         => $term,
			'access_type'       => $access_type,
		) );
	}


	/**
	 * Returns the user access date for a piece of content.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $args optional array of arguments {
	 *   @type string|array $rule_type rule type: 'content_restriction' or 'product_restriction' (purchasing_discount doesn't apply here)
	 *   @type string $content_type content type to check: 'post_type' or 'taxonomy'
	 *   @type string $content_type_name content type name: a valid post type or taxonomy name
	 *   @type string|int $object_id post or term ID or slug
	 *   @type string $access_type for products or product categories: either 'purchase' or 'view' (default)
	 * }
	 * @return int|null timestamp of start time or null if no access
	 */
	public function get_user_access_start_time( $args = array() ) {

		// prepare args
		$args = wp_parse_args( $args, array(
			'rule_type'          => array( 'content_restriction', 'product_restriction' ),
			'user_id'            => get_current_user_id(),
			'content_type'       => null,
			'content_type_name'  => null,
			'object_id'          => null,
			'access_type'        => 'view',
		) );

		if ( is_string( $args['rule_type'] ) ) {
			$args['rule_type'] = (array) $args['rule_type'];
		}

		// use memoization to speed up subsequent checks
		$cache_key = http_build_query( $args );

		if ( ! isset( $this->user_access_start_time[ $cache_key ] ) ) {

			// set defaults, access is immediate
			$access_time = current_time( 'timestamp', true );
			$user_id     = (int) $args['user_id'];
			$access_type = $args['access_type'];

			// get rules args
			$rules_args = $args;
			unset( $rules_args['access_type'], $rules_args['user_id'] );

			$rules = wc_memberships()->get_rules_instance()->get_rules( $rules_args );

			if ( ! empty( $rules ) ) {

				if ( ! in_array( 'product_restriction', $rules_args['rule_type'], true ) ) {

					// if there are no product restriction rules,
					// then we can safely say that access is restricted...
					$access_time = null;

				} else {

					// ...otherwise, we need to check if there are any content restriction rules,
					// or any product restriction rules that restrict the queried access type
					foreach ( $rules as $rule ) {

						if ( 'product_restriction' === $rule->get_rule_type() ) {

							// check if the product restriction rule applies to the correct access type
							if ( $access_type === $rule->get_access_type() ) {
								$access_time = null;
								break;
							}

						} else {

							// content restriction rules indicate that access is restricted
							$access_time = null;
							break;
						}
					}
				}

				// If access is restricted:
				// - determine if user has access
				// - if they have access, determine from when they should have
				if ( ! $access_time ) {

					$last_priority = 0;

					foreach ( $rules as $rule ) {

						// by default any rule applies
						$rule_applies = true;

						// check if rule applies to products, based on the access type
						if ( 'product_restriction' === $rule->get_rule_type() ) {
							if ( 'view' === $access_type ) {
								$rule_applies = in_array( $rule->get_access_type(), array( 'view', 'purchase' ), true );
							} else {
								$rule_applies = $access_type === $rule->get_access_type();
							}
						}

						if ( $rule_applies && ( $user_membership = wc_memberships()->get_user_memberships_instance()->get_user_membership( $user_id, $rule->get_membership_plan_id() ) ) ) {

							// check if a membership is active (and thus in active period) or if it's delayed
							if ( ( $membership_is_delayed = $user_membership->is_delayed() ) || $user_membership->is_active() ) {

								/**
								 * Filter the rule's content 'access from' time for a user membership.
								 *
								 * The 'access from' time is used as the base time for calculating the access start time for scheduled content.
								 *
								 * @since 1.0.0
								 *
								 * @param int $from_time access from time, as a timestamp
								 * @param \WC_Memberships_Membership_Plan_Rule $rule current rule being evaluated
								 * @param \WC_Memberships_User_Membership $user_membership user membership that applies for the rule
								 */
								$from_time = apply_filters( 'wc_memberships_access_from_time', $user_membership->get_start_date( 'timestamp' ), $rule, $user_membership );

								// if there is no time to calculate the access time from, simply use the current time as access start time
								if ( ! is_numeric( $from_time ) ) {
									$access_time = current_time( 'timestamp', true );
									break;
								}

								$membership_inactive_time     = 0;
								$rule_grants_immediate_access = $rule->grants_immediate_access();

								// Unless the membership is delayed (which doesn't deal with inactive periods), update the inactive time (default 0).
								// Also, ignore the inactive time if the rule grants immediate access, as it doesn't matter if the membership was paused or inactive.
								if ( ! $membership_is_delayed && ! $rule_grants_immediate_access ) {
									$membership_inactive_time = $user_membership->get_total_inactive_time();
								}

								// Unless the rule has dripping settings, the start time is immediate.
								// We can match this with the user membership start time or current time.
								if ( $rule_grants_immediate_access ) {
									$rule_access_time = $rule->get_access_start_time( (int) $from_time );
								} else {
									$rule_access_time = $rule->get_access_start_time( (int) $from_time + $membership_inactive_time );
								}

								// Handle rule priorities:
								$rule_priority = $rule->get_priority();
								// - if this rule has higher priority than last rule, override the previous access time
								// - if this has the same priority as the last rule, and grants earlier access, override previous access time
								if (    ( $rule_priority > $last_priority )
								     || ( $rule_priority === $last_priority && ( ! $access_time || $rule_access_time < $access_time ) ) ) {

									$access_time   = $rule_access_time;
									$last_priority = $rule_priority;
								}
							}
						}
					}
				}
			}

			/**
			 * Filter user's access start time to a piece of content.
			 *
			 * @since 1.0.0
			 *
			 * @param int|null $access_time access start timestamp or null if no access should be granted
			 * @param array $args array of arguments {
			 *   @type string $content_type content type: one of 'post_type' or 'taxonomy'
			 *   @type string $content_type_name content type name: a valid post type or taxonomy name
			 *   @type string|int $object_id optional post or taxonomy term ID or slug
			 *   @type string $access_type the access type (for products: view or purchase)
			 * }
			 */
			$access_time = apply_filters( 'wc_memberships_user_object_access_start_time', $access_time, $args );

			$this->user_access_start_time[ $cache_key ] = is_numeric( $access_time ) ? (int) $access_time : null;
		}

		return $this->user_access_start_time[ $cache_key ];
	}


	/**
	 * Checks if user can view a post or a product.
	 *
	 * @since 1.7.1
	 *
	 * @param int $user_id user ID
	 * @param int $post_id WP_Post or WC_Product ID
	 * @return bool
	 */
	private function user_can_view( $user_id, $post_id ) {

		if ( wc_memberships()->get_restrictions_instance()->is_post_public( $post_id ) ) {
			$can_view = true;
		} else {
			if ( 'product' === get_post_type( $post_id ) ) {
				$rules    = wc_memberships()->get_rules_instance()->get_product_restriction_rules( $post_id );
				$can_view = $this->user_has_product_view_access_from_rules( $user_id, $rules, $post_id );
			} else {
				$rules    = wc_memberships()->get_rules_instance()->get_post_content_restriction_rules( $post_id );
				$can_view = $this->user_has_content_access_from_rules( $user_id, $rules, $post_id );
			}
		}

		return $can_view;
	}


	/**
	 * Checks if a user can purchase a product.
	 *
	 * @since 1.7.1
	 *
	 * @param int $user_id user ID
	 * @param int $product_id WC_Product ID
	 * @return bool
	 */
	private function user_can_purchase( $user_id, $product_id )  {

		if ( wc_memberships()->get_restrictions_instance()->is_product_public( $product_id ) ) {
			$can_purchase = true;
		} else {
			$rules        = wc_memberships()->get_rules_instance()->get_product_restriction_rules( $product_id );
			$can_purchase = $this->user_has_product_purchase_access_from_rules( $user_id, $rules );
		}

		return $can_purchase;
	}


	/**
	 * Checks if a post (post type or product) is accessible (viewable or purchasable).
	 *
	 * TODO for now $target only supports 'post' => id or 'product' => id  {FN 2016-04-26}
	 * Having an array can be more future proof compatible if we decide to check for other content types such as taxonomies, terms, etc.
	 *
	 * @since 1.4.0
	 *
	 * @param int $user_id the user ID to check for access
	 * @param string|array $action type of action(s): 'view', 'purchase' (products only)
	 * @param array $content associative array of content type and content id to access to
	 * @param int|string $access_time UTC timestamp to compare for content access (optional, defaults to now time)
	 * @return bool
	 */
	public function user_can( $user_id, $action, $content, $access_time = '' ) {

		if ( $user_id > 0 && user_can( $user_id, 'manage_woocommerce' ) ) {
			// do not bother further for shop managers
			return true;
		} elseif ( ! $user_id > 0 || ! wc_memberships_is_user_active_member( $user_id ) ) {
			// sanity check: bail out early if we are checking capabilities for an invalid user
			return false;
		} elseif ( empty( $access_time ) ) {
			// default value for start access time is now
			$access_time = current_time( 'timestamp', true );
		}

		$user_can   = false;
		$content_id = reset( $content );
		$actions    = is_array( $action ) ? $action : null;

		if ( $actions ) {

			$conditions = array();

			foreach ( $actions as $capability ) {

				if ( 'view' === $capability ) {
					$user_can = $this->user_can_view( $user_id, $content_id );
				} elseif ( 'purchase' === $capability ) {
					$user_can = $this->user_can_purchase( $user_id, $content_id );
				}

				$conditions[] = $user_can && $access_time >= $this->get_user_access_start_time_for_post( $user_id, $content_id, $capability );
			}

			$user_can = in_array( true, $conditions, true );

		} else {

			if ( 'view' === $action ) {
				$user_can = $this->user_can_view( $user_id, $content_id );
			} elseif ( 'purchase' === $action ) {
				$user_can = $this->user_can_purchase( $user_id, $content_id );
			}

			$user_start_time = $this->get_user_access_start_time_for_post( $user_id, $content_id, $action );
			$user_can        = null === $user_start_time || ! $user_can ? false : $access_time >= $user_start_time;
		}

		return $user_can;
	}


}
