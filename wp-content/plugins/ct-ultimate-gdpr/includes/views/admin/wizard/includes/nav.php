<?php
$current_page = ct_ultimate_gdpr_wizard_current_step();
$step = str_replace('step', '', $current_page);

?>
<?php
if (ct_ultimate_gdpr_wizard_is_step('step')): ?>
<div class="container">
    <nav class="nav nav-pills nav-fill">
    <?php for($i = 1; $i <= 8; $i++): ?>
        <?php $step_number = 'step'.$i;
            if($i === 1){
                $step_number = 'step1a';
            }
        ?>
        <button class="js-save-and-go <?php if(ct_ultimate_gdpr_wizard_is_step($step_number)): ?> active<?php endif; ?> <?php if($i < intval($step)): ?>done<?php endif; ?>" href="<?php echo ct_ultimate_gdpr_wizard_step_url($step_number); ?>"><?php _e( 'Step', 'ct-ultimate-gdpr');?> <?php echo $i; ?></button>
    <?php endfor; ?>
    </nav>
</div>
<?php endif; ?>