<?php

/**
 * The template for displaying WpForms Lite service view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/service folder
 *
 * @version 1.0
 *
 */

?>
<div class="wpforms-field wpforms-field-checkbox">
    <ul class="wpforms-field-required">
        <li>
            <input class="ct-ultimate-gdpr-consent-field" type="checkbox" name="ct-ultimate-gdpr-consent-field" required id="ct-ultimate-gdpr-consent-field-wpforms-lite"/>
            <label for="ct-ultimate-gdpr-consent-field-wpforms-lite">
                <?php echo esc_html__('I consent to the storage of my data according to the Privacy Policy', 'ct-ultimate-gdpr'); ?>
            </label>
        </li>
    </ul>
</div>
