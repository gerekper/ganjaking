<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Member Class
 *
 * @class   YITH_WCMBS_Membership
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 */
class YITH_WCMBS_Membership {

    /**
     * id of membership
     *
     * @var int
     * @since 1.0.0
     */
    public $id;

    /**
     * post of membership
     *
     * @var WP_Post|bool
     * @since 1.0.0
     */
    public $post;

    /**
     * Constructor
     *
     * @param int   $membership_id the membership id
     * @param array $args          array of meta for creating membership
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    public function __construct( $membership_id = 0, $args = array() ) {
        $notify = true;
        //populate the membership if $membership_id is defined
        if ( $membership_id ) {
            $this->id = $membership_id;
            $this->populate();
        }

        //add a new membership if $args is passed
        if ( $membership_id == 0 && !empty( $args ) ) {
            $this->add_membership( $args );
            // check and loads credits
            $this->check_credits( true );
            $notify = false;
        }

        // check if status is expired or in expiring
        $this->check_is_expiring( $notify );
        $this->check_is_expired( $notify );
    }

    /**
     * __get function.
     *
     * @param string $key
     * @return mixed
     */
    public function __get( $key ) {
        $value = get_post_meta( $this->id, '_' . $key, true );

        if ( !empty( $value ) ) {
            $this->$key = $value;
        }

        return $value;
    }

    /**
     * __set function.
     *
     * @param string $property
     * @param mixed  $value
     * @return bool|int
     */
    public function set( $property, $value ) {
        $this->$property = $value;

        return update_post_meta( $this->id, '_' . $property, $value );
    }

    /**
     * Populate the membership
     *
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com
     * @return void
     */
    public function populate() {

        $this->post = get_post( $this->id );

        foreach ( $this->get_membership_meta() as $key => $value ) {
            $this->$key = $value;
        }

        do_action( 'yith_wcmbs_membership_loaded', $this );
    }

    /**
     * Check if the Membership is valid, controlling if this post exist
     *
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com
     * @return bool
     */
    public function is_valid() {
        return !!$this->post;
    }

    /**
     * Add new membership
     *
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com
     * @return void
     */
    public function add_membership( $args ) {

        $plan_title = isset( $args[ 'title' ] ) ? $args[ 'title' ] : '';

        $membership_id = wp_insert_post( array(
                                             'post_status' => 'publish',
                                             'post_type'   => 'ywcmbs-membership',
                                             'post_title'  => $plan_title
                                         ) );

        if ( $membership_id ) {
            $this->id = $membership_id;
            $meta     = wp_parse_args( $args, $this->get_default_meta_data() );
            $this->update_membership_meta( $meta );
            $this->populate();

            do_action( 'yith_wcmbs_membership_created', $this );

            $this->add_activity( 'new', $this->status, __( 'Membership successfully created.', 'yith-woocommerce-membership' ) );

            $this->notify( 'new_member' );
        }

        do_action( 'yith_wcmbs_delete_transients' );
    }

    /**
     * Update post meta in membership
     *
     * @param array $meta the meta
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com
     * @return void
     */
    function update_membership_meta( $meta ) {
        foreach ( $meta as $key => $value ) {
            update_post_meta( $this->id, '_' . $key, $value );
        }
    }

