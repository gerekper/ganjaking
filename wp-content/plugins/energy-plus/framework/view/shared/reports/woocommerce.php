<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>

<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => esc_html__('Reports', 'energyplus'), 'description' => '', 'buttons'=>'')); ?>

<?php echo EnergyPlus_View::run('reports/nav') ?>

<div id="energyplus-reports-woocommerce">
  <?php
  if (!empty($report)) {
    if ('customers' === $report) {
      $url =  admin_url('admin.php?page=wc-admin&path=/' . esc_attr($report));
    } else {
      $url =  admin_url('admin.php?page=wc-admin&path=/analytics/' . esc_attr($report));
    }
  } else {
    $url =  admin_url('admin.php?page=wc-reports');
  }
  ?>
  <iframe src="<?php echo esc_url($url)?>" id="energyplus-frame" frameborder=0></iframe>
</div>
