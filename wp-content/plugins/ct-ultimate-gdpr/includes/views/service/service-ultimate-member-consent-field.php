<?php

/**
 * The template for displaying service consent checkbox
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/service folder
 *
 * @version 1.0
 *
 */

?>

<div class="um-field">
    <div class="um-field-area">
        <div class="um-field-area">
            <p>
                <input class="ct-ultimate-gdpr-consent-field" type="checkbox" name="ct-ultimate-gdpr-consent-field" required id="ct-ultimate-gdpr-consent-field-ultimate-member"/>
                <label class="ct-ultimate-gdpr-consent-field" for="ct-ultimate-gdpr-consent-field-ultimate-member">
                    <?php echo esc_html__('I consent to the storage of my data according to the Privacy Policy ', 'ct-ultimate-gdpr'); ?>
                </label>
            </p>
        </div>
    </div>

