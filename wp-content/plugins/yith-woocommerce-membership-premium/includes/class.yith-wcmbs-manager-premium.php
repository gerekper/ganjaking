<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Manager Class
 *
 * @class   YITH_WCMBS_Manager_Premium
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 */
class YITH_WCMBS_Manager_Premium extends YITH_WCMBS_Manager {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Manager_Premium
     * @since 1.0.0
     */
    protected static $_instance;

    public $plan_post_type = 'yith-wcmbs-plan';

    public $restricted_items_transient_name = 'yith-wcmbs-restr-items';
    public $restricted_users_transient_name = 'yith-wcmbs-restr-users';
    public $transient_expiration            = 0;

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    protected function __construct() {
        $this->transient_expiration = 60 * 60 * 24;

        $this->post_types = array( 'post', 'product', 'page', 'attachment' );

        if ( defined( 'YITH_WCMBS_RESTRICTED_CPT_MANAGEMENT_ENABLED' ) && YITH_WCMBS_RESTRICTED_CPT_MANAGEMENT_ENABLED ) {
            $restricted_custom_post_types = get_option( 'yith-wcmbs-membership-restricted-custom-post-types', array() );
            if ( !!$restricted_custom_post_types ) {
                $this->post_types = array_merge( $this->post_types, $restricted_custom_post_types );
            }
        }
        $this->post_types = array_unique( apply_filters( 'yith_wcmbs_membership_restricted_post_types', $this->post_types ) );
    }

    /**
     * Magic getter
     */
    public function __get( $key ) {
        if ( 'status_list' == $key ) {
            if ( empty( $this->status_list ) ) {
                $this->status_list = yith_wcmbs_get_membership_statuses();
            }

            return $this->status_list;
        }

        if ( 'plans' == $key ) {
            if ( empty( $this->plans ) ) {
                $this->plans = $this->get_plans();
            }

            return $this->plans;
        }
    }

    public function __isset( $key ) {
        if ( 'status_list' == $key ) {
            if ( empty( $this->status_list ) ) {
                $this->status_list = yith_wcmbs_get_membership_statuses();
            }

            return isset( $this->status_list );
        }

        if ( 'plans' == $key ) {
            if ( empty( $this->plans ) ) {
                $this->plans = $this->get_plans();
            }

            return isset( $this->plans );
        }
    }


    /**
     * Get the ids of users that have a membership plan
     *
     * @param int $plan_id
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
     * @access public
     * @return string|bool
     * @since  1.0.0
     */
    public function get_post_link( $post_id, $user_id ) {
        $products_manager = YITH_WCMBS_Products_Manager();
        $post_type        = get_post_type( $post_id );

        if ( $this->user_has_access_to_post( $user_id, $post_id ) || ( $post_type == 'product' && $products_manager->is_allowed_download() ) ) {
            if ( $post_type != 'attachment' ) {
                return get_permalink( $post_id );
            } else {
                return add_query_arg( array( 'protected_media' => $post_id ), home_url( '/' ) );
            }
        }

        return false;
    }

    /**
     * count users that have a membership plan
     *
     * @param int $plan_id
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
     * @access public
     * @return bool
     * @since  1.0.0
     */
    public function user_has_active_plans( $user_id, $plan_ids ) {
        $member = YITH_WCMBS_Members()->get_member( $user_id );
        if ( $member ) {
            if ( !empty( $plan_ids ) ) {
                foreach ( $plan_ids as $plan_id ) {
                    $has_active = $member->has_active_plan( $plan_id );
                    if ( $has_active )
                        return true;
                }
            }
        }

        return false;
    }

    /**
     * Get all plan posts
     *
     * @access public
     * @param array $args
     * @return bool|WP_Post[]
     * @since  1.0.0
     */
    public function get_plans( $args = array() ) {
        $plan_args = array(
            'posts_per_page'             => -1,
            'post_type'                  => 'yith-wcmbs-plan',
            'post_status'                => 'publish',
            'orderby'                    => 'post_title',
            'order'                      => 'ASC',
            'yith_wcmbs_suppress_filter' => true,
            'lang'                       => false   // support for Polylang
        );

        $plan_args = wp_parse_args( $args, $plan_args );

        $plans = get_posts( $plan_args );

        return $plans;
    }

