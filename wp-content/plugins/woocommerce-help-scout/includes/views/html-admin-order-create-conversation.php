<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$current_user = wp_get_current_user();
?>

<div id="conversation-fields">
	<p><?php _e( 'Start a conversation with your customer to discuss an issue.', 'woocommerce-help-scout' ); ?></p>

	<?php do_action( 'woocommerce_help_scout_conversation_admin_form_start' ); ?>

	<p>
		<label for="conversation-subject"><?php _e( 'Subject', 'woocommerce-help-scout' ); ?> <span class="required"><?php _e( '(required)', 'woocommerce-help-scout' ); ?></span></label>
		<input type="text" class="conversation-field" id="conversation-subject" name="conversation_subject" />
	</p>

	<p>
		<label for="conversation-description"><?php _e( 'Description', 'woocommerce-help-scout' ); ?> <span class="required"><?php _e( '(required)', 'woocommerce-help-scout' ); ?></span></label>
		<textarea id="conversation-description" class="conversation-field" name="conversation_description" cols="25" rows="5"></textarea>
	</p>

	<?php do_action( 'woocommerce_help_scout_conversation_admin_form' ); ?>

	<p>
		<a id="open-conversation" href="#" class="button button-primary"><?php _e( 'Start Conversation', 'woocommerce-help-scout' ); ?></a>
	</p>

	<?php do_action( 'woocommerce_help_scout_conversation_admin_form_end' ); ?>

</div>
