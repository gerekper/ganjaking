<?php
/** @var string $iframe_url */


$iframe_url = home_url($iframe_url);
?>

<h1><?php echo esc_html__('Preview', 'ct-ultimate-gdpr'); ?></h1>


<form method="POST" action="<?php echo esc_html(admin_url('admin-post.php')); ?>" class="js-form-wizard">
    <input type="hidden" name="action" value="ct_ultimate_gdpr_wizard_save">
    <?php wp_nonce_field('ct_ultimate_gdpr_wizard_save', 'ct_ultimate_gdpr_wizard'); ?>
    <input type="hidden" name="redirectToUrl" value="<?php echo ct_ultimate_gdpr_wizard_step_url('step8c'); ?>">


    <div class="row justify-content-md-center" style="min-height:700px;">
        <div class="col-md-auto col-md-12">

            <iframe src="<?php echo $iframe_url; ?>" style="width:100%; height:100%;" frameBorder="0"></iframe>

            <?php ct_ultimate_gdpr_wizard_submit(esc_html__('Submit', 'ct-ultimate-gdpr')); ?>

        </div>
    </div>
    <!-- / row -->


</form>
