<?php
/**
 * Metabox for Subscription Actions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="subscription_actions">
	<select name="ywsbs_subscription_actions">
		<option value=""><?php esc_html_e( 'Actions', 'yith-woocommerce-subscription' ); ?></option>
		<?php if ( $subscription->can_be_active() ) : ?>
			<option value="active"><?php esc_html_e( 'Active Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php if ( $subscription->can_be_overdue() ) : ?>
			<option value="overdue"><?php esc_html_e( 'Overdue Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php if ( $subscription->can_be_suspended() ) : ?>
			<option value="suspended"><?php esc_html_e( 'Suspend Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php if ( $subscription->can_be_paused() ) : ?>
			<option value="paused"><?php esc_html_e( 'Pause Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php if ( $subscription->can_be_resumed() ) : ?>
			<option value="resumed"><?php esc_html_e( 'Resume Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		
		<?php if ( $subscription->can_be_cancelled() ) : ?>
			<option value="cancelled"><?php esc_html_e( 'Cancel Subscription', 'yith-woocommerce-subscription' ); ?></option>
			<option value="cancel-now"><?php esc_html_e( 'Cancel Subscription Now', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php if ( $subscription->can_be_create_a_renew_order() ) : ?>
			<option value="renew-order"><?php esc_html_e( 'Create a Renew Order Manually', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>
	</select>
</div>
<div class="subscription_actions_footer">
	<button type="submit" class="button button-primary" title="<?php esc_html_e( 'Apply', 'yith-woocommerce-subscription' ); ?>" name="ywsbs_subscription_button" value="actions"><?php esc_html_e( 'Processing', 'yith-woocommerce-subscription' ); ?></button>
</div>
