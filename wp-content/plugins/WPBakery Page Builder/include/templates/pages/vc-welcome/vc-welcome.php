<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>
<div class="vc_welcome-tab changelog">
	<div class="vc_feature-section-teaser">
		<?php
		vc_include_template( 'editors/partials/promo-content.tpl.php', array(
			'is_about_page' => true,
		) );
		?>
	</div>
	<p class="vc-thank-you">
		<?php esc_html_e( 'Thank you for choosing WPBakery Page Builder,', 'js_composer' ); ?><br/><?php esc_html_e( 'Michael M, CEO at WPBakery,', 'js_composer' ); ?>
	</p>
</div>