    /**
     * Get the product linked to a plan
     *
     * @param int $plan_id the id of the plan
     * @access     public
     * @return int|bool
     * @since      1.0.0
     * @deprecated since 1.2.10
     */
    public function get_membership_product_id_for_plan( $plan_id ) {
        return get_post_meta( $plan_id, '_membership-product', true );
    }

    /**
     * Get the product linked to a plan
     *
     * @param int $plan_id the id of the plan
     * @access public
     * @return array
     * @since  1.2.10
     */
    public function get_membership_product_ids_by_plan( $plan_id ) {
        $product_ids = get_post_meta( $plan_id, '_membership-product', true );
        if ( !$product_ids )
            $product_ids = array();
        else if ( is_string( $product_ids ) )
            $product_ids = explode( ',', $product_ids );
        else if ( !is_array( $product_ids ) )
            $product_ids = (array) $product_ids;

        return $product_ids;
    }

    /**
     * Get the plan id by the membership product id
     *
     * @param int $product_id the id of the membership product
     * @access public
     * @return int|bool
     * @since  1.0.0
     */
    public function get_plan_by_membership_product( $product_id ) {
        if ( $product_id ) {
            if ( !empty( $this->plans ) ) {
                foreach ( $this->plans as $plan ) {
                    $plan_prod_ids = $this->get_membership_product_ids_by_plan( $plan->ID );
                    if ( in_array( $product_id, $plan_prod_ids ) )
                        return $plan->ID;
                }
            }
        }

        return false;
    }

    /**
     * Get one plan post by id
     *
     * @param int $id
     * @access public
     * @return WP_Post|bool
     * @since  1.0.0
     */
    public function get_plan_by_id( $id ) {
        $plan = get_post( $id );

        if ( $plan && $plan->post_type == $this->plan_post_type ) {
            return $plan;
        }

        return false;
    }


    /**
     * Get the not allowed posts for a user
     *
     * @param int $user_id the user id
     * @access public
     * @return array
     * @since  1.0.0
     */
    public function get_non_allowed_post_ids_for_user( $user_id ) {
        // FULL ACCESS TO ADMIN
        if ( user_can( $user_id, 'create_users' ) )
            return array();

        $restricted_user_transient = get_transient( $this->restricted_users_transient_name );

        if ( $restricted_user_transient && isset( $restricted_user_transient[ $user_id ] ) ) {
            $not_allowed = $restricted_user_transient[ $user_id ];
        } else {

            $member = YITH_WCMBS_Members()->get_member( $user_id );

            $restricted_items = $this->get_restricted_items();
            $not_allowed      = array();

            if ( !empty( $restricted_items ) ) {
                foreach ( $restricted_items as $plan_id => $ids ) {
                    $not_allowed = array_merge( $not_allowed, $ids );
                }
            }
            $not_allowed = array_unique( $not_allowed );

            $user_membership_plans     = (array) $member->get_membership_plans( array( 'return' => 'complete' ) );
            $allowed_by_plan           = array();
            $user_membership_plans_ids = array();
            foreach ( $user_membership_plans as $membership ) {
                $r_args = array(
                    'include_products' => true,
                    'include_media'    => true,
                    'parse_by_delay'   => true,
                    'membership'       => $membership,
                    'exclude_hidden'   => true
                );

                $allowed_by_this_plan = $this->get_restricted_items_in_plan( $membership->plan_id, $r_args );

                $allowed_by_plan             = array_merge( $allowed_by_plan, $allowed_by_this_plan );
                $user_membership_plans_ids[] = $membership->plan_id;
            }

            $allowed_by_plan = array_unique( $allowed_by_plan );
            $allowed_by_plan = apply_filters( 'yith_wcmbs_filter_allowed_by_vendor_plans', $allowed_by_plan, $user_membership_plans_ids );

            $not_allowed = array_unique( array_diff( $not_allowed, $allowed_by_plan ) );
            $not_allowed = apply_filters( 'yith_wcmbs_not_allowed_post_ids', $not_allowed, $user_id );

            // Set transient
            if ( !$restricted_user_transient )
                $restricted_user_transient = array();
            $restricted_user_transient[ $user_id ] = $not_allowed;
            if ( did_action( 'init' ) ) {
                set_transient( $this->restricted_users_transient_name, $restricted_user_transient, $this->transient_expiration );
            }
        }

        return apply_filters( 'yith_wcmbs_non_allowed_post_ids_for_user', $not_allowed, $user_id );
    }


