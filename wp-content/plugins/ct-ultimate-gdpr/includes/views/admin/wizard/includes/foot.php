<?php
/**
 *
 */
?>
<div class="container">
    <?php if(ct_ultimate_gdpr_wizard_is_step('step') && ! ct_ultimate_gdpr_wizard_is_step('step8c') ): ?>
        <?php if( ! ct_ultimate_gdpr_wizard_is_step('step1b')): ?>
            <div class="float-start">
                <a href="#" class="btn btn-cancel" data-toggle="modal" data-target="#cancelModal"><?php _e( "Cancel Creator", 'ct-ultimate-gdpr'); ?></a>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title" id="cancelModalLongTitle"><?php _e('Warning', 'ct-ultimate-gdpr'); ?></h5>
                    <?php echo esc_html__('Creator is the easiest way to configure the basic parameters of the plugin. If you are not an advanced user, we recommend finishing the setup wizard. After completing the setup, you will have access to all options.', 'ct-ultimate-gdpr'); ?>
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="<?php echo admin_url( "admin.php?page=ct-ultimate-gdpr" ); ?>" class="btn btn-cancel"><?php _e( "Cancel Creator", 'ct-ultimate-gdpr'); ?></a>
                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?php _e( "Back to Creator", 'ct-ultimate-gdpr'); ?></button>
                </div>
                </div>
            </div>
            </div>
            
            <div class="float-end">

                <?php if(! ct_ultimate_gdpr_wizard_is_step('step1') && ! ct_ultimate_gdpr_wizard_is_step('step2b') && ! ct_ultimate_gdpr_wizard_is_step('step8b') ): ?>
                    <button href="<?php echo ct_ultimate_gdpr_wizard_prev_step(); ?>" class="btn js-save-and-go btn-back"><i class="bi bi-arrow-left-circle"></i> <?php _e( "Back", 'ct-ultimate-gdpr'); ?></button>
                <?php endif; ?>

                <?php if(ct_ultimate_gdpr_wizard_is_step('step2b') ): ?>
                    <button href="<?php echo ct_ultimate_gdpr_wizard_step_url('step2'); ?>" class="btn js-save-and-go btn-back"><i class="bi bi-arrow-left-circle"></i> <?php _e( "Back", 'ct-ultimate-gdpr'); ?></button>
                <?php endif; ?>
                
                <?php if(ct_ultimate_gdpr_wizard_is_step('step8b') ): ?>
                    <button href="<?php echo ct_ultimate_gdpr_wizard_step_url('step8'); ?>" class="btn js-save-and-go btn-back"><i class="bi bi-arrow-left-circle"></i> <?php _e( "Back", 'ct-ultimate-gdpr'); ?></button>
                <?php endif; ?>

                <?php if (ct_ultimate_gdpr_wizard_is_step('step1a')) : ?>
                    <button href="#" class="btn btn-primary js-submit"><?php _e( "Scan For Cookies", 'ct-ultimate-gdpr'); ?></button>
                <?php elseif (ct_ultimate_gdpr_wizard_is_step('step1c')) : ?>
                    <button href="#" class="btn btn-primary js-submit"><?php _e( "Go To Display", 'ct-ultimate-gdpr'); ?></button>
                <?php elseif (ct_ultimate_gdpr_wizard_is_step('step6')) : ?>
                    <button href="#" class="btn btn-primary js-submit"><?php _e( "I Understand", 'ct-ultimate-gdpr'); ?></button>
                <?php else : ?>
                    <button href="#" class="btn btn-primary js-submit"><?php _e( "Next step", 'ct-ultimate-gdpr'); ?></button>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if(ct_ultimate_gdpr_wizard_is_step('welcome')): ?>
        <div class="float-start">
            <a href="<?php echo admin_url( "admin.php?page=ct-ultimate-gdpr" ); ?>" class="btn btn-cancel"><?php _e( "NOT RIGHT NOW", 'ct-ultimate-gdpr'); ?></a>
        </div>
    <?php endif; ?>
</div>
