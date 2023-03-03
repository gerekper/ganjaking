<?php
/**
 *
 */
?>

<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step8'); ?>">

    <div class="row justify-content-md-center">
        <div class="col-md-auto col-md-12 text-center">
            <h1><?php echo esc_html__("That's all", "ct-ultimate-gdpr"); ?></h1>
            <p><?php echo esc_html__('Thank you for your time. Plugin is now configured and ready to use.', 'ct-ultimate-gdpr'); ?><br/>
            <?php echo esc_html__('You can update any of the plugin settings under: Ultimate GDPR & CCPA. Check also our', 'ct-ultimate-gdpr'); ?> <a href="https://gdpr-plugin.readthedocs.io/en/latest/" target="_blank"><?php echo esc_html__('documentation', 'ct-ultimate-gdpr'); ?></a><br/>
            <?php echo esc_html__("If you need any assistance, don't hesitate to contact our", 'ct-ultimate-gdpr'); ?> <a href="https://createit.support/" target="_blank"><?php echo esc_html__("support team", 'ct-ultimate-gdpr'); ?></a></p>
            <div class="arrow-circle"><i class="bi bi-arrow-down-circle"></i></div>
            <a href="<?php echo admin_url( "admin.php?page=ct-ultimate-gdpr" ); ?>" class="btn btn-primary">
                <?php echo esc_html__("Go to Dashboard", 'ct-ultimate-gdpr'); ?>
            </a>

        </div>
    </div>

    <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>

</form>