    /**
     * Updates status of membership
     *
     * @param string $new_status
     * @param string $activity
     * @param string $additional_note
     * @param bool   $notify
     */
    public function update_status( $new_status, $activity = 'change_status', $additional_note = '', $notify = true ) {
        if ( !$this->id ) {
            return;
        }

        $old_status = $this->status;

        // cannot update status if it's expired or cancelled
        if ( in_array( $old_status, array( 'expired', 'cancelled' ) ) )
            return;

        $allowed = apply_filters( 'yith_wcmb_update_membership_status_allowed', true, $old_status, $new_status, $activity, $additional_note, $notify );
        if ( !$allowed )
            return;

        if ( $new_status !== $old_status || !in_array( $new_status, array_keys( yith_wcmbs_get_membership_statuses() ) ) ) {

            // Status was changed
            do_action( 'yith_wcmbs_membership_status_' . $new_status, $this->id, $this );
            do_action( 'yith_wcmbs_membership_status_' . $old_status . '_to_' . $new_status, $this->id, $this );
            do_action( 'yith_wcmbs_membership_status_changed', $this->id, $old_status, $new_status, $this );

            switch ( $new_status ) {
                case 'active' :
                    // Update the membership status
                    $this->set( 'status', $new_status );
                    $note = __( 'Membership has now been activated.', 'yith-woocommerce-membership' ) . ' ' . $additional_note;
                    $this->add_activity( $activity, $new_status, $note );
                    break;

                case 'paused' :
                    if ( !$this->can_be_paused() )
                        return;
                    // Update the membership status
                    $this->set( 'status', $new_status );
                    $note = __( 'Membership paused.', 'yith-woocommerce-membership' ) . ' ' . $additional_note;
                    $this->add_activity( $activity, $new_status, $note );
                    break;
                case 'resumed' :
                    if ( !$this->can_be_resumed() )
                        return;

                    $new_end_date  = '';
                    $last_activity = $this->get_last_activity();

                    // calculate and set paused days
                    $paused_days_in_sec = time() - $last_activity->timestamp;
                    $paused_days        = intval( ( $paused_days_in_sec ) / ( 24 * 60 * 60 ) );
                    $paused_days_tot    = $paused_days + $this->paused_days;
                    $this->set( 'paused_days', $paused_days_tot );

                    // update expiring date
                    if ( !$this->is_unlimited() ) {
                        $new_end_date = $this->end_date + $paused_days_in_sec;
                        $this->set( 'end_date', $new_end_date );
                        $this->set( 'paused_days', 0 );
                    }

                    // Update the membership status
                    $this->set( 'status', 'resumed' );
                    $resumed_note = '';
                    if ( !empty( $new_end_date ) ) {
                        $resumed_note = __( 'Membership resumed.', 'yith-woocommerce-membership' ) . sprintf( __( 'Expiration date set to %s.', 'yith-woocommerce-membership' ), date_i18n( wc_date_format(), $new_end_date ) );
                    } else {
                        $resumed_note = __( 'Membership resumed.', 'yith-woocommerce-membership' );
                    }
                    $note = $resumed_note . ' ' . $additional_note;
                    $this->add_activity( $activity, $new_status, $note );

                    break;
                case 'cancelled' :
                    if ( !$this->can_be_cancelled() )
                        return;
                    $this->set( 'status', $new_status );
                    $cancelled_note = sprintf( __( 'Membership status updated to %s.', 'yith-woocommerce-membership' ), strtr( $new_status, yith_wcmbs_get_membership_statuses() ) );
                    $note           = $cancelled_note . ' ' . $additional_note;
                    $this->add_activity( $activity, $new_status, $note );
                    break;
                default:
                    $this->set( 'status', $new_status );
                    $update_status_note = sprintf( __( 'Membership status updated to %s.', 'yith-woocommerce-membership' ), strtr( $new_status, yith_wcmbs_get_membership_statuses() ) );
                    $note               = $update_status_note . ' ' . $additional_note;
                    $this->add_activity( $activity, $new_status, $note );
                    break;
            }

            if ( $notify )
                $this->notify( 'status_changed' );
        }

        // check if status is expired or in expiring
        $this->check_is_expiring();
        $this->check_is_expired();

        do_action( 'yith_wcmbs_delete_transients' );
    }

    /**
     * send email when status changed
     *
     * @param string $type type of notification. it can be: status_changed | new_member
     */
    public function notify( $type ) {
        $notification_args = array(
            'user_id'    => $this->user_id,
            'membership' => $this
        );

        $notify = apply_filters( 'yith_wcmbs_membership_notify', true, $this, $type );

        if ( $notify ) {
            switch ( $type ) {
                case 'status_changed':
                    $allowed_status_changed_notifier = array( 'cancelled', 'expiring', 'expired' );
                    if ( in_array( $this->status, $allowed_status_changed_notifier ) ) {
                        $mailer = WC()->mailer();
                        do_action( 'yith_wcmbs_membership_' . $this->status . '_notification', $notification_args );
                    }
                    break;
                case 'new_member':
                    $mailer = WC()->mailer();
                    do_action( 'yith_wcmbs_new_member_notification', $notification_args );
                    break;
                default:
                    do_action( 'yith_wcmbs_' . $type . '_notification', $notification_args );
            }
        }
    }

