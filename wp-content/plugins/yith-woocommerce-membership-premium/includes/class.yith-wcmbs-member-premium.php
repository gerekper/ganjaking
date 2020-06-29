<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Member Class
 *
 * @class   YITH_WCMBS_Member_Premium
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Member_Premium extends YITH_WCMBS_Member {

    /**
     * Constructor
     *
     * @param int $user_id the id of the user
     *
     * @access public
     * @since  1.0.0
     * @return mixed array|bool
     */
    public function __construct( $user_id ) {
        parent::__construct( $user_id );
    }

    /**
     * return true if user has membership plan active, resumed or expiring
     *
     * @access public
     * @since  1.1.2
     * @return bool
     */
    public function is_member() {
        if ( !$this->is_valid() )
            return false;

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'   => '_user_id',
                'value' => $this->id,
            ),
            array(
                'key'     => '_status',
                'value'   => array( 'active', 'resumed', 'expiring' ),
                'compare' => 'IN',
            ),
        );

        return !!( YITH_WCMBS_Membership_Helper()->get_memberships_by_meta( $meta_query, 'posts' ) );
    }

    /**
     * Get all membership plans
     *
     * @param array $args
     *
     * @access public
     * @since  1.0.0
     * @return YITH_WCMBS_Membership[]|array
     */
    public function get_membership_plans( $args = array() ) {
        if ( !$this->is_valid() )
            return array();

        $default_args = array(
            'return'         => 'id',
            'status'         => array( 'active', 'resumed', 'expiring' ), // active statuses
            'sort_by_date'   => false,
            'include_linked' => false,
            'history'        => false,
        );

        $args = wp_parse_args( $args, $default_args );

        $status = (array) $args[ 'status' ];

        $user_plans = $this->get_plans();

        $member_plans = array();

        if ( !empty( $user_plans ) && is_array( $user_plans ) ) {
            foreach ( $user_plans as $plan ) {
                if ( $plan instanceof YITH_WCMBS_Membership ) {
                    if ( in_array( $plan->status, $status ) || $args[ 'status' ] == 'any' ) {
                        if ( $args[ 'return' ] == 'id' ) {
                            $member_plans[] = $plan->plan_id;
                            if ( $args[ 'include_linked' ] ) {
                                $linked_plans_ids = $plan->get_linked_plans();
                                $member_plans     = array_merge( $member_plans, $linked_plans_ids );
                            }

                        } elseif ( $args[ 'return' ] == 'id_date' ) {
                            $member_plans[] = array(
                                'id'   => $plan->plan_id,
                                'date' => $plan->start_date + ( $plan->paused_days * 60 * 60 * 24 ),
                            );
                            if ( $args[ 'include_linked' ] ) {
                                $linked_plans_ids = $plan->get_linked_plans();
                                if ( !empty( $linked_plans_ids ) ) {
                                    foreach ( $linked_plans_ids as $p_id ) {
                                        $member_plans[] = array(
                                            'id'   => $p_id,
                                            'date' => $plan->start_date + ( $plan->paused_days * 60 * 60 * 24 ),
                                        );
                                    }
                                }
                            }

                        } elseif ( $args[ 'return' ] == 'array_complete' ) {
                            $member_plans[ $plan->plan_id ][] = $plan;
                        } elseif ( $args[ 'return' ] == 'complete' ) {
                            $member_plans[] = $plan;
                        } elseif ( $args[ 'return' ] == 'names' ) {
                            $plan_name      = get_the_title( $plan->plan_id );
                            $member_plans[] = $plan_name;
                        }
                    }
                }
            }
        }

        return $member_plans;
    }


    /**
     * control if user has the active plan
     * return true if user has the plan active
     *
     * @param int  $plan_id the id of the plan
     * @param bool $admin_has_access if true and is admin return true
     *
     * @access public
     * @since  1.0.0
     * @return bool
     */
    public function has_active_plan( $plan_id, $admin_has_access = true ) {
        if ( $admin_has_access && user_can( $this->id, 'create_users' ) )
            return true;

        $plans = $this->get_membership_plans( array( 'return' => 'id', 'include_linked' => true ) );

        if ( !empty( $plans ) ) {
            return in_array( $plan_id, $plans );
        }

        return false;
    }


    /**
     * get the oldest plan in base of id
     *
     * @param int $plan_id the id of the plan
     *
     * @access public
     * @since  1.0.0
     * @return bool|YITH_WCMBS_Membership
     */
    public function get_oldest_active_plan( $plan_id ) {
        $plans          = $this->get_membership_plans( array( 'return' => 'complete', ) );
        $oldest_plan    = false;
        $last_plan_date = 0;

        if ( !empty( $plans ) ) {
            foreach ( $plans as $plan ) {
                if ( $plan->plan_id == $plan_id && ( $plan->start_date < $last_plan_date || $last_plan_date == 0 ) ) {
                    $oldest_plan    = $plan;
                    $last_plan_date = $plan->start_date;
                }
            }
        }

        return $oldest_plan;
    }


    /**
     * create a membership for this user
     *
     * @param int   $plan_id the id of the plan
     * @param int   $order_id the id of the order. 0 if the membership is created by admin
     * @param int   $order_item_id the id of the order item. 0 if the membership is created by admin
     * @param array $args
     *
     * @return bool|YITH_WCMBS_Membership
     * @access public
     * @since  1.0.0
     */
    public function create_membership( $plan_id, $order_id = 0, $order_item_id = 0, $args = array() ) {
        $plan_post_type = get_post_type( $plan_id );

        if ( 'yith-wcmbs-plan' === $plan_post_type ) {
            $duration = get_post_meta( $plan_id, '_membership-duration', true );

            /* end date calculation */
            $start_date = time();
            $end_date   = ( $duration <= 0 ) ? 'unlimited' : ( $start_date + ( $duration * 60 * 60 * 24 ) );

            $membership_meta_data = array(
                'plan_id'       => $plan_id,
                'title'         => get_the_title( $plan_id ),
                'start_date'    => $start_date,
                'end_date'      => $end_date,
                'order_id'      => $order_id,
                'order_item_id' => $order_item_id,
                'user_id'       => $this->id,
                'status'        => 'active',
            );

            $membership_meta_data = wp_parse_args( $args, $membership_meta_data );

            /* create the Membership */

            return new YITH_WCMBS_Membership( 0, $membership_meta_data );
        }

        return false;
    }


    /**
     * get membership by subscription id
     *
     * @param int $subscription_id the id of the subscription
     *
     * @access public
     * @since  1.0.0
     * @return bool|YITH_WCMBS_Membership
     */
    public function get_memberships_by_subscription( $subscription_id ) {
        if ( !$this->is_valid() )
            return false;

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'   => '_user_id',
                'value' => $this->id,
            ),
            array(
                'key'   => '_subscription_id',
                'value' => $subscription_id,
            ),
        );

        return YITH_WCMBS_Membership_Helper()->get_memberships_by_meta( $meta_query );
    }

    /**
     * check if user has membership without subscription
     *
     * @access public
     * @since  1.0.0
     * @return bool
     */
    public function has_membership_without_subscription() {
        if ( !$this->is_valid() )
            return false;

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'   => '_user_id',
                'value' => $this->id,
            ),
            array(
                'key'     => '_subscription_id',
                'compare' => 'NOT EXISTS',
            ),
        );

        $memberships = YITH_WCMBS_Membership_Helper()->get_memberships_by_meta( $meta_query, 'posts' );

        return !empty( $memberships ) ? true : false;
    }

    /**
     *
     * check if user has just downloaded the product
     *
     * @param $product_id
     *
     * @return bool
     */
    public function has_just_downloaded_product( $product_id ) {
        if ( !$this->is_valid() )
            return false;

        $count_down = YITH_WCMBS_Downloads_Report()->count_downloads( array(
                                                                          'where'    => array(
                                                                              array(
                                                                                  'key'   => 'user_id',
                                                                                  'value' => $this->id,
                                                                              ),
                                                                              array(
                                                                                  'key'   => 'product_id',
                                                                                  'value' => $product_id,
                                                                              ),
                                                                          ),
                                                                          'distinct' => 'product_id',
                                                                      ) );

        return $count_down > 0;
    }

    /**
     * Get all download product ids for this user
     *
     * @return array
     */
    public function get_download_ids() {
        if ( !$this->is_valid() )
            return array();

        $count_down = YITH_WCMBS_Downloads_Report()->get_download_ids_for_user( $this->id );

        return !!$count_down ? $count_down : array();
    }
}