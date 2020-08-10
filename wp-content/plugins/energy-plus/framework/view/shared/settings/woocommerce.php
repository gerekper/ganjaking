<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Settings', 'energyplus'), 'description' => '', 'buttons'=>'')); ?>
<?php echo EnergyPlus_View::run('settings/nav') ?>

<div id="energyplus-settings-woocommerce">
  <iframe src="<?php echo admin_url('admin.php?page=wc-settings') ?> " id="energyplus-frame" frameborder=0></iframe>
</div>
