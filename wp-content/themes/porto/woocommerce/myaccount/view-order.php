<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page.
 *
 * @version   3.0.0
 */

defined( 'ABSPATH' ) || exit;

$notes = $order->get_customer_order_notes();

$porto_woo_version = porto_get_woo_version_number();

if ( version_compare( $porto_woo_version, '2.6', '<' ) ) {
	wc_print_notices();
}

?>

<?php if ( version_compare( $porto_woo_version, '2.6', '>=' ) ) : ?>
	<p class="order-info m-b-lg">
	<?php
		printf(
			/* translators: 1: order number 2: order date 3: order status */
			esc_html__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'porto' ),
			'<mark class="order-number">' . $order->get_order_number() . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	?>
	</p>
<?php else : ?>
	<p class="order-info alert alert-info m-b-lg">
	<?php
		printf(
			/* translators: 1: order number 2: order date 3: order status */
			esc_html__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'porto' ),
			'<mark class="order-number">' . $order->get_order_number() . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<mark class="order-date">' . date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	?>
	</p>

<?php endif; ?>


<?php if ( $notes ) : ?>

	<?php if ( version_compare( $porto_woo_version, '2.6', '<' ) ) : ?>

	<div class="featured-box align-left">
		<div class="box-content">
	<?php endif; ?>

	<h2><?php esc_html_e( 'Order Updates', 'woocommerce' ); ?></h2>

	<ol class="woocommerce-OrderUpdates commentlist notes">
		<?php foreach ( $notes as $note ) : ?>
		<li class="woocommerce-OrderUpdate comment note">
			<div class="woocommerce-OrderUpdate-inner comment_container">
				<div class="woocommerce-OrderUpdate-text comment-text">
					<p class="woocommerce-OrderUpdate-meta meta"><?php echo date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'porto' ), strtotime( $note->comment_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

					<div class="woocommerce-OrderUpdate-description description">
						<?php echo function_exists( 'porto_shortcode_format_content' ) ? porto_shortcode_format_content( $note->comment_content ) : wp_kses_post( $note->comment_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>

					<div class="clear"></div>
				</div>

				<div class="clear"></div>
			</div>
		</li>
		<?php endforeach; ?>
	</ol>



	<?php if ( version_compare( $porto_woo_version, '2.6', '<' ) ) : ?>
		</div>
	</div>
	<?php endif; ?>

<?php endif; ?>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>
