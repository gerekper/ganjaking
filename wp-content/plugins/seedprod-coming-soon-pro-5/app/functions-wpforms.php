<?php

/**
 * Get WP Forms.
 *
 * @return void
 */
function seedprod_pro_get_wpforms() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$forms = array();
		if ( function_exists( 'wpforms' ) ) {
			$forms = \wpforms()->form->get( '', array( 'order' => 'DESC' ) );
			$forms = ! empty( $forms ) ? $forms : array();
			$forms = array_map(
				function ( $form ) {
					$form->post_title = wp_html_excerpt( htmlspecialchars_decode( $form->post_title, ENT_QUOTES ), 100 );
					return $form;
				},
				$forms
			);
		}

		wp_send_json( $forms );
	}
}

/**
 * Get WP Form.
 *
 * @return void
 */
function seedprod_pro_get_wpform() {
	if ( check_ajax_referer( 'seedprod_nonce' ) && function_exists( 'wpforms_display' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$form_id          = filter_input( INPUT_GET, 'form_id', FILTER_SANITIZE_NUMBER_INT );
		$form_title       = filter_input( INPUT_GET, 'form_title', FILTER_VALIDATE_BOOLEAN );
		$form_description = filter_input( INPUT_GET, 'form_description', FILTER_VALIDATE_BOOLEAN );
		ob_start();
		?>
		<link rel='stylesheet' id='wpforms-full-css' href='<?php echo content_url(); ?>/plugins/wpforms-lite/assets/css/wpforms-full.css' media='all' /><?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet ?>
		<?php
		wpforms_display( $form_id, $form_title, $form_description );
		wp_send_json( ob_get_clean() );
	}
}
