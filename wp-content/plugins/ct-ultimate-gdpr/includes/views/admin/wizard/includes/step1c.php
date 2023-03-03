<?php
/** @var array $services */
/** @var string $cookie_scan_period */
/** @var string $cookie_default_level_assigned_for_inserted_cookies */
?>

<h1><?php echo esc_html__('Cookies Scanner', 'ct-ultimate-gdpr'); ?></h1>
<p><?php echo esc_html__('Our busy bees are back with the following results. Please take a moment to verify if all cookies are correctly assigned to the groups.', 'ct-ultimate-gdpr'); ?><br/>
<?php echo esc_html__('You can edit all the settings below now or get back to these anytime later in the Cookie Consent Settings.', 'ct-ultimate-gdpr'); ?></p>

<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step2'); ?>">

    
    <div class="row justify-content-md-center">
        <div class="col-md-auto col-md-12">
            <div class="mb-3">
                
                <?php
                $values = ct_ultimate_gdpr_wizard_get_levels();
                ?>
                
                <div class="row">
                    <?php foreach ( $values as $value ) : ?>
                        <div class="group__item col-md-auto mb-sm-4 js-group__item<?php echo $value; ?>">
                            <h3 class="mb-4"><?php echo __(CT_Ultimate_GDPR_Model_Group::get_label( $value ),'ct-ultimate-gdpr'); ?></h3>
                            <?php foreach($services[$value] as $service): ?>
                                <div class="service__item <?php echo ($service->is_active) ? 'active' : 'non-active'; ?>" data-id="<?php echo $service->ID; ?>">
                                    <?php echo esc_html__($service->post_title, 'ct-ultimate-gdpr'); ?>

                                    <button type="button" class="link--move p-0" data-bs-toggle="dropdown">
                                        <?php echo esc_html__('Move', 'ct-ultimate-gdpr'); ?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><span class="dropdown-item pe-none"><?php echo esc_html__('Move to:', 'ct-ultimate-gdpr'); ?></span></li>
                                        <?php foreach ( $values as $value ) : ?>
                                            <li class="menu<?php echo $value; ?>"><a class="dropdown-item js-move" data-serviceid="<?php echo $value; ?>" href="#"><?php echo __(CT_Ultimate_GDPR_Model_Group::get_label( $value ),'ct-ultimate-gdpr'); ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="row mb-3">
                        <div class="col-md-7 d-flex align-items-center">
                            <label for="cookie_scan_period" class="col-form-label"><?php echo esc_html__( 'Choose how often cookie scans should be performed', 'ct-ultimate-gdpr' ); ?></label>
                        </div>
                        <div class="col-md-5">
                            <?php echo $cookie_scan_period; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-7 d-flex align-items-center">
                            <label for="cookie_default_level_assigned_for_inserted_cookies" class="col-form-label"><?php echo esc_html__( 'Choose default level of new cookies', 'ct-ultimate-gdpr' ); ?></label>
                        </div>
                        <div class="col-md-5">
                            <?php echo $cookie_default_level_assigned_for_inserted_cookies; ?>
                        </div>
                    </div>

                    <div class="cookie-notice mt-5">
                        <span class="d-block"><?php echo esc_html__( 'Notice!', 'ct-ultimate-gdpr' ); ?></span>
                        <p class="mb-0"><?php echo esc_html__( 'If we install new things, the list of cookies may change.', 'ct-ultimate-gdpr' ); ?></p>
                    </div>
                </div>
            </div>
            <!-- / row -->

            <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>

        </div>
    </div>
    <!-- / row -->


</form>
