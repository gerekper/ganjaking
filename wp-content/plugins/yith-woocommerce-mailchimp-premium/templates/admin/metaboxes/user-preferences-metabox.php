<?php
/**
 * Mailchimp data metabox
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly

?>

<div class="checkout-preferences">
	<h4><?php esc_html_e( 'Checkout preferences', 'yith-woocommerce-mailchimp' ); ?></h4>
	<p class="description">
		<?php esc_html_e( 'In this section you\'ll find data about customer consent to Mailchimp subscription', 'yith-woocommerce-mailchimp' ); ?>
	</p>
	<p class="option">
		<span class="option-label"><?php esc_html_e( 'Customer subscribed:', 'yith-woocommerce-mailchimp' ); ?></span>
		<span class="option-value"><?php echo $customer_subscribed ? esc_html__( 'yes', 'yith-woocommerce-mailchimp' ) : esc_html__( 'no', 'yith-woocommerce-mailchimp' ); ?></span>
	</p>

	<p class="option">
		<span class="option-label"><?php esc_html_e( 'Checkbox shown at checkout:', 'yith-woocommerce-mailchimp' ); ?></span>
		<span class="option-value"><?php echo $show_checkbox ? esc_html__( 'yes', 'yith-woocommerce-mailchimp' ) : esc_html__( 'no', 'yith-woocommerce-mailchimp' ); ?></span>
	</p>

	<p class="option">
		<span class="option-label"><?php esc_html_e( 'Submitted value:', 'yith-woocommerce-mailchimp' ); ?></span>
		<span class="option-value"><?php echo 'yes' == $submitted_value ? esc_html__( 'yes', 'yith-woocommerce-mailchimp' ) : esc_html__( 'no', 'yith-woocommerce-mailchimp' ); ?></span>
	</p>
</div>

<?php if ( ! empty( $personal_data ) ) : ?>
	<div class="mailchimp-personal-data">
		<h4><?php esc_html_e( 'Personal data', 'yith-woocommerce-mailchimp' ); ?></h4>
		<p class="description">
			<?php esc_html_e( 'In this section you\'ll find customer personal data that was sent to Mailchimp servers', 'yith-woocommerce-mailchimp' ); ?>
		</p>
		<?php foreach ( $personal_data as $data ) : ?>
			<p class="option">
				<span class="option-label"><?php echo esc_html( $data['label'] ); ?>:</span>
				<span class="option-value">
					<?php
					if ( is_scalar( $data['value'] ) ) {
						echo esc_html( $data['value'] );
					} else {
						echo '<pre>';
						print_r( $data['value'] );
						echo '</pre>';
					}
					?>
				</span>
			</p>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
