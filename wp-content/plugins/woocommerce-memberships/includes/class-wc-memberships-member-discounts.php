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
 * Member Discounts class.
 *
 * This class handles all purchasing discounts for members.
 *
 * @since 1.3.0
 */
class WC_Memberships_Member_Discounts {


	/** @var bool whether Memberships discounts are being evaluated for the current user */
	private $applying_discounts = false;

	/** @var array memoized associative array storing bool flag whether a user or guest has discount for a given product */
	private $user_has_product_discount = [];

	/** @var string transient key to store cached IDs of products excluded from member discounts */
	private $products_excluded_from_discounts_transient_key = 'wc_memberships_products_excluded_from_discounts';

	/** @var array memoization for product discounts exclusion. */
	private $product_excluded_from_discounts = array();

	/** @var bool whether products on sale are excluded from discounts. */
	private $exclude_on_sale_products = false;

	/** @var array memoization for product on sale before discount. */
	private $product_is_on_sale_before_discount = array();

	/** @var bool whether discounts should be applied in the current thread */
	private $should_apply_discounts = false;

	/** @val null|bool whether discounts should be applied when an access granting product is in cart */
	private $should_apply_discounts_when_purchasing_membership;

	/** @var bool whether the sale badge is being displayed. */
	private $displaying_sale_badge = false;

	/** @var null|int the discount rounding precision */
	private $rounding_precision;


