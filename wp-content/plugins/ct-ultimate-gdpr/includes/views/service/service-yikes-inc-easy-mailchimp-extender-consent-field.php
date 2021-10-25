<?php

/**
 * The template for displaying Easy Form for Mailchimp service view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/service folder
 *
 * @version 1.0
 *
 */

?>

<span class="">
    <span class=" accept-this-1">
        <span class="yikes-inc-easy-mailchimp-extender-d-flex ">
            <input  type="checkbox" name="ct-ultimate-gdpr-consent-field" required class="ct-ultimate-gdpr-consent-field" />
            <label >
                <?php echo esc_html__($options['consent_text'], 'ct-ultimate-gdpr'); ?>
            </label>
        </span>
    </span>
</span>
<style>
    .yikes-inc-easy-mailchimp-extender-d-flex {
        display: flex;
    }
</style>