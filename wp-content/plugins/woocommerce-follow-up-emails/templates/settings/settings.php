<style>
	div.fue-settings h3 {
		margin-top: 30px;
	}
</style>
<div class="wrap fue-settings woocommerce">
	<div class="icon32"><img src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/send_mail.png" /></div>
	<h2>
		<?php esc_html_e('Follow-Up &mdash; Settings', 'follow_up_emails'); ?>
	</h2>

	<?php include FUE_TEMPLATES_DIR .'/settings/notifications.php'; ?>

	<?php include FUE_TEMPLATES_DIR .'/settings/menu.php'; ?>

	<?php
	switch ( $tab ) {

		case 'auth':
			include 'settings-auth.php';
			break;

		case 'info':
			include 'system-info.php';
			break;

		case 'subscribers':
			include 'settings-subscribers.php';
			break;

		case 'integration':
			include 'settings-integration.php';
			break;

		case 'email':
		case 'crm':
		case 'system':
			$bounce_defaults = FUE_Bounce_Handler::get_default_settings();
			$bounce = wp_parse_args( $bounce, $bounce_defaults );
			$enable_daily_summary = get_option( 'fue_enable_daily_summary', 'yes' );
			$staging = get_option( 'fue_staging', 'no' );

			include 'settings-system.php';
			break;

		case 'tools':
			include 'settings-tools.php';
			break;

		default:
			do_action( "fue_settings_{$tab}" );
			break;

	}

	do_action('fue_settings_form');

	?>

</div>
<script>
	jQuery(document).ready(function() {
		jQuery(".tips, .help_tip").tipTip({
			'attribute' : 'title',
			'fadeIn' : 50,
			'fadeOut' : 50,
			'delay' : 200
		});
	});
</script>
