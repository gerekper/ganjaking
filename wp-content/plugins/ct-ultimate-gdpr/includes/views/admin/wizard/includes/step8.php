<?php
/** @var array $shortcodes */
?>
<h1><?php echo esc_html__('Shortcodes', 'ct-ultimate-gdpr'); ?></h1>

<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step8c'); ?>">

    <p><?php echo esc_html__( 'Shortcodes allow you to render complex elements on your WordPress page without coding. Below you will see the list of all shortcodes available to use with our plugin. Just paste the [shortcode] in your page content, and we will do the rest: display the contact form to the user, render the cookie list and more 😊', 'ct-ultimate-gdpr'); ?></p>

    <p class="fw-bold"><?php echo esc_html__( 'We recommend you to use at least the [ultimate_gdpr_myaccount] shortcode, so your visitors could easily contact you for information about their personal data.', 'ct-ultimate-gdpr'); ?> </p>

    <ol class="list-group list-group-numbered m-0">
        <li class="list-group-item m-0">
            <div class="d-flex justify-content-between">
                <div class="list-group-item-left fw-bold"><?php echo esc_html__( 'User Settings:', 'ct-ultimate-gdpr' ); ?></div>
                <div class="list-group-item-right">
                    <?php echo $shortcodes[1]; ?>
                    <?php ct_ultimate_gdpr_wizard_preview_url('step8b&ctshortcode=1'); ?>
                </div>
            </div>
            <p class="list-group-item-content mb-0">
                <?php echo esc_html__( 'Generates form for the user, which allows please about share, changing it or deleting his data.', 'ct-ultimate-gdpr' ); ?>
            </p>
        </li>
        <li class="list-group-item m-0">
            <div class="d-flex justify-content-between">
                <div class="list-group-item-left fw-bold"><?php echo esc_html__( 'Privacy Policy Button:', 'ct-ultimate-gdpr' ); ?></div>
                <div class="list-group-item-right">
                    <?php echo $shortcodes[2]; ?>
                    <?php ct_ultimate_gdpr_wizard_preview_url('step8b&ctshortcode=2'); ?>
                </div>
            </div>
        </li>
        <li class="list-group-item m-0">
            <div class="d-flex justify-content-between">
                <div class="list-group-item-left fw-bold"><?php echo esc_html__( 'Terms and Conditions Button:', 'ct-ultimate-gdpr' ); ?></div>
                <div class="list-group-item-right">
                    <?php echo $shortcodes[3]; ?>
                    <?php ct_ultimate_gdpr_wizard_preview_url('step8b&ctshortcode=3'); ?>
                </div>
            </div>
        </li>

        <li class="list-group-item m-0">
            <div class="d-flex justify-content-between">
                <div class="list-group-item-left fw-bold"><?php echo esc_html__( 'Cookie Popup Link:', 'ct-ultimate-gdpr' ); ?></div>
                <div class="list-group-item-right">
                    <?php echo $shortcodes[4]; ?>
                    <?php ct_ultimate_gdpr_wizard_preview_url('step8b&ctshortcode=4'); ?>
                </div>
            </div>
            <p class="list-group-item-content mb-0">
                <?php echo esc_html__( 'Generates a link to subsites connected with GDPR.', 'ct-ultimate-gdpr' ); ?>
            </p>
        </li>
        <li class="list-group-item m-0">
            <div class="d-flex justify-content-between">
                <div class="list-group-item-left fw-bold"><?php echo esc_html__( 'Display Cookies List:', 'ct-ultimate-gdpr' ); ?></div>
                <div class="list-group-item-right">
                    <?php echo $shortcodes[5]; ?>
                    <?php ct_ultimate_gdpr_wizard_preview_url('step8b&ctshortcode=5'); ?>
                </div>
            </div>
            <p class="list-group-item-content mb-0">
                <?php echo esc_html__( 'Shows table with discovered and save on the list file cookies.', 'ct-ultimate-gdpr' ); ?>
            </p>
        </li>
        <li class="list-group-item m-0">
            <div class="d-flex justify-content-between">
                <div class="list-group-item-left fw-bold"><?php echo esc_html__( 'Protect Content:', 'ct-ultimate-gdpr' ); ?></div>
                <div class="list-group-item-right">
                    <?php echo $shortcodes[6]; ?>
                    <?php ct_ultimate_gdpr_wizard_preview_url('step8b&ctshortcode=6'); ?>
                </div>
            </div>
            <p class="list-group-item-content mb-0">
                <?php echo esc_html__( 'If we put content in this shortcode it will be hide till the user accept cookie files at a given level.', 'ct-ultimate-gdpr' ); ?>
            </p>
        </li>
        <li class="list-group-item m-0">
            <div class="d-flex justify-content-between">
                <div class="list-group-item-left fw-bold"><?php echo esc_html__( 'Privacy Center:', 'ct-ultimate-gdpr' ); ?></div>
                <div class="list-group-item-right">
                    <?php echo $shortcodes[7]; ?>
                    <?php ct_ultimate_gdpr_wizard_preview_url('step8b&ctshortcode=7'); ?>
                </div>
            </div>
            <p class="list-group-item-content mb-0">
                <?php echo esc_html__( 'Txt about this code. ', 'ct-ultimate-gdpr' ); ?>
            </p>
        </li>
    </ol>

    <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>

</form>