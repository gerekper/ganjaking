<?php
/*
 * Template for Membership with Subscripion Reports
 */

$membership_with_subscription_args = array(
	'only_subscription' => true,
);

?>
<div class="postbox yith-wcmbs-reports-metabox opened">
	<h2><span><?php esc_html_e( 'Membership with Subscription', 'yith-woocommerce-membership' ) ?></span></h2>

	<div class="yith-wcmbs-reports-content">
		<div class="yith-wcmbs-reports-big-number"><?php
			$active_membership_with_subscrption_count = YITH_WCMBS_Membership_Helper()->get_count_membership_with_status( array( 'active', 'resumed' ), $membership_with_subscription_args );
			echo esc_html( $active_membership_with_subscrption_count );
			?>
		</div>
		<div class="yith-wcmbs-reports-subtitle"><span class='yith-wcmbs-membership-status-text active yith-wcmbs-membership-status-with-margin'><?php esc_html_e( 'active + resumed', 'yith-woocommerce-membership' ) ?></span></div>
	</div>

	<div class="yith-wcmbs-reports-content">
		<?php
		$membership_statuses = yith_wcmbs_get_membership_statuses();

		foreach ( $membership_statuses as $current_status => $current_status_label ) {
			$current_status_count = YITH_WCMBS_Membership_Helper()->get_count_membership_with_status( $current_status, $membership_with_subscription_args );
			if ( $current_status_count > 0 ) {
				echo '<span class="yith-wcmbs-membership-status-text ' . esc_attr( $current_status ) . ' yith-wcmbs-membership-status-with-margin">' . esc_html( $current_status_label ) . ': ' . esc_html( $current_status_count ) . '</span>';

			}
		}
		?>
	</div>

</div>
