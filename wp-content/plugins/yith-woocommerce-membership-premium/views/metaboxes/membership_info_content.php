<?php
/**
 * @var YITH_WCMBS_Membership $membership
 */

?>
<div id="membership-data" class="panel">

	<h2><?php echo esc_html( printf( __( 'Membership #%d details', 'yith-woocommerce-membership' ), $membership->id ) ); ?> <span
				class="yith-wcmbs-membership-status <?php echo esc_attr( $membership->status ) ?>"><?php echo esc_html( $membership->get_status_text() ); ?></span></h2>

	<p class="membership_title"> <?php
		$link       = get_edit_post_link( $membership->plan_id );
		$plan_title = $membership->get_plan_title();

		if ( $link ) {
			$plan_title = '<a href="' . $link . '">' . $plan_title . '</a>';
		}
		echo wp_kses_post( sprintf( esc_html__( 'Membership Plan: %s', 'yith-woocommerce-membership' ), $plan_title ) ); ?>
	</p>

	<div class="membership_data_column_container">
		<div class="membership_data_column">
			<h4><?php esc_html_e( 'General Details', 'yith-woocommerce-membership' ); ?></h4>

			<p class="field_info"><label><strong><?php esc_html_e( 'Starting Date', 'yith-woocommerce-membership' ); ?>:</strong></label>
				<?php echo esc_html( $membership->get_formatted_date( 'start_date' ) ); ?>
			</p>

			<p class="field_info"><label><strong><?php esc_html_e( 'Expiration Date', 'yith-woocommerce-membership' ); ?>:</strong></label>
				<?php echo apply_filters( 'yith_wcmbs_admin_membership_info_expiration_date', $membership->get_formatted_date( 'end_date' ), $membership ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</p>

			<p class="field_info"><label><strong><?php esc_html_e( 'Order ID', 'yith-woocommerce-membership' ); ?>:</strong></label>
				<?php
				$order_id = $membership->order_id;
				if ( $order_id > 0 ) {
					$order_link = get_edit_post_link( $order_id );
					echo '<a href="' . esc_url( $order_link ) . '">#' . esc_html( $order_id ) . '</a>';
				} else {
					esc_html_e( 'Created by Admin', 'yith-woocommerce-membership' );
				}
				?>
			</p>
			<?php do_action( 'yith_wcmbs_membership_metabox_info_after_first_column', $membership ); ?>
		</div>
		<div class="membership_data_column">
			<h4><?php esc_html_e( 'User Details', 'yith-woocommerce-membership' ); ?></h4>

			<p class="field_info"><label><strong><?php esc_html_e( 'User', 'yith-woocommerce-membership' ); ?>:</strong></label>
				<?php
				$user_id = $membership->user_id;
				if ( $user_id > 0 ) {
					$user      = get_user_by( 'id', $user_id );
					$edit_link = get_edit_user_link( $user_id );
					echo '<a href="' . esc_url( $edit_link ) . '">#' . esc_html( $user->user_login ) . '</a>';
				} else {
					?>
					<select name="_yith_wcmbs_membership_user_id" class="yith_wcmbs_ajax_select2_select_customer" style="width:95%;" data-placeholder="<?php esc_attr_e( 'Search user...', 'yith-woocommerce-membership' ); ?>">
					</select>
					<?php
				}
				?>
			</p>

			<?php if ( $membership->has_credit_management() ): ?>
				<h4><?php esc_html_e( 'Credits', 'yith-woocommerce-membership' ); ?></h4>
				<p class="field_info"><label><strong><?php esc_html_e( 'Remaining Credits', 'yith-woocommerce-membership' ); ?>:</strong></label>
					<?php
					echo esc_html( $membership->get_remaining_credits() );
					?>
				</p>
				<p class="field_info"><label><strong><?php esc_html_e( 'Last update', 'yith-woocommerce-membership' ) ?>:</strong></label>
					<?php
					echo esc_html( date_i18n( wc_date_format(), $membership->credits_update ) );
					?>
				</p>
				<p class="field_info"><label><strong><?php esc_html_e( 'Next update', 'yith-woocommerce-membership' ) ?>:</strong></label>
					<?php
					echo esc_html( date_i18n( wc_date_format(), $membership->next_credits_update ) );
					?>
				</p>
			<?php endif ?>


			<?php if ( $membership->has_discount() ): ?>
				<h4><?php esc_html_e( 'Discounts', 'yith-woocommerce-membership' ); ?></h4>
				<p class="field_info"><label><strong><?php esc_html_e( 'Shop Discount', 'yith-woocommerce-membership' ); ?>:</strong></label>
					<?php echo wp_kses_post( $membership->get_discount_html() ); ?>
				</p>
			<?php endif ?>

			<?php do_action( 'yith_wcmbs_membership_metabox_info_after_second_column', $membership ); ?>
		</div>
	</div>

	<div class="clear"></div>

	<?php
	$activities = $membership->activities;

	if ( ! empty( $activities ) ) : ?>
		<h4><?php esc_html_e( 'Membership History', 'yith-woocommerce-membership' ); ?></h4>
		<table class="yith-wcmbs-admin-table">
			<tr>
				<th><?php esc_html_e( 'Status', 'yith-woocommerce-membership' ); ?></th>
				<th><?php esc_html_e( 'Update', 'yith-woocommerce-membership' ); ?></th>
				<th><?php esc_html_e( 'Note', 'yith-woocommerce-membership' ); ?></th>
			</tr>
			<?php foreach ( $activities as $activity ) :
				/** @var YITH_WCMBS_Activity $activity */
				?>
				<tr>
					<td><?php echo esc_html( strtr( $activity->status, yith_wcmbs_get_membership_statuses() ) ); ?></td>
					<td><?php echo esc_html( $activity->get_formatted_date() ); ?></td>
					<td><?php echo wp_kses_post( $activity->note ); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
</div>