    /**
     * Fill the default metadata with the post meta stored in db
     *
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com
     * @return array
     */
    function get_membership_meta() {
        $membership_meta = array();
        foreach ( $this->get_default_meta_data() as $key => $value ) {
            $membership_meta[ $key ] = get_post_meta( $this->id, '_' . $key, true );
        }

        return $membership_meta;
    }


    /**
     * Return an array of all custom fields membership
     *
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com
     * @return array
     */
    private function get_default_meta_data() {
        $membership_meta_data = array(
            'plan_id'             => 0,
            'title'               => '',
            'start_date'          => '',
            'end_date'            => '',
            'order_id'            => 0,
            'order_item_id'       => 0,
            'user_id'             => 0,
            'status'              => 'active',
            'paused_days'         => 0,
            'activities'          => array(),
            'credits'             => -1,
            'credits_update'      => 0,
            'next_credits_update' => 0
        );

        return $membership_meta_data;
    }

    /**
     * check and load credits
     *
     * @param bool $first_activation
     */
    public function check_credits( $first_activation = false ) {
        if ( !function_exists( 'YITH_WCMBS_Products_Manager' ) )
            return;

        if ( YITH_WCMBS_Products_Manager()->is_allowed_download() && $this->is_active() ) {
            $download_limit = get_post_meta( $this->plan_id, '_download-limit', true );
            if ( $download_limit == 0 )
                return;

            /* Get initial credits if this is the first activation */
            if ( $first_activation ) {
                $initial_download_limit = get_post_meta( $this->plan_id, '_initial-download-limit', true );
                if ( $initial_download_limit === '' || $initial_download_limit == -1 ) {
                    /* the initial credits are equals to credits */
                } else {
                    $download_limit = $initial_download_limit;
                }
            }

            $today = strtotime( 'now midnight' );
            //$next_update = $this->get_next_update_credits_date();
            $calculated_next_update = $this->get_next_update_credits_date();
            $next_update            = $this->next_credits_update;
            if ( $calculated_next_update != $next_update ) {
                $this->set( 'next_credits_update', $calculated_next_update );
                $next_update = $calculated_next_update;
            }

            if ( $next_update <= $today ) {
                $new_credits = $download_limit;
                if ( $this->get_remaining_credits() != -1 ) {
                    $can_be_accumulated = get_post_meta( $this->plan_id, '_can-be-accumulated', true );
                    if ( $can_be_accumulated ) {
                        $new_credits = $download_limit + $this->get_remaining_credits();
                    }
                }
                $this->set( 'credits', $new_credits );
                $this->set( 'credits_update', $today );
                $this->set( 'next_credits_update', $this->get_next_update_credits_date() );
            }
        }
    }

    public function get_next_update_credits_date() {
        $download_limit_period      = get_post_meta( $this->plan_id, '_download-limit-period', true );
        $download_limit_period_unit = get_post_meta( $this->plan_id, '_download-limit-period-unit', true );

        if ( $this->credits_update ) {
            $next_update = strtotime( '+' . $download_limit_period . $download_limit_period_unit, $this->credits_update );
        } else {
            $next_update = strtotime( 'now midnight' );
        }

        return $next_update;
    }

    /**
     * check if this membership has credits management
     */
    public function has_credit_management() {
        return $this->get_remaining_credits() >= 0;
    }

    /**
     * get the remaining credits
     *
     * @return int
     */
    public function get_remaining_credits() {
        return is_numeric( $this->credits ) ? $this->credits : -1;
    }

    /**
     * remove credit
     *
     * @param int $credits number of credits to remove
     */
    public function remove_credit( $credits = 1 ) {
        $remaining_credits = $this->credits - $credits;
        $remaining_credits = absint( $remaining_credits );
        $this->set( 'credits', $remaining_credits );
    }

    /**
     * Add Activity to membership
     *
     * @param string $activity
     * @param string $status
     * @param string $note
     * @access public
     * @since  1.0.0
     */
    public function add_activity( $activity, $status, $note = '' ) {
        $timestamp = time();

        $act = new YITH_WCMBS_Activity( $activity, $status, $timestamp, $note );

        $activities   = $this->get_activities();
        $activities[] = $act;
        $this->set( 'activities', $activities );
    }

