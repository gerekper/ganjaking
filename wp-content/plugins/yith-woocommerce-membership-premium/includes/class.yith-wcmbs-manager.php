<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Manager Class
 *
 * @class   YITH_WCMBS_Manager
 * @author  Yithemes
 * @since   1.0.0
 * @package Yithemes
 */
class YITH_WCMBS_Manager {

	/**
	 * Single instance of the class
	 *
	 * @var YITH_WCMBS_Manager
	 */
	protected static $instance;

	/**
	 * The post types.
	 *
	 * @var array
	 */
	public $post_types = array( 'post', 'product', 'page', 'attachment' );

	/**
	 * Transient for restricted items
	 *
	 * @var string
	 */
	public $restricted_items_transient_name = 'yith-wcmbs-restr-items';

	/**
	 * Transient for restricted items by user
	 *
	 * @var string
	 */
	public $restricted_users_transient_name = 'yith-wcmbs-restr-users';

	/**
	 * Transient for allowed items by user
	 *
	 * @var string
	 */
	public $allowed_users_transient_name = 'yith-wcmbs-allow-users';

	/**
	 * Returns single instance of the class
	 *
	 * @return YITH_WCMBS_Manager
	 */
	public static function get_instance() {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
	}

	/**
	 * Constructor
	 */
	protected function __construct() {
		if ( defined( 'YITH_WCMBS_RESTRICTED_CPT_MANAGEMENT_ENABLED' ) && YITH_WCMBS_RESTRICTED_CPT_MANAGEMENT_ENABLED ) {
			$restricted_custom_post_types = get_option( 'yith-wcmbs-membership-restricted-custom-post-types', array() );
			if ( ! ! $restricted_custom_post_types ) {
				$this->post_types = array_merge( $this->post_types, $restricted_custom_post_types );
			}
		}
		$this->post_types = array_unique( apply_filters( 'yith_wcmbs_membership_restricted_post_types', $this->post_types ) );
	}

	/**
	 * Getter for backward compatibility.
	 *
	 * @param string $key The key.
	 *
	 * @return int
	 */
	public function __get( $key ) {
		if ( 'transient_expiration' === $key ) {
			return $this->get_transient_expiration();
		}
	}

	/**
	 * Get the ids of users that have a membership plan
	 *
	 * @param int $plan_id
	 *
	 * @access public
	 * @return array
	 * @since  1.0.0
	 */
	public function get_user_ids_by_plan_id( $plan_id ) {
		$user_ids = YITH_WCMBS_Membership_Helper()->get_members( $plan_id, array( 'return' => 'ids' ) );

		return array_unique( $user_ids );
	}


	/**
	 * Get the link of post in base of user access
	 *
	 * @param int $post_id the id of the post
	 * @param int $user_id the id of the user
	 *
	 * @access public
	 * @return string|bool
	 * @since  1.0.0
	 */
	public function get_post_link( $post_id, $user_id ) {
		$products_manager = YITH_WCMBS_Products_Manager();
		$post_type        = get_post_type( $post_id );
		$link             = false;

		if ( $this->user_has_access_to_post( $user_id, $post_id ) || ( $post_type == 'product' && $products_manager->is_allowed_download() ) ) {
			if ( $post_type !== 'attachment' ) {
				$link = get_permalink( $post_id );
			} else {
				$link = add_query_arg( array( 'protected_media' => $post_id ), home_url( '/' ) );
			}
		}

		return $link;
	}

	/**
	 * count users that have a membership plan
	 *
	 * @param int $plan_id
	 *
	 * @access public
	 * @return int
	 * @since  1.0.0
	 */
	public function count_users_in_plan( $plan_id ) {
		return count( $this->get_user_ids_by_plan_id( $plan_id ) );
	}

	/**
	 * control if user has the active plan
	 * return true if user has the plan active
	 *
	 * @param int $user_id the id of the user
	 * @param int $plan_id the id of the plan
	 *
	 * @access public
	 * @return bool
	 * @since  1.0.0
	 */
	public function user_has_active_plan( $user_id, $plan_id ) {
		$member = YITH_WCMBS_Members()->get_member( $user_id );
		if ( $member ) {
			return $member->has_active_plan( $plan_id );
		}

		return false;
	}

	/**
	 * control if user has one plan at least
	 * return true if user has one plan active at least
	 *
	 * @param int   $user_id  the id of the user
	 * @param array $plan_ids the ids of plans
	 *
	 * @access public
	 * @return bool
	 * @since  1.0.0
	 */
	public function user_has_active_plans( $user_id, $plan_ids ) {
		$member = YITH_WCMBS_Members()->get_member( $user_id );
		if ( $member ) {
			if ( ! empty( $plan_ids ) ) {
				foreach ( $plan_ids as $plan_id ) {
					$has_active = $member->has_active_plan( $plan_id );
					if ( $has_active ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Get all plans
	 *
	 * @param array $deprecated
	 *
	 * @return YITH_WCMBS_Plan[]
	 */
	public function get_plans( $deprecated = array() ) {
		if ( $deprecated ) {
			return yith_wcmbs_get_plans( wp_parse_args( $deprecated, array( 'fields' => 'all' ) ) );
		}
		static $plans = null;
		if ( is_null( $plans ) ) {
			$plans = yith_wcmbs_get_plans( array( 'fields' => 'plans' ) );
		}

		return $plans;
	}

	/**
	 * Get the product linked to a plan
	 *
	 * @param int $plan_id the id of the plan
	 *
	 * @access     public
	 * @return array
	 * @since      1.0.0
	 * @deprecated since 1.2.10
	 */
	public function get_membership_product_id_for_plan( $plan_id ) {
		return $this->get_membership_product_ids_by_plan( $plan_id );
	}

	/**
	 * Get the product linked to a plan
	 *
	 * @param int $plan_id the id of the plan
	 *
	 * @access     public
	 * @return array
	 * @since      1.2.10
	 * @deprecated since 1.4.0 | use YITH_WCMBS_Plan::get_target_products instead
	 */
	public function get_membership_product_ids_by_plan( $plan_id ) {
		$plan = yith_wcmbs_get_plan( $plan_id );

		return $plan && $plan->is_purchasable() ? $plan->get_target_products() : array();
	}

	/**
	 * Get the plan id by the membership product id
	 *
	 * @param int $product_id the id of the membership product
	 *
	 * @access public
	 * @return int|bool
	 * @since  1.0.0
	 */
	public function get_plan_by_membership_product( $product_id ) {
		if ( $product_id && $product_id != 0 ) {
			foreach ( $this->get_plans() as $plan ) {
				if ( $plan->is_purchasable() && in_array( $product_id, $plan->get_target_products() ) ) {
					return $plan->get_id();
				}
			}
		}

		return false;
	}

	/**
	 * Get one plan post by id
	 *
	 * @param int $id
	 *
	 * @return false|WP_Post
	 * @deprecated 1.4.0 | use yith_wcmbs_get_plan instead
	 * @since      1.0.0
	 */
	public function get_plan_by_id( $id ) {
		$plan = get_post( $id );

		if ( $plan && $plan->post_type === YITH_WCMBS_Post_Types::$plan ) {
			return $plan;
		}

		return false;
	}


	/**
	 * Get the not allowed posts for a user
	 *
	 * @param int $user_id the user id
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function get_non_allowed_post_ids_for_user( $user_id ) {
		if ( yith_wcmbs_has_full_access( $user_id ) ) {
			return array();
		}

		$restricted_user_transient = yith_wcmbs_transient_enabled() ? get_transient( $this->restricted_users_transient_name ) : false;

		if ( $restricted_user_transient !== false && is_array( $restricted_user_transient ) && isset( $restricted_user_transient[ $user_id ] ) ) {
			$not_allowed = $restricted_user_transient[ $user_id ];
		} else {
			$restricted_items = $this->get_restricted_items();
			$not_allowed      = array();

			if ( ! empty( $restricted_items ) ) {
				foreach ( $restricted_items as $plan_id => $ids ) {
					$not_allowed = array_merge( $not_allowed, $ids );
				}
			}
			$not_allowed = array_unique( $not_allowed );

			$allowed = $this->get_allowed_post_ids_for_user( $user_id );

			$not_allowed = array_unique( array_diff( $not_allowed, $allowed ) );
			$not_allowed = apply_filters( 'yith_wcmbs_not_allowed_post_ids', $not_allowed, $user_id );

			// Set transient
			if ( ! $restricted_user_transient ) {
				$restricted_user_transient = array();
			}
			$restricted_user_transient[ $user_id ] = $not_allowed;
			if ( did_action( 'init' ) ) {
				set_transient( $this->restricted_users_transient_name, $restricted_user_transient, $this->get_transient_expiration() );
			}
		}

		return apply_filters( 'yith_wcmbs_non_allowed_post_ids_for_user', $not_allowed, $user_id );
	}

	/**
	 * Get the allowed post ids for the user
	 * Note: this not includes all posts or products in case "all posts" or "all products" option is selected in plan
	 *
	 * @param false|int $user_id
	 *
	 * @return array
	 * @since 1.4.0
	 */
	public function get_allowed_post_ids_for_user( $user_id = false ) {
		$user_id                 = ! ! $user_id ? $user_id : get_current_user_id();
		$allowed_users_transient = yith_wcmbs_transient_enabled() ? get_transient( $this->allowed_users_transient_name ) : false;

		if ( $allowed_users_transient !== false && is_array( $allowed_users_transient ) && isset( $allowed_users_transient[ $user_id ] ) ) {
			$allowed = $allowed_users_transient[ $user_id ];
		} else {
			$member = YITH_WCMBS_Members()->get_member( $user_id );

			$memberships   = $member->get_membership_plans( array( 'return' => 'complete' ) );
			$allowed       = array();
			$user_plan_ids = array();

			foreach ( $memberships as $membership ) {
				$r_args = array(
					'include_products' => true,
					'include_media'    => true,
					'parse_by_delay'   => true,
					'membership'       => $membership,
					'exclude_hidden'   => true,
				);

				$plan                 = $membership->get_plan();
				$allowed_by_this_plan = ! ! $plan ? $plan->get_restricted_item_ids( $r_args ) : array();

				$allowed         = array_merge( $allowed, $allowed_by_this_plan );
				$user_plan_ids[] = $membership->plan_id;
			}

			$allowed = array_unique( $allowed );
			$allowed = apply_filters( 'yith_wcmbs_filter_allowed_by_vendor_plans', $allowed, $user_plan_ids );

			// User can see also target products of plans: this way he/she'll be able to purchase the plan!
			if ( ! YITH_WCMBS_Products_Manager()->is_allowed_download() && yith_wcmbs_has_any_plan_with_all_posts_included( 'product' ) ) {
				$allow_all_target_products                             = apply_filters( 'yith_wcmbs_allow_all_target_products', true );
				$allow_target_products_of_plans_including_all_products = apply_filters( 'yith_wcmbs_allow_target_products_of_plans_including_all_products', true );
				$plans_to_include_target_products                      = array();
				if ( $allow_all_target_products ) {
					$plans_to_include_target_products = $this->get_plans();
				} elseif ( $allow_target_products_of_plans_including_all_products ) {
					$plans                            = yith_wcmbs_get_plans_including_all_posts( 'product' );
					$plans_to_include_target_products = array_map( 'yith_wcmbs_get_plan', $plans );
				}

				if ( $plans_to_include_target_products ) {
					foreach ( $plans_to_include_target_products as $plan ) {
						/**
						 * @var YITH_WCMBS_Plan $plan
						 */
						if ( $plan->is_purchasable() && $plan->get_target_products() ) {
							$allowed = array_unique( array_merge( $allowed, array_map( 'absint', $plan->get_target_products() ) ) );
						}
					}
				}
			}

			// Set transient
			if ( ! $allowed_users_transient ) {
				$allowed_users_transient = array();
			}
			$allowed_users_transient[ $user_id ] = $allowed;
			if ( did_action( 'init' ) ) {
				set_transient( $this->allowed_users_transient_name, $allowed_users_transient, $this->get_transient_expiration() );
			}
		}

		return $allowed;
	}


	/**
	 * parse post ids checking if user has access in base of delay time of contents
	 *
	 * @param array $post_ids
	 * @param int   $user_id
	 *
	 * @return array
	 * @access public
	 * @since  1.0.0
	 */
	public function parse_allowed_with_delay_time( $post_ids, $user_id ) {
		$member                = YITH_WCMBS_Members()->get_member( $user_id );
		$user_membership_plans = $member->get_membership_plans( array( 'return' => 'id_date', 'include_linked' => true ) );
		$new_post_ids          = array();
		if ( ! empty( $post_ids ) && ! empty( $user_membership_plans ) ) {
			foreach ( $post_ids as $id ) {
				$delay                   = get_post_meta( $id, '_yith_wcmbs_plan_delay', true );
				$restricted_access_plans = yith_wcmbs_get_plans_meta_for_post( $id );
				if ( ! empty( $delay ) ) {
					foreach ( $user_membership_plans as $plan ) {
						if ( ! isset( $delay[ $plan['id'] ] ) ) {
							if ( in_array( $plan['id'], (array) $restricted_access_plans ) ) {
								$new_post_ids[] = $id;
							}
						} else {
							if ( yith_wcmbs_local_strtotime_midnight_to_utc( '+' . $delay[ $plan['id'] ] . ' days', $plan['date'] ) <= yith_wcmbs_local_strtotime_midnight_to_utc() ) {
								$new_post_ids[] = $id;
							}
						}
					}
				} else {
					$new_post_ids[] = $id;
				}
			}
		}

		return array_unique( $new_post_ids );
	}

	/**
	 * create an array with info of plans
	 * return relations between plans and its associated product cats, post cats, and tags
	 *
	 * @return array
	 * @access public
	 * @since  1.0.0
	 */
	public function get_plans_info_array() {
		$prod_cats_plans_array = array();
		$post_cats_plans_array = array();
		$prod_tags_plans_array = array();
		$post_tags_plans_array = array();

		foreach ( $this->get_plans() as $plan ) {
			$plan_prod_cats = $plan->get_product_categories();
			$plan_post_cats = $plan->get_post_categories();
			$plan_prod_tags = $plan->get_product_tags();
			$plan_post_tags = $plan->get_post_tags();

			if ( ! empty( $plan_prod_cats ) ) {
				foreach ( $plan_prod_cats as $cat_id ) {
					$prod_cats_plans_array[ $cat_id ][] = $plan->get_id();
				}
			}
			if ( ! empty( $plan_post_cats ) ) {
				foreach ( $plan_post_cats as $cat_id ) {
					$post_cats_plans_array[ $cat_id ][] = $plan->get_id();
				}
			}
			if ( ! empty( $plan_prod_tags ) ) {
				foreach ( $plan_prod_tags as $tag_id ) {
					$prod_tags_plans_array[ $tag_id ][] = $plan->get_id();
				}
			}
			if ( ! empty( $plan_post_tags ) ) {
				foreach ( $plan_post_tags as $tag_id ) {
					$post_tags_plans_array[ $tag_id ][] = $plan->get_id();
				}
			}
		}

		return array(
			'prod_cats_plans_array' => $prod_cats_plans_array,
			'post_cats_plans_array' => $post_cats_plans_array,
			'prod_tags_plans_array' => $prod_tags_plans_array,
			'post_tags_plans_array' => $post_tags_plans_array,
		);
	}

	/**
	 * Get the ids of items that have restricted access
	 *
	 * @param null $deprecated Deprecated param
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function get_restricted_items( $deprecated = null ) {
		static $items = null;

		if ( is_null( $items ) ) {
			$items = yith_wcmbs_transient_enabled() ? get_transient( $this->restricted_items_transient_name ) : false;
			if ( ! is_array( $items ) ) {
				$plans = $this->get_plans();
				$items = array();
				$args  = array(
					'include_products' => ! YITH_WCMBS_Products_Manager()->is_allowed_download(),
				);

				if ( ! empty( $plans ) ) {
					foreach ( $plans as $plan ) {
						$plan_items = $plan->get_restricted_item_ids( $args );

						if ( ! empty( $plan_items ) ) {
							$items[ $plan->get_id() ] = array_unique( $plan_items );
						}
					}
				}

				if ( did_action( 'init' ) ) {
					set_transient( $this->restricted_items_transient_name, $items, $this->get_transient_expiration() );
				}
			}
		}

		return $items;
	}

	/**
	 * Get the ids of items that are in a plan
	 *
	 * @param int   $plan_id
	 * @param array $args
	 *
	 * @access     public
	 * @return array
	 * @since      1.0.0
	 * @deprecated 1.4.0 | use YITH_WCMBS_Plan::get_restricted_items instead
	 */
	public function get_restricted_items_in_plan( $plan_id, $args = array() ) {
		$plan = yith_wcmbs_get_plan( $plan_id );

		return ! ! $plan ? $plan->get_restricted_item_ids( $args ) : array();
	}

	/**
	 * exclude hidden items in plan
	 * TODO: to remove, since this feature is not available since 1.4.0
	 *
	 * @param $items
	 * @param $plan_id
	 *
	 * @return array array of item ids
	 */
	public function exclude_hidden_items( $items, $plan_id ) {
		$hidden_in_plan = get_post_meta( $plan_id, '_yith_wcmbs_hidden_item_ids', true );
		if ( ! empty( $hidden_in_plan ) && is_array( $hidden_in_plan ) ) {
			$items = array_diff( $items, $hidden_in_plan );
		}

		return $items;
	}

	/**
	 * Get the ids of post included in a plan
	 *
	 * @param int  $plan_id
	 * @param bool $exclude_hidden
	 *
	 * @access public
	 * @return array
	 * @since  1.0.0
	 */
	public function get_allowed_posts_in_plan( $plan_id, $exclude_hidden = false ) {
		$plan  = yith_wcmbs_get_plan( $plan_id );
		$items = ! ! $plan ? $plan->get_restricted_item_ids( array( 'include_products' => true, 'include_media' => false, 'exclude_hidden' => $exclude_hidden ) ) : array();

		$items = apply_filters( 'yith_wcmbs_filter_allowed_by_vendor_plans', $items, array( $plan_id ) );

		return apply_filters( 'yith_wcmbs_get_allowed_posts_in_plan', $items, $plan_id, $exclude_hidden );
	}

	/**
	 * Check if the user has access to the post
	 *
	 * @param int  $user_id
	 * @param int  $post_id
	 * @param bool $check_for_all_posts
	 *
	 * @return bool
	 * @access public
	 * @since  1.0.0
	 */
	public function user_has_access_to_post( $user_id, $post_id, $check_for_all_posts = true ) {
		if ( $check_for_all_posts ) {
			$post_type           = get_post_type( $post_id );
			$post_types_to_check = ! YITH_WCMBS_Products_Manager()->is_allowed_download() ? array( 'post', 'product' ) : array( 'post' );
			if ( in_array( $post_type, $post_types_to_check ) && yith_wcmbs_has_any_plan_with_all_posts_included( $post_type ) ) {
				if ( yith_wcmbs_has_full_access_to_all_posts( $post_type, $user_id ) ) {
					return true;
				} else {
					$allowed = $this->get_allowed_post_ids_for_user( $user_id );

					return in_array( $post_id, $allowed );
				}
			}
		}

		$not_allowed_for_this_user = $this->get_non_allowed_post_ids_for_user( $user_id );

		return ! in_array( $post_id, $not_allowed_for_this_user );
	}

	/**
	 * Get Transient Expiration
	 *
	 * @return int
	 * @since 1.4.8
	 */
	public function get_transient_expiration() {
		return apply_filters( 'yith_wcmbs_transient_expiration', DAY_IN_SECONDS );
	}

	/**
	 * delete the transients
	 */
	public function delete_transients() {
		delete_transient( $this->restricted_items_transient_name );
		delete_transient( $this->restricted_users_transient_name );
		delete_transient( $this->allowed_users_transient_name );
	}
}

/**
 * Unique access to instance of YITH_WCMBS_Manager class
 *
 * @return YITH_WCMBS_Manager
 * @since 1.0.0
 */
function YITH_WCMBS_Manager() {
	return YITH_WCMBS_Manager::get_instance();
}