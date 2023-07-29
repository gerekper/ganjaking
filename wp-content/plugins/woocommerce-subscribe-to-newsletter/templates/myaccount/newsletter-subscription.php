<?php
/**
 * My account: 'Newsletter subscription'.
 *
 * @package WC_Newsletter_Subscription/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var array $fields Form fields.
 */
?>
<p><?php esc_html_e( 'Manage your newsletter subscription preferences.', 'woocommerce-subscribe-to-newsletter' ); ?></p>

<form class="wc-newsletter-subscription-my-account-form edit-subscription" method="post" action="">
	<?php
	foreach ( $fields as $key => $field ) :
		woocommerce_form_field( $key, $field, ( isset( $field['value'] ) ? $field['value'] : null ) );
	endforeach;
	?>

	<p>
		<?php
		wp_nonce_field( 'save_newsletter_subscription', 'save-newsletter-subscription-nonce' );
		printf(
			'<button type="submit" class="woocommerce-Button button" name="action" value="save_newsletter_subscription">%1$s</button>',
			esc_attr__( 'Save changes', 'woocommerce' ) // phpcs:ignore WordPress.WP.I18n.TextDomainMismatch
		);
		?>
	</p>
</form>

