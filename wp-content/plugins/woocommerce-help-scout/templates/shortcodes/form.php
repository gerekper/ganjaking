<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php /*
<link rel="stylesheet" href="http://localhost/final/wp-content/plugins/woocommerce-help-scout-PLUGIN-500/assets/css/frontend/jquery-ui.css" type="text/css" />
<link rel="stylesheet" href="http://localhost/final/wp-content/plugins/woocommerce-help-scout-PLUGIN-500/assets/css/frontend/jquery.ui.plupload.css" type="text/css" />

<script src="http://localhost/final/wp-content/plugins/woocommerce-help-scout-PLUGIN-500/assets/js/frontend/jquery.min.js"></script>
<script type="text/javascript" src="http://localhost/final/wp-content/plugins/woocommerce-help-scout-PLUGIN-500/assets/js/frontend/jquery-ui.min.js"></script>

<!-- production -->
<script type="text/javascript" src="http://localhost/final/wp-content/plugins/woocommerce-help-scout-PLUGIN-500/assets/js/frontend/plupload.full.min.js"></script>
<script type="text/javascript" src="http://localhost/final/wp-content/plugins/woocommerce-help-scout-PLUGIN-500/assets/js/frontend/jquery.ui.plupload.js"></script>
*/?>
<div class="woocommerce">
	<form method="post" id="wc-help-scout-conversation-form-<?php echo $counter; ?>" class="wc-help-scout-conversation-form" data-inc="<?php echo $counter; ?>" enctype="multipart/form-data">
		<?php do_action( 'woocommerce_help_scout_shortcode_conversation_form_start' ); ?>

		<?php if ( ! empty( $orders_list ) ) : ?>
			<p class="form-row form-row-wide">
				<label for="conversation-order"><?php _e( 'Order', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
				<select name="conversation_order_id" id="conversation-order" class="conversation-field" required="required">
					<?php foreach ( $orders_list as $key => $value ) : ?>
						<option value="<?php echo intval( $key ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php else : ?>
			<p class="form-row form-row-wide">
				<label for="conversation-customer-name"><?php _e( 'Your Name', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text conversation-field" name="conversation_customer_name" id="conversation-customer-name" required="required" />
			</p>

			<p class="form-row form-row-wide">
				<label for="conversation-email"><?php _e( 'Your Email', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
				<input type="email" class="input-text conversation-field" name="conversation_email" id="conversation-email" required="required" />
			</p>

			<input type="hidden" class="conversation-field" name="conversation_order_id" value="0" />
		<?php endif; ?>

		<p class="form-row form-row-wide">
			<label for="conversation-subject"><?php _e( 'Subject', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
			<input type="text" class="input-text conversation-field" name="conversation_subject" id="conversation-subject" required="required" />
		</p>

		<p class="form-row form-row-wide">
			<label for="conversation-description"><?php _e( 'Description', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
			<textarea name="conversation_description" class="conversation-field" id="conversation-description" rows="10" cols="50" required="required"></textarea>
		</p>
		<div id="file-<?php echo $counter; ?>" class="pluploder-ui-input">
			<p>Your browser doesn't have Flash, Silverlight or HTML5 support.</p>
		</div>
		<br>
		<?php do_action( 'woocommerce_help_scout_shortcode_conversation_form' ); ?>

		<p class="form-row">
			<input type="hidden" name="uploaded_files" id="uploaded_files_<?php echo $counter; ?>" value="" >
			<input type="submit" class="button" name="conversation_send" value="<?php _e( 'Send', 'woocommerce-help-scout' ); ?>" />
		</p>

		<?php do_action( 'woocommerce_help_scout_shortcode_conversation_form_end' ); ?>
	</form>
</div>

