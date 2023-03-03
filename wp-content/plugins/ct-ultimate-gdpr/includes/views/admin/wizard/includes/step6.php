<?php
/**
 *
 */
?>
<h1><?php echo esc_html__('Consents log', 'ct-ultimate-gdpr'); ?></h1>
<p><?php echo _e('You can download a log file with all the information about consents given by visitors of your website under <i>Cookie Consent</i> >>>>>> <i>Consent Log</i>.', 'ct-ultimate-gdpr'); ?></p>
<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step7'); ?>">

    <div class="row">
        <div class="col-md-6">
            <img src="<?php echo ct_ultimate_gdpr_url('includes/views/admin/wizard/assets/images/consent_dashboard.png'); ?>" alt="">
        </div>
        <div class="col-md-6">
        <img src="<?php echo ct_ultimate_gdpr_url('includes/views/admin/wizard/assets/images/consent_download.png'); ?>" alt="">
        </div>
    </div>

    <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>

</form>