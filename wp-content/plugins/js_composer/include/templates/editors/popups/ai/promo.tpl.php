<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @var string $logo_template_path
 * @var string $message_template_path
 * @var string $modal_controller
 * @var string $error_message
 */
?>

<div class="vc_ui-helper-modal-ai-promo">
	<div class="vc_ui-helper-modal-ai-promo--inner">
		<?php

		vc_include_template( $logo_template_path );

		if ( empty( $error_message ) ) {
			vc_include_template( $message_template_path, [ 'modal_controller' => $modal_controller ] );
		} else {
			?>
			<h3 class="vc_heading">
				<?php esc_html_e( 'Error with access to AI', 'js_composer' ); ?>
			</h3>
			<p class="vc_description">
				<?php
				echo esc_html( $error_message );
				?>
			</p>
			<?php
		}
		?>
	</div>
</div>