    /**
     * Set the end date in base of duration
     *
     * @param int $duration
     * @access public
     * @since  1.0.0
     */
    public function set_end_date( $duration ) {
        if ( $duration < 1 ) {
            $this->set( 'end_date', 'unlimited' );
        } else {
            $this->set( 'end_date', $duration + $this->start_date );
        }
    }


    /**
     * Get the last timestamp date in activities
     *
     * @access public
     * @since  1.0.0
     * @return string|bool
     */
    public function get_last_timestamp_date() {
        $last_activity = $this->get_last_activity();

        return ( $last_activity ) ? $last_activity->timestamp : false;
    }

    /**
     * get activities
     *
     * @return array
     */
    public function get_activities() {
        return is_array( $this->activities ) ? $this->activities : array();
    }

    /**
     * Get the last activity
     *
     * @access public
     * @since  1.0.0
     * @return YITH_WCMBS_Activity
     */
    public function get_last_activity() {
        $activities = $this->get_activities();
        return end( $activities );
    }

    /**
     * Get the expire date, considering paused_days
     *
     * @access public
     * @since  1.0.0
     */
    public function get_expire_date() {
        /*if ( !$this->is_unlimited() && $this->paused_days > 0 ) {
            return ( $this->end_date + ( $this->paused_days * 60 * 60 * 24 ) );
        }*/

        return $this->end_date;
    }


    /**
     * Return html containing the start and expiration dates
     *
     * @access public
     * @since  1.0.0
     * @return string
     */
    public function get_dates_html() {
        $data = __( 'Starting Date', 'yith-woocommerce-membership' ) . ':<br />' . $this->get_formatted_date( 'start_date' ) . '<br />';
        $data .= __( 'Expiration Date', 'yith-woocommerce-membership' ) . ':<br />' . $this->get_formatted_date( 'end_date' ) . '<br />';

        return $data;
    }

    /**
     * Return html containing all info about plan
     *
     * @access public
     * @since  1.0.0
     * @return string
     */
    public function get_plan_info_html() {
        $html = $this->get_dates_html();
        $html .= __( 'Status', 'yith-woocommerce-membership' ) . ':<br />' . $this->get_status_text() . '<br />';

        return apply_filters( 'yith_wcmbs_membership_get_plan_info_html', $html, $this );
    }


    /**
     * Return html containing membership info span
     *
     * @access public
     * @since  1.0.0
     * @return string
     */
    public function get_plan_info_span() {
        $p_name          = $this->get_plan_title();
        $p_info          = $this->get_plan_info_html();
        $p_edit_url      = get_edit_post_link( $this->id );
        $membership_info = "<span class='yith-wcmbs-users-membership-info {$this->status}'>{$p_name}";
        $membership_info .= "<span class='dashicons dashicons-info tips' data-tip='{$p_info}'></span>";
        if ( defined( 'YITH_WCMBS_PREMIUM' ) && YITH_WCMBS_PREMIUM )
            $membership_info .= "<a href='$p_edit_url' target='_blank'><span class='dashicons dashicons-edit'></span></a>";
        $membership_info .= '</span>';

        return $membership_info;
    }

    /**
     * Return string for status
     *
     * @access public
     * @since  1.0.0
     * @return string
     */
    public function get_status_text() {
        $text = strtr( $this->status, yith_wcmbs_get_membership_statuses() );

        return $text;
    }

    /**
     * Return string for dates
     *
     * @param string $date_type the type of date
     * @param bool   $with_time if it's true include time in date format
     * @access public
     * @since  1.0.0
     * @return string
     */
    public function get_formatted_date( $date_type, $with_time = false ) {
        $format = wc_date_format();
        $format .= $with_time ? ( ' ' . wc_time_format() ) : '';

        $date = '';

        switch ( $date_type ) {
            case 'end_date':
                if ( $this->is_unlimited() )
                    return __( 'Unlimited', 'yith-woocommerce-membership' );

                $date = $this->get_expire_date();
                break;
            case 'last_update':
                $date = $this->get_last_timestamp_date();
                break;
            default:
                $date = $this->$date_type;
                break;
        }

        if ( !is_numeric( $date ) )
            return '';

        $date   = intval( $date );
        $offset = get_option( 'gmt_offset' );
        $date   += $offset * HOUR_IN_SECONDS;

        return date_i18n( $format, $date );
    }

