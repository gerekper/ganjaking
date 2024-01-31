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
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;


/**
 * Restricts content to specified membership plans.
 *
 * @since 1.0.0
 *
 * @param string $content
 * @param string|int|array $membership_plans optional: the membership plan or plans to check against - accepts a plan slug, id, or an array of slugs or IDs (default: all plans)
 * @param string|null $delay default none (empty)
 * @param bool $exclude_trial default false
 */
function wc_memberships_restrict( $content, $membership_plans = null, $delay = null, $exclude_trial = false ) {

	$has_access   = false;
	$member_since = null;
	$access_time  = null;

	// grant access to super users
	if ( current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {
		$has_access = true;
	}

	// convert to an array in all cases
	$membership_plans = (array) $membership_plans;

	// default to use all plans if no plan is specified
	if ( empty( $membership_plans ) ) {
		$membership_plans = wc_memberships_get_membership_plans();
	}

	foreach ( $membership_plans as $plan_id_or_slug ) {

		$membership_plan = wc_memberships_get_membership_plan( $plan_id_or_slug );

		if ( $membership_plan && wc_memberships_is_user_active_member( get_current_user_id(), $membership_plan->get_id() ) ) {

			$has_access = true;

			if ( ! $delay && ! $exclude_trial ) {
				break;
			}

			// determine the earliest membership for the user
			if ( $user_membership = wc_memberships()->get_user_memberships_instance()->get_user_membership( get_current_user_id(), $membership_plan->get_id() ) ) {

				// create a pseudo-rule to help applying filters
				$rule = new \WC_Memberships_Membership_Plan_Rule( array(
					'access_schedule_exclude_trial' => $exclude_trial ? 'yes' : 'no'
				) );

				/** This filter is documented in src/class-wc-memberships-capabilities.php **/
				$from_time = apply_filters( 'wc_memberships_access_from_time', $user_membership->get_start_date( 'timestamp' ), $rule, $user_membership );

				// if there is no time to calculate the access time from,
				// simply use the current time as access start time
				if ( ! $from_time ) {
					$from_time = current_time( 'timestamp', true );
				}

				if ( null === $member_since || $from_time < $member_since ) {
					$member_since = $from_time;
				}
			}
		}
	}

	// add delay
	if ( $has_access && ( $delay || $exclude_trial ) && $member_since ) {

		$access_time = $member_since;

		// determine access time
		if ( strpos( $delay, 'month' ) !== false ) {

			$parts  = explode( ' ', $delay );
			$amount = isset( $parts[1] ) ? (int) $parts[0] : '';

			$access_time = wc_memberships_add_months_to_timestamp( $member_since, $amount );

		} elseif ( $delay ) {

			$access_time = strtotime( $delay, $member_since );

		}

		// output or show delayed access message
		if ( $access_time <= current_time( 'timestamp', true ) ) {
			echo $content;
		} else {
			echo \WC_Memberships_User_Messages::get_message_html( 'content_delayed', array( 'access_time' => $access_time ) );
		}

	} elseif ( $has_access ) {

		echo $content;
	}
}


/**
 * Checks if a post/page content is restricted.
 *
 * @since 1.0.0
 *
 * @param int|\WP_Post|null $post_id optional, defaults to current post
 * @return bool
 */
function wc_memberships_is_post_content_restricted( $post_id = null ) {
	global $post;

	$post_type = null;

	if ( ! $post_id && $post && isset( $post->ID ) ) {
		$post_id   = $post->ID;
		$post_type = $post->post_type;
	} elseif ( $post_id instanceof \WP_Post ) {
		$post_type = $post_id->post_type;
		$post_id   = $post_id->ID;
	}

	$rules = is_numeric( $post_id ) && (int) $post_id > 0 ? wc_memberships()->get_rules_instance()->get_post_content_restriction_rules( $post_id ) : '';

	return ! empty( $rules ) && ! wc_memberships()->get_restrictions_instance()->is_post_public( $post_id, $post_type );
}


/**
 * Checks if a taxonomy term is restricted.
 *
 * Note: does not check if any of its ancestors are restricted.
 *
 * @since 1.11.1
 *
 * @param null|int|\WP_Term $term_id term ID
 * @param null|string $taxonomy taxonomy name (unused when checking directly a WP_Term object)
 * @return bool
 */
function wc_memberships_is_term_restricted( $term_id = null, $taxonomy = null ) {
	global $wp_query;

	$restricted = false;

	if ( null === $term_id && null === $taxonomy && ( $wp_query->is_tax() || $wp_query->is_category() || $wp_query->is_tag() ) ) {

		$term = get_queried_object();

		if ( $term instanceof \WP_Term ) {
			$taxonomy = $term->taxonomy;
			$term_id  = $term->term_id;
		}

	} elseif ( $term_id instanceof \WP_Term ) {

		$taxonomy = $term_id->taxonomy;
		$term_id  = $term_id->term_id;
	}

	if ( (int) $term_id > 0 && is_string( $taxonomy ) ) {

		if ( 'product_cat' === $taxonomy ) {

			$rules = wc_memberships()->get_rules_instance()->get_taxonomy_term_product_restriction_rules( $taxonomy, $term_id );

			// filter out rules that are just for purchase restriction
			foreach ( $rules as $index => $rule ) {
				if ( $rule->is_access_type( 'purchase' ) ) {
					unset( $rules[ $index ] );
				}
			}

		} elseif ( '' !== $taxonomy ) {

			$rules = wc_memberships()->get_rules_instance()->get_taxonomy_term_content_restriction_rules( $taxonomy, $term_id );
		}

		$restricted = ! empty( $rules );
	}

	return $restricted;
}


/**
 * Checks if a product category term is restricted from viewing.
 *
 * @since 1.11.1
 *
 * @param int|\WP_Term $category term ID or object
 * @return bool
 */
function wc_memberships_is_product_category_viewing_restricted( $category ) {

	return wc_memberships_is_term_restricted( $category, 'product_cat' );
}


/**
 * Checks if viewing a product is restricted.
 *
 * @since 1.0.0
 *
 * @param int|\WC_Product|\WP_Post|null $post_id optional, defaults to current post
 * @return bool
 */
function wc_memberships_is_product_viewing_restricted( $post_id = null ) {
	global $post;

	if ( ! $post_id && $post && isset( $post->ID ) ) {
		$post_id = $post->ID;
	} elseif ( $post_id instanceof \WP_Post ) {
		$post_id = $post_id->ID;
	} elseif ( $post_id instanceof \WC_Product ) {
		$post_id = $post_id->get_id();
	}

	$rules         = is_numeric( $post_id ) && (int) $post_id > 0 ? wc_memberships()->get_rules_instance()->get_product_restriction_rules( $post_id ) : null;
	$is_restricted = false;

	if ( ! empty( $rules ) ) {
		foreach ( $rules as $rule ) {
			if ( 'view' === $rule->get_access_type() ) {
				$is_restricted = true;
			}
		}
	}

	return $is_restricted && ! wc_memberships()->get_restrictions_instance()->is_product_public( $post_id );
}


/**
 * Checks if purchasing a product is restricted.
 *
 * @since 1.0.0
 *
 * @param int|\WC_Product|\WP_Post|null $post_id optional, defaults to current post
 * @return bool
 */
function wc_memberships_is_product_purchasing_restricted( $post_id = null ) {
	global $post;

	if ( ! $post_id && $post && isset( $post->ID ) ) {
		$post_id   = $post->ID;
		$post_type = get_post_type( $post );
	} elseif ( $post_id instanceof \WP_Post ) {
		$post_id   = $post_id->ID;
		$post_type = get_post_type( $post_id );
	} elseif ( $post_id instanceof \WC_Product ) {
		$post_id   = $post_id->get_id();
		$post_type = 'product';
	} elseif ( is_numeric( $post_id ) ) {
		$post_type = get_post_type( $post_id );
	} else {
		$post_type = '';
	}

	if ( ! $post_id || 'product' !== $post_type ) {

		$is_restricted = false;

	} else {

		$rules         = wc_memberships()->get_rules_instance()->get_product_restriction_rules( $post_id );
		$is_restricted = false;

		if ( ! empty( $rules ) ) {
			foreach ( $rules as $rule ) {
				if ( 'purchase' === $rule->get_access_type() ) {
					$is_restricted = true;
				}
			}
		}
	}

	return $is_restricted && ! wc_memberships()->get_restrictions_instance()->is_product_public( $post_id );
}


/**
 * Returns the user access start timestamp for a content or product.
 *
 * It will returns the time in local time (according to site timezone).
 *
 * TODO for now $target only supports 'post' => id or 'product' => id  {FN 2016-04-26}
 *
 * @since 1.4.0
 *
 * @param int $user_id user to get access time for
 * @param array $content associative array of content type and content id to access to
 * @param string $action type of access, 'view' or 'purchase' (products only)
 * @param bool $gmt whether to return a UTC timestamp (default false, uses site timezone)
 * @return int|null timestamp of start access time
 */
function wc_memberships_get_user_access_start_time( $user_id, $action, $content, $gmt = false ) {

	$access_time = wc_memberships()->get_capabilities_instance()->get_user_access_start_time_for_post( $user_id, reset( $content ), $action );

	if ( null !== $access_time ) {
		return ! $gmt ? wc_memberships_adjust_date_by_timezone( $access_time, 'timestamp' ) : $access_time;
	}

	return null;
}
