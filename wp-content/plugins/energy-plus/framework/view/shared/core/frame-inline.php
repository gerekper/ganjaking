<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>
<?php echo EnergyPlus_View::run('header-page', array('type'=> 1, 'title' => $title, 'description' => '', 'buttons'=>'')); ?>
<?php $nav ?>
<div id="energyplus-frame-inline">
  <iframe src="<?php echo esc_url_raw($iframe_url); ?> " id="energyplus-frame" frameborder=0></iframe>
</div>
