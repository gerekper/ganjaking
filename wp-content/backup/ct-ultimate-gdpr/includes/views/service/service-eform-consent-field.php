<?php

/**
 * The template for displaying eForm service view in wp-admin
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr/service folder
 *
 * @version 1.0
 *
 */

?>

<div class="ipt_uif_column ipt_uif_column_full ipt_uif_conditional ipt_fsqm_container_s_checkbox" id="ipt_fsqm_form_999_pinfo_1" style="">
    <div class="ipt_uif_column_inner side_margin">
        <div class="ipt_uif_label_column column_1">
            <input type="checkbox" class="check_me validate[required] ipt_uif_checkbox  ipt_uif_s_checkbox filled-in" name="ct-ultimate-gdpr-consent" id="ct-ultimate-gdpr-consent-field-eform" value="1">
            <label for="ct-ultimate-gdpr-consent-field-eform" data-labelcon="">
	            <?php echo esc_html__('I consent to the storage of my data according to the Privacy Policy', 'ct-ultimate-gdpr'); ?>
            </label>
        </div>
        <div class="clear-both"></div><div class="clear-both"></div>	</div>
</div>