<?php
	if ( ! is_array( $msg ) ) { return; }
	if ( ! isset( $msg['id'] ) ) { return; }
	if ( empty( $msg['content'] ) ) { return; }

	$js_url = $module_url . 'dashboard-notice.js';

	if ( empty( $msg['id'] ) ) {
		$msg_dismiss = '';
	} else {
		$msg_dismiss = __( 'Saving', 'wpmudev' );
	}

	?>
	<div class="wdp-notice notice notice-<?php echo esc_attr( $type ); ?> is-dismissible" style="display:none">
		<input type="hidden" name="msg_id" value="<?php echo esc_attr( $msg['id'] ); ?>" />
		<?php echo wp_kses_post( $msg['content'] ); ?>
		<script src="<?php echo esc_url( $js_url ); ?>"></script> <?php //phpcs:ignore ?>

	</div>
