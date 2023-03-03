<?php
/** @var string $forgotten_automated_forget */
/** @var string $forgotten_automated_user_email */
/** @var string $forgotten_notify_mail */
/** @var string $forgotten_notify_email_subject */
/** @var string $dataaccess_automated_dataaccess */
/** @var string $dataaccess_notify_mail */
/** @var string $dataaccess_mail_title */
/** @var string $breach_mail_title */
/** @var string $rectification_notify_mail */
/** @var string $rectification_mail_title */

?>

<h1><?php echo esc_html__('Personal Data Access', 'ct-ultimate-gdpr'); ?></h1>
<p><?php echo esc_html__('Define how we should process visitor’s requests for their personal data information access. Also, set custom messages for the email title, content, 
and the sender’s email address.', 'ct-ultimate-gdpr'); ?></p>
<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step4'); ?>">

    <div class="row g-5">
        <div class="col-md-6">
            <fieldset class="mt-3">
                <legend><?php echo esc_html__( 'Right To Be Forgotten Settings', 'ct-ultimate-gdpr' ); ?></legend>

                <div class="mb-3 form-check">
                    <label for="forgotten_automated_forget" class="form-check-label"><?php echo esc_html__( "Automatically remove data of users who confirmed their email", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $forgotten_automated_forget; ?>
                    <div class="ct-ultimate-gdpr-form-check">
                        <div class="ct-ultimate-gdpr-checkbox-switch"> 
                            <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    
                    <label for="forgotten_automated_user_email" class="form-check-label"><?php echo esc_html__( "Automatically send email about data removal to users who confirmed their email", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $forgotten_automated_user_email; ?>
                    <div class="ct-ultimate-gdpr-form-check">
                        <div class="ct-ultimate-gdpr-checkbox-switch">
                            <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="forgotten_notify_mail"
                           class="form-label"><?php echo esc_html__( "Admin email to send new request notifications to", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $forgotten_notify_mail; ?>
                </div>
                <div class="mb-3">
                    <label for="forgotten_notify_email_subject"
                           class="form-label"><?php echo esc_html__( "User notification email subject", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $forgotten_notify_email_subject; ?>
                </div>
            </fieldset>

            <fieldset class="mt-3">
                <legend><?php echo esc_html__( 'Data Access', 'ct-ultimate-gdpr' ); ?></legend>

                <div class="mb-3 form-check">
                    
                    <label for="dataaccess_automated_dataaccess" class="form-check-label"><?php echo esc_html__( "Automatically send data to all users on their request", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $dataaccess_automated_dataaccess; ?>
                    <div class="ct-ultimate-gdpr-form-check">
                        <div class="ct-ultimate-gdpr-checkbox-switch">
                        <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="dataaccess_notify_mail"
                           class="form-label"><?php echo esc_html__( "Email to send new request notifications to", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $dataaccess_notify_mail; ?>
                </div>
                <div class="mb-3">
                    <label for="dataaccess_mail_title"
                           class="form-label"><?php echo esc_html__( "Mail title", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $dataaccess_mail_title; ?>
                </div>
            </fieldset>

        </div>
        <div class="col-md-6">

            <fieldset class="mt-3">
                <legend><?php echo esc_html__( 'Data Breach', 'ct-ultimate-gdpr' ); ?></legend>
                <div class="mb-3">
                    <label for="breach_mail_title"
                           class="form-label"><?php echo esc_html__( "Mail title", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $breach_mail_title; ?>
                </div>
            </fieldset>


            <fieldset class="mt-3">
                <legend><?php echo esc_html__( 'Data Rectification', 'ct-ultimate-gdpr' ); ?></legend>
                <div class="mb-3">
                    <label for="rectification_notify_mail"
                           class="form-label"><?php echo esc_html__( "Email to send admin notifications to", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $rectification_notify_mail; ?>
                </div>
                <div class="mb-3">
                    <label for="rectification_mail_title"
                           class="form-label"><?php echo esc_html__( "User mail title", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $rectification_mail_title; ?>
                </div>
            </fieldset>
        </div>
    </div>
    <!-- / row -->

    <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>


</form>


