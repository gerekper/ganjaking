<?php

/**
 * The template for displaying Ninja Forms service view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/service folder
 *
 * @version 1.0
 *
 */

?>
<div id="ct-ultimate-gdpr-consent-field-wrapper">
    <label for="nf-field-consent" id="nf-label-field-consent" class="nf-checked-label">
        <input id="nf-field-consent" name="nf-field-consent" aria-describedby="nf-error-consent"
               class="ct-ultimate-gdpr-consent-field"
               aria-labelledby="nf-label-field-consent" required="" type="checkbox">
        <?php echo esc_html__('I consent to the storage of my data according to the Privacy Policy', 'ct-ultimate-gdpr'); ?>
        <span class="ninja-forms-req-symbol">*</span>
    </label>
</div>

<div id="nf-error-consent" class="ct-ultimate-gdpr-nf-consent-field-error" role="alert" style="display: none">
    <div class="nf-error-msg nf-error-required-error"><?php echo esc_html__('This is a required field.', 'ct-ultimate-gdpr'); ?></div>
</div>