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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Membership Plans handler.
 *
 * This class handles general membership plans related functionality.
 *
 * @since 1.0.0
 */
class WC_Memberships_Membership_Plans {


	/** @var array helper for lazy membership plans getter */
	private $membership_plans = array();


	/**
	 * Plans handler constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		require_once( wc_memberships()->get_plugin_path() . '/includes/class-wc-memberships-membership-plan.php' );

		// delete related data upon plan deletion
		add_action( 'delete_post', array( $this, 'delete_related_data' ) );

		// trigger free memberships access upon user registration event
		add_action( 'user_register', array( $this, 'grant_access_to_free_membership' ), 10, 2 );

		// trigger memberships access upon products purchases
		add_action( 'woocommerce_order_status_completed',  [ $this, 'grant_access_to_membership_from_order' ], 9 );
		add_action( 'woocommerce_order_status_processing', [ $this, 'grant_access_to_membership_from_order' ], 9 );
	}


	/**
	 * Returns a single membership plan.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|\WP_Post|null $post optional, post object, post slug, or post id of the membership plan
	 * @param \WC_Memberships_User_Membership|int|null optional, user membership object or id, used in filter
	 * @return \WC_Memberships_Membership_Plan|false
	 */
	public function get_membership_plan( $post = null, $user_membership = null ) {

		if ( empty( $post ) && isset( $GLOBALS['post'] ) ) {

			$post = $GLOBALS['post'];

		} elseif ( is_numeric( $post ) ) {

			$post = get_post( $post );

		} elseif ( $post instanceof \WC_Memberships_Membership_Plan ) {

			$post = get_post( $post->get_id() );

		} elseif ( is_string( $post ) ) {

			$posts = get_posts( array(
				'name'           => $post,
				'post_type'      => 'wc_membership_plan',
				'posts_per_page' => 1,
			) );

			if ( ! empty( $posts ) ) {
				$post = $posts[0];
			}

		} elseif ( ! ( $post instanceof \WP_Post ) ) {

			$post = null;
		}

		// if no acceptable post is found, bail out
		if ( ! $post || 'wc_membership_plan' !== get_post_type( $post ) ) {
			return false;
		}

		if ( is_numeric( $user_membership ) ) {
			$user_membership = wc_memberships_get_user_membership( $user_membership );
		}

		$membership_plan = new \WC_Memberships_Membership_Plan( $post );

		/**
		 * Filter a membership plan before returning it.
		 *
		 * This filter is important as it's also used internally to extend a Membership Plan when used with Subscriptions.
		 *
		 * @since 1.7.0
		 *
		 * @param \WC_Memberships_Membership_Plan $membership_plan the membership plan
		 * @param \WP_Post $membership_plan_post the membership plan post object
		 * @param \WC_Memberships_User_Membership|null $user_membership optional, when calling this filter from a user membership
		 */
		return apply_filters( 'wc_memberships_membership_plan', $membership_plan, $post, $user_membership );
	}


