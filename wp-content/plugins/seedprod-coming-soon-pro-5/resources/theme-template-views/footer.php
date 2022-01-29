<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php echo seedprod_pro_get_theme_template_by_type_condition( 'footer', false, false, true );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
<?php wp_footer(); ?>

<?php
global $seedprod_theme_requirements;
//if ( in_array( 'optinform', $seedprod_theme_requirements, true ) ) {
	// subscriber callback
	$seedprod_subscribe_callback_ajax_url = html_entity_decode( wp_nonce_url( admin_url() . 'admin-ajax.php?action=seedprod_pro_subscribe_callback', 'seedprod_pro_subscribe_callback' ) );
	?>
	<?php if ( in_array( 'recaptcha', $seedprod_theme_requirements, true ) ) { ?>
	<!-- Recaptcha -->
	<script src="https://www.google.com/recaptcha/api.js?onload=sp_CaptchaCallback&render=explicit" async defer></script> <?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
	<?php } ?>

<script>
var seedprod_api_url = "<?php echo esc_url( SEEDPROD_PRO_API_URL ); ?>";
	<?php if ( in_array( 'recaptcha', $seedprod_theme_requirements, true ) && ! empty( $settings->enable_recaptcha ) ) { ?>
var seeprod_enable_recaptcha = <?php echo (int) $settings->enable_recaptcha; ?>;
<?php } else { ?>
	var seeprod_enable_recaptcha = 0;
<?php } ?>

var sp_subscriber_callback_url = '<?php echo esc_url_raw( $seedprod_subscribe_callback_ajax_url ); ?>';
</script>
<?php //} ?>

<?php if ( in_array( 'twitter_sdk', $seedprod_theme_requirements, true ) ) { ?>
<script>
		window.twttr = (function (d,s,id) {
			var t, js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
			js.src="https://platform.twitter.com/widgets.js";
			fjs.parentNode.insertBefore(js, fjs);
			return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
		}(document, "script", "twitter-wjs"));
</script>
<?php } ?>

</body>
</html>
