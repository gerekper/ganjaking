<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Metabox for Subscription Action Content
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 *
 * @var YWSBS_Subscription $subscription Current subscription.
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

$dev = isset( $_GET['ywsbs_dev'] ); //phpcs:ignore
?>
<div class="subscription_actions">
	<select name="ywsbs_subscription_actions" class="wc-enhanced-select">
		<option value=""><?php esc_html_e( 'Actions', 'yith-woocommerce-subscription' ); ?></option>
		<?php if ( $subscription->can_be_active() ) : ?>
			<option
				value="active"><?php esc_html_e( 'Activate Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php if ( $subscription->can_be_overdue() ) : ?>
			<option
				value="overdue"><?php esc_html_e( 'Overdue Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php if ( $subscription->can_be_suspended() ) : ?>
			<option
				value="suspended"><?php esc_html_e( 'Suspend Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php if ( $subscription->can_be_paused() ) : ?>
			<option
				value="paused"><?php esc_html_e( 'Pause Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php if ( $subscription->can_be_resumed() ) : ?>
			<option
				value="resumed"><?php esc_html_e( 'Resume Subscription', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>


		<?php if ( $subscription->can_be_cancelled() ) : ?>
			<option
				value="cancelled"><?php esc_html_e( 'Cancel Subscription', 'yith-woocommerce-subscription' ); ?></option>
			<option
				value="cancel-now"><?php esc_html_e( 'Cancel Subscription Now', 'yith-woocommerce-subscription' ); ?></option>
		<?php endif ?>

		<?php
			$renew_order = $subscription->can_be_create_a_renew_order();

		if ( true === $renew_order ) :
			?>
			<option
				value="renew-order"><?php esc_html_e( 'Create a Renew Order Manually', 'yith-woocommerce-subscription' ); ?></option>
			<?php elseif ( $renew_order > 0 ) : ?>
				<option
					value="delete-current-renew-order"><?php printf( '%s #%d', esc_html__( 'Delete the current renew order: ', 'yith-woocommerce-subscription' ), esc_html( $renew_order ) ); ?></option>
				<?php if ( $subscription->can_be_editable( 'payment_date' ) ) : ?>
					<option value="pay-current-renew-order"><?php printf( '%s #%d', esc_html__( 'Try to pay the current renew order: ', 'yith-woocommerce-subscription' ), esc_html( $renew_order ) ); ?></option>
				<?php endif; ?>
				<?php if ( $dev ) : ?>
					<option value="set-status-during-the-renew"><?php printf( '%s #%d', esc_html__( 'Schedule status change during the renew: ', 'yith-woocommerce-subscription' ), esc_html( $renew_order ) ); ?></option>
				<?php endif; ?>
		<?php endif ?>
	</select>
</div>
<div class="subscription_actions_footer">
	<button type="submit" class="button button-primary"
		title="<?php esc_html_e( 'Process', 'yith-woocommerce-subscription' ); ?>" name="ywsbs_subscription_button"
		value="actions"><?php esc_html_e( 'Process', 'yith-woocommerce-subscription' ); ?></button>
</div>