	/**
	 * Return all membership plans.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args optional array of arguments, to pass to `get_posts()`
	 * @return \WC_Memberships_Membership_Plan[] $plans array of membership plans
	 */
	public function get_membership_plans( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'posts_per_page' => -1,
		) );

		$args['post_type'] = 'wc_membership_plan';

		// unique key for caching the applied rule results
		$cache_key = http_build_query( $args );

		if ( ! isset( $this->membership_plans[ $cache_key ] ) ) {

			$membership_plan_posts = get_posts( $args );

			$this->membership_plans[ $cache_key ] = array();

			if ( ! empty( $membership_plan_posts ) ) {

				foreach ( $membership_plan_posts as $post ) {

					$plan = $this->get_membership_plan( $post );

					if ( $plan ) {
						$this->membership_plans[ $cache_key ][ $plan->get_id() ] = $plan;
					}
				}
			}
		}

		return $this->membership_plans[ $cache_key ];
	}


	/**
	 * Returns membership plans accessed upon user registration.
	 *
	 * @since 1.7.0
	 *
	 * @param array $args optional array of arguments, to pass to `get_posts()`
	 * @return \WC_Memberships_Membership_Plan[]
	 */
	public function get_free_membership_plans( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'meta_query' => array(
				array(
					'key'   => '_access_method',
					'value' => 'signup',
				),
			),
		) );

		return $this->get_membership_plans( $args );
	}


	/**
	 * Gets membership plans that a given product can grant access to.
	 *
	 * @since 1.19.0
	 *
	 * @param \WC_Product $product the product
	 * @param array $args optional array of arguments
	 * @return \WC_Memberships_Membership_Plan[]|\WC_Memberships_Integration_Subscriptions_Membership_Plan[] array of membership plan objects indexed by their IDs
	 */
	public function get_membership_plans_for_product( \WC_Product $product, array $args = [] ) {

		$product_id       = $product->get_id();
		$membership_plans = [];

		if ( $product_id > 0 ) {

			// It would be easier if we could pass query args like `'fields' => 'ids'` and run a `meta_query` here to look into the plan's `_product_ids`.
			// Unfortunately `meta_query` doesn't work when the meta value is stored as a serialized array and we want to search that array providing a possible value of it.
			// Using `'compare' => 'LIKE'` would be error prone, so let's just query all plans and discard those who don't apply; the query will be cached anyway.
			foreach ( wc_memberships_get_membership_plans( $args ) as $membership_plan ) {

				if ( $membership_plan->has_product( $product_id ) ) {

					$membership_plans[ $membership_plan->get_id() ] = $membership_plan;
				}
			}
		}

		/**
		 * Filters membership plans matched to a product that grants access access.
		 *
		 * @since 1.19.0
		 *
		 * @param \WC_Memberships_Membership_Plan|\WC_Memberships_Integration_Subscriptions_Membership_Plan $membership_plans associative array of membership plan IDs and objects
		 * @param \WC_Product $product product object
		 */
		return (array) apply_filters( 'wc_memberships_get_membership_plans_for_product', $membership_plans, $product );
	}


	/**
	 * Returns the count of the existing membership plans.
	 *
	 * By default will only return the count of published plans.
	 * If you need to count also drafts and plans with other statuses, you need to pass an appropriate `post_status` argument.
	 *
	 * @since 1.9.0
	 *
	 * @param array $args optional array of arguments as in `get_posts()`
	 * @return int
	 */
	public function get_membership_plans_count( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'nopaging'       => true,
		) );

		// there's no point to gather objects in this context
		$args['fields'] = 'ids';

		return count( $this->get_membership_plans( $args ) );
	}


	/**
	 * Returns membership plans' possible access methods.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $with_labels whether to return labels along with access method keys
	 * @return array indexed or associative array
	 */
	public function get_membership_plans_access_methods( $with_labels = false ) {

		$access_methods = array(
			/* translators: A User Membership is manually created */
			'manual-only' => __( 'Manual assignment only', 'woocommerce-memberships' ),
			/* translators: A User Membership is created when a user registers an account */
			'signup'      => __( 'User account registration', 'woocommerce-memberships' ),
			/* translators: A User Membership is created when a customer purchases a product that grants access */
			'purchase'    => __( 'Product(s) purchase', 'woocommerce-memberships' ),
		);

		return true !== $with_labels ? array_keys( $access_methods ) : $access_methods;
	}


	/**
	 * Returns membership plans' possible access length types.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $with_labels whether to return labels along with access length keys
	 * @return array indexed or associative array
	 */
	public function get_membership_plans_access_length_types( $with_labels = false ) {

		$access_length_types = array(
			/* translators: Membership of an unlimited length */
			'unlimited' => __( 'Unlimited', 'woocommerce-memberships' ),
			/* translators: Specify the length of a membership */
			'specific'  => __( 'Specific length', 'woocommerce-memberships' ),
			/* translators: Membership set to expire in a specified date */
			'fixed'     => __( 'Fixed dates', 'woocommerce-memberships' )
		);

		return true !== $with_labels ? array_keys( $access_length_types ) : $access_length_types;
	}


	/**
	 * Returns membership plans' possible access length periods.
	 *
	 * @since 1.7.0
	 *
	 * @param bool $with_labels whether to return labels along with access length keys
	 * @return array indexed or associative array
	 */
	public function get_membership_plans_access_length_periods( $with_labels = false ) {

		$access_length_periods = array(
			'days'   => __( 'Day(s)', 'woocommerce-memberships' ),
			'weeks'  => __( 'Week(s)', 'woocommerce-memberships' ),
			'months' => __( 'Month(s)', 'woocommerce-memberships' ),
			'years'  => __( 'Year(s)', 'woocommerce-memberships' ),
		);

		/**
		 * Filter plan access length periods.
		 *
		 * Note: acceptable keys should be time values recognizable by `strtotime()`
		 *
		 * @since 1.6.1
		 *
		 * @param array $access_length_periods associative array of keys and labels
		 */
		$access_length_periods = apply_filters( 'wc_memberships_plan_access_period_options', $access_length_periods );

		return true !== $with_labels ? array_keys( $access_length_periods ) : $access_length_periods;
	}


	/**
	 * Returns membership plans that are available to use for user memberships assignments.
	 *
	 * Skips trashed items, includes plans with all other statuses, not just published active ones.
	 *
	 * @since 1.9.0
	 *
	 * @param string $values either return plan 'objects' (default, associative array of IDs and plan objects), 'ids' (array of integers) or 'labels' (associative array values)
	 * @return array associative array of keys and labels
	 */
	public function get_available_membership_plans( $values = 'objects' ) {

		$available_plans  = array();
		$membership_plans = $this->get_membership_plans( array(
			'post_status' => array( 'publish', 'private', 'future', 'draft', 'pending' )
		) );

		if ( ! empty( $membership_plans ) ) {

			foreach ( $membership_plans as $membership_plan ) {

				if ( 'labels' === $values ) {

					$membership_plan_name = $membership_plan->get_formatted_name();

					if ( 'publish' !== $membership_plan->post->post_status ) {
						/* translators: Placeholder: Membership plan name for a membership that is inactive */
						$membership_plan_name = sprintf( __( '%s (inactive)', 'woocommerce-memberships' ), $membership_plan_name );
					}

					$available_plans[ $membership_plan->get_id() ] = $membership_plan_name;

				} elseif ( 'objects' === $values ) {

					$available_plans[ $membership_plan->get_id() ] = $membership_plan;

				} elseif ( 'ids' === $values ) {

					$available_plans[]                             = $membership_plan->get_id();
				}
			}
		}

		return $available_plans;
	}


	/**
	 * Deletes any related data if membership plan is deleted.
	 *
	 * For sanity, also deletes any related user memberships and plan rules.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id deleted post ID
	 */
	public function delete_related_data( $post_id ) {
		global $wpdb;

		if ( 'wc_membership_plan' === get_post_type( $post_id ) ) {

			// find related membership IDs
			$user_memberships = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_parent = %d", $post_id ) );

			// delete each membership plan
			if ( ! empty( $user_memberships ) ) {
				foreach ( $user_memberships as $user_membership_id ) {
					wp_delete_post( $user_membership_id, true );
				}
			}

			// find related rules and delete them
			$plan_rules   = wc_memberships()->get_rules_instance()->get_rules();
			$delete_rules = array();

			foreach ( $plan_rules as $rule ) {
				if ( $rule->get_membership_plan_id() === (int) $post_id ) {
					$delete_rules[] = $rule->get_id();
				}
			}

			wc_memberships()->get_rules_instance()->delete_rules( $delete_rules );

			// clear caches
			$this->membership_plans = array();
		}
	}


	/**
	 * Grants access to free membership plans to users that just signed up for an account.
	 *
	 * @since 1.7.0
	 *
	 * @param int|\WP_User $user_id newly registered WP_User id
	 * @param bool $renew whether to renew a membership if the user is already a member, default true
	 * @param int|\WC_Memberships_Membership_Plan|null|false $plan optional plan to grant access to, will otherwise run through all free plans
	 * @return void|null|\WC_Memberships_User_Membership the newly created membership or null if none created or fail
	 */
	public function grant_access_to_free_membership( $user_id, $renew = true, $plan = null ) {

		$user_id         = $user_id instanceof \WP_User ? $user_id->ID : $user_id;
		$user_membership = null;

		// no need to run this for admins and users that can access everything anyway
		if ( ! user_can( $user_id, 'wc_memberships_access_all_restricted_content' ) ) {

			if ( null !== $plan ) {

				if ( is_numeric( $plan ) ) {
					$plan = $this->get_membership_plan( (int) $plan );
				}

				if ( $plan instanceof \WC_Memberships_Membership_Plan ) {
					$free_membership_plans = array( $plan );
				}

			} else {

				$free_membership_plans = $this->get_free_membership_plans();
			}

			if ( ! empty( $free_membership_plans ) ) {

				foreach ( $free_membership_plans as $membership_plan ) {

					// sanity check
					if ( $membership_plan->is_access_method( 'signup' ) ) {

						$action = wc_memberships_is_user_member( $user_id, $membership_plan->get_id(), false ) ? 'renew' : 'create';

						if ( ! $renew && 'renew' === $action ) {
							continue;
						}

						// used in filter and `wc_memberships_create_user_membership()`
						$access_args = array(
							'user_id' => (int) $user_id,
							'plan_id' => $membership_plan->get_id(),
						);

						/**
						 * Confirm grant access to a free membership.
						 *
						 * @since 1.7.0
						 *
						 * @param bool $grant_access true by default
						 * @param array $args {
						 *      @type int $user_id user ID being granted access
						 *      @type int $plan_id ID of the free plan accessing to
						 * }
						 */
						$grant_access = (bool) apply_filters( 'wc_memberships_grant_access_to_free_membership', true, $access_args );

						// assign a membership to this user
						if ( $grant_access ) {

							try {

								$user_membership = wc_memberships_create_user_membership( $access_args, $action );

								/**
								 * Fires after a user has been granted membership access after signing up for a new account
								 *
								 * @since 1.19.0
								 *
								 * @param \WC_Memberships_Membership_Plan $membership_plan the plan that user was granted access to
								 * @param array $args {
								 *     @type int $user_id newly registered user ID
								 *     @type int $user_membership_id the ID of the new user membership
								 * }
								 */
								do_action( 'wc_memberships_grant_free_membership_access_from_sign_up', $membership_plan, [
									'user_id'            => $user_id,
									'user_membership_id' => $user_membership->get_id(),
								] );

							} catch ( Framework\SV_WC_Plugin_Exception $e ) {

								$user_membership = null;
							}
						}
					}
				}
			}
		}

		// when used as hook callback, doesn't need to return anything
		if ( 'user_register' === current_action() ) {
			return;
		}

		return $user_membership;
	}


	/**
	 * Grants customer access to membership when making a purchase.
	 *
	 * Note: this method runs also when an order is manually added in WC admin.
	 *
	 * @since 1.7.0
	 *
	 * @param int|\WC_Order $order WC_Order id or object
	 */
	public function grant_access_to_membership_from_order( $order ) {

		$order = is_numeric( $order ) ? wc_get_order( (int) $order ) : $order;

		if ( ! $order instanceof \WC_Order ) {
			return;
		}

		$order_items      = $order->get_items();
		$user_id          = $order->get_user_id();
		$membership_plans = $this->get_membership_plans();

		// skip if guest user, no order items or no membership plans to begin with
		if ( ! $user_id || empty( $order_items ) || empty( $membership_plans ) ) {
			return;
		}

		// loop over all available membership plans
		foreach ( $membership_plans as $plan ) {

			// skip if no products grant access to this plan
			if ( ! $plan->has_products() ) {
				continue;
			}

			$access_granting_product_ids = wc_memberships_get_order_access_granting_product_ids( $plan, $order, $order_items );

			if ( ! empty( $access_granting_product_ids ) ) {

				// We check if the order has granted access already before looping products,
				// so we can allow the purchase of multiple access granting products to extend the duration of a plan,
				// should multiple products grant access to the same plan having a specific end date (relative to now).
				/** @see wc_memberships_cumulative_granting_access_orders_allowed() */
				/** @var \WC_Memberships_Membership_Plan::grant_access_from_purchase() */
				$order_granted_access_already = wc_memberships_has_order_granted_access( $order, array( 'membership_plan' => $plan ) );

				foreach ( $access_granting_product_ids as $product_id ) {

					// sanity check: make sure the selected product ID in fact does grant access
					if ( ! $plan->has_product( $product_id ) ) {
						continue;
					}

					/**
					 * Confirm grant access from new purchase to paid plan.
					 *
					 * @since 1.3.5
					 *
					 * @param bool $grant_access by default true unless the order already granted access to the plan
					 * @param array $args {
					 *      @type int $user_id customer id for purchase order
					 *      @type int $product_id ID of product that grants access
					 *      @type int $order_id order ID containing the product
					 * }
					 */
					$grant_access = (bool) apply_filters( 'wc_memberships_grant_access_from_new_purchase', ! $order_granted_access_already, array(
						'user_id'    => (int) $user_id,
						'product_id' => (int) $product_id,
						'order_id'   => (int) $order->get_id(),
					) );

					if ( $grant_access ) {
						// delegate granting access to the membership plan instance
						$plan->grant_access_from_purchase( $user_id, $product_id, (int) $order->get_id() );
					}
				}
			}
		}
	}


}