    /**
     * get the linked plans ids
     * return false if the plan don't have linked plans
     *
     * @access public
     * @since  1.0.0
     * @return array
     */
    public function get_linked_plans() {
        $linked_plans = get_post_meta( $this->plan_id, '_linked-plans', true );

        return !empty( $linked_plans ) ? $linked_plans : array();
    }

    /**
     * Return true if status is active, resumed or expiring
     *
     * @return bool
     * @access public
     * @since  1.0.0
     */
    public function is_active() {
        return in_array( $this->status, array( 'active', 'resumed', 'expiring' ) );
    }

    /**
     * Return true if membership is unlimited
     *
     * @return bool
     * @access public
     * @since  1.0.0
     */
    public function is_unlimited() {
        return $this->end_date == 'unlimited';
    }

    /**
     * Check if this is in expired
     *
     * @param bool $notify
     * @return void
     * @access public
     * @since  1.0.0
     */
    public function check_is_expired( $notify = true ) {
        if ( in_array( $this->status, array( 'active', 'resumed', 'expiring' ) ) && !$this->is_unlimited() ) {
            if ( $this->get_remaining_days() <= 0 ) {
                $this->update_status( 'expired', 'change_status', '', $notify );
            }
        }
    }

    /**
     * Check if this is in expiring
     *
     * @param bool $notify
     * @return void
     * @access public
     * @since  1.0.0
     */
    public function check_is_expiring( $notify = true ) {
        if ( in_array( $this->status, array( 'active', 'resumed' ) ) && !$this->is_unlimited() ) {
            if ( $this->get_remaining_days() <= apply_filters( 'yith_wcmbs_membership_max_days_number_to_send_expiring_email', 10 ) ) {
                $this->update_status( 'expiring', 'change_status', '', $notify );
            }
        }
    }


    /**
     * Return the remaining days
     *
     * @return int
     * @access public
     * @since  1.0.0
     */
    public function get_remaining_days() {
        if ( $this->is_unlimited() ) {
            $remaining_days = -1;
        } else {
            $remaining_days = ( strtotime( 'midnight', $this->get_expire_date() ) - strtotime( 'midnight' ) ) / ( 60 * 60 * 24 );
            $remaining_days = ( $remaining_days > 0 ) ? absint( $remaining_days ) : 0;
        }

        return apply_filters( 'yith_wcmbs_membership_get_remaining_days', $remaining_days, $this );
    }

    /**
     * return true if the membership can be cancelled
     *
     * @return bool
     * @access public
     * @since  1.0.0
     */
    public function can_be_cancelled() {
        return !in_array( $this->status, array( 'expired', 'cancelled' ) );
    }

    /**
     * return true if the membership can be paused
     *
     * @return bool
     * @access public
     * @since  1.0.0
     */
    public function can_be_paused() {
        return $this->is_active();
    }

    /**
     * return true if the membership can be resumed
     *
     * @return bool
     * @access public
     * @since  1.0.0
     */
    public function can_be_resumed() {
        return in_array( $this->status, array( 'not_active', 'paused' ) );
    }

    /**
     * get the current name of plan
     *
     * @return string
     * @access public
     * @since  1.0.0
     */
    public function get_plan_title() {
        $title = get_the_title( $this->plan_id );
        if ( empty( $title ) ) {
            $title = $this->title;
        }

        return apply_filters( 'yith_wcmbs_membership_get_plan_title', $title, $this );
    }

    /**
     * control if thi membership has subscription plan linked
     *
     * @return bool
     * @access public
     * @since  1.0.0
     */
    public function has_subscription() {
        $subscription_id = $this->subscription_id;

        return !empty( $subscription_id );
    }

