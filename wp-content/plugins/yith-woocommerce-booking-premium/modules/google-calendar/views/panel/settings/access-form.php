<?php
/**
 * View for Google Calendar Access form
 *
 * @var string $auth_url     OAuth URL.
 * @var string $redirect_uri The redirect URI.
 *
 * @package YITH\Booking\Views\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit();
?>

<ul class="yith-wcbk-google-calendar-access-steps">
	<li><?php esc_html_e( 'Credentials', 'yith-booking-for-woocommerce' ); ?></li>
	<li class="active"><?php esc_html_e( 'Access', 'yith-booking-for-woocommerce' ); ?></li>
</ul>

<a style="margin: 40px 0 30px" href="<?php echo esc_url( $auth_url ); ?>" class="yith-plugin-fw__button--primary yith-plugin-fw__button--xl">
	<?php esc_html_e( 'Click here to access', 'yith-booking-for-woocommerce' ); ?>
</a>
<div class="yith-wcbk-google-calendar-how-to">
	<div style="text-align: left; margin-bottom: 15px">
		<?php echo wp_kses_post( __( 'Please note: you should add the following URI to the <strong>Allowed Redirect URI</strong> in your ID client OAuth 2.0', 'yith-booking-for-woocommerce' ) ); ?>
	</div>

	<?php
	yith_plugin_fw_get_field(
		array(
			'id'    => 'yith-wcbk-google-calendar-redirect-uri',
			'type'  => 'copy-to-clipboard',
			'value' => $redirect_uri,
		),
		true,
		false
	)
	?>
</div>
