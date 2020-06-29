<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Implements admin notification email for YWAF plugin (Plain)
 *
 * @class   YWAF_Admin_Notification
 * @package Yithemes
 * @since   1.0.5
 * @author  Your Inspiration Themes
 */

$risk_factor = $order->get_meta( 'ywaf_risk_factor' );
$risk_data   = YITH_WAF()->get_risk_level( isset ( $risk_factor['score'] ) ? $risk_factor['score'] : '' );

echo $email_heading . "\n\n";

switch ( $risk_data['class'] ) {

	case 'high':

		printf( __( 'Anti-fraud checks on order %s failed. Returned risk percentage is %s and therefore the order has been cancelled.', 'yith-woocommerce-anti-fraud' ), '#' . $order->get_order_number(), $risk_factor['score'] . '%' );
		break;

	case 'medium':

		printf( __( 'Anti-fraud checks on order %s returned a %s risk percentage and the order is now "on hold".', 'yith-woocommerce-anti-fraud' ), '#' . $order->get_order_number(), $risk_factor['score'] . '%' );
		break;

	default:

		printf( __( 'Order %s has successfully passed selected anti-fraud checks', 'yith-woocommerce-anti-fraud' ), '#' . $order->get_order_number() );

}

if ( ! empty( $risk_factor['failed_rules'] ) ) {

	echo "\n\n" . __( 'Summary of anti-fraud checks on this order:', 'yith-woocommerce-anti-fraud' ) . "\n\n";

	foreach ( $risk_factor['failed_rules'] as $failed_rule ) {

		if ( class_exists( $failed_rule ) ) {

			$rule = new $failed_rule;
			echo '&bull;' . $rule->get_message() . "\n";
		}

	}
}

echo "\n\n\n" . apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );