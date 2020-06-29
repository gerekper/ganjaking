<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

! defined( 'ABSPATH' ) && exit; // Exit if accessed directly

$ext_charset = apply_filters( 'ywsn_additional_charsets', get_option( 'ywsn_active_charsets', array() ) );
$sms_length  = empty( $ext_charset ) ? 160 : 70;

if ( get_option( 'ywsn_enable_sms_length', 'no' ) === 'yes' ) {
	$sms_length = get_option( 'ywsn_sms_length', '160' );
}

?>
<div class="ywsn-sms-send-wrapper">
	<select name="ywsn_sms_test_message" id="ywsn_sms_test_message" class="wc-enhanced-select">
		<option value=""><?php esc_html_e( 'Choose message', 'yith-woocommerce-sms-notifications' ); ?></option>
		<option value="write-sms"><?php esc_html_e( 'Type message', 'yith-woocommerce-sms-notifications' ); ?></option>
		<option value="admin"><?php esc_html_e( 'Admin message', 'yith-woocommerce-sms-notifications' ); ?></option>
		<option value="generic"><?php esc_html_e( 'Default message', 'yith-woocommerce-sms-notifications' ); ?></option>
		<?php foreach ( wc_get_order_statuses() as $key => $label ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>"><?php echo $label; ?></option>
		<?php endforeach; ?>
		<?php if ( ywsn_is_booking_active() ) : ?>
			<option value="booking_admin"><?php esc_html_e( 'Booking: Admin message', 'yith-woocommerce-sms-notifications' ); ?></option>
			<option value="booking_generic"><?php esc_html_e( 'Booking: Default message', 'yith-woocommerce-sms-notifications' ); ?></option>
			<?php foreach ( yith_wcbk_get_booking_statuses( true ) as $key => $label ) : ?>
				<option value="booking_<?php echo esc_attr( $key ); ?>"><?php echo( esc_html__( 'Booking', 'yith-woocommerce-sms-notifications' ) . ': ' . $label ); ?></option>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>

	<select name="ywsn_sms_test_message_country" id="ywsn_sms_test_message_country" class="wc-enhanced-select">

		<?php foreach ( WC()->countries->get_countries() as $key => $label ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php echo( ( substr( get_option( 'woocommerce_default_country' ), 0, 2 ) === $key ? 'selected="selected"' : '' ) ); ?> ><?php echo $label; ?></option>
		<?php endforeach; ?>

	</select>

	<input name="ywsn_sms_test" id="ywsn_sms_test" type="text" class="ywsn-test-sms" placeholder="<?php esc_html_e( 'Type a phone number to send a test SMS text message', 'yith-woocommerce-sms-notifications' ); ?>" />

	<div class="ywsn-write-sms">
		<textarea class="ywsn-custom-message"></textarea>
		<div class="ywsn-char-count"><?php esc_html_e( 'Remaining characters', 'yith-woocommerce-sms-notifications' ); ?>: <span><?php echo apply_filters( 'ywsn_sms_limit', $sms_length ); ?></span></div>
	</div>

	<button type="button" class="button-secondary ywsn-send-test-sms"><?php esc_html_e( 'Send Test SMS', 'yith-woocommerce-sms-notifications' ); ?></button>

	<div class="ywsn-send-result send-progress"><?php esc_html_e( 'Sending...', 'yith-woocommerce-sms-notifications' ); ?></div>
</div>
