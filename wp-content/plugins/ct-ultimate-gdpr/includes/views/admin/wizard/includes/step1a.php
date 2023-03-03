<?php
/** @var string $admin_envato_key */
?>

<div class="wrapper text-center">
    <div class="main-content">
        <h1><?php echo esc_html__('Cookie scanner', 'ct-ultimate-gdpr'); ?></h1>
        <p><?php echo esc_html__('This website has not been scanned yet.', 'ct-ultimate-gdpr'); ?><br/><?php echo esc_html__('We need to know what cookies are already in use to ensure proper functioning of the plugin. ', 'ct-ultimate-gdpr'); ?></p>
        <p><?php echo esc_html__('We recommend you repeat the scan from time to time to keep the cookie list up to date.', 'ct-ultimate-gdpr'); ?> <br/><?php echo esc_html__("You can manage automatic cookie scans in 'Cookie scanner settings'.", 'ct-ultimate-gdpr'); ?></p>
        <p class="highlight-text"><?php echo __('Before start paste your license key from <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Envato.com</a>', 'ct-ultimate-gdpr'); ?></p>
    </div>
    <form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
        <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
        <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
        <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step1b'); ?>">

        <div class="d-block d-sm-flex">
            <label for="admin_envato_key"
                class="form-label mb-2 mb-sm-0 align-self-center"><?php echo esc_html__("License Envato key", 'ct-ultimate-gdpr'); ?></label>
            <?php echo $admin_envato_key; ?>
        </div>

        <?php ct_ultimate_gdpr_wizard_submit(esc_html__( 'Scan for cookies', 'ct-ultimate-gdpr' )); ?>
        <!-- / row -->
    </form>
</div>
