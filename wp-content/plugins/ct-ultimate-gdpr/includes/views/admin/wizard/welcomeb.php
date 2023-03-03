<?php
/**
 *
 */
?>
<div class="row justify-content-md-center">
    <div class="d-flex">
        <div class="col-md-auto col-md-12 text-center">
            <h1><?php echo esc_html__('One more','ct-ultimate-gdpr'); ?></h1>
            <p><?php echo __("<strong>Remember</strong>, you can always adjust each option from this wizard in the settings tab of Ultimate CCPA & GDPR,<br/> or you can start this wizard again.", "ct-ultimate-gdpr") ?></p>
            <div class="arrow-circle"><i class="bi bi-arrow-down-circle"></i></div>
            <a href="<?php echo ct_ultimate_gdpr_wizard_step_url('step1a'); ?>" class="btn btn-primary"><?php echo esc_html__('OK, Let’s start', 'ct-ultimate-gdpr'); ?></a>
        </div>
    </div>
</div>

