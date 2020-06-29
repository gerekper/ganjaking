<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$custom_tag = 'script';
?>
<<?php echo esc_attr( $custom_tag ); ?>>
	window.vcAdminNonce = '<?php echo esc_js( vc_generate_nonce( 'vc-admin-nonce' ) ); ?>';
</<?php echo esc_attr( $custom_tag ); ?>>
<form action="options.php" method="post" id="vc_settings-automapper"
		class="vc_settings-tab-content vc_settings-tab-content-active"
>
	<?php vc_automapper()->renderHtml(); ?>
</form>
