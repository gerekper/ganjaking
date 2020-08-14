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
 * Implements admin notification email for YWAF plugin (HTML)
 *
 * @class   YWAF_Admin_Notification
 * @package Yithemes
 * @since   1.0.5
 * @author  Your Inspiration Themes
 */

$risk_factor = $order->get_meta( 'ywaf_risk_factor' );
$risk_data   = YITH_WAF()->get_risk_level( isset ( $risk_factor['score'] ) ? $risk_factor['score'] : '' );

if ( ! empty( $risk_factor['failed_rules'] ) ) {

	$risk_score = '<span style="font-weight: bold; color: ' . $risk_data['color'] . '">' . $risk_factor['score'] . '%</span>';

}

$order_number = '<a class="link" href="' . esc_url( admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ) ) . '">#' . $order->get_order_number() . '</a>';

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>

	<?php

	switch ( $risk_data['class'] ) {

		case 'high':

			printf( __( 'Anti-fraud checks on order %s failed. Returned risk percentage is %s and therefore the order has been cancelled.', 'yith-woocommerce-anti-fraud' ), $order_number, $risk_score );
			break;

		case 'medium':

			printf( __( 'Anti-fraud checks on order %s returned a %s risk percentage and the order is now "on hold".', 'yith-woocommerce-anti-fraud' ), $order_number, $risk_score );
			break;

		default:

			printf( __( 'Order %s has successfully passed selected anti-fraud checks', 'yith-woocommerce-anti-fraud' ), $order_number );

	}

	?>

</p>

<?php if ( ! empty( $risk_factor['failed_rules'] ) ): ?>

    <div>

		<?php _e( 'Summary of anti-fraud checks on this order:', 'yith-woocommerce-anti-fraud' ); ?>

        <ul style="margin: 0;">

			<?php foreach ( $risk_factor['failed_rules'] as $failed_rule ) : ?>

				<?php if ( class_exists( $failed_rule ) ) : ?>

					<?php $rule = new $failed_rule; ?>

                    <li><?php echo $rule->get_message(); ?></li>

				<?php endif; ?>

			<?php endforeach; ?>

        </ul>

    </div>


<?php endif; ?>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