    /**
     * parse post ids checking if user has access in base of delay time of contents
     *
     * @param array $post_ids
     * @param int   $user_id
     * @return array
     * @access public
     * @since  1.0.0
     */
    public function parse_allowed_with_delay_time( $post_ids, $user_id ) {
        $member                = YITH_WCMBS_Members()->get_member( $user_id );
        $user_membership_plans = $member->get_membership_plans( array( 'return' => 'id_date', 'include_linked' => true ) );
        $new_post_ids          = array();
        if ( !empty( $post_ids ) && !empty( $user_membership_plans ) ) {
            foreach ( $post_ids as $id ) {
                $delay                   = get_post_meta( $id, '_yith_wcmbs_plan_delay', true );
                $restricted_access_plans = get_post_meta( $id, '_yith_wcmbs_restrict_access_plan', true );
                if ( !empty( $delay ) ) {
                    foreach ( $user_membership_plans as $plan ) {
                        if ( !isset( $delay[ $plan[ 'id' ] ] ) ) {
                            if ( in_array( $plan[ 'id' ], (array) $restricted_access_plans ) ) {
                                $new_post_ids[] = $id;
                            }
                        } else {
                            if ( strtotime( '+' . $delay[ $plan[ 'id' ] ] . ' days midnight', $plan[ 'date' ] ) <= strtotime( 'midnight' ) ) {
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

        if ( !empty( $this->plans ) ) {
            foreach ( $this->plans as $plan ) {
                $plan_prod_cats = get_post_meta( $plan->ID, '_product-cats', true );
                $plan_post_cats = get_post_meta( $plan->ID, '_post-cats', true );
                $plan_prod_tags = get_post_meta( $plan->ID, '_product-tags', true );
                $plan_post_tags = get_post_meta( $plan->ID, '_post-tags', true );

                if ( !empty( $plan_prod_cats ) ) {
                    foreach ( $plan_prod_cats as $cat_id ) {
                        $prod_cats_plans_array[ $cat_id ][] = $plan->ID;
                    }
                }
                if ( !empty( $plan_post_cats ) ) {
                    foreach ( $plan_post_cats as $cat_id ) {
                        $post_cats_plans_array[ $cat_id ][] = $plan->ID;
                    }
                }
                if ( !empty( $plan_prod_tags ) ) {
                    foreach ( $plan_prod_tags as $tag_id ) {
                        $prod_tags_plans_array[ $tag_id ][] = $plan->ID;
                    }
                }
                if ( !empty( $plan_post_tags ) ) {
                    foreach ( $plan_post_tags as $tag_id ) {
                        $post_tags_plans_array[ $tag_id ][] = $plan->ID;
                    }
                }
            }
        }

        $info_array = array(
            'prod_cats_plans_array' => $prod_cats_plans_array,
            'post_cats_plans_array' => $post_cats_plans_array,
            'prod_tags_plans_array' => $prod_tags_plans_array,
            'post_tags_plans_array' => $post_tags_plans_array,
        );

        return $info_array;
    }

    /**
     * Get the ids of items that have restricted access
     *
     * @param array $args
     * @access public
     * @return array
     * @since  1.0.0
     */
    public function get_restricted_items( $args = array() ) {

        $restricted_items = get_transient( $this->restricted_items_transient_name );
        if ( $restricted_items )
            return $restricted_items;

        $plans = $this->get_plans();

        $items = array();

        $default_args = array(
            'include_products' => false
        );

        $args = wp_parse_args( $args, $default_args );

        if ( !empty( $plans ) ) {
            foreach ( $plans as $plan ) {
                $plan_items = $this->get_restricted_items_in_plan( $plan->ID, $args );

                if ( !empty( $plan_items ) ) {
                    $items[ $plan->ID ] = array_unique( $plan_items );
                }
            }
        }

        if ( did_action( 'init' ) ) {
            set_transient( $this->restricted_items_transient_name, $items, $this->transient_expiration );
        }

        return $items;
    }

    /**
     * Get the ids of items that are in a plan
     *
     * @param int   $plan_id
     * @param array $args
     * @access public
     * @return array
     * @since  1.0.0
     */
    public function get_restricted_items_in_plan( $plan_id, $args = array() ) {
        $default_args = array(
            'include_products' => false,
            'include_media'    => true,
            'parse_by_delay'   => false,
            'membership'       => false,
            'include_linked'   => true,
            'exclude_hidden'   => false
        );

        $args             = wp_parse_args( $args, $default_args );
        $include_products = $args[ 'include_products' ];
        $include_media    = $args[ 'include_media' ];
        $include_linked   = $args[ 'include_linked' ];
        $exclude_hidden   = $args[ 'exclude_hidden' ];
        $parse_by_delay   = $args[ 'parse_by_delay' ];
        $membership       = $args[ 'membership' ];

        $parse_by_delay = apply_filters( 'yith_wcmbs_get_restricted_items_in_plan_parse_by_delay', $parse_by_delay, $plan_id, $args );
        $exclude_hidden = apply_filters( 'yith_wcmbs_get_restricted_items_in_plan_exclude_hidden', $exclude_hidden, $plan_id, $args );

        if ( $parse_by_delay && $membership ) {
            if ( !$membership instanceof YITH_WCMBS_Membership ) {
                if ( is_numeric( $membership ) ) {
                    $membership = new YITH_WCMBS_Membership( $membership );
                }
            }
        }

        $restricted_post_types = apply_filters( 'yith_wcmbs_restricted_post_types', $this->post_types );
        if ( !$include_media ) {
            $restricted_post_types = array_diff( $restricted_post_types, array( 'attachment' ) );
        }

        $plan_ids = array( $plan_id );
        if ( $include_linked ) {
            $linked_ids = get_post_meta( $plan_id, '_linked-plans', true );
            $plan_ids   = !empty( $linked_ids ) ? array_merge( $plan_ids, $linked_ids ) : $plan_ids;
        }

        $plan_items = array();

        foreach ( $plan_ids as $plan_id ) {
            $plan_post_cats = get_post_meta( $plan_id, '_post-cats', true );
            $plan_post_tags = get_post_meta( $plan_id, '_post-tags', true );
            $plan_prod_cats = get_post_meta( $plan_id, '_product-cats', true );
            $plan_prod_tags = get_post_meta( $plan_id, '_product-tags', true );

            foreach ( $restricted_post_types as $post_type ) {
                $meta_query = array(
                    'relation' => 'AND',
                    array(
                        'key'     => '_yith_wcmbs_restrict_access_plan',
                        'value'   => $plan_id,
                        'compare' => 'LIKE'
                    )
                );

                $args       = array(
                    'post_type'                  => $post_type,
                    'posts_per_page'             => -1,
                    'post_status'                => $post_type == 'attachment' ? 'any' : 'publish',
                    'yith_wcmbs_suppress_filter' => true,
                    'meta_query'                 => $meta_query,
                    'fields'                     => 'ids'
                );
                $this_items = get_posts( $args );
                if ( $exclude_hidden )
                    $this_items = $this->exclude_hidden_items( $this_items, $plan_id );

                $plan_items = array_unique( array_merge( $plan_items, $this_items ) );

                if ( !in_array( $post_type, array( 'page', 'attachment' ) ) ) {
                    $tax_query = array(
                        'relation' => 'OR',
                        array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'term_id',
                            'terms'    => $plan_prod_cats,
                            'operator' => 'IN'
                        ),
                        array(
                            'taxonomy' => 'product_tag',
                            'field'    => 'term_id',
                            'terms'    => $plan_prod_tags,
                            'operator' => 'IN'
                        ),
                        array(
                            'taxonomy' => 'category',
                            'field'    => 'term_id',
                            'terms'    => $plan_post_cats,
                            'operator' => 'IN'
                        )
                    );

                    $args       = array(
                        'post_type'                  => $post_type,
                        'posts_per_page'             => -1,
                        'post_status'                => $post_type == 'attachment' ? 'any' : 'publish',
                        'yith_wcmbs_suppress_filter' => true,
                        'tax_query'                  => $tax_query,
                        'fields'                     => 'ids'
                    );
                    $this_items = get_posts( $args );
                    if ( $exclude_hidden )
                        $this_items = $this->exclude_hidden_items( $this_items, $plan_id );

                    $plan_items = array_unique( array_merge( $plan_items, $this_items ) );
                }

                if ( !in_array( $post_type, array( 'product', 'page', 'attachment' ) ) && !empty( $plan_post_tags ) ) {
                    $args       = array(
                        'post_type'                  => $post_type,
                        'posts_per_page'             => -1,
                        'post_status'                => 'publish',
                        'yith_wcmbs_suppress_filter' => true,
                        'tag__in'                    => $plan_post_tags,
                        'fields'                     => 'ids'
                    );
                    $this_items = get_posts( $args );
                    if ( $exclude_hidden )
                        $this_items = $this->exclude_hidden_items( $this_items, $plan_id );

                    $plan_items = array_unique( array_merge( $plan_items, $this_items ) );
                }
            }
            if ( $include_products ) {
                $products = apply_filters( 'yith_wcmbs_allowed_in_plan', array(), $plan_id );
                if ( $exclude_hidden )
                    $products = $this->exclude_hidden_items( $products, $plan_id );
                $plan_items = array_unique( array_merge( $plan_items, $products ) );
                //$plan_items = apply_filters( 'yith_wcmbs_allowed_in_plan', $plan_items, $plan_id );
            }
        }

        // Filter by delay time if setted
        if ( $parse_by_delay && $membership ) {
            $filtered_items = array();
            $start_date     = $membership->start_date + ( $membership->paused_days * 60 * 60 * 24 );
            foreach ( $plan_items as $id ) {
                $delay = get_post_meta( $id, '_yith_wcmbs_plan_delay', true );
                if ( !empty( $delay ) ) {
                    if ( !isset( $delay[ $membership->plan_id ] ) ) {
                        $linked         = $membership->get_linked_plans();
                        $min_delay_time = 0;
                        $first          = true;
                        foreach ( $linked as $plan_id ) {
                            if ( isset( $delay[ $plan_id ] ) ) {
                                if ( $first ) {
                                    $min_delay_time = $delay[ $plan_id ];
                                    $first          = false;
                                } else {
                                    if ( $delay[ $plan_id ] < $min_delay_time ) {
                                        $min_delay_time = $delay[ $plan_id ];
                                    }
                                }
                            }
                        }

                        if ( $min_delay_time > 0 ) {
                            if ( strtotime( '+' . $min_delay_time . ' days midnight', $start_date ) <= strtotime( 'midnight' ) ) {
                                $filtered_items[] = $id;
                            }
                        } else {
                            $filtered_items[] = $id;
                        }
                    } else {
                        if ( $delay[ $membership->plan_id ] < 1 || strtotime( '+' . $delay[ $membership->plan_id ] . ' days midnight', $start_date ) <= strtotime( 'midnight' ) ) {
                            $filtered_items[] = $id;
                        }
                    }
                } else {
                    $filtered_items[] = $id;
                }
            }

            $plan_items = $filtered_items;
        }

        return apply_filters( 'yith_wcmbs_get_restricted_items_in_plan', $plan_items, $plan_id, $args );
    }

    /**
     * exclude hidden items in plan
     *
     * @param $items
     * @param $plan_id
     * @return array array of item ids
     */
    public function exclude_hidden_items( $items, $plan_id ) {
        $hidden_in_plan = get_post_meta( $plan_id, '_yith_wcmbs_hidden_item_ids', true );
        if ( !empty( $hidden_in_plan ) && is_array( $hidden_in_plan ) ) {
            $items = array_diff( $items, $hidden_in_plan );
        }

        return $items;
    }

    /**
     * Get the ids of post included in a plan
     *
     * @param int  $plan_id
     * @param bool $exclude_hidden
     * @access public
     * @return array
     * @since  1.0.0
     */
    public function get_allowed_posts_in_plan( $plan_id, $exclude_hidden = false ) {
        $items = $this->get_restricted_items_in_plan( $plan_id, array( 'include_products' => true, 'include_media' => false, 'exclude_hidden' => $exclude_hidden ) );

        $items = apply_filters( 'yith_wcmbs_filter_allowed_by_vendor_plans', $items, array( $plan_id ) );

        return apply_filters( 'yith_wcmbs_get_allowed_posts_in_plan', $items, $plan_id, $exclude_hidden );
    }

    /**
     * Check if the user has access to the post
     *
     * @param int $user_id
     * @param int $post_id
     * @access public
     * @return bool
     * @since  1.0.0
     */
    public function user_has_access_to_post( $user_id, $post_id ) {
        $not_allowed_for_this_user = $this->get_non_allowed_post_ids_for_user( $user_id );

        return !in_array( $post_id, $not_allowed_for_this_user );
    }
}
