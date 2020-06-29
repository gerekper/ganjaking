<?php
/**
 * My Account edit-delivery
 *
 * @package WC_OD/Templates
 * @since   1.5.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Global variables.
 *
 * @global WC_Subscription $subscription
 */

if ( ! $subscription || ! wc_od_user_has_subscription_delivery_caps( $subscription ) ) {
	$notice = sprintf(
		wp_kses(
			__( 'Invalid Subscription. <a href="%s" class="wc-forward">My Account</a>', 'woocommerce-order-delivery' ),
			array( 'a' => array( 'href' => array(), 'class' => array() ) )
		),
		esc_url( wc_get_page_permalink( 'myaccount' ) )
	);

	wc_print_notice( $notice, 'error' );
	return;
}

$fields = wc_od_get_subscription_delivery_fields( $subscription );
?>
<div class="wc-od-subscription-delivery">
	<p>
		<?php
		echo wp_kses_post(
			sprintf(
				/* translators: %s: view subscription URL */
				__( 'Here you can change the delivery preferences for the subscription: %s', 'woocommerce-order-delivery' ),
				sprintf( '<a href="%1$s">#%2$s</a>', esc_url( $subscription->get_view_order_url() ), esc_html( $subscription->get_id() ) )
			)
		);
		?>
	</p>

	<?php if ( empty( $fields ) ) : ?>

		<p><?php esc_html_e( 'There is no preferences for this subscription.', 'woocommerce-order-delivery' ); ?></p>

	<?php else : ?>

		<form method="post">

			<?php foreach ( $fields as $key => $field ) : ?>

				<?php woocommerce_form_field( $key, $field, wc_od_get_subscription_delivery_field_value( $subscription, $key ) ); ?>

			<?php endforeach; ?>

			<p>
				<?php wp_nonce_field( 'wc_od_edit_delivery' ); ?>
				<input type="hidden" name="action" value="edit_delivery" />
				<input type="hidden" name="subscription_id" id="subscription_id" value="<?php echo esc_attr( $subscription->get_id() ); ?>" />
				<input type="submit" class="button" name="save_delivery" value="<?php esc_attr_e( 'Save Preferences', 'woocommerce-order-delivery' ); ?>" />
			</p>
		</form>

	<?php endif; ?>
</div>
