<?php
/*
 * Template for Membership with Subscripion Reports
 */

$membership_with_subscription_args = array(
    'only_subscription' => true,
);

?>
<div class="postbox yith-wcmbs-reports-metabox opened">
    <h2><span><?php _e( 'Membership with Subscription', 'yith-woocommerce-membership' ) ?></span></h2>

    <div class="yith-wcmbs-reports-content">
        <div class="yith-wcmbs-reports-big-number"><?php
            $active_membership_with_subscrption_count = YITH_WCMBS_Membership_Helper()->get_count_membership_with_status( array( 'active', 'resumed' ), $membership_with_subscription_args );
            echo $active_membership_with_subscrption_count;
            ?>
        </div>
        <div class="yith-wcmbs-reports-subtitle"><span class='yith-wcmbs-membership-status active yith-wcmbs-membership-status-with-margin'><?php _e('active + resumed', 'yith-woocommerce-membership')?></span></div>
    </div>

    <div class="yith-wcmbs-reports-content">
        <?php
        $membership_statuses = yith_wcmbs_get_membership_statuses();

        foreach ( $membership_statuses as $current_status => $current_status_label ) {
            $current_status_count = YITH_WCMBS_Membership_Helper()->get_count_membership_with_status( $current_status, $membership_with_subscription_args );
            if ( $current_status_count > 0 )
                echo "<span class='yith-wcmbs-membership-status $current_status yith-wcmbs-membership-status-with-margin'>$current_status_label: $current_status_count</span>";
        }
        ?>
    </div>

</div>