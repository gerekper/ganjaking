<?php
$scanner_mode = 'scan';
if (isset($_GET['notice']) && $_GET['notice'] === 'scanner-success') {
    // success, show results
    $scanner_mode = 'show-results';
}
?>
<h1><?php echo esc_html__('Scanner of cookies', 'ct-ultimate-gdpr'); ?></h1>
<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step1c'); ?>">

    <div class="justify-content-md-center">
        <?php
        ct_ultimate_gdpr_render_template(ct_ultimate_gdpr_locate_template('admin/includes/cookie-scanner', true));
        ?>
        <?php if ($scanner_mode === 'scan'): ?>
            <div class="form__submit">
                <p class="submit">
                    <input type="submit" name="ct-ultimate-gdpr-check-cookies" id="submit1" class="button"
                            value="<?php echo esc_html__('Scan for cookies', 'ct-ultimate-gdpr'); ?>">
                </p>
                <p class="submit">
                    <a href="<?php echo ct_ultimate_gdpr_wizard_step_url('step1c'); ?>" id="submit2"
                        class="btn btn-primary"><?php echo esc_html__('Skip', 'ct-ultimate-gdpr'); ?></a>
                </p>
                <script>
                    window.onload = function () {
                        setTimeout(function () {
                            const submit = document.getElementById("submit1");
                            submit.click();
                            submit.style.display = 'none';
                        }, 0)
                    }
                </script>
            </div>
        <?php else: ?>
            <div class="form__submit">
                <p class="submit">
                    <?php $result_url = ct_ultimate_gdpr_wizard_step_url('step1c'); ?>
                    <script>
                        window.location.href = '<?php echo $result_url; ?>';
                    </script>
                    <a href="<?php echo $result_url; ?>" id="submit2"
                        class="btn btn-primary"><?php echo esc_html__('Show results', 'ct-ultimate-gdpr'); ?></a>
                </p>
            </div>
        <?php endif; ?>
    </div><!-- / row -->

    <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>

</form>