<?php
/*
 * Template for Metabox Send Message
 */
?>

<div id="yith-wcmbs-send-message-editor" data-thread-id="<?php echo esc_attr( $thread_id ); ?>" data-user-id="<?php echo esc_attr( get_current_user_id() ); ?>">
	<?php wp_editor( '', 'yith-wcmbs-message-to-send', array( 'media_buttons' => false, 'tinymce' => false ) ); ?>
	<p>
		<input id="yith-wcmbs-send-button" type="button" class="button button-primary alignright" value="<?php esc_html_e( 'Send', 'yith-woocommerce-membership' ); ?>">
		<span class="spinner"></span>
	</p>
</div>