	/**
	 * Sets up member discounts: Welcome to the jungle.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		// bail out if in an admin ajax context
		if ( ! $this->is_wp_admin_ajax() ) {

			/** \WC_Cart_Session::get_cart_from_session() runs on wp_loaded with priority 10, so run ::init() after to make sure the cart's content is available. */
			add_action( 'wp_loaded', [ $this, 'init' ], 15 );
		}
	}


	/**
	 * Init member discounts.
	 *
	 * We follow here a pattern common in many price-affecting extensions, due to
	 * the need to produce a "price before/after discount" type of HTML output,
	 * so shop customers can easily understand the deal they're being offered.
	 *
	 * To do so we need to juggle WooCommerce prices, we start off by instantiating
	 * this class with our discounts active, so we can be sure to always pass those
	 * to other extensions if a user eligible for discounts. In Memberships,
	 * we filter sale prices and pass member discounts as apparent sale prices.
	 * So WooCommerce core can trigger the HTML output sought by Memberships, which
	 * shows a before/after price change.
	 *
	 * Extensions and third party code that need to know if Memberships price modifiers
	 * are being applied or not in these two phases, can use doing_action and hook into
	 * 'wc_memberships_discounts_enable_price_adjustments' and
	 * 'wc_memberships_discounts_disable_price_adjustments', or call directly the
	 * callback methods found in this class, which we use to add and remove
	 * price modifiers.
	 *
	 * @internal
	 *
	 * @since 1.7.1
	 */
	public function init() {

		$this->should_apply_discounts   = $this->is_user_eligible();
		$this->exclude_on_sale_products = 'yes' === get_option( 'wc_memberships_exclude_on_sale_products_from_member_discounts', 'no' );

		// refreshes the mini cart upon member login
		add_action( 'wp_login', array( $this, 'refresh_cart_upon_member_login' ), 10, 2 );

		// class methods are available on both frontend and backend, but hooks below should run in frontend only
		if ( ! ( is_admin() && ! is_ajax() ) ) {

			// initialize discount actions that will be called in this class methods
			add_action( 'wc_memberships_discounts_enable_price_adjustments',       array( $this, 'enable_price_adjustments' ) );
			add_action( 'wc_memberships_discounts_disable_price_adjustments',      array( $this, 'disable_price_adjustments' ) );
			add_action( 'wc_memberships_discounts_enable_price_html_adjustments',  array( $this, 'enable_price_html_adjustments' ) );
			add_action( 'wc_memberships_discounts_disable_price_html_adjustments', array( $this, 'disable_price_html_adjustments' ) );

			if ( $this->should_apply_discounts ) {

				$this->applying_discounts = true;

				// activate discounts, if applicable to the current user
				do_action( 'wc_memberships_discounts_enable_price_adjustments' );
				do_action( 'wc_memberships_discounts_enable_price_html_adjustments' );

				// force calculations in cart
				add_filter( 'woocommerce_update_cart_action_cart_updated', '__return_true' );

				// handle "On Sale" badges and "Member Discount" badges
				add_action( 'woocommerce_before_template_part',                  array( $this, 'before_sale_badge_template' ), 999, 1 );
				add_action( 'woocommerce_after_template_part',                   array( $this, 'after_sale_badge_template'  ), 999, 1 );
				// output the "Member Discount" badge HTML output in product page and shop loop
				add_action( 'wc_memberships_product_member_discount_badge',      'wc_memberships_show_product_member_discount_badge' );
				add_action( 'wc_memberships_product_member_loop_discount_badge', 'wc_memberships_show_product_loop_member_discount_badge' );
				// disable the "Member Discount" badge for excluded products
				add_filter( 'wc_memberships_member_discount_badge',              array( $this, 'disable_discount_badge_for_excluded_products' ), 10, 3 );
				add_filter( 'woocommerce_get_item_data', [ $this, 'display_cart_purchasing_discount_message' ], 10, 2 );
			}
		}
	}


	/**
	 * Determines if we are in an admin context where member discounts should not run.
	 *
	 * @since 1.7.3
	 *
	 * @return bool
	 */
	private function is_wp_admin_ajax() {

		// check if any of the enhanced search product actions are being done.
		if ( is_admin() ) {

			/* @see WC_AJAX::add_ajax_events() */
			$search_products = array(
				'json_search_products',
				'json_search_products_and_variations',
				'json_search_grouped_products',
				'json_search_downloadable_products_and_variations',
			);

			foreach ( $search_products as $ajax_event ) {
				if ( did_action( 'wp_ajax_woocommerce_' . $ajax_event ) ) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Determines whether discounts should be applied if a membership product is in cart.
	 *
	 * @since 1.17.0
	 *
	 * @return bool
	 */
	private function should_apply_member_discounts_from_membership_product_in_cart() {

		if ( null === $this->should_apply_discounts_when_purchasing_membership ) {
			$this->should_apply_discounts_when_purchasing_membership = 'yes' === get_option( 'wc_memberships_apply_member_discounts_when_purchasing_membership', 'no' );
		}

		return $this->should_apply_discounts_when_purchasing_membership;
	}


	/**
	 * Determines whether cumulative member discounts should be allowed.
	 *
	 * Memberships standard behavior is to offer the best discounts to members of multiple plans that target the same products with purchasing discount rules (stacking).
	 *
	 * @since 1.17.0
	 *
	 * @param int $user_id user ID (user in filter callback)
	 * @param \WC_Product $product product object (used in filter callback)
	 * @return bool default true
	 */
	private function should_apply_cumulative_member_discounts( $user_id, $product ) {

		/**
		 * Filters whether to allow stacking product discounts.
		 *
		 * This is for users who are members of multiple plans with overlapping discount rules for the same products.
		 *
		 * @since 1.7.0
		 *
		 * @param bool $allow_cumulative_discounts default true (allow)
		 * @param int $user_id the user id discounts are calculated for
		 * @param \WC_Product $product the product object being discounted
		 */
		return (bool) apply_filters( 'wc_memberships_allow_cumulative_member_discounts', true, $user_id, $product );
	}


	/**
	 * Returns the flag whether the discounts are being evaluated for the current user.
	 *
	 * @since 1.8.3
	 *
	 * @return bool
	 */
	public function applying_discounts() {
		return $this->applying_discounts;
	}


	/**
	 * Determines whether a user is eligible for member discounts.
	 *
	 * @since 1.17.0
	 *
	 * @param null|int $user_id a valid \WP_User ID or null to get the current user ID (0 for guests)
	 * @return bool
	 */
	private function is_user_eligible( $user_id = null ) {

		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		$is_eligible = $user_id > 0 && wc_memberships_is_user_member( $user_id );

		if ( ! $is_eligible && is_int( $user_id ) && $this->should_apply_member_discounts_from_membership_product_in_cart() ) {

			// user is eligible if there are membership products that grant purchasing discounts in the cart
			$is_eligible = ! empty( $this->get_membership_plans_from_access_granting_products_in_cart() );

			// user is eligible if a membership product that grants purchasing discounts is about to be added to the cart
			$is_eligible = $is_eligible ?: $this->is_add_to_cart_request_for_membership_product();
		}

		/**
		 * Filters whether the user should be eligible for member discounts.
		 *
		 * @since 1.17.0
		 *
		 * @param bool $is_eligible if eligible, any member discounts will be calculated for the user
		 * @param int $user_id the user unique identifier (0 for guests)
		 */
		return (bool) apply_filters( 'wc_memberships_is_user_eligible_for_member_discounts', $is_eligible, $user_id );
	}


	/**
	 * Gets membership plans the user would have access to based on cart contents.
	 *
	 * Note: if opening this method to public, consider moving it to the plans handler or another handler that is more generic.
	 *
	 * @since 1.17.0
	 *
	 * @param array $args {
	 *     optional array of args
	 *
	 *     @type \WC_Memberships_Membership_Plan[] $plans array of plans to search from (uses all membership plans by default)
	 *     @type string $index_key field to use as key in the results, either product_id or plan_id (default)
	 * }
	 * @return \WC_Memberships_Membership_Plan[] array of found plans that user could have access to from cart purchase
	 */
	private function get_membership_plans_from_access_granting_products_in_cart( $args = [] ) {

		$args = wp_parse_args( $args, [
			'plans'     => null,
			'index_key' => 'plan_id'
		] );

		$found = [];
		$cart  = wc()->cart ? wc()->cart->get_cart() : [];
		$plans = is_array( $args['plans'] ) ? $args['plans'] : wc_memberships_get_membership_plans();

		if ( ! empty( $plans ) && ! empty( $cart ) ) {

			foreach ( $plans as $membership_plan ) {

				foreach ( $cart as $cart_item ) {

					$product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;

					if ( $product instanceof \WC_Product && $this->product_grants_access_to_membership_plan( $product, $membership_plan ) ) {

						// no need to check other products, save the found plan and move on to the next
						$index_key           = 'product_id' === $args['index_key'] ? $product->get_id() : $membership_plan->get_id();
						$found[ $index_key ] = $membership_plan;
						break;
					}
				}
			}
		}

		return $found;
	}


	/**
	 * Determines whether a product grants access to the given membership plan when purchased.
	 *
	 * Note: if need to open method to public, consider moving to a more appropriate handler.
	 *
	 * @since 1.17.0
	 *
	 * @param \WC_Product $product the product
	 * @param \WC_Memberships_Membership_Plan $membership_plan the membership plan
	 * @return bool
	 */
	private function product_grants_access_to_membership_plan( \WC_Product $product, \WC_Memberships_Membership_Plan $membership_plan ) {

		$grants_access = $membership_plan->has_product( $product->get_id() );

		if ( ! $grants_access && $product->is_type( 'variation' ) ) {
			$grants_access = $membership_plan->has_product( $product->get_parent_id() );
		}

		return $grants_access;
	}


	/**
	 * Determines whether the current request is an Add to Cart request for a product that grants access to a membership plan.
	 *
	 * @since 1.17.0
	 *
	 * @return bool
	 */
	private function is_add_to_cart_request_for_membership_product() {

		$is_adding_membership_product = false;

		if ( is_numeric( Framework\SV_WC_Helper::get_requested_value( 'add-to-cart' ) ) ) {

			$product_id = (int) Framework\SV_WC_Helper::get_requested_value( 'add-to-cart' );

		} elseif ( 'add_to_cart' === Framework\SV_WC_Helper::get_requested_value( 'wc-ajax' ) && is_numeric( Framework\SV_WC_Helper::get_posted_value( 'product_id' ) ) ) {

			$product_id = (int) Framework\SV_WC_Helper::get_posted_value( 'product_id' );

		}

		$product = isset( $product_id ) ? wc_get_product( $product_id ) : null;

		if ( $product instanceof \WC_Product ) {

			$plans = wc_memberships_get_membership_plans();

			if ( ! empty( $plans ) ) {

				foreach ( $plans as $plan ) {

					if ( $this->product_grants_access_to_membership_plan( $product, $plan ) ) {

						$is_adding_membership_product = true;
						break;
					}
				}
			}
		}

		return $is_adding_membership_product;
	}


	/**
	 * Checks if a user (or a guest) has membership discounts for a product.
	 *
	 * @since 1.6.4
	 *
	 * @param int|\WC_Product|\WC_Product_Variable|null $the_product optional, a product id or object to check if it has member discounts (if not set, looks for a current product)
	 * @param int|\WP_User|null $the_user optional, the user to check if has discounts for the product (defaults to current user, or guest)
	 * @return bool
	 */
	public function user_has_member_discount( $the_product = null, $the_user = null ) {
		global $product;

		$has_discount = $user_id = false;

		// get the user ID
		if ( null === $the_user ) {
			$user_id = get_current_user_id();
		} elseif ( is_numeric( $the_user ) ) {
			$user_id = (int) $the_user;
		} elseif ( isset( $the_user->ID ) ) {
			$user_id = (int) $the_user->ID;
		}

		// bail out if user ID is not an integer
		if ( false === $user_id ) {
			return $has_discount;
		}

		// get the product
		if ( is_numeric( $the_product ) ) {

			if ( isset( $this->user_has_product_discount[ $user_id ][ $the_product ] ) ) {

				// bail early if we are passing a product ID and the memoized entry was already set before
				return $this->user_has_product_discount[ $user_id ][ $the_product ];
			}

			$the_product = wc_get_product( (int) $the_product );

		} elseif ( null === $the_product && $product instanceof \WC_Product ) {

			$the_product = $product;
		}

		// bail out if no product
		if ( ! $the_product instanceof \WC_Product ) {
			return $has_discount;
		}

		$product_id = $the_product->get_id();

		// use memoized entry if found, or store a new one
		if ( isset( $this->user_has_product_discount[ $user_id ][ $product_id ] ) ) {

			$has_discount = $this->user_has_product_discount[ $user_id ][ $product_id ];

		} else {

			$has_discount = $this->user_has_product_purchasing_discount_from_rules( $user_id, $product_id );

			// if a variable product, before return false check for its variations
			if ( ! $has_discount && ( $the_product->is_type( 'variable' ) || $the_product->has_child() ) ) {

				foreach ( $the_product->get_children() as $product_child_id ) {

					// sanity check as some extensions might return a product object instead of its ID
					if ( $product_child_id instanceof \WC_Product ) {
						$product_child_id = $product_child_id->get_id();
					}

					if ( ! is_numeric( $product_child_id ) ) {
						continue;
					}

					$has_discount = $this->user_has_product_purchasing_discount_from_rules( $user_id, $product_child_id );

					$this->user_has_product_discount[ $user_id ][ $product_child_id ] = $has_discount;

					if ( $has_discount || $product_id === $product_child_id ) {
						// if one of the child variations has a discount, it's legit, to say that the parent variable product has member discounts
						$this->user_has_product_discount[ $user_id ][ $product_id ] = $has_discount;
						break;
					}
				}

			} else {

				$this->user_has_product_discount[ $user_id ][ $product_id ] = $has_discount;
			}
		}

		return $has_discount;
	}


	/**
	 * Gets product purchasing discount rules for a given user, or a guest.
	 *
	 * @since 1.17.0
	 *
	 * @param int $user_id the ID of the user (0 for guests) to get applicable discount rules for (for guests, it may depend on cart items if discounts from membership items in cart are enabled)
	 * @param int $product_id the ID of the related product to check discounts for
	 * @return \WC_Memberships_Membership_Plan_Rule[] array of membership plan purchasing discount rules
	 */
	private function get_user_product_purchasing_discount_rules( $user_id, $product_id ) {

		if ( ! is_numeric( $user_id ) || ! is_numeric( $product_id ) ) {
			return [];
		}

		$all_discount_rules = wc_memberships()->get_rules_instance()->get_product_purchasing_discount_rules( $product_id );
		$applicable_rules   = $apply_discount_rule = [];

		foreach ( $all_discount_rules as $discount_rule ) {

			if ( ! $discount_rule->is_active() ) {
				continue;
			}

			$plan_id = $discount_rule->get_membership_plan_id();

			if ( ! array_key_exists( $plan_id, $apply_discount_rule ) ) {

				$apply_discount_rule[ $plan_id ] = wc_memberships_is_user_active_member( $user_id, $plan_id );

				if ( ! $apply_discount_rule[ $plan_id ] && $this->should_apply_member_discounts_from_membership_product_in_cart() ) {

					// query product only once: it's the same product
					if ( ! isset( $product ) ) {
						$product = wc_get_product( $product_id );
					}

					if ( $product instanceof \WC_Product ) {

						// query plans only once while in the loop
						if ( ! isset( $cart_plans ) ) {
							$cart_plans = $this->get_membership_plans_from_access_granting_products_in_cart();
						}

						if ( isset( $cart_plans[ $plan_id ] ) ) {

							// if the product in question gives access to the current rule's plan, do not bother
							if ( $cart_plans[ $plan_id ]->has_product( $product->get_id() ) ) {

								$apply_discount_rule[ $plan_id ] = false;

							// same if it is the parent product that grants access to the plan
							} elseif ( $product->is_type( 'variation' ) && $cart_plans[ $plan_id ]->has_product( $product->get_parent_id() ) ) {

								$apply_discount_rule[ $plan_id ] = false;

							// at least one of the products in the cart grant access to the rule's plan and that product is not the current product
							} else {

								$apply_discount_rule[ $plan_id ] = true;
							}

						} else {

							// none of the products in the cart grant access to the rule's plan
							$apply_discount_rule[ $plan_id ] = false;
						}
					}
				}
			}

			if ( ! empty( $apply_discount_rule[ $plan_id ] ) ) {
				$applicable_rules[ $discount_rule->get_id() ] = $discount_rule;
			}
		}

		return $applicable_rules;
	}


	/**
	 * Determines if user has member discounts for a specific product.
	 *
	 * @since 1.9.0
	 *
	 * @param int $user_id WP_User ID
	 * @param int $product_id WC_Product ID
	 * @return bool
	 */
	private function user_has_product_purchasing_discount_from_rules( $user_id, $product_id ) {

		return ! empty( $this->get_user_product_purchasing_discount_rules( $user_id, $product_id ) );
	}


	/**
	 * Checks whether products on sale should be excluded from discount rules.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function excluding_on_sale_products_from_member_discounts() {
		return $this->exclude_on_sale_products;
	}


	/**
	 * Gets a list of products marked to be excluded from member discounts from any plan rule.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $use_cache optional, whether to look or skip cached values in transient
	 * @return int[] product IDs
	 */
	public function get_products_excluded_from_member_discounts( $use_cache = true ) {
		global $wpdb;

		if ( true === $use_cache ) {

			$products = get_transient( $this->products_excluded_from_discounts_transient_key );

			// transient has expired, must query posts again and update cache
			if ( ! is_array( $products ) ) {
				$products = $this->get_products_excluded_from_member_discounts( false );
			}

		} else {

			$products = array_map( 'absint', $wpdb->get_col( "
				SELECT p.ID FROM $wpdb->posts p
				LEFT JOIN $wpdb->postmeta pm
				ON p.ID = pm.post_id
				WHERE p.post_type = 'product'
				AND pm.meta_key = '_wc_memberships_exclude_discounts'
				AND pm.meta_value = 'yes'
			" ) );

			if ( ! empty( $products ) ) {

				// account for variations of variable products
				$parents  = '(' . implode( ',', $products ) . ')';
				$products = array_merge( $products, array_map( 'absint', $wpdb->get_col( "
					SELECT ID FROM $wpdb->posts
					WHERE post_parent IN $parents
				" ) ) );

				$this->update_excluded_member_discounts_products_cache( array_unique( $products ) );
			}
		}

		return $products;
	}


	/**
	 * Sets a product's member discounts exclusion status (helper method).
	 *
	 * @since 1.12.0
	 *
	 * @param array|int|\WP_Product|\WP_Post $product product's object, post, ID - or array of either
	 * @param string $exclusion either 'yes' or 'no'
	 * @return int number of processed products (0 for failure)
	 */
	private function set_product_discounts_exclusion( $product, $exclusion ) {

		$success = 0;

		if ( in_array( $exclusion, array( 'yes', 'no' ), true ) ) {

			$items = is_array( $product ) ? $product : array( $product );

			foreach ( $items as $item ) {
				if ( wc_memberships_set_content_meta( $item, '_wc_memberships_exclude_discounts', $exclusion ) ) {
					$success++;
				}
			}
		}

		if ( $success > 0 ) {
			$this->update_excluded_member_discounts_products_cache();
		}

		return $success;
	}


	/**
	 * Marks a product as excluded from member discounts, when applicable.
	 *
	 * @since 1.12.0
	 *
	 * @param array|\WC_Product|\WP_Post|int $product product's ID, post or object - or array of either
	 * @return int number of processed products (0 for failure)
	 */
	public function set_product_excluded_from_member_discounts( $product ) {

		return $this->set_product_discounts_exclusion( $product, 'yes' );
	}


	/**
	 * Marks a product as affected by member discounts, if applicable.
	 *
	 * @since 1.12.0
	 *
	 * @param array|\WC_Product|\WP_Post|int $product product ID or object, or array of
	 * @return int number of processed products (0 for failure)
	 */
	public function unset_product_excluded_from_member_discounts( $product ) {

		return $this->set_product_discounts_exclusion( $product, 'no' );
	}


	/**
	 * Updates the discount-excluded products cache.
	 *
	 * @since 1.12.0
	 *
	 * @param array associative array of product IDs and excluded status (boolean)
	 * @return bool
	 */
	public function update_excluded_member_discounts_products_cache( $product_ids = array() ) {

		/**
		 * Adjusts the expiration time for excluded discounts products cache.
		 *
		 * @since 1.12.0
		 *
		 * @param int $expiration time in seconds (default uses WEEK_IN_SECONDS constant)
		 */
		$expiration = absint( apply_filters( 'wc_memberships_excluded_member_discounts_products_cache_expiration', WEEK_IN_SECONDS ) );

		if ( $expiration > 0 ) {
			$success = set_transient( $this->products_excluded_from_discounts_transient_key, ! empty( $product_ids ) ? $product_ids : $this->get_products_excluded_from_member_discounts( false ), max( MINUTE_IN_SECONDS, $expiration ) );
		} else {
			$success = $this->delete_excluded_member_discounts_products_cache();
		}

		// flush object cache
		$this->product_excluded_from_discounts = array();

		return $success;
	}


	/**
	 * Deletes the discount-excluded products cache.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	public function delete_excluded_member_discounts_products_cache() {

		// flush object cache
		$this->product_excluded_from_discounts = array();

		return delete_transient( $this->products_excluded_from_discounts_transient_key );
	}


	/**
	 * Checks if a product is to be excluded from discount rules.
	 *
	 * Note: even if not excluded, discount rules may or may not still apply.
	 *
	 * @since 1.7.0
	 *
	 * @param int|\WP_Post|\WC_Product $product product object or ID
	 * @return bool
	 */
	public function is_product_excluded_from_member_discounts( $product ) {

		$excluded = false;

		if ( $product instanceof \WP_Post ) {
			$product_id = $product->ID;
		} elseif ( $product instanceof \WC_Product ) {
			$product_id = $product->get_id();
		} else {
			$product_id = $product;
		}

		if ( is_numeric( $product_id ) && $product_id > 0 ) {

			if ( ! isset( $this->product_excluded_from_discounts[ $product_id ] ) ) {

				// exclude if product-level setting is enabled to exclude this product
				$exclude_product = in_array( $product_id, $this->get_products_excluded_from_member_discounts(), false );
				// exclude if on sale and global-level setting is enabled to exclude all products on sale
				$exclude_on_sale = ! $exclude_product ? $this->excluding_on_sale_products_from_member_discounts() && $this->product_is_on_sale_before_discount( $product ) : false;

				$this->product_excluded_from_discounts[ $product_id ] = $exclude_product || $exclude_on_sale;
			}

			/**
			 * Filters a product from having discount rules applied.
			 *
			 * @since 1.7.0
			 *
			 * @param bool $excluded whether the product is excluded from discount rules
			 * @param int|\WC_Product|\WP_Post $product the product object, ID or related post object
			 */
			$excluded = (bool) apply_filters( 'wc_memberships_exclude_product_from_member_discounts', $this->product_excluded_from_discounts[ $product_id ], $product );
		}

		return $excluded;
	}


	/**
	 * Filter the product sale status.
	 *
	 * @since 1.6.2
	 *
	 * @param bool $on_sale whether the product is on sale
	 * @param \WC_Product|\WC_Product_Variable $product the product object
	 * @return bool
	 */
	public function product_is_on_sale( $on_sale, $product ) {

		// Bail out if any of the following is true:
		// - discounts shouldn't be applied
		// - product is excluded from member discounts
		// - user has no member discount over the product
		if (      $this->should_apply_discounts
		     && ! $this->is_product_excluded_from_member_discounts( $product )
		     && ! $this->member_prices_display_sale_price()
		     &&   $this->user_has_member_discount( $product ) ) {

			if ( ! $this->displaying_sale_badge() ) {

				$product_id = $product->get_id();

				// maybe store the original sale status before a member discount tweaks the value
				if ( isset( $this->product_is_on_sale_before_discount[ $product_id ] ) ) {
					$on_sale = $this->product_is_on_sale_before_discount[ $product_id ];
				} else {
					$on_sale = $this->get_product_unfiltered_sale_status( $product );
					$this->product_is_on_sale_before_discount[ $product_id ] = $on_sale;
				}

			} else {

				$on_sale = false;
			}
		}

		return $on_sale;
	}


	/**
	 * Sets an internal flag when displaying the sale badge template.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param string $template_name the current template name
	 */
	public function before_sale_badge_template( $template_name ) {

		if ( in_array( $template_name, array( 'single-product/sale-flash.php', 'loop/sale-flash.php' ), true ) ) {

			$this->displaying_sale_badge = true;
		}
	}


	/**
	 * Sets a flag after the sale badge template has been displayed.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param string $template_name the current template name
	 */
	public function after_sale_badge_template( $template_name ) {
		global $post;

		if ( $post && in_array( $template_name, array( 'single-product/sale-flash.php', 'loop/sale-flash.php' ), true ) ) {

			$this->displaying_sale_badge = false;

			if (    ! $this->is_product_excluded_from_member_discounts( $post->ID )
			     &&   $this->user_has_member_discount( $post->ID, get_current_user_id() ) ) {

				if ( 'single-product/sale-flash.php' === $template_name ) {

					/**
					 * Upon displaying a member discount badge for an individual product page.
					 *
					 * @since 1.7.4
					 */
					do_action( 'wc_memberships_product_member_discount_badge' );

				} elseif ( 'loop/sale-flash.php' === $template_name ) {

					/**
					 * Upon displaying a member discount badge for a product in a loop context.
					 *
					 * @since 1.9.0
					 */
					do_action( 'wc_memberships_product_member_loop_discount_badge' );
				}
			}
		}
	}


	/**
	 * Returns the flag whether the sale badge is being displayed.
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	private function displaying_sale_badge() {
		return $this->displaying_sale_badge;
	}


	/**
	 * Adjusts the optional display price suffix for variable products (to account for discounts at variation level).
	 *
	 * Caveat: not always accurate since the variation with the minimum price incl/excl tax might be different than the one stored in the variation prices array.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param string $price_display_suffix the display price suffix string
	 * @param \WC_Product|\WC_Product_Variable $product the product object
	 * @return string The price suffix.
	 */
	public function get_variable_price_html_suffix( $price_display_suffix, $product ) {

		$price_display_suffix_raw = $price_display_suffix_raw_test = get_option( 'woocommerce_price_display_suffix' );
		$price_suffix_merge_tags  = [
			'{price_including_tax}',
			'{price_excluding_tax}',
		];

		if ( str_replace( $price_suffix_merge_tags, [ '', '' ], $price_display_suffix_raw_test ) !== $price_display_suffix_raw ) {

			$variation_prices = $product->get_variation_prices();
			$variation_prices = array_keys( $variation_prices['price'] );

			if ( ! empty( $variation_prices ) && ( $min_variation = wc_get_product( current( $variation_prices ) ) ) ) {

				$replace = [
					wc_price( wc_get_price_including_tax( $min_variation ) ),
					wc_price( wc_get_price_excluding_tax( $min_variation ) ),
				];

				$price_suffix         = str_replace( $price_suffix_merge_tags, $replace, $price_display_suffix_raw );
				$price_display_suffix = ' <small class="woocommerce-price-suffix">' . $price_suffix . '</small>';
			}
		}

		return $price_display_suffix;
	}


	/**
	 * Adjusts the discounted product price HTML.
	 *
	 * @internal
	 *
	 * @since 1.3.0
	 *
	 * @param string $html the price HTML maybe after discount
	 * @param \WC_Product|\WC_Product_Variable|\WC_Product_Variation $product the product object for which we may have discounts
	 * @return string the original price HTML if no discount or a new formatted string showing before/after discount
	 */
	public function get_member_price_html( $html, $product ) {

		/**
		 * Controls whether or not member prices should use discount format when displayed.
		 *
		 * @since 1.3.0
		 *
		 * @param bool $use_discount_format Defaults to true.
		 */
		$use_discount_format = (bool) apply_filters( 'wc_memberships_member_prices_use_discount_format', true );

		// Bail out if any of the following conditions applies:
		// - custom code set to not to use discount format
		// - discounts shouldn't be applied
		// - product is excluded from discount rules
		// - current user has no discounts for the product
		// - product has no applicable member discount
		if (      $use_discount_format
		     &&   $this->should_apply_discounts
		     && ! $this->is_product_excluded_from_member_discounts( $product )
		     &&   $this->user_has_member_discount( $product ) ) {

			// prune caches for variable products
			if ( $product->is_type( 'variable' ) ) {
				\WC_Cache_Helper::get_transient_version( 'product', true );
			}

			// get string price BEFORE discount
			$html_before_discount = $this->get_price_html_before_discount( $product, $html );
			// get string price AFTER discount
			$html_after_discount  = $this->get_price_html_after_discount( $product, $html );

			// add a "Member Discount" badge for single variation prices
			if ( $product->is_type( 'variation' ) ) {
				$html_after_discount .= ' ' . $this->get_member_discount_badge( $product, true );
			}

			/**
			 * Filters the HTML price after member discounts have been applied.
			 *
			 * @since 1.7.2
			 *
			 * @param string $html the price HTML output
			 * @param \WC_Product $product the product the discounted price is meant for
			 * @param string $html_before_discount original HTML before discounts
			 * @param string $html_after_discount original HTML after discounts
			 */
			$html = (string) apply_filters( 'wc_memberships_get_discounted_price_html', $html_after_discount, $product, $html_before_discount, $html_after_discount );
		}

		/**
		 * Filters the HTML price after member discounts may have been applied.
		 *
		 * @since 1.7.1
		 *
		 * @param string $html the price HTML
		 * @param \WC_Product $product the product the price is meant for
		 */
		return apply_filters( 'wc_memberships_get_price_html', $html, $product );
	}


	/**
	 * Returns the product HTML price after discount.
	 *
	 * @since 1.7.4
	 *
	 * @param \WC_Product|\WC_Product_Variable|\WC_Product_Variation $product the product
	 * @param string $price_html the original price after discount
	 * @return string HTML
	 */
	private function get_price_html_after_discount( $product, $price_html = '' ) {

		// temporarily disable membership HTML price adjustments.
		do_action( 'wc_memberships_discounts_disable_price_html_adjustments' );

		if ( $product->is_type( 'variable' ) ) {

			add_filter( 'woocommerce_get_price_suffix', array( $this, 'get_variable_price_html_suffix' ), 999, 2 );

			// variable products: prune transient cache
			\WC_Cache_Helper::get_transient_version( 'product', true );

			$html_after_discount = $product->get_price_html();

			remove_filter( 'woocommerce_get_price_suffix', array( $this, 'get_variable_price_html_suffix' ), 999 );

		} else {

			$html_after_discount = $product->get_price_html();
		}

		// re-enable membership HTML price adjustments
		do_action( 'wc_memberships_discounts_enable_price_html_adjustments' );

		/**
		 * Filters the price after a member discount was applied.
		 *
		 * @since 1.8.0
		 *
		 * @param string $html_before_discount the price before member discount
		 * @param \WC_Product $product the product
		 * @param string $original_price_html the original price
		 */
		return apply_filters( 'wc_memberships_get_price_html_after_discount', $html_after_discount, $product, $price_html );
	}


	/**
	 * Returns the product HTML price before discount.
	 *
	 * @since 1.7.0
	 *
	 * @param \WC_Product|\WC_Product_Variable|\WC_Product_Variation $product the product
	 * @param string $price_html the original price before discount
	 * @return string HTML
	 */
	private function get_price_html_before_discount( $product, $price_html = '' ) {

		// temporarily disable membership price adjustments
		do_action( 'wc_memberships_discounts_disable_price_adjustments' );
		do_action( 'wc_memberships_discounts_disable_price_html_adjustments' );

		// variable products: prune caches
		if ( $product->is_type( 'variable' ) ) {
			\WC_Cache_Helper::get_transient_version( 'product', true );
		}

		$html_before_discount = $product ? $product->get_price_html() : '';

		// re-enable membership price adjustments
		do_action( 'wc_memberships_discounts_enable_price_adjustments' );
		do_action( 'wc_memberships_discounts_enable_price_html_adjustments' );

		/**
		 * Filters the price before member discount.
		 *
		 * @since 1.8.0
		 *
		 * @param string $html_before_discount the price before member discount
		 * @param \WC_Product $product the product
		 * @param string $original_price_html the original price
		 */
		return apply_filters( 'wc_memberships_get_price_html_before_discount', $html_before_discount, $product, $price_html );
	}


	/**
	 * Returns the unfiltered sale status.
	 *
	 * @since 1.7.4
	 *
	 * @param \WC_Product $product the product object
	 * @return bool
	 */
	private function get_product_unfiltered_sale_status( $product ) {

		// temporarily disable membership price adjustments
		do_action( 'wc_memberships_discounts_disable_price_adjustments' );
		do_action( 'wc_memberships_discounts_disable_price_html_adjustments' );

		$on_sale = $product->is_on_sale();

		// re-enable membership price adjustments
		do_action( 'wc_memberships_discounts_enable_price_adjustments' );
		do_action( 'wc_memberships_discounts_enable_price_html_adjustments' );

		return $on_sale;
	}


	/**
	 * Checks whether to show sale prices as regular when displaying discounts to members.
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	private function member_prices_display_sale_price() {

		/**
		 * Controls whether or not member prices should display sale prices as well.
		 *
		 * @since 1.3.0
		 *
		 * @param bool $display_sale_price defaults to false
		 */
		return (bool) apply_filters( 'wc_memberships_member_prices_display_sale_price', false );
	}


	/**
	 * Determines if a product was marked on sale before membership price adjustments.
	 *
	 * @since 1.7.0
	 *
	 * @param int|\WC_Product|\WC_Product_Variable $product the product object or ID
	 * @return bool
	 */
	public function product_is_on_sale_before_discount( $product ) {

		$on_sale_before = false;

		if ( is_numeric( $product ) && isset( $this->product_is_on_sale_before_discount[ $product ] ) ) {

			// bail early if we are passing a product ID and the memoized entry was already set
			$on_sale_before = $this->product_is_on_sale_before_discount[ $product ];

		} else {

			$product = ! $product instanceof \WC_Product ? wc_get_product( $product ) : $product;

			if ( $product instanceof \WC_Product ) {

				$product_id = $product->get_id();

				if ( ! array_key_exists( $product_id, $this->product_is_on_sale_before_discount ) ) {

					// handles both new WC 3.0+ and older filters
					$excluded_filters = array(
						'woocommerce_product_is_on_sale',
						'woocommerce_product_get_sale_price',
						'woocommerce_product_variation_get_sale_price',
						'woocommerce_product_get_price',
						'woocommerce_product_variation_get_price',
						'woocommerce_product_get_regular_price',
						'woocommerce_get_variation_prices_hash',
						'woocommerce_get_sale_price',
						'woocommerce_get_variation_sale_price',
						'woocommerce_product_variation_get_regular_price',
						'woocommerce_variation_prices_sale_price',
						'woocommerce_variation_prices_price',
						'woocommerce_variation_prices_regular_price',
						'woocommerce_subscriptions_product_sale_price',
					);

					// Bail out if any of the following conditions applies:
					// - discounts shouldn't be applied
					// - current user has no discounts for the product
					// - one of the above filters is being passed, which could lead to infinite loops
					if ( ( $this->should_apply_discounts && $this->user_has_member_discount( $product ) ) || in_array( current_filter(), $excluded_filters, true ) ) {
						$this->product_is_on_sale_before_discount[ $product_id ] = $this->get_product_unfiltered_sale_status( $product );
					} else {
						$this->product_is_on_sale_before_discount[ $product_id ] = $product->is_on_sale();
					}
				}

				$on_sale_before = $this->product_is_on_sale_before_discount[ $product_id ];
			}
		}

		return $on_sale_before;
	}


	/**
	 * Applies purchasing discounts to a product price.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string|int|float $price price to discount (normally a float, maybe a string number)
	 * @param \WC_Product $product the product object
	 * @return float price
	 */
	public function get_member_price( $price, $product ) {

		// Bail out if any of the following is true:
		// - discounts shouldn't be applied
		// - product is excluded from member discounts
		// - user has no member discount over the product
		if (      $this->should_apply_discounts
		     && ! $this->is_product_excluded_from_member_discounts( $product )
		     &&   $this->user_has_member_discount( $product ) ) {

			// account also for variation sale price filter
			if ( in_array( current_filter(), [ 'woocommerce_product_get_sale_price', 'woocommerce_product_variation_get_sale_price' ], false ) ) {
				$member_price = $product->get_price();
			} else {
				$member_price = $this->get_discounted_price( $price, $product, get_current_user_id() );
			}

			$price = is_numeric( $member_price ) ? $member_price : $price;
		}

		return $price;
	}


	/**
	 * Applies purchasing discounts to a product variation price.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param string|int|float $price price to discount (normally a float, maybe a string number)
	 * @param \WC_Product_Variation $variation the variation object
	 * @param \WC_Product $product the product object
	 * @return float price
	 */
	public function get_member_variation_price( $price, $variation, $product ) {

		// Bail out if any of the following is true:
		// - discounts shouldn't be applied
		// - product is excluded from member discounts
		// - user has no member discount over the product
		if (      $this->should_apply_discounts
		     && ! $this->is_product_excluded_from_member_discounts( $variation )
		     &&   $this->user_has_member_discount( $variation ) ) {

			if ( 'woocommerce_variation_prices_sale_price' === current_filter() ) {
				$member_price = apply_filters( 'woocommerce_variation_prices_price', $variation->get_price( 'edit' ), $variation, $product );
			} else {
				$member_price = $this->get_discounted_price( $price, $variation, get_current_user_id() );
			}

			$price = is_numeric( $member_price ) ? $member_price : $price;
		}

		return $price;
	}


	/**
	 * Replaces regular prices with sale before discounts.
	 *
	 * Runs when calculating price HTML strings and sale prices must be shown to users as reference.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param string|int|float $regular_price regular price used as reference
	 * @param \WC_Product $product the product object
	 * @return float price
	 */
	public function get_member_regular_price( $regular_price, $product ) {

		// Bail out if any of the following is true:
		// - discounts shouldn't be applied
		// - product is excluded from member discounts
		// - user has no member discount over the product
		if (      $this->should_apply_discounts
		     && ! $this->is_product_excluded_from_member_discounts( $product )
		     &&   $this->user_has_member_discount( $product )
		     &&   $this->member_prices_display_sale_price() ) {

			// temporarily disable membership price adjustments
			do_action( 'wc_memberships_discounts_disable_price_adjustments' );

			if ( $product->is_on_sale() ) {
				$regular_price = $product->get_sale_price();
			}

			// re-enable membership price adjustments
			do_action( 'wc_memberships_discounts_enable_price_adjustments' );
		}

		return $regular_price;
	}


	/**
	 * Replaces regular prices with sale before discounts.
	 *
	 * Runs when calculating price HTML strings and sale prices must be shown to users as reference.
	 *
	 * @internal
	 *
	 * @since 1.8.0
	 *
	 * @param string|int|float $regular_price regular price used as reference
	 * @param \WC_Product_Variation $variation the variation object
	 * @param \WC_Product $product the product object
	 * @return float price
	 */
	public function get_member_variation_regular_price( $regular_price, $variation, $product ) {

		// Bail out if any of the following is true:
		// - discounts shouldn't be applied
		// - product is excluded from member discounts
		// - user has no member discount over the product
		if (      $this->should_apply_discounts
		     && ! $this->is_product_excluded_from_member_discounts( $product )
		     &&   $this->user_has_member_discount( $product )
		     &&   $this->member_prices_display_sale_price() ) {

			// temporarily disable membership price adjustments
			do_action( 'wc_memberships_discounts_disable_price_adjustments' );

			$price         = apply_filters( 'woocommerce_variation_prices_price', $variation->get_price( 'edit' ), $variation, $product );
			$sale_price    = apply_filters( 'woocommerce_variation_prices_sale_price', $variation->get_sale_price( 'edit' ), $variation, $product );
			$regular_price = $regular_price !== $sale_price && $price === $sale_price ? $sale_price : $regular_price;

			// re-enable membership price adjustments
			do_action( 'wc_memberships_discounts_enable_price_adjustments' );
		}

		return $regular_price;
	}


	/**
	 * Adds the current user ID to the variation prices hash for caching.
	 *
	 * @internal
	 *
	 * @since 1.3.2
	 *
	 * @param array|string $data the existing hash data (note: this is expected to be an array, but third parties might cast a wrong type)
	 * @param \WC_Product $product the current product variation
	 * @return array|string $data the hash data with a user ID added if applicable
	 */
	public function set_user_variation_prices_hash( $data, $product ) {

		if ( empty( $data ) ) {
			$data = [];
		}

		// Bail out if:
		// - discounts shouldn't be applied
		// - user has no member discounts over the product
		// - product is being explicitly excluded from member discounts
		if (      $this->should_apply_discounts
		     &&   is_array( $data )
		     && ! $this->is_product_excluded_from_member_discounts( $product )
		     &&   $this->user_has_member_discount( $product ) ) {

			$data[] = get_current_user_id();

			if ( $this->member_prices_display_sale_price() ) {
				$data[] = 'member_prices_display_sale_price';
			}
		}

		return $data;
	}


	/**
	 * Returns the member discount badge for HTML templates.
	 *
	 * @since 1.6.4
	 *
	 * @param \WC_Product $product the product object to output a badge for (passed to filter)
	 * @param bool $variation whether to output a discount badge specific for a product variation (default false)
	 * @return string
	 */
	public function get_member_discount_badge( $product, $variation = false ) {
		global $post;

		$label = __( 'Member discount!', 'woocommerce-memberships' );

		// we have a slight different output for badge classes and filter
		if ( true !== $variation ) {

			// used in filter for backwards compatibility reasons
			$the_post = $post;

			if ( ! $the_post instanceof \WP_Post ) {
				$the_post = get_post( $product->get_id() );
			}

			$badge = '<span class="onsale wc-memberships-member-discount">' . esc_html( $label ) . '</span>';

			/**
			 * Filters the member discount badge.
			 *
			 * @since 1.0.0
			 *
			 * @param string $badge the badge HTML
			 * @param \WP_Post $post the product post object
			 * @param \WC_Product_Variation $variation the product variation
			 */
			$badge = (string) apply_filters( 'wc_memberships_member_discount_badge', $badge, $the_post, $product );

		} else {

			$badge = '<span class="wc-memberships-variation-member-discount">' . esc_html( $label ) . '</span>';

			/**
			 * Filters the variation member discount badge.
			 *
			 * @since 1.3.2
			 *
			 * @param string $badge the badge HTML
			 * @param \WC_Product|\WC_Product_Variation $variation the product variation
			 */
			$badge = apply_filters( 'wc_memberships_variation_member_discount_badge', $badge, $product );

		}

		return $badge;
	}


	/**
	 * Filters the member discount badge for products excluded from member discount rules.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param string $badge badge HTML
	 * @param \WP_Post $post the post object
	 * @param \WC_Product $product the product object
	 * @return bool|string empty string if product is excluded from member discounts
	 */
	public function disable_discount_badge_for_excluded_products( $badge, $post, $product ) {
		return $this->is_product_excluded_from_member_discounts( $product ) ? '' : $badge;
	}


	/**
	 * Formats the price according to decimals used in store.
	 *
	 * @since 1.13.2
	 *
	 * @param string|float|int $price a price as a number (without currency symbols or thousands separator)
	 * @return float price as a number trimmed to the decimals used in shop
	 */
	private function format_price_decimals( $price ) {

		$price = ! is_numeric( $price ) ? 0 : $price;

		return (float) number_format( (float) max( 0, (float) $price ), wc_get_price_decimals(), '.', '' );
	}


	/**
	 * Returns the rounding precision based on the currency decimals.
	 *
	 * Uses @const WC_DISCOUNT_ROUNDING_MODE as fallback (normally 2).
	 *
	 * @since 1.9.5
	 *
	 * @return int
	 */
	private function get_rounding_precision() {

		if ( null === $this->rounding_precision ) {

			/**
			 * Filters the rounding precision used to round down discounted product prices.
			 *
			 * @since 1.9.5
			 *
			 * @param int $rounding_precision by default uses the same rounding precision as WooCommerce core
			 */
			$this->rounding_precision = (int) apply_filters( 'wc_memberships_discount_rounding_precision', wc_get_rounding_precision() );
		}

		return $this->rounding_precision;
	}


	/**
	 * Gets the product discounted price for a user that is eligible for member discounts.
	 *
	 * @since 1.3.0
	 *
	 * @param float $base_price original price
	 * @param int|\WC_Product $product product ID or product object
	 * @param int|null $user_id optional, defaults to current user ID
	 * @return float|string|null the discounted price or null if no discount applies (output is numerical string in REST requests)
	 */
	public function get_discounted_price( $base_price, $product, $user_id = null ) {

		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		} elseif ( is_numeric( $user_id ) ) {
			$user_id = (int) $user_id;
		}

		if ( is_numeric( $product ) ) {
			$product = wc_get_product( (int) $product );
		}

		$price          = null;
		$product_id     = null;
		$discount_rules = [];

		// we need a product and a numerical user ID to get a member discounted price
		if ( $product instanceof \WC_Product && is_int( $user_id ) ) {
			$product_id     = $product->get_id();
			$discount_rules = $this->get_user_product_purchasing_discount_rules( $user_id, $product_id );
		}

		if ( $product_id && ! empty( $discount_rules ) ) {

			$allow_cumulative_discounts = $this->should_apply_cumulative_member_discounts( $user_id, $product );

			$price  = (float) $base_price;
			$prices = array();

			// find out the discounted price for the current user
			foreach ( $discount_rules as $rule ) {

				$discount_amount = (float) $rule->get_discount_amount();

				switch ( $rule->get_discount_type() ) {
					case 'percentage':
						$discounted_price = $price * ( 100 - $discount_amount ) / 100;
					break;
					case 'amount':
						$discounted_price = $price - $discount_amount;
					break;
				}

				// make sure that the lowest price gets applied and doesn't become negative
				if ( isset( $discounted_price ) && $discounted_price < $price ) {
					if ( true === $allow_cumulative_discounts ) {
						$price    = max( $discounted_price, 0 );
					} else {
						$prices[] = max( $discounted_price, 0 );
					}
				}
			}

			// pick the lowest price
			if ( ! empty( $prices ) ) {
				$price = min( $prices );
			}

			// sanity check
			if ( $price >= $base_price ) {
				$price = null;
			} else {
				$price = round( $price, $this->get_rounding_precision(), PHP_ROUND_HALF_DOWN );
			}
		}

		/**
		 * Filters the discounted price of a membership product.
		 *
		 * @since 1.7.1
		 *
		 * @param null|float $price the discounted price or null if no discount applies
		 * @param float $base_price the original price (not discounted by Memberships)
		 * @param int $product_id the ID of the product (or variation) the price is for
		 * @param int $user_id the ID of the logged in user/member (it's zero for non logged in users)
		 * @param \WC_Product $product the product object for the price being discounted
		 */
		$price = apply_filters( 'wc_memberships_get_discounted_price', $price, $base_price, $product_id, $user_id, $product );

		if ( is_numeric( $price ) ) {

			$price = $this->format_price_decimals( $price );

			// special handling for REST responses, ensure the price is consistent with a numerical string type
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				$price = (string) $price;
			}
		}

		return $price;
	}


	/**
	 * Checks if the product is discounted for the user.
	 *
	 * @since 1.3.0
	 *
	 * @param float $base_price original price
	 * @param int|\WC_Product $product product ID or object
	 * @param null|int $user_id optional, defaults to current user ID
	 * @return bool
	 */
	public function has_discounted_price( $base_price, $product, $user_id = null ) {

		if ( is_numeric( $product ) ) {
			$product = wc_get_product( (int) $product );
		}

		$has_discounted_price = is_numeric( $this->get_discounted_price( $base_price, $product, $user_id ) );

		if ( ! $has_discounted_price && $product->is_type( 'variable' ) && ( $variations = $product->get_children() ) ) {

			$variations_discounts = array();

			foreach ( $variations as $variation_id ) {
				$variations_discounts[] = $this->has_discounted_price( $base_price, $variation_id, $user_id );
			}

			$has_discounted_price = in_array( true, $variations_discounts, true );
		}

		return $has_discounted_price;
	}


	/**
	 * Returns the original price from a discounted price.
	 *
	 * This is essentially a reversed discounted price method:
	 * @see \WC_Memberships_Member_Discounts::get_discounted_price()
	 *
	 * Normally you would not need to use this, as the raw prices could be determined by deactivating filters.
	 * However, there are cases where the price is compound or calculated on the fly and not stored somewhere,
	 * so it comes displayed already discounted while there might be need to restore and show the original prior to discounts.
	 *
	 * @since 1.8.8
	 *
	 * @param float $discounted_price the discounted price we need to retrieve the original of
	 * @param \WC_Product $product the product to get the original price for
	 * @param int $user_id the current user ID (0 for guests)
	 * @return float
	 */
	public function get_original_price( $discounted_price, $product, $user_id = null ) {

		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		} elseif ( is_numeric( $user_id ) ) {
			$user_id = (int) $user_id;
		}

		$original_price = $discounted_price;
		$discount_rules = [];

		if ( $product instanceof \WC_Product && is_int( $user_id ) ) {
			$discount_rules = $this->get_user_product_purchasing_discount_rules( $user_id, $product->get_id() );
		}

		if ( ! empty( $discount_rules ) ) {

			$cumulative_discounts = $this->should_apply_cumulative_member_discounts( $user_id, $product );
			$original_prices      = [];
			$original_price       = 0;

			// find out the discounted price for the current user
			foreach ( $discount_rules as $rule ) {

				$discount_amount = (float) $rule->get_discount_amount();

				switch ( $rule->get_discount_type() ) {

					case 'percentage':
						// check for 100% discount to avoid divisions by zero - caveat: 100% discounted prices aren't reversible
						$original_price = (float) 100 === $discount_amount ? $original_price : 100 * ( $discounted_price / ( 100 - $discount_amount ) );
					break;

					case 'amount':
						$original_price = $discounted_price + $discount_amount;
					break;
				}

				// make sure that the lowest price gets applied and doesn't become negative
				if ( $original_price > $discounted_price ) {
					if ( false === $cumulative_discounts ) {
						$original_price    = max( $original_price, 0 );
					} else {
						$original_prices[] = max( $original_price, 0 );
					}
				}
			}

			// pick the highest price
			if ( ! empty( $original_prices ) ) {
				$original_price = round( max( $original_prices ), $this->get_rounding_precision(), PHP_ROUND_HALF_UP );
			}

			// sanity check
			if ( $original_price <= $discounted_price ) {
				$original_price = $discounted_price;
			}
		}

		$original_price = ! $original_price ?: $this->format_price_decimals( $original_price );

		// special handling for REST responses, ensure the price is consistent with a numerical string type
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST && is_numeric( $original_price ) ) {
			$original_price = (string) $original_price;
		}

		return $original_price;
	}


	/**
	 * Returns the possible discount amounts for a given product for any plan.
	 *
	 * @see \WC_Memberships_Member_Discounts::get_product_discount()
	 *
	 * @since 1.12.0
	 *
	 * @param int $product_id discounted product ID
	 * @param int|float $product_price the normal product price
	 * @param null|int|int[] $plan_id optional membership plan ID or array of IDs, to query only discounts of a specific plan
	 * @return float[] array of amounts
	 */
	private function get_product_discounts( $product_id, $product_price = 0, $plan_id = null ) {

		$discount_amounts = array();
		$discount_rules   = wc_memberships()->get_rules_instance()->get_product_purchasing_discount_rules( $product_id );

		if ( ! empty( $discount_rules ) ) {

			foreach ( $discount_rules as $discount_rule ) {

				// skip if discount is not applying
				if ( ! $discount_rule->is_active() ) {
					continue;
				}

				// skip if specified plan ID to get discount for does not match
				if ( null !== $plan_id ) {

					if ( is_numeric( $plan_id ) && (int) $plan_id !== (int) $discount_rule->get_membership_plan_id() ) {
						continue;
					}

					if ( is_array( $plan_id ) && ! in_array( $discount_rule->get_membership_plan_id(), $plan_id, false ) ) {
						continue;
					}
				}

				switch ( $discount_rule->get_discount_type() ) {

					case 'amount' :

						$discount_amount = max( 0, (float) $discount_rule->get_discount_amount() );

						if ( $discount_amount > 0 ) {
							$discount_amounts[] = $discount_amount > $product_price ? $product_price : $discount_amount;
						}

					break;

					case 'percentage' :

						$discount_percentage = max( 0, (float) $discount_rule->get_discount_amount() );

						if ( $discount_percentage === (float) 100 ) {
							$discount_amounts[] = $product_price;
						} elseif ( $discount_percentage > 0 ) {
							$discount_amounts[] = max( 0, ( $discount_percentage * (float) $product_price ) / 100 );
						}

					break;
				}
			}
		}

		return $discount_amounts;
	}


	/**
	 * Returns the amount of a discount for a given product.
	 *
	 * For variable products it will return the largest discounted amount calculated from all the discounted variations.
	 *
	 * @since 1.12.0
	 *
	 * @param \WP_Post|\WC_Product|\WC_Product_Variable|\WC_Product_Variation|int $the_product product object, post or ID
	 * @param string $return_value either 'min', 'max' (default) or 'average' to return the corresponding discount value among all possible discounts
	 * @param null|\WC_Memberships_Membership_Plan|int|int[] optional plan object or ID (or array of IDs), if unspecified will grab the lowest discount attainable
	 * @return float discount amount as a number
	 */
	public function get_product_discount( $the_product, $return_value = 'max', $the_plan = null ) {

		$amounts = array();
		$product = is_numeric( $the_product ) || $the_product instanceof \WP_Post ? wc_get_product( $the_product ) : $the_product;

		if ( $product instanceof \WC_Product && ! $this->is_product_excluded_from_member_discounts( $product ) ) {

			if ( $this->applying_discounts ) {
				do_action( 'wc_memberships_discounts_disable_price_adjustments' );
			}

			$plan_id    = null;
			$product_id = $product->get_id();

			if ( $the_plan instanceof \WC_Memberships_Membership_Plan ) {
				$plan_id = $the_plan->get_id();
			} elseif ( is_numeric( $the_plan ) ) {
				$plan_id = (int) $the_plan;
			} elseif ( is_array( $the_plan ) ) {
				$plan_id = $the_plan;
			}

			if ( $product->is_type( 'variable' ) ) {
				$product_price = max( 0, (float) $product->get_variation_price( 'max' ) );
			} else {
				$product_price = max( 0, (float) $product->get_price() );
			}

			$amounts = $this->get_product_discounts( $product_id, $product_price, $plan_id );

			// if the product is a variation, add discounts applying for the parent variable too
			if ( $product->is_type( 'variation' ) ) {

				$parent_product = wc_get_product( $product->get_parent_id( 'edit' ) );

				if ( $parent_product && ! $this->is_product_excluded_from_member_discounts( $parent_product ) ) {

					$amounts = array_merge( $amounts, $this->get_product_discounts( $parent_product->get_id(), $product_price, $plan_id ) );
				}
			}

			if ( $this->applying_discounts ) {
				do_action( 'wc_memberships_discounts_enable_price_adjustments' );
			}
		}

		$values = ! empty( $amounts ) ? array_map( 'floatval', $amounts ) : array( 0 );

		switch ( $return_value ) {
			case 'min' :
				$discount = min( $values );
			break;
			case 'max' :
				$discount = max( $values );
			break;
			default : // average
				$discount = array_sum( $values ) / max( 1, count( $values ) );
			break;
		}

		return max( 0, $discount );
	}


	/**
	 * Returns the formatted discount HTML for a given product.
	 *
	 * This method is intended for showing a formatted discount amount to users who are not eligible for a discount.
	 *
	 * @since 1.12.0
	 *
	 * @param \WP_Post|\WC_Product|int $product product object, post or ID
	 * @param string $discount_value either 'min', 'max' (default) or 'average' to return the corresponding discount value among all possible discounts
	 * @param string $format either 'amount' (default) or 'percentage' of the normal product price
	 * @param null|\WC_Memberships_Membership_Plan|int optional plan object or ID, if unspecified will grab the lowest discount attainable
	 * @return string discount as an HTML price
	 */
	public function get_product_discount_html( $product, $discount_value = 'max', $format = 'amount', $plan = null ) {

		$discount = $this->get_product_discount( $product, $discount_value, $plan );

		switch ( $format ) {

			case 'percentage' :
			case 'percent' :
			case '%' :

				$product = is_numeric( $product ) || $product instanceof \WP_Post ? wc_get_product( $product ) : $product;
				$price   = $product instanceof \WC_Product ? (float) $product->get_price() : 0;

				if ( $discount <= 0 || $price <= 0 ) {
					$percentage = 0;
				} elseif ( $discount < 1 ) {
					$percentage = $price / ( 100 / $discount ) * $discount;
				} else {
					$percentage = ( 100 / $price ) * $discount;
				}

				$output = Framework\SV_WC_Helper::number_format( max( 0, $percentage ) ) . '%';

			break;

			case 'amount' :
			default :

				$output = wc_price( $discount );

			break;
		}

		return $output;
	}


	/**
	 * Refreshes cart fragments upon user login.
	 *
	 * This is useful if a non-logged in user added items to cart and becomes eligible for discounts - which otherwise would have no membership discounts applied.
	 * @see \WC_Cart::reset()
	 *
	 * @internal
	 *
	 * @since 1.6.4
	 *
	 * @param string $user_login user login name
	 * @param \WP_User $user user that just logged in
	 */
	public function refresh_cart_upon_member_login( $user_login, $user ) {

		// small "hack" to trigger a refresh in cart contents, that will set any membership discounts to products that apply
		if ( $user_login && wc_memberships_is_user_active_member( $user, null, false ) ) {
			$this->reset_cart_session_data();
		}
	}


	/**
	 * Resets the cart session data.
	 *
	 * @see \WC_Cart::reset() private method
	 *
	 * @since 1.6.4
	 */
	private function reset_cart_session_data() {

		$wc = WC();

		// Some very zealous sanity checks here:
		if ( $wc && isset( $wc->cart->cart_session_data ) ) {

			$session_data = $wc->cart->cart_session_data;

			if ( ! empty( $session_data ) ) {

				foreach ( $session_data as $key => $default ) {

					if ( isset( $wc->session->$key ) ) {
						unset( $wc->session->$key );
					}
				}
			}

			// WooCommerce core filter
			do_action( 'woocommerce_cart_reset', $wc->cart, true );
		}
	}


	/**
	 * Displays a note saying that the product received a discount from a membership product in the cart.
	 *
	 * @internal
	 *
	 * @since 1.17.0
	 *
	 * @param array $item_data array of cart item data
	 * @param array $cart_item cart item object
	 * @return array
	 */
	public function display_cart_purchasing_discount_message( $item_data, $cart_item ) {

		$product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;

		if ( $product instanceof \WC_Product && $this->should_apply_member_discounts_from_membership_product_in_cart() ) {

			$user_id = get_current_user_id();

			if ( $this->user_has_member_discount( $product, $user_id ) ) {

				$cart_plans     = $this->get_membership_plans_from_access_granting_products_in_cart();
				$rules          = $this->get_user_product_purchasing_discount_rules( $user_id, $product->get_id() );
				$discount_plans = [];

				foreach ( $rules as $rule ) {

					$plan_id = $rule->get_membership_plan_id();

					if ( isset( $cart_plans[ $plan_id ] ) && ! wc_memberships_is_user_active_member( $user_id, $cart_plans[ $plan_id ] ) ) {
						$discount_plans[ $plan_id ] = $cart_plans[ $plan_id ];
					}
				}

				$message     = '';
				$plans_count = count( $discount_plans );

				if ( 1 === $plans_count ) {

					$plans_by_product = $this->get_membership_plans_from_access_granting_products_in_cart( [
						'plans'     => $discount_plans,
						'index_key' => 'product_id',
					] );

					$message = \WC_Memberships_User_Messages::get_message_html( 'product_discounted_by_membership_product_in_cart', [
						'context'  => 'notice',
						'products' => array_keys( $plans_by_product ),
					] );

				} elseif ( $plans_count > 1 ) {

					$message = \WC_Memberships_User_Messages::get_message_html( 'product_discounted_by_membership_products_in_cart', [
						'context' => 'notice',
					] );
				}

				if ( '' !== $message ) {
					echo '<div class="wc-memberships-member-discount-cart-note"><small><i>' . $message . '</i></small></div>';
				}
			}
		}

		return $item_data;
	}


	/**
	 * Enables price adjustments.
	 *
	 * Calling this method will **enable** Membership adjustments for product prices that have member discounts for users eligible for discounts.
	 * @see \WC_Memberships_Member_Discounts::__construct() docblock for additional notes
	 * @see \WC_Memberships_Member_Discounts::enable_price_html_adjustments() which you'll probably want to use too
	 *
	 * @since 1.3.0
	 */
	public function enable_price_adjustments() {

		/**
		 * Filters the priority at which member pricing is adjusted.
		 *
		 * Needed in our Subscriptions discount integration to ensure discounts are done before recurring total calcs.
		 *
		 * @since 1.9.1
		 *
		 * @param int $priority the filter priority
		 */
		$priority = apply_filters( 'wc_memberships_price_adjustments_filter_priority', 999 );

		// apply membership discount to product price
		add_filter( 'woocommerce_product_get_sale_price',              array( $this, 'get_member_price' ), $priority, 2 );
		add_filter( 'woocommerce_product_variation_get_sale_price',    array( $this, 'get_member_price' ), $priority, 2 );
		add_filter( 'woocommerce_product_get_price',                   array( $this, 'get_member_price' ), $priority, 2 );
		add_filter( 'woocommerce_product_variation_get_price',         array( $this, 'get_member_price' ), $priority, 2 );
		// replace regular price with sale
		/** @see \WC_Memberships_Member_Discounts::member_prices_display_sale_price() */
		add_filter( 'woocommerce_product_get_regular_price',           array( $this, 'get_member_regular_price' ), $priority, 2 );
		add_filter( 'woocommerce_product_variation_get_regular_price', array( $this, 'get_member_regular_price' ), $priority, 2 );
		// apply membership discount to variation price
		add_filter( 'woocommerce_variation_prices_sale_price',    array( $this, 'get_member_variation_price' ), $priority, 3 );
		add_filter( 'woocommerce_variation_prices_price',         array( $this, 'get_member_variation_price' ), $priority, 3 );
		add_filter( 'woocommerce_variation_prices_regular_price', array( $this, 'get_member_variation_regular_price' ), $priority, 3 );
		// clear variation prices cache
		add_filter( 'woocommerce_get_variation_prices_hash', [ $this, 'set_user_variation_prices_hash' ], $priority, 2 );
	}


	/**
	 * Disables price adjustments.
	 *
	 * Calling this method will **disable** Membership adjustments for product prices that have member discounts for logged in users that are eligible for discounts.
	 * @see \WC_Memberships_Member_Discounts::__construct() docblock for additional notes
	 * @see \WC_Memberships_Member_Discounts::disable_price_html_adjustments() which you'll probably want to use too
	 *
	 * @since 1.3.0
	 */
	public function disable_price_adjustments() {

		/**
		 * Filters the priority at which member pricing is adjusted.
		 *
		 * Needed in our Subscriptions discount integration to ensure discounts are done before recurring total calcs.
		 *
		 * @since 1.9.1
		 *
		 * @param int $priority the filter priority
		 */
		$priority = apply_filters( 'wc_memberships_price_adjustments_filter_priority', 999 );

		// restore prices to original amount before membership discount
		remove_filter( 'woocommerce_product_get_sale_price',              array( $this, 'get_member_price' ), $priority );
		remove_filter( 'woocommerce_product_get_price',                   array( $this, 'get_member_price' ), $priority );
		remove_filter( 'woocommerce_product_variation_get_price',         array( $this, 'get_member_price' ), $priority );
		remove_filter( 'woocommerce_product_variation_get_sale_price',    array( $this, 'get_member_price' ), $priority );
		remove_filter( 'woocommerce_product_get_regular_price',           array( $this, 'get_member_regular_price' ), $priority );
		remove_filter( 'woocommerce_product_variation_get_regular_price', array( $this, 'get_member_regular_price' ), $priority );
		remove_filter( 'woocommerce_variation_prices_sale_price',    array( $this, 'get_member_variation_price' ), $priority );
		remove_filter( 'woocommerce_variation_prices_price',         array( $this, 'get_member_variation_price' ), $priority );
		remove_filter( 'woocommerce_variation_prices_regular_price', array( $this, 'get_member_variation_regular_price' ), $priority );
		remove_filter( 'woocommerce_get_variation_prices_hash',      [ $this, 'set_user_variation_prices_hash' ], $priority );
	}


	/**
	 * Enables price HTML adjustments.
	 *
	 * @see \WC_Memberships_Member_Discounts::__construct() docblock for additional notes
	 * @see \WC_Memberships_Member_Discounts::enable_price_adjustments() which you'll probably want to use too
	 *
	 * @since 1.3.0
	 */
	public function enable_price_html_adjustments() {

		// adjust environment for calculating discounted price html strings
		add_filter( 'woocommerce_get_price_html',                   array( $this, 'get_member_price_html' ), 999, 2 );
		add_filter( 'woocommerce_product_variation_get_price_html', array( $this, 'get_member_price_html' ), 999, 2 );

		// make sure that by default, 'is_on_sale' is based on prices before member discounts
		add_filter( 'woocommerce_product_is_on_sale', array( $this, 'product_is_on_sale' ), 999, 2 );
	}


	/**
	 * Disables price HTML adjustments.
	 *
	 * @see \WC_Memberships_Member_Discounts::__construct() docblock for additional notes
	 * @see \WC_Memberships_Member_Discounts::disable_price_adjustments() which you'll probably want to use too
	 *
	 * @since 1.3.0
	 */
	public function disable_price_html_adjustments() {

		// adjust environment for calculating discounted price html strings
		remove_filter( 'woocommerce_get_price_html',                   array( $this, 'get_member_price_html' ), 999 );
		remove_filter( 'woocommerce_product_variation_get_price_html', array( $this, 'get_member_price_html' ), 999 );

		// make sure that by default, 'is_on_sale' is based on prices before member discounts
		remove_filter( 'woocommerce_product_is_on_sale', array( $this, 'product_is_on_sale' ), 999 );
	}


}
