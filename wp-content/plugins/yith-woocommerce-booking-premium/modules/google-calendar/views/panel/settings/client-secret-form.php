<?php
/**
 * View for Client Secret form.
 *
 * @var string $client_id     The client ID.
 * @var string $client_secret The client secret.
 * @var string $redirect_uri  The Redirect URI.
 *
 * @package YITH\Booking\Views\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit();
?>
<ul class="yith-wcbk-google-calendar-access-steps">
	<li class="active"><?php esc_html_e( 'Credentials', 'yith-booking-for-woocommerce' ); ?></li>
	<li><?php esc_html_e( 'Access', 'yith-booking-for-woocommerce' ); ?></li>
</ul>
<input type='hidden' name='yith-wcbk-google-calendar-action' value='save-credentials'/>
<div class="yith-wcbk-google-calendar-form">
	<input type='text' name='yith-wcbk-gcal-options[client-id]' value='<?php echo esc_attr( $client_id ); ?>' placeholder="<?php esc_html_e( 'Client ID', 'yith-booking-for-woocommerce' ); ?>"/>
	<input type='text' name='yith-wcbk-gcal-options[client-secret]' value='<?php echo esc_attr( $client_secret ); ?>' placeholder="<?php esc_html_e( 'Client Secret', 'yith-booking-for-woocommerce' ); ?>"/>
</div>

<div class="yith-wcbk-google-calendar-how-to">
	<div style="text-align: left">
		<?php esc_html_e( 'To use this integration you should:', 'yith-booking-for-woocommerce' ); ?>
		<ol>
			<li>
				<?php
				// translators: 1. link start; 2. link end.
				echo wp_kses_post( sprintf( __( 'create a project in %1$s<strong>Google Developers Console</strong>%2$s', 'yith-booking-for-woocommerce' ), '<a href="https://console.developers.google.com" target="_blank">', '</a>' ) );
				?>
			</li>
			<li>
				<?php echo wp_kses_post( __( 'enable the <strong>Google Calendar API</strong> in <strong>Your Project > Library</strong>', 'yith-booking-for-woocommerce' ) ); ?>
			</li>
			<li>
				<?php echo wp_kses_post( __( 'create an <strong>OAuth Client ID</strong> for a <strong>Web application</strong> in <strong>Your Project > Credentials > Create Credentials</strong>', 'yith-booking-for-woocommerce' ) ); ?>
			</li>
			<li>
				<?php echo wp_kses_post( __( 'add the following URI to the <strong>Allowed Redirect URI</strong> in your ID client OAuth 2.0', 'yith-booking-for-woocommerce' ) ); ?>
			</li>
		</ol>
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
