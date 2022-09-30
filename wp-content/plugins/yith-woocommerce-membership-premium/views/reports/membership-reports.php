<?php
/*
 * Template for Membership Reports
 */
?>
<div class="postbox yith-wcmbs-reports-metabox">
	<h2><span><?php esc_html_e( 'Active memberships', 'yith-woocommerce-membership' ) ?></span></h2>

	<div class="yith-wcmbs-reports-content">
		<div class="yith-wcmbs-reports-big-number">
			<?php
			$active_membership_count = YITH_WCMBS_Membership_Helper()->get_count_active_membership();
			echo esc_html( $active_membership_count );
			?>
		</div>
	</div>
	<?php if ( $active_membership_count > 0 ): ?>
		<div class="yith-wcmbs-reports-content">
			<?php
			$membership_statuses = yith_wcmbs_get_membership_statuses();
			$active_statuses     = array( 'active', 'resumed', 'expiring' );

			foreach ( $active_statuses as $current_active_status ) {
				$current_status_label = strtr( $current_active_status, $membership_statuses );
				$current_status_count = YITH_WCMBS_Membership_Helper()->get_count_membership_with_status( $current_active_status );
				if ( $current_status_count > 0 ) {
					echo '<span class="yith-wcmbs-membership-status-text ' . esc_attr( $current_active_status ) . ' yith-wcmbs-membership-status-with-margin">' . esc_html( $current_status_label ) . ': ' . esc_html( $current_status_count ) . '</span>';
				}
			}
			?>
		</div>
	<?php endif; ?>
</div>
<div class="postbox yith-wcmbs-reports-metabox">
	<h2><span><?php esc_html_e( 'Last actived memberships', 'yith-woocommerce-membership' ) ?></span></h2>

	<div class="yith-wcmbs-reports-content">
		<table class="yith-wcmbs-reports-table-membership">
			<tr>
				<th><?php esc_html_e( 'Today', 'yith-woocommerce-membership' ); ?></th>
				<td><?php echo esc_html( YITH_WCMBS_Membership_Helper()->get_count_actived_membership( 'today' ) ); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Yesterday', 'yith-woocommerce-membership' ); ?></th>
				<td><?php echo esc_html( YITH_WCMBS_Membership_Helper()->get_count_actived_membership( 'yesterday' ) ); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e( '7 days', 'yith-woocommerce-membership' ); ?></th>
				<td><?php echo esc_html( YITH_WCMBS_Membership_Helper()->get_count_actived_membership( '7day' ) ); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'This month', 'yith-woocommerce-membership' ); ?></th>
				<td><?php echo esc_html( YITH_WCMBS_Membership_Helper()->get_count_actived_membership( 'month' ) ); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Last month', 'yith-woocommerce-membership' ); ?></th>
				<td><?php echo esc_html( YITH_WCMBS_Membership_Helper()->get_count_actived_membership( 'last_month' ) ); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'This year', 'yith-woocommerce-membership' ); ?></th>
				<td><?php echo esc_html( YITH_WCMBS_Membership_Helper()->get_count_actived_membership( 'year' ) ); ?></td>
			</tr>
		</table>
	</div>
</div>
<div class="postbox yith-wcmbs-reports-metabox">
	<h2><span><?php esc_html_e( 'Total membership (ever)', 'yith-woocommerce-membership' ) ?></span></h2>

	<div class="yith-wcmbs-reports-content">
		<div class="yith-wcmbs-reports-big-number"><?php
			$membership_ever_count = YITH_WCMBS_Membership_Helper()->get_count_actived_membership( 'ever' );
			echo esc_html( $membership_ever_count );
			?></div>
	</div>

	<?php if ( $membership_ever_count > 0 ): ?>
		<div class="yith-wcmbs-reports-content">
			<?php
			$membership_statuses = yith_wcmbs_get_membership_statuses();

			foreach ( $membership_statuses as $current_status => $current_status_label ) {
				$current_status_count = YITH_WCMBS_Membership_Helper()->get_count_membership_with_status( $current_status );
				if ( $current_status_count > 0 ) {
					echo '<span class="yith-wcmbs-membership-status-text ' . esc_attr( $current_status ) . ' yith-wcmbs-membership-status-with-margin">' . esc_html( $current_status_label ) . ': ' . esc_html( $current_status_count ) . '</span>';
				}
			}
			?>
		</div>
	<?php endif; ?>
</div>

<?php do_action( 'yith_wcmbs_after_membership_reports' ); ?>
