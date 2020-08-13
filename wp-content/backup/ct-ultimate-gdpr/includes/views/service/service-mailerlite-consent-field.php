<?php
	
	/**
	 * The template for displaying Mailerlite service view in wp-admin
	 *
	 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/service folder
	 *
	 * @version 1.0
	 *
	 */

?>
<div class = "ct-ultimate-gdpr-mailerlite">
    <input class="ct-ultimate-gdpr-consent-field" type="checkbox" name="ct-ultimate-gdpr-consent-field" required id="ct-ultimate-gdpr-consent-field-mailerlite"/>
    <label for="ct-ultimate-gdpr-consent-field-mailerlite">
        <?php echo esc_html__( 'I consent to my data being stored according to the Privacy Policy', 'ct-ultimate-gdpr' ); ?>
        <span class="mailerlite-required">*</span>
    </label>
    <label id="ct-ultimate-gdpr-consent-field-error" class="error" for="ct-ultimate-gdpr-consent-field" style = "display:none;">This field is required.</label>
</div>