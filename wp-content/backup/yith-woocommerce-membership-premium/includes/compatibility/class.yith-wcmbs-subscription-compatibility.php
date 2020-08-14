<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Subscription Compatibility Class
 *
 * @class   YITH_WCMBS_Subscription_Compatibility
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Subscription_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Multivendor_Compatibility
     * @since 1.0.0
     */
    protected static $_instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCMBS_Subscription_Compatibility
     * @since 1.0.0
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();

    }

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    protected function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

        add_action( 'ywsbs_subscription_status_changed', array( $this, 'subscription_status_changed' ), 10, 3 );
        add_action( 'ywsbs_subscription_deleted', array( $this, 'subscription_deleted' ), 10, 1 );

        add_action( 'yith_wcmbs_membership_metabox_info_after_second_column', array( $this, 'print_subscription_info' ), 10, 1 );

        /* Activate Membership if Subscription is payed after cancelling */
        add_action( 'ywsbs_no_activated_just_cancelled', array( $this, 'activate_membership_subscription_no_activated_just_cancelled' ) );


        /* Add subscription info in Membership List columns */
        add_filter( 'yith_wcmbs_membership_custom_columns', array( $this, 'manage_membership_list_columns' ) );
        add_action( 'yith_wcmbs_membership_render_custom_columns', array( $this, 'render_membership_list_columns' ), 10, 3 );

        add_action( 'ywsbs_my_subscriptions_view_after', array( $this, 'add_membership_history_in_subscription_view' ) );
        add_filter( 'yith_wcmbs_membership_history_shortcode_in_my_account', array( $this, 'show_only_membership_without_subscription_in_my_account' ), 10, 2 );

        /* Membership with subscription Reports*/
        add_action( 'yith_wcmbs_after_membership_reports', array( $this, 'print_membership_with_subscription_reports' ) );

        /* Add Subscription ID in editable post meta for membership [Advanced Administration]*/
        add_filter( 'yith_wcmbs_advanced_editable_membership_post_meta', array( $this, 'add_advanced_editable_membership_post_meta' ) );

        /* Add Subscription info in membership info html (data tip) */
        add_filter( 'yith_wcmbs_membership_get_plan_info_html', array( $this, 'add_subscription_info_in_membership_info_html' ), 10, 2 );


    }

    /**
     * @param string                $html
     * @param YITH_WCMBS_Membership $membership
     *
     * @return string
     */
    public function add_subscription_info_in_membership_info_html( $html, $membership ) {
        if ( $subscription_id = $membership->subscription_id ) {
            $subscription = new YWSBS_Subscription( $subscription_id );
            if ( $subscription ) {
                $subscription_statuses = ywsbs_get_status();
                $status                = strtr( $subscription->status, $subscription_statuses );

                $subscription_product_name = isset( $subscription->product_name ) ? $subscription->product_name : '';
                $subscription_title        = "#$subscription_id $subscription_product_name";

                $html .= '<hr style="border-width:1px 0 0 0" />';
                $html .= __( 'Related Subscription:', 'yith-woocommerce-membership' );
                $html .= '<br />' . $subscription_title;
                $html .= '<br />' . '( ' . $status . ' )';

                if ( $cancelled_date = $subscription->cancelled_date ) {
                    $formatted_date = date( wc_date_format() . ' ' . wc_time_format(), $cancelled_date );
                    $html .= __( 'Subscription Cancelled Date:', 'yith-woocommerce-membership' ) . '<br />' . $formatted_date . '<br />';
                }
            }
        }

        return $html;
    }

    /**
     * Add Subscription ID in editable post meta for membership [Advanced Administration]
     *
     * @param $editable_post_meta
     */
    public function add_advanced_editable_membership_post_meta( $editable_post_meta ) {
        $editable_post_meta[ 'subscription_id' ] = array(
            'type'  => 'number',
            'label' => __( 'Subscription ID', 'yith-woocommerce-membership' )
        );

        return $editable_post_meta;
    }

    /**
     * Activate Membership if Subscription is payed after cancelling
     *
     * @param YWSBS_Subscription $subscription
     */
    public function activate_membership_subscription_no_activated_just_cancelled( $subscription ) {
        if ( !$subscription instanceof YWSBS_Subscription )
            return;

        $product_id    = !empty( $subscription->variation_id ) ? $subscription->variation_id : $subscription->product_id;
        $user_id       = !empty( $subscription->user_id ) ? $subscription->user_id : false;
        $order_id      = !empty( $subscription->order_id ) ? $subscription->order_id : 0;
        $order_item_id = !empty( $subscription->order_item_id ) ? $subscription->order_item_id : 0;

        $plan_id = YITH_WCMBS_Manager()->get_plan_by_membership_product( $product_id );

        if ( $plan_id && $user_id ) {
            $member = YITH_WCMBS_Members()->get_member( $user_id );

            $end_date = $subscription->end_date;

            $new_membership = $member->create_membership( $plan_id, $order_id, $order_item_id );
            $new_membership->set( 'subscription_id', $subscription->id );
            $new_membership->set( 'end_date', $end_date );

            $additional_note = sprintf( __( 'Expiration date set to %s.', 'yith-woocommerce-membership' ), date_i18n( wc_date_format(), $end_date ) );
            $additional_note .= __( 'Reason: subscription cancelled.', 'yith-woocommerce-membership' );

            $new_membership->update_status( 'expiring', 'change_status', $additional_note, false );

        }

    }

    public function show_only_membership_without_subscription_in_my_account( $shortcode, $title ) {
        return do_shortcode( '[membership_history title="' . $title . '" type="membership"]' );
    }

    public function add_membership_history_in_subscription_view() {
        $title = __( 'Memberships with subscription', 'yith-woocommerce-membership' );
        echo do_shortcode( '[membership_history title="' . $title . '" type="subscription"]' );
    }

    /**
     * Set memberhip status to cancelled if associated subscription is deleted
     *
     * @param int $subscription_id id of the subscription
     *
     * @access public
     * @since  1.0.0
     */
    public function subscription_deleted( $subscription_id ) {
        $memberships = YITH_WCMBS_Membership_Helper()->get_memberships_by_subscription( $subscription_id );

        $additional_note = sprintf( __( 'Reason: subscription #%d deleted.', 'yith-woocommerce-membership' ), $subscription_id );

        if ( !empty( $memberships ) ) {
            foreach ( $memberships as $membership ) {
                if ( $membership instanceof YITH_WCMBS_Membership ) {
                    $membership->update_status( 'cancelled', 'change_status', $additional_note );
                }
            }
        }


    }

    /**
     * Print subscription info in Metabox of Membership
     *
     * @param YITH_WCMBS_Membership $membership the membership
     *
     * @access public
     * @since  1.0.0
     */
    public function print_subscription_info( $membership ) {
        if ( $membership->subscription_id > 0 ) {
            ?>
            <h4><?php _e( 'Subscription details', 'yith-woocommerce-membership' ) ?></h4>

            <p class="field_info"><label><strong><?php _e( 'Subscription ID', 'yith-woocommerce-membership' ) ?>:</strong></label>
                <?php
                $edit_link = get_edit_post_link( $membership->subscription_id );
                echo "<a href='{$edit_link}'>#{$membership->subscription_id}</a>";
                ?>
            </p>

            <?php
        }
    }

    /**
     * action triggered when a subscription changes status
     *
     * @param int    $subscription_id the id of the subscription
     * @param string $old_status the status before change
     * @param string $new_status the status after change
     *
     * @access public
     * @since  1.0.0
     *
     * @return void
     */
    public function subscription_status_changed( $subscription_id, $old_status, $new_status ) {
        $subscription  = new YWSBS_Subscription( $subscription_id );
        $product_id    = !empty( $subscription->variation_id ) ? $subscription->variation_id : $subscription->product_id;
        $user_id       = !empty( $subscription->user_id ) ? $subscription->user_id : false;
        $order_id      = !empty( $subscription->order_id ) ? $subscription->order_id : 0;
        $order_item_id = !empty( $subscription->order_item_id ) ? $subscription->order_item_id : 0;

        $plan_id = YITH_WCMBS_Manager()->get_plan_by_membership_product( $product_id );

        $allowed = apply_filters( 'yith_wcmb_allow_status_management_by_subscription', true, $old_status, $new_status, $subscription_id, $plan_id );

        if ( $allowed && $plan_id && $user_id && ( $old_status != $new_status || $new_status == 'cancelled' ) ) {
            $member = YITH_WCMBS_Members()->get_member( $user_id );

            switch ( $new_status ) {

                case 'trial':
                    $new_membership = $member->create_membership( $plan_id, $order_id, $order_item_id );
                    $new_membership->set( 'subscription_id', $subscription_id );
                    break;
                case 'active':
                    if ( !in_array( $old_status, array( 'trial', 'overdue', 'suspended' ) ) ) {
                        $new_membership = $member->create_membership( $plan_id, $order_id, $order_item_id, array( 'subscription_id' => $subscription_id ) );
                    } else if ( in_array( $old_status, array( 'overdue', 'suspended' ) ) ) {

                        $memberships = $member->get_memberships_by_subscription( $subscription_id );
                        if ( !empty( $memberships ) ) {
                            foreach ( $memberships as $membership ) {
                                if ( $membership instanceof YITH_WCMBS_Membership ) {
                                    $membership->update_status( 'resumed' );
                                }
                            }
                        }

                    }
                    break;

                case 'resume':
                    $memberships = $member->get_memberships_by_subscription( $subscription_id );
                    if ( !empty( $memberships ) ) {
                        foreach ( $memberships as $membership ) {
                            if ( $membership instanceof YITH_WCMBS_Membership ) {
                                $membership->update_status( 'resumed' );
                            }
                        }
                    }
                    break;
                case 'paused':
                    $memberships = $member->get_memberships_by_subscription( $subscription_id );
                    if ( !empty( $memberships ) ) {
                        foreach ( $memberships as $membership ) {
                            if ( $membership instanceof YITH_WCMBS_Membership ) {
                                $membership->update_status( 'paused' );
                            }
                        }
                    }
                    break;
                case 'suspended':
                case 'overdue':
                    $memberships = $member->get_memberships_by_subscription( $subscription_id );
                    if ( !empty( $memberships ) ) {
                        foreach ( $memberships as $membership ) {
                            if ( $membership instanceof YITH_WCMBS_Membership ) {
                                $membership->update_status( 'not_active' );
                            }
                        }
                    }
                    break;

                case 'cancelled':
                    if ( $subscription->cancelled_date == $subscription->end_date ) {
                        // subscription direct cancelling [by gateway]
                        $memberships = $member->get_memberships_by_subscription( $subscription_id );
                        if ( !empty( $memberships ) ) {
                            foreach ( $memberships as $membership ) {
                                if ( $membership instanceof YITH_WCMBS_Membership ) {
                                    $membership->update_status( 'cancelled' );
                                }
                            }
                        }
                    } else {
                        // subscription cancelling postponed => membership->end_date = subscription->end_date AND membership->paused_days = 0
                        $memberships = $member->get_memberships_by_subscription( $subscription_id );
                        if ( !empty( $memberships ) ) {
                            foreach ( $memberships as $membership ) {
                                if ( $membership instanceof YITH_WCMBS_Membership ) {
                                    $new_end_date = $subscription->end_date;
                                    $membership->set( 'end_date', $new_end_date );
                                    $membership->set( 'paused_days', 0 );

                                    $additional_note = sprintf( __( 'Expiration date set to %s.', 'yith-woocommerce-membership' ), date_i18n( wc_date_format(), $new_end_date ) );
                                    $additional_note .= __( 'Reason: subscription cancelled.', 'yith-woocommerce-membership' );

                                    $membership->update_status( 'expiring', 'change_status', $additional_note );
                                }
                            }
                        }
                    }
                    break;
                case 'expired' :
                    $memberships = $member->get_memberships_by_subscription( $subscription_id );
                    if ( !empty( $memberships ) ) {
                        foreach ( $memberships as $membership ) {
                            if ( $membership instanceof YITH_WCMBS_Membership ) {
                                $membership->update_status( 'expired' );
                            }
                        }
                    }
                    break;
            }
        }
    }


    /**
     * Manage columns column in Membership List
     *
     * @access public
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    public function manage_membership_list_columns( $custom_columns ) {
        $custom_columns[ 'subscription' ] = __( 'Subscription', 'yith-woocommerce-membership' );

        return $custom_columns;
    }

    /**
     * Render columns in Membership List
     *
     * @param string                $column column name
     * @param int                   $post_id the post id
     * @param YITH_WCMBS_Membership $membership the membership
     *
     * @access public
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    public function render_membership_list_columns( $column, $post_id, $membership ) {
        if ( $column == 'subscription' && $membership->has_subscription() ) {
            $subscription_id   = $membership->subscription_id;
            $subscription_link = get_edit_post_link( $subscription_id );

            echo "<a href='{$subscription_link}'>#{$subscription_id}</a>";

            $status = get_post_meta( $subscription_id, 'status', true );
            echo '<span class="yith-wcmbs-subscription-status ' . $status . '">' . $status . '</span>';
        }
    }

    /**
     * Membership with Subscription in Reports
     */
    public function print_membership_with_subscription_reports() {
        wc_get_template( '/reports/membership-with-subscription-reports.php', array(), '', YITH_WCMBS_TEMPLATE_PATH );
    }

    public function admin_enqueue_scripts() {
        wp_enqueue_style( 'yith-wcmbs-subscription-styles', YITH_WCMBS_ASSETS_URL . '/css/subscription.css' );
    }
}

/**
 * Unique access to instance of YITH_WCMBS_Subscription_Compatibility class
 *
 * @return YITH_WCMBS_Subscription_Compatibility
 * @since 1.0.0
 */
function YITH_WCMBS_Subscription_Compatibility() {
    return YITH_WCMBS_Subscription_Compatibility::get_instance();
}