<div id="membership-data" class="panel">

    <h2><?php printf( __( 'Membership #%d details', 'yith-woocommerce-membership' ), $membership->id ) ?> <span
                class="yith-wcmbs-membership-status <?php echo $membership->status ?>"><?php echo $membership->get_status_text() ?></span></h2>

    <p class="membership_title"> <?php
        $link       = get_edit_post_link( $membership->plan_id );
        $plan_title = $membership->get_plan_title();

        if ( $link ) {
            $plan_title = '<a href="' . $link . '">' . $plan_title . '</a>';
        }
        echo sprintf( __( 'Membership Plan: %s', 'yith-woocommerce-membership' ), $plan_title ); ?>
    </p>

    <div class="membership_data_column_container">
        <div class="membership_data_column">
            <h4><?php _e( 'General Details', 'yith-woocommerce-membership' ) ?></h4>

            <p class="field_info"><label><strong><?php _e( 'Starting Date', 'yith-woocommerce-membership' ) ?>:</strong></label>
                <?php echo $membership->get_formatted_date( 'start_date' ) ?>
            </p>

            <p class="field_info"><label><strong><?php _e( 'Expiration Date', 'yith-woocommerce-membership' ) ?>:</strong></label>
                <?php echo $membership->get_formatted_date( 'end_date' ) ?>
            </p>

            <p class="field_info"><label><strong><?php _e( 'Order ID', 'yith-woocommerce-membership' ) ?>:</strong></label>
                <?php
                $order_id = $membership->order_id;
                if ( $order_id > 0 ) {
                    $order_link = get_edit_post_link( $order_id );
                    echo "<a href='{$order_link}'>#{$order_id}</a>";
                } else {
                    _e( 'Created by Admin', 'yith-woocommerce-membership' );
                }
                ?>
            </p>
            <?php do_action( 'yith_wcmbs_membership_metabox_info_after_first_column', $membership ); ?>
        </div>
        <div class="membership_data_column">
            <h4><?php _e( 'User Details', 'yith-woocommerce-membership' ) ?></h4>

            <p class="field_info"><label><strong><?php _e( 'User', 'yith-woocommerce-membership' ) ?>:</strong></label>
                <?php
                $user_id = $membership->user_id;
                if ( $user_id > 0 ) {
                    $user      = get_user_by( 'id', $user_id );
                    $edit_link = get_edit_user_link( $user_id );
                    echo "<a href='{$edit_link}'>{$user->user_login}</a>";
                } else {
                    ?>
                    <select name="_yith_wcmbs_membership_user_id" class="yith_wcmbs_ajax_select2_select_customer" style="width:95%;" data-placeholder="Search for users">
                        <option></option>
                    </select>
                    <?php
                    _e( 'Note: when you add a user here, you won\'t be able to modify him/her anymore.', 'yith-woocommerce-membership' );
                }
                ?>
            </p>

            <?php if ( $membership->has_credit_management() ): ?>
                <h4><?php _e( 'Credits', 'yith-woocommerce-membership' ) ?></h4>
                <p class="field_info"><label><strong><?php _e( 'Remaining Credits', 'yith-woocommerce-membership' ) ?>:</strong></label>
                    <?php
                    echo $membership->get_remaining_credits();
                    ?>
                </p>
                <p class="field_info"><label><strong><?php _e( 'Last update', 'yith-woocommerce-membership' ) ?>:</strong></label>
                    <?php
                    echo date( wc_date_format(), $membership->credits_update );
                    ?>
                </p>
                <p class="field_info"><label><strong><?php _e( 'Next update', 'yith-woocommerce-membership' ) ?>:</strong></label>
                    <?php
                    echo date( wc_date_format(), $membership->next_credits_update );
                    ?>
                </p>
            <?php endif ?>

            <?php do_action( 'yith_wcmbs_membership_metabox_info_after_second_column', $membership ); ?>
        </div>
    </div>

    <div class="clear"></div>

    <?php
    $activities = $membership->activities;

    if ( !empty( $activities ) ) : ?>
        <h4><?php _e( 'Membership History', 'yith-woocommerce-membership' ) ?></h4>
        <table class="yith-wcmbs-admin-profile-membership-table">
            <tr>
                <th><?php _e( 'Status', 'yith-woocommerce-membership' ); ?></th>
                <th><?php _e( 'Update', 'yith-woocommerce-membership' ); ?></th>
                <th><?php _e( 'Note', 'yith-woocommerce-membership' ); ?></th>
            </tr>
            <?php foreach ( $activities as $activity ) :
                /** @var YITH_WCMBS_Activity $activity */
                ?>
                <tr>
                    <td><?php echo strtr( $activity->status, yith_wcmbs_get_membership_statuses() ) ?></td>
                    <td><?php echo $activity->get_formatted_date(); ?></td>
                    <td><?php echo $activity->note ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>