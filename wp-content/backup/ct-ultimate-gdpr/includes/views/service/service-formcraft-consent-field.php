<?php

/**
 * The template for displaying Formcraft service view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/service folder
 *
 * @version 1.0
 *
 */

?>
<div class = "ct-ultimate-gdpr-formcraft field-cover">
    <input class="ct-ultimate-gdpr-consent-field" type="checkbox" name="ct-ultimate-gdpr-consent-field" required id="ct-ultimate-gdpr-consent-field-formcraft"/>
    <label class = "main-label" for="ct-ultimate-gdpr-consent-field-formcraft" style = "letter-spacing:0;">
        <?php echo esc_html__( 'I consent to my data being stored according to the Privacy Policy', 'ct-ultimate-gdpr' ); ?>
        <span class="formcraft-required">*</span>
    </label>
</div>