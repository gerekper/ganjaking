<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo EnergyPlus_View::run('header-energyplus'); ?>

<iframe src="<?php echo EnergyPlus_Helpers::clean ( $page, 'about::blank' ); ?>" id="energyplus-frame" frameborder=0></iframe>
