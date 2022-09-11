<?php
/**
 * Metabox for Subscription Actions
 *
 * @var $subscription
 *
 * @package YITH WooCommerce Subscription
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="subscription_schedule yith-plugin-ui">
	<div class="ywsbs_input_fields">
		<label class="ywsbs_schedule_label" for="ywsbs_ywsbs_price_is_per"><?php echo esc_html__( 'Recurring period', 'yith-woocommerce-subscription' ); ?>:</label>
		<div class="ywsbs_recurring_period_wrapper">
		<input type="number" style="width: 80px; display: inline-block" id="ywsbs_price_is_per" name="ywsbs_price_is_per"  value="<?php echo esc_attr( $subscription->price_is_per ); ?>" min="1">
		<select style="width: 115px; margin-top: -4px;" id="ywsbs_price_time_option" class="wc-enhanced-select" name="ywsbs_price_time_option">
			<?php foreach ( ywsbs_get_time_options() as $key => $value ) : ?>
				<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $subscription->price_time_option ); ?>><?php echo esc_attr( $value ); ?></option>
			<?php endforeach; ?>
		</select>
		</div>
	</div>
	<?php
	foreach ( $fields as $field => $label ) :
			$value = ! empty( $subscription->$field ) ? date_i18n( $time_format, $subscription->$field, true ) : '';

		if ( 'next_attempt_date' === $field ) {
			if ( empty( $subscription->get( 'next_attempt_date' ) ) || empty( $subscription->get_renew_order_id() ) ) {
				continue;
			}
		}
		?>
		<div class="ywsbs_input_fields">
			<label class="ywsbs_schedule_label" for="ywsbs_<?php echo esc_attr( $field ); ?>"><?php echo esc_html( $label ); ?>:</label>
			<input class="ywsbs-timepicker" type="text" id="ywsbs_<?php echo esc_attr( $field ); ?>" name="ywsbs_<?php echo esc_attr( $field ); ?>" value="<?php echo esc_attr( $value ); ?>">
		</div>
	<?php endforeach; ?>

</div>
<div class="subscription_actions_footer">
	<input type="hidden" name="ywsbs_safe_submit_field" id="ywsbs_safe_submit_field" value="">
	<button type="submit" class="button button-primary" title="<?php esc_html_e( 'Schedule', 'yith-woocommerce-subscription' ); ?>" id="ywsbs_schedule_subscription_button" name="ywsbs_schedule_subscription_button" value="actions"><?php esc_html_e( 'Schedule', 'yith-woocommerce-subscription' ); ?></button>
</div>