    /**
     * Get products in this membership
     * include linked plans
     *
     * @param array $args              {
     *                                 Optional Arguments to retrieve products
     * @type string $return            the type of return values. Allowed 'ids', 'posts', 'products'
     * @type bool   $only_downloadable do you want retrieve only downloadable products?
     *                                 }
     * @return int[]|WC_Product[]|WP_Post[] List of products ids or product objects or post objects
     * @access public
     * @since  1.0.0
     */
    public function get_products( $args = array() ) {
        $default_args = array(
            'return'            => 'ids',
            'only_downloadable' => apply_filters( 'yith_wcmbs_membership_default_only_downloadable', false ),
        );

        $args              = wp_parse_args( $args, $default_args );
        $return            = 'ids';
        $only_downloadable = false;
        extract( $args );

        $plan_ids   = $this->get_linked_plans();
        $plan_ids[] = $this->plan_id;

        $products = array();
        // get products in plan
        foreach ( $plan_ids as $plan_id ) {
            $args = array(
                'post_type'                  => 'product',
                'posts_per_page'             => -1,
                'post_status'                => 'publish',
                'yith_wcmbs_suppress_filter' => true,
                'meta_query'                 => array(
                    array(
                        'key'     => '_yith_wcmbs_restrict_access_plan',
                        'value'   => $plan_id,
                        'compare' => 'LIKE',
                    )
                ),
            );

            $products = array_unique( array_merge( $products, get_posts( $args ) ), SORT_REGULAR );
        }

        foreach ( $plan_ids as $plan_id ) {
            $plan_cats      = get_post_meta( $plan_id, '_product-cats', true );
            $plan_prod_tags = get_post_meta( $plan_id, '_product-tags', true );

            $cat_tag_args = array(
                'post_type'                  => 'product',
                'posts_per_page'             => -1,
                'post_status'                => 'publish',
                'yith_wcmbs_suppress_filter' => true,
                'tax_query'                  => array(
                    'relation' => 'OR',
                    array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $plan_cats,
                        'operator' => 'IN'
                    ),
                    array(
                        'taxonomy' => 'product_tag',
                        'field'    => 'term_id',
                        'terms'    => $plan_prod_tags,
                        'operator' => 'IN'
                    )
                ),
            );
            $products     = array_unique( array_merge( $products, get_posts( $cat_tag_args ) ), SORT_REGULAR );
        }

        $r = array();
        if ( !empty( $products ) ) {
            foreach ( $products as $product_post ) {
                $product = wc_get_product( $product_post->ID );

                $delay = get_post_meta( $product_post->ID, '_yith_wcmbs_plan_delay', true );
                $delay = !$delay ? array() : $delay;

                $plans_delay_intersect = array_intersect( $plan_ids, array_keys( $delay ) );

                if ( !empty( $delay ) && !empty( $plans_delay_intersect ) ) {

                    // get the minimum delay [between linked plans]
                    $delay_for_plans = 0;
                    if ( isset( $delay[ $this->plan_id ] ) ) {
                        $delay_for_plans = $delay[ $this->plan_id ];
                    } else {
                        $first = true;
                        foreach ( $plan_ids as $plan_id ) {
                            if ( $first ) {
                                if ( isset( $delay[ $plan_id ] ) ) {
                                    $delay_for_plans = $delay[ $plan_id ];
                                    $first           = false;
                                }
                            } else {
                                if ( isset( $delay[ $plan_id ] ) && $delay_for_plans > $delay[ $plan_id ] ) {
                                    $delay_for_plans = $delay[ $plan_id ];
                                }
                            }
                        }
                    }

                    if ( $delay_for_plans > 0 ) {
                        $delay_days = $delay_for_plans;
                        $date       = $this->start_date + ( $this->paused_days * 60 * 60 * 24 );

                        $passed_days = intval( ( time() - $date ) / ( 24 * 60 * 60 ) );
                        if ( $passed_days <= $delay_days )
                            continue;
                    }
                }

                if ( $product ) {
                    $downloadable = false;
                    if ( !$product->is_type( 'variable' ) ) {
                        if ( $product->is_downloadable() ) {
                            $downloadable = true;
                        }
                    } else {
                        $variations = $product->get_children();
                        if ( !empty( $variations ) ) {
                            foreach ( $variations as $variation ) {
                                $p_tmp = wc_get_product( $variation );
                                if ( $p_tmp->is_downloadable() ) {
                                    $downloadable = true;
                                    break;
                                }
                            }
                        }
                    }

                    // add ONLY Downloadable Products
                    if ( !$only_downloadable || $downloadable ) {
                        switch ( $return ) {
                            case 'ids':
                                $r[] = $product_post->ID;
                                break;
                            case 'products':
                                $r[] = $product;
                                break;
                            case 'posts':
                                $r[] = $product_post;
                                break;
                        }
                    }
                }
            }
        }

        return $r;
    }

}