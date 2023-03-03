<?php
/** @var string $terms_require_administrator */
/** @var string $terms_require_users */
/** @var string $terms_require_guests */
/** @var string $terms_target_page */
/** @var string $terms_target_custom */
/** @var string $policy_require_administrator */
/** @var string $policy_require_users */
/** @var string $policy_require_guests */
/** @var string $policy_target_page */
/** @var string $policy_target_custom */

?>
<h1><?php echo esc_html__('Accept Terms', 'ct-ultimate-gdpr'); ?></h1>
<p><?php echo esc_html__( "You can require from all visitors of your website to accept the Privacy Policy and/or Terms and Conditions before accessing any other web pages. 
You can enable these settings below.", 'ct-ultimate-gdpr' ); ?></p>
<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step6'); ?>">

    <div class="row g-5">
        <div class="col-md-6">
            <fieldset class="mt-3">
                <legend><?php echo esc_html__( "Terms and Conditions", 'ct-ultimate-gdpr' ); ?></legend>

                <div class="mb-3 form-check">
                   
                    <label for="terms_require_administrator" class="form-check-label"><?php echo esc_html__( "Require administrators to accept Terms and Conditions (redirect)", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $terms_require_administrator; ?>
                    <div class="ct-ultimate-gdpr-form-check">
                        <div class="ct-ultimate-gdpr-checkbox-switch">
                            <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    
                    <label for="terms_require_users" class="form-check-label"><?php echo esc_html__( "Require logged in users to accept Terms and Conditions (redirect)", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $terms_require_users; ?>
                    <div class="ct-ultimate-gdpr-form-check">
                        <div class="ct-ultimate-gdpr-checkbox-switch">
                            <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    
                    <label for="terms_require_guests" class="form-check-label"><?php echo esc_html__( "Require guest users to accept Terms and Conditions (redirect)", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $terms_require_guests; ?>
                    <div class="ct-ultimate-gdpr-form-check">
                        <div class="ct-ultimate-gdpr-checkbox-switch">
                            <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-md-8">
                    <label for="terms_target_page"
                           class="form-label col-form-label"><?php echo esc_html__( 'The page with existing Terms and Conditions', 'ct-ultimate-gdpr' ); ?></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $terms_target_page; ?>
                    </div>
                    
                </div>
                <div class="mb-3">
                    <label for="terms_target_custom"
                           class="form-label"><?php echo esc_html__( 'Terms and Condition Custom URL', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $terms_target_custom; ?>
                </div>

            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset class="mt-3">
                <legend><?php echo esc_html__( "Privacy Policy", 'ct-ultimate-gdpr' ); ?></legend>

                <div class="mb-3 form-check">
                    
                    <label for="policy_require_administrator" class="form-check-label"><?php echo esc_html__( "Require administrators to accept Privacy Policy (redirect)", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $policy_require_administrator; ?>
                    <div class="ct-ultimate-gdpr-form-check">
                        <div class="ct-ultimate-gdpr-checkbox-switch">
                            <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    
                    <label for="policy_require_users" class="form-check-label"><?php echo esc_html__( "Require logged in users to accept Privacy Policy (redirect)", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $policy_require_users; ?>
                    <div class="ct-ultimate-gdpr-form-check">
                        <div class="ct-ultimate-gdpr-checkbox-switch">
                            <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                        </div>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    
                    <label for="policy_require_guests" class="form-check-label"><?php echo esc_html__( "Require not logged in users/guests to accept Privacy Policy (redirect)", 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $policy_require_guests; ?>
                    <div class="ct-ultimate-gdpr-form-check">
                        <div class="ct-ultimate-gdpr-checkbox-switch">
                            <span class="on label"><?php _e('Enable', 'ct-ultimate-gdpr' ); ?></span><span class="off label"><?php _e('Disable', 'ct-ultimate-gdpr' ); ?></span><span class="switch" style="left: 0px;"><?php _e('Enabled', 'ct-ultimate-gdpr' ); ?></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col-md-8">
                    <p><?php echo esc_html__( 'WordPress Privacy Policy page', 'ct-ultimate-gdpr' ); ?></p>
                    </div>
                    <div class="col-md-4 text-center">
                    <?php
                            $url = admin_url( 'privacy.php' );
                            printf("<a href='%s' target='_blank'>%s</a>",$url,esc_html__( 'Click here to create', 'ct-ultimate-gdpr' ));

                            ?>
                    </div>
                            
                           
                </div>
                <div class="mb-3 row">
                    <div class="col-md-8">
                    <label for="policy_target_page"
                           class="form-label col-form-label"><?php echo esc_html__( 'Page with existing Privacy Policy', 'ct-ultimate-gdpr' ); ?></label>
                    </div>
                    <div class="col-md-4">
                    <?php echo $policy_target_page; ?>
                    </div>
                    
                </div>

                <div class="mb-3">
                    <label for="policy_target_custom"
                           class="form-label"><?php echo esc_html__( 'Privacy Policy Custom URL', 'ct-ultimate-gdpr' ); ?></label>
                    <?php echo $policy_target_custom; ?>
                </div>

            </fieldset>
        </div>
    </div>
    <!-- / row -->

    <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr' )); ?>

</form>