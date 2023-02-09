<?php
/**
 * Shorcode for form
 *
 * @package form template
 * Checks if WooCommerce is enabled
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="woocommerce">
	<form method="post" id="wc-help-scout-conversation-form-<?php echo esc_textarea( $counter ); ?>" class="wc-help-scout-conversation-form" data-inc="<?php echo esc_textarea( $counter ); ?>" enctype="multipart/form-data">
		<?php 
			/**
			* Action for woocommerce_help_scout_customer_args.
			*
			* @since  1.3.4
			*/
			do_action( 'woocommerce_help_scout_shortcode_conversation_form_start' ); 
		?>

		<?php if ( ! empty( $orders_list ) ) : ?>
			<p class="form-row form-row-wide">
				<label for="conversation-order"><?php esc_html_e( 'Order', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
				<select name="conversation_order_id" id="conversation-order" class="conversation-field" required="required">
					<?php foreach ( $orders_list as $key => $value ) : ?>
						<option value="<?php echo intval( $key ); ?>"><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php else : ?>
			<p class="form-row form-row-wide">
				<label for="conversation-customer-name"><?php esc_html_e( 'Your Name', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text conversation-field" name="conversation_customer_name" id="conversation-customer-name" required="required" />
			</p>

			<p class="form-row form-row-wide">
				<label for="conversation-email"><?php esc_html_e( 'Your Email', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
				<input type="email" class="input-text conversation-field" name="conversation_email" id="conversation-email" required="required" />
			</p>

			<input type="hidden" class="conversation-field" name="conversation_order_id" value="0" />
		<?php endif; ?>

		<p class="form-row form-row-wide">
			<label for="conversation-subject"><?php esc_html_e( 'Subject', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
			<input type="text" class="input-text conversation-field" name="conversation_subject" id="conversation-subject" required="required" />
		</p>

		<p class="form-row form-row-wide">
			<label for="conversation-description"><?php esc_html_e( 'Description', 'woocommerce-help-scout' ); ?> <span class="required">*</span></label>
			<textarea name="conversation_description" class="conversation-field" id="conversation-description" rows="10" cols="50" required="required"></textarea>
		</p>
		<div class="input-field">
				<label class="active">Files</label>
				<div class="input-images-<?php echo esc_textarea( $counter ); ?>" style="padding-top: .5rem;"></div>
		</div>
		<br>
		<?php 
			/**
			* Action for woocommerce_help_scout_customer_args.
			*
			* @since  1.3.4
			*/
			do_action( 'woocommerce_help_scout_shortcode_conversation_form' ); 
		?>

		<p class="form-row">
			<input type="submit" class="button" name="conversation_send" value="<?php esc_html_e( 'Send', 'woocommerce-help-scout' ); ?>" />
		</p>

		<?php 
			/**
			* Action for woocommerce_help_scout_customer_args.
			*
			* @since  1.3.4
			*/
			do_action( 'woocommerce_help_scout_shortcode_conversation_form_end' ); 
		?>
	</form>
</div>
