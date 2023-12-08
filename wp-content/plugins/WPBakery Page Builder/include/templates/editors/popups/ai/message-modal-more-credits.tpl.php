<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @var Vc_Ai_Modal_Controller $modal_controller
 */
?>

<h3 class="vc_heading"><?php esc_html_e( 'Insufficient WPBakery AI Credits', 'js_composer' ); ?></h3>
<p class="vc_description">
	<?php
	echo esc_html( 'You have reached your monthly limit of free', 'js_composer' );
	echo  ' ' .esc_html( empty( $modal_controller->credits_limit ) ? '' : $modal_controller->credits_limit ) . ' ';
	esc_html_e( 'WPBakery AI credits per site.', 'js_composer' ); ?>
	<br />
	<?php esc_html_e( 'The credits are used everytime you use WPBakery AI to generate content.', 'js_composer' ); ?>
</p>
<a class="vc_general vc_ui-button vc_ui-button-action vc_ui-button-shape-rounded vc_ui-button-fw" target="_blank" href="https://kb.wpbakery.com/docs/wpbakery-ai/?utm_campaign=wpb-ai&utm_source=wpb-plugin&utm_medium=ai-window">
	<?php esc_html_e( 'Get More Credits', 'js_composer' ); ?>
</a>
