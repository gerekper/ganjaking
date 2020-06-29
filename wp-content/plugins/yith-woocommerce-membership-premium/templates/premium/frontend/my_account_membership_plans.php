<?php
/*
 * Template for Membership Plans in frontend
 */

/**
 * @var YITH_WCMBS_Membership $membership
 */
?>

<?php
if ( !empty( $title ) ) {
    echo "<h2>{$title}</h2>";
}
if ( !empty( $user_plans ) ) {
    ?>
    <div class="yith-wcmbs-my-account-accordion">
        <?php

        $details_titles = array(
            'starting-date'       => __( 'Starting Date', 'yith-woocommerce-membership' ),
            'expiration-date'     => __( 'Expiration Date', 'yith-woocommerce-membership' ),
            'status'              => __( 'Status', 'yith-woocommerce-membership' ),
            'remaining-credits'   => __( 'Remaining Credits', 'yith-woocommerce-membership' ),
            'next-credits-update' => __( 'Next credits update', 'yith-woocommerce-membership' ),
        );

        foreach ( $user_plans as $plan_id => $membership ) {
            $display_content_in_plan = get_post_meta( $membership->plan_id, '_show-contents-in-my-account', true );
            $display_content_in_plan = $display_content_in_plan == true;

            $key   = 'yith_wcmbs_membership_plans[' . $membership->plan_id . ']';
            $label = $membership->get_plan_title();
            ?>
            <h3><?php echo esc_html( $label ); ?></h3>
            <div class="yith-wcmbs-my-account-membership-container">

                <div class="yith-wcmbs-my-account-membership-status-container">
                    <table class="yith-wcmbs-membership-table shop_table_responsive">
                        <thead>
                        <tr>
                            <th class="yith-wcmbs-my-account-membership-status-starting-date-column"><?php echo $details_titles[ 'starting-date' ] ?></th>
                            <th class="yith-wcmbs-my-account-membership-status-expiration-date-column"><?php echo $details_titles[ 'expiration-date' ] ?></th>
                            <th class="yith-wcmbs-my-account-membership-status-status-column"><?php echo $details_titles[ 'status' ] ?></th>
                            <?php if ( $membership->has_credit_management() ) : ?>
                                <th class="yith-wcmbs-my-account-membership-status-remaining-credits-column"><?php echo $details_titles[ 'remaining-credits' ] ?></th>
                                <th class="yith-wcmbs-my-account-membership-status-next-credits-update-column"><?php echo $details_titles[ 'next-credits-update' ] ?></th>
                            <?php endif ?>
                        </tr>
                        </thead>
                        <tr>
                            <td class="yith-wcmbs-my-account-membership-status-starting-date-column" data-title="<?php echo $details_titles[ 'starting-date' ] ?>"><?php echo $membership->get_formatted_date( 'start_date' ) ?></td>
                            <td class="yith-wcmbs-my-account-membership-status-expiration-date-column" data-title="<?php echo $details_titles[ 'expiration-date' ] ?>"><?php
                                $expiration = ( $membership->end_date == 'unlimited' ) ? __( 'Unlimited', 'yith-woocommerce-membership' ) : $membership->get_formatted_date( 'end_date' );
                                $expiration = apply_filters( 'yith_wcmbs_my_account_membership_status_expiration_date', $expiration, $membership );
                                echo $expiration;
                                ?></td>
                            <td class="yith-wcmbs-my-account-membership-status-status-column" data-title="<?php echo $details_titles[ 'status' ] ?>">
                                <span class="yith-wcmbs-membership-status-text <?php echo $membership->status ?>"><?php echo $membership->get_status_text() ?></span></td>
                            <?php if ( $membership->has_credit_management() ) : ?>
                                <td class="yith-wcmbs-my-account-membership-status-remaining-credits-column" data-title="<?php echo $details_titles[ 'remaining-credits' ] ?>"><?php echo $membership->get_remaining_credits() ?></td>
                                <?php $date = apply_filters( 'yith_wcmbs_next_credits_update_date',date( wc_date_format(), $membership->next_credits_update ),$membership->next_credits_update ) ?>
                                <td class="yith-wcmbs-my-account-membership-status-next-credits-update-column" data-title="<?php echo $details_titles[ 'next-credits-update' ] ?>"><?php echo $date ?></td>
                            <?php endif ?>
                        </tr>
                    </table>
                </div>

                <div class="yith-wcmbs-tabs">
                    <ul>
                        <li>
                            <a href="#yith-wcmbs-tab-history-<?php echo $membership->id; ?>"><?php _e( 'History', 'yith-woocommerce-membership' ) ?></a>
                        </li>
                        <?php if ( apply_filters( 'yith_wcmb_force_showing_of_tab_contents',false )|| $display_content_in_plan && $membership->is_active() ) : ?>
                            <li>
                                <a href="#yith-wcmbs-tab-contents-<?php echo $membership->id; ?>"><?php echo apply_filters( 'yith_wcms_tab_contents_label', __( 'Contents', 'yith-woocommerce-membership' ) ) ?></a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <div id="yith-wcmbs-tab-history-<?php echo $membership->id; ?>" class="yith-wcmbs-membership-history-table-container">

                        <?php
                        $activities = $membership->activities;

                        $history_titles = array(
                            'status' => __( 'Status', 'yith-woocommerce-membership' ),
                            'update' => __( 'Update', 'yith-woocommerce-membership' ),
                            'note'   => __( 'Note', 'yith-woocommerce-membership' ),
                        );

                        if ( !empty( $activities ) ) : ?>
                            <table class="yith-wcmbs-membership-history-table yith-wcmbs-membership-table shop_table_responsive">
                                <thead>
                                <tr>
                                    <th><?php echo $history_titles[ 'status' ]; ?></th>
                                    <th><?php echo $history_titles[ 'update' ]; ?></th>
                                    <th><?php echo $history_titles[ 'note' ]; ?></th>
                                </tr>
                                </thead>
                                <?php foreach ( $activities as $activity ) :
                                    /** @var YITH_WCMBS_Activity $activity */
                                    ?>
                                    <tr>
                                        <td data-title="<?php echo $history_titles[ 'status' ]; ?>"><?php echo strtr( $activity->status, yith_wcmbs_get_membership_statuses() ) ?></td>
                                        <td data-title="<?php echo $history_titles[ 'update' ]; ?>"><?php echo $activity->get_formatted_date( true, wc_date_format() ); ?></td>
                                        <td data-title="<?php echo $history_titles[ 'note' ]; ?>"><?php echo call_user_func( '__', $activity->note, 'yith-woocommerce-membership' ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>
                    </div>

                    <?php if ( apply_filters( 'yith_wcmb_force_showing_of_tab_contents',false ) || $display_content_in_plan && $membership->is_active() ) : ?>
                        <div id="yith-wcmbs-tab-contents-<?php echo $membership->id; ?>" class="yith-wcmbs-my-account-list-plan-items-container">
                            <?php

                            $allowed_in_plan = YITH_WCMBS_Manager()->get_allowed_posts_in_plan( $membership->plan_id, true );

                            $sorted_items = get_post_meta( $membership->plan_id, '_yith_wcmbs_plan_items', true );
                            $sorted_items = apply_filters( 'yith_wcmbs_sorted_plan_items', $sorted_items, $membership->plan_id );
                            $sorted_items = !empty( $sorted_items ) ? $sorted_items : array();

                            foreach ( $sorted_items as $key => $item ) {
                                if ( is_numeric( $item ) ) {
                                    if ( !in_array( $item, $allowed_in_plan ) ) {
                                        unset( $sorted_items[ $key ] );
                                    }
                                }
                            }

                            if ( !empty( $allowed_in_plan ) ) {
                                foreach ( $allowed_in_plan as $item_id ) {
                                    if ( !in_array( $item_id, $sorted_items ) )
                                        $sorted_items[] = $item_id;
                                }
                            }

                            $t_args = array(
                                'posts' => $sorted_items,
                                'plan'  => $membership,
                            );

                            wc_get_template( '/frontend/my_account_plan_list_items.php', $t_args, '', YITH_WCMBS_TEMPLATE_PATH );

                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }

        ?>
    </div>
    <?php
} else {
    echo "<p class='yith-wcmbs-my-account-membership-plans__no-membership'>" . $no_membership_message . "</p>";
}
?>