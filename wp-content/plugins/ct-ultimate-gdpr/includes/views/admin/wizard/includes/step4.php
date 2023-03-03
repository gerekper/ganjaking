<?php
/** @var string $age_enabled */
/** @var string $age_position */
/** @var string $age_limit_to_enter */
/** @var string $age_limit_to_sell */
/** @var string $age_popup_title */
/** @var string $age_popup_content */
/** @var string $age_popup_label_accept */
/** @var string $age_box_style */
/** @var string $age_box_shape */
/** @var string $age_button_shape */
?>

<h1><?php echo esc_html__('Age Verification', 'ct-ultimate-gdpr'); ?></h1>

<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step5'); ?>">

    <div class="row g-5">
        <div class="col-md-6">
            <div class="mb-3 form-check">
                
                <label for="age_enabled" class="form-check-label"><?php echo esc_html__( "Enable Age Verification", 'ct-ultimate-gdpr' ); ?></label>
                <?php echo $age_enabled; ?>
                <div class="ct-ultimate-gdpr-form-check">
                    <div class="ct-ultimate-gdpr-checkbox-switch"><span class="on label"><?php echo esc_html__('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php echo esc_html__('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php echo esc_html__('Enabled', 'ct-ultimate-gdpr' ); ?></span></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="age_position"
                       class="form-label"><?php echo esc_html__( 'Position (bottom, top and full page layout)', 'ct-ultimate-gdpr' ); ?></label>
                <?php echo $age_position; ?>
            </div>
            <div class="mb-3">
                <label for="age_limit_to_enter"
                       class="form-label"><?php echo esc_html__( 'Lower age limit to enter the website', 'ct-ultimate-gdpr' ); ?></label>
                <?php echo $age_limit_to_enter; ?>
            </div>
            <div class="mb-3">
                <label for="age_limit_to_sell"
                       class="form-label"><?php echo esc_html__( 'Lower age limit to provide personal data', 'ct-ultimate-gdpr' ); ?></label>
                <?php echo $age_limit_to_sell; ?>
            </div>
            <div class="mb-3">
                <label for="age_popup_title"
                       class="form-label"><?php echo esc_html__( 'Popup title', 'ct-ultimate-gdpr' ); ?></label>
                <?php echo $age_popup_title; ?>
            </div>
            <div class="mb-3">
                <label for="age_popup_content"
                       class="form-label"><?php echo esc_html__( 'Age popup content', 'ct-ultimate-gdpr' ); ?></label>
                <?php echo $age_popup_content; ?>
            </div>
            <div class="mb-3">
                <label for="age_popup_label_accept"
                       class="form-label"><?php echo esc_html__("Popup 'Submit' button label", 'ct-ultimate-gdpr'); ?></label>
                <?php echo $age_popup_label_accept; ?>
            </div>
        </div>
        <div class="col-md-6">
            <fieldset class="mt-3">
                <legend><?php echo esc_html__( 'Popup box', 'ct-ultimate-gdpr' ); ?></legend>

                <div class="mb-3">
                    <label for="age_box_style"
                           class="form-label"><?php echo esc_html__('Box style', 'ct-ultimate-gdpr'); ?></label>
                    <?php echo $age_box_style; ?>
                </div>
                <div class="mb-3">
                    <label for="age_box_shape"
                           class="form-label"><?php echo esc_html__('Box shape', 'ct-ultimate-gdpr'); ?></label>
                    <?php echo $age_box_shape; ?>
                </div>
                <div class="mb-3">
                    <label for="age_button_shape"
                           class="form-label"><?php echo esc_html__('Button shape', 'ct-ultimate-gdpr'); ?></label>
                    <?php echo $age_button_shape; ?>
                </div>

            </fieldset>
            <fieldset class="mb-3">
                <legend><?php echo esc_html__('Preview', 'ct-ultimate-gdpr'); ?></legend>

                <img src="<?php echo ct_ultimate_gdpr_url('includes/views/admin/wizard/assets/images/age-verification-preview.png'); ?>" alt="<?php echo esc_html__('Age Verification', 'ct-ultimate-gdpr'); ?>">

            </fieldset>
        </div>
    </div>
    <!-- / row -->

    <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>

</form>